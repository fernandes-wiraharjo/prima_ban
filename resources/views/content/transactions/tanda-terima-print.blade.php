@extends('layouts/layoutMaster')

@section('title', 'Print - Tanda Terima')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice-print.css')}}" />
<style>
  /* Custom CSS to minimize line spacing in the table */
  .table th, .table td {
    line-height: 0; /* Reduce line-height to minimize spacing */
  }

  .table td {
    border: none !important; /* Remove top border from table cells */
  }

  .table thead th {
    border-top: 2px solid #dee2e6 !important; /* Ensure top border on the first row of headers */
    border-bottom: 2px solid #dee2e6;
    font-weight: 900;
  }

  .table-content {
    min-height: 250px; /* Set a minimum height for the invoice content */
  }

  /* Make all text bolder */
  .demo,
  .text-body,
  .fw-medium,
  p,
  span,
  th,
  td {
    font-weight: 900;
  }

  .total-row {
    border-top: 2px solid #dee2e6 !important;
    border-bottom: 2px solid #dee2e6;
  }
</style>
@endsection

@section('page-script')
<script src="{{asset('assets/js/app-invoice-print.js')}}"></script>
@endsection

@php
  $contactData = json_decode(file_get_contents(resource_path('json/contact.json')), true);
@endphp

@section('content')
<div class="invoice-print">

  <div class="d-flex justify-content-between flex-row">
    <div>
      <span class="demo text-body fw-bold">TANDA TERIMA FAKTUR</span>
      <div class="d-flex svg-illustration gap-2">
        <!-- <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span> -->
        <span class="demo text-body">{{ strtoupper(config('variables.templateName')) }}</span>
      </div>
      <p class="mb-0">{{ $contactData['address'] }}</p>
      <p class="mb-0">Telp {{ $contactData['phone'] }}</p>
    </div>
    <div>
      <!-- <h4>Invoice #3492</h4> -->
      <div>
        <span>Jakarta, </span>
        <span>{{ $formattedDate }}</span>
      </div>
    </div>
  </div>

  <!-- <hr /> -->

  <!-- <div class="text-center mt-4 mb-3">
    <h4>TANDA TERIMA FAKTUR</h4>
  </div> -->

  <div class="table-content table-responsive">
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
          <tr class="total-row">
            <td colspan="3" class="align-top">
            </td>
            <td class="text-end">
              <p class="mb-0">Total:</p>
            </td>
            <td>
              <p class="mb-0">{{$tandaTerima->total_price}}</p>
            </td>
          </tr>
      </tbody>
    </table>
  </div>

  <div class="row">
    <div class="col-12 text-end">
      <span>Penerima,</span>
    </div>
    <br><br><br>
    <div class="col-12 text-end">
      <span>{{$tandaTerima->receiver_name}}</span>
    </div>
  </div>
</div>
@endsection
