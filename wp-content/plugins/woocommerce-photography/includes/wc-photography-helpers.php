<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * i18n collections visibility.
 *
 * @param  int $collection_id
 *
 * @return string
 */
function wc_photography_i18n_collection_visibility( $collection_id, $visibility = '' ) {
	if ( ! $visibility ) {
		$visibility = WC_Photography_WC_Compat::get_term_meta( $collection_id, 'visibility', true );
	}

	$i18n = array(
		'restricted' => __( 'Restricted', 'woocommerce-photography' ),
		'public'     => __( 'Public', 'woocommerce-photography' ),
	);

	if ( isset( $i18n[ $visibility ] ) ) {
		return $i18n[ $visibility ];
	}

	return $visibility;
}

/**
 * Is collection public.
 *
 * @param  int $collection_id
 *
 * @return bool
 */
function wc_photography_is_collection_public( $collection_id ) {
	$visibility = WC_Photography_WC_Compat::get_term_meta( $collection_id, 'visibility', true );

	return ( 'public' == $visibility );
}

/**
 * Clear cache when a collection is added/updated.
 *
 * @since 1.0.24
 *
 * @return void
 */
function wc_photography_clear_collection_cache() {
	delete_transient( 'woocommerce_photography_restricted_collections' );
}

/**
 * Gets the product title for the specified image ID.
 *
 * @since 1.1.0
 *
 * @param  int   $image_id    Attachment ID.
 * @param  array $collections Optional. The collections to add the image. Default empty.
 * @return string
 */
function wc_photography_get_product_title( $image_id, $collections = array() ) {
	$image_metadata = wp_get_attachment_metadata( $image_id );

	// Get settings and image text option.
	$settings            = get_option( 'woocommerce_photography', array() );
	$settings_image_text = isset( $settings['image_text_option'] ) ? $settings['image_text_option'] : 'image_id';

	if ( 'filename' === $settings_image_text ) {
		// Get image filename without extension.
		$image_path_parts = pathinfo( $image_metadata['file'] );
		$image_filename   = $image_path_parts['filename'];
		$image_text       = $image_filename;
	} else {
		$image_text = $image_id;
	}

	if ( ! empty( $image_metadata['image_meta']['title'] ) ) {
		$title = $image_metadata['image_meta']['title'];
	} else {
		$collection = ( ! empty( $collections ) ? current( $collections ) : '' );

		if ( $collection ) {
			/* translators: 1: image ID 2: first collection name */
			$string = __( 'Photography #%1$d from %2$s', 'woocommerce-photography' );
		} else {
			/* translators: 1: image ID */
			$string = __( 'Photography #%d', 'woocommerce-photography' );
		}

		if ( 'filename' === $settings_image_text ) {
			// Avoid customers from translating the string again.
			$string = str_replace( array( '#%1$d', '#%d' ), array( '%1$s', '%s' ), $string );
		}

		$title = sprintf( $string, $image_text, $collection );
	}

	/**
	 * Filters the title of a photography product.
	 *
	 * @since 1.0.30
	 *
	 * @param string $title       The title.
	 * @param int    $image_id    Image ID.
	 * @param array  $collections The collections to add the image.
	 */
	return apply_filters( 'wc_photography_product_title', $title, $image_id, $collections );
}
