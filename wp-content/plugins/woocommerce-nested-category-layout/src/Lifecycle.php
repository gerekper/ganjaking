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

namespace SkyVerge\WooCommerce\Nested_Category_Layout;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.12.0
 *
 * @method \WC_Nested_Category_Layout get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Installs default settings.
	 *
	 * @since 1.12.0
	 */
	protected function install() {

		// in version 1.4 the database
		if ( $legacy_version = get_option( 'wc_nested_category_layout_db_version' ) ) {

			delete_option( 'wc_nested_category_layout_db_version' );

			$this->upgrade( $legacy_version );

			return;
		}

		// settings defaults
		add_option( 'woocommerce_subcat_posts_per_page', apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ) );
	}


}
