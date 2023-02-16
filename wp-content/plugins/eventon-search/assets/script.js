/*
	Javascript: Eventon Daily View
	version:	0.24
*/
jQuery(document).ready(function($){

	$('.evo-search').on('click',function(){
		var section = $(this).parent().siblings('.evo_cal_above_content');
		var item = section.find('.evo_search_bar');

		item.slideToggle('2000','easeInOutCubic', function(){
			if(item.is(':visible'))
				item.find('input').focus();
		});
	});

	// Enter key detection for pc
		$.fn.enterKey = function (fnc) {
		    return this.each(function () {
		        $(this).keypress(function (ev) {
		            var keycode = (ev.keyCode ? ev.keyCode : ev.which);
		            if (keycode == '13') {
		                fnc.call(this, ev);
		            }
		        })
		    })
		}
	// get shortcode arguments
		$.fn.evosr_shortcodes = function(){			
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

	// Submit search from 
		$('body').on('click','.evo_do_search',function(){
			do_search_box( $(this) );
		});
		$(".evosr_search_box input").enterKey(function () {
			do_search_box( $(this).siblings('.evo_do_search') );
		});

		function do_search_box(OBJ){
			SearchVal = OBJ.closest('.evosr_search_box').find('input').val();
			Evosearch = OBJ.closest('.EVOSR_section');
			OBJ.closest('.evo_search_entry').find('.evosr_msg').hide();
			console.log(SearchVal);

			if( SearchVal === undefined || SearchVal == ''){
				OBJ.closest('.evo_search_entry').find('.evosr_msg').show();
				return false;
			}
			SC = Evosearch.find('span.data').evosr_shortcodes();
			
			var data_arg = {
				action: 		'search_evo_events',
				search: 		SearchVal,
				shortcode: SC
			};
			$.ajax({
				beforeSend: function(){
					Evosearch.find('.evo_search_results_count').hide();
					Evosearch.addClass('searching');
				},
				type: 'POST',
				url:EVOSR_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					Evosearch.find('.evo_search_results').html( data.content);

					if(Evosearch.find('.no_events').length>0){

					}else{
						// find event count
						Events = Evosearch.find('.evo_search_results').find('.eventon_list_event').length;
						Evosearch.find('.evo_search_results_count span').html( Events);
						Evosearch.find('.evo_search_results_count').fadeIn();
					}
					


				},complete: function(){
					Evosearch.removeClass('searching');
				}
			});
		}

	//submit search 
		$('body').on('click','.evosr_search_btn',function(){
			search_within_calendar( $(this).siblings('input') );
		});
		$(".evo_search_bar_in input").enterKey(function () {
			search_within_calendar( $(this) );
		});

		function search_within_calendar(obj){
			
		   	var ev_cal= obj.closest('.ajde_evcal_calendar');
		   	ev_cal.find('.cal_arguments').attr({'data-s': obj.val()});

		   	var cal_head = ev_cal.find('.calendar_header');	
			var evodata = ev_cal.find('.evo-data');

			var evcal_sort = cal_head.siblings('div.evcal_sort');						
			var sort_by=evcal_sort.attr('sort_by');
			var evodata = ev_cal.evo_getevodata();
			var data_arg = {
				action: 		'the_ajax_hook',
				sort_by: 		sort_by, 	
				direction: 		'none',
				filters: 		ev_cal.evoGetFilters(),
				shortcode: 		ev_cal.evo_shortcodes(),
				evodata: 		evodata
			};

			data_arg = cal_head.evo_otherVals({'data_arg':data_arg});	

			$.ajax({
				beforeSend: function(){
					ev_cal.find('.eventon_events_list').slideUp('fast');
					ev_cal.find('#eventon_loadbar').slideDown().css({width:'0%'}).animate({width:'100%'});
				},
				type: 'POST',
				url:the_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					// /alert(data);
					//console.log(data);
					ev_cal.find('.eventon_events_list').html(data.content);
														
				},complete:function(){
					ev_cal.find('#eventon_loadbar').css({width:'100%'}).slideUp();
					ev_cal.find('.eventon_events_list').delay(300).slideDown('slow');
					ev_cal.evoGenmaps({'delay':400});
				}
			});

			// for fullcal
				if(ev_cal.hasClass('evoFC')){			 	
				 	// AJAX data array
					var data_arg_2 = {
						action: 	'evo_fc',
						next_m: 	evodata.cmonth,	
						next_y: 	evodata.cyear,
						next_d: 	data_arg.fc_focus_day,
						change: 	'',
						filters: 		ev_cal.evoGetFilters(),
						shortcode: 		ev_cal.evo_shortcodes(),
					};
					$.ajax({
						beforeSend: function(){
							//this_section.slideUp('fast');
						},
						type: 'POST',
						url:the_ajax_script.ajaxurl,
						data: data_arg_2,
						dataType:'json',
						success:function(data){
							var strip = cal_head.parent().find('.evofc_months_strip');
							strip.html(data.month_grid);

							//width adjustment
							var month_width = parseInt(strip.parent().width());
							strip.find('.evofc_month').width(month_width);
						}
					});
				}

			// for dailyview
				if(ev_cal.hasClass('evoDV')){
					// AJAX data array
					var data_arg_3 = {
						action: 	'the_ajax_daily_view',
						next_m: 	evodata.cmonth,	
						next_y: 	evodata.cyear,
						next_d: 	data_arg.dv_focus_day,
						cal_id: 	ev_cal.attr('id'),
						send_unix: 	evodata.send_unix,
						filters: 		ev_cal.evoGetFilters(),
						shortcode: 		ev_cal.evo_shortcodes(),
					};
					$.ajax({
						beforeSend: function(){
							//this_section.slideUp('fast');
						},
						type: 'POST',
						url:the_ajax_script.ajaxurl,
						data: data_arg_3,
						dataType:'json',
						success:function(data){
							var this_section = cal_head.parent().find('.eventon_daily_in');
							this_section.html(data.days_list);
						}
					});
				}		
			// for weeklyview
				if(ev_cal.hasClass('evoWV')){
					// AJAX data array
					var data_arg_4 = {
						action: 	'the_ajax_wv2',
						next_m: 	evodata.cmonth,	
						next_y: 	evodata.cyear,
						focus_week: 	data_arg.wv_focus_week,
						filters: 		ev_cal.evoGetFilters(),
						shortcode: 		ev_cal.evo_shortcodes(),
					};
					$.ajax({
						beforeSend: function(){
							//this_section.slideUp('fast');
						},
						type: 'POST',
						url:the_ajax_script.ajaxurl,
						data: data_arg_4,
						dataType:'json',
						success:function(data){
							// save width data
							var width1 = ev_cal.find('.evoWV_days').width();
							var width2 = ev_cal.find('.eventon_wv_days').width();
							var width3 = ev_cal.find('.evo_wv_day').width();
							var ml1 = ev_cal.find('.eventon_wv_days').css('margin-left');

							// add content
							ev_cal.find('.eventon_wv_days')
								.parent().html(data.content);

							ev_cal.find('.evoWV_days').css({'width':width1});
							ev_cal.find('.eventon_wv_days').css({'width':width2, 'margin-left':ml1});
							ev_cal.find('.evo_wv_day').css({'width':width3});

						}
					});
				}			

		}
	
});