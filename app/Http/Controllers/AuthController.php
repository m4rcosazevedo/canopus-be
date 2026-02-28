<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LoginWithCodeRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SendLoginCodeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\LoginCodeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): UserResource
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, true)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $request->session()->regenerate();

        return  $this->authenticatedUserResource();
    }

    public function me(): UserResource
    {
        return $this->authenticatedUserResource();
    }

    private function authenticatedUserResource(): UserResource
    {
        return new UserResource(
            Auth::user()->load(['userType.permissions'])
        );
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout realizado']);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cellphone' => $request->cellphone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function signIn(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function signOut(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Desconectado com sucesso.']);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Senha alterada com sucesso.']);
    }

    public function sendLoginCode(SendLoginCodeRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        $code = (string) random_int(100000, 999999);

        Cache::put("login_code_{$user->email}", $code, now()->addMinutes(5));

        $user->notify(new LoginCodeNotification($code, $user->name));

        return response()->json(['message' => 'Código enviado para o seu e-mail.']);
    }

    public function loginWithCode(LoginWithCodeRequest $request): UserResource
    {
        $cachedCode = Cache::get("login_code_{$request->email}");

        if (!$cachedCode || $cachedCode !== $request->code) {
            throw ValidationException::withMessages([
                'code' => ['O código fornecido é inválido ou expirou.'],
            ]);
        }

        Cache::forget("login_code_{$request->email}");

        $user = User::where('email', $request->email)->first();

        Auth::login($user, true);

        $request->session()->regenerate();

        return $this->authenticatedUserResource();
    }
}
