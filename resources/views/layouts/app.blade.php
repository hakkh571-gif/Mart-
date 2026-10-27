<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OTPOS System</title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-orange: #ff5722;
            --bg-light: #f4f6f9;
            --sidebar-bg: #ffffff;
            --card-border: #e9ecef;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', 'Kantumruy Pro', sans-serif;
            color: #212529;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Modern Left Sidebar Header */
        .otpos-sidebar {
            width: 75px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 0.8rem;
            color: var(--primary-orange);
            margin-bottom: 30px;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .nav-icon-link {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 1.25rem;
            margin-bottom: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-icon-link.active, .nav-icon-link:hover {
            background-color: #fff3e0;
            color: var(--primary-orange);
        }

        /* App Content Wrapper */
        .otpos-main-wrapper {
            margin-left: 75px;
            min-height: 100vh;
        }
    </style>
</head>
<body>

<!-- Left Navigation Sidebar Modern -->
<div class="otpos-sidebar">
    <a href="{{ route('pos.index') }}" class="brand-logo">
        <i class="bi bi-shop fs-4"></i>
        <span>OTPOS</span>
    </a>
    
    <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/') }}" class="nav-icon-link {{ request()->is('dashboard*') ? 'active' : '' }}" title="Dashboard">
        <i class="bi bi-grid-fill"></i>
    </a>
    
    <a href="{{ Route::has('pos.index') ? route('pos.index') : url('/pos') }}" class="nav-icon-link {{ request()->is('pos*') ? 'active' : '' }}" title="កន្លែងលក់ POS">
        <i class="bi bi-cart-fill"></i>
    </a>
    
    <a href="{{ Route::has('products.index') ? route('products.index') : url('/products') }}" class="nav-icon-link {{ request()->is('products*') ? 'active' : '' }}" title="បញ្ជីទំនិញ">
        <i class="bi bi-box-seam"></i>
    </a>
    
    <a href="{{ Route::has('staffs.index') ? route('staffs.index') : '#' }}" class="nav-icon-link {{ request()->is('staffs*') ? 'active' : '' }}" title="គ្រប់គ្រងបុគ្គលិក">
        <i class="bi bi-people-fill"></i>
    </a>
    
    <a href="{{ Route::has('settings.index') ? route('settings.index') : '#' }}" class="nav-icon-link {{ request()->is('settings*') ? 'active' : '' }}" title="ការកំណត់">
        <i class="bi bi-gear-fill"></i>
    </a>
</div>

<!-- Dynamic Main Page Output -->
<div class="otpos-main-wrapper">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>