<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YWSBS_Webhooks Class
 *
 * @class   YWSBS_Webhooks
 * @package YITH\Subscription\Classes
 * @since   2.4.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YWSBS_Webhooks' ) ) {

	/**
	 * Class YWSBS_Webhooks
	 */
	class YWSBS_Webhooks {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Webhooks
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Webhooks
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YWSBS_Webhooks constructor.
		 */
		private function __construct() {
			add_filter( 'woocommerce_webhook_topic_hooks', array( $this, 'add_subscription_topics' ), 30, 2 );
			add_filter( 'woocommerce_webhook_topics', array( $this, 'add_subscription_topics_admin_menu' ), 10, 1 );
			add_filter( 'woocommerce_webhook_payload', array( $this, 'add_webhook_payload' ), 10, 4 );
			add_filter( 'woocommerce_valid_webhook_resources', array( $this, 'add_resources' ), 10, 1 );
			add_filter( 'woocommerce_valid_webhook_events', array( $this, 'add_events' ), 10, 1 );

			add_action( 'ywsbs_updated_subscription_date', array( $this, 'add_subscription_update_callback' ), 10, 1 );
			add_action( 'ywsbs_subscription_status_changed', array( $this, 'add_subscription_update_callback' ), 10, 1 );
			add_action( 'ywsbs_subscription_created', array( $this, 'add_subscription_created_callback' ), 10, 1 );
			add_action( 'wp_trash_post', array( $this, 'add_subscription_deleted_callback' ), 10, 1 );
			add_action( 'untrashed_post', array( $this, 'add_subscription_restore_callback' ), 10, 1 );
			add_action( 'ywsbs_before_trash_subscription', array( $this, 'add_subscription_deleted_callback' ), 10, 1 );
		}

		/**
		 * Add new resource to topic resources.
		 *
		 * @param array $resources Existing valid resources.
		 */
		public function add_resources( $resources ) {

			// New resouces to be used.
			$new_resources = array(
				'ywsbs_subscription',
			);

			return array_merge( $resources, $new_resources );
		}

		/**
		 * Add new events for topic resources.
		 *
		 * @param array $topic_events Existing valid events for resources.
		 */
		public function add_events( $topic_events ) {

			// New events to be used for resources.
			$new_events = array(
				'trashed',
				'untrashed',
			);

			return array_merge( $topic_events, $new_events );
		}

		/**
		 * Adds the new webhook to the dropdown list on the Webhook page.
		 *
		 * @param array $topics Array of topics with the i18n proper name.
		 * @return array
		 */
		public function add_subscription_topics_admin_menu( $topics ) {
			$subscription_topics = array(
				'ywsbs_subscription.created'   => __( 'YITH Subscription created', 'yith-woocommerce-subscription' ),
				'ywsbs_subscription.updated'   => __( 'YITH Subscription updated', 'yith-woocommerce-subscription' ),
				'ywsbs_subscription.trashed'   => __( 'YITH Subscription deleted', 'yith-woocommerce-subscription' ),
				'ywsbs_subscription.untrashed' => __( 'YITH Subscription restored', 'yith-woocommerce-subscription' ),
			);

			return array_merge( $topics, $subscription_topics );

		}


		/**
		 * Add the subscription topic to the webhook list.
		 *
		 * @param array      $topics List of topics.
		 * @param WC_Webhook $webhook Webhook object.
		 *
		 * @return array
		 */
		public function add_subscription_topics( $topics, $webhook ) {
			switch ( $webhook->get_resource() ) {
				case 'ywsbs_subscription':
					$topics = array(
						'ywsbs_subscription.created'   => array(
							'ywsbs_webhook_subscription_created',
						),
						'ywsbs_subscription.updated'   => array(
							'ywsbs_webhook_subscription_updated',
						),
						'ywsbs_subscription.trashed'   => array(
							'ywsbs_webhook_subscription_deleted',
						),
						'ywsbs_subscription.untrashed' => array(
							'ywsbs_webhook_subscription_restored',
						),
					);
			}

			return $topics;
		}

		/**
		 * Setup payload for subscription webhook delivery.
		 *
		 * @param array  $payload Payload.
		 * @param string $resource Resource.
		 * @param int    $resource_id Subscription id.
		 * @param int    $id Webhook id.
		 *
		 * @return array|mixed
		 * @throws Exception Throwns an exception.
		 * @since 2.4
		 */
		public static function add_webhook_payload( $payload, $resource, $resource_id, $id ) {
			if ( 'ywsbs_subscription' === $resource && empty( $payload ) ) {
				$subscription = ywsbs_get_subscription( $resource_id );
				if ( ! $subscription ) {
					return $payload;
				}
				$webhook = new WC_Webhook( $id );

				$current_user = get_current_user_id();

				wp_set_current_user( $webhook->get_user_id() );

				switch ( $webhook->get_api_version() ) {
					case 'legacy_v3':
					case 'wp_api_v1':
					case 'wp_api_v2':
					case 'wp_api_v3':
						$request    = new WP_REST_Request( 'POST' );
						$controller = new YITH_YWSBS_REST_Subscriptions_Controller();
						$request->set_param( 'id', $resource_id );

						if ( in_array( $webhook->get_event(), array( 'trashed', 'untrashed' ), true ) ) {
							$payload = array( 'id' => $resource_id );
						} else {
							$result  = $controller->get_item( $request );
							$payload = isset( $result->data ) ? $result->data : array();
						}

						break;
				}

				wp_set_current_user( $current_user );
			}

			return $payload;
		}


		/**
		 * Subscription update callback hook.
		 *
		 * @param YWSBS_Subscription $subscription Subscription object.
		 */
		public function add_subscription_update_callback( $subscription ) {
			if ( is_numeric( $subscription ) ) {
				$subscription = ywsbs_get_subscription( $subscription );
			}
			do_action( 'ywsbs_webhook_subscription_updated', $subscription->get_id() );
		}

		/**
		 * Subscription created callback hook.
		 *
		 * @param int $subscription_id Subscription id.
		 */
		public function add_subscription_created_callback( $subscription_id ) {
			do_action( 'ywsbs_webhook_subscription_created', $subscription_id );
		}

		/**
		 * Subscription delete callback hook.
		 *
		 * @param int $subscription_id Subscription id.
		 */
		public function add_subscription_deleted_callback( $subscription_id ) {

			if ( get_post_type( $subscription_id ) === YITH_YWSBS_POST_TYPE ) {
				do_action( 'ywsbs_webhook_subscription_deleted', $subscription_id );
			}
		}

		/**
		 * Subscription untrashed callback hook.
		 *
		 * @param int $subscription_id Subscription id.
		 */
		public function add_subscription_restore_callback( $subscription_id ) {

			if ( get_post_type( $subscription_id ) === YITH_YWSBS_POST_TYPE ) {
				do_action( 'ywsbs_webhook_subscription_restored', $subscription_id );
			}
		}
	}
}
