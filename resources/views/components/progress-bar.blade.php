@props(['value' => 65])

<div class="h-2 rounded-full bg-slate-200">
    <div class="h-2 rounded-full bg-trust-600" style="width: {{ max(0, min(100, (int) $value)) }}%"></div>
</div>
