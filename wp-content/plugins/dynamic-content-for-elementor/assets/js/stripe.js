"use strict";
function makeElementsStyle(style) {
	return {
		base: {
			color: style.color,
			fontFamily: style.fontFamily + ' sans-serif',
			fontSmoothing: "antialiased",
			fontSize: style.fontSize,
			fontWeight: style.fontWeight,
			fontStyle: style.fontStyle,
			"::placeholder": {
				color: "#aab7c4"
			}
		},
		invalid: {
			color: "#fa755a",
			iconColor: "#fa755a"
		},
	};
}

function initializeStripeField(wrapper, $scope) {
	const $form = $scope.find('form');
	const postid = $form.find('[name=post_id]').val();
	const formid = $form.find('[name=form_id]').val();
	const queriedid = $form.find('[name=queried_id]').val();
	const $submitButton = $scope.find('.elementor-field-type-submit button');
	const $wrapper = jQuery(wrapper);
	const $hiddenInput = $wrapper.find('input');
	const $error = $wrapper.find('.stripe-error');
	const $elementsWrapper = $wrapper.find('.dce-stripe-elements');
	const required = $elementsWrapper.attr('data-required') === 'true';
	const fieldIndex = $elementsWrapper.attr('data-field-index');
	const intentURL = $elementsWrapper.attr('data-intent-url');
	if (typeof window.dceStripe === 'undefined') {
		window.dceStripe = Stripe($elementsWrapper.attr('data-publishable-key'));
	}
	const elements = window.dceStripe.elements();
	let submissionId = false;
	let confirmedSubmissionId = false;
	const style = makeElementsStyle( {
		color: $elementsWrapper.css('color'),
		fontFamily: $elementsWrapper.css('font-family'),
		fontSize: $elementsWrapper.css('font-size'),
		fontWeight: $elementsWrapper.css('font-weight'),
		fontStyle: $elementsWrapper.css('font-style'),
	});
	let $submit = $form.find('button[type="submit"]')
	const reenableSubmit = () => {
		$submitButton.removeAttr('disabled');
		$submitButton.removeClass('dce-submit-disabled');
	}
	const disableSubmit = () => {
		$submitButton.attr('disabled', 'disabled');
		$submitButton.addClass('dce-submit-disabled');
	}
	const cardElement = elements.create('card', {style: style, hidePostalCode: false });
	const isCardEmpty = () => {
		return $elementsWrapper.hasClass('StripeElement--empty');
	}
	cardElement.mount($elementsWrapper[0]);

	const confirmPayment = (clientSecret, subscriptionId) => {
		window.dceStripe.confirmCardPayment(clientSecret, { payment_method: {
			type: 'card',
			card: cardElement,
		}}).then((result) => {
			if (result.error) {
				reenableSubmit();
				$error.text(result.error.message);
				$error.show();
			} else {
				$error.hide();
				const fieldValue = subscriptionId ? subscriptionId : result.paymentIntent.id;
				$hiddenInput.val(fieldValue);
				confirmedSubmissionId = submissionId;
				$form.trigger('submit');
			}
		});
	}

	const fetchClientSecret = () => {
		// Backend might need current get parameters for setting the item value:
		let query = window.location.search.slice(1);
		let url = intentURL + '&' + query;
		let data = new FormData($form[0]);
		data.set('field_index', fieldIndex);
		return fetch(url, {
			method: 'POST',
			body: data,
		}).then(response => response.json());
	}

	$submit.on('click', (event) => {
		submissionId = Date.now();
	});

	$form.on('submit', (event) => {
		// if invisibile because conditional fields:
		if (wrapper.dataset.dceConditionsFieldStatus === 'inactive' || wrapper.style.display == 'none') {
			return;
		}
		if (!required && isCardEmpty()) {
			return;
		}
		if ($hiddenInput.val().startsWith('sub_')) {
			// confirmed subscriptions have already been paid, we should not do
			// anything else from now on.
			return;
		}
		if (submissionId) {
			// Here we prevent a simple payment (not a subscription, that is
			// already being taken care of by the previous if) from being
			// confirmed again in case this callback is run more than once
			// during the same submit. (Could happen because of js triggered
			// submits): We use a submission Id created before to distinguish a
			// genuine user triggered submission from a js triggered one.
			if (submissionId === confirmedSubmissionId) {
				return;
			}
		} else {
			// Random unique error id:
			$error.text("Error 7V0N5J");
			$error.show();
			return;
		}
		event.preventDefault();
		event.stopImmediatePropagation();
		disableSubmit();
		$error.hide();
		fetchClientSecret().then(data => {
			if (! data.success) {
				$error.text(data.data.message);
				$error.show();
				reenableSubmit();
			} else {
				confirmPayment(data.data.client_secret, data.data.subscription_id);
			}
		}).catch(error => console.log(error));
	});
}

function initializeAllStripeFields($scope) {
	$scope.find('.elementor-field-type-dce_form_stripe').each((_, w) => initializeStripeField(w, $scope));
}

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeAllStripeFields);
});
