@extends('layouts.app')

@section('title', 'Dashboard - ' . ($appName ?? 'Living Kost'))

@section('page-title', 'Dashboard')

@section('content')
    @livewire('dashboard')
@endsection
