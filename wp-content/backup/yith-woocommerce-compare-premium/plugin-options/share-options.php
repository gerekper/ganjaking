<?php
/**
 * SHARE OPTIONS ARRAY
 *
 * @author  Your Inspiration Themes
 * @package YITH Woocommerce Compare Premium
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

$options = array(
	'share' => array(

		array(
			'title' => __( 'Share Options', 'yith-woocommerce-compare' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-woocompare-share-options',
		),

		array(
			'id'        => 'yith-woocompare-enable-share',
			'title'     => __( 'Enable Sharing', 'yith-woocommerce-compare' ),
			'desc'      => __( 'Check this option if you want to show the link to share the compare', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
		),

		array(
			'title'             => __( 'Show sharing in', 'yith-woocommerce-compare' ),
			'desc'              => __( 'Popup', 'yith-woocommerce-compare' ),
			'id'                => 'yith-woocompare-share-in-popup',
			'default'           => 'yes',
			'type'              => 'checkbox',
			'checkboxgroup'     => 'start',
			'custom_attributes' => array(
				'data-deps' => 'yith-woocompare-enable-share',
			),
		),

		array(
			'id'            => 'yith-woocompare-share-in-page',
			'desc'          => __( 'Page', 'yith-woocommerce-compare' ),
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'end',
		),

		array(
			'id'        => 'yith-woocompare-share-socials',
			'title'     => __( 'Select Social Network Sites', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'multiple'  => true,
			'options'   => array(
				'facebook'  => __( 'Facebook', 'yith-woocommerce-compare' ),
				'twitter'   => __( 'Twitter', 'yith-woocommerce-compare' ),
				'pinterest' => __( 'Pinterest', 'yith-woocommerce-compare' ),
				'mail'      => __( 'eMail', 'yith-woocommerce-compare' ),
			),
			'class'     => 'yith-woocompare-chosen',
			'default'   => array( 'facebook', 'twitter', 'pinterest', 'mail' ),
			'deps'      => array(
				'id'    => 'yith-woocompare-enable-share',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'id'        => 'yith_woocompare_socials_title',
			'title'     => __( 'Title for Social Network Sharing', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => __( 'My Compare', 'yith-woocommerce-compare' ),
			'deps'      => array(
				'id'    => 'yith-woocompare-enable-share',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'id'        => 'yith_woocompare_socials_text',
			'title'     => __( 'Text for Social Network Sharing', 'yith-woocommerce-compare' ),
			'desc'      => __( 'It will be used on Facebook, Twitter and Pinterest. Use %compare_url% where you want to show the URL of your compare.', 'yith-woocommerce-compare' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea',
			'default'   => '',
			'deps'      => array(
				'id'    => 'yith-woocompare-enable-share',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'id'        => 'yith_woocompare_facebook_appid',
			'title'     => __( 'Facebook App ID', 'yith-woocommerce-compare' ),
			'desc'      => sprintf( __( 'Facebook App ID necessary to share contents. Read more in the official Facebook <a href="%s">documentation</a>', 'yith-woocommerce-compare' ), 'https://developers.facebook.com/docs/apps/register' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '',
			'deps'      => array(
				'id'    => 'yith-woocompare-enable-share',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'title'     => __( 'Facebook image', 'yith-woocommerce-compare' ),
			'desc'      => __( 'Select an image for Facebook sharing.', 'yith-woocommerce-compare' ),
			'id'        => 'yith_woocompare_facebook_image',
			'default'   => '',
			'type'      => 'yith-field',
			'yith-type' => 'upload',
			'deps'      => array(
				'id'    => 'yith-woocompare-enable-share',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-woocompare-share-options-end',
		),
	),
);

return apply_filters( 'yith_woocompare_share_settings', $options );
