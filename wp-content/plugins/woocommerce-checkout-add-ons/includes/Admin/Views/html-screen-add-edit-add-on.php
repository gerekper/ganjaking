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

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * View for the add-on list screen
 *
 * @since 2.0.0
 *
 * @type SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On $add_on the add-on to display, if there is one
 * @type string $screen_id the current screen ID
 * @type string $screen_title the current screen title
 * @type string $new_add_on_screen_url URL to create a new add-on
 */
?>

<div class="wrap woocommerce">
	<form method="post" id="mainform" action="" enctype="multipart/form-data" class="wc-checkout-add-ons">
		<h1 class="wp-heading-inline"><?php echo esc_html( $screen_title ); ?></h1>
		<a href="<?php echo esc_url( $new_add_on_screen_url ); ?>" class="page-title-action"><?php echo esc_html_x( 'Add add-on', 'page title action', 'woocommerce-checkout-add-ons' ); ?></a>
		<hr class="wp-header-end">

		<?php wp_nonce_field( 'wc_checkout_add_ons_add_on_data' ); ?>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
							<label class="screen-reader-text" id="title-prompt-text" for="title"><?php esc_html_e( 'Add-on name (internal)', 'woocommerce-checkout-add-ons' ); ?></label>
							<input type="text"
							       name="name"
							       value="<?php echo $add_on ? esc_attr( $add_on->get_name( 'edit' ) ) : ''; ?>"
							       id="title"
							       size="30"
							       spellcheck="true"
							       autocomplete="off"
							>
						</div>
					</div>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( $screen_id, 'side', $add_on ); ?>
				</div>

				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( $screen_id, 'normal', $add_on ); ?>
				</div>

			</div>
		</div>
	</form>
</div>
