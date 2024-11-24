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

  const selectSize = $('.select2');
  if (selectSize.length) {
    var $this = selectSize;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Size',
      dropdownParent: $this.parent()
    });
  }

  $('#print-button').on('click', function () {
    var size = $('#size').val();
    if (size) {
      window.open('/transaction/stock-size/print/' + size, '_blank');
    } else {
      alert('Please select a size.');
    }
  });
});
