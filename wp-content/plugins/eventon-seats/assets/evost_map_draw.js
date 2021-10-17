/** 
 * EVO ST map drawing method
 * @version 1.0
 */

(function($){
	var evostTM;

	// Seat Map Settings
		$.fn.evostMapSettings = function(opt){
			j = opt.json;
			c = this;
			end = opt.end;

			styles = '';

			// background color 
			if(j.bg_color !== undefined) c.css('background-color', '#'+j.bg_color);

			// map area
			if(j.map_area !== undefined){
				c.removeClass(function (index, className) {
				    return (className.match (/(^|\s)map_area\S+/g) || []).join(' ');
				});
				c.addClass('map_area'+j.map_area);

				lb = c.closest('.evost_seating_map');
				if(lb.length>0){
					lb.removeClass(function (index, className) {
					    return (className.match (/(^|\s)map_area\S+/g) || []).join(' ');
					});
					lb.addClass('map_area'+j.map_area);
				}
			}

			// seat size
			if(j.seat_size !== undefined){
				c.removeClass(function (index, className) {
				    return (className.match (/(^|\s)seat_size\S+/g) || []).join(' ');
				});
				c.addClass('seat_size'+j.seat_size);
			}

			// seat color
			if(j.seat_color !== undefined){
				styles += ".evost_section .evost_row .evost_seat.av{background-color:#"+j.seat_color+';}';
			}

			c.parent().parent().find('.evost_seat_map_styles').html( styles );

			// background image
			if( j.bg_url !== undefined && j.bg_url){
				c.css('background-image', 'url('+j.bg_url+')');
			}

			// tooltip design
			if(j && 'tooltip' in j && j.tooltip !== undefined && j.tooltip=='yes'){
				c.closest('.evost_seat_selection').addClass('fixed_tt');
				c.parent().siblings('.evost_tooltip').addClass('fixed');
			}
		}

	// tooltip
		$.fn.evostToolTip = function(opt){
			temp_part = opt.temp_part;
			data = opt.data;
			c = $(this);

			HTML = $(this).evo_HB_process_template({TD: data ,part: temp_part});

			c.html( HTML );	
		}

	// seats in cart
		$.fn.evostCartSeats = function(opt){
			temp_part = opt.temp_part;
			data = opt.j_cart;
			c = $(this);

			HTML = '';		
			HTML = $(this).evo_HB_process_template({TD: data ,part: temp_part});

			c.html( HTML );	

			// expiration time				
				E = c.find('.evost_cart_expirations span');
				es = E.data('s');

				if(es){
					//console.log('tt');
					clearInterval(evostTM);
					E.evostTimer();
				}
			
		}	

	// count down timer
		$.fn.evostTimer = function(opt){
			var c = $(this);

			if(!c) return;

			evostTM = setInterval(function(){

				new_s = parseInt(c.data('s')) -1;

				// timer ran out
				if(new_s <= 0){
					clearInterval(evostTM);

					$('body').trigger('evost_seat_time_expired', [ c.closest('.evotx_ticket_purchase_section')]);
					$('body').trigger('evost_refresh_map', [ c.closest('.evotx_ticket_purchase_section')] );
				}else{
					hours = Math.floor(new_s/3600);
					new_s %= 3600;
					minutes = Math.floor((new_s / 60));
						minutes = minutes<10? '0'+minutes:minutes;
					seconds = new_s %60;
						seconds = seconds<10? '0'+seconds:seconds;

					c.data('s', new_s);
					c.find('b').html( minutes +':'+seconds);
				}

			},1000);
		}

	// Map interaction
		$.fn.evostMapInteraction = function(opt){

			// default values
			defaults = {
				increment: 0.1,
				minScale:1
			}
			var opt = $.extend({}, defaults, opt);
			clicking = false;
			clickedX = 0;
			clickedY = 0;
			MscaleXY = 1;
			MtranslateX = MtranslateY = 0;
			
			l = opt.l = $(this);
			control = l.parent().siblings('.evost_map_information').find('.evost_view_control');
			zI = control.find('.zoomin');
			zO = control.find('.zoomout');


			// initial size adjustment
				OUT = l.parent();
				MAPSEC = l.closest('.evost_inline_seat_map');

				// set width small to get actual space available 
				OUT.width(200);
				OUT.width( MAPSEC.width()-3 );

				MscaleXY = (OUT.width() >l.width()) ? 1: ( OUT.width()/ l.width() ) ;
				
				l.css('transform','matrix('+MscaleXY+',0,0,'+MscaleXY+','+MtranslateX+','+MtranslateY+')');
				l.css('transform-origin', '50% 50% 0');

				opt.minScale = (OUT.width() / l.width() )-0.5;

				MtranslateX = l.position().left *-1;
				l.css('transform','matrix('+MscaleXY+',0,0,'+MscaleXY+','+MtranslateX+','+MtranslateY+')');
				
			// window resized
				$(window).resize(function(){					

					// l width is same just scaled via matrix
					MscaleXY = (OUT.width() >l.width()) ? 1: ( OUT.width()/ l.width() ) ;	
					MtranslateX = ((l.width() - parseInt(l.width()* MscaleXY))/2 ) *-1;
					
					l.css('transform','matrix('+MscaleXY+',0,0,'+MscaleXY+','+MtranslateX+','+MtranslateY+')');

				}); 
			// panning				
				l.mousedown(function(e){
					clicking = true;
					clickedX = e.clientX;
					clickedY = e.clientY;
					_cal_matrix();
				});

				$('body').mouseup(function(e){
					clicking = false;
					_cal_matrix();// save new map locations					
				});

				l.mousemove(function(e){
					if(clicking){
						e.preventDefault();

						var O = $(this);
						offset = O.offset();
						
						IntTranslateX = offset.left;
						translateX =  MtranslateX + (e.clientX - clickedX);

						IntTranslateY = offset.top;
						translateY = MtranslateY+ e.clientY - clickedY;

						O.css('transform','matrix('+MscaleXY+',0,0,'+MscaleXY+','+translateX+','+translateY+')');
					}
				});

				// mobile
					l.bind('touchstart',function(e){
						clicking = true;
						clickedX = (e.originalEvent.touches[0].pageX);
						clickedY = (e.originalEvent.touches[0].pageY);
						_cal_matrix();
					});
					$('body').bind('touchend',function(e){
						clicking = false;
						_cal_matrix();// save new map locations
						
					});
					l.bind('touchmove',function(e){
						if(clicking){
							e.preventDefault();

							var off = l.offset();

							mouseX = (e.originalEvent.touches[0].pageX);
							mouseY = (e.originalEvent.touches[0].pageY);
							
							IntTranslateX =  (off !== undefined && 'left' in off) ? off.left:0;
							translateX =  MtranslateX + (mouseX - clickedX);

							IntTranslateY = (off !== undefined && 'top' in off) ? off.top: 0;
							translateY = MtranslateY+ mouseY - clickedY;

							l.css('transform','matrix('+MscaleXY+',0,0,'+MscaleXY+','+translateX+','+translateY+')');
						}
					});

			// zooming
				zI.on('click',function(){
					MscaleXY = MscaleXY + opt.increment;
					opt.l.css('transform','matrix('+MscaleXY+',0,0,'+MscaleXY+','+MtranslateX+','+MtranslateY+')');
					//console.log(opt.increment+' '+MscaleXY);

				});
				zO.on('click',function(){
					MscaleXY = MscaleXY - opt.increment;

					// min max
						if(MscaleXY < opt.minScale) MscaleXY = opt.minScale;

					opt.l.css('transform','matrix('+MscaleXY+',0,0,'+MscaleXY+','+MtranslateX+','+MtranslateY+')');
				});

				//console.log(opt.increment);

			// supportive
				function _cal_matrix(){
					if( opt.l.css === undefined ) return false;
					if( opt.l.css('transform') === undefined ) return false;

					transform = opt.l.css('transform');
					if( transform === undefined ) return false;
					
					matrix = transform.split(',');

					if( matrix[0] == 'none'){}else{
						MscaleX = matrix[0].split('(');
							MscaleX = parseFloat(MscaleX[1]);
						MscaleY = parseFloat(matrix[3]);
						MtranslateX = parseInt(matrix[4]);
						if( matrix[5] !== undefined) MtranslateY = matrix[5].split(')');
							MtranslateY = parseInt(MtranslateY[0]);					
					}
				}

		}

	// Seats Map
		$.fn.evostMapDrawer = function(opt){
			defaults = {}
			var opt = $.extend({}, defaults, opt);
			
			c = $(this);

			j = opt.json;
			//incart = opt.incart;// array of seats in cart for this event
			section_id = opt.section_id;
			classes = opt.classes;
			temp_part = opt.temp_part;
			temp = opt.temp;
			end = opt.end; // backend or frontend

			if( j === undefined || j == ''){
				if(end == 'admin'){
					c.html("<span class='evost_none'>Your seat map is empty, add a new section to get started!</span>");
				}else{
					c.html("<span class='evost_none'></span>");
				}
				return false;
			} 

			d = {};
			d['sections'] = j;
			HTML = '';

			// HANDLEBAR ADDITIONS
				Handlebars.registerHelper('ifE',function(v1, options){
					return (v1 !== undefined && v1 != '' && v1)
	                    ? options.fn(this)
	                    : options.inverse(this);
				});
				// get seat unique id with row and section
				Handlebars.registerHelper('Par',function(v1, v2, v3){				
					return v1.section_id+'-'+ v2.row_id +'-'+ v3;
				});
				// pass avaiability class to sections
				Handlebars.registerHelper('avail',function(v1, v2){
					if(v1 != 'una') return false;
					if(parseInt(v2)<1) return false;
					return 'av';
				});
				Handlebars.registerHelper('ifCOND',function(v1, operator, v2, options){
					return checkCondition(v1, operator, v2)
		                ? options.fn(this)
		                : options.inverse(this);
				});
			
			if( temp_part === undefined){
				template = Handlebars.compile( temp );
				HTML = template( d );
			}else{
				HTML = $(this).evo_HB_process_template({TD: d ,part: temp_part});
			}
			
			
			c.html( HTML );	
			
			// add classes for section
			if( section_id != '' && classes !== undefined)
				c.find('#evost_section_'+section_id).addClass(classes);

			// only for frontend mark seats in cart	
			if(end == 'front' && opt.j_cart !== undefined){
				$.each(opt.j_cart.seat, function(key, dd){
					c.find('.evost_seat[data-sid="'+ dd.seat_slug +'"]').addClass('mine');
				});
			}

			// only for admin
			if(end == 'admin'){
				$('body').trigger('evost_calculate_stats');	
				$('body').trigger('evost_draggables');		
				$('body').trigger('evost_after_map_drawn');		
			}
		}

		function checkCondition(v1, operator, v2) {
	        switch(operator) {
	            case '==':
	                return (v1 == v2);
	            case '===':
	                return (v1 === v2);
	            case '!==':
	                return (v1 !== v2);
	            case '<':
	                return (v1 < v2);
	            case '<=':
	                return (v1 <= v2);
	            case '>':
	                return (v1 > v2);
	            case '>=':
	                return (v1 >= v2);
	            case '&&':
	                return (v1 && v2);
	            case '||':
	                return (v1 || v2);
	            default:
	                return false;
	        }
	    }

}(jQuery));