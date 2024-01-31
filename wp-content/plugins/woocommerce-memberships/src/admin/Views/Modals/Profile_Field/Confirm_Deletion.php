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

namespace SkyVerge\WooCommerce\Memberships\Admin\Views\Modals\Profile_Field;

defined( 'ABSPATH' ) or exit;

/**
 * A confirmation modal shown when a merchant wants to delete a profile field.
 *
 * @since 1.19.0
 */
class Confirm_Deletion extends \WC_Memberships_Modal {


	/** @var string the label for the Hide fields button */
	private $hide_button_label;


	/**
	 * Constructor.
	 *
	 * @since 1.19.0
	 */
	public function __construct() {

		parent::__construct();

		$this->id                  = 'wc-memberships-modal-confirm-profile-field-deletion';
		$this->title               = __( 'Profile field in use', 'woocommerce-memberships' );
		$this->hide_button_label   = __( 'Hide field', 'woocommerce-memberships' );
		$this->action_button_label = __( 'Delete field', 'woocommerce-memberships' );
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


	/**
	 * Gets the modal description.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	protected function get_description() {

		return sprintf(
			/** translators: Placeholders: %1$s - opening <strong> tag, %2$s - closing </strong> tag,  %3$s - opening <strong> tag, %4$s - closing </strong> tag */
			esc_html__( 'Deleting profile fields will also delete all data entered in the field(s). To hide the field from member-view instead, click %1$sHide field%2$s to change it to admin-only access. To proceed with deleting, click %3$sDelete field%4$s.', 'woocommerce-memberships' ),
			'<strong>', '</strong>',
			'<strong>', '</strong>'
		);
	}


	/**
	 * Gets the modal footer template.
	 *
	 * @since 1.19.0
	 *
	 * @return string HTML
	 */
	protected function get_template_footer() {

		ob_start();

		?>
		<footer>
			<div class="inner">
				<button id="btn-cancel" class="button button-large"><?php esc_html_e( 'Cancel', 'woocommerce-memberships' ); ?></button>
				<button id="btn-hide" class="button button-large <?php echo sanitize_html_class( $this->action_button_class ); ?>"><?php echo esc_html( $this->hide_button_label ); ?></button>
				<button id="btn-delete" class="button button-large <?php echo sanitize_html_class( $this->action_button_class ); ?>"><?php echo esc_html( $this->action_button_label ); ?></button>
			</div>
		</footer>
		<?php

		return ob_get_clean();
	}


}
