jQuery( document ).ready( function( $ ){

	/**
	 * Check if a node is blocked for processing.
	 *
	 * @param {JQuery Object} $node
	 * @return {bool} True if the DOM Element is UI Blocked, false if not.
	 */
	var is_blocked = function( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};

	/**
	 * Block a node visually for processing.
	 *
	 * @param {JQuery Object} $node
	 */
	var block = function( $node ) {
		if ( ! is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		}
	};

	/**
	 * Unblock a node after processing is complete.
	 *
	 * @param {JQuery Object} $node
	 */
	var unblock = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
	};

	/**
	 * Helps in seeking an automation opening.
	 * 
	 * If an variation item is not selected, and its the
	 * only one not selected, it helps auto open it for the user.
	 * 
	 * @param {object} node
	 * @return {bool} used this to be able to manipulate functions calling ir
	 */
	$.fn.wc_fgc_find_auto_variation_open = function( node ) {
		let $editRow       = $( '.wc_fgc_cart' ).closest( 'tr.wc-fgc-new-row' );
		let $editBtnParent = $( '.wc-fgc-show-edit' );

		if ( $( node ).find( $editBtnParent ).length > 0 ) {

			// If variation to edit is only 1, and the edit row is not yet opened.
			if ( $editBtnParent.length == 1  && $editRow.length == 0 ) {

				// Get particular id so we do not trigger multiple.
				let btnParentIdAttr = $editBtnParent.attr( 'id' );
				$( '#' + btnParentIdAttr + ' .wc_fgc_updatenow' ).trigger( 'click' );
				// observer.disconnect();
			}
			return true;
		}
		return false;
	};

	/**
	 * Observer
	 * 
	 * Trigger wc_fgc_updatenow click.
	 * Only when variation hasn't been selected
	 */
	$.fn.wc_fgc_observer = new MutationObserver( function( mutations ) {
		// loop through and only check for childList type.
		for ( let mutation of mutations ) {

			// Not childList, abeg we have no business here.
			if ( 'childList' !== mutation.type ) {
				continue;
			}

			for ( let node of mutation.addedNodes ) {
				// Did you find any element worth opening?
				if ( $( this ).wc_fgc_find_auto_variation_open( node ) == true ) {
					break;
				}
			}
		}
	} );

	let observerOptions = { attributes: false, childList: true, characterData: false, subtree: true };
	let observeTarget   = document;
	$( this ).wc_fgc_observer.observe( observeTarget, observerOptions );
	
	/**
	 * Show the variation editor
	 */
	$( document ).on( 'click', '.wc_fgc_updatenow', function( e ) {
		e.preventDefault();

		var cart_item_key = $( this ).data( 'item_key' );

		var $editRow = $( 'tr#wc-fgc-new-row_' + cart_item_key );

		// Check if window is already opened.
		if ( $editRow.length ) {
			$editRow.fadeIn( 'slow' );
			return;
		}

		block( $( '.woocommerce-cart-form' ) );

		var productID   = $( this ).data( 'product_id' );
		var variationID = $( this ).data( 'variation_id' );
		var $cartItem   = $( this ).parent().parent();

		var current_item_product = $( this ).closest( 'tr' );
		
		$.ajax( {
			url: wc_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'fgc_get_product' ),
			method: 'GET',
			cache: false,
			headers : { 'cache-control': 'no-cache' },
			data: {
				'product_id': productID,
				'variation_id' : variationID,
				'cart_item_key' : cart_item_key,
			}
		} )
		.done( function( response ) {

			if ( response.success ) {

				if ( ! $( '#wc_fgc_'+cart_item_key ).length ) {
					current_item_product.after( '<tr class="wc-fgc-new-row" id="wc-fgc-new-row_' + cart_item_key + '"><td colspan="6">' + response.data + '</td></tr>' );
				}

				// Run variation saga.
				var $form = $( '#wc_fgc_'+cart_item_key ).find( '.variations_form' );

				if ( $form ) {
					$form.wc_variation_form();
				}

				var $add_to_cart_button = $form.find( '.single_add_to_cart_button' );

				// Stash the cart item key on the add to cart button for later retrieval.
				$add_to_cart_button.data( 'cart_item_key', cart_item_key );

				// Scroll to the section, cool UX 8-).
				$( 'body,html' ).animate( {
					scrollTop: ( $( '#wc_fgc_' + cart_item_key + ' .summary' ).offset().top - 100 )
				}, 1000 );

				// CUstom trigger when loaded.
				$( 'body' ).trigger( 'wc-fgc-cart-edit-init', [ cart_item_key ] );

			} else {
				alert( response.data );
			}

		} )
		.fail( function( response ) {
			alert( wc_fgc_var_cart_params.server_error );
		} )
		.always( function( response ) {

			unblock( $( '.woocommerce-cart-form' ) );
		} );
		
	} );

	/**
	 * Reset variations.
	 */
	 $( document ).on( 'reset_data',function() {
		$( '.wc-fgc-stock-error' ).html( '' );
		$( '.wc-fgc-stock-error' ).hide();
	 } );

	/**
	 * Update variation in cart.
	 */
	 $( document ).on( 'click', '.single_add_to_cart_button', function( e ) { 

		e.preventDefault();
		 
		// Don't do anything if still disabled, parent file gats our back :).
		if ( $( this ).is( '.disabled' ) ) {
			return;
		}

		block( $( '.wc_fgc_cart' ) );

		var $id           = $( this ).closest( '.wc_fgc_cart' ).attr( 'id' );
		var cart_item_key = $( this ).data( 'cart_item_key' );

		var $form = 'something';

		var product_id = $( '#'+$id ).find( 'input[name="product_id"]' ).val();
		var variation_id = $( '#'+$id ).find( 'input[name="variation_id"]' ).val();
		var variation = {};

		$( '#'+$id ).find( 'select[name^=attribute]' ).each( function() {
			var attrName        = $( this ).attr( 'name' );
			var attrValue       = $( this ).val();
			variation[attrName] = attrValue;
		} );

		$.ajax( {
			url: wc_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'fgc_update_variation_in_cart' ),
			method: 'GET',
			cache: false,
			headers : { 'cache-control': 'no-cache' },
			data: {
				'product_id': product_id,
				'variation_id':variation_id,
				'variation': variation,
				'cart_item_key': cart_item_key
			}
		} )
		.done( function( response ) {

			if ( response.success ) {
				// Update WooCommerce Cart.
				let $wcCart = $( '.woocommerce-cart-form [name="update_cart"]' );
				$wcCart.removeAttr( 'disabled' ).trigger( 'click' );

			} else {
				$( '.wc-fgc-stock-error' ).html( response );
				$( '.wc-fgc-stock-error' ).show();
				$( 'form.variations_form' ).find( 'div .woocommerce-variation-add-to-cart .input-text' ).show();
			}

		} )
		.fail( function( response ) {
			alert( wc_fgc_var_cart_params.variation_update_error );
		} )
		.always( function( response ) {
				unblock( $( '.wc_fgc_cart' ) );
		} );
		

	 } );

	$( document ).on( 'click', '.wc-fgc-close-btn', function( e ) {
		e.preventDefault();
		let $cartContainer = $( this ).closest( '.wc-fgc-new-row' );
		let cartItemIdAttr = $cartContainer.attr( 'id' );
		let cartItemId     = cartItemIdAttr.split( '_' )[1];

		let cartItemBtnId = 'wc-fgc-item_' + cartItemId;

		$cartContainer.fadeOut( 'slow' );
		$( '#' + cartItemBtnId + ' .wc_fgc_updatenow' ).fadeIn( 'slow' );
	} );

	// Trigger auto opening if one variation FGC is found.
	$( this ).wc_fgc_find_auto_variation_open( observeTarget );

} );
