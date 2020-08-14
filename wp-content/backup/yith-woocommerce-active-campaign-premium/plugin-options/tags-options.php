<?php
/**
 * Tags settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

// retrieve lists.
$tags_options   = YITH_WCAC()->retrieve_tags();
$order_statuses = wc_get_order_statuses();

$order_statuses_options = array();

if ( ! empty( $order_statuses ) ) {
	foreach ( $order_statuses as $status_slug => $status_name ) {
		$status_slug = str_replace( 'wc-', '', $status_slug );
		$order_statuses_options[ 'tags-order-' . $status_slug . '-tags' ] = array(
			// translators: 1. Order status.
			'title'             => sprintf( __( 'Order %s tags', 'yith-woocommerce-active-campaign' ), $status_name ),

			// translators: 1. Order status.
			'desc'              => sprintf( __( 'Select tags which will be automatically added to customer when order switches to %s', 'yith-woocommerce-active-campaign' ), $status_name ),

			'type'              => 'multiselect',
			'id'                => 'yith_wcac_tags_order_' . $status_slug,
			'options'           => $tags_options,
			'custom_attributes' => empty( $tags_options ) ? array(
				'disabled' => 'disabled',
			) : array(),
			'class'             => 'chosen_select',
			'css'               => 'width:300px;',
		);
	}
}

$options = array(
	'tags' => array_merge(
		array(
			'tags-options' => array(
				'title' => __( 'Tags per status', 'yith-woocommerce-active-campaign' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'yith_wcac_tags_options',
			),
		),
		$order_statuses_options,
		array(
			'tags-options-end' => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcac_tags_options',
			),
		)
	),
);


return apply_filters( 'yith_wcac_tags_options', $options );
