jQuery(document).ready(function($) {
	$('a[href*="post-new.php?post_type=porto_builder"]').on('click', function(e) {
		$.magnificPopup.open({
			items: {
				src: '#porto-builders-input'
			},
			type: 'inline',
			mainClass: 'mfp-with-zoom',
			zoom: {
				enabled: true,
				duration: 300
			},
			callbacks: {
				open: function() {
					setTimeout(function() {
						$('#porto-builders-input input[name="builder_name"]').focus();
					}, 100);
				}
			}
		});
		e.preventDefault();
	});
});