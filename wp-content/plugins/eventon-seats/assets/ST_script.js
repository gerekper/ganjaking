/**
 * Javascript: Seating charts for eventon
 * @version  1.2.1
 */
jQuery(document).ready(function($){	

	// trigger seat map loading
	$('body').on('evo_init_ajax_success',function(){
	
		// load event map on load for event
		if($('body').find('.evost_seat_map_section').length > 0){
		
			$('body').find('.evost_seat_map_section').each(function(){
				O = $(this);

				// if set to open as lightbox skip processing
				if( O.hasClass('evost_lb_on')) return;

				const tx_data = O.evotx_get_event_data();

				var eventcard = O.closest('.event_description');

				if( eventcard.hasClass('open') && tx_data.showmap ){
					$('body').trigger('evost_load_inline_map',[O]);		
				}				
			});
		}
	});

	// after event card open
	$('body').on('evo_slidedown_eventcard_complete',function(event, event_id, obj){

		var event_box = obj.closest('.eventon_list_event');			
		var click_item = event_box.find('.event_description');

		if( !click_item.hasClass('open')) return;
		
		$('body').trigger('evost_load_inline_map',[ event_box.find('.evo_metarow_tix') ]);

	});


	// resization				
		$('body').on('evost_after_map_drawn', function(){
			windowsization();
		});

		$(window).resize(function(){
			setTimeout(function(){
				windowsization();
			}, 500);
		});

		// after the map is drawn, resize and add interaction listeners
		function windowsization(){
	      	var win = $(window); //this = window
	      	EVOST_LB = $('.evost_lightbox .evo_lightbox_content');
	      	EVOST_LB_W = parseInt(EVOST_LB.data('w'))+25;

	      	if(EVOST_LB_W > win.width()){
	      		EVOST_LB.addClass('compact');
	      	}else{
	      		EVOST_LB.removeClass('compact');
	      	}

	      	$('body').find('.evost_inline_seat_map').each(function(){

	      		var M = $(this);

	      		if( M.find('.evost_seat_selection').length>0){

		      		LAYOUT = $(this).find('.evost_seat_layout');		      				      		
		      		LAYOUT.evostMapInteraction({type:'resize'});
		      	}

	      	});
		};

	// cart page expiration timer
		if( $('body').find('.evost_cart_timer').length == 1){
			E = $('body').find('.evost_cart_timer');
			E.evostTimer();

			// refresh cart when timer expired
			$('body').on('evost_seat_time_expired', function(){
				location.reload();
			});
		}

	// show lightbox seats
		$('body')
		.on('evo_ajax_beforesend_evost_load_lb_seat_map',function(event, OO, el){
			//LB = $('body').find('.evo_lightbox.'+ OO.lightbox_key);
			
		})
		// after LB seat data loaded
		.on('evo_ajax_success_evost_load_lb_seat_map',function(event, OO, data){
			LB = $('body').find('.evo_lightbox.'+ OO.lightbox_key);
			LB.find('.evolb_content').html( data.structure);
			LB.find('.evo_loading_bar_holder').remove();

			$('body').trigger('evo_ajax_success_evost_get_seat_data',[ OO, data]);	
		})

		// click on load LB seat map - from eventcard
		.on('click','.evost_show_lightbox_seats',function(){
			O = $(this);
			const event_data = O.evotx_get_event_data();
			var ajaxdataa = $.extend({}, {}, event_data);
			ajaxdataa['action']='evost_get_seats_data';
			ajaxdataa['type']='lb';

			$(this).evo_lightbox_open({
				'd':ajaxdataa,
				'ajax':'yes',
				'uid':'evost_load_lb_seat_map',
				'lbc':'evost_seat_map',
				'lightbox_loader':false,
				'load_new_content':false,
				'end':'client',
			});
		});

	// show inline seats
		$('body').on('evost_load_inline_map',function(event, O){
			O = $(O);
			ROW = O.closest('.evorow');
			SEC = O.closest('.evotx_ticket_purchase_section');

			const event_data = O.evotx_get_event_data();

			// bail if set to wait
			if( SEC.find('.evost_seat_map_section').hasClass('evost_wait') ) return;

			var ajaxdataa = $.extend({}, {}, event_data);
			ajaxdataa['action']='evost_get_seats_data';

			SEC.addClass("evo_runningajax");		

			$(this).evo_admin_get_ajax({
				'ajaxdata':ajaxdataa,
				'uid':'evost_get_seat_data',
				'end':'client',
			});	

		})
		.on('evo_ajax_success_evost_get_seat_data',function(event, OO, data){

			SEC = $('body').find('.evo_runningajax.evotx_ticket_purchase_section');
			ROW = SEC.closest('.evorow');

			if(data.status=='good'){		

				ROW.evotx_hide_loading();

				c = SEC.find('.evost_inline_seat_map');
				c.html( data.view );

				// append seat map json data to evost_data
				SEC.find('.evost_data').data('json', data.j);						
				SEC.find('.evost_data').data('j_cart', data.j_cart);

				SEC.find('.evost_data').data('settings', data.s);

				// draw the seat map
				__redraw_map( SEC, true);

				SEC.removeClass('evo_runningajax');
				SEC.removeClass('evoloading');
				//alert('donex');
				
			}else{}
		});

	
	// mobile accordion
	// @deprecated
		$('.evost_lightbox_body').on('click','u',function(){
			SELECTION = $(this).closest('.evost_seat_selection');

			if(SELECTION.data('accordion')=='no') return false;

			LIGHTBOX = $(this).closest('.evo_lightbox_content');
			if(!LIGHTBOX.hasClass('compact')) return false;

			LAYOUT = $(this).closest('.evost_seat_layout');
			LAYOUT.find('.evost_row').hide();
			PAR = $(this).parent();
			PAR.find('.evost_row').show();
		});

	// hover over a seat
		$.fn.evost_map_tooltip = function(opt){
			defaults = {
				'type':'seat'
			}
			var OO = $.extend({}, defaults, opt);

			if( _is_mobile()) return false;

			SECTION = $(this);	
			SEATSECTION = SECTION.closest('.evost_seat_selection');
			section_id = SECTION.data('id');

			lightbox = SEATSECTION.closest('.evo_lightbox').length? true: false;
			
			evostData = SEATSECTION.find('.evost_data');
			s = evostData.data('s'); // general data
			j = evostData.data('json');	// seat map data

			data = {};
			data['price'] = get_format_price( parseFloat( j[section_id]['def_price'] ) );
			data['type'] = OO.type + 'seat';
			data['section'] = SECTION.data('name');
			data['seat_slug'] = SECTION.data('id');
			data['available'] = j[section_id]['available'];
			if(SECTION.hasClass('av')) data['canbuy'] = true;

			TOOLTIP = SEATSECTION.find('.evost_tooltip');
			TOOLTIP.evostToolTip({
				temp_part: 'evost_tooltips',
				data: data
			});

			// position
			if(!TOOLTIP.hasClass('fixed')){

				HEI = TOOLTIP.height();				
				_par = SECTION.closest('.evost_seat_selection').offset();
				
				_position = 'absolute';
				TOP = SECTION.offset().top - _par.top - HEI -20;
				LEFT = SECTION.offset().left - _par.left - 60;				

				TOOLTIP.css({
					top: TOP, 
					left: LEFT,
					position: _position,
				}).stop(true, false);
				TOOLTIP.addClass('visible');
			}
		}

		// hide tooltips on mouse out
		$.fn.evost_map_tooltip_close = function(opt){
			//return;
			el = this;
			TOOLTIP = el.find('.evost_tooltip');			
			TOOLTIP.stop(true, false).removeClass('visible');
			if( TOOLTIP.hasClass('fixed')){
				TOOLTIP.html("<div class='evost_tt_content'>"+ evo_general_params.text.evost_1 +"</div>");
			}
		}

		// unassigned seating
		$('body').on('mouseover', 'span.evost_section.type_una',function(event){
			$(this).evost_map_tooltip({'type':'una'});			
		}).mouseout(function(){
			$(this).evost_map_tooltip_close();
		});
		
		// regular seat
		$('body').on('mouseover','span.evost_seat',function(event){

			if(_is_mobile()) return false;

			SEAT = $(this);			
			SEATSECTION = SEAT.closest('.evost_seat_selection');
			SECTION = SEAT.closest('.evost_section');
			BODY = $('body');

			seat_id = $(this).data('id');
			row_id = SEAT.closest('.evost_row').data('id');
			section_id = SEAT.closest('.evost_section').data('id');
			lightbox = SEATSECTION.closest('.evo_lightbox').length? true: false;

			// get json data
			evostData = SEATSECTION.find('.evost_data');
			s = evostData.data('s'); // general data
			j = evostData.data('json');	// seat map data
			
			data = {};
			data['type'] = 'seat';
			data['seat'] = $(this).data('number');
			data['row'] = j[section_id].rows[row_id].row_index;
			data['section'] = j[section_id].section_index;
			data['section_name'] = SECTION.data('name');
			if(SEAT.hasClass('av')) data['canbuy'] = true; // available for purchase
			if(SEAT.hasClass('hand')) data['hand'] = true; // handicap

			// price
				def_price = j[section_id]['def_price'];
				seat =  __get_seat(j, seat_id,row_id,section_id);
				if(seat && seat.price!== undefined) def_price = seat.price;
				data['price'] =  get_format_price( parseFloat(def_price) );
			
			TOOLTIP = SEATSECTION.find('.evost_tooltip');
			TOOLTIP.evostToolTip({
				temp_part: 'evost_tooltips',
				data: data
			});
	
			// POSTITION
			if(!TOOLTIP.hasClass('fixed')){

				HEI = TOOLTIP.height();				
				_par = SEAT.closest('.evost_seat_selection').offset();
				
				_position = 'absolute';
				TOP = SEAT.offset().top - _par.top - HEI -10;
				LEFT = SEAT.offset().left - _par.left - 60;		

				TOOLTIP.css({
					top: TOP, 
					left: LEFT,
					position: _position,
				}).stop(true, false);
				TOOLTIP.addClass('visible');
			}

		}).mouseout(function(){
			$(this).evost_map_tooltip_close();
		});

		// Booth Seating
		$('body').on('mouseover', 'span.evost_section.type_boo',function(event){
			$(this).evost_map_tooltip({'type':'boo'});
		}).mouseout(function(){
			$(this).evost_map_tooltip_close();
		});

	// cart expiration timer ran out on event page
		$('body').on('.evost_seat_time_expired',function(event,evotx_ticket_purchase_section){
			$(evotx_ticket_purchase_section).find('.evost_seats_in_cart').html('');
		});
	// refresh the seat map
		$('body').on('evost_refresh_map', function(event,evotx_ticket_purchase_section){
			O = $(evotx_ticket_purchase_section);
			s = O.find('.evost_data').data('s');

			// ajax data
			var ajaxdataa = {};
			ajaxdataa['action']		='evost_refresh_seat_map';
			ajaxdataa['eventid'] 	= s.event_id;
			ajaxdataa['wcid'] = s.wcid;
			$.ajax({
				beforeSend: function(){ O.addClass('evoloading');	},					
				url:	evost_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					O.find('.evost_data').data('json', data.j);						
					O.find('.evost_data').data('j_cart', data.j_cart);

					__redraw_map( O, true);
				},complete:function(){ O.removeClass('evoloading');	}
			});
		});

	// format ticket price
		var global_ticket_data = false;
		function get_format_price(price){

	        // price format data
	        PF = !global_ticket_data? $('body').find('.evotx_data').data('pf'):global_ticket_data;
	        global_ticket_data = PF;
	       
	        totalPrice = price.toFixed(PF.numDec); // number of decimals
	        htmlPrice = totalPrice.toString().replace('.', PF.decSep);

	        var currencySymbol = decodeURIComponent(PF.currencySymbol);

	        if(PF.thoSep.length > 0) {
	            htmlPrice = _addThousandSep(htmlPrice, PF.thoSep);
	        }
	        if(PF.curPos == 'right') {
	            htmlPrice = htmlPrice + currencySymbol;
	        }
	        else if(PF.curPos == 'right_space') {
	            htmlPrice = htmlPrice + ' ' + currencySymbol;
	        }
	        else if(PF.curPos == 'left_space') {
	            htmlPrice = currencySymbol + ' ' + htmlPrice;
	        }
	        else {
	            htmlPrice = currencySymbol + htmlPrice;
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

	// Add seat to view
		// Booth area
		$('body').on('click', 'span.evost_section.type_boo',function(event){
			__preview_seat( $(this), 'booseat', 'prev');
		});
		// unassigned area
		$('body').on('click', 'span.evost_section.type_una',function(event){
			__preview_seat( $(this), 'unaseat', 'prev');
		});
		// regular seat
		$('body').on('click','span.evost_seat',function(event){
			c = $(this).closest('.evost_seat_map_section').data('adds');
			__preview_seat( $(this), 'seat', c);
		});

		function __preview_seat( seat, type, method){
			SEAT = $(seat);			
			if(!SEAT.hasClass('av') ) return false;
			SECTION = _SECTION = SEAT.closest('.evost_seat_map_section');
			j = SECTION.find('.evost_data').data('s');
			EVOROW = SEAT.closest('.evorow');
			TIX_SECTION = SEAT.closest('.evotx_ticket_purchase_section');
			
			// ajax data
			var ajaxdataa = {};
			ajaxdataa['action']		= method=='cart'? 'evost_seat_direct_add_cart':'evost_seat_cart_preview';
			ajaxdataa['type'] 		= type;
			ajaxdataa['event_data']	= TIX_SECTION.evotx_get_event_data();

			// pass other select data
        	ajaxdataa['other_data'] = TIX_SECTION.evotx_get_all_select_data();

			if( type == 'seat'){
				ajaxdataa['seat_slug'] = 	SEAT.data('sid');
			}else{// una section
				ajaxdataa['seat_slug'] = 	SEAT.data('id');
			}
			
			SECTION.find('.evost_msg').removeClass('error');

			//return;
			
			$.ajax({
				beforeSend: function(){ SECTION.addClass('evoloading');	},					
				url:	evost_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){

					if(method == 'cart'){

						if(data.status == 'good'){
							tSECTION = SEAT.closest('.evotx_ticket_purchase_section');
							tEVOROW = SEAT.closest('.evorow');

							$('body').trigger('evotx_added_to_cart',[ data, tSECTION]);

                   			SEAT.evotx_show_msg({'status': 'good'});

						}else{
							SEAT.evotx_show_msg({'status': 'bad', 'msg': data.msg});
						}					

					}else{ // preview seats before cart addition


						SECTION.find('.evost_data').data('json', data.j);	
						SEC = SEAT.closest('.evotx_ticket_purchase_section');
						__redraw_map( SEC, true);

						if(data.status=='good'){
							
							SECTION.find('.evost_seats_preview').html( data.view ).show();
							
							// highlishgt selected seat
							if( type == 'seat') 
								_SECTION.find('.evost_seat[data-sid="'+ ajaxdataa.seat_slug +'"]').addClass('selected');

						}else{
							SECTION.find('.evost_msg').addClass('error').html(data.msg).show().delay(5000).fadeOut();
						}
					}
				},complete:function(){ 
					_SECTION.removeClass('evoloading');	
				}
			});
		}

		// cancel seat preview
		$('body').on('click','.evost_cancel_seat_preview',function(){
			var OBJ = $(this);
			const obj_data = OBJ.data('d');
			SECTION = $(this).closest('.evost_seat_map_section');
			SECTION.find('.evost_seats_preview').html('').hide();

			// put the seat back
			if( obj_data.type == 'seat'){
				SECTION.find('.evost_seat[data-sid="'+ obj_data.slug +'"]').removeClass('selected');
			}
		});

	// when seat ticket added to cart
		$('body').on('evotx_added_to_cart', function(event, data, section){
			// none seat add to cart
			if( !data.hasOwnProperty('j_cart')) return false;

			SECTION = $(section);
			SECTION.find('.evost_seats_preview').html('').hide();
			// un-highlight seat
			SECTION.find('.evost_seat').removeClass('selected');

			SECTION.find('.evost_data').data('json', data.j);
			SECTION.find('.evost_data').data('j_cart', data.j_cart);
			__redraw_map( SECTION, true);
		});

	// remove seat from cart
		$('body').on('click','span.evost_remove_tix',function(){
			OBJ = $(this);
			SECTION = OBJ.closest('.evost_seat_map_section');
			j = SECTION.find('.evost_data').data('s');

			var ajaxdataa = {};
			ajaxdataa['action']='evost_remove_seat_from_cart';
			ajaxdataa['event_data']= SECTION.evotx_get_event_data();
			ajaxdataa['key'] = OBJ.parent().attr('id');
			ajaxdataa['qty'] = OBJ.parent().data('qty');
			ajaxdataa['seat_slug'] = OBJ.parent().data('seat_slug');
			
			OBJ.evo_admin_get_ajax({
				'ajaxdata': ajaxdataa,
				'uid':'evost_remove_seat',
				'end':'client',
				'loader_el': SECTION
			});			
		})
		.on('evo_ajax_success_evost_remove_seat',function(event,OO, data, el ){
			if(data.status=='good'){						
				$('body').trigger('evost_refresh_map',[ 
					$(el).closest('.evotx_ticket_purchase_section')] );
				
				$( OO.loader_el ).evotx_show_msg({'msg': data.message, 'hide':7000 , 'show_btn':false});

			}else{
				// error notice ***
				$( OO.loader_el ).evotx_show_msg({'status':'bad','msg': data.message, 'hide':5000 });
			}
		});
	
	// redraw map function
		function __redraw_map( section, settings){

			var SECTION = $(section);
			var evost_data = SECTION.find('.evost_data').data();

			// validate
			if(!evost_data) return false;
			if( !evost_data.hasOwnProperty('json')) return false;


			SECTION.find('.evost_seat_layout').evostMapDrawer({
				json: evost_data.json,
				j_cart: evost_data.j_cart,
				temp_part: 'evost_seat_map',
				end: 'front'
			});

			// map settings
			if(settings){
				SECTION.find('.evost_seat_layout').evostMapSettings({
					json: evost_data.settings,
					temp_part: 'evost_seat_map',
					end:'front'
				});
			}

			// seats in cart
			SECTION.find('.evost_seats_in_cart').evostCartSeats({
				j_cart: evost_data.j_cart,
				temp_part: 'evost_cart_seats',
			});

			// map area height

			$('body').trigger('evost_after_map_drawn');
		}
	
	// Supportive
		function __hasVal(obj, key){
	        return obj.hasOwnProperty(key);
	    }
	    function __get_seat(j, seat, row, section){
	    	output = false;
	    	$.each(j, function(section_id, s){

	    		if( section_id != section) return true;

	    		$.each(s.rows, function(row_id, r){
	    			if( row != row_id) return true;

	    			$.each(r.seats, function(seat_id, sd){
	    				if( seat != seat_id) return true;
	    				output = sd;
	    			});
	    		});
	    	});
	    	return output;
	    }

	    function _is_mobile(){
	    	var isMobile = false; //initiate as false
			// device detection
			if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
			    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
			    isMobile = true;
			}

			return isMobile;
	    }


});