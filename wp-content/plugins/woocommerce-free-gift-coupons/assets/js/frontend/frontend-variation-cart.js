jQuery( function( $ ) {

	/**
	 * Check if a node is blocked for processing.
	 *
	 * @param {JQuery Object} $node
	 * @return {bool} True if the DOM Element is UI Blocked, false if not.
	 */
	let is_blocked = function( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};

	/**
	 * Block a node visually for processing.
	 *
	 * @param {JQuery Object} $node
	 */
	let block = function( $node ) {
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
	let unblock = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
	};

	/**
	 * Helps serialise form data in object. 
	 *
	 * @param {JQuery Object} $form
	 * @return object JSON/object
	 */
	let getFormData = function ( $form ) {
		let form = new FormData( $form[0] );

		// Remove add-to-cart property as it causes issues, re-adds the product to cart afresh.
		form.delete( 'add-to-cart' );

		return form;
	};

	/**
	 * Hides options for preselected attributes.
	 *
	 * @param {array} data
	 */
	let hidePreSelectedAttributes = function ( data ) {
		if ( undefined === data || ! Array.isArray( data ) ) {
			return;
		}

		for ( let attribute in data ) {
			$( 'select[name="' + data[attribute] + '"]' ).closest( 'tr' ).hide();
		}

		// Hide reset link.
		$( '.variations_form.cart .reset_variations' ).hide();
	};

	/**
	 * Is it a media query condition?
	 *
	 * @param {string} mediaCondition
	 * @return {bool} 
	 */
	let isMediaQuery = function ( mediaCondition ) {
		return window.matchMedia( mediaCondition ).matches;
	};

	/**
	 * Insert the new row where variations can be selected
	 *
	 * To help align the product display properly.
	 *
	 * @param {node}   $current_item_row The node to add the response data.
	 * @param {string} cart_item_key
	 * @param {object} response 
	 */
	let renderVariationSelectionPanel = function ( $current_item_row, cart_item_key, response ) {

		// Get the total of the columns of the row and col before product name.
		let total                 = 0;
		let colsBeforeProductName = 0;
		let productNameWidth      = 0;

		// Loop through the child element.
		$current_item_row.children().each( function( i, item ) {
			// Try getting how many columns before product name.
			if ( $( item ).hasClass( 'product-name' ) ) {
				colsBeforeProductName = i;

				// Get col width also.
				productNameWidth = $( item ).width();
			}
			// Increase total.
			++total;
		});

		// Make sure our response.data is on the product name column.
		let dataColSpan = total - colsBeforeProductName;

		let template = wp.template( 'wc-fgc-edit' );

		$current_item_row.after( 
			template( {
				cart_item_key: cart_item_key,
				colsBeforeProductName: colsBeforeProductName,
				colSpan: dataColSpan,
				content: response.data
			} )
		);

		// Set width of label td.
		$( '#wc-fgc-new-row_' + cart_item_key + ' td.label' ).css( 'width', productNameWidth );

	};

	/**
	 * Auto-open gift selection panel.
	 * 
	 * If an variation item is not selected, and its the
	 * only one not selected, it auto opens it for the user.
	 */
	let autoOpenVariationSelectionDisplay = function() {
		$( '.woocommerce-cart-form .wc-fgc-auto-open-edit' ).first().trigger( 'click' );
	};

	/**
	 * Update cart thumbnail.
	 *
	 * @param {node} $current_item_row The jQuery node for the current cart row.
	 * @param {obj} The found variation.
	 */
	let updateCartThumbnail = function( $current_item_row, variation ) {

		if ( variation && variation.image && variation.image.thumb_src && variation.image.thumb_src.length > 1 ) {

			let $product_img = $current_item_row.find( '.product-thumbnail img' );

			if ( $product_img.length ) {

				$product_img.wc_set_variation_attr( 'src', variation.image.thumb_src );
				$product_img.wc_set_variation_attr( 'height', variation.image.thumb_src_h );
				$product_img.wc_set_variation_attr( 'width', variation.image.thumb_src_w );
				$product_img.wc_set_variation_attr( 'srcset', variation.image.srcset );
				$product_img.wc_set_variation_attr( 'sizes', variation.image.sizes );
				$product_img.wc_set_variation_attr( 'title', variation.image.title );
				$product_img.wc_set_variation_attr( 'data-caption', variation.image.caption );
				$product_img.wc_set_variation_attr( 'alt', variation.image.alt );

			}

		} else {

			resetCartThumbnail( $current_item_row );

		}
	};

	/**
	 * Reset cart thumbnail.
	 *
	 * @param {node} $current_item_row The jQuery node for the current cart row.
	 */
	let resetCartThumbnail = function( $current_item_row ) {
		let $product_img = $current_item_row.find( '.product-thumbnail img' );

		if ( $product_img.length ) {

			$product_img.wc_reset_variation_attr( 'src' );
			$product_img.wc_reset_variation_attr( 'width' );
			$product_img.wc_reset_variation_attr( 'height' );
			$product_img.wc_reset_variation_attr( 'srcset' );
			$product_img.wc_reset_variation_attr( 'sizes' );
			$product_img.wc_reset_variation_attr( 'title' );
			$product_img.wc_reset_variation_attr( 'data-caption' );
			$product_img.wc_reset_variation_attr( 'alt' );

		}
	};

	/**
	 * Reset variations.
	 */
	 $( document ).on( 'reset_data', function( event ) {

		let $form             = $(event.target);
		let $current_item_row = $form.data( 'fgc_current_item_row' );
		
		if ( 'undefined' !== typeof $current_item_row && $current_item_row.length ) {
			$current_item_row.find( '.wc-fgc-stock-error' ).html( '' );
			$current_item_row.find( '.wc-fgc-stock-error' ).hide();
			resetCartThumbnail( $current_item_row );
		}

	 } );

	/**
	 * Trigger auto opening when cart is updated.
	 */
	$( document.body ).on( 'updated_wc_div', function() {
		autoOpenVariationSelectionDisplay();
	} );

	/**
	 * Show the variation editor.
	 */
	$( document ).on( 'click', '.wc-fgc-edit-in-cart', function( e ) {
		e.preventDefault();

		let $button       = $( this );
		let cart_item_key = $( this ).data( 'cart_item_key' );
		let $editRow      = $( 'tr#wc-fgc-new-row_' + cart_item_key );

		// If not a button found, then it's from the notice link.
		if ( ! $button.is( ':button' ) ) {
			$button = $( '.woocommerce-cart-form' ).find( '.wc-fgc-edit-in-cart[data-cart_item_key="' + cart_item_key + '"]' );
		}

		let $current_item_row = $button.closest( 'tr.wc-fgc-cart-item' );

		if ( 'undefined' === typeof $current_item_row || ! $current_item_row.length ) {
			$current_item_row = $editRow.prevAll( 'tr.wc-fgc-cart-item:first' ); 
		}

		$current_item_row.addClass( 'wc-fgc-has-open-panel' );

		if ( $button.is( ':button' ) ) {
			$button.fadeOut();
		}

		// Check if window is already opened.
		if ( $editRow.length ) {
	
			$editRow.fadeIn();

			// Scroll to the section, cool UX 8-).
			let $variationsForm = $( '#wc_fgc_' + cart_item_key + ' .variations_form' );

			// If it's mobile, scroll to the summary section, else scroll to the cart-item.
			let $scrollTo = isMediaQuery( '(max-width:767.9px)' ) ? $variationsForm : $current_item_row;

			$( 'body,html' ).animate( {
				scrollTop: ( $scrollTo.offset().top - 50 )
			}, 1000 );

			// Autofocus first input you find.
			$variationsForm.find( '.variations' ).find( ':input:enabled:visible:first' ).trigger( 'focus' );

			return;
		}

		block( $( '.woocommerce-cart-form' ) );

		let productID   = $( this ).data( 'product_id' );
		let variationID = $( this ).data( 'variation_id' );

		$.ajax( {
			url: wc_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'fgc_get_product' ),
			method: 'GET',
			cache: false,
			headers : { 'cache-control': 'no-cache' },
			data: {
				'product_id'    : productID,
				'variation_id'  : variationID,
				'cart_item_key' : cart_item_key,
			}
		} )
		.done( function( response ) {

			if ( response.success ) {

				if ( ! $( '#wc_fgc_' + cart_item_key ).length ) {
					renderVariationSelectionPanel( $current_item_row, cart_item_key, response );
				}

				// The inserted content.
				let $cart_editor = $( '#wc_fgc_' + cart_item_key );

				// Initialize variable product scripts.
				let $form = $cart_editor.find( '.variations_form' );

				if ( $form ) {
					$form.data( 'fgc_current_item_row', $current_item_row ).wc_variation_form();
				}

				// Dynamically update the aria label on the update button to give more context to screen readers.
				let productTitle  = $cart_editor.data( 'product_title' );
				let addToCartText = $cart_editor.data( 'single_add_to_cart_text' );
				let ariaLabel     = wc_fgc_var_cart_params.i18_update_button_label.replace( '%button_text%', addToCartText ).replace( '%product_title%', productTitle );

				// Stash the cart item key on the add to cart button for later retrieval.
				$form.find( '.single_add_to_cart_button' ).data( 'cart_item_key', cart_item_key ).attr( 'aria-label', ariaLabel );

				// Stash the current variation on the cancel link for later retrieval.
				$form.find( '.wc-fgc-close-link' ).data( 'variation_id', variationID ).toggle( ! $button.hasClass( 'wc-fgc-auto-open-edit' ) );

				// Hide variation option for preselected data.
				hidePreSelectedAttributes( $button.data( 'pre_selected_attributes' ) );

				// Scroll to the section, cool UX 8-).
				let $variationsForm = $( '#wc_fgc_' + cart_item_key + ' .variations_form' );

				// If it's mobile, scroll to the summary section, else scroll to the cart-item.
				let $scrollTo = isMediaQuery( '(max-width:767.9px)' ) ? $variationsForm : $current_item_row;

				$( 'body,html' ).animate( {
					scrollTop: ( $scrollTo.offset().top - 50 )
				}, 1000 );

				// Custom trigger when loaded.
				$( 'body' ).trigger( 'wc-fgc-cart-edit-initialized', [ cart_item_key ] );

			} else {
				// Incase no data is returned, show our default error.
				let msg = ( undefined === response.data ? wc_fgc_var_cart_params.i18n_server_error : response.data );
				window.alert( msg );
			}

		} )
		.fail( function() {
			window.alert( wc_fgc_var_cart_params.i18n_server_error );
			$button.fadeIn();
		} )
		.always( function() {
			unblock( $( '.woocommerce-cart-form' ) );
		} );

	} );


	/**
	 * Listen to  variation change to update the thumbnail for current variation.
	 */
	 $( document ).on( 'found_variation', function( event, variation ) {

		let $form             = $(event.target);
		let $current_item_row = $form.data( 'fgc_current_item_row' );
		
		if ( 'undefined' !== typeof $current_item_row && $current_item_row.length ) {
			updateCartThumbnail( $current_item_row, variation );
		}
		
	 } );

	/**
	 * Reset variations.
	 */
	 $( document ).on( 'reset_data', function( e ) {
		let $form             = $(e.target);
		let $current_item_row = $form.data( 'fgc_current_item_row' );
		
		if ( 'undefined' !== typeof $current_item_row && $current_item_row.length ) {
			$current_item_row.find( '.wc-fgc-stock-error' ).html( '' );
			$current_item_row.find( '.wc-fgc-stock-error' ).hide();
			resetCartThumbnail( $current_item_row );
		}
	 } );

	/**
	 * Update variation in cart.
	 */
	 $( document ).on( 'click', '.wc-fgc-new-row .single_add_to_cart_button', function( e ) {

		e.preventDefault();
		 
		// Don't do anything if still disabled.
		if ( $( this ).is( '.disabled' ) ) {
			return;
		}

		block( $( '.wc-fgc-new-row' ) );

		let $id   = $( this ).closest( '.wc_fgc_cart' ).attr( 'id' );
		let $form = $( '#' + $id + ' form' );

		// Gather data.
		let cart_item_key = $( this ).data( 'cart_item_key' );
		let variation     = {};

		// Set data.
		let payload = getFormData( $form );
		payload.append( 'cart_item_key', cart_item_key );

		// Get variation data.
		$( '#' + $id ).find( 'select[name^=attribute]' ).each( function() {
			let attrName        = $( this ).attr( 'name' );
			let attrValue       = $( this ).val();
			variation[attrName] = attrValue;

			// Load to form data.
			payload.append( 'variation[' + attrName + ']', attrValue );
		} );

		$.ajax( {
			url: wc_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'fgc_update_variation_in_cart' ),
			type: 'POST',
			processData: false,
			contentType: false,
			cache: false,
			headers : { 'cache-control': 'no-cache' },
			data: payload,
		} )
		.done( function( response ) {

			if ( response.success ) {
				// Update WooCommerce Cart.
				let $wcCart = $( '.woocommerce-cart-form [name="update_cart"]' );
				$wcCart.prop( 'disabled', false ).trigger( 'click' );
			} else {
				$( '.wc-fgc-stock-error' ).html( response.data );
				$( '.wc-fgc-stock-error' ).show();
				$( 'form.variations_form' ).find( 'div .woocommerce-variation-add-to-cart .input-text' ).show();

				// Scroll to error.
				$.scroll_to_notices( $( '.wc-fgc-stock-error' ) );
			}

		} )
		.fail( function() {
			window.alert( wc_fgc_var_cart_params.i18n_variation_update_error );
		} )
		.always( function() {
			unblock( $( '.wc-fgc-new-row' ) );
		} );

	 } );

	$( document ).on( 'click', '.wc-fgc-new-row .wc-fgc-close-link', function( e ) {
		e.preventDefault();

		// Display warning if still disabled.
		let variationID = $(this).data( 'variation_id' );

		if ( ! variationID && $( this ).closest( '.wc_fgc_cart' ).find( '.single_add_to_cart_button' ).is( '.disabled' ) ) {
			window.alert( wc_add_to_cart_variation_params.i18n_make_a_selection_text );
		}

		let $cartContainer = $( this ).closest( '.wc-fgc-new-row' );
		let cartItemIdAttr = $cartContainer.attr( 'id' );
		let cartItemId     = cartItemIdAttr.split( '_' )[1];
		
		$cartContainer.fadeOut( function() {
			let $updateBtn = $( '.wc-fgc-edit-in-cart[data-cart_item_key=' + cartItemId + ']' );
			$updateBtn.show();
		});
		

	} );

	// Trigger auto-open on page load.
	autoOpenVariationSelectionDisplay();

} );
