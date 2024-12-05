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

  const selectCustomer = $('.select2');
  if (selectCustomer.length) {
    var $this = selectCustomer;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Customer',
      dropdownParent: $this.parent()
    });
  }

  $('#print-button').on('click', function () {
    var customerId = $('#customer').val();
    if (customerId) {
      window.open('/transaction/sale/print/by-customer/' + customerId, '_blank');
    } else {
      alert('Please select a customer.');
    }
  });
});
