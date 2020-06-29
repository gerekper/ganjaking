/*
 * One Page Checkout JS: add/remove items from a One Page Checkout page via Ajax
 */
jQuery(document).ready(function($){

	var response_messages = '';
	var timeout;
	var delay = 500;

	var $checkout = $( '.checkout' );
	var $body = $( 'body' );
	/**
	 * Review Order Template Item Management (Removal & Quantity Adjustment)
	 */

	// Quantity buttons
	$checkout.on( 'change input', '#order_review .opc_cart_item div.quantity input.qty', function(e) {

		var input = $(this),
			selectors = '.checkout #order_review .opc_cart_item div.quantity input.qty';

		clearTimeout(timeout);

		timeout = setTimeout(function() {

			var data = {
				quantity:    input.val(),
				add_to_cart: parseInt( input.closest( '.opc_cart_item' ).data( 'add_to_cart' ) ),
				update_key:  input.closest( '.opc_cart_item' ).data( 'update_key' ),
				nonce:       wcopc.wcopc_nonce,
			};

			if ( data['quantity'] == 0 ) {
				data['action'] = 'pp_remove_from_cart';
			} else {
				data['action'] = 'pp_update_add_in_cart';
			}

			input.ajax_add_remove_product( data, e, selectors );

		}, delay );

		e.preventDefault();

	} );

	// Remove buttons
	$checkout.on( 'click', '#order_review .opc_cart_item a.remove', function(e) {

		var data = {
				action:      'pp_remove_from_cart',
				add_to_cart: parseInt( $(this).closest( '.opc_cart_item' ).data( 'add_to_cart' ) ),
				update_key:  $(this).closest( '.opc_cart_item' ).data( 'update_key' ),
				nonce:       wcopc.wcopc_nonce,
			},
			selectors = '.checkout #order_review .opc_cart_item a.remove';

		$(this).ajax_add_remove_product( data, e, selectors );

		e.preventDefault();

	} );

	/**
	 * Single Product Template
	 */

	/* Add/remove products with button element or a tags */
	$( '#opc-product-selection button.single_add_to_cart_button, .wcopc-product-single button.single_add_to_cart_button' ).on( 'click', function(e) {

		var is_variable    = $(this).closest( '.variations_form' ).find( 'input[name="variation_id"]' ).length === 1,
			is_grouped     = $(this).siblings( '.group_table' ).length >= 1,
			add_to_cart_id = $(this).closest( '.cart' ).find( '[name="add-to-cart"]' ).val(),
			has_quantity   = $(this).closest( '.cart' ).find( 'input[name="quantity"]' ).length === 1,
			data = {
				action:       'pp_add_to_cart',
				nonce:        wcopc.wcopc_nonce,
				input_data:   $(this).closest( '.product-quantity, .wcopc-product-single form' ).find( 'input[name!="variation_id"][name!="product_id"][name!="add-to-cart"][name!="quantity"], select, textarea' ).serialize(),
			},
			selectors = '#opc-product-selection button.single_add_to_cart_button, .wcopc-product-single button.single_add_to_cart_button';

		if ( is_grouped ) {

			data.add_to_cart = [];

			$(this).siblings( '.group_table' ).find( 'input[name^="quantity"]' ).each( function( index, value ) {

				if ( ( ! $( this ).is( ':checkbox' ) && $( this ).val() > 0 ) || ( $( this ).is( ':checkbox' ) && $( this ).is( ':checked' ) ) ) {

					var product_id = parseInt( $( this ).attr( 'name' ).match( /quantity\[(\d+)\]/ )[1] ),
						quantity   = parseInt( $( this ).val() );

					data.add_to_cart.push({
						'product_id': product_id,
						'quantity':   quantity,
					});
				}
			});

			data.add_to_cart.forEach(function(add_to_cart_data) {

				data.add_to_cart = parseInt( add_to_cart_data.product_id );
				data.quantity    = parseInt( add_to_cart_data.quantity );

				$(this).ajax_add_remove_product( data, e, selectors, false );
			});

		} else {

			data.add_to_cart = parseInt( add_to_cart_id );

			if ( is_variable ) {
				data.variation_id = parseInt( $(this).closest( '.variations_form' ).find( 'input[name="variation_id"]' ).val() );
			}

			// The quantity input field might be missing if a product is sold individually or has only 1 unit of stock remaining
			if ( has_quantity ) {
				data.quantity = parseInt( $(this).closest( '.cart' ).find( 'input[name="quantity"]' ).val() );
			}

			$(this).ajax_add_remove_product( data, e, selectors );
		}

		e.preventDefault();
	} );

	/**
	 * Other Templates
	 */

	/* Add/remove products with number input type */
	$( '#opc-product-selection input[type="number"][data-add_to_cart]' ).on( 'change input', function(e) {
		var input = $(this),
			selectors = '#opc-product-selection input[type="number"][data-add_to_cart]';

		clearTimeout(timeout);

		timeout = setTimeout(function() {

			var data = {
				quantity:    input.val(),
				add_to_cart: parseInt( input.data( 'add_to_cart' ) ),
				input_data:  input.closest( '.product-quantity' ).find( 'input[name!="product_id"], select, textarea' ).serialize(),
				nonce:       wcopc.wcopc_nonce,
			};

			if ( data['quantity'] == 0 ) {
				data['action'] = 'pp_remove_from_cart';
			} else {
				data['action'] = 'pp_update_add_in_cart';
			}

			input.ajax_add_remove_product( data, e, selectors );

		}, delay );

		e.preventDefault();

	} );

	/* Add/remove products with radio or checkbox inputs */
	$( '#opc-product-selection input[type="radio"][data-add_to_cart], #opc-product-selection input[type="checkbox"][data-add_to_cart]' ).on( 'change', function(e) {

		var input = $(this),
			selectors = '#opc-product-selection input[type="radio"][data-add_to_cart], #opc-product-selection input[type="checkbox"][data-add_to_cart]';

		clearTimeout(timeout);

		timeout = setTimeout(function() {

			var data = {
				add_to_cart: parseInt( input.data( 'add_to_cart' ) ),
				nonce:       wcopc.wcopc_nonce
			};

			if ( input.is( ':checked' ) ) {

				if ( input.prop( 'type' ) == 'radio' ) {

					data.empty_cart = 'true';
					$( 'input[data-add_to_cart]' ).prop( 'checked', false );
					input.prop( 'checked', true );
					$( '.selected' ).removeClass( 'selected' );
				}

				data.action = 'pp_add_to_cart';
				input.parents( '.product-item' ).addClass( 'selected' );

			} else {

				data.action = 'pp_remove_from_cart';
				input.parents( '.product-item' ).removeClass( 'selected' );

			}

			input.ajax_add_remove_product( data, e, selectors );

		}, delay );

	} );

	/* Add/remove products with button element or a tags */
	$( '#opc-product-selection a[data-add_to_cart], #opc-product-selection button[data-add_to_cart]' ).on( 'click', function(e) {

		var data = {
				add_to_cart: parseInt( $(this).data( 'add_to_cart' ) ),
				nonce:       wcopc.wcopc_nonce,
				input_data:  $(this).closest( '.product-quantity' ).find( 'input[name!="product_id"], select, textarea' ).serialize(),
			},
			selectors = '#opc-product-selection a[data-add_to_cart], #opc-product-selection button[data-add_to_cart]';

		// Toggle button on or off
		if ( ! $(this).parents( '.product-item' ).hasClass( 'selected' ) ) {
			data.action = 'pp_add_to_cart';
			$(this).parents( '.product-item' ).addClass( 'selected' );
		} else {
			data.action = 'pp_remove_from_cart';
			$(this).parents( '.product-item' ).removeClass( 'selected' );
		}

		$(this).ajax_add_remove_product( data, e, selectors );
	} );

	/* Add products from any Easy Pricing Table template */
	$( 'a.ptp-button, a.ptp-dg5-button, a.ptp-dg6-button, a.ptp-dg7-button, a.ptp-fancy-button, a.btn.sign-up, .ptp-stylish-pricing_button a, .ptp-design4-col > a' ).on( 'click',function(e) {

		var productParams = getUrlsParams($(this)[0].search.substring(1)),
			selectors = 'a.ptp-button, a.ptp-dg5-button, a.ptp-dg6-button, a.ptp-dg7-button, a.ptp-fancy-button, a.btn.sign-up, .ptp-stylish-pricing_button a, .ptp-design4-col > a';

		var data = {
			action:      'pp_add_to_cart',
			add_to_cart: productParams['add-to-cart'],
			empty_cart:  'true',
			nonce:       wcopc.wcopc_nonce
		};

		delete productParams['add-to-cart'];
		data.input_data = $.param( productParams );

		$( this ).ajax_add_remove_product( data, e, selectors );
	} );

	// Set response messages when the checkout is fully updated (because it would remove them if we set them before that)
	$body.on( 'updated_checkout', function(){
		if ( response_messages.length > 0 ) {
			var $opc_messages = $( '#opc-messages' );

			$opc_messages.prepend( response_messages );

			if ( 'yes' === wcopc.autoscroll && ! $opc_messages.visible() ){
				$( 'html, body' ).animate( {
					scrollTop: ($opc_messages.offset().top - 150 )
				}, 500 );
			}

			response_messages = '';
		}
	});

	/* Function to add or remove product from cart via an ajax call */
	$.fn.ajax_add_remove_product = function( data, e, selectors, async_ajax ) {

		// Default to synchonus Ajax, but for Grouped products, use async
		async_ajax = ( typeof async_ajax !== 'undefined' ) ?  async_ajax : true;

		// Guard against race conditions by disabling the inputs
		$(selectors).attr('disabled', 'disabled');

		// Custom event for devs to hook into before posting of products for processing
		$('body').trigger( 'opc_add_remove_product', [ data, e, selectors ] );

		// Read from opc_add_remove_product trigger above and maybe avoid the AJAX add to cart call.
		if ( data.invalid === true ) {
			$( selectors ).removeAttr( 'disabled' );
			return;
		}

		$.ajax({
			type: 'POST',
			url:   wcopc.ajax_url,
			data:  data,
			async: async_ajax,
			dataType: 'json',
			success: function( response, status, xhr ) {

			var fragments;

			try {

				if ( response === '-1' ) {
					throw 'invalid response';
				}

				if (xhr.getResponseHeader('Content-Type') == 'application/json') {
					response = $.parseJSON(response);
				}

				// Get fragments
				fragments = response.fragments;

				var inputs = $( '#opc-product-selection [data-add_to_cart]' );

				inputs.each( function( index, value ) {

					var product_id = $(this).data( 'add_to_cart' ),
						in_cart    = false;

					$.each( response.products_in_cart, function( cart_item_id, cart_item_data ) {
						if ( ( product_id == cart_item_id || product_id == cart_item_data.product_id ) ) {
							in_cart = true;
						}
					} );

					if ( $(this).prop( 'type' ) == 'number' ) {

						if ( in_cart ) {
							$(this).val( response.products_in_cart[ product_id ].quantity ).data( 'cart_quantity', response.products_in_cart[ product_id ].quantity );
						} else {
							$(this).val(0).data( 'cart_quantity', 0 );
						}

					} else if ( $(this).is( 'a, button' ) ) {

						if ( in_cart ) {
							$(this).parents( '.product-item' ).addClass( 'selected' );
						} else {
							$(this).parents( '.product-item' ).removeClass( 'selected' );
						}

					} else {

						if ( in_cart ) {
							$(this).prop( 'checked', true );
						} else {
							$(this).prop( 'checked', false );
							$(this).parents( '.product-item' ).removeClass( 'selected' );
						}
					}

				} );

				// Store messages for use when checkout has finished updating
				response_messages = response.messages;

			} catch ( err ) {

				if ( 'undefined' == typeof response.messages ) {
					response_messages = wcopc.ajax_error_notice;
				} else {
					response_messages = response.messages;
				}

				var $opc_messages = $( '#opc-messages' );
				$opc_messages.prepend( response.messages );

				$( 'html, body' ).animate( {
					scrollTop: ( $opc_messages.offset().top - 50 )
				}, 500);
			}

			$( '#opc-messages .woocommerce-error, #opc-messages .woocommerce-message, #opc-messages .woocommerce-info' ).remove();

			// remove 'loading' class that Flatsome theme adds
			$('button.single_add_to_cart_button').removeClass('loading');

			// Custom event for devs to hook into after products have been processed
			$body.trigger( 'after_opc_add_remove_product', [ data, response ] );

			// Tell WooCommerce to update totals
			$body.trigger( 'update_checkout' );

			// Block fragments class
			if ( fragments ) {
				$.each( fragments, function( key, value ) {
					$( key ).addClass( 'updating' );
				});
			}

			// Block fragments
				var $updating = $( '.updating' );
				$updating.fadeTo( '400', '0.6' ).block({
				message: null,
				overlayCSS: {
					opacity: 0.6
				}
			});

			// Replace fragments
			if ( fragments ) {
				$.each( fragments, function( key, value ) {
					$( key ).replaceWith( value );
				});
			}

			// Unblock
			$updating.stop( true ).css( 'opacity', '1' ).unblock();

			// It is now safe to change the cart again
			$(selectors).removeAttr('disabled');

		} } );

		e.preventDefault();
	};

	/* Only display the place order button when a product has been selected */
	showHidePlaceOrder();

	/* Append "Complete Order" anchor and "data-add_to_cart" attribute to single-product template buttons */
	initSingleProductTemplateButtons();

	/* Init custom order-review template quantity buttons */
	initOrderReviewQtyButtons();

	$body.on( 'updated_checkout',function() {
		showHidePlaceOrder();

		/* Init custom order-review template quantity buttons */
		initOrderReviewQtyButtons();

		/* Check the create account fields are correctly displayed when subscriptions are part of the cart/order after ajax update */
		create_account_toggle();
	} );

	function initOrderReviewQtyButtons() {

		$( '#order_review.opc_order_review div.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type="button" value="+" class="plus" />' ).prepend( '<input type="button" value="-" class="minus" />' );
	}

	function showHidePlaceOrder() {

		if ( $( '#order_review tbody' ).children().length>0 ) {
			$( '#place_order' ).show();
			$( '#payment .payment_methods').show();
		} else {
			$( '#place_order' ).hide();
			$( '#payment .payment_methods').hide();
		}
	}

	function initSingleProductTemplateButtons() {

		$( '#opc-product-selection button.single_add_to_cart_button' ).each( function() {

			$(this).after( wcopc.wcopc_complete_order_prompt );
			$(this).attr( 'data-add_to_cart', $(this).closest( '.cart' ).find( '[name="add-to-cart"]' ).val() );
			$(this).data( 'add_to_cart', $(this).closest( '.cart' ).find( '[name="add-to-cart"]' ).val() );
		} );

	}

	function getUrlsParams( queryString ){

		var match,
			pl     = /\+/g,  // Regex for replacing addition symbol with a space
			search = /([^&=]+)=?([^&]*)/g,
			decode = function (s) { return decodeURIComponent(s.replace(pl, ' ')); };

		urlParams = {};

		while ( match = search.exec( queryString ) ) {
			urlParams[ decode( match[1] ) ] = decode( match[2] );
		}

		return urlParams;
	}

	function create_account_toggle() {

		if ( $('.opc_order_review').length ) {

			var $p_create_account = $( 'p.create-account' );
			var $dif_create_account = $( 'div.create-account' );
			var create_account_p_original_visibility = $p_create_account.is( ":visible" );
			var create_account_div_original_visibility = $dif_create_account.is( ":visible" );

			if ( $( '.opc_order_review tfoot .recurring-totals' ).children().length>0 ) {

				// Hide create account option checkbox container if it was visible
				if ( create_account_p_original_visibility ) {
					$p_create_account.hide();
				}

				// And to be safe ensure the checkbox is checked
				$( '#createaccount' ).prop( 'checked', true );

				// If the fields weren't visible to begin with lets show them
				if ( ! create_account_div_original_visibility ) {
					$dif_create_account.show(); //show div
				}
			} else {

				// If visible originally - lets make sure its visible once again
				if ( 'no' === wc_checkout_params.wcopc_option_guest_checkout ) {
					$p_create_account.hide();
					$dif_create_account.show();
					$( '#createaccount' ).prop( 'checked', true );
				} else {
					$p_create_account.show();
					$dif_create_account.hide();
					$( '#createaccount' ).prop( 'checked', false );
				}
			}
		}
	}

} );

/*! jQuery visible 1.1.0 teamdf.com/jquery-plugins | teamdf.com/jquery-plugins/license */
(function(d){d.fn.visible=function(e,i){var a=d(this).eq(0),f=a.get(0),c=d(window),g=c.scrollTop();c=g+c.height();var b=a.offset().top,h=b+a.height();a=e===true?h:b;b=e===true?b:h;return!!(i===true?f.offsetWidth*f.offsetHeight:true)&&b<=c&&a>=g}})(jQuery);
