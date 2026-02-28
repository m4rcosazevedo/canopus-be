<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Código de Acesso</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f7;
            color: #51545e;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            width: 100% !important;
        }
        .email-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #f4f4f7;
        }
        .email-content {
            width: 100%;
            margin: 0;
            padding: 0;
            max-width: 600px;
            margin: 0 auto;
        }
        .email-masthead {
            padding: 25px 0;
            text-align: center;
        }
        .email-masthead_logo {
            max-width: 150px;
            border: 0;
        }
        .email-body {
            width: 100%;
            margin: 0;
            padding: 0;
            border-top: 1px solid #eaeaec;
            border-bottom: 1px solid #eaeaec;
            background-color: #ffffff;
        }
        .email-body_inner {
            width: 570px;
            margin: 0 auto;
            padding: 45px;
        }
        .content-cell {
            padding: 35px;
        }
        h1 {
            margin-top: 0;
            color: #333333;
            font-size: 22px;
            font-weight: bold;
            text-align: left;
        }
        p {
            margin-top: 0;
            color: #74787e;
            font-size: 16px;
            line-height: 1.5em;
            text-align: left;
        }
        .code-container {
            text-align: center;
            margin: 30px 0;
        }
        .code {
            display: inline-block;
            padding: 15px 30px;
            background-color: #3869d4;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            border-radius: 5px;
            text-decoration: none;
        }
        .email-footer {
            width: 570px;
            margin: 0 auto;
            padding: 0;
            text-align: center;
        }
        .email-footer p {
            color: #aeaeae;
            font-size: 12px;
            text-align: center;
        }
        @media only screen and (max-width: 600px) {
            .email-body_inner,
            .email-footer {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0">
                    <!-- Logo -->
                    <tr>
                        <td class="email-masthead">
                            <a href="{{ config('app.url') }}" class="email-masthead_logo_link">
                                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="email-masthead_logo">
                            </a>
                        </td>
                    </tr>
                    <!-- Email Body -->
                    <tr>
                        <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="content-cell">
                                        <h1>Olá, {{ $name }}!</h1>
                                        <p>Você solicitou um código para acessar sua conta no <strong>{{ config('app.name') }}</strong>.</p>
                                        <p>Use o código abaixo para completar seu login:</p>

                                        <div class="code-container">
                                            <span class="code">{{ $code }}</span>
                                        </div>

                                        <p>Este código é válido por 5 minutos.</p>
                                        <p>Se você não solicitou este código, por favor ignore este e-mail. Sua conta permanece segura.</p>

                                        <p>Atenciosamente,<br>Equipe {{ config('app.name') }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td>
                            <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="content-cell">
                                        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
