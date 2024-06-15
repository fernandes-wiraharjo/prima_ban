'use strict';

function redirectToIndexAdd() {
  window.location.href = '/transaction/sale/get/add';
}

// Datatable (jquery)
$(function () {
  let borderColor, bodyBg, headingColor;

  if (isDarkStyle) {
    borderColor = config.colors_dark.borderColor;
    bodyBg = config.colors_dark.bodyBg;
    headingColor = config.colors_dark.headingColor;
  } else {
    borderColor = config.colors.borderColor;
    bodyBg = config.colors.bodyBg;
    headingColor = config.colors.headingColor;
  }

  // Variable declaration for table
  var dt_table = $('.datatables-sale'),
    statusObj = {
      'belum lunas': { title: 'Belum Lunas', class: 'bg-label-danger' },
      lunas: { title: 'Lunas', class: 'bg-label-success' }
    },
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  //datatable
  if (dt_table.length) {
    var dt = dt_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '/transaction/sale/get',
        type: 'GET',
        dataSrc: 'data' // Specify the property containing the data array in the JSON response
      },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'customer_name' },
        { data: 'formatted_date' },
        { data: 'invoice_no' },
        // { data: 'subtotal_price' },
        // { data: 'discount' },
        { data: 'final_price' },
        // { data: 'bank_account_no' },
        { data: 'status' },
        { data: 'username' },
        { data: 'action' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          // id tanda terima
          targets: 1,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $customer_name = full['customer_name'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center name">' +
              '<div class="d-flex flex-column">' +
              $customer_name +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          targets: [4],
          render: function (data, type, full, meta) {
            // Format number
            return 'Rp' + Number(data).toLocaleString('id-ID', { minimumFractionDigits: 0 });
          }
        },
        {
          // Status
          targets: 5,
          render: function (data, type, full, meta) {
            var $status = full['status'];
            return '<span class="badge ' + statusObj[$status].class + '">' + statusObj[$status].title + '</span>';
          }
        },
        {
          // Actions
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            var $id = full['id'];
            var $customerName = full['customer_name'];
            var $invoiceNo = full['invoice_no'];
            return (
              '<div class="d-inline-block text-nowrap">' +
              '<button class="btn btn-sm btn-icon edit-record" title="edit/see details" data-id="' +
              $id +
              '"><i class="bx bx-edit"></i></button>' +
              '<button class="btn btn-sm btn-icon print-record" title="preview/print" data-id="' +
              $id +
              '"><i class="bx bx-printer"></i></button>' +
              '<button class="btn btn-sm btn-icon delete-record" data-id="' +
              $id +
              '" data-customer="' +
              $customerName +
              '" data-invoice="' +
              $invoiceNo +
              '"><i class="bx bx-trash"></i></button>' +
              '</div>'
            );
          }
        }
      ],
      order: [[2, 'desc']],
      dom:
        '<"row mx-2"' +
        '<"col-md-2"<"me-3"l>>' +
        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search..'
      },
      // Buttons with Dropdown
      buttons: [
        {
          text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Sale</span>',
          className: 'add-new btn btn-primary mx-3',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAdd',
            onclick: 'redirectToIndexAdd()'
          }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['customer_name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      },
      initComplete: function () {}
    });
  }

  // Add event listener for edit button
  dt.on('click', '.edit-record', function () {
    var id = $(this).data('id');
    window.location.href = '/transaction/sale/' + id;
  });

  dt.on('click', '.print-record', function () {
    var id = $(this).data('id');
    window.location.href = '/transaction/sale/' + id + '/preview';
  });

  // Delete Record
  $('.datatables-sale tbody').on('click', '.delete-record', function () {
    // dt_user.row($(this).parents('tr')).remove().draw();
    var id = $(this).data('id');
    var customer = $(this).data('customer');
    var invoice = $(this).data('invoice');
    if (
      confirm('Are you sure you want to delete sale with customer "' + customer + '" and invoice "' + invoice + '" ?')
    ) {
      // Send AJAX request to delete
      $.ajax({
        url: '/transaction/sale/' + id,
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': token
        },
        success: function (response) {
          // Handle success
          alert(response.message);
          // Refresh datatable
          dt.ajax.reload();
        },
        error: function (xhr, status, error) {
          // Handle error
          console.error(error);
        }
      });
    }
  });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
});
