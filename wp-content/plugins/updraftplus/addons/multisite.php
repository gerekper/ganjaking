<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: multisite:Multisite/Network
Description: Makes UpdraftPlus compatible with a WordPress Network (a.k.a. multi-site) and adds Network-related features
Version: 3.8
Shop: /shop/network-multisite/
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

// Options handling
if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('UpdraftPlus_Options')) return;

if (is_multisite()) {

	class UpdraftPlus_Options {

		/**
		 * Whether or not the current user has permission to manage UpdraftPlus
		 *
		 * @return Boolean
		 */
		public static function user_can_manage() {
			$user_can_manage = is_super_admin();
			// true: multisite add-on
			return apply_filters('updraft_user_can_manage', $user_can_manage, true);
		}

		/**
		 * The suffix for the table to store options in
		 *
		 * @return String
		 */
		public static function options_table() {
			return 'sitemeta';
		}

		/**
		 * Extracts the last logged message
		 *
		 * @return Mixed - Value set for the option or the default message
		 */
		public static function get_updraft_lastmessage() {
			// Storing this in the single-row option does not combine well with SQL binary logging and frequent updates during a backup process
			return get_site_option('updraft_lastmessage', __('(Nothing has been logged yet)', 'updraftplus'));
		}

		/**
		 * Get the value for a specified option
		 *
		 * @param String $option  - the option name
		 * @param Mixed	 $default - the default option value
		 *
		 * @return Mixed
		 */
		public static function get_updraft_option($option, $default = false) {
			if ('updraft_lastmessage' == $option) {
				// Storing this in the single-row option does not combine well with SQL binary logging and frequent updates during a backup process
				$ret = get_site_option($option, $default);
			} else {
				$tmp = get_site_option('updraftplus_options');
				$ret = isset($tmp[$option]) ? $tmp[$option] : $default;
			}
			return apply_filters('updraftplus_get_option', $ret, $option, $default);
		}

		/**
		 * Update an option. N.B. The $autoload parameter has no effect on a multisite install.
		 *
		 * @param String  $option	 specify option name
		 * @param String  $value	 specify option value
		 * @param Boolean $use_cache whether or not to use the WP options cache
		 * @return Boolean - as from update_site_option()
		 */
		public static function update_updraft_option($option, $value, $use_cache = true) {
			$value = apply_filters('updraftplus_update_option', $value, $option, $use_cache);
			if ('updraft_lastmessage' == $option) {
				return update_site_option('updraft_lastmessage', $value);
			}
			$tmp = get_site_option('updraftplus_options', array(), $use_cache);
			if (!is_array($tmp)) $tmp = array();
			$tmp[$option] = $value;
			return update_site_option('updraftplus_options', $tmp);
		}

		/**
		 * Delete an option
		 *
		 * @param String $option - the option name
		 */
		public static function delete_updraft_option($option) {
			$tmp = get_site_option('updraftplus_options');
			if (!is_array($tmp)) $tmp = array();
			if (isset($tmp[$option])) unset($tmp[$option]);
			update_site_option('updraftplus_options', $tmp);
		}

		/**
		 * Get the URL of the admin page
		 *
		 * @return String
		 */
		public static function admin_page_url() {
			return network_admin_url('settings.php');
		}

		public static function admin_page() {
			return 'settings.php';
		}

		public static function add_admin_pages() {
			if (is_super_admin() || apply_filters('updraft_user_can_manage', false, true)) add_submenu_page('settings.php', 'UpdraftPlus', __('UpdraftPlus Backups', 'updraftplus'), apply_filters('option_page_capability_updraft-options-group', 'manage_options'), 'updraftplus', array('UpdraftPlus_Options', 'options_printpage'));
		}

		/**
		 * Called upon plugin activation
		 */
		public static function set_defaults() {
			$tmp = get_site_option('updraftplus_options');
			
			if (is_array($tmp)) return;
			
			$arr = array(
				'updraft_encryptionphrase' => '',
				'updraft_service' => '',

				'updraftplus_dismissedautobackup' => 0, // Notice with information about the auto-backup add-on
				'updraftplus_dismisseddashnotice' => 0, // Notice on the main dashboard for free users who've been using the plugin for a few weeks
				'dismissed_general_notices_until' => 0, // Notices on the UD dashboard page
				'dismissed_review_notice' => 0, // Review notice on the UD dashboard page
				'dismissed_season_notices_until' => 0, // Seasonal notices on the UD dashboard page
				'dismissed_clone_php_notices_until' => 0, // Clone PHP notice on WP Dashboard
				'dismissed_clone_wc_notices_until' => 0, // Clone WC notice on WP Plugins page
				'updraftplus_dismissedexpiry' => 0, // Notice from the updates connector about support/updates expiry

				'updraft_log_syslog' => 0,
				'updraft_ssl_nossl' => 0,
				'updraft_ssl_useservercerts' => 0,
				'updraft_ssl_disableverify' => 0,
				'updraft_split_every' => 400,

				'updraft_dir' => '',
				'updraft_report_warningsonly' => array(),
				'updraft_report_wholebackup' => array(),
				'updraft_report_dbackup' => array(),

				'updraft_databases' => array(),
				'updraft_backupdb_nonwp' => 0,

				'updraft_remotesites' => array(),
				'updraft_migrator_localkeys' => array(),
				'updraft_central_localkeys' => array(),

				'updraft_autobackup_default' => 1,
				'updraft_delete_local' => 1,
				'updraft_debug_mode' => 1,
				'updraft_include_plugins' => 1,
				'updraft_include_themes' => 1,
				'updraft_include_uploads' => 1,
				'updraft_include_others' => 1,
				'updraft_include_wpcore' => 0,
				'updraft_include_wpcore_exclude' => '',
				'updraft_include_more' => 0,
				'updraft_include_more_path' => '',
				'updraft_include_muplugins' => 1,
				'updraft_include_blogs' => 1,
				'updraft_include_others_exclude' => UPDRAFT_DEFAULT_OTHERS_EXCLUDE,
				'updraft_include_uploads_exclude' => UPDRAFT_DEFAULT_UPLOADS_EXCLUDE,
				'updraft_interval' => 'manual',
				'updraft_interval_increments' => 'none',
				'updraft_interval_database' => 'manual',
				'updraft_extradbs' => array(),
				'updraft_retain' => 1,
				'updraft_retain_db' => 1,
				'updraft_retain_extra' => array(),
				'updraft_starttime_files' => date('H:i', time()+600),
				'updraft_starttime_db' => date('H:i', time()+600),
				'updraft_startday_files' => date('w', time()+600),
				'updraft_startday_db' => date('w', time()+600)
			);
			
			global $updraftplus;
			if (is_a($updraftplus, 'UpdraftPlus')) {
				$backup_methods = array_keys($updraftplus->backup_methods);
				
				foreach ($backup_methods as $service) {
					$arr['updraft_'.$service] = array();
				}
			}
			
			update_site_option('updraftplus_options', $arr);
		}

		public static function options_form_begin($settings_fields = 'updraft-options-group', $allow_autocomplete = true, $get_params = array(), $classes = '') {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

			$page = '';
			if (!empty($get_params)) {
				$page .= '?';
				$first_one = true;
				foreach ($get_params as $k => $v) {
					if ($first_one) {
						$first_one = false;
					} else {
						$page .= '&';
					}
					$page .= urlencode($k).'='.urlencode($v);
				}
			}

			if (!$page) $page = '?page=updraftplus';

			echo '<form method="post" action="'.$page.'"';
			if ('' != $classes) echo ' class="'.$classes.'"';
			if (!$allow_autocomplete) echo ' autocomplete="off"';
			echo '>';
		}

		public static function admin_init() {

			static $already_inited = false;
			if ($already_inited) return;
			$already_inited = true;
		
			global $updraftplus;
			$updraftplus->plugin_title .= " - ".__('Multisite Install', 'updraftplus');
		}

		/**
		 * This is the function outputing the HTML for our options page
		 */
		public static function options_printpage() {
			if (!self::user_can_manage()) {
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			if (isset($_POST['action']) && 'update' == $_POST['action'] && isset($_POST['updraft_interval'])) {
				self::mass_options_update($_POST);
			}

			global $updraftplus_admin;
			$updraftplus_admin->settings_output();

		}

		public static function mass_options_update($new_options) {

			if (!self::user_can_manage()) wp_die(__('You do not have permission to access this page.'));

			global $updraftplus, $updraftplus_admin;

			$options = get_site_option('updraftplus_options');

			$backup_methods = array_keys($updraftplus->backup_methods);
			
			foreach ($new_options as $key => $value) {
				if ('updraft_include_others_exclude' == $key || 'updraft_include_uploads_exclude' == $key || 'updraft_include_wpcore_exclude' == $key) {
					$options[$key] = UpdraftPlus_Manipulation_Functions::strip_dirslash($value);
				} elseif ('updraft_include_more_path' == $key) {
					$options[$key] = UpdraftPlus_Manipulation_Functions::remove_empties($value);
				} elseif ('updraft_delete_local' == $key || 'updraft_debug_mode' == $key || (preg_match('/^updraft_include_/', $key))) {
					// Booleans/numeric
					$options[$key] = absint($value);
				} elseif ('updraft_split_every' == $key) {
					$options[$key] = $updraftplus_admin->optionfilter_split_every($value);
				} elseif ('updraft_retain' == $key || 'updraft_retain_db' == $key) {
					$options[$key] = UpdraftPlus_Manipulation_Functions::retain_range($value);
				} elseif ('updraft_interval' == $key) {
					$options[$key] = $updraftplus->schedule_backup($value);
				} elseif ('updraft_interval_database' == $key) {
					$options[$key] = $updraftplus->schedule_backup_database($value);
				} elseif ('updraft_interval_increments' == $key) {
					$options[$key] = $updraftplus->schedule_backup_increments($value);
				} elseif ('updraft_service' == $key) {
					if (is_array($value)) {
						foreach ($value as $subkey => $subvalue) {
							if ('0' == $subvalue) unset($value[$subkey]);
						}
					}
					$value = $updraftplus->just_one($value);
					if (null === $value) $value = '';
					$options[$key] = $value;
				} elseif ('updraft_starttime_files' == $key || 'updraft_starttime_db' == $key) {
					if (preg_match("/^([0-2]?[0-9]):([0-5][0-9])$/", $value, $matches)) {
						$options[$key] = sprintf("%02d:%s", $matches[1], $matches[2]);
					} elseif ('' == $value) {
						$options[$key] = date('H:i', time()+300);
					} else {
						$options[$key] = '00:00';
					}
				} elseif ('updraft_startday_files' == $key || 'updraft_startday_db' == $key) {
					$value=absint($value);
					if ($value>28) $value=1;
					$options[$key] = $value;
				} elseif ('updraft_dir' == $key) {
					$options[$key] = UpdraftPlus_Manipulation_Functions::prune_updraft_dir_prefix($value);
				} elseif (preg_match('/^updraft_(.*)$/', $key, $matches) && in_array($matches[1], $backup_methods)) {
					$options[$key] = call_user_func(array($updraftplus, 'storage_options_filter'), $value, $key);
				} elseif (preg_match("/^updraft_/", $key)) {
					$options[$key] = $value;
				}
			}

			foreach (array('updraft_delete_local', 'updraft_debug_mode', 'updraft_include_plugins', 'updraft_include_themes', 'updraft_include_uploads', 'updraft_include_others', 'updraft_include_blogs', 'updraft_include_wpcore', 'updraft_include_more', 'updraft_include_mu-plugins', 'updraft_ssl_useservercerts', 'updraft_ssl_disableverify', 'updraft_ssl_nossl', 'updraft_log_syslog', 'updraft_autobackup_default') as $key) {
				if (empty($new_options[$key])) $options[$key] = false;
			}

			if (empty($new_options['updraft_service'])) $options['updraft_service'] = 'none';
			if (empty($new_options['updraft_email'])) $options['updraft_email'] = '';

			if (empty($new_options['updraft_report_warningsonly'])) $new_options['updraft_report_warningsonly'] = array();
			if (empty($new_options['updraft_report_wholebackup'])) $new_options['updraft_report_wholebackup'] = array();
			if (empty($new_options['updraft_report_dbbackup'])) $new_options['updraft_report_dbbackup'] = array();

			$options['updraft_report_warningsonly'] = $updraftplus_admin->return_array($new_options['updraft_report_warningsonly']);
			$options['updraft_report_wholebackup'] = $updraftplus_admin->return_array($new_options['updraft_report_wholebackup']);
			$options['updraft_report_dbbackup'] = $updraftplus_admin->return_array($new_options['updraft_report_dbbackup']);

			update_site_option('updraftplus_options', $options);

			return $options;
		}
	}

	register_activation_hook('updraftplus', array('UpdraftPlus_Options', 'set_defaults'));

	add_action('network_admin_menu', array('UpdraftPlus_Options', 'add_admin_pages'));
	add_action('admin_init', array('UpdraftPlus_Options', 'admin_init'), 15);

	class UpdraftPlusAddOn_MultiSite {
	
		/**
		 * Class constructor
		 */
		public function __construct() {
			add_filter('updraft_backupable_file_entities', array($this, 'add_backupable_file_entities'), 10, 2);
			add_filter('updraft_admin_menu_hook', array($this, 'updraft_admin_menu_hook'));
			add_action('wp_before_admin_bar_render', array($this, 'add_networkadmin_page'));
			add_filter('updraftplus_restore_all_downloaded_postscan', array($this, 'restore_all_downloaded_postscan'), 20, 7);
			add_action('updraftplus_admin_enqueue_scripts', array($this, 'updraftplus_admin_enqueue_scripts'));
			add_filter('updraft_restore_maintenance_mode', array($this, 'updraftplus_single_site_maintenance_mode'), 10, 4);
			
			// Actions/filters that need UD to be fully loaded before we can consider adding them
			add_action('plugins_loaded', array($this, 'plugins_loaded'));
		}
		
		/**
		 * Runs upon the WP action plugins_loaded
		 */
		public function plugins_loaded() {
			global $updraftplus;
			// We don't support restoring specific sites within a multisite until WP 3.5
			if (is_a($updraftplus, 'UpdraftPlus') && method_exists($updraftplus, 'get_wordpress_version') && version_compare($updraftplus->get_wordpress_version(), '3.5', '>=')) {
				add_filter('updraftplus_restore_this_table', array($this, 'restore_this_table'), 10, 3);
				add_filter('updraftplus_restore_this_site', array($this, 'restore_this_site'), 10, 4);
				add_filter('updraft_backupable_file_entities_on_restore', array($this, 'backupable_file_entities_on_restore'), 10, 3);
				add_filter('updraft_restore_backup_move_from', array($this, 'restore_backup_move_from'), 10, 4);
				add_filter('updraft_move_existing_to_old_short_circuit', array($this, 'move_existing_to_old_short_circuit'), 10, 3);
				add_filter('updraftplus_restore_delete_recursive', array($this, 'restore_delete_recursive'), 10, 3);
				add_filter('updraft_move_others_preserve_existing', array($this, 'move_others_preserve_existing'), 10, 4);
				add_filter('updraftplus_restore_move_old_mode', array($this, 'restore_move_old_mode'), 10, 3);
				add_action('updraftplus_restore_set_table_prefix_multisite_got_new_blog_id', array($this, 'drop_existing_non_wpcore_tables'), 10, 2);
			}
		}
		
		public function restore_move_old_mode($move_old_destination, $type, $restore_options) {
			$selective_restore_site_id = (is_array($restore_options) && !empty($restore_options['updraft_restore_ms_whichsites']) && $restore_options['updraft_restore_ms_whichsites'] > 0) ? $restore_options['updraft_restore_ms_whichsites'] : false;
			if ($selective_restore_site_id && ('uploads' == $type && !get_site_option('ms_files_rewriting')) || ('others' == $type && get_site_option('ms_files_rewriting'))) {
				$move_old_destination = -1;
			}
			return $move_old_destination;
		}

		public function move_others_preserve_existing($preserve_existing, $been_restored, $restore_options, $ud_backup_info) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use for $ud_backup_info
			$selective_restore_site_id = (is_array($restore_options) && !empty($restore_options['updraft_restore_ms_whichsites']) && $restore_options['updraft_restore_ms_whichsites'] > 0) ? $restore_options['updraft_restore_ms_whichsites'] : false;
			if ($selective_restore_site_id && Updraft_Restorer::MOVEIN_MAKE_BACKUP_OF_EXISTING == $preserve_existing) {
				$preserve_existing = Updraft_Restorer::MOVEIN_OVERWRITE_NO_BACKUP;
			}
			return $preserve_existing;
		}
		
		public function restore_delete_recursive($recurse, $ud_foreign, $restore_options) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
			if ($recurse) return $recurse;
			$selective_restore_site_id = (is_array($restore_options) && !empty($restore_options['updraft_restore_ms_whichsites']) && $restore_options['updraft_restore_ms_whichsites'] > 0) ? $restore_options['updraft_restore_ms_whichsites'] : false;
			if ($selective_restore_site_id) return true;
			return $recurse;
		}
		
		public function move_existing_to_old_short_circuit($short_circuit, $type, $restore_options) {
			if ($short_circuit) return $short_circuit;
			global $updraftplus;
			$selective_restore_site_id = (is_array($restore_options) && !empty($restore_options['updraft_restore_ms_whichsites']) && $restore_options['updraft_restore_ms_whichsites'] > 0) ? $restore_options['updraft_restore_ms_whichsites'] : false;

			if (!$selective_restore_site_id) return $short_circuit;
			
			// This filter isn't actually called when type is 'others'
			if ('uploads' != $type && 'others' != $type) return $short_circuit;
			if (('uploads' == $type && get_site_option('ms_files_rewriting')) || ('others' == $type && !get_site_option('ms_files_rewriting'))) return $short_circuit;
			
			if (is_main_network() && is_main_site($selective_restore_site_id)) {
				if ('uploads' == $type) {
					$updraftplus->log("This is a selective restore of uploads for the main network and site - will not move existing data out of the way (in consequence of which, your final on-disk state may include files which were not in the backup)");
					return true;
				}
			} else {
				if ('others' == $type) {
					$updraftplus->log("This is a selective restore of other wp-content data for a site which is not the main network and site, on a pre-WP-3.5-type install - will not move existing data out of the way (in consequence of which, your final on-disk state may include files which were not in the backup)");
					return true;
				}
			}
		}
		
		/**
		 * $move_from is a WP_Filesystem path
		 *
		 * @param  string $move_from
		 * @param  string $type
		 * @param  array  $restore_options
		 * @param  array  $backup_info
		 * @return string
		 */
		public function restore_backup_move_from($move_from, $type, $restore_options, $backup_info) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use for $backup_info
		
			$selective_restore_site_id = (is_array($restore_options) && !empty($restore_options['updraft_restore_ms_whichsites']) && $restore_options['updraft_restore_ms_whichsites'] > 0) ? $restore_options['updraft_restore_ms_whichsites'] : false;
			
			if (!$selective_restore_site_id || ('uploads' != $type && 'others' != $type) || !is_multisite()) return $move_from;
			
			// Others is only interesting on pre-WP-3.5 style multisites
			if ('others' == $type && !get_site_option('ms_files_rewriting')) return $move_from;

			// Uploads is not interesting on pre-WP-3.5 style multisites
			if ('uploads' == $type && get_site_option('ms_files_rewriting')) return $move_from;
			
			global $updraftplus, $wp_filesystem;
			
			if (is_main_network() && is_main_site($selective_restore_site_id)) {
				// Remove the other stuff from the backup working folder, so that we don't restore it
				$updraftplus->log_e("Restoring only the site with id=%s: removing other data (if any) from the unpacked backup", 'main');
				
				if ('uploads' == $type) {
					@$wp_filesystem->delete($move_from.'/sites', true);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				} elseif ('others' == $type) {
					@$wp_filesystem->delete($move_from.'/blogs.dir', true);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				}
				
			} else {
			
				$updraftplus->log_e("Restoring only the site with id=%s: removing other data (if any) from the unpacked backup", $selective_restore_site_id);
				
				if ('uploads' == $type) {
					// Sanity check
					if (!$wp_filesystem->exists($move_from.'/sites')) {
						$updraftplus->log("Could not find sites directory: aborting (${move_from}/sites)");
						return false;
					}
					// Remove stuff not in uploads/sites
					$potential_del_files = $wp_filesystem->dirlist($move_from, true, false);
					if (empty($potential_del_files)) $potential_del_files = array();
					foreach ($potential_del_files as $file => $filestruc) {
						if (empty($file)) continue;
						if ('sites' != strtolower($file)) @$wp_filesystem->delete($move_from.'/'.$file, true);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					}
					// Remove stuff in uploads/sites that does not match our ID
					$potential_del_files = $wp_filesystem->dirlist($move_from.'/sites', true, false);
					if (empty($potential_del_files)) $potential_del_files = array();
					foreach ($potential_del_files as $file => $filestruc) {
						if (empty($file)) continue;
						if ($file != $selective_restore_site_id) @$wp_filesystem->delete($move_from.'/sites/'.$file, true);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					}
					
					return $move_from.'/sites/'.$selective_restore_site_id;
					
				} elseif ('others' == $type) {
					// Sanity check
					if (!$wp_filesystem->exists($move_from.'/blogs.dir')) {
						$updraftplus->log("Could not find blogs.dir directory: aborting (${move_from}/blogs.dir)");
						return false;
					}
					// Remove stuff not in wp-content/blogs.dir
					$potential_del_files = $wp_filesystem->dirlist($move_from, true, false);
					if (empty($potential_del_files)) $potential_del_files = array();
					foreach ($potential_del_files as $file => $filestruc) {
						if (empty($file)) continue;
						if ('blogs.dir' != strtolower($file)) @$wp_filesystem->delete($move_from.'/'.$file.'/files', true);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					}
					// Remove stuff in uploads/sites that does not match our ID
					$potential_del_files = $wp_filesystem->dirlist($move_from.'/blogs.dir', true, false);
					if (empty($potential_del_files)) $potential_del_files = array();
					foreach ($potential_del_files as $file => $filestruc) {
						if (empty($file)) continue;
						if ($file != $selective_restore_site_id) @$wp_filesystem->delete($move_from.'/blogs.dir/'.$file.'/files', true);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					}
					
					return $move_from.'/blogs.dir/'.$selective_restore_site_id.'/files';
					
				}
			}
			return $move_from;
		}
		
		/**
		 * $backupable_entities is in the 'full info' format
		 *
		 * @param  array $backupable_entities [description]
		 * @param  array $restore_options     [description]
		 * @param  array $backup_set          [description]
		 * @return array
		 */
		public function backupable_file_entities_on_restore($backupable_entities, $restore_options, $backup_set) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use for $backup_set 

			if (!is_array($restore_options) || !is_array($backupable_entities) || empty($backupable_entities['uploads']) || empty($restore_options['updraft_restore_ms_whichsites']) || $restore_options['updraft_restore_ms_whichsites'] < 1 || !is_multisite()) return $backupable_entities;

			// User has selected only one specific site to restore
			switch_to_blog($restore_options['updraft_restore_ms_whichsites']);
			
			// Get WP to tell us where this particular site's uploads lives

			$wp_upload_dir = wp_upload_dir();
			
			// Re-populate the data with the new result
			$backupable_entities['uploads'] = array(
				'path' => untrailingslashit($wp_upload_dir['basedir']),
				'description' => __('Uploads', 'updraftplus')
			);
			
			restore_current_blog();

			return $backupable_entities;
		}
		
		/**
		 * $site_id is >=2 in current implementations (not called otherwise)
		 *
		 * @param  Boolean $restore_or_not        - whether to restore the site
		 * @param  String  $site_id
		 * @param  String  $unprefixed_table_name
		 * @param  String  $restore_options
		 *
		 * @return Boolean                        - filtered value
		 */
		public function restore_this_site($restore_or_not, $site_id, $unprefixed_table_name, $restore_options) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		
			if (!empty($restore_options['updraft_restore_ms_whichsites']) && -1 != $restore_options['updraft_restore_ms_whichsites']) {
		
				if ($site_id != $restore_options['updraft_restore_ms_whichsites']) {
					return false;
				}
				
			}
			
			return $restore_or_not;
		
		}
		
		public function restore_this_table($restore_or_not, $unprefixed_table_name, $restore_options) {

			if (!$restore_or_not) return $restore_or_not;

			if (!empty($restore_options['updraft_restore_ms_whichsites'])) {
			
				if (-1 == $restore_options['updraft_restore_ms_whichsites']) return true;
			
				if (is_main_network() && is_main_site($restore_options['updraft_restore_ms_whichsites'])) {
				
					$dont_restore_on_main = array('site', 'sitemeta', 'users', 'usermeta', 'blogs', 'blog_versions', 'signups', 'registration_log');
					
					if (in_array($unprefixed_table_name, $dont_restore_on_main)) return false;
				
					$require_prefix = '';
					
				} else {
					$require_prefix = $restore_options['updraft_restore_ms_whichsites'].'_';
				}
				
				$has_prefix = preg_match('/^(\d+_).*$/', $unprefixed_table_name, $matches) ? $matches[1] : '';
				$restore_or_not = ($require_prefix == $has_prefix);
			}

			return $restore_or_not;
		}

		/**
		 * This function will try to turn on our maintenance mode if a single site in a multisite is being restored, if unsuccessful return true otherwise false
		 *
		 * @param Boolean $filter_value         - the filter value being passed in, we return this in the event of an error
		 * @param Boolean $active               - boolean to indicate if we are turning maintenance mode off or on
		 * @param Object  $updraftplus_restorer - the updraftplus restorer object
		 * @param Object  $wp_upgrader          - the wordpress upgrader object
		 *
		 * @return Boolean - returns a boolean to indicate if we should turn on WordPress maintenance mode or not
		 */
		public function updraftplus_single_site_maintenance_mode($filter_value, $active, $updraftplus_restorer, $wp_upgrader) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $wp_upgrader Used in a Filter
			
			global $updraftplus;
			
			// Check this is a multisite install and that we are trying to restore a single site
			if (!is_multisite() || false == $updraftplus_restorer->ud_multisite_selective_restore || 1 >= $updraftplus_restorer->ud_multisite_selective_restore) return $filter_value;

			switch_to_blog($updraftplus_restorer->ud_multisite_selective_restore);
			
			$wp_upload_dir = wp_upload_dir();
			$subsite_dir = $wp_upload_dir['basedir'].'/';

			restore_current_blog();
			
			if ($active) {
				if (!file_put_contents($subsite_dir.'.maintenance', time())) {
					$updraftplus->log("Failed to put subsite into maintenance mode, falling back to WordPress default");
					return $filter_value;
				}
			} else {
				if (file_exists($subsite_dir.'.maintenance')) unlink($subsite_dir.'.maintenance');
			}

			return false;
		}
		
		/**
		 * Called upon the WP action updraftplus_admin_enqueue_scripts
		 */
		public function updraftplus_admin_enqueue_scripts() {
			global $updraftplus;
			$updraftplus->enqueue_select2();
		}
		
		/**
		 * After &$info, &$mess, &$warn, &$err are also available (but we are not using them currently)
		 *
		 * @param array 	$backups
		 * @param timestamp $timestamp
		 * @param array 	$entities
		 * @param array 	$info
		 * @return void
		 */
		public function restore_all_downloaded_postscan($backups, $timestamp, $entities, &$info) {
			global $updraftplus;

		// "Others", because on pre-WP-3.5-style networks, uploads are in wp-content/blogs.dir
			// $entities is an set of index numbers, which in the common case of 1 zip only, will be '0' - so, don't test with empty()
			if (!is_array($info) || empty($info['multisite']) || !is_array($entities) || (!isset($entities['db']) && (!isset($entities['uploads']) || get_site_option('ms_files_rewriting')) && (!isset($entities['others']) || !get_site_option('ms_files_rewriting'))) || (isset($info['same_url']) && !$info['same_url'] && !$info['url_scheme_change'])) return;

			// if (!is_array($info) || empty($info['multisite']) || empty($info['same_url'])) return $info;

			// if (!defined('UPDRAFTPLUS_MULTISITE_EXPERIMENTAL_RESTOREONLYONE') || !UPDRAFTPLUS_MULTISITE_EXPERIMENTAL_RESTOREONLYONE) return $info;

			// N.B. "If wp_is_large_network() returns TRUE, wp_get_sites() will return an empty array. By default wp_is_large_network() returns TRUE if there are 10,000 or more sites in your network." - https://codex.wordpress.org/Function_Reference/wp_get_sites (wp_is_large_network() is since WP 3.3)
			
			$added_header = false;
			
			// Are there any entities being restored except the potentially per-site ones?
			$other_entities = $entities;
			unset($other_entities['db']);
			unset($other_entities['uploads']);
			unset($other_entities['others']);
			
			$wp_version = $updraftplus->get_wordpress_version();
			$page = 0;
			$sites = array();
			while (!$page || (!empty($sites) && 100 == count($sites))) {
				// N.B. get_current_site() actually gets the current network - the function is named using old terminology
				$current_network = get_current_site();
				$params = array('network_id' => $current_network->id, 'offset' => $page * 100, 'limit' => 100);
				// The wp_get_sites() function is deprecated since WP version 4.6.0!
				$sites = (function_exists('wp_get_sites') && version_compare($wp_version, '4.6.0', '<')) ? wp_get_sites($params) : $this->wp_get_sites($params);
				// Check that there's currently >1 site, because otherwise a) there's only one to restore anyway, and b) it may confuse the user if they're thinking they can selectively restore not-currently-existing sites (they can't)
				if (is_array($sites) && count($sites) > 1) {
					foreach ($sites as $si) {
						if (!$added_header) {
							if (empty($info['addui'])) $info['addui'] = '';
							$info['addui'] .= '<strong>'.__('Which site to restore', 'updraftplus').':</strong><br>';
							// Trying to use the 'multiple' attribute here brings into play bugs with Select2 in a jQuery modal
							$class = (!defined('UPDRAFTPLUS_SELECT2_ENABLE') || UPDRAFTPLUS_SELECT2_ENABLE) ? 'updraft_select2' : '';
							$info['addui'] .= '<select style="width:100%;" name="updraft_restore_ms_whichsites" class="'.$class.'">';
							$info['addui'] .= '<option selected="selected" value="-1">'.__('All sites', 'updraftplus').'</option>';
							$added_header = true;
						}
						// Ignoring site_id - that is not supported (i.e. no multi-networks)
						$info['addui'] .= '<option value="'.$si['blog_id'].'">'.htmlspecialchars($si['domain'].$si['path']);
						if (isset($entities['db']) && is_main_network() && is_main_site($si['blog_id'])) $info['addui'] .= ' ('.__('may include some site-wide data', 'updraftplus').')';
						$info['addui'] .= '</option>';
					}
				}
				$page++;
			}
			
			if ($added_header) {
				$info['addui'] .= '</select>';
				if (!empty($other_entities)) $info['addui'] .= '<em>'.__('N.B. this option only affects the restoration of the database and uploads - other file entities (such as plugins) in WordPress are shared by the whole network.').'</em> <a href="https://updraftplus.com/restoring-single-sites-on-network/" target="_blank">'.__('Read more...', 'updraftplus').'</a>';
			}
			// return $info;
		}
		
		/**
		 * Function wp_get_sites doesn't exist until WP 3.7. We provide this for earlier versions.
		 *
		 * @param  array $args
		 * @return array
		 */
		private function wp_get_sites($args = array()) {
			global $wpdb;

			// Does not exist on the versions we're targeting
			// if (wp_is_large_network())
			// return array();

			$defaults = array(
				'network_id' => $wpdb->siteid,
				'public'     => null,
				'archived'   => null,
				'mature'     => null,
				'spam'       => null,
				'deleted'    => null,
				'limit'      => 100,
				'offset'     => 0,
			);

			$args = wp_parse_args($args, $defaults);

			$query = "SELECT * FROM $wpdb->blogs WHERE 1=1 ";

			if (isset($args['network_id']) && (is_array($args['network_id']) || is_numeric($args['network_id']))) {
					$network_ids = implode(',', wp_parse_id_list($args['network_id']));
					$query .= "AND site_id IN ($network_ids) ";
			}

			if (isset($args['public']))
					$query .= $wpdb->prepare("AND public = %d ", $args['public']);

			if (isset($args['archived']))
					$query .= $wpdb->prepare("AND archived = %d ", $args['archived']);

			if (isset($args['mature']))
					$query .= $wpdb->prepare("AND mature = %d ", $args['mature']);

			if (isset($args['spam']))
					$query .= $wpdb->prepare("AND spam = %d ", $args['spam']);

			if (isset($args['deleted']))
					$query .= $wpdb->prepare("AND deleted = %d ", $args['deleted']);

			if (isset($args['limit']) && $args['limit']) {
				if (isset($args['offset']) && $args['offset']) {
					$query .= $wpdb->prepare("LIMIT %d , %d ", $args['offset'], $args['limit']);
				} else {
					$query .= $wpdb->prepare("LIMIT %d ", $args['limit']);
				}
			}

			$site_results = $wpdb->get_results($query, ARRAY_A);

			return $site_results;
		}
		
		public function add_backupable_file_entities($arr, $full_info) {
			// Post-3.5, WordPress multisite puts uploads from blogs by default into the uploads directory (i.e. no separate location). This is indicated not by the WP version number, but by the option ms_files_rewriting (which won't exist pre-3.5). See wp_upload_dir()
			// This is a compatible way of getting the current blog's upload directory. Because of our access setup, that always resolves to the site owner's upload directory
			global $updraftplus;
			if ($full_info) {
				$arr['mu-plugins'] = array(
					'path' => WPMU_PLUGIN_DIR,
					'description' => __('Must-use plugins', 'updraftplus')
				);
				if (!get_option('ms_files_rewriting') && defined('UPLOADBLOGSDIR')) {
					$ud = $updraftplus->wp_upload_dir();
					if (strpos(UPLOADBLOGSDIR, false === $ud['basedir'])) {
						$arr['blogs'] = array(
							'path' => ABSPATH.UPLOADBLOGSDIR,
							'description' => __('Blog uploads', 'updraftplus')
						);
					}
				}
			} else {
				$arr['mu-plugins'] = WPMU_PLUGIN_DIR;
				if (!get_option('ms_files_rewriting') && defined('UPLOADBLOGSDIR')) {
					$ud = $updraftplus->wp_upload_dir();
					if (strpos(UPLOADBLOGSDIR,  false === $ud['basedir'])) {
						$arr['blogs'] = ABSPATH.UPLOADBLOGSDIR;
					}
				}
			}
			return $arr;
		}

		/**
		 * WP filter updraft_admin_menu_hook
		 *
		 * @param String $h - existing action to use for the menu hook
		 *
		 * @return String - filtered value
		 */
		public function updraft_admin_menu_hook() {
			return 'network_admin_menu';
		}

		public function add_networkadmin_page() {
			global $wp_admin_bar;
			
			if (!is_object($wp_admin_bar) || !is_super_admin() || !function_exists('is_admin_bar_showing') || !is_admin_bar_showing()) {
				if (!apply_filters('updraft_user_can_manage', false, true)) return;
			}
			
			$wp_admin_bar->add_node(array(
				'parent' => 'network-admin',
				'id' => 'updraftplus-admin-settings',
				'title' => __('UpdraftPlus Backups', 'updraftplus'),
				'href' => UpdraftPlus_Options::admin_page_url().'?page=updraftplus'
			));
		}

		/**
		 * Perform deletion of non WP core tables for the given Blog ID
		 *
		 * @param Integer $blog_id      Site Blog ID
		 * @param String  $table_prefix System table prefix
		 */
		public function drop_existing_non_wpcore_tables($blog_id, $table_prefix) {
			if (!$blog_id) return;
			global $wpdb, $updraftplus;
			$tables = array();
			$blog_tables = $wpdb->tables('blog', true, (int) $blog_id);
			foreach ($wpdb->get_results("SHOW TABLES LIKE '".$table_prefix.$blog_id."_%'") as $table) {
				$tables = array_merge($tables, array_values(get_object_vars($table)));
			}
			$tables = array_unique($tables);
			$tables = array_diff($tables, $blog_tables);
			$suppress = $wpdb->suppress_errors();
			foreach ($tables as $table) {
				if (!$wpdb->query('DROP TABLE IF EXISTS '.UpdraftPlus_Manipulation_Functions::backquote(str_replace('`', '``', $table))) && !empty($wpdb->last_error)) {
					$updraftplus->log(__METHOD__.' : '.$wpdb->last_error.' - '.$wpdb->last_query);
				}
			}
			$wpdb->suppress_errors($suppress);
		}
	}
	
	new UpdraftPlusAddOn_MultiSite;

}
