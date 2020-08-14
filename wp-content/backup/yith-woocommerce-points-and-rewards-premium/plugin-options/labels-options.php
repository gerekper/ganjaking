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
	exit;
} // Exit if accessed directly

$section1 = array(
	'labels_title'                 => array(
		'name' => __( 'Labels Settings', 'yith-woocommerce-points-and-rewards' ),
		'type' => 'title',
		'id'   => 'ywpar_labels_title',
	),

	'points_label_singular'        => array(
		'name'      => __( 'Singular label replacing "point"', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Point', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_points_label_singular',
	),

	'points_label_plural'          => array(
		'name'      => __( 'Plural label replacing "points"', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Points', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_points_label_plural',
	),

	'label_order_completed'        => array(
		'name'      => __( 'Order Completed', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Order Completed', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_order_completed',
	),

	'label_order_processing'       => array(
		'name'      => __( 'Order Processing', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Order Processing', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_order_processing',
	),

	'label_order_cancelled'        => array(
		'name'      => __( 'Order Cancelled', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Order Cancelled', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_order_cancelled',
	),

	'label_admin_action'           => array(
		'name'      => __( 'Admin Action', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Admin Action', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_admin_action',
	),

	'label_reviews_exp'            => array(
		'name'      => __( 'Reviews', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Reviews', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_reviews_exp',
	),

	'label_registration_exp'       => array(
		'name'      => __( 'Registration', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Registration', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_registration_exp',
	),

	'label_points_exp'             => array(
		'name'      => __( 'Target - Total Points', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Target achieved - Points collected', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_points_exp',
	),

	'label_amount_spent_exp'       => array(
		'name'      => __( 'Target - Total Amount', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Target achieved - Total spend', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_amount_spent_exp',
	),

	'label_num_of_orders_exp'      => array(
		'name'      => __( 'Target - Total Orders', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Target achieved - Total Orders', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_num_of_orders_exp',
	),
	'label_checkout_threshold_exp' => array(
		'name'      => __( 'Target Checkout Total Threshold', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Target achieved - Checkout Total Threshold', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_checkout_threshold_exp',
	),
	'label_birthday_exp'           => array(
		'name'      => __( 'Birthday', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Target achieved - Birthday', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_birthday_exp',
	),
	'label_expired_points'         => array(
		'name'      => __( 'Expired Points', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Expired Points', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_expired_points',
	),

	'label_order_refund'           => array(
		'name'      => __( 'Order Refund', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Order Refund', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_order_refund',
	),

	'label_refund_deleted'         => array(
		'name'      => __( 'Order Refund Deleted', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Order Refund Deleted', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_refund_deleted',
	),

	'label_redeemed_points'        => array(
		'name'      => __( 'Redeemed Points', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Redeemed Points for order', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_redeemed_points',
	),

	'label_apply_discounts'        => array(
		'name'      => __( 'Apply Discount Button', 'yith-woocommerce-points-and-rewards' ),
		'desc'      => '',
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'default'   => __( 'Apply Discount', 'yith-woocommerce-points-and-rewards' ),
		'id'        => 'ywpar_label_apply_discounts',
	),
	'labels_title_end'             => array(
		'type' => 'sectionend',
		'id'   => 'ywpar_labels_title_end',
	),
);

return array( 'labels' => $section1 );
