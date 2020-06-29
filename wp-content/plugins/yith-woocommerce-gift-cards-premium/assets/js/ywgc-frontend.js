;(function ($) {

  if (typeof ywgc_data === "undefined") {
    return;
  }

  var form_container = $("div.yith-ywgc-gift-this-product-form-container");

  $(document).on( 'click', '.ywgc-choose-image.ywgc-choose-template', function (e) {
    e.preventDefault();
    $('#yith-ywgc .yith-ywgc-popup-close').show();
  });

  //Manage the picture changed event
  $(document).on('ywgc-picture-changed', function ( event, type, id ) {

      $( '.ywgc-template-design' ).remove();
      $( '.ywgc-design-type' ).remove();

      if (id == 'custom')
        type = 'custom';

      if (id == 'custom-modal')
        type = 'custom-modal';

      $( 'form.cart' ).append('<input type="hidden" class="ywgc-design-type" name="ywgc-design-type" value="'+ type +'">');

      $( 'form.cart' ).append('<input type="hidden" class="ywgc-template-design" name="ywgc-template-design" value="' + id + '">');
    }
  );

  $(document).ready(function () {

    var datePicker = $(".datepicker");
    datePicker.datepicker({dateFormat: ywgc_data.date_format, minDate: +1, maxDate: "+1Y"});

    $('.ywgc-choose-design-preview .ywgc-design-list li:first-child .ywgc-preset-image img').click();

    if ( ywgc_data.gift_this_product_automatically ){
      $('a#give-as-present').click();
    }
  });


  show_hide_add_to_cart_button();

  /**
   * Manage the selected design images
   */
  var wc_gallery_image = $( '.product-type-gift-card .woocommerce-product-gallery__image a' );
  var wc_gallery_image_placeholder = $( '.product-type-gift-card .woocommerce-product-gallery__image--placeholder' );

  $( '.ywgc-preset-image.ywgc-default-product-image img' ).addClass( 'selected_design_image' );

  $(document).on( 'click', 'form.gift-cards_form.cart .ywgc-preset-image img', function (e) {
    e.preventDefault();

    var id = $(this).parent().data('design-id');

    $(document).trigger('ywgc-picture-changed', ['template', id]);

    $('a.lightbox-added').remove();

    if ($('.woocommerce-product-gallery__wrapper').children().length != 0) {
      $('.woocommerce-product-gallery__image').remove();

      var image_url = $(this).parent().data('design-url');
      var srcset = $(this).attr('srcset');
      var src = $(this).attr('src');

      if ( $(this).hasClass('custom-selected-image') || $(this).hasClass('custom-modal-selected-image')){
        image_url = src;
      }

      if (wc_gallery_image_placeholder.length != 0 ){
        wc_gallery_image_placeholder.remove();
      }

      $('<div data-thumb="' + src + '" data-thumb-alt class="woocommerce-product-gallery__image"><a href="' + image_url + '"><img src="' + image_url + '" class="wp-post-image size-full" alt="" data-caption="" data-src="' + image_url + '" data-large_image="' + image_url + '" data-large_image_width="1024" data-large_image_height="1024" sizes="(max-width: 600px) 100vw, 600px"' + srcset + ' width="600" height="600"></a></div>').insertBefore('.ywgc-main-form-preview-container');
    }
  });

  //manage the selected image in the gift this product
  $(document).on( 'click', '.ywgc-preset-image img', function (e) {
    e.preventDefault();

    var id = $(this).parent().data('design-id');

    $( '.ywgc-preset-image img' ).removeClass( 'selected_design_image' );
    $( '.ywgc-preset-image' ).removeClass( 'selected_image_parent' );

    $( this ).addClass( 'selected_design_image' );
    $( this ).parent().addClass('selected_image_parent');

    $(document).trigger('ywgc-picture-changed', ['template', id]);

  });


  /**
   * Manage the modal
   * */
  $(document).on('yith_ywgc_popup_template_loaded', function ( popup, item ) {

    /**
     * Manage the category selected on the modal
     * */
    $(item).on('click', 'a.ywgc-show-category', function (e) {

      var current_category = $(this).data("category-id");

      //  highlight the selected category
      $('a.ywgc-show-category').removeClass('ywgc-category-selected');
      $(this).addClass('ywgc-category-selected');

      //  Show only the design of the selected category
      if ('all' !== current_category) {
        $('.ywgc-design-item').hide();
        $('.ywgc-design-item.' + current_category).fadeIn("fast");
      }
      else {
        $('.ywgc-design-item').fadeIn("fast");

      }
      return false;
    });


    /**
     * manage the selected image in the modal
     * */
    $(item).on( 'click', '.ywgc-preset-image img', function (e) {
      e.preventDefault();

      $( '.ywgc-preset-image img' ).removeClass( 'selected_design_image' );
      $( this ).addClass( 'selected_design_image' );

      if ( $( this ).hasClass( 'selected_design_image' ) ){

        var image_url =  $( this ).attr('src');
        var srcset = $( this ).attr('srcset');

        var design_id = $(this).parent().data('design-id');
        var design_url = $(this).parent().data('design-url');


        var html_content = '<a href="' + image_url + '"><img src="' + image_url + '" class="wp-post-image size-thumbnail" alt="" data-caption="" data-src="' + image_url + '" data-large_image="' + image_url + '" data-large_image_width="1024" data-large_image_height="1024" sizes="(max-width: 600px) 100vw, 600px" width="600" height="600"></a>';

        var html_miniature = '<img src="' + image_url + '" class="attachment-thumbnail size-thumbnail selected_design_image selected_design_image_in_modal" ' +
          'alt="" sizes="(max-width: 150px) 85vw, 150px" width="150" height="150">';

        if (  $('.ywgc-design-list li:first-child .ywgc-preset-image ').hasClass('ywgc-default-product-image') ){
          wc_gallery_image.html(html_content);
          $('.ywgc-design-list li:nth-child(2) .ywgc-preset-image').html(html_miniature);
          $('.ywgc-design-list li:nth-child(2) .ywgc-preset-image').data('design-id', design_id);
          $('.ywgc-design-list li:nth-child(2) .ywgc-preset-image').data('design-url', design_url);

        }
        else{
          $('.ywgc-design-list li:first-child .ywgc-preset-image').html(html_miniature);
          $('.ywgc-design-list li:first-child .ywgc-preset-image').data('design-id', design_id);
          $('.ywgc-design-list li:first-child .ywgc-preset-image').data('design-url', design_url);
        }
      }

      $('.yith-ywgc-popup-wrapper .yith-ywgc-popup-close').click();


    });


    /**
     * manage the custom upload in the modal
     * */
    $(item).on('click', '.ywgc-upload-section-modal', function (e) {
      e.preventDefault();
      $('.ywgc-design-list-modal').hide();
      $('.ywgc-custom-upload-container-modal').show();
    });

    $(item).on('click', '.ywgc-show-category', function (e) {
      e.preventDefault();
      $('.ywgc-custom-upload-container-modal').hide();
      $('.ywgc-design-list-modal').show();
    });


    $(item).on('click', '.ywgc-custom-image-modal-submit-link', function (e) {
      e.preventDefault();

      $( 'body .ywgc-preset-image img' ).removeClass( 'selected_design_image' );



      var image_base64 = $(this).parent().find( 'img.qq-thumbnail-selector').attr('src');

      var html_content = '<img src="' + image_base64 + '" class="wp-post-image size-full" alt="" width="600" height="600">';

      var html_miniature = '<img src="' + image_base64 + '" class="attachment-thumbnail size-thumbnail custom-modal-selected-image selected_design_image selected_design_image_in_modal" ' +
        'alt="" ' +
        'srcset="' + image_base64 + ' 150w, ' +
        '' + image_base64 + ' 250w, ' +
        '' + image_base64 + ' 100w" ' +
        'sizes="(max-width: 150px) 85vw, 150px" width="150" height="150">';

      //Here we add the upload image in the design list and select it
      if (  $('.ywgc-design-list li:first-child .ywgc-preset-image ').hasClass('ywgc-default-product-image') ){
        $('.ywgc-design-list li:nth-child(2) .ywgc-preset-image').html(html_miniature);
        wc_gallery_image.html(html_content);
      }
      else{
        $('.ywgc-design-list li:first-child .ywgc-preset-image').html(html_miniature);
      }

      $('.ywgc-design-list .ywgc-preset-image img.custom-modal-selected-image').parent().attr('data-design-url', image_base64);
      $('.ywgc-design-list .ywgc-preset-image img.custom-modal-selected-image').parent().attr('data-design-id', 'custom-modal');

      $( 'form.cart' ).append('<input type="hidden" class="ywgc-custom-modal-design" name="ywgc-custom-modal-design" value="'+ image_base64 +'">');

      $('.yith-ywgc-popup-wrapper .yith-ywgc-popup-close').click();

    });


  });


  $(document).on('yith_ywgc_popup_closed', function ( popup, item ) {

    $('.ywgc-design-list .ywgc-preset-image img.selected_design_image_in_modal').click();

  })


  /**
   * Show the custom file choosed by the user as the image used on the gift card editor on product page
   * */
  $(document).on('click', '.ywgc-custom-picture', function (e) {
    e.preventDefault();
    $('#ywgc-upload-picture').click();
  });

  $('#ywgc-upload-picture').on('change', function () {

    $( '.ywgc-preset-image img' ).removeClass( 'selected_design_image' );

    var preview_image = function (file) {
      var oFReader = new FileReader();
      oFReader.readAsDataURL(file);

      oFReader.onload = function (oFREvent) {

        var image_base64 = oFREvent.target.result;

        var html_miniature = '<img src="' + image_base64 + '" class="attachment-thumbnail size-thumbnail  custom-selected-image selected_design_image" ' +
          'alt="" ' +
          'srcset="' + image_base64 + ' 150w, ' +
          '' + image_base64 + ' 250w, ' +
          '' + image_base64 + ' 100w" ' +
          'sizes="(max-width: 150px) 85vw, 150px" width="150" height="150">';

        var html_content = '<img src="' + image_base64 + '" class="wp-post-image size-full" alt="" width="600" height="600">';

        //Here we add the upload image in the design list and select it
        if (  $('.ywgc-design-list li:first-child .ywgc-preset-image ').hasClass('ywgc-default-product-image') ){
          $('.ywgc-design-list li:nth-child(2) .ywgc-preset-image').html(html_miniature);
          wc_gallery_image.html(html_content);
        }
        else{
          $('.ywgc-design-list li:first-child .ywgc-preset-image').html(html_miniature);
        }

        $('.ywgc-design-list .ywgc-preset-image img.custom-selected-image').parent().attr('data-design-url',image_base64);
        $('.ywgc-design-list .ywgc-preset-image img.custom-selected-image').parent().attr('data-design-id', 'custom');

        $('.custom-selected-image ').click();

        $(document).trigger('ywgc-picture-changed', ['custom', 'custom']);
      }
    };



    //  Manage the image errors and remove previous errors shown
    $(".ywgc-picture-error").remove();

    var ext = $(this).val().split('.').pop().toLowerCase();

    if ( $.inArray(ext, ['gif', 'png', 'jpg', 'jpeg', 'bmp'] ) == -1) {
      $( "div.gift-card-content-editor.step-appearance" ).append( '<span class="ywgc-picture-error">' +
        ywgc_data.invalid_image_extension + '</span>' );
      return;
    }

    if ( $(this)[0].files[0].size > ywgc_data.custom_image_max_size * 1024 * 1024 && ywgc_data.custom_image_max_size > 0 ) {
      $( "div.gift-card-content-editor.step-appearance").append('<span class="ywgc-picture-error">' +
        ywgc_data.invalid_image_size + '</span>' );
      return;
    }

    preview_image( $(this)[0].files[0] );
  });



  /**
   * Display the gift card form cart/checkout
   * */
  $( document ).on( 'click', 'a.ywgc-show-giftcard', show_gift_card_form );

  function show_gift_card_form() {
    $( '.ywgc_enter_code' ).slideToggle( 300, function () {
      if ( ! $( '.yith_wc_gift_card_blank_brightness' ).length ){

        $( '.ywgc_enter_code' ).find( ':input:eq( 0 )' ).focus();

        $(".ywgc_enter_code").keyup( function( event ) {
          if ( event.keyCode === 13 ) {
            $( "button.ywgc_apply_gift_card_button" ).click();
          }
        });
      }

    });
    return false;
  }

  /** Show the edit gift card button */
  $("button.ywgc-do-edit").css("display", "inline");


  function update_gift_card_amount(amount) {
    //copy the button value to the preview price
    $('.ywgc-form-preview-amount').text( amount );
  }

  function show_gift_card_editor(val) {
    $('button.gift_card_add_to_cart_button').attr('disabled', !val);
  }

  /** This code manage the amount buttons actions */
  function show_hide_add_to_cart_button() {

    var gift_this_product = $('#give-as-present');
    var amount_buttons = $('button.ywgc-amount-buttons');
    var amount_buttons_hidden_inputs = $('input.ywgc-amount-buttons');
    var manual_amount_element = $('.gift-cards-list input.ywgc-manual-amount');
    var first_amount_button = $('button.ywgc-amount-buttons:first');

    if ( !manual_amount_element.hasClass('selected_button') ){
      //Auto-select the 1st amount button
      first_amount_button.addClass('selected_button');
      if ( first_amount_button.hasClass('selected_button') )
        $('input.ywgc-amount-buttons:first').attr('name', 'gift_amounts');

      //copy the 1st button value to the preview price
      $('.ywgc-form-preview-amount').text( first_amount_button.data('wc-price') );
    }

    // select a button
    amount_buttons.on('click', function (e) {
      e.preventDefault();

      amount_buttons.removeClass('selected_button');
      amount_buttons_hidden_inputs.removeClass('selected_button');
      amount_buttons_hidden_inputs.removeAttr('name');
      manual_amount_element.removeClass('selected_button');
      $(this).addClass('selected_button');
      $(this).next().addClass('selected_button');

    });


    var manual_amount_placeholder = manual_amount_element.attr('placeholder');
    //Add the currency symbol in the custom amount
    manual_amount_element.on('click change keyup', function (e) {
      $(this).css( 'padding-left', '25px' );
      $(this).css( 'text-align', 'left' );
      $(this).removeAttr('placeholder');

      $('.ywgc-manual-currency-symbol').removeClass( 'ywgc-hidden' );
      $('.ywgc-manual-currency-symbol').addClass( 'ywgc-currency-symbol' );
    });

    manual_amount_element.on('focusout', function (e) {

      if ( manual_amount_element.val().length === 0 ){
        $(this).css( 'padding-left', 'unset' );
        $(this).css( 'text-align', 'center' );
        $(this).attr('placeholder' , manual_amount_placeholder );
        $('.ywgc-manual-currency-symbol').removeClass( 'ywgc-currency-symbol' );
        $('.ywgc-manual-currency-symbol').addClass( 'ywgc-hidden' );
        first_amount_button.click();
      }

    });


    /**
     * Manage the manual amount selection
     * */
    manual_amount_element.on('click change keyup', function (e) {
      e.preventDefault();

      amount_buttons.removeClass('selected_button');
      amount_buttons_hidden_inputs.removeClass('selected_button');
      amount_buttons_hidden_inputs.removeAttr('name');

      if ( ywgc_data.currency_format_symbol == null ){
        var currency_symbol = $('.ywgc-manual-currency-symbol').text();
      }
      else{
        var currency_symbol = ywgc_data.currency_format_symbol;
      }
      //copy the button value to the preview price
      $('.ywgc-form-preview-amount').text( $(this).data('wc-price') );

      $(this).addClass('selected_button');


      /* the user should enter a manual value as gift card amount */
      if ( manual_amount_element.length ) {

        $('.ywgc-manual-amount-error').remove();
        $(this).removeClass( 'ywgc-red-border');


        var manual_amount = manual_amount_element.val();
        var test_amount = new RegExp('^[1-9]\\d*(?:' + '\\' + ywgc_data.currency_format_decimal_sep + '\\d{1,2})?$', 'g');

        if (manual_amount.length && !test_amount.test(manual_amount)) {
          $('.ywgc-manual-currency-symbol').after('<div class="ywgc-manual-amount-error">' + ywgc_data.manual_amount_wrong_format + '</div>');
          $(this).addClass( 'ywgc-red-border');

          amount = accounting.formatMoney("", {
            symbol: currency_symbol,
            decimal: ywgc_data.currency_format_decimal_sep,
            thousand: ywgc_data.currency_format_thousand_sep,
            precision: ywgc_data.currency_format_num_decimals,
            format: ywgc_data.currency_format
          });
          update_gift_card_amount(amount);
          show_gift_card_editor(false);
        }
        else {
          if (parseInt(manual_amount) < parseInt(ywgc_data.manual_minimal_amount) && (ywgc_data.manual_minimal_amount_error.length > 0)) {
            $('.ywgc-manual-currency-symbol').after('<div class="ywgc-manual-amount-error">' + ywgc_data.manual_minimal_amount_error + '</div>');
            $(this).addClass( 'ywgc-red-border');
            amount = accounting.formatMoney(manual_amount, {
              symbol: currency_symbol,
              decimal: ywgc_data.currency_format_decimal_sep,
              thousand: ywgc_data.currency_format_thousand_sep,
              precision: ywgc_data.currency_format_num_decimals,
              format: ywgc_data.currency_format
            });
            update_gift_card_amount(amount);
            show_gift_card_editor(false);
          }
          else
            /** If the user entered a valid amount, show "add to cart" button and gift card
             *  editor.
             */
          if (manual_amount) {
            // manual amount is a valid numeric value
            show_gift_card_editor(true);

            amount = accounting.unformat(manual_amount, ywgc_data.mon_decimal_point);

            if (amount <= 0) {
              show_gift_card_editor(false);
            }
            else {
              amount = accounting.formatMoney(amount, {
                symbol: currency_symbol,
                decimal: ywgc_data.currency_format_decimal_sep,
                thousand: ywgc_data.currency_format_thousand_sep,
                precision: ywgc_data.currency_format_num_decimals,
                format: ywgc_data.currency_format
              });

              update_gift_card_amount(amount); //esto es para mostrarlo en la template del frontend

              show_gift_card_editor(true);
            }
          }
          else {
            amount = accounting.formatMoney("", {
              symbol: currency_symbol,
              decimal: ywgc_data.currency_format_decimal_sep,
              thousand: ywgc_data.currency_format_thousand_sep,
              precision: ywgc_data.currency_format_num_decimals,
              format: ywgc_data.currency_format
            });

            update_gift_card_amount(amount);
            show_gift_card_editor(false);
          }
        }
      }

    });

    var amount = first_amount_button.data('wc-price');

    //Manage the amount button selection
    amount_buttons.on( 'click', function (e) {
      e.preventDefault();

      amount_buttons_hidden_inputs.removeAttr('name');

      if (!gift_this_product.length) {

        $('.ywgc-manual-amount-error').remove();

        if (!amount_buttons.data('price')) {
          show_gift_card_editor(false);
        }
        else {
          show_gift_card_editor(true);
          amount = $('input.selected_button').data('wc-price');
          $('input.selected_button').attr('name', 'gift_amounts');
        }
        update_gift_card_amount(amount);
      }

    });

  }


  $( document ).on( 'input', '.gift-cards-list input.ywgc-manual-amount', function (e) {

    show_hide_add_to_cart_button();

  });

  $(document).on('input', '#ywgc-edit-message', function (e) {
    $(".ywgc-card-message").html($('#ywgc-edit-message').val());
  });

  $(document).on('change', '.gift-cards-list select', function (e) {
    show_hide_add_to_cart_button();
  });

  $(document).on('click', 'a.customize-gift-card', function (e) {
    e.preventDefault();
    $('div.summary.entry-summary').after('<div class="ywgc-customizer"></div>');
  });

  // Integration with yith woocommerce product bundles premium
  if ( $( '.yith-wcpb-product-bundled-items' ).length ){

    setTimeout( function(){
      $( '#give-as-present' ).prop( 'disabled', false );
    }, 1000 );

  }

  /**
   * Manage the Gift this product feature
   *
   */
  var add_to_cart_button = $( 'form.cart button.single_add_to_cart_button');
  var add_as_gift_button = add_to_cart_button.clone();

  add_as_gift_button.removeAttr( 'type' );

  $(document).on('click', '#give-as-present', function (e) {
    e.preventDefault();

    $("#give-as-present").attr('id', 'ywgc-cancel-gift-card');

    $("#ywgc-cancel-gift-card span.dashicons").removeClass('dashicons-arrow-down-alt2');
    $("#ywgc-cancel-gift-card span.dashicons").addClass('dashicons-arrow-up-alt2');

    $("#ywgc-cancel-gift-card").css("display", "inline-block");

    $('.ywgc-design-list li:first-child .ywgc-preset-image img').click();

    //Add a copy of the add to cart button in the form
    add_as_gift_button.addClass( 'ywgc-add-gift-product-to-cart' );
    add_as_gift_button.text(ywgc_data.add_gift_text);

    form_container.append( add_as_gift_button );

    form_container.slideToggle('slow' );

    $( this ).trigger( 'yith_ywgc_after_click_give_as_present' );

  });

  $(document).on('click', '#ywgc-cancel-gift-card', function (e) {
    e.preventDefault();

    $("#ywgc-cancel-gift-card").attr('id', 'give-as-present');

    $("#give-as-present span.dashicons").removeClass('dashicons-arrow-up-alt2');
    $("#give-as-present span.dashicons").addClass('dashicons-arrow-down-alt2');

    form_container.slideToggle('slow' );
  });


  $(document).on('click', '.ywgc-add-gift-product-to-cart', function (e) {
    e.preventDefault();

    if ( ywgc_data.mandatory_email == '1' ){
      $( '.yith_wc_gift_card_input_recipient_details').prop('required',true);
    }

    form_container.append('<input type="hidden" class="ywgc-as-present" name="ywgc-as-present" value="1">');

    add_to_cart_button.click();

  });


  function set_giftcard_value(value) {
    $("div.ywgc-card-amount span.amount").html(value);
  }

  $('.variations_form.cart').on('found_variation', function (ev, variation) {
    if (typeof variation !== "undefined") {
      $('#give-as-present').prop('disabled', false);
      var price_html = variation.price_html != '' ? $(variation.price_html).html() : $(".product-type-variable").find(".woocommerce-Price-amount.amount").first().html();
      set_giftcard_value(price_html);

    }
  });

  $(document).on('reset_data', function () {
    $('#give-as-present').prop('disabled', true);
    set_giftcard_value('');
  });



  function show_edit_gift_cards(element, visible) {
    var container = $(element).closest("div.ywgc-gift-card-content");
    var edit_container = container.find("div.ywgc-gift-card-edit-details");
    var details_container = container.find("div.ywgc-gift-card-details");

    if (visible) {
      //go to edit
      edit_container.removeClass("ywgc-hide");
      edit_container.addClass("ywgc-show");

      details_container.removeClass("ywgc-show");
      details_container.addClass("ywgc-hide");
    }
    else {
      //go to details
      edit_container.removeClass("ywgc-show");
      edit_container.addClass("ywgc-hide");

      details_container.removeClass("ywgc-hide");
      details_container.addClass("ywgc-show");
    }
  }

  $(document).on('click', 'button.ywgc-apply-edit', function (e) {

    var clicked_element = $(this);

    var container = clicked_element.closest("div.ywgc-gift-card-content");

    var sender = container.find('input[name="ywgc-edit-sender"]').val();
    var recipient = container.find('input[name="ywgc-edit-recipient"]').val();
    var message = container.find('textarea[name="ywgc-edit-message"]').val();
    var item_id = container.find('input[name="ywgc-item-id"]').val();

    var gift_card_element = container.find('input[name="ywgc-gift-card-id"]');
    var gift_card_id = gift_card_element.val();

    //  Apply changes, if apply button was clicked
    if (clicked_element.hasClass("apply")) {
      var data = {
        'action': 'edit_gift_card',
        'gift_card_id': gift_card_id,
        'item_id': item_id,
        'sender': sender,
        'recipient': recipient,
        'message': message
      };

      container.block({
        message: null,
        overlayCSS: {
          background: "#fff url(" + ywgc_data.loader + ") no-repeat center",
          opacity: .6
        }
      });

      $.post(ywgc_data.ajax_url, data, function (response) {
        if (response.code > 0) {
          container.find("span.ywgc-sender").text(sender);
          container.find("span.ywgc-recipient").text(recipient);
          container.find("span.ywgc-message").text(message);

          if (response.code == 2) {
            gift_card_element.val(response.values.new_id);
          }
        }

        container.unblock();

        //go to details
        show_edit_gift_cards(clicked_element, false);
      });
    }
  });

  $(document).on('click', 'button.ywgc-cancel-edit', function (e) {

    var clicked_element = $(this);

    //go to details
    show_edit_gift_cards(clicked_element, false);
  });

  $(document).on('click', 'button.ywgc-do-edit', function (e) {

    var clicked_element = $(this);
    //go to edit
    show_edit_gift_cards(clicked_element, true);
  });

  $(document).on('click', '.ywgc-gift-card-content a.edit-details', function (e) {
    e.preventDefault();
    $(this).addClass('ywgc-hide');
    $('div.ywgc-gift-card-details').toggleClass('ywgc-hide');
  });


  $('.ywgc-single-recipient input[name="ywgc-recipient-email[]"]').each(function (i, obj) {
    $(this).on('input', function () {
      $(this).closest('.ywgc-single-recipient').find('.ywgc-bad-email-format').remove();
    });
  });

  function validateEmail(email) {
    var test_email = new RegExp('^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,}$', 'i');
    return test_email.test(email);
  }

  $(document).on('submit', '.gift-cards_form', function (e) {
    var can_submit = true;
    $('.ywgc-single-recipient input[name="ywgc-recipient-email[]"]').each(function (i, obj) {

      if ($(this).val() && !validateEmail($(this).val())) {
        $(this).closest('.ywgc-single-recipient').find('.ywgc-bad-email-format').remove();
        $(this).after('<span class="ywgc-bad-email-format">' + ywgc_data.email_bad_format + '</span>');
        can_submit = false;
      }
    });
    if (!can_submit) {
      e.preventDefault();
    }
  });
  /** Manage the WooCommerce 2.6 changes in the cart template
   * with AJAX
   * @since 1.4.0
   */

  $(document).on(
    'click',
    'a.ywgc-remove-gift-card ',
    remove_gift_card_code);

  function remove_gift_card_code(evt) {
    evt.preventDefault();
    var $table = $(evt.currentTarget).parents('table');
    var gift_card_code = $(evt.currentTarget).data('gift-card-code');

    block($table);

    var data = {
      security: ywgc_data.gift_card_nonce,
      code: gift_card_code,
      action: 'ywgc_remove_gift_card_code'
    };

    $.ajax({
      type: 'POST',
      url: ywgc_data.ajax_url,
      data: data,
      dataType: 'html',
      success: function (response) {
        show_notice(response);
        $(document.body).trigger('removed_gift_card');
        unblock($table);
      },
      complete: function () {
        update_cart_totals();
      }
    });
  }

  /**
   * Apply the gift card code the same way WooCommerce do for Coupon code
   *
   * @param {JQuery Object} $form The cart form.
   */
  $( document ).on( 'click', 'button.ywgc_apply_gift_card_button', function ( e ) {
    e.preventDefault();
    var parent = $( this ).closest( 'div.ywgc_enter_code' );
    block( parent );

    var $text_field = parent.find( 'input[ name="gift_card_code" ]' );
    var gift_card_code = $text_field.val();

    var data = {
      security: ywgc_data.gift_card_nonce,
      code: gift_card_code,
      action: 'ywgc_apply_gift_card_code'
    };

    $.ajax({
      type: 'POST',
      url: ywgc_data.ajax_url,
      data: data,
      dataType: 'html',
      success: function ( response ) {
        show_notice( response );
        $( document.body ).trigger( 'applied_gift_card' );
      },
      complete: function () {

        unblock( parent );
        $text_field.val( '' );

        update_cart_totals();
      }
    });
  });

  /**
   * Block a node visually for processing.
   *
   * @param {JQuery Object} $node
   */
  var block = function ($node) {
    $node.addClass('processing').block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });
  };

  /**
   * Unblock a node after processing is complete.
   *
   * @param {JQuery Object} $node
   */
  var unblock = function ($node) {
    $node.removeClass('processing').unblock();
  };

  /**
   * Gets a url for a given AJAX endpoint.
   *
   * @param {String} endpoint The AJAX Endpoint
   * @return {String} The URL to use for the request
   */
  var get_url = function (endpoint) {
    return ywgc_data.wc_ajax_url.toString().replace(
      '%%endpoint%%',
      endpoint
    );
  };

  /**
   * Clear previous notices and shows new one above form.
   *
   * @param {Object} The Notice HTML Element in string or object form.
   */
  var show_notice = function ( html_element ) {
    $( '.woocommerce-error, .woocommerce-message' ).remove();
    $( ywgc_data.notice_target ).after( html_element );
    if ( $( '.ywgc_have_code' ).length )
      $( '.ywgc_enter_code' ).slideUp( '300' );
  };

  /**
   * Update the cart after something has changed.
   */
  function update_cart_totals() {
    block($('div.cart_totals'));

    $.ajax({
      url: get_url('get_cart_totals'),
      dataType: 'html',
      success: function (response) {
        $('div.cart_totals').replaceWith(response);
      }
    });

    $(document.body).trigger('update_checkout');
  }

  /**
   * Integration with YITH Quick View and some third party themes
   */
  $(document).on('qv_loader_stop yit_quick_view_loaded flatsome_quickview', function () {

    show_hide_add_to_cart_button();

    hide_on_gift_as_present();

  });

  var hide_on_gift_as_present = function () {
    if ($('input[name="ywgc-as-present-enabled"]').length) {
      $('.ywgc-generator').hide();
      show_gift_card_editor(false);
    }
  }

  /**
   * Add new gift card button
   */
  $(document).on( 'click', 'button.yith-add-new-gc-my-account-button', function (e) {
    e.preventDefault();
    $( this ).parent().prev('.form-link-gift-card-to-user').toggle( 'slow' );
  });


  /**
   * manage recipient and sender fields to display them automatically in the preview
   */
  var recipient_name_input = $( '.ywgc-recipient-name input' );
  recipient_name_input.on('change keyup', function (e) {
    e.preventDefault();
    var recipient_name = recipient_name_input.val();
    $('.ywgc-form-preview-to-content').text( recipient_name );

  });

  var sender_name_input = $( '.ywgc-sender-name input' );
  sender_name_input.on('change keyup', function (e) {
    e.preventDefault();
    var sender_name = sender_name_input.val();
    $('.ywgc-form-preview-from-content').text( sender_name );
  });

  var message_input = $( '.ywgc-message textarea' );
  message_input.on('change keyup', function (e) {
    e.preventDefault();
    var message = message_input.val();
    $('.ywgc-form-preview-message').html( message.replace(/\n/g, '<br/>') );
  });


  /**
   * Manage the add/remove recipients
   */
  function add_recipient(cnt) {

    var recipients_number = cnt + 2;

    var quantity_input = $("div.gift_card_template_button input[name='quantity']");

    var note = ywgc_data.multiple_recipient.replace( '%number_gift_cards%', recipients_number );

    var last = $('div.ywgc-single-recipient').last();
    var required = ywgc_data.mandatory_email ? 'required' : '';
    var new_div = '<div class="ywgc-additional-recipient">' +
      '<label for="ywgc-recipient-name' + cnt + '">' + ywgc_data.label_name + '</label>' +
      '<input type="text" id="ywgc-recipient-name' + cnt + '" name="ywgc-recipient-name[]" class="yith_wc_gift_card_input_recipient_details" placeholder="' + ywgc_data.name + '" ' + required + '/>' +
      '<br><label for="ywgc-recipient-email' + cnt + '">' + ywgc_data.label_email + '</label>' +
      '<input type="email" id="ywgc-recipient-email' + cnt + '" name="ywgc-recipient-email[]" class="ywgc-recipient yith_wc_gift_card_input_recipient_details" placeholder="' + ywgc_data.email + '" ' + required + '/>' +
      '<a href="#" class="ywgc-remove-recipient"> ' +
      '</div>';

    last.after(new_div);

    //  show the remove recipient links
    $("a.ywgc-remove-recipient").not(":first").css('visibility', 'visible');

    quantity_input.addClass('ywgc-remove-number-input');
    quantity_input.attr("onkeydown", "return false");
    quantity_input.css("background-color", "lightgray");
    quantity_input.val(recipients_number);

    //  show a message for quantity disabled when multi recipients is entered
    $(".ywgc-multi-recipients span").remove();
    $("div.gift_card_template_button div.quantity").before("<div class='ywgc-multi-recipients'><span>" + note + "</span></div>");

  }

  function remove_recipient(element, cnt) {

    var quantity_input = $("div.gift_card_template_button input[name='quantity']");

    //update the quantity input
    quantity_input.val(cnt);

    var note = ywgc_data.multiple_recipient.replace( '%number_gift_cards%', cnt );

    //update the note message
    $(".ywgc-multi-recipients span").remove();
    $("div.gift_card_template_button div.quantity").before("<div class='ywgc-multi-recipients'><span>" + note + "</span></div>");

    //  remove the element
    $(element).parent("div.ywgc-additional-recipient").remove();

    //  Avoid the deletion of all recipient
    var emails = $('input[name="ywgc-recipient-email[]"]');
    if (emails.length == 1) {
      //  only one recipient is entered...
      $("a.hide-if-alone").css('visibility', 'hidden');
      $("div.ywgc-multi-recipients").remove();
      quantity_input.removeClass('ywgc-remove-number-input');
      quantity_input.removeAttr("onkeydown");
      quantity_input.css("background-color", "");




    }
  }

  $(document).on('click', 'a.add-recipient', function (e) {
    e.preventDefault();

    var cnt = $('.ywgc-additional-recipient').length;

    var proteo_qty_arrows = $('.product-qty-arrows');

    if ( proteo_qty_arrows.length){
      proteo_qty_arrows.hide();
    }

    add_recipient( cnt );
  });

  $(document).on('click', 'a.ywgc-remove-recipient', function (e) {
    e.preventDefault();

    var cnt = $('.ywgc-additional-recipient').length;

    var proteo_qty_arrows = $('.product-qty-arrows');

    if ( proteo_qty_arrows.length && cnt === '0' ){
      proteo_qty_arrows.show();
    }

    remove_recipient($(this), cnt);
  });







})(jQuery);
