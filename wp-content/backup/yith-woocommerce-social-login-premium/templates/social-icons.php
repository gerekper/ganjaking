<?php
/**
 * Only icons for socials
 *
 * @package YITH WooCommerce Social Login
 * @since   1.0.0
 * @author  YITH
 */

foreach ( $socials as $key => $value ) {
	$enabled = get_option( 'ywsl_' . $key . '_enable' );

	if ( $enabled == 'yes' ) {

		$social_args = array(
			'value'     => $value,
			'image_url' => apply_filters( 'ywsl_custom_icon_' . $key, YITH_YWSL_ASSETS_URL . '/images/' . $key . '.png', $key ),
			'class'     => 'ywsl-social ywsl-' . $key
		);

		$social_args = apply_filters( 'yith_wc_social_login_args', $social_args );

		$image  = sprintf( '<img src="%s" alt="%s"/>', $social_args['image_url'], isset( $value['label'] ) ? $value['label'] : $value );
		$social = sprintf( '<div class="%s" data-social="%s">%s</div>', $social_args['class'], strtolower( $value['label'] ), $image );

		echo apply_filters( 'yith_wc_social_login_icon', $social, $key, $social_args );

	}
}