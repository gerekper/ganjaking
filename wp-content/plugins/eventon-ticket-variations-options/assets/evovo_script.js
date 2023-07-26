/**
 * frontend script 
 * @version 1.0.4
 */
jQuery(document).ready(function($){

   
// change in variations
    // run this on load
    setTimeout(function(){
        $('body').find('.evovo_variation_types').each(function(){
            $(this).find('select').trigger('change');
        });
    },200);
    
    $('body').on('change','.evovo_variation_types select', function (){
        SECTION = $(this).closest('.evotx_ticket_purchase_section');
        
        rDATA = SECTION.evotx_get_data();

        evovo_data = SECTION.evotx_get_custom_data('evovo_data');
        evovo_data = evovo_data.evovo_data;
        EVOROW = $(this).closest('.evorow');  

        //console.log(evovo_data);

        DATA_var = all_variations = evovo_data.v;// all variations

        new_variation_id = false;
        new_variation_price = evovo_data.defp;
        new_variation_max_qty = 'na';
        new_variation_data = '';

        // reset hidables
            SECTION.find('.evotx_hidable_section').show(); 


        evovo_data['vart'] = {};
        selected_options = {};
        selected_options['var_ids'] = {};

        SECTION.find('.evovo_variation_types select').each(function(){
            evovo_data.vart[ $(this).attr('name')] = $(this).val();
        });

        const selected_vars = evovo_data.vart;

         
        // each variation type   
            _m_var_id = false;
            _m_var_id_all = false;


            // each selected variation type
            $.each( selected_vars , function ( s_vtid, s_vval){
                $.each(all_variations, function(var_id, data){
                    $.each( data.variations , function( vtid, vval){
                        // save variation type as selected
                        if( s_vtid ==  vtid ){
                            if( s_vval ==  vval ){
                               _m_var_id = var_id; return;
                            }
                        }
                        // load variation type ALL value
                        if( vval == 'All') _m_var_id_all = var_id;
                    });

                    if( _m_var_id ) return;
                });
                if( _m_var_id ){
                    return;
                }else{
                    _m_var_id = _m_var_id_all; return;
                }
            });
            
            /*
            // each variation
            $.each(all_variations, function(var_id, data){
                var_types_match = true;

                $.each( data.variations , function( vtid, vval){

                    
                        if(vtid ==  s_vtid)
                    });

                });


                console.log(data.stock_status);

               // each selected variation type
                $.each( selected_vars , function(vt_id, vtv){
                    // variation type value for this variation
                    vtv_v = data.variations[vt_id];

                   
                    // vtv == 'All' 
                    if( (vtv == vtv_v) || ( vtv != vtv_v && (vtv_v == 'All' ) )  ){

                        if( vtv_v == 'All'  ){
                            _m_var_id_all = var_id;
                        }else{
                            _m_var_id = var_id;
                        }   
                        
                    }else{
                        _m_var_id = false;
                        return false;
                    }
                });

                if( _m_var_id != false ) return false;
            });


            if( _m_var_id != false && _m_var_id_all != false){
                _m_var_id = _m_var_id_all;
            }
            */

          
            if(_m_var_id != false){
                //console.log(DATA_var[_m_var_id]);
                new_variation_price = DATA_var[_m_var_id].regular_price;
                new_variation_id = _m_var_id;
                new_variation_max_qty = parseInt(DATA_var[_m_var_id].stock);
                new_variation_data = DATA_var[_m_var_id];
            }


        // if there is no matching variation
            if(!new_variation_id){
                SECTION.find('.evotx_add_to_cart_bottom').addClass('outofstock');
                SECTION.find('.evovo_price_options').hide();
                EVOROW.evotx_show_msg({'status':'bad','msg':'tvo2','hide_hidables':false});
                return false;
            }

        // set new variation id
            selected_options['var_ids'][new_variation_id] = 1;
            selected_options['vart'] = evovo_data['vart'];

        // check for stock status
            Current_outofstock = false;
            if(new_variation_data && new_variation_data.stock_status=='outofstock'){Current_outofstock=true;}
            if(new_variation_data && new_variation_data.stock=='0'){Current_outofstock=true;}

            if(Current_outofstock){
                SECTION.find('.evotx_add_to_cart_bottom').addClass('outofstock');
                SECTION.find('.evovo_price_options').hide();
                EVOROW.evotx_show_msg({'status':'bad','msg':'tvo3','hide_hidables':false});
            }else{
                SECTION.find('.evotx_add_to_cart_bottom').removeClass('outofstock').show();
                SECTION.find('.evovo_price_options').show();                
                SECTION.evotx_hide_msg();
            }
               
        formatted_price = get_format_price( parseFloat(new_variation_price), SECTION);

        // set new values
        SECTION.evotx_set_select_data( 'evovo', selected_options );
        SECTION.evotx_set_custom_data('evovo_data', evovo_data);
        SECTION.find('.tx_price_line .value').html(formatted_price).data('sp',new_variation_price );

        // update remaining stock if enabled
        if( rDATA.event_data && rDATA.event_data.showRem && new_variation_max_qty != 'na'){
            SECTION_remaining = SECTION.find('.evotx_remaining');
            SECTION_remaining.find('span span').html(new_variation_max_qty);
            
            if( rDATA.event_data.showRem !== false){
                SECTION_remaining.show();
            }else{
                SECTION_remaining.hide();
            }            
        }

        // stock quantity modifitcations
        QTY_SECTION = SECTION.find('.evotx_quantity');
        QTY_SECTION.find('input').data('max', new_variation_max_qty);
        Set_stock_val = QTY_SECTION.find('input').val();

        // if current qty is greater than max stock
        if(new_variation_max_qty != 'na' && Set_stock_val > new_variation_max_qty){
            QTY_SECTION.find('input').val(new_variation_max_qty);
            QTY_SECTION.find('em').html(new_variation_max_qty);
            
        }else if(Set_stock_val < new_variation_max_qty){// new max is higher than old qty
            QTY_SECTION.find('b.plu').removeClass('reached');
        }
        
        $('body').trigger('evotx_calculate_total', [SECTION]);     
        
    });

// Individual Variations
    $('body').on('click','.evovo_var_types_ind .evovo_addremove',function(){
        calculate_var_price( $(this) );         
    }); 

    function calculate_var_price(SPAN){
        SPAN = $(SPAN);
        P = SPAN.closest('p');
        SECTION = SPAN.closest('.evotx_ticket_purchase_section');
        evovo_data = SECTION.evotx_get_custom_data('evovo_data');
        DATA = evovo_data.evovo_data;
        DATA_vt = DATA['v'];

        DATA['prices'] = SECTION.find('.price.tx_price_line input').data('prices');
        if( DATA['prices'] == '' ) DATA['prices'] = {};

        var total_var_price = 0;

        selected_options = {};
        selected_options['var_ids'] = {};

        // all selected variation types
        SECTION.find('.evovo_var_types_ind').each(function(){
            QTY = $(this).find('input').val(); 
            vid = $(this).data('vid');
            DATA.prices[ vid ] = {};

            if( QTY == '0' || QTY === undefined) return true; 
            
            selected_options['var_ids'][vid] = QTY;

            DATA.prices[ vid ]['price'] = DATA_vt[vid].regular_price;
            DATA.prices[ vid ]['qty'] = QTY;
            DATA.prices[ vid ]['variations'] = DATA_vt[vid]['variations'];
            DATA.prices[ vid ]['type'] = 'ind_variation';

            total_var_price += DATA_vt[vid].regular_price * QTY;

        });

        // console.log(DATA);
        SECTION.evotx_set_select_data( 'evovo', selected_options );
        SECTION.evotx_set_custom_data('evovo_data', DATA);

        SECTION.find('.price.tx_price_line span.value').html( get_format_price(total_var_price, SECTION) );
        SECTION.find('.price.tx_price_line input').data('prices', DATA.prices );   

        $('body').trigger('evotx_calculate_total', [SECTION]);  

    }

// maximum quantity override
    $('body').on('evotx_before_qty_changed',function(event, MAX, OBJ){
        return;
    });

// Price options
    $('body').on('click','.evovo_price_option .evovo_addremove',function(){
        if(!$(this).hasClass('evotx_qty_change')) calculate_price_options( $(this) );         
    }); 

    $('body').on('evotx_qty_changed', function(event, NEWQTY, MAX, OBJ){
        calculate_price_options( OBJ );
    });
    
    function calculate_price_options(SPAN){
        // check if this is multiple of single
        SPAN = $(SPAN);

        // stop if its not evovo quantity changer
        if(!SPAN.hasClass('evovo_addremove')) return false;

        MULT = ( SPAN.hasClass('evotx_qty_change'))? true: false;

        P = SPAN.closest('p');
        SECTION = SPAN.closest('.evotx_ticket_purchase_section');
        pOptions = SECTION.find('.evovo_price_options');
        evovo_data = SECTION.evotx_get_custom_data('evovo_data');
        DATA = evovo_data.evovo_data;
        DATA_po = DATA.po;

        // if PO uncorrelated tix qty
        uncor_qty = evovo_data.po_uncor_qty == 'yes'? true:false;

        DATA['prices'] = SECTION.find('.price.tx_price_line input').data('prices');
        if( DATA['prices'] == '' ) DATA['prices'] = {};
        QTY = 0;

        // add or remove
        if( !MULT){
            if(P.hasClass('add')){
                P.removeClass('add').addClass('added');
                P.find('input').val('1');
            }else{
                P.removeClass('added').addClass('add');
                P.find('input').val('0');
            }
        }
        HTML = HTML_extra = '';
        selected_options = {};
        selected_options['options'] = {};

        // all selected price options
        if( pOptions.find('p.evovo_price_option').length > 0){
            pOptions.find('p.evovo_price_option').each(function(index){

                if( $(this).hasClass('soldout') ) return;

                pMULT = $(this).hasClass('mult')? true: false;
                po_id = $(this).data('poid');
                if( po_id === undefined) return;

                DATA.prices[ po_id ] = {};

                QTY = parseInt($(this).find('input').val()); 
                 
                if( QTY < 1) return;  

                // pass POs to other_data
                selected_options['options'][ po_id ] = QTY;

                DATA.prices[ po_id ]['type'] = 'price_option';// identify this price option add
                DATA.prices[ po_id ]['price'] = DATA_po[po_id].regular_price;
                DATA.prices[ po_id ]['qty'] = QTY;
                DATA.prices[ po_id ]['uncor'] = uncor_qty; // uncorrelated to tix qty
                DATA.prices[ po_id ]['pt'] = ('pricing_type' in DATA_po[po_id]) ? DATA_po[po_id].pricing_type:'include';

                total_price =  DATA_po[po_id].regular_price * QTY;

                formatted_total_price = get_format_price( total_price, SECTION);

                code = "<p class='evotx_item_price_line'><span class='evotx_label'>"+ DATA_po[po_id].name + "<em>"+ (QTY>1? 'x'+QTY:'') +"</em></span><span class='value'>" + formatted_total_price + "</span></p>";
            
                ( DATA.prices[ po_id ]['pt'] == 'extra') ? HTML_extra += code : HTML += code;
            });

            // set the selected price options data
            SPAN.evotx_set_select_data( 'evovo', selected_options );
        }

        //console.log(DATA);

        SECTION.find('.evovo_price_option_prices_container').html(HTML);
        SECTION.find('.evovo_price_option_prices_container_extra').html(HTML_extra);
        SECTION.evotx_set_custom_data('evovo_data', DATA);        
        SECTION.find('.price.tx_price_line input').data('prices',DATA.prices);   

        $('body').trigger('evotx_calculate_total', [SECTION]);    
    }

// FYI: add to cart is using tickets addon script

// GET format the price
    function get_format_price(price, SECTION){

        // price format data
        PF = SECTION.find('.evotx_data').data('pf');
       
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


// ADDONS -> Booking
    $('body').on('evobo_block_prices_loaded', function(event,eventRow,  data, ajaxdataa){
        
        $(eventRow).find('.evovo_variation_types select').trigger('change');

        return;                

        
    });

});