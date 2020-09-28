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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Admin\Views\Meta_Boxes\Profile_Field;

use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * A meta box model for meta boxes showing on profile fields admin screens.
 *
 * @since 1.19.0
 */
abstract class Meta_Box {


	/** @var Profile_Field_Definition object */
	protected $profile_field_definition;


	/**
	 * Profile fields meta box constructor.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 */
	public function __construct( Profile_Field_Definition $profile_field_definition ) {

		$this->profile_field_definition = $profile_field_definition;
	}


	/**
	 * Gets the profile field definition in context.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Field_Definition object
	 */
	protected function get_profile_field_definition() {

		return $this->profile_field_definition;
	}


	/**
	 * Outputs the meta box HTML
	 *
	 * @since 1.19.0
	 */
	abstract public function render();


	/**
	 * Creates and renders the meta box.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 */
	public static function create_and_render( Profile_Field_Definition $profile_field_definition ) {

		$meta_box = new static( $profile_field_definition );
		$meta_box->render();
	}


}
