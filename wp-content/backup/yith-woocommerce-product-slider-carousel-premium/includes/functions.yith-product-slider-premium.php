<?php
if( !defined( 'ABSPATH' ) )
    exit;
?>
<?php
if( ! function_exists( 'yith_wpml_get_translated_id' ) ) {
    /**
     * Get the id of the current translation of the post/custom type
     *
     * @since  2.0.0
     * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
     */
    function yith_wpml_get_translated_id( $id, $post_type ) {

        if ( function_exists( 'icl_object_id' ) ) {

            $id = icl_object_id( $id, $post_type, true );

        }

        return $id;
    }
}

if( !function_exists( 'ywcps_json_search_product_tags' ) ) {

	/**
	 * get product tags by terms
	 * @author YITHEMES
	 * @since 1.0.0
	 */
	function ywcps_json_search_product_tags()
	{
		ywcps_json_search_product_categories( '', array( 'product_tag' ) );
	}
}
add_action( 'wp_ajax_yit_slider_json_search_product_tag', 'ywcps_json_search_product_tags', 10 );

if( !function_exists( 'ywcps_json_search_product_brands' ) ) {

	/**
	 * get product brands by terms
	 * @author YITHEMES
	 * @since 1.0.0
	 */
	function ywcps_json_search_product_brands()
	{
		ywcps_json_search_product_categories( '', array( YITH_WCBR::$brands_taxonomy ) );
	}
}
add_action( 'wp_ajax_yit_slider_json_search_product_brands', 'ywcps_json_search_product_brands', 10 );

/**
 * Function that returns an array containing the IDs of the products that are out of stock.
 *
 * @since 2.0
 * @access public
 * @return array
 */
function yith_wc_get_product_ids_out_of_stock() {
	global $wpdb;

	// Load from cache
	$product_ids_out_of_stock = get_transient( 'yith_products_out_of_stock' );

	// Valid cache found
	if ( false !== $product_ids_out_of_stock ) {
		return $product_ids_out_of_stock;
	}

	$product_ids_out_of_stock = $wpdb->get_results( "
			SELECT post.ID FROM `$wpdb->posts` AS post
			LEFT JOIN `$wpdb->postmeta` AS meta ON post.ID = meta.post_id
			WHERE post.post_type IN ( 'product' )
			AND post.post_status = 'publish'
			AND meta.meta_key = '_stock_status'
			AND meta.meta_value LIKE 'outofstock'
			" );

	$product_ids_out_of_stock  = wp_list_pluck( $product_ids_out_of_stock, 'ID' );
	set_transient( 'yith_products_out_of_stock', $product_ids_out_of_stock, DAY_IN_SECONDS * 30 );

	return $product_ids_out_of_stock;
}

if( !function_exists( 'ywcps_get_term_id_by_slug')){

	/**
	 * @param $term_ids
	 * @param string $taxonomy
     * @return array;
	 */
    function ywcps_get_term_id_by_slug( $slug, $taxonomy= 'product_cat' ){

        $slug = !is_array( $slug ) ? array( $slug ) : $slug;

        $term_ids = array();

        foreach( $slug as $single_slug ){

            $term = get_term_by( 'slug', $single_slug, $taxonomy );

            if( is_object( $term ) ){

                $term_ids[] = $term->term_id;
            }

        }

        return $term_ids;
    }
}