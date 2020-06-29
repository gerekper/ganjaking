jQuery(document).ready(function ($) {
	"use strict";

	var $body = $('body');

	$(document.body).on('updated_cart_totals, added_to_cart', function () {

		// cart messages
		var $message_containers = $('.yith-cart-message');
		if ($message_containers.length > 0) {
			$.each($message_containers, function () {
				var $this = $(this);
				$.ajax({
					url     : yith_cm_general.wc_ajax_url.toString().replace('%%endpoint%%', 'ywcm_update_cart_messages'),
					type    : 'POST',
					dataType: 'json',
					data    : 'ywcm_id=' + $this.data('id'),
					success : function (res) {
						if (res.result == true) {
							$this.replaceWith(res.message);
						}
					}
				});
			});
		}
	});

});