jQuery( document ).ready(function( $ ) {
	// Check if form is submitted and override
	$( '.woothemes-updater-wrap #activate-products' ).submit(function( event ) {
		var license_keys_object = [];
		$('input[name^="license_keys["]').each(function( i, item ) {
			if ( $( this ).val().length > 0 ) {
				var license_object = { name: $( this ).attr("name").replace( 'license_keys[', '' ).replace( ']', '' ), key: $( this ).val(), method: $( this ).data( 'method' ) };
				license_keys_object.push( license_object );
			}
		});
		if ( license_keys_object.length > 0 ) {
			$( 'div.error.fade' ).remove();
			$( '#activate-products table.licenses' ).css({ opacity: 0.2 });
			$( '#activate-products' ).attr( 'disabled','disabled' );
			var submit_data = {
				action: 'woothemes_activate_license_keys',
				license_data: license_keys_object,
				security: WTHelper.activate_license_nonce
			};
			$.post( WTHelper.ajax_url, submit_data, function( data ) {
				var json_data = $.parseJSON( data );
				// Check if activation was successfull and reload page to show new activation
				if ( 'true' == json_data.success ) {
					window.location.href = json_data.url;
				}

				// If not sucessfull, show error messages.
				$( '.woothemes-updater-wrap .nav-tab-wrapper' ).after( json_data.message );
				$( '#activate-products table.licenses' ).css({ opacity: 1 });
				$( '#activate-products' ).removeAttr( 'disabled' );
				$('html, body').animate({
					scrollTop: $( '.woothemes-updater-wrap .nav-tab-wrapper' ).offset().top
				}, 2000);
			});
		}
		event.preventDefault();
	});

	// Tooltip for renews on in license table
	$('.dashicons-info').on('mouseover', function() {
		$(this).siblings('.renews-on-tooltip').css('display', 'block');
	});
	$('.dashicons-info').on('mouseout', function() {
		const tooltip = $(this).siblings('.renews-on-tooltip');
		setTimeout(function() {
			tooltip.fadeOut('fast');
		}, 1000);
	});

	// Variables
	var $submitButton = $('.woothemes-helper-submit-wrapper button');
	var $keyInputs = $('.dashboard_page_woothemes-helper .product_status input');

	// Click connect for them if they've just connected to WooCommerce.com
	if (window.location.search.indexOf('key=') > 0 && $('.woothemes-updater-wrap #activate-products').data('connected')) {
		$submitButton.trigger('submit');
	}

	// Add a message to show after key input manually
	$keyInputs.on('input', function() {
		var $connectMessage = $(this).siblings('.click-connect-message');
		if ($(this).val().length > 10) {
			var message = '<strong>Great!</strong> Now click the \'Connect Subscriptions\' button below.';
			if ($connectMessage.length > 0) {
				$connectMessage.html(message);
			} else {
				$(this).after('<div class="click-connect-message">' + message + '</div>');
			}
		} else {
			$connectMessage.remove();
		}
	})
});
