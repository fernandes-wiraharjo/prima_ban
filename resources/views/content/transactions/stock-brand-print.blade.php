@extends('layouts/layoutMaster')

@section('title', 'Print - Stock Brand')

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
      <span class="demo text-body fw-bold">Stok {{ $brand }}</span>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table border-top m-0">
      <thead>
        <tr>
          <th>Pattern</th>
          <th>Kode</th>
          <th>Ukuran</th>
          <th>Stok</th>
        </tr>
      </thead>
      <tbody>
        @php
          $groupedStockBrands = $stockBrands->groupBy('pattern_name');
        @endphp
        @foreach($groupedStockBrands as $pattern => $stocks)
          <tr>
            <td class="text-nowrap" rowspan="{{ $stocks->count() }}">{{ $pattern }}</td>
            <td>{{ $stocks[0]->product_code }}</td>
            <td>{{ $stocks[0]->size_code }}</td>
            <td>{{ $stocks[0]->product_quantity }} {{ $stocks[0]->uom_code }}</td>
          </tr>
          @foreach($stocks->slice(1) as $sb)
            <tr>
              <td>{{ $sb->product_code }}</td>
              <td>{{ $sb->size_code }}</td>
              <td>{{ $sb->product_quantity }} {{ $sb->uom_code }}</td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
