/**
 * JS for RSVP admin section 
 * @version  2.9
 */
jQuery(document).ready(function($){
	
	init();	
		
	// INITIATE script
	function init(){}

	// on save event post update page meta values
		$('body')
		.on('evo_ajax_form_complete_evors_save_eventedit_settings',function(event, OO, form){
			var fordata = form.serializeArray();
			$.each(fordata, function(index, value){
				$('body').find('input[value="' + value.name + '"]')
				.closest('tr').find('textarea').val( value.value );
			});
		});

	// GET attendee list	
		// For repeating events -> selecting a repeat and submit
			$('body').on('click','#evors_VA_submit',function(){

				var data_arg = {
					action: 		'the_ajax_evors_a1',
					e_id:			$(this).data('e_id'),
					ri: $('#evors_event_repeatInstance').val(),
					postnonce: evors_admin_ajax_script.postnonce, 
				};
				//console.log(data_arg);			
				$.ajax({
					beforeSend: function(){ $('#evors_view_attendees').addClass('loading'); },
					type: 'POST',
					url:evors_admin_ajax_script.ajaxurl,
					data: data_arg,
					dataType:'json',
					success:function(data){
						//alert(data);
						if(data.status=='0'){
							$('body').find('#evors_view_attendees_list').html(data.content).slideDown();
							
						}else{
							$('.evors_lightbox ').find('.ajde_popup_text').html('Could not load attendee list');
						}
					},complete:function(){ $('#evors_view_attendees').removeClass('loading');}
				});	
			});

		// get individual attendee information
			$('body').on('click','em.evorsadmin_rsvp',function(){
				OBJ = $(this);
				
				OBJ.evo_lightbox_open({
					uid: 	'evors_get_one_attendee',
					t: 		'Attendee Information',
					lbc: 	'evors_get_one_attendee',
					ajax: 	'yes',
					d: 			{
						action: 		'evorsadmin_attendee_info',
						rsvpid:			OBJ.parent().data('rsvpid'),
						eid:			OBJ.closest('.evors_list').data('eid'),
						load_lbcontent: true
					}
				});
				
			});

	// show rest of attendees
		$('body').on('click','.evors_repeats_showrest',function(){
			$(this).closest('td').find('.evotx_ri_cap_inputs p').removeClass('hidden');
		});
	// Sync attendees count
		$('body').on('click','.evors_sync_count_trig',function(){

			var obj = $(this);
			var data_arg = {
				action: 		'the_ajax_evors_a2',
				e_id:			$(this).data('e_id'),
				postnonce: evors_admin_ajax_script.postnonce, 
			};
			//console.log(data_arg);			
			$.ajax({
				beforeSend: function(){
					$('#evors_message').html('Syncing...').show();
				},
				type: 'POST',
				url:evors_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data);
					if(data.status=='0'){
						obj.closest('.evors_details').find('.evors_stats').html(data.content);
						$('#evors_message').html('Syncing Complete!').show();
						$('#evors_message').delay(3000).fadeOut();
					}else{
						$('#evors_message').html('Could not sync attendance at this moment. Please try again later').show();
						$('#evors_message').delay(3000).fadeOut();
					}
				},complete:function(){}
			});

		});

	// Emailing for RSVP
		$('body').on('change','.evors_emailing_options',function(){
			LB = $(this).closest('.evo_lightbox');
			VAL = $(this).find(":selected").attr('value');
			if(VAL=='someone' || VAL=='someonenot'){
				LB.find('.evo_elm_row.emails').show();
			}else{
				LB.find('.evo_elm_row.emails').hide();
			}
		});
		$('body').on('click','.evors_submit_email_form', function(){
			var obj = $(this);

			LB = $('body').find('.evors_emailing.evo_lightbox');
			
			// check validate required fields
			if( LB.find('input[name="email_subject"]').val() == ''){
				LB.evo_lightbox_show_msg({'type': 'bad', 'message': 'Required Fields Missing!' });
				return;
			}
			if( LB.find('textarea[name="email_content"]').val() == ''){
				LB.evo_lightbox_show_msg({'type': 'bad', 'message': 'Required Fields Missing!' });
				return;
			}

			// proceed
			obj.evo_ajax_lightbox_form_submit( obj.data('d') );

			return;
		});

	// Default build in checkin
	
	// CHECK in attendees
		function checkin_attendee(obj){

			var status = obj.attr('data-status');

			status = (status=='' || status=='check-in')? 'checked':'check-in';

			var data_arg = {
				action: 'the_ajax_evors_f4',
				rsvp_id: obj.data('id'),
				status:  status,
				nonce: evors_admin_ajax_script.postnonce
			};
			$.ajax({
				beforeSend: function(){
					obj.parent().animate({'opacity':'0.3'});
				},
				type: 'POST',
				url:evors_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data);
					if(data.status=='0'){
						obj.attr({'data-status':status})
							.html(data.new_status_lang)
							.removeAttr('class')
							.addClass(status+' checkin');
					}
				},complete:function(){
					obj.parent().animate({'opacity':'1'});
				}
			});
		}

	// check in attendees from all RSVPs page
		$('body').on('click','.evors_trig_checkin',function(){
			_checkin_attendee_on_admin( $(this) );
		});

	// check in attendee from rsvp edit page
		
		function _checkin_attendee_on_admin(O){
			var old_status = O.attr('data-status');

			new_status = (old_status=='' || old_status=='check-in')? 'checked':'check-in';

			var data_arg = {
				action: 'the_ajax_evors_f4',
				rsvp_id: O.data('rsvp_id'),
				status:  new_status,
				nonce: O.data('nonce')
			};
			$.ajax({
				beforeSend: function(){
					O.html( O.html()+'...');   
				},
				type: 'POST',url:evors_admin_ajax_script.ajaxurl,	data: data_arg,dataType:'json',
				success:function(data){
					//alert(data);
					O.attr({'data-status':new_status})
						.html(data.new_status_lang)
						.addClass(new_status)
						.removeClass(old_status);
				}
			});
		}

	// resend confirmation  or notification email
		$('.evors_resend_email').on('click',function(){
			var obj = $(this);
			
			var data_arg = {
				action: 'the_ajax_evors_a5',
				rsvp_id: obj.data('rsvpid'),
				T: obj.data('t'),
			};
			$.ajax({
				beforeSend: function(){	obj.closest('.evoRS_resend_conf').addClass('loading');	},
				type: 'POST',
				url:evors_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data);
					if(data.status=='0'){
						obj.siblings('.message').fadeIn().delay(5000).fadeOut();
					}
				},complete:function(){
					obj.closest('.evoRS_resend_conf').removeClass('loading');
				}
			});
		});

		$('#evoRS_custom_email').on('click',function(){
			var obj = $(this);
			var _email = obj.parent().find('input').val();
			
			var data_arg = {
				action: 'the_ajax_evors_a6',
				email: _email,
				rsvp_id: obj.data('rsvpid'),
				type: obj.parent().find('select').val()
			};

			if( _email===undefined || _email=='' ){
				obj.siblings('.message').addClass('error').html( obj.data('empty')).show().delay(5000).fadeOut(function(){
					$(this).removeClass('error');
				});
			}else{
				$.ajax({
					beforeSend: function(){
						obj.closest('.evoRS_resend_conf').addClass('loading');
					},
					type: 'POST',
					url:evors_admin_ajax_script.ajaxurl,
					data: data_arg,
					dataType:'json',
					success:function(data){
						//alert(data);
						if(data.status=='0'){
							obj.siblings('.message').fadeIn().delay(5000).fadeOut();
						}

					},complete:function(){
						obj.closest('.evoRS_resend_conf').removeClass('loading');
					}
				});
			}
		});
	function is_email(email){
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  		return regex.test(email);
	}


	// ADDONS
	// waitlist - add to list
		$('body')
		.on('evo_ajax_success_evorsw_add_to_list', function(event, OO){
			LB = $('body').find('.evo_lightbox.'+ OO.lightbox_key);
			LB.evo_lightbox_close();
		})
		// from page
		.on('evo_ajax_beforesend_evorsw_add_to_list_pg', function(event, OO, data){
			$('body').find('.evorsw_add_to_list_pg').closest('td').addClass('evoloading');
		})
		.on('evo_ajax_complete_evorsw_add_to_list_pg', function(event, OO, data){
			$('body').find('.evorsw_add_to_list_pg').closest('td').removeClass('evoloading');
		})
		.on('evo_ajax_success_evorsw_add_to_list_pg', function(event, OO, data){
			$('body').find('.evorsw_add_to_list_pg').siblings('span').html( data.new_checkin_status )
				.attr('class','rsvp_ch_st evors_trig_checkin check-in')
				.data('status','check-in');
			$('body').find('.evorsw_add_to_list_pg').remove();
		})
		// moved to waitlist
		.on('evo_ajax_success_evorsw_move_to_waitlist', function(event, OO){
			LB = $('body').find('.evo_lightbox.'+ OO.lightbox_key);
			LB.evo_lightbox_close();
		})
		;

});