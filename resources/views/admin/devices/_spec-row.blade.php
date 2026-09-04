<div class="border rounded p-3 mb-3 spec-row">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <strong class="small">Specification</strong>
        <button type="button" class="btn btn-sm btn-outline-danger remove-spec">Remove</button>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label small">Category</label>
            <select name="specs[{{ $index }}][category]" class="form-select form-select-sm" required>
                <option value="">Select category</option>
                @foreach(['Display', 'Camera', 'Battery', 'Performance', 'Body', 'Connectivity'] as $category)
                    <option value="{{ $category }}" @selected(($spec['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label small">Spec Name</label>
            <input type="text" name="specs[{{ $index }}][spec_key]" value="{{ $spec['spec_key'] ?? '' }}" class="form-control form-control-sm" placeholder="Screen Size" required maxlength="255">
        </div>

        <div class="col-md-4">
            <label class="form-label small">Value</label>
            <input type="text" name="specs[{{ $index }}][spec_value]" value="{{ $spec['spec_value'] ?? '' }}" class="form-control form-control-sm" placeholder="6.3 inches" required maxlength="500">
        </div>
    </div>
</div>
