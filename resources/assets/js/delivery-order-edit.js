'use strict';

(function () {
  var selectSupplier = $('.select-supplier'),
    selectItem = $('.item-details');

  if (selectSupplier.length) {
    var $this = selectSupplier;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Supplier',
      dropdownParent: $this.parent()
    });
  }

  if (selectItem.length) {
    $(selectItem).each(function () {
      if (!$(this).data('select2')) {
        $(this)
          .wrap('<div class="position-relative"></div>')
          .select2({
            placeholder: 'Item',
            dropdownParent: $(this).parent()
          });
      }
    });
  }

  const invoiceItemPriceList = document.querySelectorAll('.invoice-item-price'),
    invoiceItemQtyList = document.querySelectorAll('.invoice-item-qty'),
    invoiceDateList = document.querySelectorAll('.date-picker');

  // Price
  if (invoiceItemPriceList) {
    invoiceItemPriceList.forEach(function (invoiceItemPrice) {
      new Cleave(invoiceItemPrice, {
        delimiter: '',
        numeral: true
      });
    });
  }

  // Qty
  // if (invoiceItemQtyList) {
  //   invoiceItemQtyList.forEach(function (invoiceItemQty) {
  //     new Cleave(invoiceItemQty, {
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
  var applyChangesBtn = $('.btn-apply-changes'),
    discount,
    tax1,
    tax2,
    discountInput,
    tax1Input,
    tax2Input,
    sourceItem = $('.source-item'),
    itemCount = $('.repeater-wrapper > div').length,
    adminDetails = {
      'App Design': 'Designed UI kit & app pages.',
      'App Customization': 'Customization & Bug Fixes.',
      'ABC Template': 'Bootstrap 4 admin template.',
      'App Development': 'Native App Development.'
    };

  // Prevent dropdown from closing on tax change
  $(document).on('click', '.tax-select', function (e) {
    e.stopPropagation();
  });

  // On tax change update it's value value
  function updateValue(listener, el) {
    listener.closest('.repeater-wrapper').find(el).text(listener.val());
  }

  // Apply item changes btn
  if (applyChangesBtn.length) {
    $(document).on('click', '.btn-apply-changes', function (e) {
      var $this = $(this);
      tax1Input = $this.closest('.dropdown-menu').find('#taxInput1');
      tax2Input = $this.closest('.dropdown-menu').find('#taxInput2');
      discountInput = $this.closest('.dropdown-menu').find('#discountInput');
      tax1 = $this.closest('.repeater-wrapper').find('.tax-1');
      tax2 = $this.closest('.repeater-wrapper').find('.tax-2');
      discount = $('.discount');

      if (tax1Input.val() !== null) {
        updateValue(tax1Input, tax1);
      }

      if (tax2Input.val() !== null) {
        updateValue(tax2Input, tax2);
      }

      if (discountInput.val().length) {
        $this
          .closest('.repeater-wrapper')
          .find(discount)
          .text(discountInput.val() + '%');
      }
    });
  }

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

  // Item details select onchange
  // $(document).on('change', '.item-details', function () {
  //   var $this = $(this),
  //     value = adminDetails[$this.val()];
  //   if ($this.next('textarea').length) {
  //     $this.next('textarea').val(value);
  //   } else {
  //     $this.after('<textarea class="form-control" rows="2">' + value + '</textarea>');
  //   }
  // });

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
