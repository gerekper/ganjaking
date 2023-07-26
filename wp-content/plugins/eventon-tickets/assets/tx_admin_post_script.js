/** 
 * Admin view post functions
 * @version  2.0
 */
jQuery(document).ready(function($){

	
	// on save event post update page meta values
		$('body').on('evo_ajax_form_complete_evotix_save_eventedit_settings',function(event, OO, form){
			var fordata = form.serializeArray();
			$.each(fordata, function(index, value){
				$('body').find('input[value="' + value.name + '"]').closest('tr').find('textarea').val( value.value );
			});
		});

	// trigger 
		$('body')
		.on('evo_ajax_success_evotx_view_attendees',function(event, OO, data){
			if(data.status=='0'){
				$('body').evotxDrawAttendees( data );
			}else{
				LB = $('body').find('.evo_lightbox.'+ OO.lightbox_key);
				LB.evo_lightbox_populate_content({content: 'Could not load attendee list' });
			}
		})
		.on('evo_ajax_success_evotx_emailing',function(event, OO, data){
			// EMAILING editor
			$('body').find('textarea#evotx_emailing_message').trumbowyg({
				btns: [
			        ['viewHTML'],
			        ['undo', 'redo'], // Only supported in Blink browsers
			        //['formatting'],
			        ['strong', 'em', 'del'],
			        //['superscript', 'subscript'],
			        ['link'],
			        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
			        ['unorderedList', 'orderedList'],
			        ['removeformat'],
			        ['fullscreen']
			    ],
			    autogrow: true
			});
		})
		;



	// CHECK in attendees - global
	// @updated 1.7
		$('body').on('click','.evotx_status', function(){
			var obj = $(this);
			if(obj.hasClass('refunded')) return false;
			if( obj.data('gc')){
			
			var status = obj.data('status');
			var data_arg = {
				action: 'the_ajax_evotx_a5',
				tid: obj.data('tid'),
				tiid: obj.data('tiid'),
				status:  status
			};
			$.ajax({
				beforeSend: function(){
					obj.html( obj.html()+'...' );
				},
				type: 'POST',
				url:evotx_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					obj.data('status', data.new_status);
					obj.html(data.new_status_lang).removeAttr('class').addClass('evotx_status '+ data.new_status);
				}
			});
		}
		});


	// Send attendee list via email
		$('body').on('change','#evotx_emailing_options',function(){
			VAL = $(this).find(":selected").attr('value');
			if(VAL!='someone'){
				$('#evotx_emailing').find('p.text').hide();
			}else{
				$('#evotx_emailing').find('p.text').show();
			}
		});

	
	// Resend Ticket email
		$('.evoTX_resend_email').on('click',function(){
			var obj = $(this);
			MSG = obj.closest('.evoTX_rc_in').find('p.message');

			var data_arg = {
				action: 'the_ajax_evotx_a55',
				orderid: obj.data('orderid'),
			};

			// send the custom email send value
			if(obj.hasClass('customemail') && obj.siblings('input').val()!='' ){
				data_arg['email'] = obj.siblings('input').val();
			}
						
			$.ajax({
				beforeSend: function(){
					obj.closest('.evoTX_resend_conf').addClass('loading');
				},
				type: 'POST',
				url:evotx_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					
					STR = (data.status=='good')? 's':'f';
					if(STR=='f')	MSG.addClass('error');

					MSG.html( MSG.data(STR));	
					MSG.fadeIn().delay(8000).fadeOut();

				},complete:function(){
					obj.closest('.evoTX_resend_conf').removeClass('loading');
				}
			});
		});

	// view rest repeat occurance 
		$('body').on('click', '.evotx_ri_view_more a', function(){
			$(this).parent().siblings('.evotx_ri_cap_inputs').find('p').fadeIn();
			$(this).parent().fadeOut();
		});
	
	// Toggle completed vs non completed orders
		$('body').on('click','span.separatation',function(){
			$(this).parent().find('span.hidden').toggleClass('bad');
		});

	

	// Sync evo tix post data
		$('body').on('click','#evotix_sync_with_order', function(){
			var obj = $(this);			
			
			var data_arg = {
				action: 'evotx_sync_with_order',
				oid: obj.data('oid'),
			};
			$.ajax({
				beforeSend: function(){
					obj.siblings('span').html( '...' );
				},
				type: 'POST',
				url:evotx_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					obj.siblings('span').html( data.message );
				}
			});
		
		});
// SUPPORTIVE
});
