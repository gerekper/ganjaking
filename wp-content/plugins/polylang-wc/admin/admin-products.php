<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the products on admin side.
 *
 * @since 0.1
 */
class PLLWC_Admin_Products {
	/**
	 * Product language data store.
	 *
	 * @var PLLWC_Product_Language_CPT
	 */
	protected $data_store;

	/**
	 * Constructor.
	 * Setups actions and filters.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		$this->data_store = PLLWC_Data_Store::load( 'product_language' );

		// Ajax.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_product_lang_choice', array( $this, 'product_lang_choice' ) );

		// Ajax product ordering.
		if ( in_array( 'menu_order', PLL()->options['sync'] ) ) {
			add_action( 'woocommerce_after_product_ordering', array( $this, 'product_ordering' ), 10, 2 );
		}

		// Autocomplete ajax products search.
		add_filter( 'woocommerce_json_search_found_products', array( $this, 'search_found_products' ) );
		add_filter( 'woocommerce_json_search_found_grouped_products', array( $this, 'search_found_products' ) );

		// Search in Products list table.
		add_filter( 'pll_filter_query_excluded_query_vars', array( $this, 'fix_products_search' ), 10, 2 ); // Since Polylang 2.3.5.

		// Deactivate the German and Danish specific sanitization for product attributes titles.
		$specific_locales = array( 'da_DK', 'de_DE', 'de_DE_formal', 'de_CH', 'de_CH_informal' );
		if ( array_intersect( PLL()->model->get_languages_list( array( 'fields' => 'locale' ) ), $specific_locales ) ) {
			add_action( 'wp_ajax_woocommerce_load_variations', array( $this, 'remove_sanitize_title' ), 5 );
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'remove_sanitize_title' ), 5 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'add_sanitize_title' ), 5 );
		}
	}

	/**
	 * Setups the our js script (only on the products page).
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( ! empty( $screen ) && 'post' === $screen->base && 'product' === $screen->post_type ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'pllwc_product', plugins_url( '/js/build/product' . $suffix . '.js', PLLWC_FILE ), array( 'jquery', 'wp-ajax-response' ), PLLWC_VERSION, true );
		}
	}

	/**
	 * Ajax response for changing the language in the product language metabox.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function product_lang_choice() {
		check_ajax_referer( 'pll_language', '_pll_nonce' );

		if ( isset( $_POST['post_id'], $_POST['lang'], $_POST['attributes'] ) && is_array( $_POST['attributes'] ) ) {
			$post_id    = (int) $_POST['post_id'];
			$lang       = PLL()->model->get_language( sanitize_key( $_POST['lang'] ) );
			$attributes = array_map( 'sanitize_title', wp_unslash( $_POST['attributes'] ) );

			$supplemental = array();

			$x = new WP_Ajax_Response();

			// Attributes (taxonomies of select type).
			foreach ( wc_get_attribute_taxonomies() as $a ) {
				$taxonomy = wc_attribute_taxonomy_name( $a->attribute_name );
				if ( 'select' === $a->attribute_type && false !== $i = array_search( $taxonomy, $attributes ) ) {
					$out = '';

					$product_terms = get_the_terms( $post_id, $taxonomy );
					$term_ids      = is_array( $product_terms ) ? wp_list_pluck( $product_terms, 'term_id' ) : array();
					$tr_term_ids   = array_map( 'pll_get_term', $term_ids );

					$all_terms = get_terms( $taxonomy, array( 'orderby' => 'name', 'hide_empty' => 0, 'lang' => $lang->slug ) );

					if ( is_array( $all_terms ) ) {
						foreach ( $all_terms as $term ) {
							$out .= sprintf(
								'<option value="%d" %s>%s</option>',
								(int) $term->term_id,
								selected( in_array( $term->term_id, $tr_term_ids ), true, false ),
								esc_html( $term->name )
							);
						}
					}

					$supplemental[ 'value-' . $i ] = $out;
				}
			}

			if ( ! empty( $supplemental ) ) {
				$x->Add( array( 'what' => 'attributes', 'supplemental' => $supplemental ) );
			}

			$x->send();
		}
	}

	/**
	 * Synchronizes the product ordering.
	 * Hooked to the action 'woocommerce_after_product_ordering'.
	 *
	 * @since 1.0
	 *
	 * @param int   $id          Product id.
	 * @param int[] $menu_orders An array with product ids as key and menu_order as value.
	 * @return void
	 */
	public function product_ordering( $id, $menu_orders ) {
		$language = $this->data_store->get_language( $id );
		foreach ( $menu_orders as $id => $order ) {
			if ( $this->data_store->get_language( $id ) === $language ) {
				foreach ( $this->data_store->get_translations( $id ) as $tr_id ) {
					if ( $id !== $tr_id ) {
						$this->data_store->save_product_ordering( $tr_id, $order );
					}
				}
			}
		}
	}

	/**
	 * Filters the products per language in autocomplete ajax searches.
	 * Hooked to the filters 'woocommerce_json_search_found_products'
	 * and 'woocommerce_json_search_found_grouped_products'.
	 *
	 * @since 0.1
	 *
	 * @param string[] $products array with product ids as keys and names as values.
	 * @return string[]
	 */
	public function search_found_products( $products ) {
		// Either we are editing a product or an order.
		if ( ! isset( $_REQUEST['pll_post_id'] ) || ! $lang = $this->data_store->get_language( (int) $_REQUEST['pll_post_id'] ) ) {  // phpcs:ignore WordPress.Security.NonceVerification
			$lang = PLLWC_Admin::get_preferred_language();
		}

		foreach ( array_keys( $products ) as $id ) {
			if ( $this->data_store->get_language( $id ) !== $lang ) {
				unset( $products[ $id ] );
			}
		}

		return $products;
	}

	/**
	 * Fixes the search in the Products list table.
	 * It is necessary since WC 3.3.1 as the query uses 'post__in'
	 * which is usually excluded from the language filter.
	 * Hooked to the filter 'pll_filter_query_excluded_query_vars'.
	 *
	 * @since 1.0
	 *
	 * @param string[] $excludes Query vars excluded from the language filter.
	 * @param WP_Query $query    WP Query object.
	 * @return string[]
	 */
	public function fix_products_search( $excludes, $query ) {
		if ( ! empty( $query->query['product_search'] ) ) {
			$excludes = array_diff( $excludes, array( 'post__in' ) );
		}
		return $excludes;
	}

	/**
	 * Removes the German and Danish specific sanitization for titles.
	 *
	 * @since 0.7.1
	 *
	 * @return void
	 */
	public function remove_sanitize_title() {
		// Backward compatibility with Polylang < 2.9.
		$obj = isset( PLL()->filters_sanitization ) ? PLL()->filters_sanitization : PLL()->filters;
		remove_filter( 'sanitize_title', array( $obj, 'sanitize_title' ) );
	}

	/**
	 * Restores the German and Danish specific sanitization for titles.
	 *
	 * @since 0.7.1
	 *
	 * @return void
	 */
	public function add_sanitize_title() {
		// Backward compatibility with Polylang < 2.9.
		$obj = isset( PLL()->filters_sanitization ) ? PLL()->filters_sanitization : PLL()->filters;
		add_filter( 'sanitize_title', array( $obj, 'sanitize_title' ), 10, 3 );
	}
}
