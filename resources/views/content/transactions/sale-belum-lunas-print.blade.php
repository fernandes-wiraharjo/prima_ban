@extends('layouts/layoutMaster')

@section('title', 'Print - Sale Belum Lunas')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice-print.css')}}" />
@endsection

@section('page-script')
<script src="{{asset('assets/js/app-invoice-print.js')}}"></script>
@endsection

@php
  $contactData = json_decode(file_get_contents(resource_path('json/contact.json')), true);
@endphp

@section('content')
<div class="invoice-print p-5">

  <div class="col-md-12 text-center">
    <div class="svg-illustration mb-4 gap-2">
      <span class="demo text-body fw-bold">INVOICE BELUM LUNAS</span>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table border-top m-0">
      <thead>
        <tr>
          <th>Customer</th>
          <th>Tanggal</th>
          <th>Invoice</th>
          <th>Jumlah</th>
        </tr>
      </thead>
      <tbody>
         @php
          $groupedCustomerNames = $sales->groupBy('customer_name');
        @endphp
        @foreach($groupedCustomerNames as $customer => $sales)
          <tr>
            <td class="text-nowrap" rowspan="{{ $sales->count() }}">{{ $customer }}</td>
            <td>{{ $sales[0]->formatted_date }}</td>
            <td>{{ $sales[0]->invoice_no }}</td>
            <td>{{ $sales[0]->final_price }}</td>
          </tr>
          @foreach($sales->slice(1) as $sale)
            <tr onclick="window.open('/transaction/sale/{{ $sale->id }}/preview', '_blank')" style="cursor: pointer;">
              <td>{{ $sale->formatted_date }}</td>
              <td>{{ $sale->invoice_no }}</td>
              <td>{{ $sale->final_price }}</td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
