@extends('layouts/layoutMaster')

@section('title', 'Print - Sale')

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

  <div class="col-md-12 text-center">
    <div class="svg-illustration mb-4 gap-2">
      <span class="demo text-body fw-bold">INVOICE</span>
    </div>
  </div>
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
        <span>Tanggal:</span>
        <span class="fw-medium">{{ $formattedDate }}</span>
      </div>
      <div>
        <span>Kepada Yth:</span>
      </div>
      <div>
        <span class="fw-medium">{{ $customer->name }}</span>
      </div>
      <div>
        <span class="fw-medium">{{ $customer->address }}</span>
      </div>
      <div>
        <span class="fw-medium">CP: {{ $customer->pic_name }} ({{ $customer->phone_no }})</span>
      </div>
    </div>
  </div>

  <hr />

  <div class="text-center mt-4 mb-3">
    <h6>
      <span style="position: absolute; left: 0; margin-left: 50px;">Teknisi: {{ $sale->technician }}</span>
      <span style="position: relative;">NOTA: {{ $sale->invoice_no }}</span>
    </h6>
  </div>

  <div class="table-responsive">
    <table class="table border-top m-0">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Barang</th>
          <th>Qty</th>
          <th>Unit</th>
          <th>Harga</th>
          <th>Jumlah</th>
        </tr>
      </thead>
      <tbody>
        @foreach($saleDetails as $index => $detail)
          <tr>
            <td class="text-nowrap">{{ $detail->product_code }}</td>
            <td>{{ $detail->product_name }}</td>
            <td>{{ $detail->sale_quantity }}</td>
            <td>{{ $detail->product_uom }}</td>
            <td>{{ $detail->price }}</td>
            <td>{{ $detail->total_price }}</td>
          </tr>
        @endforeach
          <tr>
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
              <p class="fw-medium mb-0">{{ $sale->subtotal_price }}</p>
              <p class="fw-medium mb-0">{{ $sale->discount }}</p>
              <p class="fw-medium mb-0">{{ $sale->final_price }}</p>
            </td>
          </tr>
          <tr>
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
          <tr>
            <td colspan="6" class="align-top">
              Barang yang sudah dibeli, tidak dapat ditukar atau dikembalikan
            </td>
          </tr>
      </tbody>
    </table>
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
