( function( $ ) {
	var WidgetElements_ModalWindowHandler = function( $scope, $ ) {
		var width = 0,
		height = 0,
		paddingL, paddingR;

		var elementSettings = dceGetElementSettings( $scope );

		$scope.find('.dce-modalwindow-section').on('click','[data-type="modal-trigger"]', function(){
			var actionBtn = $(this),
				scaleValue = retrieveScale(actionBtn.next('.cd-modal-bg'));
				width = $(this).outerWidth();
				height = $(this).outerHeight();
				paddingL = elementSettings.fmw_padding.left;
				paddingR = elementSettings.fmw_padding.right;

			$(this).parent().css('width',width);
			$(this).css('width',width);

			actionBtn.addClass('to-circle').velocity({width: height, paddingLeft: 0, paddingRight: 0},100);
			actionBtn.next('.cd-modal-bg').addClass('is-visible').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
				animateLayer(actionBtn.next('.cd-modal-bg'), scaleValue, true);
			});

			//if browser doesn't support transitions...
			return false;
		});

		//trigger the animation - close modal window
		$scope.find('.dce-modalwindow-section .cd-modal-close').on('click', function(){
			closeModal();
			return false;
		});
		$(document).keyup(function(event){
			if(event.which == '27') closeModal();
		});

		$(window).on('resize', function(){
			//on window resize - update cover layer dimension and position
			if($scope.find('.dce-modalwindow-section.modal-is-visible').length > 0) window.requestAnimationFrame(updateLayer);
		});

		function retrieveScale(btn) {
			var btnRadius = btn.width()/2,
				left = btn.offset().left + btnRadius,
				top = btn.offset().top + btnRadius - $(window).scrollTop(),
				scale = scaleValue(top, left, btnRadius, $(window).height()+100, $(window).width())+100;

			btn.css('position', 'fixed').velocity({
				top: top - btnRadius,
				left: left - btnRadius,
				translateX: 0,
			}, 0);

			return scale;
		}

		function scaleValue( topValue, leftValue, radiusValue, windowW, windowH) {
			var maxDistHor = ( leftValue > windowW/2) ? leftValue : (windowW - leftValue),
				maxDistVert = ( topValue > windowH/2) ? topValue : (windowH - topValue);
			return Math.ceil(Math.sqrt( Math.pow(maxDistHor, 2) + Math.pow(maxDistVert, 2) )/radiusValue);
		}

		function animateLayer(layer, scaleVal, bool) {
			layer.velocity({ scale: scaleVal }, 600, function(){
				$('body').toggleClass('overflow-hidden', bool);
				(bool)
					? layer.parents('.dce-modalwindow-section').addClass('modal-is-visible').end().off('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend')
					: layer.removeClass('is-visible').removeAttr( 'style' ).siblings('[data-type="modal-trigger"]').removeClass('to-circle').velocity({width: width, paddingLeft: paddingL, paddingRight: paddingR}, { duration: 200, complete: function(e) { var btn = $(this); setTimeout(function(){ btn.removeAttr('style');btn.parent().removeAttr('style');},300); } });
			});
		}

		function updateLayer() {
			var layer = $scope.find('.dce-modalwindow-section.modal-is-visible').find('.cd-modal-bg'),
				layerRadius = layer.width()/2,
				layerTop = layer.siblings('.btn').offset().top + layerRadius - $(window).scrollTop(),
				layerLeft = layer.siblings('.btn').offset().left + layerRadius,
				scale = scaleValue(layerTop, layerLeft, layerRadius, $(window).height(), $(window).width());

			layer.velocity({
				top: layerTop - layerRadius,
				left: layerLeft - layerRadius,
				scale: scale,
			}, 0);
		}

		function closeModal() {

			var section = $scope.find('.dce-modalwindow-section.modal-is-visible');
			section.removeClass('modal-is-visible').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){

			});
			animateLayer(section.find('.cd-modal-bg'), 1, false);
			//if browser doesn't support transitions...
		}
	};

	// Make sure you run this code under Elementor..
	$( window ).on( 'elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/dyncontel-modalwindow.default', WidgetElements_ModalWindowHandler );
	} );
} )( jQuery );
