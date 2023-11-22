"use strict";

(() => {
	const initVisibility = ($e) => {
		const e = $e[0];
		if (! $e.hasClass('dce-visibility-event') || $e.hasClass('dce-visibility-event-initialized')) {
			return;
		}
		$e.addClass('dce-visibility-event-initialized');
		const settings = JSON.parse(e.dataset.settings);
		const shouldHide = settings.dce_visibility_selected === 'hide';
		const triggerElement = settings.dce_visibility_click;
		const triggerEvent = settings.dce_visibility_event;
		const triggerAnimation = settings.dce_visibility_click_show;
		const hideOther = settings.dce_visibility_click_other;
		const showOnLoad = settings.dce_visibility_load === 'yes';
		const isToggle = settings.dce_visibility_click_toggle === 'yes';
		const showOnLoadDelay = settings.dce_visibility_load_delay;
		const showOnLoadAnimation = settings.dce_visibility_load_show;
		const $hideOther = jQuery(hideOther).not($e);
		const transitionDelay = 400;
		const afterShow = () => {
			elementorFrontend.elementsHandler.runReadyTrigger($e);
		}
		const toggle = () => {
			if ($e.hasClass('dce-visibility-element-hidden')) {
				$e.removeClass('dce-visibility-element-hidden');
				$e.hide();
			}
			if (triggerAnimation === 'slide') {
				$e.delay(transitionDelay).slideToggle(transitionDelay, afterShow);
			} else if(triggerAnimation === 'fade') {
				$e.delay(transitionDelay).fadeToggle(transitionDelay, afterShow);
			} else {
				$e.toggle();
				afterShow();
			}
		}
		const show = () => {
			// .removeClass and .hide are needed because otherwise .show will
			// always set display to block:
			$e.removeClass('dce-visibility-element-hidden');
			$e.hide();
			if (triggerAnimation === 'slide') {
				$e.delay(transitionDelay).slideDown(transitionDelay, afterShow);
			} else if(triggerAnimation === 'fade') {
				$e.delay(transitionDelay).fadeIn(transitionDelay, afterShow);
			} else {
				$e.show();
				afterShow();
			}
		}
		const hide = () => {
			if (triggerAnimation === 'slide') {
				$e.delay(transitionDelay).slideUp(transitionDelay);
			} else if(triggerAnimation === 'fade') {
				$e.delay(transitionDelay).fadeOut(transitionDelay);
			} else {
				$e.hide();
			}
		}
		const hideOtherElements = (callback) => {
			if (triggerAnimation === 'slide') {
				$hideOther.delay(transitionDelay).slideUp(transitionDelay, callback);
			} else if(triggerAnimation === 'fade') {
				$hideOther.delay(transitionDelay).fadeOut(transitionDelay, callback);
			} else {
				$hideOther.hide();
				callback();
			}
		}
		const triggerEventCallback = (event) => {
			event.preventDefault();
			const triggerFun = isToggle ? toggle : (shouldHide? hide : show);
			if (hideOther) {
				hideOtherElements(triggerFun);
			} else {
				triggerFun();
			}
		}
		if (triggerElement) {
			jQuery(triggerElement).on(triggerEvent, triggerEventCallback);
		}
		if (showOnLoad) {
			setTimeout(() => {
				if (shouldHide) {
					hide();
					return;
				}
				$e.removeClass('dce-visibility-element-hidden');
				$e.hide();
				if (showOnLoadAnimation === 'slide') {
					$e.slideToggle(transitionDelay, afterShow);
				} else if(showOnLoadAnimation === 'fade') {
					$e.fadeToggle(transitionDelay, afterShow);
				} else {
					$e.toggle();
					afterShow();
				}
			}, showOnLoadDelay );
		}
	};
	jQuery(window).on('elementor/frontend/init', () => {
		elementorFrontend.hooks.addAction('frontend/element_ready/global', initVisibility);
	});
})();
