function initializeSignaturePad(wrapper, $scope, $) {
	let clearButton = wrapper.querySelector("[data-action=clear]");
	let changeColorButton = wrapper.querySelector("[data-action=change-color]");
	let hiddenInput = wrapper.querySelector("input");
	let canvas = wrapper.querySelector("canvas");
	let signaturePad = new SignaturePad(canvas, {
		penColor: canvas.getAttribute('data-pen-color'),
		backgroundColor: canvas.getAttribute('data-background-color')
	});
	let useJPEG = canvas.getAttribute('data-jpeg') === 'yes',
		$button = $scope.find('.elementor-field-type-submit button');
	const updateValue = () => {
		if (! signaturePad.isEmpty()) {
			hiddenInput.value = signaturePad.toDataURL('image/' + ( useJPEG ? 'jpeg' : 'png'));
		}
	}
	$button.on('click', () => updateValue());
	$(canvas).on('mouseup touchend', () => updateValue());
	// The width of a canvas, and the css style `width' of a canvas are two
	// different things.  If they diverge the pen will write at an offset.
	// The following solves the issue:
	let responsiveWidth = parseInt(getComputedStyle(wrapper).getPropertyValue("--canvas-width"))
	let ratio = 400 / responsiveWidth;
	let context = canvas.getContext("2d")
	context.scale(ratio, ratio);

	clearButton.addEventListener("click", function(event) {
		// If the responsive width is large than default signaturePad.clear()
		// only partially clears the canvas. So do it manually:
		context.fillStyle = canvas.getAttribute('data-background-color');
		context.fillRect(0, 0, responsiveWidth, responsiveWidth / 2);
		signaturePad.clear();
		hiddenInput.value = '';
	});
}

var WidgetElements_FormSignature = function($scope, $) {
	let wrappers = $scope.find(".dce-signature-wrapper");
	wrappers.each ((_, w) => initializeSignaturePad(w, $scope, $));
}

// Make sure you run this code under Elementor..
jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/form.default', WidgetElements_FormSignature);
});
