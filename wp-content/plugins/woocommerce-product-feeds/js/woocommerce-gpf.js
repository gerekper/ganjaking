jQuery(function() {
	jQuery(document).on('click', '.wc_gpf_metabox h2', function(e) {
		var metabox = jQuery(this).parent('.wc_gpf_metabox');
		if (metabox.hasClass('closed')) {
			metabox.addClass('open').removeClass('closed');
			metabox.find('.wc_gpf_metabox_content').show()
		} else {
			metabox.addClass('closed').removeClass('open');
			metabox.find('.wc_gpf_metabox_content').hide();
		}
	});
});
