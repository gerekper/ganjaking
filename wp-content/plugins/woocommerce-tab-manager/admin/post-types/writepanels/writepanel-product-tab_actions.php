<?php
/**
 * WooCommerce Tab Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://docs.woocommerce.com/document/tab-manager/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Tab Manager Tab Actions panel
 *
 * Functions for displaying the Tab Manager Tab Actions panel on the Edit Tab page
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;


/**
 * Display the product tab actions meta box.
 *
 * Displays the product actions meta box - buttons for creating and deleting the tab
 *
 * @access public
 * @param object $post product post object
 */
function wc_tab_manager_product_tab_actions_meta_box( $post ) {
	?>
	<style type="text/css">
		#edit-slug-box, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
	</style>

	<ul class="wc_product_tab_actions">
		<?php wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' ); ?>

		<?php do_action( 'woocommerce_tab_manager_product_tab_actions_top', $post ); ?>

		<li class="wide">
			<input type="submit" class="button button-primary tips" name="publish" value="<?php esc_attr_e( 'Save Tab', 'woocommerce-tab-manager' ); ?>" data-tip="<?php esc_attr_e( 'Save/update the tab', 'woocommerce-tab-manager' ); ?>" />
		</li>

		<?php do_action( 'woocommerce_tab_manager_product_tab_actions', $post->ID ); ?>
		<?php do_action( 'woocommerce_tab_manager_product_tab_actions_bottom', $post ); ?>

		<?php if ( current_user_can( 'delete_post', $post->ID ) ) : ?>
			<li class="wide">
				<?php
				if ( ! EMPTY_TRASH_DAYS ) {
					$delete_text = __( 'Delete Permanently', 'woocommerce-tab-manager' );
				} else {
					$delete_text = __( 'Move to Trash', 'woocommerce-tab-manager' );
				}
				?>
				<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo esc_attr( $delete_text ); ?></a>
			</li>
		<?php endif; ?>
	</ul>
	<?php
}
