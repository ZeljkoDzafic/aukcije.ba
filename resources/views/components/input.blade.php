@props([
    'label' => null,
    'name',
    'type' => 'text',
    'error' => null,
    'hint' => null,
])

<div class="space-y-1">
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}</label>
    @endif

    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" {{ $attributes->class([$error ? 'input-error' : 'input']) }}>{{ $slot }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" {{ $attributes->class([$error ? 'input-error' : 'input']) }}>
    @endif

    @if ($hint)
        <p class="text-sm text-slate-500">{{ $hint }}</p>
    @endif

    @if ($error)
        <p class="text-sm font-medium text-red-600">{{ $error }}</p>
    @endif
</div>
