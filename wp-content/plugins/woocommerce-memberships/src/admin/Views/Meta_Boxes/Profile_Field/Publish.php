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

namespace SkyVerge\WooCommerce\Memberships\Admin\Views\Meta_Boxes\Profile_Field;

defined( 'ABSPATH' ) or exit;

/**
 * The meta box for the profile field definition edit screen.
 *
 * @since 1.19.0
 */
class Publish extends Meta_Box {


	/**
	 * Outputs the meta box HTML.
	 *
	 * @since 1.19.0
	 */
	public function render() {

		$profile_field = $this->get_profile_field_definition();
		$button_text   = $profile_field->is_new() ? _x( 'Create', 'Create new profile field button text', 'woocommerce-memberships' ) : _x( 'Update', 'Update existing profile field button text', 'woocommerce-memberships' );

		?>
		<ul class="submitbox">
			<li class="wide">

				<?php if ( ! $profile_field->is_new() ) : ?>

					<div id="delete-action">
						<a class="submitdelete deletion" href="<?php echo esc_url( wc_memberships()->get_admin_instance()->get_profile_fields_instance()->get_delete_add_on_url( $profile_field ) ); ?>">
							<?php esc_html_e( 'Delete profile field', 'woocommerce-memberships' ); ?>
						</a>
					</div>

				<?php endif; ?>

				<input
					name="save"
					type="submit"
					class="button button-primary button-large"
					value="<?php echo esc_attr( $button_text ); ?>"
				/>

			</li>
		</ul>
		<?php
	}


}
