"use strict";

(() => {
	const getFieldValue = (form, id) => {
		let data = new FormData(form);
		let key = `form_fields[${id}]`;
		if (data.has(key)) {
			return data.get(key);
		}
		key = `form_fields[${id}][]`
		if (data.has(key))  {
			return data.getAll(key);
		}
		return "";
	}

	const makeGetFieldFunction = (form) => {
		return (id) => {
			let val = getFieldValue(form, id);
			// fields with multiple selections get summed up.
			if (Array.isArray(val)) {
				if (! val.length) {
					return 0;
				}
				val = val.map((v) => {
					let r = parseFloat(v);
					return isNaN(r) ? 0 : r;
				});
				return val.reduce((a,b) => a + b);
			}
			let r = parseFloat(val);
			return isNaN(r) ? 0 : r;
		}
	}

	const initializeAmountField = (wrapper, widget) => {
		let hiddenInput = wrapper.getElementsByClassName('dce-amount-hidden')[0];
		let visibleInput = wrapper.getElementsByClassName('dce-amount-visible')[0];
		let form = widget.getElementsByTagName('form')[0];
		let expression = hiddenInput.dataset.fieldExpression;
		let textBefore = hiddenInput.dataset.textBefore;
		let textAfter = hiddenInput.dataset.textAfter;
		let shouldRound = hiddenInput.dataset.shouldRound;
		let shouldFormat = hiddenInput.dataset.shouldFormat === 'yes';
		let formatPrecision = hiddenInput.dataset.formatPrecision;
		let roundPrecision = hiddenInput.dataset.roundPrecision;
		let refreshOn = hiddenInput.dataset.refreshOn;
		let realTime = hiddenInput.dataset.realTime === 'yes';
		if (hiddenInput.dataset.hide == 'yes') {
			wrapper.style.display = "none";
		}
		let getField = makeGetFieldFunction(form);
		let refresherGenerator;
		try {
			refresherGenerator = new Function('getField', `return () => { return ${expression}; }`);
		} catch (err) {
			console.error(err);
			visibleInput.value = amountFieldLocale.syntaxError;
			return;
		}
		let refresher = refresherGenerator(getField);
		let onChange = () => {
			let result = refresher();
			if ( shouldRound === 'yes' ) {
				result = Number(result).toFixed(roundPrecision);
			}
			if (hiddenInput.value === result) {
				// Field not changed, nohting to do.
				return;
			}
			let dispResult = shouldFormat ? Number(result)
				.toLocaleString(undefined, { minimumFractionDigits: formatPrecision, maximumFractionDigits: formatPrecision}) : result;
			hiddenInput.value = result;
			visibleInput.value = textBefore + dispResult + textAfter;
			if ("createEvent" in document) {
				var evt = document.createEvent("HTMLEvents");
				evt.initEvent("change", false, true);
				hiddenInput.dispatchEvent(evt);
			}
			else {
				hiddenInput.fireEvent("onchange");
			};
		}
		onChange();
		form.addEventListener(refreshOn, onChange);
	}

	const initializeAllAmountFields = ($scope) => {
		$scope.find('.elementor-field-type-amount').each((_, w) => initializeAmountField(w, $scope[0]));
	}

	jQuery(window).on('elementor/frontend/init', function() {
		if(elementorFrontend.isEditMode()) {
			return;
		}
		elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeAllAmountFields);
	});
})();
