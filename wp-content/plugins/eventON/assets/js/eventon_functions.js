/*
 * Javascript: EventON functions for all calendars
 * @version: 4.1.2
 */
(function($){

	$.fn.evo_cal_functions = function(O){
		el = this;
		switch(O.return){
			// load shortcodes inside calendar data
			case 'load_shortcodes':
				return el.find('.evo_cal_data').data('sc');		
			break;
			case 'update_shortcodes':
				el.find('.evo_cal_data').data( 'sc', O.SC );
			break;
		}
	};

	// Count down
	// @+ 3.0
		$.fn.evo_countdown_get = function(opt){
			var defaults = { gap:''};
			var OPT = $.extend({}, defaults, opt);
			
			distance = (OPT.gap * 1000);

			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			minutes = minutes<10? '0'+minutes : minutes;
			seconds = seconds<10? '0'+seconds : seconds;

			return {
				'd': days,
				'h':hours,
				'm':minutes,
				's':seconds
			}; 
		};
		$.fn.evo_countdown = function(opt){
			var defaults = { S1:''};
			var OPT = $.extend({}, defaults, opt);
			var el = $(this);

			const day_text = ( el.data('d') !== undefined && el.data('d') != '' )? el.data('d'):'Day';
			const days_text = ( el.data('ds') !== undefined && el.data('ds') != '' )? el.data('ds'):'Days';

			// intial run
			var gap = parseInt(el.data('gap'));
			var text = el.data('t');
			if(text === undefined) text = '';

			if( el.hasClass('evo_cd_on')) return;

			if( gap > 0 ){
				dd = el.evo_countdown_get({ 'gap': gap});
				el.html( ( dd.d>0 ? dd.d + ' ' + ( dd.d >1 ? days_text: day_text ) + " "  :'') + dd.h + ":" + dd.m +':'+ dd.s +'  '+text );
				
				el.data('gap', (gap -1) );
				var duration = el.data('dur');

				el.addClass('evo_cd_on');

				// maybe adopt moment
				//var M = moment();
				//console.log(M);
				
				// set intervals
				var CD = setInterval(function(){
					
					gap = el.data('gap');
					duration = el.data('dur');	

					const bar_elm = el.closest('.evo_event_progress').find('.evo_ep_bar');									

					if( gap > 0 ){

						// increase bar width if exists
						if( duration !== undefined && bar_elm.length){
							perc = ( (duration - gap)/ duration ) * 100;
							bar_elm.find('b').css('width',perc+'%');							
						}
						
						dd = el.evo_countdown_get({ 'gap': gap});	

						el.html( ( dd.d>0 ? dd.d + ' '  + ( dd.d >1 ? days_text: day_text ) + " ":'') + dd.h + ":" + dd.m +':'+ dd.s +' '+text );
						el.data('gap', ( gap - 1)  );						

					}else{

						const expire_timer_action = el.data('exp_act');

						// perform ajax task after expiration
						if(expire_timer_action !== undefined){
							$('body').trigger('runajax_refresh_now_cal',[ 
								el , 
								el.data('n'),
							]);
						}

						const _complete_text = el.evo_get_txt({V:'event_completed'});

						// live now text
						if(bar_elm.length){
							bar_elm.addClass('evo_completed');
							//el.siblings('.evo_ep_pre').html( _complete_text );
						}


						// event tag & live elements
						if( el.closest('.evcal_desc').length){
							el.closest('.evcal_desc').find('.eventover').html( _complete_text);
							el.closest('.evcal_desc').find('.evo_live_now').remove();
						}

						// event card
						if( el.closest('.eventon_list_event').length){
							el.closest('.eventon_list_event').find('span.evo_live_now').hide();
						}

						el.html('');
						clearInterval(CD);
					}

				},1000);
			}else{
				clearInterval(CD);
			}
		};


	// access page GLOBALS
		$.fn.evo_get_global = function(opt){
			var defaults = { S1:'', S2:''};
			var OPT = $.extend({}, defaults, opt);

			var BUS = $('#evo_global_data').data('d');

			if(!(OPT.S1 in BUS)) return false;
			if(!(OPT.S2 in BUS[OPT.S1])) return false;
			return BUS[OPT.S1][OPT.S2];
		}
		$.fn.evo_get_txt = function(opt){
			var defaults = { V:''}
			var OPT = $.extend({}, defaults, opt);

			var BUS = $('#evo_global_data').data('d');
			if(!('txt' in BUS)) return false;
			if(!(OPT.V in BUS.txt)) return false;
			return BUS.txt[OPT.V];
		}
		$.fn.evo_get_cal_def = function(opt){
			var defaults = { V:''}
			var OPT = $.extend({}, defaults, opt);

			var BUS = $('#evo_global_data').data('d');
			if(!('cal_def' in BUS)) return false;
			if(!(OPT.V in BUS.cal_def)) return false;
			return BUS.cal_def[OPT.V];
		}

		// return dms translates values from global data
		// added 4.0
		$.fn.evo_get_dms_vals = function(opt){
			// type = d, d1,d3, m, m3
			// V = 0-x
			var defaults = { type:'d', V:''}
			var OPT = $.extend({}, defaults, opt);

			var BUS = $('#evo_global_data').data('d');			
			if(!('dms' in BUS)) return false;
			if(!(OPT.type in BUS.dms)) return false;

			return BUS.dms[ OPT.type ][ OPT.V ];
			
		}

	// GENERAL ACCESS + 4.1.2
		$.fn.evo_admin_get_ajax = function(opt){
  			var defs = {
  				'lightbox_key':'',
  				'ajaxdata':'',
  			}

  			var OO = $.extend({}, defs, opt);

  			var ajaxdata = OO.ajaxdata;

  			var returnvals = '';

			$.ajax({
				beforeSend: function(){
					if( OO.lightbox_key){
						$('.'+ OO.lightbox_key).find('.ajde_popup_text').addClass( 'loading');
					}
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdata,
				dataType:'json',
				success:function(data){
					returnvals = data;
				},complete:function(){
					if( OO.lightbox_key){
						$('.'+ OO.lightbox_key).find('.ajde_popup_text').removeClass( 'loading');
					}
				}
			});	

			return returnvals;
		}

	// Handlebar process template data into html
		$.fn.evo_HB_process_template = function(opt){
			var defaults = { TD:'', part:''}
			var OPT = $.extend({}, defaults, opt);

			BUS = $('#evo_global_data').data('d');
						
			template = Handlebars.compile( BUS.temp[ OPT.part ] );
			return template( OPT.TD );
		}

	// Date range and events
		// Date range and events - from webpage
		$.fn.evo_cal_events_in_range = function(opt){
			var defaults = { S:'', E:'', 
				hide: true, 
				closeEC:true,
				showEV: false, // show events
				showEVL: false, // show events list
				showAllEvs: false // show all events regardless of the range
			};
			var OPT = $.extend({}, defaults, opt);
			var CAL = $(this);

			eJSON = CAL.find('.evo_cal_events').data('events');

			R = {};
			html = '';
			json = {};

			show = 0;

			// using events JSON data
			if( eJSON && eJSON.length > 0){
				$.each(eJSON, function(ind, ED){
					eO = CAL.find('#event_'+ ED._ID);
					if(eO === undefined || eO.length==0) return;

					if(OPT.hide)	eO.hide(); // pre hide
					this_show = false;

					// month long or year long events
					if( ED.month_long || ED.year_long ){
						this_show = true;

					}else{
						if(CAL.evo_is_in_range({
							'S': OPT.S,	'E': OPT.E,	'start': ED.event_start_unix ,	'end':ED.event_end_unix 
						})){						
							this_show = true;
						} 
					}

					if( OPT.showAllEvs) this_show = true;
					
					if( this_show){	
						// show event
						if( OPT.showEV) eO.show();

						// close open event cards
						if(OPT.closeEC && SC.evc_open == 'no') eO.find('.event_description').hide().removeClass('open');

						html += eO[0].outerHTML;
						json[ ED._ID] = ED;
						show++;	
					} 
				});
			}else{	
				// get all the events in the events list
				var cal_events = CAL.find('.eventon_list_event');

				cal_events.each(function(index, elm){
					var ED = $(elm).evo_cal_get_basic_eventdata();
					if( !ED) return;

					if(OPT.hide)	$(elm).hide(); // pre hide
					this_show = false;

					// month long or year long events
					if( $(elm).hasClass('month_long') || $(elm).hasClass('year_long') ){
						this_show = true;

					}else{
						if(CAL.evo_is_in_range({
							'S': OPT.S,	'E': OPT.E,	'start': ED.event_start_unix ,	'end':ED.event_end_unix 
						})){						
							this_show = true;
						} 
					}

					if( OPT.showAllEvs) this_show = true;
					
					if( this_show){	
						// show event
						if( OPT.showEV) $(elm).show();

						// close open event cards
						if(OPT.closeEC && SC.evc_open == 'no') 
							$(elm).find('.event_description').hide().removeClass('open');

						html += $(elm)[0].outerHTML;
						json[ ED.uID ] = ED;
						show++;	
					} 
				});
			}


			// No events
			if( OPT.showEV){
				tx_noevents = CAL.evo_get_txt({V:'no_events'});
				EL = CAL.find('.eventon_events_list');
				EL.find('.eventon_list_event.no_events').remove();
				if( show == 0 )
					EL.append('<div class="eventon_list_event no_events"><p class="no_events">'+tx_noevents+'</p></div>');
			}

			// if show events list
			if( OPT.showEVL){
				CAL.find('.eventon_events_list').show().removeClass('evo_hide');
			}

			R['count'] = show;
			R['html'] = html;
			R['json'] = json;

			return R;
		}

	// check if an event is in the given date range
		$.fn.evo_is_in_range = function(opt){
			var defaults = { S:'', E:'', start:'',end:''}
			var OPT = $.extend({}, defaults, opt);

			S = parseInt(OPT.S);
			E = parseInt(OPT.E);
			start = parseInt(OPT.start);
			end = parseInt(OPT.end);

			return (
				( start <= S && end >= E ) ||
				( start <= S && end >= S && end <= E) ||
				( start <= E && end >= E ) ||
				( start >= S && end <= E )
			) ? true: false;
		}
		$.fn.evo_cal_hide_events = function(){
			CAL = $(this);
			CAL.find('.eventon_list_event').hide();
		}

	// get event data basics from html event on page 
	// ~@version 4.0.3
		$.fn.evo_cal_get_basic_eventdata = function(){
			var ELM = $(this);

			var _time = ELM.data('time');
			if( _time === undefined ) return false;

			const time = _time.split('-');
			const ri = ELM.data('ri').replace('r','');
			const eID = ELM.data('event_id');

			var _event_title = ELM.find('.evcal_event_title').text();
			_event_title = _event_title.replace(/'/g, '&apos;');


			var RR = {
				'uID': eID + '_' + ri,
				'ID': eID ,
				'event_id': eID ,
				'ri': ri,
				'event_start_unix': parseInt(time[0]),
				'event_end_unix': parseInt(time[1]),
				'ux_val': ELM.find('.evcal_list_a').data('ux_val'),
				'event_title': _event_title,
				'hex_color': ELM.data('colr'),
				'hide_et': ELM.hasClass('no_et') ? 'y':'n',
				'evcal_event_color': ELM.data('colr'),
			};

			return RR;

		}


	// DATE time functions @+2.8		
		$.fn.evo_day_in_month = function(opt){
			var defaults = { M:'', Y:''}
			var OPT = $.extend({}, defaults, opt);

			return new Date(OPT.Y, OPT.M, 0).getDate();
		}
		$.fn.evo_get_day_name_index = function(opt){
			var defaults = { M:'', Y:'', D:''}
			var OPT = $.extend({}, defaults, opt);

			//return moment(OPT.Y+'-'+OPT.M+'-'+OPT.D).utc().day();

			return new Date(  Date.UTC(OPT.Y, OPT.M-1, OPT.D) ).getUTCDay();
		}

	// page Lightbox functions @+2.8
	// append to the lightbox main class name .evo_lightbox
		
		$.fn.evo_prepare_lb = function(){
			$(this).find('.evo_lightbox_body').html('');
		}
		$.fn.evo_show_lb = function(opt){
			var defaults = { RTL:'', calid:''}
			var OPT = $.extend({}, defaults, opt);

			$(this).addClass('show '+ OPT.RTL).attr('data-cal_id', OPT.calid);
			$('body').trigger('evolightbox_show');
		}
		$.fn.evo_append_lb = function(opt){
			var defaults = { C:'', CAL:''}
			var OPT = $.extend({}, defaults, opt);
			$(this).find('.evo_lightbox_body').html( OPT.C);

			if(  OPT.CAL!= '' && OPT.CAL !== undefined && OPT.CAL.hasClass('color')){
				const LIST = $(this).find('.eventon_events_list');
				if( LIST.length>0){
					LIST.find('.eventon_list_event').addClass('color');
				}				
			}
		}

	// Shortcodes
		// update shortcode values from filter changes
		$.fn.evo_update_sc_from_filters = function(){
			var ev_cal = $(this); 	
			SC = ev_cal.find('.evo_cal_data').data('sc');		

			var filter_section = ev_cal.find('.eventon_filter_line').first();
			
			filter_section.find('.eventon_filter').each(function(index){
				O = $(this);
				var filter_val = O.data('filter_val');	
				filter_val = filter_val == ''? 'NOT-all': filter_val;				

				ADD = (O.data('fl_o') && O.data('fl_o') != 'IN')? O.data('fl_o')+'-': '';
				SC[ O.data('filter_field') ] = ADD + filter_val;
			});	

			ev_cal.find('.evo_cal_data').data( 'sc', SC );
		}
		
		// get shortcodes from evo bottom
		$.fn.evo_shortcode_data = function(){			
			var ev_cal = $(this);
			return ev_cal.find('.evo_cal_data').data('sc');			
		}

		// get shortcode single value
		$.fn.evo_get_sc_val = function(opt){
			var defaults = {	F:''}
			var OPT = $.extend({}, defaults, opt);
			var ev_cal = $(this); 

			if(OPT.F=='') return false;
			SC = ev_cal.find('.evo_cal_data').data('sc');

			if(!(SC[ OPT.F])) return false;
			return SC[ OPT.F];
		}
		// UPDATE Single value
		$.fn.evo_update_cal_sc = function(opt){
			var defaults = {
				F:'', V:''
			}
			var OPT = $.extend({}, defaults, opt);
			var ev_cal = $(this); 
			SC = ev_cal.find('.evo_cal_data').data('sc');

			SC[ OPT.F ] = OPT.V;

			ev_cal.find('.evo_cal_data').data( 'sc', SC );
		}
		// UPDATE all shortcode values
		$.fn.evo_update_all_cal_sc = function(opt){
			var defaults = {SC:''}
			var OPT = $.extend({}, defaults, opt);
			var CAL = $(this);
			CAL.find('.evo_cal_data').data( 'sc', OPT.SC );
		}

	// OTHERS
		// hex colors // @+2.8
		$.fn.evo_is_hex_dark = function(opt){
			var defaults = { hex:'808080'}
			var OPT = $.extend({}, defaults, opt);

			hex = OPT.hex;

			var c = hex.replace('#','');
			var is_hex = typeof c === 'string' && c.length === 6 && !isNaN(Number('0x' + c));

			if(is_hex){	
				var values = c.split('');
				r = parseInt(values[0].toString() + values[1].toString(), 16);
			    g = parseInt(values[2].toString() + values[3].toString(), 16);
			    b = parseInt(values[4].toString() + values[5].toString(), 16);
			}else{
				var vals = c.substring(c.indexOf('(') +1, c.length -1).split(', ');
				var r = vals[0]  // extract red
				var g = vals[1];  // extract green
				var b = vals[2];
			}

			var luma = ((r * 299) + (g * 587) + (b * 114)) / 1000; // per ITU-R BT.709

			return luma>155? true:false;
		}
	// Other data
		$.fn.evo_get_OD = function(){			
			var ev_cal = $(this);
			return ev_cal.find('.evo_cal_data').data('od');			
		}

	// return all filter values for given calendar -- DEP 2.8
		$.fn.evoGetFilters = function(){

			var ev_cal = $(this); 
			var evodata = ev_cal.find('.evo-data');
			
			var filters_on = ( evodata.attr('data-filters_on')=='true')?'true':'false';
			
			// creat the filtering data array if exist
			if(filters_on =='true'){
				var filter_section = ev_cal.find('.eventon_filter_line');
				var filter_array = [];
				

				filter_section.find('.eventon_filter').each(function(index){
					var filter_val = $(this).attr('data-filter_val');
					
					if(filter_val !='all'){
						var filter_ar = {};
						filter_ar['filter_type'] = $(this).attr('data-filter_type');
						filter_ar['filter_name'] = $(this).attr('data-filter_field');

						if($(this).attr('data-fl_o')=='NOT'){
							filter_ar['filter_op']='NOT IN';
						}
						filter_ar['filter_val'] = filter_val;
						filter_array.push(filter_ar);
					}
				});		
			}else{
				var filter_array ='';
			}			
			return filter_array;
		}
	// old shortcode method -- DEP
		$.fn.evo_item_shortcodes = function(){			
			var OBJ = $(this);
			var shortcode_array ={};			
			OBJ.each(function(){	
				$.each(this.attributes, function(i, attrib){
					var name = attrib.name;
					if(attrib.name!='class' && attrib.name!='style' && attrib.value !=''){
						name__ = attrib.name.split('-');
						shortcode_array[name__[1]] = attrib.value;	
					}
				});
			});
			return shortcode_array;
		}
		$.fn.evo_shortcodes = function(){			
			var ev_cal = $(this);
			var shortcode_array ={};
					
			ev_cal.find('.cal_arguments').each(function(){
				$.each(this.attributes, function(i, attrib){
					var name = attrib.name;
					if(attrib.name!='class' && attrib.name!='style' && attrib.value !=''){
						name__ = attrib.name.split('-');
						shortcode_array[name__[1]] = attrib.value;	
					}
				});
			});	
			return shortcode_array;
		}

		// get evo data for a given calendar
		$.fn.evo_getevodata = function(){

			var ev_cal = $(this);
			var evoData = {};
			
			ev_cal.find('.evo-data').each(function(){
				$.each(this.attributes, function(i, attrib){
					var name = attrib.name;
					if(attrib.name!='class' && attrib.name!='style' ){
						name__ = attrib.name.split('-');
						evoData[name__[1]] = attrib.value;	
					}
				});
			});	

			return evoData;
		}

	// eventon loading functions
	// v 2.4.5
		$.fn.evo_loader_animation = function(opt){
			var defaults = {
				direction:'start'
			}
			var OPT = $.extend({}, defaults, opt);

			if(OPT.direction == 'start'){
				$(this).find('#eventon_loadbar').slideDown();
			}else{
				$(this).find('#eventon_loadbar').slideUp();
			}
		}

	
		

}(jQuery));