<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassRegistration\StoreClassRegistrationRequest;
use App\Models\ClassRegistration;

class ClassRegistrationController extends Controller
{
    public function index()
    {
        return ClassRegistration::all();
    }

    public function store(StoreClassRegistrationRequest $request)
    {
        $request->validated();
    }

}
