jQuery(document).ready(function($){
 
    var a2w_select_shipping_js = {
         init : function(){

            var shipping_wrap_node = $('.a2w_shipping_wrap'), 
            country_value = country_node.val();

            var item_id = shipping_wrap_node.find('.item_id').val();

            var page = a2w_select_shipping_js.is_product_page() ? 'product' : 'cart';

            shipping_wrap_node.block({message: null, overlayCSS: {background: '#fff', opacity: 0.6}});


            var $quantity_node = '';
                    
            if (a2w_select_shipping_js.is_product_page()){
                $quantity_node = $('form.cart input[name="quantity"]');      
            } else {
                console.log('Quantity field is not found. This is not a product page.');
            }

            var $quantity = 1;

            if ($quantity_node.val() === undefined || $quantity_node.val() === 0){
                $quantity = 1;
            }  else {
                $quantity = $quantity_node.val();
            } 


            a2w_select_shipping_js.a2w_load_shipping_info(item_id, country_value, $quantity, function (state, items, default_method, shipping_cost, shipping_info){

                if (state !== 'error') {
                    var shipping = items, 
                    shipping_select_node = shipping_wrap_node.find('.a2w_shipping'),
                    shipping_select =  shipping_wrap_node.find('.a2w_shipping select'),
                    shipping_info_node = shipping_wrap_node.find('.info'),
                    country_label = country_node.find('option:selected').text();

                    shipping_select.empty();

                    shipping_info = shipping_info.replaceAll('{country}', country_label );
                    
                    var a2w_remove_cart_item = Number(shipping_wrap_node.find('.a2w_remove_cart_item').val());
                    var a2w_fake_method = shipping_wrap_node.find('.a2w_fake_method').val();

                    if (shipping.length === 0 /*&& a2w_remove_cart_item > 0*/){
                        
                        shipping_select_node.addClass('hidden');
                        shipping_select.append('<option value="'+a2w_fake_method+'" selected="selected"></option>');    
                        
                        shipping_info_node.html(shipping_info);    
                        shipping_info_node.removeClass('hidden');

                        shipping_wrap_node.unblock();
                        return; 
                    }


                    jQuery.each(shipping, function (i, item) {

                        var decodedLabel = $("<div/>").html(item.label.replaceAll('{country}', country_label )).text();

                        shipping_select.append('<option value="' + item.serviceName + '">' + decodedLabel + '</option>');
                        
                    });

                    if (shipping.length > 0){
                        shipping_select_node.removeClass('hidden');
                        shipping_info_node.addClass('hidden');
                    } else {

                            shipping_select_node.addClass('hidden');
                            shipping_info_node.html(shipping_info);     
                            shipping_info_node.removeClass('hidden');     
                                      
                    }   
                    
                    shipping_wrap_node.unblock();

                } else {

                    shipping_wrap_node.unblock();

                    console.log('Ali2Woo can`t get shipping info for product: ' + product_id + ', country: ' + country);
                    return false;
                }

                

            }, 'select', page);

         },
         get_country_node: function(){

            var country_node = false;

            if ($("#a2w_to_country_field").length > 0){
                //product
                country_node = $("#a2w_to_country_field");
            } else if ( $('#calc_shipping_country').length > 0  ){
                //cart
                country_node = $('#calc_shipping_country');
            } else if ( $('.shipping_address').length > 0 ){
                //checkout
                if ($('.shipping_address').css('display') == "none"){
                    country_node = $('#billing_country');
                } else {
                    country_node = $('#shipping_country');      
                }
                
            }  
            
            return country_node;

         },
         is_product_page: function(){
            return  $("#a2w_to_country_field").length > 0 ? true : false;
          },
         ajax_update_shipping_method_in_cart_item : function(id, tariff_code, country){
     
            var data = {'action': 'a2w_update_shipping_method_in_cart_item','id':id, 'value': tariff_code, 'calc_shipping_country' : country};
           
               $.ajax({
                   url : a2w_ali_ship_data.ajaxurl,
                   type : 'POST',
                   data : data,
                   tryCount : 0,
                   retryLimit : 3, 
                   success : function (response) {
                  
                       if (response == ''){
                            this.tryCount++;
                            if (this.tryCount <= this.retryLimit) {
                                //try again
                                $.ajax(this);
                                return false;
                            }
                            console.log('Something is wrong with your server');            
                            return false;     
                       }
                                                  
                        var json = jQuery.parseJSON(response);
                        
                        if (json.state){
                       
                            if (json.state == "ok"){
                                //for cart
                                if ($('.woocommerce-shipping-calculator').length > 0)
                                    $('.woocommerce-shipping-calculator').submit(); 
                                else {
                                    //for checkout
                                    $( document.body ).trigger( 'update_checkout' );                           
                                }                                                       
                            }
                            
                            if (json.state == "error"){
                                //just reserved for error 
                            }
                    
                        
                        }
                    
                   },                
                   error : function(xhr, textStatus, errorThrown ) {
                        if (textStatus == 'timeout') {
                            this.tryCount++;
                            if (this.tryCount <= this.retryLimit) {
                                //try again
                                $.ajax(this);
                                return false;
                            }            
                            return false;
                        }
                        if (xhr.status == 500) {
                            //handle error
                        } else {
                            //handle error
                        }
                   }
              });    
         },

         a2w_load_shipping_info : function(product_id, country, $quantity, callback = null, type = 'select', page = 'cart') {
            var data = { 'action': 'a2w_frontend_load_shipping_info', 'id': product_id, 'country': country, 'quantity' : $quantity, 'type': type, 'page' : page };
        
            jQuery.post(a2w_ali_ship_data.ajaxurl, data).done(function (response) {
                var json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                    if (callback) { callback(json.state, [], '', '', []) }
                }
                if (json.state !== 'error' && callback) {
                    const product = json.products ? json.products : false;
                    const shipping_info = json.shipping_info;
                    callback(json.state, product ? product.items : [], product ? product.default_method : '', product ? product.shipping_cost : 0, shipping_info)
        
                    if (product && product.items.length > 0) {
                        jQuery.post(a2w_ali_ship_data.ajaxurl, { 'action': 'a2w_frontend_update_shipping_list', items: product.items})
                    }
        
                }
        
            }).fail(function (xhr, status, error) {
                console.log(error);
            });
        },
    };

    var country_node = a2w_select_shipping_js.get_country_node();

    if (!country_node){
        console.log('Ali2Woo can`t find country node on the page');
        return false;
    }


//in cart:

    //country change
    if (!a2w_select_shipping_js.is_product_page()){
        $(document.body).on(
            'change', 
            '.a2w_shipping_wrap select', function() {
            
                var shipping_wrap_node = $(this).parents('.a2w_shipping_wrap'), 
                item_id = shipping_wrap_node.find('.item_id').val(), 
                method_value = $(this).val(),
                country_value = country_node.val();


                a2w_select_shipping_js.ajax_update_shipping_method_in_cart_item(item_id, method_value, country_value);           
        }); 
    }

//on product page:

    //country change
    $( document ).on(
        'change',
        '#a2w_to_country_field',function(){
            if (country_node.val() !== "")
                a2w_select_shipping_js.init();
    });


    //quantity change
    $(document).on('change', 'form.cart input[name="quantity"]', function () {

        let $quantity = $(this), $form = $quantity.closest('form.cart');
        let shipping_wrap_node = $form.find('.a2w_shipping_wrap');
        let variation_id = $form.find('input[name="variation_id"], input.variation_id').val();
        let cur_product_id = shipping_wrap_node.find(".item_id").val();

        if (variation_id && variation_id !== cur_product_id ){

            //if variable product & variant is changed, update product id
            shipping_wrap_node.find('.item_id').val(variation_id);

        } else {
            //update shipping if variant is not change or if this is a simple product
            a2w_select_shipping_js.init();   
        }

    });

    //variation change
    $('.single_variation_wrap').on('show_variation', function (event, variation) {
       
        var shipping_wrap_node = $(this).find('.a2w_shipping_wrap');

        if (variation.is_in_stock){
            shipping_wrap_node.removeClass('hidden');
            a2w_select_shipping_js.init();
        } else {
            shipping_wrap_node.addClass('hidden');    
        }
    });
      
})