<?php
/**
 * WooCommerce Order Status Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @package     WC-Order-Status-Manager
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Order_Status_Manager;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.10.0
 *
 * @method \WC_Order_Status_Manager get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Constructs the class.
	 *
	 * @since 1.10.0
	 *
	 * @param \WC_Order_Status_Manager $plugin
	 */
	public function __construct( \WC_Order_Status_Manager $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.1.0',
			'1.3.0',
			'1.4.5',
			'1.6.1',
			'1.11.4',
			'1.13.1',
			'1.15.1',
			'1.15.2',
		];
	}


	/**
	 * Installs the defaults.
	 *
	 * @since 1.10.0
	 */
	protected function install() {

		$this->get_plugin()->get_icons_instance()->update_icon_options();

		// create posts for all order statuses
		$this->get_plugin()->get_order_statuses_instance()->ensure_statuses_have_posts();
	}


	/**
	 * Performs any upgrade tasks based on the provided installed version.
	 *
	 * @since 1.10.0
	 *
	 * @param string $installed_version installed version
	 */
	protected function upgrade( $installed_version ) {

		// always update icon options
		$this->get_plugin()->get_icons_instance()->update_icon_options();

		parent::upgrade( $installed_version );
	}


	/**
	 * Upgrades to v1.1.0.
	 *
	 * @since 1.10.0
	 */
	public function upgrade_to_1_1_0() {

		foreach ( $this->get_plugin()->get_order_statuses_instance()->get_core_order_statuses() as $slug => $core_status ) {

			$status  = new \WC_Order_Status_Manager_Order_Status( $slug );
			$post_id = $status->get_id();

			$slug = str_replace( 'wc-', '', $slug );

			switch ( $slug ) {

				case 'processing':
				case 'on-hold':
				case 'completed':
				case 'refunded':
					update_post_meta( $post_id, '_include_in_reports', 'yes' );
				break;
			}
		}
	}


	/**
	 * Upgrades to v1.3.0.
	 *
	 * @since 1.10.0
	 */
	public function upgrade_to_1_3_0() {

		foreach ( $this->get_plugin()->get_order_statuses_instance()->get_core_order_statuses() as $slug => $core_status ) {

			$status  = new \WC_Order_Status_Manager_Order_Status( $slug );
			$post_id = $status->get_id();

			$slug = str_replace( 'wc-', '', $slug );

			switch ( $slug ) {

				case 'processing':
				case 'completed':
					update_post_meta( $post_id, '_is_paid', 'yes' );
				break;
			}
		}
	}


	/**
	 * Upgrades to v1.4.5.
	 *
	 * @since 1.10.0
	 */
	public function upgrade_to_1_4_5() {

		foreach ( $this->get_plugin()->get_order_statuses_instance()->get_core_order_statuses() as $slug => $core_status ) {

			$status  = new \WC_Order_Status_Manager_Order_Status( $slug );
			$post_id = $status->get_id();

			$slug = str_replace( 'wc-', '', $slug );

			switch ( $slug ) {

				case 'processing':
				case 'completed':
				case 'on-hold':
					add_post_meta( $post_id, '_bulk_action', 'yes', true );
				break;
			}
		}
	}


	/**
	 * Upgrades to v1.6.1.
	 *
	 * @since 1.10.0
	 */
	public function upgrade_to_1_6_1() {

		foreach ( $this->get_plugin()->get_order_statuses_instance()->get_core_order_statuses() as $slug => $core_status ) {

			$status  = new \WC_Order_Status_Manager_Order_Status( $slug );
			$post_id = $status->get_id();

			$slug = str_replace( 'wc-', '', $slug );

			// for pending and failed statuses, update them if they're not set to "paid"
			if ( in_array( $slug, [ 'pending', 'failed' ], true ) && 'yes' !== get_post_meta( $post_id, '_is_paid', true ) ) {
				update_post_meta( $post_id, '_is_paid', 'needs_payment' );
			}

			// if this status doesn't have "is paid" meta saved, default to 'no'
			if ( ! metadata_exists( 'post', $post_id, '_is_paid' ) ) {
				add_post_meta( $post_id, '_is_paid', 'no', true );
			}
		}
	}


	/**
	 * Upgrades to v1.11.4.
	 *
	 * @since 1.11.4
	 */
	public function upgrade_to_1_11_4() {

		// update slugs for statuses with empty slugs
		foreach ( $this->get_plugin()->get_order_statuses_instance()->get_order_status_posts() as $status_post ) {

			if ( '' === $status_post->post_name ) {
				$status_post->post_name = $this->get_plugin()->get_order_statuses_instance()->truncate_order_status_slug( $status_post->post_title, $status_post->ID, $status_post->post_status, $status_post->post_type );
				wp_update_post( $status_post );
			}
		}
	}

	/**
	 * Upgrades to v1.13.1.
	 *
	 * Makes sure that 'pending' core status is marked as 'needs_payment' to avoid running into issues with WooCommerce core.
	 *
	 * @since 1.13.1
	 */
	public function upgrade_to_1_13_1() {

		$status = new \WC_Order_Status_Manager_Order_Status( 'pending' );
		$id     = $status->get_id();

		if ( $id > 0 && ! $status->needs_payment() ) {

			update_post_meta( $id, '_is_paid', 'needs_payment' );

			update_option( 'wc_order_status_manager_show_paid_pending_status_notice', 'yes' );
		}
	}


	/**
	 * Regenerates download permissions for paid orders.
	 *
	 * In v1.15.0 there was a bug where the download permissions were not being regenerated for orders moved to paid status.
	 *
	 * @see \WC_Order_Status_Manager_Order_Statuses::regenerate_download_permissions()
	 *
	 * @internal
	 * @since 1.15.1
	 *
	 * @param string|mixed|null $installed_version
	 * @return void
	 */
	public function upgrade_to_1_15_1( $installed_version = null ) : void {

		// this bug only affected v1.15.0
		if ( $installed_version && '1.15.0' !== $installed_version ) {
			return;
		}

		// this bug only affected orders placed after v1.15.0 was released
		$orders = wc_get_orders([
			'type'      => 'shop_order',
			'date_paid' => '>=2023-07-03',
			'limit'     => -1,
		]);

		$order_statuses = $this->get_plugin()->get_order_statuses_instance();

		if ( ! $orders || ! $order_statuses ) {
			return;
		}

		foreach ( $orders as $order ) {
			if ( $order instanceof \WC_Order_Refund ) {
				continue;
			}

			$order_statuses->regenerate_download_permissions( $order->get_id(), $order->get_status(), $order->get_status(), $order );
		}
	}


	/**
	 * Ensures that the v1.15.1 upgrade routine executes without errors.
	 *
	 * This is because the original v1.15.1 routine did not exclude order refunds from the query and may have triggered errors and not completed successfully.
	 *
	 * @since 1.15.2
	 *
	 * @param $installed_version
	 * @return void
	 */
	public function upgrade_to_1_15_2( $installed_version = null ) {

		// only re-apply the 1.15.1 upgrade routine if it was not completed successfully the first time - upgrading from 1.15.0 would still run the routine
		if ( $installed_version !== '1.15.1' ) {
			return;
		}

		$this->upgrade_to_1_15_1( '1.15.0' );
	}


}
