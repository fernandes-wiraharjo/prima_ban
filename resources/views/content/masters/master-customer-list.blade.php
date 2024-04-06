@extends('layouts/layoutMaster')

@section('title', 'Customer Page')

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
<script src="{{asset('assets/js/master-customer-list.js')}}"></script>
@endsection

@section('content')

<!-- Users List Table -->
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
    <table class="datatables-customers table border-top">
      <thead>
        <tr>
          <th></th>
          <th>Name</th>
          <!-- <th>Type</th> -->
          <th>Address</th>
          <th>Phone No</th>
          <th>Pic Name</th>
          <th>Bank Account No</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new user -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddCustomer" aria-labelledby="offcanvasAddCustomerLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddCustomerLabel" class="offcanvas-title">Add Customer</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new-customer pt-0" id="addNewCustomerForm" onsubmit="return false" action="{{ route('add-customer') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label" for="add-customer-name">Name</label>
          <input type="text" class="form-control" id="add-customer-name" placeholder="name" name="name" aria-label="name" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="type">Type</label>
          <select id="type" name="type" class="form-select">
            <option value="">Select</option>
            <option value="user">user</option>
            <option value="toko">toko</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-customer-phone">Phone No</label>
          <input type="text" class="form-control" id="add-customer-phone" placeholder="phone no" name="phone_no" aria-label="phone_no" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-customer-pic">Pic Name</label>
          <input type="text" class="form-control" id="add-customer-pic" placeholder="pic name" name="pic_name" aria-label="pic_name" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-customer-bank-account-no">Bank Account No</label>
          <input type="text" class="form-control" id="add-customer-bank-account-no" placeholder="bank account no" name="bank_account_no" aria-label="bank_account_no" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="add-customer-address">Address</label>
          <textarea row="3" id="add-customer-address" class="form-control" placeholder="address" aria-label="address" name="address"></textarea>
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
        <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </form>
    </div>
  </div>

  <!-- edit form -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditCustomer" aria-labelledby="offcanvasEditCustomerLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasEditCustomerLabel" class="offcanvas-title">Edit Customer</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form class="edit-customer-form pt-0" id="editCustomerForm" onsubmit="return false" action="{{ route('edit-customer', ['id' => 0]) }}" method="POST">
        @method('PUT')
        @csrf
            <input type="hidden" id="edit-customer-id" name="customer_id">
            <div class="mb-3">
              <label class="form-label" for="edit-customer-name">Name</label>
              <input type="text" class="form-control" id="edit-customer-name" placeholder="name" name="name" aria-label="name" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-customer-phone">Phone No</label>
              <input type="text" class="form-control" id="edit-customer-phone" placeholder="phone no" name="phone_no" aria-label="phone_no" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-customer-pic">Pic Name</label>
              <input type="text" class="form-control" id="edit-customer-pic" placeholder="pic name" name="pic_name" aria-label="pic_name" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-customer-bank-account-no">Bank Account No</label>
              <input type="text" class="form-control" id="edit-customer-bank-account-no" placeholder="bank account no" name="bank_account_no" aria-label="bank_account_no" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="edit-customer-address">Address</label>
              <textarea row="3" id="edit-customer-address" class="form-control" placeholder="address" aria-label="address" name="address"></textarea>
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
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
    </div>
</div>
</div>

@endsection
