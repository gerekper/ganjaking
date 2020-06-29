jQuery(document).ready(function($) {

    /*Desplegable Button for variable products*/
    $( '.desplegable').on( 'click', function(){

        if($(this).hasClass('dashicons-arrow-up')){
            $(this).removeClass('dashicons-arrow-up');
            $(this).addClass('dashicons-arrow-down');
        } else {
            $(this).removeClass('dashicons-arrow-down');
            $(this).addClass('dashicons-arrow-up');
        }

        $( this ).closest( 'tr' ).find( '.childs' ).each( function(){
           $( this ).toggle( 'slow' );
       }) ;

    });

    //Move the report table inside the date div
    $("div #table-content").appendTo("div .inside");


    /*Calendar in the date filter*/
    $(function () {
        $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            defaultDate: '',
            dateFormat: 'yy-mm-dd',
            numberOfMonths: 1,
            minDate: '-20Y',
            maxDate: '+1D',
            showButtonPanel: true,
            showOn: 'focus',
            buttonImageOnly: true
        });
    });

    $(function () {
        $("#end_date").datepicker({
            changeMonth: true,
            changeYear: true,
            defaultDate: '',
            dateFormat: 'yy-mm-dd',
            numberOfMonths: 1,
            minDate: '-20Y',
            maxDate: '+1D',
            showButtonPanel: true,
            showOn: 'focus',
            buttonImageOnly: true
        });
    });


    //Product Bulk Action
    $(document).ready(function() {
        // Watch the bulk actions dropdown, looking for custom bulk actions
        $("#bulk-action-selector-top, #bulk-action-selector-bottom").on('change', function(e){
            var $this = $(this);

            if ( $this.val() == 'set_cost_of_goods' ) {
                $this.after($("<input>", { type: 'text', placeholder: "Enter the Cost value", name: "yith_cog_cost" }).addClass("custom-bulk-actions-elements"));
            } else {
                $(".custom-bulk-actions-elements").remove();
            }
        });
    });


    //Variations Bulk Actions
    $('select#field_to_edit').on('variable_cost_of_goods_ajax_data', function() {
        return {value: window.prompt(woocommerce_admin_meta_boxes_variations.i18n_enter_a_value)};
    });


    // ajax para el boton apply cost
    $( '.apply_cost' ).on( 'click', function () {
        yith_apply_cost_process();
    });

    function yith_apply_cost_process( limit,offset ) {
        $ajax_zone = $('#ajax_zone');

        if (typeof(offset) === 'undefined') offset = 0;
        if (typeof(limit) === 'undefined') limit = 0;

            var post_data = {
                'limit': limit,
                'offset': offset,
                action: 'yith_apply_cost_button'
            };
            if (offset == 0)
                $ajax_zone.block({message: null, overlayCSS: {background: "#f1f1f1", opacity: .7}});
            $.ajax({
                type: "POST",
                data: post_data,
                url: object.ajaxurl,
                success: function (response) {
                    console.log('Processing, do not cancel');
                    if (response.loop == 1)
                        yith_apply_cost_process(response.limit, response.offset);
                    if (response.loop == 0)
                        $ajax_zone.unblock();
                },
                error: function (response) {
                    console.log("ERROR");
                    console.log(response);
                    $ajax_zone.unblock();
                    return false;
                }
            });
    }


    // ajax para el boton apply cost
    $( '.apply_cost_overriding' ).on( 'click', function () {
        yith_apply_cost_overriding_process();
    });

    function yith_apply_cost_overriding_process( limit,offset ) {
        $ajax_zone = $('#ajax_zone');

        if (typeof(offset) === 'undefined') offset = 0;
        if (typeof(limit) === 'undefined') limit = 0;

            var post_data = {
                'limit': limit,
                'offset': offset,
                action: 'yith_apply_cost_overriding_button'
            };
            if (offset == 0)
                $ajax_zone.block({message: null, overlayCSS: {background: "#f1f1f1", opacity: .7}});
            $.ajax({
                type: "POST",
                data: post_data,
                url: object.ajaxurl,
                success: function (response) {
                    console.log('Processing, do not cancel');
                    if (response.loop == 1)
                        yith_apply_cost_overriding_process(response.limit, response.offset);
                    if (response.loop == 0)
                        $ajax_zone.unblock();
                },
                error: function (response) {
                    console.log("ERROR");
                    console.log(response);
                    $ajax_zone.unblock();
                    return false;
                }
            });
    }


    // ajax para el boton apply cost
    $( '.apply_cost_selected_order_button' ).on( 'click', function () {
        yith_apply_cost_selected_order_process();
    });

    function yith_apply_cost_selected_order_process( ) {
        $ajax_zone = $('#ajax_zone');


        var order_id_value = $( '.apply_cost_selected_order_input').val();

            var post_data = {
                action: 'yith_apply_cost_selected_order_button',
                order_id: order_id_value
            };
            $ajax_zone.block({message: null, overlayCSS: {background: "#f1f1f1", opacity: .7}});
            $.ajax({
                type: "POST",
                data: post_data,
                url: object.ajaxurl,
                success: function (response) {
                    $ajax_zone.unblock();
                    return false;
                }
            });
    }

        var a = document.createElement('a');

        if (typeof a.download === 'undefined') {
            $('.export_csv').hide();
        }


        // Export
        $('.yith_export_csv').click(function () {

            var export_format = $(this).data('export');
            var csv_data = 'data:application/csv;charset=utf-8,';

            if ('table' === export_format) {

                $(this).offsetParent().find('thead tr,tbody tr ').each(function () {
                    $(this).find('th, td').each(function () {
                        var value = $(this).contents().not('div.childs').text();
                        value = value.replace('[?]', '').replace('#', '').replace('Show more details', '').replace('Actions', '').replace('EditView', '');
                        csv_data += '"' + value + '"' + ',';
                    });
                    csv_data = csv_data.substring(0, csv_data.length - 1);
                    csv_data += '\n';
                });
            }

            // Set data as href and return
            $(this).attr('href', encodeURI(csv_data));
            return true;
        });


    //Quick edit data cost
    $('#the-list').on('click', '.editinline', function(e) {
        var cost, inline_data, post_id;
        post_id = $(this).closest('tr').attr('id');
        post_id = post_id.replace('post-', '');
        inline_data = $('#yith_cog_inline_' + post_id);
        cost = inline_data.find('.yith_cog_cost').text();
        return $('input[name="yith_cog_cost"]').val(cost);
    });


    // ajax para el boton import cost
    $( '.import_cost' ).on( 'click', function () {
        yith_import_cost_process();
    });

    function yith_import_cost_process( limit,offset ) {
        $ajax_zone = $('#ajax_zone_import_cost');

        if (typeof(offset) === 'undefined') offset = 0;
        if (typeof(limit) === 'undefined') limit = 0;

        var post_data = {
            'limit': limit,
            'offset': offset,
            action: 'yith_import_cost_button'
        };
        if (offset == 0)
            $ajax_zone.block({message: null, overlayCSS: {background: "#f1f1f1", opacity: .7}});
        $.ajax({
            type: "POST",
            data: post_data,
            url: object.ajaxurl,
            success: function (response) {
                console.log('Processing, do not cancel');
                if (response.loop == 1)
                    yith_import_cost_process(response.limit, response.offset);
                if (response.loop == 0)
                    $ajax_zone.unblock();
            },
            error: function (response) {
                console.log("ERROR");
                console.log(response);
                $ajax_zone.unblock();
                return false;
            }
        });
    }
});
