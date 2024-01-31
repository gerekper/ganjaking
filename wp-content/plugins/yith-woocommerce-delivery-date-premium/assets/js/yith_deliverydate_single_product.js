jQuery(function ($) {

  var single_product_variation = $('#ywcdd_info_single_product_variation'),
    single_product_variation_wrapper = $('#ywcdd_info_single_product_variation_wrap'),
    toggle_general_date_content = function (show){
      var variable_date_info = $('#ywcdd_info_single_product.variable');

      if( variable_date_info.length ){
         if( show ){
           variable_date_info.show();
         }else{
           variable_date_info.hide();
         }
      }
    }

  if (single_product_variation_wrapper.length) {
    $('.variations_form.cart').on('found_variation', function (e, variation_data) {

      var last_shipping_info = typeof variation_data.ywcdd_last_shipping_info !== 'undefined' ? variation_data.ywcdd_last_shipping_info : '',
        delivery_date_info = typeof variation_data.ywcdd_delivery_info !== 'undefined' ? variation_data.ywcdd_delivery_info : '',
        show = !(variation_data.is_virtual || variation_data.is_downloadable);

      if (show) {
        toggle_general_date_content(false);
        var template = wp.template('variation-ywcdd-date-info-template');

        var template_html = template({
          'variation': variation_data
        });

        single_product_variation.html(template_html);
        if ('' === last_shipping_info) {
          single_product_variation.find('#ywcdd_info_shipping_date').hide();
        }

        if ('' === delivery_date_info) {
          single_product_variation.find('#ywcdd_info_first_delivery_date').hide();
        }
        single_product_variation_wrapper.show();
      } else {
        single_product_variation.html('');
        single_product_variation_wrapper.hide();
        toggle_general_date_content(true);
      }
    }).on('reset_data', function (e) {
      single_product_variation.html('');
      single_product_variation_wrapper.hide();
      toggle_general_date_content(true);
    });
  }
});
