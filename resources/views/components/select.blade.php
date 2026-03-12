@props(['label' => null, 'name', 'options' => [], 'error' => null])

<div class="space-y-1">
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}</label>
    @endif
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->class([$error ? 'input-error' : 'input']) }}>
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}">{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($error)
        <p class="text-sm font-medium text-red-600">{{ $error }}</p>
    @endif
</div>
