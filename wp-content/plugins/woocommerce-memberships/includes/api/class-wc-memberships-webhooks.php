<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\API;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships webhooks handler.
 *
 * @since 1.11.0
 */
class Webhooks {


	/** @var array keeps track of webhook sent to avoid duplicates */
	private $sent_webhooks = [];


	/**
	 * Extends WooCommerce webhooks.
	 *
	 * @since 1.11.0
	 */
	public function __construct() {

		// add webhook resources and events
		add_filter( 'woocommerce_valid_webhook_resources', [ $this, 'add_resources' ] );
		add_filter( 'woocommerce_valid_webhook_events',    [ $this, 'add_events' ] );
		// add webhook topics and their hooks
		add_filter( 'woocommerce_webhook_topics',      [ $this, 'add_topics' ] );
		add_filter( 'woocommerce_webhook_topic_hooks', [ $this, 'add_topic_hooks' ], 10, 2 );

		// create webhook payloads
		add_filter( 'woocommerce_webhook_payload', [ $this, 'create_payload' ], 1, 4 );

		// check whether webhook should be delivered
		add_filter( 'woocommerce_webhook_should_deliver', [ $this, 'handle_webhook_delivery' ], 100, 3 );

		// when creating a membership plan or user membership from admin, look for posts going from auto draft to publish status
		add_action( 'transition_post_status', [ $this, 'handle_new_object_published' ], 10, 3 );

		// add actions for user membership webhooks consumption
		add_action( 'wc_memberships_user_membership_created',        [ $this, 'add_user_membership_created_webhook_action' ], 999, 2 );
		add_action( 'wc_memberships_user_membership_saved',          [ $this, 'add_user_membership_created_webhook_action' ], 999, 2 );
		add_action( 'wc_memberships_user_membership_created',        [ $this, 'add_user_membership_updated_webhook_action' ], 999, 2 );
		add_action( 'wc_memberships_user_membership_status_changed', [ $this, 'add_user_membership_updated_webhook_action' ], 999, 2 );
		add_action( 'wc_memberships_user_membership_saved',          [ $this, 'add_user_membership_updated_webhook_action' ], 999, 2 );
		add_action( 'wc_memberships_user_membership_transferred',    [ $this, 'add_user_membership_transferred_webhook_action' ], 999 );
		add_action( 'wc_memberships_user_membership_deleted',        [ $this, 'add_user_membership_deleted_webhook_action' ], 999 );

		// add actions for membership plan webhooks consumption
		add_action( 'wp_insert_post', [ $this, 'add_membership_plan_created_webhook_action' ], 999, 3 );
		add_action( 'wp_insert_post', [ $this, 'add_membership_plan_updated_webhook_action' ], 999, 3 );
		add_action( 'post_updated',   [ $this, 'add_membership_plan_updated_webhook_action' ], 999, 2 );
		add_action( 'trashed_post',   [ $this, 'add_membership_plan_deleted_webhook_action' ], 999 );
		add_action( 'untrashed_post', [ $this, 'add_membership_plan_restored_webhook_action' ], 999 );
	}


	/**
	 * Adds membership objects to webhook resources.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param string[] $resources array of resources
	 * @return string[]
	 */
	public function add_resources( array $resources ) {

		$resources[] = 'user_membership';
		$resources[] = 'membership_plan';

		return array_unique( $resources );
	}


	/**
	 * Adds memberships events to webhook events.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param string[] $events array of events
	 * @return string[]
	 */
	public function add_events( array $events ) {

		$memberships_events = [
			'created',
			'updated',
			'transferred',
			'deleted',
			'restored',
		];

		foreach ( $memberships_events as $membership_event ) {
			$events[] = $membership_event;
		}

		return array_unique( $events );
	}


	/**
	 * Adds topics to the webhooks topic selection dropdown.
	 *
	 * This is typically within the admin webhook edit screens.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param array $topics associative array
	 * @return array
	 */
	public function add_topics( array $topics ) {

		// the webhooks page was moved from API to Advanced between WC 3.0+ versions
		$is_api_webhook_page = isset( $_GET['page'], $_GET['tab'], $_GET['section'] ) && 'webhooks' === $_GET['section'] && ( 'advanced' === $_GET['tab'] || 'api' === $_GET['tab'] );

		// before WC 3.4.3 the webhook topic dropdown had a bug that didn't persist the selection
		if ( $is_api_webhook_page && Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.4.3' ) ) {

			$webhook_id = ! empty( $_GET['edit-webhook'] ) ? (int) $_GET['edit-webhook'] : 0;

			try {
				$webhook = $webhook_id > 0 ? new \WC_Webhook( $webhook_id ) : null;
				$topic   = $webhook ? $webhook->get_topic() : null;
			} catch ( \Exception $e ) {
				$topic   = null;
			}

			if ( $topic && ( Framework\SV_WC_Helper::str_starts_with( $topic, 'membership_plan' ) || Framework\SV_WC_Helper::str_starts_with( $topic, 'user_membership' ) ) ) {

				// ensures the right custom Memberships webhook chosen is persisted in the dropdown
				wc_enqueue_js( '
					jQuery( document ).ready( function( $ ) {
						$( "#webhook_topic" ).val( "' . $topic . '" ).trigger( "change" );
					} );
			' );
			}
		}

		$user_memberships_topics = [
			'user_membership.created'     => __( 'User Membership Created',     'woocommerce-memberships' ),
			'user_membership.updated'     => __( 'User Membership Updated',     'woocommerce-memberships' ),
			'user_membership.transferred' => __( 'User Membership Transferred', 'woocommerce-memberships' ),
			'user_membership.deleted'     => __( 'User Membership Deleted',     'woocommerce-memberships' ),
		];

		$membership_plan_topics = [
			'membership_plan.created'     => __( 'Membership Plan Created',     'woocommerce-memberships' ),
			'membership_plan.updated'     => __( 'Membership Plan Updated',     'woocommerce-memberships' ),
			'membership_plan.deleted'     => __( 'Membership Plan Deleted',     'woocommerce-memberships' ),
			'membership_plan.restored'    => __( 'Membership Plan Restored',    'woocommerce-memberships' ),
		];

		return array_merge( $topics, $user_memberships_topics, $membership_plan_topics );
	}


	/**
	 * Adds hooks to webhook topics.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param array $topic_hooks topic hooks associative array
	 * @param \WC_Webhook $webhook webhook object
	 * @return array
	 */
	public function add_topic_hooks( $topic_hooks, $webhook ) {

		$resource = $webhook->get_resource();

		if ( 'user_membership' === $resource ) {

			/**
			 * Filters the user memberships webhook topics.
			 *
			 * @since 1.11.0
			 *
			 * @param array $topic_hooks associative array of topics
			 * @param \WC_Webhook $webhook webhook object
			 */
			$topic_hooks = (array) apply_filters( 'wc_memberships_user_membership_webhook_topic_hooks', [
				'user_membership.created'     => [
					'wc_memberships_webhook_user_membership_created',
				],
				'user_membership.updated'     => [
					'wc_memberships_webhook_user_membership_updated',
				],
				'user_membership.transferred' => [
					'wc_memberships_webhook_user_membership_transferred',
				],
				'user_membership.deleted'     => [
					'wc_memberships_webhook_user_membership_deleted',
				],
			], $webhook );

		} elseif ( 'membership_plan' === $resource ) {

			/**
			 * Filters the membership plans webhook topics.
			 *
			 * @since 1.11.0
			 *
			 * @param array $topic_hooks associative array of topics
			 * @param \WC_Webhook $webhook webhook object
			 */
			$topic_hooks = (array) apply_filters( 'wc_memberships_membership_plan_webhook_topic_hooks', [
				'membership_plan.created'  => [
					'wc_memberships_webhook_membership_plan_created',
				],
				'membership_plan.updated'  => [
					'wc_memberships_webhook_membership_plan_updated',
				],
				'membership_plan.deleted'  => [
					'wc_memberships_webhook_membership_plan_deleted',
				],
				'membership_plan.restored' => [
					'wc_memberships_webhook_membership_plan_restored',
				],
			], $webhook );
		}

		return $topic_hooks;
	}


	/**
	 * Parses a Webhook API version (helper method).
	 *
	 * @param string $version e.g. `wp_api_v2` or `v3`
	 * @return null|string
	 */
	private function parse_api_version( $version ) {

		$version = strtolower( (string) $version );

		if ( 'false' !== strpos( $version, 'wp_api_' ) ) {
			$version = substr( $version, 7 );
		}

		return is_string( $version ) && wc_memberships()->get_rest_api_instance()->is_supported_version( $version ) ? $version : null;
	}


	/**
	 * Creates a payload for memberships webhook deliveries.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param array|\WP_REST_Response $payload payload data
	 * @param string $resource resource to be handled
	 * @param int $resource_id resource ID
	 * @param int $webhook_id webhook ID
	 * @return array|\WP_REST_Response
	 */
	public function create_payload( $payload, $resource, $resource_id, $webhook_id ) {

		if ( empty( $payload ) ) {

			// get API version to use
			try {
				$webhook     = new \WC_Webhook( $webhook_id );
				$api_version = $this->parse_api_version( $webhook->get_api_version() );
			} catch ( \Exception $e ) {
				$api_version = null;
			}

			if ( null !== $api_version ) {

				$membership_plans_api = wc_memberships()->get_rest_api_instance()->get_membership_plans( $api_version );
				$user_memberships_api = wc_memberships()->get_rest_api_instance()->get_user_memberships( $api_version );

				if ( $user_memberships_api && 'user_membership' === $resource ) {
					$payload = $this->get_payload( $user_memberships_api, $resource_id, $webhook_id );
				} elseif ( $membership_plans_api && 'membership_plan' === $resource ) {
					$payload = $this->get_payload( $membership_plans_api, $resource_id, $webhook_id );
				}
			}
		}

		return $payload;
	}


	/**
	 * Gets a webhook payload for a membership object.
	 *
	 * @since 1.11.0
	 *
	 * @param Controller\User_Memberships|Controller\Membership_Plans $api membership object API controller instance
	 * @param int $resource_id membership object ID
	 * @param int $webhook_id WooCommerce webhook ID
	 * @return array|\WP_REST_Response
	 */
	private function get_payload( $api, $resource_id, $webhook_id ) {

		$payload = [];

		try {

			$webhook  = new \WC_Webhook( $webhook_id );
			$old_user = get_current_user_id();

			wp_set_current_user( $webhook->get_user_id() );

			if ( 'deleted' === $webhook->get_event() || ! get_post( $resource_id ) ) {
				$payload = [ 'id' => (int) $resource_id ];
			} elseif ( $api instanceof Controller\User_Memberships ) {
				$payload = $api->get_formatted_item_data( wc_memberships_get_user_membership( $resource_id ) );
			} elseif ( $api instanceof Controller\Membership_Plans ) {
				$payload = $api->get_formatted_item_data( wc_memberships_get_membership_plan( $resource_id ) );
			}

			wp_set_current_user( $old_user );

		} catch( \Exception $e ) {}

		return $payload;
	}


	/**
	 * Validates whether a webhook should deliver its payload.
	 *
	 * Ensures an empty payload is not sent, unless the event is for deleted data.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param bool $deliver_payload whether webhook should delivery payload
	 * @param \WC_Webhook $webhook webhook object
	 * @param int $resource_id membership object ID
	 * @return bool
	 */
	public function handle_webhook_delivery( $deliver_payload, $webhook, $resource_id ) {

		$resource = $webhook->get_resource();

		if ( in_array( $resource, [ 'user_membership', 'membership_plan' ], true ) ) {

			if ( 'deleted' === $webhook->get_event() ) {

				$deliver_payload = true;

			} elseif ( $deliver_payload ) {

				$api_version = $this->parse_api_version( $webhook->get_api_version() );

				if ( null !== $api_version ) {

					if ( 'user_membership' === $resource ) {
						$user_memberships_api = wc_memberships()->get_rest_api_instance()->get_user_memberships( $api_version );
						$user_membership      = wc_memberships_get_user_membership( $resource_id );
						$data                 = $user_memberships_api && $user_membership ? $user_memberships_api->get_formatted_item_data( $user_membership ) : null;
					} elseif ( 'membership_plan' === $resource ) {
						$membership_plan_api  = wc_memberships()->get_rest_api_instance()->get_membership_plans( $api_version );
						$membership_plan      = wc_memberships_get_membership_plan( $resource_id );
						$data                 = $membership_plan_api && $membership_plan  ? $membership_plan_api->get_formatted_item_data( $membership_plan )  : null;
					}
				}

				$deliver_payload = ! empty( $data ) && count( $data ) > 1;
			}
		}

		return $deliver_payload;
	}


	/**
	 * Handles user membership and plan creation from admin, where the post may have an auto draft status initially.
	 *
	 * In the main memberships handler this is disregarded as it's not useful in other contexts:
	 * @see \WC_Memberships_User_Memberships::transition_post_status()
	 * But it becomes relevant in Webhooks.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param string  $new_status new status assigned to the post
	 * @param string $old_status old status the post is moving away from
	 * @param \WP_Post $post_object a WordPress post that could be of a user membership or a plan
	 */
	public function handle_new_object_published( $new_status, $old_status, $post_object ) {

		if ( in_array( $old_status, [ 'auto-draft', 'new' ], true ) ) {

			$post_type = get_post_type( $post_object );

			if ( 'wc_user_membership' === $post_type ) {
				$this->add_user_membership_created_webhook_action( null, [ 'user_membership_id' => $post_object->ID ] );
			} elseif ( 'wc_membership_plan' === $post_type && 'publish' === $new_status ) {
				$this->add_membership_plan_created_webhook_action( $post_object->ID, $post_object, false );
			}
		}
	}


	/**
	 * Adds a webhook action when a user membership is created.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param null|\WC_Memberships_Membership_Plan $membership_plan plan that a new membership granted access to
	 * @param array $args additional arguments
	 */
	public function add_user_membership_created_webhook_action( $membership_plan, $args ) {

		// bail out when the callback is for a membership updated hook
		if ( isset( $args['is_update'] ) && true === $args['is_update'] ) {
			return;
		}

		if ( isset( $args['user_membership_id'] ) && is_numeric( $args['user_membership_id'] ) ) {

			$webhook_key     = 'wc_memberships_webhook_user_membership_created';
			$user_membership = wc_memberships_get_user_membership( $args['user_membership_id'] );

			if ( ! isset( $this->sent_webhooks[ $webhook_key ] ) ) {
				$this->sent_webhooks[ $webhook_key ] = [];
			}

			if (      $user_membership
			     && ! in_array( $user_membership->get_status(), [ 'draft', 'auto-draft' ], true )
			     && ! in_array( $user_membership->get_id(), $this->sent_webhooks[ $webhook_key ], true ) ) {

				/**
				 * Fires when a user membership is created, for webhook use.
				 *
				 * @since 1.11.0
				 *
				 * @param int $user_membership_id the ID of the membership created
				 */
				do_action( 'wc_memberships_webhook_user_membership_created', $user_membership->get_id() );

				$this->sent_webhooks[ $webhook_key ][] = $user_membership->get_id();
			}
		}
	}


	/**
	 * Adds a webhook action when a user membership is updated.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_Membership_Plan|\WC_Memberships_User_Membership $object membership object passed by the current action hook
	 * @param string|array $args additional param, an array of arguments when the membership is saved in admin, or new status when there is a membership status change
	 */
	public function add_user_membership_updated_webhook_action( $object, $args ) {

		// bail out when the callback is for a membership created hook
		if ( is_array( $args ) && isset( $args['is_update'] ) && true !== $args['is_update'] ) {
			return;
		}

		$current_action     = current_action();
		$user_membership_id = 0;
		$webhook_key        = 'wc_memberships_webhook_user_membership_updated';

		if ( 'wc_memberships_user_membership_saved' === $current_action && is_array( $args ) && isset( $args['user_membership_id'] ) && is_numeric( $args['user_membership_id'] ) && ( $user_membership = wc_memberships_get_user_membership( $args['user_membership_id'] ) ) ) {
			$user_membership_id = $user_membership->get_id();
		} elseif ( 'wc_memberships_user_membership_status_changed' === $current_action && $object instanceof \WC_Memberships_User_Membership ) {
			$user_membership_id = $object->get_id();
			$webhook_key        = 'wc_memberships_webhook_user_membership_updated_status_changed';
		}

		if ( ! isset( $this->sent_webhooks[ $webhook_key ] ) ) {
			$this->sent_webhooks[ $webhook_key ] = [];
		}

		if ( $user_membership_id > 0 && ! in_array( $user_membership_id, $this->sent_webhooks[ $webhook_key ], true ) ) {

			/**
			 * Fires when a user membership is updated, for webhook use.
			 *
			 * @since 1.11.0
			 *
			 * @param int $user_membership_id the ID of the membership created
			 */
			do_action( 'wc_memberships_webhook_user_membership_updated', $user_membership_id );

			$this->sent_webhooks[ $webhook_key ][] = $user_membership_id;
		}
	}


	/**
	 * Adds a webhook action when a user membership is transferred.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership user membership having been transferred
	 */
	public function add_user_membership_transferred_webhook_action( $user_membership ) {

		if ( $user_membership instanceof \WC_Memberships_User_Membership ) {

			$webhook_key        = 'wc_memberships_webhook_user_membership_transferred';
			$user_membership_id = $user_membership->get_id();

			if ( ! isset( $this->sent_webhooks[ $webhook_key ] ) ) {
				$this->sent_webhooks[ $webhook_key ] = [];
			}

			if ( ! in_array( $user_membership_id, $this->sent_webhooks[ $webhook_key ], true ) ) {

				/**
				 * Fires when a user membership is transferred, for webhook use.
				 *
				 * @since 1.11.0
				 *
				 * @param int $user_membership_id ID of the transferred membership (after a transfer occurred)
				 */
				do_action( 'wc_memberships_webhook_user_membership_transferred', $user_membership_id );

				$this->sent_webhooks[ $webhook_key ][] = $user_membership_id;
			}
		}
	}


	/**
	 * Adds a webhook action when a user membership is deleted.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership membership being deleted
	 */
	public function add_user_membership_deleted_webhook_action( $user_membership ) {

		if ( $user_membership_id = $user_membership->get_id() ) {

			$webhook_key = 'wc_memberships_webhook_user_membership_deleted';

			if ( ! isset( $this->sent_webhooks[ $webhook_key ] ) ) {
				$this->sent_webhooks[ $webhook_key ] = [];
			}

			if ( ! in_array( (int) $user_membership_id, $this->sent_webhooks[ $webhook_key ], true ) ) {

				/**
				 * Fires when a user membership is deleted, for webhook use.
				 *
				 * @since 1.11.0
				 *
				 * @param int $user_membership_id ID of the deleted membership (immediately before it's actually removed)
				 */
				do_action( 'wc_memberships_webhook_user_membership_deleted', (int) $user_membership_id );

				$this->sent_webhooks[ $webhook_key ][] = (int) $user_membership_id;
			}
		}
	}


	/**
	 * Adds a webhook action when a membership plan is created.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param int $post_id post ID
	 * @param \WP_Post $post post object
	 * @param bool $updated whether this is an update and not a new post creation
	 */
	public function add_membership_plan_created_webhook_action( $post_id, $post, $updated ) {

		if ( 'wc_membership_plan' === get_post_type( $post ) && ! in_array( $post->post_status, [ 'new', 'auto-draft' ], true ) ) {

			if ( ! $updated ) {

				$membership_plan_id = (int) $post_id;
				$webhook_key        = 'wc_memberships_webhook_membership_plan_created';

				if ( ! isset( $this->sent_webhooks[ $webhook_key ] ) ) {
					$this->sent_webhooks[ $webhook_key ] = [];
				}

				if ( ! in_array( $membership_plan_id, $this->sent_webhooks[ $webhook_key ], true ) ) {

					/**
					 * Fires when a membership plan is created, for webhook use.
					 *
					 * @since 1.11.0
					 *
					 * @param int $membership_plan_id ID of the membership plan created
					 */
					do_action( 'wc_memberships_webhook_membership_plan_created', $membership_plan_id );

					$this->sent_webhooks[ $webhook_key ][] = $membership_plan_id;
				}

			} else {

				$this->add_membership_plan_updated_webhook_action( $post_id, $post );
			}
		}
	}


	/**
	 * Adds a webhook action when a membership plan is updated.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param int $post_id post ID
	 * @param \WP_Post $post post object
	 */
	public function add_membership_plan_updated_webhook_action( $post_id, $post ) {

		if ( 'wc_membership_plan' === get_post_type( $post ) && ! in_array( $post->post_status, [ 'new', 'auto-draft', 'trash' ], true ) ) {

			$membership_plan_id = (int) $post_id;
			$webhook_key        = 'wc_memberships_webhook_membership_plan_updated';

			if ( ! isset( $this->sent_webhooks[ $webhook_key ] ) ) {
				$this->sent_webhooks[ $webhook_key ] = [];
			}

			if ( ! in_array( $membership_plan_id, $this->sent_webhooks[ $webhook_key ], true ) ) {

				/**
				 * Fires when a membership plan is updated, for webhook use.
				 *
				 * @since 1.11.0
				 *
				 * @param int $membership_plan_id ID of the membership plan updated
				 */
				do_action( 'wc_memberships_webhook_membership_plan_updated', $membership_plan_id );

				$this->sent_webhooks[ $webhook_key ][] = $membership_plan_id;
			}
		}
	}


	/**
	 * Adds a webhook action when a membership plan is sent to trash.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param int $post_id post ID
	 */
	public function add_membership_plan_deleted_webhook_action( $post_id ) {

		if ( 'wc_membership_plan' === get_post_type( $post_id ) ) {

			$membership_plan_id = (int) $post_id;
			$webhook_key        = 'wc_memberships_webhook_membership_plan_deleted';

			if ( ! isset( $this->sent_webhooks[ $webhook_key ] ) ) {
				$this->sent_webhooks[ $webhook_key ] = [];
			}

			if ( ! in_array( $membership_plan_id, $this->sent_webhooks[ $webhook_key ], true ) ) {

				/**
				 * Fires when a membership plan is deleted (trashed), for webhook use.
				 *
				 * @since 1.11.0
				 *
				 * @param int $membership_plan_id ID of the membership plan sent to trash
				 */
				do_action( 'wc_memberships_webhook_membership_plan_deleted', $membership_plan_id );

				$this->sent_webhooks[ $webhook_key ][] = $membership_plan_id;
			}
		}
	}


	/**
	 * Adds a webhook action when a membership plan is restored from trash.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 *
	 * @param int $post_id post ID
	 */
	public function add_membership_plan_restored_webhook_action( $post_id ) {

		if ( 'wc_membership_plan' === get_post_type( $post_id ) ) {

			$membership_plan_id = (int) $post_id;
			$webhook_key        = 'wc_memberships_webhook_membership_plan_restored';

			if ( ! isset( $this->sent_webhooks[ $webhook_key ] ) ) {
				$this->sent_webhooks[ $webhook_key ] = [];
			}

			if ( ! in_array( $membership_plan_id, $this->sent_webhooks[ $webhook_key ], true ) ) {

				/**
				 * Fires when a membership plan is restored from the trash, for webhook use.
				 *
				 * @since 1.11.0
				 *
				 * @param int $membership_plan_id ID of the membership plan restored
				 */
				do_action( 'wc_memberships_webhook_membership_plan_restored', $membership_plan_id );

				$this->sent_webhooks[ $webhook_key ][] = $membership_plan_id;
			}
		}
	}


}
