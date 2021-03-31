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

use SkyVerge\WooCommerce\PluginFramework\v5_10_6 as Framework;

/**
 * The Default Tab Layout Admin UI and action handler for the WooCommerce Tab Manager plugin
 */


/**
 * Renders the default tab layout.
 *
 * @internal
 *
 * @since 1.0.0
 */
function wc_tab_manager_render_layout_page() {

	?>
	<div class="wrap">
		<?php

		// show any action messages
		if ( isset( $_GET['result'] ) ) :

			?>
			<div id="message" class="updated">
				<p><strong><?php
					/* translators: Placeholder: %s - updated notice (e.g. Tab layout updated, Tab layout saved, etc.) */
					printf( esc_html__( 'Tabs layout %s', 'woocommerce-tab-manager' ), $_GET['result'] );
				?></strong></p>
			</div>
			<?php

		endif;

		?>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Default tab layout', 'woocommerce-tab-manager' ); ?></h1>

		<form action="admin-post.php" method="post">

			<?php $tabs = get_option( 'wc_tab_manager_default_layout', false ); ?>

			<div class="postbox" id="woocommerce-product-data">
				<div class="inside">
					<input type="hidden" value="9c065bb457" name="woocommerce_meta_nonce" id="woocommerce_meta_nonce">
					<input type="hidden" value="/wp-admin/post.php?post=234&amp;action=edit&amp;message=1" name="_wp_http_referer">
					<div class="panel-wrap product_data">
						<?php wc_tab_manager_sortable_product_tabs( $tabs ); ?>
					</div>
				</div>
			</div>

			<p class="submit">
				<input type="hidden" name="action" value="wc_tab_manager_default_layout_save">
				<input type="submit" name="save" value="<?php esc_attr_e( 'Save changes', 'woocommerce-tab-manager' ); ?>" class="button-primary">
			</p>

		</form>
	</div>
	<?php
}


add_action( 'admin_post_wc_tab_manager_default_layout_save', 'wc_tab_manager_default_layout_save' );

/**
 * Saves the default tab layout settings.
 *
 * @internal
 *
 * @since 1.0.0
 */
function wc_tab_manager_default_layout_save() {

	$new_tabs = wc_tab_manager_process_tabs();
	$old_tabs = get_option( 'wc_tab_manager_default_layout', array() );

	do_action( 'wc_tab_manager_default_layout_before_update', $new_tabs, $old_tabs );

	update_option( 'wc_tab_manager_default_layout', $new_tabs );

	return wp_redirect( add_query_arg( array( 'page' => \WC_TAB_MANAGER::PLUGIN_ID, 'result' => 'saved' ), admin_url( 'admin.php' ) ) );
}
