<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

require_once( __DIR__ . '/class-fue-privacy-query-builder.php' );

class FUE_Privacy extends WC_Abstract_Privacy {
	/**
	 * Custom DB tables to be used for privacy export/erase.
	 *
	 * Format is 'table_name' => 'fields' => list of fields, 'where' => array( 'key' =>.array ( 'map' => 'email' | 'user_id' ) ), 'description' => ''
	 *   Format of list of fields is table_key => array( 'value' => value_to_set )
	 *   OR
	 *   Format of list of fields is table_key => array( 'type' => type_to_anonymize )
	 *     where type_to_anonymize is a valid type that `wp_privacy_anonymize_data` accepts.
	 *
	 * @return array
	 */
	protected function get_custom_privacy_table_data() {
		return array(
			'followup_followup_history' => array(
				'fields'      => array(
					'user_id' => array( 'value' => '0' ),
					'content' => array( 'type'  => 'longtext' ),
				),
				'where'       => array(
					'user_id' => array( 'map' => 'user_id' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails History Data', 'follow_up_emails' ),
			),
			'followup_subscribers' => array(
				'fields'      => array(
					'email'      => array( 'type' => 'email' ),
					'first_name' => array( 'type' => 'text' ),
					'last_name'  => array( 'type' => 'text' ),
				),
				'where'       => array(
					'email'      => array( 'map' => 'email' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails Subscribers Data', 'follow_up_emails' ),
			),
			'followup_customer_carts' => array(
				'fields'      => array(
					'user_id'    => array( 'value' => '0' ),
					'first_name' => array( 'type'  => 'text' ),
					'last_name'  => array( 'type'  => 'text' ),
					'user_email' => array( 'type'  => 'email' ),
				),
				'where'       => array(
					'user_email' => array( 'map' => 'email' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails Customer Carts Data', 'follow_up_emails' ),
			),
			'followup_customer_notes' => array(
				'fields'      => array(
					'followup_customer_id' => array( 'value' => '0' ),
					'author_id'            => array( 'value' => '0' ),
					'note'                 => array( 'type'  => 'text' ),
				),
				'where'       => array(
					'followup_customer_id' => array( 'map' => 'user_id' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails Customer Notes Data', 'follow_up_emails' ),
			),
			'followup_customers' => array(
				'fields'      => array(
					'user_id'       => array( 'value' => '0' ),
					'email_address' => array( 'type'  => 'email' ),
				),
				'where'       => array(
					'email_address' => array( 'map' => 'email' ),
					'user_id'       => array( 'map' => 'user_id' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails Customers Data', 'follow_up_emails' ),
			),
			'followup_email_logs' => array(
				'fields'      => array(
					'email_id'      => array( 'value' => '0' ),
					'user_id'       => array( 'value' => '0' ),
					'email_name'    => array( 'type'  => 'text' ),
					'customer_name' => array( 'type'  => 'text' ),
					'email_address' => array( 'type'  => 'email' ),
				),
				'where'       => array(
					'user_id'       => array( 'map' => 'user_id' ),
					'email_address' => array( 'map' => 'email' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails Logs Data', 'follow_up_emails' ),
			),
			'followup_email_tracking' => array(
				'fields'      => array(
					'email_id'      => array( 'value' => '0' ),
					'user_id'       => array( 'value' => '0' ),
					'user_email'    => array( 'type'  => 'mail' ),
					'user_ip'       => array( 'type'  => 'ip' ),
					'user_country'  => array( 'type'  => 'text' ),
				),
				'where'       => array(
					'user_id'       => array( 'map' => 'user_id' ),
					'user_email'    => array( 'map' => 'email' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails Tracking Data', 'follow_up_emails' ),
			),
			'followup_coupon_logs' => array(
				'fields'      => array(
					'email_name'    => array( 'type'  => 'text' ),
					'email_address' => array( 'type'  => 'email' ),
					'email_id'      => array( 'value' => '0' ),
				),
				'where'       => array(
					'email_address' => array( 'map' => 'email' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails Coupon Logs Data', 'follow_up_emails' ),
			),
			'followup_email_orders' => array(
				'fields'      => array(
					'user_id'       => array( 'value' => '0' ),
					'user_email'    => array( 'type'  => 'mail' ),
					'email_id'      => array( 'value' => '0' ),
				),
				'where'       => array(
					'user_id'       => array( 'map' => 'user_id' ),
					'user_email'    => array( 'map' => 'email' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails Order Data', 'follow_up_emails' ),
			),
			'followup_email_excludes' => array(
				'fields'      => array(
					'email_id'   => array( 'value' => '0' ),
					'email_name' => array( 'type'  => 'text' ),
					'email'      => array( 'type'  => 'email' ),
				),
				'where'       => array(
					'email'      => array( 'map' => 'email' ),
				),
				'description' => __( 'WooCommerce Follow-Up Emails Exclude Data', 'follow_up_emails' ),
			),
		);
	}

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Follow-Up Emails', 'follow_up_emails' ) );

		$this->add_exporter( 'woocommerce-follow-up-emails-customer-data', __( 'WooCommerce Follow-Up Emails Customer Data', 'follow_up_emails' ), array( $this, 'customer_data_exporter' ) );
		$this->add_eraser( 'woocommerce-follow-up-emails-customer-data', __( 'WooCommerce Follow-Up Emails Customer Data', 'follow_up_emails' ), array( $this, 'customer_data_eraser' ) );

		$custom_privacy_table_data = $this->get_custom_privacy_table_data();

		foreach ( $custom_privacy_table_data as $db_table => $db_data ) {

			if ( version_compare( phpversion(), '5.3.0', '<' ) ) {
				// Yes.. I know what you are thinking about this code, but PHP :shrug:
				// This is also the only reason the methods are static, because
				// create_function does not support specifying context.
				$exportfn = create_function(
					'$email_address, $page',
					sprintf(
						'return FUE_Privacy::handle_db_export( stripslashes( "%s" ), unserialize( stripslashes( "%s" ) ), $email_address, $page );',
						addslashes( $db_table ),
						addslashes( serialize( $db_data ) )
					)
				);

				$erasefn  = create_function(
					'$email_address, $page',
					sprintf(
						'return FUE_Privacy::handle_db_erase( stripslashes( "%s" ), unserialize( stripslashes( "%s" ) ), $email_address, $page );',
						addslashes( $db_table ),
						addslashes( serialize( $db_data ) )
					)
				);
			} else {
				require( __DIR__ . '/class-fue-privacy-php53.php' );
			}

			$this->add_exporter(
				'woocommerce-follow-up-emails-' . $db_table,
				$db_data['description'],
				$exportfn
			);

			$this->add_eraser(
				'woocommerce-follow-up-emails-' . $db_table,
				$db_data['description'],
				$erasefn
			);
		}
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		/* translators: 1: URL to Privacy policy */
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'follow_up_emails' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-follow-up-emails' ) );
	}

	/**
	 * Finds and exports DB data by email address.
	 *
	 * @param string $db_table      The DB table.
	 * @param array  $db_data       Data for the DB table.
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public static function handle_db_export( $db_table, $db_data, $email_address, $page ) {
		$user    = get_user_by( 'email', $email_address );
		$user_id = '';
		if ( $user instanceof WP_User ) {
			$user_id = $user->ID;
		}

		$where_values  = array(
			'email'   => $email_address,
			'user_id' => $user_id,
		);

		$fields  = array_keys( $db_data['fields'] );
		$where   = FUE_Privacy_Query_Builder::construct_where( $db_data['where'], $where_values );
		$results = FUE_Privacy_Query_Builder::run_select_query( $fields, $db_table, $where, $page );

		$data_to_export = array();

		foreach ( $results as $index => $result ) {
			$data = array();

			foreach ( $result as $key => $value ) {
				$data[] = array(
					// Note: Field name cannot be localized due to the way
					// we're constructing it.
					'name'  => ucwords( str_replace( '_', ' ', $key ) ),
					'value' => $value,
				);
			}

			$data_to_export[] = array(
				'group_id'    => $db_table,
				'group_label' => $db_data['description'],
				'item_id'     => $db_table . '-' . $index,
				'data'        => $data,
			);
		}

		$done = 10 > count( $results );

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases DB data by email address.
	 *
	 * @param string $db_table      The DB table.
	 * @param array  $db_data       Data for the DB table.
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public static function handle_db_erase( $db_table, $db_data, $email_address, $page ) {
		$user    = get_user_by( 'email', $email_address );
		$user_id = '';
		if ( $user instanceof WP_User ) {
			$user_id = $user->ID;
		}

		$messages      = array();

		$where_values  = array(
			'email'   => $email_address,
			'user_id' => $user_id,
		);

		$set_values    = FUE_Privacy_Query_Builder::construct_set_fields( $db_data['fields'] );
		$where         = FUE_Privacy_Query_Builder::construct_where( $db_data['where'], $where_values );
		$result        = FUE_Privacy_Query_Builder::run_update_query( $db_table, $set_values, $where );
		$items_removed = $result > 0;

		if ( $items_removed ) {
			/* translators: 1: Data description */
			$messages[] = sprintf( __( '%s Removed.', 'follow_up_emails' ), $db_data['description'] );
		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => $messages,
			'done'           => true,
		);
	}

	/**
	 * Finds and exports customer data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function customer_data_exporter( $email_address, $page ) {
		$user           = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
		$data_to_export = array();

		if ( $user instanceof WP_User ) {
			$data_to_export[] = array(
				'group_id'    => 'woocommerce_customer',
				'group_label' => __( 'Customer Data', 'follow_up_emails' ),
				'item_id'     => 'user',
				'data'        => array(
					array(
						'name'  => __( 'Follow-up Emails Twitter handle', 'follow_up_emails' ),
						'value' => get_user_meta( $user->ID, 'twitter_handle', true ),
					),
					array(
						'name'  => __( 'Follow-up Emails API ck/cs', 'follow_up_emails' ),
						'value' => implode( ':', array_filter( array(
							get_user_meta( $user->ID, 'fue_api_consumer_key', true ),
							get_user_meta( $user->ID, 'fue_api_consumer_secret', true ),
						) ) ),
					),
				),
			);
		}

		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}

	/**
	 * Finds and erases customer data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function customer_data_eraser( $email_address, $page ) {
		$page = (int) $page;
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$twitter_handle = get_user_meta( $user->ID, 'twitter_handle', true );
		$fue_api_ck = get_user_meta( $user->ID, 'fue_api_consumer_key', true );
		$fue_api_cs = get_user_meta( $user->ID, 'fue_api_consumer_secret', true );

		$items_removed  = false;
		$messages       = array();

		if ( ! empty( $twitter_handle ) || ! empty( $fue_api_ck ) || ! empty( $fue_api_cs ) ) {
			$items_removed = true;
			delete_user_meta( $user->ID, 'twitter_handle' );
			delete_user_meta( $user->ID, 'fue_api_consumer_key' );
			delete_user_meta( $user->ID, 'fue_api_consumer_secret' );
			$messages[] = __( 'Follow-up Emails User Data Erased.', 'follow_up_emails' );
		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => $messages,
			'done'           => true,
		);
	}
}

new FUE_Privacy();
