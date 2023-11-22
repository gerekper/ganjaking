/**
 * WeeklyView Javascript
 * @version  1.1.3
 */
jQuery(document).ready(function($){

	var SC = '';


	// INIT AJAX - success
		$('body').on('evo_init_ajax_success_each_cal', function(event, data, calid, v){
			CAL = $('body').find('#'+ calid);

			if(!CAL.hasClass('evoWV')) return;
			populate_calendar( CAL );
		});

		var BUS = {};
		var wv_range_format = {
			0: 'MM D', 1: 'MM D, YYYY'
		};

		$('body').on('evo_init_ajax_success',function(event, data){
			$('body').find('.ajde_evcal_calendar.noiajx.evoWV').each(function(){
				populate_calendar( $(this) );
			});
		});

	// DRAW
		function populate_calendar( CAL){
			BUS = $('#evo_global_data').data('d');

			// load range format data
			if( 'cal_def' in BUS && 'wv_range_format' in BUS.cal_def){
				wv_range_format = BUS.cal_def.wv_range_format;
			}

			//JSON = CAL.find('.evo_cal_events').data('events');
			SC = CAL.evo_shortcode_data();


			// grid style
			if( SC.week_style == '1')	CAL.find('#evcal_list').addClass('evo_hide');

			var now = moment();

			var template_data = {};
			template_data['days'] = {};
			
			// week strip
			var SU = parseInt( SC.focus_start_date_range);			
			var iSU = parseInt( SC._in_ws);			
			var M = moment(SU*1000).utc();	

			var month = M.get('month')+1;
			for(x=1; x<=7; x++){
				template_data['days'][x] = {};

				template_data['days'][x]['newmo'] = (month != (M.get('month')+1)? M.get('month')+1:'no' );
				month = M.get('month')+1;	

				
				template_data['days'][x]['SU'] = M.unix();
				template_data['days'][x]['D'] = M.get('date');
				template_data['days'][x]['DN'] = M.day();
				template_data['days'][x]['today'] = (now.format('YYYY M D') == M.format('YYYY M D')? 'today':'');
				
				M.add(1,'d');					
			}

			template_data['table_style'] = SC.table_style;
			template_data['week_style'] = SC.week_style;
			template_data['fixed_week'] = SC.fixed_week;
			template_data['disable_week_switch'] = SC.disable_week_switch;

				
			_HTML = CAL.evo_HB_process_template({
				TD:template_data, part:'evowv_week'
			});

			// replace or insert HTML
			if( CAL.find('.EVOWV_content').length == 0){				
				_HTMLt = CAL.evo_HB_process_template({
					TD:template_data, part:'evowv_top'
				});
				
				ELM = CAL.find('#eventon_loadbar_section');
				ELM.after( _HTMLt );
			}

			CAL.find('.EVOWV_grid').html( _HTML );

			draw_week_switcher( CAL);
			populate_events_into_weekgrid( CAL );


			// show this week button if focus week is different
			if( (now.unix() < SU || (SU+604800) < now.unix() ) && CAL.find('.evowv_this_weekbtn').length == 0 && 
				SC.disable_week_switch=='no'){
				
				now_sow = _get_unix_of_now_week_start();
				incre = parseInt((now_sow - iSU)/604800);
				_txt = CAL.evo_get_txt({V:'this_week'});
				CAL.find('.EVOWV_dates').prepend('<a class="evowv_this_weekbtn evcal_btn" data-week_incre="'+incre+'">'+_txt+'</a>');
			}

			// remove pre laoder section
			CAL.find('.evowv_pre_loader').remove();
		}

		function _get_unix_of_now_week_start(){
			var now = moment().utc();
			$start_ow = $('body').evo_get_cal_def({V: 'start_of_week'});
			$today_day = now.day();

			if( $start_ow >1) $dayDif = $today_day -( $start_ow-1);
			if( $today_day > $start_ow ) $dayDif = $today_day - $start_ow;
			if( $today_day == $start_ow ) $dayDif = 0;
			if( $start_ow > $today_day) $dayDif = 7 - $start_ow;

			now.subtract( $dayDif, 'days');
			now.startOf('day');
			return now.unix();
		}

		// populate the week grid
		function populate_events_into_weekgrid( CAL){
			var eJSON = CAL.find('.evo_cal_events').data('events');
			var grid = CAL.find('.EVOWV_grid');
			var cal_events = CAL.find('.eventon_list_event');

			// run for each day in the week
			grid.find('.evo_wv_day').each(function(index){	
				var O = $(this);	
				time_format = CAL.evo_get_global({S1:'cal_def',S2:'wp_time_format'});

				EC = 0;
				// each event in the events list
				cal_events.each(function(index, elm){
					var ED = $(elm).evo_cal_get_basic_eventdata();
					if(!ED) return;

					SU = parseInt(O.data('su'));	
					EU = SU+86399;

					var inrange = CAL.evo_is_in_range({
						'S': SU,	'E': EU,	'start': ED.event_start_unix,'end':ED.event_end_unix
					});

					if(inrange){
						EC++;
						O.addClass('has_events');

						// table style week
						if( SC.week_style == '1'){
 
							style = SC.table_style =='0'? 'border-color':'background-color';

							// calculate display time
							_time = '';			
		
							// all day events
							if( 
								(ED.event_start_unix <= SU || ED.event_start_unix <= SU +1) && EU <= ED.event_end_unix 
							){
								_time = CAL.evo_get_txt({V:'all_day'});
							}else{
								m = moment.unix( ED.event_start_unix ).utc();
								me = moment.unix( ED.event_end_unix ).utc();

								_time += (SU <= ED.event_start_unix && ED.event_start_unix <= EU ) ? 	m.format( time_format ): '';
								_time += ' - ';
								_time += ( ED.event_end_unix < EU) ? me.format( time_format) : '';
							}

							O.find(".evowv_col_events")
								.append("<span class='event evowv_tb_event' data-ec='"+ ED.uID +"' style='"+style+":"+ ED.hex_color +"' data-uxval='"+ ED.ux_val +"'><span class='time'>"+ _time +"</span>"+ ED.event_title +"</span>");
						}else{
							if( EC<4){
								O.find('span.day_events').append("<em class='dayTag' style='background-color:"+ ED.hex_color +"' title='"+ ED.event_title +"'></em>");
							}
						}
					}
				});

				if( EC>3 && SC.week_style != '1'){
					O.find('span.day_events').append("<em class='dayTag more' title='+"+ EC+"'></em>")
						.addClass('has_more_events');
				}
				if( EC == 0) O.addClass('noE');

				return;

				/* legacy since 2.0
				EC = 0;
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


						// table style week
						if( SC.week_style == '1'){
 
							style = SC.table_style =='0'? 'border-color':'background-color';

							// calculate display time
							_time = '';			
		

							// all day events
							if( 
								(ev.event_start_unix <= SU || ev.event_start_unix <= SU +1) && EU <= ev.event_end_unix 
							){
								_time = CAL.evo_get_txt({V:'all_day'});
							}else{
								m = moment.unix( ev.event_start_unix).utc();
								me = moment.unix( ev.event_end_unix).utc();

								_time += (SU <= ev.event_start_unix && ev.event_start_unix <= EU ) ? 
									m.format( time_format ): '';

								_time += ' - ';

								_time += ( ev.event_end_unix < EU) ? me.format( time_format) : '';
							}


							O.find(".evowv_col_events")
								.append("<span class='event evowv_tb_event' data-ec='"+ ev._ID+"' style='"+style+":#"+ ev.hex_color +"' data-uxval='"+ ev.event_pmv._evcal_exlink_option +"'><span class='time'>"+ _time +"</span>"+ ev.event_title +"</span>");
						}else{
							if( EC<4){
								O.find('span.day_events').append("<em class='dayTag' style='background-color:#"+ev.hex_color+"' title='"+ ev.event_title +"'></em>");
							}
						}
					}
				});

				if( EC>3 && SC.week_style != '1'){
					O.find('span.day_events').append("<em class='dayTag more' title='+"+ EC+"'></em>")
						.addClass('has_more_events');
				}
				if( EC == 0) O.addClass('noE');
				*/

			});
		}

		// draw week switcher
		function draw_week_switcher(CAL){
			var SC = CAL.evo_shortcode_data();
			var SU = parseInt( SC.focus_start_date_range);
			var date_format = CAL.evo_get_global({S1:'cal_def',S2:'wp_date_format'});
			
			// update current focus range 
			S = moment(SU*1000).utc();
			E = moment( (parseInt( SC.focus_end_date_range)) *1000).utc();

			N = CAL.find('.EVOWV_thisdates_range');
			N.html( get_formatted_date_range( S, E) )
				.data('su', SU);

			// update switcher week ranges
			UL = CAL.find('.EVOWV_date_ranges');
			S.add(-2,'w');	
			_HTML = get_week_switcher_weeks( S.unix(), CAL);
			UL.html( _HTML);
		}

		function get_formatted_date_range( S, E){
			var m1 = BUS['dms']['m'];
			var m3 = BUS['dms']['m3'];

			var R = '';

			if( wv_range_format[0].includes('MMMM')){
				R += m1[ S.month()+1 ]+ ' '+ S.date();
			}else if( wv_range_format[0].includes('MM')){
				R += m3[ S.month()+1 ]+ ' '+ S.date();
			}
			if( wv_range_format[0].includes('YYYY')){
				R += ', '+ S.year();
			}
			R+= ' - ';

			// end
			if( wv_range_format[1].includes('MMMM')){
				R += m1[ E.month()+1 ]+ ' '+ E.date();
			}else if( wv_range_format[1].includes('MM')){
				R += m3[ E.month()+1 ]+ ' '+ E.date();
			}
			if( wv_range_format[1].includes('YYYY')){
				R += ', '+ E.year();
			}


			return R;
		}

		// week switcher week list
		function get_week_switcher_weeks( SU , CAL){

			var S = moment.unix( SU).utc();
			var E = moment.unix( SU).utc().add(1,'w').subtract(1,'s');

			var SC = CAL.evo_shortcode_data();
			var SU = parseInt( SC.focus_start_date_range);
			var iSU = parseInt( SC._in_ws);

			_HTML = '';
			for(x=1; x<=5; x++){

				now = (SU == S.unix())? 'thisweek':'';
				incre = parseInt((S.unix() - iSU)/604800);				

				_HTML += "<li class='"+now+"' data-week_incre='"+incre+"' data-su='"+ S.unix()+"'><em>"
					+ get_formatted_date_range(S, E) +"</em></li>";
				S.add(1,'w');
				E.add(1,'w');
			}

			return _HTML;
		}

	
	$('body')
		// Hover on event dots tooltips
			.on('mouseover','em.dayTag',function(){
				OBJ = $(this);
				
				PAR = OBJ.closest('.EVOWV_grid');

				p = OBJ.offset();
				t = PAR.offset();
				w = PAR.width();
				xleft = p.left - t.left;
				xtop = p.top - t.top;

				TITLE = OBJ.attr('title');

				// adjust side of the tooltip
				if((w/2) > xleft){
					HTML = "<em class='evowv_tooltip' style='top:"+(xtop-40)+"px;left:"+(xleft+3)+"px;'>"+TITLE+"</em>";
				}else{

					xright = w - xleft;
					HTML = "<em class='evowv_tooltip left' style='top:"+(xtop-40)+"px;right:"+(xright-3)+"px;'>"+TITLE+"</em>";
				}
				
				PAR.append(HTML);
			}).on('mouseout','em.dayTag',function(){
				OBJ = $(this);			
				OBJ.closest('.EVOWV_grid').find('.evowv_tooltip').remove();
			})
		// click on table event
			.on('click', '.evowv_tb_event',function(){
				O = $(this);
				CAL = O.closest('.ajde_evcal_calendar');
				var e_cl = 'event_'+O.data('ec');

				const clicked_event_uxval = O.data('uxval');

				// if event is set to slide down
				if( clicked_event_uxval == '1' ){
					CAL.find('.'+e_cl).find('.desc_trig').data('ux_val', 3);
				}

				CAL.find('.'+e_cl).find('.desc_trig').trigger('click');
			})

		// calendar view switching
			.on('evo_vSW_clicked',function(event, OBJ, CAL, DD){
				if(!(OBJ.hasClass('evowv'))) return;	

				var SC = CAL.evo_shortcode_data();
				var DATA = OBJ.data('d');

				// calculate week date range using shortcode dates					
					DD.setUTCDate( SC.fixed_day ); // adjust date to fixed date

					var sow = DATA.sow;
					var today_day = DD.getUTCDay();

					var dayDif = _in_ws = '';

					if( sow >1) dayDif = today_day -( sow-1);
					if( today_day > sow ) dayDif = today_day - sow;
					if( today_day == sow ) dayDif = 0;
					if( sow > today_day) dayDif = 7 - sow;

					if(dayDif != 0) DD.setDate( DD.getDate() - dayDif );
					
					CAL.evo_update_cal_sc({
						F:'focus_start_date_range', 
						V: Math.floor(DD.getTime()/1000)
					});

					_in_ws = Math.floor(DD.getTime()/1000);

					// end date
					DD.setSeconds( DD.getSeconds() + (7*24*3600) -1  );

					CAL.evo_update_cal_sc({
						F:'focus_end_date_range', 
						V: Math.floor(DD.getTime()/1000)
					});

				CAL.evo_update_cal_sc({F:'fixed_day', V: SC.fixed_day });
				CAL.evo_update_cal_sc({F:'calendar_type',V: 'weekly'});				
				CAL.evo_update_cal_sc({F:'_in_ws',V: _in_ws });
				CAL.evo_update_cal_sc({F:'disable_week_switch',V: 'no'});
				CAL.evo_update_cal_sc({F:'table_style',V: '0'});
				CAL.evo_update_cal_sc({F:'week_style',V: '0'});

				populate_calendar( CAL);

				// process calendar events in new range
				CAL.evo_cal_events_in_range({
					S: CAL.evo_get_sc_val({'F':'focus_start_date_range'}),
					E: CAL.evo_get_sc_val({'F':'focus_end_date_range'}),
					showEV:true,
					showEVL:true
				});
			})

		// move to this week
			.on('click','.evowv_this_weekbtn',function(){
				CAL = $(this).closest('.ajde_evcal_calendar');
				week_incre = parseInt($(this).data('week_incre'));
				new_week(CAL, week_incre, 'wv_newweek');
				CAL.find('.evowv_this_weekbtn').remove();
			})

		// Click on each week day on WEEK strip
			.on('click','.evo_wv_day', function(){
				OBJ = O = $(this);
				CAL = OBJ.closest('.ajde_evcal_calendar');
				SC = CAL.evo_shortcode_data();

				// skip table week view
				if(CAL.hasClass('evoWV_tb')) return;

				showEVL = true;

				if( O.hasClass('focus')){
					SC = CAL.evo_shortcode_data();
					O.removeClass('focus');
					SU = parseInt(SC.focus_start_date_range);
					EU = parseInt(SC.focus_end_date_range);

					showEVL = false;
				// showing events for this day
				}else{
					OBJ.siblings().removeClass('focus');
					OBJ.addClass('focus');
					SU = parseInt( O.data('su'));
					EU = SU + 86399;
				}	

				//console.log( SU+' '+EU);

				// get events from evnet list
					R = CAL.evo_cal_events_in_range({
						S: SU,
						E: EU,
						closeEC: true,
						showEV: true,
						showEVL: showEVL
					});

				// if hide events list on load
				if(SC.hide_events_onload =='yes' && !(O.hasClass('focus')) ){
					CAL.find('.eventon_events_list').hide().addClass('evo_hide');
				}
			})
	;
	
	
	// Week range selection
		$('body')
		.on('mouseover', '.EVOWV_change', function(){
			CAL = $(this).closest('.ajde_evcal_calendar');
			CAL.find('.EVOWV_ranger').show();
		})
		.on('mouseleave','.EVOWV_ranger', function(){
			// reset the switcher to fixed_week
			CAL = $(this).closest('.ajde_evcal_calendar');
			SU = CAL.find('.EVOWV_thisdates_range').data('su');
			S = moment.unix( SU).utc();	S.add(-2,'w');
			_HTML = get_week_switcher_weeks( S.unix(), CAL);

			CAL.find('.EVOWV_date_ranges').html( _HTML );
			$(this).hide();
		})
		.on('click','.EVOWV_range_mover',function(){
			var OBJ = $(this),
			UL = OBJ.siblings('.EVOWV_ranger_handle').find('ul');
			CAL = $(this).closest('.ajde_evcal_calendar');

			UL.find('li').addClass('O');

			if( OBJ.hasClass('up')){	
				tSU = UL.children(":first").data('su');
				S = moment.unix( tSU).utc();
				S.add(-5,'w');

				_HTML = get_week_switcher_weeks( S.unix(), CAL);

				UL.prepend( _HTML);
				UL.css({'top':-150});
				UL.animate({'top':0},400,function(){
					UL.find('.O').remove();
				});
			}else{
				tSU = UL.children(":last").data('su');
				S = moment.unix( tSU).utc();
				S.add(1,'w');

				_HTML = get_week_switcher_weeks( S.unix(), CAL);

				UL.append( _HTML);
				UL.css({'top':0});
				UL.animate({'top':-150},400,function(){
					UL.find('.O').remove();
					UL.css({'top':0});
				});
			}			
		});
	// click on side arrows
		$('body').on('click', '.evowv_arrow', function(event){
			var CAL = $(this).closest('.ajde_evcal_calendar');
				
			// new week increment
			week_incre = parseInt(CAL.evo_get_sc_val({F:'week_incre'}));
			week_incre = !week_incre ? 0: week_incre;
			adj = parseInt($(this).data('week'));

			new_week_incre = week_incre + adj;
			new_week(CAL , new_week_incre, 'wv_newweek');
		});	

	// click on a new range
		$('body').on('click','.EVOWV_date_ranges li',function(){
			var CAL = $(this).closest('.ajde_evcal_calendar');			
			week_incre = parseInt($(this).data('week_incre'));
			new_week(CAL, week_incre, 'wv_newweek');

			$(this).closest('.EVOWV_ranger').hide();
		} );	

	// AJAX
		// SUCCESS
		$('body').on('evo_main_ajax_success',function(event, CAL, ajaxtype, data, data_arg){
			SC = data_arg.shortcode;
			if(  SC.calendar_type == 'weekly'){
				CAL.find('.eventon_events_list').removeClass('evowv_hide');			
			}
		});

		// COMPLETE
		$('body').on('evo_main_ajax_complete', function(event, CAL,  ajaxtype, data , data_arg){
			SC = data_arg.shortcode;
			if( SC.calendar_type == 'weekly'){
				populate_calendar( CAL );
			}
		});

	// AJAX: New Week events and data update
		function new_week(CAL,   week_incre, ajaxtype){			
			SC = CAL.evo_shortcode_data();

			CAL.evo_update_cal_sc({F:'week_incre', V: week_incre});

			$('body').trigger('evo_run_cal_ajax', [ CAL.attr('id'), 'none', ajaxtype]);
		}
			
	// if mobile check
		function is_mobile(){
			return ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )? true: false;
		}
});