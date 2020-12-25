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
	 * Variation processing threshold.
	 *
	 * @var int
	 */
	const THRESHOLD = 3;

	/**
	 * Hooks.
	 */
	public static function init() {
		if ( is_admin() ) {
			$options = get_option( 'woocommerce-product-search', array() );
			$use_weights = isset( $options[ WooCommerce_Product_Search::USE_WEIGHTS ] ) ? $options[ WooCommerce_Product_Search::USE_WEIGHTS ] : WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT;
			if ( $use_weights ) {

				add_action( 'woocommerce_product_write_panel_tabs', array( __CLASS__, 'product_write_panel_tabs' ) );
				add_action( 'woocommerce_product_data_panels',      array( __CLASS__, 'product_write_panels' ) );
				add_action( 'woocommerce_process_product_meta',     array( __CLASS__, 'process_product_meta' ), 10, 2 );

				add_filter( 'manage_product_posts_columns', array( __CLASS__, 'manage_product_posts_columns' ) );
				add_action( 'manage_product_posts_custom_column', array( __CLASS__, 'manage_product_posts_custom_column' ), 10, 2 );

				add_filter( 'manage_edit-product_sortable_columns', array( __CLASS__, 'manage_product_posts_sortable_columns' ) );

				add_action( 'product_cat_add_form_fields', array( __CLASS__, 'product_cat_add_form_fields' ) );
				add_action( 'product_cat_edit_form_fields', array( __CLASS__, 'product_cat_edit_form_fields' ), 10, 2 );
				add_filter( 'manage_edit-product_cat_columns', array( __CLASS__, 'product_cat_columns' ) );
				add_filter( 'manage_product_cat_custom_column', array( __CLASS__, 'product_cat_column' ), 10, 3 );
			}

			add_action( 'save_post', array( __CLASS__, 'save_post' ), 10000, 3 );

			add_action( 'deleted_post', array( __CLASS__, 'deleted_post' ), 10000 );

			add_action( 'created_term', array( __CLASS__, 'created_term' ), 10, 3 );

			add_action( 'edited_term', array( __CLASS__, 'edited_term' ), 10, 3 );

			add_action( 'delete_term', array( __CLASS__, 'delete_term' ), 10000, 5 );

			add_action( 'edited_term_taxonomies', array( __CLASS__, 'edited_term_taxonomies' ) );

			add_action( 'deleted_term_relationships', array( __CLASS__, 'deleted_term_relationships' ), 10000, 3 );
		}
	}

	public static function save_post( $post_id = null, $post = null, $update = false ) {

		global $wpdb;

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) ) {
		} else {
			$post_type = get_post_type( $post_id );
			if ( $post_type === 'product' ) {
				$guardian = new WooCommerce_Product_Search_Guardian();
				$guardian->start();
				$indexer = new WooCommerce_Product_Search_Indexer();
				$post_status = get_post_status( $post_id );
				switch ( $post_status ) {
					case 'publish' :
					case 'pending' :
					case 'draft' :
					case 'private' :
						$indexer->index( $post_id );

						$variation_ids = $wpdb->get_col( $wpdb->prepare(
							"SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation'",
							intval( $post_id )
						) );
						if ( is_array( $variation_ids ) ) {
							$variation_ids = array_unique( array_map( 'intval', $variation_ids ) );
							$threshold = is_numeric( WPS_DEFER_VARIATIONS_THRESHOLD ) ? intval( WPS_DEFER_VARIATIONS_THRESHOLD ) : self::THRESHOLD;
							if ( $threshold < 0 ) {
								$threshold = 0;
							}
							if ( $threshold > 0 ) {
								$processed = 0;
								foreach( $variation_ids as $variation_id ) {
									if ( $guardian->is_ok() ) {
										$indexer->index( $variation_id );
										$processed++;
										if ( $processed >= $threshold ) {
											wps_log_info(
												'WooCommerce Product Search - ' .
												esc_html__( 'Info', 'woocommerce-product-search' ) .
												' : ' .
												sprintf(
													esc_html__( 'Deferred further variation processing on reaching threshold (%d).', 'woocommerce-product-search' ),
													$threshold
												)
											);
											break;
										}
									} else {
										wps_log_info(
											'WooCommerce Product Search - ' .
											esc_html__( 'Info', 'woocommerce-product-search' ) .
											' : ' .
											esc_html__( 'Deferred variation processing to avoid PHP resource limit issues.', 'woocommerce-product-search' )
										);
										break;
									}
								}
							}
						}
						break;
					default :
						$indexer->purge( $post_id );
				}
				unset( $indexer );
			}
		}
	}

	public static function deleted_post( $post_id = null ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type === 'product' ) {
			$indexer = new WooCommerce_Product_Search_Indexer();
			$indexer->purge( $post_id );
			unset( $indexer );
		}
	}

	/**
	 * Triggered on removal of object-term relationship.
	 *
	 * @since 3.0.0
	 *
	 * @param int $object_id object ID for which the object-term relationship has been deleted
	 * @param array $tt_ids term taxonomy IDs
	 * @param string $taxonomy taxonomy slug
	 */
	public static function deleted_term_relationships( $object_id, $tt_ids, $taxonomy ) {

		self::deleted_post( $object_id );
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
				'desc_tip'    => true,
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
		$search_weight = get_term_meta( $term->term_id, '_search_weight', true );
		echo '<tr class="form-field">';
		echo '<th scope="row" valign="top">';
		echo '<label>';
		echo esc_html( __( 'Search Weight', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</th>';
		echo '<td>';
		printf( '<input type="text" name="_search_weight" placeholder="%s" value="%s" />', esc_attr( __( 'unspecified', 'woocommerce-product-search' ) ), esc_attr( $search_weight ) );
		$search_weight_sum = get_term_meta( $term->term_id, '_search_weight_sum', true );
		if ( !empty( $search_weight_sum ) ) {
			if ( intval( $search_weight_sum ) !== intval( $search_weight ) ) {
				echo '<p class="description">';
				printf( '&#931; = %d', intval( $search_weight_sum ) );
				echo '</p>';
			}
		}
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
		self::edited_term( $term_id, $term_taxonomy_id, $taxonomy );
	}

	/**
	 * Update a term's search weight.
	 *
	 * @since 3.0.0
	 *
	 * @param int $term_id the term ID
	 * @param int $term_taxonomy_id the relating ID
	 * @param string $taxonomy the term's taxonomy
	 */
	public static function edited_term( $term_id, $term_taxonomy_id, $taxonomy ) {

		$product_taxonomies = WooCommerce_Product_Search_Indexer::get_applicable_product_taxonomies();
		if ( in_array( $taxonomy, $product_taxonomies ) ) {
			$indexer = new WooCommerce_Product_Search_Indexer();
			$options = get_option( 'woocommerce-product-search', array() );
			$use_weights = isset( $options[ WooCommerce_Product_Search::USE_WEIGHTS ] ) ? $options[ WooCommerce_Product_Search::USE_WEIGHTS ] : WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT;
			if ( $use_weights ) {
				$search_weight = isset( $_POST['_search_weight'] ) ? trim( $_POST['_search_weight'] ) : '';
				if ( strlen( $search_weight ) > 0 ) {
					update_term_meta( $term_id, '_search_weight', intval( $search_weight ) );
				} else {
					delete_term_meta( $term_id, '_search_weight' );
				}

				$indexer->process_term_weights( array( $term_id ) );
			}

			$indexer->preprocess_terms();

			$indexer->process_terms( $term_id );
		}
	}

	/**
	 * Triggered on removal of term.
	 *
	 * @since 3.0.0
	 *
	 * @param int $term term ID
	 * @param int $tt_id term taxonomy ID
	 * @param string $taxonomy taxonomy slug
	 * @param WP_Term|array|WP_Error|null $deleted_term deleted term
	 * @param array $object_ids term object IDs
	 */
	public static function delete_term( $term, $tt_id, $taxonomy, $deleted_term, $object_ids ) {

		$product_taxonomies = WooCommerce_Product_Search_Indexer::get_applicable_product_taxonomies();
		if ( in_array( $taxonomy, $product_taxonomies ) ) {
			$indexer = new WooCommerce_Product_Search_Indexer();
			$indexer->process_terms( intval( $term ) );

			if ( $taxonomy === 'product_cat' ) {
				$indexer->process_term_weights();
			}
		}
	}

	/**
	 * Updated related terms.
	 *
	 * @since 3.0.0
	 *
	 * @param array $edit_tt_ids
	 */
	public static function edited_term_taxonomies( $edit_tt_ids ) {

		global $wpdb;
		if ( is_array( $edit_tt_ids ) && count( $edit_tt_ids ) > 0 ) {

			$taxonomies = WooCommerce_Product_Search_Indexer::get_applicable_product_taxonomies();
			if ( count( $taxonomies ) > 0 ) {
				$edit_tt_ids = array_map( 'intval', $edit_tt_ids );
				$query =
					"SELECT DISTINCT term_id FROM $wpdb->term_taxonomy " .
					'WHERE term_taxonomy_id IN ( ' . implode( ',', $edit_tt_ids ) . ' ) ' .
					'AND ' .
					"taxonomy IN ( '" . implode( "','", esc_sql( $taxonomies ) ) . "' ) ";
				$term_ids = $wpdb->get_col( $query );
				if ( count( $term_ids ) > 0 ) {
					$term_ids = array_unique( array_map( 'intval', $term_ids ) );
					$indexer = new WooCommerce_Product_Search_Indexer();
					$options = get_option( 'woocommerce-product-search', array() );
					$use_weights = isset( $options[ WooCommerce_Product_Search::USE_WEIGHTS ] ) ? $options[ WooCommerce_Product_Search::USE_WEIGHTS ] : WooCommerce_Product_Search::USE_WEIGHTS_DEFAULT;
					if ( $use_weights ) {

						$indexer->process_term_weights( $term_ids );
					}

					$indexer->process_terms( $term_ids );
				}
			}
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
			$search_weight = get_term_meta( $term_id, '_search_weight', true );
			if ( !empty( $search_weight ) ) {
				$search_weight = intval( $search_weight );
				$columns .= $search_weight;
			}

			$search_weight_sum = get_term_meta( $term_id, '_search_weight_sum', true );
			if ( !empty( $search_weight_sum ) ) {
				$search_weight_sum = intval( $search_weight_sum );
				if ( $search_weight_sum !== $search_weight ) {
					$columns .= sprintf( ' [&#931; = %d]', $search_weight_sum );
				}
			}
		}
		return $columns;
	}
}
WooCommerce_Product_Search_Admin_Product::init();
