@extends('layouts/layoutMaster')

@section('title', 'Add - Tanda Terima')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<!-- <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script> -->
@endsection

@section('page-script')
<!-- <script src="{{asset('assets/js/offcanvas-send-invoice.js')}}"></script> -->
<script src="{{asset('assets/js/tanda-terima-add.js')}}"></script>
@endsection

@php
  $contactData = json_decode(file_get_contents(resource_path('json/contact.json')), true);
@endphp

@section('content')

@if ($errors->any())
  <div class="alert alert-danger alert-dismissible" role="alert">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
@elseif(session('othererror'))
  <div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('othererror') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
@endif

<form class="source-item py-sm-3" method="post" action="{{ route('add-tanda-terima') }}">
@csrf
<div class="row invoice-add">
  <!-- Invoice Add-->
  <div class="col-lg-9 col-12 mb-lg-0 mb-4">
    <div class="card invoice-preview-card">
      <div class="card-body">
        <div class="row p-sm-3 p-0">
          <div class="col-md-6 mb-md-0 mb-4">
            <div class="d-flex svg-illustration mb-4 gap-2">
              <!-- <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span> -->
              <span class="demo text-body fw-bold">{{ strtoupper(config('variables.templateName')) }}</span>
            </div>
            <p class="mb-1">{{ $contactData['address'] }}</p>
            <p class="mb-0">Telp {{ $contactData['phone'] }}</p>
          </div>
          <div class="col-md-6">
            <dl class="row mb-2">
              <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                <span class="fw-normal">Jakarta, </span>
              </dt>
              <dd class="col-sm-6 d-flex justify-content-md-end">
                <div class="w-px-150">
                  <input type="text" name="date" class="form-control date-picker" placeholder="YYYY-MM-DD" required />
                </div>
              </dd>
            </dl>
          </div>
        </div>

        <hr class="my-4 mx-n4" />

        <div class="text-center mt-4 mb-3">
          <h4>TANDA TERIMA FAKTUR</h4>
          <br>
        </div>

        <!-- <form class="source-item py-sm-3"> -->
          <div class="mb-3" data-repeater-list="group-a">
            <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item>
              <div class="d-flex border rounded position-relative pe-0">
                <div class="row w-100 m-0 p-3">
                  <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                    <p class="mb-2 repeater-title">Tgl Faktur</p>
                    <input type="date" class="form-control" name="group-a[0][invoice_date]" placeholder="tgl faktur" />
                  </div>
                  <div class="col-md-3 col-12 mb-md-0 mb-3">
                    <p class="mb-2 repeater-title">No Faktur</p>
                    <input type="text" class="form-control" name="group-a[0][invoice_no]" placeholder="no faktur" />
                  </div>
                  <div class="col-md-3 col-12 mb-md-0 mb-3">
                    <p class="mb-2 repeater-title">Nilai Faktur</p>
                    <input type="number" class="form-control invoice-price" placeholder="100000" name="group-a[0][invoice_price]"/>
                  </div>
                  <div class="col-md-3 col-12 pe-0">
                    <p class="mb-2 repeater-title">Keterangan</p>
                    <input type="text" class="form-control" name="group-a[0][invoice_description]" placeholder="keterangan" />
                  </div>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                  <i class="bx bx-x fs-4 text-muted cursor-pointer" data-repeater-delete></i>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="button" class="btn btn-primary" data-repeater-create>Add Item</button>
            </div>
          </div>
        <!-- </form> -->

        <!-- <hr class="my-4 mx-n4" />

        <div class="row py-sm-3">
          <div class="col-md-6 mb-md-0 mb-3">
          </div>
          <div class="col-md-6 d-flex justify-content-end">
            <div class="invoice-calculations">
              <div class="d-flex justify-content-between">
                <span class="w-px-100">Total:</span>
                <span class="fw-medium" id="total-price">Rp0</span>
              </div>
            </div>
          </div>
        </div> -->

        <hr class="my-4 mx-n4" />

        <div class="row py-sm-3">
          <div class="col-md-6 mb-md-0 mb-3">
          </div>
          <div class="col-md-6 d-flex justify-content-end">
            <div class="invoice-calculations">
              <div class="d-flex justify-content-between mb-2">
                <span class="w-px-100">Penerima,</span>
              </div>
            </div>
          </div>
        </div>

        <div class="row py-sm-3 mt-4">
          <div class="col-md-6 mb-md-0 mb-3">
          </div>
          <div class="col-md-6 d-flex justify-content-end">
            <div class="invoice-calculations">
              <div class="d-flex justify-content-between mb-2">
                <input type="text" name="receiver_name" class="form-control" placeholder="nama penerima" required />
              </div>
            </div>
          </div>
        </div>

        <!-- <div class="row">
          <div class="col-4">
            <div class="mb-5">
              <br>
            </div>
          </div>
        </div> -->
      </div>
    </div>
  </div>
  <!-- /Invoice Add-->

  <!-- Invoice Actions -->
  <div class="col-lg-3 col-12 invoice-actions">
    <div class="card mb-4">
      <div class="card-body">
        <a href="{{url('/transaction/tanda-terima')}}" class="btn btn-label-secondary d-grid w-100 mb-3">Cancel</a>
        <button type="submit" class="btn btn-label-secondary d-grid w-100">Save</button>
      </div>
    </div>
  </div>
  <!-- /Invoice Actions -->
</div>
</form>

<!-- Offcanvas -->
@include('_partials/_offcanvas/offcanvas-send-invoice')
<!-- /Offcanvas -->
@endsection
