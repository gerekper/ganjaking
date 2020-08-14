/**
 * Created by Your Inspiration on 22/02/2016.
 */
jQuery(document).ready(function($){

    /*PRICE RULE METABOXES*/
    var check_cat_rule = $('#_ywcrbp_cat_rule-container'),
        check_tag_rule = $('#_ywcrbp_tag_rule-container'),
        container_cat_rule = check_cat_rule.parent(),
        container_tag_rule = check_tag_rule.parent();

    $('#_ywcrbp_add_cat_rule').on('change', function(e){

        if( $(this).is(':checked'))
            container_cat_rule.show();
        else
            container_cat_rule.hide();
    }).change();

    $('#_ywcrbp_add_tag_rule').on('change', function(e){

        if( $( this ).is(':checked'))
            container_tag_rule.show();
        else
            container_tag_rule.hide();
    }).change();

    /*PRODUCT VARIATION METABOXES*/

    var variation_rule = $('.product_variable_rule');

    variation_rule.on('change keyup', function(e){
        var rule = $(this),
            content = rule.closest('.woocommerce_variation');

        content.addClass('variation-needs-update');

    });


    /*GENERAL SETTINGS FIELDS*/

    var check_hide_guest_price = $('#ywcrbp_hide_price_guest'),
        check_hide_user_price = $('#ywcrbp_hide_price_for_role_check'),
        check_hide_user_cart = $('#ywcrbp_hide_add_to_cart_for_role_check');


    check_hide_guest_price.on('change',function(e){

        var t = $(this),
            guest_message_content = $('#ywcrbp_message').closest('tr'),
            guest_color_content = $('#ywcrbp_message_color').closest('tr'),
            guest_post_content = $('#ywcrbp_position_guest_txt').closest('tr');

        if(t.is(':checked')) {
            guest_message_content.show();
            guest_color_content.show();
            guest_post_content.show();
        }
        else {
            guest_message_content.hide();
            guest_color_content.hide();
            guest_post_content.hide();
        }

    }).change();

    check_hide_user_price.on('change',function(e){

        var t = $( this),
            user_role_content = $('#ywcrbp_hide_price_for_role').closest('tr'),
            user_message_content = $('#ywcrbp_message_user').closest('tr'),
            user_color_content = $('#ywcrbp_message_color_user').closest('tr'),
            user_pos_content = $('#ywcrbp_position_user_txt').closest('tr');

        if(t.is(':checked')){

            user_message_content.show();
            user_role_content.show();
            user_color_content.show();
            user_pos_content.show();
        }
        else{
            user_message_content.hide();
            user_role_content.hide();
            user_color_content.hide();
            user_pos_content.hide();
        }

    }).change();

    check_hide_user_cart.on('change',function(e){

        var t= $(this),
            user_role_content = $('#ywcrbp_hide_add_to_cart_for_role').closest('tr');

        if(t.is(':checked'))
        user_role_content.show();
        else
        user_role_content.hide();
    }).change();

    $('.ywcrbp_clear_transient').on('click', function(e){

        e.preventDefault();

        $('.ywcrbp_deleted').remove();
        var data = {

            action : ywcrbp_panel.actions.delete_role_price_transient
        },
            button = $(this),
            block_params = {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                },
                ignoreIfBlocked: true
            };
        button.block( block_params );
        $.ajax({
            type: 'POST',
            url: ywcrbp_panel.ajax_url,
            data: data,
            dataType: 'json',
            success: function (response) {

               button.unblock();
               button.after('<span class="ywcrbp_deleted">'+response.message+'</span>');
            }

        });
    });
});