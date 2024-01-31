jQuery(document).ready(function($){
    
    var a2w_shipping_api = { 
         get_country_node: function(){

            var country_node = false;

            if ($("#a2w_to_country_popup_field").length > 0){
                //product
                country_node = $("#a2w_to_country_popup_field");
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
           return  $("#a2w_to_country_popup_field").length > 0 ? true : false;
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
                            if ($('.woocommerce-shipping-calculator').length > 0){
                                $('.woocommerce-shipping-calculator').submit(); 
                            }
                            else {
                                //for checkout
                                $('#a2w_shipping_modal_'+id).dialog('destroy');
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
    };
   
    //popup js
    var a2w_popup_shipping_js = {
        
        apply_shipping_method : function(country_node, item_id){          
            var shipping_wrap_node = $('#a2w_shipping_wrap_'+item_id);
              
            var init_method_value = shipping_wrap_node.find('.a2w_shipping_method_field').val();

            const shipping_data = shipping_wrap_node.data('shipping-info');
            const initial_shipping_data = shipping_wrap_node.data('initial-shipping-info'); 

            var country_value = country_node.val();
            var method_value = shipping_data.default_method;

      

            if (a2w_shipping_api.is_product_page()){
         
                //Allow adding the product to the cart if it can't be delivered
                if (!shipping_data.default_method){

                    var a2w_remove_cart_item = Number(shipping_wrap_node.find('.a2w_remove_cart_item').val());
                    var a2w_fake_method = shipping_wrap_node.find('.a2w_fake_method').val();

                   //it can be used to not allow
                   /* if (a2w_remove_cart_item > 0){  */      
                        shipping_wrap_node.find('.a2w_to_country_field').val(country_value);
                        shipping_wrap_node.find('.a2w_shipping_method_field').val(a2w_fake_method);
    
                        shipping_wrap_node.find('.shipping_info').html(shipping_data.shipping_info);
                    /*}*/
                   
                    return;
   

                }

            } else {

                //Prevent page reloading on checkout & cart page
                //if any of below conditions are fired

                if (shipping_data.default_method == init_method_value){
                    return;
                }

                if (!shipping_data.default_method){
                    return;
                }
            }

    

            /*   var min_price = shipping_data.formated_price,
            country_label = country_node.find("option:selected").text(),
            method_label = shipping_data.method_label;*/

            //Next, goes a code for items with shipping methods

            var country_label = country_node.find("option:selected").text();


            //update visual short info

            //if (shipping_data.shipping || initial_shipping_data.normalized_methods){

                shipping = shipping_data.shipping ? shipping_data.shipping  : initial_shipping_data.normalized_methods;

                $.each(shipping, function (i, item) {

                    if (item.serviceName == method_value ){

                        shipping_wrap_node.find('.shipping_info').html( item.label );

                    }
                });
           // }


        
         
            //update data 
            if (a2w_shipping_api.is_product_page()){
                //for product page
                shipping_wrap_node.find('.a2w_to_country_field').val(country_value);
                shipping_wrap_node.find('.a2w_shipping_method_field').val(method_value);
            } else {
                //for cart & checkout
                a2w_shipping_api.ajax_update_shipping_method_in_cart_item( item_id, method_value, country_value );
    
            }

        },
        
        find_min_shipping_price : function(items, default_method) {
            var result = false;
            var p = -1;
            jQuery.each(items, function (i, item) {
                const price = item.previewFreightAmount ? item.previewFreightAmount.value : item.freightAmount.value;
                if (p < 0 || price < p || item.serviceName == default_method) {
                    p = price;
                    result = { 'serviceName': item.serviceName, 'price': price, 'formated_price': price > 0.009 ? (item.formated_price) : 'Free', 'name': item.company, 'time': item.time };
                    if (item.serviceName == default_method) {
                        return result;
                    }
                }
            });
            return result;
        },

        fill_modal_shipping_info : function(item_id, product_id, country, items, default_method = '', shipping_info='') {

            var shipping_wrap_node = $('#a2w_shipping_wrap_'+item_id);
            var shipping_modal_node = $('#a2w_shipping_modal_'+item_id);
            

            const tmp_data = { item_id, product_id, country, 'shipping': items, default_method,  shipping_info};
            shipping_wrap_node.data('shipping-info',tmp_data);

            var shipping_table_node = shipping_modal_node.find('.shipping-table');
            var modal_shipping_info_node = shipping_modal_node.find('.shipping_info');

            if (items.length > 0) {
                modal_shipping_info_node.hide();
                
             
                const min_shipping_price = a2w_popup_shipping_js.find_min_shipping_price(tmp_data.shipping, default_method);
            
                let html = shipping_modal_node.find('.shipping-table div.first-row').prop('outerHTML');
            
                jQuery.each(tmp_data.shipping, function (i, item) {
                    html += item.html_row.replaceAll('{item_id}', item_id);
                });
            
                shipping_table_node.html(html);
               // shipping_table_node.show();
               shipping_table_node.removeClass('hidden');      
             
                if (min_shipping_price){
                    var min_method_selector = '#a2w_shipping_method_popup_field_'+item_id+'_'+min_shipping_price.serviceName;
                    $(min_method_selector).prop("checked", true);
                }

            } else {
                //shipping_table_node.hide(); 
                shipping_table_node.addClass('hidden'); 
                modal_shipping_info_node.html(shipping_info);
                modal_shipping_info_node.show(); 
            }
            
        },

        a2w_load_shipping_info : function(product_id, country, $quantity, callback = null, type = 'select', page = 'cart') {
            var data = { 'action': 'a2w_frontend_load_shipping_info', 'id': product_id, 'country': country, 'quantity' : $quantity, 'type': type , 'page' : page };
        
            jQuery.post(a2w_ali_ship_data.ajaxurl, data).done(function (response) {
                var json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                    if (callback) { callback(json.state, [], '', '', []) }
                }
                if (json.state !== 'error' && callback) {
                    const product = json.products ? json.products : falseж
                    const shipping_info = json.shipping_info;
                    callback(json.state, product ? product.items : [], product ? product.default_method : '', product ? product.shipping_cost : 0, shipping_info)
        
                    if (product && product.items.length > 0) {
                        jQuery.post(a2w_ali_ship_data.ajaxurl, { 'action': 'a2w_frontend_update_shipping_list', items: product.items })
                    }
        
                }
        
            }).fail(function (xhr, status, error) {
                console.log(error);
            });
        },

        init : function(country_node, apply_shipping = true){
         
                $(".a2w_shipping_wrap").each(function(){
                    var country = country_node.val();
                    var shipping_wrap_node = $(this); 

                    var item_id = shipping_wrap_node.find('.item_id').val(); 
                    
                    if (typeof variation_id !== "undefined"){
                        var item_id = variation_id;
                    } else {
                        var item_id = shipping_wrap_node.find('.item_id').val();
                    }
               
                    var shipping_modal_node = $('#a2w_shipping_modal_'+item_id);

                    var product_id = shipping_wrap_node.find(".product_id").val();

                    var page = a2w_shipping_api.is_product_page() ? 'product' : 'cart';

                    var shipping_result_node = shipping_modal_node.find('.shipping-result');
                
                    shipping_result_node.block({message: null, overlayCSS: {background: '#fff', opacity: 0.6}});

                    var $quantity_node = '';
                    
                    if (a2w_shipping_api.is_product_page()){
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

                    a2w_popup_shipping_js.a2w_load_shipping_info(product_id, country, $quantity, function (state, items, default_method, shipping_cost, shipping_info) {
           
                      if (!a2w_shipping_api.is_product_page()){
                        default_method = shipping_wrap_node.find(".a2w_shipping_method_field").val();
                      }

                        if (state !== 'error') {

                            a2w_popup_shipping_js.fill_modal_shipping_info(item_id, product_id, country, items, default_method, shipping_info);
                     
                            if (apply_shipping){

                                a2w_popup_shipping_js.apply_shipping_method(country_node, item_id);
                              
                            }

                            shipping_result_node.unblock();
    
                        } else {

                            shipping_result_node.unblock();

                            console.log('Ali2Woo can`t get shipping info for product: ' + product_id + ', country: ' + country);
                            return false;
                        }
    
                    }, 'popup', page);
                })
        }

    }

    var country_node = a2w_shipping_api.get_country_node();

    if (!country_node){
        console.log('Ali2Woo can`t find country node on the page');
        return false;
    }
 
    //init popus on page load
    //  a2w_popup_shipping_js.init(country_node, false);


    //open popup on click
    $( document ).on(
        'click',
        '.a2w_shipping_wrap .shipping_info',
        function(){

            var shipping_wrap_node = $(this).parents('.a2w_shipping_wrap');
            var item_id = shipping_wrap_node.find('.item_id').val();
      
            var shipping_modal_node = $('#a2w_shipping_modal_'+item_id);
            
            shipping_modal_node.dialog({
                'modal': true,
                'resizable': false,
                'closeText': "close",
                'classes': {
                    "ui-dialog": "ali2woo-ui-dialog"
                },
                beforeClose: function( event, ui ) {

                    var shipping_wrap_node = $('#a2w_shipping_wrap_'+item_id);
                    var shipping_modal_node = $('#a2w_shipping_modal_'+item_id);

                    var shipping_data = shipping_wrap_node.data('shipping-info');

                    if (typeof shipping_data == "undefined"){
                        shipping_data = { item_id : false, product_id : false, country :  false, 'shipping': false, default_method : false,  shipping_info : false};
                    }

                    shipping_table_node = shipping_modal_node.find('.shipping-table');   

                    $radios = shipping_modal_node.find("input[name='a2w_shipping_method_popup_field_"+item_id+"']");

                    if (shipping_table_node.is(":hidden") || $radios.length < 1){
                        shipping_data.default_method = false;
                        shipping_wrap_node.data('shipping-info', shipping_data);
                        return;
                    } 

                    //remember popup data
                    var selected_method = $radios.parent().find(':checked').val();

                    var selected_row_dom =  $radios.parent().find(':checked').parent().parent();

                    shipping_data.default_method = selected_method;
                
                    shipping_data.formated_price = selected_row_dom.find('.a2w-div-table-col').eq(2).html();
                    shipping_data.tracking = selected_row_dom.find('.a2w-div-table-col').eq(3).html();
                    shipping_data.method_label = selected_row_dom.find('.a2w-div-table-col').eq(4).html();

                    shipping_wrap_node.data('shipping-info',shipping_data);

                },
                close : function(){
                    
                },
                'buttons': [
                    {
                    'text': a2w_ali_ship_data.lang.apply,
                    'icon': "ui-icon-heart",
                    'click': function() {

                        //get country again, because need to have the actual country value
                        var country_node = a2w_shipping_api.get_country_node();

                        $( this ).dialog( "close" );
                        a2w_popup_shipping_js.apply_shipping_method(country_node, item_id, false);                           
                    }
                
                    // Uncommenting the following line would hide the text,
                    // resulting in the label being used as a tooltip
                    //showText: false
                    }
                ],
            
            });
        }
    );

    //---POPUP BEHAVIOUR ON COUNTRY CHANGE---
           
   //on cart page
   $( document.body ).on('updated_wc_div', function(){  

      //clear old modals
      $(".ali2woo-ui-dialog .a2w_shipping_modal").each(function(){
        $(this).dialog('destroy').remove();
      });
    
       // a2w_popup_shipping_js.init(country_node);
   }); 

      //on checkout page
  $( document.body ).on('update_checkout', function(){  
       //todo: perhaps this is a rudiment code, remove it?
       // a2w_popup_shipping_js.init(country_node);
   }); 

   
   
//on product page:

    //quantity change
    $(document).on('change', 'form.cart input[name="quantity"]', function () {
      
        let $quantity = $(this), $form = $quantity.closest('form.cart');
        let shipping_wrap_node = $form.find('.a2w_shipping_wrap');
        let variation_id = $form.find('input[name="variation_id"], input.variation_id').val();
        let cur_product_id = shipping_wrap_node.find(".product_id").val();

        if (variation_id && variation_id !== cur_product_id ){

            //if variable product & variant is changed, update product id
            shipping_wrap_node.find(".product_id").val(variation_id);

        } else {
            //update shipping if variant is not change or if this is a simple product
            a2w_popup_shipping_js.init(country_node, true);    
        }
           
    });

    //country change
    $( document ).on(
        'change',
        '#a2w_to_country_popup_field',function(){
            if (country_node.val() !== ""){
                a2w_popup_shipping_js.init(country_node, false);
            }
    });

    //variation change
     $('.single_variation_wrap').on('show_variation', function (event, variation) {
   
            var shipping_wrap_node = $(this).find('.a2w_shipping_wrap');          

            if (variation.is_in_stock){
                shipping_wrap_node.removeClass('hidden');
                a2w_popup_shipping_js.init(country_node, true);
            } else {
                shipping_wrap_node.addClass('hidden');    
            }
        
    });
    
      
})