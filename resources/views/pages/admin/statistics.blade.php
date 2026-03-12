@extends('layouts.admin')

@section('title', 'Statistike')
@section('admin_heading', 'Statistike i analitika')

@section('content')
<section class="space-y-6">
    @livewire('admin.statistics-overview')
</section>
@endsection
