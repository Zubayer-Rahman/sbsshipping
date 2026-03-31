<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SBS Shipping') — Dashboard</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --primary:        #1a56db;
            --primary-dark:   #1340b0;
            --primary-light:  #e8f0fe;
            --primary-glow:   rgba(26,86,219,.15);
            --accent:         #06b6d4;
            --accent-light:   #e0f7fa;
            --success:        #10b981;
            --warning:        #f59e0b;
            --danger:         #ef4444;
            --sidebar-bg:     #0f1f4b;
            --sidebar-text:   rgba(255,255,255,.72);
            --sidebar-active: #1a56db;
            --body-bg:        #f0f4ff;
            --card-bg:        #ffffff;
            --text-primary:   #0f172a;
            --text-muted:     #64748b;
            --border:         #e2e8f0;
            --shadow-sm:      0 1px 3px rgba(15,31,75,.08), 0 1px 2px rgba(15,31,75,.06);
            --shadow-md:      0 4px 16px rgba(15,31,75,.10);
            --shadow-lg:      0 10px 40px rgba(15,31,75,.14);
            --sidebar-w:      260px;
            --topbar-h:       64px;
            --radius:         14px;
            --radius-sm:      8px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--body-bg);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── SIDEBAR ──────────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 20px 24px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-brand .brand-icon {
            width: 40px; height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: #fff;
            box-shadow: 0 4px 12px rgba(26,86,219,.4);
        }
        .sidebar-brand .brand-text {
            font-family: 'Syne', sans-serif;
            font-size: 18px; font-weight: 800;
            color: #fff; line-height: 1;
        }
        .sidebar-brand .brand-text span { color: var(--accent); }
        .sidebar-brand .brand-sub {
            font-size: 10px; color: var(--sidebar-text);
            text-transform: uppercase; letter-spacing: .08em;
        }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section-label {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .12em;
            color: rgba(255,255,255,.3);
            padding: 12px 12px 6px;
        }

        .nav-item { list-style: none; margin-bottom: 2px; }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: var(--radius-sm);
            color: var(--sidebar-text);
            text-decoration: none; font-size: 14px; font-weight: 500;
            transition: all .2s ease;
            position: relative;
        }
        .nav-link i { font-size: 18px; width: 20px; text-align: center; }
        .nav-link:hover {
            background: rgba(255,255,255,.07);
            color: #fff;
        }
        .nav-link.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(26,86,219,.35);
        }
        .nav-link .badge-count {
            margin-left: auto;
            background: var(--accent);
            color: #fff; font-size: 10px; font-weight: 700;
            padding: 2px 7px; border-radius: 20px;
        }

        /* Sub-menu */
        .nav-submenu { display: none; padding-left: 20px; }
        .nav-submenu.open { display: block; }
        .nav-submenu .nav-link {
            font-size: 13px; padding: 8px 12px;
            color: rgba(255,255,255,.55);
        }
        .nav-submenu .nav-link::before {
            content: '';
            width: 5px; height: 5px;
            background: currentColor;
            border-radius: 50%; flex-shrink: 0;
        }
        .nav-submenu .nav-link.active,
        .nav-submenu .nav-link:hover { color: #fff; background: rgba(255,255,255,.07); }
        .nav-submenu .nav-link.active { background: rgba(26,86,219,.5); }

        .nav-arrow {
            margin-left: auto; font-size: 12px;
            transition: transform .2s;
        }
        .nav-item.open > .nav-link .nav-arrow { transform: rotate(90deg); }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background .2s;
        }
        .sidebar-user:hover { background: rgba(255,255,255,.07); }
        .user-avatar {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: #fff;
        }
        .user-info .user-name { font-size: 13px; font-weight: 600; color: #fff; }
        .user-info .user-role { font-size: 11px; color: var(--sidebar-text); }

        /* ── MAIN CONTENT ──────────────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ──────────────────────────────────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky; top: 0; z-index: 50;
            box-shadow: var(--shadow-sm);
        }
        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-size: 18px; font-weight: 700;
            color: var(--text-primary);
        }
        .topbar-breadcrumb {
            font-size: 13px; color: var(--text-muted);
            display: flex; align-items: center; gap: 6px;
        }
        .topbar-breadcrumb span { color: var(--primary); }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }

        .topbar-btn {
            width: 38px; height: 38px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: transparent; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); font-size: 18px;
            transition: all .2s;
        }
        .topbar-btn:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary); }

        .topbar-date {
            font-size: 13px; color: var(--text-muted); font-weight: 500;
        }

        /* ── PAGE CONTENT ────────────────────────────────────────────────── */
        .page-content {
            flex: 1;
            padding: 28px;
        }

        /* ── CARDS ───────────────────────────────────────────────────────── */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }
        .card-header {
            padding: 18px 22px 0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title {
            font-size: 15px; font-weight: 700;
            color: var(--text-primary);
        }
        .card-body { padding: 20px 22px; }

        /* ── STAT BOXES ──────────────────────────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 22px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px;
        }
        .stat-card.blue::before   { background: linear-gradient(90deg, var(--primary), var(--accent)); }
        .stat-card.green::before  { background: linear-gradient(90deg, var(--success), #34d399); }
        .stat-card.orange::before { background: linear-gradient(90deg, var(--warning), #fbbf24); }
        .stat-card.red::before    { background: linear-gradient(90deg, var(--danger), #f97316); }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 14px;
        }
        .stat-card.blue   .stat-icon { background: var(--primary-light); color: var(--primary); }
        .stat-card.green  .stat-icon { background: #d1fae5; color: var(--success); }
        .stat-card.orange .stat-icon { background: #fef3c7; color: var(--warning); }
        .stat-card.red    .stat-icon { background: #fee2e2; color: var(--danger); }

        .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 26px; font-weight: 800;
            color: var(--text-primary); line-height: 1;
        }
        .stat-label {
            font-size: 13px; color: var(--text-muted);
            margin-top: 4px; font-weight: 500;
        }
        .stat-change {
            margin-top: 10px; font-size: 12px; font-weight: 600;
            display: flex; align-items: center; gap: 4px;
        }
        .stat-change.up   { color: var(--success); }
        .stat-change.down { color: var(--danger); }

        /* ── CHARTS GRID ─────────────────────────────────────────────────── */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }
        .charts-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px;
            margin-bottom: 28px;
        }

        /* ── TABLE ───────────────────────────────────────────────────────── */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: var(--body-bg);
            padding: 10px 14px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            color: var(--text-muted);
            text-align: left; border-bottom: 1px solid var(--border);
        }
        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }
        tbody tr:hover { background: var(--body-bg); }
        tbody td {
            padding: 12px 14px;
            font-size: 13.5px; color: var(--text-primary);
        }
        tbody tr:last-child { border-bottom: none; }

        /* ── BADGES ──────────────────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            text-transform: capitalize;
        }
        .badge::before {
            content: ''; width: 6px; height: 6px;
            border-radius: 50%; background: currentColor;
        }
        .badge-pending    { background: #fef3c7; color: #92400e; }
        .badge-in-transit { background: #dbeafe; color: #1e40af; }
        .badge-delivered  { background: #d1fae5; color: #065f46; }
        .badge-cancelled  { background: #fee2e2; color: #991b1b; }

        /* ── FORMS ───────────────────────────────────────────────────────── */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }

        .form-label {
            font-size: 13px; font-weight: 600;
            color: var(--text-primary);
        }
        .form-label .opt {
            font-weight: 400; color: var(--text-muted); font-size: 11px;
        }
        .form-control, .form-select {
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit; font-size: 14px;
            color: var(--text-primary);
            background: var(--card-bg);
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
        textarea.form-control { resize: vertical; min-height: 90px; }

        /* ── BUTTONS ─────────────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: var(--radius-sm);
            font-family: inherit; font-size: 14px; font-weight: 600;
            cursor: pointer; border: none; text-decoration: none;
            transition: all .2s; white-space: nowrap;
        }
        .btn-primary {
            background: var(--primary); color: #fff;
            box-shadow: 0 4px 12px rgba(26,86,219,.3);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 6px 18px rgba(26,86,219,.4);
            transform: translateY(-1px);
        }
        .btn-outline {
            background: transparent; color: var(--primary);
            border: 1.5px solid var(--primary);
        }
        .btn-outline:hover { background: var(--primary-light); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 6px 14px; font-size: 12px; }

        /* ── ALERTS ──────────────────────────────────────────────────────── */
        .alert {
            padding: 12px 16px; border-radius: var(--radius-sm);
            margin-bottom: 20px; font-size: 14px;
            display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }

        /* ── PAGINATION ──────────────────────────────────────────────────── */
        .pagination-wrapper { padding: 16px 22px; border-top: 1px solid var(--border); }

        /* ── RESPONSIVE ──────────────────────────────────────────────────── */
        @media (max-width: 1100px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .charts-grid { grid-template-columns: 1fr; }
            .charts-grid-3 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
            .charts-grid-3 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- ── SIDEBAR ────────────────────────────────────────────────────────────── -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-tsunami"></i></div>
        <div>
            <div class="brand-text"><span>SBS</span> Shipping</div>
            <div class="brand-sub">Management System</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul style="list-style:none">
            <li class="nav-section-label">Main</li>

            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>

            <li class="nav-section-label">Inventory</li>

            <li class="nav-item {{ request()->routeIs('items.*') ? 'open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}"
                   onclick="toggleSubmenu(this); return false;">
                    <i class="bi bi-box-seam"></i> Items
                    <i class="bi bi-chevron-right nav-arrow"></i>
                </a>
                <ul class="nav-submenu {{ request()->routeIs('items.*') ? 'open' : '' }}">
                    <li class="nav-item">
                        <a href="{{ route('items.list') }}" class="nav-link {{ request()->routeIs('items.list') ? 'active' : '' }}">
                            List Items
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('items.create') }}" class="nav-link {{ request()->routeIs('items.create') ? 'active' : '' }}">
                            Add Item
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ request()->routeIs('jobs.*') ? 'open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}"
                   onclick="toggleSubmenu(this); return false;">
                    <i class="bi bi-briefcase"></i> Jobs Manager
                    <i class="bi bi-chevron-right nav-arrow"></i>
                </a>
                <ul class="nav-submenu {{ request()->routeIs('jobs.*') ? 'open' : '' }}">
                    <li class="nav-item">
                        <a href="{{ route('jobs.create') }}" class="nav-link {{ request()->routeIs('jobs.create') ? 'active' : '' }}">
                            Create Job
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('jobs.list') }}" class="nav-link {{ request()->routeIs('jobs.list') ? 'active' : '' }}">
                            Jobs List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('jobs.forwarding') }}" class="nav-link {{ request()->routeIs('jobs.forwarding') ? 'active' : '' }}">
                            Forwarding
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-left:auto">
                @csrf
                <button type="submit" class="topbar-btn" title="Logout" style="background:transparent;border:none;color:rgba(255,255,255,.4);font-size:18px;cursor:pointer;padding:4px">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- ── MAIN WRAPPER ───────────────────────────────────────────────────────── -->
<div class="main-wrapper">
    <!-- Topbar -->
    <header class="topbar">
        <button class="topbar-btn" id="sidebarToggle" style="display:none">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-breadcrumb">
                <span>SBS</span> / @yield('breadcrumb', 'Dashboard')
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date" id="topbar-date"></div>
            <button class="topbar-btn"><i class="bi bi-bell"></i></button>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
    // Date in topbar
    const d = new Date();
    document.getElementById('topbar-date').textContent =
        d.toLocaleDateString('en-GB', { weekday:'short', day:'2-digit', month:'short', year:'numeric' });

    // Submenu toggle
    function toggleSubmenu(el) {
        const li = el.closest('.nav-item');
        const sub = li.querySelector('.nav-submenu');
        li.classList.toggle('open');
        sub.classList.toggle('open');
    }

    // Sidebar mobile toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle) {
        sidebarToggle.style.display = '';
        sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    }
</script>
@stack('scripts')
</body>
</html>