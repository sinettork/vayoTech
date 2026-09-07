<div class="border p-3 mb-3 variant-row">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <strong class="small">Variant</strong>
        <button type="button" class="btn btn-sm btn-outline-danger remove-variant">Remove</button>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label small">RAM</label>
            <input type="text" name="variants[{{ $index }}][ram]" value="{{ $variant['ram'] ?? '' }}" class="form-control form-control-sm" required maxlength="50" placeholder="8GB">
            @error("variants.$index.ram")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label small">Storage</label>
            <input type="text" name="variants[{{ $index }}][storage]" value="{{ $variant['storage'] ?? '' }}" class="form-control form-control-sm" required maxlength="50" placeholder="128GB">
            @error("variants.$index.storage")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small">Storage type</label>
            <input type="text" name="variants[{{ $index }}][storage_type]" value="{{ $variant['storage_type'] ?? '' }}" class="form-control form-control-sm" maxlength="50" placeholder="UFS 4.0">
            @error("variants.$index.storage_type")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small">Model code</label>
            <input type="text" name="variants[{{ $index }}][model_code]" value="{{ $variant['model_code'] ?? '' }}" class="form-control form-control-sm" maxlength="100" placeholder="SM-S928B">
            @error("variants.$index.model_code")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small">Market</label>
            <input type="text" name="variants[{{ $index }}][market]" value="{{ $variant['market'] ?? '' }}" class="form-control form-control-sm" maxlength="50" placeholder="Global">
            @error("variants.$index.market")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label small">Price</label>
            <input type="number" name="variants[{{ $index }}][price]" value="{{ $variant['price'] ?? '' }}" class="form-control form-control-sm" min="0" step="0.01" placeholder="999.00">
            @error("variants.$index.price")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label small">Currency</label>
            <input type="text" name="variants[{{ $index }}][currency]" value="{{ $variant['currency'] ?? 'USD' }}" class="form-control form-control-sm text-uppercase" maxlength="3">
            @error("variants.$index.currency")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check mb-2">
                <input type="hidden" name="variants[{{ $index }}][is_default]" value="0">
                <input type="checkbox" name="variants[{{ $index }}][is_default]" value="1" class="form-check-input" id="variant-default-{{ $index }}" @checked($variant['is_default'] ?? false)>
                <label class="form-check-label small" for="variant-default-{{ $index }}">Set as default</label>
            </div>
        </div>
    </div>
</div>
