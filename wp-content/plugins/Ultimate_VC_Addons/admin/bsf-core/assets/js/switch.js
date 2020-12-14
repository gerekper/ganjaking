jQuery(document).ready(function(){
	var switch_btn = jQuery(".bsf-switch-btn");
	jQuery(document).on('click', '.bsf-switch-btn', function(e){
		var id = jQuery(this).data('id');
		var value = jQuery(this).parents(".switch-wrapper").find("#"+id).val();

		if( value == 1 || value == '1' ) {
			jQuery(this).parents(".switch-wrapper").find("#"+id).attr('value','0');
		} else {
			jQuery(this).parents(".switch-wrapper").find("#"+id).attr('value','1');
		}
		
		jQuery(this).parents(".switch-wrapper").find(".bsf-switch-input").trigger('change');
	
	});
});