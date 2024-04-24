@extends('layouts/layoutMaster')

@section('title', 'Purchase Page')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}" />

@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/purchase.js')}}"></script>
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
  <div class="card-datatable table-responsive">
    <table class="datatables-purchases table border-top">
      <thead>
        <tr>
          <th></th>
          <th>Supplier</th>
          <th>Invoice No</th>
          <th>Date</th>
          <th>Price</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAdd" aria-labelledby="offcanvasAddLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddLabel" class="offcanvas-title">Add Purchase</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new pt-0" id="addNewForm" onsubmit="return false" action="{{ route('add-purchase') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label" for="supplier">Supplier</label>
          <select id="supplier" name="id_supplier" class="select2 selectSupplier form-select">
            <option value="">Select</option>
            @foreach($suppliers as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-invoice-no">Invoice No</label>
          <input type="text" class="form-control" id="add-invoice-no" placeholder="invoice no" name="invoice_no" aria-label="invoice_no" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-date">Date</label>
          <input type="text" id="add-date" name="date" class="form-control date-picker" placeholder="YYYY-MM-DD" aria-label="date" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-final-price">Price</label>
          <input type="text" class="form-control" id="add-final-price" placeholder="price" name="final_price" aria-label="final_price" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label d-block">Status</label>
          <small>
            <div class="form-check form-check-inline mt-3">
              <input class="form-check-input" type="radio" name="status" id="status_lunas" value="lunas" checked />
              <label class="form-check-label" for="status_lunas">LUNAS</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="status_belum_lunas" value="belum lunas" />
              <label class="form-check-label" for="status_belum_lunas">BELUM LUNAS</label>
            </div>
          </small>
        </div>
        <!-- <br><br><br><br><br><br><br> -->
        <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </form>
    </div>
  </div>

  <!-- edit form -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEdit" aria-labelledby="offcanvasEditLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasEditLabel" class="offcanvas-title">Edit Purchase</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form class="edit-form pt-0" id="editForm" onsubmit="return false" action="{{ route('edit-purchase', ['id' => 0]) }}" method="POST">
        @method('PUT')
        @csrf
            <input type="hidden" id="edit-id" name="id">
            <div class="mb-3">
              <label class="form-label" for="edit-supplier">Supplier</label>
              <select id="edit-supplier" name="id_supplier" class="select2 selectSupplier form-select">
                <option value="">Select</option>
                @foreach($suppliers as $id => $name)
                  <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-invoice-no">Invoice No</label>
              <input type="text" class="form-control" id="edit-invoice-no" placeholder="invoice no" name="invoice_no" aria-label="invoice_no" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-date">Date</label>
              <input type="text" id="edit-date" name="date" class="form-control date-picker" placeholder="YYYY-MM-DD" aria-label="date" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-final-price">Price</label>
              <input type="text" class="form-control" id="edit-final-price" placeholder="price" name="final_price" aria-label="final_price" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label d-block">Status</label>
              <small>
                <div class="form-check form-check-inline mt-3">
                  <input class="form-check-input" type="radio" name="status" id="edit-status_lunas" value="lunas" checked />
                  <label class="form-check-label" for="edit-status_lunas">LUNAS</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="edit-status_belum lunas" value="belum lunas" />
                  <label class="form-check-label" for="edit-status_belum lunas">BELUM LUNAS</label>
                </div>
              </small>
            </div>
            <!-- <br><br><br><br><br><br><br> -->
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
    </div>
</div>
</div>

@endsection
