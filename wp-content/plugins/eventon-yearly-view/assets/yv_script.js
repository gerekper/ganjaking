/**
 * Javascript: Eventon YV
 * @version 0.1
 */
jQuery(document).ready(function($){

	var current_date;
	var current_day;
	var current_events;
	var SC;

	// DRAW
		function populate_YV_calendar(CAL,type){
						
			BUS = $('#evo_global_data').data('d');
			eJSON = CAL.find('.evo_cal_events').data('events');
			SC = CAL.evo_shortcode_data();

			var days_list = {};

			day_names = {};
			_z = start_of_week = BUS.cal_def.start_of_week;
			for(z=0; z<=6; z++){
				day_names[z]= BUS.dms.d1[_z];
				_z++;
				_z = (_z>6)? 0: _z;
			}

			// each month
				var SU = parseInt(SC.focus_start_date_range);
				for(x=1; x<=12; x++){
					days_in_month = CAL.evo_day_in_month({M: x, Y: SC.fixed_year});
					days_list[x] = {};
					days_list[x]['days'] = {};
					days_list[x]['day_names'] = day_names;

					first_day_index = CAL.evo_get_day_name_index({D:1,M: x, Y: SC.fixed_year});				
					boxes = ( first_day_index < start_of_week)? 
								((7 - start_of_week) +first_day_index): (first_day_index- start_of_week);
					days_list[x]['blanks'] = boxes;

					for(y=1; y<= days_in_month; y++){
						days_list[x]['days'][y] = {};
						days_list[x]['days'][y]['su'] = SU;
						days_list[x]['days'][y]['eu'] = SU+86399;
						days_list[x]['days'][y]['e'] = {};
						SU = SU +  86400;
					}
				}			

			var template_data = {};

			template_data['cal_def'] = BUS.cal_def;
			template_data['months'] = days_list;
			template_data['calid'] = CAL.attr('id');


			cd_html = get_evo_temp_processed_html( template_data , 'evoyv_grid');

			// replace or insert HTML
			if( CAL.find('.evoyv_year_grid').length>0){
				CAL.find('.evoyv_year_grid').replaceWith( cd_html );
			}else{
				ELM = CAL.find('#eventon_loadbar_section');
				ELM.after( cd_html );
			}
			CAL.find('.evoyv_year_grid').fadeIn();

			// focus current day
			CAL.find('.evoyv_month[data-m="'+SC.fixed_month +'"]').addClass('this_m');
			CAL.find('.evoyv_month[data-m="'+SC.fixed_month +'"]').find('.day_box[data-d="'+SC.fixed_day+'"]').addClass('this_d');

			// hide event list
			CAL.find('#evcal_list').hide();			
			CAL.find('.eventon_list_event').hide();			
		}


		// load events into year grid
		function _load_events_top_grid(CAL){
			eJSON = CAL.find('.evo_cal_events').data('events');
			grid = CAL.find('.evoyv_year_grid');
			SC = CAL.evo_shortcode_data();

			K = 0;// all the events
			MAX = 0;
			grid.find('.day_box').each(function(index){
				var B = $(this);
				var events = '';

				L = 0;// events in the date
				$.each(eJSON, function(ie, ev){	
					SU = parseInt(B.data('su'));
					var inrange = CAL.evo_is_in_range({
						'S': SU,
						'E': SU+86399,
						'start': ev.event_start_unix,
						'end':ev.event_end_unix
					});

					if(inrange){
						L++;K++;
						if(SC.loading_animation == 'yes'){
							B.delay( 5*K).queue(function(){							
								B.addClass('he');
							});							
						}else{
							B.addClass('he').data('e',L);
						}

						events += ev._ID+',';
					}

					B.attr('ev', events).data('ev', events);
				});

				if( L > MAX) MAX = L;

			});


			// each day with events
			if(MAX>0 && SC.heat_circles == 'yes'){
				grid.find('.day_box.he').each(function(index){
					var e = parseInt($(this).data('e'));
					$(this).find('.day_box_color').css('opacity', e/MAX);
				});
			}
		}


		function get_evo_temp_processed_html( template_data, part){
			BUS = $('#evo_global_data').data('d');
			template = Handlebars.compile( BUS.temp[ part ] );
			return template( template_data );
		}

	// INIT AJAX - success
		$('body').on('evo_init_ajax_success_each_cal', function(event, data, calid, v){
			CAL = $('body').find('#'+ calid);

			if(!CAL.hasClass('evoYV')) return;

			populate_YV_calendar( CAL );
			_load_events_top_grid( CAL);
		});

	// AJAX:  when changing months
		// SUCCESS
		$('body').on('evo_main_ajax_success',function(event, CAL, ajaxtype, data){
			if(  data.SC.calendar_type == 'yearly'){				
				populate_YV_calendar( CAL, 'replace');
				_load_events_top_grid( CAL);
			}
		});		
	
	// ALL Calendar actions
		$('body')	
			// Hover over a day circle
			.on('mouseover' , '.evoyv_day.he', function(){
				O = $(this);
				CAL = O.closest('.ajde_evcal_calendar');
				SC = CAL.evo_shortcode_data();
				_grid_O = CAL.find('.evoyv_year_grid');

				SU = parseInt(O.data('su'));
				R = CAL.evo_cal_events_in_range({S: SU, E: SU+86399, hide:false});

				// event names
				if(SC.hover_style =='2' || SC.hover_style =='3'){			
					_events_html = '';	
					
					titletip = CAL.find('.evoyv_title_tip');
					
					// events count
					if(SC.hover_style =='2') CAL.find('.evoyv_ttle_cnt').html( R.count);
					if(SC.hover_style =='3') CAL.find('.evoyv_ttle_cnt').hide();

					// event names
					_C = 0;
					$.each( R.json, function(key, ED){
						if(_C<3)
							_events_html += "<li style='border-left-color:#"+ ED.hex_color +"'>"+ ED.event_title +"</li>";	
						_C++;
					});
					if( _C>3){
						_events_html += "<li>+ "+ CAL.evo_get_txt({V:'more'}) +"</li>";	
					}
					CAL.find('.evoyv_ttle_events').html( _events_html );

					// Positioning
						TITLETIP_HEIGHT = titletip.height();

						var offs = O.position();
						width = _grid_O.width();

						BOXCOUNT = O.data('cnt');

						if( O.offset().left < ( _grid_O.offset().left + _grid_O.width() - (O.width()*4) ) ){
							titletip.removeClass('lefter');
							leftOff = offs.left + (O.width()/2);
							rightOFF = 'initial';
						}else{
							titletip.addClass('lefter');
							leftOff = 'initial';
							rightOFF = width - offs.left - (O.width()/2);	
						}

						titletip.css({
							top: ( offs.top - TITLETIP_HEIGHT-10), 
							left:leftOff, 
							right:rightOFF
						})
						.stop(true, false)
						.fadeIn('fast');

				}

				// just event count number
				if(SC.hover_style =='1'){
					var popup = CAL.find('.evoyv_tip');
					var offs = O.position();
					var leftOff ='';

					var dayh = O.height();

					if(O.offset().left < ( _grid_O.offset().left + _grid_O.width() - (O.width()*3) ) ){
						leftOff = offs.left + O.width()+2;
					}else{
						popup.addClass('leftyy');
						leftOff = offs.left - 15;							
					}						
					popup.css({top: (offs.top ), left:leftOff});
					popup.html( R.count ).stop(true, false).fadeIn('fast');
				}
				
			})
			.on('mouseout' , '.evoyv_day.he', function(){
				O = $(this);
				CAL = O.closest('.ajde_evcal_calendar');
				SC = CAL.evo_shortcode_data();

				if(SC.hover_style=='2' || SC.hover_style=='3'){
					CAL.find('.evoyv_title_tip').removeClass('lefter');
					CAL.find('.evoyv_title_tip').stop(true, false).hide();
				}else{
					var popup = CAL.find('.evoyv_tip');
					popup.removeClass('leftyy');			
					popup.stop(true, false).hide();
				}
			})
			// calendar view switching
			.on('evo_vSW_clicked',function(event, OBJ, CAL){
				if(!(OBJ.hasClass('evoyv'))) return;

				CAL.evo_update_cal_sc({F:'calendar_type', V: 'yearly'});

				// RUN new AJAX call for events
				$('body').trigger('evo_run_cal_ajax',[ CAL.attr('id'), 'none','']);

			})
			// click on a calendar day
			.on('click','.day_box',function(){
				CAL = $(this).closest('.ajde_evcal_calendar');

				$('.evoyv_lightbox').evo_prepare_lb();

				TD = {};
				TD['day'] = $(this).data('d');
				TD['month'] = $(this).closest('.month_box').data('m');

				

				// get events from evnet list
				events = $(this).data('ev');

 
				R = CAL.evo_cal_events_in_range({
					S: $(this).data('su'),
					E: parseInt($(this).data('su'))+86399
				});

				TD['html'] = R.html;

				html = get_evo_temp_processed_html( TD , 'evoyv_lb');


				$('.evoyv_lightbox').evo_append_lb({C: html});

				$('.evoyv_lightbox').find('.eventon_list_event').show();
				$('.evoyv_lightbox').evo_show_lb({calid: CAL.attr('id')});

			})			
		;

	
});