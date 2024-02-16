/**
 * Javascript code that is associated with the front end of the calendar
 * @version 4.5.8
 */

jQuery(document).ready(function($){

		
	var BODY = $('body');
	var BUS = ''; // initial eventon calendar data
	var ajax_url = evo_general_params.ajaxurl;

	init();

	
// EventON calendar main function
// 2.8.6
	$.fn.evo_calendar = function (options) {

		el = this;
		var cal = {};
		var defaults = {
			'SC': {},
			'json':{}
		};
		var calO = {};

		var init = function(){
			calO = el.O = $.extend({},defaults, options);

			// load shortcodes to calendar
			if( calO.SC !== undefined){
				el.evo_cal_functions({action:'update_shortcodes',SC: calO.SC});
			}

			// load events json to calednar
			if( calO.json !== undefined){
				el.find('.evo_cal_events').data('events', calO.json);
			}

			load_maps();
			interactions();			
		};

		var load_maps = function(){
			if(calO.SC.evc_open == 'yes'){
				el.find('.desc_trig').each(function(index){
					var self = this;
					setTimeout(function(){
						$(self).evoGenmaps({'fnt':2,'cal': el});
					},index*600);					
				});
			}
		};
		
		var interactions = function(){
			close_eventcard();
			counters();
			live_now_cal();
		}


		var live_now_cal = function(){
			$(el).find('.evo_img_time').each(function(){
				if( $(this).closest('a.desc_trig').find('em.evcal_time').length ){
					_html = $(this).closest('a.desc_trig').find('em.evcal_time')[0].outerHTML;
					$(this).html( _html );
				}				
			});
		}

		var counters = function(){
			$(el).find('.evo_countdowner').each(function(){
				$(this).evo_countdown();
			});
		}

		var close_eventcard = function(){
			$(el).on('click','.evcal_close',function(){
				$(this).closest('.evcal_eventcard').slideUp().removeClass('open');
			});
		}	

		init();	
	};

	/*
	$('body').bind('touchend',function(event){
		console.log(event);
		if( $(event.target).hasClass('evcal_arrows') ){
			$(event.target).hover();
		}
	});
	*/

// localize event time function @ 4.5.5
	$.fn.evo_local_time = function( options){
		el = this;

		$(el).find('.evo_mytime').each(function(){
			const obj = $(this);
			const event_obj = obj.closest('.eventon_list_event');
			var time  = obj.data('times');
			var time_format = obj.data('__f');

			new_time = time.split('-');
			
			utc_offset = $(this).data('tzo');
			utc_offset = 0;

			start = parseInt(new_time[0]);
			end = parseInt(new_time[1]);
			offset_start = start + utc_offset;


			// end
			var Me = moment.unix( end ).utc().local();
			var M1 = moment.unix( start ).utc().local();

			var _html = '';

			// same month 
			if( Me.format('YYYY/M') == M1.format('YYYY/M')){

				// same date
				if( Me.format('DD') == M1.format('DD')){
					_html = M1.format( time_format ) + ' - ' + Me.format( obj.data('__tf') ) ;
				// dif date
				}else{
					_html = M1.format( time_format ) +' - '+ Me.format( time_format );
				}
			// dif month
			}else{
				_html = M1.format( time_format ) +' - '+ Me.format( time_format );				
			}


			event_obj.find('.evo_mytime').find('b').html( _html );
			event_obj.find('.evo_mytime').removeClass('tzo_trig');
			
		});
	};
	// user local time		
		BODY.on('click','.tzo_trig',function(event){
			event.preventDefault();
			event.stopPropagation();			
			$(this).parent().evo_local_time();
			return;
		});

// RUN on Page load
	function init(){

		init_run_gmap_openevc();
		fullheight_img_reset();	


		init_load_cal_data();

		handlebar_additional_arguments();

		// run basic countdown timers
		BODY.find('.evo_countdowner').each(function(){
			$(this).evo_countdown();
		});


		jitsi();
	}

// GENERALIZED functions
// General AJAX trigger - added 3.1
	$(document).on('click','.evo_trig_ajax',function(event ){
		var ajax_data = {};
		const obj = $(this);

		ajax_data = obj.data();
		$(document).data( 'evo_data', ajax_data );

		$(document).trigger('evo_before_trig_ajax', [obj]);

		var new_ajax_data = $(document).data( 'evo_data');
		new_ajax_data['nn'] = the_ajax_script.postnonce;

		$.ajax({
			beforeSend: function(){
				$(document).trigger('evo_beforesend_trig_ajax', [obj, new_ajax_data]);
			},
			type: 'POST',url: get_ajax_url('eventon_gen_trig_ajax') ,data: new_ajax_data,dataType:'json',
			success:function(return_data){
				$(document).trigger('evo_success_trig_ajax', [obj, new_ajax_data, return_data]);
			},complete:function(){
				$(document).trigger('evo_complete_trig_ajax', [obj, new_ajax_data]);
			}
		});

	});

// Virtual Events & jitsi
	function jitsi(mod_refresh){
		
		const domain = 'meet.jit.si';
	    const api = [];

	    jQuery('.evo-jitsi-wrapper').each(function(index, element) {
	    	const O = $(this);
	    	const eventO = O.closest('.eventon_list_event');

	    	// check if mod refresh is set or no
	    	if( mod_refresh != '' && mod_refresh == 'mod_refresh_no' && O.hasClass('mod')) return;

	        var roomName = jQuery(element).data('n'),
	            width = jQuery(element).data('width'),
	            height = jQuery(element).data('height'),
	            audioMuted = jQuery(element).data('audiomute'),
	            videoMuted = jQuery(element).data('videomute'),
	            screenSharing = jQuery(element).data('screen');
	           

	        const myOverwrite =
			{
			 	'TOOLBAR_BUTTONS': $(element).data('d'),
			    "DEFAULT_BACKGROUND": '#494a4e',
			    'MOBILE_APP_PROMO': false,
			    'SETTINGS_SECTIONS':['devices', 'language', 'profile', 'calendar'],
			};


	        const options = {
	            roomName,
	            width,
	            height,
	            parentNode: element,	            
	            configOverwrite: { 
	            	startWithAudioMuted: audioMuted,
	                startWithVideoMuted: videoMuted,
	                startScreenSharing: false,	  
	                disableInviteFunctions: false,             
	            },
	            interfaceConfigOverwrite: myOverwrite,     
	        };


	        const api = new JitsiMeetExternalAPI(domain, options);      

	        api.addEventListener('participantRoleChanged', function(event){

	        	// record moderator joined
	        	if (event.role === "moderator"){	        		
	        		_record_moderator_join( 'yes', eventO.data('event_id'), eventO.data('ri'));
	        	}

	        	const pp = jQuery(element).data('p');
	        	if (event.role === "moderator" && pp != '__') {
	        		ppp = pp.replace('_','');
			        api.executeCommand('password', ppp);
			    }	        	
	        });	

	        // moderator leave	        
	        api.addEventListener('videoConferenceLeft', function(event){
	        	if( eventO.find('.evo_vir_data').data('ismod') =='y'){
	        		_record_moderator_join( 'no', eventO.data('event_id'), eventO.data('ri'));
	        		O.siblings('.evo_vir_mod_left').show();
	        		O.hide();
	        	}
	        });
	    });
	}
	// record moderator logins for jitsi
		function _record_moderator_join(joined, eid, ri){
			var data_arg = {
				'action': 'eventon_record_mod_joined',
				'eid': eid,
				'ri': ri,
				'joined': joined,
				'nonce': evo_general_params.n,				
			};

			$.ajax({
				beforeSend: function(){},
				type: 'POST',url: ajax_url,data: data_arg,dataType:'json',
				success:function(data){	}
			});
		}
	

	// refresh event card elements  - evo_reload_virtual_events
	// @+ 3.1
		$('body').on('evo_refresh_elements',function(event, send_data ){

			if( send_data.length <= 0 || !send_data) return;

			send_data['nonce'] = evo_general_params.n;

			$.ajax({
				beforeSend: function(){	
					if( 'evo_data' in send_data){
						$.each(send_data.evo_data, function(ekey, eclasses){
							$.each(eclasses, function(classnm, val){	
								if(val && 'loader' in val && val['loader'] && 'loader_class' in val){
									$('#event_'+ekey).find('.'+val['loader_class']).addClass('evoloading');	
								}
							});
						});	
					}
				},
				type: 'POST',url: get_ajax_url('eventon_refresh_elm'),data: send_data,dataType:'json',
				success:function(data){
					if( data.status == 'good' ){
						evo_apply_refresh_content( data );
					}
				},complete: function(){	
					if( 'evo_data' in send_data){
						$.each(send_data.evo_data, function(ekey, eclasses){
							$.each(eclasses, function(classnm, val){
								if(val && 'loader' in val && val['loader'] && 'loader_class' in val){
									$('#event_'+ekey).find('.'+val['loader_class']).removeClass('evoloading');	
								}
							});
						});	
					}
				}
			});
		});

		// refresh the closest hearbeat run parent
		$('body').on('evo_refresh_designated_elm', function(ee, elm, elm_class, extra_data){

			//get closest event object
			const event = $(elm).closest('.eventon_list_event');

			if( !event ) return;
			if( event.find('.'+elm_class).length == 0 ) return;

			const refresh_elm = event.find('.'+elm_class);

			var send_data = {};		

			send_data['evo_data'] = build_elm_refresh_data( refresh_elm , extra_data);
			
			$('body').trigger('evo_refresh_elements',[ send_data ]);
		});

	// record sign in - virtual plus
		// @+3.1
		$('body').on('click','.evo_vir_signin_btn',function(){
						
			extra_data = {};
			extra_data['signin'] = 'y';
			extra_data['refresh_main'] = 'y';
			extra_data['loader'] = true;
			extra_data['loader_class'] = 'evo_vir_main_content';

			$('body').trigger('evo_refresh_designated_elm',[ $(this) , 'evo_vir_data',extra_data]);
		});


	// apply refresh event element content with matching data that is sent
		function evo_apply_refresh_content(data){

			if( 'evo_data' in data ){

				$.each(data.evo_data, function(eclass, boxes){
					// if event exists in the page

					var vir_data_vals = false;
					if( 'evo_vir_data' in boxes) vir_data_vals = boxes.evo_vir_data.data;

					$('body').find('.'+eclass).each(function(){
						const event_elm = $(this);

						// set html
						$.each(boxes, function(boxclass, boxdata){
							if( !('html' in boxdata) ) return;
							if( boxdata.html == '' ) return;
							if( event_elm.find('.'+boxclass).legnth <= 0 ) return;

							event_elm.find( '.'+boxclass ).html( boxdata.html );
						});

						// only for virtual event update
						if( vir_data_vals ){

							// reload jitsi for main content - if main content html is sent it will refresh
								if( vir_data_vals && ('vir_type' in vir_data_vals) 
									&& vir_data_vals.vir_type == 'jitsi' 
									&& ('evo_vir_main_content' in boxes) 
									&& ('html' in boxes.evo_vir_main_content)  
									&& boxes.evo_vir_main_content.html != ''
								){
									jitsi('mod_refresh_no');
								}

							// update data for sent object
								$.each(boxes, function(boxclass, boxdata){
									if( boxdata.data == '' || boxdata.data === undefined) return;

									// for jitsi if mod left --> force refresh main
									if( boxdata !== undefined && vir_data_vals.vir_type == 'jitsi' && vir_data_vals.mod_joined =='left'){
										
										// force refresh main
										boxdata.data['refresh_main'] = 'yy';
									}	

									event_elm.find( '.'+boxclass ).data( boxdata.data );
								});
						}

					});
				});
			}
		}
	// get refresh data for specified elem
		function build_elm_refresh_data( elm , extra_data){

			dataObj = {};

			// get closest event element and event id/ri from it
				const event = $(elm).closest('.eventon_list_event');

				dataObj = {}
				const ekey = event.data('event_id')+'_'+ parseInt(event.data('ri'));
				dataObj[ ekey ] = {};

			const key2 = elm.data('key');

			// append new data to the element
			dataObj[ ekey ][ key2 ] = elm.data();

			// check if awaitmod need checked
				if( elm.data('check_awaitmod')){

					// if waiting for mod element is on page -> set as user awaiting mod
					if( ( event.find('.evo_vir_jitsi_waitmod').length>0) )
						dataObj[ ekey ][ key2 ]['refresh_main'] = 'yy';

					// if jitsi is loaded on page & mod is still in --> stop refreshing main
					if( event.find('.evo-jitsi-wrapper').length>0 && dataObj[ ekey ][ key2 ]['mod_joined'] !='left') 
						dataObj[ ekey ][ key2 ]['refresh_main'] =  '';
				}

			// append extra data
			if( extra_data && extra_data !== undefined){
				$.each( extra_data, function (index,val){
					dataObj[ ekey ][ key2 ][index] = val;
				});
			}

			return dataObj;
		}	

	// mark as virtual event ended
		$(document)
		.on('evo_before_trig_ajax',function(event, obj){
			if(!obj.hasClass('evo_trig_vir_end')) return;
			
			var new_ajax_data = $(document).data( 'evo_data');
			new_ajax_data['fnct'] = 'mark_event_ended';
			$(document).data( 'evo_data', new_ajax_data );
			
		})
		.on('evo_beforesend_trig_ajax',function( event, obj, new_ajax_data){
			if(!obj.hasClass('evo_trig_vir_end')) return;

			obj.closest('.evo_vir_mod_box').addClass('evoloading');
		})
		.on('evo_success_trig_ajax',function( event, obj, new_ajax_data, return_data){
			if(!obj.hasClass('evo_trig_vir_end')) return;

			// if virtual events were marked as ended
			if(!('_vir_ended' in return_data)) return;

			// refresh the virtual main content
			extra_data = {};
			extra_data['refresh_main'] = 'yy';
			extra_data['loader'] = true;
			extra_data['loader_class'] = 'evo_vir_main_content';

			//console.log(extra_data);

			$('body').trigger('evo_refresh_designated_elm',[ obj , 'evo_vir_data',extra_data]);
		})
		.on('evo_complete_trig_ajax',function( event, obj, new_ajax_data){

			if(!obj.hasClass('evo_trig_vir_end')) return;
			obj.closest('.evo_vir_mod_box').removeClass('evoloading');
		})
		;

// Heartbeat - added 3.1
	//hook into heartbeat-send
	jQuery(document).on('heartbeat-send', function(e, data) {

		// if there is run heartbeat items
		if( $('body').find('.evo_refresh_on_heartbeat').length>0 ){
			
			$('body').find('.evo_refresh_on_heartbeat').each(function(){
				if( $(this).closest('.eventon_list_event').length <= 0) return;
				if( $(this).data('refresh')!== undefined && !$(this).data('refresh') ) return;

				data['evo_data'] = build_elm_refresh_data( $(this) );
			});			
		}
	});
	
	//hook into heartbeat-tick
	jQuery(document).on('heartbeat-tick', function(e, data) {	
		evo_apply_refresh_content( data );
	});

// Schedule View - 4.0 / u 4.5.6
	$('body')
	.on('evo_init_ajax_success_each_cal',function(event, data, i, v, CAL){
		$('body').find('.ajde_evcal_calendar.evoSV').each(function(){
			evosv_populate( $(this) );
		});
	})
	.on('evo_main_ajax_complete', function(event, CAL,  ajaxtype, data , data_arg){
		SC = data_arg.shortcode;
		if( SC.calendar_type == 'schedule'){
			evosv_populate( CAL );
		}
	})
	// view switching
	.on('evo_vSW_clicked_before_ajax',function(event, O, CAL, DD, reload_cal_data){
		if(!(O.hasClass('evosv'))) return;
		var SC = CAL.evo_shortcode_data();

		CAL.evo_update_cal_sc({F:'calendar_type', V: 'schedule'});
		CAL.evo_update_cal_sc({F:'fixed_day', V: SC.fixed_day });

	})
	.on('evo_vSW_clicked',function(event, OBJ, CAL, DD, reload_cal_data){
		if(!(OBJ.hasClass('evosv'))) return;

		//if( reload_cal_data) return;
				
		var _reload_cal_data = false;
		var SC = CAL.evo_shortcode_data();

		DD.setUTCDate( 1 );

		if( SC.focus_start_date_range != Math.floor(DD.getTime()/1000) )
			_reload_cal_data = true;

		CAL.evo_update_cal_sc({F:'focus_start_date_range',V: Math.floor(DD.getTime()/1000) });

		DD.setUTCMonth( DD.getUTCMonth() + 1);
		DD.setUTCDate( -1);

		CAL.evo_update_cal_sc({F:'focus_end_date_range', 
			V: Math.floor(DD.getTime()/1000) });


		DD.setUTCDate( SC.fixed_day ); // adjust date to fixed date

		CAL.evo_update_cal_sc({F:'calendar_type', V: 'schedule'});
		CAL.evo_update_cal_sc({F:'fixed_day', V: SC.fixed_day });
		
		if( _reload_cal_data ){
			$('body').trigger('evo_run_cal_ajax',[CAL.attr('id'),'none','filering']);
		}else{
			evosv_populate( CAL );
		}

		
	})
	// open events from schedule view
		.on('click','.evosv_items',function(event, elm){
			O = $(this);
			CAL = O.closest('.ajde_evcal_calendar');
			var e_cl = 'event_'+O.data('id');
			
			const clicked_event_uxval = O.data('uxval');

			// if event is set to slide down .. switch to lightbox
			if( clicked_event_uxval == '1' ){
				CAL.find('.'+e_cl).find('.desc_trig').data('ux_val', 3);
			}

			CAL.find('.'+e_cl).find('.desc_trig').trigger('click');
		});

	// populate the schedule view data @4.5.8
	function evosv_populate(CAL){
		var SC = CAL.evo_shortcode_data();
		OD = CAL.evo_get_OD(); // calendar other data 

		var cal_events = CAL.find('.eventon_list_event');
		days_in_month = CAL.evo_day_in_month({M: SC.fixed_month, Y: SC.fixed_year});
		time_format = CAL.evo_get_global({S1:'cal_def',S2:'wp_time_format'});

		// text strings
			_txt = CAL.evo_get_txt({V:'no_events'});
			_txt2 = CAL.evo_get_txt({V:'until'});
			_txt3 = CAL.evo_get_txt({V:'from'});
			_txt4 = CAL.evo_get_txt({V:'all_day'});
		
		CAL.find('#evcal_list').addClass('evo_hide');

		var has_events = false;
		var html = '';
		var template_data = {};
		var processed_ids = {};

		// Set initial date - date values
			var SU = parseInt( SC.focus_start_date_range);	var EU = '';
			var M = moment.unix( SU ).tz( OD.cal_tz );

		// go through each day in month
		for(var x=1; x<= days_in_month; x++){
			
			var month_name = CAL.evo_get_dms_vals({ V: (M.get('month') +1), type:'m3'});
			var day_name = CAL.evo_get_dms_vals({ V: M.day(), type:'d3'});
			
			// set event unix into moment
				SU = M.unix();	M.endOf('day');
				EU = M.unix();	M.startOf('day');
			
			// run through each event and get events in this date
				var events = {};

				cal_events.each(function(index, elm){
					ED = $(elm).evo_cal_get_basic_eventdata();
					if( !ED) return;

					processed_ids[ED.uID] = ED.uID;
					ESU = ED.unix_start; EEU = ED.unix_end;

					// check for date range
						var inrange = CAL.evo_is_in_range({
							'S': SU,	'E': EU,	'start': ESU,	'end':EEU
						});
						if(!inrange) return; // skip if no in range

					has_events = true;

					// event time relative to calendar tz
					m = moment.unix( ESU ).tz( OD.cal_tz );
					me = moment.unix( end ).tz( OD.cal_tz );

					var all_day = $(elm).find('a.desc_trig').hasClass('allday') ? true: false;

					// get event time correct for all day
					if( all_day ){
						ED['t'] = _txt4;
					}else{
						if( ESU <= SU ){
							if( EEU >= EU) ED['t'] = _txt4;
							if( EEU < EU ) ED['t'] = _txt2+' ' + me.format( time_format);		
						}else if(ESU > SU){
							if( EEU >= EU)  ED['t'] = _txt3+' '+ m.format( time_format);
							if( EEU < EU ) ED['t'] = m.format( time_format) +' - '+ me.format( time_format);
						}	
					}						

					// hide end time
					if( ED.hide_et == 'y')		ED['t'] = m.format( time_format);

					events[index] = ED;
				});			

			// if there are events in this date
				if( events && Object.keys(events).length > 0){
					
					template_data[ x ] = {};
					template_data[ x ]['date'] = '<b>' + M.get('date')+'</b> '+ month_name+' '+ day_name;
					template_data[ x ]['d'] =  M.format('YYYY-M-D');
					template_data[ x ]['SU'] = SU;
					template_data[ x ]['events'] = {}

					$.each(events, function(index, item){		

						location_data = organizer_data = event_tags = '';

						// location 
						if( SC.show_location == 'yes' && 'location' in item){
							location_data = "<div class='evosv_subdata evosv_location'><i class='fa fa-location-pin marr5'></i>" +item.location+"</div>";
						}

						// organizer					
						if( SC.show_organizer == 'yes' && 'organizer' in item){
							organizer_data = "<div class='evosv_subdata evosv_org'>" +item.organizer+"</div>";
						}

						// event tags
						if( SC.show_tags == 'yes' && 'event_tags' in item){
							event_tags = "<div class='evosv_subdata evosv_tags'>";
							$.each( item.event_tags, function(index, val){
								event_tags += "<span class='evosv_tag " + index +"'>" + val+"</span>";
							});
							event_tags += "</div>";
						}

						template_data[ x ]['events'][ item.uID ] = {
							'time': item.t,
							'ux_val': item.ux_val,
							'title': item.event_title,
							'color':item.hex_color,
							'tag': event_tags,
							'loc': location_data,
							'org': organizer_data,
							'i': item
						}

					});					
				}

			// next date
			M.add(1, 'd');
		}

		var html_ = "<div class='evosv_grid evoADDS'>";
		
		// if no events
		if( !has_events){
			no_event_content = CAL.evo_get_global({S1: 'html', S2:'no_events'});			
			html_ += "<div class='date_row'><div class='row no_events evosv'>"+no_event_content+"</div></div>";
		}else{
			html_ += CAL.evo_HB_process_template({
				TD:template_data, part:'evosv_grid'
			});
		}

		html_ += '</div>';

		if( CAL.find('.evosv_grid').length > 0){
			CAL.find('.evosv_grid').replaceWith( html_ );
		}else{
			ELM = CAL.find('#eventon_loadbar_section');
			ELM.after( html_ );
		}
		
	}

// Initial load data via ajax
	function init_load_cal_data(){

		// check if calendars are present in the page
			var run_initload = false;

			if( $('body').find('.ajde_evcal_calendar').length > 0 ) run_initload = true;
			if( $('body').find('.ajax_loading_cal').length > 0 ) run_initload = true;
			if( $('body').find('.eventon_single_event').length > 0 ) run_initload = true;

			if(run_initload == false) return false;

			var data_arg = {};	

			BODY = $('body');
			BODY.trigger('evo_global_page_run');

			data_arg['global'] = $('#evo_global_data').data('d');
			data_arg['cals'] ={};			

		// pass ajax loading calendar shortcodes
			$('body').find('.ajax_loading_cal').each(function(){
				CAL = $(this);
				SC = CAL.find('.evo_cal_data').data('sc');

				data_arg['cals'][ CAL.attr('id')] = {};
				data_arg['cals'][ CAL.attr('id')]['sc'] = SC;
			});
		
		$.ajax({
			beforeSend: function(){},
			type: 'POST',
			url: get_ajax_url('eventon_init_load'), 
			data: data_arg,dataType:'json',
			success:function(data){
				$('#evo_global_data').data('d', data);

				BUS = data;

				// append html to calendars if present
				if('cals' in data){
					var time = 300;
					$.each(data.cals, function(i,v){

						setTimeout( function(){

							CAL = BODY.find('#'+ i);
							if(CAL.length === 0) return;
							
							if('html' in v){						
								CAL.find('#evcal_list').html( v.html );
								CAL.removeClass('ajax_loading_cal');
								CAL.find('.evo_ajax_load_events').remove();

								//CAL.evo_local_time();
							}							

							CAL.evo_calendar({
								SC:v.sc,	json: v.json
							});

							$('body').trigger('evo_init_ajax_success_each_cal', [data, i, v, CAL]);

						}, time);
						time += 300;
					});
				}

				$('body').trigger('evo_init_ajax_success', [data]);

			},complete:function(data){					
				// process all calendars in the page
				$('body').find('.ajde_evcal_calendar').each(function(){
					if($(this).hasClass('.ajax_loading_cal')) return;					
					$(this).evo_calendar();
				});

				$('body').trigger('evo_init_ajax_completed', [data]);
			}
		});

	}

// ELEMENTS
// tooltips
	$('body').on('mouseover','.ajdeToolTip',function(event){
		event.stopPropagation();
		if($(this).hasClass('show')) return;

		const t = $(this).data('d');
		var p = $(this).position();
		
		var cor = getCoords(event.target);

		$('.evo_tooltip_box').removeClass('show').removeClass('L').html(t);
		var box_height = $('.evo_tooltip_box').height();
		var box_width = $('.evo_tooltip_box').width();

		$('.evo_tooltip_box').css({'top': (cor.top - 55 - box_height), 'left': ( cor.left + 5 ) })
			.addClass('show');

		// left align
		if( $(this).hasClass('L')){
			$('.evo_tooltip_box').css({'left': (cor.left - box_width - 15) }).addClass('L');			
		}
	})
	.on('mouseout','.ajdeToolTip',function(){	
		$('.evo_tooltip_box').removeClass('show');
	});

	function getCoords(elem) { // crossbrowser version
	    var box = elem.getBoundingClientRect();
	    //console.log(box);

	    var body = document.body;
	    var docEl = document.documentElement;

	    var scrollTop = window.pageYOffset || docEl.scrollTop || body.scrollTop;
	    var scrollLeft = window.pageXOffset || docEl.scrollLeft || body.scrollLeft;

	    var clientTop = docEl.clientTop || body.clientTop || 0;
	    var clientLeft = docEl.clientLeft || body.clientLeft || 0;

	    var top  = box.top +  scrollTop - clientTop;
	    var left = box.left + scrollLeft - clientLeft;

	    return { top: Math.round(top), left: Math.round(left) };
	}
	
// Other
	// LIGHTBOX		
		// since 4.2 moving to functions
		// open lightbox @2.9
		$('body').on('evo_open_lightbox',function(event, lb_class, content){
			const LIGHTBOX = $('.evo_lightbox.'+lb_class).eq(0);

			// if already open
			if(LIGHTBOX.is("visible")===true) return false;

			if( content != ''){
				LIGHTBOX.find('.evo_lightbox_body').html( content );
			}
			$('body').trigger('evolightbox_show', [ lb_class ]);
		});
		// close popup
			BODY.on('click','.evolbclose', function(){	
				if( $(this).hasClass('evolb_close_btn')) return;
				LIGHTBOX = 	$(this).closest('.evo_lightbox');
				closing_lightbox( LIGHTBOX );				
			});

		// close with click outside popup box when pop is shown	
			$(document).on('click', function(event) {
				//event.stopPropagation(); 
				//console.log($(event.target));
			    if( 
			    	$(event.target).hasClass('evo_content_inin')
			    ){
			    	closing_lightbox( $(event.target).closest('.evo_lightbox') );
			       	//console.log('5');
			    }
			});		
			function closing_lightbox( lightboxELM){
				
				if(! lightboxELM.hasClass('show')) return false;
				Close = (lightboxELM.parent().find('.evo_lightbox.show').length == 1)? true: false;
				lightboxELM.removeClass('show');

				$('body').trigger('lightbox_before_event_closing', [lightboxELM]);

				setTimeout( function(){ 
					lightboxELM.find('.evo_lightbox_body').html('');
					
					if(Close){
						$('body').removeClass('evo_overflow');
						$('html').removeClass('evo_overflow');
					}
					
					// trigger action to hook in at this stage
						$('body').trigger('lightbox_event_closing', [lightboxELM]);
				}, 100);
			}

		// when lightbox open triggered
		$('body').on('evolightbox_show',function(event, lb_class){
			$('.evo_lightboxes').show();
			$('body').addClass('evo_overflow');
			$('html').addClass('evo_overflow');

			$('body').trigger('evolightbox_opened',[ lb_class ]);
		});
		
	// OPENING event card -- USER INTREACTION and loading google maps
		//event full description\		
		$('body').on('click','.eventon_events_list .desc_trig', function(event){

			var obj = $(this);					
			
			var attr = obj.closest('.evo_lightbox').attr('data-cal_id');
			if(typeof attr !== typeof undefined && attr !== false){
				var cal_id = attr;
				var CAL = cal = $('#'+cal_id);
			}else{
				var CAL = cal = obj.closest('.ajde_evcal_calendar');
			}

			SC = CAL.evo_shortcode_data();

			var evodata = cal.find('.evo-data');

			click_sinev_box = (obj.closest('.eventon_single_event').length>0 && evodata.data('exturl')) ? true: false;
			const event_id = obj.closest('.eventon_list_event').data('event_id');
			var event_list = obj.closest('.eventon_events_list');
						
			// event specific values
			var ux_val = obj.data('ux_val');
			var exlk = obj.data('exlk');			
			
			// override overall calendar user intereaction OVER individual event UX
			if(SC.ux_val!='' && SC.ux_val!== undefined && SC.ux_val!='0'){
				ux_val = SC.ux_val;
			}

			// special mobile only user interaction 
				if( SC.ux_val_mob !== undefined && SC.ux_val_mob != '' && 
					SC.ux_val_mob != '-' && SC.ux_val_mob != ux_val){
					if( is_mobile() ) ux_val = SC.ux_val_mob;
				}



			// open as lightbox
			if(ux_val=='3' || ux_val == '3a'){
				event.preventDefault();

				repeat_interval = parseInt(obj.closest('.eventon_list_event').data('ri'));
				repeat_interval = (repeat_interval)? repeat_interval: '0';
				
				var alt_SC_data = {};


				alt_SC_data['repeat_interval'] = repeat_interval;
				alt_SC_data['ux_val'] = ux_val;
				alt_SC_data['evortl'] = event_list.hasClass('evortl')? 'yes':'no';
				alt_SC_data['event_id'] = parseInt(event_id);
				alt_SC_data['ajax_eventtop_show_content'] = true;

				var new_SC_data = $.extend( {}, SC , alt_SC_data );

				// since 4.2
				eventon_open_event_lightbox( new_SC_data, obj, CAL);
				
				return false;

			// open in single events page 
			}else if(ux_val=='4'){		
				
				var url = obj.attr('href');
				
				if( url =='' ||  url === undefined){

					url = obj.parent().siblings('.evo_event_schema').find('a').attr('href');
					window.open(url, '_self');		
				}

				if(obj.attr('target') != '_blank')	window.open(url, '_self');

				return;

			// open in single events page  in new window
			}else if(ux_val=='4a'){
				
				if( obj.attr('href')!='' &&  obj.attr('href')!== undefined){
					return;
				}else{
					var url = obj.parent().siblings('.evo_event_schema').find('a').attr('href');
					window.open(url);
					return false;
				}

			// open as external link
			}else if(ux_val=='2'){
				//var url = obj.parent().siblings('.evo_event_schema').find('a').attr('href');
				var url = obj.attr('href');

				// if the click is coming from single event box
				if( click_sinev_box ){
					event.preventDefault();
					return false;
				}

				//console.log(url);
				if(url !== undefined && url != ''){
					if(obj.attr('target') == '_blank'){  
						var win = window.open(url, '_blank');
						win.focus();
					}else{
						window.open(url, '_self');
					}	

					event.preventDefault();				
				}
				return true;

			// do not do anything
			}else if(ux_val=='X'){
				return false;
			}else if(ux_val=='none'){
				return false;
			}else{
				
				// redirecting to external link
				if(exlk=='1' && ux_val!='1'){
					// if there is no href
					if( obj.attr('href')!='' &&  obj.attr('href')!== undefined){
						return;
					}else{
						var url = obj.siblings('.evo_event_schema').find('a').attr('href');
						if(obj.attr('target') == '_blank'){  window.open(url);}else{ window.open(url, '_self');}

						event.preventDefault();								
						return false;
					}
				// SLIDE DOWN eventcard
				}else{

					var event_box = obj.closest('.eventon_list_event');			
					var click_item = event_box.find('.event_description');

					if(click_item.hasClass('open')){
						event_box.removeClass('open');
						click_item.slideUp().removeClass('open');
					}else{
						// accordion
						if( SC.accord == 'yes'){
							cal.find('.eventon_list_event').removeClass('open');
							cal.find('.event_description').slideUp().removeClass('open');
						}
						event_box.addClass('open');
						click_item.slideDown().addClass('open');						
					}
					
					// This will make sure markers and gmaps run once and not multiples			
					if( obj.attr('data-gmstat')!= '1'){	

						obj.attr({'data-gmstat':'1'});						
						obj.evoGenmaps({
							'fnt':2 ,
							'cal':cal,
						});
					}	

					// trigger 
					if( obj.data('runjs')){
						$('body').trigger('evo_load_single_event_content',[ event_id, obj]);
					}	

					$('body').trigger('evo_slidedown_eventcard_complete',[ event_id, obj]);			

					return false;
				}
			}
		});	
		

		// process various lightbox types
		$('body')
		.on('evo_lightbox_processed', function(event, OO, LIGHTBOX){
			if( OO.uid != 'evo_open_eventcard_lightbox') return false;

			var CAL = OO.other_data.CAL;

			LIGHTBOX.addClass('eventcard eventon_events_list');
			LIGHTBOX_content = LIGHTBOX.find('.evolb_content');
			LIGHTBOX_content.attr('class', 'evolb_content '+ OO.other_data.extra_classes );
			LIGHTBOX_content.find('.evopop_top')
					.evoGenmaps({	'fnt':2 ,'cal':OO.other_data.CAL });

			var SC = OO.other_data.SC;
			var obj = OO.other_data.obj;

			fullheight_img_reset(); 

			// update border color and eventtop color
				const evoet_data = OO.other_data.et_data;
				
				bgcolor = evoet_data.bgc;
				bggrad = evoet_data.bggrad;

				// if tiles and eventtop style set to clean
				var show_lightbox_color = SC.tiles == 'yes' && ( SC.eventtop_style == '0' || SC.eventtop_style == '4') ? false: true;
				
				if( (CAL && CAL.hasClass('color') && show_lightbox_color) ||
					(!CAL && show_lightbox_color)

				){
					LIGHTBOX_content.addClass('color');
					LIGHTBOX_content.find('.evopop_top').css({
						'background-color':bgcolor,
						'background-image': bggrad,
					});
				}else{
					LIGHTBOX_content.find('.evopop_top').css({'border-left':'3px solid '+bgcolor});
				}

			// trigger 
			if( obj.data('runjs')){
				$('body').trigger('evo_load_single_event_content',[ SC.event_id, OO.other_data.obj]);
			}
			
			// countdown
			LIGHTBOX_content.find('.evo_countdowner').each(function(){
				obj.removeClass('evo_cd_on');
				obj.evo_countdown();
			});


			
			// RTL
			if( SC.evortl =='yes')	LIGHTBOX.addClass('evortl');

			$('body').trigger('evolightbox_end');	

		})

		// after eventcard content is loaded to lightbox via 3a - @since 4.2.3
			.on('evo_ajax_success_evo_open_eventcard_lightbox', function (event, OO, data){
				
				if( OO.ajaxdata.uid != "load_single_eventcard_content_3a") return false;
				
				LIGHTBOX = $('.evo_lightbox.'+ OO.lightbox_key);
				LIGHTBOX_content = LIGHTBOX.find('.evolb_content');

				CAL = $('body').find('#'+ OO.ajaxdata.calid);

				// generate map
				LIGHTBOX_content.evoGenmaps({	'fnt':2 ,'cal': CAL });


				// countdown
				LIGHTBOX_content.find('.evo_countdowner').each(function(){
					var obj = $(this);
					obj.removeClass('evo_cd_on');
					obj.evo_countdown();
				});

				$('body').trigger('evolightbox_end');		
			})
		;

		// open event as lightbox / U 4.3.3
		function eventon_open_event_lightbox( SC_data, obj, CAL){

		//console.log(SC_data);	


			const cancel_class = ( obj.hasClass('cancel_event')) ? ' cancel_event':'';

			var other_data = {
				extra_classes: 'evo_lightbox_body eventon_list_event evo_pop_body evcal_eventcard event_'+SC_data.event_id +'_'+ SC_data.repeat_interval + cancel_class,
				CAL:CAL,
				obj: obj,
				et_data: obj.find('.evoet_data').data(),// @4.5.5
				SC: SC_data
			};
			
			// generate a random ID for this 
				maximum = 99;
				minimum = 10;
				var randomnumber = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;

			// AJAX via lightbox
			if(SC_data.ux_val == '3a'){

				var new_content = '';
				new_content += '<div class="evo_cardlb" style="padding:10px 10px 0 10px">';
				new_content += '<div style="margin-bottom:20px; width:100%; height:200px" class="evo_preloading"></div>';
				const box = '<div style="display:flex;justify-content: space-between;margin-bottom:10px"><div style="width:40px;height:40px; margin-right:20px" class="evo_preloading"></div> <div style="flex:1 0 auto"> <div class="evo_preloading" style="width:70%; height:20px; margin-bottom:10px"></div><div class="evo_preloading" style="width:100%; height:80px; margin-bottom:10px"></div>  </div> </div>';
				new_content += box + box + box;
				new_content += '</div>';

				
				var data_arg = {};
				data_arg['event_id'] = SC_data.event_id;
				data_arg['ri'] = SC_data.repeat_interval;
				data_arg['SC'] = SC_data;
				data_arg['load_lbcontent'] = true;
				data_arg['action'] = 'eventon_load_single_eventcard_content';
				data_arg['uid'] = 'load_single_eventcard_content_3a';
				data_arg['calid'] = CAL.attr('id');

				
				//reset view to match
					if( data_arg.SC.tile_style == '2') data_arg.SC.eventtop_style = '0';
					data_arg.SC.tile_style = '0';
					data_arg.SC.tile_bg = '0';
					data_arg.SC.tiles = 'no';

				CAL.evo_lightbox_open({
					uid:'evo_open_eventcard_lightbox',
					//uid:'load_single_eventcard_content_3a',
					lbc:'evo_eventcard_'+ randomnumber,
					end:'client',
					content: new_content,
					ajax:'yes',
					ajax_type: 'endpoint',
					ajax_action: 'eventon_load_single_eventcard_content',
					d: 	data_arg,
					other_data: other_data
				});
				

			}else{

				var content = obj.closest('.eventon_list_event').find('.event_description').html();
				var _content = $(content).not('.evcal_close');
								
				CAL.evo_lightbox_open({
					uid:'evo_open_eventcard_lightbox',
					lbc:'evo_eventcard_'+ randomnumber,
					end:'client',
					content: '<div class="evopop_top">'+ obj.html() +'</div>' + content,
					other_data: other_data
				});

				return;
			}						
		}	

	// load eventon event anywhere via lightbox ajax
		$('body').on('click','.eventon_anywhere.evoajax', function(event){
			var obj = $(this);
			var data = obj.data('sc');

			if( data.ev_uxval == '4') return;

			event.preventDefault();

			data['evortl'] = 'no';
			data['event_id'] = data.id;
			data['ux_val'] = '3a';
			data['ajax_eventtop_show_content'] = false;



			//console.log(data);
			eventon_open_event_lightbox( data, obj, false);
		});

	// copy event link
		var evo_card_socialshare_html = '';
		$('body').on('click','.copy.evo_ss', function(event){
			const OBJ = $(this);
			const ROW = OBJ.closest('.evcal_evdata_row');
			var link = decodeURIComponent( OBJ.data('l') );
			navigator.clipboard.writeText( link );



			evo_card_socialshare_html = ROW.html();
			ROW.html( "<p style='display:flex'><i class='fa fa-check marr10'></i> " + $(this).data("t") + "</p>");

			setTimeout(function(){
				ROW.html( evo_card_socialshare_html);
			},3000);
		});

	// call to run google maps on load
		function init_run_gmap_openevc(delay){
			$('.ajde_evcal_calendar').each(function(){
		
				var CAL = $(this);
				var SC = CAL.evo_shortcode_data();

				if( SC === undefined || SC === null) return;

				if( typeof SC !== 'object' ){
					SC = JSON.parse(SC);
				}

				if( 'evc_open' in SC && SC.evc_open =='yes'){

					$(this).find('.desc_trig').each(function(){
						if(delay!='' && delay !== undefined){							
							$(this).evoGenmaps({'fnt':2, 'cal': CAL, 'delay':delay});
						}else{
							$(this).evoGenmaps({'fnt':2,'cal': CAL });							
						}
					});
				}				
			});
		}
	
	// Calendar Interaction
		// event bubbles
		$('.ajde_evcal_calendar.bub').on('mouseover','.eventon_list_event', function(){
			O = $(this);
			LIST = O.closest('.eventon_events_list');
			title = O.find('.evoet_dayblock').data('bub');

			p = O.position();

			LIST.append('<span class="evo_bub_box" style="">'+ title +"</span>");
			B = LIST.find('.evo_bub_box');

			l = p.left;
			t = p.top- B.height() -30;

			// adjust bubble to left if event on right edge
			LM = LIST.width();
			tl = p.left + B.width() + O.width();
			if(   tl > LM){
				l = l - B.width() +O.width()-20;
			}

			B.css({'top':t, 'left':l});

			LIST.find('.evo_bub_box').addClass('show');
		}).on('mouseout',function(){
			B = $(this).find('.evo_bub_box').remove();
		});

	// Click on eventtop items
		$('body').on('click','.evocmd_button', function(event){
			event.preventDefault();
			event.stopPropagation();

			href = $(this).data('href');			
			if( $(this).data('target')=='yes'){
				window.open(href,'_blank');
			}else{
				window.location = href;
			}

		});

	// GO TO TODAY
	// @+ 2.3 @up 2.8
		$('body').on('click','.evo-gototoday-btn', function(){
			var obj = $(this);
			CAL = obj.closest('.ajde_evcal_calendar');			
			var calid = CAL.attr('id');

			CAL.evo_update_cal_sc({F:'fixed_month', V: obj.data('mo')});
			CAL.evo_update_cal_sc({F:'fixed_year', V: obj.data('yr')});
			
			run_cal_ajax( calid,'none','today');
			obj.fadeOut();
		});

		$('body').on('evo_main_ajax_before', function(event, CAL, ajaxtype){
			if(ajaxtype == 'switchmonth')	CAL.find('.evo-gototoday-btn').fadeIn();
		});
		$('body').on('evo_main_ajax_complete', function(event, CAL,ajaxtype, D , data_arg){
			// if focused month and year are same as current month and year hide the current month button
			var N = moment().utc();
			SC = CAL.evo_shortcode_data();

			var SU = parseInt( SC.focus_start_date_range);				
			var M = moment.unix(SU).utc();	

			if( N.format('YYYY M') == M.format('YYYY M')){				
				CAL.find('.evo-gototoday-btn').fadeOut();
			}

			// load countdown timers
			BODY.find('.evo_countdowner').each(function(){
				$(this).evo_countdown();
			});
			
		});

	// MONTH jumper
		$('.ajde_evcal_calendar').on('click','.evo-jumper-btn', function(){
			$(this).parent().siblings().find('.evo_j_container').slideToggle();
		});

		// select a new time from jumper
		$('.evo_j_dates').on('click','a',function(){
			var val = $(this).attr('data-val'),
				type = $(this).parent().parent().attr('data-val'),
				CAL = $(this).closest('.ajde_evcal_calendar');
				SC = CAL.evo_shortcode_data();

			if(type=='m'){ // change month
				CAL.evo_update_cal_sc({F:'fixed_month', V: val });
			}else{
				CAL.evo_update_cal_sc({F:'fixed_year', V: val });
			}

			run_cal_ajax( CAL.attr('id') ,'none','jumper');
			
			// hide month jumper if not set to leave expanded
			if(SC.expj =='no')	container.delay(2000).slideUp();
		});

	// RESET general calendar
		// @U 2.8.9
		function cal_resets(calOBJ){
			calargs = $(calOBJ).find('.cal_arguments');
			calargs.attr('data-show_limit_paged', 1 );

			calOBJ.evo_update_cal_sc({
				F:'show_limit_paged',V:'1'
			});
		}
				
	// change IDs for map section for eventon widgets
		if( $('.ajde_evcal_calendar').hasClass('evcal_widget')){
			cal.find('.evcal_gmaps').each(function(){
				var gmap_id = obj.attr('id');
				var new_gmal_id =gmap_id+'_widget'; 
				obj.attr({'id':new_gmal_id})
			});
		}
	
	// Tab view switcher
		$('body').find('.evo_tab_container').each(function(){
			$(this).find('.evo_tab_section').each(function(){
				if(!$(this).hasClass('visible')){
					$(this).addClass('hidden');
				}
			});
		});
		$('body').on('click','.evo_tab',function(){
			tab = $(this).data('tab');
			tabsection = $(this).closest('.evo_tab_view').find('.evo_tab_container');
			tabsection.find('.evo_tab_section').addClass('hidden').removeClass('visible');
			tabsection.find('.'+tab).addClass('visible').removeClass('hidden');

			$(this).parent().find('.evo_tab').removeClass('selected');
			$(this).addClass('selected');

			$('body').trigger('evo_tabs_newtab_selected',[ $(this)]);
		});

	// layout view changer - legacy
		if($('body').find('.evo_layout_changer').length>0){
			// menu button focus adjust
			$('body').find('.evo_layout_changer').each(function(item){
				if($(this).parent().hasClass('boxy')){
					$(this).find('.fa-th-large').addClass('on');
				}else{
					$(this).find('.fa-reorder').addClass('on');
				}
			});

			// interaction
			$('.evo_layout_changer').on('click','i',function(){
				const CAL = $(this).closest('.ajde_evcal_calendar');
				TYPE = $(this).data('type');
				$(this).parent().find('i').removeClass('on');
				$(this).addClass('on');

				//console.log(TYPE);
				
				if(TYPE=='row'){
					CAL.attr('class','ajde_evcal_calendar');
					// set tile colors
					CAL.find('.eventon_list_event').each(function(){
						$(this).find('.desc_trig').css('background-color',  '');
						$(this).find('.desc_trig_outter').css('background-color',  '');
					});
				}else if(TYPE =='bar'){
					CAL.attr('class','ajde_evcal_calendar  box_2 sev cev');
					
					// set tile colors
					CAL.find('.eventon_list_event').each(function(){
						const color = $(this).data('colr');
						$(this).find('.desc_trig').css('background-color',  color);
					});
				}else{

					// set tile colors
					CAL.find('.eventon_list_event').each(function(){
						const color = $(this).data('colr');
						$(this).find('.desc_trig_outter').css('background-color',  color);
					});

					CAL.attr('class','ajde_evcal_calendar boxy boxstyle0 box_2');
				}				
			});
		}
	
	// v4.0 view switcher
		$('body').on('click', '.evo_cal_view_switcher_cur',function(){
					$(this).parent().toggleClass('SH');
			})
			.on('click', '.evo_vSW',function(){
				O = $(this);
				var DATA = O.data('d');
				if(O.hasClass('focusX')) return;
								
				CAL = O.closest('.ajde_evcal_calendar');

				// remove other additions from other views
				CAL.find('.evoADDS').fadeOut().delay(200).queue(function(){
					$(this).remove();
				});

				var SC = CAL.evo_shortcode_data();
				var reload_cal_data = false;
				
				// Create date object
					var DD = new Date(SC.fixed_year,SC.fixed_month -1 , SC.fixed_day, 0,0,0 );
					DD.setUTCHours(0);
					DD.setUTCFullYear( SC.fixed_year );
					DD.setUTCMonth( SC.fixed_month -1 );
					DD.setUTCDate( SC.fixed_day );

				// switch to normal
					O.siblings('.evo_vSW').removeClass('focusX');					
					O.addClass('focusX');
					CAL.find('.evo_cal_view_switcher_cur').html( O.html() );
					O.closest('.evo_cal_view_switcher').removeClass('SH');

				// calendar class toggling
					O.siblings('.evo_vSW').each(function(){
						var _d = $(this).data('d');
						if( _d && 'c' in _d )	CAL.removeClass( _d['c'] ); // remove other cls
					});
					if( DATA && 'c' in DATA)	CAL.addClass( DATA.c );

				$('body').trigger('evo_vSW_clicked_before_ajax', [ O, CAL, DD, reload_cal_data]);
				
				// switching to calendars that need 1 month of events
				if( !(O.hasClass('evowv')) ){

					DD.setUTCDate( 1 );

					if( SC.focus_start_date_range != Math.floor(DD.getTime()/1000) )
						reload_cal_data = true;

					CAL.evo_update_cal_sc({F:'focus_start_date_range',V: Math.floor(DD.getTime()/1000) });

					DD.setUTCMonth( DD.getUTCMonth() + 1);
					DD.setUTCDate( -1);

					CAL.evo_update_cal_sc({F:'focus_end_date_range', 
						V: Math.floor(DD.getTime()/1000) });

					CAL.evo_update_cal_sc({F:'calendar_type', V: 'default'});

					// treating events list based on dif preferences
					// vals el_visibility = hide_events, show_events
					if( 'el_visibility' in DATA){
						el_visibility = DATA.el_visibility;

						if( el_visibility =='show_events') CAL.find('.eventon_list_event').show();
						if( el_visibility =='hide_events') CAL.find('.eventon_list_event').hide();
						if( el_visibility =='hide_list') CAL.find('#evcal_list').addClass('evo_hide');
						if( el_visibility =='show_all'){
							CAL.find('#evcal_list').removeClass('evo_hide');
							CAL.find('.eventon_list_event').show();
						} 
					}

					
					//CAL.find('.eventon_list_event').show();

					// run ajax to load new events in the range
					if( reload_cal_data ){
						$('body').trigger('evo_run_cal_ajax',[CAL.attr('id'),'none','filering']);
					}
				}

							
				$('body').trigger('evo_vSW_clicked', [ O, CAL, DD, reload_cal_data]);

			});
	
	// SORTING & FILTERING
		// display sort section
		$('body').on('click','.evo-filter-btn', function(){
			CAL = $(this).closest('.ajde_evcal_calendar');
			line = CAL.find('.eventon_sorting_section');
			if(line.is(':visible')){
				line.hide();
				$(this).removeClass('show');
			}else{
				line.show();
				$(this).addClass('show');
			}
			// hide sort menu
			CAL.find('.eventon_sort_line').hide();
		});	
		
		// SORTing
			$('body').on('click','.evo-sort-btn',function(){
				line = $(this).find('.eventon_sort_line');
				if(line.is(':visible')){
					line.hide();
					$(this).removeClass('show');
				}else{
					line.show();
					$(this).addClass('show');
				}
			});
		
			// update calendar based on the sorting selection
				$('body').on('click', '.evo_sort_option',function(){
					O = $(this);
					var CAL = O.closest('.ajde_evcal_calendar');
					var sort_by = O.data('val');
					
					// update new values everywhere
					CAL.evo_update_cal_sc({F:'sort_by',V:sort_by});

					O.parent().find('p').removeClass('select');
					O.addClass('select');	

					run_cal_ajax(CAL.attr('id'),'none','sorting');						
				});		
		
		// filtering section open and close menu
			$('body').on('click','.filtering_set_val',function(){
				// close sorting
					sortSelect = $(this).closest('.eventon_sorting_section').find('.evo_srt_options');
					if(sortSelect.is(':visible') == true) sortSelect.fadeToggle();

				var obj = $(this);
				var current_Drop = obj.siblings('.eventon_filter_dropdown');
				var current_drop_pare = obj.closest('.eventon_filter');

				current_drop_pare.siblings('.eventon_filter').find('.eventon_filter_dropdown').each(function(){
					if($(this).is(':visible')== true ){
						$(this).hide();
						$(this).siblings('p').removeClass('show');
					}				
				});

				if(current_Drop.is(':visible')== true){
					obj.siblings('.eventon_filter_dropdown').fadeOut('fast');	
					obj.removeClass('show');	
				}else{
					obj.siblings('.eventon_filter_dropdown').fadeIn('fast');
					obj.addClass('show');
				}			
			});	
		
		// selection on filter dropdown list
			$('body').on('click','p.evo_filter_val',function (){

				CAL = $(this).closest('.ajde_evcal_calendar');	
				var new_filter_val = $(this).data('filter_val'),
					O = $(this),
					filter_section = $(this).closest('.eventon_filter_line'),
					filter = $(this).closest('.eventon_filter'),
					filter_current_set_val = filter.data('filter_val');
					filter_def_val = filter.data('fldef');

				const filter_set_val = filter.find('.filtering_set_val');
				const filter_btn = CAL.find('.evo-filter-btn');

				const arg_filter_type = filter_section.data('filter_type');

				FILTER_DROPDOWN = $(this).parent();
				CAL = $(this).closest('.ajde_evcal_calendar');	


				// for filter values with checkboxes
				if(arg_filter_type == 'select'){	
					val = '';


					// NOT all
					if( new_filter_val != 'all'){
						O.parent().find('p.all').removeClass('select');
						O.toggleClass('select');	

						var unselect_count = 0;
						O.parent().find('p').each(function(){
							if( $(this).hasClass('select')){
								val += $(this).data('filter_val')+',';
							}else{
								if(!$(this).hasClass('all')) unselect_count++;
							}
						});	

						// all selected
						if(unselect_count == 0){
							O.parent().find('p.all').addClass('select');
							val = 'all';
						}	

						// remove comma at the end
						val = ( val.slice(-1) == ',')? val.slice(0,-1): val;

						// if all field is not visible; nothing selected = all
						if(val == '' && O.parent().find('p.all').length == 0) val='all';

					// == all
					}else{ 
						if( O.hasClass('select')){
							O.parent().find('p').removeClass('select');
						}else{
							O.parent().find('p').addClass('select');
							val = 'all';
						}						
					}
					
					filter.data('filter_val',val); // v 2.7.4
				
				// filter_type = default
				}else{					
					if( new_filter_val == 'all'){
						// if all is already selected
						if( O.hasClass('select')){
							new_filter_val = 'NOT-all';
							O.parent().find('p').removeClass('select');
						}else{
							O.parent().find('p').addClass('select');
						}						
					}else{
						O.parent().find('p').removeClass('select');
						O.addClass('select');
					}

					filter.data('filter_val',new_filter_val);
				}

				var set_new_filter_val = filter.data('filter_val');


				// display filter informer	
					if( !( CAL.hasClass('flhi') ) ){
						//console.log( CAL.attr('class'));
						//console.log(set_new_filter_val+' '+ filter_def_val);
						if( set_new_filter_val == filter_def_val){
							filter_set_val.removeClass('set');						
						}else{
							filter_set_val.addClass('set');
						}

						var count_filters = filter_section.find('.filtering_set_val.set').length;

						if( count_filters > 0){
							filter_btn.find('em').html( count_filters ).addClass('o');
							CAL.find('.evo_filter_clear').addClass('show');
						}else{
							filter_btn.find('em').removeClass('o');		
							CAL.find('.evo_filter_clear').removeClass('show');					
						}
					}

					

				// if select filter type 
				if(arg_filter_type == 'select') return;

				
				// For non checkbox select options
					if(filter_current_set_val == new_filter_val){
						$(this).parent().fadeOut();
					}else{					
						cal_resets( CAL );
								
						// make changes					
						filter.data('filter_val', new_filter_val);

						CAL.evo_update_sc_from_filters();					

						run_cal_ajax( CAL.attr('id') ,'none','filering');
											
						// reset the new values				
						var new_filter_name = $(this).html();
						
						O.parent().fadeOut();
						filter_set_val.removeClass('show');
					}
			});
			
			// apply filters via button to the calendar
				$('.eventon_filter_dropdown').on('change','input',function(event){
					FILTER = $(this).closest('.eventon_filter');

					val = '';
					FILTER.find('input').each(function(){
						val = ($(this).is(':checked'))? val + $(this).data('filter_val') +',': val;
					});
					val = (val=='')? '':val; // v2.5.2
					FILTER.data('filter_val',val); // v 2.7.4
				});
			// apply filters
				$('body').on('click','.evo_filter_submit',function(){
					// fadeout any open filter dropdowns
						$(this).closest('.eventon_filter_line').find('.eventon_filter_dropdown').fadeOut();
						$(this).closest('.eventon_filter_line').find('.filtering_set_val').removeClass('show');

					CAL = $(this).closest('.ajde_evcal_calendar');	
					cal_resets( CAL);

					CAL.evo_update_sc_from_filters();	// update shortcode from filters
					
					run_cal_ajax(CAL.attr('id'),'none','filering');
				});

			// CLEAR filters - @4.2 u @4.4
			// reset back to default on page load values
				$('body').on('click','.evo_filter_clear',function(){
					CAL = $(this).closest('.ajde_evcal_calendar');

					CAL.find('.eventon_filter').each(function(){
						const filter = $(this);
						const NOT_values = filter.data('notvals');
						const IN_values = filter.data('invals');

						$(this).find('.filtering_set_val').removeClass('set');
						CAL.find('.evo-filter-btn em').removeClass('o');

						
						if( IN_values == ''){
							
						}else if(IN_values == 'all'){
							filter.find('.evo_filter_val').addClass('select');
						}else if( IN_values != ''){
							filter.find('.evo_filter_val').removeClass('select');
							$.each( IN_values, function( index, value){
								filter.find('.evo_filter_val[data-filter_val="'+ value +'"]')
											.addClass('select');
							});
						}

						// set the default filter value
						var default_value = '';
						$.each(NOT_values, function(index, value){
							default_value += 'NOT-'+ value + ',';
						});
						$.each(IN_values, function(index, value){
							default_value +=  value + ',';
						});

						// remove last comma from the string @since 4.4
							default_value = default_value.replace(/,\s*$/, "");

						filter.data('filter_val', default_value);

					});

					$(this).removeClass('show');

					// update shortcode and run new ajax for events
					CAL.evo_update_sc_from_filters();					
					run_cal_ajax(CAL.attr('id'),'none','filering');
				});
		
	// CAL BODY Listeners
		$('body')
			// Show more events on list
				.on('click','.evoShow_more_events',  function(){
					CAL = $(this).closest('.ajde_evcal_calendar');
					SC = CAL.evo_shortcode_data();

					OBJ = $(this);

					// redirect to an external link 
						if(SC.show_limit_redir !== ''){
							window.location = SC.show_limit_redir;	return false;
						}

					// ajax pagination
					if( SC.show_limit_ajax =='yes'){
						CURRENT_PAGED = parseInt(SC.show_limit_paged);				
						CAL.evo_update_cal_sc({F:'show_limit_paged', V: CURRENT_PAGED+1});
						run_cal_ajax( CAL.attr('id'), 'none','paged');

					}else{
						var event_count = parseInt( SC.event_count );
						
						var eventList = OBJ.parent();
						var allEvents = eventList.find('.eventon_list_event').length;

						var currentShowing = eventList.find('.eventon_list_event:visible').length;

						for(x=1; x<=event_count ; x++ ){
							var inde = currentShowing+x-1;
							eventList.find('.eventon_list_event:eq('+ inde+')').slideDown();
						}

						// hide view more button
						if(allEvents > currentShowing && allEvents<=  (currentShowing+event_count)){
							$(this).fadeOut();
						}
					}		

				})
			// MONTH switch
				.on('click','.evcal_btn_prev', function(){
					var cal_id = $(this).closest('.ajde_evcal_calendar').attr('id');
					var direction = $(this).closest('.ajde_evcal_calendar').hasClass('evortl')? 'next': 'prev';
					run_cal_ajax(cal_id, direction ,'switchmonth');
				})
				.on('click','.evcal_btn_next',function(){					
					var cal_id = $(this).closest('.ajde_evcal_calendar').attr('id');			
					var direction = $(this).closest('.ajde_evcal_calendar').hasClass('evortl')? 'prev': 'next';
					run_cal_ajax(cal_id, direction ,'switchmonth');
				})

			// JUMPER switch
				.on('calendar_month_changed',function(event, CAL){
					SC = CAL.evo_shortcode_data();

					var O = CAL.find('.evo_j_container');
					O.find('.evo_j_months a').removeClass('set');
					O.find('.evo_j_months a[data-val="'+ SC.fixed_month +'"]').addClass('set');

					O.find('.evo_j_years a').removeClass('set');
					O.find('.evo_j_years a[data-val="'+ SC.fixed_year +'"]').addClass('set');

					// show go to today 
					B = CAL.find('.evo-gototoday-btn');
					if( SC.fixed_month != B.data('mo') || SC.fixed_year != B.data('yr')){
						B.show();
					}
				})
			
			// show more/less event details
				.on('click','.evobtn_details_show_more',function(){		
					control_more_less( $(this));	
				})
			// refresh now calendar
				.on('runajax_refresh_now_cal',function(e, OBJ, nonce){

					const section = OBJ.closest('.evo_eventon_live_now_section');
					const CAL = section.find('.ajde_evcal_calendar').eq(0);

					var dataA = {
						nonce: nonce,
						other: OBJ.data(),
						SC: CAL.evo_shortcode_data()
					};

					

					$.ajax({
						beforeSend: function(){
							section.addClass('evoloading');
						},
						type: 'POST',url: get_ajax_url('eventon_refresh_now_cal'), data: dataA,dataType:'json',
						success:function(data){
							if( data.status == 'good'){
								section.html( data.html);

								$('body').trigger('evo_refresh_designated_elm',[ OBJ, 'evo_vir_data']);
							}

						},complete:function(data){
							section.removeClass('evoloading');

							BODY.find('.evo_countdowner').each(function(){
								$(this).evo_countdown();
							});
						}
					});
				})

			// refresh event top
				.on('runajax_refresh_eventtop',function(e, OBJ, nonce){

				})

			// click on no events - @v 4.2
				.on('click','.evo_no_events_btn', function (e){
					BODY.trigger('click_on_no_event_btn', [$(this) ] );
				})
				;
		

	// PRIMARY hook to get content	
		// MAIN AJAC for calendar events v2.8
		function run_cal_ajax( cal_id, direction, ajaxtype){
			
			// identify the calendar and its elements.
			var CAL = ev_cal = $('#'+cal_id); 

			// check if ajax post content should run for this calendar or not			
			if(CAL.attr('data-runajax')!='0'){

				// category filtering for the calendar
				var cat = CAL.find('.evcal_sort').attr('cat');

				// reset paged values for switching months
				if(ajaxtype=='switchmonth'){
					CAL.find('.cal_arguments').attr('data-show_limit_paged',1);
					CAL.evo_update_cal_sc({F:'show_limit_paged', V: '1'});
				}	

				SC = CAL.evo_shortcode_data();

				$('body').trigger('evo_main_ajax_before', [CAL, ajaxtype, direction, SC]);		

				var data_arg = {
					//action: 		'eventon_get_events',
					direction: 		direction,
					shortcode: 		SC,
					ajaxtype: 		ajaxtype,
					nonce: 			evo_general_params.n
				};	

				EVENTS_LIST = ev_cal.find('.eventon_events_list');

				$.ajax({
					beforeSend: function(){
						ev_cal.addClass('evo_loading');
						if(ajaxtype != 'paged')	EVENTS_LIST.slideUp('fast');

						if(ajaxtype == 'paged'){
							txt = EVENTS_LIST.find('.evoShow_more_events').html();
							EVENTS_LIST.find('.evoShow_more_events').html('. . .').data('txt',txt);
						}
						ev_cal.evo_loader_animation();					
					},
					type: 'POST', url: get_ajax_url('eventon_get_events'),data: data_arg,dataType:'json',
					success:function(data){
						if(!data) return false;
						if(ajaxtype == 'paged'){	
							EVENTS_LIST.find('.evoShow_more_events').remove();
							EVENTS_LIST.find('.clear').remove();


							EVENTS_LIST.append( data.html + "<div class='clear'></div>");

							// hide show more events if all events loaded
							var events_in_list = EVENTS_LIST.find('.eventon_list_event').length;
							if( 'total_events' in data && data.total_events == events_in_list){
								EVENTS_LIST.find('.evoShow_more_events').hide();
							}	

							// for month lists duplicate headers // @+2.8.1
							var T = {};
							EVENTS_LIST.find('.evcal_month_line').each(function(){
								d = $(this).data('d');
								if( T[d]) 
									$(this).remove();
								else
									T[d] = true;
							});

							var T = {};
							EVENTS_LIST.find('.sep_month_events').each(function(){
								d = $(this).data('d');
								if( T[d]){
									var H = $(this).html();
									EVENTS_LIST.find('.sep_month_events[data-d="'+d+'"]').append( H );
									$(this).remove();
								}else{T[d] = true;}
							});
							
						}else{
							EVENTS_LIST.html(data.html);
						}
						
						// update calednar month title
						animate_month_switch(data.cal_month_title, ev_cal.find('.evo_month_title'));
							
						// update calendar shortcode values after ajax
						ev_cal.evo_update_all_cal_sc({SC: data.SC});

						// update events list to calendar footer data
						ev_cal.find('.evo_cal_events').data('events', data.json);

						$('body').trigger('calendar_month_changed',[CAL]);
						
						$('body').trigger('evo_main_ajax_success', [CAL, ajaxtype, data, data_arg]);
															
					},complete:function(data){
						ev_cal.evo_loader_animation({direction:'end'});

						// show events list events if not set to hide on load
						if(! EVENTS_LIST.hasClass('evo_hide')) EVENTS_LIST.delay(300).slideDown('slow');
						
						ev_cal.evoGenmaps({'delay':400});
						init_run_gmap_openevc(600);
						fullheight_img_reset(cal_id);

						// scroll to top of calendar
						$('html, body').animate({
		                    //scrollTop: ev_cal.offset().top - 20
		                }, 500);

						// pluggable
						$('body').trigger('evo_main_ajax_complete', [CAL, ajaxtype, data.responseJSON , data_arg]);
						ev_cal.removeClass('evo_loading');
					}
				});
			}			
		}

		$('body').on('evo_run_cal_ajax',function(event,cal_id, direction, ajaxtype){
			run_cal_ajax( cal_id, direction, ajaxtype);
		});

		// deprecated bridge function for sortby value 
		function ajax_post_content(sortby, cal_id, direction, ajaxtype){
			run_cal_ajax( cal_id, direction, ajaxtype);
		}

	// subtle animation when switching months
		function animate_month_switch(new_data, title_element){			
			var current_text = title_element.html();
			var CAL = title_element.closest('.ajde_evcal_calendar');

			
			title_element.html(new_data);
			return;

			/*  - @v 4.2
			var hei = title_element.height();
			var wid= title_element.width();
			
			
			title_element.html("<span style='position:absolute; width:"+wid+"; height:"+hei+" ;'>"+current_text+"</span><span style='position:absolute; display:none;'>"+new_data+"</span>").width(wid);

			var LC_w = title_element.find('span:last-child').width()+5;

			console.log(title_element);
						
			title_element.animate({width: LC_w},2000);
			title_element.find('span:first-child').fadeOut(800); 
			title_element.find('span:last-child').fadeIn(800, function(){
				title_element.html(new_data);
			});
			*/
			
		}
	
	// actual animation/function for more/less button
		function control_more_less(obj){
			var content = obj.attr('content');
			var current_text = obj.find('.ev_more_text').html();
			var changeTo_text = obj.find('.ev_more_text').attr('data-txt');
			const cell = obj.closest('.evcal_evdata_cell');

				
			// show more
			if(content =='less'){			
				
				obj.closest('.evcal_evdata_cell').removeClass('shorter_desc');
						

				obj.attr({'content':'more'});
				obj.find('.ev_more_arrow').removeClass('ard');
				obj.find('.ev_more_text').attr({'data-txt':current_text}).html(changeTo_text);
				
			}else{
				obj.closest('.evcal_evdata_cell').addClass('shorter_desc');
				
				obj.attr({'content':'less'});
				obj.find('.ev_more_arrow').addClass('ard');
				obj.find('.ev_more_text').attr({'data-txt':current_text}).html(changeTo_text);
			}
		}
		
	// expand and shrink featured image		
		$('body').on('click','.evcal_evdata_img',function(){
			if(!$(this).hasClass('evo_noclick')){		
				feature_image_expansion($(this), 'click');
			}
		});		
	
	// featured image height processing
		function feature_image_expansion(image, type){
			img = image;
			
			var img_status = img.attr('data-status');
			var img_style = img.attr('data-imgstyle');
			
			// if image already expanded
			if(img_status=='open' ){
				img.attr({'data-status':'close'}).css({'height':''});			
			}else{	
				var img_full_height = parseInt(img.attr('data-imgheight'));
				var cal_width = parseInt(img.closest('.ajde_evcal_calendar').width());
					cal_width = (cal_width)? cal_width: img.width();
				var img_full_width = parseInt(img.attr('data-imgwidth'));


				// show at minimized height
				if(img_style=='100per'){
					relative_height = img_full_height;
				}else if(img_style=='full'){
					relative_height = parseInt(img_full_height * (cal_width/img_full_width)) ;
				}else{
					// minimized version
					if(type=='click'){
						relative_height = parseInt(img_full_height * (cal_width/img_full_width));
						relative_height = (relative_height)? relative_height: img_full_height;
						
						relative_height = parseInt((cal_width * img_full_height) /img_full_width);
						
						//console.log(relative_height+ ' '+img_full_height+' '+cal_width);

					}else{
						relative_height = img.attr('data-minheight');
					}					
				}
				
				// when to set the status as open for images
				if(img_status=='' && img_style=='minmized'){
					img.attr({'data-status':'close'}).removeClass('expanded');
				}else{
					img.attr({'data-status':'open'}).addClass('expanded');
				}
				img.css({'height':relative_height});
			}			
		}

	// reset featured images based on settings
		function fullheight_img_reset(calid){
			if(calid){
				$('#'+calid).find('.eventon_list_event .evo_metarow_fimg').each(function(){
					feature_image_expansion($(this));
				});
			}else{
				$('.evo_metarow_fimg').each(function(){					
					feature_image_expansion($(this));					
				});
			}
		}
			
	// treatments for calendar events upon load
		function treat_events(calid){
			if(calid!=''){
				if(is_mobile()){
					$('#'+calid).find('.evo_metarow_getDr form').attr({'target':'_self'});
				}
			}
		}

		// if mobile check
		function is_mobile(){
			return ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )? true: false;
		}

	// edit event button redirect
		$('body').on('click','.editEventBtnET', function(event){
			event.stopPropagation();
			href = $(this).attr('href');
			window.open(href);
		});

	// repeat events series
	// @u 2.6.9
		$('body').on('click','.evo_repeat_series_date',function(){
			if( $(this).parent().data('click') ){
				URL =  $(this).data('l');
				window.location = URL;
			}
		});

	// Tax Archive Content
		// organizer links from eventtop @4.5
		$('body').on('click','.evo_org_clk_link',function(){
			window.open( $(this).data('link') , "_blank");
		});

	// event location archive card page
		// @u 2.8.6
		$('body').find('.evo_location_map').each(function(){			
			$(this).evo_load_gmap();
		});

		// on event card lightbox load -> organizer details @since 4.2
		$('body').on('evo_ajax_complete_eventon_get_tax_card_content', function(event,  OO){
			
			LB = $('body').find('.'+ OO.lightbox_key);

			if( LB.find('.evo_trigger_map').length == 0 ) return;
			map_id_elm = LB.find('.evo_trigger_map');
			
			map_id_elm.evo_load_gmap();

			// run countdown timers
			LB.find('.evo_countdowner').each(function(){
				$(this).evo_countdown();
			});
			
		});
		
	// SINGLE EVENTS
		// Loading single event json based content
			$('body').on('evo_load_single_event_content', function(event, eid, obj){
				var ajaxdataa = {};
				ajaxdataa['eid'] = eid;
				ajaxdataa['nonce'] = the_ajax_script.postnonce;	

				// pass on other event values
				if(obj.data('j')){
					$.each(obj.data('j'), function(index,val){
						ajaxdataa[ index] = val;
					});
				}			
				
				$.ajax({
					beforeSend: function(){ 	},	
					url:	get_ajax_url('eventon_load_event_content'),
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){
						$('body').trigger('evo_single_event_content_loaded', [data, obj]);
					},complete:function(){ 	}
				});
			});
	
		if(is_mobile()){
			if($('body').find('.fb.evo_ss').length != 0){
				$('body').find('.fb.evo_ss').each(function(){
					obj = $(this);
					obj.attr({'href':'http://m.facebook.com/sharer.php?u='+obj.attr('data-url')});
				});
			}
		}

		// on single event page
		if($('body').find('.evo_sin_page').length>0){
			$('.evo_sin_page').each(function(){
				$('body').trigger('evo_load_single_event_content',[ $(this).data('eid'), $(this)]);
				$(this).find('.desc_trig ').attr({'data-ux_val':'none'});
			});
		}
		
		// Single events box
			// Click on single event box
				$('.eventon_single_event').on('click', '.evcal_list_a',function(event){
					var obj = $(this);				
					var CAL = obj.closest('.ajde_evcal_calendar');
					var SC = CAL.evo_shortcode_data();

					event.preventDefault();

					// open in event page
					if(SC.ux_val == 4){ 
						var url = obj.parent().siblings('.evo_event_schema').find('[itemprop=url]').attr('href');
						window.location.href= url;
					}else if(SC.ux_val == '2'){ // External Link
						var url = SC.exturl;
						window.location.href= url;
					}else if(SC.ux_val == 'X'){ // do not do anything
						return false;
					}
				});
			// each single event box
				$('body').find('.eventon_single_event').each(function(){
					var _this = $(this);

					var CAL = _this.closest('.ajde_evcal_calendar');
					var SC = CAL.evo_shortcode_data();	
					var evObj = CAL.find('.eventon_list_event');									

					// show expanded eventCard
					if( SC.expanded =='yes'){
						_this.find('.evcal_eventcard').show();
						var idd = _this.find('.evcal_gmaps');						

						// close button
						_this.find('.evcal_close').parent().css({'padding-right':0});
						_this.find('.evcal_close').hide();

						//console.log(idd);
						var obj = _this.find('.desc_trig');

						// Google Map
						obj.evoGenmaps({
							'fnt':2, 
							'cal':CAL,
						});

						// mark as eventcard open @since 4.4
						evObj.find('.event_description').addClass('open');

					// open eventBox and lightbox	
					}else if(SC.uxval =='3'){

						var obj = _this.find('.desc_trig');
						// remove other attr - that cause to redirect
						obj.removeAttr('data-exlk').attr({'data-ux_val':'3'});
					}

					// show event excerpt
					var ev_excerpt = CAL.find('.event_excerpt').html();
					
					if(ev_excerpt!='' && ev_excerpt!== undefined && SC.excerpt =='yes' ){
						var appendation = '<div class="event_excerpt_in">'+ev_excerpt+'</div>'
						evObj.append(appendation);
					}

					// trigger support @since 4.4
					var obj = evObj.find('.desc_trig');
					var event_id = evObj.data('event_id');


					$('body').trigger('evo_slidedown_eventcard_complete',[ event_id, obj]);	
				});

	// HELPER items script
		// yes no button		
			$('body').on('click','.ajde_yn_btn ', function(event){

				// stop this code from working on wp-admin
				if($('body').hasClass('wp-admin')) return false; 
				
				var obj = $(this);
				var afterstatement = obj.attr('afterstatement');
					afterstatement = (afterstatement === undefined)? obj.attr('data-afterstatement'): afterstatement;	
				var uid = '';

				// yes
				if(obj.hasClass('NO')){					
					obj.removeClass('NO');
					obj.siblings('input').val('yes');

					// afterstatment
					if(afterstatement!=''){
						var type = (obj.attr('as_type')=='class')? '.':'#';
						if( obj.data('uid') !== undefined) uid = obj.data('uid');
						$(type+ afterstatement).slideDown('fast');						
					}

				}else{//no
					obj.addClass('NO');
					obj.siblings('input').val('no');
					
					if(afterstatement!=''){
						var type = (obj.attr('as_type')=='class')? '.':'#';
						$(type+ afterstatement ).slideUp('fast');
					}
				}
			});

// Search Scripts
	// Enter key detection for pc
		$.fn.evo_enterKey = function (fnc) {
		    return this.each(function () {
		        $(this).keypress(function (ev) {
		            var keycode = (ev.keyCode ? ev.keyCode : ev.which);
		            if (keycode == '13') {
		                fnc.call(this, ev);
		            }
		        })
		    })
		}
		
	$('body').on('click','.evo-search',function(){
		var section = $(this).closest('.calendar_header');
		var item = section.find('.evo_search_bar');

		item.slideToggle('fast', function(){
			if(item.is(':visible'))  item.find('input').focus();
		});
	});

	// Submit search from search box u 4.5.8
		$('body').on('click','.evo_do_search',function(){
			do_search_box( $(this) );
		});

		// dynamic enter key press on the search input field @4.2
		$(".evo_search_field").evo_enterKey(function () {
			do_search_box( $(this).siblings('.evo_do_search') );
		});
			

		// primary search function
		function do_search_box(OBJ){
			SearchVal = OBJ.closest('.evosr_search_box').find('input').val();
			Evosearch = OBJ.closest('.EVOSR_section');
			OBJ.closest('.evo_search_entry').find('.evosr_msg').hide();

			//console.log(SearchVal);

			if( SearchVal === undefined || SearchVal == ''){
				OBJ.closest('.evo_search_entry').find('.evosr_msg').show();
				return false;
			}

			$('body')
			.on('evo_ajax_beforesend_evo_get_search_results', function (event, uid){
				Evosearch.find('.evo_search_results_count').hide();
				Evosearch.addClass('searching');
			}).on('evo_ajax_complete_evo_get_search_results', function (event, uid){
				Evosearch.removeClass('searching');
			}).on('evo_ajax_success_evo_get_search_results', function (event, uid, data){
				Evosearch.find('.evo_search_results').html( data.content);

				if(Evosearch.find('.no_events').length==0){
					// find event count
					Events = Evosearch.find('.evo_search_results').find('.eventon_list_event').length;
					Evosearch.find('.evo_search_results_count span').html( Events);
					Evosearch.find('.evo_search_results_count').fadeIn();
				}
			});

			var ajax_results = OBJ.evo_admin_get_ajax({
				'ajaxdata': {
					//action: 		'eventon_search_evo_events',
					search: 		SearchVal,
					shortcode:  	Evosearch.find('span.data').data('sc'),
					nonce: 			evo_general_params.n				
				},
				ajax_type:'endpoint',
				ajax_action:'eventon_search_evo_events',
				'uid':'evo_get_search_results',
				end: 'client'
			});

		}

	// submit search from calendar
		$('body').on('click','.evosr_search_btn',function(){	
			search_within_calendar( $(this).siblings('input') );		});
		$(".evo_search_bar_in input").evo_enterKey(function () {	
			search_within_calendar( $(this) );		});

		function search_within_calendar(obj){

			var ev_cal = obj.closest('.ajde_evcal_calendar');
			
			ev_cal.evo_update_cal_sc({F:'show_limit_paged',V: '1' });
			ev_cal.evo_update_cal_sc({F:'s',V: obj.val() });

			run_cal_ajax( ev_cal.attr('id'),'none','search');
			
		   	return false;	
		}	

		// reset search field @since 4.5
		$('body').on('evo_main_ajax_complete',function(event,CAL, ajaxtype, responseJSON , data_arg ){

			if(ajaxtype == 'search' ){
				if( data_arg.shortcode['s'] != '' ){
					CAL.find('.evosr_search_clear_btn').addClass('show');
				}					
			}
		}).on('click','.evosr_search_clear_btn',function(event){
			event.preventDefault();
			const obj = $(this);
			var ev_cal = obj.closest('.ajde_evcal_calendar');
			ev_cal.evo_update_cal_sc({F:'s',V: '' });
			run_cal_ajax( ev_cal.attr('id'),'none','search');

			obj.removeClass('show');
			obj.siblings('input').val('');
		});


	// More event images interaction
		$('body').on('click','.evo_event_more_img',function(){
			var O = $(this);
			var eCARD = O.closest('.evcal_eventcard');
			var fIMG = O.find('img');
			var fURL = fIMG.data('f');

			O.parent().find('span').removeClass('select'); O.addClass('select');

			eCARD.find('img.evo_event_main_img').attr({'src': fURL});

			relIMG = eCARD.find('.evo_metarow_fimg');
			relIMG.css('background-image', 'url('+ fURL +')' );
			relIMG.data('imgheight', fIMG.data('h'));
		});	

// supportive
	// ajax url function  @u 4.5.5
		function get_ajax_url(action){
			var ajax_type = 'endpoint';
			if('ajax_method' in evo_general_params ) ajax_type = evo_general_params.ajax_method;
			return $('body').evo_get_ajax_url({a:action, type: 	ajax_type });
		}
	// handlebar additions
		function handlebar_additional_arguments(){
			Handlebars.registerHelper('ifE',function(v1, options){
				return (v1 !== undefined && v1 != '' && v1)
                    ? options.fn(this)
                    : options.inverse(this);
			});

			Handlebars.registerHelper('ifEQ',function(v1, v2, options){
				return ( v1 == v2)? options.fn(this): options.inverse(this);
			});
			Handlebars.registerHelper('ifNEQ',function(v1, v2, options){
				return ( v1 != v2)? options.fn(this): options.inverse(this);
			});
			Handlebars.registerHelper('BUStxt',function(V, options){	
				if( !( V in BUS.txt) ) return V;
				return BUS.txt[V];
			});
			Handlebars.registerHelper('GetDMnames',function(V, U, options){				
				return BUS.dms[U][ V ];
			});
			// get total of increments
			Handlebars.registerHelper('forAdds',function(count, add_val, options){	
				O = '';
				for(x=1; x<= count; x++){	O += add_val;	}			
				return O;
			});
			Handlebars.registerHelper('GetEvProp',function(EID, PROP, CALID){
				EID = EID.split('-');	
				EV = $('#'+ CALID).find('.evo_cal_events').data('events');
				
				var O = '';
				$.each(EV, function(i,d){
					if( d.ID == EID[0] && d.ri == EID[1]){
						if( !(PROP in d.event_pmv)) return;
						O = d.event_pmv[PROP][0];
					}
				});
				return O;
			});
			Handlebars.registerHelper('GetEvV',function(EID, PROP, CALID){
				EID = EID.split('-');	
				EV = $('#'+ CALID).find('.evo_cal_events').data('events');
				
				var O = '';
				$.each(EV, function(i,d){
					if( d.ID == EID[0] && d.ri == EID[1]){
						O = d[PROP];
					}
				});
				return O;
			});
			Handlebars.registerHelper('COUNT',function( V){		
				return Object.keys(V).length;
			});
			Handlebars.registerHelper('CountlimitLess',function( AR, C,options){		
				var L= Object.keys(AR).length;
				return ( L < C)? options.inverse(this): options.fn(this);
			});
			Handlebars.registerHelper('ifCOND',function(v1, operator, v2, options){
				return checkCondition(v1, operator, v2)
	                ? options.fn(this)
	                : options.inverse(this);
			});
			Handlebars.registerHelper('toJSON', function(obj) {
			    return new Handlebars.SafeString(JSON.stringify(obj));
			});
			Handlebars.registerHelper('Cal_def_check',function(V, options){		
				if( BUS.cal_def && BUS.cal_def[V] ) return options.fn(this);
				return options.inverse(this);
			});
			Handlebars.registerHelper('TypeCheck',function(V, options){		
				if( options.type == V ) return options.fn(this);
				return options.inverse(this);
			});
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
	// @2.9.1
	// increase and reduce quantity
	    $('body').on('click','.evo_qty_change', function(event){
	        var OBJ = $(this);
	        var QTY = oQTY = parseInt(OBJ.siblings('em').html());
	        var MAX = OBJ.siblings('input').attr('max');
	        var BOX = OBJ.closest('.evo_purchase_box');

	        var pfd = BOX.find('.evo_purchase_box_data').data('pfd');
	        

	        (OBJ.hasClass('plu'))?  QTY++: QTY--;

	        QTY =(QTY==0)? 1: QTY;
	        QTY = (MAX!='' && QTY > MAX)? MAX: QTY;

	        // new total price
	        var sin_price = OBJ.parent().data('p');
	        new_price = sin_price * QTY;

	        new_price = get_format_price( new_price, pfd);

	        BOX.find('.total .value').html( new_price);

	        OBJ.siblings('em').html(QTY);
	        OBJ.siblings('input').val(QTY);

	        $('body').trigger('evo_qty_changed',[QTY,oQTY, new_price,OBJ ]);
	    });

    // Total formating
        function get_format_price(price, data){

            // price format data
            PF = data;
           
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