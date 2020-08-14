jQuery(document).ready(function($) {

 var original_content = $(document).find('#ywcdd_info_single_product');


    $('.variations_form.cart').on('found_variation',function(e, variation_data ){

        var variation_info =  typeof  variation_data.ywcdd_date_info !== 'undefined' ? variation_data.ywcdd_date_info : false;

        $(document).find('#ywcdd_info_single_product').replaceWith(variation_info);

 }).on('reset_data',function(e){
        $(document).find('#ywcdd_info_single_product').replaceWith(original_content);
    });

});