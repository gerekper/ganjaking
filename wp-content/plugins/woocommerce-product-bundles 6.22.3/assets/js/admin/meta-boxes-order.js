/* global wc_bundles_admin_order_params */
/* global woocommerce_admin_meta_boxes */
jQuery( function( $ ) {

	var $order_items = $( '#woocommerce-order-items' ),
		view         = false,
		functions    = {

			handle_events: function() {

				$order_items

					.on( 'click', 'button.configure_bundle', { action: 'configure' }, this.clicked_edit_button )
					.on( 'click', 'button.edit_bundle', { action: 'edit' }, this.clicked_edit_button );

				$( document.body )

					.on( 'click', 'input.bundled_product_checkbox', this.toggle_optional_item );
			},

			toggle_optional_item: function( event ) {

				var $input     = $( this ),
					is_checked = $input.is( ':checked' ),
					$content   = $input.closest( '.details' ).find( '.bundled_item_cart_content' );

				if ( is_checked ) {
					$content.show();
				} else {
					$content.hide();
				}
			},

			clicked_edit_button: function( event ) {

				var WCPBBackboneModal = $.WCBackboneModal.View.extend( {
					addButton: functions.clicked_done_button
				} );

				var $item   = $( this ).closest( 'tr.item' ),
					item_id = $item.attr( 'data-order_item_id' );

				view = new WCPBBackboneModal( {
					target: 'wc-modal-edit-bundle',
					string: {
						action: 'configure' === event.data.action ? wc_bundles_admin_order_params.i18n_configure : wc_bundles_admin_order_params.i18n_edit,
						item_id: item_id
					}
				} );

				functions.populate_form();

				return false;
			},

			clicked_done_button: function( event ) {

				functions.block( view.$el.find( '.wc-backbone-modal-content' ) );

				var data = $.extend( {}, functions.get_taxable_address(), {
					action:    'woocommerce_edit_bundle_in_order',
					item_id:   view._string.item_id,
					fields:    view.$el.find( 'input, select, textarea' ).serialize(),
					dataType:  'json',
					order_id:  woocommerce_admin_meta_boxes.post_id,
					security:  wc_bundles_admin_order_params.edit_bundle_nonce
				} );

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

					if ( response.result && 'success' === response.result ) {

						$order_items.find( '.inside' ).empty();
						$order_items.find( '.inside' ).append( response.html );

						$order_items.trigger( 'wc_order_items_reloaded' );

						// Update notes.
						if ( response.notes_html ) {
							$( 'ul.order_notes' ).empty();
							$( 'ul.order_notes' ).append( $( response.notes_html ).find( 'li' ) );
						}

						functions.unblock( view.$el.find( '.wc-backbone-modal-content' ) );

						// Make it look like something changed.
						functions.block( $order_items, { fadeIn: 0 } );
						setTimeout( function() {
							functions.unblock( $order_items );
						}, 250 );

						view.closeButton( event );

					} else {
						window.alert( response.error ? response.error : wc_bundles_admin_order_params.i18n_validation_error );
						functions.unblock( view.$el.find( '.wc-backbone-modal-content' ) );
					}

				} );
			},

			populate_form: function() {

				functions.block( view.$el.find( '.wc-backbone-modal-content' ) );

				var data = {
					action:    'woocommerce_configure_bundle_order_item',
					item_id:   view._string.item_id,
					dataType:  'json',
					order_id:  woocommerce_admin_meta_boxes.post_id,
					security:  wc_bundles_admin_order_params.edit_bundle_nonce
				};

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

					if ( response.result && 'success' === response.result ) {
						view.$el.find( 'form' ).html( response.html );
						functions.unblock( view.$el.find( '.wc-backbone-modal-content' ) );
					} else {
						window.alert( wc_bundles_admin_order_params.i18n_form_error );
						functions.unblock( view.$el.find( '.wc-backbone-modal-content' ) );
						view.$el.find( '.modal-close' ).trigger( 'click' );
					}

				} );
			},

			get_taxable_address: function() {

				var country          = '';
				var state            = '';
				var postcode         = '';
				var city             = '';

				if ( 'shipping' === woocommerce_admin_meta_boxes.tax_based_on ) {
					country  = $( '#_shipping_country' ).val();
					state    = $( '#_shipping_state' ).val();
					postcode = $( '#_shipping_postcode' ).val();
					city     = $( '#_shipping_city' ).val();
				}

				if ( 'billing' === woocommerce_admin_meta_boxes.tax_based_on || ! country ) {
					country  = $( '#_billing_country' ).val();
					state    = $( '#_billing_state' ).val();
					postcode = $( '#_billing_postcode' ).val();
					city     = $( '#_billing_city' ).val();
				}

				return {
					country:  country,
					state:    state,
					postcode: postcode,
					city:     city
				};
			},

			block: function( $target, params ) {

				var defaults = {
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity:    0.6
						}
					};

				var opts = $.extend( {}, defaults, params || {} );

				$target.block( opts );
			},

			unblock: function( $target ) {
				$target.unblock();
			}

		};

	/*
	 * Initialize.
	 */
	functions.handle_events();

} );
