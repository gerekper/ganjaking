<?php
/**
 * WooCommerce Social Login
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Integrations handler.
 *
 * @since 2.5.0
 */
class WC_Social_Login_Integrations {


	/** @var \WC_Social_Login_Integrations_Memberships instance */
	private $memberships;

	/** @var bool whether WooCommerce Memberships is installed and active */
	private $memberships_active;


	/**
	 * Loads integration handlers.
	 *
	 * @since 2.5.0
	 */
	public function __construct() {

		if ( $this->is_memberships_active() ) {
			$this->memberships = wc_social_login()->load_class( '/src/integrations/class-wc-social-login-integrations-memberships.php', 'WC_Social_Login_Integrations_Memberships' );
		}
	}


	/**
	 * Checks whether WooCommerce Memberships is active.
	 *
	 * @since 2.5.0
	 *
	 * @return bool
	 */
	public function is_memberships_active() {

		if ( null === $this->memberships_active ) {
			$this->memberships_active = wc_social_login()->is_plugin_active( 'woocommerce-memberships.php' );
		}

		return $this->memberships_active;
	}


	/**
	 * Returns the WooCommerce Memberships integration handler.
	 *
	 * @since 2.5.0
	 *
	 * @return \WC_Social_Login_Integrations_Memberships
	 */
	public function get_memberships() {
		return $this->memberships;
	}


}



