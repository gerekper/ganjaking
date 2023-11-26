(function($) {
    'use strict';

    $(document).ready(function() {
        $('body').on('change', 'input[name="payment_method"]', function() {
            $('body').trigger('update_checkout');
        });

        /////// for tier  rule custom css 
        var rtwwdpd_rule_name = rtwwdpd_ajax.rtwwdpd_rule_name;
        $.each( rtwwdpd_rule_name, function( key, value ) {
            $(document).find(".rtwwdpd_custom_css").css({"text-align":'center',"color":"black","background-color": value.rtwwdpd_offer_header_color});
            $(document).find(".rtwwdpd_same_th").css({"text-align":'center',"color":"black","background-color": value.rtwwdpd_offer_lft_col_color});
            $(document).find(".rtwwdpd_same_td").css({"text-align":'center',"color":"black","background-color": value.rtwwdpd_offer_right_col_color});
          });

        ///////

        /////// for tier cat  rule custom css 
        var rtwwdpd_tier_cat_rule_name = rtwwdpd_ajax.rtwwdpd_tier_cat_rule_name;
        //   console.log(rtwwdpd_tier_cat_rule_name);
        $.each( rtwwdpd_tier_cat_rule_name, function( key, value ) {
            $(document).find(".rtwwdpd_heading_color").css({"text-align":'center',"color":"black","background-color": value.rtwwdpd_offer_header_color});
            // $(document).find(".rtwwdpd_column_color").css({"text-align":'center',"color":"black","background-color": value.rtwwdpd_offer_lft_col_color});
            $(document).find(".rtwwdpd_column_color").css({"text-align":'center',"color":"black","background-color": value.rtwwdpd_offer_right_col_color});
          });
          
        ///////

  ///// set timer for sale
     /////////////  Satrt cod  For Timer //////////////////////
     function makeTimer() 
     {
         if(rtwwdpd_ajax.rtwwdpd_without_ajax)
         {
             var end_date=rtwwdpd_ajax.rtwwdpd_without_ajax['end_date'];
             var cur_date=rtwwdpd_ajax.rtwwdpd_without_ajax['curnt_date'];
             var end_time=rtwwdpd_ajax.rtwwdpd_without_ajax['end_time'];
             var msg=rtwwdpd_ajax.rtwwdpd_without_ajax['msg'];
             var e_time=$.now(end_time);
             var tim = $.now();
             var today = new Date();
             
             var time = today.getHours() + ":" + today.getMinutes();
             var seconds = Date.parse(end_date+' '+end_time)-tim;
             var  totalSeconds = parseInt(Math.floor(seconds / 1000));
             var  totalMinutes = parseInt(Math.floor(totalSeconds / 60));
             var  totalHours = parseInt(Math.floor(totalMinutes / 60));
             var  days = parseInt(Math.floor(totalHours / 24));
             seconds = parseInt(totalSeconds % 60);
             var  minutes = parseInt(totalMinutes % 60);
             var  hours = parseInt(totalHours % 24);
            if(cur_date > end_date)
            {  
                $(document).find("#test1").remove();
                $(document).find("#days").remove();
                $(document).find("#hours").remove();
                $(document).find("#minutes").remove();
                $(document).find("#second").remove();
                // $(document).find(".rtweo_sale_message").remove();
                return false; 
            }
            else
            {
                $(document).find(".rtweo_sale_message").css("display","block");
                
                 if( cur_date == end_date )
                 {   
                     if(time >= end_time)
                     { 
                         $(document).find("#test1").remove();
                         $(document).find("#days").remove();
                         $(document).find("#hours").remove();
                         $(document).find("#minutes").remove();
                         $(document).find("#second").remove();
                         $(document).find(".rtweo_sale_message").remove();

                         return false;
                     }
                 }
                  
                    $(document).find("#test1").html(msg);
                    $(document).find("#days").html( days);
                    $(document).find("#hours").html(hours);
                    $(document).find("#minutes").html( minutes);
                    $(document).find("#second").html( seconds);
            }
         }
         else
         {
             $(document).find("#test1").remove();
                 return false; 
         }
     }
     setInterval(function() { makeTimer(); }, 1000);
     /////////////  End cod  For Timer //////////////////////
    
        /////////////////////////

        // jQuery('div.woocommerce').on('change', '.qty', function(){
        // 	jQuery("[name='update_cart']").prop("disabled", false);
        // 	jQuery("[name='update_cart']").trigger("click"); 
        // });
        // jQuery("[name='update_cart']").on('click', function(){
        // 	window.location.reload();
        // });
        $('.tier_offer_table').addClass('active');
        var counter = false;
        setInterval(function() 
        {
            var c =  $('.tier_offer_table');
            counter = !counter;
            var percent=$('.percent');
            var fixed=$(".fixed");
            fixed.each(function(){
                $(this).css('display', counter ? 'block' : 'none');
            });
            percent.each(function(){
                $(this).css('display', counter ? 'none' : 'block');
            });
        }, 2000);


        $(document).on('change', '.variation_id', function() 
        {
            var rtwwdpd_var_id = $(this).val();
            var rtwwdpd_product_id = $(this).closest('div').find("input[name=product_id]").val();
            if (rtwwdpd_var_id != '') 
            {
                var data = 
                {
                    action: 'rtwwdpd_variation_id',
                    rtwwdpd_var_id: rtwwdpd_var_id,
                    rtwwdpd_prod_id: rtwwdpd_product_id,
                    security_check: rtwwdpd_ajax.rtwwdpd_nonce
                };
                $.ajax({
                    url: rtwwdpd_ajax.ajax_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    success: function(response) 
                    {
                        if (response != 0 && response != '') 
                        {
                            $(document).find('.rtwwdpd_apply_on_variation_' + rtwwdpd_product_id).html(response);
                            $(document).find('.rtwwdpd_apply_on_variation_' + rtwwdpd_product_id).hide();
                        }
                    }
                });
            } else 
            {
                $(document).find('.rtwwdpd_apply_on_variation_' + rtwwdpd_product_id).html('');
                $(document).find('.rtwwdpd_apply_on_variation_' + rtwwdpd_product_id).hide();
            }
        });
    })

})(jQuery);