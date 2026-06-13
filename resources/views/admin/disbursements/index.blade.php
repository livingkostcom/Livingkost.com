@extends('layouts.app')

@section('title', 'Pencairan - ' . ($appName ?? 'Living Kost'))

@section('page-title', 'Pencairan')

@section('content')
    <livewire:admin.disbursement-index />
@endsection
