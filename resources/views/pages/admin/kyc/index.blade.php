@extends('layouts.admin')

@section('title', 'KYC pregled')
@section('admin_heading', 'KYC pregled')

@section('content')
<section class="space-y-6">
    @livewire('admin.kyc-backoffice')
</section>
@endsection
