/**
 * Order metaboxes, customize order items in admin.
 *
 * @package WooCommerce Mix and Match Products/Scripts
 *
 * global wc_mnm_admin_order_params
 * global woocommerce_admin_meta_boxes
 */

jQuery(
	function( $ ) {

		var $order_items = $( '#woocommerce-order-items' ),
		view             = false,
		functions        = {

			handle_events: function() {

				$order_items

				 .on( 'click', 'button.configure_container', { action: 'configure' }, this.clicked_edit_button )
				 .on( 'click', 'button.edit_container', { action: 'edit' }, this.clicked_edit_button );

				 // Hook into Mix and Match to change button.
				$( 'body' ).on( 'wc-mnm-initializing', function( event, container ) {
					container.$mnm_button = $( '#btn-ok' );
				} );

			},

			clicked_edit_button: function( event ) {

				event.preventDefault();

				var $item    = $( this ).closest( 'tr.item' );
				var item_id  = $item.attr( 'data-order_item_id' );

				$.ajax( {
					url: woocommerce_admin_meta_boxes.ajax_url,
					type: 'POST',
					data: {
						action:    'woocommerce_configure_container_order_item',
						item_id:   item_id,
						dataType:  'json',
						order_id:  woocommerce_admin_meta_boxes.post_id,
						security:  wc_mnm_admin_order_params.edit_container_nonce
					},
					success: function( response ) {

						if ( response.success ) {

							var WCMNMBackboneModal = $.WCBackboneModal.View.extend(
								{
									addButton: functions.clicked_done_button
								}
							);
			
							view = new WCMNMBackboneModal(
								{
									target: 'wc-modal-edit-container',
									string: {
										action: 'configure' === event.data.action ? wc_mnm_admin_order_params.i18n_configure : wc_mnm_admin_order_params.i18n_edit,
										item_id: item_id
									}
								}
							);

							// Load the Form in the modal.
							view.$el.find( 'article' ).html( response.data );
							
							// Initialize validation scripts.
							view.$el.find( '.mnm_form' ).each( function() {
								$(this).wc_mnm_form();
							} );
				
						} else {
							window.alert( response.data );
						}

					},
					fail: function() {
						window.alert( wc_mnm_admin_order_params.i18n_form_error );
					}
				} );

			},

			clicked_done_button: function( event ) {

				functions.block( view.$el.find( '.wc-backbone-modal-content' ) );

				var data = $.extend(
					view.getFormData(),
					functions.get_taxable_address(),
					{
						action:    'woocommerce_edit_container_in_order',
						item_id:   view._string.item_id,
						dataType:  'json',
						order_id:  woocommerce_admin_meta_boxes.post_id,
						security:  wc_mnm_admin_order_params.edit_container_nonce
					}
				);

				$.ajax( {
					url: woocommerce_admin_meta_boxes.ajax_url,
					type: 'POST',
					data: data,
					success: function( response ) {

						if ( response.success ) {
							$order_items.find( '.inside' ).empty();
							$order_items.find( '.inside' ).append( response.data.html );

							$order_items.trigger( 'wc_order_items_reloaded' );

							// Update notes.
							if ( response.data.notes_html ) {
								$( 'ul.order_notes' ).empty();
								$( 'ul.order_notes' ).append( $( response.data.notes_html ).find( 'li' ) );
							}

							functions.unblock( view.$el.find( '.wc-backbone-modal-content' ) );

							// Make it look like something changed.
							functions.block( $order_items, { fadeIn: 0 } );
							setTimeout(
								function() {
									functions.unblock( $order_items );
								},
								250
							);

							view.closeButton( event );
						} else {
							window.alert( response.data );
							functions.unblock( view.$el.find( '.wc-backbone-modal-content' ) );
						}

					},
					fail: function() {
						window.alert( wc_mnm_admin_order_params.i18n_form_error );
					}
				} );

			},

			get_taxable_address: function() {

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

	}
);
