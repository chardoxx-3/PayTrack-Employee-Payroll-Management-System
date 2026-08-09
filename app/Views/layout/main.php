<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS | Payroll Management System</title>
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
            --sidebar-bg: #241d10;
            --sidebar-bg-2: #2f2515;
            --bg-light: #fffcf5;
        }

        body { font-family: 'IBM Plex Sans', sans-serif; background-color: var(--bg-light); color: var(--ink); }

        /* Sidebar Styles */
        #sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--sidebar-bg-2) 100%);
            min-height: 100vh;
            width: 260px;
            transition: all 0.3s;
            position: fixed;
        }

        #sidebar .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 24px 22px 20px;
        }

        #sidebar .brand-seal {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid var(--gold-500);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        #sidebar .brand-seal i { color: var(--gold-500); font-size: 14px; }

        #sidebar .brand-text h4 {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: 1.05rem;
            color: #fffdf6;
            margin-bottom: 0;
            line-height: 1.1;
        }

        #sidebar .brand-text h4 span { color: var(--gold-500); }

        #sidebar .brand-text small {
            color: #cbb98a;
            font-size: 0.7rem;
            letter-spacing: 0.04em;
        }

        #sidebar .nav-link {
            color: #cbb98a;
            padding: 12px 25px;
            font-weight: 500;
            font-size: 0.92rem;
            border-left: 4px solid transparent;
        }

        #sidebar .nav-link i { width: 25px; color: #a68f5c; }

        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #fffdf6;
            background: rgba(255, 253, 246, 0.06);
            border-left-color: var(--gold-500);
        }

        #sidebar .nav-link:hover i, #sidebar .nav-link.active i { color: var(--gold-500); }

        #sidebar hr { border-color: rgba(255, 253, 246, 0.12); }

        #sidebar .nav-link.text-danger { color: #d98c6b !important; }
        #sidebar .nav-link.text-danger:hover { background: rgba(217, 140, 107, 0.1); border-left-color: #d98c6b; }

        /* Content Wrapper */
        #content { margin-left: 260px; width: calc(100% - 260px); }

        .navbar {
            background: var(--paper);
            border-bottom: 1px solid var(--line);
        }

        .navbar .navbar-text {
            font-family: 'IBM Plex Sans', sans-serif;
            font-weight: 500;
        }

        .navbar .btn-light {
            background: var(--paper-dim);
            border: 1px solid var(--line);
            color: var(--gold-700);
        }

        .navbar .btn-light:hover { background: var(--line); }

        .breadcrumb { font-size: 0.85rem; }

        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; }
            #content { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar" class="d-none d-md-block">
        <div class="brand-row">
            <div class="brand-seal"><i class="fas fa-scale-balanced"></i></div>
            <div class="brand-text">
                <h4>PMS <span>Admin</span></h4>
                <small>Payroll Management System</small>
            </div>
        </div>
        <div class="nav flex-column">
            <a href="/dashboard" class="nav-link <?= url_is('dashboard*') ? 'active' : '' ?>"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="/employee" class="nav-link <?= url_is('employee*') ? 'active' : '' ?>"><i class="fas fa-user-tie"></i> Employees</a>
            <a href="/payroll" class="nav-link <?= url_is('payroll*') ? 'active' : '' ?>"><i class="fas fa-wallet"></i> Payroll</a>
            <a href="/payslip" class="nav-link <?= url_is('payslip*') ? 'active' : '' ?>"><i class="fas fa-file-invoice"></i> Payslips</a>
            <a href="/report" class="nav-link <?= url_is('report*') ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Reports</a>
            <hr class="mx-3">
            <a href="/user" class="nav-link <?= url_is('user*') ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> User Management</a>
            <a href="/auth/logout" class="nav-link text-danger mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg px-4 py-3 sticky-top">
            <div class="container-fluid">
                <span class="navbar-text text-dark fw-medium">Welcome back, <?= session()->get('username') ?></span>
                <div class="ms-auto d-flex align-items-center">
                    <a href="/user/settings" class="btn btn-light btn-sm rounded-circle p-2"><i class="fas fa-cog"></i></a>
                </div>
            </div>
        </nav>

        <div class="p-4">
            <?= $this->renderSection('content') ?>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>