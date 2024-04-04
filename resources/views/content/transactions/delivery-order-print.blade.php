@extends('layouts/layoutMaster')

@section('title', 'Print - Surat Jalan')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice-print.css')}}" />
@endsection

@section('page-script')
<script src="{{asset('assets/js/app-invoice-print.js')}}"></script>
@endsection

@php
  $contactData = json_decode(file_get_contents(resource_path('json/contact.json')), true);
@endphp

@section('content')
<div class="invoice-print p-5">

  <div class="d-flex justify-content-between flex-row">
    <div class="mb-4">
      <div class="d-flex svg-illustration mb-3 gap-2">
        <!-- <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span> -->
        <span class="demo text-body fw-bold">{{ strtoupper(config('variables.templateName')) }}</span>
      </div>
      <p class="mb-1">{{ $contactData['address'] }}</p>
      <p class="mb-0">Telp {{ $contactData['phone'] }}</p>
    </div>
    <div>
      <!-- <h4>Invoice #3492</h4> -->
      <div class="mb-2">
        <span>Jakarta, </span>
        <span class="fw-medium">{{ $formattedDate }}</span>
      </div>
      <div>
        <span>Kepada Yth:</span>
        <!-- <span class="fw-medium">May 25, 2021</span> -->
      </div>
      <div>
        <span class="fw-medium">{{ $supplier->name }}</span>
      </div>
      <div>
        <span class="fw-medium">{{ $supplier->address }}</span>
      </div>
      <div>
        <span class="fw-medium">CP: {{ $supplier->pic_name }} ({{ $supplier->phone_no }})</span>
      </div>
    </div>
  </div>

  <hr />

  <div class="text-center mt-4 mb-3">
    <h4>SURAT JALAN</h4>
  </div>

  <div class="table-responsive">
    <table class="table border-top m-0">
      <thead>
        <tr>
          <th>Barang</th>
          <th></th>
          <th></th>
          <th></th>
          <th>Qty</th>
        </tr>
      </thead>
      <tbody>
        @foreach($deliveryOrderDetails as $index => $detail)
          <tr>
            <td>{{ $detail->name }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $detail->quantity }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="row">
    <div class="col-12 text-end mt-5">
      <span class="fw-medium">Hormat kami,</span>
    </div>
  </div>
</div>
@endsection
