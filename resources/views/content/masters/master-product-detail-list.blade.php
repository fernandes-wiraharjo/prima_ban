@extends('layouts/layoutMaster')

@section('title', 'Product Detail Page')

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
<script src="{{asset('assets/js/master-product-detail-list.js')}}"></script>
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
  <a href="{{ route('master-product') }}">
    <span class="text-muted fw-light">{{ $name }} /</span>
  </a>
  Detail List
</h4>

<div class="card">
<input type="hidden" id="id-product" value="{{ $idProduct }}">
  <div class="card-datatable table-responsive">
    <table class="datatables-product-details table border-top">
      <thead>
        <tr>
          <th></th>
          <th>Size</th>
          <th>Code</th>
          <th>User Cash</th>
          <th>User Tempo</th>
          <th>Toko Cash</th>
          <th>Toko Tempo</th>
          <th>Quantity</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAdd" aria-labelledby="offcanvasAddLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddLabel" class="offcanvas-title">Add Product Detail</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new pt-0" id="addNewForm" onsubmit="return false" action="{{ route('add-product-detail') }}" method="POST">
        @csrf
        <input type="hidden" name="id_product" value="{{ $idProduct }}">
        <div class="mb-3">
          <label class="form-label" for="size">Size</label>
          <select id="size" name="id_size" class="select2 form-select">
            <option value="">Select</option>
            @foreach($sizes as $id => $code)
              <option value="{{ $id }}">{{ $code }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-code">Code</label>
          <input type="text" class="form-control" id="add-code" placeholder="code" name="code" aria-label="code" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-price-user-cash">Price User Cash</label>
          <input type="text" class="form-control" id="add-price-user-cash" placeholder="price user cash" name="price_user_cash" aria-label="price-user-cash" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-discount-user-cash">Discount User Cash</label>
          <input type="text" class="form-control" id="add-discount-user-cash" placeholder="discount user cash" name="discount_user_cash" aria-label="discount-user-cash" onkeyup="formatNumber(this)" onblur="updateFinalPriceUserCash('add')" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-final-price-user-cash">Final Price User Cash</label>
          <input type="text" class="form-control" id="add-final-price-user-cash" placeholder="final price user cash" name="final_price_user_cash" aria-label="final-price-user-cash" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-price-user-tempo">Price User Tempo</label>
          <input type="text" class="form-control" id="add-price-user-tempo" placeholder="price user tempo" name="price_user_tempo" aria-label="price-user-tempo" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-discount-user-tempo">Discount User Tempo</label>
          <input type="text" class="form-control" id="add-discount-user-tempo" placeholder="discount user tempo" name="discount_user_tempo" aria-label="discount-user-tempo" onkeyup="formatNumber(this)" onblur="updateFinalPriceUserTempo('add')" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-final-price-user-tempo">Final Price User Tempo</label>
          <input type="text" class="form-control" id="add-final-price-user-tempo" placeholder="final price user tempo" name="final_price_user_tempo" aria-label="final-price-user-tempo" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-price-toko-cash">Price Toko Cash</label>
          <input type="text" class="form-control" id="add-price-toko-cash" placeholder="price toko cash" name="price_toko_cash" aria-label="price-toko-cash" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-discount-toko-cash">Discount Toko Cash</label>
          <input type="text" class="form-control" id="add-discount-toko-cash" placeholder="discount toko cash" name="discount_toko_cash" aria-label="discount-toko-cash" onkeyup="formatNumber(this)" onblur="updateFinalPriceTokoCash('add')" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-final-price-toko-cash">Final Price Toko Cash</label>
          <input type="text" class="form-control" id="add-final-price-toko-cash" placeholder="final price toko cash" name="final_price_toko_cash" aria-label="final-price-toko-cash" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-price-toko-tempo">Price Toko Tempo</label>
          <input type="text" class="form-control" id="add-price-toko-tempo" placeholder="price toko tempo" name="price_toko_tempo" aria-label="price-toko-tempo" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-discount-toko-tempo">Discount Toko Tempo</label>
          <input type="text" class="form-control" id="add-discount-toko-tempo" placeholder="discount toko tempo" name="discount_toko_tempo" aria-label="discount-toko-tempo" onkeyup="formatNumber(this)" onblur="updateFinalPriceTokoTempo('add')" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-final-price-toko-tempo">Final Price Toko Tempo</label>
          <input type="text" class="form-control" id="add-final-price-toko-tempo" placeholder="final price toko tempo" name="final_price_toko_tempo" aria-label="final-price-toko-tempo" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-quantity">Quantity</label>
          <input type="text" class="form-control" id="add-quantity" placeholder="stock" name="quantity" aria-label="quantity" onkeyup="formatNumber(this)" />
        </div>
        <div class="mb-3">
          <label class="form-label d-block">Status</label>
          <small>
            <div class="form-check form-check-inline mt-3">
              <input class="form-check-input" type="radio" name="is_active" id="status_active" value="1" checked />
              <label class="form-check-label" for="status_active">ACTIVE</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="is_active" id="status_inactive" value="0" />
              <label class="form-check-label" for="status_inactive">INACTIVE</label>
            </div>
          </small>
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
        <h5 id="offcanvasEditLabel" class="offcanvas-title">Edit Product Detail</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form class="edit-form pt-0" id="editForm" onsubmit="return false" action="{{ route('edit-product-detail', ['id' => 0]) }}" method="POST">
        @method('PUT')
        @csrf
            <input type="hidden" id="edit-id" name="id">
            <div class="mb-3">
              <label class="form-label" for="edit-size">Size</label>
              <select id="edit-size" name="id_size" class="select2 form-select">
                <option value="">Select</option>
                @foreach($sizes as $id => $code)
                  <option value="{{ $id }}">{{ $code }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-code">Code</label>
              <input type="text" class="form-control" id="edit-code" placeholder="code" name="code" aria-label="code" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-price-user-cash">Price User Cash</label>
              <input type="text" class="form-control" id="edit-price-user-cash" placeholder="price user cash" name="price_user_cash" aria-label="price-user-cash" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-discount-user-cash">Discount User Cash</label>
              <input type="text" class="form-control" id="edit-discount-user-cash" placeholder="discount user cash" name="discount_user_cash" aria-label="discount-user-cash" onkeyup="formatNumber(this)" onblur="updateFinalPriceUserCash('edit')" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-final-price-user-cash">Final Price User Cash</label>
              <input type="text" class="form-control" id="edit-final-price-user-cash" placeholder="final price user cash" name="final_price_user_cash" aria-label="final-price-user-cash" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-price-user-tempo">Price User Tempo</label>
              <input type="text" class="form-control" id="edit-price-user-tempo" placeholder="price user tempo" name="price_user_tempo" aria-label="price-user-tempo" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-discount-user-tempo">Discount User Tempo</label>
              <input type="text" class="form-control" id="edit-discount-user-tempo" placeholder="discount user tempo" name="discount_user_tempo" aria-label="discount-user-tempo" onkeyup="formatNumber(this)" onblur="updateFinalPriceUserTempo('edit')" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-final-price-user-tempo">Final Price User Tempo</label>
              <input type="text" class="form-control" id="edit-final-price-user-tempo" placeholder="final price user tempo" name="final_price_user_tempo" aria-label="final-price-user-tempo" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-price-toko-cash">Price Toko Cash</label>
              <input type="text" class="form-control" id="edit-price-toko-cash" placeholder="price toko cash" name="price_toko_cash" aria-label="price-toko-cash" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-discount-toko-cash">Discount Toko Cash</label>
              <input type="text" class="form-control" id="edit-discount-toko-cash" placeholder="discount toko cash" name="discount_toko_cash" aria-label="discount-toko-cash" onkeyup="formatNumber(this)" onblur="updateFinalPriceTokoCash('edit')" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-final-price-toko-cash">Final Price Toko Cash</label>
              <input type="text" class="form-control" id="edit-final-price-toko-cash" placeholder="final price toko cash" name="final_price_toko_cash" aria-label="final-price-toko-cash" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-price-toko-tempo">Price Toko Tempo</label>
              <input type="text" class="form-control" id="edit-price-toko-tempo" placeholder="price toko tempo" name="price_toko_tempo" aria-label="price-toko-tempo" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-discount-toko-tempo">Discount Toko Tempo</label>
              <input type="text" class="form-control" id="edit-discount-toko-tempo" placeholder="discount toko tempo" name="discount_toko_tempo" aria-label="discount-toko-tempo" onkeyup="formatNumber(this)" onblur="updateFinalPriceTokoTempo('edit')" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-final-price-toko-tempo">Final Price Toko Tempo</label>
              <input type="text" class="form-control" id="edit-final-price-toko-tempo" placeholder="final price toko tempo" name="final_price_toko_tempo" aria-label="final-price-toko-tempo" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-quantity">Quantity</label>
              <input type="text" class="form-control" id="edit-quantity" placeholder="stock" name="quantity" aria-label="quantity" onkeyup="formatNumber(this)" />
            </div>
            <div class="mb-3">
              <label class="form-label d-block">Status</label>
              <small>
                <div class="form-check form-check-inline mt-3">
                  <input class="form-check-input" type="radio" name="is_active" id="edit-status_1" value="1" checked />
                  <label class="form-check-label" for="edit-status_1">ACTIVE</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_active" id="edit-status_0" value="0" />
                  <label class="form-check-label" for="edit-status_0">INACTIVE</label>
                </div>
              </small>
            </div>
            <!-- <br><br><br><br> -->
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
    </div>
</div>
</div>

@endsection
