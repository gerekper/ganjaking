jQuery(document).ready(function($) {

	$('body').prepend('<style>.porto-elementor-notice { content: ""; position: fixed; left: 0; width: 100%; height: 100%; top: 0; background: rgba(0, 0, 0, .4); z-index: 9999; display: flex; align-items: center; justify-content: center; }</style>' +
		'<div class="porto-elementor-notice"><div class="alert alert-info alert-dismissible"><p>Do you want to disable Elementors\' default styles and use the theme defaults?</p>' +
		'<div class="porto-elementor-notice-actions">' +
			'<a href="#" class="btn-close"></a>' +
			'<a href="#" class="btn btn-info mt-xs mb-xs" data-option="yes">Yes</a>&nbsp;' +
			'<a href="#" class="btn btn-default mt-xs mb-xs" data-option="no">No</a>' +
		'</div>' +
	'</div></div>');

	$('.porto-elementor-notice-actions .btn-close').on('click', function() {
		$(this).closest('.porto-elementor-notice').fadeOut();
	});
	$('.porto-elementor-notice-actions .btn').on('click', function() {
		var option = $(this).data('option');
		$.ajax({
			url: theme.ajax_url,
			data: {
				option: option,
				nonce: portoElementorNotice.nonce,
				action: 'porto_elementor_disable_default_styles'
			},
			type: 'post',
			success: function() {
				if (option === 'yes') {
					parent.location.reload();
				} else {
					$('.porto-elementor-notice').fadeOut(400, function() {
						$(this).remove();
					});
				}
			}
		});
	});
});
