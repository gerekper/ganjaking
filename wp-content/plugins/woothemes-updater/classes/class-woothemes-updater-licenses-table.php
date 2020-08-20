<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WooThemes_Updater_Licenses_Table extends WP_List_Table {
	public $per_page = 100;

	public $data;
	private $api;

	/**
	 * Constructor.
	 * @since  1.0.0
	 */
	public function __construct( $args = array() ) {
		global $status, $page;

		parent::__construct( array(
			 'singular'  => 'license',     //singular name of the listed records
			  'plural'    => 'licenses',   //plural name of the listed records
			  'ajax'      => false        //does this table support ajax?
		) );
		$status = 'all';

		$page = $this->get_pagenum();

		$this->data = array();

		// Load the API.
		require_once( 'class-woothemes-updater-api.php' );
		$this->api = new WooThemes_Updater_API();

		// Make sure this file is loaded, so we have access to plugins_api(), etc.
		require_once( ABSPATH . '/wp-admin/includes/plugin-install.php' );

		parent::__construct( $args );
	} // End __construct()

	/**
	 * Text to display if no items are present.
	 * @since  1.0.0
	 * @return  void
	 */
	public function no_items () {
		echo wpautop( __( 'No WooCommerce products found.', 'woothemes-updater' ) );
	} // End no_items(0)

	/**
	 * The content of each column.
	 * @param  array $item         The current item in the list.
	 * @param  string $column_name The key of the current column.
	 * @since  1.0.0
	 * @return string              Output for the current column.
	 */
	public function column_default ( $item, $column_name ) {
		switch( $column_name ) {
			case 'product':
			case 'product_status':
			case 'product_version':
			case 'license_expiry':
				return $item[$column_name];
			break;
		}
	} // End column_default()

	/**
	 * Retrieve an array of sortable columns.
	 * @since  1.0.0
	 * @return array
	 */
	public function get_sortable_columns () {
	  return array();
	} // End get_sortable_columns()

	/**
	 * Retrieve an array of columns for the list table.
	 * @since  1.0.0
	 * @return array Key => Value pairs.
	 */
	public function get_columns () {
		$renews_on_info = $this->api->get_master_key_info() ? '' : ' <span class="dashicons dashicons-info"></span> <div class="renews-on-tooltip">Please connect your account or paste in a subscription key to enable upgrades.</div>';
		$columns = array(
			'product_name' => __( 'Product', 'woothemes-updater' ),
			'product_version' => __( 'Version', 'woothemes-updater' ),
			'product_status' => __( 'Key', 'woothemes-updater' ),
			'product_expiry' => __( 'Renews On', 'woothemes-updater' ) . $renews_on_info,
		);
		 return $columns;
	} // End get_columns()

	/**
	 * Content for the "product_name" column.
	 * @param  array  $item The current item.
	 * @since  1.0.0
	 * @return string       The content of this column.
	 */
	public function column_product_name ( $item ) {
		return wpautop( '<strong>' . $item['product_name'] . '</strong>' );
	} // End column_product_name()

	/**
	 * Content for the "product_version" column.
	 * @param  array  $item The current item.
	 * @since  1.0.0
	 * @return string       The content of this column.
	 */
	public function column_product_version ( $item ) {
		if ( isset( $item['latest_version'], $item['product_version'] ) && version_compare( $item['product_version'], $item['latest_version'], '<' ) ) {
			$version_text = '<strong>' . $item['product_version'] . '<a href="' . admin_url( 'update-core.php' ) . '" class="update-available"> - ' . sprintf( __( 'version %1$s available', 'woothemes-updater' ), esc_html( $item['latest_version'] ) ) .  '</span></strong>' . "\n";
		} else {
			$version_text = '<strong class="latest-version">' . $item['product_version'] . '</strong>' . "\n";
		}

		return wpautop( $version_text );
	} // End column_product_version()

	/**
	 * Content for the "status" column.
	 * @param  array  $item The current item.
	 * @since  1.0.0
	 * @return string       The content of this column.
	 */
	public function column_product_status ( $item ) {
		$response = '';
		if ( 'active' == $item['product_status'] && $item['license_expiry'] !== 'Please activate' ) {
			$response = '<em>Connected</em>'; // Could be connected to any account - not just the linked one
		} else {
			$match = isset( $item['product_id'] ) && ! empty( $item['product_id'] ) ? $this->get_master_key_product_match( $item['product_id'] ) : false;
			$key = $match ? $match->product_key : '';
			$method = $match ? 'master' : 'manual';

			$response .= '<input data-method="' . $method . '" name="license_keys[' . esc_attr( $item['product_file_path'] ) . ']" id="license_keys-' . esc_attr( $item['product_file_path'] ) . '" type="text" value="' . $key . '" size="37" aria-required="true" placeholder="' . esc_attr__( 'Place your subscription key here', 'woothemes-updater' ) . '" />' . "\n";
			$response .= '<input type="hidden" name="license_methods[' . esc_attr( $item['product_file_path'] ) . ']" value="' . $method . '" />';
		}

		return $response;
	} // End column_status()

	public function column_product_expiry ( $item ) {
		if ( isset( $item['license_expiry'] ) && $item['license_expiry'] == 'Please activate' ) {
			$item['license_expiry'] = 'Please connect';
		}

		// Disconnect url
		$disconnect = '';
		if ( $item['product_status'] == 'active' && $item['license_expiry'] !== 'Please connect' ) {
			$deactivate_url = wp_nonce_url( add_query_arg( 'action', 'deactivate-product', add_query_arg( 'filepath', $item['product_file_path'], add_query_arg( 'page', 'woothemes-helper', network_admin_url( 'index.php' ) ) ) ), 'bulk-licenses' );
			$disconnect = '<a href="' . esc_url( $deactivate_url ) . '" class="button button-secondary">' . __( 'Disconnect', 'woothemes-updater' ) . '</a>' . "\n";
		}

		// Format / set expiry date
		if ( '-' !== $item['license_expiry'] && 'active' == $item['product_status'] ) {
			if ( 'Please connect' !== $item['license_expiry'] ) {
				$date = new DateTime( $item['license_expiry'] );
				if ( $date > new DateTime() ) {
					$date_string = $date->format( 'M j, Y' );
				} else {
					$date_string = '<a href="' . esc_url( 'https://woocommerce.com/my-account/my-subscriptions?renew_expired_subscription=' . $item['product_id'] ) . '" target="_blank" title="Click to renew now">Expired</a>';
				}
			} else {
				$date_string = '<em>Unknown</em>';
			}
		} else {
			if ( $item['license_expiry'] == '-' ) {
				$date_string = 'Lifetime';
			} else {
				$date_string = sanitize_text_field( $item['license_expiry'] );
			}
		}

		// If current key not connected/active but matches existing master key, show helpful message
		if ( isset( $item['license_expiry'] ) && $item['license_expiry'] == 'Please connect' && $item['product_status'] !== 'active' ) {
			// check if current product has match in our master key retrieved data
			if ( $this->get_master_key_product_match( $item['product_id'] ) ) {
				return '<div class="enable-connection"><span class="dashicons dashicons-admin-plugins dashicons-circle"></span> Click connect below</div>';
			}

			// no match - check if they're even connected
			if ( ! $this->api->get_master_key_info() ) {
				return '<div class="enable-connection"><span class="dashicons dashicons-admin-plugins dashicons-circle"></span> Enable connection above</div>';
			}

			// no match but still connected
			return '<div class="enable-connection"><a href="https://woocommerce.com?utm_source=helper&utm_medium=product&utm_content=subscriptiontab" target="_blank"><span class="dashicons dashicons-admin-plugins dashicons-circle"></span> Purchase or add a key</a></div>';
		}

		return $date_string . $disconnect;
	}

	/**
	 * Retrieve an array of possible bulk actions.
	 * @since  1.0.0
	 * @return array
	 */
	public function get_bulk_actions () {
	  $actions = array();
	  return $actions;
	} // End get_bulk_actions()

	/**
	 * Prepare an array of items to be listed.
	 * @since  1.0.0
	 * @return array Prepared items.
	 */
	public function prepare_items () {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$total_items = count( $this->data );

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $total_items                   //WE have to determine how many items to show on a page
		) );
	  	$this->items = $this->data;
	} // End prepare_items()

	/**
	 * Gets a master key product match.
	 * Goes through all master key products and if the input product id matches, returns that product object.
	 * Otherwise returns false.
	 * @param $product_id | int
	 * @return mixed
	 */
	public function get_master_key_product_match( $product_id ) {
		$master_key_products = $this->api->get_master_key_info() ? $this->api->get_master_key_info()->user_products : false;
		$product_id = intval( $product_id );

		$match = false;
		if ( $master_key_products && $product_id > 0 ) {
			foreach( $master_key_products as $product ) {
				if ( $product->product_id == $product_id ) {
					$match = $product;
					break;
				}
			}
		}

		return $match;
	}
} // End Class
?>