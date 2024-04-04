@extends('layouts/layoutMaster')

@section('title', 'Preview - Surat Jalan')

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
            <div>
              <span class="me-1">Kepada Yth:</span>
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
      </div>

      <hr class="my-0" />

      <div class="text-center mt-4 mb-3">
        <h4>SURAT JALAN</h4>
      </div>

      <div class="table-responsive">
        <table class="table border-top m-0">
          <thead>
            <tr>
              <th>Item</th>
              <th></th>
              <th></th>
              <th></th>
              <th>Qty</th>
            </tr>
          </thead>
          <tbody>
          @foreach($deliveryOrderDetails as $index => $detail)
            <tr>
              <td class="text-nowrap">{{ $detail->name }}</td>
              <td></td>
              <td></td>
              <td></td>
              <td>{{ $detail->quantity }}</td>
            </tr>
          @endforeach
            <tr>
              <td colspan="3" class="align-top px-4 py-5">
              </td>
              <td class="text-end px-4 py-5">
              </td>
              <td class="px-4 py-5">
                <p class="fw-medium mb-2">Hormat kami,</p>
                <br><br><br>
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
        <a class="btn btn-label-secondary d-grid w-100 mb-3" href="{{url('transaction/delivery-order')}}">
          Cancel
        </a>
        <a class="btn btn-label-secondary d-grid w-100 mb-3" href="{{url('transaction/delivery-order/' . $id)}}">
          Edit
        </a>
        <a class="btn btn-label-secondary d-grid w-100 mb-3" target="_blank" href="{{url('transaction/delivery-order/' . $id . '/print')}}">
          Print
        </a>
      </div>
    </div>
  </div>
  <!-- /Invoice Actions -->
</div>
@endsection
