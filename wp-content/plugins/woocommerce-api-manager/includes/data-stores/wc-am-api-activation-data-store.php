<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager API Activation Data Store Class
 *
 * @since       2.0
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/API Activation Data Store
 */
class WC_AM_API_Activation_Data_Store {

	private string $api_resource_table   = '';
	private string $api_activation_table = '';

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return null|\WC_AM_API_Activation_Data_Store
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {
		$this->api_resource_table   = WC_AM_USER()->get_api_resource_table_name();
		$this->api_activation_table = WC_AM_USER()->get_api_activation_table_name();

		add_action( 'init', array( $this, 'delete_my_account_activation' ) );
	}

	/**
	 * Get the total number of activations for a product using a
	 * Master API Key or Product Order API Key.
	 *
	 * @since 2.0
	 *
	 * @param string     $api_key Master API Key or Product Order API Key
	 * @param string|int $product_id
	 *
	 * @return false|object
	 */
	public function get_total_activations_resources_for_api_key_by_product_id( $api_key, $product_id ) {
		global $wpdb;

		$sql = "
				SELECT *
				FROM {$wpdb->prefix}" . $this->api_activation_table . "
				WHERE ( master_api_key = %s OR product_order_api_key = %s )
				AND ( assigned_product_id = %d OR product_id = %s )
			";

		$activation_resources = $wpdb->get_results( $wpdb->prepare( $sql, $api_key, $api_key, $product_id, $product_id ) );

		return ! WC_AM_FORMAT()->empty( $activation_resources ) ? $activation_resources : false;
	}

	/**
	 * Get all activations assigned to user_id grouped by product ID.
	 *
	 * @since 2.0
	 *
	 * @param int $user_id
	 *
	 * @return array|bool|null|object
	 */
	public function get_activation_resources_by_user_id( $user_id ) {
		global $wpdb;

		$sql = "
				SELECT *
				FROM {$wpdb->prefix}" . $this->api_activation_table . "
				WHERE user_id = %d
				ORDER BY product_id
			";

		$activation_resources = $wpdb->get_results( $wpdb->prepare( $sql, $user_id ) );

		return ! WC_AM_FORMAT()->empty( $activation_resources ) ? $activation_resources : false;
	}

	/**
	 * Get all activations assigned to order_id grouped by product ID.
	 *
	 * @since 2.0
	 *
	 * @param int $order_id
	 *
	 * @return array|bool|null|object
	 */
	public function get_activation_resources_by_order_id( $order_id ) {
		global $wpdb;

		$sql = "
			SELECT *
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE order_id = %d
			ORDER BY assigned_product_id
		";

		$activation_resources = $wpdb->get_results( $wpdb->prepare( $sql, $order_id ) );

		return ! WC_AM_FORMAT()->empty( $activation_resources ) ? $activation_resources : false;
	}

	/**
	 * Get all activations assigned to sub_parent_id ordered by product ID.
	 *
	 * @since 2.0
	 *
	 * @param int $sub_parent_id
	 *
	 * @return array|bool|null|object
	 */
	public function get_activation_resources_by_sub_parent_id( $sub_parent_id ) {
		global $wpdb;

		$sql = "
			SELECT *
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE sub_parent_id = %d
			ORDER BY assigned_product_id
		";

		$activation_resources = $wpdb->get_results( $wpdb->prepare( $sql, $sub_parent_id ) );

		return ! WC_AM_FORMAT()->empty( $activation_resources ) ? $activation_resources : false;
	}

	/**
	 * Gets the total number of activations for this resource.
	 *
	 * @since 2.0
	 *
	 * @param array $resources
	 *
	 * @return int
	 */
	public function get_total_activations( $resources ) {
		$total_activations = (int) array_sum( wp_list_pluck( $resources, 'activations_total' ) );

		return ! WC_AM_FORMAT()->empty( $total_activations ) ? $total_activations : 0;
	}

	/**
	 * Returns the api_resource_id using the activation_id.
	 *
	 * @since 2.0
	 *
	 * @param int $activation_id
	 *
	 * @return bool|int
	 */
	public function get_api_resource_id_by_activation_id( $activation_id ) {
		global $wpdb;

		$api_resource_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT api_resource_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE activation_id = %d
		", $activation_id ) );

		return ! WC_AM_FORMAT()->empty( $api_resource_id ) ? (int) $api_resource_id : false;
	}

	/**
	 * Returns the api_resource_id by the instance_id.
	 *
	 * @since 3.2
	 *
	 * @param string $instance_id
	 *
	 * @return bool|int
	 */
	public function get_api_resource_id_by_instance_id( $instance_id ) {
		global $wpdb;

		$api_resource_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT api_resource_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE instance = %s
		", $instance_id ) );

		return ! WC_AM_FORMAT()->empty( $api_resource_id ) ? (int) $api_resource_id : false;
	}

	/**
	 * Returns the api_resource_id by the sub_item_id.
	 *
	 * @since 3.2
	 *
	 * @param int $sub_item_id
	 *
	 * @return bool|int
	 */
	public function get_api_resource_id_by_sub_item_id( $sub_item_id ) {
		global $wpdb;

		$api_resource_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT api_resource_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE sub_item_id = %s
		", $sub_item_id ) );

		return ! WC_AM_FORMAT()->empty( $api_resource_id ) ? (int) $api_resource_id : false;
	}

	/**
	 * Returns the api_resource_id by the user_id.
	 *
	 * @since 3.2
	 *
	 * @param int $user_id
	 *
	 * @return bool|int
	 */
	public function get_api_resource_id_by_user_id( $user_id ) {
		global $wpdb;

		$api_resource_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT api_resource_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE user_id = %s
		", $user_id ) );

		return ! WC_AM_FORMAT()->empty( $api_resource_id ) ? (int) $api_resource_id : false;
	}

	/**
	 * Returns the api_resource_id by the user_id.
	 *
	 * @since 3.2
	 *
	 * @param int $order_id
	 *
	 * @return bool|int
	 */
	public function get_api_resource_id_by_order_id( $order_id ) {
		global $wpdb;

		$api_resource_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT api_resource_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE order_id = %s
		", $order_id ) );

		return ! WC_AM_FORMAT()->empty( $api_resource_id ) ? (int) $api_resource_id : false;
	}

	/**
	 * Returns the associated_api_key_id for the Associated API Key using the activation_id.
	 *
	 * @since 2.0
	 *
	 * @param int $activation_id
	 *
	 * @return bool|int
	 */
	public function get_associated_api_key_id_by_activation_id( $activation_id ) {
		global $wpdb;

		$associated_api_key_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT associated_api_key_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE activation_id = %d
		", $activation_id ) );

		return ! WC_AM_FORMAT()->empty( $associated_api_key_id ) ? (int) $associated_api_key_id : false;
	}

	/**
	 * Return the $available_resource where an activation can be added, otherwise return false if there are no resources available to add an activation.
	 *
	 * @since   2.0
	 * @updated 3.2 Added if ( $this->get_activation_count_of_activation_ids_by_api_resource_id( $resource->api_resource_id ) < $resource->activations_purchased_total ) {
	 *
	 * @param array $resources
	 *
	 * @return array|bool
	 */
	public function get_available_product_api_resource_for_activation( $resources ) {
		$total_activations_purchased = WC_AM_API_RESOURCE_DATA_STORE()->get_total_activations_purchased( $resources );
		$total_activations           = $this->get_total_activations( $resources );
		$available_resource          = array();

		if ( $total_activations < $total_activations_purchased ) {
			foreach ( $resources as $resource ) {
				$sub_id                                 = ! WC_AM_FORMAT()->empty( $resource->sub_id );
				$total_activations_with_api_resource_id = $this->get_activation_count_of_activation_ids_by_api_resource_id( $resource->api_resource_id );

				if ( $total_activations_with_api_resource_id < $resource->activations_purchased_total ) {
					if ( WCAM()->get_wc_subs_exist() && $sub_id ) {
						if ( WC_AM_SUBSCRIPTION()->is_subscription_for_order_active( $resource->sub_id ) ) {
							$available_resource[ 'api_resource_id' ]             = $resource->api_resource_id;
							$available_resource[ 'assigned_product_id' ]         = $resource->product_id;
							$available_resource[ 'activations_total' ]           = $resource->activations_total;
							$available_resource[ 'activations_purchased_total' ] = $resource->activations_purchased_total;
							$available_resource[ 'order_id' ]                    = $resource->order_id;
							$available_resource[ 'order_item_id' ]               = $resource->order_item_id;
							$available_resource[ 'sub_id' ]                      = $resource->sub_id;
							$available_resource[ 'sub_item_id' ]                 = $resource->sub_item_id;
							$available_resource[ 'sub_parent_id' ]               = $resource->sub_parent_id;

							return $available_resource;
						}
					} elseif ( ! $sub_id ) {
						$available_resource[ 'api_resource_id' ]             = $resource->api_resource_id;
						$available_resource[ 'assigned_product_id' ]         = $resource->product_id;
						$available_resource[ 'activations_total' ]           = $resource->activations_total;
						$available_resource[ 'activations_purchased_total' ] = $resource->activations_purchased_total;
						$available_resource[ 'order_id' ]                    = $resource->order_id;
						$available_resource[ 'order_item_id' ]               = $resource->order_item_id;

						return $available_resource;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Returns object of data row using the instance_id.
	 *
	 * @since 2.0
	 *
	 * @param int $instance_id
	 *
	 * @return bool|object|void|null
	 */
	public function get_row_data_by_instance_id( $instance_id ) {
		global $wpdb;

		$activation_resource = $wpdb->get_row( $wpdb->prepare( "
			SELECT *
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE instance = %s
		", $instance_id ) );

		return ! WC_AM_FORMAT()->empty( $activation_resource ) ? $activation_resource : false;
	}

	/**
	 * Returns object of data row using the activation_id.
	 *
	 * @since 2.0
	 *
	 * @param int $activation_id
	 *
	 * @return bool|object|void|null
	 */
	public function get_activation_resource_by_activation_id( $activation_id ) {
		global $wpdb;

		$activation_resource = $wpdb->get_row( $wpdb->prepare( "
			SELECT *
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE activation_id = %d
		", $activation_id ) );

		return ! WC_AM_FORMAT()->empty( $activation_resource ) ? $activation_resource : false;
	}

	/**
	 * Returns object of data row using the sub_item_id.
	 *
	 * @since 2.1.2
	 *
	 * @param int $sub_item_id
	 *
	 * @return bool|object|void|null
	 */
	public function get_activation_resource_by_sub_item_id( $sub_item_id ) {
		global $wpdb;

		$activation_resource = $wpdb->get_row( $wpdb->prepare( "
			SELECT *
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE sub_item_id = %s
		", $sub_item_id ) );

		return ! WC_AM_FORMAT()->empty( $activation_resource ) ? $activation_resource : false;
	}

	/**
	 * Return total number of activations for an api_resource_id.
	 *
	 * @since 2.0
	 *
	 * @param $api_resource_id
	 *
	 * @return int
	 */
	public function get_activation_count_of_activation_ids_by_api_resource_id( $api_resource_id ) {
		global $wpdb;

		$activations_count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(activation_id)
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE api_resource_id = %d
		", $api_resource_id ) );

		return ! WC_AM_FORMAT()->empty( $activations_count ) ? (int) $activations_count : 0;
	}

	/**
	 * Return total number of activations.
	 *
	 * @since 2.1
	 *
	 * @return int
	 */
	public function get_activation_count() {
		global $wpdb;

		$activations_count = $wpdb->get_var( "
			SELECT COUNT(activation_id)
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
		" );

		return ! WC_AM_FORMAT()->empty( $activations_count ) ? (int) $activations_count : 0;
	}

	/**
	 * Get array of activation IDs using an order ID from the api_activation_table.
	 *
	 * @since   2.0
	 * @updated 3.2 Wrapper for $this->get_activations_by_order_id_from_api_activation_table();
	 *
	 * @param int $order_id
	 *
	 * @return array|bool
	 */
	public function get_activations_by_order_id( $order_id ) {
		return $this->get_activations_by_order_id_from_api_activation_table( $order_id );
	}

	/**
	 * Get array of activation IDs using an order ID from the api_activation_table.
	 *
	 * @since   2.5.5
	 * @updated 3.1.2 Replaced activation_ids with actual column name activation_id for the api_activation_table.
	 *
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function get_activations_by_order_id_from_api_activation_table( $order_id ) {
		global $wpdb;

		$ids = array();

		$activation_ids = $wpdb->get_results( $wpdb->prepare( "
			SELECT activation_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE order_id = %d
		", $order_id ), ARRAY_A );

		if ( ! WC_AM_FORMAT()->empty( $activation_ids ) && is_array( $activation_ids ) ) {
			foreach ( $activation_ids as $k ) {
				$ids[] = $k[ 'activation_id' ];
			}
		}

		return ! WC_AM_FORMAT()->empty( $ids ) ? $ids : array();
	}

	/**
	 * Get array of activation IDs by sub_item_id.
	 *
	 * @since   2.0
	 * @updated 3.2 Updated to get results from api_activation_table.
	 *
	 * @param int $sub_item_id
	 *
	 * @return array|bool
	 */
	public function get_activations_by_sub_item_id( $sub_item_id ) {
		global $wpdb;

		$ids = array();

		$activation_ids = $wpdb->get_results( $wpdb->prepare( "
			SELECT activation_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE sub_item_id = %d
		", $sub_item_id ), ARRAY_A );

		if ( ! WC_AM_FORMAT()->empty( $activation_ids ) && is_array( $activation_ids ) ) {
			foreach ( $activation_ids as $k ) {
				$ids[] = $k[ 'activation_id' ];
			}
		}

		return ! WC_AM_FORMAT()->empty( $ids ) ? $ids : array();
	}

	/**
	 * Get array of activation IDs by sub_id.
	 *
	 * @since   2.0
	 * @updated 3.2 Updated to get results from api_activation_table.
	 *
	 * @param int $sub_id
	 *
	 * @return array|bool
	 */
	public function get_activations_by_subscription_order_id( $sub_id ) {
		global $wpdb;

		$ids = array();

		$activation_ids = $wpdb->get_results( $wpdb->prepare( "
			SELECT activation_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE sub_id = %d
		", $sub_id ), ARRAY_A );

		if ( ! WC_AM_FORMAT()->empty( $activation_ids ) && is_array( $activation_ids ) ) {
			foreach ( $activation_ids as $k ) {
				$ids[] = $k[ 'activation_id' ];
			}
		}

		return ! WC_AM_FORMAT()->empty( $ids ) ? $ids : array();
	}

	/**
	 * Get array of activation IDs by order_item_id.
	 *
	 * @since   2.0
	 * @updated 3.2 Updated to get results from api_activation_table.
	 *
	 * @param int $order_item_id
	 *
	 * @return array|bool
	 */
	public function get_activations_by_order_item_id( $order_item_id ) {
		global $wpdb;

		$ids = array();

		$activation_ids = $wpdb->get_results( $wpdb->prepare( "
			SELECT activation_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE order_item_id = %d
		", $order_item_id ), ARRAY_A );

		if ( ! WC_AM_FORMAT()->empty( $activation_ids ) && is_array( $activation_ids ) ) {
			foreach ( $activation_ids as $k ) {
				$ids[] = $k[ 'activation_id' ];
			}
		}

		return ! WC_AM_FORMAT()->empty( $ids ) ? $ids : array();
	}

	/**
	 * Returns an array of activation_ids using the api_resource_id.
	 *
	 * @since 3.2
	 *
	 * @param int $api_resource_id
	 *
	 * @return array
	 */
	public function get_activation_ids_by_api_resource_id( $api_resource_id ) {
		global $wpdb;

		$ids = array();

		$activation_ids = $wpdb->get_results( $wpdb->prepare( "
			SELECT activation_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE api_resource_id = %d
		", $api_resource_id ), ARRAY_A );

		if ( ! WC_AM_FORMAT()->empty( $activation_ids ) && is_array( $activation_ids ) ) {
			foreach ( $activation_ids as $k ) {
				$ids[] = $k[ 'activation_id' ];
			}
		}

		return ! WC_AM_FORMAT()->empty( $ids ) ? $ids : array();
	}

	/**
	 * Returns the Instance ID.
	 *
	 * @since 2.2.8
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool|string
	 */
	public function get_instance_id_by_api_resource_id( $api_resource_id ) {
		global $wpdb;

		$instance_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT instance
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE api_resource_id = %d
		", $api_resource_id ) );

		return ! WC_AM_FORMAT()->empty( $instance_id ) ? $instance_id : false;
	}

	/**
	 * Get all API Activation Order IDs.
	 *
	 * @since 2.5.5
	 *
	 * @return array
	 */
	public function get_all_order_ids() {
		global $wpdb;

		$order_ids = $wpdb->get_col( "
			SELECT DISTINCT order_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
		" );

		return ! WC_AM_FORMAT()->empty( $order_ids ) ? $order_ids : array();
	}

	/**
	 * Returns true if activations exist for an order.
	 *
	 * @since 2.1.5
	 *
	 * @param int $order_id
	 *
	 * @return bool
	 */
	public function has_activations_for_order_id( $order_id ) {
		global $wpdb;

		$sql = "
			SELECT activation_id
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE order_id = %d
			LIMIT 1
		";

		$has_activations = $wpdb->get_var( $wpdb->prepare( $sql, $order_id ) );

		return ! WC_AM_FORMAT()->empty( $has_activations );
	}

	/**
	 * Returns true if the instance ID is associatiated with an activation.
	 *
	 * @since 2.0
	 *
	 * @param string $instance
	 *
	 * @return bool
	 */
	public function is_instance_activated( $instance ) {
		global $wpdb;

		$sql = "
			SELECT instance
			FROM {$wpdb->prefix}" . $this->api_activation_table . "
			WHERE instance = %s
		";

		$activation = $wpdb->get_var( $wpdb->prepare( $sql, $instance ) );

		return ! WC_AM_FORMAT()->empty( $activation );
	}

	/**
	 * Add the activation data to the wc_am_api_activation table, and update the activations_total field in the wc_am_api_resource table.
	 *
	 * @since 2.0
	 *
	 * @param int   $user_id
	 * @param array $resources
	 * @param array $request_data
	 *
	 * @return bool
	 */
	public function add_api_key_activation( $user_id, $resources, $request_data ) {
		// Returns the $available_resource where an activation can be added. If an $available_resource is returned then at least one activation can be added.
		$available_resource = $this->get_available_product_api_resource_for_activation( $resources );

		if ( ! WC_AM_FORMAT()->empty( $available_resource ) && ! WC_AM_FORMAT()->empty( $resources ) && ! WC_AM_FORMAT()->empty( $request_data ) ) {
			global $wpdb;

			$master_api_key = current( wp_list_pluck( $resources, 'master_api_key' ) );

			if ( empty( $master_api_key ) ) {
				return false;
			}

			$product_order_api_key = current( wp_list_pluck( $resources, 'product_order_api_key' ) );
			$associated_api_key_id = WC_AM_ASSOCIATED_API_KEY_DATA_STORE()->get_associated_api_key_id_by_associated_api_key( $request_data[ 'api_key' ] );
			$associated_api_key_id = ! WC_AM_FORMAT()->empty( $associated_api_key_id ) && ( $request_data[ 'api_key' ] != $master_api_key || $request_data[ 'api_key' ] != $product_order_api_key ) ? (int) $associated_api_key_id : 0;

			$data = array(
				'activation_time'       => WC_AM_ORDER_DATA_STORE()->get_current_time_stamp(),
				'api_key'               => ! WC_AM_FORMAT()->empty( $request_data[ 'api_key' ] ) ? (string) $request_data[ 'api_key' ] : '',
				'api_resource_id'       => ! WC_AM_FORMAT()->empty( $available_resource[ 'api_resource_id' ] ) ? (int) $available_resource[ 'api_resource_id' ] : 0,
				'assigned_product_id'   => ! WC_AM_FORMAT()->empty( $available_resource[ 'assigned_product_id' ] ) ? (int) $available_resource[ 'assigned_product_id' ] : 0,
				'associated_api_key_id' => $associated_api_key_id,
				'ip_address'            => ! WC_AM_FORMAT()->empty( $request_data[ 'user_ip' ] ) ? (string) $request_data[ 'user_ip' ] : '',
				'instance'              => ! WC_AM_FORMAT()->empty( $request_data[ 'instance' ] ) ? (string) $request_data[ 'instance' ] : '',
				'master_api_key'        => (string) $master_api_key,
				'object'                => ! WC_AM_FORMAT()->empty( $request_data[ 'object' ] ) ? (string) $request_data[ 'object' ] : '',
				'order_id'              => ! WC_AM_FORMAT()->empty( $available_resource[ 'order_id' ] ) ? (int) $available_resource[ 'order_id' ] : 0,
				'order_item_id'         => ! WC_AM_FORMAT()->empty( $available_resource[ 'order_item_id' ] ) ? (int) $available_resource[ 'order_item_id' ] : 0,
				'product_id'            => ! WC_AM_FORMAT()->empty( $request_data[ 'product_id' ] ) ? (string) $request_data[ 'product_id' ] : '',
				'product_order_api_key' => ! WC_AM_FORMAT()->empty( $product_order_api_key ) ? (string) $product_order_api_key : '',
				'sub_id'                => isset( $available_resource[ 'sub_id' ] ) && ! WC_AM_FORMAT()->empty( $available_resource[ 'sub_id' ] ) ? (int) $available_resource[ 'sub_id' ] : 0,
				'sub_item_id'           => isset( $available_resource[ 'sub_item_id' ] ) && ! WC_AM_FORMAT()->empty( $available_resource[ 'sub_item_id' ] ) ? (int) $available_resource[ 'sub_item_id' ] : 0,
				'sub_parent_id'         => isset( $available_resource[ 'sub_parent_id' ] ) && ! WC_AM_FORMAT()->empty( $available_resource[ 'sub_parent_id' ] ) ? (int) $available_resource[ 'sub_parent_id' ] : 0,
				'version'               => ! WC_AM_FORMAT()->empty( $request_data[ 'version' ] ) ? (string) WC_AM_FORMAT()->string_to_version( $request_data[ 'version' ] ) : '',
				'user_id'               => (int) $user_id
			);

			$format = array(
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d'
			);

			$result = $wpdb->insert( $wpdb->prefix . $this->api_activation_table, $data, $format );

			if ( ! WC_AM_FORMAT()->empty( $result ) ) {
				$data = array(
					'activations_total' => $available_resource[ 'activations_total' ] + 1
				);

				$where = array(
					'api_resource_id' => $available_resource[ 'api_resource_id' ]
				);

				$data_format = array(
					'%d'
				);

				$where_format = array(
					'%d'
				);

				$wpdb->update( $wpdb->prefix . $this->api_resource_table, $data, $where, $data_format, $where_format );

				return true;
			}
		}

		return false;
	}

	/**
	 * Update the version data for an activation.
	 *
	 * @since 2.0.10
	 *
	 * @param $instance
	 * @param $version
	 */
	public function update_version( $instance, $version ) {
		if ( ! WC_AM_FORMAT()->empty( $instance ) && ! WC_AM_FORMAT()->empty( $version ) ) {
			global $wpdb;

			$data = array(
				'version' => $version
			);

			$where = array(
				'instance' => $instance
			);

			$data_format = array(
				'%s'
			);

			$where_format = array(
				'%s'
			);

			$wpdb->update( $wpdb->prefix . $this->api_activation_table, $data, $where, $data_format, $where_format );
		}
	}

	/**
	 * Replace the Master API Key value.
	 *
	 * @since 2.0.12
	 *
	 * @param string $mak
	 * @param int    $user_id
	 */
	public function update_master_api_key( $mak, $user_id ) {
		if ( ! WC_AM_FORMAT()->empty( $mak ) ) {
			global $wpdb;

			$data = array(
				'master_api_key' => $mak
			);

			$where = array(
				'user_id' => (int) $user_id
			);

			$data_format = array(
				'%s'
			);

			$where_format = array(
				'%d'
			);

			$wpdb->update( $wpdb->prefix . $this->api_activation_table, $data, $where, $data_format, $where_format );
		}
	}

	/**
	 * Delete activation in My Account > API Keys row.
	 *
	 * @since 2.0
	 */
	public function delete_my_account_activation() {
		$request = wc_clean( $_REQUEST );

		if ( ! WC_AM_FORMAT()->empty( $request ) && is_array( $request ) && isset( $request[ 'wcam_delete_activation' ] ) && isset( $request[ 'instance' ] ) && isset( $request[ '_wpnonce' ] ) ) {
			if ( ! wp_verify_nonce( $request[ '_wpnonce' ], 'wcam_delete_activation' ) ) {
				wc_add_notice( esc_html__( 'The activation could not be deleted.', 'woocommerce-api-manager' ), 'error' );

				wp_safe_redirect( esc_url( WC_AM_URL()->get_api_keys_url() ) );

				exit();
			}

			$result = $this->delete_api_key_activation_by_instance_id( $request[ 'instance' ] );

			$to_delete = array(
				'instance'      => $request[ 'instance' ],
				'order_id'      => $request[ 'order_id' ],
				'sub_parent_id' => $request[ 'sub_parent_id' ],
				'api_key'       => $request[ 'api_key' ],
				'product_id'    => $request[ 'product_id' ],
				'user_id'       => $request[ 'user_id' ]
			);

			/**
			 * Delete cache.
			 *
			 * @since 2.2.0
			 */
			WC_AM_SMART_CACHE()->delete_cache( wc_clean( array( 'admin_resources' => $to_delete ) ), true );

			if ( ! WC_AM_FORMAT()->empty( $result ) ) {
				wp_safe_redirect( esc_url( WC_AM_URL()->get_api_keys_url() ) );

				exit();
			} else {
				wc_add_notice( esc_html__( 'The activation could not be deleted.', 'woocommerce-api-manager' ), 'error' );
			}
		}
	}

	/**
	 * Deletes all API Key activations by activation ID.
	 *
	 * @since   2.0
	 * @updated 3.2 Added update_activations_total_by_api_resource_id().
	 *
	 * @param int $activation_id
	 *
	 * @return bool
	 */
	public function delete_api_key_activation_by_activation_id( $activation_id ) {
		$api_resource_id = $this->get_api_resource_id_by_activation_id( $activation_id );
		$result          = $this->delete_by( array( 'activation_id' => $activation_id ), array( '%d' ) );

		if ( ! WC_AM_FORMAT()->empty( $result ) ) {
			// Update the activations_total in the api_resource_table.
			WC_AM_API_RESOURCE_DATA_STORE()->update_activations_total_by_api_resource_id( $api_resource_id );
		}

		return $result;
	}

	/**
	 * Delete all API Key activations by api_resource_id.
	 *
	 * @since   2.0
	 * @updated 3.2 Added update_activations_total_by_api_resource_id().
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function delete_api_key_activation_by_api_resource_id( $api_resource_id ) {
		$result = $this->delete_by( array( 'api_resource_id' => $api_resource_id ), array( '%d' ) );

		if ( ! WC_AM_FORMAT()->empty( $result ) ) {
			// Update the activations_total in the api_resource_table.
			WC_AM_API_RESOURCE_DATA_STORE()->update_activations_total_by_api_resource_id( $api_resource_id );
		}

		return $result;
	}

	/**
	 * Delete all API Key activations by sub_item_id.
	 *
	 * @since   2.1
	 * @updated 3.2 Added update_activations_total_by_api_resource_id().
	 *
	 * @param int $sub_item_id
	 *
	 * @return bool
	 */
	public function delete_api_key_activation_by_sub_item_id( $sub_item_id ) {
		$api_resource_id = $this->get_api_resource_id_by_sub_item_id( $sub_item_id );
		$result          = $this->delete_by( array( 'sub_item_id' => $sub_item_id ), array( '%d' ) );

		if ( ! WC_AM_FORMAT()->empty( $result ) ) {
			// Update the activations_total in the api_resource_table.
			WC_AM_API_RESOURCE_DATA_STORE()->update_activations_total_by_api_resource_id( $api_resource_id );
		}

		return $result;
	}

	/**
	 * Deletes all API Key activations with the User ID.
	 *
	 * @since   2.0
	 * @updated 3.2 Added update_activations_total_by_api_resource_id().
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public function delete_api_key_activation_by_user_id( $user_id ) {
		$api_resource_id = $this->get_api_resource_id_by_user_id( $user_id );
		$result          = $this->delete_by( array( 'user_id' => $user_id ), array( '%d' ) );

		if ( ! WC_AM_FORMAT()->empty( $result ) ) {
			// Update the activations_total in the api_resource_table.
			WC_AM_API_RESOURCE_DATA_STORE()->update_activations_total_by_api_resource_id( $api_resource_id );
		}

		return $result;
	}

	/**
	 * Delete all API Key activations by order_id.
	 *
	 * @since   2.0
	 * @updated 3.2 Added update_activations_total_by_api_resource_id().
	 *
	 * @param int $order_id
	 *
	 * @return bool
	 */
	public function delete_api_key_activation_by_order_id( $order_id ) {
		$api_resource_id = $this->get_api_resource_id_by_order_id( $order_id );
		$result          = $this->delete_by( array( 'order_id' => $order_id ), array( '%d' ) );

		if ( ! WC_AM_FORMAT()->empty( $result ) ) {
			// Update the activations_total in the api_resource_table.
			WC_AM_API_RESOURCE_DATA_STORE()->update_activations_total_by_api_resource_id( $api_resource_id );
		}

		return $result;
	}

	/**
	 * Delete all API Key activations by instance_id.
	 *
	 * @since    2.0
	 * @updated  3.2 Refactored. Added update_activations_total_by_api_resource_id().
	 *
	 * @param string $instance_id
	 *
	 * @return bool
	 */
	public function delete_api_key_activation_by_instance_id( $instance_id ) {
		$api_resource_id = $this->get_api_resource_id_by_instance_id( $instance_id );
		$result          = $this->delete_by( array( 'instance' => $instance_id ), array( '%s' ) );

		if ( ! WC_AM_FORMAT()->empty( $result ) ) {
			// Update the activations_total in the api_resource_table.
			WC_AM_API_RESOURCE_DATA_STORE()->update_activations_total_by_api_resource_id( $api_resource_id );
		}

		return $result;
	}

	/**
	 * Deletes all rows with $needle value(s).
	 *
	 * @since 2.0
	 *
	 * @param array $needle What to delete. i.e. array( 'user_id' => $user_id ). ( 'string' => int|string )
	 * @param array $format Either %s or %d. i.e. array( '%d' ). ( 'string' )
	 *
	 * @return bool
	 */
	public function delete_by( $needle, $format ) {
		global $wpdb;

		$result = $wpdb->delete( $wpdb->prefix . $this->api_activation_table, $needle, $format );

		return ! WC_AM_FORMAT()->empty( $result );
	}

	/**
	 * Delete all API Resource Activation IDs by User ID.
	 *
	 * @since 2.1.3
	 *
	 * @param int $user_id
	 */
	public function delete_all_api_resource_activation_ids_by_user_id( $user_id ) {
		global $wpdb;

		$this->delete_api_key_activation_by_user_id( $user_id );

		$data = array(
			'activations_total' => 0
		);

		$where = array(
			'user_id' => $user_id
		);

		$data_format = array(
			'%d'
		);

		$where_format = array(
			'%d'
		);

		$wpdb->update( $wpdb->prefix . $this->api_resource_table, $data, $where, $data_format, $where_format );
	}

	/**
	 * Delete excess API Key activations by activation resource ID.
	 *
	 * @since   2.0
	 * @updated 3.2 Updated to use the wc_am_api_activation table for activation data.
	 *
	 * @param int $api_resource_id
	 * @param int $activations_purchased_total
	 */
	public function delete_excess_api_key_activations_by_activation_id( $api_resource_id, $activations_purchased_total ) {
		$activation_ids       = $this->get_activation_ids_by_api_resource_id( $api_resource_id );
		$activation_ids_total = WC_AM_FORMAT()->count( $activation_ids );

		if ( ! WC_AM_FORMAT()->empty( $activation_ids_total ) && $activation_ids_total > 0 && ! WC_AM_FORMAT()->empty( $activations_purchased_total ) && $activation_ids_total > (int) $activations_purchased_total ) {
			$num_to_delete = $activation_ids_total - $activations_purchased_total;

			if ( $num_to_delete > 0 ) {
				for ( $i = 0; $i < $num_to_delete; $i ++ ) {
					if ( ! WC_AM_FORMAT()->empty( $activation_ids ) ) {
						$activation_id = current( $activation_ids );

						if ( ! WC_AM_FORMAT()->empty( $activation_id ) ) {
							$this->delete_api_key_activation_by_activation_id( $activation_id );

							array_pop( $activation_ids );
						}
					}
				}
			}
		}
	}

	/**
	 * Delete all API Key activations by api_resource_id.
	 *
	 * @since   2.5.5
	 * @updated 3.2 Removed WC_AM_ASSOCIATED_API_KEY_DATA_STORE()->delete_associated_api_key_activation_ids()
	 *
	 * @param int $api_resource_id
	 */
	public function delete_all_api_key_activations_by_api_resource_id( $api_resource_id ) {
		$activation_ids = $this->get_activation_ids_by_api_resource_id( $api_resource_id );

		if ( ! WC_AM_FORMAT()->empty( $activation_ids ) ) {
			foreach ( $activation_ids as $k => $activation_id ) {
				// Deletes all the API Key activations with the activation ID.
				$this->delete_api_key_activation_by_activation_id( $activation_id );
			}
		}
	}

	/**
	 * Delete all API Key activations by order_id.
	 *
	 * @since   2.5.5
	 * @updated 3.2 Removed WC_AM_ASSOCIATED_API_KEY_DATA_STORE()->delete_associated_api_key_activation_ids()
	 *
	 * @param int $order_id
	 */
	public function delete_all_api_key_activations_by_order_id( $order_id ) {
		$activation_ids = $this->get_activations_by_order_id( $order_id );

		if ( ! WC_AM_FORMAT()->empty( $activation_ids ) ) {
			foreach ( $activation_ids as $k => $activation_id ) {
				// Deletes all the API Key activations with the activation ID.
				$this->delete_api_key_activation_by_activation_id( $activation_id );
			}
		}
	}
}