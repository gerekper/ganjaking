<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 **/

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

return array(

	'socialbox' => apply_filters(
		'yith_ctpw_socialbox_options',
		array(

			'socialbox_options_start'                    => array(
				'type' => 'sectionstart',
			),

			'socialbox_options_title'                    => array(
				'title' => esc_html_x( 'Social Box Settings', 'Section title in Settings', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => '',
			),

			'socialbox_enable_socialbox'                 => array(
				'title'     => esc_html_x( 'Enable Social Box', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Check this option to show the social sharing section', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_enable_social_box',
				'default'   => 'yes',
			),

			'socialbox_enable_fb_socialbox'              => array(
				'title'     => esc_html_x( 'Enable Facebook', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Check this option to show Facebook sharing button', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_enable_fb_social_box',
				'default'   => 'yes',
				'deps'      => array(
					'id'    => 'yith_ctpw_enable_social_box',
					'value' => 'yes',
					'type'  => 'disable',
				),
			),

			'socialbox_enable_twitter_socialbox'         => array(
				'title'     => esc_html_x( 'Enable Twitter', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Check this option to show Twitter sharing button', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_enable_twitter_social_box',
				'default'   => 'yes',
				'deps'      => array(
					'id'    => 'yith_ctpw_enable_social_box',
					'value' => 'yes',
					'type'  => 'disable',
				),
			),

			'socialbox_enable_pinterest_socialbox'       => array(
				'title'     => esc_html_x( 'Enable Pinterest', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => esc_html_x( 'Check this option to show the Pinterest sharing button', 'Admin option description', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'yith_ctpw_enable_pinterest_social_box',
				'default'   => 'yes',
				'deps'      => array(
					'id'    => 'yith_ctpw_enable_social_box',
					'value' => 'yes',
					'type'  => 'disable',
				),
			),

			'socialbox_options_end'                      => array(
				'type' => 'sectionend',
			),

			// url shortener settings.
			'ctpw_shorturl_options_sbox_start'           => array(
				'type' => 'sectionstart',
			),

			'ctpw_shorturl_options_sbox_title'           => array(
				'title' => esc_html_x( 'URL Shortening Settings ', 'Panel: page title', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => '',
			),

			'ctpw_shorturl_options_sbox_service_select'  => array(
				'name'      => esc_html__( 'URL shortening service', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'id'        => 'ctpw_url_shortening',
				'options'   => array(
					'none'   => esc_html__( 'None', 'yith-custom-thankyou-page-for-woocommerce' ),
					'google' => esc_html__( 'Google', 'yith-custom-thankyou-page-for-woocommerce' ),
					'bitly'  => esc_html__( 'Bitly', 'yith-custom-thankyou-page-for-woocommerce' ),
				),
				'default'   => 'none',
				'desc'      => esc_html_x( 'Select the tool to shorten your URLs', 'Admin option', 'yith-custom-thankyou-page-for-woocommerce' ),
			),

			'ctpw_google_api_key'                        => array(
				'name'              => esc_html__( 'Google API Key', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'              => 'text',
				'id'                => 'ctpw_google_api_key',
				'css'               => 'width: 50%',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),

			'ctpw_bitly_api_key'                         => array(
				'name'              => esc_html__( 'Bitly API Key', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'              => 'text',
				'id'                => 'ctpw_bitly_access_token',
				'css'               => 'width: 50%',
				'custom_attributes' => array(
					'required' => 'required',
				),

			),

			'ctpw_shorturl_options_sbox_end'             => array(
				'type' => 'sectionend',
			),

			// box title style.
			'ctpw_cstyles_options_sbox_start'            => array(
				'type' => 'sectionstart',
			),

			'ctpw_cstyles_options_sbox_title'            => array(
				'title' => esc_html_x( 'Social Box Style ', 'Panel: page title', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => '',
			),

			'ctpw_styles_options_sbox_title_color'       => array(
				'title'     => esc_html__( 'Box title font color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_social_box_title_color',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#000000',
			),

			'ctpw_styles_options_sbox_title_font_size'   => array(
				'title'     => esc_html__( 'Box title font size', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'number',
				'default'   => 20,
				'id'        => 'ctpw_social_box_title_fontsize',
				'min'       => 10,
				'max'       => 50,
				'step'      => 1,

			),

			'ctpw_styles_options_sbox_title_font_weight' => array(
				'title'     => esc_html__( 'Box title font weight', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'default'   => 'bold',
				'id'        => 'ctpw_social_box_title_fontweight',
				'options'   => array(
					'lighter' => 'Lighter',
					'normal'  => 'Normal',
					'bold'    => 'Bold',
					'bolder'  => 'Bolder',
				),
			),

			'ctpw_styles_options_sbox_socials_titles_color' => array(
				'title'     => esc_html__( 'Social font color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_socials_titles_color',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#ffffff',
			),

			'ctpw_styles_options_sbox_socials_titles_color_hover' => array(
				'title'     => esc_html__( 'Social font hover color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_socials_titles_color_hover',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#6d6d6d',

			),

			'ctpw_styles_options_sbox_socials_titles_color_active' => array(
				'title'     => esc_html__( 'Social font active color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_socials_titles_color_active',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#dc446e',

			),
			'ctpw_styles_options_sbox_socials_titles_color_active_hover' => array(
				'title'     => esc_html__( 'Social font active color on hover', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_socials_titles_color_active_hover',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#dc446e',

			),

			'ctpw_styles_options_sbox_socials_main_background_selected' => array(
				'title'     => esc_html__( 'Selected tab background color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_socials_box_main_background_selected',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#e7e7e7',

			),

			'ctpw_styles_options_sbox_socials_main_background' => array(
				'title'     => esc_html__( 'Tab main background color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_socials_box_main_background',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#b3b3b3',

			),

			'ctpw_styles_options_sbox_socials_arrow_box_color' => array(
				'title'     => esc_html__( 'Arrow box color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_socials_box_arrow_box_color',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#b3b3b3',

			),

			'ctpw_styles_options_sbox_socials_button_color' => array(
				'title'     => esc_html__( 'Sharing button color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_socials_box_button_color',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#b3b3b3',

			),

			'ctpw_styles_options_sbox_share_button_title_font_size' => array(
				'title'     => esc_html__( 'Sharing button font size', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'      => 'yith-field',
				'yith-type' => 'number',
				'default'   => 15,
				'id'        => 'ctpw_social_box_button_title_fontsize',
				'min'       => 10,
				'max'       => 50,
				'step'      => 1,
			),

			'ctpw_styles_options_sbox_socials_button_font_color' => array(
				'title'     => esc_html__( 'Sharing button font color', 'yith-custom-thankyou-page-for-woocommerce' ),
				'id'        => 'ctpw_socials_box_button_fontcolor',
				'type'      => 'yith-field',
				'yith-type' => 'colorpicker',
				'default'   => '#ffffff',

			),

			'ctpw_cstyles_options_sbox_end'              => array(
				'type' => 'sectionend',
			),

		)
	),
);
