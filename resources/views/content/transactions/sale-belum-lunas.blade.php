@extends('layouts/layoutMaster')

@section('title', 'Sale Belum Lunas Page')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}" />

@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/sale-belum-lunas.js')}}"></script>
@endsection

@section('content')

  @if(session('success'))
    <div class="alert alert-primary alert-dismissible" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
  @elseif ($errors->any())
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

<div class="card">
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-4">
        <label for="customer" class="form-label">Select Customer</label>
        <select id="customer" name="id_customer" class="select2 form-select">
        <option value="">Select</option>
          @foreach($customers as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4 align-self-end">
        <button id="print-button" class="btn btn-primary">Preview</button>
      </div>
    </div>
  </div>
</div>

@endsection
