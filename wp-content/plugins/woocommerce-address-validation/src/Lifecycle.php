<?php
/**
 * WooCommerce Address Validation
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Address Validation to newer
 * versions in the future. If you wish to customize WooCommerce Address Validation for your
 * needs please refer to http://docs.woocommerce.com/document/address-validation/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Address_Validation;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 2.4.0
 *
 * @method \WC_Address_Validation get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 2.4.2
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'2.0.0',
		];
	}


	/**
	 * Installs default settings.
	 *
	 * @since 2.4.0
	 */
	protected function install() {

		// load admin handler if not instantiated yet
		$admin = $this->get_plugin()->get_admin_instance();
		$admin = ! $admin instanceof \WC_Address_Validation_Admin ? $this->get_plugin()->load_class( '/src/admin/class-wc-address-validation-admin.php', 'WC_Address_Validation_Admin' ) : $admin;

		// not installed, install default options
		foreach ( $admin->get_settings_page()->get_settings() as $setting ) {

			if ( isset( $setting['default'] ) ) {

				add_option( $setting['id'], $setting['default'] );
			}
		}
	}


	/**
	 * Updates to version 2.0.0
	 *
	 * @since 2.4.2
	 */
	protected function upgrade_to_2_0_0() {

		// encourage users to switch to Addressy after upgrade
		update_option( 'wc_address_validation_encourage_addressy_upgrade_switch', true );
	}


}
