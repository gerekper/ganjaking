/**
 * frontend script 
 * @version 0.1
 */
jQuery(document).ready(function($){

   
// change in variations
    $('body').on('change','.evovo_variation_types select', function (){
        SECTION = $(this).closest('.evotx_ticket_purchase_section');
        
        rDATA = SECTION.find('.evotx_data').data();

        evovo_data = rDATA.evovo_data;
        EVOROW = $(this).closest('.evorow');  

        DATA_var = evovo_data.v;

        new_variation_id = false;
        new_variation_price = evovo_data.defp;
        new_variation_max_qty = 'na';
        new_variation_data = '';

        //console.log(DATA_var);

        evovo_data['vart'] = {};

        SECTION.find('.evovo_variation_types select').each(function(){
            evovo_data.vart[ $(this).attr('name')] = $(this).val();
        });

        //console.log(evovo_data);
        
        // each variation type   
            _m_var_id = false;
           
            $.each(DATA_var, function(var_id, data){
                var_types_match = true;

               // each variation type
                $.each(evovo_data.vart, function(vt_id, vtv){
                    vtv_v = data.variations[vt_id];// variation type value for this variation
                    
                    // vtv == 'All' 
                    if( (vtv == vtv_v) || ( vtv != vtv_v && (vtv_v == 'All' ) )
                    ){
                        _m_var_id = var_id;
                    }else{
                        _m_var_id = false;
                        return false;
                    }
                });

                if( _m_var_id != false ) return false;
            });

          
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
                $('body').trigger('evotx_ticket_msg',[EVOROW,'bad','tvo2']);
                return false;
            }

        // set new variation id
            evovo_data['var_id'] = new_variation_id;

        // check for stock status
            Current_outofstock = false;
            if(new_variation_data && new_variation_data.stock_status=='outofstock'){Current_outofstock=true;}
            if(new_variation_data && new_variation_data.stock=='0'){Current_outofstock=true;}

            if(Current_outofstock){
                SECTION.find('.evotx_add_to_cart_bottom').addClass('outofstock');
                SECTION.find('.evovo_price_options').hide();
                $('body').trigger('evotx_ticket_msg',[EVOROW,'bad','tvo1']);
            }else{
                SECTION.find('.evotx_add_to_cart_bottom').removeClass('outofstock').show();
                SECTION.find('.evovo_price_options').show();
                $('body').trigger('evotx_ticket_msg_hide',[EVOROW]);
            }
       
        
        formatted_price = get_format_price( parseFloat(new_variation_price), SECTION);

        SECTION.find('.evotx_data').data('evovo_data', evovo_data);
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
        DATA = SECTION.find('.evotx_data').data('evovo_data');
        DATA_po = DATA.po;

        DATA['pot'] = {};
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
        // all selected price options
        if( pOptions.find('p.evovo_price_option').length > 0){
            pOptions.find('p.evovo_price_option').each(function(){

                pMULT = $(this).hasClass('mult')? true: false;
                po_id = $(this).data('poid');
                if( po_id === undefined) return true;

                QTY = $(this).find('input').val();  
                 
                if( QTY == '0') return true;         

                DATA.pot[ po_id ] = {};
                DATA.pot[ po_id ]['price'] = DATA_po[po_id].regular_price;
                DATA.pot[ po_id ]['qty'] = QTY;
                DATA.pot[ po_id ]['pt'] = ('pricing_type' in DATA_po[po_id]) ? DATA_po[po_id].pricing_type:'include';

                total_price = DATA_po[po_id].regular_price * QTY;
                formatted_total_price = get_format_price( total_price, SECTION);

                code = "<p class='evotx_item_price_line'><span class='evotx_label'>"+ DATA_po[po_id].name + "<em>"+ (QTY>1? 'x'+QTY:'') +"</em></span><span class='value'>" + formatted_total_price + "</span></p>";
            
                ( DATA.pot[ po_id ]['pt'] == 'extra') ? HTML_extra += code : HTML += code;
            });
        }

        SECTION.find('.evovo_price_option_prices_container').html(HTML);
        SECTION.find('.evovo_price_option_prices_container_extra').html(HTML_extra);
        SECTION.find('.evotx_data').data('evovo_data',DATA);        
        SECTION.find('.price.tx_price_line input').data('prices',DATA.pot);   

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


});