@extends('layouts.app')

@section('title', 'Kelola User - ' . ($appName ?? 'Living Kost'))

@section('page-title', 'Kelola User')

@section('content')
    <livewire:admin.user-index />
@endsection
