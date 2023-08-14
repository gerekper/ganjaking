<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: migrator:One-click migrate a WordPress site to a different location with multisite to selective site restore.
Description: One click migration and selective site migration from multisite backups
Version: 4.0
Shop: /shop/migrator/
Latest Change: 1.23.5
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

updraft_try_include_file('includes/migrator-lite.php', 'require_once');
class UpdraftPlus_Addons_Migrator extends UpdraftPlus_Migrator_Lite {

	protected $restore_options = array();

	// This is also used to detect the situation of importing a single site into a multisite
	// Public, as it is used externally
	public $new_blogid;
	
	/**
	 * The url for the current site where WordPress application files
	 *
	 * @var string
	 */
	protected $siteurl;

	/**
	 * The url for the current site where the front end is accessible
	 *
	 * @var string
	 */
	protected $home;
	
	/**
	 * The url for the content directory
	 *
	 * @var string|boolean
	 */
	protected $content;

	/**
	 * The url for the uploads directory
	 *
	 * @var string
	 */
	private $uploads;

	/**
	 * The old url for the content directory
	 *
	 * @var string|boolean
	 */
	private $old_content;

	/**
	 * The old url for the uploads directory
	 *
	 * @var string
	 */
	private $old_uploads;

	/**
	 * Constructor, called during UD initialisation
	 */
	public function __construct() {
		add_action('updraftplus_restore_db_pre', array($this, 'updraftplus_restore_db_pre'));

		add_action('updraftplus_restore_db_record_old_content', array($this, 'updraftplus_restore_db_record_old_content'));
		add_action('updraftplus_restore_db_record_old_uploads', array($this, 'updraftplus_restore_db_record_old_uploads'));

		// MU
		add_action('updraftplus_restored_themes_one', array($this, 'restored_themes_one'));
		// Migrate tab output
		add_action('updraftplus_migrate_tab_output', array($this, 'updraftplus_migrate_tab_output'));

		// MU
		add_filter('updraftplus_restore_set_table_prefix', array($this, 'restore_set_table_prefix'), 10, 2);

		add_filter('updraftplus_get_history_status_result', array($this, 'get_history_status_result'));
		// Both MU and normal site
		// Actions/filters that need UD to be fully loaded before we can consider adding them
		add_action('plugins_loaded', array($this, 'plugins_loaded'));

		parent::__construct();
	}

	public function plugins_loaded() {
		global $updraftplus;
		// We don't support restoring single sites into multisite until WP 3.5
		// Some (significantly out-dated) information on what import-into-multisite involves: http://iandunn.name/comprehensive-wordpress-multisite-migrations/
		if (is_a($updraftplus, 'UpdraftPlus') && method_exists($updraftplus, 'get_wordpress_version') && version_compare($updraftplus->get_wordpress_version(), '3.5', '>=')) {
			// Both MU and normal site
			add_filter('updraftplus_restore_all_downloaded_postscan', array($this, 'restore_all_downloaded_postscan'), 10, 7);
			// MU
			add_filter('updraftplus_restore_this_table', array($this, 'restore_this_table'), 10, 3);
			// MU
			add_filter('updraftplus_pre_restore_move_in', array($this, 'pre_restore_move_in'), 10, 7);
			add_action('updraftplus_restorer_restore_options', array($this, 'restorer_restore_options'));
			// MU
			add_filter('updraftplus_restore_delete_recursive', array($this, 'restore_delete_recursive'), 10, 4);
			add_action('updraftplus_admin_enqueue_scripts', array($this, 'updraftplus_admin_enqueue_scripts'));
		}
	}

	public function updraftplus_admin_enqueue_scripts() {
		global $updraftplus;
		$updraftplus->enqueue_select2();
	}

	public function restore_delete_recursive($recurse, $ud_foreign, $restore_options, $type) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Unused parameter is present because the method is used as a WP filter.
		if ($recurse) return $recurse;
		// If doing a single-site-to-multisite import on the uploads, then we expect subdirectories to be around - they need deleting without raising any user-visible errors
		return ('uploads' == $type && !empty($this->new_blogid)) ? true : $recurse;
	}
	
	public function pre_restore_move_in($now_done, $type, $working_dir, $info, $backup_info, $restorer, $wp_filesystem_dir) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Unused parameter is present because the method is used as a WP filter.
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
				@$wp_filesystem->delete($move_from);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- Silenced to suppress errors that may arise because of the method.

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

	public function restore_this_table($restore_or_not, $unprefixed_table_name, $restore_options) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused parameter is present because the method is used as a WP filter.

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
	public function restore_all_downloaded_postscan($backups, $timestamp, $elements, &$info, &$mess, &$warn, &$err) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Unused parameter is present because the method is used as a WP filter.

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

				while (!isset($users) || count($users) > 0) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- The variable is defined inside the loop.

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
			$admins = get_users(array(
				'login__in' => get_super_admins(),
				'fields' => array( 'user_ID')
			));

			$user_id = 0;

			if (is_array($admins) && !empty($admins)) {
				$user_id = $admins[0]->ID;
			}

			$blog_data = array(
				'domain' => $domain,
				'path' => $path,
				'network_id' => $site_id,
				'title' => $weblog_title,
				'user_id' => $user_id,
			);
			$blog_id = wp_insert_site($blog_data);
		}

		return $blog_id;
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
}

global $updraftplus_addons_migrator;
if (!is_a($updraftplus_addons_migrator, 'UpdraftPlus_Addons_Migrator')) $updraftplus_addons_migrator = new UpdraftPlus_Addons_Migrator;


if (!class_exists('UpdraftPlus_Addons_Migrator_RemoteSend_UI')) updraft_try_include_file('includes/class-remote-send.php', 'require_once');
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
				<p><?php echo htmlspecialchars(__('To allow another site to send a backup to this site, create a key below.', 'updraftplus').' '.__("When you are shown the key, then press the 'Migrate' button on the other (sending) site, and copy-and-paste the key over there (in the 'Send a backup to another site' section).", 'updraftplus')); ?></p>
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

					var db_anon_all = jQuery('#updraft-navtab-migrate-content #updraftplus_migration_backupnow_db_anon_all').is(':checked') ? 1 : 0;
					var db_anon_non_staff = jQuery('#updraft-navtab-migrate-content #updraftplus_migration_backupnow_db_anon_non_staff').is(':checked') ? 1 : 0;
					var db_anon_wc_orders = jQuery('#updraft-navtab-migrate-content #updraftplus_migration_backupnow_db_anon_wc_order_data').is(':checked') ? 1 : 0;

					var extradata = {
						services: 'remotesend/'+site_id,
						db_anon: {all: db_anon_all, non_staff: db_anon_non_staff, wc_orders: db_anon_wc_orders}
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
