<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Meta_Boxes;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Add-On Publish Meta Box Class
 *
 * @since 2.0.0
 */
class Add_On_Publish extends Add_On_Meta_Box {


	public function render() {

		$button_text = wc_checkout_add_ons()->get_admin_instance()->is_new_add_on_screen()
			         ? _x( 'Create', 'save action button text', 'woocommerce-checkout-add-ons' )
			         : _x( 'Update', 'save action button text', 'woocommerce-checkout-add-ons' );
		?>

		<ul class="order_actions submitbox">

			<?php
			/**
			 * Fires when rendering the `Publish` meta box for adding or editing a checkout add-on
			 * to allow other actors to add markup here for other types of actions.
			 *
			 * @since 2.0.0
			 *
			 * @param Add_On|null the add-on being edited, or null if creating a new add-on
			 */
			do_action( 'wc_checkout_add_ons_meta_box_publish_actions', $this->add_on );
			?>

			<li class="wide">
				<?php if ( null !== $this->add_on ) : ?>
					<div id="duplicate-action">
						<a class="submitduplicate duplication"
							href="<?php echo esc_attr( wc_checkout_add_ons()->get_admin_instance()->get_duplicate_add_on_url( $this->add_on->get_id() ) ); ?>">
							<?php echo esc_html_x( 'Duplicate Add-On', 'publish box action', 'woocommerce-checkout-add-ons' ); ?>
						</a>
					</div>
					<div id="delete-action">
						<a class="submitdelete deletion"
							href="<?php echo esc_attr( wc_checkout_add_ons()->get_admin_instance()->get_delete_add_on_url( $this->add_on->get_id() ) ); ?>">
							<?php echo esc_html_x( 'Delete Add-On', 'publish box action', 'woocommerce-checkout-add-ons' ); ?>
						</a>
					</div>
				<?php endif; ?>

				<input name="save"
				       type="submit"
				       class="button save_order button-primary button-large"
				       value="<?php echo esc_attr( $button_text ); ?>"
				>
			</li>
		</ul>

		<?php
	}


}
