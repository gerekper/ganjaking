<?php
if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');


// Search/replace code adapted in according with the licence from https://github.com/interconnectit/Search-Replace-DB

// phpcs:ignore
class UpdraftPlus_Migrator_Lite {

	private $is_migration;

	private $restored_blogs = false;

	private $restored_sites = false;

	private $wpdb_obj = false;

	protected $restore_options = array();

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
		add_action('updraftplus_debugtools_dashboard', array($this, 'debugtools_dashboard'), 30);
		add_action('updraftplus_adminaction_searchreplace', array($this, 'adminaction_searchreplace'));
		add_action('updraftplus_creating_table', array($this, 'updraftplus_creating_table'), 10, 1);
		// Displaying notices after migration if migrated url exists in .htaccess file
		add_action('all_admin_notices', array($this, 'migration_admin_notices'));
		 
		add_filter('updraftplus_dbscan_urlchange', array($this, 'dbscan_urlchange'), 10, 3);
		add_filter('updraftplus_https_to_http_additional_warning', array($this, 'https_to_http_additional_warning'), 10, 1);
		add_filter('updraftplus_http_to_https_additional_warning', array($this, 'http_to_https_additional_warning'), 10, 1);
		add_filter('updraftplus_dbscan_urlchange_www_append_warning', array($this, 'dbscan_urlchange_www_append_warning'), 10, 1);
		
		add_filter('updraftplus_restorecachefiles', array($this, 'restorecachefiles'), 10, 2);
		add_filter('updraftplus_restored_plugins', array($this, 'restored_plugins'));

		// Both MU and normal site
		// Actions/filters that need UD to be fully loaded before we can consider adding them
		add_action('plugins_loaded', array($this, 'lite_plugins_loaded'));
	}
	
	public function lite_plugins_loaded() {
		global $updraftplus;

		// We don't support restoring single sites into multisite until WP 3.5
		// Some (significantly out-dated) information on what import-into-multisite involves: http://iandunn.name/comprehensive-wordpress-multisite-migrations/
		if (is_a($updraftplus, 'UpdraftPlus') && method_exists($updraftplus, 'get_wordpress_version') && version_compare($updraftplus->get_wordpress_version(), '3.5', '>=')) {
			add_filter('updraftplus_restore_all_downloaded_postscan', array($this, 'lite_restore_all_downloaded_postscan'), 20, 7);
			// Both MU and normal site
			add_action('updraftplus_restorer_restore_options', array($this, 'restorer_restore_options'));
		}
	}

	public function restorer_restore_options($restore_options) {
		$this->restore_options = $restore_options;
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
			updraft_try_include_file('restorer.php', 'include_once');
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
	public function dbscan_urlchange($output, $old_siteurl, $restore_options) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		return sprintf(__('This looks like a migration (the backup is from a site with a different address/URL, %s).', 'updraftplus'), htmlspecialchars($old_siteurl));
	}
	
	/**
	 * WordPress filter updraftplus_https_to_http_additional_warning
	 *
	 * @param String $output - Filter input
	 *
	 * @return String - filtered
	 */
	public function https_to_http_additional_warning($output) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		return ' '.__('This restoration will work if you still have an SSL certificate (i.e. can use https) to access the site.', 'updraftplus').' '.__('Otherwise, you will want to use below search and replace to search/replace the site address so that the site can be visited without https.', 'updraftplus');
	}
	
	/**
	 * WordPress filter updraftplus_http_to_https_additional_warning
	 *
	 * @param String $output - Filter input
	 *
	 * @return String - filtered
	 */
	public function http_to_https_additional_warning($output) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		return ' '.__('As long as your web hosting allows http (i.e. non-SSL access) or will forward requests to https (which is almost always the case), this is no problem.', 'updraftplus').' '.__('If that is not yet set up, then you should set it up, or use below search and replace so that the non-https links are automatically replaced.', 'updraftplus');
	}
	
	/**
	 * WordPress filter updraftplus_dbscan_urlchange_www_append_warning
	 *
	 * @param String $output - the unfiltered output (free plugin gives empty string)
	 *
	 * @return String - filtered
	 */
	public function dbscan_urlchange_www_append_warning($output) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		return __('you will want to use below search and replace site location in the database (migrate) to search/replace the site address.', 'updraftplus');
	}
		
	public function restored_plugins_one($plugin) {
		global $updraftplus;
		$updraftplus->log(__('Processed plugin:', 'updraftplus').' '.$plugin, 'notice-restore');
		$updraftplus->log("Processed plugin: $plugin");
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
	public function lite_restore_all_downloaded_postscan($backups, $timestamp, $elements, &$info, &$mess, &$warn, &$err) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		if (is_array($info) && isset($info['migration']) && true === $info['migration']) {
			if (empty($info['addui'])) $info['addui'] = '';
			$info['addui'] .= '<div id="updraft_restorer_dboptions" class="notice before-h2 updraft-restore-option updraft-hidden">';
			$info['addui'] .= '<h4>' . __('Database restoration options:', 'updraftplus') . '</h4>';
			$info['addui'] .= '<input name="updraft_restorer_replacesiteurl" id="updraft_restorer_replacesiteurl" type="checkbox" value="1" checked><label for="updraft_restorer_replacesiteurl" title="'.sprintf(__('All references to the site location in the database will be replaced with your current site URL, which is: %s', 'updraftplus'), htmlspecialchars(untrailingslashit(site_url()))).'"> '.__('Search and replace site location in the database (migrate)', 'updraftplus').'</label>';
			$info['addui'] .= '</div>';
		}
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
		if (function_exists('set_time_limit')) @set_time_limit(1800);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- Silenced to suppress errors that may arise because of the function.

		$stripped_table = substr($table, strlen($import_table_prefix));
		// Remove multisite site number prefix, if relevant
		if (is_multisite() && preg_match('/^(\d+)_(.*)$/', $stripped_table, $matches)) $stripped_table = $matches[2];

		// This array is for tables that a) we know don't need URL search/replacing and b) are likely to be sufficiently big that they could significantly delay the progress of the migrate (and increase the risk of timeouts on hosts that enforce them)
		// The term_relationships table contains 3 columns, all integers. Therefore, we can skip it. It can easily get big, so this is a good time-saver.
		$skip_tables = array('slim_stats', 'statpress', 'term_relationships', 'icl_languages_translations', 'icl_string_positions', 'icl_string_translations', 'icl_strings', 'redirection_logs', 'Counterize', 'Counterize_UserAgents', 'Counterize_Referers', 'adrotate_stats', 'login_security_solution_fail', 'wfHits', 'wfhits', 'wbz404_logs', 'wbz404_redirects', 'wfFileMods', 'wffilemods', 'tts_trafficstats', 'tts_referrer_stats', 'dmsguestbook', 'relevanssi', 'wponlinebackup_generations', 'svisitor_stat', 'simple_feed_stats', 'itsec_log', 'rp_tags', 'woocommerce_order_items', 'relevanssi_log', 'blc_instances', 'wysija_email_user_stat', 'woocommerce_sessions', 'et_bloom_stats', 'redirection_404', 'lbakut_activity_log', 'stream_meta', 'wfBlockedIPLog', 'wfblockediplog', 'page_visit_history', 'strack_st', 'eum_logs');

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

		$schemes = array('http', 'https');
		$prefixes = array('www.', '');
		// Add the opposite http version so that sites with mixed links are caught
		foreach ($from_array as $key => $value) {
			if (preg_match('#^https?://(?:www\.)?(.+)#i', $value, $matches)) {
				foreach ($schemes as $scheme) {
					foreach ($prefixes as $prefix) {
						if (!in_array($scheme."://".$prefix.$matches[1], $from_array)) {
							$from_array[] = $scheme."://".$prefix.$matches[1];
							$to_array[] = $to_array[$key];
						}
					}
				}
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

		if (function_exists('set_time_limit')) @set_time_limit(1800);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- Silenced to suppress errors that may arise because of the function.

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
							$updraftplus->log(__('Search and replacing table:', 'updraftplus') .': '.__('skipped (not in list)', 'updraftplus'), 'notice-restore', 'restore-skipped-'.$tablename);
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
						$updraftplus->log(sprintf(__('Search and replacing table:', 'updraftplus')) . ' ' .$tablename.': '.__('already done', 'updraftplus'), 'notice-restore', 'restore-table-already-done-'.$tablename);
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

