@extends('layouts.app')

@section('title', 'Compare Devices')

@section('content')
<div class="mb-4">
    <span class="text-primary small fw-semibold text-uppercase">Side by side</span>
    <h1 class="mb-1">Compare Devices</h1>
    <p class="text-muted mb-0">Choose up to four phones and compare their specifications in one view.</p>
</div>

<form method="GET" action="{{ route('compare.index') }}" class="card comparison-panel card-body mb-4">
    <label class="form-label fw-semibold" for="device-picker">Select up to 4 devices</label>
    <select name="device_ids[]" multiple class="form-select mb-3" size="6" id="device-picker">
        @foreach ($allDevices as $d)
            <option value="{{ $d->id }}" @if (in_array($d->id, $devices->pluck('id')->toArray())) selected @endif>
                {{ $d->brand->name }} {{ $d->name }}
            </option>
        @endforeach
    </select>
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
        <span class="text-muted small">Hold Ctrl/Cmd to select multiple devices.</span>
        <button type="submit" class="btn btn-primary btn-sm">Compare selected</button>
    </div>
</form>

@if ($devices->count() < 2)
    <div class="alert alert-light border text-muted">Select at least 2 devices above to see a comparison.</div>
@else
    <div class="card comparison-panel">
        <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0 comparison-table">
            <thead>
                <tr>
                    <th>Spec</th>
                    @foreach ($devices as $device)
                        <th>{{ $device->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedKeys as $category => $keys)
                    <tr class="table-secondary">
                        <th colspan="{{ $devices->count() + 1 }}">{{ $category }}</th>
                    </tr>
                    @foreach ($keys as $key => $_)
                        <tr>
                            <th>{{ $key }}</th>
                            @foreach ($devices as $device)
                                @php
                                    $spec = $device->specs->first(fn ($specification) => $specification->category === $category && $specification->spec_key === $key);
                                @endphp
                                <td>{{ $spec->spec_value ?? '—' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
@endif

<script>
// Auto-submit form as query string using ?devices=1,2,3 instead of Laravel's default array format
document.querySelector('form').addEventListener('submit', function (e) {
    e.preventDefault();
    const selected = Array.from(document.getElementById('device-picker').selectedOptions)
        .map(opt => opt.value);
    window.location.href = "{{ route('compare.index') }}?devices=" + selected.join(',');
});
</script>
@endsection
