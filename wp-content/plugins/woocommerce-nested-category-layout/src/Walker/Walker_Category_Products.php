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
 * @copyright Copyright (c) 2012-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Category walker for products.
 *
 * @see \SkyVerge\WooCommerce\Nested_Category_Layout\Walker\Category_Products duplicate for backwards compatibility
 * TODO remove this by version 2.0.0 or by August 2020 {FN 2020-02-19}
 *
 * @since 1.0
 * @deprecated since 1.14.2
 */
class Walker_Category_Products extends \SkyVerge\WooCommerce\Nested_Category_Layout\Walker\Category_Products {


	/**
	 * Constructor.
	 *
	 * Sends a deprecation warning when trying to instantiate this class.
	 *
	 * @since 1.0
	 * @deprecated since 1.14.2
	 *
	 * @param array $category_depths
	 */
	public function __construct( $category_depths ) {

		parent::__construct( $category_depths );

		wc_deprecated_function( 'Walker_Category_Products class', '1.14.2', '\SkyVerge\WooCommerce\Nested_Category_Layout\Walker\Category_Products class' );
	}


}
