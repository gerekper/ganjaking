/**
 * Order metaboxes, customize order items in admin.
 *
 * @package WooCommerce Mix and Match Products/Scripts
 *
 * global wc_mnm_admin_order_params
 * global woocommerce_admin_meta_boxes
 */

jQuery(
	function ( $ ) {

		/**
		 * Order Data Panel
		 */
		var wc_mnm_meta_boxes_order = {

			$order_items : $( '#woocommerce-order-items' ),
			view         : false,

			handle_events: function () {

				wc_mnm_meta_boxes_order.$order_items
				.on( 'click', 'button.configure_container', { action: 'configure' }, this.clicked_edit_button )
				.on( 'click', 'button.edit_container', { action: 'edit' }, this.clicked_edit_button );

				// Hook into Mix and Match to change button.
				$( 'body' ).on(
					'wc-mnm-initializing',
					function ( event, container ) {
							container.$mnm_button = $( '#btn-ok' );

							// Signal a script source for use later on the backend.
							container.$mnm_form.data( 'source', 'metabox' );

							// Move the reset button link.
							container.$mnm_price.after( container.$mnm_reset );
					}
				);

			},

			clicked_edit_button: function ( event ) {

				event.preventDefault();

				wc_mnm_meta_boxes_order.block( wc_mnm_meta_boxes_order.$order_items );

				var $button      = $( event.target );
				var item_id      = $button.data( 'order_item_id' );
				var container_id = $button.data( 'container_id' );

				$.ajax(
					{
						url: wc_mnm_admin_order_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'mnm_get_edit_container_order_item_form' ),
						type: 'POST',
						data: {
							item_id     : item_id,
							dataType    : 'json',
							order_id    : woocommerce_admin_meta_boxes.post_id,
							container_id: container_id,
							security    : wc_mnm_admin_order_params.edit_container_nonce,
							source      : 'metabox'
						},
						success: function ( response ) {

							if ( response.success && response.data ) {

								var WCMNMBackboneModal = $.WCBackboneModal.View.extend(
									{
										addButton: wc_mnm_meta_boxes_order.clicked_done_button
									}
								);

									wc_mnm_meta_boxes_order.view = new WCMNMBackboneModal(
										{
											target: 'wc-modal-edit-container',
											string: {
												action: 'configure' === event.data.action ? wc_mnm_admin_order_params.i18n_configure : wc_mnm_admin_order_params.i18n_edit,
												item_id: item_id
											}
										}
									);

									// Load the Form in the modal. We get fragments returned, but in admin we only need the form.
								if ( 'undefined' !== typeof response.data[ 'div.wc-mnm-edit-container' ] ) {
									wc_mnm_meta_boxes_order.view.$el.find( 'article' ).html( response.data[ 'div.wc-mnm-edit-container' ] );

									// Initialize validation scripts.
									wc_mnm_meta_boxes_order.view.$el.find( 'form' ).each(
										function () {
											var type = $( this ).data( 'product_type' ) || 'mix-and-match';

											// Launch the Mix and Match validation scrtips. Share the current script source with mini-extensions.
											$( this ).trigger( 'wc-mnm-initialize.' + type ).data( 'source', 'metabox' );

										}
									);

								}

							} else {
								window.alert( response.data );
							}

						},
						fail: function () {
							window.alert( wc_mnm_admin_order_params.i18n_form_error );
						},
						complete: function () {
							wc_mnm_meta_boxes_order.unblock( wc_mnm_meta_boxes_order.$order_items );
						}
					}
				);

			},

			clicked_done_button: function ( event ) {

				wc_mnm_meta_boxes_order.block( wc_mnm_meta_boxes_order.view.$el.find( '.wc-backbone-modal-content' ) );

				var Form       = wc_mnm_meta_boxes_order.view.$el.find( '.mnm_form' ).wc_get_mnm_script();
				var extra_data = $( document ).triggerHandler( 'wc_mnm_update_container_order_item_data', [ Form ] ) || {};

				var data = $.extend(
					wc_mnm_meta_boxes_order.get_taxable_address(),
					{
						item_id : wc_mnm_meta_boxes_order.view._string.item_id,
						dataType: 'json',
						order_id: woocommerce_admin_meta_boxes.post_id,
						security: wc_mnm_admin_order_params.edit_container_nonce,
						config  : 'undefined' !== typeof Form ? Form.api.get_container_config() : [],
						source  : 'metabox'
					},
					extra_data
				);

				$.ajax(
					{
						url: wc_mnm_admin_order_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'mnm_update_container_order_item' ),
						type: 'POST',
						data: data,
						success: function ( response ) {

							if ( response.success ) {

								if ( 'undefined' !== typeof response.data.html ) {
									wc_mnm_meta_boxes_order.$order_items.find( '.inside' ).empty();
									wc_mnm_meta_boxes_order.$order_items.find( '.inside' ).append( response.data.html );
									wc_mnm_meta_boxes_order.$order_items.trigger( 'wc_order_items_reloaded' );
								}

								// Update notes.
								if ( 'undefined' !== typeof response.data.notes_html ) {
									$( 'ul.order_notes' ).empty();
									$( 'ul.order_notes' ).append( $( response.data.notes_html ).find( 'li' ) );
								}

								wc_mnm_meta_boxes_order.unblock( wc_mnm_meta_boxes_order.view.$el.find( '.wc-backbone-modal-content' ) );

								// Make it look like something changed.
								wc_mnm_meta_boxes_order.block( wc_mnm_meta_boxes_order.$order_items, { fadeIn: 0 } );
								setTimeout(
									function () {
										wc_mnm_meta_boxes_order.unblock( wc_mnm_meta_boxes_order.$order_items );
									},
									250
								);

									wc_mnm_meta_boxes_order.view.closeButton( event );
							} else {
								window.alert( response.data );
								wc_mnm_meta_boxes_order.unblock( wc_mnm_meta_boxes_order.view.$el.find( '.wc-backbone-modal-content' ) );
							}

						},
						fail: function () {
							window.alert( wc_mnm_admin_order_params.i18n_form_error );
						}
					}
				);

			},

			get_taxable_address: function () {

				var country  = '';
				var state    = '';
				var postcode = '';
				var city     = '';

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

			block: function ( $target, params ) {

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

			unblock: function ( $target ) {
				$target.unblock();
			}

		};

		/*
			* Initialize.
			*/
		wc_mnm_meta_boxes_order.handle_events();

	}
);
