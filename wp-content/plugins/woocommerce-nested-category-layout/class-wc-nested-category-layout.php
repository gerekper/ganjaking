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
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Nested category layout main class.
 *
 * @since 1.4
 */
class WC_Nested_Category_Layout extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.15.1';

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
				'text_domain' =>'woocommerce-nested-category-layout',
			]
		);

		// includes required files
		$this->includes();

		// hook early to override some WooCommerce template functions
		add_action( 'after_setup_theme', [ $this, 'include_template_functions' ] );
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
			add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );

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

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

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

		// if we're on the shop page or product category page and the nested layout option is enabled
		if (    ( is_shop() && 'yes' === get_option( 'woocommerce_nested_subcat_shop', 'no' ) )
		     || ( is_product_category() && $this->is_nested_subcategories_enabled_for_category( $this->get_current_product_category_id() ) ) )  {

			add_action( 'woocommerce_archive_description', [ $this, 'list_categories_and_products' ], 15 );
			add_action( 'woocommerce_pagination',          [ $this, 'fix_query_object' ], 1 );
		}
	}


	/**
	 * Includes required core files.
	 *
	 * @since 1.0
	 */
	private function includes() {

		// walker to pre-determine the categories depth
		require_once( $this->get_plugin_path() . '/includes/Walker/Category_Depth.php' );

		// TODO remove this by version 2.0.0 or by August 2020 {FN 2020-02-19}
		if ( ! class_exists( 'Walker_Category_Depth', false ) ) {
			require_once( $this->get_plugin_path() . '/includes/Walker/Walker_Category_Depth.php' );
		}

		if ( ! is_admin() || is_ajax() ) {

			require_once( $this->get_plugin_path() . '/includes/Walker/Category_Products.php' );

			// TODO remove this by version 2.0.0 or by August 2020 {FN 2020-02-19}
			if ( ! class_exists( 'Walker_Category_Products', false ) ) {
				require_once( $this->get_plugin_path() . '/includes/Walker/Walker_Category_Products.php' );
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

		require_once( $this->get_plugin_path() . '/includes/Functions/Template.php' );
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
	 * @param \WP_Query $query WordPress query object
	 */
	public function pre_get_posts( $query ) {

		// Only apply to:
		// - product categories;
		// - the product post archive;
		// - the shop page;
		// - product tags;
		// - product attribute taxonomies.
		if (    ( ! $query->is_main_query() )
		     || ( ! $query->is_post_type_archive( 'product' ) && ! $query->is_tax( array_merge( [ 'product_cat', 'product_tag' ], wc_get_attribute_taxonomy_names() ) ) ) ) {
			return;
		}

		// don't mess with the pagination on the search page
		if ( $query->is_search() ) {
			return;
		}

		// product category page: is the nested layout enabled for this category?
		if ( $query->is_tax( 'product_cat' ) && 'no' === get_option( 'woocommerce_nested_subcat_' . $this->get_current_product_category_id(), 'no' ) ) {
			return;
		}

		// main shop page: is the nested layout enabled?
		if ( 'no' === get_option( 'woocommerce_nested_subcat_shop', 'no' ) ) {
			if ( $query->is_post_type_archive( 'product' ) || $query->is_page( wc_get_page_id( 'shop' ) ) ) {
				return;
			}
		}

		// if this is a leaf category, bail and allow the normal pagination to take over
		if ( 0 === count( get_categories( [ 'taxonomy' => 'product_cat', 'child_of' => $this->get_current_product_category_id() ] ) ) ) {
			return;
		}

		// unlimited number of products
		$query->set( 'posts_per_page', -1 );

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

		// And remove the pre_get_posts hook
		remove_filter( 'pre_get_posts', [ $this, 'pre_get_posts' ] );

		do_action( 'wc_nested_category_layout_pre_get_posts', $query );
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
		global $wp_the_query, $wp_query, $woocommerce_loop;

		// if home page/front page, bail
		if (  $wp_the_query->is_home() || ( $wp_the_query->is_front_page() && wc_get_page_id( 'shop' ) !== (int) get_option( 'page_on_front' ) ) ) {
			return;
		}

		// Previously I had a check here to bail if $wp_query->is_main_query() was
		// false, however this was incorrectly happening on a clients site, even
		// though when I printed out and compared $wp_query and $wp_the_query they
		// appeared to be the same, as expected. I'm not sure that this check is
		// even 100% necessary since this is only invoked from the action on the
		// archive-product.php template, so I figured it was safe to remove for now.
		// WooCommerce Nested Category support issue #13

		// search page, or not a product category or shop page, bail
		if ( is_search() || ( ! is_product_category() && ! is_shop() ) ) {
			return;
		}

		// get the category depths so we can display products only in their most specific categories
		$category_depths = $this->get_product_category_depths();

		$args = [
			'taxonomy'   => 'product_cat',
			'child_of'   => $this->get_current_product_category_id(),
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

		// remove template actions which are unnecessary with nested categories
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		// fire the before/after shop loop actions so that we integrate better with other plugins (ie Advanced Ajax Layered Nav Widget)
		do_action( 'woocommerce_before_shop_loop' );
		wp_list_categories( $args );
		do_action( 'woocommerce_after_shop_loop' );

		// If we rendered products and categories...
		if (    ( isset( $woocommerce_loop['has_products'] )   && $woocommerce_loop['has_products'] )
		     || ( isset( $woocommerce_loop['has_categories'] ) && $woocommerce_loop['has_categories'] ) ) {

			// ...force the have_posts() call on the archive-product.php template to fail.
			$wp_query->current_post = $wp_query->post_count + 2;

			// ...however, that seems not to work with WooCommerce 3.3, which may return a no products found notice:
			if (    Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.3' )
			     && ( $wp_query->is_tax( 'product_cat' ) || is_shop() ) ) {

				remove_action( 'woocommerce_no_products_found', 'wc_no_products_found' );
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
			   ( isset( $woocommerce_loop['has_products'] )   && $woocommerce_loop['has_products'] )
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
						'id'   => 'nested_category_layout_options'
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

		return admin_url( 'admin.php?page=wc-settings&tab=products&section=display' );
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


}
