@extends('layouts.app')

@section('title', 'Pengeluaran Perusahaan - ' . ($appName ?? 'Living Kost'))

@section('page-title', 'Pengeluaran Perusahaan')

@section('content')
    <livewire:admin.company-ledger-index type="expense" />
@endsection
