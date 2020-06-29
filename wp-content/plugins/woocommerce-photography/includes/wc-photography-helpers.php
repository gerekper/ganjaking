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
		'public'     => __( 'Public', 'woocommerce-photography' )
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
