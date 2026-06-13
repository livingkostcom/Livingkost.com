@extends('layouts.app')

@section('title', 'Dompet - ' . ($appName ?? 'Living Kost'))

@section('page-title', 'Dompet')

@section('content')
    <livewire:owner.wallet-index />
@endsection
