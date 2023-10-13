/**
 * Event Ticket script 
 * @version 2.2.2
 */
jQuery(document).ready(function($){



    // ticket data
        $.fn.evotx_hide_loading = function(O){
            el = this;        
            return el.closest('.evorow').find('.evo_loading_bar_holder').remove();
        };

        $.fn.evotx_get_data = function(O){
            el = this;        
            dd = el.hasClass('evotx_ticket_purchase_section') ? el.find('.evotx_data').data() : 
                el.closest('.evotx_ticket_purchase_section').find('.evotx_data').data();

            return dd;
        }; 
        $.fn.evotx_update_data = function(data){
            
            el = this;      
            ROW =   el.closest('.evorow');
            if( el.hasClass('evotx_ticket_purchase_section')) ROW = el;
            tx_data = ROW.evotx_get_data();

            var new_tx_data = $.extend({}, tx_data, data );
            
            ROW.find('.evotx_data').data( new_tx_data );
        };
        $.fn.evotx_get_event_data = function(O){
            el = this;        
            dd = el.hasClass('evotx_ticket_purchase_section') ? el.find('.evotx_data').data() : 
                el.closest('.evotx_ticket_purchase_section').find('.evotx_data').data();

            if( dd === undefined ) return false;
            if( !('event_data' in dd) ) return false;
            return dd.event_data;
        };
        $.fn.evotx_set_event_data = function(new_event_data){
            el = this;        
            dd = el.hasClass('evotx_ticket_purchase_section') ? el.find('.evotx_data').data() : 
                el.closest('.evotx_ticket_purchase_section').find('.evotx_data').data();
            
            dd['event_data'] = new_event_data;
            el.closest('.evorow').data( dd );
        };

    // other data
        $.fn.evotx_get_all_select_data = function(){
            el = this;
            var other_data = {};

            pel = el.closest('.evorow');
            if( el.hasClass('evotx_ticket_purchase_section')) pel = el;
            
            pel.find('.evotx_other_data').each(function(ii){
                $.each( $(this).data(), function (index, value){
                    other_data[ index ] = value; 
                });
                
            });

            return other_data;
        };
        $.fn.evotx_get_select_data = function(unique_class){
            el = this;
            pel = el.closest('.evorow');
            if( el.hasClass('evotx_ticket_purchase_section')) pel = el;

            var other_data = pel.find('.evotx_other_data.'+unique_class).data();

            return other_data;
        };
        $.fn.evotx_set_select_data = function(unique_class, data){
            
            el = this;        
            dd = el.evotx_get_select_data(unique_class);   

            var new_data = $.extend({}, dd , data );
            
            el.closest('.evorow').find('.evotx_other_data.'+unique_class).data(new_data);
            //console.log(new_data);
        };

    // other addon data
        $.fn.evotx_get_custom_data = function(unique_class){
            el = this;
            pel = el.closest('.evorow');
            if( el.hasClass('evotx_ticket_purchase_section')) pel = el;

            var other_data = pel.find('.'+unique_class).data();
            return other_data;
        };
        $.fn.evotx_set_custom_data = function(unique_class, data){
            
            el = this; 
            pel = el.closest('.evorow');
            if( el.hasClass('evotx_ticket_purchase_section')) pel = el;

            dd = pel.evotx_get_custom_data(unique_class);   
            
            pel.find('.'+ unique_class).data( $.extend({}, dd, data ) );
        };


// on change variable product selection
    $('body').on('change','table.variations select',function(){
        CART = $(this).closest('table').siblings('.evotx_orderonline_add_cart');
        STOCK = CART.find('p.stock');

        // check if variable products are out of stock
        if(STOCK.hasClass('out-of-stock')){
            CART.find('.variations_button').hide();
        }else{
            CART.find('.variations_button').show();
        }
    });


// get ticket product total price
    $('body').on('evotx_qty_changed', function(event,QTY, MAX, OBJ ){
        SECTION = OBJ.closest('.evotx_ticket_purchase_section');
        $('body').trigger('evotx_calculate_total', [SECTION]);        
    });

// calculate total price
    $('body').on('evotx_calculate_total', function(event, SECTION ){

        QTY = SECTION.find('input[name=quantity]').val();
        sin_price = SECTION.find('p.price.tx_price_line span.value').data('sp');
        price_extra = 0;

        // single ticket price
        sin_price = parseFloat(sin_price);

        // include sin price additions
        if( SECTION.find('p.price.tx_price_line input').length>0){

            SECTION.find('p.price.tx_price_line input').each(function(){
                if( $(this).hasClass('nyp')) return;

                DATA = SECTION.find('p.price.tx_price_line input').data('prices');
               
                if(DATA === undefined) return;

                price_muli = 0;
                price_extra = 0;
                if( Object.keys(DATA).length>0){
                    $.each(DATA, function(index, val){

                        if( val === undefined) return;
                        if( !('price') in val) return;
                        if( val.price === undefined) return;

                        p =  parseFloat(val.price);
                        p = p * parseInt( val.qty);

                        // uncorrelated and extra price items
                        if( ('pt' in val && val.pt == 'extra') || ('uncor' in val && val.uncor) ){
                            price_extra += p;
                        }else{
                            price_muli += p;
                        }
                        
                    })
                }

                sin_price += price_muli;
            });
        }

        new_price = sin_price * QTY;  
        new_price += price_extra;




        // formating          
        new_price = get_format_price( new_price, SECTION);
        SECTION.find('.evotx_addtocart_total span.value').html( new_price);
    });

// GET format the price
    function get_format_price(price, SECTION){

        // price format data
        tx_data = SECTION.evotx_get_data();
        PF = tx_data.pf;
       
        totalPrice = price.toFixed(PF.numDec); // number of decimals
        htmlPrice = totalPrice.toString().replace('.', PF.decSep);

        if(PF.thoSep.length > 0) {
            htmlPrice = _addThousandSep(htmlPrice, PF.thoSep);
        }
        if(PF.curPos == 'right') {
            htmlPrice = htmlPrice + PF.currencySymbol;
        }
        else if(PF.curPos == 'right_space') {
            htmlPrice = htmlPrice + ' ' + PF.currencySymbol;
        }
        else if(PF.curPos == 'left_space') {
            htmlPrice = PF.currencySymbol + ' ' + htmlPrice;
        }
        else {
            htmlPrice = PF.currencySymbol + htmlPrice;
        }
        return htmlPrice;
    }
    function _addThousandSep(n, thoSep){
        var rx=  /(\d+)(\d{3})/;
        return String(n).replace(/^\d+/, function(w){
            while(rx.test(w)){
                w= w.replace(rx, '$1'+thoSep+'$2');
            }
            return w;
        });
    };

// increase and reduce quantity
    $('body').on('click','.evotx_qty_change', function(event){

        OBJ = $(this);

        if(OBJ.closest('.evotx_quantity').hasClass('one')) return;

        QTY = parseInt(OBJ.siblings('em').html());
        MAX = OBJ.siblings('input').data('max');  

        // plug
        $('body').trigger('evotx_before_qty_changed',[ MAX, OBJ]);

        if(!MAX) MAX = OBJ.siblings('input').attr('max');
           

        NEWQTY = (OBJ.hasClass('plu'))?  QTY+1: QTY-1;

        NEWQTY =(NEWQTY <= 0)? 0: NEWQTY;


        // can not go below 1
        if( NEWQTY == 0 && OBJ.hasClass('min') && !OBJ.hasClass('zpos')){
            return;
        }

        NEWQTY = (MAX!='' && NEWQTY > MAX)? MAX: NEWQTY;

        OBJ.siblings('em').html(NEWQTY);
        OBJ.siblings('input').val(NEWQTY);

        //console.log(MAX);

        if( QTY != NEWQTY) $('body').trigger('evotx_qty_changed',[NEWQTY, MAX, OBJ]);
       
        if(NEWQTY == MAX){

            PLU = OBJ.parent().find('b.plu');
            if(!PLU.hasClass('reached')) PLU.addClass('reached');   

            if(QTY == MAX)   $('body').trigger('evotx_qty_max_reached',[NEWQTY, MAX, OBJ]);                 
        }else{            
            OBJ.parent().find('b.plu').removeClass('reached');
        } 
    });

// on triggers for variations form
    $('body').on('reset_data','form.evotx_orderonline_variable',function(event){
        FORM = $(this);     
        FORM.find('.evotx_variation_purchase_section').hide();
    });

    $('body').on('evolightbox_end',function(){
        $('body').trigger('show_variation');
    });
    $('body').on('show_variation','form.evotx_orderonline_variable',function(event, variation, purchasable){
        FORM = $(this);    
        
        // variation not in stock
        if(!variation.is_in_stock){
            FORM.find('.evotx_variations_soldout').show();
            FORM.find('.evotx_variation_purchase_section').hide();
        }else{
            FORM.find('.evotx_variations_soldout').hide();
            FORM.find('.evotx_variation_purchase_section').show();
        }

        if(variation.sold_individually){
            FORM.find('.evotx_quantity').hide();
        }

        NEWQTY = parseInt(FORM.find('.evotx_quantity_adjuster em').html());
        NEWQTY = (variation.max_qty!= '' && NEWQTY > variation.max_qty)? variation.max_qty: NEWQTY;

        FORM.find('.evotx_quantity_adjuster em').html( NEWQTY);
        FORM.find('.evotx_quantity_adjuster input').val( NEWQTY);
    });

// Standalone button
    $('body').on('click','.trig_evotx_btn',function(){
        LIGHTBOX = $('.evotx_lightbox');
        LIGHTBOX.addClass('show');
        $('body').trigger('evolightbox_show');


        // get form html
        var ajaxdataa = {};
        
        ajaxdataa['action'] = 'evotx_standalone_form';
        ajaxdataa['eid'] = parseInt($(this).data('eid'));
        ajaxdataa['ri'] = parseInt($(this).data('ri'));
        $.ajax({
            beforeSend: function(){ 
                LIGHTBOX.find('.evo_lightbox_body').addClass('evoloading').html('<p class="loading_content"></p>');
            },                  
            url:    evotx_object.ajaxurl,
            data:   ajaxdataa,  dataType:'json', type:  'POST',
            success:function(data){

                LIGHTBOX.find('.evo_lightbox_body').html( data.content );
                $('body').trigger('evotx_standlone_loaded',[ LIGHTBOX, ajaxdataa ]);
                
            },complete:function(){ 
                LIGHTBOX.find('.evo_lightbox_body').removeClass('evoloading');
            }
        });
    });

// Add to cart custom method
// this method is used by ticket addons when adding tickets to cart and simple ticket product
// @version 1.7 up
    $('body').on('click', '.evotx_addtocart', function(event){

        event.preventDefault();

        var ajaxdata = {};

        // make sure the button is good to go
        if( $(this).data('green') != 'y') return;

        var BTN = $(this);
        var SECTION = BTN.closest('.evotx_ticket_purchase_section');
        
        ajaxdata['qty'] = SECTION.find('input[name="quantity"]').val(); 
        ajaxdata['nyp'] = SECTION.find('input[name="nyp"]').val(); 
        ajaxdata['action'] = 'evotx_add_to_cart';
        ajaxdata['event_data'] = SECTION.evotx_get_event_data();

        // pass other select data
        ajaxdata['other_data'] = SECTION.evotx_get_all_select_data();

        if( ajaxdata['qty'] === undefined && BTN.hasClass('si')) ajaxdata['qty'] = 1;

        // gather other input fields
            SECTION.find('input').each(function(){
                if( $(this).attr('name') === undefined) return;
                if( $(this).attr('name') == 'add-to-cart') return;
                ajaxdata[ $(this).attr('name') ] = $(this).val();
            });

        // check for quantity
        if( ajaxdata.qty== undefined || ajaxdata.qty=='' || ajaxdata.qty == 0){
            SECTION.evotx_show_msg({'status':'bad','msg':'t5'});
            return false;
        }

        $.ajax({
            beforeSend: function(){       
                SECTION.addClass( 'evoloading');    
            },                  
            url:    evotx_object.ajaxurl,
            data:   ajaxdata,  dataType:'json', type:  'POST',
            success:function(data){

                if( data.status == 'good'){                    

                    $('body').trigger('evotx_added_to_cart',[ data, SECTION, ajaxdata ]);
                    
                    SECTION.evotx_show_msg({'msg': data.msg});

                    // if need to be redirected to cart after adding
                        if(evotx_object.redirect_to_cart == 'cart'){
                            window.location.href = evotx_object.cart_url;
                        }else if( evotx_object.redirect_to_cart =='checkout'){
                            window.location.href = evotx_object.checkout_url;
                        }else{
                            $('body').trigger('evo_update_wc_cart');
                        }  
                }else{ 
                    SECTION.evotx_show_msg({'status':'bad','msg':data.msg });
                }     

            },complete:function(){ 
                SECTION.removeClass( 'evoloading');
            }
        });
    });

// name your price
    $(document).on('keypress',function(e){
        var obj = $(e.target);
        if(e.which == 13 &&  obj.hasClass('nyp')  ){
            e.preventDefault();
            return;
        } 
    });
    $('body').on('change','input.nyp',function(){
        
        var EVOROW = $(this).closest('.evorow');  

        var raw_new = $(this).val();

        $(this).parent().data('sp', raw_new);

        var new_price = Number(raw_new.replace(/[^0-9.-]+/g,""));
        //var new_price = parseFloat( raw_new );

        
        var min_nyp = parseFloat($(this).data('minnyp'));
        
        // min price higher than 0
        if( min_nyp > 0 ){
            // price is less than minimum
            if(new_price < min_nyp){
                tx_data = $(EVOROW).evotx_get_data();
                evotx_DATA = tx_data.t;

                EVOROW.evotx_show_msg({'status':'bad','msg':'t6','hide_hidables':false});
                EVOROW.find('.evotx_addtocart').data('green','n');
            // price is good
            }else{
                EVOROW.find('.evotx_addtocart').data('green','y');
                EVOROW.evotx_hide_msg();
                $('body').trigger('evotx_calculate_total', [ $(this).closest('.evotx_ticket_purchase_section') ]); 
            }  
        // minimum price less than 0          
        }else{
            $('body').trigger('evotx_calculate_total', [ $(this).closest('.evotx_ticket_purchase_section') ]);  
        }              
    });

// Show add to cart notification messages
    $.fn.evotx_show_msg = function(opt){
        var defs = {
            'msg':'', 
            'status':'good',
            'hide': false,
            'hide_hidables': true,
            'show_btn':true
        }
        var OO = $.extend({}, defs, opt);
        el = $(this);
        const TIX_SECTION = el.hasClass('evotx_ticket_purchase_section') ? 
            el : el.closest('.evotx_ticket_purchase_section');
        const msg_el = TIX_SECTION.find('.tx_wc_notic');
        var evotx_data = TIX_SECTION.evotx_get_data();
        
        var msg_data = evotx_data.msg_interaction;

        // button
            if( OO.show_btn && OO.status == 'good'){
                TIX_SECTION.find('.evotx_cart_actions').show();
            }else{
                TIX_SECTION.find('.evotx_cart_actions').hide();
            }

        // get message content
        var message = OO.msg;
        if( OO.msg in  evotx_data.t ) message = evotx_data.t[ OO.msg ];
        if( message == '' || message === undefined ) message = OO.status == 'good' ? evotx_data.t.t1 : evotx_data.t.t4;

        msg_el.html( "<p class='evotx_success_msg "+ OO.status +"'><b>"+ message +"!</b></p>").show();

        // hide the message
            if( msg_data.hide_after == true || OO.hide ){
                setTimeout(function(){
                    $(TIX_SECTION).find('.evotx_addtocart_msg').hide();
                }, 3000);
            }

        // redirecting
            if(msg_data.redirect != 'nonemore' && OO.hide_hidables){
                // hide only the hidable section
                $(TIX_SECTION).find('.evotx_hidable_section').hide(); 
            }

    }; 
    $.fn.evotx_hide_msg = function(){
        el = $(this);
        const TIX_SECTION = el.hasClass('evotx_ticket_purchase_section') ? 
            el : el.closest('.evotx_ticket_purchase_section');
        const msg_el = TIX_SECTION.find('.tx_wc_notic');

        msg_el.hide();

        if( el.hasClass('evorow')){
            el.closest('.evorow').find('.evotx_addtocart_msg').hide();
        }else{
            el.find('.evotx_addtocart_msg').hide();
        }     
    };

    $('body').on('evotx_ticket_msg', function(event, EVOROW, STATUS, bad_msg, hide_hidables){
        
        $(EVOROW).evotx_show_msg({
            'status': STATUS,
            'hide_hidables': hide_hidables,
            'msg': bad_msg
        });
        return;
    });
    $('body').on('evotx_ticket_msg_hide',function(event, EVOROW){
        $(EVOROW).evotx_hide_msg();
    });
    
// click add to cart for variable product
// OLD Method
    $('body').on('click','.evoAddToCart', function(e){

        e.preventDefault();
        thisButton = $(this);

        // loading animation
        thisButton.closest('.evoTX_wc').addClass('evoloading');

        // Initial
            TICKET_ROW = thisButton.closest('.evo_metarow_tix');
            PURCHASESEC = TICKET_ROW.find('.evoTX_wc');

        // set cart item additional data
            var ticket_row = thisButton.closest('.evo_metarow_tix');
            var event_id = ticket_row.attr('data-event_id');
            var ri = ticket_row.attr('data-ri');
            var lang = thisButton.data('l');
            var event_location = thisButton.closest('.evcal_eventcard').find('.evo_location_name').html();
           
            event_location = (event_location !== undefined && event_location != '' )? 
                encodeURIComponent(event_location):'';

            // passing location values
               location_str = event_location!= ''? '&eloc='+event_location: '';

            // pass lang
               lang_str = ( lang !== undefined)? '&lang='+lang:'';

            //console.log(event_location);
            
            // variable item
                if(thisButton.hasClass('variable_add_to_cart_button')){

                    var variation_form = thisButton.closest('form.variations_form'),
                        variations_table = variation_form.find('table.variations'),
                        singleVariation = variation_form.find('.single_variation p.stock');

                        // Stop processing is out of stock
                        if(singleVariation.hasClass('out-of-stock')){
                            return;
                        }

                    var product_id = parseInt(variation_form.attr('data-product_id'));
                    var variation_id = parseInt(variation_form.find('input[name=variation_id]').val());
                    var quantity = parseInt(variation_form.find('input[name=quantity]').val());

                    quantity = (quantity=== undefined || quantity == '' || isNaN(quantity)) ? 1: quantity;

                    values = variation_form.serialize();

                    var attributes ='';
                    variations_table.find('select').each(function(index){
                        attributes += '&'+ $(this).attr('name') +'='+ $(this).val();
                    });

                    // get data from the add to cart form
                    dataform = thisButton.closest('.variations_form').serializeArray();
                    var data_arg = dataform;

                    $.ajax({
                        type: 'POST',data: data_arg,
                        url: '?add-to-cart='+product_id+'&variation_id='+variation_id+attributes+'&quantity='+quantity +'&ri='+ri+'&eid='+event_id + location_str + lang_str,
                        beforeSend: function(){
                            $('body').trigger('adding_to_cart');
                        },
                        success: function(response, textStatus, jqXHR){

                            // Show success message
                            thisButton.evotx_show_msg();

                        }, complete: function(){
                            thisButton.closest('.evoTX_wc').removeClass('evoloading');

                            // if need to be redirected to cart after adding
                            if(evotx_object.redirect_to_cart == 'cart'){
                                window.location.href = evotx_object.cart_url;
                            }else if( evotx_object.redirect_to_cart =='checkout'){
                                window.location.href = evotx_object.checkout_url;
                            }else{
                                update_wc_cart();
                            }                        
                        }
                    }); 
                }
    
            // simple product add to cart method is deprecated since 1.8        
        return false;
    });

// Update mini cart content
    $('body').on('evo_update_wc_cart',function(){
        update_wc_cart();
    });
    function update_wc_cart(){
        var data = {
            action: 'evoTX_ajax_09'
        };
        $.ajax({
            type:'POST',url:evotx_object.ajaxurl,
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

// inquiry submissions
    $('body').on('click','.evotx_INQ_submit', function(event){
        event.preventDefault();

        const LB = $(this).closest('.evo_lightbox');
        var form = LB.find('.evotxINQ_form');

        //reset 
        	form.find('.evotxinq_field').removeClass('error');

        var data = { action: 'evotx_ajax_06' };
       
        // validate captcha
            var human = validate_human( form.find('input.captcha') );
    		if(!human){
    			form.find('input.captcha').addClass('error');
    			LB.evo_lightbox_show_msg({
                    'type': 'bad', 
                    'message':evotx_object.text['003'], 
                });
                return;
    		}

        // empty fields
            var error = false;
            form.find('input, textarea').each(function(index){
                if( $(this).val()==''){
                    error = true;
                    $(this).addClass('error');
                } 
                data[$(this).attr('name')] = $(this).val();
            });

            if( error ){
                LB.evo_lightbox_show_msg({
                    'type': 'bad', 
                    'message':evotx_object.text['002'], 
                });
                return;
            }
        
       
        // submit form
        LB.evo_admin_get_ajax({
            'lightbox_key':'evotx_inqure_form',
            'uid':'evotx_inqure_submit',
            'ajaxdata': data,
            'end':'client'
        });
    });

	// validate humans
		function validate_human(field){
			if(field==undefined){
				return true;
			}else{
				var numbers = ['11', '3', '6', '3', '8'];
				if(numbers[field.attr('data-cal')] == field.val() ){
					return true;
				}else{ return false;}
			}				
		}

// add to cart button from eventtop
     $('body').on('click','.evotx_add_to_cart em', function(){   });

    // hover over guests list icons
        $('body').on('mouseover','.evotx_whos_coming span', function(){
            name = $(this).attr('data-name');
            html = $(this).html();
            $(this).html(name).attr('data-intials', html).addClass('hover');
        });
        $('body').on('mouseout','.evotx_whos_coming span', function(){
            $(this).html( $(this).attr('data-intials')).removeClass('hover');
        });

// My account view ticket
    $('body').on('click','.evotx_view_ticket',function(){
        LIGHTBOX = $('.evotx_lightbox');
        LIGHTBOX.addClass('show');
        $('body').trigger('evolightbox_show');


        // get form html
        var ajaxdataa = {};
        
        ajaxdataa['action'] = 'evotx_my_account_ticket';
        ajaxdataa['tn'] = $(this).data('tn');
        $.ajax({
            beforeSend: function(){ 
                LIGHTBOX.find('.evo_lightbox_body').addClass('evoloading')
                    .html('<p class="loading_content"></p>');
            },                  
            url:    evotx_object.ajaxurl,
            data:   ajaxdataa,  dataType:'json', type:  'POST',
            success:function(data){
                LIGHTBOX.find('.evo_lightbox_body').html( data.html );                
            },complete:function(){ 
                LIGHTBOX.find('.evo_lightbox_body').removeClass('evoloading');
            }
        });
    });
	
// ActionUser event manager
    // show ticket stats for events
        $('#evoau_event_manager').on('click','a.load_tix_stats',function(event){
            event.preventDefault();
            MANAGER = $(this).closest('.evoau_manager');
            var data_arg = {
                action: 'evotx_ajax_get_auem_stats',
                eid: $(this).data('eid')
            };
            $.ajax({
                beforeSend: function(){
                    MANAGER.find('.eventon_actionuser_eventslist').addClass('evoloading');
                },
                type: 'POST',
                url:evotx_object.ajaxurl,
                data: data_arg,
                dataType:'json',
                success:function(data){
                    $('body').trigger('evoau_show_eventdata',[MANAGER, data.html, true]);
                },complete:function(){ 
                    MANAGER.find('.eventon_actionuser_eventslist').removeClass('evoloading');
                }
            });
        });

    // check in attendees
        $('body').on('click','.evotx_status', function(){
            var obj = $(this);
            if(obj.hasClass('refunded')) return false;
            if( obj.data('gc')== false) return false;
           
            var data_arg = {
                action: 'the_ajax_evotx_a5',
                tid: obj.data('tid'),
                tiid: obj.data('tiid'),
                status: obj.data('status'),
            };
            $.ajax({
                beforeSend: function(){    obj.html( obj.html()+'...' );  },
                type: 'POST',
                url:evotx_object.ajaxurl,
                data: data_arg,
                dataType:'json',
                success:function(data){
                    obj.data('status', data.new_status)
                    obj.html(data.new_status_lang).removeAttr('class').addClass('evotx_status '+ data.new_status);

                }
            });
        });
    // open incompleted orders
        $('.evoau_manager_event_content').on('click','span.evotx_incomplete_orders',function(){
            $(this).closest('table').find('td.hidden').toggleClass('bad');
        });
});