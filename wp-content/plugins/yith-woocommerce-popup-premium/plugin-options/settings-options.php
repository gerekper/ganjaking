<?php

$list = YITH_Popup()->get_popups_list();
if ( empty( $list ) ) {
	$desc = sprintf( __( 'Attention: You should create a new popup to set this option. <a href="%s">Create a new popup</a>', 'yith-woocommerce-popup' ), admin_url( 'post-new.php?post_type=yith_popup' ) );
} else {
	$desc = __( 'Select the popup that you want to show as default', 'yith-woocommerce-popup' );
}

$settings = array(

	'settings' => array(

		'header'   => array(

			array(
				'name' => __( 'General Settings', 'yith-woocommerce-popup' ),
				'type' => 'title',
			),

			array( 'type' => 'close' ),
		),


		'settings' => array(

			array( 'type' => 'open' ),

			array(
				'id'   => 'ypop_enable',
				'name' => __( 'Enable Popup', 'yith-woocommerce-popup' ),
				'desc' => '',
				'type' => 'on-off',
				'std'  => 'yes',
			),

			array(
				'id'   => 'ypop_enable_in_mobile',
				'name' => __( 'Enable Popup in Mobile Device', 'yith-woocommerce-popup' ),
				'desc' => '',
				'type' => 'on-off',
				'std'  => 'yes',
			),

			array(
				'name' => __( 'Show on all pages', 'yith-woocommerce-popup' ),
				'desc' => __( 'Enable newsletter popup in all pages.', 'yith-woocommerce-popup' ),
				'id'   => 'ypop_enabled_everywhere',
				'type' => 'on-off',
				'std'  => 'yes',
			),

			array(
				'name'     => __( 'Select where you want to show the popup', 'yith-woocommerce-popup' ),
				'desc'     => __( 'Select in which pages you want to show the popup. ', 'yith-woocommerce-popup' ),
				'id'       => 'ypop_popup_pages',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'multiple' => true,
				'options'  => ypop_get_available_pages(),
				'std'      => array(),
			// 'deps' => array(
			// 'ids' => 'ypop_enabled_everywhere',
			// 'values' => 'no'
			// )
			),

			array(
				'name' => __( 'Cookie Variable', 'yith-woocommerce-popup' ),
				'desc' => __( 'Set the name for the cookie generated after closing the link of the popup. In this way, as soon as you\'ll change this value, all your visitors will see the link again even if they have disabled it. Don\'t abuse of this function!', 'yith-woocommerce-popup' ),
				'id'   => 'ypop_cookie_var',
				'type' => 'text',
				'std'  => __( 'yithpopup', 'yith-woocommerce-popup' ),
			),


			array(
				'name'    => __( 'Hide policy', 'yith-woocommerce-popup' ),
				'desc'    => __( 'Select when popup should be hidden. By default, it will be hidden only when the hiding checkbox is flagged) ', 'yith-woocommerce-popup' ),
				'id'      => 'ypop_hide_policy',
				'type'    => 'select',
				'options' => array(
					'always'  => __( 'Hide when the "Hiding checkbox" is flagged', 'yith-woocommerce-popup' ),
					'session' => __( 'Show only once per session', 'yith-woocommerce-popup' ),
				),
				'std'     => 'always',
			),

			array(
				'name' => __( 'How many days should the popup be hidden for?', 'yith-woocommerce-popup' ),
				'desc' => __( 'Set how many days have to pass before showing again the lightbox', 'yith-woocommerce-popup' ),
				'id'   => 'ypop_hide_days',
				'css'  => 'width:50px;',
				'type' => 'text',
				'std'  => '3',
				'deps' => array(
					'ids'    => 'ypop_hide_policy',
					'values' => 'always',
				),
			),

			array(
				'name' => __( 'Hiding text', 'yith-woocommerce-popup' ),
				'desc' => __( 'The title displayed next to the checkbox that lets users hide the popup forever. You can also use HTML code', 'yith-woocommerce-popup' ),
				'id'   => 'ypop_hide_text',
				'type' => 'text',
				'std'  => __( 'Do not show it anymore.', 'yith-woocommerce-popup' ),
			),

			array(
				'name'     => __( 'Select the default popup', 'yith-woocommerce-popup' ),
				'desc'     => __( 'Attention: You should create a new popup to set this option', 'yith-woocommerce-popup' ),
				'desc'     => $desc,
				'id'       => 'ypop_popup_default',
				'type'     => 'select',
				'multiple' => false,
				'options'  => YITH_Popup()->get_popups_list(),
				'std'      => '',
			),

			array( 'type' => 'close' ),

		),
	),
);

return apply_filters( 'yith_ypop_panel_settings_options', $settings );
