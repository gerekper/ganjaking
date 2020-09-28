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

namespace SkyVerge\WooCommerce\Memberships\Admin\Profile_Fields;

use SkyVerge\WooCommerce\Memberships\Admin\Profile_Fields;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The profile fields admin list screen handler.
 *
 * @since 1.19.0
 */
class List_Screen {


	/** @var Profile_Fields admin instance */
	private $profile_fields_admin_instance;


	/**
	 * The profile fields admin list screen constructor.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Fields $profile_fields instance
	 */
	public function __construct( Profile_Fields $profile_fields ) {

		$this->profile_fields_admin_instance = $profile_fields;
	}


	/**
	 * Gets the profile fields admin handler instance.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Fields instance
	 */
	private function get_profile_fields_admin_handler() {

		return $this->profile_fields_admin_instance;
	}


	/**
	 * Outputs the screen HTML.
	 *
	 * @since 1.19.0
	 */
	public function render() {

		// sanity check: should redirect deletion requests to the edit screen
		if ( $this->get_profile_fields_admin_handler()->is_delete_profile_field_definition_screen() ) {
			$screen = new Edit_Screen( $this->get_profile_fields_admin_handler() );
			$screen->render();
		}

		$list_table = new List_Table();

		?>
		<div class="wrap woocommerce wc-memberships-profile-fields">
			<form method="get" id="mainform" action="">
				<h1 class="wp-heading-inline"><?php esc_html_e( 'Profile Fields', 'woocommerce-memberships' ); ?></h1>
				<a href="<?php echo esc_url( $this->get_profile_fields_admin_handler()->get_new_profile_field_definition_screen_url() ); ?>" class="page-title-action"><?php esc_html_e( 'Add profile field', 'woocommerce-memberships' ); ?></a>
				<p><?php esc_html_e( 'Add profile fields that members or the site admin can update to store additional member information.', 'woocommerce-memberships' ); ?></p>
				<hr class="wp-header-end">
				<input type="hidden" name="page" value="wc_memberships_profile_fields" />
				<?php $list_table->prepare_items(); ?>
				<?php $list_table->display(); ?>
			</form>
		</div>
		<?php
	}


}
