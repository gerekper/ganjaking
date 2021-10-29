/**
 * JS for Review admin section 
 * @version  0.1
 */
jQuery(document).ready(function($){
	
	// GET reviews list
		$('#evore_VR').on('click',function(){
			RIACTIVE = $(this).data('riactive');
			//console.log(RIACTIVE);
			if(RIACTIVE =='no'){
				var data_arg = {
					action: 		'the_ajax_evore2',
					e_id:			$(this).data('e_id'),
					postnonce: evore_admin_ajax_script.postnonce, 
				};
				//console.log(data_arg);			
				$.ajax({
					beforeSend: function(){},
					type: 'POST',
					url:evore_admin_ajax_script.ajaxurl,
					data: data_arg,
					dataType:'json',
					success:function(data){
						//alert(data);
						if(data.status=='0'){
							$('.evore_lightbox ').find('.ajde_popup_text').html(data.content);
							$('.evore_lightbox').find('.evore_list span.checkin').click(function(){
								var obj = $(this);
								checkin_attendee(obj);
							});
						}else{
							$('.evore_lightbox ').find('.ajde_popup_text').html('Could not load reviews list');
						}
					},complete:function(){}
				});
			}else{
				$('body').find('#evore_view_reviews_list').html('').hide();
			}
		});
		$('body').on('click','#evore_VR_submit',function(){
			var data_arg = {
				action: 		'the_ajax_evore2',
				e_id:			$(this).data('e_id'),
				ri: $('#evore_event_repeatInstance').val(),
				postnonce: evore_admin_ajax_script.postnonce, 
			};
			//console.log(data_arg);			
			$.ajax({
				beforeSend: function(){ $('#evore_view_reviews').addClass('loading'); },
				type: 'POST',
				url:evore_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data);
					if(data.status=='0'){
						$('body').find('#evore_view_reviews_list').html(data.content).slideDown();	
					}else{
						$('.evore_lightbox ').find('.ajde_popup_text').html('Could not load reviews list');
					}
				},complete:function(){ $('#evore_view_reviews').removeClass('loading');}
			});	
		});

	// Sync reviews & ratings
		$('#evore_SY').on('click',function(){

			var obj = $(this);
			var data_arg = {
				action: 		'the_ajax_evore3',
				e_id:			$(this).data('e_id'),
				postnonce: evore_admin_ajax_script.postnonce, 
			};
			//console.log(data_arg);			
			$.ajax({
				beforeSend: function(){
					$('#evore_message').html( $('#evore_message').data('t1') ).show();
				},
				type: 'POST',
				url:evore_admin_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data);
					if(data.status=='0'){
						obj.closest('.evore_details').find('.evore_star_rating').html(data.content);
						$('#evore_message').fadeOut();
					}else{
						$('#evore_message').html( $('#evore_message').data('t2') ).show();
						$('#evore_message').delay(3000).fadeOut();
					}
				},complete:function(){}
			});
		});

	
	$('.evore_star_rating_new').on('click','span',function(){
		rating = $(this).data('value');
		$(this).parent().find('span').removeClass('fa-star fa-star-half-full fa-star-o');

		$(this).addClass('fa-star');
		$(this).prevAll().addClass('fa-star');
		$(this).nextAll().addClass('fa-star-o');

		$(this).siblings('input').attr('value',rating);
	});
	
	function is_email(email){
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  		return regex.test(email);
	}
});