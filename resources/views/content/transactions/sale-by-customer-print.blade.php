@extends('layouts/layoutMaster')

@section('title', 'Print - Sales By Customer')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice-print.css')}}" />
<style>
  .address-section {
      font-size: 14px;
      line-height: 1.6;
      color: #555;
  }

  .address-label {
      font-weight: bold;
  }

  .address-details {
      font-style: italic;
  }
</style>
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
      <span class="demo text-body fw-bold">{{ $customer->name }}</span>
    </div>
  </div>

  <!-- Address Section -->
  <div class="col-md-12">
    <div class="address-section mt-4">
      <span class="address-details">{{ $customer->address }}</span>
    </div>
  </div>

  <div class="table-responsive mt-3">
    <table class="table border-top m-0">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Invoice</th>
          <th>Jumlah</th>
          <th>Barang/Jasa</th>
          <th>Ket.</th>
          <th>Harga</th>
          <th>Total Harga</th>
        </tr>
      </thead>
      <tbody>
      @php
        $groupedDate = $salesByCustomer->groupBy('formatted_date');
      @endphp
      @foreach($groupedDate as $date => $sales)
        @php
          $groupedInvoices = $sales->groupBy('invoice_no');
          $dateRowSpan = $sales->count();
          $grandTotal = $sales->sum('total_price');
        @endphp
        @foreach($groupedInvoices as $invoiceNo => $invoiceSales)
          <tr>
            @if ($loop->first)
              <td class="text-nowrap" rowspan="{{ $dateRowSpan }}">{{ $date }}</td>
            @endif
            <td rowspan="{{ $invoiceSales->count() }}">{{ $invoiceNo }}</td>
            <td>{{ $invoiceSales[0]->quantity }}</td>
            <td>{{ $invoiceSales[0]->product_name }}</td>
            <td>{{ $invoiceSales[0]->description }}</td>
            <td>{{ $invoiceSales[0]->price }}</td>
            <td>{{ $invoiceSales[0]->total_price_formatted }}</td>
          </tr>
          @foreach($invoiceSales->slice(1) as $sale)
            <tr>
              <td>{{ $sale->quantity }}</td>
              <td>{{ $sale->product_name }}</td>
              <td>{{ $sale->description }}</td>
              <td>{{ $sale->price }}</td>
              <td>{{ $sale->total_price_formatted }}</td>
            </tr>
          @endforeach
        @endforeach
        <!-- Add a row for the grand total -->
        <tr>
          <td colspan="5" class="text-end"><strong>Grand Total:</strong></td>
          <td><strong>Rp{{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

</div>
@endsection
