@extends('admin.layouts.app')

@section('title', 'Brands')

@section('content')

<div class="mb-4">

    <div class="sidebar-title mb-2">
        Catalog
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">

        <div>
            <h1 class="h3 mb-1">
                Brands
            </h1>

            <p class="text-muted mb-0">
                Manage phone manufacturers and their brand assets.
            </p>
        </div>

        <a
            href="{{ route('admin.brands.create') }}"
            class="btn btn-dark"
        >
            Add Brand
        </a>

    </div>

</div>


<form
    method="GET"
    action="{{ route('admin.brands.index') }}"
    class="mb-4"
>

    <div class="row g-2">

        <div class="col-md-10">

            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                class="form-control"
                placeholder="Search brand or domain..."
            >

        </div>

        <div class="col-md-2 d-grid">

            <button
                type="submit"
                class="btn btn-outline-dark"
            >
                Search
            </button>

        </div>

    </div>

</form>


<div class="table-responsive bg-white border">

    <table class="table align-middle mb-0">

        <thead>

        <tr>
            <th>Brand</th>
            <th>Domain</th>
            <th>Devices</th>
            <th class="text-end">Actions</th>
        </tr>

        </thead>


        <tbody>

        @forelse($brands as $brand)

            <tr>

                <td>

                    <div class="d-flex align-items-center gap-3">

                        @if($brand->brandfetch_logo_url)

                            <img
                                src="{{ $brand->brandfetch_logo_url }}"
                                alt="{{ $brand->name }}"
                                width="48"
                                height="48"
                                class="border bg-white"
                                style="object-fit:contain;padding:6px;"
                            >

                        @elseif($brand->logo)

                            <img
                                src="{{ asset('storage/' . $brand->logo) }}"
                                alt="{{ $brand->name }}"
                                width="48"
                                height="48"
                                class="border bg-white"
                                style="object-fit:contain;padding:6px;"
                            >

                        @endif


                        <div>

                            <div class="fw-semibold">
                                {{ $brand->name }}
                            </div>

                            @if($brand->description)

                                <div class="text-muted small">
                                    {{ Str::limit($brand->description, 80) }}
                                </div>

                            @endif

                        </div>

                    </div>

                </td>


                <td>

                    @if($brand->brand_domain)

                        <span class="small">
                            {{ $brand->brand_domain }}
                        </span>

                    @else

                        <span class="text-muted small">
                            Not connected
                        </span>

                    @endif

                </td>


                <td>
                    {{ $brand->devices_count }}
                </td>


                <td class="text-end">

                    <a
                        href="{{ route('brands.show', $brand->slug) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        View
                    </a>

                    <a
                        href="{{ route('admin.brands.edit', $brand) }}"
                        class="btn btn-sm btn-outline-dark"
                    >
                        Edit
                    </a>

                    <form
                        action="{{ route('admin.brands.destroy', $brand) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Delete this brand?');"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-sm btn-outline-danger"
                        >
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="4"
                    class="text-center py-5 text-muted"
                >
                    No brands found.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>


@if($brands->hasPages())

    <div class="mt-4">
        {{ $brands->links() }}
    </div>

@endif

@endsection
