/*
* Copyright: (C) 2013 - 2021 José Conti
*/
jQuery( function ( $ ) {
	var redsys_buttom_charge_depo = {
			init: function() {
				this.stupidtable.init();

				$( '#woocommerce-order-items' )
					.on( 'click', 'button.redsys-charge-full-deposit', this.confirm_redsys_charge_depo );

				$( document.body )
					.on( 'wc_backbone_modal_loaded', this.backbone.init )
					.on( 'wc_backbone_modal_response', this.backbone.response );
			},

			block: function() {
				$( '#woocommerce-order-items' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
			},

			unblock: function() {
				$( '#woocommerce-order-items' ).unblock();
			},

			confirm_redsys_charge_depo: function() {
				var data = {
					action: 'redsys_charge_depo_action',
					order_id: redsys_charge_depo.postid,
				};

				redsys_buttom_charge_depo.block();

				$.ajax({
					url:  woocommerce_admin_meta_boxes.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						alert( response );
						redsys_buttom_charge_depo.unblock();
						redsys_buttom_charge_depo.stupidtable.init();
						window.location.reload();
					}
				});
			},

			backbone: {
				init: function( e, target ) {
					if ( 'wc-modal-add-products' === target ) {
						$( document.body ).trigger( 'wc-enhanced-select-init' );
					}
				}
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
	redsys_buttom_charge_depo.init();
});