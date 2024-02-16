
var InteractiveCard = function($scope, $) {
	var $interactiveCard = $scope.find(".interactive-card").eq(0),
		$id =
			$interactiveCard.data("interactive-card-id") !== undefined
				? $interactiveCard.data("interactive-card-id")
				: "",
		$animation =
			$interactiveCard.data("animation") !== undefined
				? $interactiveCard.data("animation")
				: "",
		$animation_time =
			$interactiveCard.data("animation-time") !== undefined
				? $interactiveCard.data("animation-time")
				: "";

	var options = {
		containerId: "interactive-card-" + $id,
		frontAnimation: {
			start: "fade-out",
			end: "fade-in"
		},
		rearAnimation: {
			start: "zoom-out",
			end: "zoom-in"
		},
		contentAnimation: $animation.toString(),
		revealTime: $animation_time
	};
	interactiveCards(options);

	let vimeoIframes = $('.eael-ic-vimeo-iframe', $scope);
	if( vimeoIframes.length > 0 ){
		vimeoIframes.closest('.content').addClass('eael-vimeo-conatiner');
	}

	//Stop video on click of close button (rear content)
	let interactiveCardId = '#interactive-card-' + $id;
	$(document).on('click', interactiveCardId + ' .close.close-me', function (){
		let $interactiveCardIframe = $(interactiveCardId + ' iframe');
		$interactiveCardIframe.attr('src', $interactiveCardIframe.attr('src') );
	});
};
jQuery(window).on("elementor/frontend/init", function() {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-interactive-card.default",
		InteractiveCard
	);
});
