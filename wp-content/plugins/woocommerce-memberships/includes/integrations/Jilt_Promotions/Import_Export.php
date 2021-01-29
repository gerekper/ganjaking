<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Integrations\Jilt_Promotions;

defined( 'ABSPATH' ) or exit;

/**
 * Jilt Promotions prompt for the Memberships Import/Export pages.
 *
 * TODO remove this class by December 2021 or by version 2.0.0, whichever comes first {FN 2020-11-11}
 *
 * @since 1.17.6
 * @deprecated since 1.20.0
 */
class Import_Export {


	/**
	 * Import_Export constructor.
	 *
	 * TODO remove this method by December 2021 or by version 2.0.0, whichever comes first {FN 2020-11-11}
	 *
	 * @since 1.20.0
	 * @deprecated since 1.20.0
	 */
	public function __construct() {

		wc_deprecated_function( __CLASS__, '1.20.0' );
	}


	/**
	 * Enqueues the assets.
	 *
	 * TODO remove this method by December 2021 or by version 2.0.0, whichever comes first {FN 2020-11-11}
	 *
	 * @internal
	 *
	 * @since 1.17.6
	 * @deprecated since 1.20.0
	 */
	public function enqueue_assets() {

		wc_deprecated_function( __METHOD__, '1.20.0' );
	}


	/**
	 * Adds admin notices to be shown.
	 *
	 * TODO remove this method by December 2021 or by version 2.0.0, whichever comes first {FN 2020-11-11}
	 *
	 * @internal
	 *
	 * @since 1.17.6
	 * @deprecated since 1.20.0
	 */
	public function add_admin_notices() {

		wc_deprecated_function( __METHOD__, '1.20.0' );
	}


	/**
	 * Maybe enables a message to be shown on the import/export screens.
	 *
	 * TODO remove this method by December 2021 or by version 2.0.0, whichever comes first {FN 2020-11-11}
	 *
	 * @internal
	 *
	 * @since 1.17.6
	 * @deprecated since 1.20.0
	 */
	public function maybe_enable_import_export_message() {

		wc_deprecated_function( __METHOD__, '1.20.0' );
	}


}
