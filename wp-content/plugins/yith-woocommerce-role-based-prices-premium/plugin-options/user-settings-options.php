<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$woocommerce_hook = array(
	'template_single_title'       => __( 'Before product name', 'yith-woocommerce-role-based-prices' ),
	'template_single_price'       => __( 'Before price', 'yith-woocommerce-role-based-prices' ),
	'template_single_excerpt'     => __( 'Before product excerpt', 'yith-woocommerce-role-based-prices' ),
	'template_single_add_to_cart' => __( 'Before Add to Cart', 'yith-woocommerce-role-based-prices' ),
	'template_single_meta'        => __( 'Before meta', 'yith-woocommerce-role-based-prices' ),
	'template_single_sharing'     => __( 'Before sharing buttons', 'yith-woocommerce-role-based-prices' )
);
$all_roles        = ywcrbp_get_user_role();
$defaults         = array();
$roles_options    = array(
	'regular'        => __( 'Regular price', 'yith-woocommerce-role-based-prices' ),
	'on_sale'        => __( 'On sale price', 'yith-woocommerce-role-based-prices' ),
	'your_price'     => __( 'Role-based price',
		'yith-woocommerce-role-based-prices' ),
	'add_to_cart'    => __( 'Add to Cart', 'yith-woocommerce-role-based-prices' ),
	'how_show_price' => __( 'Show Price Incl Tax', 'yith-woocommerce-role-based-prices' )
);

foreach ( $all_roles as $key => $role ) {
	foreach ( $roles_options as $key_role => $role_option ) {

		$defaults[ $key ][ $key_role ] = 1;
	}
}

$setting = array(

	'user-settings' => array(

		'hide_price_section_start' => array(
			'name' => __( 'User settings', 'yith-woocommerce-role-based-prices' ),
			'id'   => 'ywcrbp_hide_price_section_start',
			'type' => 'title'
		),

		'show_prices_for_role'   => array(
			'type'    => 'show-prices-user-role',
			'id'      => 'ywcrbp_show_prices_for_role',
			'value'   => '',
			'default' => $defaults
		),
		'hide_price_section_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywcrbp_hide_price_section_end'
		),

	)
);


return apply_filters( 'ywcrbp_user_settings_opt', $setting );