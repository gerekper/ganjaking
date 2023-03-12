<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: anonymisation:Anonymisation functions
Description: Anonymise personal data in your database backups
Version: 1.0
Shop: /shop/anonymisation/
Latest Change: 1.16.37
*/
// @codingStandardsIgnoreEnd

if (!defined('ABSPATH')) die('No direct access allowed');

UpdraftPlus_Anonymisation_Functions::add_hooks();

class UpdraftPlus_Anonymisation_Functions {

	/**
	 * Adds hooks for anonymization UI
	 */
	public static function add_hooks() {
		add_filter('updraft_backupnow_database_showmoreoptions', array('UpdraftPlus_Anonymisation_Functions', 'backupnow_database_showmoreoptions'), 10, 2);
		add_filter('updraftplus_migration_additional_ui', array('UpdraftPlus_Anonymisation_Functions', 'updraftplus_migration_anonymisation_options'));
		add_filter('updraftplus_clone_additional_ui', array('UpdraftPlus_Anonymisation_Functions', 'updraftplus_clone_anonymisation_options'));
		add_action('pre_database_backup_setup', array('UpdraftPlus_Anonymisation_Functions', 'setup_anonymisation_settings'));
	}

	/**
	 * Adds database anonymisation options to the backup now modal
	 *
	 * @param  String $ret this contains the upgrade to premium link and gets cleared here and replaced with table content
	 *
	 * @return String A string that contains HTML to be appended to the backup now modal
	 */
	public static function backupnow_database_showmoreoptions($ret) {

		global $updraftplus;

		$ret = '<em>'.__('These options can anonymize personal data in your database backup.', 'updraftplus').' '.__('N.B. Anonymized information cannot be recovered; the original non-anonymized data will be absent from the backup.', 'updraftplus').'</em><br>';

		$ret .= '<input type="checkbox" id="backupnow_db_anon_all">
		<label for="backupnow_db_anon_all">'.__('Anonymize personal data for all users except the logged-in user', 'updraftplus').'</label><br>';
		$ret .= '<input type="checkbox" id="backupnow_db_anon_non_staff">
		<label for="backupnow_db_anon_non_staff">'.__('Anonymize personal data for all users except staff', 'updraftplus').' <a href="'.$updraftplus->get_url('anon_backups').'" target="_blank">'.__('Learn more', 'updraftplus').'</a></label><br>';

		if (class_exists('WooCommerce')) {
			$ret .= '<input type="checkbox" id="backupnow_db_anon_wc_order_data">
			<label for="backupnow_db_anon_wc_order_data">'.__('Anonymize WooCommerce order data', 'updraftplus') .'</label><br><hr/>';
		}
		return $ret;
	}

	/**
	 * Adds database anonymisation options to the migration UI
	 *
	 * @param string $output - the current migration options UI
	 *
	 * @return string - the altered migration options UI
	 */
	public static function updraftplus_migration_anonymisation_options($output) {

		global $updraftplus;

		$output = '<em>'.__('These options can anonymize personal data in your database backup.', 'updraftplus').' '.__('N.B. Anonymized information cannot be recovered; the original non-anonymized data will be absent from the backup.', 'updraftplus').'</em><br>';
		
		$output .= '<label class="updraft_checkbox" for="updraftplus_migration_backupnow_db_anon_all"><input type="checkbox" id="updraftplus_migration_backupnow_db_anon_all">'.__('Anonymize personal data for all users except the logged-in user', 'updraftplus').'</label>';
		
		$output .= '<label class="updraft_checkbox" for="updraftplus_migration_backupnow_db_anon_non_staff"><input type="checkbox" id="updraftplus_migration_backupnow_db_anon_non_staff">'.__('Anonymize personal data for all users except staff', 'updraftplus').' <a href="'.$updraftplus->get_url('anon_backups').'" target="_blank">'.__('Learn more', 'updraftplus').'</a></label>';
		
		if (class_exists('WooCommerce')) {
		  $output .= '<label class="updraft_checkbox" for="updraftplus_migration_backupnow_db_anon_wc_order_data"><input type="checkbox" id="updraftplus_migration_backupnow_db_anon_wc_order_data">'.__('Anonymize WooCommerce order data', 'updraftplus').'</label>';
		}
		

		return $output;
	}

	/**
	 * Adds database anonymisation options to the clone creation UI
	 *
	 * @param string $output - the current clone options UI
	 *
	 * @return string - the altered clone options UI
	 */
	public static function updraftplus_clone_anonymisation_options($output) {

		global $updraftplus;

		$output .= '<p class="updraftplus-option backupnow-db-anon-all">';
		$output .= '<input type="checkbox" id="updraftplus_clone_backupnow_db_anon_all">';
		$output .= '<label for="updraftplus_clone_backupnow_db_anon_all">'.__('Anonymize personal data for all users except the logged-in user', 'updraftplus').'</label>';
		$output .= '</p>';

		$output .= '<p class="updraftplus-option backupnow-db-anon-all">';
		$output .= '<input type="checkbox" id="updraftplus_clone_backupnow_db_anon_non_staff">';
		$output .= '<label for="updraftplus_clone_backupnow_db_anon_non_staff">'.__('Anonymize personal data for all users except staff', 'updraftplus').' <a href="'.$updraftplus->get_url('anon_backups').'" target="_blank">'.__('Learn more', 'updraftplus').'</a></label>';
		$output .= '</p>';
		if (class_exists('WooCommerce')) {
			$output .= '<p class="updraftplus-option backupnow-db-anon-wc-orders">';
			$output .= '<input type="checkbox" id="updraftplus_clone_backupnow_db_anon_wc_order_data">';
			$output .= '<label for="updraftplus_clone_backupnow_db_anon_wc_order_data">'.__('Anonymize WooCommerce order data', 'updraftplus').'</label>';
			$output .= '</p>';
		}

		return $output;
	}

	/**
	 * This function will add data to the backup options that is needed for the anonymised backup job
	 *
	 * @param array $options - the backup options array
	 * @param array $request - the extra data we want to add to the backup options
	 *
	 * @return array - the backup options array with the extra data added
	 */
	public static function updraftplus_backup_anonymisation_options($options, $request) {
		if (!is_array($options)) return $options;

		if (isset($request['db_anon_all'])) $options['db_anon_all'] = $request['db_anon_all'];
		if (isset($request['db_anon_non_staff'])) $options['db_anon_non_staff'] = $request['db_anon_non_staff'];
		if (isset($request['db_anon_wc_orders'])) $options['db_anon_wc_orders'] = $request['db_anon_wc_orders'];

		return $options;
	}


	/**
	 *  Provides all the wc order fields that needs to be anonymized
	 *
	 * @return array - WooCommerc order meta fields which needs to be anonymized
	 */
	public static function get_wc_order_anonymize_fields() {
		return array(
			'_billing_address_index' => 'text',
			'_shipping_address_index' => 'text',
			'_customer_ip_address' => 'ip',
			'_customer_user_agent' => 'text',
			'_billing_first_name' => 'text',
			'_billing_last_name' => 'text',
			'_billing_company' => 'text',
			'_billing_address_1' => 'text',
			'_billing_address_2' => 'text',
			'_billing_city' => 'text',
			'_billing_postcode' => 'text',
			'_billing_state' => 'address_state',
			'_billing_country' => 'address_country',
			'_billing_phone' => 'phone',
			'_billing_email' => 'email',
			'_shipping_first_name' => 'text',
			'_shipping_last_name' => 'text',
			'_shipping_company' => 'text',
			'_shipping_address_1' => 'text',
			'_shipping_address_2' => 'text',
			'_shipping_city' => 'text',
			'_shipping_postcode' => 'text',
			'_shipping_state' => 'address_state',
			'_shipping_country' => 'address_country',
			'_shipping_phone' => 'phone',
			'_transaction_id' => 'numeric_id',
		);
	}

	/**
	 * Sets up the backup job data for when we are starting a backup job with anonymisation settings.
	 *
	 * @param array $jobdata - the initial job data that we want to change
	 * @param array $options - options sent from the front end
	 *
	 * @return array - the modified jobdata
	 */
	public static function updraftplus_backup_anonymisation_jobdata($jobdata, $options) {
		if (!is_array($jobdata)) return $jobdata;

		$anonymisation_options = array();

		if (isset($options['db_anon_all'])) $anonymisation_options['backup_anonymise_all_data'] = $options['db_anon_all'];
		if (isset($options['db_anon_non_staff'])) $anonymisation_options['backup_anonymise_non_staff_data'] = $options['db_anon_non_staff'];
		if (isset($options['db_anon_wc_orders'])) $anonymisation_options['backup_anonymise_wc_data'] = $options['db_anon_wc_orders'];


		if (!empty($anonymisation_options)) {
			$jobdata[] = 'anonymisation_options';
			$jobdata[] = $anonymisation_options;
		}

		return $jobdata;
	}

	/**
	 * Look through the backup anonymisation options and add the relevant filters
	 *
	 * @return void
	 */
	public static function setup_anonymisation_settings() {
		global $updraftplus;

		$anonymisation_options = $updraftplus->jobdata_get('anonymisation_options', array());

		foreach ($anonymisation_options as $option_name => $value) {
			if ($value) add_filter('updraftplus_backup_table_results', 'UpdraftPlus_Anonymisation_Functions::'.$option_name, 10, 4);
		}
	}

	/**
	 * Anonymise the personal data in the users and usermeta table for all users except the logged in user
	 *
	 * @param array  $result       - the data returned from the SQL call
	 * @param string $table        - the table this data is from
	 * @param string $table_prefix - the table prefix
	 * @param string $whichdb      - which db we are working on
	 *
	 * @return array - the data with personal data anonymised
	 */
	public static function backup_anonymise_all_data($result, $table, $table_prefix, $whichdb) {
		$user_id = get_current_user_id();
	
		if (empty($user_id)) return $result;

		if ('wp' == $whichdb && (!empty($table_prefix) && strtolower($table_prefix.'users') == strtolower($table))) {
			foreach ($result as $key => $data) {
				if ($user_id != $data['ID']) $result[$key]['user_email'] = md5(rand())."@example.com";
			}
		} elseif ('wp' == $whichdb && (!empty($table_prefix) && strtolower($table_prefix.'usermeta') == strtolower($table))) {
			foreach ($result as $key => $data) {
				if ($user_id != $data['user_id']) {
					if ('first_name' == $data['meta_key'] || 'last_name' == $data['meta_key']) $result[$key]['meta_value'] = md5(rand());
				}
			}
		}

		return $result;
	}

	/**
	 * Anonymise the personal data in the users and usermeta table for all users except staff (Admin, Editor, Shop Manager)
	 *
	 * @param array  $result       - the data returned from the SQL call
	 * @param string $table        - the table this data is from
	 * @param string $table_prefix - the table prefix
	 * @param string $whichdb      - which db we are working on
	 *
	 * @return array - the data with personal data anonymised
	 */
	public static function backup_anonymise_non_staff_data($result, $table, $table_prefix, $whichdb) {

		$user_ids = array();

		static $user_data = false;
		if (!$user_data) {
			$user_query = new WP_User_Query(array('role__in' => apply_filters('updraftplus_anonymise_staff_data_roles', array('administrator', 'editor', 'shop_manager', 'fue_manager', 'plugin_manager', 'wpseo_editor', 'seo_manager', 'moderator'))));
			$user_data = $user_query->get_results();
		}


		foreach ($user_data as $data) {
			$user_ids[] = $data->ID;
		}
		
		if (empty($user_ids)) return $result;

		if ('wp' == $whichdb && (!empty($table_prefix) && strtolower($table_prefix.'users') == strtolower($table))) {
			foreach ($result as $key => $data) {
				if (!in_array($data['ID'], $user_ids)) $result[$key]['user_email'] = md5(rand())."@example.com";
			}
		} elseif ('wp' == $whichdb && (!empty($table_prefix) && strtolower($table_prefix.'usermeta') == strtolower($table))) {
			foreach ($result as $key => $data) {
				if (!in_array($data['user_id'], $user_ids)) {
					if ('first_name' == $data['meta_key'] || 'last_name' == $data['meta_key']) $result[$key]['meta_value'] = md5(rand());
				}
			}
		}

		return $result;
	}

	/**
	 * Anonymise the personal data in the postmeta table for orders
	 *
	 * @param array  $result       - the data returned from the SQL call
	 * @param string $table        - the table this data is from
	 * @param string $table_prefix - the table prefix
	 * @param string $whichdb      - which db we are working on
	 *
	 * @return array - the data with personal data anonymised
	 */
	public static function backup_anonymise_wc_data($result, $table, $table_prefix, $whichdb) {
		$anonymisation_function = array('UpdraftPlus_Manipulation_Functions', 'anonymize_data');
		if ('wp' == $whichdb && ((!empty($table_prefix) && (strtolower($table_prefix . 'postmeta') == strtolower($table) || strtolower($table_prefix . 'wc_orders_meta') == strtolower($table))))) {
			$wc_anon_fields = self::get_wc_order_anonymize_fields();
			foreach ($result as $key => $data) {
				if (array_key_exists($data['meta_key'], $wc_anon_fields)) {
					$result[$key]['meta_value'] = call_user_func($anonymisation_function, $wc_anon_fields[$data['meta_key']], $data['meta_value']);
				}
			}
		}


		if ('wp' == $whichdb && (!empty($table_prefix) && (strtolower($table_prefix . 'wc_orders') == strtolower($table) || strtolower($table_prefix . 'wc_order_addresses') == strtolower($table) ))) {
			foreach ($result as $key => $data) {
				if (!empty($data['billing_email'])) {
					$result[$key]['billing_email'] = call_user_func($anonymisation_function, 'email', $data['billing_email']);
				}
				if (!empty($data['ip_address'])) {
					$result[$key]['ip_address'] = call_user_func($anonymisation_function, 'ip', $data['ip_address']);
				}
				if (!empty($data['first_name'])) {
					$result[$key]['first_name'] = call_user_func($anonymisation_function, 'text', $data['first_name']);
				}
				if (!empty($data['last_name'])) {
					$result[$key]['last_name'] = call_user_func($anonymisation_function, 'text', $data['last_name']);
				}
				if (!empty($data['company'])) {
					$result[$key]['company'] = call_user_func($anonymisation_function, 'text', $data['company']);
				}
				if (!empty($data['address_1'])) {
					$result[$key]['address_1'] = call_user_func($anonymisation_function, 'text', $data['address_1']);
				}
				if (!empty($data['address_2'])) {
					$result[$key]['address_2'] = call_user_func($anonymisation_function, 'text', $data['address_2']);
				}
				if (!empty($data['state'])) {
					$result[$key]['state'] = call_user_func($anonymisation_function, 'text', $data['state']);
				}
				if (!empty($data['city'])) {
					$result[$key]['city'] = call_user_func($anonymisation_function, 'text', $data['city']);
				}
				if (!empty($data['postcode'])) {
					$result[$key]['postcode'] = call_user_func($anonymisation_function, 'text', $data['postcode']);
				}
				if (!empty($data['country'])) {
					$result[$key]['country'] = call_user_func($anonymisation_function, 'text', $data['country']);
				}
				if (!empty($data['email'])) {
					$result[$key]['email'] = call_user_func($anonymisation_function, 'email', $data['email']);
				}
				if (!empty($data['phone'])) {
					$result[$key]['phone'] = call_user_func($anonymisation_function, 'text', $data['text']);
				}
					
			}
			if ('wp' == $whichdb && (!empty($table_prefix) && (strtolower($table_prefix . 'wc_order_operational_data') == strtolower($table)))) {
				if (!empty($data['created_via'])) {
					$result[$key]['created_via'] = call_user_func($anonymisation_function, 'text', $data['created_via']);
				}
			}
		}
		return $result;
	}
}
