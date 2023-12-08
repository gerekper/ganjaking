/*section column link*/
jQuery(document).ready(function( $ ) {
	jQuery(document).on('click','[data-tp-sc-link]',function() {
		/*Get Attributes*/
		var tp_sc_link = $(this).data('tp-sc-link'),
			tp_sc_link_external = $(this).data('tp-sc-link-external'),
			addlink = '<a href="' + tp_sc_link + '" data-tp-sc-link-add style="display:none"></a>';
		
		/*Open in new window*/
		if( tp_sc_link_external == 'on' ) {
			addlink = '<a href="' + tp_sc_link + '" target="_blank" data-tp-sc-link-add style="display:none"></a>';
		}
		
		/*length*/
		if ( jQuery(this).find('[data-tp-sc-link-add]').length == 0) {
			jQuery(this).append(addlink);
		}
		
		var firelink = jQuery(this).find('[data-tp-sc-link-add]');
		    firelink[0].click();	
	});
});