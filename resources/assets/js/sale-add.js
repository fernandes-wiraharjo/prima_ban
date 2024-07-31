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
    for (let id in products) {
      options += `<option value="${id}">${products[id]}</option>`;
    }
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
          </div>
          <div class="col-md-2 col-12 mb-md-0 mb-3">
            <p class="mb-2 repeater-title">Qty</p>
            <input type="text" class="form-control invoice-item-qty" placeholder="1" name="group-a[${itemCount}][quantity]" />
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
});
