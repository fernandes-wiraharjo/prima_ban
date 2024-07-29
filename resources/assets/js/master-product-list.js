'use strict';

function redirectToProductDetail(button) {
  var idProduct = button.getAttribute('data-id');
  var productName = button.getAttribute('data-name');
  var url = '/master/product/' + idProduct + '/' + productName + '/detail';
  window.location.href = url;
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
  var dt_table = $('.datatables-products'),
    selectParentBrand = $('.selectParentBrand'),
    selectBrand = $('.selectBrand'),
    selectPattern = $('.selectPattern'),
    selectUOM = $('.selectUOM'),
    statusObj = {
      0: { title: 'Inactive', class: 'bg-label-secondary' },
      1: { title: 'Active', class: 'bg-label-success' }
    },
    token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  if (selectParentBrand.length) {
    selectParentBrand.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select Brand',
        dropdownParent: $this.parent()
      });
    });
  }

  if (selectBrand.length) {
    selectBrand.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select Group Type',
        dropdownParent: $this.parent()
      });
    });
  }

  if (selectPattern.length) {
    selectPattern.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select Pattern',
        dropdownParent: $this.parent()
      });
    });
  }

  if (selectUOM.length) {
    selectUOM.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select UOM',
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
        url: '/master/product/get',
        type: 'GET',
        dataSrc: 'data' // Specify the property containing the data array in the JSON response
      },
      columns: [
        // columns according to JSON
        { data: '' },
        { data: 'name' },
        { data: 'parent_brand' },
        { data: 'brand_name' },
        { data: 'pattern_name' },
        { data: 'uom_name' },
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
            var $name = full['name'];
            // Creates full output for row
            var $row_output =
              '<div class="d-flex justify-content-start align-items-center name">' +
              '<div class="d-flex flex-column">' +
              $name +
              '</div>' +
              '</div>';
            return $row_output;
          }
        },
        {
          // Status
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
              // '<button class="btn btn-sm btn-icon delete-record" data-id="' +
              // $id +
              // '" data-name="' +
              // $name +
              // '"><i class="bx bx-trash"></i></button>' +
              '<button class="btn btn-sm btn-icon" data-id="' +
              $id +
              '" data-name="' +
              $name +
              '" onclick="redirectToProductDetail(this)" title="View Product Details"><i class="bx bx-list-ul"></i></button>' +
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
          text: '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New Product</span>',
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

  function setPatternByBrand(parentBrand, brandId, patternId) {
    if (brandId) {
      $.ajax({
        url: '/master/pattern/brand/' + parentBrand + '/' + brandId,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          $('#edit-pattern').empty();
          $.each(data, function (key, value) {
            $('#edit-pattern').append('<option value="' + value.id + '">' + value.name + '</option>');
          });

          $('#edit-pattern').val(patternId).trigger('change');
        }
      });
    } else {
      $('#edit-pattern').empty();
    }
  }

  // Add event listener for edit button
  dt.on('click', '.edit-record', function () {
    var id = $(this).data('id');
    // Retrieve customer data via AJAX and populate the form fields
    $.ajax({
      url: '/master/product/' + id,
      type: 'GET',
      headers: {
        'X-CSRF-TOKEN': token
      },
      success: function (response) {
        // Populate the form fields with customer data
        $('#edit-id').val(response.id);
        $('#editForm').attr('action', '/master/product/' + response.id);
        $('#edit-parent-brand').val(response.parent_brand).trigger('change');
        $('#edit-brand').val(response.id_brand).trigger('change');
        setPatternByBrand(response.parent_brand, response.id_brand, response.id_pattern);
        // $('#edit-pattern').val(response.id_pattern).trigger('change');
        $('#edit-uom').val(response.id_uom).trigger('change');
        $('#edit-name').val(response.name);
        $('#edit-status_' + response.is_active).prop('checked', true);

        // Show the edit brand offcanvas
        var editOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasEdit'));
        editOffcanvas.show();
      },
      error: function (xhr, status, error) {
        // Handle error
        console.error(error);
      }
    });
  });

  // Delete Record
  $('.datatables-products tbody').on('click', '.delete-record', function () {
    // dt_user.row($(this).parents('tr')).remove().draw();
    var id = $(this).data('id');
    var name = $(this).data('name');
    if (confirm('Are you sure you want to delete product ' + name + ' ?')) {
      // Send AJAX request to delete
      $.ajax({
        url: '/master/product/' + id,
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

// Validation
(function () {
  const addNewForm = document.getElementById('addNewForm'),
    editForm = document.getElementById('editForm');

  // Add New Form Validation
  const fv = FormValidation.formValidation(addNewForm, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      parent_brand: {
        validators: {
          notEmpty: {
            message: 'Please select a brand'
          }
        }
      },
      id_brand: {
        validators: {
          notEmpty: {
            message: 'Please select a group type'
          }
        }
      },
      id_pattern: {
        validators: {
          notEmpty: {
            message: 'Please select a pattern'
          }
        }
      },
      id_uom: {
        validators: {
          notEmpty: {
            message: 'Please select a uom'
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
      name: {
        validators: {
          notEmpty: {
            message: 'Please enter name'
          }
        }
      },
      parent_brand: {
        validators: {
          notEmpty: {
            message: 'Please select a brand'
          }
        }
      },
      id_brand: {
        validators: {
          notEmpty: {
            message: 'Please select a group type'
          }
        }
      },
      id_pattern: {
        validators: {
          notEmpty: {
            message: 'Please select a pattern'
          }
        }
      },
      id_uom: {
        validators: {
          notEmpty: {
            message: 'Please select a uom'
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

  //slBrand onchange
  $('#brand').on('change', function () {
    var parentBrand = $('#parent-brand').val();
    var brandId = $(this).val();
    if (brandId && parentBrand) {
      $.ajax({
        url: '/master/pattern/brand/' + parentBrand + '/' + brandId,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          $('#pattern').empty();
          $.each(data, function (key, value) {
            $('#pattern').append('<option value="' + value.id + '">' + value.name + '</option>');
          });
        }
      });
    } else {
      $('#pattern').empty();
    }
  });

  $('#parent-brand').on('change', function () {
    var parentBrand = $(this).val();
    var brandId = $('#brand').val();
    if (brandId && parentBrand) {
      $.ajax({
        url: '/master/pattern/brand/' + parentBrand + '/' + brandId,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          $('#pattern').empty();
          $.each(data, function (key, value) {
            $('#pattern').append('<option value="' + value.id + '">' + value.name + '</option>');
          });
        }
      });
    } else {
      $('#pattern').empty();
    }
  });

  $('#edit-brand').on('change', function () {
    var parentBrand = $('#edit-parent-brand').val();
    var brandId = $(this).val();
    if (brandId && parentBrand) {
      $.ajax({
        url: '/master/pattern/brand/' + parentBrand + '/' + brandId,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          $('#edit-pattern').empty();
          $.each(data, function (key, value) {
            $('#edit-pattern').append('<option value="' + value.id + '">' + value.name + '</option>');
          });
        }
      });
    } else {
      $('#edit-pattern').empty();
    }
  });

  $('#edit-parent-brand').on('change', function () {
    var parentBrand = $(this).val();
    var brandId = $('#edit-brand').val();
    if (brandId && parentBrand) {
      $.ajax({
        url: '/master/pattern/brand/' + parentBrand + '/' + brandId,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          $('#edit-pattern').empty();
          $.each(data, function (key, value) {
            $('#edit-pattern').append('<option value="' + value.id + '">' + value.name + '</option>');
          });
        }
      });
    } else {
      $('#edit-pattern').empty();
    }
  });
})();
