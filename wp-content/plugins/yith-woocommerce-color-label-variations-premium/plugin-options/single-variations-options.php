<?php
/**
 * SINGLE VARIATIONS ARRAY OPTIONS
 *
 * @since 1.0.0
 * @author Francesco Licandro
 * @package YITH WooCommerce Color and Label Variations Premium
 */

defined( 'YITH_WCCL' ) || exit; // Exit if accessed directly.

$single_variations = array(

	'single-variations' => array(

		array(
			'title' => __( 'Single Variations in loop', 'yith-woocommerce-color-label-variations' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wccl-single-variations-options',
		),

		array(
			'title'     => __( 'Show single variations in loop', 'yith-woocommerce-color-label-variations' ),
			'desc'      => __( 'Choose whether to show single variations in archive pages of your store or not', 'yith-woocommerce-color-label-variations' ),
			'id'        => 'yith-wccl-show-single-variations-loop',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		array(
			'title'     => __( 'Hide parent products', 'yith-woocommerce-color-label-variations' ),
			'desc'      => __( 'Hide parent products in loop when single variations are showed', 'yith-woocommerce-color-label-variations' ),
			'id'        => 'yith-wccl-hide-parent-products-loop',
			'default'   => 'yes',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		array(
			'title'     => __( 'Order products by ID', 'yith-woocommerce-color-label-variations' ),
			'desc'      => __( 'Order products by ID in all WooCommerce loop. This option alters the main WordPress query so you may notice changes in different sections of your site. We recommend enabling this option if you see your variations sequentially', 'yith-woocommerce-color-label-variations' ),
			'id'        => 'yith-wccl-order-products-by-id',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wccl-single-variations-options',
		),
	),
);

return apply_filters( 'yith_wccl_panel_single_variations_options', $single_variations );
