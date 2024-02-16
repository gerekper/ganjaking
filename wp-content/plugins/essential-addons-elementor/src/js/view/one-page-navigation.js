(function ($) {
	"use strict";

	var OnePageNavHandler = function ($scope, $) {
		var onepage_nav_elem = $scope.find(".eael-one-page-nav").eq(0);

		var $section_id = "#" + onepage_nav_elem.data("section-id"),
			$section_ids = onepage_nav_elem.data("section-ids"),
			$top_offset = onepage_nav_elem.data("top-offset"),
			$scroll_speed = onepage_nav_elem.data("scroll-speed"),
			$scroll_wheel = onepage_nav_elem.data("scroll-wheel"),
			$scroll_touch = onepage_nav_elem.data("scroll-touch"),
			$scroll_keys = onepage_nav_elem.data("scroll-keys"),
			$target_dot = $section_id + " .eael-one-page-nav-item a",
			$nav_item = $section_id + " .eael-one-page-nav-item",
			$active_item = $section_id + " .eael-one-page-nav-item.active";

		$($target_dot).on("click", function (e) {
			e.preventDefault();
			e.stopPropagation();
			if (0 === $("#" + $(this).data("row-id")).length) {
				return;
			}
			if ($("html, body").is(":animated")) {
				return;
			}

			$("html, body").animate(
				{
					scrollTop: $("#" + $(this).data("row-id")).offset().top - $top_offset,
				},
				$scroll_speed
			);
			$($section_id + " .eael-one-page-nav-item").removeClass("active");
			$(this).parent().addClass("active");
			return false;
		});
		updateDot();
		$(window).on("scroll", function () {
			updateDot();
		});
		function updateDot() {
			$.each($section_ids, function (index, item) {
				var $this = $('#' + item);
				if (
					$this.offset().top - $(window).height() / 2 < $(window).scrollTop() &&
					($this.offset().top >= $(window).scrollTop() || $this.offset().top + $this.height() - $(window).height() / 2 > $(window).scrollTop())
				) {
					$($section_id + ' .eael-one-page-nav-item a[data-row-id="' + $this.attr("id") + '"]')
						.parent()
						.addClass("active");
				} else {
					$($section_id + ' .eael-one-page-nav-item a[data-row-id="' + $this.attr("id") + '"]')
						.parent()
						.removeClass("active");
				}
			});
		}
		if ($scroll_wheel == "on") {
			var lastAnimation = 0,
				quietPeriod = 500,
				animationTime = 800,
				startX,
				startY,
				timestamp;
			$(document).on("mousewheel DOMMouseScroll", function (e) {
				var timeNow = new Date().getTime();
				if (timeNow - lastAnimation < quietPeriod + animationTime) {
					e.preventDefault();
					return;
				}
				//wDelta = e.wheelDelta < 0 ? 'down' : 'up';
				var delta = e.originalEvent.detail < 0 || e.originalEvent.wheelDelta > 0 ? 1 : -1;
				if (!$("html,body").is(":animated")) {
					if (delta < 0) {
						if ($($active_item).next().length > 0) {
							$($active_item).next().find("a").trigger("click");
						}
					} else {
						if ($($active_item).prev().length > 0) {
							$($active_item).prev().find("a").trigger("click");
						}
					}
				}
				lastAnimation = timeNow;
			});
			if ($scroll_touch == "on") {
				$(document)
					.on("pointerdown touchstart", function (e) {
						var touches = e.originalEvent.touches;
						if (touches && touches.length) {
							startY = touches[0].screenY;
							timestamp = e.originalEvent.timeStamp;
						}
					})
					.on("touchmove", function (e) {
						if ($("html,body").is(":animated")) {
							e.preventDefault();
						}
					})
					.on("pointerup touchend", function (e) {
						var touches = e.originalEvent;
						if (touches.pointerType === "touch" || e.type === "touchend") {
							var Y = touches.screenY || touches.changedTouches[0].screenY;
							var deltaY = startY - Y;
							var time = touches.timeStamp - timestamp;
							// swipe up.
							if (deltaY < 0) {
								if ($($active_item).prev().length > 0) {
									$($active_item).prev().find("a").trigger("click");
								}
							}
							// swipe down.
							if (deltaY > 0) {
								if ($($active_item).next().length > 0) {
									$($active_item).next().find("a").trigger("click");
								}
							}
							if (Math.abs(deltaY) < 2) {
								return;
							}
						}
					});
			}
		}
		if ($scroll_keys == "on") {
			$(document).keydown(function (e) {
				var tag = e.target.tagName.toLowerCase();
				if (tag === "input" && tag === "textarea") {
					return;
				}
				switch (e.which) {
					case 38: // up arrow key.
						$($active_item).prev().find("a").trigger("click");
						break;
					case 40: // down arrow key.
						$($active_item).next().find("a").trigger("click");
						break;
					case 33: // pageup key.
						$($active_item).prev().find("a").trigger("click");
						break;
					case 36: // pagedown key.
						$($active_item).next().find("a").trigger("click");
						break;
					default:
						return;
				}
			});
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction("frontend/element_ready/eael-one-page-nav.default", OnePageNavHandler);
	});
})(jQuery);
