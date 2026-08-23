<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Payroll Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gov-navy: #1a2b4a;
            --gov-blue: #2563eb;
            --gov-blue-hover: #1d4ed8;
            --gov-gray-50: #f8fafc;
            --gov-gray-100: #f1f5f9;
            --gov-gray-200: #e2e8f0;
            --gov-gray-400: #94a3b8;
            --gov-gray-600: #475569;
            --gov-gray-800: #1e293b;
            --gov-error: #dc2626;
            --gov-error-bg: #fef2f2;
            --gov-error-border: #fecaca;
            --focus-ring: rgba(37, 99, 235, 0.25);
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--gov-gray-800);
            background: var(--gov-gray-50);
        }

        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid var(--gov-gray-200);
            border-radius: 12px;
            padding: 40px 36px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 6px 16px rgba(0, 0, 0, 0.04);
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: var(--gov-gray-100);
            color: var(--gov-blue);
            font-size: 20px;
            margin-bottom: 20px;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gov-gray-800);
            margin: 0 0 6px;
            letter-spacing: -0.01em;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: var(--gov-gray-600);
            margin: 0;
            line-height: 1.5;
        }

        .login-alert {
            background: var(--gov-error-bg);
            border: 1px solid var(--gov-error-border);
            color: var(--gov-error);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: none;
        }

        .login-alert:not(:empty) {
            display: block;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gov-gray-800);
            margin-bottom: 6px;
            letter-spacing: 0.01em;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--gov-gray-800);
            background: #ffffff;
            border: 1.5px solid var(--gov-gray-200);
            border-radius: 8px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control::placeholder {
            color: var(--gov-gray-400);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--gov-blue);
            box-shadow: 0 0 0 3px var(--focus-ring);
        }

        .password-field {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gov-gray-400);
            cursor: pointer;
            padding: 4px;
            font-size: 0.9rem;
            transition: color 0.15s ease;
        }

        .password-toggle:hover,
        .password-toggle:focus {
            color: var(--gov-gray-600);
            outline: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            color: #ffffff;
            background: var(--gov-blue);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.15s ease, transform 0.1s ease;
        }

        .btn-login:hover {
            background: var(--gov-blue-hover);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.82rem;
            color: var(--gov-gray-600);
            line-height: 1.5;
        }

        .login-footer a {
            color: var(--gov-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .login-footer .secure-note {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 12px;
            font-size: 0.78rem;
            color: var(--gov-gray-400);
        }

        .login-footer .secure-note i {
            font-size: 0.85rem;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>

<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-building-columns"></i>
            </div>
            <h1 class="login-title">Sign In</h1>
            <p class="login-subtitle">Enter your credentials to access the Payroll Management System.</p>
        </div>

        <div class="login-alert" id="loginAlert">
            <?= session()->getFlashdata('error') ?>
        </div>

        <form action="<?= base_url('auth/authenticate') ?>" method="post" novalidate>
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="password-field">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Show password">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="login-footer">
            <div>Forgot password? <a href="#">Contact your system administrator</a></div>
            <div class="secure-note">
                <i class="fas fa-shield-halved"></i>
                <span>Authorized personnel only. All activity is logged.</span>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    document.getElementById('loginAlert').addEventListener('transitionend', function() {
        if (this.textContent.trim() === '') {
            this.style.display = 'none';
        }
    });
</script>

</body>
</html>
