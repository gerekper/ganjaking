<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: migrator:Migrate a WordPress site to a different location.
Description: Import a backup into a different site, including database search-and-replace. Ideal for development and testing and cloning of sites.
Version: 3.7
Shop: /shop/migrator/
Latest Change: 1.14.12
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

// Search/replace code adapted in according with the licence from https://github.com/interconnectit/Search-Replace-DB

global $updraftplus_addons_migrator;
if (!is_a($updraftplus_addons_migrator, 'UpdraftPlus_Addons_Migrator')) $updraftplus_addons_migrator = new UpdraftPlus_Addons_Migrator;

class UpdraftPlus_Addons_Migrator {

	private $is_migration;

	private $restored_blogs = false;

	private $restored_sites = false;

	private $wpdb_obj = false;

	private $restore_options = array();

	private $page_size = 5000;

	private $old_abspath = '';
	
	// This is also used to detect the situation of importing a single site into a multisite
	// Public, as it is used externally
	public $new_blogid;

	/**
	 * Constructor, called during UD initialisation
	 */
	public function __construct() {
		add_action('updraftplus_restored_db', array($this, 'updraftplus_restored_db'), 10, 2);
		add_action('updraftplus_restored_db_table', array($this, 'updraftplus_restored_db_table'), 10, 3);
		add_action('updraftplus_restore_db_pre', array($this, 'updraftplus_restore_db_pre'));
		add_action('updraftplus_restore_db_record_old_siteurl', array($this, 'updraftplus_restore_db_record_old_siteurl'));
		add_action('updraftplus_restore_db_record_old_home', array($this, 'updraftplus_restore_db_record_old_home'));
		add_action('updraftplus_restore_db_record_old_content', array($this, 'updraftplus_restore_db_record_old_content'));
		add_action('updraftplus_restore_db_record_old_uploads', array($this, 'updraftplus_restore_db_record_old_uploads'));
		add_action('updraftplus_restore_db_record_old_abspath', array($this, 'updraftplus_restore_db_record_old_abspath'));
		add_action('updraftplus_restored_plugins_one', array($this, 'restored_plugins_one'));
		add_action('updraftplus_restored_themes_one', array($this, 'restored_themes_one'));
		add_action('updraftplus_debugtools_dashboard', array($this, 'debugtools_dashboard'), 30);
		add_action('updraftplus_adminaction_searchreplace', array($this, 'adminaction_searchreplace'));
		add_action('updraftplus_migrate_tab_output', array($this, 'updraftplus_migrate_tab_output'));
		add_action('updraftplus_creating_table', array($this, 'updraftplus_creating_table'), 10, 1);
		// Displaying notices after migration if migrated url exists in .htaccess file
		add_action('all_admin_notices', array($this, 'migration_admin_notices'));
		 
		add_filter('updraftplus_restore_set_table_prefix', array($this, 'restore_set_table_prefix'), 10, 2);
		add_filter('updraftplus_dbscan_urlchange', array($this, 'dbscan_urlchange'), 10, 3);
		add_filter('updraftplus_https_to_http_additional_warning', array($this, 'https_to_http_additional_warning'), 10, 1);
		add_filter('updraftplus_http_to_https_additional_warning', array($this, 'http_to_https_additional_warning'), 10, 1);
		add_filter('updraftplus_dbscan_urlchange_www_append_warning', array($this, 'dbscan_urlchange_www_append_warning'), 10, 1);
		
		add_filter('updraftplus_restorecachefiles', array($this, 'restorecachefiles'), 10, 2);
		add_filter('updraftplus_restored_plugins', array($this, 'restored_plugins'));
		add_filter('updraftplus_get_history_status_result', array($this, 'get_history_status_result'));
		// Actions/filters that need UD to be fully loaded before we can consider adding them
		add_action('plugins_loaded', array($this, 'plugins_loaded'));
	}
	
	public function plugins_loaded() {
		global $updraftplus;
		// We don't support restoring single sites into multisite until WP 3.5
		// Some (significantly out-dated) information on what import-into-multisite involves: http://iandunn.name/comprehensive-wordpress-multisite-migrations/
		if (is_a($updraftplus, 'UpdraftPlus') && method_exists($updraftplus, 'get_wordpress_version') && version_compare($updraftplus->get_wordpress_version(), '3.5', '>=')) {
			add_filter('updraftplus_restore_all_downloaded_postscan', array($this, 'restore_all_downloaded_postscan'), 10, 7);
			add_filter('updraftplus_restore_this_table', array($this, 'restore_this_table'), 10, 3);
			add_filter('updraftplus_pre_restore_move_in', array($this, 'pre_restore_move_in'), 10, 7);
			add_action('updraftplus_restorer_restore_options', array($this, 'restorer_restore_options'));
			add_filter('updraftplus_restore_delete_recursive', array($this, 'restore_delete_recursive'), 10, 4);
			add_action('updraftplus_admin_enqueue_scripts', array($this, 'updraftplus_admin_enqueue_scripts'));
		}
	}

	public function updraftplus_admin_enqueue_scripts() {
		global $updraftplus;
		$updraftplus->enqueue_select2();
	}
	
	public function restore_delete_recursive($recurse, $ud_foreign, $restore_options, $type) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if ($recurse) return $recurse;
		// If doing a single-site-to-multisite import on the uploads, then we expect subdirectories to be around - they need deleting without raising any user-visible errors
		return ('uploads' == $type && !empty($this->new_blogid)) ? true : $recurse;
	}
	
	public function pre_restore_move_in($now_done, $type, $working_dir, $info, $backup_info, $restorer, $wp_filesystem_dir) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if ($now_done) return $now_done;

		if (is_multisite() && 0 == $restorer->ud_backup_is_multisite && (('plugins' == $type || 'themes' == $type) || ('uploads' == $type && !empty($this->new_blogid) && !get_site_option('ms_files_rewriting')))) {

			global $wp_filesystem, $updraftplus;
		
			$skin = $restorer->ud_get_skin();
		
			// Migrating a single site into a multisite
			if ('plugins' == $type || 'themes' == $type) {

				$move_from = $restorer->get_first_directory($working_dir, array(basename($info['path']), $type));
				// Only move in entities that are not already there
				$move_mode = Updraft_Restorer::MOVEIN_DO_NOTHING_IF_EXISTING;

				$skin->feedback('moving_backup');

				$new_move_failed = (false === $move_from) ? true : false;
				if (false === $new_move_failed) {
					$move_in = $restorer->move_backup_in($move_from, trailingslashit($wp_filesystem_dir), $move_mode, array(), $type, true);
					if (is_wp_error($move_in)) return $move_in;
					if (!$move_in) $new_move_failed = true;
				}
				if ($new_move_failed) return new WP_Error('new_move_failed', $restorer->strings['new_move_failed']);
				@$wp_filesystem->delete($move_from);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

				// Nothing more needs doing
				$now_done = true;
				
			} elseif ('uploads' == $type) {

				$skin->feedback('moving_old');

				switch_to_blog($this->new_blogid);

				$ud = wp_upload_dir();
				$wpud = $ud['basedir'];
				$fsud = trailingslashit($wp_filesystem->find_folder($wpud));

				restore_current_blog();
				
				if (!is_string($fsud)) {
					$updraftplus->log("Could not find basedir folder for site ($wpud)");
					return new WP_Error('new_move_failed', $restorer->strings['new_move_failed']);
				}
				
				$updraftplus->log("Will move into: ".$fsud);
				
				return $fsud;
				
				// This now drops through to the uploads section
			}
		}
		return $now_done;
	}
	
	public function restorer_restore_options($restore_options) {
		$this->restore_options = $restore_options;
	}

	public function restore_this_table($restore_or_not, $unprefixed_table_name, $restore_options) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		// We're only interested in filtering out the user/usermeta table when importing single site into multisite
		if (!$restore_or_not || empty($this->new_blogid)) return $restore_or_not;

		// Don't restore these - they're not used
		if ('users' == $unprefixed_table_name || 'usermeta' == $unprefixed_table_name) return false;
		
		return $restore_or_not;
	}

	/**
	 * Runs upon the WP filter updraftplus_get_history_status_result
	 *
	 * @param Array $result - the pre-filtered information
	 *
	 * @return Array - after our filtering
	 */
	public function get_history_status_result($result) {
		if (!is_array($result)) return $result;
		ob_start();
		$this->updraftplus_migrate_tab_output();
		$tab_output = ob_get_contents();
		ob_end_clean();
		$result['migrate_tab'] = $tab_output;
		return $result;
	}
	
	public function updraftplus_migrate_tab_output() {
		global $updraftplus, $updraftplus_admin;// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Its used on line 187 but for some reason its flagged assuming becuase of the closed and open php tags ?>
		<div id="updraft_migrate_tab_main">

			<?php $updraftplus_admin->include_template('wp-admin/settings/temporary-clone.php'); ?>

			<h2><?php _e('Migrate (create a copy of a site on hosting you control)', 'updraftplus'); ?></h2>

			<div id="updraft_migrate" class="postbox">
				<div class="updraft_migrate_intro">
					<p>
						<?php echo __('A "migration" is ultimately the same as a restoration - but using backup archives that you import from another site.', 'updraftplus').' '.__('The UpdraftPlus Migrator modifies the restoration operation appropriately, to fit the backup data to the new site.', 'updraftplus'); ?>
						<a href="https://updraftplus.com/faqs/how-do-i-migrate-to-a-new-site-location/" target="_blank"><?php _e('Read this article to see step-by-step how it\'s done.', 'updraftplus'); ?></a>
					</p>
				</div>
				<?php
				
				echo $this->migrate_widget();

				do_action('updraft_migrate_after_widget');
				
				?>
				<div id="updraft_migrate_tab_alt" style="display:none;"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the HTML of the migrate widget
	 *
	 * @param Array|Boolean $backup_history - the backup history list to use, or false to get the current list
	 *
	 * @return String - the HTML
	 */
	private function migrate_widget($backup_history = false) {
	
		global $updraftplus, $updraftplus_admin;
 
		if (false === $backup_history) $backup_history = UpdraftPlus_Backup_History::get_history();
		
		// Save on SQL queries by using the method that batch-fetches
		$backup_history = UpdraftPlus_Backup_History::add_jobdata($backup_history);

		$ret = '<div class="updraft_migrate_widget_module_content">';
		$ret .= '<header>';
		$ret .= '<button class="button button-link close"><span class="dashicons dashicons-arrow-left-alt2"></span>'.__('back', 'updraftplus').'</button>';
		$ret .= '<h3><span class="dashicons dashicons-migrate"></span>'.__('Restore an existing backup set onto this site', 'updraftplus').'</h3>';
		$ret .= '</header>';

		$ret .= '<a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'" onclick="jQuery(\'#updraft-navtab-backups\').trigger(\'click\'); return false;">'.__('To import a backup set, go to the "Existing backups" section in the "Backup/Restore" tab', 'updraftplus')."</a>";
		
		if (empty($backup_history)) {
			$ret .= '<p><em>'.__('This site has no backups to restore from yet.', 'updraftplus').'</em></p>';
			$ret .= '</div>';
			return $ret;
		}

		$incremental_set_found = false;

		$ret .= '<p class="updraft_migrate_select_backup">
			<select id="updraft_migrate_select_backup">';

		krsort($backup_history);
		foreach ($backup_history as $key => $backup) {
			if (count($backup['incremental_sets']) > 1) $incremental_set_found = true;
			
			// https://core.trac.wordpress.org/ticket/25331 explains why the following line is wrong
			// $pretty_date = date_i18n('Y-m-d G:i',$key);
			// Convert to blog time zone
// $pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int)$key), 'Y-m-d G:i');
			$pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int) $key), 'M d, Y G:i');

			$non = $backup['nonce'];

			$jobdata = isset($backup['jobdata']) ? $backup['jobdata'] : $updraftplus->jobdata_getarray($non);

			// $delete_button = $this->delete_button($key, $non, $backup);

			$date_label = $updraftplus_admin->date_label($pretty_date, $key, $backup, $jobdata, $non, true);

			$ret .= '<option value="'.esc_attr($key).'">'.htmlspecialchars($date_label).'</option>';

		}


		$ret .= '</select>';

		$ret .= '<button id="updraft_migrate_select_backup_go" title="'.__('After pressing this button, you will be given the option to choose which components you wish to migrate', 'updraftplus').'" type="button" class="button button-primary" onclick="var whichset=jQuery(\'#updraft_migrate_select_backup\').val();  updraft_initiate_restore(whichset);">'.__('Restore', 'updraftplus').'</button>';

		$ret .= '</p>';

		if ($incremental_set_found) $ret .= '<p>'.__('For incremental backups, you will be able to choose which increments to restore at a later stage.', 'updraftplus').'</p>';
		
		$ret .= '</div>';

// $ret .= '</tbody></table>';
		return $ret;
	}

	/**
	 * Disable W3TC and WP Super Cache, etc.
	 */
	public function restored_plugins() {
		if (true !== $this->is_migration) return;
		global $updraftplus;
		$active_plugins = maybe_unserialize($updraftplus->option_filter_get('active_plugins'));
		if (!is_array($active_plugins)) return;
		$disable_plugins = array(
			'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
			'wp-super-cache/wp-cache.php' => 'W3 Super Cache',
			'quick-cache/quick-cache.php' => 'Quick Cache',
			'wp-fastest-cache/wpFastestCache.php' => 'WP Fastest Cache'
		);
		foreach ($disable_plugins as $slug => $desc) {
			// in_array is case sensitive
			// if (in_array($slug, $active_plugins)) {
			if (preg_grep("#".$slug."#i", $active_plugins)) {
				unset($active_plugins[$slug]);
				
				$updraftplus->log("Disabled this plugin: %s: re-activate it manually when you are ready.", $desc);
				$updraftplus->log(sprintf(__("Disabled this plugin: %s: re-activate it manually when you are ready.", 'updraftplus'), $desc), 'notice-restore');

			}
		}
		update_option('active_plugins', $active_plugins);
	}

	public function restorecachefiles($val, $file) {
		// On a migration, we don't want to add cache files if they do not already exist (because usually they won't work until re-installed)
		if (true !== $this->is_migration || false == $val) return $val;
		$val = (is_file(WP_CONTENT_DIR.'/'.$file)) ? $val : false;
		if (false == $val) {
			global $updraftplus;
			$updraftplus->log_e("%s: Skipping cache file (does not already exist)", $file);
		}
		return $val;
	}

	public function adminaction_searchreplace($options = array()) {
	
		global $updraftplus_restorer;
		
		$options = wp_parse_args($options, array(
			'show_return_link' => true,
			'show_heading' => true,
		));
	
		if (!empty($options['show_heading'])) echo '<h2>'.__('Search / replace database', 'updraftplus').'</h2>';
		echo '<strong>'.__('Search for', 'updraftplus').':</strong> '.htmlspecialchars($_POST['search'])."<br>";
		echo '<strong>'.__('Replace with', 'updraftplus').':</strong> '.htmlspecialchars($_POST['replace'])."<br>";
		$this->page_size = (empty($_POST['pagesize']) || !is_numeric($_POST['pagesize'])) ? 5000 : $_POST['pagesize'];
		$this->which_tables = (empty($_POST['whichtables'])) ? '' : explode(',', ($_POST['whichtables']));
		if (empty($_POST['search'])) {
			echo sprintf(__("Failure: No %s was given.", 'updraftplus'), __('search term', 'updraftplus'))."<br>";
			
			if (!empty($options['show_return_link'])) {
				echo '<a href="'.UpdraftPlus_Options::admin_page_url().'?page=updraftplus">'.__('Return to UpdraftPlus Configuration', 'updraftplus').'</a>';
			}
			
			return;
		}

		if (empty($updraftplus_restorer) || !is_a($updraftplus_restorer, 'Updraft_Restorer')) {
			// Needed for the UpdraftPlus_WPDB class and Updraft_Restorer::sql_exec() method
			include_once(UPDRAFTPLUS_DIR.'/restorer.php');
			$updraftplus_restorer = new Updraft_Restorer(null, null, true);
			add_filter('updraftplus_logline', array($updraftplus_restorer, 'updraftplus_logline'), 10, 5);
			$updraftplus_restorer->search_replace_obj->updraftplus_restore_db_pre();
		}
		$this->updraftplus_restore_db_pre();
		$this->tables_replaced = array();
		$this->updraftplus_restored_db_dosearchreplace($_POST['search'], $_POST['replace'], $this->base_prefix, false);
		if (!empty($options['show_return_link'])) echo '<a href="'.UpdraftPlus_Options::admin_page_url().'?page=updraftplus">'.__('Return to UpdraftPlus Configuration', 'updraftplus').'</a>';
	}

	/**
	 * This method will check if the newly created table has already been created before, if it has then we should mark it to be search and replaced again.
	 *
	 * @param  String $table - the name of the newly created table
	 */
	public function updraftplus_creating_table($table) {
		global $updraftplus;

		if (!empty($this->tables_replaced[$table]) && $this->tables_replaced[$table]) {
			$this->tables_replaced[$table] = false;
			$updraftplus->log('Warning: This database table has already been created once, now marking it to be search and replaced again - will try to continue but if errors are encountered then check that the backup is correct.', 'notice-restore');
		}
	}

	public function debugtools_dashboard() {
		global $updraftplus_admin;
	?>
		<div class="advanced_tools search_replace">
			<h3><?php _e('Search / replace database', 'updraftplus'); ?></h3>
			<p><em><?php _e('This can easily destroy your site; so, use it with care!', 'updraftplus');?></em></p>
			<form id="search_replace_form" method="post" onsubmit="return(confirm('<?php echo esc_js(__('A search/replace cannot be undone - are you sure you want to do this?', 'updraftplus'));?>'))">
				<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('updraftplus-credentialtest-nonce');?>">
				<input type="hidden" name="action" value="updraftplus_broadcastaction">
				<input type="hidden" name="subaction" value="updraftplus_adminaction_searchreplace">
				<table>
				<?php
					echo $updraftplus_admin->settings_debugrow('<label for="search">'.__('Search for', 'updraftplus').'</label>:', '<input id="search" type="text" name="search" value="" style="width:380px;">');
					echo $updraftplus_admin->settings_debugrow('<label for="replace">'.__('Replace with', 'updraftplus').'</label>:', '<input id="replace" type="text" name="replace" value="" style="width:380px;">');
					echo $updraftplus_admin->settings_debugrow('<label for="pagesize">'.__('Rows per batch', 'updraftplus').'</label>:', '<input id="pagesize" type="number" min="1" step="1" name="pagesize" value="5000" style="width:380px;">');
					echo $updraftplus_admin->settings_debugrow('<label for="whichtables">'.__('These tables only', 'updraftplus').'</label>:', '<input id="whichtables" type="text" name="whichtables" title="'.esc_attr(__('Enter a comma-separated list; otherwise, leave blank for all tables.', 'updraftplus')).'" value="" style="width:380px;">');
				?>
				<?php echo $updraftplus_admin->settings_debugrow('', '<input class="button-primary search_and_replace" type="submit" value="'.esc_attr(__('Go', 'updraftplus')).'">'); ?>
				</table>
			</form>
		</div>
	<?php
	}

	/**
	 * WordPress filter updraftplus_dbscan_urlchange
	 *
	 * @param String $output		  - the unfiltered output (free plugin gives advice that you need the Migrator add-on)
	 * @param String $old_siteurl	  - the old site URL
	 * @param Array	 $restore_options - restoration options
	 *
	 * @return String - filtered
	 */
	public function dbscan_urlchange($output, $old_siteurl, $restore_options) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return sprintf(__('This looks like a migration (the backup is from a site with a different address/URL, %s).', 'updraftplus'), htmlspecialchars($old_siteurl));
	}
	
	/**
	 * WordPress filter updraftplus_https_to_http_additional_warning
	 *
	 * @param String $output - Filter input
	 *
	 * @return String - filtered
	 */
	public function https_to_http_additional_warning($output) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return ' '.__('This restoration will work if you still have an SSL certificate (i.e. can use https) to access the site. Otherwise, you will want to use below search and replace to search/replace the site address so that the site can be visited without https.', 'updraftplus');
	}
	
	/**
	 * WordPress filter updraftplus_http_to_https_additional_warning
	 *
	 * @param String $output - Filter input
	 *
	 * @return String - filtered
	 */
	public function http_to_https_additional_warning($output) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return ' '.__('As long as your web hosting allows http (i.e. non-SSL access) or will forward requests to https (which is almost always the case), this is no problem. If that is not yet set up, then you should set it up, or use below search and replace so that the non-https links are automatically replaced.', 'updraftplus');
	}
	
	/**
	 * WordPress filter updraftplus_dbscan_urlchange_www_append_warning
	 *
	 * @param String $output - the unfiltered output (free plugin gives empty string)
	 *
	 * @return String - filtered
	 */
	public function dbscan_urlchange_www_append_warning($output) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return __('you will want to use below search and replace site location in the database (migrate) to search/replace the site address.', 'updraftplus');
	}
		
	public function restored_plugins_one($plugin) {
		global $updraftplus;
		$updraftplus->log(__('Processed plugin:', 'updraftplus').' '.$plugin, 'notice-restore');
		$updraftplus->log("Processed plugin: $plugin");
	}

	public function restored_themes_one($theme) {
		// Network-activate
		$allowed_themes = get_site_option('allowedthemes');
		$allowed_themes[$theme] = true;
		update_site_option('allowedthemes', $allowed_themes);
		global $updraftplus;
		$updraftplus->log(__('Network activating theme:', 'updraftplus').' '.$theme, 'notice-restore');
		$updraftplus->log('Network activating theme: '.$theme);
	}

	public function restore_set_table_prefix($import_table_prefix, $backup_is_multisite) {
		if (!is_multisite() || 0 !== $backup_is_multisite) return $import_table_prefix;
		
		$new_blogid = $this->generate_new_blogid();

		if (!is_integer($new_blogid)) return $new_blogid;

		do_action('updraftplus_restore_set_table_prefix_multisite_got_new_blog_id', $new_blogid, $import_table_prefix);

		$this->new_blogid = $new_blogid;

		return (string) $import_table_prefix.$new_blogid.'_';
	}

	/**
	 * WordPress action updraftplus_restore_all_downloaded_postscan called during the restore process.
	 *
	 * The last four parameters can be edited in-place.
	 *
	 * @param Array	  $backups	 - list of backups
	 * @param Integer $timestamp - the timestamp (epoch time) of the backup being restored
	 * @param Array	  $elements	 - elements being restored (as the keys of the array)
	 * @param Array	  $info		 - information about the backup being restored
	 * @param Array	  $mess		 - array of informational-level messages
	 * @param Array	  $warn		 - array of warning-level messages
	 * @param Array	  $err		 - array of error-level messages
	 */
	public function restore_all_downloaded_postscan($backups, $timestamp, $elements, &$info, &$mess, &$warn, &$err) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		if (is_array($info) && is_multisite() && isset($info['multisite']) && !$info['multisite']) {

			$original_error_count = count($err);
		
			if (!empty($elements['wpcore'])) {
				$err[] = sprintf(__('You selected %s to be included in the restoration - this cannot / should not be done when importing a single site into a network.', 'updraftplus'), __('WordPress core', 'updraftplus')).' <a href="https://updraftplus.com/information-on-importing-a-single-site-wordpress-backup-into-a-wordpress-network-i-e-multisite/" target="_blank">'.__('Go here for more information.', 'updraftplus').'</a>';
			}
			if (!empty($elements['others'])) {
				$err[] = sprintf(__('You selected %s to be included in the restoration - this cannot / should not be done when importing a single site into a network.', 'updraftplus'), __('other content from wp-content', 'updraftplus')).' <a href="https://updraftplus.com/information-on-importing-a-single-site-wordpress-backup-into-a-wordpress-network-i-e-multisite/" target="_blank">'.__('Go here for more information.', 'updraftplus').'</a>';
			}
			if (!empty($elements['mu-plugins'])) {
				$err[] = sprintf(__('You selected %s to be included in the restoration - this cannot / should not be done when importing a single site into a network.', 'updraftplus'), __('Must-use plugins', 'updraftplus')).' <a href="https://updraftplus.com/information-on-importing-a-single-site-wordpress-backup-into-a-wordpress-network-i-e-multisite/" target="_blank">'.__('Go here for more information.', 'updraftplus').'</a>';
			}

			global $updraftplus;
			if (version_compare($updraftplus->get_wordpress_version(), '3.5', '<')) {
				$err[] = __('Importing a single site into a multisite install', 'updraftplus').': '.sprintf(__('This feature requires %s version %s or later', 'updraftplus'), 'WordPress', '3.5');
			} elseif (get_site_option('ms_files_rewriting')) {
				$err[] = __('Importing a single site into a multisite install', 'updraftplus').': '.sprintf(__('This feature is not compatible with %s', 'updraftplus'), 'pre-WordPress-3.5-style multisite uploads rewriting', 'updraftplus');
			}
			
			if (count($err) > $original_error_count) return;
			
			if (empty($info['addui'])) $info['addui'] = '';
			$info['addui'] .= '<p><strong>'.__('Information needed to continue:', 'updraftplus').'</strong><br>';
			$info['addui'] .= __('Enter details for where this new site is to live within your multisite install:', 'updraftplus').'<br>';
			
			global $current_site;
			
			if (!is_subdomain_install()) {
				$info['addui'] .= ' <label for="blogname">'.$current_site->domain.$current_site->path.'</label><input name="updraftplus_migrate_blogname" data-invalidpattern="'.esc_attr(__('You must use lower-case letters or numbers for the site path, only.', 'updraftplus')).'" pattern="^[a-z0-9]+$" type="text" id="blogname" class="required" value="" maxlength="60" /><br>';
			} else {
				$info['addui'] .= ' <input class="required" name="updraftplus_migrate_blogname" data-invalidpattern="'.esc_attr(__('You must use lower-case letters or numbers for the site path, only.', 'updraftplus')).'" pattern="^[a-z0-9]+$" type="text" id="blogname" value="" maxlength="60" />.<label for="blogname">' . (preg_replace('|^www\.|', '', $current_site->domain)) . '</label><br>';
			}

			$info['addui'] .= '</p>';

			if (!empty($elements['db'])) {
			
				if (empty($info['addui'])) $info['addui'] = '';
				$info['addui'] .= '<p><label for="updraft_restore_content_to_user"><strong>'.__('Attribute imported content to user', 'updraftplus').':</strong></label><br>';
				
				$class = (!defined('UPDRAFTPLUS_SELECT2_ENABLE') || UPDRAFTPLUS_SELECT2_ENABLE) ? 'updraft_select2' : '';
				
				$info['addui'] .= '<selectx style="width:100%;" id="updraft_restore_content_to_user" name="updraft_restore_content_to_user" class="'.$class.'">';

// $main_site_id = $current_site->blog_id;
				$page = 0;

				while (!isset($users) || count($users) > 0) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
					
					$users = get_users(array(
						// Not documented in codex, but the source reveals that to get "all sites", you use an ID of 0
						'blog_id' => 0,
						'offset' => $page * 500,
						'number' => 500,
						'fields' => array('ID', 'user_login', 'user_nicename'),
					));
					
					if (!is_array($users)) $users = array();
					
					foreach ($users as $user) {
						$info['addui'] .= '<option value="'.$user->ID.'">'.htmlspecialchars($user->user_login.' ('.$user->user_nicename.')');
						$info['addui'] .= '</option>';
					}
					$page++;
				}
				
				$info['addui'] .= '</p>';
			}
			
		}
		
		if (is_array($info) && isset($info['migration']) && true === $info['migration']) {
			if (empty($info['addui'])) $info['addui'] = '';
			$info['addui'] .= '<div id="updraft_restorer_dboptions" class="notice before-h2 updraft-restore-option updraft-hidden">';
			$info['addui'] .= '<h4>' . __('Database restoration options:', 'updraftplus') . '</h4>';
			$info['addui'] .= '<input name="updraft_restorer_replacesiteurl" id="updraft_restorer_replacesiteurl" type="checkbox" value="1" checked><label for="updraft_restorer_replacesiteurl" title="'.sprintf(__('All references to the site location in the database will be replaced with your current site URL, which is: %s', 'updraftplus'), htmlspecialchars(untrailingslashit(site_url()))).'"> '.__('Search and replace site location in the database (migrate)', 'updraftplus').'</label>';
			$info['addui'] .= '</div>';
		}
// return $info;
	}

	private function generate_new_blogid() {

		$blog_title = __('Migrated site (from UpdraftPlus)', 'updraftplus');

		if (!isset($this->restore_options['updraftplus_migrate_blogname'])) {
			return new WP_Error('multisite_info_missing', sprintf(__('Required information for restoring this backup was not given (%s)', 'updraftplus'), 'new multisite import location'));
		}

		// Verify value given
		$result = wpmu_validate_blog_signup($this->restore_options['updraftplus_migrate_blogname'], $blog_title);

		if (!empty($result['errors']) && is_wp_error($result['errors']) && $result['errors']->get_error_code()) {
			return $result['errors'];
		}

		global $wpdb, $updraftplus;
		if (domain_exists($result['domain'], $result['path'], $wpdb->siteid)) {
			return new WP_Error('already_taken', sprintf(__('Error: %s', 'updraftplus'), 'Site URL already taken'));
		}

		$create = $this->create_empty_blog($result['domain'], $result['path'], $blog_title, $wpdb->siteid);

		if (is_integer($create)) {
			$url = untrailingslashit($result['domain'].$result['path']);
			
			$updraftplus->log(__('New site:', 'updraftplus').' '.$url, 'notice-restore');

			switch_to_blog($create);
			// Update record of what we want to rewrite the URLs to in the search/replace operation
			$this->siteurl = untrailingslashit(site_url());
			$this->home = untrailingslashit(home_url());

			// The next line can't work, because content_url() fetches from the constant WP_CONTENT_URL
			// $this->content = untrailingslashit(content_url());
			$wp_upload_dir = wp_upload_dir();
			$this->uploads = $wp_upload_dir['baseurl'];
			if (is_subdomain_install()) {
				// For some reason, wp_upload_dir() on a subdomain install tends to return a URL with the host set to a different site's domain, despite switch_to_blog() having been called. Try to detect + fix this (though, it also usually won't matter anyway).
				$uploads_host = parse_url($this->uploads, PHP_URL_HOST);
				$expected_uploads_host = parse_url($this->home, PHP_URL_HOST);
				if ($uploads_host && $expected_uploads_host && $uploads_host != $expected_uploads_host) {
					$this->uploads = UpdraftPlus_Manipulation_Functions::str_replace_once($uploads_host, $expected_uploads_host, $this->uploads);
					$updraftplus->log("wp_upload_dir() returned an unexpected uploads hosts on a subdomain multisite ($uploads_host, rather than $expected_uploads_host) - correcting; destination uploads URL is now: ".$this->uploads);
				}
			}
			// We have to assume that on the imported site, uploads is at /uploads relative to the content directory
			if (empty($this->old_uploads)) $this->old_uploads = $this->old_content.'/uploads';
			$this->content = false;
			$this->old_content = false;
// $this->siteurl = 'http://'.$url;
// $this->home = 'http://'.$url;
// $this->content = // ??
			restore_current_blog();
			
			return $create;
			
		} elseif (is_wp_error($create)) {
			// Currently returns strings for errors, but being ready in case it improves doesn't hurt.
			return $create;
		} else {
			// Things like __('<strong>ERROR</strong>: problem creating site entry.' )
			$updraftplus->log(__('Error when creating new site at your chosen address:', 'updraftplus'), 'warning-restore');
			return new WP_Error('create_empty_blog_failed', __('Error when creating new site at your chosen address:', 'updraftplus').' '.(is_string($create) ? $create : print_r($create, true)));
		}
		
	}
	
	/**
	 * Deprecated in WP 4.4 - https://core.trac.wordpress.org/changeset/34753 - hence, folded into the plugin instead
	 *
	 * @param  string  $domain
	 * @param  string  $path
	 * @param  string  $weblog_title
	 * @param  integer $site_id
	 */
	public function create_empty_blog($domain, $path, $weblog_title, $site_id = 1) {
	
		// Out of an abundance of caution, call the native, un-deprecated version if there is one
		global $updraftplus;
		$wp_version = $updraftplus->get_wordpress_version();
		if (version_compare($wp_version, '4.4', '<') && function_exists('create_empty_blog')) return create_empty_blog($domain, $path, $weblog_title, $site_id);
	 
		if (empty($path)) $path = '/';
	 
		// Check if the domain has been used already. We should return an error message.
		if (domain_exists($domain, $path, $site_id)) return __('<strong>ERROR</strong>: Site URL already taken.');
	 
		// Need to backup wpdb table names, and create a new wp_blogs entry for new blog.
		// Need to get blog_id from wp_blogs, and create new table names.
		// Must restore table names at the end of function.
		
		// insert_blog() and install_blog() are deprecated as of WP 5.1.0.
		// This has also caused an error when using install_blog on 5.1+, so we have switched to the new 'wp_insert_site'
		if (version_compare($wp_version, '5.1', '<')) {
			if (!$blog_id = insert_blog($domain, $path, $site_id)) return __('<strong>ERROR</strong>: problem creating site entry.');
		 
			switch_to_blog($blog_id);
			install_blog($blog_id);
			restore_current_blog();
		} else {
			$blog_data = array(
				'domain' => $domain,
				'path' => $path,
				'network_id' => $site_id,
				'title' => $weblog_title,
			);
			$blog_id = wp_insert_site($blog_data);
		}

		return $blog_id;
	}

	public function updraftplus_restore_db_record_old_siteurl($old_siteurl) {
		// Only record once
		if (!empty($this->old_siteurl)) return;
		$this->old_siteurl = $old_siteurl;
	}

	public function updraftplus_restore_db_record_old_home($old_home) {
		// Only record once
		if (!empty($this->old_home)) return;
		$this->old_home = $old_home;
	}

	public function updraftplus_restore_db_record_old_content($old_content) {
		// Only record once
		if (!empty($this->old_content)) return;
		$this->old_content = $old_content;
	}

	public function updraftplus_restore_db_record_old_uploads($old_uploads) {
		// Only record once
		if (!empty($this->old_uploads)) return;
		$this->old_uploads = $old_uploads;
	}

	/**
	 * This function is called via a filter it saves the passed in old abspath value from restorer.php to a class variable for later use
	 *
	 * @param String $old_abspath - the old abspath
	 *
	 * @return void
	 */
	public function updraftplus_restore_db_record_old_abspath($old_abspath) {
		if ('' !== $this->old_abspath) return;
		$this->old_abspath = $old_abspath;
	}

	public function updraftplus_restore_db_pre() {

		global $wpdb, $updraftplus, $updraftplus_restorer;

		$this->siteurl = untrailingslashit(site_url());
		$this->home = untrailingslashit(home_url());
		$this->content = untrailingslashit(content_url());
		$this->use_wpdb = $updraftplus_restorer->use_wpdb();
		
		$this->base_prefix = $updraftplus->get_table_prefix(false);

		$mysql_dbh = false;
		$use_mysqli = false;

		if (!$this->use_wpdb) {
			// We have our own extension which drops lots of the overhead on the query
			$wpdb_obj = $updraftplus_restorer->get_db_object();
			// Was that successful?
			if (!$wpdb_obj->is_mysql || !$wpdb_obj->ready) {
				$this->use_wpdb = true;
			} else {
				$this->wpdb_obj = $wpdb_obj;
				$mysql_dbh = $wpdb_obj->updraftplus_get_database_handle();
				$use_mysqli = $wpdb_obj->updraftplus_use_mysqli();
			}
		}

		$this->mysql_dbh = $mysql_dbh;
		$this->use_mysqli = $use_mysqli;

		if (true == $this->use_wpdb) $updraftplus->log_e('Database access: Direct MySQL access is not available, so we are falling back to wpdb (this will be considerably slower)');

		if (is_multisite()) {
			$sites = $wpdb->get_results('SELECT id, domain, path FROM '.UpdraftPlus_Manipulation_Functions::backquote($this->base_prefix.'site'), ARRAY_N);
			if (is_array($sites)) {
				$nsites = array();
				foreach ($sites as $site) $nsites[$site[0]] = array($site[1], $site[2]);
				$this->original_sites = $nsites;
			}
		}

		$this->report = array(
			'tables' => 0,
			'rows' => 0,
			'change' => 0,
			'updates' => 0,
			'timetaken' => 0,
			'errors' => array(),
		);

	}

	public function updraftplus_restored_db_table($table, $import_table_prefix, $engine = '') {

		global $updraftplus, $wpdb, $updraftplus_restorer;

		if (!empty($this->new_blogid) && !empty($this->restore_options['updraft_restore_content_to_user'])) {
			if ($table == $import_table_prefix.'posts') {
				$updraftplus->log("Setting all content (posts/post_author) to be owned by ID: ".$this->restore_options['updraft_restore_content_to_user']);
				$posts_updated = $wpdb->query("UPDATE ".UpdraftPlus_Manipulation_Functions::backquote($table)." SET post_author=".(int) $this->restore_options['updraft_restore_content_to_user']);
				if (is_numeric($posts_updated)) {
					$updraftplus->log("Number of rows updated: ".$posts_updated);
				} else {
					$updraftplus->log("An error occurred when updating content ownership");
				}
			} elseif ($table == $import_table_prefix.'postmeta') {
				// Set WooCommerce orders to belong to guest
				$keys_deleted = $wpdb->query("DELETE FROM ".UpdraftPlus_Manipulation_Functions::backquote($table)." WHERE meta_key='_customer_user'");
				if (is_numeric($keys_deleted)) {
					$updraftplus->log("Number of WooCommerce orders re-assigned to Guest: ".$keys_deleted);
				}
			}
		}

		// Anything else to do?
		if (empty($this->restore_options['updraft_restorer_replacesiteurl'])) return;

		// Can only do something if the old siteurl is known
		$old_siteurl = isset($this->old_siteurl) ? $this->old_siteurl : '';
		$old_home = isset($this->old_home) ? $this->old_home : '';
		$old_content = isset($this->old_content) ? $this->old_content : $old_siteurl.'/wp-content';
		// This wasn't stored in the backup header until 1.11.20. It's usually $old_content.'/uploads', but there's no need to force that, as on a default setup, the search/replace is caught by the content replace anyway
		$old_uploads = isset($this->old_uploads) ? $this->old_uploads : false;
		if (!$old_home && !$old_siteurl) return;

		$old_abspath = $this->old_abspath;

		if (empty($this->tables_replaced)) $this->tables_replaced = array();

		// Already done?
		if (!empty($this->tables_replaced[$table])) return;

		// If not done already, then search & replace this table, + record that it is done
		if (function_exists('set_time_limit')) @set_time_limit(1800);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		$stripped_table = substr($table, strlen($import_table_prefix));
		// Remove multisite site number prefix, if relevant
		if (is_multisite() && preg_match('/^(\d+)_(.*)$/', $stripped_table, $matches)) $stripped_table = $matches[2];

		// This array is for tables that a) we know don't need URL search/replacing and b) are likely to be sufficiently big that they could significantly delay the progress of the migrate (and increase the risk of timeouts on hosts that enforce them)
		// The term_relationships table contains 3 columns, all integers. Therefore, we can skip it. It can easily get big, so this is a good time-saver.
		$skip_tables = array('slim_stats', 'statpress', 'term_relationships', 'icl_languages_translations', 'icl_string_positions', 'icl_string_translations', 'icl_strings', 'redirection_logs', 'Counterize', 'Counterize_UserAgents', 'Counterize_Referers', 'adrotate_stats', 'login_security_solution_fail', 'wfHits', 'wfhits', 'wbz404_logs', 'wbz404_redirects', 'wfFileMods', 'wffilemods', 'tts_trafficstats', 'tts_referrer_stats', 'dmsguestbook', 'relevanssi', 'wponlinebackup_generations', 'svisitor_stat', 'simple_feed_stats', 'itsec_log', 'rp_tags', 'woocommerce_order_items', 'relevanssi_log', 'blc_instances', 'wysija_email_user_stat', 'woocommerce_sessions', 'et_bloom_stats', 'redirection_404', 'lbakut_activity_log', 'stream_meta', 'wfBlockedIPLog', 'wfblockediplog', 'page_visit_history', 'strack_st');

		if (in_array($stripped_table, $skip_tables)) {
			$this->tables_replaced[$table] = true;
			$updraftplus->log_e("Skipping this table: data in this table (%s) should not be search/replaced", $table);
			return;
		}
		
		if ('ARCHIVE' == $engine) {
			$this->tables_replaced[$table] = true;
			$updraftplus->log_e("Skipping this table: this table (%s) should not be search/replaced, as it uses the %s engine", $table, $engine);
			return;
		}

		// Blogs table on multisite doesn't contain the full URL
		if (is_multisite() && ($table == $this->base_prefix.'blogs' || $table == $this->base_prefix.'site') && (preg_match('#^https?://([^/]+)#i', $this->home, $matches) || preg_match('#^https?://([^/]+)#i', $this->siteurl, $matches)) && (preg_match('#^https?://([^/]+)#i', $old_home, $omatches) || preg_match('#^https?://([^/]+)#i', $old_siteurl, $omatches))) {
			$from_array = strtolower($omatches[1]);
			$to_array = strtolower($matches[1]);
			$updraftplus->log_e("Replacing in blogs/site table: from: %s to: %s", htmlspecialchars($from_array), htmlspecialchars($to_array));
			$try_site_blog_replace = true;
		} else {

			list($from_array, $to_array) = $this->build_searchreplace_array($old_siteurl, $old_home, $old_content, $old_uploads, $old_abspath);

			// This block is for multisite installs, to do the search/replace of each site's URL individually. We want to try to do it here for efficiency - i.e. so that we don't have to double-pass tables
			if (!empty($this->restored_blogs) && preg_match('/^(\d+)_(.*)$/', substr($table, strlen($import_table_prefix)), $tmatches) && (preg_match('#^((https?://)([^/]+))#i', $this->home, $matches) || preg_match('#^((https?://)([^/]+))#i', $this->siteurl, $matches)) && (preg_match('#^((https?://)([^/]+))#i', $old_home, $omatches) || preg_match('#^((https?://)([^/]+))#i', $old_siteurl, $omatches))) {
				$old_home_domain = strtolower($omatches[3]);
				$new_home_domain = strtolower($matches[3]);
				$blog_id = $tmatches[1];
				if ($old_home_domain == $this->restored_blogs[1]['domain'] && isset($this->restored_blogs[$blog_id])) {
					$bdom = $this->restored_blogs[$blog_id]['domain'];
					$bpath = $this->restored_blogs[$blog_id]['path'];
					$sblog = $omatches[2].$bdom.untrailingslashit($bpath);
					$rblog = $omatches[2].str_replace($old_home_domain, $new_home_domain, $bdom).untrailingslashit($bpath);
					if (!in_array($sblog, $from_array)) {
						$from_array[] = $sblog;
						$to_array[] = $rblog;
					}
				}
			}
		}

		// The search/replace parameters are allowed to be either strings or arrays
		$report = $updraftplus_restorer->search_replace_obj->icit_srdb_replacer($from_array, $to_array, array($table => $stripped_table), 5000);

		// If we just replaced either the blogs or site table, then populate our records of what is *now* (i.e. post-restore) in them
		if (!empty($try_site_blog_replace)) {
			if ($table == $this->base_prefix.'blogs') {
				$blogs = $wpdb->get_results('SELECT blog_id, domain, path, site_id FROM '.UpdraftPlus_Manipulation_Functions::backquote($this->base_prefix.'blogs'), ARRAY_N);
				if (is_array($blogs)) {
					$nblogs = array();
					foreach ($blogs as $blog) {
						$nblogs[$blog[0]] = array('domain' => $blog[1], 'path' => $blog[2], 'site_id' => $blog[3]);
					}
					$this->restored_blogs = $nblogs;
				}
			} elseif ($table == $this->base_prefix.'site') {
				$sites = $wpdb->get_results('SELECT id, domain, path FROM '.UpdraftPlus_Manipulation_Functions::backquote($this->base_prefix.'site').' ORDER BY id ASC', ARRAY_N);
				if (is_array($sites)) {
					$nsites = array();
					foreach ($sites as $site) {
						$nsites[$site[0]] = array($site[1], $site[2]);
					}
					$this->restored_sites = $nsites;
				}
			}
			if (!empty($this->restored_sites) && !empty($this->restored_blogs) && !empty($this->original_sites)) {
				// Adjust paths
				// Domain, path
				$any_site_changes = false;
				foreach ($this->original_sites as $oid => $osite) {
					if (empty($this->restored_sites[$oid])) continue;
					$rsite = $this->restored_sites[$oid];
					// Task: 1) Replace the site path with the previous site path 2) Replace all the blog path prefixes from the same blog
					if ($rsite[1] != $osite[1]) {
						$any_site_changes = true;
						$sitepath = $osite[1];
						$this->restored_sites[$oid][1] = $sitepath;
						foreach ($this->restored_blogs as $blog_id => $blog) {
							// From this site?
							if ($blog['site_id'] != $oid) continue;
							// Replace the prefix according to the change in prefix for the site
							$this->restored_blogs[$blog_id] = array('domain' => $blog['domain'], 'path' => $sitepath.substr($blog['path'], strlen($rsite[1])), 'site_id' => $oid);
						}
					}
				}
				if ($any_site_changes) {
					$updraftplus->log_e('Adjusting multisite paths');
					foreach ($this->restored_sites as $site_id => $osite) {
						$wpdb->query($wpdb->prepare("UPDATE ".UpdraftPlus_Manipulation_Functions::backquote($this->base_prefix.'site')." SET path='%s' WHERE id=%d", array($osite[1], (int) $site_id)));
					}
					foreach ($this->restored_blogs as $blog_id => $blog) {
						$wpdb->query($wpdb->prepare("UPDATE ".UpdraftPlus_Manipulation_Functions::backquote($this->base_prefix.'blogs')." SET path='%s' WHERE blog_id=%d", array($blog['path'], (int) $blog_id)));
					}
				}
			}
		}

		// Output any errors encountered during the db work.
		if (!empty($report['errors']) && is_array($report['errors'])) {
			$updraftplus->log(__('Error:', 'updraftplus'), 'warning-restore', 'restore-db-error');
			$processed_errors = array();
			foreach ($report['errors'] as $error) {
				if (in_array($error, $processed_errors)) continue;
				$processed_errors[] = $error;
				$num = count(array_keys($report['errors'], $error));
				$err_string = $error;
				if ($num > 1) $err_string .= ' (x'.$num.')';
				$updraftplus->log($err_string, 'warning-restore');
			}
		}

		if (false == $report) {
			$updraftplus->log(sprintf(__('Failed: the %s operation was not able to start.', 'updraftplus'), __('search and replace', 'updraftplus')), 'warning-restore');
		} elseif (!is_array($report)) {
			$updraftplus->log(sprintf(__('Failed: we did not understand the result returned by the %s operation.', 'updraftplus'), __('search and replace', 'updraftplus')), 'warning-restore');
		} else {

			$this->tables_replaced[$table] = true;

			// Calc the time taken.
			foreach (array('tables', 'rows', 'change', 'updates') as $key) {
				$this->report[$key] += $report[$key];
			}
			$this->report['timetaken'] += $report['end'] - $report['start'];
		}

	}
	
	/**
	 * Displays admin notice if .htaccess have any old migrated site reference.
	 */
	public function migration_admin_notices() {
		$updraftplus_migrated_site_domain = get_site_option('updraftplus_migrated_site_domain', false);
		if ($updraftplus_migrated_site_domain) {
			$htaccess_file_path = ABSPATH.'.htaccess';
			$htaccess_file_reference_line_num_arr = array();
			if (file_exists($htaccess_file_path) && is_file($htaccess_file_path)) {
				$current_site_domain = rtrim(str_ireplace(array('http://', 'https://'), '', get_home_url()), '/');
				$htaccess_file_lines = file($htaccess_file_path);
				if (false !== $htaccess_file_lines) {
					foreach ($htaccess_file_lines as $num => $line) {
						$migrated_site_domain_pos = stripos($line, $updraftplus_migrated_site_domain);
						if (false !== $migrated_site_domain_pos && stripos($line, $current_site_domain) !== $migrated_site_domain_pos) {
							$htaccess_file_reference_line_num_arr[] = $num + 1;
						}
					}
				}
			}
			$count_old_site_references = count($htaccess_file_reference_line_num_arr);
			if ($count_old_site_references > 0) {
				?>
				<div class="notice error updraftplus-migration-notice is-dismissible" >					<p>
						<?php
						printf('<strong>'.__('Warning', 'updraftplus').':</strong> '._n('Your .htaccess has an old site reference on line number %s. You should remove it manually.', 'Your .htaccess has an old site references on line numbers %s. You should remove them manually.', $count_old_site_references, 'updraftplus'), implode(', ', $htaccess_file_reference_line_num_arr));
						?>
					</p>
				</div>
				<?php
				add_action('admin_footer', array($this, 'dismiss_notice_for_old_site_references'));
			} else {
				delete_site_option('updraftplus_migrated_site_domain');
			}
		}
	}
		
	/**
	 * Builds from supplied parameters and $this->(siteurl,home,content,uploads,abspath)
	 *
	 * @param String         $old_siteurl - the old site url
	 * @param String         $old_home    - the old home url
	 * @param Boolean|String $old_content - the old content url
	 * @param Boolean|String $old_uploads - the old upload url
	 * @param String         $old_abspath - the old abspath
	 *
	 * @return Array - itself containing two arrays, with corresponding 'search' and 'replace' items.
	 */
	private function build_searchreplace_array($old_siteurl, $old_home, $old_content = false, $old_uploads = false, $old_abspath = '') {
	
		// The uploads parameter, if === false, should be ignored - it is only intended to be used in the special case of single-into-multisite imports (only in that case with $this->uploads get set)
		if (false === $old_content && false === $old_uploads) $old_content = $old_siteurl.'/wp-content';
		$from_array = array();
		$to_array = array();
		
		if (!empty($old_siteurl) && $old_siteurl == $old_home) {
			$from_array[] = $old_home;
			// Used to be site until Sep 2016, but that is wrong. Most likely it was the best possibility before the upload URL was also recorded/known.
			$to_array[] = $this->home;
		} elseif (!empty($old_home) && strpos($old_siteurl, $old_home) === 0) {
			// strpos: haystack, needle - i.e. old_home is a (proper, since they were not ==) substring of old_siteurl
			$from_array[] = $old_siteurl;
			$to_array[] = $this->siteurl;
			$from_array[] = $old_home;
			$to_array[] = $this->home;
			// If the source home URL is also a proper substring of the destination site URL, then this should be skipped
			if ($old_home != $this->siteurl && strpos($this->siteurl, $old_home) === 0) {
				// Not pretty, but the only solution that can cope with content in posts that contains references to both site and home URLs in this case. This extra search URL un-does the adding of an unnecessary duplicate portion to site URLs in the case that is detected here.
				$from_array[] = $this->home.substr($this->home, strlen($old_home));
				$to_array[] = $this->home;
			}
		} elseif (!empty($old_siteurl) && strpos($old_home, $old_siteurl) === 0) {
			// old_siteurl is a substring of old_home (weird!)
			$from_array[] = $old_home;
			$to_array[] = $this->home;
			$from_array[] = $old_siteurl;
			$to_array[] = $this->siteurl;
		} else {
			// neither contains the other
			if (!empty($old_siteurl)) {
				$from_array[] = $old_siteurl;
				$to_array[] = $this->siteurl;
			}
			if (!empty($old_home)) {
				$from_array[] = $old_home;
				$to_array[] = $this->home;
			}
		}
		// We now have a minimal array based on the site_url and home settings
		// The case we need to detect is: (site_url is a prefix of content_url and new_site_url is a prefix of new_content_url and the remains are the same.
		// We do [0] of the existing array, to handle the weird case where old_siteurl is a substring of old_home (i.e. we get the shortest possible match)
		// We will want to do the content URLs first, since they are likely to be longest
		if (empty($old_content) || empty($this->content) || (!empty($from_array) && 0 === strpos($old_content, $from_array[0]) && 0 === strpos($this->content, $to_array[0]) && substr($old_content, strlen($from_array[0])) === substr($this->content, strlen($to_array[0])))) {
			// OK - nothing to do - is already covered
		} else {
			// Search/replace needed
			array_unshift($from_array, $old_content);
			array_unshift($to_array, $this->content);
		}
		if (empty($old_uploads) || empty($this->uploads) || (!empty($from_array) && 0 === strpos($old_uploads, $from_array[0]) && 0 === strpos($this->uploads, $to_array[0]) && substr($old_uploads, strlen($from_array[0])) === substr($this->uploads, strlen($to_array[0])))) {
			// OK - nothing to do - is already covered or no data is present
		} else {
			// Search/replace needed
			array_unshift($from_array, $old_uploads);
			array_unshift($to_array, $this->uploads);
		}

		// Add the opposite http version so that sites with mixed links are caught
		foreach ($from_array as $key => $value) {
			if (0 === stripos($value, 'https://')) {
				$from_array[] = 'http://'.substr($value, 8);
				$to_array[] = $to_array[$key];
			} elseif (0 === stripos($value, 'http://')) {
				$from_array[] = 'https://'.substr($value, 7);
				$to_array[] = $to_array[$key];
			}
		}

		if (rtrim($old_abspath, '/') !== '') {
			$from_array[] = rtrim($old_abspath, '/');
			$to_array[] = rtrim(ABSPATH, '/');
		}
		
		return array($from_array, $to_array);
	}

	public function updraftplus_restored_db($info, $import_table_prefix) {

		global $wpdb, $updraftplus;

		$updraftplus->log('Begin search and replace (updraftplus_restored_db)');
		$updraftplus->log(__('Database: search and replace site URL', 'updraftplus'), 'database-replace-site-url');

		if (empty($this->restore_options['updraft_restorer_replacesiteurl'])) {
			$updraftplus->log_e('This option was not selected.');
			return;
		}

		$replace_this_siteurl = isset($this->old_siteurl) ? $this->old_siteurl : '';

		// Don't call site_url() - the result may/will have been cached
// if (isset($this->new_blogid)) switch_to_blog($this->new_blogid);
// $db_siteurl_thissite = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name='siteurl'")->option_value;
// $db_home_thissite = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name='home'")->option_value;
// if (isset($this->new_blogid)) restore_current_blog();

		// Until 1.12.25, we just used the main options table, which resulted in wrong results when importing a single site into a multisite
		$options_table = empty($this->new_blogid) ? 'options' : $this->new_blogid.'_options';
		
		$db_siteurl_thissite = $wpdb->get_row("SELECT option_value FROM ".UpdraftPlus_Manipulation_Functions::backquote($this->base_prefix.$options_table)." WHERE option_name='siteurl'")->option_value;
		
		$db_home_thissite = $wpdb->get_row("SELECT option_value FROM ".UpdraftPlus_Manipulation_Functions::backquote($this->base_prefix.$options_table)." WHERE option_name='home'")->option_value;

		if (!$replace_this_siteurl) {
			$replace_this_siteurl = $db_siteurl_thissite;
		}

		$replace_this_home = isset($this->old_home) ? $this->old_home : '';
		if (!$replace_this_home) {
			$replace_this_home = $db_home_thissite;
		}

		$replace_this_content = isset($this->old_content) ? $this->old_content : '';
		if (!$replace_this_content) {
			$replace_this_content = $replace_this_siteurl.'/wp-content';
		}
		
		$replace_this_uploads = isset($this->old_uploads) ? $this->old_uploads : false;

		$replace_this_abspath = $this->old_abspath;

		// Sanity checks
		if (empty($replace_this_siteurl)) {
			$updraftplus->log(sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'backup_siteurl', $this->siteurl), 'warning-restore');
			return;
		}
		if (empty($replace_this_home)) {
			$updraftplus->log(sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'backup_home', $this->home), 'warning-restore');
			return;
		}
		if (empty($replace_this_content)) {
			$updraftplus->log(sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'backup_content_url', $this->content), 'warning-restore');
			return;
		}

		if (empty($this->siteurl)) {
			$updraftplus->log(sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'new_siteurl', $replace_this_siteurl), 'warning-restore');
			return;
		}
		if (empty($this->home)) {
			$updraftplus->log(sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'new_home', $replace_this_home), 'warning-restore');
			return;
		}
		// Only complain about the empty content parameter if it's not the case where we use the uploads parameter instead
		if (empty($this->content) && empty($this->uploads)) {
			$updraftplus->log(sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'new_contenturl', $replace_this_content), 'warning-restore');
			return;
		}
		
		// Remove any scheduled backup jobs on any imported-into-multisite site
		if (!empty($this->new_blogid)) {
			switch_to_blog($this->new_blogid);
			wp_clear_scheduled_hook('updraft_backup');
			wp_clear_scheduled_hook('updraft_backup_database');
			wp_clear_scheduled_hook('updraft_backup_increments');
			restore_current_blog();
		}

		if ($replace_this_siteurl == $this->siteurl && $replace_this_home == $this->home && $replace_this_content == $this->content) {
			$this->is_migration = false;
			$updraftplus->log(sprintf(__('Nothing to do: the site URL is already: %s', 'updraftplus'), $this->siteurl), 'notice-restore');
			return;
		}

		$this->is_migration = true;

		do_action('updraftplus_restored_db_is_migration');
		
		// Detect situation where the database's siteurl in the header differs from that actual row data in the options table. This can occur if the options table was being over-ridden by a constant. In that case, the search/replace will have failed to set the option table's siteurl; and the result will be that that siteurl is hence wrong, leading to site breakage. The solution is to re-set it.
		// $info['expected_oldsiteurl'] is from the db.gz file header
		if (isset($info['expected_oldsiteurl']) && $info['expected_oldsiteurl'] != $db_siteurl_thissite && $db_siteurl_thissite != $this->siteurl) {
				 $updraftplus->log_e(sprintf(__("Warning: the database's site URL (%s) is different to what we expected (%s)", 'updraftplus'), $db_siteurl_thissite, $info['expected_oldsiteurl']));
			// Here, we change only the site URL entry; we don't run a full search/replace based on it. In theory, if someone developed using two different URLs, then this might be needed.
			if (!empty($this->base_prefix) && !empty($this->siteurl)) {
				$wpdb->query($wpdb->prepare("UPDATE ".UpdraftPlus_Manipulation_Functions::backquote($this->base_prefix.$options_table)." SET option_value='%s' WHERE option_name='siteurl'", array($this->siteurl)));
			}
		}
		
		if (isset($info['expected_oldhome']) && $info['expected_oldhome'] != $db_home_thissite && $db_home_thissite != $this->home) {
			$updraftplus->log_e(sprintf(__("Warning: the database's home URL (%s) is different to what we expected (%s)", 'updraftplus'), $db_home_thissite, $info['expected_oldhome']));
			if (!empty($this->base_prefix) && !empty($this->home)) {
				$wpdb->query($wpdb->prepare("UPDATE ".UpdraftPlus_Manipulation_Functions::backquote($this->base_prefix.$options_table)." SET option_value='%s' WHERE option_name='home'", array($this->home)));
			}
		}

		if (function_exists('set_time_limit')) @set_time_limit(1800);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		list($from_array, $to_array) = $this->build_searchreplace_array($replace_this_siteurl, $replace_this_home, $replace_this_content, $replace_this_uploads, $replace_this_abspath);

		foreach ($from_array as $ind => $from_url) {
			$updraftplus->log_e('Database search and replace: replace %s in backup dump with %s', $from_url, $to_array[$ind]);
		}

		return $this->updraftplus_restored_db_dosearchreplace($from_array, $to_array, $import_table_prefix);
	}

	private function updraftplus_restored_db_dosearchreplace($from_array, $to_array, $import_table_prefix, $examine_siteurls = true) {

		global $updraftplus, $wpdb, $updraftplus_restorer;

		// Now, get an array of tables and then send it off to $updraftplus_restorer->search_replace_obj->icit_srdb_replacer()
		// Code modified from searchreplacedb2.php version 2.1.0 from http://www.davidcoveney.com

		// Do we have any tables and if so build the all tables array
		$tables = array();

		// We use $wpdb for non-performance-sensitive operations (e.g. one-time calls)
		$tables_mysql = $wpdb->get_results('SHOW TABLES', ARRAY_N);

		$is_multisite = is_multisite();
		if ($examine_siteurls && $is_multisite && empty($this->new_blogid)) {
		
			$sites = $wpdb->get_results('SELECT id, domain, path FROM '.UpdraftPlus_Manipulation_Functions::backquote($import_table_prefix.'site').' ORDER BY id ASC', ARRAY_N);
			$nsites = array();
			foreach ($sites as $site) {
				$nsites[$site[0]] = array('dom' => $site[1], 'path' => $site[2]);
			}
		
			$blogs = $wpdb->get_results('SELECT blog_id, domain, path, site_id FROM '.UpdraftPlus_Manipulation_Functions::backquote($import_table_prefix.'blogs').' ORDER BY blog_id ASC', ARRAY_N);
			$nblogs = array();
			foreach ($blogs as $blog) {
				$nblogs[$blog[0]] = array('dom' => $blog[1], 'path' => $blog[2], 'site_id' => $blog[3]);
			}
		}

		if (!$tables_mysql) {
			$updraftplus->log(__('Error:', 'updraftplus').' '.__('Could not get list of tables', 'updraftplus'), 'warning-restore');
			$updraftplus->log('Could not get list of tables');
			$updraftplus_restorer->search_replace_obj->print_error('SHOW TABLES');
			return false;
		} else {
			// Run through the array - each element a numerically-indexed array
			
			$multisite_processed_sites = array();
			
			foreach ($tables_mysql as $table) {

				// Type equality is necessary, as we don't want to match false
				// "Warning: strpos(): Empty delimiter" means that the second parameter is a zero-length string
				if (0 === strpos($table[0], $import_table_prefix)) {
					$tablename = $table[0];

					$stripped_table = substr($tablename, strlen($import_table_prefix));
					// Remove multisite site number prefix, if relevant
					if (is_multisite() && preg_match('/^(\d+)_(.*)$/', $stripped_table, $matches)) $stripped_table = $matches[2];

					if (!empty($this->which_tables) && is_array($this->which_tables)) {
						if (!in_array($tablename, $this->which_tables)) {
							$updraftplus->log(sprintf(__('Search and replacing table:', 'updraftplus')).$tablename.': '.__('skipped (not in list)', 'updraftplus'), 'notice-restore', 'restore-skipped-'.$tablename);
							continue;
						}
					}

					$still_needs_doing = empty($this->tables_replaced[$tablename]);

					// Looking for site tables on multisite
					if ($examine_siteurls && $is_multisite && !empty($this->restored_blogs) && preg_match('/^(\d+)_(.*)$/', substr($tablename, strlen($import_table_prefix)), $tmatches) && is_numeric($tmatches[1]) && !empty($this->restored_blogs[$tmatches[1]]) && !empty($nblogs[$tmatches[1]]) && (preg_match('#^((https?://)([^/]+))#i', $this->home, $matches) || preg_match('#^((https?://)([^/]+))#i', $this->siteurl, $matches))) {
						// If the database file was not created by UD, then it may be out of order. Specifically, the 'blogs' table might have come *after* the tables for the individual sites. As a result, the tables for those sites may not have been fully searched + replaced... so we need to check that.
						// What are we expecting the site_url to be?
						$blog_id = $tmatches[1];
						if (empty($multisite_processed_sites[$blog_id])) {
							$multisite_processed_sites[$blog_id] = true;
							$site_url_current = $wpdb->get_var("SELECT option_value FROM ".UpdraftPlus_Manipulation_Functions::backquote($import_table_prefix.$blog_id)."_options WHERE option_name='siteurl'");
							if (is_string($site_url_current)) {
								$bpath = $this->restored_blogs[$blog_id]['path'];
								// Jan 2016: This line is old, and removes the main site's path, if present, from the front of this site's path - but why? I suspect it was so that images could be referenced directly without help from .htaccess - perhaps from when media used to be differently organised?
								// $bpathroot = $this->restored_blogs[1]['path'];
								// if (substr($bpath, 0, strlen($bpathroot)) == $bpathroot) $bpath = substr($bpath, strlen($bpathroot)-1);

								$proto = $matches[2];
								
								$site_url_target = $proto.$nblogs[$blog_id]['dom'].untrailingslashit($bpath);
								if ($site_url_target != $site_url_current) {
									$updraftplus->log("Site url ($site_url_current) for this blog (blog_id=$blog_id) did not match the expected value ($site_url_target) - replacing");
									$multisite_processed_sites[$blog_id] = 1;
									$still_needs_doing = true;
									$from_array[] = $site_url_current;
									$to_array[] = $site_url_target;
								}
							}
						} elseif (!$still_needs_doing && 1 === $multisite_processed_sites[$blog_id]) {
							$still_needs_doing = true;
						}
					}

					if ($still_needs_doing) {
						$tables[$tablename] = $stripped_table;
					} else {
						$updraftplus->log(sprintf(__('Search and replacing table:', 'updraftplus')).' '.$tablename.': '.__('already done', 'updraftplus'), 'notice-restore', 'restore-table-already-done-'.$tablename);
						$updraftplus->log('Search and replacing table: '.$tablename.': already done');
					}
				}
			}
		}

		$final_report = $this->report;

		if (!empty($tables)) {

			$report = $updraftplus_restorer->search_replace_obj->icit_srdb_replacer($from_array, $to_array, $tables, $this->page_size);

			// Output any errors encountered during the db work.
			if (!empty($report['errors']) && is_array($report['errors'])) {
			
				$updraftplus->log(__('Error:', 'updraftplus'), 'warning-restore', 'db-replace-error');

				$processed_errors = array();
				foreach ($report['errors'] as $error) {
					if (in_array($error, $processed_errors)) continue;
					$processed_errors[] = $error;
					$num = count(array_keys($report['errors'], $error));
					$error_msg = $error;
					if ($num > 1) $error_msg .= ' (x'.$num.')';
					$updraftplus->log($error_msg, 'warning-restore');
				}
			}

			if (false == $report) {
				$updraftplus->log(sprintf(__('Failed: the %s operation was not able to start.', 'updraftplus'), 'search and replace'), 'warning-notice');
			} elseif (!is_array($report)) {
				$updraftplus->log(sprintf(__('Failed: we did not understand the result returned by the %s operation.', 'updraftplus'), 'search and replace'), 'warning-notice');
			}

			// Calc the time taken.
			foreach (array('tables', 'rows', 'change', 'updates') as $key) {
				$final_report[$key] += $report[$key];
			}
			$final_report['timetaken'] += $report['end'] - $report['start'];
			foreach ($report['errors'] as $error) {
				$final_report['errors'][] = $error;
			}

		}

		$updraftplus->log(__('Tables examined:', 'updraftplus').' '.$final_report['tables'], 'notice-restore', 'restore-tables-examined');
		$updraftplus->log(__('Rows examined:', 'updraftplus').' '.$final_report['rows'], 'notice-restore', 'restore-rows-examined');
		$updraftplus->log(__('Changes made:', 'updraftplus').' '.$final_report['change'], 'notice-restore', 'restore-changes-made');
		$updraftplus->log(__('SQL update commands run:', 'updraftplus').' '.$final_report['updates'], 'notice-restore', 'restore-sql-commands-run');
		$updraftplus->log(__('Errors:', 'updraftplus').' '. count($final_report['errors']), 'notice-restore', 'restore-tables-errors');
		$updraftplus->log(__('Time taken (seconds):', 'updraftplus').' '.round($final_report['timetaken'], 3), 'notice-restore', 'restore-tables-time-taken');
		
		// Here, We are saving migrated site url for scanning .htaccess file for migrated site url. if migrated site url exist in .htaccess file, plugin should prompt alert message for it. This site option stored if and if only Migrator addon is exist. It requires to add after search and replace.
		if (!empty($this->old_siteurl)) update_site_option('updraftplus_migrated_site_domain', rtrim(str_ireplace(array('http://', 'https://'), '', $this->old_siteurl), '/'));
	}

	/**
	 * Add js for dismiss migration old site references notice
	 *
	 * @return void
	 */
	public function dismiss_notice_for_old_site_references() {
		global $pagenow;
		if (UpdraftPlus_Options::admin_page() != $pagenow || empty($_REQUEST['page']) || 'updraftplus' != $_REQUEST['page']) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $pagenow is undefined
			$GLOBALS['updraftplus_admin']->admin_enqueue_scripts();
			?>
			<script>
			var updraft_credentialtest_nonce='<?php echo wp_create_nonce('updraftplus-credentialtest-nonce');?>';
			</script>		
		<?php
		}
		?>
		<script>
		jQuery(function($) {
			$('.updraftplus-migration-notice').on('click', '.notice-dismiss', function() {
				updraft_send_command('dismiss_migration_notice_for_old_site_reference');
			});
		});
		</script>
		<?php
	}
}

if (!class_exists('UpdraftPlus_Addons_Migrator_RemoteSend_UI')) require_once(UPDRAFTPLUS_DIR.'/includes/class-remote-send.php');
if (!class_exists('UpdraftPlus_Addons_Migrator_RemoteSend')) {
	class UpdraftPlus_Addons_Migrator_RemoteSend extends UpdraftPlus_RemoteSend {

		public function __construct() {
			parent::__construct();
			add_action('updraft_migrate_after_widget', array($this, 'updraft_migrate_after_widget'));
			add_action('admin_footer', array($this, 'admin_footer'));
		}

		public function updraft_migrate_after_widget() {
			?>
			<div class="updraft_migrate_widget_module_content">
				<header>
					<button class="button button-link close"><span class="dashicons dashicons-arrow-left-alt2"></span><?php _e('back', 'updraftplus'); ?></button>
					<h3><span class="dashicons dashicons-upload"></span><?php _e('Send a backup to another site', 'updraftplus');?></h3>
				</header>
				<div id="updraft_migrate_receivingsites" style="clear:both; margin-top:10px;">
					<?php echo $this->get_remotesites_selector();?>
				</div>
				<div class="updraft_migrate_add_site" style="display: none;">
					<p>
						<?php
						echo __("To add a site as a destination for sending to, enter that site's key below.", 'updraftplus').' <a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'" onclick="alert(\''.esc_js(__('Keys for a site are created in the section "receive a backup from a remote site".', 'updraftplus').' '.__("So, to get the key for the remote site, open the 'Migrate Site' window on that site, and go to that section.", 'updraftplus')).'\'); return false;">'.__("How do I get a site's key?", 'updraftplus').'</a>';
						?>
					</p>
					<div class="input-field">
						<label><?php _e('Site key', 'updraftplus'); ?></label> <input type="text" id="updraft_migrate_receiving_new" placeholder="<?php esc_attr(__('Paste key here', 'updraftplus'));?>"> <button class="button-primary" id="updraft_migrate_receiving_makenew" onclick="updraft_migrate_receiving_makenew();"><?php _e('Add site', 'updraftplus');?></button>
					</div>
				</div>
			</div>

			<div class="updraft_migrate_widget_module_content">
				<header>
					<button class="button button-link close"><span class="dashicons dashicons-arrow-left-alt2"></span><?php _e('back', 'updraftplus'); ?></button>
					<h3><span class="dashicons dashicons-download"></span><?php _e('Receive a backup from a remote site', 'updraftplus');?></h3>
				</header>
				<p><?php echo htmlspecialchars(__("To allow another site to send a backup to this site, create a key below. When you are shown the key, then press the 'Migrate' button on the other (sending) site, and copy-and-paste the key over there (in the 'Send a backup to another site' section).", 'updraftplus')); ?></p>
				<p>
					<?php _e('Create a key: give this key a unique name (e.g. indicate the site it is for), then press "Create key":', 'updraftplus'); ?><br>
					<input id="updraft_migrate_receivingsites_keyname" type="text" placeholder="<?php _e('Enter your chosen name', 'updraftplus');?>" value="<?php echo __('Key', 'updraftplus').' - '.date('Y-m-d');?>">
					
					<?php _e('Encryption key size:', 'updraftplus');?>
					<select id="updraft_migrate_receivingsites_keysize">
						<option value="1024"><?php printf(__('%s bits', 'updraftplus').' - '.__('faster (possibility for slow PHP installs)', 'updraftplus'), '1024');?></option>
						<option value="2048" selected="selected"><?php printf(__('%s bytes', 'updraftplus').' - '.__('recommended', 'updraftplus'), '2048');?></option>
						<option value="4096"><?php printf(__('%s bits', 'updraftplus').' - '.__('slower, strongest', 'updraftplus'), '4096');?></option>
					</select>
					
					<button id="updraft_migrate_receivingsites_createkey" class="button button-primary" onclick="updraft_migrate_receivingsites_createkey();"><?php _e('Create key', 'updraftplus');?></button>
					
				</p>

				<div id="updraft_migrate_new_key_container" style="display:none;">
					<?php _e('Your new key:', 'updraftplus'); ?><br>
					<textarea id="updraft_migrate_new_key" onclick="this.select();" style="width:625px; height:235px; word-wrap:break-word; border: 1px solid #aaa; border-radius: 3px; padding:4px;"></textarea>
				</div>

				<div id="updraft_migrate_our_keys_container">
					<?php echo $this->list_our_keys(); ?>
				</div>
			</div>

			<?php

		}

		/**
		 * Runs upon the WP action admin_footer. If on a relevant page, we output some JavaScript.
		 */
		public function admin_footer() {
			global $updraftplus, $pagenow;
			// Next, the actions that only come on the UpdraftPlus page
			if (UpdraftPlus_Options::admin_page() != $pagenow || empty($_REQUEST['page']) || 'updraftplus' != $_REQUEST['page']) return;

			?>
			<script>

				function updraft_migrate_receivingsites_createkey() {
					
					var $ = jQuery;
					
					// Remember to tell them that this key will never be shown again
					
					var key_name = $('#updraft_migrate_receivingsites_keyname').val();
					var key_size = $('#updraft_migrate_receivingsites_keysize').val();

					if ('' == key_name || false == key_name || null == key_name) {
						alert(updraftlion.nokeynamegiven);
						return false;
					}

					$('#updraft_migrate_new_key_container').show();
					$('#updraft_migrate_new_key').html(updraftlion.creating_please_allow);

					var data = {
						subsubaction: 'updraft_migrate_key_create',
						name: key_name,
						size: key_size
					}
					
					updraft_send_command('doaction', data, function(resp) {
						if (resp.hasOwnProperty('bundle')) {
							$('#updraft_migrate_receivingsites_keyname').val('');
							$('#updraft_migrate_new_key').html(resp.bundle);
							if (resp.hasOwnProperty('selector')) {
								$('#updraft_migrate_receivingsites').html(resp.selector);
							}
							if (resp.hasOwnProperty('r')) {
								alert(resp.r);
							}
							if (resp.hasOwnProperty('ourkeys')) {
								$('#updraft_migrate_our_keys_container').html(resp.ourkeys);
							}
						} else if (resp.hasOwnProperty('e')) {
							$('#updraft_migrate_new_key').html(resp.r);
							console.log(resp);
						} else {
							alert(updraftlion.servererrorcode);
							console.log(resp);
							console.log(response);
							$('#updraft_migrate_new_key_container').hide();
						}
					}, { error_callback: function(response, status, error_code, resp) {
							var msg = '';
							if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
								console.error(resp.fatal_error_message);
								msg = resp.fatal_error_message;
							}
							if (response.hasOwnProperty('responseText') && response.responseText) {
								msg = response.responseText;
								if (response.hasOwnProperty('statusText') && response.statusText) {
									msg += ' ('+response.statusText+')';
								}
							} else if (response.hasOwnProperty('statusText') && response.statusText) {
								msg = response.statusText;
							}
							$('#updraft_migrate_new_key').html(updraftlion.error+' '+msg);
							alert(updraftlion.error+' '+msg);
							console.log(response);
							console.log(status);
						}
					});
					
					// Update (via AJAX) the list of existing keys
					// AJAX command to delete an existing key
				};
				
				function updraft_migrate_local_key_delete(keyid) {
				
					var $ = jQuery;
				
					var $keylink = $('a.updraft_migrate_local_key_delete[data-keyid="'+keyid+']');
					
					var data = {
						subsubaction: 'updraft_migrate_key_delete',
						keyid: keyid,
					}
					
					$keylink.html(updraftlion.deleting);
					
					updraft_send_command('doaction', data, function(resp) {
							if (resp.hasOwnProperty('ourkeys')) {
								$('#updraft_migrate_our_keys_container').html(resp.ourkeys);
							} else {
								alert(updraftlion.unexpectedresponse+' '+response);
								console.log(resp);
								console.log(response);
							}
					}, { error_callback: function(response, status, error_code, resp) {
							if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
								console.error(resp.fatal_error_message);
								alert(resp.fatal_error_message);
							} else {
								var error_message = "updraft_send_command: error: "+status+" ("+error_code+")";
								console.log(error_message);
								alert(error_message);
							console.log(response);
							}
						}
					});
				}

				function updraft_migrate_send_backup_options() {

					var $ = jQuery;

					var site_url = $('#updraft_remotesites_selector option:selected').text();

					$('#updraft_migrate .updraft_migrate_widget_module_content, .updraft_migrate_intro').hide();
					$('#updraft_migrate_tab_alt').html('<header><button class="button button-link close"><span class="dashicons dashicons-arrow-left-alt2"></span>'+updraftlion.back+'</button><h3><span class="dashicons dashicons-download"></span>'+updraftlion.send_to_another_site+'</h3></header><p><strong>'+updraftlion.sendtosite+'</strong> '+site_url+'</p><p>'+updraftlion.remote_send_backup_info+'</p><button class="button button-primary" id="updraft_migrate_send_existing_button" onclick="updraft_migrate_send_existing_backup();">'+updraftlion.send_existing_backup+'</button><button class="button button-primary" id="updraft_migrate_send_new_button" onclick="updraft_migrate_send_backup();">'+updraftlion.send_new_backup+'</button>').slideDown('fast');

				}

				function updraft_migrate_send_existing_backup() {
					
					var $ = jQuery;

					var site_url = $('#updraft_remotesites_selector option:selected').text();
					$('#updraft_migrate .updraft_migrate_widget_module_content, .updraft_migrate_intro').hide();
					$('#updraft_migrate_tab_alt').html('<header><button class="button button-link close"><span class="dashicons dashicons-arrow-left-alt2"></span>'+updraftlion.back+'</button><h3><span class="dashicons dashicons-download"></span>'+updraftlion.send_to_another_site+'</h3></header><p><strong>'+updraftlion.sendtosite+'</strong> '+site_url+'</p><p>'+updraftlion.remote_send_backup_info+'</p><p id="updraft_migrate_findingbackupsprogress">'+updraftlion.scanning_backups+'</p>').slideDown('fast');

					updraft_send_command('get_backup_list', {}, function(resp, status, response) {
						$('#updraft_migrate_findingbackupsprogress').replaceWith('');
						$('#updraft_migrate_tab_alt').append(resp.data);
					}, { error_callback: function(response, status, error_code, resp) {
							if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
								$('#updraft_migrate_tab_alt').append('<p style="color:red;">'+resp.fatal_error_message+'</p>');
								console.error(resp.fatal_error_message);
							} else {
								$('#updraft_migrate_tab_alt').append('<p style="color:red;">'+updraftlion.unexpectedresponse+' '+response+'</p>');
								console.log(err);
								console.log(response);
							}
						}
					});

				}
				
				function updraft_migrate_send_backup() {

					var $ = jQuery;

					$('#updraft_migrate .updraft_migrate_widget_module_content, .updraft_migrate_intro').hide();
					var site_id = $('#updraft_remotesites_selector').val();
					var site_url = $('#updraft_remotesites_selector option:selected').text();
					$('#updraft_migrate_tab_alt').html('<header><button class="button button-link close"><span class="dashicons dashicons-arrow-left-alt2"></span>'+updraftlion.back+'</button><h3><span class="dashicons dashicons-download"></span>'+updraftlion.send_to_another_site+'</h3></header><p><strong>'+updraftlion.sendtosite+'</strong> '+site_url+'</p><p id="updraft_migrate_testinginprogress">'+updraftlion.testingconnection+'</p>').slideDown('fast');

					var data = {
						subsubaction: 'updraft_remote_ping_test',
						id: site_id,
						url: site_url
					};
					updraft_send_command('doaction', data, function(resp, status, response) {
						try {
							if (resp.hasOwnProperty('e')) {
								console.log(resp);
								$('#updraft_migrate_tab_alt').append('<p style="color:red;">'+updraftlion.unexpectedresponse+' '+resp.r+' ('+resp.code+'). '+updraftlion.checkrpcsetup+'</p>');
								if (resp.hasOwnProperty('moreinfo')) {
									$('#updraft_migrate_tab_alt').append(resp.moreinfo);
								}
								// alert(updraftlion.unexpectedresponse+' '+resp.r+' ('+resp.code+'). '+updraftlion.checkrpcsetup);
							} else if (resp.hasOwnProperty('success')) {
								if (resp.hasOwnProperty('r')) {
									$('#updraft_migrate_testinginprogress').replaceWith('<p style="">'+resp.r+'</p>');
								}
							}
						} catch(err) {
							$('#updraft_migrate_tab_alt').append('<p style="color:red;">'+updraftlion.unexpectedresponse+' '+response+'</p>');
							console.log(err);
							console.log(response);
							return;
						}
					}, { error_callback: function(response, status, error_code, resp) {
							if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
								$('#updraft_migrate_tab_alt').append('<p style="color:red;">'+resp.fatal_error_message+'</p>');
								console.error(resp.fatal_error_message);
							} else {
								$('#updraft_migrate_tab_alt').append('<p style="color:red;">'+updraftlion.unexpectedresponse+' '+response+'</p>');
								console.log(err);
								console.log(response);
							}
						}
					});
				}
				
				function updraft_migrate_go_existing_backup() {
					var $ = jQuery;

					var site_id = $('#updraft_remotesites_selector').val();
					var backup_select = $('#updraft_migrate_tab_alt #updraftplus_remote_send_backup_options').find('option:selected');
					var nonce = backup_select.data('nonce');
					var timestamp = backup_select.data('timestamp');
					var services = 'remotesend';
					var extradata = {
						services: 'remotesend/'+site_id
					};

					updraft_send_command('upload_local_backup', {
						use_nonce: nonce,
						use_timestamp: timestamp,
						services: services,
						extradata: extradata
					}, function (response) {
						jQuery('#updraft-navtab-backups').trigger('click');
						alert(updraftlion.local_upload_started);
					});
				}

				/**
				 * Migrate send a backup
				 */
				function updraft_migrate_go_backup() {
					var site_id = jQuery('#updraft_remotesites_selector').val();
					var entities = [ '<?php
						$entities = $updraftplus->get_backupable_file_entities();
						echo implode("', '", array_keys($entities));
						?>' ];
					var onlythisfileentity = ''; 
					var arrayLength = entities.length; 
					for (var i = 0; i < arrayLength; i++) { 
					  if (jQuery('#remotesend_updraft_include_'+entities[i]).is(':checked')) { 
						if (onlythisfileentity != '') { onlythisfileentity += ','; } 
						onlythisfileentity += entities[i]; 
					  } 
					  //Do something 
					} 
				
					var backupnow_nodb = jQuery('#remotesend_backupnow_db').is(':checked') ? 0 : 1; 
				
					var backupnow_nofiles = 0; 
					if ('' == onlythisfileentity) { backupnow_nofiles = 1; } 
				
					var backupnow_nocloud = 1; 
					var extradata = {
						services: 'remotesend/'+site_id
					};
				
					if (jQuery('#remotesend_backupnow_cloud').is(':checked')) { 
					  backupnow_nocloud = 0; 
					} 
				
					if (backupnow_nodb && backupnow_nofiles) { 
					  alert(updraftlion.excludedeverything); 
					  return; 
					} 
				
					setTimeout(function() { 
					  jQuery('#updraft_lastlogmessagerow').fadeOut('slow', function() { 
						jQuery(this).fadeIn('slow'); 
					  }); 
					}, 1700); 
					 
					updraft_backupnow_go(backupnow_nodb, backupnow_nofiles, backupnow_nocloud, onlythisfileentity, extradata, jQuery('#remotesend_backupnow_label').val(), '');
					jQuery('#updraft-navtab-backups').trigger('click');
				}
			
				function updraft_migrate_receiving_makenew() {
				
					var $ = jQuery;
				
					var data = {
						subsubaction: 'updraft_migrate_newdestination',
						key: $('#updraft_migrate_receiving_new').val()
					}
					$('#updraft_migrate_receiving_makenew').html(updraftlion.addingsite);
					updraft_send_command('doaction', data, function(resp, status, response) {
						$('#updraft_migrate_receiving_makenew').html(updraftlion.addsite);
						
						if (resp.hasOwnProperty('e')) {
							console.log(resp);
							alert(resp.e);
						} else if (resp.hasOwnProperty('r')) {
							if (resp.hasOwnProperty('selector')) {
								$('#updraft_migrate_receivingsites').html(resp.selector);
							}
							$('#updraft_migrate_receiving_new').val('');
							alert(resp.r);
						} else {
							alert(updraftlion.unexpectedresponse+' '+response);
							console.log(resp);
							console.log(response);
						}					
					}, { error_callback: function(response, status, error_code, resp) {
							$('#updraft_migrate_receiving_makenew').html(updraftlion.addsite);
							if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
								console.error(resp.fatal_error_message);
								alert(resp.fatal_error_message);
							} else {
								var error_message = "updraft_send_command: error: "+status+" ("+error_code+")";
								console.log(error_message);
								alert(error_message);
								console.log(response);
							}					
						}
					});
				}

				function updraft_migrate_delete_existingsites(confirmation_message) {

					if (confirm(confirmation_message)) {

						var $ = jQuery;

						var data = {
							subsubaction: 'updraft_migrate_delete_existingsites'
						}
						
						updraft_send_command('doaction', data, function(resp) {
							if (resp.hasOwnProperty('success')) {
								alert(resp.success);
							}
							if (resp.hasOwnProperty('html')) {
								$('#updraft_migrate_receivingsites').html(resp.html);
							}
						}, { error_callback: function(response, status, error_code, resp) {
							var msg = '';
							if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
								console.error(resp.fatal_error_message);
								msg = resp.fatal_error_message;
							}
							if (response.hasOwnProperty('responseText') && response.responseText) {
								msg = response.responseText;
								if (response.hasOwnProperty('statusText') && response.statusText) {
									msg += ' ('+response.statusText+')';
								}
							} else if (response.hasOwnProperty('statusText') && response.statusText) {
								msg = response.statusText;
							}
							alert(msg);
							console.log(response);
							console.log(status);
							}
						});
					}

				}
			</script>
			<?php
		}
	}
}

new UpdraftPlus_Addons_Migrator_RemoteSend();
