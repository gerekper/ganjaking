<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Scheduler class
 *
 * @class   YITH\Subscription\RestApi\Loader
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */

namespace YITH\Subscription\RestApi\Schedulers;

defined( 'ABSPATH' ) || exit;

use YITH\Subscription\RestApi\Reports\Subscriptions\Stats\DataStore as SubscriptionDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;
/**
 * Class Scheduler
 */
class Scheduler {
	/**
	 * Single instance of the class
	 *
	 * @var Scheduler
	 */
	protected static $instance;

	/**
	 * Singleton implementation
	 *
	 * @return Scheduler
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * YWSBS_Subscription_Scheduler constructor.
	 */
	public function __construct() {
		add_action( 'save_post', array( __CLASS__, 'possibly_schedule_import' ) );
		add_action( 'ywsbs_subscription_status_changed', array( __CLASS__, 'possibly_schedule_import' ) );
		add_action( 'ywsbs_renew_subscription', array( __CLASS__, 'possibly_schedule_import' ) );
		add_action( 'woocommerce_order_status_changed', array( __CLASS__, 'possibly_schedule_import' ) );
		add_action( 'ywsbs_admin_import_subscription', array( __CLASS__, 'import' ), 10, 2 );
		add_action( 'ywsbs_import_subscriptions', array( __CLASS__, 'import_init' ), 10, 2 );
	}

	/**
	 * Import subscriptions to table.
	 *
	 * @param int $limit Limit.
	 * @param int $page Current page.
	 */
	public static function import_init_schedule( $limit, $page ) {
		$schedule_info = array(
			'hook' => 'ywsbs_import_subscriptions',
			'args' => array(
				'limit' => $limit,
				'page'  => $page,
			),
		);

		$has_hook_scheduled = as_next_scheduled_action( $schedule_info['hook'], $schedule_info['args'] );

		if ( ! $has_hook_scheduled ) {
			as_schedule_single_action( time() + 60 * 5, $schedule_info['hook'], $schedule_info['args'] );
		}
	}

	/**
	 * Import subscriptions
	 *
	 * @param int $limit Limit of orders to sync.
	 * @param int $page Page to sync.
	 */
	public static function import_init( $limit, $page ) {
		global $wpdb;
		$offset = $page > 1 ? ( $page - 1 ) * $limit : 0;

		$count = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->posts} as post
LEFT JOIN {$wpdb->postmeta} as pm ON ( pm.post_id =   post.ID AND pm.meta_key= 'subscriptions') 
			WHERE post.post_type IN ( 'shop_order', 'shop_order_refund' )
			AND post.post_status NOT IN ( 'wc-auto-draft', 'auto-draft', 'trash' )
			AND pm.post_id IS NOT NULL"
		); // phpcs:ignore unprepared SQL ok.

		$order_ids = absint( $count ) > 0 ? $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} as post
				LEFT JOIN {$wpdb->postmeta} as pm ON ( pm.post_id =   post.ID AND pm.meta_key= 'subscriptions') 
				WHERE post.post_type IN ( 'shop_order', 'shop_order_refund' )
				AND post.post_status NOT IN ( 'wc-auto-draft', 'auto-draft', 'trash' ) 
				AND pm.post_id IS NOT NULL
				ORDER BY post_date_gmt ASC
				LIMIT %d
				OFFSET %d",
				$limit,
				$offset
			)
		) : array(); // phpcs:ignore unprepared SQL ok.

		if ( ! empty( $order_ids ) ) {
			foreach ( $order_ids as $order_id ) {
				self::possibly_schedule_import( $order_id );
			}

			self::import_init_schedule( $limit, $page + 1 );
		}

	}

	/**
	 * Schedule this import if the post is an order or refund.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function possibly_schedule_import( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( ! in_array( $post_type, array( YITH_YWSBS_POST_TYPE, 'shop_order' ), true ) ) {
			return;
		}

		if ( YITH_YWSBS_POST_TYPE === $post_type ) {
			self::schedule_subscription_import( $post_id );
		} else {
			$order = wc_get_order( $post_id );

			if ( ! $order ) {
				return;
			}

			$subscriptions = $order->get_meta( 'subscriptions' );

			if ( ! empty( $subscriptions ) ) {
				foreach ( $subscriptions as $subscription_id ) {
					self::schedule_subscription_import( $subscription_id, $order->get_id() );
				}
			}
		}
	}


	/**
	 * Schedule subscription import
	 *
	 * @param int        $subscription_id Subscription Id.
	 * @param int|string $order_id Order Id.
	 *
	 * @return void;
	 * @since 2.3.0
	 */
	public static function schedule_subscription_import( $subscription_id, $order_id = '' ) {

		$schedule_info = array(
			'hook' => 'ywsbs_admin_import_subscription',
			'args' => array(
				'subscription_id' => $subscription_id,
				'order_id'        => $order_id,
			),
		);

		$has_hook_scheduled = as_next_scheduled_action( $schedule_info['hook'], $schedule_info['args'] );

		if ( ! $has_hook_scheduled ) {
			as_schedule_single_action( time() + 5, $schedule_info['hook'], $schedule_info['args'] );
		}
	}

	/**
	 * Import the new subscription inside the stats table
	 *
	 * @param int        $subscription_id Subscription id.
	 * @param int|string $order_id Order Id.
	 */
	public static function import( $subscription_id, $order_id ) {
		$subscription = ywsbs_get_subscription( $subscription_id );

		// If the subscription isn't found for some reason, skip the sync.
		if ( ! $subscription ) {
			return;
		}

		SubscriptionDataStore::get_instance()->sync_subscription( $subscription_id, $order_id );
		ReportsCache::invalidate();
	}

}
