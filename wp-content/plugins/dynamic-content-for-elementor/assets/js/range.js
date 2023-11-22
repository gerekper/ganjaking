"use strict";

function initializeRangeField( wrapper, widget) {
	let range = wrapper.getElementsByTagName('input')[0];
	let showValue = range.dataset.showValue;

	if ( showValue ) {
		let rangeValue = wrapper.getElementsByClassName('range-value')[0];
		let textBefore = range.dataset.textBefore;
		let textAfter = range.dataset.textAfter;
		rangeValue.innerHTML = textBefore + ' <span>' +  range.value + '</span> ' + textAfter;

		range.oninput = function() {
			rangeValue.innerHTML = textBefore + ' <span>' + this.value + '</span> ' + textAfter;
		}
	}
}

function initializeAllRangeFields($scope) {
	$scope.find('.elementor-field-type-dce_range').each((_, w) => initializeRangeField(w, $scope[0]));
}

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeAllRangeFields);
});
