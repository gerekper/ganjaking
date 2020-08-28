<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\CSV_Export\Integrations\Jilt_Promotions;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\CSV_Export\Automations\Automation;
use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Installation;
use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Prompt;
use SkyVerge\WooCommerce\Jilt_Promotions\Messages;
use SkyVerge\WooCommerce\Jilt_Promotions\Notices\Notice;

/**
 * Handler for Jilt Promotion prompts on the Automations screen.
 *
 * @since 5.1.0
 */
final class Automations extends Prompt {


	/** @var string  */
	private $automated_exports_message_id = 'coc-export-automated-exports';


	/**
	 * Adds the necessary action & filter hooks.
	 *
	 * @since 5.1.0
	 */
	protected function add_prompt_hooks() {

		if ( ! Messages::is_message_enabled( $this->automated_exports_message_id ) ) {
			add_action( 'wc_customer_order_export_automated_export_saved', [ $this, 'maybe_enable_automated_exports_message' ] );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_notices', [ $this, 'add_admin_notices' ] );
	}


	/**
	 * Gets the connection redirect args to attribute the plugin installation to this prompt.
	 *
	 * @see Prompt::add_connection_redirect_args()
	 *
	 * @since 5.1.0
	 *
	 * @return array
	 */
	protected function get_connection_redirect_args() {

		$args = [];

		if ( $this->automated_exports_message_id === Installation::get_jilt_installed_from() ) {
			$args = [ 'utm_term' => $this->automated_exports_message_id ];
		}

		return $args;
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 5.1.0
	 */
	public function enqueue_assets() {

		if ( Messages::is_message_enabled( $this->automated_exports_message_id ) ) {

			wp_enqueue_style( Installation::INSTALL_SCRIPT_HANDLE );
			wp_enqueue_script( Installation::INSTALL_SCRIPT_HANDLE );
		}
	}


	/**
	 * Enables the automated exports message when a customers automated export is saved.
	 *
	 * @internal
	 *
	 * @since 5.1.0
	 *
	 * @param Automation $automation
	 */
	public function maybe_enable_automated_exports_message( Automation $automation ) {

		if ( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS === $automation->get_export_type() ) {
			Messages::enable_message( $this->automated_exports_message_id );
		}
	}


	/**
	 * Outputs the admin notices for the Automated exports message.
	 *
	 * @internal
	 *
	 * @since 5.1.0
	 */
	public function add_admin_notices() {

		if ( Messages::is_message_enabled( $this->automated_exports_message_id ) ) {

			$notice = new Notice();

			$notice->set_message_id( $this->automated_exports_message_id );
			$notice->set_title( __( 'Communicate with customers in minutes!', 'woocommerce-customer-order-csv-export' ) );
			$notice->set_content( __( 'Jilt automatically syncs your store and customer data, so you can send powerful, personalized messages to your customers with just a few clicks.', 'woocommerce-customer-order-csv-export' ) );
			$notice->set_actions( [
				[
					'name'  => 'learn-more',
					'label' => __( 'Learn more', 'woocommerce-customer-order-csv-export' ),
					'url'   => 'https://www.skyverge.com/go/contact-customers',
					'type'  => Notice::ACTION_TYPE_LINK,
				],
				[
					'name'    => 'try-jilt',
					'label'   => __( 'I want to try Jilt', 'woocommerce-customer-order-csv-export' ),
					'primary' => true,
					'type'    => Notice::ACTION_TYPE_BUTTON,
				],
			] );

			$notice->render();
		}
	}


}


