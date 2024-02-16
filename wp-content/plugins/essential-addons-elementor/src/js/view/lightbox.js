var LightBox = function ($scope, $) {
	var $lightBox = $scope.find(".eael-lightbox-wrapper").eq(0),
		$main_class = $lightBox.data("main-class") !== undefined ? $lightBox.data("main-class") : "",
		$popup_layout = $lightBox.data("popup-layout") !== undefined ? $lightBox.data("popup-layout") : "",
		$close_button = $lightBox.data("close_button") === "yes" ? true : false,
		$effect = $lightBox.data("effect") !== undefined ? $lightBox.data("effect") : "",
		$type = $lightBox.data("type") !== undefined ? $lightBox.data("type") : "",
		$iframe_class = $lightBox.data("iframe-class") !== undefined ? $lightBox.data("iframe-class") : "",
		$src = $lightBox.data("src") !== undefined ? $lightBox.data("src") : "",
		$trigger_element = $lightBox.data("trigger-element") !== undefined ? $lightBox.data("trigger-element") : "",
		$delay = $lightBox.data("delay") != "" ? $lightBox.data("delay") : 0,
		$trigger = $lightBox.data("trigger") !== undefined ? $lightBox.data("trigger") : "",
		$popup_id = $lightBox.data("lightbox-id") !== undefined ? $lightBox.data("lightbox-id") : "",
		$display_after = $lightBox.data("display-after") !== undefined ? $lightBox.data("display-after") : "",
		$esc_exit = $lightBox.data("esc_exit") === "yes" ? true : false,
		$click_exit = $lightBox.data("click_exit") === "yes" ? true : false;
	$main_class += " " + $popup_layout + " " + $effect;

	if ("eael-lightbox-popup-fullscreen" == $popup_layout) {
		var win_height = $(window).height() - 20;
		$(".eael-lightbox-container.content-type-image-now").css({
			"max-height": win_height + "px",
			"margin-top": "10px",
		});
	}

	if ("eael_lightbox_trigger_exit_intent" == $trigger) {
		var flag = true,
			mouseY = 0,
			topValue = 0;

		if ($display_after === 0) {
			$.removeCookie($popup_id, { path: "/" });
		}
		window.addEventListener(
			"mouseout",
			function (e) {
				mouseY = e.clientY;
				if (mouseY < topValue && !$.cookie($popup_id)) {
					$.magnificPopup.open({
						items: {
							src: $src, //ID of inline element
						},
						iframe: {
							markup:
								'<div class="' +
								$iframe_class +
								'">' +
								'<div class="modal-popup-window-inner">' +
								'<div class="mfp-iframe-scaler">' +
								'<div class="mfp-close"></div>' +
								'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
								"</div>" +
								"</div>" +
								"</div>",
						},
						type: $type,
						showCloseBtn: $close_button,
						enableEscapeKey: $esc_exit,
						closeOnBgClick: $click_exit,
						removalDelay: 500, //Delaying the removal in order to fit in the animation of the popup
						mainClass: $main_class,
					});

					ea.hooks.doAction("ea-lightbox-triggered", $src);
					$(document).trigger('eael-lightbox-open', );

					if ($display_after > 0) {
						$.cookie($popup_id, $display_after, {
							expires: $display_after,
							path: "/",
						});
					} else {
						$.removeCookie($popup_id);
					}
				}
			},
			false
		);
	} else if ("eael_lightbox_trigger_pageload" == $trigger) {
		if ($display_after === 0) {
			$.removeCookie($popup_id, { path: "/" });
		}
		if (!$.cookie($popup_id)) {
			setTimeout(function () {
				$.magnificPopup.open({
					items: {
						src: $src,
					},
					iframe: {
						markup:
							'<div class="' +
							$iframe_class +
							'">' +
							'<div class="modal-popup-window-inner">' +
							'<div class="mfp-iframe-scaler">' +
							'<div class="mfp-close"></div>' +
							'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
							"</div>" +
							"</div>" +
							"</div>",
					},
					type: $type,
					showCloseBtn: $close_button,
					enableEscapeKey: $esc_exit,
					closeOnBgClick: $click_exit,
					mainClass: $main_class,
				});

				ea.hooks.doAction("ea-lightbox-triggered", $src);
				$(document).trigger('eael-lightbox-open', );

				if ($display_after > 0) {
					$.cookie($popup_id, $display_after, {
						expires: $display_after,
						path: "/",
					});
				} else {
					$.removeCookie($popup_id);
				}
			}, $delay);
		}
	} else {
		if (typeof $trigger_element === "undefined" || $trigger_element === "") {
			$trigger_element = ".eael-modal-popup-link";
		}

		$scope.on('keydown', $trigger_element, function (e) {
			if (e.which === 13 || e.which === 32) {
				$(this).trigger('click');
			}
		});

		$($trigger_element).magnificPopup({
			image: {
				markup:
					'<div class="' +
					$iframe_class +
					'">' +
					'<div class="modal-popup-window-inner">' +
					'<div class="mfp-figure">' +
					'<div class="mfp-close"></div>' +
					'<div class="mfp-img"></div>' +
					'<div class="mfp-bottom-bar">' +
					'<div class="mfp-title"></div>' +
					'<div class="mfp-counter"></div>' +
					"</div>" +
					"</div>" +
					"</div>" +
					"</div>",
			},
			iframe: {
				markup:
					'<div class="' +
					$iframe_class +
					'">' +
					'<div class="modal-popup-window-inner">' +
					'<div class="mfp-iframe-scaler">' +
					'<div class="mfp-close"></div>' +
					'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
					"</div>" +
					"</div>" +
					"</div>",
			},
			items: {
				src: $src,
				type: $type,
			},
			removalDelay: 500,
			showCloseBtn: $close_button,
			enableEscapeKey: $esc_exit,
			closeOnBgClick: $click_exit,
			mainClass: $main_class,
			callbacks: {
				open: () => {
					ea.hooks.doAction("ea-lightbox-triggered", $src);
					$(document).trigger('eael-lightbox-open', );
				},
			},
			type:'inline',
		});
	}

	$.extend(true, $.magnificPopup.defaults, {
		tClose: "Close",
	});
};

jQuery(window).on("elementor/frontend/init", function () {

	if (ea.elementStatusCheck('eaelLightboxLoad')) {
		return false;
	}

	elementorFrontend.hooks.addAction("frontend/element_ready/eael-lightbox.default", LightBox);
});
