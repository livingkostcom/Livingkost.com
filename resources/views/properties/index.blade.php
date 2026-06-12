@extends('layouts.app')

@section('title', 'Properties - ' . ($appName ?? 'Fluty Kos'))

@section('page-title', 'Properties')

@section('content')
    <livewire:property.property-index />
@endsection
