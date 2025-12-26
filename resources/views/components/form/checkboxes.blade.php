{{--
|--------------------------------------------------------------------------
| Form Checkbox Group Component (Anonymous Blade Component)
|--------------------------------------------------------------------------
|
| Purpose:
|   Reusable Bootstrap-based checkbox group with label, validation, and
|   automatic old() value handling.
|
| Usage:
|   <x-form.checkbox name="grant_types" :options="$grantTypes" label="Grant Types" required />
|   <x-form.checkbox name="permissions" :options="['read' => 'Read', 'write' => 'Write']" :selected="['read']" />
|
| Props:
|   - name (string)        : Field name (required)
|   - options (array)      : Array of checkboxes [value => label]
|   - label (string|null)  : Group label text
|   - required (bool)      : Shows required (*) indicator
|   - selected (array)     : Array of selected values
|   - extra_info (string)  : Help text below checkboxes
|   - cols (string)        : Column width class (default: 'col-md-4')
|
|--------------------------------------------------------------------------
--}}

@props([
    'name' => '',
    'id' => null,
    'label' => '',
    'required' => false,
    'options' => [],
    'selected' => [],
    'cols' => 'col-md-4',
])

@php
    $inputId = $id ?? "_$name";
    $inputName = $name . '[]';

    // Handle selected values - check old() first, then fall back to $selected
    $selectedValues = old($name) !== null ? old($name) : $selected;
    $selectedValues = is_array($selectedValues) ? $selectedValues : (!empty($selectedValues) ? [$selectedValues] : []);
@endphp

@if ($label)
    <label class="form-label">
        <strong>{{ $label }}</strong>
        @if ($required)
            <span class="text-danger">&nbsp;<i class="fas fa-xs fa-asterisk"></i></span>
        @endif
    </label>
@endif

<div class="row @error($name) is-invalid @enderror" style="margin: 0 5px; border: 1px solid #ced4da; border-radius: .375rem; padding: 10px;">
    @foreach ($options as $key => $labelText)
        <div class="{{ $cols }} mb-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="{{ $inputName }}" value="{{ $key }}" id="{{ $inputId }}_{{ $key }}"
                    {{ in_array($key, $selectedValues) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $inputId }}_{{ $key }}">
                    {{ $labelText }}
                </label>
            </div>
        </div>
    @endforeach
</div>

@error($name)
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
