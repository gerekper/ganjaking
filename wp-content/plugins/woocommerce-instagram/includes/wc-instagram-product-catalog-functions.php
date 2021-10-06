<?php
/**
 * Product Catalog functions
 *
 * @package WC_Instagram/Functions
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the slug of the rewrite rule used by the product catalogs.
 *
 * @since 3.0.0
 *
 * @return string
 */
function wc_instagram_get_product_catalog_rewrite_slug() {
	$rewrite_slug = wc_instagram_get_setting( 'product_catalog_permalink', 'product-catalog' );
	$rewrite_slug = untrailingslashit( $rewrite_slug );

	/**
	 * Filters the slug of the rewrite rule used by the product catalogs.
	 *
	 * @since 3.0.0
	 *
	 * @pram string $rewrite_slug The rewrite slug.
	 */
	return apply_filters( 'wc_instagram_product_catalog_rewrite_slug', $rewrite_slug );
}

/**
 * Gets the product catalogs.
 *
 * @since 3.0.0
 *
 * @return array
 */
function wc_instagram_get_product_catalogs() {
	$catalogs = get_option( 'wc_instagram_product_catalogs', array() );

	if ( ! is_array( $catalogs ) ) {
		$catalogs = array();
	}

	return $catalogs;
}

/**
 * Gets the product catalog.
 *
 * @since 3.0.0
 *
 * @param mixed $the_catalog Product catalog object, ID, slug or data.
 * @return WC_Instagram_Product_Catalog|false A Product Catalog object. False otherwise.
 */
function wc_instagram_get_product_catalog( $the_catalog ) {
	if ( $the_catalog instanceof WC_Instagram_Product_Catalog ) {
		return $the_catalog;
	}

	$catalog = ( is_array( $the_catalog ) ? $the_catalog : false );

	if ( ! $catalog ) {
		$catalogs = wc_instagram_get_product_catalogs();

		if ( is_numeric( $the_catalog ) && isset( $catalogs[ $the_catalog ] ) ) {
			$catalog = $catalogs[ $the_catalog ];
		} elseif ( is_string( $the_catalog ) ) {
			foreach ( $catalogs as $data ) {
				if ( isset( $data['slug'] ) && $the_catalog === $data['slug'] ) {
					$catalog = $data;
					break;
				}
			}
		}
	}

	return ( $catalog ? new WC_Instagram_Product_Catalog( $catalog ) : false );
}

/**
 * Gets the product catalog slug.
 *
 * @since 3.0.0
 *
 * @param mixed $the_catalog Product catalog object, ID or slug.
 * @return string
 */
function wc_instagram_get_product_catalog_slug( $the_catalog ) {
	if ( is_string( $the_catalog ) && ! is_numeric( $the_catalog ) ) {
		return $the_catalog;
	}

	$catalog = wc_instagram_get_product_catalog( $the_catalog );

	return ( $catalog ? $catalog->get_slug() : '' );
}

/**
 * Gets the product catalog URL.
 *
 * @since 3.0.0
 *
 * @global WP_Rewrite $wp_rewrite The WP Rewrite instance.
 *
 * @param mixed $the_catalog Product catalog object, ID or slug.
 * @return string
 */
function wc_instagram_get_product_catalog_url( $the_catalog ) {
	global $wp_rewrite;

	$slug = wc_instagram_get_product_catalog_slug( $the_catalog );

	if ( ! $slug ) {
		return '';
	}

	$base         = $wp_rewrite->using_index_permalinks() ? 'index.php/' : '/';
	$rewrite_slug = wc_instagram_get_product_catalog_rewrite_slug();

	return home_url( $base . "{$rewrite_slug}/{$slug}.xml" );
}

/**
 * Generates a unique product catalog slug.
 *
 * @since 3.0.0
 *
 * @param string $string           The string to use for generating the slug.
 * @param array  $exclude_catalogs Optional. An array with the IDs of the catalogs to exclude from the validation. Default empty.
 * @return string
 */
function wc_instagram_generate_product_catalog_slug( $string, $exclude_catalogs = array() ) {
	$product_catalogs = wc_instagram_get_product_catalogs();

	if ( ! empty( $exclude_catalogs ) ) {
		$product_catalogs = array_diff_key( $product_catalogs, array_flip( (array) $exclude_catalogs ) );
	}

	$catalog_slugs = wp_list_pluck( $product_catalogs, 'slug' );

	$slug        = sanitize_title( $string );
	$unique_slug = $slug;
	$count       = 2;

	while ( in_array( $unique_slug, $catalog_slugs, true ) ) {
		$unique_slug = $slug . '-' . $count;

		$count++;
	}

	return $unique_slug;
}

/**
 * Gets the class name used to format the product catalog.
 *
 * @since 3.0.0
 *
 * @param string $format The format to render the catalog.
 * @return string
 */
function wc_instagram_get_product_catalog_format_class( $format ) {
	$class = 'WC_Instagram_Product_Catalog_Format_' . strtoupper( $format );

	/**
	 * Filters the class used to format the product catalog.
	 *
	 * @since 3.0.0
	 *
	 * @param string $class  The class name.
	 * @param string $format The format to render the catalog.
	 */
	return apply_filters( 'wc_instagram_product_catalog_format_class', $class, $format );
}

/**
 * Gets the object used to render the catalog in the specified format.
 *
 * @since 3.0.0
 *
 * @param mixed  $the_catalog Product catalog object, ID or slug.
 * @param array  $args        Optional. Additional arguments.
 * @param string $format      The format to render the catalog.
 * @return WC_Instagram_Product_Catalog_Format|false A product catalog formatter object. False on failure.
 */
function wc_instagram_get_product_catalog_formatter( $the_catalog, $args = array(), $format = 'xml' ) {
	$formatter = false;
	$classname = wc_instagram_get_product_catalog_format_class( $format );

	if ( $classname && class_exists( $classname ) ) {
		try {
			/**
			 * Filters the arguments used to format the product catalog.
			 *
			 * @since 3.0.0
			 *
			 * @param array  $args        The formatter arguments.
			 * @param string $format      The format to render the catalog.
			 * @param mixed  $the_catalog Product catalog object, ID or slug.
			 */
			$args = apply_filters( 'wc_instagram_get_product_catalog_format_args', $args, $format, $the_catalog );

			$formatter = new $classname( $the_catalog, $args );
		} catch ( Exception $e ) {
			wc_instagram_log( $e->getMessage(), 'error' );
		}
	}

	return $formatter;
}

/**
 * Gets the product catalog in the specified format.
 *
 * @since 3.0.0
 *
 * @param mixed  $the_catalog Product catalog object, ID or slug.
 * @param array  $args        Optional. Additional arguments.
 * @param string $format      The format to render the catalog.
 * @return string
 */
function wc_instagram_get_formatted_product_catalog( $the_catalog, $args = array(), $format = 'xml' ) {
	$formatter = wc_instagram_get_product_catalog_formatter( $the_catalog, $args, $format );

	return ( $formatter instanceof WC_Instagram_Product_Catalog_Format ? $formatter->get_output() : '' );
}

/**
 * Gets the formatted tax location of the product catalog.
 *
 * @since 3.6.1
 *
 * @param mixed  $the_catalog Product catalog object, ID or slug.
 * @param string $default     Optional. The value when there is no tax location. Default empty.
 * @return string
 */
function wc_instagram_get_formatted_product_catalog_tax_location( $the_catalog, $default = '' ) {
	$catalog = wc_instagram_get_product_catalog( $the_catalog );

	if ( ! $catalog ) {
		return $default;
	}

	$tax_location = $default;

	if ( $catalog->get_include_tax() ) {
		$countries    = WC()->countries->get_countries();
		$tax_location = $catalog->get_tax_location();

		if ( ! empty( $tax_location ) && 'base' !== get_option( 'woocommerce_tax_based_on' ) ) {
			$country_code = $tax_location[0];
		} else {
			$country_code = WC()->countries->get_base_country();
		}

		$tax_location = ( isset( $countries[ $country_code ] ) ? $countries[ $country_code ] : $country_code );
	}

	/**
	 * Filters the formatted tax location of the product catalog.
	 *
	 * @since 3.6.1
	 *
	 * @param string                       $tax_location The formatted tax location.
	 * @param WC_Instagram_Product_Catalog $catalog      Product catalog object.
	 */
	return apply_filters( 'wc_instagram_product_catalog_formatted_tax_location', $tax_location, $catalog );
}
