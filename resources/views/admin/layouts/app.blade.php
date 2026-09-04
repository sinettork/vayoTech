<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - VayoTech</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #f5f7fa; }
        .admin-sidebar { min-height: calc(100vh - 56px); }
        .admin-stat { border: 0; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
        .table img { object-fit: contain; background: #f8f9fa; }
        .admin-content { min-height: calc(100vh - 56px); }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">VayoTech Admin</a>
        <div class="d-flex align-items-center gap-3 text-white">
            <a class="btn btn-outline-light btn-sm" href="{{ route('home') }}" target="_blank">View Site</a>
            <span class="small d-none d-md-inline">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-danger btn-sm" type="submit">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 col-md-3 bg-white border-end admin-sidebar p-3">
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="list-group-item list-group-item-action {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}" href="{{ route('admin.devices.index') }}">Devices</a>
                <a class="list-group-item list-group-item-action {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">Brands</a>
                <a class="list-group-item list-group-item-action {{ request()->routeIs('admin.news.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}">News</a>
            </div>
        </aside>

        <main class="col-lg-10 col-md-9 admin-content p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
