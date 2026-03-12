@extends('layouts.admin')

@section('title', 'Kategorije')
@section('admin_heading', 'Kategorije')

@section('content')
<section class="space-y-6">
    @livewire('admin.category-manager')
</section>
@endsection
