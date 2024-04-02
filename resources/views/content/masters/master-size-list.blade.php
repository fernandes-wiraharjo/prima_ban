@extends('layouts/layoutMaster')

@section('title', 'Size Page')

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
<script src="{{asset('assets/js/master-size-list.js')}}"></script>
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
    <table class="datatables-sizes table border-top">
      <thead>
        <tr>
          <th></th>
          <th>Code</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
  <!-- Offcanvas to add new -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAdd" aria-labelledby="offcanvasAddLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasAddLabel" class="offcanvas-title">Add Size</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form class="add-new pt-0" id="addNewForm" onsubmit="return false" action="{{ route('add-size') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label" for="add-code">Code</label>
          <input type="text" class="form-control" id="add-code" placeholder="code" name="code" aria-label="code" />
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
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEdit" aria-labelledby="offcanvasEditLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasEditLabel" class="offcanvas-title">Edit Size</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form class="edit-form pt-0" id="editForm" onsubmit="return false" action="{{ route('edit-size', ['id' => 0]) }}" method="POST">
        @method('PUT')
        @csrf
            <input type="hidden" id="edit-id" name="id">
            <div class="mb-3">
              <label class="form-label" for="edit-code">Code</label>
              <input type="text" class="form-control" id="edit-code" placeholder="code" name="code" aria-label="code" />
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
