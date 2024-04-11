@extends('layouts/layoutMaster')

@section('title', 'Preview - Tanda Terima')

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
              <span class="me-1">Jakarta, </span>
              <span class="fw-medium">{{ $formattedDate }}</span>
            </div>
          </div>
        </div>
      </div>

      <hr class="my-0" />

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
            <tr>
              <td colspan="3" class="align-top px-4 py-5">
              </td>
              <td class="text-end px-4 py-5">
              </td>
              <td class="px-4 py-5">
                <p class="fw-medium mb-2">Penerima,</p>
                <br><br><br>
                <p class="fw-medium mb-2">{{ $tandaTerima->receiver_name }}</p>
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
        <a class="btn btn-label-secondary d-grid w-100 mb-3" href="{{url('transaction/tanda-terima')}}">
          Cancel
        </a>
        <a class="btn btn-label-secondary d-grid w-100 mb-3" href="{{url('transaction/tanda-terima/' . $id)}}">
          Edit
        </a>
        <a class="btn btn-label-secondary d-grid w-100 mb-3" target="_blank" href="{{url('transaction/tanda-terima/' . $id . '/print')}}">
          Print
        </a>
      </div>
    </div>
  </div>
  <!-- /Invoice Actions -->
</div>
@endsection
