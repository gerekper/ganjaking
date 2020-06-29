<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'wp_privacy_personal_data_exporters', 'ywces_register_exporter' );
add_filter( 'wp_privacy_personal_data_erasers', 'ywces_register_eraser' );

/**
 * Registers the personal data exporter.
 *
 * @since   1.3.1
 *
 * @param   $exporters
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ywces_register_exporter( $exporters ) {
	$exporters['ywces-accept'] = array(
		'exporter_friendly_name' => esc_html__( 'Coupon Email System', 'yith-woocommerce-coupon-email-system' ),
		'callback'               => 'ywces_exporter',
	);

	return $exporters;
}

/**
 * Finds and exports personal data associated with an email address.
 *
 * @since   1.3.1
 *
 * @param  $email_address
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ywces_exporter( $email_address ) {

	$user           = get_user_by( 'email', $email_address );
	$data_to_export = array();

	if ( $user && get_user_meta( $user->ID, 'ywces_receive_coupons', true ) == 'yes' ) {

		$data_to_export[] = array(
			'group_id'    => 'ywces_accept',
			'group_label' => esc_html__( 'Coupon Email System Status', 'yith-woocommerce-coupon-email-system' ),
			'item_id'     => "accept-0",
			'data'        => array(
				array(
					'name'  => esc_html__( 'Acceptance', 'yith-woocommerce-coupon-email-system' ),
					'value' => esc_html__( 'This customer has given consent to receive coupons', 'yith-woocommerce-coupon-email-system' ),
				)
			),
		);

	}

	return array(
		'data' => $data_to_export,
		'done' => true,
	);
}

/**
 * Registers the personal data eraser.
 *
 * @since   1.3.1
 *
 * @param   $erasers
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ywces_register_eraser( $erasers ) {
	$erasers['ywces-accept'] = array(
		'eraser_friendly_name' => esc_html__( 'Coupon Email System', 'yith-woocommerce-coupon-email-system' ),
		'callback'             => 'ywces_eraser',
	);

	return $erasers;
}

/**
 * Erases personal data associated with an email address.
 *
 * @since 1.3.1
 *
 * @param  $email_address
 *
 * @return array
 */
function ywces_eraser( $email_address ) {

	if ( empty( $email_address ) ) {
		return array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	// Limit us to 500 comments at a time to avoid timing out.
	$items_removed = false;
	$user          = get_user_by( 'email', $email_address );

	if ( $user ) {

		delete_user_meta( $user->ID, 'ywces_receive_coupons' );
		$items_removed = true;

	}

	return array(
		'items_removed'  => $items_removed,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => true,
	);
}

if ( ! function_exists( 'ywces_order_count' ) ) {

	function ywces_order_count( $user_id, $vendor_id = '' ) {

		global $wpdb;

		$statuses = apply_filters( 'ywces_order_count_statuses', array( 'wc-completed', 'wc-refunded' ) );
		$statuses = "'" . implode( "','", $statuses ) . "'";

		if ( $vendor_id != '' ) {

			$count = $wpdb->get_var( "SELECT COUNT(*)
			FROM $wpdb->posts as posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id

			WHERE   meta.meta_key       = '_customer_user'
			AND     posts.post_type     IN ('shop_order')
			AND     posts.post_status   IN (" . $statuses . ")
			AND		posts.post_author	= $vendor_id
			AND     meta_value          = $user_id
		" );

		} else {

			$count = $wpdb->get_var( "SELECT COUNT(*)
			FROM $wpdb->posts as posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id

			WHERE   meta.meta_key       = '_customer_user'
			AND     posts.post_type     IN ('shop_order')
			AND     posts.post_status   IN (" . $statuses . ")
			AND     posts.post_parent = 0
			AND     meta_value          = $user_id
		" );

		}

		return absint( $count );
	}

}

if ( ! function_exists( 'ywces_total_spent' ) ) {

	function ywces_total_spent( $user_id, $vendor_id = '' ) {

		global $wpdb;

		$statuses = apply_filters( 'ywces_total_spent_statuses', array( 'wc-completed', 'wc-refunded' ) );
		$statuses = "'" . implode( "','", $statuses ) . "'";

		if ( $vendor_id != '' ) {

			$spent = $wpdb->get_var( "SELECT SUM(meta2.meta_value)
			FROM $wpdb->posts as posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id

			WHERE   meta.meta_key       = '_customer_user'
			AND     meta.meta_value     = $user_id
			AND     posts.post_type     IN ('shop_order')
			AND     posts.post_status   IN (" . $statuses . ")
			AND		posts.post_author	= $vendor_id
			AND     meta2.meta_key      = '_order_total'
		" );

		} else {

			$spent = $wpdb->get_var( "SELECT SUM(meta2.meta_value)
			FROM $wpdb->posts as posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			LEFT JOIN {$wpdb->postmeta} AS meta2 ON posts.ID = meta2.post_id

			WHERE   meta.meta_key       = '_customer_user'
			AND     meta.meta_value     = $user_id
			AND     posts.post_type     IN ('shop_order')
			AND     posts.post_status   IN (" . $statuses . ")
			AND     posts.post_parent = 0
			AND     meta2.meta_key      = '_order_total'
		" );

		}

		return $spent;
	}

}

if ( ! function_exists( 'ywces_user_registration' ) ) {

	/**
	 * Trigger coupon on user registration
	 *
	 * @since   1.0.0
	 *
	 * @param    $customer_id
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	function ywces_user_registration( $customer_id ) {

		if ( get_option( 'ywces_refuse_coupon' ) == 'yes' && get_user_meta( $customer_id, 'ywces_receive_coupons', true ) == 'yes' ) {
			return;
		}

		$coupon_code = get_option( 'ywces_coupon_register' );

		if ( get_option( 'ywces_enable_register' ) == 'yes' && YITH_WCES()->check_if_coupon_exists( $coupon_code ) ) {

			$user = get_user_by( 'id', $customer_id );

			YITH_WCES()->bind_coupon( $coupon_code, $user->user_email );

			$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'register', $coupon_code );

			if ( ! $email_result ) {
				YITH_WCES()->write_log( array(
					                        'coupon_code' => $coupon_code,
					                        'type'        => 'register'
				                        ) );
			}

		}

	}

	add_action( 'woocommerce_created_customer', 'ywces_user_registration', 10, 1 );

}

if ( ! function_exists( 'ywces_update_1_3_3' ) ) {

	/**
	 * Add columns for YITH WooCommerce Product Vendors compatibility
	 *
	 * @since   1.3.3
	 * @return  void
	 * @author  Alberto ruggiero
	 */
	function ywces_update_1_3_3() {

		$ywces_db_option = get_option( 'ywces_db_version' );

		if ( empty( $ywces_db_option ) || version_compare( $ywces_db_option, '1.3.3', '<' ) ) {

			global $wpdb;

			$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}usermeta WHERE meta_key = 'ywces_birthday'" );
			$limit = 50;
			$pages = ceil( $total / $limit );

			for ( $page = 1; $page <= $pages; $page ++ ) {

				$offset  = $limit * ( $page - 1 );
				$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}usermeta WHERE meta_key = 'ywces_birthday' LIMIT {$offset },{$limit}" );

				foreach ( $results as $result ) {
					add_user_meta( $result->user_id, 'yith_birthday', $result->meta_value );
				}

			};

			update_option( 'ywces_db_version', '1.3.3' );

		}

	}

	add_action( 'admin_init', 'ywces_update_1_3_3' );

}



