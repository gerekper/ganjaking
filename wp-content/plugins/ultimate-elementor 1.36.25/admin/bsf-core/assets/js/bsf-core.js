jQuery( document ).on('click', '.bsf-envato-form-activation', function(event) {
	submitButton 	 		= jQuery( this ).parent('.submit-button-wrap');
	console.log(submitButton);
	product_id 				= submitButton.siblings( 'form input[name="product_id"]' ).val();
	url 					= submitButton.siblings( 'form input[name="url"]' ).val();
	redirect 				= submitButton.siblings( 'form input[name="redirect"]' ).val();
	privacyConsent 			= submitButton.siblings( 'input#bsf-license-privacy-consent').val();
	termsConditionConsent 	= submitButton.siblings( 'input#bsf-license-terms-conditions-consent').val();
	envato_activation_nonce = bsf_core.envato_activation_nonce;
	jQuery.ajax({
		url: ajaxurl,
		dataType: 'json',
		data: {
			action: 'bsf_envato_redirect_url',
			product_id: product_id,
			url: url, 
			redirect: redirect,
			privacy_consent: privacyConsent,
			terms_conditions_consent: termsConditionConsent,
			envato_activation_nonce: envato_activation_nonce,
		}
	})
	.done(function( response ) {
		window.location = response.data.url;
		return true;
	})
	.fail(function(e) {
		return false;
	});

	return false;
});