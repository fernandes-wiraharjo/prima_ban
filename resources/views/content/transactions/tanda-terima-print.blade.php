@extends('layouts/layoutMaster')

@section('title', 'Print - Tanda Terima')

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
    </div>
  </div>

  <hr />

  <div class="text-center mt-4 mb-3">
    <h4>TANDA TERIMA FAKTUR</h4>
  </div>

  <div class="table-responsive">
    <table class="table border-top m-0">
      <thead>
        <tr>
          <th>No</th>
          <th>Tgl Faktur</th>
          <th>No Faktur</th>
          <th>Nilai Faktur</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tandaTerimaDetails as $index => $detail)
          <tr>
            <td class="text-nowrap">{{ $index + 1}}</td>
            <td>{{ $detail->formatted_invoice_date }}</td>
            <td>{{ $detail->invoice_no }}</td>
            <td>{{ $detail->invoice_price }}</td>
            <td>{{ $detail->invoice_description }}</td>
          </tr>
        @endforeach
          <tr>
            <td colspan="3" class="align-top px-4 py-5">
            </td>
            <td class="text-end px-4 py-5">
              <p class="mb-0">Total:</p>
            </td>
            <td class="px-4 py-5">
              <p class="fw-medium mb-0">{{$tandaTerima->total_price}}</p>
            </td>
          </tr>
      </tbody>
    </table>
  </div>

  <div class="row">
    <div class="col-12 text-end mt-5">
      <span class="fw-medium">Penerima,</span>
    </div>
    <br><br><br><br>
    <div class="col-12 text-end mt-5">
      <span class="fw-medium">{{$tandaTerima->receiver_name}}</span>
    </div>
  </div>
</div>
@endsection
