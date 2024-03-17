@extends('layouts/layoutMaster')

@section('title', 'eCommerce Settings Shipping - Apps')

@section('page-script')
<script src="{{asset('assets/js/app-ecommerce-settings.js')}}"></script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">eCommerce /</span> Settings
</h4>

<div class="row g-4">

  <!-- Navigation -->
  <div class="col-12 col-lg-4">
    <div class="d-flex justify-content-between flex-column mb-3 mb-md-0">
      <ul class="nav nav-align-left nav-pills flex-column">
        <li class="nav-item mb-1">
          <a class="nav-link" href="{{url('/app/ecommerce/settings/details')}}">
            <i class="bx bx-store me-2"></i>
            <span class="align-middle">Store details</span>
          </a>
        </li>
        <li class="nav-item mb-1">
          <a class="nav-link" href="{{url('/app/ecommerce/settings/payments')}}">
            <i class="bx bxs-credit-card me-2"></i>
            <span class="align-middle">Payments</span>
          </a>
        </li>
        <li class="nav-item mb-1">
          <a class="nav-link" href="{{url('/app/ecommerce/settings/checkout')}}">
            <i class="bx bx-cart me-2"></i>
            <span class="align-middle">Checkout</span>
          </a>
        </li>
        <li class="nav-item mb-1">
          <a class="nav-link active" href="javascript:void(0);">
            <i class="bx bx-package me-2"></i>
            <span class="align-middle">Shipping & delivery</span>
          </a>
        </li>
        <li class="nav-item mb-1">
          <a class="nav-link" href="{{url('/app/ecommerce/settings/locations')}}">
            <i class="bx bx-location-plus me-2"></i>
            <span class="align-middle">Locations</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{url('/app/ecommerce/settings/notifications')}}">
            <i class="bx bx-bell me-2"></i>
            <span class="align-middle">Notifications</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <!-- /Navigation -->

  <!-- Options -->
  <div class="col-12 col-lg-8 pt-4 pt-lg-0">
    <div class="tab-content p-0">
      <!-- Shipping & delivery Tab -->
      <div class="tab-pane fade show active" id="shipping_delivery" role="tabpanel">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-start">
            <div class="card-title m-0">
              <h5 class="m-0">Shipping zones</h5>
              <p class="text-muted mb-0">Choose where you ship and how much you charge for shipping at check out.</p>
            </div>
            <a href="javascript:void(0);">Create zone</a>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center">
                <div class="me-3">
                  <i class="fi fi-us fis rounded-circle fs-2"></i>
                </div>
                <div class="d-flex flex-column">
                  <h6 class="m-0">Domestic</h6>
                  <p class="mb-0">United states of America</p>
                </div>
              </div>
              <div>
                <a href="javascript:void(0);" class="text-muted"><i class='bx bx-pencil me-3'></i></a>
                <a href="javascript:void(0);" class="text-muted"><i class='bx bx-trash'></i></a>
              </div>
            </div>

            <div class="card mb-3 border shadow-none">
              <div class="table-responsive text-nowrap rounded">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Rate Name</th>
                      <th>Condition</th>
                      <th>Price</th>
                      <th class="text-end">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Weight</td>
                      <td>5kg -10kg</td>
                      <td>
                        $9
                      </td>
                      <td class="text-end">
                        <div class="dropdown pe-3">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>VAT</td>
                      <td>12%</td>
                      <td>
                        $25
                      </td>
                      <td class="text-end">
                        <div class="dropdown pe-3">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-2"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-2"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr class="border-transparent">
                      <td>Duty</td>
                      <td>-</td>
                      <td>-</td>
                      <td class="text-end">
                        <div class="dropdown pe-3">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-2"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-2"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <button class="btn btn-label-primary mb-4">Add rate</button>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center">
                <div class="me-2">
                  <i class='bx bx-globe bx-md'></i>
                </div>
                <div class="d-flex flex-column">
                  <h6 class="m-0">International</h6>
                  <p class="mb-0">United states of America</p>
                </div>
              </div>
              <div>
                <a href="javascript:void(0);" class="text-muted"><i class='bx bx-pencil me-3'></i></a>
                <a href="javascript:void(0);" class="text-muted"><i class='bx bx-trash'></i></a>
              </div>
            </div>

            <div class="card mb-3 border shadow-none">
              <div class="table-responsive text-nowrap rounded">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Rate Name</th>
                      <th>Condition</th>
                      <th>Price</th>
                      <th class="text-end">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Weight</td>
                      <td>5kg -10kg</td>
                      <td>
                        $19
                      </td>
                      <td class="text-end">
                        <div class="dropdown pe-3">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>VAT</td>
                      <td>12%</td>
                      <td>
                        $25
                      </td>
                      <td class="text-end">
                        <div class="dropdown pe-3">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-2"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-2"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr class="border-transparent">
                      <td>Duty</td>
                      <td>Japan</td>
                      <td>$49</td>
                      <td class="text-end">
                        <div class="dropdown pe-3">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-2"></i> Edit</a>
                            <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-2"></i> Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <button class="btn btn-label-primary">Add rate</button>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3">
          <button type="reset" class="btn btn-label-secondary">Discard</button>
          <a class="btn btn-primary" href="locations">Save</a>
        </div>
      </div>
    </div>
    <!-- /Options-->
  </div>
</div>

@endsection
