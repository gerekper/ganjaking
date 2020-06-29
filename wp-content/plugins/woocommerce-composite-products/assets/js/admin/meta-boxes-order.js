/* global wc_composite_admin_order_params */
/* global woocommerce_admin_meta_boxes */
jQuery( function($) {

	var $order_items = $( '#woocommerce-order-items' ),
		view         = false,
		functions    = {

			handle_events: function() {

				$order_items

					.on( 'click', 'button.configure_composite', { action: 'configure' }, this.clicked_edit_button )
					.on( 'click', 'button.edit_composite', { action: 'edit' }, this.clicked_edit_button );
			},

			clicked_edit_button: function( event ) {

				var WCCPBackboneModal = $.WCBackboneModal.View.extend( {
					addButton: functions.clicked_done_button
				} );

				var $item   = $( this ).closest( 'tr.item' ),
					item_id = $item.attr( 'data-order_item_id' );

				view = new WCCPBackboneModal( {
					target: 'wc-modal-edit-composite',
					string: {
						action: 'configure' === event.data.action ? wc_composite_admin_order_params.i18n_configure : wc_composite_admin_order_params.i18n_edit,
						item_id: item_id
					}
				} );

				functions.populate_form();

				return false;
			},

			clicked_done_button: function( event ) {

				functions.block( view.$el.find( '.wc-backbone-modal-content' ) );

				var data = $.extend( {}, functions.get_taxable_address(), {
					action:    'woocommerce_edit_composite_in_order',
					item_id:   view._string.item_id,
					fields:    view.$el.find( 'input, select, textarea' ).serialize(),
					dataType:  'json',
					order_id:  woocommerce_admin_meta_boxes.post_id,
					security:  wc_composite_admin_order_params.edit_composite_nonce
				} );

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

					if ( response.result && 'success' === response.result ) {

						$order_items.find( '.inside' ).empty();
						$order_items.find( '.inside' ).append( response.html );

						if ( 'yes' === wc_composite_admin_order_params.is_wc_version_gte_3_4 ) {
							$order_items.trigger( 'wc_order_items_reloaded' );
						} else {
							functions.core.init_tiptip();
							functions.core.stupidtable.init();
						}

						if ( 'yes' === wc_composite_admin_order_params.is_wc_version_gte_3_6 ) {

							// Update notes.
							if ( response.notes_html ) {
								$( 'ul.order_notes' ).empty();
								$( 'ul.order_notes' ).append( $( response.notes_html ).find( 'li' ) );
							}
						}

						functions.unblock( view.$el.find( '.wc-backbone-modal-content' ) );

						// Make it look like something changed.
						functions.block( $order_items, { fadeIn: 0 } );
						setTimeout( function() {
							functions.unblock( $order_items );
						}, 250 );

						view.closeButton( event );

					} else {
						window.alert( response.error ? response.error : wc_composite_admin_order_params.i18n_validation_error );
						functions.unblock( view.$el.find( '.wc-backbone-modal-content' ) );
					}

				} );
			},

			populate_form: function() {

				functions.block( view.$el.find( '.wc-backbone-modal-content' ) );

				var data = {
					action:    'woocommerce_configure_composite_order_item',
					item_id:   view._string.item_id,
					dataType:  'json',
					order_id:  woocommerce_admin_meta_boxes.post_id,
					security:  wc_composite_admin_order_params.edit_composite_nonce
				};

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

					if ( response.result && 'success' === response.result ) {

						var $form = view.$el.find( 'form' );

						$form.html( response.html );
						$form.sw_select2();

						view.$el.find( '.composite_component' ).each( function() {
							new Component( $( this ) );
						} );

						functions.unblock( view.$el.find( '.wc-backbone-modal-content' ) );

					} else {
						window.alert( wc_composite_admin_order_params.i18n_form_error );
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
	 * Add some extra duplicated bits if the 'wc_order_items_reloaded' event handler is missing from WC core.
	 */
	if ( 'no' === wc_composite_admin_order_params.is_wc_version_gte_3_4 ) {

		functions.core = {

			init_tiptip: function() {

				$( '#tiptip_holder' ).removeAttr( 'style' );
				$( '#tiptip_arrow' ).removeAttr( 'style' );
				$( '.tips' ).tipTip( {
					'attribute': 'data-tip',
					'fadeIn':    50,
					'fadeOut':   50,
					'delay':     200
				} );
			},

			stupidtable: {

				init: function() {
					$( '.woocommerce_order_items' ).stupidtable();
					$( '.woocommerce_order_items' ).on( 'aftertablesort', this.add_arrows );
				},

				add_arrows: function( event, data ) {
					var th    = $( this ).find( 'th' );
					var arrow = data.direction === 'asc' ? '&uarr;' : '&darr;';
					var index = data.column;
					th.find( '.wc-arrow' ).remove();
					th.eq( index ).append( '<span class="wc-arrow">' + arrow + '</span>' );
				}
			}
		};
	}

	/*
	 * Initialize.
	 */
	functions.handle_events();

	var Component = function( $component ) {

		var component = this;

		this.$selection_el = $component.find( 'select.component_option_select' );
		this.$view_el      = $component.find( '.component_option_selection_details_wrapper' );

		this.component_id  = $component.data( 'component_data' ).component_id;
		this.composite_id  = $component.data( 'component_data' ).composite_id;

		/**
		 * Component selection model.
		 */
		var Component_Selection_Order_Model = Backbone.Model.extend( {

			selected_product_data: {
				product_html: ''
			},

			initialize: function() {
				this.set( {
					selected_product: component.$selection_el.val(),
				} );
			},

			update_selection: function( selected_product ) {

				if ( ! selected_product ) {
					this.update_selected_product( '', { product_html: '' } );
					return;
				}

				this.load_selection_data( selected_product );
			},

			load_selection_data: function( product_id ) {

				var model         = this,
					data          = {
						action:        'woocommerce_get_composited_product_data',
						product_id:    product_id,
						component_id:  component.component_id,
						composite_id:  component.composite_id
					};

				$.ajax( {

					type:     'POST',
					url:      woocommerce_admin_meta_boxes.ajax_url,
					data:     data,
					timeout:  15000,
					dataType: 'json',

					success: function( response ) {

						if ( 'success' === response.result ) {

							var product_data = response.product_data;

							model.trigger( 'selected_product_data_loaded', product_id, product_data );
							model.update_selected_product( product_id, product_data );

						} else {
							model.trigger( 'selected_product_data_load_error', product_id );
						}
					},

					error: function() {
						model.trigger( 'selected_product_data_load_error', product_id );
					}

				} );
			},

			update_selected_product: function( product, product_data ) {
				this.selected_product_data = product_data;
				this.set( { selected_product: product } );
			},

			get_selected_product_data: function() {
				return this.selected_product_data;
			}

		} );

		/**
		 * Component selection view.
		 */
		var	Component_Selection_Order_View = Backbone.View.extend( {

			initialize: function() {

				this.listenTo( this.model, 'change:selected_product', this.render );
				this.listenTo( this.model, 'selected_product_data_load_error', this.selection_data_load_error );

				component.$selection_el.on( 'change', this.selection_changed );
			},

			selection_changed: function() {

				var selected_product = $( this ).val() || '';

				if ( component.selection_model.get( 'selected_product' ) === selected_product ) {
					return false;
				}

				if ( selected_product ) {

					component.selection_view.block();
					component.selection_model.update_selection( selected_product );

				} else {
					component.selection_model.update_selection( '' );
				}
			},

			render: function() {

				component.$view_el.html( this.model.get_selected_product_data().product_html );

				if ( this.model.get( 'selected_product' ) ) {
					this.unblock();
				}
			},

			selection_data_load_error: function() {

				var selected_product = this.model.get( 'selected_product' );

				window.alert( wc_composite_admin_order_params.i18n_selection_request_timeout );

				component.$selection_el.val( selected_product ).change();

				this.unblock();
			},

			block: function () {
				functions.block( component.$view_el );
			},

			unblock: function() {
				functions.unblock( component.$view_el );
			}

		} );

		// Initialize model.
		this.selection_model = new Component_Selection_Order_Model();

		// Initialize view.
		this.selection_view  = new Component_Selection_Order_View( {
			el:    component.$view_el,
			model: component.selection_model,
		} );

	};

} );
