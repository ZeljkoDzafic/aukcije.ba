@extends('layouts.admin')

@section('title', 'Moderacija aukcija')
@section('admin_heading', 'Moderacija aukcija')

@section('content')
<section class="space-y-6">
    @livewire('admin.auction-moderation')
</section>
@endsection
