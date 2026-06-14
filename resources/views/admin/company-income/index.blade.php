@extends('layouts.app')

@section('title', 'Pendapatan Lain - ' . ($appName ?? 'Living Kost'))

@section('page-title', 'Pendapatan Lain')

@section('content')
    <livewire:admin.company-ledger-index type="income" />
@endsection
