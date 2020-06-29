<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$desc = sprintf( '%s: %s,%s,%s.', __( 'Set rule visibility', 'yith-woocommerce-role-based-prices' ),
	__( 'If Global, the rule applies to all products', 'yith-woocommerce-role-based-prices' ),
	__( 'If Product Category, the rule applies to selected categories', 'yith-woocommerce-role-based-prices' ),
	__( 'If Product Tag, the rule applies to selected tags', 'yith-woocommerce-role-based-prices' ) );

$currency_symbol = get_woocommerce_currency_symbol();
$args            = array(
	'label'    => __( 'Price rule settings', 'yith-woocommerce-role-based-prices' ),
	'pages'    => 'yith_price_rule', //or array( 'post-type1', 'post-type2')
	'context'  => 'normal', //('normal', 'advanced', or 'side')
	'priority' => 'default',
	'tabs'     => array(
		'rules' => array(
			'label'  => __( 'Price rule', 'yith-woocommerce-role-based-prices' ),
			'fields' => array(

				'ywcrbp_role' => array(
					'label'       => __( 'Apply rule to', 'yith-woocommerce-role-based-prices' ),
					'desc'        => '',
					'type'        => 'chosen-user-role',
					'placeholder' => 'Select role',
					'multiple'    => false,
					'options'     => ywcrbp_get_user_role(),
					'std'         => ''
				),

				'ywcrbp_type_rule'        => array(
					'label'   => __( 'Rule type', 'yith-woocommerce-role-based-prices' ),
					'type'    => 'select',
					'options' => array(
						'global'       => __( 'All products', 'yith-woocommerce-role-based-prices' ),
						'category'     => __( 'Product category', 'yith-woocommerce-role-based-prices' ),
						'exc_category' => __( 'Exclude Product Category', 'yith-woocommerce-role-based-prices' ),
						'tag'          => __( 'Product tag', 'yith-woocommerce-role-based-prices' ),
						'exc_tag'      => __( 'Exclude Product tag', 'yith-woocommerce-role-based-prices' ),
					),
					'std'     => 'global',
					'desc'    => $desc
				),
				'ywcrbp_category_product' => array(
					'label'    => __( 'Select categories', 'yith-woocommerce-role-based-prices' ),
					'desc'     => __( 'Select product categories to which the rule applies', 'ywcps' ),
					'type'     => 'ajax-terms',
					'data'     => array(
						'taxonomy' => 'product_cat',
					),
					'multiple' => true,
					'std'      => array(),
					'options'  => array(),
					'id'       => '_ywcrbp_category_product',
					'deps'     => array(
						'ids'    => '_ywcrbp_type_rule',
						'values' => 'category',
					),
				),
				'ywcrbp_tag_product'      => array(
					'label'    => __( 'Select tags', 'yith-woocommerce-role-based-prices' ),
					'desc'     => __( 'Select product tags to which the rule applies', 'yith-woocommerce-role-based-prices' ),
					'type'     => 'ajax-terms',
					'data'     => array(
						'taxonomy' => 'product_tag',
					),
					'multiple' => true,
					'std'      => array(),
					'options'  => array(),
					'id'       => '_ywcrbp_tag_product',

					'deps' => array(
						'ids'    => '_ywcrbp_type_rule',
						'values' => 'tag',
					),
				),
				'ywcrbp_exc_category_product' => array(
					'label'    => __( 'Exclude categories', 'yith-woocommerce-role-based-prices' ),
					'desc'     => __( 'Select product categories to which the rule not applies', 'ywcps' ),
					'type'     => 'ajax-terms',
					'data'     => array(
						'taxonomy' => 'product_cat',
					),
					'multiple' => true,
					'std'      => array(),
					'options'  => array(),
					'id'       => '_ywcrbp_exc_category_product',
					'deps'     => array(
						'ids'    => '_ywcrbp_type_rule',
						'values' => 'exc_category',
					),
				),
				'ywcrbp_exc_tag_product'      => array(
					'label'    => __( 'Exclude tags', 'yith-woocommerce-role-based-prices' ),
					'desc'     => __( 'Select product tags to which the rule not applies', 'yith-woocommerce-role-based-prices' ),
					'type'     => 'ajax-terms',
					'data'     => array(
						'taxonomy' => 'product_tag',
					),
					'multiple' => true,
					'std'      => array(),
					'options'  => array(),
					'id'       => '_ywcrbp_exc_tag_product',

					'deps' => array(
						'ids'    => '_ywcrbp_type_rule',
						'values' => 'exc_tag',
					),
				),
				'ywcrbp_type_price'       => array(

					'label'   => __( 'Discount or markup', 'yith-woocommerce-role-based-prices' ),
					'desc'    => '',
					'type'    => 'select',
					'options' => array(
						'discount_perc' => __( 'Discount %', 'yith-woocommerce-role-based-prices' ),
						'discount_val'  => sprintf( '%s %s', __( 'Discount ', 'yith-woocommerce-role-based-prices' ), $currency_symbol ),
						'markup_perc'   => __( 'Markup %', 'yith-woocommerce-role-based-prices' ),
						'markup_val'    => sprintf( '%s %s', __( 'Markup ', 'yith-woocommerce-role-based-prices' ), $currency_symbol ),
					),
					'std'     => 'discount_perc'
				),

				'ywcrbp_price_value' => array(
					'label' => __( 'Price', 'yith-woocommerce-role-based-prices' ),
					'desc'  => __( 'Enter an amount to be removed or added to regular price.' ),
					'type'  => 'custom-text',
					'deps'  => array( 'ids' => '_ywcrbp_type_price', 'values' => 'discount_val,markup_val' ),
					'class' => 'wc_input_price'
				),

				'ywcrbp_decimal_value' => array(
					'label' => __( 'Value', 'yith-woocommerce-role-based-prices' ),
					'desc'  => __( 'Enter a percent value to calculate discount or markup.', 'yith-woocommerce-role-based-prices' ),
					'type'  => 'custom-text',
					'deps'  => array( 'ids' => '_ywcrbp_type_price', 'values' => 'discount_perc,markup_perc' ),
					'class' => 'wc_input_decimal'
				),

				'ywcrbp_priority_rule' => array(
					'label' => __( 'Priority', 'yith-woocommerce-role-based-prices' ),
					'desc'  => __( 'If more than one rule of the same type (Global, product category or product tag) are created for the same user role, priority allows you to create an order for rules to be applied. Ex. A priority 1 rule will be applied before a priority 10 rule.', 'yith-woocommerce-role-based-prices' ),
					'type'  => 'number',
					'std'   => 1,
					'min'   => 1
				),
				'ywcrbp_active_rule'   => array(
					'label' => __( 'Activate Rule', 'yith-woocommerce-role-based-prices' ),
					'type'  => 'checkbox',
					'std'   => true,
					'desc'  => __( 'Activate or deactivate the rule', 'yith-woocommerce-role-based-prices' )
				)
			)
		)
	)
);


return $args;