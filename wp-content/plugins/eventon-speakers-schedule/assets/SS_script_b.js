/**
 * Javascript: Lists and Items for eventon
 * @version  0.1
 */
jQuery(document).ready(function($){

// Speaker
	// change speakers from List
		$('.evo_tax_list_terms_items').on('click','b',function(){
			if($(this).hasClass('fa-circle-o')){// selecting
				$(this).addClass('fa-dot-circle-o');
				$(this).removeClass('fa-circle-o');
			}else{
				$(this).removeClass('fa-dot-circle-o');
				$(this).addClass('fa-circle-o');
			}
			update_selected_speakers( $(this).closest('.evo_tax_list_terms_items'));
		});

		function update_selected_speakers(OBJ){
			selected = OBJ.find('b.fa-dot-circle-o').length;
			$string = '';
			if(selected>0){				
				OBJ.find('b.fa-dot-circle-o').each(function(){
					$string += $(this).attr('data-value')+',';
				});
			}
			OBJ.find('input').attr('value', $string);
		}
	// save changes from list
		$('body').on('click','.evo_tax_list_terms_items_save',function(){
			OBJ = $(this);
			BOX = OBJ.closest('.evomb_body');
			LIST = $(this).parent().parent().find('.evo_tax_selected_list_values');

			var ajaxdataa = { };
			ajaxdataa['action']='evoss_change_speaker';
			ajaxdataa['values'] = LIST.val();
			ajaxdataa['eventid'] = OBJ.attr('data-eventid');
			
			$.ajax({
				beforeSend: function(){
					BOX.addClass('loading');
				},
				type: 'POST',
				url:evoss_ajax_script.evoss_ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						BOX.find('.evo_tax_saved_terms').html(data.content);
					}else{
						BOX.find('.evo_tax_msg').html( data.status).show();
					}
				},complete:function(){
					BOX.removeClass('loading');
				}
			});
		});

	// click on edit speaker values
		$('.evo_tax_saved_terms').on('click','p',function(){
			OBJ = $(this);
			BOX = OBJ.closest('.evomb_body');
			ENTRY = $('#evospk_new_block_form' );

			var ajaxdataa = { };
			ajaxdataa['action']='evoss_get_speaker_values';
			ajaxdataa['termid'] = OBJ.attr('data-termid');
			
			$.ajax({
				beforeSend: function(){
					BOX.addClass('loading');
				},
				type: 'POST',
				url:evoss_ajax_script.evoss_ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$.each(data.meta, function(i, val){
							if(i=='evo_speaker_desc'){
								ENTRY.find('.evoss_field[name="'+i+'"]').html(val );
							}else{
								ENTRY.find('.evoss_field[name="'+i+'"]').attr('value', val );
							}							
						});
						if(data.imgsrc!=''){
							ENTRY.find('span.image_src img').attr('src', data.imgsrc).show();
						}
						ENTRY.find('.evoss_add_new_speaker').attr('data-termid',ajaxdataa.termid);
						//ENTRY.slideDown();
					}else{
						BOX.find('.evo_tax_msg').html('Could not complete request.').show();
					}
				},complete:function(){
					BOX.removeClass('loading');
					$('.evospk_open_speaker_form').trigger('click');
				}
			});
		});
	// add new speaker
		$('body').on('click','.evoss_add_new_speaker',function(){
			OBJ = $(this);
			ENTRY = OBJ.closest('.evo_tax_entry');
			MBBOX = $('#ev_speakers');
			BOX = OBJ.closest('.ajde_popup_text');
				
			var ajaxdataa = { };
			ajaxdataa['action']='evoss_new_speaker';
			ajaxdataa['eventid']= OBJ.attr('data-eventid');
			ajaxdataa['termid']= OBJ.attr('data-termid');

			error = 0;
			ENTRY.find('.evoss_field').each(function(){
				THIS = $(this);
				if(THIS.attr('name')=='evo_speaker_name' && (THIS.val() === undefined || THIS.val()=='')) error++;
				ajaxdataa[ THIS.attr('name')] = THIS.val();
			});

			if(error==0){
				$.ajax({
					beforeSend: function(){	BOX.addClass('loading');	},
					type: 'POST',
					url:evoss_ajax_script.evoss_ajaxurl,
					data: ajaxdataa,
					dataType:'json',
					success:function(data){
						if(data.status=='good'){
							MBBOX.find('.evo_tax_saved_terms').html(data.content);
							MBBOX.find('.evo_tax_list_terms_items').html(data.list); // update list
							setTimeout(function(){
								BOX.parent().find('.ajde_close_pop_btn').trigger('click'); // close lightbox
							},2000);
							$('body').trigger('ajde_lightbox_show_msg',['Successfully Processed!','evospk_new_block']);
						}else{// addition was not good
							$('body').trigger('ajde_lightbox_show_msg',[data.status,'evospk_new_block','bad']);
						}
					},complete:function(){	BOX.removeClass('loading');	}
				});
			}else{
				$('body').trigger('ajde_lightbox_show_msg',['Required fields missing!','evospk_new_block','bad']);
			}			
		});		
	// Reset form
		$('.ajde_close_pop_btn').on('click',function(){
			if($(this).closest('.ajde_popup').hasClass('evospk_new_block')){
				FORM = $('#evospk_new_block_form');
				FORM.find('.evoss_field').each(function(){
					$(this).attr('value','');
					if($(this).is('textarea')) $(this).html('');
				});
				FORM.find('.evospk_profile_image').attr('src','').hide();
				FORM.find('.evo_tax_msg').html('').hide();
				FORM.find('.evoss_add_new_speaker').attr('data-termid','');
			}
		});
	
	// remove speaker from event
		$('.evo_tax_saved_terms').on('click','i',function(event){
			event.preventDefault();
			event.stopPropagation();

			OBJ = $(this);
			BOX = OBJ.closest('.evomb_body');

			termid = OBJ.parent().data('termid');

			LISTitem = BOX.find('.evo_tax_list_terms_items b[data-value="'+termid+'"]');
			LISTitem.attr('class','fa fa-circle-o');
			update_selected_speakers( BOX.find('.evo_tax_list_terms_items') );

			BOX.find('.evo_tax_list_terms_items_save').trigger('click');

		});
	// open divs
		$('.evcal_speaker_data_section').on('click','.evo_btn',function(){
			$(this).toggleClass('opened');
			BOX = $(this).closest('.evo_meta_inside_row');
			BOX.next('.sections').toggle();
		});
// schedule	
	$('.ajde_popup_text').find('.evossh_stime').timepicker({'step': 5});
	$('.ajde_popup_text').find('.evossh_etime').timepicker({'step': 5});

	// sortable schedule list
		_run_sortable_schedules();
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

						$.ajax({
							beforeSend: function(){BOX.addClass('loading');},
							type: 	'POST',
							url: 	evoss_ajax_script.evoss_ajaxurl,
							data: 	ajaxdataa,
							dataType:'json',
							success:function(data){

							},
							complete:function(){	BOX.removeClass('loading');	}
						});
					}
				});
			});
		}

	// new
		$('body').on('click','.evoss_add_new_schedule',function(){
			OBJ = $(this);
			BOX = OBJ.closest('.ajde_popup_text');
			MBBOX = $('#ev_schedule');
			ENTRY = BOX.find('.evo_tax_entry' );

			var ajaxdataa = { };
			ajaxdataa['action']='evoss_save_schedule';
			ajaxdataa['eventid'] = OBJ.attr('data-eventid');
			ajaxdataa['key'] = OBJ.attr('data-key');
			ajaxdataa['day'] = ENTRY.find('.evossh_date option:selected').attr('data-date');

			// field validation
				error = 0;
				BOX.find('.evoss_field').each(function(){
					THIS = $(this);
					if(THIS.hasClass('evossh_spk')) return true;
					if(THIS.val() === undefined || THIS.val()=='') error++;
					ajaxdataa[ THIS.attr('name')] = THIS.val();
				});

				BOX.find('.evossh_spk').each(function(){
					if($(this).is(':checked')){
						ajaxdataa[ $(this).attr('name')] = $(this).val();
					}
				});
			
			if(error==0){
				$.ajax({
					beforeSend: function(){BOX.addClass('loading');},
					type: 	'POST',
					url: 	evoss_ajax_script.evoss_ajaxurl,
					data: 	ajaxdataa,
					dataType:'json',
					success:function(data){
						if(data.status=='good'){
							MBBOX.find('.evosch_blocks_list').html(data.content);
							setTimeout(function(){
								BOX.parent().find('.ajde_close_pop_btn').trigger('click'); // close lightbox
							},2000);
							// show success message
							$('body').trigger('ajde_lightbox_show_msg',['Successfully Processed!','evosch_new_block']);
							
							_run_sortable_schedules();
						}else{
							$('body').trigger('ajde_lightbox_show_msg',[data.status,'evosch_new_block','bad']);
						}
					},complete:function(){	BOX.removeClass('loading');	}
				});
			}else{
				$('body').trigger('ajde_lightbox_show_msg',['Required fields missing!','evosch_new_block','bad']);
			}
		});
	// reset fields when form closed
		$('.ajde_close_pop_btn').on('click',function(){
			if($(this).closest('.ajde_popup').hasClass('evosch_new_block')){
				FORM = $('#evosch_new_block_form');
				FORM.find('.evoss_field').each(function(){
					$(this).attr('value','');
					if($(this).is('textarea')){
						$(this).html('');
					}
				});
				FORM.find('.evoss_add_new_schedule').attr('data-key','');
			}
		});
	// remove schedule from list
		$('.evosch_blocks_list').on('click','em',function(event){
			event.preventDefault();
			event.stopPropagation();

			OBJ = $(this);
			BOX = OBJ.closest('.evomb_body');
			LIST = OBJ.closest('.evosch_blocks_list');
			LI = OBJ.closest('li');

			var ajaxdataa = { };
			ajaxdataa['action']='evoss_delete_schedule';
			ajaxdataa['eventid'] = LIST.attr('data-eventid');
			ajaxdataa['day'] = LI.attr('data-day');
			ajaxdataa['key'] = LI.attr('id');
			$.ajax({
				beforeSend: function(){	BOX.addClass('loading');	},
				type: 'POST',
				url:evoss_ajax_script.evoss_ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						BOX.find('.evosch_blocks_list').html(data.content);
						_run_sortable_schedules();
					}
				},complete:function(){	BOX.removeClass('loading');	}
			});
		});
	// edit
		$('.evosch_blocks_list').on('click','i',function(event){
			OBJ = $(this);
			BOX = OBJ.closest('.evomb_body');
			LI = OBJ.closest('li');
			LIST = OBJ.closest('.evosch_blocks_list');
			LIGTHBOX = $('.evosch_new_block');

			var ajaxdataa = { };
			ajaxdataa['action']='evoss_form_schedule';
			ajaxdataa['eventid'] = LIST.attr('data-eventid');
			ajaxdataa['key'] = LI.attr('id');
			ajaxdataa['day'] = LI.attr('data-day');
			
			$.ajax({
				beforeSend: function(){
					BOX.addClass('loading');
				},
				type: 'POST',
				url:evoss_ajax_script.evoss_ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						LIGTHBOX.find('.ajde_popup_text').html(data.content);
					}else{
						BOX.find('.evo_tax_msg').html('Could not complete request.').show();
					}
				},complete:function(){	
					BOX.removeClass('loading');
					$('.evosch_open_schedule_form').trigger('click', ['forced']);
				}
			});
		});
	// open add new form
		$('.evosch_open_schedule_form').on('click',function(event, param){
			if(param=='forced') return true;

			OBJ = $(this);
			BOX = OBJ.closest('.evomb_body');
			LIGTHBOX = $('.evosch_new_block');

			var ajaxdataa = { };
			ajaxdataa['action']='evoss_form_schedule';
			ajaxdataa['eventid'] = OBJ.attr('data-eventid');
			$.ajax({
				beforeSend: function(){		},
				type: 'POST',
				url:evoss_ajax_script.evoss_ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						LIGTHBOX.find('.ajde_popup_text').html(data.content);
					}else{
						BOX.find('.evo_tax_msg').html('Could not complete request.').show();
					}
				},complete:function(){		}
			});
		});
	
	// switching the schedule dates
		$('.evosch_blocks_list').on('click','.evosch_nav li',function(){
			OBJ = $(this);
			DAY = OBJ.attr('data-day');
			OBJ.parent().find('li').removeClass('show');
			OBJ.addClass('show');
			
			OBJ.closest('.evosch_blocks_list').find('.evosch_oneday_schedule').removeClass('show');
			OBJ.closest('.evosch_blocks_list').find('ul.evosch_date_'+DAY).addClass('show');
		});

});