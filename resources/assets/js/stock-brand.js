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

  const selectBrand = $('.select2');
  if (selectBrand.length) {
    var $this = selectBrand;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Brand',
      dropdownParent: $this.parent()
    });
  }

  $('#print-button').on('click', function () {
    var brandId = $('#brand').val();
    if (brandId) {
      window.open('/transaction/stock-brand/print/' + brandId, '_blank');
    } else {
      alert('Please select a brand.');
    }
  });
});
