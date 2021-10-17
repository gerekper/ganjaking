/**
 * Javascript: Event Tickets Calendar - ticket settings page
 * @version 0.1
 */
jQuery(document).ready(function($){
	
	// search for ticket information
		$('body').on('click','#evotx_find_tix', function(){

			var obj = $(this),
				value = obj.siblings('input').val(),
				section = obj.closest('.evotx_searchtix_section');

			if(!value || value == ''){ 
				section.find('.evotx_searchtix_msg').html('Ticket Number Required!').fadeIn().delay(5000).fadeOut();
				return false;
			}

			var data_arg = {
				action: 		'evoTX_ajax_07',
				tickernumber:	value,
			};
			//console.log(data_arg);				
			$.ajax({
				beforeSend: function(){
					obj.html('...');
				},
				type: 'POST',
				url:evotx_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//console.log(data.status);
					if(data.status=='good'){
						section.find('.evotx_searchtix').html(data.content);
						section.find('span.tix_status').click(function(){
							checkin_attendee( $(this));	
						});

						section.find('.evotx_searchtix_msg').html('Found Ticket Information!').fadeIn().delay(5000).fadeOut();
					}else{
						section.find('.evotx_searchtix_msg').html('Ticket Information could not be found!').fadeIn().delay(5000).fadeOut();
					}

				},complete:function(){
					obj.html('Find Ticket');
				}
			});
		});
	
	// CHECK in attendees
		function checkin_attendee(obj){

			var status = obj.attr('data-status');
			var data_arg = {
				action: 'the_ajax_evotx_a5',
				tid: obj.attr('data-tid'),
				tiid: obj.attr('data-tiid'),
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
					obj.attr({'data-status':data.new_status}).html(data.new_status_lang).removeAttr('class').addClass('tix_status '+ data.new_status);
				}
			});
		}

});