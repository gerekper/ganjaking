<?php
/**
 * WooCommerce Customer/Order/Coupon CSV Import Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon CSV Import Suite for your
 * needs please refer to http://docs.woocommerce.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

if ( ! class_exists( 'WP_Importer' ) ) return;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Customer Importer class for managing the import process of a CSV file.
 *
 * @since 1.0.0
 *
 * Class renamed from WC_CSV_Customer_Import to WC_CSV_Import_Suite_Customer_Import in 3.0.0
 */
class WC_CSV_Import_Suite_Customer_Import extends \WC_CSV_Import_Suite_Importer {


	/** @var array address fields */
	private $address_fields;

	/** @var array Array of known customer data fields */
	private $customer_data_fields;


	/**
	 * Construct and initialize the importer
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		$this->title = __( 'Import Customers', 'woocommerce-csv-import-suite' );

		$this->i18n = array(
			'count'          => esc_html__( '%s customers' ),
			'count_inserted' => esc_html__( '%s customers inserted' ),
			'count_merged'   => esc_html__( '%s customers merged' ),
			'count_skipped'  => esc_html__( '%s customers skipped' ),
			'count_failed'   => esc_html__( '%s customers failed' ),
		);

		$this->address_fields = array(
			'first_name',
			'last_name',
			'company',
			'email',
			'phone',
			'address_1',
			'address_2',
			'city',
			'state',
			'postcode',
			'country',
		);

		$this->customer_data_fields = array(
			'billing_address',
			'shipping_address',
			'paying_customer',
		);

		add_filter( 'wc_csv_import_suite_woocommerce_customer_csv_column_default_mapping', array( $this, 'column_default_mapping' ), 10, 2 );

		add_action( 'wc_csv_import_suite_before_import_options_fields', array( $this, 'advanced_import_options' ) );
	}


	/**
	 * Get CSV column mapping options
	 *
	 * @since 3.0.0
	 * @return array Associative array of column mapping options
	 */
	public function get_column_mapping_options() {

		$billing_prefix  = __( 'Billing: %s',  'woocommerce-csv-import-suite' );
		$shipping_prefix = __( 'Shipping: %s', 'woocommerce-csv-import-suite' );

		return array(

			__( 'User data', 'woocommerce-csv-import-suite' ) => array(
				'id'              => __( 'User ID', 'woocommerce-csv-import-suite' ),
				'username'        => __( 'Username', 'woocommerce-csv-import-suite' ),
				'email'           => __( 'Email', 'woocommerce-csv-import-suite' ),
				'password'        => __( 'Password', 'woocommerce-csv-import-suite' ),
				'date_registered' => __( 'Registered date', 'woocommerce-csv-import-suite' ),
				'role'            => __( 'Role', 'woocommerce-csv-import-suite' ),
				'url'             => __( 'URL', 'woocommerce-csv-import-suite' ),
			),

			__( 'Customer data', 'woocommerce-csv-import-suite' ) => array(
				'billing_first_name'  => sprintf( $billing_prefix,  __( 'First name', 'woocommerce-csv-import-suite' ) ),
				'billing_last_name'   => sprintf( $billing_prefix,  __( 'Last name', 'woocommerce-csv-import-suite' ) ),
				'billing_company'     => sprintf( $billing_prefix,  __( 'Company', 'woocommerce-csv-import-suite' ) ),
				'billing_address_1'   => sprintf( $billing_prefix,  __( 'Address 1', 'woocommerce-csv-import-suite' ) ),
				'billing_address_2'   => sprintf( $billing_prefix,  __( 'Address 2', 'woocommerce-csv-import-suite' ) ),
				'billing_city'        => sprintf( $billing_prefix,  __( 'City', 'woocommerce-csv-import-suite' ) ),
				'billing_state'       => sprintf( $billing_prefix,  __( 'State', 'woocommerce-csv-import-suite' ) ),
				'billing_postcode'    => sprintf( $billing_prefix,  __( 'Postcode', 'woocommerce-csv-import-suite' ) ),
				'billing_country'     => sprintf( $billing_prefix,  __( 'Country', 'woocommerce-csv-import-suite' ) ),
				'billing_email'       => sprintf( $billing_prefix,  __( 'Email', 'woocommerce-csv-import-suite' ) ),
				'billing_phone'       => sprintf( $billing_prefix,  __( 'Phone', 'woocommerce-csv-import-suite' ) ),
				'shipping_first_name' => sprintf( $shipping_prefix, __( 'First name', 'woocommerce-csv-import-suite' ) ),
				'shipping_last_name'  => sprintf( $shipping_prefix, __( 'Last name', 'woocommerce-csv-import-suite' ) ),
				'shipping_company'    => sprintf( $shipping_prefix, __( 'Company', 'woocommerce-csv-import-suite' ) ),
				'shipping_address_1'  => sprintf( $shipping_prefix, __( 'Address 1', 'woocommerce-csv-import-suite' ) ),
				'shipping_address_2'  => sprintf( $shipping_prefix, __( 'Address 2', 'woocommerce-csv-import-suite' ) ),
				'shipping_city'       => sprintf( $shipping_prefix, __( 'City', 'woocommerce-csv-import-suite' ) ),
				'shipping_state'      => sprintf( $shipping_prefix, __( 'State', 'woocommerce-csv-import-suite' ) ),
				'shipping_postcode'   => sprintf( $shipping_prefix, __( 'Postcode', 'woocommerce-csv-import-suite' ) ),
				'shipping_country'    => sprintf( $shipping_prefix, __( 'Country', 'woocommerce-csv-import-suite' ) ),
				'paying_customer'     => __( 'Paying customer', 'woocommerce-csv-import-suite' ),
			),

		);
	}


	/**
	 * Adjust default mapping for deprecated columns
	 *
	 * @since 3.0.0
	 * @param string $map_to
	 * @param string column
	 * @return string
	 */
	public function column_default_mapping( $map_to, $column ) {

		switch ( $column ) {

			case 'ID':
			case 'user_id':         return 'id';

			case 'user_login':      return 'username';
			case 'user_pass':       return 'password';
			case 'user_email':      return 'email';
			case 'user_registered': return 'date_registered';
		}

		return $map_to;
	}


	/**
	 * Render advanced options for customer CSV import
	 *
	 * @since 3.0.0
	 */
	public function advanced_import_options() {

		if ( ! isset( $_GET['import'] ) || 'woocommerce_customer_csv' != $_GET['import'] ) {
			return;
		}

		?>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Shipping Address', 'woocommerce-csv-import-suite' ); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" value="1" name="options[billing_address_for_shipping_address]" id="wc-csv-import-suite-customer-copy-billing-address" />
					<?php esc_html_e( 'Use billing address as shipping address if not set', 'woocommerce-csv-import-suite' ); ?>
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( "Don't hash user passwords", 'woocommerce-csv-import-suite' ); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" value="1" name="options[hashed_passwords]" id="wc-csv-import-suite-customer-hashed-passwords" />
					<?php esc_html_e( 'Enable this if your customer import file contains passwords which are already correctly hashed for WordPress.', 'woocommerce-csv-import-suite' ); ?>
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Send emails', 'woocommerce-csv-import-suite' ); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" value="1" name="options[send_welcome_emails]" id="wc-csv-import-suite-customer-send-emails" />
					<?php esc_html_e( 'Send welcome emails to new customers and password update emails to updated customers after import.', 'woocommerce-csv-import-suite' ); ?>
				</label>
			</td>
		</tr>

		<?php

	}


	/**
	 * Parse raw customer data
	 *
	 * @since 3.0.0
	 * @param array $item Raw customer data from CSV
	 * @param array $options Optional. Options
	 * @param array $raw_headers Optional. Raw headers
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array|bool Parsed customer data or false on failure
	 */
	protected function parse_item( $item, $options = array(), $raw_headers = array() ) {

		$customer_id = ! empty( $item['id'] ) ? $item['id'] : 0;
		$username    = isset( $item['username'] ) && $item['username'] ? sanitize_user( $item['username'] ) : null;
		$email       = isset( $item['email'] ) && $item['email'] ? $item['email'] : null;

		$merging             = $options['merge'];
		$insert_non_matching = isset( $options['insert_non_matching'] ) && $options['insert_non_matching'];

		/* translators: Placeholders: %s - row number */
		$preparing = $merging ? __( '> Row %s - preparing for merge.', 'woocommerce-csv-import-suite' ) : __( '> Row %s - preparing for import.', 'woocommerce-csv-import-suite' );
		wc_csv_import_suite()->log( '---' );
		wc_csv_import_suite()->log( sprintf( $preparing, $this->get_line_num() ) );

		// prepare for merging
		if ( $merging ) {

			$results     = $this->find_registered_customer( $customer_id, $username, $email, $insert_non_matching );
			$customer_id = $results['customer_id'];
			$merging     = $results['merging'];
		}

		// prepare for importing
		if ( ! $merging ) {

			// Required fields. although login (user_login) is technically also required, we can use email for that
			if ( ! $email ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_customer_email', __( 'No email set for new customer.', 'woocommerce-csv-import-suite' ) );
			}

			// Check if user already exists
			$user_exists = $username && username_exists( $username ) || $email && email_exists( $email );

			if ( $user_exists ) {

				$identifier = esc_html( $username ? $username : $email );
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_customer_already_exists', sprintf( __( 'Customer %s already exists.', 'woocommerce-csv-import-suite' ), $identifier ) );
			}
		}

		// validate username
		if ( $username && ! validate_username( $username ) ) {
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_username', sprintf( __( 'Invalid username: %s', 'woocommerce-csv-import-suite' ), esc_html( $username) ) );
		}

		// validate email
		if ( $email && ! is_email( $email ) ) {
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_email', sprintf( __( 'Invalid email: %s', 'woocommerce-csv-import-suite' ), esc_html( $email ) ) );
		}

		// verify the role: allow by slug or by name. skip if not found (TODO is this too harsh? {IT 2016-04-09})
		if ( isset( $item['role'] ) && $item['role'] ) {
			global $wp_roles;

			if ( ! isset( $wp_roles->role_names[ $item['role'] ] ) ) {
				$found_role_by_name = false;

				// fallback to first role by name
				foreach ( $wp_roles->role_names as $slug => $name ) {
					if ( $name == $item['role'] ) {

						$item['role']       = $slug;
						$found_role_by_name = true;
						break;
					}
				}

				if ( ! $found_role_by_name ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_role', sprintf( __( 'Role "%s" not found.', 'woocommerce-csv-import-suite' ), $item['role'] ) );
				}
			}
		}

		// prepare user & usermeta for import
		$user = $usermeta = array();

		// if merging, set user ID
		if ( $merging && $customer_id ) {
			$user['id'] = $customer_id;
		}

		// email
		$user['email'] = $email;

		// ensure username is set (required)
		$user['username'] = $username ? $username : sanitize_user( $email );

		// password
		if ( isset( $item['password'] ) ) {
			$user['password'] = $item['password'];
		}

		// user role, defaults to customer if not merging
		if ( isset( $item['role'] ) && $item['role'] ) {
			$user['role'] = $item['role'];
		}

		if ( isset( $item['first_name'] ) && $item['first_name'] ) {
			$user['first_name'] = $item['first_name'];
		}

		if ( isset( $item['last_name'] ) && $item['last_name'] ) {
			$user['last_name'] = $item['last_name'];
		}

		if ( isset( $item['date_registered'] ) && $item['date_registered'] ) {
			$user['date_registered'] = $item['date_registered'];
		}

		$user['billing_address'] = $user['shipping_address'] = array();

		// get any known customer data (meta) fields
		foreach ( $this->customer_data_fields as $column ) {

			switch ( $column ) {

				// normalize customer addresses, to match the WC_API/CLI formats
				case 'billing_address':
				case 'shipping_address':

					$type           = substr( $column, 0, strpos( $column, '_' ) );
					$address_fields = $this->address_fields;

					if ( 'shipping' == $type ) {
						unset( $address_fields['phone'] );
						unset( $address_fields['email'] );
					}

					foreach ( $address_fields as $key ) {

						$meta_key = $type . '_' . $key;

						// on insert use all columns, on merge only use if there is a value.
						if ( isset( $item[ $meta_key ] ) && ( ! $merging || $item[ $meta_key ] ) ) {
							$user[ $column ][ $key ] = $item[ $meta_key ];
						}

						// on create default wp user first/last name to billing first/last
						if ( ! $merging ) {

							if ( 'billing_first_name' == $meta_key && ! empty( $item[ $meta_key ] ) && empty( $user['first_name'] ) ) {
								$user['first_name'] = $item[ $meta_key ];
							}

							elseif ( 'billing_last_name' == $meta_key && ! empty( $item[ $meta_key ] ) && empty( $user['last_name'] ) ) {
								$user['last_name'] = $item[ $meta_key ];
							}
						}
					}
				break;

				// normalize the paying customer field
				case 'paying_customer':

					if ( isset( $item[ $column ] ) && ( ! $merging || $item[ $column ] ) ) {
						$usermeta[ $column ] = $item[ $column ];
					}
				break;
			}
		}

		// handle the billing/shipping address defaults as needed
		$copy_billing_to_shipping = isset( $options['billing_address_for_shipping_address'] ) && $options['billing_address_for_shipping_address'];

		if ( $copy_billing_to_shipping && ! empty( $user['billing_address'] ) ) {

			foreach ( $user['billing_address'] as $key => $value ) {

				// if the shipping address field is set, use that. Otherwise, copy the
				// value from billing address
				if ( ! isset( $user['shipping_address'][ $key ] ) || ! $user['shipping_address'][ $key ] ) {
					$user['shipping_address'][ $key ] = $value;
				}
			}
		}

		// get any custom meta fields
		foreach ( $item as $key => $value ) {

			if ( ! $value ) {
				continue;
			}

			// handle meta: columns - import as custom fields
			if ( Framework\SV_WC_Helper::str_starts_with( $key, 'meta:' ) ) {

				// get meta key name
				$meta_key = trim( str_replace( 'meta:', '', $key ) );

				// skip known meta fields
				if ( in_array( $meta_key, $this->customer_data_fields ) ) {
					continue;
				}

				// add to usermeta array
				$usermeta[ $meta_key ] = $value;
			}
		}

		$user['user_meta'] = $usermeta;

		/**
		 * Filter parsed customer data
		 *
		 * Gives a chance for 3rd parties to parse data from custom columns
		 *
		 * @since 3.0.0
		 * @param array $customer Parsed customer data
		 * @param array $data Raw customer data from CSV
		 * @param array $options Import options
		 * @param array $raw_headers Raw CSV headers
		 */
		return apply_filters( 'wc_csv_import_suite_parsed_customer_data', $user, $item, $options, $raw_headers );
	}


	/**
	 * Check if the user for an import row exists. Match on ID, username, then email.
	 *
	 * @since 3.3.1
	 *
	 * @param int $customer_id the customer ID from the import row
	 * @param string $username the customer username from the import row
	 * @param string $email the customer email from the import row
	 * @param bool $insert_non_matching true if non-matching customer rows should be inserted
	 *
	 * @throws \WC_CSV_Import_Suite_Import_Exception
	 * @return string[] results of finding customers
	 */
	protected function find_registered_customer( $customer_id, $username, $email, $insert_non_matching ) {

		$found_customer = false;
		$merging        = true;

		// check that at least one required field for merging is provided
		if ( ! $customer_id && ! $username && ! $email ) {

			$message = __( '> > Cannot merge without id, email, or username.', 'woocommerce-csv-import-suite' );

			if ( $insert_non_matching ) {
				$message .= ' ' .  __( 'Importing instead.', 'woocommerce-csv-import-suite' );
			}

			wc_csv_import_suite()->log( $message );
			$merging = false;
		}

		// 1. try matching on user ID
		if ( $customer_id ) {

			$found_customer = get_user_by( 'id', $customer_id );

			if ( ! $found_customer ) {

				// no other fields to match on
				if ( ! $username && ! $email ) {

					if ( $insert_non_matching ) {
						wc_csv_import_suite()->log( sprintf( __( '> > Skipped. Cannot find customer with id %s. Importing instead.', 'woocommerce-csv-import-suite' ), $customer_id ) );
						$merging = false;
					} else {
						throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_find_customer', sprintf( __( 'Cannot find customer with id %s.', 'woocommerce-csv-import-suite' ), $customer_id ) );
					}

				} else {
					// we can keep trying with username and/or email
					wc_csv_import_suite()->log( sprintf( __( '> > Cannot find customer with id %s.', 'woocommerce-csv-import-suite' ), $customer_id ) );
				}

			} else {
				wc_csv_import_suite()->log( sprintf( __( '> > Found user with id %s.', 'woocommerce-csv-import-suite' ), $customer_id ) );
			}
		}

		// 2. try matching on username
		if ( ! $found_customer && $username ) {

			// check by username
			$found_customer = username_exists( $username );

			if ( ! $found_customer ) {

				if ( ! $email ) {

					if ( $insert_non_matching ) {
						wc_csv_import_suite()->log( sprintf( __( '> > Skipped. Cannot find customer with username %s. Importing instead.', 'woocommerce-csv-import-suite' ), $username ) );
						$merging = false;
					} else {
						throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_find_customer', sprintf( __( 'Cannot find customer with username %s.', 'woocommerce-csv-import-suite' ), $username ) );
					}

				} else {
					// We can keep trying with email
					wc_csv_import_suite()->log( sprintf( __( '> > Cannot find customer with username %s.', 'woocommerce-csv-import-suite' ), $username ) );
				}

			} else {
				wc_csv_import_suite()->log( sprintf( __( '> > Found user with username %s.', 'woocommerce-csv-import-suite' ), $username ) );
				$customer_id = $found_customer;
			}
		}

		// 3. try matching on email
		if ( ! $found_customer && $email ) {

			// check by email
			$found_customer = email_exists( $email );

			if ( ! $found_customer ) {

				if ( $insert_non_matching ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Skipped. Cannot find customer with email %s. Importing instead.', 'woocommerce-csv-import-suite' ), $email ) );
					$merging = false;
				} else {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_find_customer', sprintf( __( 'Cannot find customer with email %s.', 'woocommerce-csv-import-suite' ), $email ) );
				}

			} else {
				wc_csv_import_suite()->log( sprintf( __( '> > Found user with email %s.', 'woocommerce-csv-import-suite' ), $email ) );
				$customer_id = $found_customer;
			}
		}

		$results = array(
			'customer_id' => $customer_id,
			'merging'     => $merging,
		);

		return $results;
	}


	/**
	 * Process a customer
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $data Parsed customer data
	 * @param array $options Optional. Options
	 * @param array $raw_headers Optional. Raw headers
	 *
	 * @return int|null
	 */
	protected function process_item( $data, $options = array(), $raw_headers = array() ) {

		$merging = $options['merge'] && isset( $data['id'] ) && $data['id'];
		$dry_run = isset( $options['dry_run'] ) && $options['dry_run'];

		wc_csv_import_suite()->log( __( 'Processing customer.', 'woocommerce-csv-import-suite' ) );

		$user_id = null;

		if ( $merging ) {

			wc_csv_import_suite()->log( sprintf( __( '> Merging customer ID %s.', 'woocommerce-csv-import-suite' ), $data['id'] ) );

			if ( ! $dry_run ) {
				$user_id = $this->update_customer( $data['id'], $data, $options );
			}

		} else {

			// insert customer
			wc_csv_import_suite()->log( sprintf( __( '> Inserting customer %s', 'woocommerce-csv-import-suite' ), esc_html( $data['username'] ) ) );

			if ( ! $dry_run ) {
				$user_id = $this->create_customer( $data, $options );
			}
		}

		// import failed
		if ( ! $dry_run && is_wp_error( $user_id ) ) {
			$this->add_import_result( 'failed', $user_id->get_error_message() );
			return null;
		}

		// TODO: is that OK to log and return as user_id in case of dry run?
		if ( $dry_run ) {
			$user_id = $merging ? $data['id'] : 9999;
		}

		$result  = $merging ? 'merged' : 'inserted';
		$message = $merging
						 ? __( '> Finished merging customer ID %s.',   'woocommerce-csv-import-suite' )
						 : __( '> Finished importing customer ID %s.', 'woocommerce-csv-import-suite' );

		wc_csv_import_suite()->log( sprintf( $message, $user_id ) );

		$this->add_import_result( $result );

		return $user_id;
	}


	/**
	 * Create a customer
	 *
	 * Based on WC_API_Customers::create_customer
	 *
	 * @since 3.0.0
	 * @param array $data
	 * @param array $options
	 *
	 * @return int|WP_Error
	 */
	public function create_customer( $data, $options ) {

		try {

			/**
			 * Filter new customer data from CSV
			 *
			 * @since 3.0.0
			 * @param array $data
			 * @param array $options
			 * @param object $this
			 */
			$data = apply_filters( 'wc_csv_import_suite_create_customer_data', $data, $options, $this );

			// checks if the email is missing.
			if ( ! isset( $data['email'] ) ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_customer_email', sprintf( __( 'Missing parameter %s', 'woocommerce-csv-import-suite' ), 'email' ) );
			}

			// sets the username.
			$data['username'] = ! empty( $data['username'] ) ? $data['username'] : '';

			// sets the password.
			$data['password'] = ! empty( $data['password'] ) ? $data['password'] : '';

			// force generate the password if not provided
			if ( ! $data['password'] ) {
				add_filter( 'pre_option_woocommerce_registration_generate_password', array( $this, 'force_password_generation' ), 1 );
			}

			// enable/disable new customer emails
			$send_welcome_emails = isset( $options['send_welcome_emails'] ) && $options['send_welcome_emails'];
			$enabled             = $send_welcome_emails ? '__return_true' : '__return_false';

			add_filter( 'woocommerce_email_enabled_customer_new_account', $enabled, 1000 );

			// attempts to create the new customer
			$id = wc_create_new_customer( $data['email'], $data['username'], $data['password'] );

			// remove send_welcome_emails filter
			remove_filter( 'woocommerce_email_enabled_customer_new_account', $enabled, 1000 );

			// remove forced password generation
			if ( ! $data['password'] ) {
				remove_filter( 'pre_option_woocommerce_registration_generate_password', array( $this, 'force_password_generation' ), 1 );
			}

			// checks for an error in the customer creation.
			if ( is_wp_error( $id ) ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( $id->get_error_code(), $id->get_error_message() );
			}

			// update customer data.
			$this->update_customer_data( $id, $data, $options );

			/**
			 * Triggered after a customer has been created via CSV import
			 *
			 * @since 3.0.0
			 * @param int $id Customer ID
			 * @param array $data Data from CSV
			 * @param array $options Import options
			 */
			do_action( 'wc_csv_import_suite_create_customer', $id, $data, $options );

			return $id;

		} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {
			return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
		}
	}


	/**
	 * Update a customer
	 *
	 * Based on WC_API_Customers::edit_customer()
	 *
	 * @since 3.0.0
	 * @param int $id the customer ID
	 * @param array $data
	 * @param array $options
	 *
	 * @return int|WP_Error
	 */
	public function update_customer( $id, $data, $options ) {

		try {

			$id = absint( $id );

			// validate the customer ID.
			if ( empty( $id ) ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_customer_id', __( 'Invalid customer ID', 'woocommerce-csv-import-suite' ) );
			}

			// non-existent IDs return a valid WP_User object with the user ID = 0
			$customer = new \WP_User( $id );

			if ( 0 === $customer->ID ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_customer', __( 'Invalid customer', 'woocommerce-csv-import-suite' ) );
			}

			/**
			 * Filter data that is going to be updated for a customer via CSV
			 *
			 * @since 3.0.0
			 * @param array $data
			 * @param array $options
			 * @param object $this
			 */
			$data = apply_filters( 'wc_csv_import_suite_update_customer_data', $data, $options, $this );

			$user_data = array();

			// customer email.
			if ( isset( $data['email'] ) ) {
				$user_data['user_email'] = sanitize_email( $data['email'] );
			}

			// customer password.
			if ( isset( $data['password'] ) ) {
				$user_data['user_pass'] = $data['password'];
			}

			/**
			 * Determines if WordPress 'password updated' emails should be sent during customer merge if password is changed.
			 *
			 * This can be useful to suppress in case hashes differ between sites, as passwords may not truly be changed despite
			 *    hashes potentially changing in the database.
			 *
			 * @since 3.1.2
			 * @param bool $send_password_emails true if WP password reset emails should be sent when a user's password is updated
			 * @param int $id customer ID
			 * @param int $data data from imported CSV
			 * @param array $options configured import options
			 */
			$send_password_emails = apply_filters( 'wc_csv_import_suite_send_password_reset_emails', isset( $options['send_welcome_emails'] ) && $options['send_welcome_emails'], $id, $data, $options );

			if ( ! empty( $user_data ) ) {

				$user_data['ID'] = (int) $id;

				// wp_insert_user can perform an update without sending pw reset emails
				$updated = $send_password_emails ? wp_update_user( $user_data ) : $this->update_existing_user( $data );

				if ( is_wp_error( $updated ) ) {
					throw new WC_CSV_Import_Suite_Import_Exception( $updated->get_error_code(), $updated->get_error_message() );
				}
			}

			// update customer data.
			$this->update_customer_data( $id, $data, $options );

			/**
			 * Triggered after a customer has been updated via CSV import
			 *
			 * @since 3.0.0
			 * @param int $id Customer ID
			 * @param array $data Data from CSV
			 * @param array $options Import options
			 */
			do_action( 'wc_csv_import_suite_update_customer', $id, $data, $options );

			return $id;

		} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {
			return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
		}
	}


	/**
	 * Add/Update customer data.
	 *
	 * Based on WC_API_Customers::update_customer_data()
	 *
	 * @since 3.0.0
	 * @param int $id the customer ID
	 * @param array $data
	 * @param array $options
	 */
	protected function update_customer_data( $id, $data, $options ) {

		global $wpdb;

		// customer first name.
		if ( isset( $data['first_name'] ) ) {
			update_user_meta( $id, 'first_name', wc_clean( $data['first_name'] ) );
		}

		// customer last name.
		if ( isset( $data['last_name'] ) ) {
			update_user_meta( $id, 'last_name', wc_clean( $data['last_name'] ) );
		}

		// customer billing address.
		if ( isset( $data['billing_address'] ) ) {
			foreach ( $this->address_fields as $field ) {
				if ( isset( $data['billing_address'][ $field ] ) ) {
					update_user_meta( $id, 'billing_' . $field, wc_clean( $data['billing_address'][ $field ] ) );
				}
			}
		}

		// customer shipping address.
		if ( isset( $data['shipping_address'] ) ) {
			foreach ( $this->address_fields as $field ) {
				if ( isset( $data['shipping_address'][ $field ] ) ) {
					update_user_meta( $id, 'shipping_' . $field, wc_clean( $data['shipping_address'][ $field ] ) );
				}
			}
		}

		$user_data = array();

		// set user role
		if ( isset( $data['role'] ) ) {
			$user_data['role'] = $data['role'];
		}

		// set user registered date
		if ( isset( $data['date_registered'] ) ) {
			$user_data['user_registered'] = date( 'Y-m-d H:i:s', strtotime( $data['date_registered'] ) );
		}

		if ( ! empty( $user_data ) ) {
			wp_update_user( $user_data + array( 'ID' => $id ) );
		}

		// set password without hashing
		$hashed_passwords = isset( $options['hashed_passwords'] ) && $options['hashed_passwords'];

		if ( $hashed_passwords && ! empty( $data['password'] ) ) {
			$wpdb->update( $wpdb->users, array( 'user_pass' => $data['password'] ), array( 'ID' => $id ) );
		}

		// add/update user meta
		if ( ! empty( $data['user_meta'] ) ) {
			foreach ( $data['user_meta'] as $meta_key => $meta_value ) {
				update_user_meta( $id, $meta_key, maybe_unserialize( $meta_value ) );
			}
		}

		/**
		 * Triggered after customer data has been updated via CSV
		 *
		 * This will be triggered for both new and updated customers
		 *
		 * @since 3.0.0
		 * @param int $id Customer ID
		 * @param array $data Customer data
		 * @param array $options Import options
		 */
		do_action( 'wc_csv_import_suite_update_customer_data', $id, $data, $options );
	}


	/**
	 * Update existing user data without sending WordPress emails.
	 *
	 * @since 3.2.2
	 *
	 * @param array $data associative array of import data for this user
	 */
	protected function update_existing_user( $data ) {

		// Get the existing user data we have
		$user_data = get_userdata( (int) $data['id'] );

		// we want to only overwrite the data passed in, but wp_insert_user sets several properties to null
		// so we need to check for the existing values and include them
		// see https://codex.wordpress.org/Function_Reference/wp_insert_user#Notes
		$new_data = [

			// these props could be in the import file
			'user_pass'            => isset( $data['password'] )   ? wp_hash_password( $data['password'] ) : $user_data->user_pass,
			'user_email'           => isset( $data['email'] )      ? sanitize_email( $data['email'] )      : $user_data->user_email,
			'user_login'           => isset( $data['username'] )   ? $data['username']                     : $user_data->user_login,
			'first_name'           => isset( $data['first_name'] ) ? $data['first_name']                   : $user_data->first_name,
			'last_name'            => isset( $data['last_name'] )  ? $data['last_name']                    : $user_data->last_name,

			// these props we just want to check for existing values
			'nickname'             => isset( $user_data->nickname )             ? $user_data->nickname             : null,
			'description'          => isset( $user_data->description )          ? $user_data->description          : null,
			'rich_editing'         => isset( $user_data->rich_editing )         ? $user_data->rich_editing         : null,
			'comment_shortcuts'    => isset( $user_data->comment_shortcuts )    ? $user_data->comment_shortcuts    : null,
			'admin_color'          => isset( $user_data->admin_color )          ? $user_data->admin_color          : null,
			'use_ssl'              => isset( $user_data->use_ssl )              ? $user_data->use_ssl              : null,
			'show_admin_bar_front' => isset( $user_data->show_admin_bar_front ) ? $user_data->show_admin_bar_front : null,
			'locale'               => isset( $user_data->locale )               ? $user_data->locale               : null,

		];

		// when a user is found, ensures an additional user is not created in some circumstances when emails do not match
		if ( $user_data instanceof \WP_User && (int) $user_data->ID > 0 ) {
			$new_data['ID'] = (int) $user_data->ID;
		}

		wp_insert_user( $new_data );
	}


	/**
	 * Force generates a password for a new customer.
	 *
	 * @since 3.0.0
	 *
	 * @param string $generate
	 * @return string Always 'yes'
	 */
	public function force_password_generation( $generate ) {

		return 'yes';
	}


}
