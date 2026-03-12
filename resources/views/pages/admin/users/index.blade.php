@extends('layouts.admin')

@section('title', 'Korisnici')
@section('admin_heading', 'Upravljanje korisnicima')

@section('content')
<section class="space-y-6">
    @livewire('admin.user-directory')
</section>
@endsection
