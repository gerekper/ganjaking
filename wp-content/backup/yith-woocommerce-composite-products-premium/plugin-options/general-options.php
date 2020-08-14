<?php
/**
 * GENERAL ARRAY OPTIONS
 */

$general = array(

	'general'  => array(

		array(
	        'title'		=> __( 'General', 'yith-composite-products-for-woocommerce' ),
	        'type'		=> 'title',
	        'desc'		=> '',
	        'id'		=> 'yith_wcp_settings_type'
	    ),

		array(
			'title'		=> __( 'Enable Plugin Features', 'yith-composite-products-for-woocommerce' ),
			'type'		=> 'checkbox',
			'id'  		=> 'yith_wcp_settings_enable_plugin_features',
			'default' 	=> 'yes',
		),

		array(
			'title'		=> __( 'AJAX variation threshold', 'yith-composite-products-for-woocommerce' ),
			'type'		=> 'text',
			'desc'		=> __( 'If the variation count is greater than the specified value, WooCommerce will load the selected variations via AJAX to avoid page overload', 'yith-composite-products-for-woocommerce' ),
			'id'  		=> 'yith_wcp_settings_ajax_variation_treshold',
			'default' 	=> 30,
			'desc_tip'	=> true,
		),

        array(
            'title'		=> __( 'Remove composite product quantity from order table', 'yith-composite-products-for-woocommerce' ),
            'type'		=> 'checkbox',
            'id'  		=> 'yith_wcp_remove_composite_product_quantity_from_order_table',
            'default' 	=> 'no',
        ),

	    array(
	        'type' 		=> 'sectionend',
	        'id' 		=> 'yith_wcp_settings_end'
	    ),

	)

);

return apply_filters( 'yith_wapo_panel_general_options', $general );