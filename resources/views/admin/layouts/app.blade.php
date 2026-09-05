<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - VayoTech</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="admin-body">

<div class="admin-shell">

    <header class="site-header admin-header">
        <nav class="navbar navbar-dark bg-dark">
            <div class="container-fluid page-shell">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    VayoTech Admin
                </a>

                <div class="d-flex align-items-center gap-3">
                    <span class="text-white-50 small d-none d-md-inline">
                        {{ auth()->user()->name }}
                    </span>

                    <a
                        class="btn btn-outline-light btn-sm"
                        href="{{ route('home') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        View Site
                    </a>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="btn btn-light btn-sm" type="submit">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <div class="admin-main">
        <div class="admin-main-shell page-shell">

            <aside class="admin-sidebar">
                <div class="p-3">
                    <div class="sidebar-title mb-2">
                        Administration
                    </div>

                    <nav class="nav flex-column">

                        <a
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}"
                        >
                            Dashboard
                        </a>

                        <a
                            class="nav-link {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}"
                            href="{{ route('admin.devices.index') }}"
                        >
                            Devices
                        </a>

                        <a
                            class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                            href="{{ route('admin.brands.index') }}"
                        >
                            Brands
                        </a>

                        <a
                            class="nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}"
                            href="{{ route('admin.news.index') }}"
                        >
                            News
                        </a>

                    </nav>
                </div>

                <div class="admin-sidebar-footer p-3 border-top">
                    <div class="small text-secondary">
                        Laravel {{ app()->version() }}
                    </div>
                </div>
            </aside>

            <main class="admin-content">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please fix the following:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="admin-content-inner">
                    @yield('content')
                </div>

            </main>

        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>
