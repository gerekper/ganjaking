jQuery(document).ready(function($){

    $(document).on('click', '#ywcdd_delivery_order_metabox .edit_address', function (e) {

        e.preventDefault();
        $('.processing_delivery_date').hide();
        $('.edit_processing_delivery_date').show();
    });

    $(document).on('click', '.edit_processing_delivery_date .ywcdd_update_date', function (e) {
            e.preventDefault();
            var input = $(this).parents('.edit_processing_delivery_date').find(':input'),
                is_valid = true;

            $.each( input, function(i, field){

                var id = $(field).attr('id');

                if( typeof  id !== 'undefined' ){

                    if( $(field).val() === '' ){
                        is_valid = false;
                        $(field).css({'border':'1px solid red'});
                    }else{
                        $(field).css({'border': 'none'});
                    }
                }
            });

           if( is_valid ) {

               var data = {
                   'order_id': $('#post_ID').val(),
                   'processing_method_id': $('#ywcdd_edit_processing_method').val(),
                   'carrier_id': $('#ywcdd_edit_carrier').val(),
                   'processing_date': $('#ywcdd_edit_processing_date').val(),
                   'delivery_date': $('#ywcdd_edit_delivery_date').val(),
                   'time_from': $('#ywcdd_edit_time_from').val(),
                   'time_to': $('#ywcdd_edit_time_to').val(),
                   'action': ywcdd_order_args.actions.update_order_details,
                   'security' : ywcdd_order_args.update_order_details_nonce
               },
                   block_ui = {
                       message: null,
                       overlayCSS: {
                           background: '#fff',
                           opacity: 0.6
                       },
                       ignoreIfBlocked: true
                   };

               $('#ywcdd_delivery_order_metabox').block( block_ui);

               $.ajax({
                   type: 'POST',
                   url: ywcdd_order_args.ajax_url,
                   data: data,
                   dataType: 'json',
                   success: function (response) {
                       $('#ywcdd_delivery_order_metabox').unblock();
                   }
               });
           }
    });

    $('.ywcdd_timepicker').timepicker({
        'timeFormat': ywcdd_order_args.timeformat,
        'step': ywcdd_order_args.timestep
    });

    $('.ywcdd_datepicker').datepicker({
       dateFormat: 'yy-mm-dd'
    });
});