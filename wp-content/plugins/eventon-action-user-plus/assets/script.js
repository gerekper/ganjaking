/**
 * ActionUser Plus script 
 * @version 0.1
 */
jQuery(document).ready(function($){

// Select submission level
    $('body').on('click','.evoaup_purchase_form .evoaup_slevel',function(){
        FORM = $(this).closest('.evoaup_purchase_form');
        FORM.find('.evoaup_slevel').removeClass('selected');
        FORM.find('.evoaup_purchase').hide();
        $(this).addClass('selected');
        $(this).find('.evoaup_purchase').show();
    });
    
// click add to cart 
    $('body').on('click','.evoaup_add_to_cart',function(){
        OBJ = $(this);
        LEVEL = OBJ.closest('.evoaup_slevel'); 
        FORM = LEVEL.parent();       
        var type = OBJ.data('type');
        var ajaxdataa = {};

        if(type != 'paidev') return false;
        
        ajaxdataa['action'] = 'evoaup_add_cart';
        ajaxdataa['wcid'] = OBJ.data('wcid');
        ajaxdataa['level'] = OBJ.data('level');
        ajaxdataa['sformat'] = OBJ.data('sformat');
        ajaxdataa['qty'] = LEVEL.find('input[name="quantity"]').val();
        ajaxdataa['url'] = window.location.href;

        $.ajax({
            beforeSend: function(){ 
                FORM.addClass( 'evoloading');
            },                  
            url:    evoaup_ajax_script.ajaxurl,
            data:   ajaxdataa,  dataType:'json', type:  'POST',
            success:function(data){
                if(data.status=='good'){
                    FORM.html( data.html );
                    if(data.redirect=='yes'){
                        window.location.href = data.cart_url;
                    }else{
                        update_wc_cart();
                    }
                }else{
                   FORM.find('.evoaup_msg').html(data.msg).addClass('error').show(); 
                }
                
            },complete:function(){  
                FORM.removeClass( 'evoloading');
            }
        });
    });

// Get submission form
     $('body').on('click','.evoaup_submission_level_selection p',function(){
        OBJ = $(this);
        SECTION = OBJ.closest('.evoaup_section');
        var ajaxdataa = {};
        
        ajaxdataa['action'] = 'evoaup_get_submission_form';
        ajaxdataa['wcid'] = OBJ.data('wcid');
        ajaxdataa['level'] = OBJ.data('level');
        ajaxdataa['sformat'] = OBJ.data('sformat');
        ajaxdataa['d'] = SECTION.find('.evoau_form_atts').data('d');

        $.ajax({
            beforeSend: function(){ 
                SECTION.addClass( 'evoloading');
            },                  
            url:    evoaup_ajax_script.ajaxurl,
            data:   ajaxdataa,  dataType:'json', type:  'POST',
            success:function(data){

                if(data.status=='good'){
                    SECTION.addClass('hasform');
                    SECTION.html( data.html );

                    FORM = SECTION.find('.evoau_submission_form');
                    $('body').trigger('evoau_loading_form_content',[ FORM ]);
                }else{
                    SECTION.addClass('error');
                    SECTION.html(data.msg);
                }
                

            },complete:function(){  
                SECTION.removeClass( 'evoloading');
            }
        });
     });

// Update mini cart content
    function update_wc_cart(){
        var data = {
            action: 'evoaup_update_wccart'
        };
        $.ajax({
            type:'POST',url:evoaup_ajax_script.ajaxurl,
            data:data,
            dataType:'json',
            success:function(data){
                
                if (!data) return;

                var this_page = window.location.toString();
                this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );

                var fragments = data.fragments;
                var cart_hash = data.cart_hash;

                // Block fragments class
                fragments && $.each(fragments, function (key, value) {
                    $(key).addClass('updating');
                });
                 
                // Block fragments class
                    if ( fragments ) {
                        $.each( fragments, function( key ) {
                            $( key ).addClass( 'updating' );
                        });
                    }   

                // Block widgets and fragments
                    $( '.shop_table.cart, .updating, .cart_totals' )
                        .fadeTo( '400', '0.6' )
                        .block({
                            message: null,
                            overlayCSS: {
                                opacity: 0.6
                            }
                    });           
                 
                // Replace fragments
                    if ( fragments ) {
                        $.each( fragments, function( key, value ) {
                            $( key ).replaceWith( value );
                        });

                        $( document.body ).trigger( 'wc_fragments_loaded' );            
                    }
                 
                // Unblock
                $( '.widget_shopping_cart, .updating' ).stop( true ).css( 'opacity', '1' ).unblock();
                 
                // Cart page elements
                $( '.shop_table.cart' ).load( this_page + ' .shop_table.cart:eq(0) > *', function() {

                    $( '.shop_table.cart' ).stop( true ).css( 'opacity', '1' ).unblock();

                    $( document.body ).trigger( 'cart_page_refreshed' );
                });

                $( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
                    $( '.cart_totals' ).stop( true ).css( 'opacity', '1' ).unblock();
                });
                 
                // Trigger event so themes can refresh other areas
                $( document.body ).trigger( 'added_to_cart', [ fragments, cart_hash ] );
            }
        });
    }

});