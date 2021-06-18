jQuery(document).ready(function(){
	jQuery( "#save_widgets_admin" ).click(function(){
			updb_save_widgets_admin();
	});
	
	function updb_overlay_confirmation(message){

		if (jQuery('.userpro-modal-inner').length){
			jQuery('.userpro-modal-inner').remove();
		}
		jQuery('body').append('<div class="userpro-modal-inner"><i class="userpro-icon-ok"></i><i class="userpro-icon-remove"></i>' + message + '</div>');
		jQuery('.userpro-modal-inner').css({
			'margin-top' : '-' + jQuery('.userpro-modal-inner').innerHeight() / 2 + 'px',
			'opacity' : 1
		});
		jQuery('.userpro-modal-inner').delay(1500).fadeOut(300); 
	}
	
	function updb_save_widgets_admin(){
		var col1 = jQuery('#updb-customizer_1').sortable('toArray').toString();
		var col2 = jQuery('#updb-customizer_2').sortable('toArray').toString();
		var col3 = jQuery('#updb-customizer_3').sortable('toArray').toString();
		var unused_widget = jQuery('#updb_unused_widget').sortable('toArray').toString();
		jQuery('.userpro-loading').show().addClass('inline');
		jQuery.ajax({
			url: ajaxurl,
			data:{action:'updb_save_widgets_admin', col1:col1, col2:col2, col3:col3, unused_widget:unused_widget},
			type:'POST',
			dataType:'JSON',
			success: function(res){
				updb_overlay_confirmation( 'Profile View Updated Successfully' );
			}
		});
	}
});