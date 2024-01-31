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

namespace SkyVerge\WooCommerce\Memberships\Admin\Views\Modals\User_Membership;

defined( 'ABSPATH' ) or exit;

/**
 * A confirmation modal shown when a merchant wants to edit a profile field.
 *
 * @since 1.19.0
 */
class Confirm_Edit_Profile_Fields extends \WC_Memberships_Modal {


	/**
	 * Constructor.
	 *
	 * @since 1.19.0
	 */
	public function __construct() {

		parent::__construct();

		$this->id                  = 'wc-memberships-modal-edit-member-profile-fields';
		$this->title               = __( 'Member Profile Fields', 'woocommerce-memberships' );
		$this->action_button_label = __( 'Save', 'woocommerce-memberships' );
	}


	/**
	 * Gets the modal description.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	protected function get_description() {

		return __( 'Heads up! Members will see changes to their profile fields reflected in the account area. Are you sure you want to continue?', 'woocommerce-memberships' );
	}


	/**
	 * Gets the modal body template.
	 *
	 * @since 1.19.0
	 *
	 * @return string HTML
	 */
	public function get_template_body() {

		ob_start();

		?>
		<article>
			<form id="<?php echo esc_attr( $this->get_id() ); ?>">
				<div class="wc-memberships-profile-field-modal-description">
					<?php echo $this->get_description(); ?>
				</div>
			</form>
		</article>
		<?php

		return ob_get_clean();
	}


}
