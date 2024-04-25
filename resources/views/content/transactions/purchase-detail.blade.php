@extends('layouts/layoutMaster')

@section('title', 'Purchase Detail Page')

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
<script src="{{asset('assets/js/purchase-detail.js')}}"></script>
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

<h4 class="py-3 mb-4">
  <a href="{{ route('transaction-purchase') }}">
    <span class="text-muted fw-light">{{ $supplier }} ({{$invoice}}) /</span>
  </a>
  Detail List
</h4>

<div class="card">
<input type="hidden" id="id-purchase" value="{{ $idPurchase }}">
  <div class="card-datatable table-responsive">
    <table class="datatables-purchase-details table border-top">
      <thead>
        <tr>
          <th></th>
          <th>Product</th>
          <th>Purchase Price</th>
          <th>Quantity</th>
          <th>Total</th>
          <th>Action</th>
        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAdd" aria-labelledby="offcanvasAddLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddLabel" class="offcanvas-title">Add Purchase Detail</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new pt-0" id="addNewForm" onsubmit="return false" action="{{ route('add-purchase-detail') }}" method="POST">
        @csrf
        <input type="hidden" name="id_purchase" value="{{ $idPurchase }}">
        <div class="mb-3">
          <label class="form-label" for="product">Product</label>
          <select id="product" name="id_product_detail" class="select2 form-select">
            <option value="">Select</option>
            @foreach($productDetails as $id => $product_detail_name)
              <option value="{{ $id }}">{{ $product_detail_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-price">Purchase Price</label>
          <input type="text" class="form-control" id="add-price" placeholder="purchase price" name="price" aria-label="price" onkeyup="formatNumber(this)" onblur="updateTotalPrice('add')" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-quantity">Quantity</label>
          <input type="text" class="form-control" id="add-quantity" placeholder="quantity" name="quantity" aria-label="quantity" onkeyup="formatNumber(this)" onblur="updateTotalPrice('add')" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-total">Total</label>
          <input type="text" class="form-control" id="add-total" placeholder="total" name="total_price" aria-label="total_price" readonly />
        </div>
        <!-- <br><br><br><br> -->
        <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </form>
    </div>
  </div>

  <!-- edit form -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEdit" aria-labelledby="offcanvasEditLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasEditLabel" class="offcanvas-title">Edit Purchase Detail</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form class="edit-form pt-0" id="editForm" onsubmit="return false" action="{{ route('edit-purchase-detail', ['id' => 0]) }}" method="POST">
        @method('PUT')
        @csrf
            <input type="hidden" id="edit-id" name="id">
            <input type="hidden" name="id_purchase" value="{{ $idPurchase }}">
            <div class="mb-3">
              <label class="form-label" for="edit-product">Product</label>
              <select id="edit-product" name="id_product_detail" class="select2 form-select">
                <option value="">Select</option>
                @foreach($productDetails as $id => $product_detail_name)
                  <option value="{{ $id }}">{{ $product_detail_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-price">Purchase Price</label>
              <input type="text" class="form-control" id="edit-price" placeholder="purchase price" name="price" aria-label="price" onkeyup="formatNumber(this)" onblur="updateTotalPrice('edit')" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-quantity">Quantity</label>
              <input type="text" class="form-control" id="edit-quantity" placeholder="quantity" name="quantity" aria-label="quantity" onkeyup="formatNumber(this)" onblur="updateTotalPrice('edit')" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-total">Total</label>
              <input type="text" class="form-control" id="edit-total" placeholder="total" name="total_price" aria-label="total_price" readonly />
            </div>
            <!-- <br><br><br><br> -->
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
    </div>
</div>
</div>

@endsection
