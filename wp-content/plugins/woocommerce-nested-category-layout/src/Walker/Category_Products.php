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

namespace SkyVerge\WooCommerce\Nested_Category_Layout\Walker;

defined( 'ABSPATH' ) or exit;

/**
 * Category walker for products.
 *
 * A category walker which performs a traversal through the categories tree, skipping the root category (which is the category of the current page), and rendering any non-empty category names as headings, and any products within them.
 *
 * A non-empty category is defined as a category which contains products, or has any descendants that contain products.
 *
 * Products can belong to multiple categories and are therefore rendered only at the deepest category level.
 * This means that if a product belongs to sibling categories at its deepest level it can appear more than once.
 *
 * Originally introduced in v1.0 but namespaced & renamed in v1.14.2 to avoid class collisions.
 *
 * @since 1.14.2
 */
class Category_Products extends \Walker {


	/** @var string Taxonomy being handled. */
	public $tree_type = 'product_cat';

	/** @var array DB fields used. */
	public $db_fields = [ 'parent' => 'parent', 'id' => 'term_id' ];

	/** @var array Associative array of category id's to category depth. */
	protected $category_depths;


	/**
	 * Constructor.
	 *
	 * @since 1.14.2
	 *
	 * @param array $category_depths
	 */
	public function __construct( $category_depths ) {

		$this->category_depths = $category_depths;
	}


	/**
	 * Called after a category has been rendered.
	 *
	 * @see \Walker::end_el()
	 *
	 * @since 1.14.2
	 *
	 * @param string|array $output Passed by reference. Used to append additional content.
	 * @param \WP_Term $category The category.
	 * @param int $depth Level depth of the category.
	 * @param array $args Additional args.
	 */
	public function end_el( &$output, $category, $depth = 0, $args = [] ) {

		if ( is_array( $output ) ) {
			array_pop( $output );
		}

		if ( empty( $output ) ) {
			$output = '';
		}
	}


	/**
	 * Starts the list before the elements are added.
	 *
	 * The 'proper' way to use this callback is to update the $output memo with
	 * the output to be displayed. Since we're not interested in making a hierarchical
	 * list, instead we're doing a much simpler "flat" list, and it's more convenient
	 * to echo all output.
	 *
	 * @see \Walker::start_el()
	 *
	 * @since 1.14.2
	 *
	 * @param array $output Passed by reference. Used to remember categories that might need their title displayed.
	 * @param \WP_Term $category Category data object.
	 * @param int $depth Depth of category. Used for padding.
	 * @param array $args Uses 'selected', 'show_count', and 'show_last_update' keys, if they exist.
	 * @param int $current_object_id Current object ID.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = [], $current_object_id = 0 ) {

		global $woocommerce_loop;

		if ( ! is_array( $output ) ) {
			$output = [];
		}

		// determine whether the current sub-categories contains any products
		$has_products = apply_filters( 'wc_nested_category_layout_has_products', false, $this );

		if ( ! $has_products ) {
			$has_products = $category->count > 0;
		}

		// keep track of the current category in case a child category
		//  has products and we need to display titles
		$category->depth = $depth;

		$output[] = $category;

		if ( $has_products ) {

			// tell the template to display a "See More" link if necessary
			$woocommerce_loop['see_more'] = true;

			// record the fact that categories have been displayed.  Do this so we can alter the
			//  query to skip any products that would otherwise be displayed, this is done so that
			//  the number of products to be displayed can be set to '0' to have nested categories
			//  only
			$woocommerce_loop['has_categories'] = true;

			// display nested category title(s) and product(s)
			woocommerce_nested_category_products_content_section( $output );
		}
	}


}
