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
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Frontend;

use SkyVerge\WooCommerce\Memberships\Frontend\My_Account\Members_Area;
use SkyVerge\WooCommerce\Memberships\Frontend\My_Account\Profile_Fields_Area;

defined( 'ABSPATH' ) or exit;

/**
 * Handler responsible for initializing the different memberships areas in My Account.
 *
 * @since 1.19.0
 */
class My_Account {


	/** @var Members_Area members area handler */
	private $members_area;

	/** @var Profile_Fields_Area profile fields area handler */
	private $profile_fields_area;


	/**
	 * Constructor.
	 *
	 * @since 1.19.0
	 */
	public function __construct() {

		$this->members_area        = wc_memberships()->load_class( '/src/frontend/My_Account/Members_Area.php',        Members_Area::class );
		$this->profile_fields_area = wc_memberships()->load_class( '/src/frontend/My_Account/Profile_Fields_Area.php', Profile_Fields_Area::class );

		/** @deprecated remove this legacy class alias when the plugin has fully migrated to namespaces */
		class_alias( Members_Area::class, 'WC_Memberships_Members_Area' );
	}


	/**
	 * Gets the Members Area handler instance.
	 *
	 * @since 1.19.0
	 *
	 * @return Members_Area
	 */
	public function get_members_area_instance() {

		return $this->members_area;
	}


	/**
	 * Gets the Profile Fields Area handler instance.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Fields_Area
	 */
	public function get_profile_fields_area_instance() {

		return $this->profile_fields_area;
	}


}
