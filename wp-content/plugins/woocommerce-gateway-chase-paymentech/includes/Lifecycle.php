<?php
/**
 * WooCommerce Chase Paymentech
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Chase Paymentech to newer
 * versions in the future. If you wish to customize WooCommerce Chase Paymentech for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-chase-paymentech/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Chase_Paymentech;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_3 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.12.0
 *
 * @method \WC_Chase_Paymentech get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Plugin install method. Perform any installation tasks here.
	 *
	 * @since 1.12.0
	 */
	protected function install() {

		$admin_notice_handler = new Framework\SV_WC_Admin_Notice_Handler( $this->get_plugin() );

		// we want to display a dismissible notice for newly installed merchants to register themselves with Chase
		$admin_notice_handler->undismiss_notice( 'register-with-chase' );
	}


}
