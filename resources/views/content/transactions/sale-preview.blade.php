@extends('layouts/layoutMaster')

@section('title', 'Preview - Sale')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
@endsection

@section('page-script')
<!-- <script src="{{asset('assets/js/offcanvas-add-payment.js')}}"></script>
<script src="{{asset('assets/js/offcanvas-send-invoice.js')}}"></script> -->
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
@endsection

@php
  $contactData = json_decode(file_get_contents(resource_path('json/contact.json')), true);
@endphp

@section('content')

<div class="row invoice-preview">
  <!-- Invoice -->
  <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
    <div class="card invoice-preview-card">
      <div class="card-body">
        <div class="col-md-12 text-center">
          <div class="svg-illustration mb-4 gap-2">
            <span class="demo text-body fw-bold">INVOICE</span>
          </div>
        </div>
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column p-sm-3 p-0">
          <div class="mb-xl-0 mb-4">
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
              <span class="me-1">Tanggal:</span>
              <span class="fw-medium">{{ $formattedDate }}</span>
            </div>
            <div>
              <span class="me-1">Kepada Yth:</span>
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
      </div>

      <hr class="my-0" />

      <div class="text-center mt-4 mb-3">
        <h6>
          <span style="position: absolute; left: 0; margin-left: 20px;">No Kendaraan: {{ $sale->technician }}</span>
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
              <td colspan="2" class="align-top px-4 py-5">
                Note: <br> {{ $sale->note }}
              </td>
              <td colspan="2" class="align-top px-4 py-5">
                Pembayaran ke rek: <br> {{ $sale->bank_account_no }}
              </td>
              <td class="text-end px-4 py-5">
                <p class="mb-2">Subtotal:</p>
                <p class="mb-2">Diskon:</p>
                <p class="mb-0">Total:</p>
              </td>
              <td class="px-4 py-5">
                <p class="fw-medium mb-2">{{ $sale->subtotal_price }}</p>
                <p class="fw-medium mb-2">{{ $sale->discount }}</p>
                <p class="fw-medium mb-0">{{ $sale->final_price }}</p>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="align-top px-4 py-5">
                Tanda Terima,
              </td>
              <td class="text-end px-4 py-5">
              </td>
              <td class="px-4 py-5">
                <br><br><br><br><br>
                {{ strtoupper(config('variables.templateName')) }}
              </td>
            </tr>
            <tr>
              <td colspan="5" class="align-top px-4 py-5">
                Barang yang sudah dibeli, tidak dapat ditukar atau dikembalikan
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- /Invoice -->

  <!-- Invoice Actions -->
  <div class="col-xl-3 col-md-4 col-12 invoice-actions">
    <div class="card">
      <div class="card-body">
        <a class="btn btn-label-secondary d-grid w-100 mb-3" href="{{url('transaction/sale')}}">
          Cancel
        </a>
        <a class="btn btn-label-secondary d-grid w-100 mb-3" href="{{url('transaction/sale/' . $id)}}">
          Edit
        </a>
        <a class="btn btn-label-secondary d-grid w-100 mb-3" target="_blank" href="{{url('transaction/sale/' . $id . '/print')}}">
          Print
        </a>
      </div>
    </div>
  </div>
  <!-- /Invoice Actions -->
</div>
@endsection
