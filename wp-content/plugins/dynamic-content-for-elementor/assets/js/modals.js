(function ($) {
	var WidgetElementsModalsHandler = function ($scope, $) {

		var elementSettings = dceGetElementSettings($scope);
		var id_scope = $scope.attr('data-id');
		var id_popup = $scope.find('.dce-modal').attr('id');
		var is_animate = false;
		var modal = $scope.find('.dce-popup-container-' + id_scope);

		push_actions();

		const shouldDisableBecauseCookie = () => {
			if (elementSettings.always_visible) {
				return false;
			}
			return dceGetCookie(elementSettings.cookie_name);
		}

		// ON LOAD
		$('.dce-popup-container-'+id_scope+'.dce-popup-onload').each(function () {
			var id_modal = $(this).find('.dce-modal').attr('id');

			if (! shouldDisableBecauseCookie()) {
				dce_show_modal(id_modal);
			}
		});

		if( 'button' === elementSettings.trigger ) {
			// Button
			$scope.on('click', '.dce-button-open-modal, .dce-button-next-modal', function () {
				let id_modal = $(this).data('target');
				dce_show_modal(id_modal);
			});

			// Trigger other selectors
			if( elementSettings.trigger_other ){
				selectors = elementSettings.trigger_other_selectors;
				let target = $scope.find('button,img.dce-button-popup').first().data('target');
				$(selectors).click(function (e) {
					dce_show_modal(target);
				});
			}
		} else if ( 'scroll' === elementSettings.trigger ) {
			if (! shouldDisableBecauseCookie()) {
				if ($('.dce-popup-container-'+id_scope+'.dce-popup-scroll').length) {
					$(window).on('scroll', function () {
						$('.dce-popup-scroll').each(function () {
							if ($(window).scrollTop() > elementSettings.scroll_display_displacement) {
								$(this).removeClass('dce-popup-scroll');
								var id_modal = $(this).find('.dce-modal').attr('id');
								dce_show_modal(id_modal);
							}
						});
					});
				}
			}
		}

		$(window).on('wheel touchmove', function () {
			$('.modal-hide-on-scroll:visible').each(function () {
				$(this).removeClass('modal-hide-on-scroll');
				dce_hide_modal($(this).attr('id'));
			});
		});

		// Close with ESC
		$(document).on('keyup', function (evt) {
			if (evt.keyCode == 27) {
				$('.modal-hide-esc:visible').each(function () {
					dce_hide_modal($(this).attr('id'));
				});
			}
		});

		// Close with X
		$(document).on('click', '#'+id_popup+'.dce-modal .dce-modal-close, .dce-modal .dce-button-close-modal, .dce-modal .dce-button-next-modal', function () {
			dce_hide_modal(id_popup);
		});

		// Closing when click background
		$(document).on('click', '#'+id_popup+'-background.dce-modal-background-layer-close', function () {
			dce_hide_modal(id_popup);
		});

		function dce_show_modal(id_modal) {
			var id_modal_scope = id_modal.split('-');
			id_modal_scope.pop();
			id_modal_scope = id_modal_scope.join('-');

			var open_delay = 0;
			if (elementSettings.open_delay.size) {
				open_delay = elementSettings.open_delay.size;
			}
			if(!is_animate)
				setTimeout(function () {
					// Add the class 'open' to the body
					if (!elementorFrontend.isEditMode()) {
						is_animate = true;
						$('body').removeClass('modal-close-' + id_modal).removeClass('modal-close-' + id_modal_scope);
						$('body').addClass('modal-open-' + id_modal).addClass('modal-open-' + id_modal_scope).addClass('dce-modal-open');
						$('html').addClass('dce-modal-open');

						$('#' + id_modal + ' .modal-dialog').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {
							is_animate = false;
						});
					}
					if (elementSettings.wrapper_maincontent) {
						$(elementSettings.wrapper_maincontent).addClass('dce-push').addClass('animated').parent().addClass('perspective');
					}
					$('#' + id_modal).show();
					$('#' + id_modal + '-background').show().removeClass('fadeOut').addClass('fadeIn').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {
						is_animate = false;
					});
					if ( window.elementorFrontend && window.elementorFrontend.elementsHandler && window.elementorFrontend.elementsHandler.runReadyTrigger) {
						let runReadyTrigger = window.elementorFrontend.elementsHandler.runReadyTrigger;
						$( '#' + id_modal ).find('.elementor-widget').each( function() {
							runReadyTrigger($( this ));
						});
					}
				}, open_delay);
		}

		function dce_hide_modal(id_modal) {
			// set cookie
			var id_modal_scope = id_modal.split('-');
			id_modal_scope.pop();
			id_modal_scope = id_modal_scope.join('-');

			if (!elementSettings.always_visible && elementSettings.cookie_set === 'yes') {
				dceSetCookie(elementSettings.cookie_name, 1, elementSettings.cookie_lifetime);
			}
			var settings_close_delay = 0;
			if (elementSettings.close_delay) {
				settings_close_delay = elementSettings.close_delay;
			}

			$('.elementor-video').each(function(){
				if (typeof this.contentWindow === 'undefined') {
					return;
				}
				this.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*')
			});

			$('.elementor-video-iframe').each(function(){
				if (typeof this.contentWindow === 'undefined') {
					return;
				}
				this.contentWindow.postMessage('{"method":"pause"}', '*')
			});

			// Remove from body the "open" class
			$('body').removeClass('modal-open-' + id_modal).removeClass('modal-open-' + id_modal_scope);
			$('body').addClass('modal-close-' + id_modal).addClass('modal-close-' + id_modal_scope);
			$('#' + id_modal + '-background').hide();

			$('#' + id_modal + ' .modal-dialog').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {
				$('#' + id_modal).hide();
				setTimeout(function () {
					if (!elementorFrontend.isEditMode()) {
						$('body').removeClass('modal-close-' + id_modal).removeClass('modal-close-' + id_modal_scope).removeClass('dce-modal-open');
						$('html').removeClass('dce-modal-open');
						$(el.currentTarget).off('webkitAnimationEnd oanimationend msAnimationEnd animationend');
					}
					if (elementSettings.wrapper_maincontent)
						$(elementSettings.wrapper_maincontent).removeClass('dce-push').removeClass('animated').parent().removeClass('perspective');
				}, 300);
			});

			setTimeout(function () {
				$('#' + id_modal + '-background').removeClass('fadeIn').addClass('fadeOut');
			}, settings_close_delay);
		}

		function push_actions() {
			if (!elementorFrontend.isEditMode()) {
				$(modal).prependTo("body");
			}
		}
		// allow custom user scripts to close the modal:
		modal.data('dce-modal', {close: () => dce_hide_modal(id_popup)});
	};

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-popup.default', WidgetElementsModalsHandler);
	});
})(jQuery);
