<?php
/**
 * WooCommerce Nested Category Layout
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Nested Category Layout to newer
 * versions in the future. If you wish to customize WooCommerce Nested Category Layout for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-nested-category-layout/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Nested category layout main class.
 *
 * @since 1.4
 */
class WC_Nested_Category_Layout extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.17.4';

	/** @var WC_Nested_Category_Layout single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'nested_category_layout';


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.4
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			[
				'text_domain' => 'woocommerce-nested-category-layout',
			]
		);

		// includes required files
		$this->includes();

		// hook early to override some WooCommerce template functions
		add_action( 'after_setup_theme', [ $this, 'include_template_functions' ] );
	}


	 /**
	 * Is it main query for products.
	 *
	 * @since 1.17.4
	 *
	 * @param \WP_Query $wp_query WordPress query object
	 * @return boolean
	*/
	public function is_product_main_query( $query = null ) {
		global $wp_query;

		if ( ! $query && $wp_query ) {
			$query = $wp_query;
		}

		return $query && $query->is_main_query() && ( ( isset( $query->query_vars[ 'post_type' ] ) && 'product' === $query->query_vars[ 'post_type' ] ) || is_product_category() );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function init_plugin() {

		add_action( 'wp', [ $this, 'wp_init' ] );

		if ( ! is_admin() ) {

			// no pagination: return all products when displaying nested categories/products
			add_action( 'woocommerce_product_query', [ $this, 'woocommerce_product_query' ] );
			add_action( 'pre_get_posts', [ $this, 'handle_third_party_plugins_compatibility' ], 1, 999 );

		} else {

			// inject our admin options
			add_filter( 'woocommerce_product_settings', [ $this, 'nested_category_layout_settings' ] );
		}
	}


	/**
	 * Initializes the plugin lifecycle handler.
	 *
	 * @since 1.12.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Nested_Category_Layout\Lifecycle( $this );
	}


	/**
	 * Initialize admin settings, depending on version of WooCommerce
	 *
	 * TODO remove this method by version 2.0.0 or by March 2020 {FN 2019-03-27}
	 *
	 * @since 1.3
	 * @deprecated since 1.12.0
	 */
	public function init_admin_settings() {

		_deprecated_function( __METHOD__, '1.2.0' );
	}


	/**
	 * Initializes nested categories handling.
	 *
	 * At this point we can determine the page we're on because the conditional query tag functions are available.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function wp_init() {

		$is_shop             = is_shop() && 'yes' === get_option( 'woocommerce_nested_subcat_shop', 'no' );
		$is_product_category = is_product_category() && $this->is_nested_subcategories_enabled_for_category( $this->get_current_product_category_id() );

		// if we're on the shop page or a product category page
		if ( $is_shop ) {

			// replace the no product message notice
			add_action( 'woocommerce_no_products_found', [ $this, 'list_categories_and_products' ], 1 );

		} elseif ( $is_product_category ) {

			// or a product category page
			add_action( 'woocommerce_no_products_found', [ $this, 'list_categories_and_products' ], 1 );
			add_action( 'woocommerce_after_shop_loop', [ $this, 'list_categories_and_products' ], 1 );
			add_action( 'woocommerce_pagination', [ $this, 'fix_query_object' ], 1 );
		}

		if ( $is_shop || $is_product_category ) {
			// remove template actions which are unnecessary with nested categories
			add_action( 'woocommerce_before_shop_loop', [ $this, 'remove_category_template_unnecessary_actions' ], 1 );
		}
	}


	/**
	 * Removes template actions which are unnecessary with nested categories.
	 *
	 * @internal
	 *
	 * @since 1.70.0-dev.1
	 */
	public function remove_category_template_unnecessary_actions() {

		// remove ordering and results count actions/section
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
	}


	/**
	 * Includes required core files.
	 *
	 * @since 1.0
	 */
	private function includes() {

		// walker to pre-determine the categories depth
		require_once( $this->get_plugin_path() . '/src/Walker/Category_Depth.php' );

		// TODO remove this by version 2.0.0 or by August 2020 {FN 2020-02-19}
		if ( ! class_exists( 'Walker_Category_Depth', false ) ) {
			require_once( $this->get_plugin_path() . '/src/Walker/Walker_Category_Depth.php' );
		}

		if ( ! is_admin() || wp_doing_ajax() ) {

			require_once( $this->get_plugin_path() . '/src/Walker/Category_Products.php' );

			// TODO remove this by version 2.0.0 or by August 2020 {FN 2020-02-19}
			if ( ! class_exists( 'Walker_Category_Products', false ) ) {
				require_once( $this->get_plugin_path() . '/src/Walker/Walker_Category_Products.php' );
			}
		}
	}


	/**
	 * Override some template functions from woocommerce/woocommerce-template.php with our own template functions file.
	 *
	 * Largely to allow us to load template parts first from this plugin's template directory.
	 *
	 * @since 1.0
	 */
	public function include_template_functions() {

		require_once( $this->get_plugin_path() . '/src/Functions/Template.php' );
	}


	/**
	 * Filters the products per page to unlimited.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * Pagination with this sort of layout would be quite challenging:
	 * TODO: how would we limit ourselves to just the products that need to be displayed in a hierarchy of categories where the products are shown only at the deepest levels?
	 * TODO: should we be using loop_shop_per_page rather than pre_get_posts?
	 *
	 * @param \WP_Query $wp_query WordPress query object
	 */
	public function woocommerce_product_query( $wp_query ) {

		// don't mess with the pagination on the search page or the product tag page
		if ( $wp_query->is_search() ) {
			return;
		}

		$current_product_category_id = $this->get_current_product_category_id();

		// product category page: is the nested layout enabled for this category?
		if ( $wp_query->is_tax( 'product_cat' ) && 'no' === get_option( 'woocommerce_nested_subcat_' . $current_product_category_id, 'no' ) ) {
			return;
		}

		// main shop page: is the nested layout enabled?
		$is_shop_page = $wp_query->is_post_type_archive( 'product' ) || $wp_query->is_page( wc_get_page_id( 'shop' ) );

		if ( $is_shop_page && 'no' === get_option( 'woocommerce_nested_subcat_shop', 'no' ) ) {
			return;
		}

		// bail if this is neither a shop page or a category page
		if ( false === $is_shop_page && 0 === $current_product_category_id ) {
			return;
		}

		// if this is a leaf category, bail and allow the normal pagination to take over
		if ( 0 === count( get_categories( [ 'taxonomy' => 'product_cat', 'child_of' => $current_product_category_id ] ) ) ) {
			return;
		}

		// load only direct children products, ignore sub-categories
		if ( $current_product_category_id && isset( $wp_query->tax_query->queried_terms['product_cat'] ) ) {
			// unlimited number of products
			$wp_query->set( 'posts_per_page', - 1 );

			$category_query = $wp_query->tax_query->queried_terms['product_cat'];

			$category_query['taxonomy']         = 'product_cat';
			$category_query['include_children'] = false;

			$wp_query->set( 'tax_query', array_merge( $wp_query->get( 'tax_query' ), [ $category_query ] ) );
		} else {
			// load nothing
			$wp_query->set( 'post__in', [ - 1 ] );
		}

		// Unless cache_results is disabled, then we run into a memory allocation
		// error when we query for more than around 125 products on a page, with
		// a memory limit of 64MB.
		//
		// Upping the memory limit to 128MB safely allows us at least 200 products,
		// but is this reasonable?  On the other hand, I don't know what the
		// consequences are of disabling cache_results, though I'd imagine you
		// take some sort of performance hit...
		//
		// $q->set( 'cache_results', false );

		// And remove the hook
		remove_filter( 'woocommerce_product_query', [ $this, 'woocommerce_product_query' ] );

		do_action( 'wc_nested_category_layout_pre_get_posts', $wp_query );
	}


	/**
	 * Render our nested categories, and the products they contain.
	 *
	 * This is the heart of the plugin!
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function list_categories_and_products() {

		global $wp_the_query;

		// if home page/front page, bail
		if ( $wp_the_query->is_home() || ( $wp_the_query->is_front_page() && wc_get_page_id( 'shop' ) !== (int) get_option( 'page_on_front' ) ) ) {
			return;
		}

		// Previously I had a check here to bail if $wp_query->is_main_query() was
		// false, however this was incorrectly happening on a clients site, even
		// though when I printed out and compared $wp_query and $wp_the_query they
		// appeared to be the same, as expected. I'm not sure that this check is
		// even 100% necessary since this is only invoked from the action on the
		// archive-product.php template, so I figured it was safe to remove for now.
		// WooCommerce Nested Category support issue #13

		$is_shop                   = is_shop();
		$is_product_category       = is_product_category();
		$is_empty_product_category = $is_product_category && 0 === $wp_the_query->found_posts;

		// search page, or not a product category or shop page, bail
		if ( ( ! $is_product_category && ! $is_shop ) || is_search() ) {
			return;
		}

		$current_category_id = $this->get_current_product_category_id();

		// if shop page, make sure to ignore the no product message notice
		remove_action( 'woocommerce_no_products_found', 'wc_no_products_found' );

		// remove this callback to avoid infinite recursion
		remove_action( 'woocommerce_after_shop_loop', [ $this, 'list_categories_and_products' ], 1 );

		// trick WC loop that it has products to show
		wc_set_loop_prop( 'total', 1 );
		wc_set_loop_prop( 'total_pages', 1 );
		wc_set_loop_prop( 'current_page', 1 );
		wc_set_loop_prop( 'is_paginated', true );

		// get the category depths so we can display products only in their most specific categories
		$category_depths = $this->get_product_category_depths();

		$args = [
			'taxonomy'   => 'product_cat',
			'child_of'   => $current_category_id,
			'walker'     => new \SkyVerge\WooCommerce\Nested_Category_Layout\Walker\Category_Products( $category_depths ),
			'echo'       => false,
			'show_count' => 0, // note: originally I had this true, which worked fine, except for leaf categories, where it would generate a WordPress SQL Error notice due to the custom taxonomy 'product_cat' with 'term_taxonomy_id IN ()'
		];

		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.6.0' ) ) {
			$args['orderby'] = 'menu_order';
		}

		/**
		 * Filters the nested category layout categories list query args.
		 *
		 * @since 1.6.0
		 *
		 * @param array $args associative array for wp_list_categories()
		 */
		$args = (array) apply_filters( 'wc_nested_category_layout_list_categories_args', $args );

		// trigger before loop action
		if ( $is_shop || $is_empty_product_category ) {
			do_action( 'woocommerce_before_shop_loop' );
		}

		wp_list_categories( $args );

		// trigger after loop action
		if ( $is_shop || $is_empty_product_category ) {
			do_action( 'woocommerce_after_shop_loop' );
		}

		if( ! $is_empty_product_category ) {
			global $wp_query;
			foreach( $ncl_tax_query = $wp_query->get( 'tax_query' ) as $index => $term ) {

				if( isset( $term['taxonomy'] ) && 'product_cat' === $term['taxonomy'] ) {

					$ncl_tax_query[$index]['include_children'] = true;
					$wp_query->tax_query = $ncl_tax_query;
					$wp_query->tax_query = new WP_Tax_Query( $ncl_tax_query );
				}
			}
		}

	}


	/**
	 * Fixes the global query object as if it was altered from the archive-product.php template.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function fix_query_object() {

		global $woocommerce_loop, $wp_query;

		if (
			( isset( $woocommerce_loop['has_products'] ) && $woocommerce_loop['has_products'] )
			|| ( isset( $woocommerce_loop['has_categories'] ) && $woocommerce_loop['has_categories'] )
		) {

			$wp_query->current_post = $wp_query->post_count + 1;

			$wp_query->rewind_posts();
		}
	}


	/**
	 * Adds admin settings (nested list of product categories).
	 *
	 * The shop admin can determine which catalog pages display our nested categories/products.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param array $settings associative-array of WooCommerce settings
	 * @return array associative-array of WooCommerce settings
	 */
	public function nested_category_layout_settings( $settings ) {

		$updated_settings = [];

		if ( is_array( $settings ) ) {

			foreach ( $settings as $section ) {

				$updated_settings[] = $section;

				// after the Catalog Options section
				if ( 'catalog_options' === $section['id'] && 'sectionend' === $section['type'] ) {

					// we're only interested in categories that have sub-categories
					$categories     = [];
					$tmp_categories = $this->get_product_category_depths();

					foreach ( $tmp_categories as $term_id => $depth ) {

						$child_categories = get_categories( [ 'taxonomy' => 'product_cat', 'child_of' => $term_id ] );

						if ( count( $child_categories ) > 0 ) {
							$categories[ $term_id ] = $depth;
						}
					}

					$updated_settings[] = [
						'name' => __( 'Catalog Pages by Category', 'woocommerce-nested-category-layout' ),
						'type' => 'title',
						'desc' => __( 'The following options determine which catalog pages will display nested subcategories and products.', 'woocommerce-nested-category-layout' ),
						'id'   => 'nested_category_layout_options',
					];

					$updated_settings[] = [
						'name'     => __( 'Products per Subcategory', 'woocommerce-nested-category-layout' ),
						'desc'     => __( 'The number of products to display per subcategory', 'woocommerce-nested-category-layout' ),
						'id'       => 'woocommerce_subcat_posts_per_page',
						'std'      => apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ),
						'type'     => 'text',
						'desc_tip' => true,
					];

					// special case: shop page
					$shop_page_section = [
						'name' => __( 'Show products/subcategories', 'woocommerce-nested-category-layout' ),
						'desc' => __( 'Shop Page', 'woocommerce-nested-category-layout' ),
						'id'   => 'woocommerce_nested_subcat_shop',
						'std'  => 'no',
						'type' => 'checkbox',
					];

					if ( count( $categories ) > 0 ) {
						$shop_page_section['checkboxgroup'] = 'start';
					}

					$updated_settings[] = $shop_page_section;

					// go through the product categories, if any
					foreach ( $categories as $term_id => $depth ) {

						$category = get_term( $term_id, 'product_cat' );

						$updated_settings[] = [
							'desc'          => str_repeat( '-', $depth ) . ' ' . $category->name,
							'id'            => 'woocommerce_nested_subcat_' . $term_id,
							'std'           => 'no',
							'type'          => 'checkbox',
							'checkboxgroup' => '',
						];
					}

					// end the checkbox group if needed
					if ( count( $categories ) > 0 ) {
						$updated_settings[ count( $updated_settings ) - 1 ]['checkboxgroup'] = 'end';
					}

					// end the section
					$updated_settings[] = [
						'type' => 'sectionend',
						'id'   => 'nested_category_layout_options',
					];
				}
			}
		}

		return $updated_settings;
	}


	/**
	 * Gets product category depths relative to the current category.
	 *
	 * @since 1.0
	 *
	 * @return array of category id to depth
	 */
	private function get_product_category_depths() {

		$categories = get_categories( [ 'taxonomy' => 'product_cat', 'child_of' => $this->get_current_product_category_id() ] );
		$categories = walk_category_tree( $categories, 0, [ 'walker' => new \SkyVerge\WooCommerce\Nested_Category_Layout\Walker\Category_Depth() ] );

		// no categories found
		if ( ! is_array( $categories ) ) {
			$categories = [];
		}

		return $categories;
	}


	/**
	 * Gets the current product category ID.
	 *
	 * @since 1.0
	 *
	 * @return int
	 */
	public function get_current_product_category_id() {

		// Get the category ID of the current page.
		if ( $product_cat_slug = get_query_var( 'product_cat' ) ) {
			$product_cat = get_term_by( 'slug', $product_cat_slug, 'product_cat' );
			$category_id = $product_cat instanceof \WP_Term ? $product_cat->term_id : 0;
		} else {
			$category_id = 0;
		}

		return $category_id;
	}


	/**
	 * Determines if nested subcategories are enabled for the given category.
	 *
	 * @since 1.2.5
	 *
	 * @param int $category category ID
	 * @return bool
	 */
	public function is_nested_subcategories_enabled_for_category( $category ) {

		return 'yes' === get_option( 'woocommerce_nested_subcat_' . $category, 'no' );
	}


	/**
	 * Gets the plugin configuration URL.
	 *
	 * @since 1.4
	 *
	 * @param null|string $_ unused
	 * @return string
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=products' );
	}


	/**
	 * Gets the plugin name, localized.
	 *
	 * @since 1.4
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Nested Category Layout', 'woocommerce-nested-category-layout' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 1.4
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-nested-category-layout/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 1.12.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-nested-category-layout/';
	}


	/**
	 * Main Nested Category Layout Instance, ensures only one instance is/can be loaded.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Nested_Category_Layout
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Handle third party plugins compatibility.
	 *
	 * @internal
	 *
	 * @since 1.17.3
	 * @param \WP_Query $wp_query WordPress query object
	 */
	public function handle_third_party_plugins_compatibility( $wp_query ) {

		if( ! $this->is_product_main_query( $wp_query ) ) {
			return;
		}
		// YITH WooCommerce Ajax Product Filter
		if ( isset( $_REQUEST['yith_wcan'] ) ) {
			$this->disable_nested_categories();
		}

		// WOOF - WooCommerce Products Filters
		if ( isset( $_REQUEST['swoof'] ) ) {
			$this->disable_nested_categories();
		}

		// Advanced AJAX Product Filters
		if ( class_exists( 'BeRocket_url_parse_page' ) && ! isset( $_REQUEST['filters'] ) ) {
			remove_action( 'woocommerce_product_query', [ new BeRocket_url_parse_page(), 'woocommerce_product_query'], 99999999, 1 );
		}

		// Advanced AJAX Product Filters
		if ( class_exists( 'BeRocket_url_parse_page' ) && isset( $_REQUEST['filters'] ) ) {
			$this->disable_nested_categories();
		}

		// WooCommerce product filter widget
		if ( WC_Query::get_layered_nav_chosen_attributes() ) {
			$this->disable_nested_categories();
		}
	}


	/**
	 * Remove product query hook to disable NCL.
	 *
	 * @since 1.17.3
	 */
	private function disable_nested_categories() {
		remove_action( 'wp', [ $this, 'wp_init' ] );
		remove_action( 'woocommerce_product_query', [ $this, 'woocommerce_product_query' ] );
	}


}
