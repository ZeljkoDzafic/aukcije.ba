@props(['html' => ''])

@php
    $sanitizedHtml = app(\App\Support\HtmlSanitizer::class)->sanitize($html);
@endphp

<div {{ $attributes->merge(['class' => 'rich-text prose prose-slate max-w-none prose-headings:text-slate-900 prose-p:text-slate-700 prose-strong:text-slate-900']) }}>
    {!! $sanitizedHtml !!}
</div>
