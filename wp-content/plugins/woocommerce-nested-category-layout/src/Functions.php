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
 * Returns the One True Instance of Nested Category Layout.
 *
 * @since 1.6.0
 *
 * @return \WC_Nested_Category_Layout
 */
function wc_nested_category_layout() {

	return \WC_Nested_Category_Layout::instance();
}
