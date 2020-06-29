
jQuery( function( $ ) {

	var wc_shipment_tracking_items = {

		// init Class
		init: function() {
			$( '#woocommerce-shipment-tracking' )
				.on( 'click', 'a.delete-tracking', this.delete_tracking )
				.on( 'click', 'button.button-show-form', this.show_form )
				.on( 'click', 'button.button-save-form', this.save_form );
		},

		// When a user enters a new tracking item
		save_form: function () {

			if ( !$( 'input#tracking_number' ).val() ) {
				return false;
			}

			$( '#shipment-tracking-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );

			var data = {
				action:                   'wc_shipment_tracking_save_form',
				order_id:                 woocommerce_admin_meta_boxes.post_id,
				tracking_provider:        $( '#tracking_provider' ).val(),
				custom_tracking_provider: $( '#custom_tracking_provider' ).val(),
				custom_tracking_link:     $( 'input#custom_tracking_link' ).val(),
				tracking_number:          $( 'input#tracking_number' ).val(),
				date_shipped:             $( 'input#date_shipped' ).val(),
				security:                 $( '#wc_shipment_tracking_create_nonce' ).val()
			};


			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
				$( '#shipment-tracking-form' ).unblock();
				if ( response != '-1' ) {
					$( '#shipment-tracking-form' ).hide();
					$( '#woocommerce-shipment-tracking #tracking-items' ).append( response );
					$( '#woocommerce-shipment-tracking button.button-show-form' ).show();
					$( '#tracking_provider' ).selectedIndex = 0;
					$( '#custom_tracking_provider' ).val( '' );
					$( 'input#custom_tracking_link' ).val( '' );
					$( 'input#tracking_number' ).val( '' );
					$( 'input#date_shipped' ).val( '' );
					$('p.preview_tracking_link').hide();
				}
			});

			return false;
		},

		// Show the new tracking item form
		show_form: function () {
			$( '#shipment-tracking-form' ).show();
			$( '#woocommerce-shipment-tracking button.button-show-form' ).hide();
		},

		// Delete a tracking item
		delete_tracking: function() {

			var tracking_id = $( this ).attr( 'rel' );

			$( '#tracking-item-' + tracking_id ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			var data = {
				action:      'wc_shipment_tracking_delete_item',
				order_id:    woocommerce_admin_meta_boxes.post_id,
				tracking_id: tracking_id,
				security:    $( '#wc_shipment_tracking_delete_nonce' ).val()
			};

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
				$( '#tracking-item-' + tracking_id ).unblock();
				if ( response != '-1' ) {
					$( '#tracking-item-' + tracking_id ).remove();
				}
			});

			return false;
		},

		refresh_items: function() {
			var data = {
				action:                   'wc_shipment_tracking_get_items',
				order_id:                 woocommerce_admin_meta_boxes.post_id,
				security:                 $( '#wc_shipment_tracking_get_nonce' ).val()
			};

			$( '#woocommerce-shipment-tracking' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
				$( '#woocommerce-shipment-tracking' ).unblock();
				if ( response != '-1' ) {
					$( '#woocommerce-shipment-tracking #tracking-items' ).html( response );
				}
			});
		},
	}

	wc_shipment_tracking_items.init();

	window.wc_shipment_tracking_refresh = wc_shipment_tracking_items.refresh_items;
} );
