/** 
 * Javascript for countdown addon
 * @version  0.12
 */

jQuery(document).ready(function($){

	$.fn.start_timer = function(opt){

		var obj = this;	
		
		function run_timer(){	
			
			var until_ = parseInt(obj.attr('data-et'));			
			var lang = $.parseJSON(obj.attr('data-timetx'));

			// for lightbox event cards
				p = obj.closest('.evo_lightbox_content');
				var pp = ( p.length>0) ? true: false;

				if(pp){
					obj.removeClass('is-countdown');
					obj.html('');
				}

			obj.countdown({
				labels: [lang.yr, lang.o, lang.w, lang.d, lang.h, lang.m, lang.s], 
				labels1: [lang.yr, lang.o, lang.w, lang.d, lang.h, lang.m, lang.s], // for one value
				//layout:'<b>{d<}{dn} {dl} and {d>}</b>'+ '<b>{hn} {hl}</b><b>{mn} {ml}, {sn} {sl}</b>',
				until: +until_,
				onExpiry: function(){
					ex_tx = obj.siblings('.evocd_text').attr('data-ex_tx');
					obj.siblings('.evocd_text').html(ex_tx).addClass('timeexpired');
					obj.fadeOut();

					ex_ux = obj.attr('data-ex_ux');

					// blackout
					if(ex_ux=='3')
						obj.closest('.eventon_list_event').addClass('blackout');

					// dont do anything
					if(ex_ux=='2')
						obj.closest('.evcal_list_a').attr('data-ux_val','X');

					// hide event
					if(ex_ux=='1')
						obj.closest('.eventon_list_event').slideUp(function(){
							$(this).remove();
						});

					// custom trigger action
					if( obj.data('trig') !== undefined){
						$('body').trigger( obj.data('trig'), [ $(this) , $(this).data('refresher')] );
					}
				},
				onTick: function(periods){					
					var seconds = get_seconds(periods);
					obj.attr('data-et',seconds);
				}
			}); 

			//obj.countdown('pause');
		}

		run_timer();
	}

	// run timer scripts
		$('body').on('evolightbox_show',function(){
			init_timers();
		});
		$('body').on('lightbox_before_event_closing',function(event, LB){
			timer = LB.find('.evocd_time');
			timer.countdown('destroy');
		});
		
		$(document).ajaxComplete(function(){
			init_timers();	
		});

		// each cal after loaded via ajax
		$('body').on('evo_init_ajax_success_each_cal', function(event, data, calid, v){			
			init_timers();
		});

		function init_timers(){
			$('body').find('.evocd_time').each(function(){
				$(this).start_timer();
			});

			$('body').find('.evocd_ondemand_timer').each(function(){
				$(this).start_timer();
			});
		}
		

	// get seconds value
		function get_seconds(periods){
			var seconds = periods[6];
			seconds += periods[5]*60;
			seconds += periods[4]*3600;
			seconds += periods[3]*86400;

			return seconds;
		}
	
	// INTEGRATION
		// fullcal intergration
			$('body').on('click','.evo_fc_day',function(){
				$('.ajde_evcal_calendar').ajaxComplete(function(){
					//$('.evocd_time').countdown('toggle');
					$('body').evo_timers();			
				});
			});
		// daily view intergration
			$('body').on('click','.evo_day',function(){
				$('.ajde_evcal_calendar').ajaxComplete(function(){
					//$('.evocd_time').countdown('toggle');
					$('body').evo_timers();			
				});
			});
			$('body').on('click','.evodv_daynum span',function(){
				$('.ajde_evcal_calendar').ajaxComplete(function(){
					//$('.evocd_time').countdown('toggle');
					$('body').evo_timers();			
				});
			});

		// weekly view
			$('body').on('click','.evowv_arrow',function(){
				$('.ajde_evcal_calendar').ajaxComplete(function(){
					//$('.evocd_time').countdown('toggle');
					$('body').evo_timers();			
				});
			});

		// month jumper
			$('body').on('click','.evo_j_container a',function(){
				$('.ajde_evcal_calendar').ajaxComplete(function(){
					$('body').evo_timers();			
				});
			});

	
	
});

