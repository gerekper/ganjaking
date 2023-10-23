<?php
/**
 * Blocks Tab
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$blocks = array(
	'blocks' => array(
		'blocks-tab' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wapo_show_blocks_tab',
            'title'  => __( 'Options blocks', 'yith-woocommerce-product-add-ons' ),
            'description'  => __( 'Create blocks of options and set in which products to show them.', 'yith-woocommerce-product-add-ons' ),
		),
	),
);

return apply_filters( 'yith_wapo_panel_blocks_options', $blocks );
