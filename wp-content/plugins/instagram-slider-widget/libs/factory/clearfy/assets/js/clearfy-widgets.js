jQuery(function($) {
	$('#wbcr-factory-subscribe-widget__subscribe-form').submit(function(e) {
		e.preventDefault();
		var agree = $(this).find('[name=agree_terms]:checked'),
			pluginName = $('#wbcr-factory-subscribe-widget__plugin-name').val(),
			email = $('#wbcr-factory-subscribe-widget__email').val(),
			groupId = $('#wbcr-factory-subscribe-widget__group-id').val();

		if( agree.length === 0 ) {
			return;
		}

		$.ajax({
			method: "POST",
			url: ajaxurl,
			dataType: 'json',
			data: {
				action: 'wbcr-clearfy-subscribe-for-' + pluginName,
				email: email,
				group_id: groupId,
				plugin_name: pluginName,
				_wpnonce: $(this).data('nonce')
			},
			success: function(response) {
				if( !response || !response.success ) {
					if( response.data ) {
						console.log(response.data.error_message);
						noticeId = $.wbcr_factory_clearfy_236.app.showNotice('Error: [' + response.data.error_message + ']', 'danger');
						setTimeout(function() {
							$.wbcr_factory_clearfy_236.app.hideNotice(noticeId);
						}, 5000);
					} else {
						console.log(response);
					}

					return;
				}

				if( response.data ) {
					$(".wbcr-factory-subscribe-widget__text").hide();

					if( response.data.subscribed ) {
						$(".wbcr-factory-subscribe-widget__text--success").show();
					} else {
						$(".wbcr-factory-subscribe-widget__text--success2").show();
					}
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {

				console.log(xhr.status);
				console.log(xhr.responseText);
				console.log(thrownError);

				var noticeId = $.wbcr_factory_clearfy_236.app.showNotice('Error: [' + thrownError + '] Status: [' + xhr.status + '] Error massage: [' + xhr.responseText + ']', 'danger');
				setTimeout(function() {
					$.wbcr_factory_clearfy_236.app.hideNotice(noticeId);
				}, 5000);
			}
		});
	});
});