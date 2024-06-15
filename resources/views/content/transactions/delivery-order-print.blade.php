@extends('layouts/layoutMaster')

@section('title', 'Print - Surat Jalan')

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
    border-top: 2px solid #dee2e6; /* Ensure top border on the first row of headers */
    border-bottom: 2px solid #dee2e6;
    font-weight: 900;
  }

  .right-column {
    margin-left: 350px; /* Adjust the left margin */
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

  .barang-column {
    width: 70%;
  }

  .qty-column {
    width: 30%;
  }

  @media print {
    .table-content {
      min-height: 250px !important;
    }

    .right-column {
      margin-left: 350px !important;
    }

    .text-end {
      text-align: right !important;
    }
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
  <div class="d-flex flex-row">
    <div>
      <span class="demo text-body fw-bold">SURAT JALAN</span>
      <div class="d-flex svg-illustration gap-2">
        <!-- <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span> -->
        <span class="demo text-body">{{ strtoupper(config('variables.templateName')) }}</span>
      </div>
      <p class="mb-0">{{ $contactData['address'] }}</p>
      <p class="mb-0">Telp {{ $contactData['phone'] }}</p>
    </div>
    <div class="right-column">
      <!-- <h4>Invoice #3492</h4> -->
      <div>
        <span>Jakarta, </span>
        <span>{{ $formattedDate }}</span>
      </div>
      <div>
        <span>Kepada Yth:</span>
        <!-- <span>May 25, 2021</span> -->
      </div>
      <div>
        <span>{{ $supplier->name }}</span>
      </div>
      <div>
        <span>{{ $supplier->address }}</span>
      </div>
      <div>
        <span>CP: {{ $supplier->pic_name }} ({{ $supplier->phone_no }})</span>
      </div>
    </div>
  </div>

  <!-- <hr /> -->

  <!-- <div class="text-center">
    <h5>SURAT JALAN</h5>
  </div> -->

  <div class="table-content table-responsive">
    <table class="table border-top m-0">
      <thead>
        <tr>
          <th class="barang-column">Barang</th>
          <!-- <th></th> -->
          <!-- <th></th> -->
          <!-- <th></th> -->
          <th class="qty-column">Qty</th>
        </tr>
      </thead>
      <tbody>
        @foreach($deliveryOrderDetails as $index => $detail)
          <tr>
            <td class="barang-column">{{ $detail->name }}</td>
            <!-- <td></td> -->
            <!-- <td></td> -->
            <!-- <td></td> -->
            <td class="qty-column">{{ $detail->quantity }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <hr>

  <div class="row">
    <div class="col-12 text-end">
      <span>Hormat kami,</span>
    </div>
  </div>
</div>
@endsection
