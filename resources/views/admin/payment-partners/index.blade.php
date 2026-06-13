@extends('layouts.app')

@section('title', 'Kemitraan Pembayaran - ' . ($appName ?? 'Living Kost'))

@section('page-title', 'Kemitraan Pembayaran')

@section('content')
    <livewire:admin.payment-partner-index />
@endsection
