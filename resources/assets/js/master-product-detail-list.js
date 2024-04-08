'use strict';

function formatNumber(input) {
  // Remove non-numeric characters
  var value = input.value.replace(/\D/g, '');

  // Format with thousand separators
  var formattedValue = Number(value).toLocaleString('id-ID', { minimumFractionDigits: 0 });

  // Update input value
  input.value = formattedValue;
}

function updateFinalPriceUserCash(action) {
  var price =
    parseFloat(
      document
        .getElementById(action + '-price-user-cash')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var discount =
    parseFloat(
      document
        .getElementById(action + '-discount-user-cash')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var finalPrice = price - discount;
  document.getElementById(action + '-final-price-user-cash').value = finalPrice.toLocaleString('id-ID', {
    minimumFractionDigits: 0
  });
}

function updateFinalPriceUserTempo(action) {
  var price =
    parseFloat(
      document
        .getElementById(action + '-price-user-tempo')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var discount =
    parseFloat(
      document
        .getElementById(action + '-discount-user-tempo')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var finalPrice = price - discount;
  document.getElementById(action + '-final-price-user-tempo').value = finalPrice.toLocaleString('id-ID', {
    minimumFractionDigits: 0
  });
}

function updateFinalPriceTokoCash(action) {
  var price =
    parseFloat(
      document
        .getElementById(action + '-price-toko-cash')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var discount =
    parseFloat(
      document
        .getElementById(action + '-discount-toko-cash')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var finalPrice = price - discount;
  document.getElementById(action + '-final-price-toko-cash').value = finalPrice.toLocaleString('id-ID', {
    minimumFractionDigits: 0
  });
}

function updateFinalPriceTokoTempo(action) {
  var price =
    parseFloat(
      document
        .getElementById(action + '-price-toko-tempo')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var discount =
    parseFloat(
      document
        .getElementById(action + '-discount-toko-tempo')
        .value.replace(/\./g, '')
        .replace(',', '.')
    ) || 0;
  var finalPrice = price - discount;
  document.getElementById(action + '-final-price-toko-tempo').value = finalPrice.toLocaleString('id-ID', {
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
  var dt_table = $('.datatables-product-details'),
    select2 = $('.select2'),
    statusObj = {
      0: { title: 'Inactive', class: 'bg-label-secondary' },
      1: { title: 'Active', class: 'bg-label-success' }
    },
    idProduct = document.getElementById('id-product').value,
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select Size',
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
        url: '/master/product/' + idProduct + '/get-detail',
        type: 'GET',
        dataSrc: 'data' // Specify the property containing the data array in the JSON response
      },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'size_name' },
        { data: 'final_price_user_cash' },
        { data: 'final_price_user_tempo' },
        { data: 'final_price_toko_cash' },
        { data: 'final_price_toko_tempo' },
        { data: 'quantity' },
        { data: 'is_active' },
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
            var $sizeName = full['size_name'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center name">' +
              '<div class="d-flex flex-column">' +
              $sizeName +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // Price
          targets: [2, 3, 4, 5],
          render: function (data, type, full, meta) {
            // Format quantity as thousands
            return 'Rp' + Number(data).toLocaleString('id-ID', { minimumFractionDigits: 0 });
          }
        },
        {
          // Quantity
          targets: 6,
          render: function (data, type, full, meta) {
            // Format quantity as thousands
            return Number(data).toLocaleString('id-ID', { minimumFractionDigits: 0 });
          }
        },
        {
          // Status
          targets: 7,
          render: function (data, type, full, meta) {
            var $status = full['is_active'];

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
            var $sizeName = full['size_name'];
            return (
              '<div class="d-inline-block text-nowrap">' +
              '<button class="btn btn-sm btn-icon edit-record" data-id="' +
              $id +
              '"><i class="bx bx-edit"></i></button>' +
              '<button class="btn btn-sm btn-icon delete-record" data-id="' +
              $id +
              '" data-sizename="' +
              $sizeName +
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
          text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Product Detail</span>',
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
              return 'Details of ' + data['size_name'];
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
            url: '/master/product/detail/' + id,
            type: 'GET',
            headers: {
              'X-CSRF-TOKEN': token
            },
            success: function (response) {
              // Populate the form fields with related data
              $('#edit-id').val(response.id);
              $('#editForm').attr('action', '/master/product/detail/' + response.id);
              $('#edit-size').val(response.id_size).trigger('change');
              $('#edit-price-user-cash').val(
                Number(response.price_user_cash).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-discount-user-cash').val(
                Number(response.discount_user_cash).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-final-price-user-cash').val(
                Number(response.final_price_user_cash).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-price-user-tempo').val(
                Number(response.price_user_tempo).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-discount-user-tempo').val(
                Number(response.discount_user_tempo).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-final-price-user-tempo').val(
                Number(response.final_price_user_tempo).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-price-toko-cash').val(
                Number(response.price_toko_cash).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-discount-toko-cash').val(
                Number(response.discount_toko_cash).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-final-price-toko-cash').val(
                Number(response.final_price_toko_cash).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-price-toko-tempo').val(
                Number(response.price_toko_tempo).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-discount-toko-tempo').val(
                Number(response.discount_toko_tempo).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-final-price-toko-tempo').val(
                Number(response.final_price_toko_tempo).toLocaleString('id-ID', { minimumFractionDigits: 0 })
              );
              $('#edit-quantity').val(Number(response.quantity).toLocaleString('id-ID', { minimumFractionDigits: 0 }));
              $('#edit-status_' + response.is_active).prop('checked', true);

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
          var sizeName = $(this).data('sizename');
          if (confirm('Are you sure you want to delete size ' + sizeName + ' ?')) {
            // Send AJAX request to delete
            $.ajax({
              url: '/master/product/detail/' + id,
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
      price_user_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter price user cash'
          }
        }
      },
      discount_user_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter discount user cash'
          }
        }
      },
      final_price_user_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter final price user cash'
          }
        }
      },
      price_user_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter price user tempo'
          }
        }
      },
      discount_user_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter discount user tempo'
          }
        }
      },
      final_price_user_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter final price user tempo'
          }
        }
      },
      price_toko_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter price toko cash'
          }
        }
      },
      discount_toko_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter discount toko cash'
          }
        }
      },
      final_price_toko_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter final price toko cash'
          }
        }
      },
      price_toko_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter price toko tempo'
          }
        }
      },
      discount_toko_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter discount toko tempo'
          }
        }
      },
      final_price_toko_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter final price toko tempo'
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
      id_size: {
        validators: {
          notEmpty: {
            message: 'Please select a size'
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
      price_user_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter price user cash'
          }
        }
      },
      discount_user_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter discount user cash'
          }
        }
      },
      final_price_user_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter final price user cash'
          }
        }
      },
      price_user_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter price user tempo'
          }
        }
      },
      discount_user_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter discount user tempo'
          }
        }
      },
      final_price_user_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter final price user tempo'
          }
        }
      },
      price_toko_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter price toko cash'
          }
        }
      },
      discount_toko_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter discount toko cash'
          }
        }
      },
      final_price_toko_cash: {
        validators: {
          notEmpty: {
            message: 'Please enter final price toko cash'
          }
        }
      },
      price_toko_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter price toko tempo'
          }
        }
      },
      discount_toko_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter discount toko tempo'
          }
        }
      },
      final_price_toko_tempo: {
        validators: {
          notEmpty: {
            message: 'Please enter final price toko tempo'
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
      id_size: {
        validators: {
          notEmpty: {
            message: 'Please select a size'
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
