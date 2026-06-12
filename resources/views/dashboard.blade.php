@extends('layouts.app')

@section('title', 'Dashboard - ' . ($appName ?? 'Fluty Kos'))

@section('page-title', 'Dashboard')

@section('content')
    @livewire('dashboard')
@endsection
