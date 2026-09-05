<div class="card device-card h-100">
    <a href="{{ route('devices.show', $device) }}" class="text-decoration-none text-dark d-block">
        @if ($device->image)
            <img src="{{ asset('storage/' . $device->image) }}" class="device-image" alt="{{ $device->name }}" loading="lazy">
        @else
            <div class="device-image d-flex align-items-center justify-content-center">
                <span class="text-muted small">No image</span>
            </div>
        @endif
        <div class="card-body p-2 text-center">
            <h3 class="h6 card-title mb-1">{{ $device->name }}</h3>
            <p class="text-muted small mb-1">
                {{ $device->brand->name ?? '' }}
                @if ($device->release_date) &middot; {{ $device->release_date->format('M Y') }} @endif
            </p>
            @if ($device->status === 'rumored')
                <span class="badge text-bg-warning">Coming soon</span>
            @endif
        </div>
    </a>
</div>