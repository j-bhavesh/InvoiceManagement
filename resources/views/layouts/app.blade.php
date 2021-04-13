<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Invoice Management') | {{ config('app.name') }}</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background-color: #f4f6f9; }

        /* Sidebar */
        #sidebar {
            min-height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, #1a1f36 0%, #2d3561 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            transition: width 0.3s;
        }
        #sidebar .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        #sidebar .sidebar-brand h4 {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
        }
        #sidebar .sidebar-brand small {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
        }
        #sidebar .nav-item .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            transition: all 0.2s;
            border-radius: 0;
        }
        #sidebar .nav-item .nav-link:hover,
        #sidebar .nav-item .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.12);
            text-decoration: none;
        }
        #sidebar .nav-item .nav-link i {
            width: 18px;
            text-align: center;
        }
        #sidebar .nav-section {
            padding: 16px 24px 6px;
            color: rgba(255,255,255,0.3);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* Main Content */
        #content {
            margin-left: 260px;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .topbar .page-title {
            font-weight: 600;
            font-size: 1rem;
            color: #1a1f36;
            margin: 0;
        }
        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
            color: #6c757d;
        }
        .topbar .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .main-content { padding: 24px; }

        /* Cards */
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; border-radius: 12px 12px 0 0 !important; padding: 16px 20px; font-weight: 600; }

        /* Stat Cards */
        .stat-card { border-radius: 12px; padding: 20px; color: #fff; position: relative; overflow: hidden; }
        .stat-card .stat-icon { font-size: 2rem; opacity: 0.3; position: absolute; right: 20px; top: 50%; transform: translateY(-50%); }
        .stat-card h3 { font-size: 1.8rem; font-weight: 700; margin: 0; }
        .stat-card p { margin: 0; font-size: 0.85rem; opacity: 0.9; }
        .stat-blue   { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-green  { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .stat-orange { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-red    { background: linear-gradient(135deg, #fc4a1a, #f7b733); }

        /* Badges */
        .badge-draft   { background: #e2e8f0; color: #475569; }
        .badge-sent    { background: #dbeafe; color: #1d4ed8; }
        .badge-paid    { background: #dcfce7; color: #16a34a; }
        .badge-overdue { background: #fee2e2; color: #dc2626; }

        /* Table */
        .table thead th { background: #f8fafc; border-top: none; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; }
        .table tbody td { vertical-align: middle; font-size: 0.9rem; }
        .table-hover tbody tr:hover { background: #f8fafc; }

        /* Buttons */
        .btn-primary { background: #667eea; border-color: #667eea; }
        .btn-primary:hover { background: #5a6fd8; border-color: #5a6fd8; }

        /* Forms */
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102,126,234,0.25); }

        /* Alert */
        .alert { border-radius: 8px; border: none; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }
        .alert-warning { background: #fef9c3; color: #854d0e; }

        @media (max-width: 768px) {
            #sidebar { width: 0; }
            #content { margin-left: 0; }
        }
    </style>
    @yield('styles')
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fas fa-file-invoice-dollar mr-2"></i>InvoicePro</h4>
        <small>Management System</small>
    </div>

    <ul class="nav flex-column mt-2">
        <li class="nav-section">Main</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>

        <li class="nav-section">Billing</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
                <i class="fas fa-file-invoice"></i> Invoices
            </a>
        </li>

        <li class="nav-section">Manage</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="fas fa-users"></i> Clients / Users
            </a>
        </li>

        <li class="nav-section">Account</li>
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</nav>

<!-- Main Content -->
<div id="content">
    <!-- Topbar -->
    <div class="topbar">
        <h5 class="page-title">@yield('page-title', 'Dashboard')</h5>
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <span>{{ auth()->user()->name }}</span>
        </div>
    </div>

    <!-- Page Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@yield('scripts')
</body>
</html>
