<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SBS Shipping') — Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #1340b0;
            --primary-light: #e8f0fe;
            --primary-glow: rgba(26, 86, 219, .15);
            --accent: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --sidebar-bg: #0f1f4b;
            --sidebar-text: rgba(255, 255, 255, .72);
            --body-bg: #f0f4ff;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(15, 31, 75, .08), 0 1px 2px rgba(15, 31, 75, .06);
            --shadow-md: 0 4px 16px rgba(15, 31, 75, .10);
            --sidebar-expanded: 260px;
            --sidebar-collapsed: 68px;
            --topbar-h: 64px;
            --radius: 14px;
            --radius-sm: 8px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── SIDEBAR ───────────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-expanded);
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: width .28s cubic-bezier(.4, 0, .2, 1);
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        /* Brand */
        .sidebar-brand {
            padding: 0 14px;
            height: 64px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 4px 12px rgba(26, 86, 219, .4);
            flex-shrink: 0;
        }

        .brand-text-wrap {
            overflow: hidden;
            white-space: nowrap;
            transition: opacity .2s, width .28s;
        }

        .collapsed .brand-text-wrap {
            opacity: 0;
            width: 0;
        }

        .brand-text {
            font-family: 'Inter', sans-serif;
            font-size: 17px;
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
        }

        .brand-text span {
            color: var(--accent)
        }

        .brand-sub {
            font-size: 10px;
            color: var(--sidebar-text);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        /* Toggle button */
        .sidebar-toggle {
            margin-left: auto;
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .08);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, .7);
            font-size: 16px;
            transition: background .2s, transform .28s;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, .15);
            color: #fff;
        }

        .collapsed .sidebar-toggle {
            transform: rotate(180deg);
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            padding: 12px 8px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .12);
            border-radius: 2px;
        }

        .nav-item {
            list-style: none;
            margin-bottom: 2px;
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: background .2s, color .2s;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-link i {
            font-size: 18px;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link-label {
            flex: 1;
            transition: opacity .2s;
            overflow: hidden;
            white-space: nowrap;
        }

        .collapsed .nav-link-label,
        .collapsed .nav-arrow,
        .collapsed .nav-submenu {
            opacity: 0;
            pointer-events: none;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, .07);
            color: #fff;
        }

        .nav-link.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(26, 86, 219, .35);
        }

        /* Tooltip when collapsed */
        .collapsed .nav-item:not(.has-submenu)>.nav-link::after,
        .collapsed .nav-item.has-submenu>.nav-link::after {
            content: attr(data-label);
            position: fixed;
            left: calc(var(--sidebar-collapsed) + 8px);
            background: #1e3a8a;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .2);
            z-index: 9999;
            transition: opacity .15s;
        }

        .collapsed .nav-item>.nav-link:hover::after {
            opacity: 1;
        }

        /* Submenu */
        .nav-submenu {
            display: none;
            padding-left: 20px;
            transition: opacity .2s;
        }

        .nav-submenu.open {
            display: block;
        }

        .nav-submenu .nav-link {
            font-size: 13px;
            padding: 7px 12px;
            color: rgba(255, 255, 255, .55);
        }

        .nav-submenu .nav-link::before {
            content: '';
            width: 5px;
            height: 5px;
            background: currentColor;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .nav-submenu .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, .07);
        }

        .nav-submenu .nav-link.active {
            color: #fff;
            background: rgba(26, 86, 219, .5);
        }

        .nav-arrow {
            margin-left: auto;
            font-size: 11px;
            flex-shrink: 0;
            transition: transform .2s, opacity .2s;
        }

        .nav-item.open>.nav-link .nav-arrow {
            transform: rotate(90deg);
        }

        /* Footer */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 6px;
            border-radius: var(--radius-sm);
            transition: background .2s;
        }

        .sidebar-user:hover {
            background: rgba(255, 255, 255, .07);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info-wrap {
            overflow: hidden;
            white-space: nowrap;
            transition: opacity .2s, width .28s;
            flex: 1;
        }

        .collapsed .user-info-wrap {
            opacity: 0;
            width: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
        }

        .user-role {
            font-size: 11px;
            color: var(--sidebar-text);
        }

        .logout-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, .4);
            font-size: 17px;
            cursor: pointer;
            padding: 4px;
            flex-shrink: 0;
            transition: color .2s, opacity .2s;
        }

        .collapsed .logout-btn {
            opacity: 0;
            pointer-events: none;
        }

        .logout-btn:hover {
            color: var(--danger);
        }

        /* ── MAIN ──────────────────────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-expanded);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
            transition: margin-left .28s cubic-bezier(.4, 0, .2, 1);
        }

        .main-wrapper.sidebar-collapsed {
            margin-left: var(--sidebar-collapsed);
        }

        .topbar {
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--shadow-sm);
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .topbar-breadcrumb {
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .topbar-breadcrumb span {
            color: var(--primary);
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-btn {
            width: 38px;
            height: 38px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 18px;
            transition: all .2s;
        }

        .topbar-btn:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary);
        }

        .topbar-date {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .page-content {
            flex: 1;
            padding: 28px;
            min-width: 0;
        }

        /* ── CARDS ──────────────────────────────────────────────────────── */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .card-header {
            padding: 18px 22px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-body {
            padding: 20px 22px;
        }

        /* ── STATS ──────────────────────────────────────────────────────── */
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
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card.blue::before {
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .stat-card.green::before {
            background: linear-gradient(90deg, var(--success), #34d399);
        }

        .stat-card.orange::before {
            background: linear-gradient(90deg, var(--warning), #fbbf24);
        }

        .stat-card.red::before {
            background: linear-gradient(90deg, var(--danger), #f97316);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 14px;
        }

        .stat-card.blue .stat-icon {
            background: var(--primary-light);
            color: var(--primary);
        }

        .stat-card.green .stat-icon {
            background: #d1fae5;
            color: var(--success);
        }

        .stat-card.orange .stat-icon {
            background: #fef3c7;
            color: var(--warning);
        }

        .stat-card.red .stat-icon {
            background: #fee2e2;
            color: var(--danger);
        }

        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
        }

        .stat-change {
            margin-top: 10px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-change.up {
            color: var(--success);
        }

        .stat-change.down {
            color: var(--danger);
        }

        /* ── CHARTS ─────────────────────────────────────────────────────── */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        /* ── TABLE ──────────────────────────────────────────────────────── */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: var(--body-bg);
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        tbody tr:hover {
            background: var(--body-bg);
        }

        tbody td {
            padding: 12px 14px;
            font-size: 13.5px;
            color: var(--text-primary);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        /* ── FORMS ──────────────────────────────────────────────────────── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full {
            grid-column: 1/-1;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-control,
        .form-select {
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            color: var(--text-primary);
            background: var(--card-bg);
            transition: border-color .2s, box-shadow .2s;
            outline: none;
            width: 100%;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 90px;
        }

        /* ── BUTTONS ─────────────────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .2s;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(26, 86, 219, .3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 1.5px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary-light);
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 12px;
        }

        /* ── ALERTS ──────────────────────────────────────────────────────── */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ── PAGINATION ──────────────────────────────────────────────────── */
        .pagination-wrapper {
            padding: 16px 22px;
            border-top: 1px solid var(--border);
        }

        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .pagination {
            display: flex;
            gap: 4px;
            list-style: none;
            margin: 0;
        }

        .pagination li a,
        .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid var(--border);
            color: var(--text-muted);
            text-decoration: none;
            transition: all .15s;
        }

        .pagination li a:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .pagination li.active span {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* ── RESPONSIVE ──────────────────────────────────────────────────── */
        @media(max-width:1100px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width:768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-expanded) !important;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-wrapper,
            .main-wrapper.sidebar-collapsed {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .form-grid,
            .form-grid-3 {
                grid-template-columns: 1fr;
            }

            #desktopToggle {
                display: none !important;
            }

            #mobileToggle {
                display: flex !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <aside class="sidebar" id="sidebar">

        {{-- Brand + toggle --}}
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-tsunami"></i></div>
            <div class="brand-text-wrap">
                <div class="brand-text"><span>SBS</span> Shipping</div>
                <div class="brand-sub">Management System</div>
            </div>
            <button class="sidebar-toggle" id="desktopToggle" title="Toggle sidebar" onclick="toggleSidebar()">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav">
            <ul style="list-style:none">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        data-label="Dashboard">
                        <i class="bi bi-grid-1x2"></i>
                        <span class="nav-link-label">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item has-submenu {{ request()->routeIs('contacts.*') ? 'open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}"
                        data-label="Contacts"
                        onclick="toggleSubmenu(this);return false;">
                        <i class="bi bi-people"></i>
                        <span class="nav-link-label">Contacts</span>
                        <i class="bi bi-chevron-right nav-arrow"></i>
                    </a>
                    <ul class="nav-submenu {{ request()->routeIs('contacts.*') ? 'open' : '' }}">
                        <li class="nav-item">
                            <a href="{{ route('contacts.index', 'supplier') }}"
                                class="nav-link {{ (request()->routeIs('contacts.index') && request('type')=='supplier') ? 'active' : '' }}">
                                <span class="nav-link-label">Suppliers</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('contacts.index', 'client') }}"
                                class="nav-link {{ (request()->routeIs('contacts.index') && request('type')=='client') ? 'active' : '' }}">
                                <span class="nav-link-label">Clients</span>
                            </a>
                        </li>
                    </ul>
                </li>

                @php $jobsActive = request()->routeIs('jobs.*') || request()->routeIs('forwarding.*'); @endphp
                <li class="nav-item has-submenu {{ $jobsActive ? 'open' : '' }}">
                    <a href="#" class="nav-link {{ $jobsActive ? 'active' : '' }}"
                        data-label="Jobs Manager"
                        onclick="toggleSubmenu(this);return false;">
                        <i class="bi bi-briefcase"></i>
                        <span class="nav-link-label">Jobs Manager</span>
                        <i class="bi bi-chevron-right nav-arrow"></i>
                    </a>
                    <ul class="nav-submenu {{ $jobsActive ? 'open' : '' }}">
                        <li class="nav-item"><a href="{{ route('jobs.create') }}" class="nav-link {{ request()->routeIs('jobs.create')     ?'active':'' }}"><span class="nav-link-label">Create Job</span></a></li>
                        <li class="nav-item"><a href="{{ route('jobs.list') }}" class="nav-link {{ request()->routeIs('jobs.list')       ?'active':'' }}"><span class="nav-link-label">Jobs List</span></a></li>
                        <li class="nav-item"><a href="{{ route('jobs.forwarding') }}" class="nav-link {{ request()->routeIs('jobs.forwarding') ?'active':'' }}"><span class="nav-link-label">Forwarding</span></a></li>
                        <li class="nav-item"><a href="{{ route('forwarding.list') }}" class="nav-link {{ request()->routeIs('forwarding.list') ?'active':'' }}"><span class="nav-link-label">Forwarding List</span></a></li>
                    </ul>
                </li>

                <li class="nav-item has-submenu {{ request()->routeIs('items.*') ? 'open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}"
                        data-label="Items"
                        onclick="toggleSubmenu(this);return false;">
                        <i class="bi bi-box-seam"></i>
                        <span class="nav-link-label">Items</span>
                        <i class="bi bi-chevron-right nav-arrow"></i>
                    </a>
                    <ul class="nav-submenu {{ request()->routeIs('items.*') ? 'open' : '' }}">
                        <li class="nav-item"><a href="{{ route('items.list') }}" class="nav-link {{ request()->routeIs('items.list')   ?'active':'' }}"><span class="nav-link-label">List Items</span></a></li>
                        <li class="nav-item"><a href="{{ route('items.create') }}" class="nav-link {{ request()->routeIs('items.create') ?'active':'' }}"><span class="nav-link-label">Add Item</span></a></li>
                    </ul>
                </li>

                @php $expActive = request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') || request()->routeIs('purchases.*'); @endphp
                <li class="nav-item has-submenu {{ $expActive ? 'open' : '' }}">
                    <a href="#" class="nav-link {{ $expActive ? 'active' : '' }}"
                        data-label="Expenses"
                        onclick="toggleSubmenu(this);return false;">
                        <i class="bi bi-receipt"></i>
                        <span class="nav-link-label">Expenses</span>
                        <i class="bi bi-chevron-right nav-arrow"></i>
                    </a>
                    <ul class="nav-submenu {{ $expActive ? 'open' : '' }}">
                        <li class="nav-item"><a href="{{ route('expenses.list') }}" class="nav-link {{ request()->routeIs('expenses.list')           ?'active':'' }}"><span class="nav-link-label">List Expenses</span></a></li>
                        <li class="nav-item"><a href="{{ route('expenses.create') }}" class="nav-link {{ request()->routeIs('expenses.create')         ?'active':'' }}"><span class="nav-link-label">Add Expense</span></a></li>
                        <li class="nav-item"><a href="{{ route('expense-categories.list') }}" class="nav-link {{ request()->routeIs('expense-categories.list') ?'active':'' }}"><span class="nav-link-label">Expense Categories</span></a></li>
                        <li class="nav-item"><a href="{{ route('purchases.list') }}" class="nav-link {{ request()->routeIs('purchases.list')          ?'active':'' }}"><span class="nav-link-label">List Purchases</span></a></li>
                        <li class="nav-item"><a href="{{ route('purchases.create') }}" class="nav-link {{ request()->routeIs('purchases.create')        ?'active':'' }}"><span class="nav-link-label">Add Purchase</span></a></li>
                    </ul>
                </li>

                @if(Route::has('ious.index'))
                <li class="nav-item has-submenu {{ request()->routeIs('ious.*') ? 'open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('ious.*') ? 'active' : '' }}"
                        data-label="IOU Management"
                        onclick="toggleSubmenu(this);return false;">
                        <i class="bi bi-wallet2"></i>
                        <span class="nav-link-label">IOU Management</span>
                        <i class="bi bi-chevron-right nav-arrow"></i>
                    </a>
                    <ul class="nav-submenu {{ request()->routeIs('ious.*') ? 'open' : '' }}">
                        <li class="nav-item"><a href="{{ route('ious.index') }}" class="nav-link {{ request()->routeIs('ious.index','ious.show') ?'active':'' }}"><span class="nav-link-label">List IOUs</span></a></li>
                        <li class="nav-item"><a href="{{ route('ious.create') }}" class="nav-link {{ request()->routeIs('ious.create')           ?'active':'' }}"><span class="nav-link-label">Add IOU</span></a></li>
                        <li class="nav-item"><a href="{{ route('ious.release-list') }}" class="nav-link {{ request()->routeIs('ious.release-list')     ?'active':'' }}"><span class="nav-link-label">IOU Release List</span></a></li>
                    </ul>
                </li>
                @endif

            </ul>
        </nav>

        {{-- Footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                <div class="user-info-wrap">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;flex-shrink:0">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <div class="main-wrapper" id="mainWrapper">
        <header class="topbar">
            {{-- Mobile hamburger --}}
            <button class="topbar-btn" id="mobileToggle" style="display:none" onclick="toggleMobile()">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <div class="topbar-title">@yield('page-title','Dashboard')</div>
                <div class="topbar-breadcrumb"><span>SBS</span> / @yield('breadcrumb','Dashboard')</div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date" id="topbar-date"></div>
                <button class="topbar-btn"><i class="bi bi-bell"></i></button>
            </div>
        </header>

        <main class="page-content">
            @if(session('success'))
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    <script>
        // ── Date ──────────────────────────────────────────────────────────────────────
        const d = new Date();
        document.getElementById('topbar-date').textContent =
            d.toLocaleDateString('en-GB', {
                weekday: 'short',
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });

        // ── Sidebar collapse (desktop) ───────────────────────────────────────────────
        const sidebar = document.getElementById('sidebar');
        const mainWrapper = document.getElementById('mainWrapper');

        function applySidebarState(collapsed) {
            if (collapsed) {
                sidebar.classList.add('collapsed');
                mainWrapper.classList.add('sidebar-collapsed');
            } else {
                sidebar.classList.remove('collapsed');
                mainWrapper.classList.remove('sidebar-collapsed');
            }
        }

        function toggleSidebar() {
            const isCollapsed = sidebar.classList.contains('collapsed');
            applySidebarState(!isCollapsed);
            localStorage.setItem('sidebarCollapsed', !isCollapsed);
        }

        // Restore saved state on load
        (function() {
            const saved = localStorage.getItem('sidebarCollapsed');
            if (saved === 'true') applySidebarState(true);
        })();

        // ── Mobile toggle ─────────────────────────────────────────────────────────────
        function toggleMobile() {
            sidebar.classList.toggle('mobile-open');
        }
        // Close on outside click (mobile)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 &&
                !sidebar.contains(e.target) &&
                !document.getElementById('mobileToggle').contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });

        // ── Submenu toggle ────────────────────────────────────────────────────────────
        function toggleSubmenu(el) {
            // If sidebar is collapsed on desktop, expand it first
            if (sidebar.classList.contains('collapsed') && window.innerWidth > 768) {
                applySidebarState(false);
                localStorage.setItem('sidebarCollapsed', false);
                setTimeout(() => {
                    _doToggleSubmenu(el);
                }, 300);
                return;
            }
            _doToggleSubmenu(el);
        }

        function _doToggleSubmenu(el) {
            const li = el.closest('.nav-item');
            const sub = li.querySelector('.nav-submenu');
            li.classList.toggle('open');
            if (sub) sub.classList.toggle('open');
        }
    </script>
    @stack('scripts')
</body>

</html>