jQuery(document).ready(function ($) {

    $( '#yith_ywgc_transform_smart_coupons' ).on( 'click', function () {
        yith_ywgc_transform_smart_coupons();
    });

    function yith_ywgc_transform_smart_coupons( limit,offset ) {
        var ajax_zone = $('#ywgc_ajax_zone_transform_smart_coupons');

        if (typeof(offset) === 'undefined') offset = 0;
        if (typeof(limit) === 'undefined') limit = 0;

        var post_data = {
            'limit': limit,
            'offset': offset,
            action: 'yith_convert_smart_coupons_button'
        };
        if (offset == 0)
            ajax_zone.block({message: null, overlayCSS: {background: "#f1f1f1", opacity: .7}});
        $.ajax({
            type: "POST",
            data: post_data,
            url: ywgc_data.ajax_url,
            success: function (response) {
                console.log('Processing, do not cancel');
                if (response.loop == 1)
                    yith_ywgc_transform_smart_coupons(response.limit, response.offset);
                if (response.loop == 0)
                    ajax_zone.unblock();
            },
            error: function (response) {
                console.log("ERROR");
                console.log(response);
                ajax_zone.unblock();
                return false;
            }
        });
    }


    //Settings dependencies
    /**
     * Select on gift this product settings
     * */
    $( document ).ready(function() {
        if ($('input#ywgc_permit_its_a_present').prop('checked')) {

            $('select#ywgc_gift_this_product_redirected_page').parent().parent().removeClass('ywgc-disabled-option');
            $(this).closest('.form-table').next('h2').removeClass('ywgc-disabled-option');
        }});

    $('input#ywgc_permit_its_a_present').change(function() {

        if ( ! $( this ).hasClass( 'onoffchecked') && ! $('input#ywgc_permit_its_a_present').prop('checked') ){

            $( 'select#ywgc_gift_this_product_redirected_page' ).parent().parent().addClass( 'ywgc-disabled-option' );
            $(this).closest('.form-table').next('h2').addClass('ywgc-disabled-option');
        }
        else{
            $( 'select#ywgc_gift_this_product_redirected_page' ).parent().parent().removeClass( 'ywgc-disabled-option' );
            $(this).closest('.form-table').next('h2').removeClass('ywgc-disabled-option');
        }
    });

    /**
     * Select on Recipient and delivery settings
     * */

    $( document ).ready(function() {
    if ( $( 'input#ywgc_auto_discount_button_activation' ).prop('checked') ){

        $( 'select#ywgc_redirected_page' ).parent().parent().removeClass( 'ywgc-disabled-option' );
    }});

    $('input#ywgc_auto_discount_button_activation').change(function() {

        if ( ! $( this ).hasClass( 'onoffchecked') ){

            $( 'select#ywgc_redirected_page' ).parent().parent().addClass( 'ywgc-disabled-option' );
        }
        else{
            $( 'select#ywgc_redirected_page' ).parent().parent().removeClass( 'ywgc-disabled-option' );
        }
    });


    $( document ).ready(function() {
        if ( $( 'input#ywgc_gift_this_product_button_redirect-to_product_page' ).prop('checked') ){

            $( '#ywgc_gift_this_product_redirected_page' ).parent().parent().addClass( 'ywgc-disabled-option' );
        }});

    $(document).on("click", "#ywgc_gift_this_product_button_redirect-to_customize_page", function (e) {

        $( '#ywgc_gift_this_product_redirected_page' ).parent().parent().removeClass( 'ywgc-disabled-option' );

    });

    $(document).on("click", "#ywgc_gift_this_product_button_redirect-to_product_page", function (e) {

        $( '#ywgc_gift_this_product_redirected_page' ).parent().parent().addClass( 'ywgc-disabled-option' );

    });





  $(document).on('click', '.ywgc_update_cron_button', function (e) {
    e.preventDefault();

    var interval =  $( this ).parent().parent().parent().find( "[data-dep-target='ywgc_delivery_mode'] " );
    var radio_hourly = interval.find( '#ywgc_delivery_mode-hourly');

    var hour_option =  $( this ).parent().parent().parent().find( "[data-dep-target='ywgc_delivery_hour'] " );
    var hour = hour_option.find( '#ywgc_delivery_hour').val();


    if ( radio_hourly.prop('checked') ){
      var interval_mode = 'hourly';
    }
    else{
      var interval_mode = 'daily';
    }


    var block_zone = $( this ).parent().parent();

    block_zone.block({message: null, overlayCSS: {background: "#f1f1f1", opacity: .7}});

    var data = {
      security: ywgc_data.gift_card_nonce,
      interval_mode: interval_mode,
      hour: hour,
      action: 'ywgc_update_cron'
    };

    $.ajax({
      type: 'POST',
      url: ywgc_data.ajax_url,
      data: data,
      dataType: 'html',
      success: function (response) {
        block_zone.unblock();
        console.log('Cron Updated!');
      },
      error: function (response) {
        block_zone.unblock();
        console.log("ERROR");
        console.log(response);
      }
    });



  });











});
