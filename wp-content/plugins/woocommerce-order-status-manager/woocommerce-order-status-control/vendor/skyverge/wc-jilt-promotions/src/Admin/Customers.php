<?php
/**
 * Jilt for WooCommerce Promotions
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Jilt_Promotions\Admin;

defined( 'ABSPATH' ) or exit;

use Automattic\WooCommerce\Admin\PageController;
use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Installation;
use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Prompt;
use SkyVerge\WooCommerce\Jilt_Promotions\Messages;
use SkyVerge\WooCommerce\Jilt_Promotions\Package;

/**
 * The prompt handler for the Customers page.
 *
 * @since 1.1.0
 */
final class Customers extends Prompt {


	/** @var string the ID of the customers download message */
	private $download_message_id = 'wc-customers-download';


	/**
	 * Adds the necessary action & filter hooks.
	 *
	 * @since 1.1.0
	 */
	protected function add_prompt_hooks() {

		if ( ! Messages::is_message_enabled( $this->download_message_id ) ) {

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

			add_action( 'admin_footer', [ $this, 'render_try_jilt_modal' ] );
		}
	}


	/**
	 * Gets the connection redirect args to attribute the plugin installation to this prompt.
	 *
	 * @see Prompt::add_connection_redirect_args()
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	protected function get_connection_redirect_args() {

		$args = [];

		if ( $this->download_message_id === Installation::get_jilt_installed_from() ) {
			$args = [ 'utm_term' => $this->download_message_id ];
		}

		return $args;
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function enqueue_assets() {

		if ( $this->is_woocommerce_js_page() ) {

			wp_enqueue_style( Installation::INSTALL_SCRIPT_HANDLE );
			wp_enqueue_script( 'sv-wc-jilt-prompt-customers', Package::get_assets_url() . '/js/admin/customers.min.js', [ Installation::INSTALL_SCRIPT_HANDLE ], Package::VERSION, true );

			wp_localize_script( 'sv-wc-jilt-prompt-customers', 'sv_wc_jilt_prompt_customers', [
				'download_message_id' => $this->download_message_id,
			] );
		}
	}


	/**
	 * Outputs the template for the I want to try Jilt modal.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function render_try_jilt_modal() {

		// bail if this is not a React-based WooCommerce page
		if ( ! $this->is_woocommerce_js_page() ) {
			return;
		}

		?>
		<script type="text/template" id="tmpl-sv-wc-jilt-promotions-<?php esc_attr_e( $this->download_message_id ); ?>-modal">
			<div id="sv-wc-jilt-install-modal" class="wc-backbone-modal">
				<div class="wc-backbone-modal-content">
					<section class="wc-backbone-modal-main" role="main">
						<header class="wc-backbone-modal-header">
							<h1><?php esc_html_e( 'Communicate with your customers in minutes!', 'sv-wc-jilt-promotions' ); ?></h1>
							<button class="modal-close modal-close-link dashicons dashicons-no-alt">
								<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'sv-wc-jilt-promotions' ); ?></span>
							</button>
						</header>
						<article><?php esc_html_e( 'Jilt automatically syncs your store and customer data, so you can send powerful, personalized messages to your customers. Do you want to install Jilt for WooCommerce to start emailing customers? You’ll be able to connect with one click!', 'sv-wc-jilt-promotions' ); ?></article>
						<footer>
							<div class="sv-wc-jilt-prompt-actions inner">
								<a class="sv-wc-jilt-prompt-action" href="https://www.skyverge.com/go/customer-communication" target="_blank"><?php esc_html_e( 'Learn more', 'sv-wc-jilt-promotions' ); ?></a>
								<button id="sv-wc-jilt-install-button-install" class="sv-wc-jilt-prompt-action button button-large button-primary"><?php esc_html_e( 'I want to try Jilt', 'sv-wc-jilt-promotions' ); ?></button>
							</div>
						</footer>
					</section>
				</div>
			</div>
			<div class="wc-backbone-modal-backdrop modal-close"></div>
		</script>
		<?php
	}


	/**
	 * Determines whether the current page is a React-based WooCommerce page.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	private function is_woocommerce_js_page() {

		$is_js_page = false;

		if ( class_exists( PageController::class ) && is_callable( PageController::class, 'instance' ) && $page_controller = PageController::get_instance() ) {

			if ( is_callable( [ $page_controller, 'get_current_page' ] ) && $current_page = $page_controller->get_current_page() ) {
				$is_js_page = isset( $current_page['js_page'] ) && $current_page['js_page'];
			}
		}

		return $is_js_page;
	}


}
