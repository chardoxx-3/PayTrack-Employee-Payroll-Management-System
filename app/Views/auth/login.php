<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Payroll Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #201a0e;
            --ink-soft: #574a30;
            --paper: #fffcf5;
            --paper-dim: #f7f1df;
            --line: #e7dcc0;
            --gold-500: #c99a2e;
            --gold-600: #b3860e;
            --gold-700: #9c6b0e;
            --gold-800: #7a530c;
            --gold-900: #5c3f0f;
            --seal: #2f5233;
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            color: var(--ink);
            background: var(--paper);
        }

        .pms-screen {
            display: flex;
            min-height: 100vh;
        }

        /* ---------- LEFT: SIGN-IN ---------- */
        .pms-main {
            flex: 0 0 42%;
            max-width: 42%;
            background: var(--paper);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4vw 5vw;
            position: relative;
        }

        .pms-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
        }

        .pms-seal {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid var(--gold-700);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pms-seal i { color: var(--gold-700); font-size: 16px; }

        .pms-brand-text .name {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: 1.15rem;
            line-height: 1.1;
            letter-spacing: 0.01em;
        }

        .pms-brand-text .tag {
            font-size: 0.72rem;
            color: var(--ink-soft);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .pms-form-wrap { max-width: 380px; }

        .pms-eyebrow {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gold-700);
            margin-bottom: 10px;
        }

        .pms-form-wrap h1 {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: 2.1rem;
            line-height: 1.15;
            margin-bottom: 8px;
        }

        .pms-form-wrap p.pms-sub {
            color: var(--ink-soft);
            font-size: 0.94rem;
            margin-bottom: 30px;
        }

        .pms-alert {
            border: 1px solid #e0b4b4;
            background: #fbe9e9;
            color: #8a2c2c;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .pms-field { margin-bottom: 18px; }

        .pms-field label {
            display: block;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--ink-soft);
            margin-bottom: 6px;
        }

        .pms-input-wrap { position: relative; }

        .pms-field input {
            width: 100%;
            padding: 13px 14px;
            border: 1.5px solid var(--line);
            border-radius: 9px;
            background: #fffefb;
            font-family: 'IBM Plex Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--ink);
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .pms-field input:focus {
            outline: none;
            border-color: var(--gold-600);
            box-shadow: 0 0 0 3px rgba(201, 154, 46, 0.18);
        }

        .pms-toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--ink-soft);
            cursor: pointer;
            padding: 4px;
        }

        .pms-submit {
            width: 100%;
            border: none;
            border-radius: 9px;
            padding: 13px;
            font-weight: 600;
            font-size: 0.98rem;
            letter-spacing: 0.01em;
            color: #fffdf7;
            background: linear-gradient(180deg, var(--gold-600), var(--gold-800));
            box-shadow: 0 6px 16px rgba(156, 107, 14, 0.28);
            margin-top: 8px;
            transition: transform .12s ease, box-shadow .12s ease;
        }

        .pms-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(156, 107, 14, 0.34);
        }

        .pms-footnote {
            text-align: center;
            font-size: 0.85rem;
            color: var(--ink-soft);
            margin-top: 22px;
        }

        .pms-footnote a { color: var(--gold-700); font-weight: 600; text-decoration: none; }
        .pms-footnote a:hover { text-decoration: underline; }

        .pms-secure {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            color: var(--ink-soft);
            margin-top: 40px;
        }

        .pms-secure i { color: var(--seal); }

        /* ---------- RIGHT: CONTEXT PANEL ---------- */
        .pms-side {
            flex: 1;
            position: relative;
            background:
                repeating-linear-gradient(
                    to bottom,
                    rgba(255,255,255,0.05) 0px,
                    rgba(255,255,255,0.05) 1px,
                    transparent 1px,
                    transparent 29px
                ),
                linear-gradient(155deg, #c99228 0%, #9c6b0e 48%, #5c3f0f 100%);
            color: #fffdf6;
            padding: 5vw 4.5vw;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .pms-side::before {
            content: "";
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.10), transparent 70%);
            top: -140px;
            right: -140px;
        }

        .pms-side-inner { position: relative; max-width: 560px; z-index: 1; }

        .pms-side-eyebrow {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #f4e4b8;
            margin-bottom: 14px;
        }

        .pms-side h2 {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: 2.4rem;
            line-height: 1.14;
            margin-bottom: 14px;
            max-width: 480px;
        }

        .pms-side p.pms-side-sub {
            font-size: 0.98rem;
            color: #f6ead0;
            max-width: 460px;
            margin-bottom: 34px;
            line-height: 1.55;
        }

        /* Mock payslip stub — signature element */
        .pms-stub {
            background: var(--paper);
            color: var(--ink);
            border-radius: 12px;
            padding: 20px 22px;
            width: 300px;
            box-shadow: 0 18px 40px rgba(0,0,0,0.28);
            position: relative;
            margin-bottom: 38px;
            transform: rotate(-2deg);
        }

        .pms-stub::before, .pms-stub::after {
            content: "";
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--paper-dim);
            top: 50%;
            transform: translateY(-50%);
        }

        .pms-stub::before { left: -9px; }
        .pms-stub::after { right: -9px; }

        .pms-stub-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px dashed var(--line);
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .pms-stub-name {
            font-weight: 600;
            font-size: 0.98rem;
        }

        .pms-stub-role {
            font-size: 0.75rem;
            color: var(--ink-soft);
        }

        .pms-stamp {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            border: 2px dashed var(--seal);
            color: var(--seal);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 0.55rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            transform: rotate(-14deg);
            flex-shrink: 0;
            line-height: 1.1;
            padding: 4px;
        }

        .pms-stub-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            color: var(--ink-soft);
            margin-bottom: 6px;
        }

        .pms-stub-row span:last-child {
            font-family: 'IBM Plex Mono', monospace;
            color: var(--ink);
        }

        .pms-stub-net {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            border-top: 1px dashed var(--line);
            margin-top: 10px;
            padding-top: 10px;
        }

        .pms-stub-net .label {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--ink-soft);
        }

        .pms-stub-net .amount {
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--gold-800);
        }

        /* Feature checklist */
        .pms-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .pms-feature {
            background: rgba(255, 253, 246, 0.96);
            color: var(--ink);
            border-radius: 9px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.82rem;
            font-weight: 500;
            line-height: 1.25;
        }

        .pms-feature i {
            color: var(--gold-700);
            font-size: 0.95rem;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {
            .pms-side { display: none; }
            .pms-main { flex: 1; max-width: 100%; }
        }

        @media (max-width: 480px) {
            .pms-main { padding: 8vw 6vw; }
        }
    </style>
</head>
<body>

<div class="pms-screen">
    <!-- LEFT: SIGN-IN -->
    <div class="pms-main">
        <div class="pms-brand">
            <div class="pms-seal"><i class="fas fa-scale-balanced"></i></div>
            <div class="pms-brand-text">
                <div class="name">PMS</div>
                <div class="tag">Payroll Management System</div>
            </div>
        </div>

        <div class="pms-form-wrap">
            <div class="pms-eyebrow">Staff Sign-In</div>
            <h1>Welcome back.</h1>
            <p class="pms-sub">Sign in with your assigned credentials to access payroll records.</p>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="pms-alert"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('auth/authenticate') ?>" method="post">
                <div class="pms-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="pms-field">
                    <label for="password">Password</label>
                    <div class="pms-input-wrap">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <button type="button" class="pms-toggle-pass" onclick="pmsTogglePassword()" aria-label="Show password">
                            <i class="fas fa-eye" id="pmsEyeIcon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="pms-submit">Sign In</button>
            </form>

            <div class="pms-footnote">Forgot password? <a href="#">Contact your system administrator</a></div>

            <div class="pms-secure">
                <i class="fas fa-shield-halved"></i>
                Authorized personnel only. All activity is logged.
            </div>
        </div>
    </div>

    <!-- RIGHT: SYSTEM CONTEXT -->
    <div class="pms-side">
        <div class="pms-side-inner">
            <div class="pms-side-eyebrow">Payroll Management System</div>
            <h2>One record for every payslip, every office.</h2>
            <p class="pms-side-sub">
                Built to replace scattered Excel worksheets with a single, searchable
                payroll system — so no one has to hunt across tabs to build a payslip again.
            </p>

            <div class="pms-stub">
                <div class="pms-stub-top">
                    <div>
                        <div class="pms-stub-name">Maria D. Santos</div>
                        <div class="pms-stub-role">Administrative Officer II · Office of the Treasurer</div>
                    </div>
                    <div class="pms-stamp">Verified<br>Copy</div>
                </div>
                <div class="pms-stub-row"><span>Pay Period</span><span>Jul 1–15, 2026</span></div>
                <div class="pms-stub-row"><span>Gross Pay</span><span>₱ 24,150.00</span></div>
                <div class="pms-stub-row"><span>Deductions</span><span>− ₱ 5,697.70</span></div>
                <div class="pms-stub-net">
                    <span class="label">Net Pay</span>
                    <span class="amount">₱ 18,452.30</span>
                </div>
            </div>

            <div class="pms-features">
                <div class="pms-feature"><i class="fas fa-database"></i>Centralized payroll database</div>
                <div class="pms-feature"><i class="fas fa-building-columns"></i>Office-based filtering</div>
                <div class="pms-feature"><i class="fas fa-magnifying-glass"></i>Employee search</div>
                <div class="pms-feature"><i class="fas fa-file-invoice-dollar"></i>Instant payslip generation</div>
                <div class="pms-feature"><i class="fas fa-print"></i>Batch payslip printing</div>
                <div class="pms-feature"><i class="fas fa-percent"></i>Monthly deduction management</div>
            </div>
        </div>
    </div>
</div>

<script>
    function pmsTogglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('pmsEyeIcon');
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
</script>
</body>
</html>