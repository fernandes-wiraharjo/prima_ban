@extends('layouts/layoutMaster')

@section('title', 'Add - Sale')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<!-- <script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script> -->
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script>
  var products = @json($products);
</script>
<!-- <script src="{{asset('assets/js/offcanvas-send-invoice.js')}}"></script> -->
<script src="{{asset('assets/js/sale-add.js')}}"></script>
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

<form class="source-item py-sm-3" method="post" action="{{ route('add-sale') }}">
@csrf
<div class="row invoice-add">
  <!-- Invoice Add-->
  <div class="col-lg-9 col-12 mb-lg-0 mb-4">
    <div class="card invoice-preview-card">
      <div class="card-body">
        <div class="row p-sm-3 p-0">
          <div class="col-md-12 text-center">
            <div class="svg-illustration mb-4 gap-2">
              <span class="demo text-body fw-bold">INVOICE</span>
            </div>
          </div>
          <div class="col-md-6 mb-md-0 mb-4">
            <div class="d-flex svg-illustration mb-4 gap-2">
              <!-- <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span> -->
              <span class="demo text-body fw-bold">{{ strtoupper(config('variables.templateName')) }}</span>
            </div>
            <p class="mb-1">{{ $contactData['address'] }}</p>
            <p class="mb-0">Telp: {{ $contactData['phone'] }}</p>
          </div>
          <div class="col-md-6">
            <dl class="row mb-2">
              <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                <span class="fw-normal">Tanggal: </span>
              </dt>
              <dd class="col-sm-6 d-flex justify-content-md-end">
                <div class="w-px-150">
                  <input type="text" name="date" class="form-control date-picker" placeholder="YYYY-MM-DD" required />
                </div>
              </dd>
              <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                <span class="fw-normal">Kepada Yth: </span>
              </dt>
              <dd class="col-sm-6 d-flex justify-content-md-end">
                <div class="w-px-150">
                <select id="customer" name="id_customer" class="select2 select-customer form-select" required>
                  <option value="">Select</option>
                  @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" data-type="{{ $customer->type }}">{{ $customer->name }}</option>
                  @endforeach
                </select>
                </div>
              </dd>
              <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                <span class="fw-normal">No Kendaraan: </span>
              </dt>
              <dd class="col-sm-6 d-flex justify-content-md-end">
                <div class="w-px-150">
                  <input type="text" name="technician" class="form-control" />
                </div>
              </dd>
              <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                <span class="fw-normal">Nota: </span>
              </dt>
              <dd class="col-sm-6 d-flex justify-content-md-end">
                <div class="w-px-150">
                  <input type="text" name="invoice_no" class="form-control" value="INV/" required />
                </div>
              </dd>
            </dl>
          </div>
        </div>

        <hr class="my-4 mx-n4" />

        <!-- <div class="text-center mt-4 mb-3">
          <h4>NOTA</h4>
          <br>
        </div> -->

        <!-- <form class="source-item py-sm-3"> -->
        <div class="mb-3" data-repeater-list="group-a">
          <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item>
            <div class="d-flex border rounded position-relative pe-0">
              <div class="row w-100 m-0 p-3">
                <div class="col-md-7 col-12 mb-md-0 mb-3 ps-md-0">
                  <p class="mb-2 repeater-title">Barang</p>
                  <select class="select2 form-select item-details mb-2" name="group-a[0][item]">
                    <option selected disabled>Item</option>
                    <!-- <option value="">Select</option> -->
                    @foreach($products as $product)
                      <option value="{{ $product->id }}"
                        data-type="{{ $product->type }}"
                        data-price-user-cash="{{ $product->final_price_user_cash }}"
                        data-price-user-tempo="{{ $product->final_price_user_tempo }}"
                        data-price-toko-cash="{{ $product->final_price_toko_cash }}"
                        data-price-toko-tempo="{{ $product->final_price_toko_tempo }}">
                        {{ $product->name }}
                      </option>
                    @endforeach
                  </select>
                  <!-- <span class="item-price"></span> -->
                </div>
                <div class="col-md-3 col-12 mb-md-0 mb-3">
                  <p class="mb-2 repeater-title">Harga</p>
                  <input type="text" class="form-control invoice-item-price" placeholder="0" name="group-a[0][price]" onkeyup="formatNumber(this)" required />
                </div>
                <div class="col-md-2 col-12 mb-md-0 mb-3">
                  <p class="mb-2 repeater-title">Qty</p>
                  <input type="text" class="form-control invoice-item-qty" placeholder="1" name="group-a[0][quantity]" required />
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
            <button type="button" class="btn btn-primary" id="add-item" data-repeater-create>Add Item</button>
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
            <!-- <div class="d-flex align-items-center mb-3">
              <label for="salesperson" class="form-label me-5 fw-medium">Note:</label>
              <input type="text" class="form-control" id="salesperson" placeholder="Edward Crowley" />
            </div> -->
            <input type="text" class="form-control" id="note" name="note" placeholder="Note" />
          </div>
          <div class="col-md-6 d-flex justify-content-end">
            <div class="invoice-calculations">
              <!-- <div class="d-flex justify-content-between mb-2">
                <span class="w-px-100">Subtotal:</span>
                <span class="fw-medium">$00.00</span>
              </div> -->
              <div class="d-flex justify-content-between mb-2">
                <span class="w-px-100">Diskon:</span>
                <span class="fw-medium">
                  <input type="number" class="form-control" id="discount" name="discount" value="0" placeholder="Diskon" required style="width: 120px;" />
                </span>
              </div>
              <!-- <div class="d-flex justify-content-between mb-2">
                <span class="w-px-100">Tax:</span>
                <span class="fw-medium">$00.00</span>
              </div>
              <hr />
              <div class="d-flex justify-content-between">
                <span class="w-px-100">Total:</span>
                <span class="fw-medium">$00.00</span>
              </div> -->
            </div>
          </div>
        </div>

        <!-- <div class="row py-sm-3 mt-4">
          <div class="col-md-6 mb-md-0 mb-3">
          </div>
          <div class="col-md-6 d-flex justify-content-end">
            <div class="invoice-calculations">
              <div class="d-flex justify-content-between mb-2">
                <input type="text" name="receiver_name" class="form-control" placeholder="nama penerima" required />
              </div>
            </div>
          </div>
        </div> -->

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
        <a href="{{url('/transaction/sale')}}" class="btn btn-label-secondary d-grid w-100 mb-3">Cancel</a>
        <button type="submit" class="btn btn-label-secondary d-grid w-100">Save</button>
      </div>
    </div>
    <div>
      <p class="mb-2">Pembayaran ke rek</p>
      <select class="form-select mb-4" id="bank-account-no" name="bank_account_no">
        @foreach($contactData['bankAccountNo'] as $accountNo)
          <option value="{{ $accountNo }}">{{ $accountNo }}</option>
        @endforeach
      </select>
      <p class="mb-2">Status</p>
      <select class="form-select mb-4" id="status" name="status">
        <option value="lunas">Lunas</option>
        <option value="belum lunas">Belum Lunas</option>
      </select>
    </div>
    <p class="mb-2">Tipe Pembayaran</p>
      <select class="form-select mb-4" id="payment-type" name="payment_type" required>
        <option value=""></option>
        <option value="cash">Tunai</option>
        <option value="tempo">Tempo</option>
      </select>
    </div>
  </div>
  <!-- /Invoice Actions -->
</div>
</form>
@endsection
