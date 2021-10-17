/**
 * Javascript: Seating charts for eventon
 * @version  0.1
 */
jQuery(document).ready(function($){	

	$('body').on('evo_init_ajax_success',function(){
	// load event map on load for event

		if($('body').find('.evost_seat_map_section').length > 0){

			// check if set to open as lightbox			
			$('body').find('.evost_seat_map_section').each(function(){
				O = $(this);
				var eventcard = O.closest('.event_description');
				if(O.data('showmap') && eventcard.hasClass('open')){
					$('body').trigger('evost_load_inline_map',[O]);		
					O.find('.evost_show_inline_seats').hide();		
				}				
			});
		}
	});

	$('body').on('click','.evost_show_inline_seats',function(){
		O = $(this);
		$('body').trigger('evost_load_inline_map',[O]);
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
		      		LAYOUT.evostMapInteraction();
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

	// show inline seats
		$('body').on('evost_load_inline_map',function(event, O){
			O = $(O);
			ROW = O.closest('.evorow');
			SEC = O.closest('.evotx_ticket_purchase_section');
			var ajaxdataa = {};
			ajaxdataa['action']='evost_get_seats_data';
			ajaxdataa['event_id'] = ROW.data('event_id');
			ajaxdataa['wcid'] = ROW.data('wcid');		

			$.ajax({
				beforeSend: function(){ ROW.addClass('evoloading');	},					
				url:	evost_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if(data.status=='good'){						
						c = SEC.find('.evost_inline_seat_map');
						c.html( data.view );

						// append seat map json data to evost_data
						ROW.find('.evost_data').data('json', data.j);						
						ROW.find('.evost_data').data('j_cart', data.j_cart);

						ROW.find('.evost_data').data('settings', data.s);

						// draw the seat map
						__redraw_map( SEC, true);

						ROW.find('.evost_show_inline_seats').hide();
						//ROW.removeClass('evoloading');
						//alert('donex');
						
					}else{}
				},complete:function(){ ROW.removeClass('evoloading');	}
			});

		});


	// Show Seats lightbox 
	// @depre
		$('body').on('click','.evost_show_seat_selection',function(){
			OBJ = $(this);

			var ajaxdataa = {};
			ajaxdataa['action']='evost_get_seat_layout';
			ajaxdataa['eventid'] = OBJ.data('eventid');
			ajaxdataa['pid'] = OBJ.data('pid');
			ROW = OBJ.closest('.evorow');
			
			$.ajax({
				beforeSend: function(){ ROW.addClass('evoloading');	},					
				url:	evost_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if(data.status=='good'){
						
						if(data.classnames)
							$('.evost_lightbox').find('.evo_lightbox_content').width(data.width);
							$('.evost_lightbox').find('.evo_lightbox_content').attr({
								'class':'evo_lightbox_content '+data.classnames,
								'data-w':data.width
							});
						$('.evost_lightbox').find('.evo_lightbox_body').html( data.content );
						
						windowsization();

						$('.evost_lightbox.evo_lightbox').addClass('show');
						$('body').trigger('evolightbox_show');
					}else{
						// error notice ***
					}
				},complete:function(){ ROW.removeClass('evoloading');	}
			});
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
		// unassigned seating
		$('body').on('mouseover', 'span.evost_section.type_una',function(event){

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
			data['type'] = 'unaseat';
			data['section'] = SECTION.data('name');
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
		}).mouseout(function(){
			TOOLTIP = $(this).find('.evost_tooltip');			
			TOOLTIP.stop(true, false).removeClass('visible');
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
				TOP = SEAT.offset().top - _par.top - HEI -20;
				LEFT = SEAT.offset().left - _par.left - 60;		

				TOOLTIP.css({
					top: TOP, 
					left: LEFT,
					position: _position,
				}).stop(true, false);
				TOOLTIP.addClass('visible');
			}

		}).mouseout(function(){
			TOOLTIP = $(this).find('.evost_tooltip');			
			TOOLTIP.stop(true, false).removeClass('visible');
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
					ROW.find('.evost_data').data('json', data.j);						
					ROW.find('.evost_data').data('j_cart', data.j_cart);
					
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
			
			// ajax data
			var ajaxdataa = {};
			ajaxdataa['action']		= method=='cart'? 'evost_seat_direct_add_cart':'evost_seat_cart_preview';
			ajaxdataa['eventid'] 	= j.event_id;
			ajaxdataa['ri'] 		= j.ri;
			ajaxdataa['product_id'] = j.wcid;
			ajaxdataa['type'] 		= type;
			ajaxdataa['data'] = EVOROW.find('.evotx_data').data();

			if( type == 'seat'){
				ajaxdataa['seat_slug'] = 	SEAT.data('sid');
			}else{// una section
				ajaxdataa['seat_slug'] = 	SEAT.data('id');
			}
			
			SECTION.find('.evost_msg').removeClass('error');
			
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
                   			$('body').trigger('evotx_ticket_msg',[tEVOROW,'good']);
						}else{
							$('body').trigger('evotx_ticket_msg', [tEVOROW,'bad', data.msg]);
						}					

					}else{ // preview seats before cart addition

						// repopulate evotx_data values
						$('body').trigger('evotx_repopulate_evotx_data',[EVOROW, data.event_data]);

						SECTION.find('.evost_data').data('json', data.j);	
						SEC = SEAT.closest('.evotx_ticket_purchase_section');
						__redraw_map( SEC, true);

						if(data.status=='good'){
							
							SECTION.find('.evost_seats_preview').html( data.view );
							
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

	// when seat ticket added to cart
		$('body').on('evotx_added_to_cart', function(event, data, section){
			// none seat add to cart
			if( !data.hasOwnProperty('j_cart')) return false;

			SECTION = $(section);
			SECTION.find('.evost_seats_preview').html('');
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
			ajaxdataa['eventid'] = j.event_id;
			ajaxdataa['product_id'] = j.wcid;
			ajaxdataa['key'] = OBJ.parent().attr('id');
			ajaxdataa['qty'] = OBJ.parent().data('qty');
			ajaxdataa['seat_slug'] = OBJ.parent().data('seat_slug');
			SECTION.find('.evost_msg').removeClass('error');
			
			$.ajax({
				beforeSend: function(){ SECTION.addClass('evoloading');	},					
				url:	evost_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if(data.status=='good'){
						
						$('body').trigger('evost_refresh_map',[ OBJ.closest('.evotx_ticket_purchase_section')] );
						SECTION.find('.evost_msg').html(data.message).show().delay(5000).fadeOut();
					}else{
						// error notice ***
						//$('.evost_lightbox').find('.evost_seat_layout').html( data.seat_map );
						SECTION.find('.evost_msg').addClass('error').html(data.message).show().delay(5000).fadeOut();
					}
				},complete:function(){ SECTION.removeClass('evoloading');	}
			});
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