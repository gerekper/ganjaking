jQuery(document).ready( function($) {
	$('.wp-tab-bar a').click(function(event){
		event.preventDefault();
		
		// Limit effect to the container element.
		var context = $(this).closest('.wp-tab-bar').parent();
		$('.wp-tab-bar li', context).removeClass('wp-tab-active');
		$(this).closest('li').addClass('wp-tab-active');
		$('.egb_contents .wp-tab-panel', context).hide();
		$( $(this).attr('href'), context ).show();
	});

	// Make setting wp-tab-active optional.
	$('.wp-tab-bar').each(function(){
		if ( $('.wp-tab-active', this).length )
			$('.wp-tab-active', this).click();
		else
			$('a', this).first().click();
	});
});