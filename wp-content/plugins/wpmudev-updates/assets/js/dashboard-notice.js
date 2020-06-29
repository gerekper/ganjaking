jQuery(function() {
	var el_notice, msg_id = el_notice, btn_dismiss, btn_dismiss_wp;

	// Display the notice after the page was loaded.
	function initialize() {

		el_notice = jQuery(".wdp-notice");
		msg_id = el_notice.find("input[name=msg_id]").val();
		btn_dismiss = el_notice.find(".wdp-notice-dismiss");
		btn_dismiss_wp = el_notice.find(".notice-dismiss");

		// Dismiss the notice without any action.
		btn_dismiss.click(function(ev) {
			ev.preventDefault();
			dismiss_dash_notice("wdev_notice_dismiss");
		});

		// Dismiss the notice without any action.
		btn_dismiss_wp.click(function(ev) {
			ev.preventDefault();
			dismiss_dash_notice("wdev_notice_dismiss");
		});

		// Display the notification.
		el_notice.fadeIn(500);
	}

	// Hide the notice after a CTA button was clicked
	function remove_dash_notice() {
		el_notice.fadeTo(100 , 0, function() {
			el_notice.slideUp(100, function() {
				el_notice.remove();
			});
		});
	}

	// Notify WordPress about the users choice and close the message.
	function dismiss_dash_notice(action) {
		var ajax_data = {};

		if ('0' !== msg_id) {
			el_notice.addClass("loading");

			ajax_data.msg_id = msg_id;
			ajax_data.action = action;
			jQuery.post(
				window.ajaxurl,
				ajax_data,
				remove_dash_notice
			);
		} else {
			remove_dash_notice();
		}
	}

	// Premium version uses a HIGHER delay than the notice in free plugins.
	// So if any free plugin display a notice it will be displayed instead of
	// the premium notice.
	//
	// 1050 ... free notice uses 500 delay + 500 fade in + 20 to let browser render the changes.
	//          So after 1020ms the free notice is considered "visible".
	window.setTimeout(initialize, 1050);
});
