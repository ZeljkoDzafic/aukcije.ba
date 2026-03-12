@extends('layouts.guest')

@section('title', 'Aktivne aukcije')
@section('meta')
    <x-seo-meta
        title="Aktivne aukcije - Aukcije.ba"
        description="Pretraži aktivne aukcije po kategoriji, cijeni, lokaciji i statusu završetka."
    />
@endsection

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    @livewire('auction-search')
</section>
@endsection
