<?php
/**
 * WC_PB_Stock_Notifications class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.10.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hooks for initiating and modifying no/low stock notification e-mails for Product Bundles.
 *
 * @class    WC_PB_Stock_Notifications
 * @version  6.15.4
 */
class WC_PB_Stock_Notifications {

	/**
	 * Save bundle stock quantity for use in downstream context.
	 * @var int
	 */
	private static $bundle_stock_quantity;

	/**
	 * Setup.
	 */
	public static function init() {

		// Initiator for no/low stock e-mail notifications.
		add_action( 'woocommerce_bundle_stock_quantity_changed', array( __CLASS__, 'bundle_stock_quantity_changed' ), 10, 3 );

		// Modify no stock notifications for bundles with insufficient stock.
		add_action( 'woocommerce_no_stock_notification', array( __CLASS__, 'before_bundle_stock_notification' ), 9, 1 );
		add_action( 'woocommerce_no_stock_notification', array( __CLASS__, 'after_bundle_stock_notification' ), 11, 1 );
		add_action( 'woocommerce_low_stock_notification', array( __CLASS__, 'before_bundle_stock_notification' ), 9, 1 );
		add_action( 'woocommerce_low_stock_notification', array( __CLASS__, 'after_bundle_stock_notification' ), 11, 1 );
	}

	/**
	 * Get bundle stock quantity remaining based on bundled items' stock alone.
	 *
	 * @param  WC_Product_Bundle  $product
	 * @return mixed
	 */
	public static function get_bundled_items_stock_quantity( $product ) {

		$bundled_items_stock_quantity = '';

		foreach ( $product->get_bundled_data_items( 'edit' ) as $bundled_data_item ) {

			$bundled_item_min_qty = $bundled_data_item->get_meta( 'quantity_min' );

			if ( 'yes' === $bundled_data_item->get_meta( 'optional' ) || 0 === $bundled_item_min_qty || is_null( $bundled_item_min_qty ) ) {
				continue;
			}

			$bundled_item_stock_quantity = $bundled_data_item->get_meta( 'max_stock' );

			// Infinite qty? Move on.
			if ( '' === $bundled_item_stock_quantity || is_null( $bundled_item_stock_quantity ) ) {
				continue;
			}

			// No stock? Break.
			if ( 0 === $bundled_item_stock_quantity ) {
				$bundled_items_stock_quantity = 0;
				break;
			}

			// How many times could this bundle be purchased if it only contained this item?
			$bundled_item_parent_stock_quantity = intval( floor( $bundled_item_stock_quantity / $bundled_item_min_qty ) );

			if ( '' === $bundled_items_stock_quantity || $bundled_item_parent_stock_quantity < $bundled_items_stock_quantity ) {
				$bundled_items_stock_quantity = $bundled_item_parent_stock_quantity;
			}
		}

		return $bundled_items_stock_quantity;
	}

	/**
	 * Send no/low stock notification e-mails when bundle stock quantity changes.
	 * If the parent stock quantity is below the notification threshold, no notifications are sent: Instead, we are leaving it up to WooCommerce to handle those, and modify the message in 'before_bundle_stock_notification'.
	 *
	 * @param  int                $to_quantity
	 * @param  int                $from_quantity
	 * @param  WC_Product_Bundle  $product
	 * @return void
	 */
	public static function bundle_stock_quantity_changed( $to_quantity, $from_quantity, $product ) {

		// Send low/no stock notification e-mails when bundles run low in stock or out of stock due to a bundled product stock change.
		if ( '' !== $to_quantity && $to_quantity < $from_quantity ) {

			// If the bundle is in the trash, don't send a notification.
			if ( 'trash' === $product->get_status() ) {
				return;
			}

			if ( false === apply_filters( 'woocommerce_bundles_send_stock_notifications', true, $product->get_id() ) ) {
				return;
			}

			$bundle_stock_quantity = $to_quantity;
			$parent_stock_quantity = $product->get_stock_quantity( 'edit' );
			$parent_stock_quantity = is_null( $parent_stock_quantity ) ? '' : $parent_stock_quantity;

			$no_stock_threshold  = absint( get_option( 'woocommerce_notify_no_stock_amount', 0 ) );
			$low_stock_threshold = function_exists( 'wc_get_low_stock_amount' ) ? absint( wc_get_low_stock_amount( $product ) ) : absint( get_option( 'woocommerce_notify_low_stock_amount', 2 ) );

			// Is this running in a background sync task?
			$is_stock_syncing = isset( $_GET[ 'action' ] ) && ( 'wp_' . get_current_blog_id() . '_wc_pb_db_sync_task_runner' ) === $_GET[ 'action' ];

			if ( $bundle_stock_quantity <= $no_stock_threshold ) {

				// If the parent quantity is below the no-stock threshold, we don't need to send a notification as WC core will eventually send one anyway.
				if ( '' !== $parent_stock_quantity && $parent_stock_quantity <= $no_stock_threshold ) {
					return;
				}

				if ( apply_filters( 'woocommerce_should_send_no_stock_notification', true, $product->get_id() ) ) {

					if ( $is_stock_syncing ) {
						self::set_deferred_transactional_emails();
					}

					do_action( 'woocommerce_no_stock', $product );

					if ( $is_stock_syncing ) {
						self::set_deferred_transactional_emails( false );
					}
				}

			} elseif ( $bundle_stock_quantity <= $low_stock_threshold ) {

				// If the parent quantity is below the low-stock threshold, we don't need to send a notification as WC core will eventually send one anyway.
				if ( '' !== $parent_stock_quantity && $parent_stock_quantity <= $low_stock_threshold ) {
					return;
				}

				if ( apply_filters( 'woocommerce_should_send_low_stock_notification', true, $product->get_id() ) ) {

					if ( $is_stock_syncing ) {
						self::set_deferred_transactional_emails();
					}

					do_action( 'woocommerce_low_stock', $product );

					if ( $is_stock_syncing ) {
						self::set_deferred_transactional_emails( false );
					}
				}
			}
		}
	}

	/**
	 * Adds filters that implement notification content changes.
	 * The content is only modified when the remaining stock is limited due to bundled item constraints.
	 * This is determined by comparing the parent stock quantity against the remaining stock quantity.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function before_bundle_stock_notification( $product ) {

		if ( ! $product->is_type( 'bundle' ) ) {
			return;
		}

		$bundled_items_stock_quantity = self::get_bundled_items_stock_quantity( $product );

		if ( '' === $bundled_items_stock_quantity ) {
			return;
		}

		self::$bundle_stock_quantity = $bundled_items_stock_quantity;

		$no_stock_threshold  = absint( get_option( 'woocommerce_notify_no_stock_amount', 0 ) );
		$low_stock_threshold = function_exists( 'wc_get_low_stock_amount' ) ? absint( wc_get_low_stock_amount( $product ) ) : absint( get_option( 'woocommerce_notify_low_stock_amount', 2 ) );

		if ( $bundled_items_stock_quantity <= $no_stock_threshold ) {
			add_filter( 'gettext', array( __CLASS__, 'filter_no_stock_notification_message' ), 10, 2 );
		} elseif ( $bundled_items_stock_quantity <= $low_stock_threshold ) {
			add_filter( 'gettext', array( __CLASS__, 'filter_low_stock_notification_message' ), 10, 2 );
			add_filter( 'woocommerce_product_get_stock_quantity', array( __CLASS__, 'filter_bundle_stock_quantity' ), 10, 2 );
		}
	}

	/**
	 * Removes filters that implement notification content changes.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function after_bundle_stock_notification( $product ) {

		if ( ! $product->is_type( 'bundle' ) ) {
			return;
		}

		self::$bundle_stock_quantity = null;

		remove_filter( 'gettext', array( __CLASS__, 'filter_no_stock_notification_message' ), 10 );
		remove_filter( 'gettext', array( __CLASS__, 'filter_low_stock_notification_message' ), 10 );
		remove_filter( 'woocommerce_product_get_stock_quantity', array( __CLASS__, 'filter_bundle_stock_quantity' ), 10 );
	}

	/**
	 * Modifies the no stock notification message.
	 *
	 * @param  string  $translation
	 * @param  string  $text
	 * @return string
	 */
	public static function filter_no_stock_notification_message( $translation, $text ) {

		if ( '%s is out of stock.' === $text ) {
			/* translators: %s: Product title */
			return __( '%s is out of stock. Please restock its contents.', 'woocommerce-product-bundles' );
		}

		return $translation;
	}

	/**
	 * Modifies the low stock notification message.
	 *
	 * @param  string  $translation
	 * @param  string  $text
	 * @return string
	 */
	public static function filter_low_stock_notification_message( $translation, $text ) {

		if ( '%1$s is low in stock. There are %2$d left.' === $text ) {
			/* translators: %1$s: Product title, %2$s: Stock quantity */
			return _n( '%1$s is low in stock (%2$d left). Please restock its contents.', '%1$s is low in stock (%2$d left). Please restock its contents.', null !== self::$bundle_stock_quantity ? self::$bundle_stock_quantity : 2 , 'woocommerce-product-bundles' );
		}

		return $translation;
	}

	/**
	 * Modifies the stock quantity in low stock notifications.
	 *
	 * @param  string  $translation
	 * @param  string  $text
	 * @return string
	 */
	public static function filter_bundle_stock_quantity( $quantity, $product ) {
		return $product->get_bundle_stock_quantity();
	}

	/**
	 * Enable/disable deferring of transactional emails.
	 *
	 * @param  boolean  $defer
	 */
	public static function set_deferred_transactional_emails( $defer = true ) {
		if ( $defer ) {
			add_filter( 'woocommerce_defer_transactional_emails', '__return_true' );
		} else {
			remove_filter( 'woocommerce_defer_transactional_emails', '__return_true' );
		}
	}
}

WC_PB_Stock_Notifications::init();
