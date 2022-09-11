/**
 * ywsbs-frontend.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 1.0.0
 */

/* global yith_ywsbs_frontend */
jQuery(function ($) {

	'use strict';

	var $body = $('body');

	var blockParams = {
		message        : null,
		overlayCSS     : { background: '#fff', opacity: 0.7 },
		ignoreIfBlocked: true
	};

	// Change Add to cart Label on variable product when a variation is selected.
	$.fn.yith_ywsbs_variations = function () {
		var $form = $('.variations_form'),
			$button = $form.find('.single_add_to_cart_button');


		$form.on('found_variation', function (event, variation) {
			if (variation.is_subscription == true) {
				$button.text(yith_ywsbs_frontend.add_to_cart_label);
			} else {
				$button.text(yith_ywsbs_frontend.default_cart_label);
			}
		});

	};

	if ($body.hasClass('single-product')) {
		$.fn.yith_ywsbs_variations();
	}





	/**
	 * MODAL
	 */
	var modal = false,
		modal_wrapper = false;

	var openModal = function(){
		modal.fadeIn('slow');
	};

	var closeModal = function(){
		modal.fadeOut('slow');
	};


	var modalWrapperPosition = function () {
		var modalWidth = modal_wrapper.width(),
			modalHeigth = modal_wrapper.width(),
			window_w = $(window).width(),
			window_h = $(window).height(),
			margin = ((window_h - modalHeigth) / 2) + 'px',
			width = ((window_w - 100) > modalWidth) ? modalWidth + 'px' : 'auto';

		modal_wrapper.css({
			'margin-top': margin,
			'margin-bottom': margin,
			'width': width,
		});
	};

	$(document).on('click', '.ywsbs-dropdown-item, .open-modal', function (e) {
		e.stopPropagation();
		var $t = $(this);
		var	modalToOpenID = $t.data('target');

		modal = $('#' + modalToOpenID);
		modal_wrapper = modal.find('.ywsbs-modal-wrapper');
		modalWrapperPosition();
		openModal();
	});

	$(document).on('click', '.ywsbs-modal .close', function (e) {
		e.preventDefault();
		closeModal();
	});

	$(window).on( 'click', function (e) {
		var target = e.target;
		if( $(target).hasClass('ywsbs-modal-container')){
			closeModal();
		}
	});


	/**
	 * Change subscription status
	 */

	function reloadSubscriptionView(){
		$.post( document.location.href, function (data) {
			if (data != '') {
				var c = $("<div></div>").html(data),
					wrap = c.find('.ywsbs-subscription-view-wrap');
				$('.ywsbs-subscription-view-wrap').html(wrap.html());
			}
		});
	}

	$(document).on('click','.ywsbs-action-button', function(e){
		e.preventDefault();
		var $t = $(this),
			container = $t.closest('.ywsbs-action-button-wrap'),
			modalWrapper = $t.closest('.ywsbs-modal-body'),
			modalBody = modalWrapper.find('.ywsbs-content-text'),
			closeButton = modalWrapper.find('.close-modal-wrap'),
			status = $t.data('action'),
			sbs_id = $t.data('id'),
			security = $t.data('nonce');

		container.block( blockParams );
		var data = {
			subscription_id: sbs_id,
			action: 'ywsbs_'+status+'_subscription',
			change_status: status,
			security: security,
			context:'frontend'
		};

		$.ajax({
			url: yith_ywsbs_frontend.ajaxurl,
			data: data,
			type: 'POST',
			success: function (response) {
				if( response.success ){
					modalBody.html( response.success );
				}

				if( response.error){
					modalBody.html( '<span class="error">'+response.error+'</span>' );
				}

				$t.fadeOut();
				closeButton.fadeOut();
				setTimeout( function(){ closeModal(); reloadSubscriptionView( sbs_id ); }, 2500);
			},
			complete: function () {
				container.unblock();
			}
		});
	});




});