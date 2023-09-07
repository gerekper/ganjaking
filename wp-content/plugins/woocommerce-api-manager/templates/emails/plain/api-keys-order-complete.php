<?php
/**
 * API Keys Order Complete Email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/api-keys-order-complete.php.
 *
 * HOWEVER, on occasion WooCommerce API Manager will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Todd Lahman LLC
 * @package WooCommerce API Manager/Templates/Emails
 * @version 3.1
 */

defined( 'ABSPATH' ) || exit;

if ( is_object( $order ) && ! empty( $resources ) ) {
	$hide_product_order_api_keys = WC_AM_USER()->hide_product_order_api_keys();
	$hide_master_api_key         = WC_AM_USER()->hide_master_api_key();

	echo "\n\n" . esc_html( '-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-' );
	echo "\n\n" . esc_html__( apply_filters( 'wc_api_manager_email_api_product_heading', __( 'API Product Information', 'woocommerce-api-manager' ) ) ) . "\n\n";

	foreach ( $resources as $resource ) {
		$product_object = WC_AM_PRODUCT_DATA_STORE()->get_product_object( $resource->product_id );

		if ( WCAM()->get_wc_subs_exist() && ! empty( $resource->sub_id ) ) {
			$expires = ( WC_AM_SUBSCRIPTION()->has_end_date_by_sub( $resource->sub_id ) ) ? date_i18n( wc_date_format(), WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $resource->sub_id, 'end', 'site' ) ) : _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' );
		} else {
			if ( WC_AM_ORDER_DATA_STORE()->is_time_expired( $resource->access_expires ?? false ) ) {
				$expires = __( 'Expired', 'woocommerce-api-manager' );
			} else {
				$expires = $resource->access_expires == 0 ? _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' ) : WC_AM_FORMAT()->unix_timestamp_to_date( $resource->access_expires );
			}
		}

		// translators: %s placeholder is title
		esc_html_e( sprintf( __( 'Product: %s', 'woocommerce-api-manager' ), $product_object->get_title() ) );
		echo "\n";

		if ( ! $hide_product_order_api_keys ) {
			// translators: %s placeholder is Product Order Api Key
			esc_html_e( sprintf( apply_filters( 'wc_api_manager_email_product_order_api_keys_row', __( 'Product Order API Key(s): %s', 'woocommerce-api-manager' ) ), $resource->product_order_api_key ) );
			echo "\n";
		}

		// translators: %s placeholder is Product ID
		esc_html_e( sprintf( __( 'Product ID: %s', 'woocommerce-api-manager' ), absint( $resource->product_id ) ) );
		echo "\n";
		// translators: %s placeholder is Activations
		esc_html_e( sprintf( __( 'Activations: %s', 'woocommerce-api-manager' ), absint( $resource->activations_purchased_total ) ) );
		echo "\n";
		// translators: %s placeholder is Expires date and time
		esc_html_e( sprintf( __( 'Expires: %s', 'woocommerce-api-manager' ), $expires ) );
		echo "\n";

		echo esc_html( '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~' ) . "\n\n";
	}

	if ( ! $hide_master_api_key ) {
		// translators: %s placeholder is Master API Key
		esc_html_e( sprintf( apply_filters( 'wc_api_manager_email_master_api_key_row', __( 'Master API Key: %s', 'woocommerce-api-manager' ) ), WC_AM_USER()->get_master_api_key( $order->get_customer_id() ) ) );
		echo "\n\n";

		esc_html_e( apply_filters( 'wc_api_manager_email_master_api_key_message_row', __( 'A Master API Key can be used to activate any and all products.', 'woocommerce-api-manager' ) ) );
		echo "\n";
	}

	if ( $order->has_downloadable_item() ) {
		echo "\n";
		// translators: %s placeholder is My Account > API Downloads -> URL
		esc_html_e( __( 'Click here to login and download your file(s):', 'woocommerce-api-manager' ) );

		echo esc_url( wc_get_endpoint_url( 'api-downloads', '', wc_get_page_permalink( 'myaccount' ) ) ) . "\n";
	}

	echo "\n" . esc_html( '_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_' );
	echo "\n\n";
}