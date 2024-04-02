'use strict';

function redirectToIndexAdd() {
  window.location.href = '/transaction/delivery-order/get/add';
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
  var dt_table = $('.datatables-delivery-orders'),
    select2 = $('.select2'),
    statusObj = {
      0: { title: 'Inactive', class: 'bg-label-secondary' },
      1: { title: 'Active', class: 'bg-label-success' }
    },
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select Brand',
        dropdownParent: $this.parent()
      });
    });
  }

  //datatable
  if (dt_table.length) {
    var dt = dt_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '/transaction/delivery-order/get',
        type: 'GET',
        dataSrc: 'data' // Specify the property containing the data array in the JSON response
      },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'id' },
        { data: 'formatted_date' },
        { data: 'supplier_name' },
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
          // Name
          targets: 1,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $id = full['id'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center name">' +
              '<div class="d-flex flex-column">' +
              $id +
              '</div>' +
              '</div>';
            return $row_output;
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
            var $supplierName = full['supplier_name'];
            return (
              '<div class="d-inline-block text-nowrap">' +
              '<button class="btn btn-sm btn-icon edit-record" title="edit/see details" data-id="' +
              $id +
              '"><i class="bx bx-edit"></i></button>' +
              '<button class="btn btn-sm btn-icon delete-record" data-id="' +
              $id +
              '" data-supplier="' +
              $supplierName +
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
          text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Delivery Order</span>',
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
              return 'Details of ' + data['id'];
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
    window.location.href = '/transaction/delivery-order/' + id;
  });

  // Delete Record
  $('.datatables-delivery-orders tbody').on('click', '.delete-record', function () {
    // dt_user.row($(this).parents('tr')).remove().draw();
    var id = $(this).data('id');
    var supplierName = $(this).data('supplier');
    if (
      confirm('Are you sure you want to delete delivery order with id ' + id + ' and supplier ' + supplierName + ' ?')
    ) {
      // Send AJAX request to delete
      $.ajax({
        url: '/transaction/delivery-order/' + id,
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
