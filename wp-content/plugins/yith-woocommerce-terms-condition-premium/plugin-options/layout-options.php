<?php
/**
 * General settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Terms & Conditions
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WCTC' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters(
	'yith_wctc_layout_settings',
	array(
		'layout' => array(

			'popup-options' => array(
				'title' => __( 'Popup Layout', 'yith-woocommerce-terms-conditions' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'yith_wctc_popup_options'
			),

			'popup-round-corners' => array(
				'id'        => 'yith_wctc_popup_round_corners',
				'name'      => __( 'Round Popup Corners', 'yith-woocommerce-terms-conditions' ),
				'type'      => 'checkbox',
				'default'   => 'yes'
			),

			'popup-background-color' => array(
				'id'      => 'yith_wctc_popup_background_color',
				'name'    => __( 'Modal Window Background Color', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'color',
				'desc'    => '',
				'default' => '#ffffff'
			),

			'popup-overlay-color' => array(
				'id'      => 'yith_wctc_popup_overlay_color',
				'name'    => __( 'Modal Overlay Background Color', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'color',
				'desc'    => '',
				'default' => '#000000'
			),

			'popup-overlay-opacity' => array(
				'id'        => 'yith_wctc_popup_overlay_opacity',
				'name'      => __( 'Popup overlay opacity', 'yith-woocommerce-terms-conditions' ),
				'type'      => 'text',
				'desc'      => __( 'Popup overlay opacity (between 0 and 1)', 'yith-woocommerce-terms-conditions' ),
				'default'   => 1,
				'css'       => 'min-width:300px;'
			),

			'popup-close-button-color' => array(
				'id'      => 'yith_wctc_popup_close_button_color',
				'name'    => __( 'Modal Close Button Background Color', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'color',
				'desc'    => '',
				'default' => '#000000'
			),

			'popup-close-button-opacity' => array(
				'id'        => 'yith_wctc_popup_close_button_opacity',
				'name'      => __( 'Popup Close Button opacity', 'yith-woocommerce-terms-conditions' ),
				'type'      => 'text',
				'desc'      => __( 'Popup Close Button opacity (between 0 and 1)', 'yith-woocommerce-terms-conditions' ),
				'default'   => 1,
				'css'       => 'min-width:300px;'
			),

			'popup-close-button-style' => array(
				'id'      => 'yith_wctc_popup_close_button_style',
				'name'    => __( 'Popup close button style', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'select',
				'options' => array(
					'big'         => __( 'Big', 'yith-woocommerce-terms-conditions' ),
					'small'       => __( 'Small', 'yith-woocommerce-terms-conditions' ),
					'square'      => __( 'Square', 'yith-woocommerce-terms-conditions' ),
					'round-big'   => __( 'Round big', 'yith-woocommerce-terms-conditions' ),
					'round-small' => __( 'Round small', 'yith-woocommerce-terms-conditions' ),
				),
				'default'   => 'big',
				'css'       => 'min-width:300px;'
			),

			'popup-loading-image' => array(
				'id'      => 'yith_wctc_popup_loading_image',
				'name'    => __( 'Loading image', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'select',
				'options' => array(
					'circle' => __( 'Circle', 'yith-woocommerce-terms-conditions' ),
					'dots'   => __( 'Dots', 'yith-woocommerce-terms-conditions' ),
					'ios'    => __( 'Ios', 'yith-woocommerce-terms-conditions' ),
					'quads'  => __( 'Quads', 'yith-woocommerce-terms-conditions' )
				),
				'default' => 'circle',
				'css'       => 'min-width:300px;'
			),

			'popup-effect' => array(
				'id'      => 'yith_wctc_popup_effect',
				'name'    => __( 'Popup show effect', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'select',
				'options' => array(
					'0' => __( 'None', 'yith-woocommerce-terms-conditions' ),
					'1' => __( 'Huge Inc', 'yith-woocommerce-terms-conditions' ),
					'2' => __( 'Corner', 'yith-woocommerce-terms-conditions' ),
					'3' => __( 'Slide down', 'yith-woocommerce-terms-conditions' ),
					'4' => __( 'Scale', 'yith-woocommerce-terms-conditions' ),
					'5' => __( 'Little genie', 'yith-woocommerce-terms-conditions' ),
				),
				'css'       => 'min-width:300px;',
				'default' => 0
			),

			'popup-options-end' => array(
				'type'  => 'sectionend',
				'id'    => 'yith_wctc_popup_options'
			),

			'agree-button-options' => array(
				'title' => __( '"I Agree" Button Layout', 'yith-woocommerce-terms-conditions' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'yith_wctc_agree_button_options'
			),

			'agree-button-alignment' => array(
				'id'      => 'yith_wctc_agree_button_alignment',
				'name'    => __( 'Button alignment', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'select',
				'options' => array(
					'left' => __( 'Left', 'yith-woocommerce-terms-conditions' ),
					'center' => __( 'Center', 'yith-woocommerce-terms-conditions' ),
					'right' => __( 'Right', 'yith-woocommerce-terms-conditions' )
				),
				'default' => 'right',
				'css'       => 'min-width:300px;'
			),

			'agree-button-type' => array(
				'id'      => 'yith_wctc_agree_button_type',
				'name'    => __( '"I Agree" button type', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'select',
				'options' => array(
					'button' => __( 'Button', 'yith-woocommerce-terms-conditions' ),
					'anchor' => __( 'Anchor', 'yith-woocommerce-terms-conditions' )
				),
				'default' => 'button',
				'css'       => 'min-width:300px;'
			),

			'agree-button-style' => array(
				'id'      => 'yith_wctc_agree_button_style',
				'name'    => __( '"I Agree" button style', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'select',
				'options' => array(
					'woocommerce' => __( 'WooCommerce', 'yith-woocommerce-terms-conditions' ),
					'custom' => __( 'Custom', 'yith-woocommerce-terms-conditions' )
				),
				'default' => 'woocommerce',
				'css'       => 'min-width:300px;'
			),

			'agree-button-corners' => array(
				'id'        => 'yith_wctc_agree_button_round_corners',
				'name'      => __( 'Round Corners for "I Agree" Button', 'yith-woocommerce-terms-conditions' ),
				'type'      => 'checkbox',
				'default'   => 'yes'
			),

			'agree-button-background-color' => array(
				'id'      => 'yith_wctc_agree_button_background_color',
				'name'    => __( '"I Agree" Button Background Color', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'color',
				'desc'    => '',
				'default' => '#ebe9eb'
			),

			'agree-button-color' => array(
				'id'      => 'yith_wctc_agree_button_color',
				'name'    => __( '"I Agree" Button Text Color', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'color',
				'desc'    => '',
				'default' => '#515151'
			),

			'agree-button-border-color' => array(
				'id'      => 'yith_wctc_agree_button_border_color',
				'name'    => __( '"I Agree" Button Border Color', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'color',
				'desc'    => '',
				'default' => '#ebe9eb'
			),

			'agree-button-background-color-hover' => array(
				'id'      => 'yith_wctc_agree_button_background_hover_color',
				'name'    => __( '"I Agree" Button Hover Background Color', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'color',
				'desc'    => '',
				'default' => '#dad8da'
			),

			'agree-button-color-hover' => array(
				'id'      => 'yith_wctc_agree_button_hover_color',
				'name'    => __( '"I Agree" Button Hover Text Color', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'color',
				'desc'    => '',
				'default' => '#515151'
			),

			'agree-button-border-color-hover' => array(
				'id'      => 'yith_wctc_agree_button_border_hover_color',
				'name'    => __( '"I Agree" Button Hover Border Color', 'yith-woocommerce-terms-conditions' ),
				'type'    => 'color',
				'desc'    => '',
				'default' => '#dad8da'
			),

			'agree-button-options-end' => array(
				'type'  => 'sectionend',
				'id'    => 'yith_wctc_agree_button_options'
			),

		)
	)
);