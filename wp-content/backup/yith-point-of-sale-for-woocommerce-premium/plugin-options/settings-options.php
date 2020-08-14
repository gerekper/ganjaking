<?php
// Exit if accessed directly
! defined( 'YITH_POS' ) && exit();

$indexed_payment_methods = yith_pos_get_indexed_payment_methods( true );
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
$settings = array(
	'settings' => array(
		'settings_section_start'                                      => array(
			'type' => 'sectionstart',
		),
		'settings_registers_title'                                    => array(
			'title' => _x( 'General Settings', 'Panel: page title', 'yith-point-of-sale-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
		),
		'settings_registers_audio_enabled'                            => array(
			'name'      => __( 'Enable sound effect', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Enable or disable the sound effect when a product is added to cart', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith_pos_audio_enabled',
			'default'   => 'yes',
		),
		'settings_registers_close_modals_when_clicking_on_background' => array(
			'name'      => __( 'Close popup windows when clicking on the background', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'If enabled, all popup windows will be closed by clicking on the background', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith_pos_close_modals_when_clicking_on_background',
			'default'   => 'yes',
		),

		'settings_section_end'                => array(
			'type' => 'sectionend',
		),
		'settings_login_section_start'        => array(
			'name' => __( 'Login page', 'yith-point-of-sale-for-woocommerce' ),
			'type' => 'title'
		),
		'settings_pos_page'                   => array(
			'name'  => __( 'Login page', 'yith-point-of-sale-for-woocommerce' ),
			'desc'  => __( 'Select the page of login', 'yith-point-of-sale-for-woocommerce' ),
			'id'    => 'settings_pos_page',
			'type'  => 'single_select_page',
			'class' => 'wc-enhanced-select',
			'css'   => 'min-width:300px;',
		),
		'settings_login_logo'                 => array(
			'name'      => __( 'Login logo', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select logo for login form', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'id'        => 'yith_pos_login_logo',
			'default'   => YITH_POS_ASSETS_URL . '/images/logo-pos.png',
		),
		'settings_login_background_image'     => array(
			'name'      => __( 'Login background image', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select image for background login page', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'id'        => 'yith_pos_login_background_image',
		),
		'settings_login_background_color'     => array(
			'name'      => __( 'Login background color', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the background color for login page', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'yith_pos_login_background_color',
			'default'   => '#707070',
		),
		'settings_login_section_end'          => array(
			'type' => 'sectionend',
		),
		'settings_gateways_section_start'     => array(
			'name' => __( 'Payment method settings', 'yith-point-of-sale-for-woocommerce' ),
			'type' => 'title'
		),
		'payment_methods'                     => array(
			'id'        => 'yith_pos_general_gateway_enabled',
			'yith-type' => 'checkbox-array',
			'type'      => 'yith-field',
			'name'      => __( 'Payment methods', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the global payment methods. These methods can be overridden for every single Register.', 'yith-point-of-sale-for-woocommerce' ),
			'class'     => 'yith-pos-register-payment-methods no-bottom',
			'options'   => $indexed_payment_methods,
			'std'       => array_keys( $indexed_payment_methods ),
		),
		'settings_gateways_section_start_end' => array(
			'type' => 'sectionend',
		),
		'settings_presets_section_start'      => array(
			'name' => __( 'Preset settings', 'yith-point-of-sale-for-woocommerce' ),
			'type' => 'title'
		),

		'number_keyboard_presets' => array(
			'id'        => 'yith_pos_numeric_controller_discount_presets',
			'yith-type' => 'presets',
			'type'      => 'yith-field',
			'name'      => __( 'Number Keyboard presets', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Set the percentage discount presets within the number keyboard', 'yith-point-of-sale-for-woocommerce' ),
			'slot_num'  => 4,
			'std'       => array( 5, 10, 15, 20 ),
			'step'      => 1,
			'min'       => 1,
			'max'       => 100
		),

		'fee_and_discount_presets' => array(
			'id'        => 'yith_pos_fee_and_discount_presets',
			'yith-type' => 'presets',
			'type'      => 'yith-field',
			'name'      => __( 'Fee and Discount presets', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Set the percentage discount presets within the fee and discount editor', 'yith-point-of-sale-for-woocommerce' ),
			'slot_num'  => 5,
			'std'       => array( 5, 10, 15, 20, 50 ),
			'step'      => 1,
			'min'       => 1,
			'max'       => 100
		),

		'settings_presets_section_end' => array(
			'type' => 'sectionend',
		),

		'settings_stock_start' => array(
			'name' => __( 'Stock Management', 'yith-point-of-sale-for-woocommerce' ),
			'type' => 'title'
		),

		'settings_show_stock_on_pos' => array(
			'name'      => __( 'Show stock on register', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Choose if to show the stock count on the register', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith_pos_show_stock_on_pos',
			'default'   => 'no',
		),

		'settings_multistock_enabled' => array(
			'name'      => __( 'Enable multistock', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Choose if enable or disable the multistock option', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'yith_pos_multistock_enabled',
			'default'   => 'yes',
		),

		'settings_multistock_condition' => array(
			'name'      => __( 'Products of a store without stock are', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => implode( "<br />", array(
				__( 'Choose how to manage the purchase of products when no stock value has been set for a specific store.', 'yith-point-of-sale-for-woocommerce' ),
				__( "When you enable the multi-stock option, you'll have to set the product stock for every store. If one of the store stock is not specified, then, this settings will apply.", 'yith-point-of-sale-for-woocommerce' )
			) ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'id'        => 'yith_pos_multistock_condition',
			'options'   => array(
				'allowed'     => __( 'purchasable without stock management', 'yith-point-of-sale-for-woocommerce' ),
				'general'     => __( 'purchasable from the general stock', 'yith-point-of-sale-for-woocommerce' ),
				'not_allowed' => __( 'non-purchasable', 'yith-point-of-sale-for-woocommerce' ),
			),
			'default'   => 'not_allowed',
			'deps'      => array(
				'id'    => 'yith_pos_multistock_enabled',
				'value' => 'yes'
			)
		),


		'settings_stock_end' => array(
			'type' => 'sectionend',
		),

		'settings_color_start'                                => array(
			'name' => __( 'Color Settings', 'yith-point-of-sale-for-woocommerce' ),
			'type' => 'title'
		),
		'settings_registers_colors_primary'                   => array(
			'name'      => __( 'Primary color', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the primary color for Registers', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'yith_pos_registers_primary',
			'default'   => '#09adaa',
		),
		'settings_registers_colors_secondary'                 => array(
			'name'      => __( 'Secondary color', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the secondary color for Registers', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'yith_pos_registers_secondary',
			'default'   => '#c65338',
		),
		'settings_registers_colors_products_background'       => array(
			'name'      => __( 'Products grid background', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the background color for products grid', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'yith_pos_registers_products_background',
			'default'   => '#eaeaea',
		),
		'settings_registers_colors_products_title_background' => array(
			'name'      => __( 'Product title background', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the background color for product title', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'yith_pos_registers_products_title_background',
			'default'   => 'rgba(67, 67, 67, .75)',
		),
		'settings_registers_colors_save_cart_background'      => array(
			'name'      => __( 'Saved cart buttons background', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the background color for saved cart buttons', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'yith_pos_registers_save_cart_background',
			'default'   => '#e09914',
		),
		'settings_registers_colors_pay_button_background'     => array(
			'name'      => __( 'Pay button background', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the background color for pay button', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'yith_pos_registers_pay_button_background',
			'default'   => '#a0a700',
		),
		'settings_registers_colors_note_button_background'    => array(
			'name'      => __( 'Note buttons background', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the background color for note button', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'yith_pos_registers_note_button_background',
			'default'   => '#4d4d4d',
		),
		'settings_registers_colors_header_bar_background'     => array(
			'name'      => __( 'Header background', 'yith-point-of-sale-for-woocommerce' ),
			'desc'      => __( 'Select the background color for header', 'yith-point-of-sale-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'id'        => 'yith_pos_registers_header_bar_background',
			'default'   => '#435756',
		),
		'settings_color_end'                                  => array(
			'type' => 'sectionend',
		),

	),
);

return apply_filters( 'yith_pos_panel_settings_tab', $settings );