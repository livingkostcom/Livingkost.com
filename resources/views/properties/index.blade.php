@extends('layouts.app')

@section('title', 'Properties - ' . ($appName ?? 'Living Kost'))

@section('page-title', 'Properties')

@section('content')
    <livewire:property.property-index />
@endsection
