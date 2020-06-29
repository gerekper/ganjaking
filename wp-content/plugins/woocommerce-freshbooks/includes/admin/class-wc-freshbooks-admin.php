<?php
/**
 * WooCommerce FreshBooks
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * FreshBooks Admin class
 *
 * Handles general admin tasks
 *
 * @since 3.2.0
 */
class WC_FreshBooks_Admin {


	/**
	 * Add various admin hooks/filters
	 *
	 * @since 3.2.0
	 */
	public function __construct() {

		// load necessary admin styles / scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// show client ID field on edit user pages
		add_action( 'show_user_profile', array( $this, 'render_client_id_meta_field' ) );
		add_action( 'edit_user_profile', array( $this, 'render_client_id_meta_field' ) );

		// save client ID field
		add_action( 'personal_options_update',  array( $this, 'save_client_id_meta_field' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_client_id_meta_field' ) );

		// add a debug tool (to WooCommerce > System Status > Tools) to recreate webhooks
		add_filter( 'woocommerce_debug_tools', array( $this, 'add_recreate_webhook_debug_tool' ) );
	}


	/**
	 * Load admin JS/CSS
	 *
	 * @since 3.2.0
	 * @param string $hook_suffix
	 */
	public function load_styles_scripts( $hook_suffix ) {

		// Load admin JS/CSS only load on settings / order / product pages
		if ( in_array( $hook_suffix, array( Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id(), 'edit.php', 'post.php', 'post-new.php' ), true ) ) {

			wp_enqueue_script( 'wc-freshbooks-admin-scripts', wc_freshbooks()->get_plugin_url() . '/assets/js/admin/wc-freshbooks-admin.min.js', array(), \WC_FreshBooks::VERSION, true );

			wp_enqueue_style( 'wc-freshbooks-admin-styles',  wc_freshbooks()->get_plugin_url() . '/assets/css/admin/wc-freshbooks-admin.min.css', array( 'woocommerce_admin_styles' ), \WC_FreshBooks::VERSION );
		}
	}


	/**
	 * Display a field for the FreshBooks user ID meta on the view/edit user page
	 *
	 * @since 3.2.0
	 * @param \WP_User $user user object for the current edit page
	 */
	public function render_client_id_meta_field( $user ) {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		?>
		<h3><?php esc_html_e( 'FreshBooks Client Details', 'woocommerce-freshbooks' ) ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="_wc_freshbooks_client_id"><?php esc_html_e( 'User ID', 'woocommerce-freshbooks' ); ?></label></th>
				<td>
					<input type="text" name="_wc_freshbooks_client_id" id="_wc_freshbooks_client_id" value="<?php echo esc_attr( get_user_meta( $user->ID, '_wc_freshbooks_client_id', true ) ); ?>" class="regular-text" /><br/>
					<span class="description"><?php esc_html_e( 'The FreshBooks Client ID for the user. Only edit this if necessary.', 'woocommerce-freshbooks' ); ?></span>
				</td>
			</tr>
		</table>
		<?php
	}


	/**
	 * Save the FreshBooks User ID meta field on the view/edit user page
	 *
	 * @since 3.2.0
	 * @param int $user_id identifies the user to save the settings for
	 */
	public function save_client_id_meta_field( $user_id ) {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( ! empty( $_POST['_wc_freshbooks_client_id'] ) ) {
			update_user_meta( $user_id, '_wc_freshbooks_client_id', trim( $_POST['_wc_freshbooks_client_id'] ) );
		} else {
			delete_user_meta( $user_id, '_wc_freshbooks_client_id' );
		}
	}


	/**
	 * Adds a debug tool to WooCommerce > System Status > Tools to recreate
	 * webhooks
	 *
	 * @since 3.2.0
	 * @param array $tools debug tools
	 * @return array
	 */
	public function add_recreate_webhook_debug_tool( $tools ) {

		$tools['wc_freshbooks_recreate_webhook'] = array(
			'name'     => __( 'Recreate FreshBooks Webhook', 'woocommerce-freshbooks' ),
			'button'   => __( 'Recreate webhook now', 'woocommerce-freshbooks' ),
			'desc'     => __( 'This tool will recreate your FreshBooks webhook.', 'woocommerce-freshbooks' ),
			'callback' => array( $this, 'recreate_webhook' ),
		);

		return $tools;
	}


	/**
	 * Helper method used in the System Status > Tools menu to delete
	 * any existing webhooks and re-add the webhook. This is helpful to use
	 * if the webhook couldn't be created on install or somehow has gotten
	 * lost.
	 *
	 * @since 3.2.0
	 */
	public function recreate_webhook() {

		try {

			$webhooks = wc_freshbooks()->get_api()->get_webhooks();

			// webhooks exist, remove them
			if ( ! empty( $webhooks ) ) {

				foreach ( $webhooks as $webhook ) {

					wc_freshbooks()->get_api()->delete_webhook( $webhook['id'] );
				}
			}

			// create a new webhook
			$webhook_id = wc_freshbooks()->get_api()->create_webhook();

			update_option( 'wc_freshbooks_webhook_id', $webhook_id );

			return true;

		} catch ( Framework\SV_WC_API_Exception $e ) {

			wc_freshbooks()->log( $e->getMessage() );

			return false;
		}
	}


}
