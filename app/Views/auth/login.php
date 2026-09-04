<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Payroll Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0f2a4a;
            --navy-light: #1d3f66;
            --navy-subtle: #eef3f9;
            --ink: #1a2233;
            --muted: #6b7a90;
            --border: #dde4ee;
            --white: #ffffff;
            --gold: #c9a227;
            --error: #c0392b;
            --error-bg: #fdf1f0;
            --error-border: #f3d3d0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body { height: 100%; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, #f6f8fb 0%, #edf1f6 100%);
        }

        .login-page {
            display: flex;
            min-height: 100vh;
        }

        /* ---- left: brand panel ---- */
        .login-brand {
            flex: 0 0 420px;
            background: linear-gradient(165deg, var(--navy) 0%, var(--navy-light) 100%);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 56px 44px;
            position: relative;
            overflow: hidden;
        }

        .login-brand::before {
            content: "";
            position: absolute;
            top: -20%;
            right: -30%;
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
            border-radius: 50%;
        }

        .brand-head {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo-img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }

        .brand-logo-text {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            line-height: 1.5;
        }

        .brand-logo-text span {
            opacity: 0.7;
            font-weight: 400;
        }

        .brand-content {
            position: relative;
            z-index: 1;
            margin: 40px 0;
        }

        .brand-title {
            font-size: 1.9rem;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -0.01em;
            margin-bottom: 16px;
        }

        .brand-subtitle {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.72);
            line-height: 1.7;
            max-width: 320px;
        }

        .brand-features {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.88rem;
            color: rgba(255,255,255,0.85);
        }

        .brand-feature .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gold);
            flex-shrink: 0;
        }

        .brand-foot {
            position: relative;
            z-index: 1;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.55);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        /* ---- right: form panel ---- */
        .login-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-card {
            width: 100%;
            max-width: 520px;
            background: var(--white);
            border: 1px solid var(--border);
            padding: 40px 36px;
            box-shadow: 0 1px 2px rgba(15, 42, 74, 0.04), 0 12px 32px rgba(15, 42, 74, 0.08);
        }

        .login-header {
            margin-bottom: 28px;
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--ink);
            margin: 0 0 6px;
            letter-spacing: -0.01em;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: var(--muted);
            margin: 0;
        }

        .login-alert {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error);
            padding: 12px 14px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .login-alert:empty { display: none; }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 7px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            font-size: 0.95rem;
            font-family: inherit;
            color: var(--ink);
            background: var(--white);
            border: 1px solid var(--border);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control::placeholder { color: #aab6c5; }

        .form-control:focus {
            outline: none;
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(29, 63, 102, 0.12);
        }

        .password-field { position: relative; }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 6px;
            font-size: 0.95rem;
        }

        .password-toggle:hover {
            color: var(--navy);
            background: var(--navy-subtle);
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            color: #ffffff;
            background: var(--navy);
            border: none;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.15s ease;
        }

        .btn-login:hover {
            background: var(--navy-light);
        }

        .login-footer {
            text-align: center;
            margin-top: 26px;
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.7;
        }

        .login-footer a {
            color: var(--navy);
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover { text-decoration: underline; }

        .login-footer .secure-note {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            font-size: 0.78rem;
            color: #93a1b4;
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 991.98px) {
            .login-brand { display: none; }
            .login-main { padding: 24px; }
        }

        @media (max-width: 480px) {
            .login-card { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="login-page">
    <div class="login-brand">
        <div class="brand-head">
            <img src="<?= base_url('mahinoglgu.png') ?>" alt="LGU Mahinog Logo" class="brand-logo-img">
            <div class="brand-logo-text">
                Municipality of Mahinog<br>
                <span>Province of Camiguin</span>
            </div>
        </div>

        <div class="brand-content">
            <h1 class="brand-title">Payroll Management System</h1>
            <p class="brand-subtitle">A centralized platform for accurate, transparent, and efficient payroll processing across all offices.</p>
        </div>

        <div class="brand-features">
            <div class="brand-feature"><span class="dot"></span> Centralized payroll database</div>
            <div class="brand-feature"><span class="dot"></span> Office-based filtering</div>
            <div class="brand-feature"><span class="dot"></span> Instant payslip generation</div>
            <div class="brand-feature"><span class="dot"></span> Secure access &amp; audit logging</div>
        </div>

        <div class="brand-foot">© <?= date('Y') ?> LGU-Mahinog</div>
    </div>

    <div class="login-main">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-title">Sign In</h1>
                <p class="login-subtitle">Enter your credentials to access the system.</p>
            </div>

            <div class="login-alert" id="loginAlert">
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
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="login-footer">
                <div>Forgot password? <a href="#">Contact your system administrator</a></div>
                <div class="secure-note">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span>Authorized personnel only. All activity is logged.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.querySelector('.password-toggle');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        }
    }

    const loginAlert = document.getElementById('loginAlert');
    if (loginAlert && loginAlert.textContent.trim() === '') {
        loginAlert.style.display = 'none';
    }
</script>

</body>
</html>
