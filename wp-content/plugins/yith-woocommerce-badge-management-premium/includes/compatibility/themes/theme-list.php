<?php
/**
 * Theme List
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Compatibility
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

return array(
	'basel'         => array(
		'min_version'         => '2.10.0',
		'compatibility_class' => 'YITH_WCBM_Basel_Theme_Compatibility',
	),
	'yith-proteo'   => array(
		// since 1.3.27.
		'start' => array(
			'hook'     => 'woocommerce_before_shop_loop_item_title',
			'priority' => 9,
		),
		'end'   => array(
			'hook'     => 'woocommerce_before_shop_loop_item_title',
			'priority' => 20,
		),
	),
	'yith-booking'  => array(
		// since 1.3.20.
		'min_version'         => '1.0.0',
		'compatibility_class' => 'YITH_WCBM_YITH_Booking_Theme_Compatibility',
	),
	'electro'       => array(
		// since 1.3.6.
		'min_version'         => '1.2.9',
		'compatibility_class' => 'YITH_WCBM_Electro_Theme_Compatibility',
	),
	'estore'        => array(
		// since 1.3.7.
		'start' => array(
			'hook'     => 'woocommerce_before_shop_loop_item_title',
			'priority' => 9,
		),
		'end'   => array(
			'hook'     => 'woocommerce_before_shop_loop_item_title',
			'priority' => 20,
		),
	),
	'flatsome'      => array(
		// do nothing: you need to activate the Force Positioning option.
		'min_version' => '3.7.0',
	),
	'shopkeeper'    => array(
		// since 1.3.7.
		'min_version'         => '2.3',
		'compatibility_class' => 'YITH_WCBM_Shopkeeper_Theme_Compatibility',
		'start'               => array(
			'hook'     => 'woocommerce_before_single_product_summary_product_images',
			'priority' => 9,
		),
		'end'                 => array(
			'hook'     => 'woocommerce_before_single_product_summary_product_images',
			'priority' => 21,
		),
	),
	'total'         => array(
		// since 1.3.7.
		'start' => array(
			'hook'     => 'woocommerce_before_shop_loop_item_title',
			'priority' => 9,
		),
		'end'   => array(
			'hook'     => 'woocommerce_before_shop_loop_item_title',
			'priority' => 11,
		),
	),
	'twenty-twenty' => array(
		// since 1.3.23.
		'compatibility_class' => 'YITH_WCBM_Twenty_Twenty_Theme_Compatibility',
		'min_version'         => '1.0',
	),
	'avada'         => array(
		// since 2.0.0.
		'compatibility_class' => 'YITH_WCBM_Avada_Theme_Compatibility',
	),
	'fana'          => array(
		'compatibility_class' => 'YITH_WCBM_Fana_Theme_Compatibility',
		'min_version' => '1.0.3',
		'start'       => array(
			'hook'     => 'woocommerce_before_shop_loop_item',
			'priority' => 5,
		),
		'end'         => array(
			'hook'     => 'tbay_woocommerce_before_content_product',
			'priority' => 5,
		),
	),
);
