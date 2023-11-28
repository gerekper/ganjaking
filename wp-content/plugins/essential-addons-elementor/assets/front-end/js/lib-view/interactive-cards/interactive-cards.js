(function ($) {
	"use strict";

	// Interactive phone screen
	window.interactiveCards = function (options) {
		var values = {
			container: options.containerId,
			frontAnimation: options.frontAnimation,
			rearAnimation: options.rearAnimation,
			contentAnimation: options.contentAnimation,
			revealTime: options.revealTime,
		};

		var interactiveCard = $("#" + values.container);
		var frontContent = $("#" + values.container + " .front-content");
		var imageScreen = $(
			"#" + values.container + " .front-content .image-screen"
		);
		var imageScreenBg = imageScreen.data("bg");
		var contentShow = $("#" + values.container + " .content");
		var closeMe = $("#" + values.container + " .close-me");

		imageScreen.on("click", function () {
			$(this)
				.removeClass(values.frontAnimation.end)
				.addClass(values.frontAnimation.start);
			setTimeout(function () {
				frontContent
					.removeClass(values.rearAnimation.end)
					.addClass(values.rearAnimation.start);
				setTimeout(function () {
					contentShow.addClass(values.contentAnimation);
				}, options.revealTime * 2);
			}, options.revealTime);

			var $thisWrapper = $(this).closest('.interactive-card').not('.eaNiceScrollActivated');
			if ($thisWrapper.length) {
				eaNiceScroll($thisWrapper);
				$thisWrapper.addClass('eaNiceScrollActivated');
			}
		});

		closeMe.on("click", function () {
			contentShow.removeClass(values.contentAnimation);
			setTimeout(function () {
				frontContent
					.removeClass(values.rearAnimation.start)
					.addClass(values.rearAnimation.end);
				setTimeout(function () {
					imageScreen
						.removeClass(values.frontAnimation.start)
						.addClass(values.frontAnimation.end);
				}, options.revealTime * 2);
			}, options.revealTime);
		});

		/**
		 * Carousel Scripts
		 */
		var carouselContainer = $(
				"#" + options.containerId + " .carousel-container"
			),
			carousel = carouselContainer.find("ul"),
			carouselItem = carousel.find("li"),
			containerWidth = carouselContainer.width(),
			carouselItemWidth = carouselItem.first().children("img").width(),
			carouselItemQuantity = carousel.children("li").length,
			carouselWidth = carouselItemWidth * carouselItemQuantity,
			currentItem = 1;

		carousel.css("width", carouselWidth + "px");
		carouselItem.css("width", containerWidth + "px");

		var navSelector = $("#" + options.containerId + " a.nav");
		navSelector.on("click", function (e) {
			e.preventDefault();
			var navButtonSelector = $(this).data("nav");
			if ("next" === navButtonSelector) {
				if (currentItem === carouselItemQuantity) {
					currentItem = 1;
					scrollIn(currentItem, carouselItemWidth);
				} else {
					currentItem++;
					scrollIn(currentItem, carouselItemWidth);
				}
			} else if ("prev" === navButtonSelector) {
				if (currentItem == 1) {
					currentItem = carouselItemQuantity;
					scrollIn(currentItem, carouselItemWidth);
				} else {
					currentItem--;
					scrollIn(currentItem, carouselItemWidth);
				}
			}
		});

		function scrollIn(currentItem, width) {
			var slideItem = -(currentItem - 1) * width;
			carousel.animate({
				left: slideItem,
			});
		}

		// NiceScroll Effect
		function eaNiceScroll($scope) {
			var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

			if (!isMobile) {
				$(".content-overflow", $scope).niceScroll({
					cursorcolor: "#424242",
					cursorwidth: "5px",
					cursorborder: "1px solid #fff",
					cursorborderradius: "5px",
					zindex: 1000,
				});
			}
		}
	};
})(jQuery);
