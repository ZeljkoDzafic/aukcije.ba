@extends('layouts.admin')

@section('title', 'Feature Flags')
@section('admin_heading', 'Feature flags')

@section('content')
<section class="space-y-6">
    @livewire('admin.feature-flags')
</section>
@endsection
