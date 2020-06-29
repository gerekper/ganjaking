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

namespace SkyVerge\WooCommerce\Nested_Category_Layout\Walker;

defined( 'ABSPATH' ) or exit;

/**
 * Category walker for depths.
 *
 * A category walker which walks through the categories, and determines their depths.
 *
 * Originally introduced in v1.0 but namespaced & renamed in v1.14.2 to avoid class collisions.
 *
 * @since 1.14.2
 */
class Category_Depth extends \Walker {


	/** @var string The taxonomy where the class applies to. */
	public $tree_type = 'product_cat';

	/** @var array DB fields being used. */
	public $db_fields = [ 'parent' => 'parent', 'id' => 'term_id' ];

	/** @var bool $already_run A flag to know when to coerce the output into an array. */
	private $already_run = false;


	/**
	 * Starts the list before the elements are added.
	 *
	 * @see \Walker::start_el()
	 *
	 * @since 1.14.2
	 *
	 * @param string|array $output Passed by reference. Used to append additional content.
	 * @param \WP_Term $category Category data object.
	 * @param int $depth Depth of category. Used for padding.
	 * @param array $args Uses 'selected', 'show_count', and 'show_last_update' keys, if they exist.
	 * @param int $current_object_id The current object ID.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = [], $current_object_id = 0 ) {

		// if this is our first run, coerce the string into an array, then
		// adjust our counter because we don't really care after that point
		if ( ! $this->already_run ) {
			$output            = [];
			$this->already_run = true;
		}

		$output[ $category->term_id ] = $depth;
	}


}
