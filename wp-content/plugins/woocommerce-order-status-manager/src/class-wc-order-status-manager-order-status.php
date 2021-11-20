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
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * # Order Status
 *
 * This class represents the Order Status object, which is persisted as a
 * Custom Post Type with the following attributes:
 *
 * * post_name - the status slug (non-prefixed)
 * * post_title - the status display name
 * * post_type - wc_order_status
 *
 * And the following postmeta:
 *
 * * _icon - string the status icon
 * * _color - string the status color
 * * _next_statuses - array of strings the next statuses by (non-prefixed) slug
 * * _action_icon - string the action icon (if any)
 * * _bulk_action - string 'yes' if this status can be applied to orders in bulk, false otherwise
 * * _include_in_reports - string 'yes' if orders with this status should be included in order reports, false otherwise
 * * _is_paid - string 'yes' if orders with this status imply a payment that has been received,
 * *   'needs_payment' if payment is required, 'no' if the status is not paid, but requires no payment (such as "on-hold" or "refunded")
 *
 * @since 1.0.0
 */
class WC_Order_Status_Manager_Order_Status {


	/** @var int Order status (post) ID */
	private $id;

	/** @var string Order status name */
	private $name;

	/** @var string Order status (post) slug */
	private $slug;

	/** @var string Order status (post) description (post_excerpt) */
	private $description;

	/** @var \WC_Order Order status post object */
	private $post;


	/**
	 * Set up order status class
	 *
	 * @since 1.0.0
	 * @param int|\WP_Post|string $id Status slug, related post object or ID
	 */
	public function __construct( $id ) {

		if ( ! $id ) {
			return;
		}

		// Get the order status post object
		if ( is_numeric( $id ) ) {
			$this->post = get_post( $id );
		} elseif ( is_object( $id ) ) {
			$this->post = $id;
		} else {

			// Try to get order status post object by slug
			$posts = get_posts( array(
				'name'           => str_replace( 'wc-', '', $id ),
				'post_type'      => 'wc_order_status',
				'posts_per_page' => 1,
			) );

			if ( ! empty( $posts ) ) {
				$this->post = $posts[0];
			}
		}

		// Load in post data
		if ( $this->post ) {

			$this->id          = $this->post->ID;
			$this->name        = $this->post->post_title;
			$this->slug        = $this->post->post_name;
			$this->description = $this->post->post_excerpt;
		}
	}


	/**
	 * Get the ID
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_id() {

		return $this->id;
	}


	/**
	 * Get the name
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_name() {

		/**
		 * Filter the name
		 *
		 * @since 1.3.0
		 * @param string $name The order status name
		 * @param string $slug The order status slug
		 */
		return apply_filters( 'wc_order_status_manager_order_status_name', $this->name, $this->slug );
	}


	/**
	 * Get the slug
	 *
	 * @since 1.0.0
	 * @param bool $include_prefix Optional. Whether to include the `wc-` prefix or not. Default is false.
	 * @return string
	 */
	public function get_slug( $include_prefix = false ) {
		return $include_prefix ? 'wc-' . $this->slug : $this->slug;
	}


	/**
	 * Get the description
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_description() {

		/**
		 * Filter the description
		 *
		 * @since 1.3.0
		 * @param string $description The order status description
		 * @param string $slug The order status slug
		 */
		return apply_filters( 'wc_order_status_manager_order_status_description', $this->description, $this->slug );
	}


	/**
	 * Get the color
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_color() {

		/**
		 * Filter the color
		 *
		 * @since 1.0.0
		 * @param string $color The hex color
		 * @param string $slug The order status slug
		 */
		return apply_filters( 'wc_order_status_manager_order_status_color', get_post_meta( $this->get_id(), '_color', true ), $this->get_slug() );
	}


	/**
	 * Get the icon
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_icon() {

		/**
		 * Filter the icon
		 *
		 * @since 1.0.0
		 * @param string $icon The icon class
		 * @param string $slug The order status slug
		 */
		return apply_filters( 'wc_order_status_manager_order_status_icon', get_post_meta( $this->get_id(), '_icon', true ), $this->get_slug() );
	}


	/**
	 * Get the action icon
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_action_icon() {

		/**
		 * Filter the action icon
		 *
		 * @since 1.0.0
		 * @param string $icon The action icon class
		 * @param string $slug The order status slug
		 */
		return apply_filters( 'wc_order_status_manager_order_status_action_icon', get_post_meta( $this->get_id(), '_action_icon', true ), $this->get_slug() );
	}


	/**
	 * Get next statuses
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_next_statuses() {

		/**
		 * Filter the next statuses
		 *
		 * @since 1.0.0
		 * @param array $next_statuses The next statuses array
		 * @param string $slug The order status slug
		 */
		return apply_filters( 'wc_order_status_manager_order_status_next_statuses', get_post_meta( $this->get_id(), '_next_statuses', true ), $this->get_slug() );
	}


	/**
	 * Get status type
	 *
	 * @since 1.0.0
	 * @return string The status type. One of 'Core', 'Third Party', or 'Custom'
	 */
	public function get_type() {

		$type = __( 'Custom', 'woocommerce-order-status-manager' );

		// hard coded core WC statuses
		$wc_core_statuses = array(
			'wc-pending',
			'wc-processing',
			'wc-on-hold',
			'wc-completed',
			'wc-cancelled',
			'wc-refunded',
			'wc-failed',
		);

		if ( in_array( $this->get_slug( true ), $wc_core_statuses, true ) ) {
			$type = __( 'Core', 'woocommerce-order-status-manager' );
		} else if ( $this->is_core_status() ) {
			$type = __( 'Third Party', 'woocommerce-order-status-manager' );
		}

		return $type;
	}


	/**
	 * Check if this status can be applied in bulk
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_bulk_action() {
		return 'yes' === get_post_meta( $this->get_id(), '_bulk_action', true );
	}


	/**
	 * Check if this status should be included in order reports
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function include_in_reports() {
		return 'yes' === get_post_meta( $this->get_id(), '_include_in_reports', true );
	}


	/**
	 * Check if this status implies a payment that has been received
	 *
	 * @since 1.3.0
	 * @return bool
	 */
	public function is_paid() {
		return 'yes' === get_post_meta( $this->get_id(), '_is_paid', true );
	}


	/**
	 * Check if this status implies that a payment is required
	 *
	 * @since 1.6.0
	 * @return bool
	 */
	public function needs_payment() {
		return 'needs_payment' === get_post_meta( $this->get_id(), '_is_paid', true );
	}


	/**
	 * Check if this status is a core (manually registered) status or not
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_core_status() {

		$core_statuses = wc_order_status_manager()->get_order_statuses_instance()->get_core_order_statuses();

		return isset( $core_statuses[ $this->get_slug( true ) ] );
	}


	/**
	 * Check if this status has an icon
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function has_icon() {
		return (bool) $this->get_icon();
	}


	/**
	 * Check if this status has an action icon
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function has_action_icon() {
		return (bool) $this->get_action_icon();
	}


	/**
	 * Check if this status has orders
	 *
	 * @since 1.0.0
	 * @param array $args Optional args for orders query
	 * @return bool
	 */
	public function has_orders( $args = array() ) {

 		$has_orders = false;

		$post_status = $this->get_slug( true );

		// Check if status has been registered
		if ( $this->get_slug() && get_post_status_object( $post_status ) ) {

			$posts = new WP_Query( wp_parse_args( $args, array(
				'fields'         => 'ids',
				'post_type'      => 'shop_order',
				'post_status'    => $post_status,
				'posts_per_page' => 1,
			) ) );

			$has_orders = $posts->post_count;
		}

		return $has_orders;
	}


}
