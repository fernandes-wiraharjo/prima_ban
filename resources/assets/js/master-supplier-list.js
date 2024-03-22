/**
 * Page User List
 */

'use strict';

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
  var dt_supplier_table = $('.datatables-suppliers'),
    statusObj = {
      0: { title: 'Inactive', class: 'bg-label-secondary' },
      1: { title: 'Active', class: 'bg-label-success' }
    },
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Customers datatable
  if (dt_supplier_table.length) {
    var dt_supplier = dt_supplier_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '/master/supplier/get',
        type: 'GET',
        dataSrc: 'data' // Specify the property containing the data array in the JSON response
      },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'name' },
        { data: 'address' },
        { data: 'phone_no' },
        { data: 'pic_name' },
        { data: 'bank_account_no' },
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
          // Customer Name
          targets: 1,
          responsivePriority: 4,
          render: function (data, type, full, meta) {
            var $name = full['name'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center user-name">' +
              '<div class="d-flex flex-column">' +
              $name +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // Customer Status
          targets: 6,
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
            var $name = full['name'];
            return (
              '<div class="d-inline-block text-nowrap">' +
              '<button class="btn btn-sm btn-icon edit-record" data-id="' +
              $id +
              '"><i class="bx bx-edit"></i></button>' +
              '<button class="btn btn-sm btn-icon delete-record" data-id="' +
              $id +
              '" data-name="' +
              $name +
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
          text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New Supplier</span>',
          className: 'add-new btn btn-primary mx-3',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAddSupplier'
          }
        }
      ],
      // For responsive popup
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['name'];
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
  dt_supplier_table.on('click', '.edit-record', function () {
    var supplierId = $(this).data('id');
    // Retrieve customer data via AJAX and populate the form fields
    $.ajax({
      url: '/master/supplier/' + supplierId,
      type: 'GET',
      headers: {
        'X-CSRF-TOKEN': token
      },
      success: function (response) {
        // Populate the form fields with customer data
        $('#edit-supplier-id').val(response.id);
        $('#editSupplierForm').attr('action', '/master/supplier/' + response.id);
        $('#edit-supplier-name').val(response.name);
        $('#edit-supplier-address').val(response.address);
        $('#edit-supplier-phone').val(response.phone_no);
        $('#edit-supplier-pic').val(response.pic_name);
        $('#edit-supplier-bank-account-no').val(response.bank_account_no);
        $('#edit-status_' + response.is_active).prop('checked', true);

        // Show the edit supplier offcanvas
        var editSupplierOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasEditSupplier'));
        editSupplierOffcanvas.show();
      },
      error: function (xhr, status, error) {
        // Handle error
        console.error(error);
      }
    });
  });

  // Delete Record
  $('.datatables-suppliers tbody').on('click', '.delete-record', function () {
    // dt_user.row($(this).parents('tr')).remove().draw();
    var supplierId = $(this).data('id');
    var name = $(this).data('name');
    if (confirm('Are you sure you want to delete supplier ' + name + ' ?')) {
      // Send AJAX request to delete supplier
      $.ajax({
        url: '/master/supplier/' + supplierId,
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': token
        },
        success: function (response) {
          // Handle success
          alert(response.message);
          // Refresh datatable
          dt_supplier.ajax.reload();
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

// Validation
(function () {
  const addNewSupplierForm = document.getElementById('addNewSupplierForm'),
    editSupplierForm = document.getElementById('editSupplierForm');

  // Add New Supplier Form Validation
  const fv = FormValidation.formValidation(addNewSupplierForm, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      phone_no: {
        validators: {
          notEmpty: {
            message: 'Please enter phone no'
          }
        }
      },
      pic_name: {
        validators: {
          notEmpty: {
            message: 'Please enter pic name'
          }
        }
      },
      bank_account_no: {
        validators: {
          notEmpty: {
            message: 'Please enter bank account number'
          }
        }
      },
      address: {
        validators: {
          notEmpty: {
            message: 'Please enter address'
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

  // Edit User Form Validation
  const fvEdit = FormValidation.formValidation(editSupplierForm, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      phone_no: {
        validators: {
          notEmpty: {
            message: 'Please enter phone no'
          }
        }
      },
      pic_name: {
        validators: {
          notEmpty: {
            message: 'Please enter pic name'
          }
        }
      },
      bank_account_no: {
        validators: {
          notEmpty: {
            message: 'Please enter bank account number'
          }
        }
      },
      address: {
        validators: {
          notEmpty: {
            message: 'Please enter address'
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
