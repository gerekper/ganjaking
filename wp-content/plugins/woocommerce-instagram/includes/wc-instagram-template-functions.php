<?php
/**
 * Template functions
 *
 * @package WC_Instagram/Functions
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues scripts and styles.
 *
 * @since 2.0.0
 */
function wc_instagram_enqueue_scripts() {
	if ( ! wc_instagram_has_business_account() || ! is_product() ) {
		return;
	}

	// Add styles for the compatible themes.
	if ( 'storefront' === get_template() ) {
		$theme = wp_get_theme( 'storefront' );

		// Styles no longer necessary for Storefront 2.5.5 or higher.
		if ( $theme && version_compare( $theme['Version'], '2.5.5', '<' ) ) {
			wp_enqueue_style( 'wc-instagram-storefront-styles', WC_INSTAGRAM_URL . 'assets/css/themes/storefront.css', array( 'storefront-style' ), WC_INSTAGRAM_VERSION );
		}
	}
}

/**
 * Sets up the Instagram loop from the passed args.
 *
 * Inspired by the `wc_setup_loop` function.
 *
 * @since 2.0.0
 *
 * @global array $wc_instagram_loop The Instagram loop.
 *
 * @param array $args Optional. Additional arguments.
 */
function wc_instagram_setup_loop( $args = array() ) {
	global $wc_instagram_loop;

	$default_args = array(
		'loop'    => 0,
		'columns' => wc_instagram_get_columns(),
		'name'    => '',
	);

	// Merge any existing values.
	if ( is_array( $wc_instagram_loop ) ) {
		$default_args = array_merge( $default_args, $wc_instagram_loop );
	}

	$wc_instagram_loop = wp_parse_args( $args, $default_args );
}

/**
 * Sets a property in the Instagram loop.
 *
 * Inspired by the `wc_set_loop_prop` function.
 *
 * @since 2.0.0
 *
 * @global array $wc_instagram_loop The Instagram loop.
 *
 * @param string $prop  Prop to set.
 * @param string $value Optional. Value to set.
 */
function wc_instagram_set_loop_prop( $prop, $value = '' ) {
	global $wc_instagram_loop;

	if ( ! $wc_instagram_loop ) {
		wc_instagram_setup_loop();
	}

	$wc_instagram_loop[ $prop ] = $value;
}

/**
 * Gets a property from the Instagram loop.
 *
 * Inspired by the `wc_get_loop_prop` function.
 *
 * @since 2.0.0
 *
 * @global array $wc_instagram_loop The Instagram loop.
 *
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function wc_instagram_get_loop_prop( $prop, $default = '' ) {
	global $wc_instagram_loop;

	wc_instagram_setup_loop(); // Ensure loop is set up.

	return ( ( is_array( $wc_instagram_loop ) && ! empty( $wc_instagram_loop[ $prop ] ) ) ? $wc_instagram_loop[ $prop ] : $default );
}

/**
 * Gets item class for Instagram loops.
 *
 * Inspired by the `wc_get_loop_class` function.
 *
 * @since 2.0.0
 *
 * @return string
 */
function wc_instagram_get_loop_class() {
	$loop_index = wc_instagram_get_loop_prop( 'loop', 0 );
	$columns    = absint( max( 1, wc_instagram_get_loop_prop( 'columns' ) ) );

	$loop_index ++;
	wc_instagram_set_loop_prop( 'loop', $loop_index );

	if ( 0 === ( $loop_index - 1 ) % $columns || 1 === $columns ) {
		return 'first';
	}

	if ( 0 === $loop_index % $columns ) {
		return 'last';
	}

	return '';
}

/**
 * Outputs the classes for the container of an Instagram image.
 *
 * @since 2.0.0
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 */
function wc_instagram_image_class( $class = '' ) {
	$classes = array(
		'wc-instagram-loop-item',
		'instagram', // Backward compatibility.
		'product',
		wc_instagram_get_loop_class(),
	);

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}

		$classes = array_merge( $classes, array_map( 'esc_attr', $class ) );
	}

	/**
	 * Filters the classes for the container of an Instagram image.
	 *
	 * @since 2.0.0
	 *
	 * @param array $classes The classes of the image.
	 */
	$classes = apply_filters( 'wc_instagram_image_class', $classes );

	echo 'class="' . esc_attr( trim( join( ' ', $classes ) ) ) . '"';
}

if ( ! function_exists( 'wc_instagram_product_hashtag' ) ) {
	/**
	 * Outputs the Instagram hashtag section for the current product.
	 *
	 * @since 2.0.0
	 *
	 * @global WC_Product $product The current product.
	 */
	function wc_instagram_product_hashtag() {
		global $product;

		if ( ! $product || ! wc_instagram_has_business_account() ) {
			return;
		}

		$hashtag = wc_instagram_get_product_hashtag( $product->get_id() );

		if ( ! $hashtag ) {
			return;
		}

		$args = array(
			'hashtag' => $hashtag,
			'columns' => wc_instagram_get_columns( 'product_hashtag' ),
			'images'  => wc_instagram_get_product_hashtag_images( $product->get_id() ),
		);

		// Set global loop values.
		wc_instagram_set_loop_prop( 'name', 'product_hashtag' );
		wc_instagram_set_loop_prop( 'columns', $args['columns'] );

		wc_instagram_get_template( 'single-product/instagram.php', $args );
	}
}

if ( ! function_exists( 'wc_instagram_loop_start' ) ) {
	/**
	 * Outputs the start of an Instagram loop.
	 *
	 * @since 2.0.0
	 */
	function wc_instagram_loop_start() {
		wc_instagram_get_template( 'instagram/loop-start.php' );
	}
}

if ( ! function_exists( 'wc_instagram_loop_end' ) ) {
	/**
	 * Outputs the end of an Instagram loop.
	 *
	 * @since 2.0.0
	 */
	function wc_instagram_loop_end() {
		wc_instagram_get_template( 'instagram/loop-end.php' );
	}
}

if ( ! function_exists( 'wc_instagram_image' ) ) {
	/**
	 * Outputs the Instagram image.
	 *
	 * @since 2.0.0
	 *
	 * @param array $image An array with the Instagram image data.
	 */
	function wc_instagram_image( $image ) {
		wc_instagram_get_template( 'instagram/image.php', array( 'image' => $image ) );
	}
}
