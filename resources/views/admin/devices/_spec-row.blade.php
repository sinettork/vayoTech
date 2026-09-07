<div class="border p-3 mb-3 spec-row">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <strong class="small">Specification</strong>
        <button type="button" class="btn btn-sm btn-outline-danger remove-spec">Remove</button>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label small">Specification</label>
            <select name="specs[{{ $index }}][definition_id]" class="form-select form-select-sm" required>
                <option value="">Select canonical specification</option>
                @foreach($specDefinitions as $definition)
                    <option value="{{ $definition->id }}" @selected((string) ($spec['definition_id'] ?? '') === (string) $definition->id)>
                        {{ $definition->category }} — {{ $definition->label }}{{ $definition->unit ? ' (' . $definition->unit . ')' : '' }}
                    </option>
                @endforeach
            </select>
            @error("specs.$index.definition_id")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label small">Value</label>
            <input
                type="text"
                name="specs[{{ $index }}][spec_value]"
                value="{{ $spec['spec_value'] ?? '' }}"
                class="form-control form-control-sm"
                placeholder="Enter verified value"
                required
                maxlength="500"
            >
            @error("specs.$index.spec_value")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
