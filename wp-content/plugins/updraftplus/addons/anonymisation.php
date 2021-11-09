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

class UpdraftPlus_Anonymisation_Functions {

	/**
	 * This function adds database anonymisation options to the backup now modal
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
		<label for="backupnow_db_anon_non_staff">'.__('Anonymize personal data for all users except staff', 'updraftplus').' <a href="'.$updraftplus->get_url('anon_backups').'" target="_blank">'.__('Learn more', 'updraftplus').'</a></label><br><hr>';
		
		return $ret;
	}

	/**
	 * This function adds database anonymisation options to the clone creation UI
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

		return $options;
	}

	/**
	 * This function will set up the backup job data for when we are starting a backup job with anonymisation settings.
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

		if (!empty($anonymisation_options)) {
			$jobdata[] = 'anonymisation_options';
			$jobdata[] = $anonymisation_options;
		}

		return $jobdata;
	}

	/**
	 * This function will look through the backup anonymisation options and add the relevant filters
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
	 * This function will anonymise the personal data in the users and usermeta table for all users except the logged in user
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
	 * This function will anonymise the personal data in the users and usermeta table for all users except staff (Admin, Editor, Shop Manager)
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
}

add_filter('updraft_backupnow_database_showmoreoptions', 'UpdraftPlus_Anonymisation_Functions::backupnow_database_showmoreoptions', 10, 2);
add_filter('updraftplus_clone_additional_ui', 'UpdraftPlus_Anonymisation_Functions::updraftplus_clone_anonymisation_options');

add_action('pre_database_backup_setup', 'UpdraftPlus_Anonymisation_Functions::setup_anonymisation_settings');
