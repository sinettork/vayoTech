@props(['brand', 'showCount' => false])

@php
    $deviceCount = $brand->devices_count ?? null;
@endphp

<a href="{{ route('brands.show', $brand) }}" class="brand-card" aria-label="Explore {{ $brand->name }} phones">
    <span class="brand-card-logo">
        @if($brand->brandfetch_logo_url)
            <img src="{{ $brand->brandfetch_logo_url }}" alt="{{ $brand->name }} logo" loading="lazy">
        @elseif($brand->logo)
            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }} logo" loading="lazy">
        @else
            <span>{{ substr($brand->name, 0, 1) }}</span>
        @endif
    </span>

    <span class="brand-card-content">
        <span class="brand-card-name">{{ $brand->name }}</span>
        @if($showCount && $deviceCount !== null)
            <span class="brand-card-count">{{ $deviceCount }} {{ Str::plural('phone', $deviceCount) }}</span>
        @else
            <span class="brand-card-action">Explore phones</span>
        @endif
    </span>

    <span class="brand-card-arrow" aria-hidden="true">→</span>
</a>

@once
    @push('styles')
    <style>
        .brand-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 82px;
            padding: 12px 14px;
            background: #fff;
            border: 1px solid var(--phonespecs-border, #dee2e6);
            color: #212529;
            text-decoration: none;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }

        .brand-card:hover {
            color: #212529;
            border-color: #adb5bd;
            box-shadow: 0 5px 16px rgba(0, 0, 0, .06);
            transform: translateY(-1px);
        }

        .brand-card-logo {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #212529;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .brand-card-logo img {
            width: 100%;
            height: 100%;
            padding: 7px;
            object-fit: contain;
        }

        .brand-card-content {
            min-width: 0;
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 2px;
        }

        .brand-card-name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: .95rem;
            font-weight: 600;
        }

        .brand-card-action,
        .brand-card-count {
            color: var(--phonespecs-muted, #6c757d);
            font-size: .75rem;
        }

        .brand-card-arrow {
            color: #6c757d;
            font-size: 1rem;
            transition: transform .15s ease, color .15s ease;
        }

        .brand-card:hover .brand-card-arrow {
            color: #0d6efd;
            transform: translateX(2px);
        }

        .brand-card-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        @media (max-width: 991.98px) {
            .brand-card-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .brand-card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 479.98px) {
            .brand-card-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush
@endonce