<?php
/**
 * class-woocommerce-product-search-admin-product.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 1.2.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin extensions to the product post type.
 */
class WooCommerce_Product_Search_Admin_Product {

	/**
	 * Hooks.
	 */
	public static function init() {
		if ( is_admin() ) {
			$options = get_option( 'woocommerce-product-search', array() );
			$use_weights = isset( $options[WooCommerce_Product_Search::USE_WEIGHTS] ) ? $options[WooCommerce_Product_Search::USE_WEIGHTS] : WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT;
			if ( $use_weights ) {

				add_action( 'woocommerce_product_write_panel_tabs', array( __CLASS__, 'product_write_panel_tabs' ) );
				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.6' ) >= 0 ) {
					add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_write_panels' ) );
				} else {
					add_action( 'woocommerce_product_write_panels', array( __CLASS__, 'product_write_panels' ) );
				}
				add_action( 'woocommerce_process_product_meta', array( __CLASS__, 'process_product_meta' ), 10, 2 );

				add_filter( 'manage_product_posts_columns', array( __CLASS__, 'manage_product_posts_columns' ) );
				add_action( 'manage_product_posts_custom_column', array( __CLASS__, 'manage_product_posts_custom_column' ), 10, 2 );

				add_filter( 'manage_edit-product_sortable_columns', array( __CLASS__, 'manage_product_posts_sortable_columns' ) );

				add_action( 'product_cat_add_form_fields', array( __CLASS__, 'product_cat_add_form_fields' ) );
				add_action( 'product_cat_edit_form_fields', array( __CLASS__, 'product_cat_edit_form_fields' ), 10, 2 );
				add_action( 'created_term', array( __CLASS__, 'created_term' ), 10, 3 );
				add_action( 'edit_term', array( __CLASS__, 'edit_term' ), 10, 3 );
				add_filter( 'manage_edit-product_cat_columns', array( __CLASS__, 'product_cat_columns' ) );
				add_filter( 'manage_product_cat_custom_column', array( __CLASS__, 'product_cat_column' ), 10, 3 );
			}
		}
	}

	/**
	 * Search tab.
	 */
	public static function product_write_panel_tabs() {
		echo
			'<li class="search_tab search_options">' .
			'<a href="#woocommerce_product_search">' .
			esc_html( __( 'Search', 'woocommerce-product-search' ) ) .
			'</a>' .
			'</li>';
	}

	/**
	 * Search tab content.
	 */
	public static function product_write_panels() {

		global $post, $wpdb, $woocommerce;

		$search_weight  = get_post_meta( $post->ID, '_search_weight', true );


		echo '<style type="text/css">';
		echo '#woocommerce-product-data ul.wc-tabs li.search_tab a:before {';
		printf( 'content: url( %s );', esc_url( WOO_PS_PLUGIN_URL . '/images/woocommerce-product-search-12x12.png' ) );
		echo 'padding: 0 4px;';
		echo '}';
		echo '</style>';

		echo '<div id="woocommerce_product_search" class="panel woocommerce_options_panel" style="padding: 1em 0;">';

		woocommerce_wp_text_input(
			array(
				'id'          => '_search_weight',
				'label'       => __( 'Search Weight', 'woocommerce-product-search' ),
				'value'       => $search_weight,
				'description' => __( 'The search weight of this product represents its relevance in product search results. Products with higher search weights appear first in search results.', 'woocommerce-product-search' ),
				'placeholder' => __( 'unspecified', 'woocommerce-product-search' ),
				'type'        => 'number'
			)
		);
		echo '</div>'; 
	}

	/**
	 * Checks and saves search meta.
	 *
	 * @param int $post_id product ID
	 * @param object $post product
	 */
	public static function process_product_meta( $post_id, $post ) {
		$search_weight = isset( $_POST['_search_weight'] ) ? trim( $_POST['_search_weight'] ) : '';
		if ( strlen( $search_weight ) > 0 ) {
			$search_weight = intval( $search_weight );
			update_post_meta( $post_id, '_search_weight', $search_weight );
		} else {
			delete_post_meta( $post_id, '_search_weight' );
		}
	}

	/**
	 * Add the weight column to the products screen.
	 *
	 * @param array $posts_columns current columns
	 *
	 * @return array
	 */
	public static function manage_product_posts_columns( $posts_columns ) {
		$posts_columns['search_weight'] = sprintf(
			'<span title="%s">%s</span>',
			__( 'Product search relevance.', 'woocommerce-product-search' ),
			__( 'Search Weight', 'woocommerce-product-search' )
		);
		return $posts_columns;
	}

	/**
	 * Indicate the weight column as sortable.
	 *
	 * @param array $posts_columns current sortable columns
	 *
	 * @return array
	 */
	public static function manage_product_posts_sortable_columns( $posts_columns ) {
		$posts_columns['search_weight'] = 'search_weight';
		return $posts_columns;
	}

	/**
	 * Renders the content for the weight column.
	 *
	 * @param string $column_name which column
	 * @param int $post_id which post
	 */
	public static function manage_product_posts_custom_column( $column_name, $post_id ) {
		switch ( $column_name ) {
			case 'search_weight' :
				$search_weight = get_post_meta( $post_id, '_search_weight', true );
				echo esc_html( $search_weight );
				break;

		}
	}

	/**
	 * Renders the search weight field for a new product category term.
	 */
	public static function product_cat_add_form_fields() {
		echo '<div class="form-field">';
		echo '<label for="_search_weight">';
		echo esc_html( __( 'Search Weight', 'woocommerce-product-search' ) );
		echo '</label>';
		echo ' ';
		printf( '<input type="text" name="_search_weight" placeholder="%s" />', esc_attr( __( 'unspecified', 'woocommerce-product-search' ) ) );
		echo '</div>';
	}

	/**
	 * Renders the search weight field for a product category term.
	 *
	 * @param object $term which term
	 * @param string $taxonomy which taxonomy
	 */
	public static function product_cat_edit_form_fields( $term, $taxonomy ) {
		$search_weight = get_woocommerce_term_meta( $term->term_id, '_search_weight', true );
		echo '<tr class="form-field">';
		echo '<th scope="row" valign="top">';
		echo '<label>';
		echo esc_html( __( 'Search Weight', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</th>';
		echo '<td>';
		printf( '<input type="text" name="_search_weight" placeholder="%s" value="%s" />', esc_attr( __( 'unspecified', 'woocommerce-product-search' ) ), esc_attr( $search_weight ) );
		echo '</td>';
		echo '</tr>';
	}

	/**
	 * Save a new term's search weight.
	 *
	 * @param int $term_id the term ID
	 * @param int $term_taxonomy_id the relating ID
	 * @param string $taxonomy the term's taxonomy
	 */
	public static function created_term( $term_id, $term_taxonomy_id, $taxonomy ) {
		self::edit_term( $term_id, $term_taxonomy_id, $taxonomy );
	}

	/**
	 * Update a term's search weight.
	 *
	 * @param int $term_id the term ID
	 * @param int $term_taxonomy_id the relating ID
	 * @param string $taxonomy the term's taxonomy
	 */
	public static function edit_term( $term_id, $term_taxonomy_id, $taxonomy ) {
		$search_weight = isset( $_POST['_search_weight'] ) ? trim( $_POST['_search_weight'] ) : '';
		if ( strlen( $search_weight ) > 0 ) {
			update_woocommerce_term_meta( $term_id, '_search_weight', intval( $search_weight ) );
		} else {
			delete_woocommerce_term_meta( $term_id, '_search_weight' );
		}
	}

	/**
	 * Search weight column.
	 *
	 * @param array $columns current columns
	 *
	 * @return array
	 */
	public static function product_cat_columns( $columns ) {
		$columns['search_weight'] = __( 'Search Weight', 'woocommerce-product-search' );
		return $columns;
	}

	/**
	 * Thumbnail column value added to category admin.
	 *
	 * @access public
	 *
	 * @param mixed $columns current columns
	 * @param mixed $column current column
	 * @param mixed $term_id current term ID
	 *
	 * @return array
	 */
	public static function product_cat_column( $columns, $column, $term_id ) {
		if ( $column == 'search_weight' ) {
			$search_weight = get_woocommerce_term_meta( $term_id, '_search_weight', true );
			$columns .= $search_weight;
		}
		return $columns;
	}
}
WooCommerce_Product_Search_Admin_Product::init();
