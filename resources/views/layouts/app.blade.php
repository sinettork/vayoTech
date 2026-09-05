<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VayoTech')</title>
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @else
        <meta name="description" content="@yield('description', 'Compare phone specs, browse the latest devices, and read tech news on VayoTech.')">
    @endif

    {{-- Open Graph (for social sharing previews) --}}
    <meta property="og:site_name" content="VayoTech">
    <meta property="og:title" content="@yield('og_title', 'VayoTech')">
    @hasSection('og_description')
        <meta property="og:description" content="@yield('og_description')">
    @elseif (View::hasSection('meta_description'))
        <meta property="og:description" content="@yield('meta_description')">
    @else
        <meta property="og:description" content="@yield('description', 'Compare phone specs and read the latest tech news on VayoTech.')">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif

    @hasSection('canonical')
        <link rel="canonical" href="@yield('canonical')">
    @else
        <link rel="canonical" href="{{ url()->current() }}">
    @endif

    @stack('structured_data')
    @yield('schema')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container page-shell">
                <a class="navbar-brand" href="{{ route('home') }}">VayoTech</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navigation" aria-controls="main-navigation" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="main-navigation">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('devices.index') && request('status') === 'available' ? 'active' : '' }}" href="{{ route('devices.index', ['status' => 'available']) }}">Available</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('devices.index') && request('status') === 'rumored' ? 'active' : '' }}" href="{{ route('devices.index', ['status' => 'rumored']) }}">Rumored</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}">Explore brands</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" href="{{ route('news.index') }}">News</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('compare.*') ? 'active' : '' }}" href="{{ route('compare.index') }}">Compare</a>
                        </li>
                    </ul>
                    <div class="position-relative mt-3 mt-lg-0" style="width: 300px;">
                        <label class="visually-hidden" for="search-box">Search phones</label>
                        <input type="search" id="search-box" class="form-control" placeholder="Search phones..." autocomplete="off" spellcheck="false">
                        <div id="search-results" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container page-shell my-4">
        @yield('content')
    </main>

    <footer class="bg-light text-center py-3 mt-5">
        <p class="mb-0">&copy; {{ date('Y') }} VayoTech. <a href="{{ route('privacy') }}">Privacy</a> &middot; <a href="{{ route('terms') }}">Terms</a></p>
    </footer>
    <div id="cookie-banner" class="alert alert-dark rounded-0 fixed-bottom mb-0 d-none" role="dialog" aria-label="Cookie notice">
        <div class="container d-flex flex-column flex-md-row align-items-md-center gap-2 justify-content-between">
            <span>We use essential cookies to keep VayoTech working. Read our <a class="alert-link" href="{{ route('privacy') }}">Privacy Policy</a>.</span>
            <button id="accept-cookies" type="button" class="btn btn-sm btn-light">Accept</button>
        </div>
    </div>
    <script>
const searchBox = document.getElementById('search-box');
const resultsBox = document.getElementById('search-results');
const searchUrl = @json(route('devices.search'));
let debounceTimer;
let activeSearchController;

searchBox.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    activeSearchController?.abort();

    const query = this.value.trim();

    if (query.length < 2) {
        resultsBox.innerHTML = '';
        resultsBox.style.display = 'none';
        return;
    }

    debounceTimer = setTimeout(async () => {
        activeSearchController = new AbortController();

        resultsBox.innerHTML = '<div class="list-group-item text-muted">Searching...</div>';
        resultsBox.style.display = 'block';

        try {
            const response = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' },
                signal: activeSearchController.signal,
            });

            if (!response.ok) {
                throw new Error(`Search request failed with status ${response.status}`);
            }

            const devices = await response.json();

            if (searchBox.value.trim() !== query) {
                return;
            }

            resultsBox.innerHTML = '';

            if (devices.length === 0) {
                resultsBox.innerHTML = '<div class="list-group-item text-muted">No results found</div>';
                return;
            }

            devices.forEach(device => {
                const item = document.createElement('a');
                item.href = device.url;
                item.className = 'list-group-item list-group-item-action';

                const row = document.createElement('div');
                row.className = 'd-flex align-items-center gap-2';

                if (device.image) {
                    const image = document.createElement('img');
                    image.src = device.image;
                    image.alt = device.name;
                    image.width = 40;
                    image.height = 40;
                    image.loading = 'lazy';
                    image.style.cssText = 'width:40px;height:40px;object-fit:contain;border-radius:4px;flex:0 0 40px;';
                    row.appendChild(image);
                }

                const text = document.createElement('div');
                text.className = 'min-w-0';

                const name = document.createElement('div');
                name.className = 'fw-semibold small text-truncate';
                name.textContent = device.name;

                const brand = document.createElement('div');
                brand.className = 'text-muted small text-truncate';
                brand.textContent = device.brand || '';

                text.append(name, brand);
                row.appendChild(text);
                item.appendChild(row);
                resultsBox.appendChild(item);
            });
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            resultsBox.innerHTML = '<div class="list-group-item text-muted">Unable to search right now.</div>';
        }
    }, 250);
});

document.addEventListener('click', function (e) {
    if (!searchBox.contains(e.target) && !resultsBox.contains(e.target)) {
        resultsBox.style.display = 'none';
    }
});

const cookieBanner = document.getElementById('cookie-banner');
const acceptCookiesButton = document.getElementById('accept-cookies');

if (localStorage.getItem('phonespecs-cookie-consent') !== 'accepted') {
    cookieBanner.classList.remove('d-none');
}

acceptCookiesButton.addEventListener('click', function () {
    localStorage.setItem('phonespecs-cookie-consent', 'accepted');
    cookieBanner.classList.add('d-none');
});
</script>

@stack('scripts')
</body>
</html>
