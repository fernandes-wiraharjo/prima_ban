@extends('layouts/layoutMaster')

@section('title', 'Print - Stock Size')

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
      <span class="demo text-body fw-bold">Stok Ukuran {{ $size }}</span>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table border-top m-0">
      <thead>
        <tr>
          <th>Brand</th>
          <th>Pattern</th>
          <th>Stok</th>
          <th>Harga User Cash</th>
          <th>Harga User Tempo</th>
          <th>Harga Toko Cash</th>
          <th>Harga Toko Tempo</th>
        </tr>
      </thead>
      <tbody>
        @php
          $groupedStockSizes = $stockSizes->groupBy('parent_brand');
        @endphp
        @foreach($groupedStockSizes as $parent_brand => $stocks)
          <tr>
            <td class="text-nowrap" rowspan="{{ $stocks->count() }}">{{ $parent_brand }}</td>
            <td>{{ $stocks[0]->pattern_name }}</td>
            <td>{{ $stocks[0]->product_quantity }} {{ $stocks[0]->uom_code }}</td>
            <td>{{ $stocks[0]->final_price_user_cash }}</td>
            <td>{{ $stocks[0]->final_price_user_tempo }}</td>
            <td>{{ $stocks[0]->final_price_toko_cash }}</td>
            <td>{{ $stocks[0]->final_price_toko_tempo }}</td>
          </tr>
          @foreach($stocks->slice(1) as $ss)
            <tr>
              <td>{{ $ss->pattern_name }}</td>
              <td>{{ $ss->product_quantity }} {{ $ss->uom_code }}</td>
              <td>{{ $ss->final_price_user_cash }}</td>
              <td>{{ $ss->final_price_user_tempo }}</td>
              <td>{{ $ss->final_price_toko_cash }}</td>
              <td>{{ $ss->final_price_toko_tempo }}</td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
