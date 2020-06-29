jQuery(document).ready( function($) {
	// Отправдяем запрос на маркировку нотиса, если пользователь его закрыл
	$( '.wbcr-advt-notice' ).click( function() {
		$.post(
			ajaxurl,
			{
				action: 'wbcr_advt_mark_notice'
			},
			function(data) {
			}
		);
	});
});