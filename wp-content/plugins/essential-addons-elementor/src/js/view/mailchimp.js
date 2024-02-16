jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction("frontend/element_ready/eael-mailchimp.default", function($scope, $) {
		var $mailchimp = $(".eael-mailchimp-wrap", $scope),
			$mailchimp_id = $mailchimp.data("mailchimp-id") !== undefined ? $mailchimp.data("mailchimp-id") : "",
			$list_id = $mailchimp.data("list-id") !== undefined ? $mailchimp.data("list-id") : "",
			$button_text = $mailchimp.data("button-text") !== undefined ? $mailchimp.data("button-text") : "",
			$success_text = $mailchimp.data("success-text") !== undefined ? $mailchimp.data("success-text") : "",
			$pending_text = $mailchimp.data("pending-text") !== undefined ? $mailchimp.data("pending-text") : "",
			$loading_text = $mailchimp.data("loading-text") !== undefined ? $mailchimp.data("loading-text") : "";

		$("#eael-mailchimp-form-" + $mailchimp_id, $scope).on("submit", function(e) {
			e.preventDefault();
			var _this = $(this);

			$(".eael-mailchimp-message", _this)
				.css("display", "none")
				.html("");

			$(".eael-mailchimp-subscribe", _this).addClass("button--loading");
			$(".eael-mailchimp-subscribe span", _this).html($loading_text);
			$.ajax({
				url: localize.ajaxurl,
				type: "POST",
				data: {
					action: "mailchimp_subscribe",
					fields: _this.serialize(),
					listId: $list_id,
					nonce: localize.nonce
				},
				success: function(data) {
					if (data.status == "subscribed") {
						$("input[type=text], input[type=email], textarea", _this).val("");
						$(".eael-mailchimp-message", _this)
							.css("display", "block")
							.html("<p>" + $success_text + "</p>");
					} else if (data.status == "pending") {
						$("input[type=text], input[type=email], textarea", _this).val("");
						$(".eael-mailchimp-message", _this)
							.css("display", "block")
							.html("<p>" + $pending_text + "</p>");
					} else {
						$(".eael-mailchimp-message", _this)
							.css("display", "block")
							.html("<p>" + data.status + "</p>");
					}

					$(".eael-mailchimp-subscribe", _this).removeClass("button--loading");
					$(".eael-mailchimp-subscribe span", _this).html($button_text);
				}
			});
		});
	});
});
