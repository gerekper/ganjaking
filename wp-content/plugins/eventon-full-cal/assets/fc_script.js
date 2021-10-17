/**
 * Main javascript for fullCal addon for eventon
 * @version 0.10
 * @updated 1.1.2  
 */
jQuery(document).ready(function($){	
	//init();	

	$.fn.evo_fullcal = function(){
		el = this;

		var B = $(this).evo_cal_functions({return:'load_shortcodes'});
		
	};


	// EVO INIT AJAX - success
		$('body').on('evo_init_ajax_success_each_cal', function(event, data, calid, v){
			CAL = $('body').find('#'+ calid);

			if(!CAL.hasClass('evoFC')) return;
			SC = CAL.evo_shortcode_data();

			CAL.evo_fullcal();
			
			if(SC.evc_open == 'no' && SC.load_fullmonth=='no'){
				CAL.find('#evcal_list').addClass('evo_hide');
			}

			draw_fullcal( CAL ,'init');

			// load only fixed day events
			if(SC.load_fullmonth=='no')	load_correct_events( CAL, 'init');
		});

		// after running for each cal 
		$('body').on('evo_init_ajax_success', function(event, data){
			$('body').find('.ajde_evcal_calendar.evoFC').each(function(){
				//CAL = $(this);
				//SC = CAL.evo_shortcode_data();
				//draw_fullcal( CAL ,'replace');
				//load_correct_events( CAL, 'init');
			});			
		});
		

	// DRAW FullCal grid
		function draw_fullcal( CAL, type){

			BUS = $('#evo_global_data').data('d');
			var eJSON = CAL.find('.evo_cal_events').data('events');
			SC = CAL.evo_shortcode_data();
			var template_data = {};


			days_in_month = CAL.evo_day_in_month({M: SC.fixed_month, Y: SC.fixed_year});
			fixed_day_name_index = CAL.evo_get_day_name_index({M: SC.fixed_month, Y: SC.fixed_year, D: SC.fixed_day});

			// day names
			day_names = {};
			_z = start_of_week = BUS.cal_def.start_of_week;
			for(z=0; z<=6; z++){
				day_names[z]= BUS.dms.d3[_z];
				_z++;
				_z = (_z>6)? 0: _z;
			}

			first_day_index = CAL.evo_get_day_name_index({D:1,M: SC.fixed_month, Y: SC.fixed_year});				
			boxes = ( first_day_index < start_of_week)? 
						((7 - start_of_week) +first_day_index): (first_day_index- start_of_week);

			M = moment();

			//window.alert(first_day_index + ' '+boxes);

			template_data['blanks'] = boxes;
			template_data['days'] = {};
			template_data['day_names'] = day_names;
			template_data['month'] = SC.fixed_month;

			// month strip class name additions
				_class_adds_mo = '';
				if(SC.style == 'names') _class_adds_mo += ' names';
				if(SC.style == 'nobox') _class_adds_mo += ' nobox';
				if(SC.heat == 'yes') _class_adds_mo += ' heatmap';
					template_data['months_strip_classes'] = _class_adds_mo;

			// each day
			SU = parseInt(SC.focus_start_date_range);

			var boxcount = boxes;
			var row = 1;
			var calrows = Math.ceil( (boxes+days_in_month)/7);
			var boxsin_lastrow = (boxes+days_in_month)%7;
			
			for(var x=1; x<= days_in_month; x++){
				boxcount++;

				_class_adds = '';
				if(x == M.date() && SC.fixed_month == (M.month()+1) && SC.fixed_year == M.year()) _class_adds += ' today';
				if(x == SC.fixed_day ) _class_adds += ' on_focus';
				if(x > (days_in_month-7)) _class_adds += ' bb';

				// before last row
				if(boxsin_lastrow != 0 && row == calrows-1 && x <= (days_in_month-7)) _class_adds += ' blsr';


				// last row cell
				if( boxcount> 21 && calrows == 4 && boxsin_lastrow != 0) _class_adds += ' nobrt';
				if( boxcount> 28 && calrows == 5 && boxsin_lastrow != 0) _class_adds += ' nobrt';
				if( boxcount> 35 && calrows == 6 && boxsin_lastrow != 0) _class_adds += ' nobrt';

				
				// last day of the week
				if( boxcount %7 == 0){
					_class_adds += ' lstdw';
					row++;
				}

				template_data['days'][x] = {};
				template_data['days'][x]['cls'] = _class_adds;
				template_data['days'][x]['su'] = SU;
				template_data['days'][x]['eu'] = SU + 86399;
				template_data['days'][x]['e'] = {};

				SU = SU+ 86400;
			}

			_html_grid = get_evo_temp_processed_html( template_data , 'evofc_grid');

			// replace or insert HTML
			if(type == 'replace'){
				return _html_grid;
			}else{
				_html_base = get_evo_temp_processed_html( template_data , 'evofc_base');				

				ELM = CAL.find('#eventon_loadbar_section');
				ELM.after( _html_base );
				CAL.find('.evofc_months_strip').html( _html_grid );
			}
			CAL.find('.evofc_month_grid').fadeIn();


			if( type != 'replace') populate_grid_boxes_with_events( CAL );
		}	
			function get_evo_temp_processed_html( template_data, part){
				BUS = $('#evo_global_data').data('d');
				template = Handlebars.compile( BUS.temp[ part ] );
				return template( template_data );
			}

		// populate the grid boxes with events JSON
		function populate_grid_boxes_with_events(CAL){
			var eJSON = CAL.find('.evo_cal_events').data('events');
			grid = CAL.find('.evofc_month_grid');
			SC = CAL.evo_shortcode_data();
			
			var _txt_more = CAL.evo_get_txt({V:'more'});

			// EACH DAY
			max_events = 0; days = {};
			grid.find('.evofc_day').each(function(index){	
				O = $(this);

				EC = 0;
				O_span = O.find('span');
				O_span.html('');
				O.removeClass('has_events');

				// EACH EVENT
				$.each(eJSON, function(ie, ev){
					SU = parseInt(O.data('su'));
					EU = SU+86399;				
					var inrange = CAL.evo_is_in_range({
						'S': SU,
						'E': EU,
						'start': ev.event_start_unix,
						'end':ev.event_end_unix
					});
					if(inrange){
						EC++;
						O.addClass('has_events');
						O.removeClass('noE');

						// Names in the grid
						if( SC.style == 'names'){
							if(EC <3){
								_color = parseInt( ev.hex_color, 16) > 0xffffff/2 ? '#000':'#fff';

								var _day_class = '';

								if(ev.event_start_unix < SU && SU < ev.event_end_unix && ev.event_end_unix <=EU) _day_class = 'muld_e';
								if(ev.event_start_unix < SU && EU < ev.event_end_unix ) _day_class = 'muld_m';
								if(SU <= ev.event_start_unix && EU < ev.event_end_unix ) _day_class = 'muld_s';

								if(ev.event_start_unix < SU && O.hasClass('d_1')) _day_class += ' strpr';

								var _html_title = ev.event_title;
								// hide title for inside dates
								if( (_day_class == 'muld_m' || _day_class == 'muld_e') && !(O.prev().hasClass('lstdw')) && !(O.hasClass('d_1')) 
								){	
									//_html_title = '';	
								}

								var _content = "<i class='"+ ev._ID+" "+_day_class+"' style='background-color:#"+ ev.hex_color +"; color:"+_color+"' title='"+ ev.event_title +"'>"+ _html_title +"</i>";

								// if previous date has same event
								if(O.prev().find('i.'+ ev._ID).length>0){
									prev_ind = O.prev().find('i.'+ ev._ID).index();
									if( prev_ind == 0){
										O_span.prepend( _content);	
									}else{// prev ind = 1
										if( O_span.find('i').length==0){
											var prev_i = O.prev().find('i.'+ ev._ID);
											O.prev().find('span').prepend( prev_i);
										}
										O_span.append( _content);											
									}
								}else{
									O_span.append( _content);	
								}
							}else{
								if(O.find('b').length == 0) O.find('span').append( "<b>+ "+_txt_more+"</b>");
							}					
						}else{
							if(EC >=5 ){
								O_span.html( "<b>"+EC+" "+_txt_more+"</b>");
							}else{
								O_span.append( "<i title='"+ ev.event_title +"'></i>");	
							}
						}
						if( EC> max_events) max_events = EC;
					}
				});

				if( EC == 0) O.addClass('noE');
				days[index] = EC;
				
			});

			// heat map coloring
			if( SC.heat =='yes'){
				grid.find('.evofc_day').each(function(index){
					events = days[index];						
					opacity = ( events/ max_events).toFixed(2) ;
					color = '206177';
					if(events>0) $(this).css({'background-color':'#206177','opacity':opacity});					
				});
			}

		}
	
		
	// GENERAL Interactions
		// fix ratios for resizing the calendar size
			$( window ).resize(function() {	$('body').trigger('evofc_resize_cal_grid');	});
		// CLICK on a day
			if(is_mobile()){
				if(is_android()){
					$('body').on( 'click','.evofc_day',function(){	clickon_day($(this));	});	
				}else{
					$('body').on( 'tap','.evofc_day',function(){	clickon_day($(this));	});	
				}			
			}else{
				$('body').on( 'click','.evofc_day',function(){	clickon_day($(this));	});			
			}
			function clickon_day(obj){
				if( obj.hasClass('evo_fc_empty')) return;
				
				CAL = obj.closest('.ajde_evcal_calendar');
				SC = CAL.evo_shortcode_data();
				CAL.evo_update_cal_sc({		F:'fixed_day', V:obj.data('d')	});
			
				load_correct_events( CAL );
			}

		// load correct events in event list based on fixed day
		function load_correct_events(CAL, type){
			SC = CAL.evo_shortcode_data();

			if(type == 'init' && SC.grid_ux==2) return;
			
			fixed_day = parseInt(SC.fixed_day);

			var box_obj = CAL.find('.evofc_day.d_'+fixed_day);
			box_obj.siblings('.evofc_day').removeClass('on_focus');
			box_obj.addClass('on_focus');
			
			sunix = parseInt(box_obj.data('su'));
			eunix = sunix + 86399;

			eJSON = CAL.find('.evo_cal_events').data('events');
			// if load full month to calendar
			if(type == 'init' && SC.load_fullmonth=='yes'){
				
				R = eJSON;

			}else{
				// get events from evnet list
				R = CAL.evo_cal_events_in_range({
					S: sunix,
					E: eunix,
					closeEC: (SC.evc_open=='yes'? false:true)
				});
			}			
			
			// grid interaction
				if( SC.grid_ux == 1){
					$([document.documentElement, document.body]).animate({
				        scrollTop: CAL.find('#evcal_list').offset().top - 50
				    }, 200);
				}

			// if no events
			var inside_content = '';
			if( R.count == 0){
				txt = CAL.evo_get_global({S1:'txt',S2:'no_events'});
				inside_content = "<div class='eventon_list_event no_events'><p class='no_events' >"+ txt +"</p></div>";
			}

			// load events in lightbox
			if(SC.grid_ux==2 && type != 'init') {
				$('.evofc_lightbox').evo_prepare_lb();	

				if( 'count' in R && R.count > 0){
					$('.evofc_lightbox').evo_append_lb({C: R.html});
				}else{
					$('.evofc_lightbox').evo_append_lb({C: inside_content });
				}			
				
				$('.evofc_lightbox').evo_show_lb({calid: CAL.attr('id')});

				$('.evofc_lightbox').find('.eventon_list_event').show();
			}else{
				eList = CAL.find('#evcal_list');
				if(R.count == 0){
					OD = CAL.evo_get_OD();					

					if(eList.has('.no_events.eventon_list_event').length){
						eList.find('.no_events.eventon_list_event').show();
					}else{						
						eList.append( inside_content );
					}						
				}else{

					$.each(R.json,function(i,evid){
						eList.find('#event_'+i).show();
					});
					CAL.find('.eventon_list_event.no_events').remove();
				}
				CAL.find('#evcal_list').removeClass('evo_hide').show();
			}
		}
		
	// BODY GEN
		$('body')
			// calendar view switching
			.on('evo_vSW_clicked',function(event, OBJ, CAL){
				if(!(OBJ.hasClass('evofc'))) return;

				CAL.evo_update_cal_sc({F:'calendar_type', V: 'fullcal'});
				CAL = OBJ.closest('.ajde_evcal_calendar');

				draw_fullcal( CAL );

			})
			// resize fullcal grid
				.on('evofc_resize_cal_grid', function(){
					$('.eventon_fullcal').each(function(){
						var cal_width = $(this).width();
						var strip = $(this).find('.evofc_months_strip');
						var multiplier = strip.attr('data-multiplier');
						
						if(multiplier<0){
							strip.width(cal_width*3).css({'margin-left':(multiplier*cal_width)+'px'});					
						}
						$(this).find('.evofc_month').width(cal_width);
					});
				})			
			// tool tips on calendar dates
				.on('mouseover' , '.evofc_day.has_events', function(){
					O = $(this);
					CAL = O.closest('.ajde_evcal_calendar');
					SC = CAL.evo_shortcode_data();
					_fc_grid_O = CAL.find('.evofc_month_grid');

					SU = parseInt(O.data('su'));
					R = CAL.evo_cal_events_in_range({S: SU, E: SU+86399, hide:false});

					// event names
					if(SC.hover =='numname'){			
						_events_html = '';	
						
						titletip = CAL.find('.evofc_title_tip');
						
						// events count
						CAL.find('.evofc_ttle_cnt').html( R.count);

						// event names
						_C = 0;
						$.each( R.json, function(key, ED){
							if(_C>2) return;
							_events_html += "<li style='border-left-color:#"+ ED.hex_color +"'>"+ ED.event_title +"</li>";	
							_C++;
						});
						if( _C>2){
							_events_html += "<li>+ "+ CAL.evo_get_global({S1:'txt',S2:'more'}) +"</li>";	
						}
						CAL.find('.evofc_ttle_events').html( _events_html );

						// Positioning
							TITLETIP_HEIGHT = titletip.height();

							var offs = O.position();
							width = $('.eventon_fullcal').width();
							var dayh = CAL.find('.eventon_fc_daynames').height();

							BOXCOUNT = O.data('cnt');

							if( O.offset().left < ( _fc_grid_O.offset().left + _fc_grid_O.width() - (O.width()*4) ) ){
								titletip.removeClass('lefter');
								leftOff = offs.left + O.width();
								rightOFF = 'initial';
							}else{
								titletip.addClass('lefter');
								leftOff = 'initial';
								rightOFF = width- offs.left ;	
							}

							titletip.css({
								top: (offs.top+dayh - TITLETIP_HEIGHT), 
								left:leftOff, 
								right:rightOFF
							})
							.stop(true, false)
							.fadeIn('fast');

					}else{ // just event count number
						var popup = CAL.find('.evoFC_tip');
						var offs = O.position();
						var leftOff ='';

						var dayh = O.closest('.evofc_month').find('.eventon_fc_daynames')
							.height();

						if(O.offset().left < ( _fc_grid_O.offset().left + _fc_grid_O.width() - (O.width()*3) ) ){
							leftOff = offs.left + O.width()+2;
						}else{
							popup.addClass('leftyy');
							leftOff = offs.left - 17;							
						}						
						popup.css({top: (offs.top+dayh), left:leftOff});
						popup.html( R.count ).stop(true, false).fadeIn('fast');
					}
					
				})
				.on('mouseout' , '.evofc_day.has_events', function(){
					O = $(this);
					CAL = O.closest('.ajde_evcal_calendar');
					SC = CAL.evo_shortcode_data();

					if(SC.hover=='numname'){
						CAL.find('.evofc_title_tip').removeClass('lefter');
						CAL.find('.evofc_title_tip').stop(true, false).hide();
					}else{
						var popup = CAL.find('.evoFC_tip');
						popup.removeClass('leftyy');			
						popup.stop(true, false).hide();
					}
				});
	
	// AJAX Operations
		// SUCCESS
		$('body').on('evo_main_ajax_success',function(event, CAL, ajaxtype, data, data_arg){
			if(  data.SC.calendar_type == 'fullcal'){

				SC = data.SC;

				_month_grid_adds = draw_fullcal( CAL,  'replace');

				var this_section = CAL.find('.eventon_fc_days');
				var strip = CAL.find('.evofc_months_strip');
				var cur_margin = parseInt(strip.css('marginLeft'));
				var month_width = parseInt(strip.parent().width());
				var months = strip.find('.evofc_month').length;
				var super_margin;
				var pre_elems = strip.find('.focus').prevAll().length;
				var next_elems = strip.find('.focus').nextAll().length;
				
				// build out month grid animation
					if( data_arg.direction =='next' || ajaxtype=='jumper' || ajaxtype == 'today'){
						strip.css({'width': (month_width*2)});
						if( months ==2 && next_elems==0){
							strip.find('.evofc_month:first-child').remove();
							strip.css({'margin-left':(cur_margin+month_width)+'px'});						
							super_margin = cur_margin;
							strip.append( _month_grid_adds );
							
						}else if(months== 2 && next_elems==1){
							super_margin = cur_margin-month_width;
						}else{
							strip.append( _month_grid_adds );
							super_margin = cur_margin-month_width;
							
							if(ajaxtype=='jumper'){
								strip.find('.evofc_month:first-child').remove();					
								strip.css({'margin-left':'0'});
							} 		
						}					
						
						strip.attr({'data-multiplier':'-1'}).find('.evofc_month').removeClass('focus');
						strip.find('.evofc_month:last-child').addClass('focus');
						
					}else if( data_arg.direction =='prev'){						
						if(months==2 && pre_elems==0){	
							strip.prepend( _month_grid_adds );
							strip.css({'margin-left':(cur_margin-month_width)+'px'});
							
							strip.find('.evofc_month:last-child').remove();
							super_margin =0;	
						}else if(months== 2 && pre_elems==1){
							super_margin =0;
						}else{							
							strip.prepend( _month_grid_adds );
							strip.css({'margin-left':(cur_margin-month_width)+'px'});
							super_margin = 0;							
						}
						
						strip.attr({'data-multiplier':'+1'}).find('.evofc_month').removeClass('focus');
						strip.find('.evofc_month:first-child').addClass('focus');						
					}else{// no month change - filter, search
						strip.find('.focus').replaceWith(  _month_grid_adds );
						strip.find('.evofc_month[month='+ SC.fixed_month +']').addClass('focus');
					}

				strip.find('.evofc_month').width(month_width);
				
				// animate the month grid
				if(data_arg.direction =='none' && ajaxtype != 'today'){
					populate_grid_boxes_with_events( CAL );
					load_correct_events( CAL ,'init');
					strip.attr({'data-multiplier':'0'});
				}else{
					strip.delay(100).animate({'margin-left':super_margin+'px'}, 500, 'easeOutQuint',function(){
						strip.find('.focus').siblings().remove();
						strip.css({'margin-left':'0'});
						strip.attr({'data-multiplier':'0'});
						
						// load correct events and populate the new grid
						populate_grid_boxes_with_events( CAL );

						if(SC.load_fullmonth=='no')	load_correct_events( CAL, 'init');
					});
				}
			}
		});
		
	// if mobile check
		function is_mobile(){
			return ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )? true: false;
		}
		function is_android(){
			var ua = navigator.userAgent.toLowerCase();
			return ( ua.indexOf("android") > -1)? true: false;
		}

});