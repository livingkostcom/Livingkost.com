@extends('layouts.app')

@section('content')
    @livewire('invoice.invoice-form', ['invoiceId' => $invoice->id])
@endsection
