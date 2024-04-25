'use strict';

function formatNumber(input) {
  // Remove non-numeric characters
  var value = input.value.replace(/\D/g, '');

  // Format with thousand separators
  var formattedValue = Number(value).toLocaleString('id-ID', { minimumFractionDigits: 0 });

  // Update input value
  input.value = formattedValue;
}

function updateTotalPrice(action) {
  var price =
    parseFloat(
      document
        .getElementById(action + '-price')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var quantity =
    parseFloat(
      document
        .getElementById(action + '-quantity')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var totalPrice = price * quantity;
  document.getElementById(action + '-total').value = totalPrice.toLocaleString('id-ID', {
    minimumFractionDigits: 0
  });
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
  var dt_table = $('.datatables-purchase-details'),
    select2 = $('.select2'),
    idPurchase = document.getElementById('id-purchase').value,
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select Product',
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
        url: '/transaction/purchase/' + idPurchase + '/get-detail',
        type: 'GET',
        dataSrc: 'data' // Specify the property containing the data array in the JSON response
      },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'product_detail_name' },
        { data: 'price' },
        { data: 'quantity' },
        { data: 'total_price' },
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
            var $product = full['product_detail_name'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center name">' +
              '<div class="d-flex flex-column">' +
              $product +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // Price
          targets: [2, 4],
          render: function (data, type, full, meta) {
            // Format price as rupiah currency and thousands
            return 'Rp' + Number(data).toLocaleString('id-ID', { minimumFractionDigits: 0 });
          }
        },
        {
          // Quantity
          targets: 3,
          render: function (data, type, full, meta) {
            // Format quantity as thousands
            return Number(data).toLocaleString('id-ID', { minimumFractionDigits: 0 });
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
            var $product = full['product_detail_name'];
            return (
              '<div class="d-inline-block text-nowrap">' +
              '<button class="btn btn-sm btn-icon edit-record" data-id="' +
              $id +
              '"><i class="bx bx-edit"></i></button>' +
              '<button class="btn btn-sm btn-icon delete-record" data-id="' +
              $id +
              '" data-product="' +
              $product +
              '"><i class="bx bx-trash"></i></button>' +
              '</div>'
            );
          }
        }
      ],
      order: [[1, 'asc']],
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
          text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Purchase Detail</span>',
          className: 'add-new btn btn-primary mx-3',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAdd'
          }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['product_detail_name'];
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
      initComplete: function () {
        $(document.body).on('click', '.edit-record', function () {
          var id = $(this).data('id');
          // Retrieve customer data via AJAX and populate the form fields
          $.ajax({
            url: '/transaction/purchase/detail/' + id,
            type: 'GET',
            headers: {
              'X-CSRF-TOKEN': token
            },
            success: function (response) {
              // Populate the form fields with related data
              $('#edit-id').val(response.id);
              $('#editForm').attr('action', '/transaction/purchase/detail/' + response.id);
              $('#edit-product').val(response.id_product_detail).trigger('change');
              $('#edit-price').val(Number(response.price).toLocaleString('id-ID', { minimumFractionDigits: 0 }));
              $('#edit-quantity').val(Number(response.quantity).toLocaleString('id-ID', { minimumFractionDigits: 0 }));
              $('#edit-total').val(Number(response.total_price).toLocaleString('id-ID', { minimumFractionDigits: 0 }));

              // Show the edit brand offcanvas
              var editOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasEdit'));
              editOffcanvas.show();
              $('.modal').modal('hide');
            },
            error: function (xhr, status, error) {
              // Handle error
              console.error(error);
            }
          });
        });

        $(document.body).on('click', '.delete-record', function () {
          var id = $(this).data('id');
          var product = $(this).data('product');
          if (confirm('Are you sure you want to delete purcase of ' + product + ' ?')) {
            // Send AJAX request to delete
            $.ajax({
              url: '/transaction/purchase/detail/' + id,
              type: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': token
              },
              success: function (response) {
                // Handle success
                $('.modal').modal('hide');
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
      }
    });
  }

  // Add event listener for edit button
  // dt.on('click', '.edit-record', function () {
  // });

  // Delete Record
  // $('.datatables-product-details tbody').on('click', '.delete-record', function () {
  // dt_user.row($(this).parents('tr')).remove().draw();
  // });

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
});

// Validation
(function () {
  const addNewForm = document.getElementById('addNewForm'),
    editForm = document.getElementById('editForm');

  // Add New Form Validation
  const fv = FormValidation.formValidation(addNewForm, {
    fields: {
      price: {
        validators: {
          notEmpty: {
            message: 'Please enter purchase price'
          }
        }
      },
      quantity: {
        validators: {
          notEmpty: {
            message: 'Please enter quantity'
          }
        }
      },
      total_price: {
        validators: {
          notEmpty: {
            message: 'Please enter total purchase price'
          }
        }
      },
      id_product_detail: {
        validators: {
          notEmpty: {
            message: 'Please select a product'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          return '.mb-3';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });

  // Edit Form Validation
  const fvEdit = FormValidation.formValidation(editForm, {
    fields: {
      price: {
        validators: {
          notEmpty: {
            message: 'Please enter purchase price'
          }
        }
      },
      quantity: {
        validators: {
          notEmpty: {
            message: 'Please enter quantity'
          }
        }
      },
      total_price: {
        validators: {
          notEmpty: {
            message: 'Please enter total purchase price'
          }
        }
      },
      id_product_detail: {
        validators: {
          notEmpty: {
            message: 'Please select a product'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        // Use this for enabling/changing valid/invalid class
        eleValidClass: '',
        rowSelector: function (field, ele) {
          // field is the field name & ele is the field element
          return '.mb-3';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      // Submit the form when all fields are valid
      defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });
})();
