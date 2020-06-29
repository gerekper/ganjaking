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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;

/**
 * The Default Tab Layout Admin UI and action handler for the WooCommerce Tab Manager plugin
 */


/**
 * Renders the default tab layout which allows global/core/3rd party tabs to be
 * rearranged.
 *
 * The following globals and variables are expected:
 *
 * @access public
 * @global WC_Tab_manager wc_tab_manager() the Tab Manager main class
 */
function wc_tab_manager_render_layout_page() {

	$tabs = get_option( 'wc_tab_manager_default_layout', false );

	// show any error messages ?>
	<form action="admin-post.php" method="post">
		<div class="wrap woocommerce">
			<?php if ( isset( $_GET['result'] ) ) : /* show any action messages */ ?>
				<div id="message" class="updated">
					<?php
					$message = sprintf( __( 'Tabs layout %s', 'woocommerce-tab-manager' ), $_GET['result'] );
					?>
					<p><strong><?php echo esc_html( $message ) ?></strong></p>
				</div>
			<?php endif; ?>

			<div class="postbox" id="woocommerce-product-data">
				<h3 class="hndle"><span><?php esc_html_e( 'Default Tab Layout', 'woocommerce-tab-manager' ); ?></span></h3>
				<div class="inside">
					<input type="hidden" value="9c065bb457" name="woocommerce_meta_nonce" id="woocommerce_meta_nonce">
					<input type="hidden" value="/wp-admin/post.php?post=234&amp;action=edit&amp;message=1" name="_wp_http_referer">

					<div class="panel-wrap product_data">
						<?php wc_tab_manager_sortable_product_tabs( $tabs ); ?>
					</div>
				</div>
			</div>
		</div>

		<p class="submit">
			<input type="hidden" name="action" value="wc_tab_manager_default_layout_save" />
			<input type="submit" name="save" value="<?php esc_attr_e( 'Save Changes', 'woocommerce-tab-manager' ); ?>" class="button-primary" />
		</p>
	</form>

	<?php
}


add_action( 'admin_post_wc_tab_manager_default_layout_save', 'wc_tab_manager_default_layout_save' );

/**
 * Save the default tab layout settings
 * @access public
 */
function wc_tab_manager_default_layout_save() {

	$new_tabs = wc_tab_manager_process_tabs();
	$old_tabs = get_option( 'wc_tab_manager_default_layout', array() );

	do_action( 'wc_tab_manager_default_layout_before_update', $new_tabs, $old_tabs );

	update_option( 'wc_tab_manager_default_layout', $new_tabs );

	return wp_redirect( add_query_arg( array( 'page' => \WC_TAB_MANAGER::PLUGIN_ID, 'result' => 'saved' ), admin_url( 'admin.php' ) ) );
}
