/**
 * Javascript: Eventon Daily View
 * @version 2.0.3
 */
jQuery(document).ready(function($){

	var current_date;
	var current_day;
	var current_events;
	var SC;

	// INIT AJAX - success
		$('body').on('evo_init_ajax_success_each_cal', function(event, data, calid, v){
			CAL = $('body').find('#'+ calid);

			if(!CAL.hasClass('evoDV')) return;

			populate_DV_calendar( CAL, v );
			set_daily_strip_sizes( CAL );
		});

		$('body').on('evo_init_ajax_success',function(event, data){
			$('body').find('.ajde_evcal_calendar.noiajx.evoDV').each(function(){
				populate_DV_calendar( $(this) );
				set_daily_strip_sizes( $(this) );
			});
		});
	// AJAX
		// SUCCESS
		$('body').on('evo_main_ajax_success',function(event, CAL, ajaxtype, data){
			if(  data.SC.calendar_type == 'daily'){
				D = {};
				D['json'] = data.json;
				D['other'] = data.other;
				D['sc'] = data.SC;

				populate_DV_calendar( CAL, D, 'replace');
			}
		});		

		// COMPLETE
		$('body').on('evo_main_ajax_complete', function(event, CAL, ajaxtype, data , data_arg){		
			var SC = data_arg.shortcode;
			if(  SC.calendar_type == 'daily'){

				// reset dv scroll type value
				CAL.evo_update_cal_sc({	F:'dv_scroll_type', V:''});

				D = {};
				D['json'] = data.json;
				D['sc'] = SC;

				var this_section_days = CAL.find('.eventon_daily_list');
				this_section_days.slideDown('slow');	

				populate_DV_calendar( CAL, D, 'replace');

				set_daily_strip_sizes( CAL );		
			}
		});	

	// DRAW
		function populate_DV_calendar(CAL, data, type){
			
			var BUS = $('#evo_global_data').data('d');
			var eJSON = data? data.json: {};
			var SC = CAL.evo_shortcode_data();
			var days_list = {};
			var cal_events = CAL.find('.eventon_list_event');

			days_in_month = CAL.evo_day_in_month({M: SC.fixed_month, Y: SC.fixed_year});
			fixed_day_name_index = CAL.evo_get_day_name_index({M: SC.fixed_month, Y: SC.fixed_year, D: SC.fixed_day});

			M = moment();

			// with day strip
			if( SC.dv_view_style != 'oneday'){
				
				for(var x=1; x<= days_in_month; x++){
					var _class_adds = '';

					var SU = parseInt(SC.focus_start_date_range) + ( (x-1) * 86400)
					var DD = new Date( SU *1000);
					var DN = DD.getUTCDay();
					days_list[x] = {};
					days_list[x]['dn'] = DN;
					days_list[x]['su'] = SU;
					days_list[x]['eu'] = SU + 86399;
					days_list[x]['e'] = {};

					// class additions
					if( SC.fixed_day == x) _class_adds += 'on_focus';
					if(x == M.date() && SC.fixed_month == (M.month()+1) && SC.fixed_year == M.year() )
						_class_adds += ' today';


					days_list[x]['evo_dv_day_classes'] = _class_adds;
				}

			}else{
				days_list[SC.fixed_day] = {};
				days_list[SC.fixed_day]['e'] = eJSON;
			}

			var template_data = {};

			template_data['fixed_day_name'] = BUS.dms.d[ fixed_day_name_index ];
			template_data['fixed_day'] = parseInt(SC.fixed_day);
			template_data['cal_def'] = BUS.cal_def;
			template_data['days'] = days_list;
			template_data['calid'] = CAL.attr('id');
			template_data['width'] = days_in_month *(80)+130;
			template_data['marginLeft'] = SC.fixed_day *(-60)+130;
			template_data['dv_view_style'] = SC.dv_view_style;


			var fixed_day_start_unix =  (SC.dv_view_style == 'oneday') ?
				parseInt(SC.focus_start_date_range):
				parseInt(SC.focus_start_date_range) + ( (parseInt(SC.fixed_day) - 1) * 86400 );

			CAL.find('.evo_bottom').attr('data-fixed_unix', fixed_day_start_unix).data('fixed_unix', fixed_day_start_unix);

			var list_html = cd_html = '';

			// day box html
			if( SC.dv_view_style == 'def'|| SC.dv_view_style =='onedayplus') 
				cd_html = CAL.evo_HB_process_template({TD: template_data,part: 'evodv_cd'});
			
			// month strip html
			if( SC.dv_view_style != 'oneday' && SC.dv_view_style != 'onedayplus') 
				list_html = CAL.evo_HB_process_template({TD: template_data , part:'evodv_list'});

			if(type == 'replace'){
				CAL.find('.evodv_current_day').replaceWith( cd_html );
				if( SC.dv_view_style != 'oneday') CAL.find('.eventon_daily_list').replaceWith( list_html );
			}else{
				ELM = CAL.find('#eventon_loadbar_section');
				if( SC.dv_view_style != 'oneday') ELM.after( list_html );
				ELM.after( cd_html );
			}

			// appearance animation
			if(type != 'replace'){
				CAL.find('.evodv_current_day').fadeIn();
				CAL.find('.eventon_daily_list').delay(300).fadeIn(function(){
					set_daily_strip_sizes( CAL );
				});
			}else{				
				CAL.find('.evodv_current_day').show();
				CAL.find('.eventon_daily_list').show();
			}

			// POPULATE with events
			populate_with_events(CAL);

			_scroll_interaction(CAL);

			// show correct events in event list
			load_correct_events(CAL);	

			// swiping interaction
			touch_swiping_interaction( CAL );

			// remove pre laoder section
			CAL.find('.evodv_pre_loader').remove();		
		}

		// populate the day boxes with events information - + 2.0
		function populate_with_events(CAL){
			var cal_events = CAL.find('.eventon_list_event');
			SC = CAL.evo_shortcode_data();
			var _txt_events = CAL.evo_get_txt({V:'events'});

			CAL.find('.evo_dv_day').each(function(){
				var SU = parseInt($(this).data('unix'));
				var EU = SU + 86399;

				var EC = 0;
				var html = '';
				cal_events.each(function(index, elm){
					ED = $(elm).evo_cal_get_basic_eventdata();
					if( !ED) return;

					var inrange = CAL.evo_is_in_range({
						'S': SU,	'E': EU,	'start': ED.event_start_unix,
						'end':ED.event_end_unix
					});

					if(!inrange) return;

					EC++;
					html += "<em class='evodv_spot' data-title='"+ ED.event_title +"' data-eid='"+ ED.ID+"' style='background-color:"+ ED.hex_color+"'></em>";

				});

				// if more than 3 events in the date
				if( EC >3){
					html = "<em class='evodv_spot more' data-title='"+EC+" "+_txt_events+"'></em>";
				}

				if( html != ''){
					$(this).find('.evoday_events').html( html );
				}

			});
		}
	

	// show correct events for the day
		function load_correct_events(CAL, load_events_count){

			SC = CAL.evo_shortcode_data();
			BUS = $('#evo_global_data').data('d');

			fixed_day = SC.fixed_day;

			// sunix = SC.focus_start_date_range;
			// eunix = SC.focus_end_date_range;
			
			
			// get start unix using focus date
			sunix = parseInt( CAL.find('.evo_dv_day[data-date="'+fixed_day+'"]').data('unix') );

			// get using bottom if no date box
			if( !sunix )
				sunix = parseInt( CAL.find('.evo_bottom').data('fixed_unix'));
			
			eunix = sunix+ 86399;


			show = 0;
			CAL.find('.eventon_list_event').each(function(){
				$(this).removeClass('dayevent');
				time = $(this).data('time');
				if( time === undefined ) return;
				t = time.split('-');
				$(this).hide();
				this_show = false;

				// month long or year long events
				if( $(this).hasClass('month_long') || $(this).hasClass('year_long')){
					this_show = true;
				}else{
					if(CAL.evo_is_in_range({
						'S': sunix,
						'E': eunix,
						'start': t[0] ,
						'end':t[1] 
					})) this_show = true;
				}
				
				if( this_show){	
					$(this).addClass('dayevent');

					if( SC.show_limit == 'yes' && show == SC.event_count) return;
					$(this).show();	show++;	
				} 
			});


			// Showing the events
				if(show == 0){
					OD = CAL.evo_get_OD();
					eList = CAL.find('#evcal_list');

					if(eList.has('.no_events.eventon_list_event').length){
						eList.find('.no_events.eventon_list_event').show();
					}else{
						eList.append("<div class='eventon_list_event no_events'><p class='no_events' >"+ OD.lang_no_events +"</p></div>");
					}
					
				}else{
					CAL.find('.eventon_list_event.no_events').remove();
				}

			// Update current day no events
				var numevents = show;
				if(numevents!=='' && numevents!==false && numevents !== undefined ){
					ITM = CAL.find('.evodv_current_day .evodv_events');
					(numevents>1 || numevents == 0)? 
						ITM.html('<span>'+numevents+'</span>'+ BUS.txt.events) : 
						ITM.html('<span>'+numevents+'</span>'+ BUS.txt.event);
					ITM.show();
				}

			// show all events
			CAL.find('#evcal_list').removeClass('evo_hide');
		}
	
	// change the dates within month
		function changin_dates_within_month(CAL, type, ajax){
			SC = CAL.evo_shortcode_data();
			BUS = $('#evo_global_data').data('d');

			// focus adjust for current day strip
				CAL.find('.evodv_current_fixed_day').html( SC.fixed_day );
				DD = new Date( SC.fixed_year, SC.fixed_month-1, SC.fixed_day);
				CAL.find('.evodv_dayname').html( BUS.dms.d[ DD.getDay() ] );

			// focus adjust for strip
				STRIP = CAL.find('.eventon_daily_in');
				STRIP.find('.evo_day').removeClass('on_focus');
				STRIP.find('.evo_day[data-date="'+ SC.fixed_day +'"]').addClass('on_focus');
			
			if(ajax){	
				$('body').trigger('evo_run_cal_ajax', [CAL.attr('id'), 'none', 'dv_newday']);
			}else{
				load_correct_events(CAL);
			}
		}
	
	// ALL Calendar actions
		$('body')	
			// calendar view switching
			.on('evo_vSW_clicked_before_ajax',function(event, O, CAL, DD, reload_cal_data){
				if(!(O.hasClass('evodv'))) return;
				var SC = CAL.evo_shortcode_data();

				CAL.evo_update_cal_sc({F:'calendar_type', V: 'daily'});
				CAL.evo_update_cal_sc({F:'fixed_day', V: SC.fixed_day });
				CAL.evo_update_cal_sc({F:'dv_view_style', V: 'def'});

			})
			.on('evo_vSW_clicked',function(event, OBJ, CAL, DD){
				if(!(OBJ.hasClass('evodv'))) return;
				
				var reload_cal_data = false;
				var SC = CAL.evo_shortcode_data();

				DD.setUTCDate( 1 );

				if( SC.focus_start_date_range != Math.floor(DD.getTime()/1000) )
					reload_cal_data = true;

				CAL.evo_update_cal_sc({F:'focus_start_date_range',V: Math.floor(DD.getTime()/1000) });

				DD.setUTCMonth( DD.getUTCMonth() + 1);
				DD.setUTCDate( -1);

				CAL.evo_update_cal_sc({F:'focus_end_date_range', 
					V: Math.floor(DD.getTime()/1000) });


				DD.setUTCDate( SC.fixed_day ); // adjust date to fixed date

				CAL.evo_update_cal_sc({F:'calendar_type', V: 'daily'});
				CAL.evo_update_cal_sc({F:'fixed_day', V: SC.fixed_day });
				CAL.evo_update_cal_sc({F:'dv_view_style', V: 'def'});

				D = {};
				D['json'] = CAL.find('.evo_cal_events').data('events');
				D['other'] = {};
				D['sc'] = CAL.evo_shortcode_data();

				if( reload_cal_data ){
					$('body').trigger('evo_run_cal_ajax',[CAL.attr('id'),'none','filering']);
				}

				populate_DV_calendar( CAL, D);

			})
			// day stripe interaction
			.on('click', '.evodv_action',function(event){
				if( $(this).hasClass('scroll') ){
					eventon_daily_in = $(this).siblings('.eventon_daily_in');
					if($(this).hasClass('prev')){
						swiping('swiperight', eventon_daily_in);
					}else{// next
						swiping('swipeleft', eventon_daily_in);
					}
				// switch months
				}else{
					classN = ($(this).hasClass('next'))? 'evcal_btn_next':'evcal_btn_prev';
					calendar = $(this).closest('.ajde_evcal_calendar');
					
					calendar.find('.'+classN).trigger('click');
				}				
			})
			// use current day arrows to switch days
			.on('click', '.evodv_daynum_switch', function(){

				OBJ = $(this);
				var dir = OBJ.attr('data-dir');
				CAL = OBJ.closest('.ajde_evcal_calendar');
				SC = CAL.evo_shortcode_data();

				var runajax = SC.dv_view_style == 'def'? false:true;

				var daysinmonth = CAL.evo_day_in_month({Y: SC.fixed_year, M: SC.fixed_month});

				// remove disable class
				OBJ.parent().find('span').removeClass('disable');				

				if(dir == 'next'){
					if(SC.fixed_day == daysinmonth){	
						CAL.evo_update_cal_sc({	F:'dv_scroll_type', V:'arrow'	});
						CAL.find('.evcal_btn_next').trigger('click');
					}else{
						
						m_date = SC.fixed_year+'-'+ SC.fixed_month +'-'+SC.fixed_day;
						DD = moment( m_date, 'YYYY-M-D');
						DD.add( 1 ,'days');	

						CAL.evo_update_cal_sc({F:'fixed_day', V:DD.date() });
						changin_dates_within_month(CAL, 'days', runajax);
					}					
				}else{ // previous
					if(SC.fixed_day == 1){
						CAL.evo_update_cal_sc({	F:'dv_scroll_type', V:'arrow'	});
						CAL.find('.evcal_btn_prev').trigger('click');
					}else{

						m_date = SC.fixed_year+'-'+ SC.fixed_month +'-'+SC.fixed_day;
						DD = moment( m_date, 'YYYY-M-D');
						DD.subtract( 1 ,'days');	

						CAL.evo_update_cal_sc({F:'fixed_day', V:DD.date() });
						changin_dates_within_month(CAL, 'days', runajax);
					}
				}			

			})
			// click on a day from strip
			.on( 'click','.evo_day',function(){
				var new_day = $(this).find('.evo_day_num').html();
				var CAL = $(this).closest('.ajde_evcal_calendar');
				
				var daysinmonth = CAL.find('.eventon_daily_in .evo_day').length;
				var thisday = parseInt($(this).find('.evo_day_num').html());
				var arrows =  CAL.find('.evodv_daynum');
				arrows.find('span').removeClass('disable'); // remove disable class

				// add disable class to side arrows
				if(thisday==1 ) arrows.find('.prev').addClass('disable');
				if(thisday==daysinmonth) arrows.find('.next').addClass('disable');

				// Update CAL SC with new date
				CAL.evo_update_cal_sc({F:'fixed_day', V:new_day });

				changin_dates_within_month(CAL, 'strip', false);
			})
			// tooltip
			.on('mouseover','.evodv_spot',function(){
				OBJ = $(this);
				
				PAR = OBJ.closest('.eventon_daily_list');

				p = OBJ.offset();
				t = PAR.offset();
				w = PAR.width();
				xleft = p.left - t.left;
				xtop = p.top - t.top;

				TITLE = OBJ.data('title');

				// adjust side of the tooltip
				if((w/2) > xleft){
					HTML = "<em class='evodv_tooltip' style='top:"+(xtop-40)+"px;left:"+(xleft+5)+"px;'>"+TITLE+"</em>";
				}else{
					xright = w - xleft;
					HTML = "<em class='evodv_tooltip left' style='top:"+(xtop-40)+"px;right:"+(xright-5)+"px;'>"+TITLE+"</em>";
				}
				
				PAR.append(HTML);

			})
			.on('mouseout','.evodv_spot',function(){
				OBJ = $(this);
				OBJ.closest('.eventon_daily_list').find('.evodv_tooltip').remove();

			})
		;
		

	// touch function
		function touch_swiping_interaction(CAL){
			var slide_area = CAL.find('.eventon_daily_in');
			slide_area.on('swipeleft', function(event){					
				if( !$(event.currentTarget).hasClass('eventon_daily_in')) return;
				event.stopImmediatePropagation();	
				event.preventDefault();		

				console.log('eed');

				swiping('swipeleft', $(this));
			});
			slide_area.on('swiperight', function(event){				
				swiping('swiperight', $(this));
				event.preventDefault();
			});
		}
		

		function swiping(direction, OBJ){
			var leftNow = parseInt(OBJ.css('marginLeft'));
			var Pwid = OBJ.parent().width();
			var width = parseInt(OBJ.css('width') );
			one_day_width = parseInt(OBJ.find('.evo_day:gt(20)').outerWidth());
			maxMLEFT = (width-Pwid)*(-1);

			STRIP = OBJ.closest('.eventon_dv_outter');
			STRIP_Width = STRIP.width();
			ARROW_Width = STRIP.find('span.prev').width() + STRIP.find('span.next').width();

			swipeMove = (one_day_width*5);

			if( (swipeMove + ARROW_Width) > STRIP_Width ){
				swipeMove = (one_day_width*2);
			}
			
			if(direction =='swipeleft'){
				var newLEFT = ( leftNow - swipeMove );	
				// /console.log(newLEFT);

				if( newLEFT*(-1) < (width) ){
					newLEFT = ( newLEFT <maxMLEFT)? maxMLEFT: newLEFT;
					OBJ.stop().animate({'margin-left': newLEFT },function(){
						scroll_o_switch( OBJ.parent() );
					});
				}
			}else{
				var newLEFT = ( leftNow + swipeMove );	
				// /console.log(newLEFT);

				newLEFT = ( newLEFT >0 )? 0: newLEFT;
				OBJ.stop().animate({'margin-left': newLEFT },function(){
					scroll_o_switch( OBJ.parent() );
				});
			}
		}
	// adjust margin left when window resized
		$(window).on('resize', function(){
			$('.eventon_daily_list').each(function(){
				adjust_days_width( $(this));				
			});
		});
	
	// daily list sliders	
		function set_daily_strip_sizes(cal){
			if(cal === undefined) return;

			var holder = cal.find('.eventon_daily_list');
			adjust_days_width(holder);
			scroll_o_switch(holder);
		}

			function adjust_days_width(holder){
				CAL = holder.closest('.ajde_evcal_calendar');
				SC = CAL.evo_shortcode_data();

				if(SC === undefined) return;

				var day_holder = holder.find('.eventon_daily_in');
				var days = day_holder.children('.evo_day');	
				var day_width = parseInt(day_holder.find('.evo_day:gt(20)').outerWidth());
				OUTTERWidth = parseInt(day_holder.parent().width());

				wALLDAYS = (parseInt(days.length)+2 )* (day_width);
													

				FOCUSday = parseInt( SC.fixed_day );
				LEFTwidth = (FOCUSday-1) * day_width;
				RIGHTwidth = wALLDAYS - LEFTwidth;

				LEFTmargin = ( RIGHTwidth > OUTTERWidth ) ? LEFTwidth: wALLDAYS-OUTTERWidth;
				LEFTmargin = -1* LEFTmargin;

				day_holder.css({'width':wALLDAYS, 'margin-left':LEFTmargin});

				// /console.log(OUTTERWidth+' '+wALLDAYS+' '+LEFTmargin+' '+LEFTwidth+' '+RIGHTwidth);

				//console.log(LEFTmargin);
			}
		
		// scroll or switch months
			function scroll_o_switch(list){
				holder = list.find('.eventon_daily_in');
				current_marginleft = parseInt(holder.css('marginLeft'));
				max_marginleft = parseInt(holder.parent().width()) - parseInt(holder.width());
				

				// set default
				holder.siblings('.next').attr({'class':'evodv_action next scroll'});
				holder.siblings('.prev').attr({'class':'evodv_action prev scroll'});

								
				if( current_marginleft <= max_marginleft){
					holder.siblings('.next').removeClass('scroll').addClass('switch');
					holder.siblings('.prev').removeClass('switch').addClass('scroll');
				}

				if(current_marginleft >= 0){
					holder.siblings('.next').removeClass('switch').addClass('scroll');
					holder.siblings('.prev').removeClass('scroll').addClass('switch');
				}
				

			}

	// DEP
		function _scroll_interaction(CAL){
			CAL.find('.eventon_daily_in').mousewheel(function(e, delta) {
				//$(this).scrollLeft -= (delta * 40);
				OBJ = $(this);

				var cur_mleft = parseInt(OBJ.css('marginLeft')),
					width = parseInt(OBJ.css('width') ),
					Pwid = OBJ.parent().width();
				maxMLEFT = (width-Pwid)*(-1);

				
				if( cur_mleft<=0){
					
					var new_marl = (cur_mleft+ (delta * 500));					
					if(new_marl>0){ new_marl=0;}
					
					// moving to left
					if(delta <0 && ( (new_marl*(-1))< (width -200)) ){
						new_marl = ( new_marl < maxMLEFT)? maxMLEFT: new_marl;
						OBJ.stop().animate({'margin-left': new_marl },function(){
							scroll_o_switch( OBJ.parent().parent() );
						});
					
					}else if(delta >0){
						OBJ.stop().animate({'margin-left': new_marl },function(){
							scroll_o_switch( OBJ.parent().parent() );
						});
					}
				}
				e.preventDefault();
			});
		}
});