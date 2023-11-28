(function ($) {
	window.EAELOffcanvasContent = function ($scope) {
		this.node = $scope;
		if ($scope.find(".eael-offcanvas-toggle").length < 1) return;

		this.wrap = $scope.find(".eael-offcanvas-content-wrap");
		this.content = $scope.find(".eael-offcanvas-content");
		this.button = $scope.find(".eael-offcanvas-toggle");
		this.settings = this.wrap.data("settings");
		this.id = this.settings.content_id;
		this.transition = this.settings.transition;
		this.esc_close = this.settings.esc_close;
		this.body_click_close = this.settings.body_click_close;
		this.open_offcanvas = this.settings.open_offcanvas;
		this.direction = this.settings.direction;
		this.duration = 500;

		this.init();
	};

	EAELOffcanvasContent.prototype = {
		id: "",
		node: "",
		wrap: "",
		content: "",
		button: "",
		settings: {},
		transition: "",
		duration: 400,
		initialized: false,
		animations: ["slide", "slide-along", "reveal", "push"],

		init: function () {
			if (!this.wrap.length) {
				return;
			}

			$("html").addClass("eael-offcanvas-content-widget");

			if ($(".eael-offcanvas-container").length === 0) {
				$("body").wrapInner('<div class="eael-offcanvas-container eael-offcanvas-container-' + this.id + '" />');
				this.content.insertBefore(".eael-offcanvas-container");
			}

			if (this.wrap.find(".eael-offcanvas-content").length > 0) {
				if ($(".eael-offcanvas-container > .eael-offcanvas-content-" + this.id).length > 0) {
					$(".eael-offcanvas-container > .eael-offcanvas-content-" + this.id).remove();
				}
				if ($("body > .eael-offcanvas-content-" + this.id).length > 0) {
					$("body > .eael-offcanvas-content-" + this.id).remove();
				}
				$("body").prepend(this.wrap.find(".eael-offcanvas-content"));
			}

			this.bindEvents();
		},

		destroy: function () {
			this.close();

			this.animations.forEach(function (animation) {
				if ($("html").hasClass("eael-offcanvas-content-" + animation)) {
					$("html").removeClass("eael-offcanvas-content-" + animation);
				}
			});

			if ($("body > .eael-offcanvas-content-" + this.id).length > 0) {
				//$('body > .eael-offcanvas-content-' + this.id ).remove();
			}
		},

		bindEvents: function () {
			if (this.open_offcanvas === "yes") {
				this.show();
			}
			this.button.on("click", $.proxy(this.toggleContent, this));

			$("body").delegate(".eael-offcanvas-content .eael-offcanvas-close", "click", $.proxy(this.close, this));

			if (this.esc_close === "yes") {
				this.closeESC();
			}
			if (this.body_click_close === "yes") {
				this.closeClick();
			}
		},

		toggleContent: function () {
			if (!$("html").hasClass("eael-offcanvas-content-open")) {
				this.show();
			} else {
				this.close();
			}
		},

		show: function () {
			$(".eael-offcanvas-content-" + this.id).addClass("eael-offcanvas-content-visible");
			// init animation class.
			$("html").addClass("eael-offcanvas-content-" + this.transition);
			$("html").addClass("eael-offcanvas-content-" + this.direction);
			$("html").addClass("eael-offcanvas-content-open");
			$("html").addClass("eael-offcanvas-content-" + this.id + "-open");
			$("html").addClass("eael-offcanvas-content-reset");
		},

		close: function () {
			$("html").removeClass("eael-offcanvas-content-open");
			$("html").removeClass("eael-offcanvas-content-" + this.id + "-open");
			setTimeout(
				$.proxy(function () {
					$("html").removeClass("eael-offcanvas-content-reset");
					$("html").removeClass("eael-offcanvas-content-" + this.transition);
					$("html").removeClass("eael-offcanvas-content-" + this.direction);
					$(".eael-offcanvas-content-" + this.id).removeClass("eael-offcanvas-content-visible");
				}, this),
				500
			);
		},

		closeESC: function () {
			var self = this;

			if ("" === self.settings.esc_close) {
				return;
			}

			// menu close on ESC key
			$(document).on("keydown", function (e) {
				if (e.keyCode === 27) {
					// ESC
					self.close();
				}
			});
		},

		closeClick: function () {
			var self = this;

			$(document).on("click", function (e) {
				if (
					$(e.target).is(".eael-offcanvas-content") ||
					$(e.target).parents(".eael-offcanvas-content").length > 0 ||
					$(e.target).is(".eael-offcanvas-toggle") ||
					$(e.target).parents(".eael-offcanvas-toggle").length > 0
				) {
					return;
				} else {
					self.close();
				}
			});
		},
	};
})(jQuery);
