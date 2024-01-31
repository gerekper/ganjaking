/* global srp_redeem_points_params, ajaxurl */

jQuery( function ( $ ) {
	'use strict' ;

	var SRP_Apply_Points = {
		init : function ( ) {
			// Toggle custom point usage
			$( document ).on( 'click' , '.srp-redeem-point-popup-button' , this.redeem_point_popup_button ) ;
			// Redeem Point Value
			$(document).on('click', '.srp-redeem-point-btn', this.redeem_point );
			$(document).on('click', '.save_order', this.validate_reward_gateway_points );
			$(document).on('click', '.save_order', this.validate_point_price_product );
		} , redeem_point_popup_button : function ( e ) {
			e.preventDefault() ;
			let $this = $(e.currentTarget),
				wrapper = $('.wc-order-bulk-actions');
				SRP_Apply_Points.block(wrapper);

			let data = ({
				action: 'srp_display_redeem_point_popup',
				order_id: $($this).val(),
				user_id: $('#customer_user').val(),
				sumo_security: srp_redeem_points_params.redeem_points_nonce,
			});

			$.post(srp_redeem_points_params.ajax_url, data, function (res) {
				if (true === res.success) {
					$('#srp-redeem-point-popup').html(res.data.html);
					$(document.body).trigger('srp-enhanced-lightcase');
					$('.srp-popup-point-lightcase').trigger('click');
				} else {
					alert(res.data.error);
				}
				SRP_Apply_Points.unblock(wrapper);
			}
			);
		}, redeem_point: function (event) {
			event.preventDefault();
			let $this = $(event.currentTarget),
				wrapper = $($this).closest('.srp-redeem-point-content');
			SRP_Apply_Points.block(wrapper);

			let data = ({
				action: 'srp_redeem_point_manually',
				order_id: $('#post_ID').val(),
				user_id: $('#customer_user').val(),
				point_value: wrapper.find('#srp-point-value').val(),
				sumo_security: srp_redeem_points_params.redeem_points_nonce,
			});

			$.post(srp_redeem_points_params.ajax_url, data, function (res) {
				if (true === res.success) {
					alert(res.data.success);
					$('#lightcase-overlay').css("display", "none");
					$('#lightcase-case').css("display", "none");
				} else {
					alert(res.data.error);
				}
				$('#woocommerce-order-items').trigger('wc_order_items_reload');
				SRP_Apply_Points.unblock(wrapper);
			}
			);
		} , validate_reward_gateway_points : function ( e ) {
			let gateway_id = $('#_payment_method').val(),
				user_id = $('#customer_user').val();

			if( 'reward_gateway' != gateway_id ){
				return;
			}

			e.preventDefault() ;
			let wrapper = $('.wc-order-bulk-actions');
			SRP_Apply_Points.block(wrapper);

			let data = ({
				action: 'validate_gateway_redeemed_points',
				order_id: $('#post_ID').val(),
				payment_method: gateway_id,
				user_id: user_id,
				sumo_security: srp_redeem_points_params.redeem_points_nonce,
			});

			$.post(srp_redeem_points_params.ajax_url, data, function (res) {
				if (true === res.success) {
					$('.save_order').closest('form').submit();
				} else {
					alert(res.data.error);
				}
				SRP_Apply_Points.unblock(wrapper);
			});
		}  , validate_point_price_product : function ( e ) {
			let gateway_id = $('#_payment_method').val(),
				user_id = $('#customer_user').val();

			if( 'reward_gateway' == gateway_id ){
				return;
			}

			e.preventDefault() ;
			let wrapper = $('.wc-order-bulk-actions');
			SRP_Apply_Points.block(wrapper);

			let data = ({
				action: 'validate_point_price_product',
				order_id: $('#post_ID').val(),
				payment_method: gateway_id,
				user_id: user_id,
				sumo_security: srp_redeem_points_params.redeem_points_nonce,
			});

			$.post(srp_redeem_points_params.ajax_url, data, function (res) {
				if (true === res.success) {
					$('.save_order').closest('form').submit();
				} else {
					alert(res.data.error);
				}
				SRP_Apply_Points.unblock(wrapper);
			});
		}, block : function ( id ) {
			if ( ! SRP_Apply_Points.is_blocked( id ) ) {
				$( id ).addClass( 'processing' ).block( {
					message : null ,
					overlayCSS : {
						background : '#fff' ,
						opacity : 0.7
					}
				} ) ;
			}
		} , unblock : function ( id ) {
			$( id ).removeClass( 'processing' ).unblock() ;
		} , is_blocked : function ( id ) {
			return $( id ).is( '.processing' ) || $( id ).parents( '.processing' ).length ;
		}
	} ;
	SRP_Apply_Points.init( ) ;
} ) ;
