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
            --gov-navy: #0f172a;
            --gov-navy-light: #1e293b;
            --gov-teal: #0d5c4e;
            --gov-teal-hover: #0a4a3f;
            --gov-teal-subtle: #e6f4f1;
            --gov-gray-50: #f8fafc;
            --gov-gray-100: #f1f5f9;
            --gov-gray-200: #e2e8f0;
            --gov-gray-300: #cbd5e1;
            --gov-gray-400: #94a3b8;
            --gov-gray-500: #64748b;
            --gov-gray-600: #475569;
            --gov-gray-700: #334155;
            --gov-gray-800: #1e293b;
            --gov-error: #dc2626;
            --gov-error-bg: #fef2f2;
            --gov-error-border: #fecaca;
            --focus-ring: rgba(13, 92, 78, 0.25);
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--gov-gray-800);
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
        }

        .login-page {
            display: flex;
            min-height: 100vh;
        }

        .login-brand {
            flex: 0 0 420px;
            background: linear-gradient(160deg, var(--gov-navy) 0%, var(--gov-navy-light) 100%);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }

        .login-brand::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -30%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(13, 92, 78, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .login-brand::after {
            content: "";
            position: absolute;
            bottom: -40%;
            left: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.04) 0%, transparent 70%);
            border-radius: 50%;
        }

        .brand-content {
            position: relative;
            z-index: 1;
        }

        .brand-seal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.1);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            margin-bottom: 32px;
            backdrop-filter: blur(10px);
        }

        .brand-seal i {
            font-size: 24px;
            color: #ffffff;
        }

        .brand-title {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .brand-subtitle {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            margin-bottom: 48px;
            max-width: 320px;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .brand-feature i {
            width: 20px;
            text-align: center;
            color: var(--gov-teal);
            font-size: 0.95rem;
        }

        .login-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid var(--gov-gray-200);
            border-radius: 16px;
            padding: 44px 40px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .login-header {
            margin-bottom: 32px;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gov-gray-800);
            margin: 0 0 8px;
            letter-spacing: -0.01em;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: var(--gov-gray-500);
            margin: 0;
            line-height: 1.5;
        }

        .login-alert {
            background: var(--gov-error-bg);
            border: 1px solid var(--gov-error-border);
            color: var(--gov-error);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .login-alert:empty {
            display: none;
        }

        .login-alert i {
            margin-top: 2px;
            flex-shrink: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gov-gray-700);
            margin-bottom: 8px;
            letter-spacing: 0.01em;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--gov-gray-800);
            background: #ffffff;
            border: 1.5px solid var(--gov-gray-200);
            border-radius: 10px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control::placeholder {
            color: var(--gov-gray-400);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--gov-teal);
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
            padding: 6px;
            font-size: 0.95rem;
            transition: color 0.15s ease;
            border-radius: 6px;
        }

        .password-toggle:hover,
        .password-toggle:focus {
            color: var(--gov-gray-600);
            outline: none;
            background: var(--gov-gray-100);
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            color: #ffffff;
            background: var(--gov-teal);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-login:hover {
            background: var(--gov-teal-hover);
            box-shadow: 0 4px 12px rgba(13, 92, 78, 0.2);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.85rem;
            color: var(--gov-gray-500);
            line-height: 1.6;
        }

        .login-footer a {
            color: var(--gov-teal);
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .login-footer .secure-note {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            font-size: 0.8rem;
            color: var(--gov-gray-400);
            padding-top: 16px;
            border-top: 1px solid var(--gov-gray-100);
        }

        .login-footer .secure-note i {
            font-size: 0.9rem;
            color: var(--gov-gray-400);
        }

        @media (max-width: 991.98px) {
            .login-brand {
                display: none;
            }
            .login-main {
                padding: 24px;
            }
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
    <div class="login-brand">
        <div class="brand-content">
            <div class="brand-seal">
                <i class="fas fa-building-columns"></i>
            </div>
            <h1 class="brand-title">Payroll Management System</h1>
            <p class="brand-subtitle">A centralized platform for accurate, transparent, and efficient payroll processing across all offices.</p>

            <div class="brand-features">
                <div class="brand-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>Centralized payroll database</span>
                </div>
                <div class="brand-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>Office-based filtering</span>
                </div>
                <div class="brand-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>Instant payslip generation</span>
                </div>
                <div class="brand-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>Secure access and audit logging</span>
                </div>
            </div>
        </div>
    </div>

    <div class="login-main">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-title">Sign In</h1>
                <p class="login-subtitle">Enter your credentials to access the system.</p>
            </div>

            <div class="login-alert" id="loginAlert">
                <i class="fas fa-circle-exclamation"></i>
                <span><?= session()->getFlashdata('error') ?></span>
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

    const loginAlert = document.getElementById('loginAlert');
    if (loginAlert && loginAlert.textContent.trim() === '') {
        loginAlert.style.display = 'none';
    }
</script>

</body>
</html>
