'use strict';

(function () {
  const invoicePriceList = document.querySelectorAll('.invoice-price'),
    invoiceDateList = document.querySelectorAll('.date-picker');

  // Price
  if (invoicePriceList) {
    invoicePriceList.forEach(function (invoicePrice) {
      new Cleave(invoicePrice, {
        delimiter: '',
        numeral: true
      });
    });
  }

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

  // Repeater init
  if (sourceItem.length) {
    sourceItem.on('submit', function (e) {
      // e.preventDefault();
    });
    sourceItem.repeater({
      show: function () {
        $(this).slideDown();
        // Initialize tooltip on load of each item
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      },
      hide: function (e) {
        $(this).slideUp();
      }
    });
  }

  $(document).on('click', '[data-repeater-delete]', function () {
    var repeaterWrapper = $(this).closest('.repeater-wrapper');
    repeaterWrapper.remove();
  });
});
