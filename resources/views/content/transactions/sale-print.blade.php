@extends('layouts/layoutMaster')

@section('title', 'Print - Sale')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice-print.css')}}" />
<style>
  /* .invoice-print {
    padding: 0.1rem !important;
    font-size: 0.65rem;
  } */

  .invoice-print h6 {
    /* font-size: 0.8rem; */
    margin-bottom: 0;
  }

  .invoice-print .table th, .invoice-print .table td {
    /* font-size: 0.77rem; */
    padding: 0rem !important;
    line-height: 1;
  }

  .invoice-print .mb-4, .invoice-print .mb-3, .invoice-print .mb-2, .invoice-print .mb-1, .invoice-print .mb-0 {
    margin-bottom: 0.1rem !important;
  }

  /* .invoice-print .text-center {
    margin-bottom: 0.1rem !important;
  } */

  /* .invoice-print .table {
    width: 100%;
    table-layout: auto;
    border-collapse: collapse;
  } */

  /* .invoice-print .table th, .invoice-print .table td { */
    /* word-wrap: break-word; */
    /* border: 1px solid #dee2e6; mmg dicomment */
    /* vertical-align: top; */
    /* text-align: center; mmg dicomment */
  /* } */

  .invoice-print .table th {
    background-color: #f8f9fa;
    border-top: 2px solid #dee2e6; /* Ensure top border on the first row of headers */
    border-bottom: 2px solid #dee2e6;
    font-weight: 900;
  }

  /* .page-break {
    page-break-inside: avoid;
  } */

  /* .footer-text {
    white-space: nowrap;
  } */

  /* @media print {
    .invoice-print {
      page-break-inside: avoid;
    }
    .invoice-print .table tr {
      page-break-inside: avoid;
    }
  } */

  .table td {
    border: none !important; /* Remove top border from table cells */
  }

  .table-content {
    min-height: 250px; /* Set a minimum height for the invoice content */
    margin-bottom: -15px;
  }

  .right-column {
    margin-left: 350px; /* Adjust the left margin */
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

  .bottom-row {
    border-top: 2px solid #dee2e6;
    /* border-bottom: 2px solid #dee2e6; */
  }

  .small-line-space {
    margin-top: -5px;
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
  <!-- <div class="col-md-12 text-center">
    <div class="svg-illustration gap-2">
      <span class="demo text-body fw-bold">INVOICE</span>
    </div>
  </div> -->
  <div class="d-flex flex-row">
    <div>
      <span class="demo text-body fw-bold">INVOICE</span>
      <div class="d-flex svg-illustration gap-2 small-line-space">
        <!-- <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span> -->
        <span class="demo text-body">{{ strtoupper(config('variables.templateName')) }}</span>
      </div>
      <p class="mb-0 small-line-space">{{ $contactData['address'] }}</p>
      <p class="mb-0 small-line-space">Telp {{ $contactData['phone'] }}</p>
    </div>
    <div class="right-column">
      <!-- <h4>Invoice #3492</h4> -->
      <div class="small-line-space">
        <span>Tanggal:</span>
        <span>{{ $formattedDate }}</span>
      </div>
      <div class="small-line-space">
        <span>Kepada Yth:</span>
      </div>
      <div class="small-line-space">
        <span>{{ $customer->name }}</span>
      </div>
      <div class="small-line-space">
        <span>{{ $customer->address }}</span>
      </div>
      <div class="small-line-space">
        <span>CP: {{ $customer->pic_name }} ({{ $customer->phone_no }})</span>
      </div>
    </div>
  </div>

  <!-- <hr /> -->

  <div class="text-center">
    <h6>
      <span style="position: absolute; left: 0; margin-left: 50px;">No Kendaraan: {{ $sale->technician }}</span>
      <span style="position: relative;">NOTA: {{ $sale->invoice_no }}</span>
    </h6>
  </div>

  <div class="table-content table-responsive">
    <table class="table border-top m-0">
      <thead>
        <tr>
          <th style="width: 75px;">Kode</th>
          <th>Barang</th>
          <th style="width: 75px;">Qty</th>
          <th style="width: 75px;">Unit</th>
          <th>Harga</th>
          <th>Jumlah</th>
        </tr>
      </thead>
      <tbody>
        @foreach($saleDetails as $index => $detail)
          <tr>
            <td class="text-nowrap" style="width: 75px;">{{ $detail->product_code }}</td>
            <td>{{ $detail->product_name }}</td>
            <td style="width: 75px;">{{ $detail->sale_quantity }}</td>
            <td style="width: 75px;">{{ $detail->product_uom }}</td>
            <td>{{ $detail->price }}</td>
            <td>{{ $detail->total_price }}</td>
          </tr>
        @endforeach
          <tr class="bottom-row">
            <td colspan="2" class="align-top">
              Note: <br> {{ $sale->note }}
            </td>
            <td colspan="2" class="align-top">
              Pembayaran ke rek: <br> {{ $sale->bank_account_no }}
            </td>
            <td class="text-end">
              <p class="mb-0">Subtotal:</p>
              <p class="mb-0">Diskon:</p>
              <p class="mb-0">Total:</p>
            </td>
            <td>
              <p class="mb-0">{{ $sale->subtotal_price }}</p>
              <p class="mb-0">{{ $sale->discount }}</p>
              <p class="mb-0">{{ $sale->final_price }}</p>
            </td>
          </tr>
          <!-- <tr class="bottom-row">
            <td colspan="4" class="align-top">
              Tanda Terima,
            </td>
            <td class="text-end">
            </td>
            <td>
              <br><br><br>
              {{ strtoupper(config('variables.templateName')) }}
            </td>
          </tr>
          <tr class="bottom-row">
            <td colspan="6" class="align-top">
              Barang yang sudah dibeli, tidak dapat ditukar atau dikembalikan
            </td>
          </tr> -->
      </tbody>
    </table>
  </div>

  <hr>

  <div class="d-flex justify-content-between flex-row text-body" style="margin-top: -10px">
    <div>
      Tanda Terima,
    </div>
    <div style="margin-bottom: -10px">
      <br><br>
      {{ strtoupper(config('variables.templateName')) }}
    </div>
  </div>

  <hr>

  <div class="text-body" style="margin-top: -10px">
    Barang yang sudah dibeli, tidak dapat ditukar atau dikembalikan
  </div>

  <!-- <div class="row">
    <div class="col-12 text-end mt-5">
      <span class="fw-medium">Penerima,</span>
    </div>
    <br><br><br><br>
    <div class="col-12 text-end mt-5">

    </div>
  </div> -->
</div>
@endsection
