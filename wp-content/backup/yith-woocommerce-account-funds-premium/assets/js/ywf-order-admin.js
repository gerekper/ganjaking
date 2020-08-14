/**
 * Created by salvatore on 13/04/16.
 */
jQuery(document).ready(function($){

    woocommerce_admin['ywf_error_message_refund'] =  ywf_params.messages.error_message_refund;
    woocommerce_admin['ywf_error_message_wrong_funds'] =  ywf_params.messages.error_wrong_funds;

$('.button.refund-items').on('click',function(e){

    if( $('.ywf_available_user_fund').length ){

        var refund_items = $('.wc-order-refund-items'),
            refund_total = $('.ywf_available_user_fund').val(),
            tr_total_av_refund = refund_items.find('tr:nth-child(3)'),
            td_total_av_refund = tr_total_av_refund.find('td.total'),
            td_total_av_refund_label = tr_total_av_refund.find('td.label'),
            label = td_total_av_refund_label.html(),
            old_amount = td_total_av_refund.find('span.amount').text(),
            format_refund = accounting.formatMoney( refund_total, {
               symbol:    woocommerce_admin_meta_boxes.currency_format_symbol,
               decimal:   woocommerce_admin_meta_boxes.currency_format_decimal_sep,
               thousand:  woocommerce_admin_meta_boxes.currency_format_thousand_sep,
               precision: woocommerce_admin_meta_boxes.currency_format_num_decimals,
               format:    woocommerce_admin_meta_boxes.currency_format
           } );


        if( !td_total_av_refund.find('span.ywf_ref_amount').length ) {

            var old_amount_value = accounting.unformat( old_amount, woocommerce_admin.mon_decimal_point );
            if( old_amount_value > refund_total ) {
                td_total_av_refund.html('<span class="amount ywf_ref_amount"><del>' + old_amount + '</del> ' + format_refund);
            }
            td_total_av_refund_label.html('<span class="woocommerce-help-tip" data-tip="' + ywf_params.messages.tot_av_refund_tip + '"></span> ' +label);

            // Tooltips
            var tiptip_args = {
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200
            };
            $('.woocommerce-help-tip').tipTip(tiptip_args);
        }


        if(  $('#order_line_items tr.item').length === 1 ){
            var item_row = $('#order_line_items tr.item'),
                amount = $('.ywf_available_user_fund').val();

            item_row.find( 'input.line_total' ).val( amount );
            item_row.find( 'input.line_total' ).attr('data-total', amount );


        }
    }
});

    $('.wc-order-refund-items').on('change keyup','#refund_amount', function (e){

        var max_user_funds_ref = $('.ywf_available_user_fund'),
            refund_amount = $(this).val();
        if( max_user_funds_ref.length ){

            var user_funds = max_user_funds_ref.val(),
                value_refund = accounting.unformat( refund_amount, woocommerce_admin.mon_decimal_point );


            if( value_refund > user_funds ){
               $('.refund-actions button').attr('disabled','disabled');
              
                $( document.body ).triggerHandler( 'wc_add_error_tip', [ $(this),  'ywf_error_message_refund' ] );
            } 
            else{
                $( document.body ).triggerHandler( 'wc_remove_error_tip', [ $(this), 'ywf_error_message_refund'  ] );
                $('.refund-actions button').attr('disabled',false );
               
            }

        }
    }
    );

    var remove_refunds_for_parital_payments = function () {

        var is_partial_payment = $(document).find('#ywf_partial_payment');

        if( is_partial_payment.length && 'yes' == is_partial_payment.val() ){
            $(document).find('.refund-actions button.do-api-refund').remove();

            var message = $('<p>');

            message.css({'color':'red', 'padding-right':'10px','margin-top':'20px'});
            message.addClass('ywf_refund_warning');
            message.html( ywf_params.messages.no_automatic_refund );

            message.appendTo( $(document).find('.refund-actions') );
        }
    };

    remove_refunds_for_parital_payments();

    $(document).on('click','.ywf_add_funds.button',function (e) {
       e.preventDefault();

       var order_id = $(document).find('#post_ID').val(),
           customer_id = $(document).find('#yith_customer_id').val(),
           funds_to_add = $(document).find('#yith_add_funds').val(),
           data = {
                order_id:order_id,
               customer_id:customer_id,
               funds_to_add:funds_to_add,
               action:ywf_params.actions.add_funds
           },
           block_params = {
               message: null,
               overlayCSS: {
                   background: '#fff',
                   opacity: 0.6
               },
               ignoreIfBlocked: true
           };

       if( funds_to_add > 0 ){
           $.ajax({
               type: 'POST',
               url: ywf_params.ajax_url,
               data: data,
               dataType: 'json',
               beforeSend:function(){

                   $('#yith-wc-order-account-funds-metabox').block(block_params);
               },
               success:function (response) {

                   $(document).find('#yith_add_funds').val('');
                   $(document).find('#add_order_note').val( response );
                   $(document).find('.add_note.button').click();

               },
               complete:function () {
                   $('#yith-wc-order-account-funds-metabox').unblock();
               }
           });
       }else{
           $( document.body ).triggerHandler( 'wc_add_error_tip', [$(document).find('#yith_add_funds'),  'ywf_error_message_wrong_funds' ] );
       }
    });
});