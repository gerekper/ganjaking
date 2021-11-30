jQuery(document).ready(function($){
	var elements = jQuery('.modal-overlay, .modal');

	jQuery( document ).on( 'click', '.close-modal', function(){
	    elements.removeClass('active');
	});
	jQuery( document ).on( 'click', '.mwb_crp_cancel', function(){
	    elements.removeClass('active');
	});

	
	var mwb_crp_deactivate_link = '';
	jQuery( document ).on( 'change', '.mwb_crp_reason_button', function(){
		jQuery(this).siblings('textarea').addClass('mwb_crp_message_show');
		jQuery(this).siblings('textarea').removeClass('mwb_crp_message');
		
		var hide_prev1 = jQuery(this).closest('div').prevAll();
		jQuery(hide_prev1).each(function( index ) {
		  	if (jQuery(this).hasClass('mwb_crp_reason_wrap')) {
				jQuery(this).find('#mwb_crp_message').removeClass('mwb_crp_message_show');
				jQuery(this).find('#mwb_crp_message').addClass('mwb_crp_message');
			}
		});
		
		var hide_next1 = jQuery(this).closest('div').nextAll('div');
		
		jQuery(hide_next1).each(function( index ) {
			if (jQuery(this).hasClass('mwb_crp_reason_wrap')) {
				jQuery(this).find('#mwb_crp_message').removeClass('mwb_crp_message_show');
				jQuery(this).find('#mwb_crp_message').addClass('mwb_crp_message');
			}
		  	
		});

	});

	var mwb_crp_deactivate_slug = jQuery('.mwb_crp_deactivate_slug').prev();
		mwb_crp_deactivate_slug.on("click", function (e) {
        e.preventDefault();	
        elements.addClass('active');
        mwb_crp_deactivate_link = jQuery(this).attr('href');
        elements.find('a.mwb_crp_skip').attr('href', mwb_crp_deactivate_link);  
     });

	jQuery( document ).on( 'click', '.mwb_crp_deactivate', function(){
		var selected_option = jQuery("input[name='mwb_crp_reason']:checked").val();
		var text= jQuery("input[name='mwb_crp_reason']:checked").next().next().val();
		var data={
			action:'mwb_crp_admin_deactvate_callback',
			option:selected_option,
			text:text
		};
		$.ajax({
			url:mwb_ajax.ajaxurl,
			type:'POST',
			data:data,
			dataType:'json',
			success:function(response){
				window.location.href = mwb_crp_deactivate_link;
			}
		});
	});
});