<?php
/**
 * WooCommerce Product Documents
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Documents to newer
 * versions in the future. If you wish to customize WooCommerce Product Documents for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-documents/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Product_Documents;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.9.0
 *
 * @method \WC_Product_Documents get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Installs default settings.
	 *
	 * @since 1.9.0
	 */
	protected function install() {

		$admin = wc_product_documents()->get_admin_instance();

		if ( ! $admin instanceof \WC_Product_Documents_Admin ) {
			$admin = $this->get_plugin()->load_class( '/src/admin/class-wc-product-documents-admin.php', 'WC_Product_Documents_Admin' );
		}

		foreach ( $admin->get_global_settings() as $setting ) {

			if ( isset( $setting['id'], $setting['default'] ) ) {

				update_option( $setting['id'], $setting['default'] );
			}
		}
	}


}
