/*
	Javascript: Eventon Active User - Admin settings page
*/
jQuery(document).ready(function($){

	// role selector
	$('#evoau_role_selector').change(function(){
		
		var data_arg = {
			action:'the_ajax_au',
			role:$(this).val()
		};
		
		var this_section = $('.capabilities_list ');
		$.ajax({
			beforeSend: function(){
				this_section.fadeOut('fast');
				$('.evoau_msg').show();
			},
			type: 'POST',
			url:the_ajax_script.ajaxurl,
			data: data_arg,
			dataType:'json',
			success:function(data){
				//alert(data);
				this_section.html(data.content);				
			},complete:function(){
				this_section.fadeIn('slow');
				$('.evoau_msg').hide();
			}
		});
		
	});
});