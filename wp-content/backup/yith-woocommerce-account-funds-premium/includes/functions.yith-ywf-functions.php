<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ywf_get_min_fund_rechargeable' ) ) {

	/**
	 * returns the minimum rechargeable funds
	 * @return float
	 * @since 1.0.0
	 * @author YITHEMES
	 */
	function ywf_get_min_fund_rechargeable() {

		$min_rech = get_option( 'yith_funds_min_value' );
		$min_rech = empty( $min_rech ) ? 0 : $min_rech;

		return apply_filters( 'yith_min_deposit', $min_rech );
	}
}


if ( ! function_exists( 'ywf_get_max_fund_rechargeable' ) ) {
	/**
	 * returns the maximum  rechargeable funds
	 * @return bool|float
	 * @since 1.0.0
	 * @author YITHEMES
	 */
	function ywf_get_max_fund_rechargeable() {

		$max_rech = get_option( 'yith_funds_max_value' );
		$max_rech = empty( $max_rech ) ? '' : $max_rech;

		return apply_filters( 'yith_max_deposit', $max_rech );
	}
}

if ( ! function_exists( 'ywf_enable_discount' ) ) {
	/**
	 * check if is enable discount
	 * @return bool
	 * @since 1.0.0
	 * @author YITHEMES
	 */
	function ywf_enable_discount() {

		$enable_discount = get_option( 'yith_discount_enable_discount' );

		return apply_filters( 'ywf_enable_discount', $enable_discount == 'yes' );

	}
}

if ( ! function_exists( 'ywf_get_discount_type' ) ) {
	/**
	 * @return string
	 * @since 1.0.0
	 * @author YITHEMES
	 */
	function ywf_get_discount_type() {

		$discount_type = get_option( 'yith_discount_type_discount' );

		return $discount_type;
	}
}


if ( ! function_exists( 'ywf_get_discount_value' ) ) {

	/**
	 * @return string
	 * @since 1.0.0
	 * @author YITHEMES
	 */
	function ywf_get_discount_value() {

		$discount_value = get_option( 'yith_discount_value' );

		return wc_format_decimal( $discount_value );
	}
}

if ( ! function_exists( 'ywf_partial_payment_enabled' ) ) {
	/**
	 * check if partial payment is enabled
	 * @return bool
	 * @since 1.0.0
	 * @author YITHEMES
	 */
	function ywf_partial_payment_enabled() {

		$partial_payment = get_option( 'yith_enable_partial_payment', 'no' );

		return $partial_payment == 'yes';
	}
}

if ( ! function_exists( 'ywf_get_fund_endpoint_name' ) ) {
	/**
	 * @param string $endpoint_id
	 *
	 * @return string
	 * @author YITHEMES
	 * @since 1.0.0
	 */
	function ywf_get_fund_endpoint_name( $endpoint_id ) {

		return yith_account_funds_get_endpoint_title( $endpoint_id );
	}
}

if ( ! function_exists( 'ywf_get_make_a_deposit_slug' ) ) {
	/**
	 * get endpoint slug
	 *
	 *
	 *
	 * @return string
	 * @deprecated form version 1.4.0
	 * @author YITHEMES
	 * @since 1.0.0
	 */
	function ywf_get_make_a_deposit_slug( ) {

		return yith_account_funds_get_endpoint_slug( 'make-a-deposit' );
	}
}

if ( ! function_exists( 'ywf_get_view_history_slug' ) ) {
	/**
	 * get endpoint slug
	 *
	 *
	 *
	 * @return string
	 * @deprecated
	 * @author YITHEMES
	 * @since 1.0.0
	 */
	function ywf_get_view_history_slug() {

		return yith_account_funds_get_endpoint_slug( 'view-history' );
	}
}

if ( ! function_exists( 'ywf_order_has_deposit' ) ) {
	/**
	 * check if order is a deposit
	 *
	 * @param WC_Order $order
	 *
	 * @return bool
	 * @author YITHEMES
	 * @since 1.0.0
	 */
	function ywf_order_has_deposit( $order ) {
		$has_deposit = $order->get_meta( '_order_has_deposit' );

		return $has_deposit == 'yes';


	}
}

if ( ! function_exists( 'ywf_get_endpoint_url' ) ) {

	function ywf_get_endpoint_url( $type, $args = array() ) {

		$endpoint = yith_account_funds_get_endpoint_slug( $type );

		if ( count( $args ) > 0 ) {
			$url = esc_url( add_query_arg( $args, wc_get_page_permalink( 'myaccount' ) . $endpoint ) );
		} else {
			$url = esc_url( wc_get_page_permalink( 'myaccount' ) . $endpoint );
		}

		return apply_filters( 'ywf_get_endpoint_url', $url, $type, $args );
	}
}

if ( ! function_exists( 'ywf_get_customize_my_account_menu' ) ) {

	function ywf_get_customize_my_account_menu() {

		$position = get_option( 'yith-wcmap-menu-position', 'left' );
		$tab      = get_option( 'yith-wcmap-menu-style', 'sidebar' ) == 'tab' ? '-tab' : '';
		$menu     = '<div id="my-account-menu' . $tab . '" class="yith-wcmap position-' . $position . '">' . YITH_WCMAP_Frontend()->my_account_menu() . '</div>';

		return $menu;
	}
}

if ( ! function_exists( 'ywf_is_make_deposit' ) ) {
	function ywf_is_make_deposit() {

		global $is_make_a_deposit_form, $post, $wp_query;

		$shortcode                = '[yith_ywf_make_a_deposit_endpoint]';
		$make_a_deposit_end_point = yith_account_funds_get_endpoint_slug('make-a-deposit');


		if ( isset( $post ) ) {
			$post_content = $post->post_content;
			preg_match( '/\[yith_ywf_make_a_deposit_endpoint[^\]]*\]/', $post_content, $shortcode );
		}

		return ( isset( $wp_query->query_vars[ $make_a_deposit_end_point ] ) ) || ( isset( $is_make_a_deposit_form ) && $is_make_a_deposit_form ) || ( isset( $shortcode[0] ) );
	}
}

if ( ! function_exists( 'ywf_get_gateway' ) ) {

	function ywf_get_gateway() {
		$payment  = WC()->payment_gateways->payment_gateways();
		$gateways = array();
		foreach ( $payment as $gateway ) {
			if ( $gateway->enabled == 'yes' && $gateway->id != 'yith_funds' ) {
				$gateways[ $gateway->id ] = $gateway->title;
			}
		}

		return $gateways;
	}
}

if ( ! function_exists( 'ywf_get_user_currency' ) ) {
	function ywf_get_user_currency( $user_id ) {

		$args = array(
			'numberposts' => 1,
			'meta_query'  => array(
				array(
					'key'     => '_customer_user',
					'value'   => $user_id,
					'compare' => '=',
					'type'    => 'numeric'
				,
				),
				array(
					'key'     => '_order_has_deposit',
					'value'   => 'yes',
					'compare' => 'LIKE'
				),

			),
			'post_type'   => 'shop_order',
			'post_status' => 'wc-completed',
			'fields'      => 'ids'
		);

		$order_id = get_posts( $args );

		return isset( $order_id[0] ) ? $order_id[0] : - 1;
	}
}

if ( ! function_exists( 'ywf_get_date_created_order' ) ) {
	/**
	 * @param WC_Order $order
	 * @param string $context
	 */
	function ywf_get_date_created_order( $order, $context = 'view' ) {

		global $YITH_FUNDS;

		$order_date = '';
		if ( $YITH_FUNDS->is_wc_2_7 ) {
			$order_date = $order->get_date_created( $context );

		} else {

			$order_date = $order->post->post_date;
		}

		return $order_date;
	}
}

if ( ! function_exists( 'ywf_get_operation_type' ) ) {

	function ywf_get_operation_type() {

		$operation_types = array(

			'deposit'  => __( 'Deposit', 'yith-woocommerce-account-funds' ),
			'pay'      => __( 'Payment', 'yith-woocommerce-account-funds' ),
			'admin_op' => __( 'Admin activity', 'yith-woocommerce-account-funds' ),
			'restore'  => __( 'Funds restored', 'yith-woocommerce-account-funds' ),
			'remove'   => __( 'Funds refunded', 'yith-woocommerce-account-funds' ),
		);


		return apply_filters( 'ywf_operation_type', $operation_types );
	}
}

if ( ! function_exists( 'ywf_check_if_can_send_email' ) ) {

	function ywf_check_if_can_send_email( $send_email, $user_id ) {

		$show_checkbox = get_option( 'ywf_user_privacy', 'no' );

		if ( 'yes' == $show_checkbox ) {

			$user_meta = get_user_meta( $user_id, '_ywf_agree_to_send_email', true );

			if ( ! $user_meta ) {
				$send_email = false;
			}
		}

		return $send_email;
	}

	add_filter( 'ywf_send_email', 'ywf_check_if_can_send_email', 10, 2 );
}

if ( ! function_exists( 'ywf_update_db_1_0_1' ) ) {

	function ywf_update_db_1_0_1() {

		$fund_db_option = get_option( 'ywf_dbversion', '1.0.0' );

		if ( $fund_db_option && version_compare( $fund_db_option, '1.0.1', '<' ) ) {

			global $wpdb;

			$sql = "ALTER TABLE {$wpdb->prefix}ywf_user_fund_log ADD `editor_id` INT NOT NULL AFTER `user_id`";
			$wpdb->query( $sql );

			update_option( 'ywf_dbversion', '1.0.1' );
		}
	}

	add_action( 'admin_init', 'ywf_update_db_1_0_1' );
}


if ( ! function_exists( 'yith_account_funds_clear_session' ) ) {
	function yith_account_funds_clear_session() {

		if ( ! is_null( WC()->session ) ) {
			WC()->session->set( 'ywf_fund_used', false );
			WC()->session->set( 'ywf_partial_payment', 'no' );
			WC()->session->set( 'deposit_amount', false );
			WC()->session->set( 'ywf_order_awaiting_partial_payment', false );
			WC()->session->set( 'yith_remain_funds', 0 );
		}

		if ( function_exists( 'wc_clear_notices' ) ) {
			wc_clear_notices();
		}
	}
}

if ( ! function_exists( 'yith_account_funds_get_partial_coupon_code' ) ) {
	/**
	 * @return string
	 */
	function yith_account_funds_get_partial_coupon_code() {
		$user_id = get_current_user_id();

		return apply_filters( 'yith_partial_coupon_code', 'yith_partial_coupon_' . $user_id, $user_id );
	}
}


if( !function_exists('yith_account_funds_get_endpoint_slug') ) {
	/**
	 *return the right endpoint slug
	 */
	function yith_account_funds_get_endpoint_slug( $endpoint ) {

		$is_customize_active = defined( 'YITH_WCMAP_PREMIUM' ) && YITH_WCMAP_PREMIUM;

		$account_funds_endpoint = array( 'make-a-deposit', 'view-history', 'redeem-funds' );
		$slug                   = '';
		if ( in_array( $endpoint, $account_funds_endpoint ) ) {

			if ( 'view-history' == $endpoint ) {
				$option_slug_name = 'view_income_expenditure_history';
			} else {
				$option_slug_name = str_replace( '-', '_', $endpoint );
			}
			$slug = get_option( 'ywf_' . $option_slug_name . '_slug', $endpoint );

			if ( $is_customize_active ) {
				$option_slug_name = str_replace( '-', '_', $endpoint );
				$slug             = get_option( 'woocommerce_myaccount_' . $option_slug_name . '_endpoint', $endpoint );
			}
		}

		return $slug;
	}
}

if( !function_exists('yith_account_funds_get_endpoint_title') ) {
	function yith_account_funds_get_endpoint_title( $endpoint ) {

		$account_funds_endpoint = array( 'make-a-deposit', 'view-history', 'redeem-funds' );

		$title = '';
		if ( in_array( $endpoint, $account_funds_endpoint ) ) {
			if ( 'view-history' == $endpoint ) {
				$option_slug_name = 'view_income_expenditure_history';
			} else {
				$option_slug_name = str_replace( '-', '_', $endpoint );
			}

			$default_title = '';
			switch ( $option_slug_name ) {

				case 'view_income_expenditure_history' :
					$default_title = __( 'Income/Expenditure History', 'yith-woocommerce-account-funds' );
					break;
				case 'redeem_funds':
					$default_title = _x( 'Redeem Funds','Endpoint title visible on My Account page', 'yith-woocommerce-account-funds' );
					break;
				default:
					$default_title = __( 'Make a deposit', 'yith-woocommerce-account-funds' );
					break;


			}
			$title = get_option( 'ywf_' . $option_slug_name, $default_title );
		}

		return $title;
	}
}
