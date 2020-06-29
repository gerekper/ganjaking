/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH Infinite Scrolling Premium
 * @version 1.0.0
 */

jQuery(document).ready( function($) {
    "use strict";

    var block_loader    = ( typeof yith_ywpar_admin !== 'undefined' ) ? yith_ywpar_admin.block_loader : false;


    $('#ywpar_apply_points_from_wc_points_rewards_btn').on('click', function(e) {
        e.preventDefault();
        var from = $(this).prev().val(),
            container   = $('#ywpar_apply_points_from_wc_points_rewards_btn').closest('.option');

        container.find('.response').remove();

        if (block_loader) {
            container.block({
                message   : null,
                overlayCSS: {
                    background: 'transparent',
                    opacity   : 0.5,
                    cursor    : 'none'
                }
            });
        }

        $.ajax({
            type    : 'POST',
            url     : yith_ywpar_admin.ajaxurl,
            dataType: 'json',
            data    : 'action=ywpar_apply_wc_points_rewards&from=' + from + '&security=' + yith_ywpar_admin.apply_wc_points_rewards,
            success : function (response) {
                container.unblock();
                container.append('<span class="response">'+response+'</span>');
            }
        });
    });


    $('#yit_ywpar_options_fix_expiration_points-container .button').on('click', function(e) {
        e.preventDefault();
        var container   = $('#yit_ywpar_options_fix_expiration_points-container');

        container.find('.response').remove();

        if (block_loader) {
            container.block({
                message   : null,
                overlayCSS: {
                    background: 'transparent',
                    opacity   : 0.5,
                    cursor    : 'none'
                }
            });
        }

        $.ajax({
            type    : 'POST',
            url     : yith_ywpar_admin.ajaxurl,
            dataType: 'json',
            data    : 'action=ywpar_fix_expiration_points&security=' + yith_ywpar_admin.fix_expiration_points,
            success : function (response) {
                container.unblock();
                container.append('<span class="response">'+response+'</span>');
				location.reload(false);
            }
        });
    });

    $( '#ywpar-notice-is-dismissable' ).find('.notice-dismiss').on( 'click', function(){
        $.cookie('ywpar_notice', 'dismiss', { path: '/' });
    } );


    /****
     * remove a row in custom type field
     ****/
    $(document).on('click', '#yith_woocommerce_points_and_rewards_points .ywpar-remove-row', function() {
        var $t = $(this),
            current_row = $t.closest('.role-conversion-options');
        current_row.remove();
    });
    $(document).on('click', '.extrapoint-options .ywpar-remove-row', function () {
        var $t = $(this),
            current_row = $t.closest('.extrapoint-options');
        current_row.remove();
    });

    /****
     * add a row in custom type field
     ****/
    $(document).on('click', '#yith_woocommerce_points_and_rewards_extra-points .ywpar-add-row', function() {
        var $t = $(this),
            wrapper = $t.closest('.yith-plugin-fw-field-wrapper'),
            current_option = $t.closest('.extrapoint-options'),
            current_index = parseInt( current_option.data('index')),
            clone = current_option.clone(),
            options = wrapper.find('.extrapoint-options'),
            max_index = 1;

        options.each(function(){
            var index = $(this).data('index');
            if( index > max_index ){
                max_index = index;
            }
        });

        var new_index = max_index + 1;
        clone.attr( 'data-index', new_index );

        var fields = clone.find("[name*='list']");
        fields.each(function(){
            var $t = $(this),
                name = $t.attr('name'),
                id =  $t.attr('id'),

                new_name = name.replace('[list]['+current_index+']', '[list]['+new_index+']'),
                new_id = id.replace('[list]['+current_index+']', '[list]['+new_index+']');

            $t.attr('name', new_name);
            $t.attr('id', new_id);
            $t.val('');

        });

        clone.find('.ywpar-remove-row').removeClass('hide-remove');
        clone.find('.chosen-container').remove();

        wrapper.append(clone);

    });
    $(document).on('click', '#yith_woocommerce_points_and_rewards_points .ywpar-add-row', function() {
        var $t = $(this),
            wrapper = $t.closest('.yith-plugin-fw-field-wrapper'),
            current_option = $t.closest('.role-conversion-options'),
            current_index = parseInt( current_option.data('index')),
            clone = current_option.clone(),
            options = wrapper.find('.role-conversion-options'),
            max_index = 1;

        options.each(function(){
            var index = $(this).data('index');
            if( index > max_index ){
                max_index = index;
            }
        });

        var new_index = max_index + 1;
        clone.attr( 'data-index', new_index );

        var fields = clone.find("[name*='role_conversion']");
        fields.each(function(){
            var $t = $(this),
                name = $t.attr('name'),
                id =  $t.attr('id'),

                new_name = name.replace('[role_conversion]['+current_index+']', '[role_conversion]['+new_index+']'),
                new_id = id.replace('[role_conversion]['+current_index+']', '[role_conversion]['+new_index+']');

            $t.attr('name', new_name);
            $t.attr('id', new_id);
            $t.val('');

        });

        clone.find('.ywpar-remove-row').removeClass('hide-remove');
        clone.find('.chosen-container').remove();

        wrapper.append(clone);

    });


    /**
     *  Apply Points from previous orders
     */
    $('#ywpar_apply_points_previous_order-container .panel-datepicker').each( function() {
        $(this).datepicker({
            dateFormat : 'yy-mm-dd'
        });
    });
    $('#ywpar_apply_points_previous_order_btn').on('click', function(e) {
        e.preventDefault();
        var from = $(this).prev().val(),
            container   = $('#ywpar_apply_points_previous_order-container .option');

        container.find('.response').remove();

        if (block_loader) {
            container.block({
                message   : null,
                overlayCSS: {
                    background: 'transparent',
                    opacity   : 0.5,
                    cursor    : 'none'
                }
            });
        }

        $.ajax({
            type    : 'POST',
            url     : yith_ywpar_admin.ajaxurl,
            dataType: 'json',
            data    : 'action=ywpar_apply_previous_order&from=' + from + '&security=' + yith_ywpar_admin.apply_previous_order_none,
            success : function (response) {
                container.unblock();
                container.append('<span class="response">'+response+'</span>');
            }
        });
    });


    /**
     * Reset points to all customer
     */
    $('.ywrac_reset_points').on('click', function(e) {
        e.preventDefault();

        var conf = confirm( yith_ywpar_admin.reset_points_confirm );

        if( ! conf ){
            return false;
        }

        var container   = $(this).closest('.yith-plugin-fw-field-wrapper');

        container.find('.response').remove();

        if (block_loader) {
            container.block({
                message   : null,
                overlayCSS: {
                    background: 'transparent',
                    opacity   : 0.5,
                    cursor    : 'none'
                }
            });
        }

        $.ajax({
            type    : 'POST',
            url     : yith_ywpar_admin.ajaxurl,
            dataType: 'json',
            data    : 'action=ywpar_reset_points&security=' + yith_ywpar_admin.reset_points,
            success : function (response) {
                container.unblock();
                container.append('<span class="response">'+response+'</span>');
            }
        });

    });

    /**
     * Import Export Tab Javascript
     */
    if( $('#ywpar_import_points').length ){
        $('#ywpar_import_points').closest('form').attr('enctype',"multipart/form-data");
    }
    $('#ywpar_import_points').on('click', function(e){
        e.preventDefault();
        var action = $('#type_action').val();
		$('.ywpar_safe_submit_field').val( action + '_points');
		$(this).closest('form').submit();
    });

    $('#type_action').on('change', function(){
        var $t = $(this),
            action = $t.val();
        if( action == 'import' ){
            $t.closest('.option').find("[data-val='import']").show();
        }else{
			$t.closest('.option').find("[data-val='import']").hide();
        }
    });
    $('#type_action').change();



    /**
     * Reset button on Customer points view
     * @since 1.6
     */
    $(document).on('click', '.ywpar_reset_points', function(e){
        e.preventDefault();
        var $t = $(this),
            username = $t.data('username'),
            message= yith_ywpar_admin.reset_point_message + ' ' + username  + '?';
        if( confirm( message )){
          window.location.href = $t.attr('href');
        }
    });

    /**
     * Some deps
     * @since 1.6
     */
    $(document).on('change', '#ywpar_conversion_rate_method, #ywpar_rewards_points_for_role', function () {
        var $t = $('#ywpar_conversion_rate_method'),
        $onoff = $('#ywpar_rewards_points_for_role');

        if( 'yes' == $onoff.val()){
            if ('fixed' == $t.val()) {
                $('#ywpar_rewards_points_role_rewards_fixed_conversion_rate-container').closest('tr').show();
                $('#ywpar_rewards_points_role_rewards_percentage_conversion_rate-container').closest('tr').hide();
            } else {
                $('#ywpar_rewards_points_role_rewards_percentage_conversion_rate-container').closest('tr').show();
                $('#ywpar_rewards_points_role_rewards_fixed_conversion_rate-container').closest('tr').hide();
            }
        }else{
            $('#ywpar_rewards_points_role_rewards_fixed_conversion_rate-container').closest('tr').hide();
            $('#ywpar_rewards_points_role_rewards_percentage_conversion_rate-container').closest('tr').hide();
        }
    });
    $('#ywpar_conversion_rate_method').change();
    $('#ywpar_rewards_points_for_role').change();


    //tab Bulk
    $(document).on('change', '#ywpar_type_user_search', function () {
        var $t = $(this),
            val = $t.val();
        $('.ywpar-deps').each(function () {

            var $sb = $(this),
                dep = $sb.data('deps');
            dep = dep ? dep.split(',') : [];
            if (dep.indexOf(val) !== -1) {
                $sb.show();
            } else {
                $sb.hide();
            }
        });
    });

    $('#ywpar_bulk_action_type').change();

    $(document).on('change', '#ywpar_bulk_action_type', function () {
        var $t = $(this),
            val = $t.val();
        $('.ywpar-deps_action').each(function () {

            var $sb = $(this),
                dep = $sb.data('deps');
            dep = dep ? dep.split(',') : [];
            if (dep.indexOf(val) !== -1) {
                $sb.show();
            } else {
                $sb.hide();
            }
        });
    });
    $('#ywpar_bulk_action_type').change();

    $('#ywpar_bulk_action_points').on('click', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');

        $('.ywpar-bulk-trigger').append('<div class="ywpar-bulk-progress"><div>0%</div></div>');

        process_step(1, form.serialize(), form);

    });

    var process_step = function( step, data, form ) {

        var block_container = $('.ywpar-bulk-trigger');


        $.ajax({
            type: 'POST',
            url: yith_ywpar_admin.ajaxurl,
            data: {
                form: data,
                action: 'ywpar_bulk_action',
                step: step
            },
            dataType: 'json',
            success: function( response ) {
                if( 'done' == response.step ) {
                    block_container.find('.ywpar-bulk-progress').hide('slow').remove();
                    window.location = response.url;
                }else if( 'error' == response.step){
                    block_container.find('.ywpar-bulk-progress div').html( response.error );
                } else {
                    block_container.find('.ywpar-bulk-progress div').html( response.percentage + '%' );
                    block_container.find('.ywpar-bulk-progress div').animate({
                        width: response.percentage + '%',
                    }, 50, function() {
                        // Animation complete.
                    });
                    process_step( parseInt( response.step ), data, form );
                }

            }
        });

    }

    $.fn.serializefiles = function() {
        var obj = $(this);
        /* ADD FILE TO PARAM AJAX */
        var formData = new FormData();
        var params = $(obj).serializeArray();

        $.each(params, function (i, val) {
           formData.append(val.name, val.value);
        });

        return formData;
    };
});
