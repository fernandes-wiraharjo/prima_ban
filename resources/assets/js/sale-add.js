'use strict';

(function () {
  // const invoicePriceList = document.querySelectorAll('.invoice-price'),
  const invoiceDateList = document.querySelectorAll('.date-picker'),
    selectCustomer = $('.select-customer'),
    selectItem = $('.item-details');

  //select customer
  if (selectCustomer.length) {
    var $this = selectCustomer;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Customer',
      dropdownParent: $this.parent()
    });
  }

  if (selectItem.length) {
    var $this = selectItem;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Item',
      dropdownParent: $this.parent()
    });
  }

  // Price
  // if (invoicePriceList) {
  //   invoicePriceList.forEach(function (invoicePrice) {
  //     new Cleave(invoicePrice, {
  //       delimiter: '',
  //       numeral: true
  //     });
  //   });
  // }

  // Datepicker
  if (invoiceDateList) {
    invoiceDateList.forEach(function (invoiceDateEl) {
      invoiceDateEl.flatpickr({
        monthSelectorType: 'static'
      });
    });
  }

  $('form').on('submit', function (e) {
    e.preventDefault(); // Prevent form submission

    // Get the invoice number from the input field
    var invoiceNo = $('input[name="invoice_no"]').val();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Make an AJAX request to check if the invoice number is unique
    $.ajax({
      url: '/check-invoice-no', // Create a route for checking uniqueness
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken
      },
      data: {
        invoice_no: invoiceNo
      },
      success: function (response) {
        if (response.is_unique) {
          // If unique, submit the form
          e.currentTarget.submit();
        } else {
          // Show an error message if not unique
          alert('Invoice number already exists.');
        }
      },
      error: function () {
        alert('Something went wrong.');
      }
    });
  });
})();

// repeater (jquery)
$(function () {
  var sourceItem = $('.source-item');
  var itemCount = $('.repeater-wrapper > div').length;

  // Repeater init
  if (sourceItem.length) {
    sourceItem.on('submit', function (e) {
      // e.preventDefault();
    });
    // sourceItem.repeater({
    //   show: function () {
    //     $(this).slideDown();
    //     // Initialize tooltip on load of each item
    //     const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    //     tooltipTriggerList.map(function (tooltipTriggerEl) {
    //       return new bootstrap.Tooltip(tooltipTriggerEl);
    //     });
    //   },
    //   hide: function (e) {
    //     $(this).slideUp();
    //   }
    // });
  }

  $(document).on('click', '[data-repeater-delete]', function () {
    var repeaterWrapper = $(this).closest('.repeater-wrapper > div');
    repeaterWrapper.remove();
    updateNames();
  });

  // Function to dynamically create select2 options
  function getSelectOptions(products) {
    let options = '<option selected disabled>Item</option>';
    products.forEach(product => {
      options += `<option value="${product.id}"
                    data-type="${product.type}"
                    data-price-user-cash="${product.final_price_user_cash}"
                    data-price-user-tempo="${product.final_price_user_tempo}"
                    data-price-toko-cash="${product.final_price_toko_cash}"
                    data-price-toko-tempo="${product.final_price_toko_tempo}">
                    ${product.name}
                  </option>`;
    });
    return options;
  }

  $('#add-item').on('click', function () {
    const newItemHtml = `
      <div class="d-flex border rounded position-relative pe-0 mt-5">
        <div class="row w-100 m-0 p-3">
          <div class="col-md-10 col-12 mb-md-0 mb-3 ps-md-0">
            <p class="mb-2 repeater-title">Barang</p>
            <select class="select2 form-select item-details mb-2" name="group-a[${itemCount}][item]">
              ${getSelectOptions(products)}
            </select>
            <span class="item-price"></span>
          </div>
          <div class="col-md-2 col-12 mb-md-0 mb-3">
            <p class="mb-2 repeater-title">Qty</p>
            <input type="text" class="form-control invoice-item-qty" placeholder="1" name="group-a[${itemCount}][quantity]" required />
          </div>
        </div>
        <div class="d-flex flex-column align-items-center justify-content-between border-start p-2">
          <i class="bx bx-x fs-4 text-muted cursor-pointer" data-repeater-delete></i>
        </div>
      </div>`;

    $('.repeater-wrapper').append(newItemHtml);
    itemCount++;
    $('.select2').select2(); // Re-initialize Select2 for newly added items
  });

  function updateNames() {
    $('.repeater-wrapper > div').each(function (index) {
      $(this).find('.item-details').attr('name', `group-a[${index}][item]`);
      $(this).find('.invoice-item-qty').attr('name', `group-a[${index}][quantity]`);
    });
    itemCount = $('.repeater-wrapper > div').length;
  }

  // Handle selection change to update price
  $(document).on('change', '.item-details', function () {
    const selectedOption = $(this).find(':selected');
    const type = selectedOption.data('type'); //product type
    const customerType = $('#customer').find(':selected').data('type');
    const paymentType = $('#payment-type').find(':selected').val();

    let price = '';

    if (!customerType || !paymentType) {
      alert('Please select customer and payment type');
      return;
    }

    if (type === 'product') {
      if (customerType === 'user' && paymentType === 'cash') {
        price = selectedOption.data('price-user-cash');
      } else if (customerType === 'user' && paymentType === 'tempo') {
        price = selectedOption.data('price-user-tempo');
      } else if (customerType === 'toko' && paymentType === 'cash') {
        price = selectedOption.data('price-toko-cash');
      } else if (customerType === 'toko' && paymentType === 'tempo') {
        price = selectedOption.data('price-toko-tempo');
      }
    } else if (type === 'service') {
      price = selectedOption.data('price-user-cash'); // Only one price for services
    }

    // Format the price using thousand separator
    if (price) {
      price = parseFloat(price).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
    }

    // Display the price next to the selected item
    $(this).closest('.row').find('.item-price').text(`(${price})`);
  });
});
