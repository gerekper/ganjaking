(function($) {
	$(document).ready(function($) {
		var forms = $(".post-list-ajax-search-form");

		forms.each(function(index, form) {
			var $ID = $(form)
					.children("form")
					.attr("id"),
				$form = $("#" + $ID),
				$input = $form.find('input[type="text"]'),
				$postType = $form.find('input[name="post_type"]').val(),
				$wrapper = $form.siblings(".result-posts-wrapper").hide();

			$input.keypress(function(e) {
				if (e.which == 13) {
					return false;
				} else {
					return true;
				}
			});

			$input.on("keyup", function(e) {
				e.preventDefault();

				var $key = $(this).val(),
					$nonce = localize.nonce;

				$.ajax({
					url: localize.ajaxurl,
					type: "post",
					data: {
						action: "eael_ajax_post_search",
						post_type: $postType,
						_nonce: $nonce,
						key: $key
					},
					success: function(r) {
						if ($key != "") {
							if ("" != r) {
								setTimeout(function() {
									$wrapper.html(r);
									$wrapper.fadeIn();
								}, 50);
							}
						} else {
							$wrapper.hide();
						}
					},
					error: function(r) {
						console.log("err", r);
					}
				});
			});
		});
	});
})(jQuery);
