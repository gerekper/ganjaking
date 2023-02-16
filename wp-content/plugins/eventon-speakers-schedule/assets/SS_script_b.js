/**
 * Javascript: Lists and Items for eventon
 * @version  2.0.1
 */
jQuery(document).ready(function($){

	// load event edit page data
		$('body').on('evo_ajax_success_eventedit_onload', function(event, OO, data){
			$('#evo_pageload_data_ss').html( data.content_array.evoss);
			_run_sortable_schedules();
		});


// schedule	
	$('.ajde_popup_text').find('.evossh_stime').timepicker({'step': 5});
	$('.ajde_popup_text').find('.evossh_etime').timepicker({'step': 5});

	// sortable schedule list		
		function _run_sortable_schedules(){
			$('body').find('.evosch_oneday_schedule').each(function(){
				$(this).sortable({
					update:function( event, ui){
						
						var FORM = $(this).closest('form');
						var BOX = $(this).closest('.evosch_blocks_list');
						var ajaxdataa = { };
						ajaxdataa['action']='evoss_save_schedule_order';
						ajaxdataa['eventid'] = BOX.data('eventid');

						var BLS = {};
						BOX.find('li.evosch_block').each(function(i){
							if( !( $(this).data('day') in BLS )) BLS[ $(this).data('day')] = {};
							BLS[ $(this).data('day')][i] = $(this).attr('id');
						});

						ajaxdataa['order'] = BLS;
						console.log(ajaxdataa);

						$(this).evo_admin_get_ajax({
							'ajaxdata': ajaxdataa,
							'uid':'evoss_sort_schedule'
						});

					}
				});
			});
		}
		$('body')
		.on('evo_ajax_beforesend_evoss_sort_schedule',function(event, OO){
			$('body').find('.evosch_blocks_list').addClass('evoloading');
		}).on('evo_ajax_success_evoss_sort_schedule',function(event, OO){
			$('body').find('.evosch_blocks_list').removeClass('evoloading');
		});

	// save new from form
		$('body').on('click','.evoss_add_new_schedule',function(){
			OBJ = $(this);
			FORM = OBJ.closest('form');
			LB = $('body').find('.evo_lightbox.evo_config_schedule');
									
			// field validation
				error = 0;
				FORM.find('.evoss_field').each(function(){
					THIS = $(this);
					if(THIS.attr('name') =='evo_sch_title' && THIS.val()=='' || THIS.val() === undefined ) error++;
				});

			// pass on date value
			const dayval = FORM.find('.evossh_date option:selected').attr('data-date');
			FORM.find('input[name="day"]').val( dayval );

			
			if(error > 0){
				LB.evo_lightbox_show_msg({'type':'bad','message':'Required fields missing!'});
			}else{
				FORM.evo_ajax_lightbox_form_submit({
					'lightbox_key':'evo_config_schedule',
					'uid':'evoss_save_schedule',
					'hide_lightbox':2000
				});
			}
		});
		$('body').on('evo_ajax_success_evoss_save_schedule',function(event, OO, data){
			$('body').find('.evosch_blocks_list').html(data.content);
			_run_sortable_schedules();
		});
		$('body').on('evo_ajax_success_evoss_edit_schedule',function(event, OO, data){
			
		});

	// delete processed
		$('body').on('evo_ajax_beforesend_evoss_del_schedule',function(event, OO){
			$('body').find('.evosch_blocks_list').addClass('evoloading');
		});
		$('body').on('evo_ajax_complete_evoss_del_schedule',function(event, OO){
			$('body').find('.evosch_blocks_list').removeClass('evoloading');
		});
		$('body').on('evo_ajax_success_evoss_del_schedule',function(event, OO, data){
			$('body').find('.evosch_blocks_list').html(data.content);
			_run_sortable_schedules();
		});


	// switching the schedule dates
		$('body').on('click','.evosch_nav li',function(){
			OBJ = $(this);
			DAY = OBJ.attr('data-day');
			OBJ.parent().find('li').removeClass('show');
			OBJ.addClass('show');
			
			OBJ.closest('.evosch_blocks_list').find('.evosch_oneday_schedule').removeClass('show');
			OBJ.closest('.evosch_blocks_list').find('ul.evosch_date_'+DAY).addClass('show');
		});

});