<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$setting = array(

	'settings' => array(
		'section_general_settings' => array(
			'name' => __( 'General settings', 'yith-donations-for-woocommerce' ),
			'type' => 'title',
			'id'   => 'ywcds_section_general'
		),


		'show_donation_in_cart' => array(
			'name'    => __( 'Show in Cart ', 'yith-donations-for-woocommerce' ),
			'desc'    => __( 'Show donation form in cart', 'yith-donations-for-woocommerce' ),
			'type'    => 'yith-field',
			'yith-type' => 'onoff',
			'std'     => 'no',
			'default' => 'no',
			'id'      => 'ywcds_show_donation_in_cart'
		),
		'multi_amount' => array(
			'name' =>__( 'Donation pre-set amounts', 'yith-donations-for-woocommerce' ),
			'desc' => __( 'Enter the available donation amounts that your users can choose from. Separate values with | .', 'yith-donations-for-woocommerce' ),
			'type' => 'yith-field',
			'yith-type' => 'text',
			'id' => 'ywcds_multi_amount',
			'deps' => array(
				'id' => 'ywcds_show_donation_in_cart',
				'value' => 'yes',
				'type' => 'disable'
			)
			),

		'multi_amount_style' => array(
			'name' =>__( 'Style', 'yith-donations-for-woocommerce' ),
			'desc' => __( 'If you\'ve entered pre-set amounts, choose how to display them, either with labels or radio buttons.', 'yith-donations-for-woocommerce' ),
			'type' => 'yith-field',
			'yith-type' => 'select',
			'id' => 'ywcds_multi_amount_style',
			'deps' => array(
				'id' => 'ywcds_show_donation_in_cart',
				'value' => 'yes',
				'type' => 'disable'
			),
			'options' => array(
				'radio' => __( 'Radio Button', 'yith-donations-for-woocommerce' ),
				'label'    => __( 'Label', 'yith-donations-for-woocommerce' )
			),
			'default' => 'label',
			),
		'show_donation_reference' => array(
			'name' => __( 'Show an extra field in the donation form','yith-donations-for-woocommerce'),
			'type' => 'yith-field',
			'yith-type' => 'onoff',
			'id' => 'ywcds_show_donation_reference',
			'default' => 'no',
			'deps' => array(
				'id' => 'ywcds_show_donation_in_cart',
				'value' => 'yes',
				'type' => 'disable'
			),

		),

		'text_extra_field' => array(
			'name' => __( 'Extra field label', 'yith-donations-for-woocommerce' ),
			'desc' => __( 'This text appears before the extra field', 'yith-donations-for-woocommerce' ),
			'type' => 'yith-field',
			'yith-type' => 'text',
			'id' => 'ywcds_text_extra_field',
			'default' => __('Why you are making a donation', 'yith-donations-for-woocommerce' ),
			'deps' => array(
				'id' => 'ywcds_show_donation_reference',
				'value' => 'yes',
				'type' => 'hide'
			),
		),

		'min_donation' => array(
			'name'              => __( 'Minimun Donation Required', 'yith-donations-for-woocommerce' ),
			'type'              => 'number',
			'id'                => 'ywcds_min_donation',
			'custom_attributes' => array(
				'min' => 0,
				'step' => 'any'
			),
			'std'               => 10,
			'default'           => 10,

		),

		'max_donation' => array(
			'name'              => __( 'Maximun Donation Allowed', 'yith-donations-for-woocommerce' ),
			'type'              => 'number',
			'id'                => 'ywcds_max_donation',
			'custom_attributes' => array(
				'min' => 0,
				'step' => 'any'
			),
			'std'               => 100,
			'default'           => 100
		),

		'select_gateway' => array(
			'name'      => __( 'Payment method', 'yith-donations-for-woocommerce' ),
			'desc'      => __( 'Select payment method for donations', 'yith-donations-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'id'        => 'ywcds_select_gateway',
			'options'   => ywcds_get_gateway(),
			'multiple'  => true,
			'default'   => array(),

		),

		'link_product_donation' => array(
			'name'        => '',
			'desc'        => '',
			'type'        => 'yith-field',
			'yith-type'   => 'donation-product-link',
			'link_text'   => __( 'click here', 'yith-donations-for-woocommerce' ),
			'before_text' => __( 'To let the plugin work correctly, a special product has been created to let you manage your donations. ', 'yith-donations-for-woocommerce' ),
			'after_text'  => __( ' Show it', 'yith-donations-for-woocommerce' ),
			'post_id'     => get_option( '_ywcds_donation_product_id' ),
			'id'          => 'ywcds_product_link'
		),

		'section_general_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywcds_section_general_end'
		),

		'section_button_settings' => array(
			'name' => __( 'Button settings', 'yith-donations-for-woocommerce' ),
			'type' => 'title',
			'id'   => 'ywcds_button_general'
		),

		'button_text' => array(
			'name'    => __( 'Button Text', 'yith-donations-for-woocommerce' ),
			'desc'    => __( 'Set a text for your donation button', 'yith-donations-for-woocommerce' ),
			'type'    => 'text',
			'std'     => __( 'Add Donation', 'yith-donations-for-woocommerce' ),
			'default' => __( 'Add Donation', 'yith-donations-for-woocommerce' ),
			'id'      => 'ywcds_button_text',
		),

		'button_style_select' => array(
			'name'    => __( 'Button Style', 'yith-donations-for-woocommerce' ),
			'type'    => 'select',
			'options' => array(
				'wc'     => __( 'WooCommerce Style', 'yith-donations-for-woocommerce' ),
				'custom' => __( 'Custom Style', 'yith-donations-for-woocommerce' ),
			),

			'default' => 'wc',
			'id'      => 'ywcds_button_style_select',
		),

		'button_typography' => array(
			'name'      => __( 'Button Typography', 'yith-donations-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'donation-button-typography',
			'id'        => 'ywcds_button_typography',
			'default'   => array(
				'size'      => 13,
				'unit'      => 'px',
				'style'     => 'regular',
				'transform' => 'uppercase',
			)
		),

		'button_text_color' => array(
			'name'      => __( 'Button Text Color', 'yith-donations-for-woocommerce' ),
			'type'      => 'color',
			'id'        => 'ywcds_text_color',
			'std'       => '#fff',
			'default'   => '#fff'
		),

		'button_bg_color' => array(
			'name'    => __( 'Button Background Color', 'yith-donations-for-woocommerce' ),
			'type'    => 'color',
			'id'      => 'ywcds_bg_color',
			'std'     => '#333',
			'default' => '#333'
		),

		'button_text_hov_color' => array(
			'name'    => __( 'Button Text Hover Color', 'yith-donations-for-woocommerce' ),
			'type'    => 'color',
			'id'      => 'ywcds_text_hov_color',
			'std'     => '#333',
			'default' => '#333'
		),

		'button_bg_hov_color' => array(
			'name'    => __( 'Button Background Hover Color', 'yith-donations-for-woocommerce' ),
			'type'    => 'color',
			'id'      => 'ywcds_bg_hov_color',
			'std'     => '#fff',
			'default' => '#fff'
		),

		'section_button_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywcds_section_button_end'
		),

	)
);

return apply_filters( 'yith_wc_donations_settings', $setting );