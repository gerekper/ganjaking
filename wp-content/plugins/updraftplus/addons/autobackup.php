<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: autobackup:Automatic Backups
Description: Save time and worry by automatically create backups before updating WordPress components
Version: 2.7
Shop: /shop/autobackup/
Latest Change: 1.12.28
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (defined('UPDRAFTPLUS_NOAUTOBACKUPS') && UPDRAFTPLUS_NOAUTOBACKUPS) return;

new UpdraftPlus_Addon_Autobackup;

class UpdraftPlus_Addon_Autobackup {

	// Has to be synced with WP_Automatic_Updater::run()
	private $lock_name = 'auto_updater.lock';

	private $already_backed_up = array();

	private $inpage_restrict = '';

	private $is_autobackup_core = null;

	/**
	 * Plugin constructor
	 */
	public function __construct() {
		add_filter('updraftplus_autobackup_blurb', array($this, 'updraftplus_autobackup_blurb'));
		add_action('admin_action_update-selected',  array($this, 'admin_action_update_selected'));
		add_action('admin_action_update-selected-themes', array($this, 'admin_action_update_selected_themes'));
		add_action('admin_action_do-plugin-upgrade', array($this, 'admin_action_do_plugin_upgrade'));
		add_action('admin_action_do-theme-upgrade', array($this, 'admin_action_do_theme_upgrade'));
		add_action('admin_action_do-theme-upgrade', array($this, 'admin_action_do_theme_upgrade'));
		add_action('admin_action_upgrade-plugin', array($this, 'admin_action_upgrade_plugin'));
		add_action('admin_action_upgrade-theme', array($this, 'admin_action_upgrade_theme'));
		add_action('admin_action_do-core-upgrade', array($this, 'admin_action_do_core_upgrade'));
		add_action('admin_action_do-core-reinstall', array($this, 'admin_action_do_core_upgrade'));
		add_action('ud_wp_maybe_auto_update', array($this, 'ud_wp_maybe_auto_update'));
		add_action('updraftplus_configprint_expertoptions', array($this, 'configprint_expertoptions'));
		
		// Hooks into JetPack's remote updater (manual updates performed from the wordpress.com console)
		add_action('jetpack_pre_plugin_upgrade', array($this, 'jetpack_pre_plugin_upgrade'), 10, 3);
		add_action('jetpack_pre_theme_upgrade', array($this, 'jetpack_pre_theme_upgrade'), 10, 2);
		add_action('jetpack_pre_core_upgrade', array($this, 'jetpack_pre_core_upgrade'));
		
		add_action('plugins_loaded', array($this, 'plugins_loaded'));
		
		add_action('admin_footer', array($this, 'admin_footer_possibly_network_themes'));
		add_action('pre_current_active_plugins', array($this, 'pre_current_active_plugins'));
		add_action('install_plugins_pre_plugin-information', array($this, 'install_plugins_pre_plugin'));
		add_filter('updraftplus_dirlist_wpcore_override', array($this, 'updraftplus_dirlist_wpcore_override'), 10, 2);
		add_filter('updraft_wpcore_description', array($this, 'wpcore_description'));
	}

	/**
	 * Runs upon the WP action plugins_loaded
	 */
	public function plugins_loaded() {
	
		global $updraftplus;
	
		$wp_version = $updraftplus->get_wordpress_version();
		
		if (version_compare($wp_version, '4.4.0', '<')) {
			// Somewhat inelegant... see: https://core.trac.wordpress.org/ticket/30441
			add_filter('auto_update_plugin', array($this, 'auto_update_plugin'), PHP_INT_MAX, 2);
			add_filter('auto_update_theme', array($this, 'auto_update_theme'), PHP_INT_MAX, 2);
			add_filter('auto_update_core', array($this, 'auto_update_core'), PHP_INT_MAX, 2);
		} else {
			// Action added in WP 4.4
			add_action('pre_auto_update', array($this, 'pre_auto_update'), 10, 2);
		}
		
		// Shiny updates land in wp-admin/themes.php with WP 4.6 (and the trunk before the release, of course)
		if (version_compare($wp_version, '4.5.999', '>')) {
			add_action('load-themes.php', array($this, 'load_themes_php'));
		}

	}
	
	/**
	 * All 3 of these hooks since JetPack 3.9.2 (assuming our patch goes in)
	 *
	 * @param  array $plugin
	 * @param  array $plugins
	 * @param  array $update_attempted
	 * @return array
	 */
	public function jetpack_pre_plugin_upgrade($plugin, $plugins, $update_attempted) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		$this->auto_update(true, $plugin, 'plugins');
	}
	
	public function jetpack_pre_theme_upgrade($theme, $themes) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		$this->auto_update(true, $theme, 'themes');
	}
	
	public function jetpack_pre_core_upgrade($update) {
		$this->auto_update(true, $update, 'core');
	}
	
	public function load_themes_php() {
		if (!current_user_can('update_themes')) return;
		$this->inpage_restrict = 'themes';
		add_action('admin_footer', array($this, 'admin_footer_inpage_backup'));
	}
	
	public function install_plugins_pre_plugin() {
		if (!current_user_can('update_plugins')) return;
		$this->inpage_restrict = 'plugins';
		add_action('admin_footer', array($this, 'admin_footer_inpage_backup'));
	}

	public function wpcore_description($desc) {
		global $updraftplus;
		$is_autobackup = $updraftplus->jobdata_get('is_autobackup', false);
		if (empty($this->is_autobackup_core) && !$is_autobackup) return $desc;
		return $is_autobackup ? __('WordPress core (only)', 'updraftplus') : $desc;
	}

	public function ud_wp_maybe_auto_update($lock_value) {
		$lock_result = get_option($this->lock_name);
		if ($lock_result != $lock_value) return;

		// Remove the lock, to allow the WP updater to claim it and proceed
		delete_option($this->lock_name);

		$this->do_not_filter_auto_backup = true;
		wp_maybe_auto_update();
	}

	public function configprint_expertoptions() {
		?>
		<tr class="expertmode updraft-hidden" style="display:none;">
			<th><?php _e('UpdraftPlus Automatic Backups', 'updraftplus');?>:</th>
			<td><?php $this->auto_backup_form(false, 'updraft_autobackup_default', '1');?></td>
		</tr>
		<?php
	}

	public function initial_jobdata($jobdata) {
		if (!is_array($jobdata)) return $jobdata;
		$jobdata[] = 'reschedule_before_upload';
		$jobdata[] = true;
		return $jobdata;
	}

	public function initial_jobdata2($jobdata) {
		if (!is_array($jobdata)) return $jobdata;
		$jobdata[] = 'is_autobackup';
		$jobdata[] = true;
		$jobdata[] = 'label';
		$jobdata[] = __('Automatic backup before update', 'updraftplus');
		return $jobdata;
	}

	/**
	 * This function will add some extra logging data to the log when it's an automatic backup adding what triggered the backup
	 *
	 * @param string $extralog - the extra log data
	 *
	 * @return string - the modified extra log data
	 */
	public function autobackup_extralog($extralog) {
		global $updraftplus;

		$backup_entities = array_keys($updraftplus->jobdata_get('job_file_entities'));

		if (empty($backup_entities) || empty($extralog)) return $extralog;

		$entities = join(', ', $backup_entities);

		if (!empty($entities)) $extralog .= " caused by entities ($entities)";
		
		return $extralog;
	}

	/**
	 * WP 4.4+
	 *
	 * @param  array  $type This is the type such as plugin or theme
	 * @param  object $item THis is the item
	 * @return string
	 */
	public function pre_auto_update($type, $item) {
		// Can also be 'translation'. We don't auto-backup for those.
		if ('plugin' == $type || 'theme' == $type) {
			$this->auto_update(true, $item, $type.'s');
		} elseif ('core' == $type) {
			$this->auto_update(true, $item, $type);
		}
	}
	
	/**
	 * Before WP 4.4
	 *
	 * @param  string $update
	 * @param  object $item
	 * @return string
	 */
	public function auto_update_plugin($update, $item) {
		return $this->auto_update($update, $item, 'plugins');
	}

	public function auto_update_theme($update, $item) {
		return $this->auto_update($update, $item, 'themes');
	}

	public function auto_update_core($update, $item) {
		return $this->auto_update($update, $item, 'core');
	}

	/**
	 * Note - with the addition of support for JetPack remote updates (via manual action in a user's wordpress.com dashboard), this is now more accurately a method to handle *background* updates, rather than "automatic" ones.
	 *
	 * @param  string $update
	 * @param  object $item
	 * @param  array  $type
	 * @return string
	 */
	public function auto_update($update, $item, $type) {
		if (!$update || !empty($this->do_not_filter_auto_backup) || in_array($type, $this->already_backed_up) || !UpdraftPlus_Options::get_updraft_option('updraft_autobackup_default') || (!$this->doing_filter('wp_maybe_auto_update') && !$this->doing_filter('jetpack_pre_plugin_upgrade') && !$this->doing_filter('jetpack_pre_theme_upgrade') && !$this->doing_filter('jetpack_pre_core_upgrade'))) return $update;

		if ('core' == $type) {
			// This has to be copied from WP_Automatic_Updater::should_update() because it's another reason why the eventual decision may be false.
			// If it's a core update, are we actually compatible with its requirements?
			global $wpdb;
			$php_compat = version_compare(phpversion(), $item->php_version, '>=');
			if (file_exists(WP_CONTENT_DIR . '/db.php') && empty($wpdb->is_mysql))
				$mysql_compat = true;
			else $mysql_compat = version_compare($wpdb->db_version(), $item->mysql_version, '>=');
			if (!$php_compat || !$mysql_compat)
				return false;
		}

		// Go ahead - it's auto-backup-before-auto-update time.
		// Add job data to indicate that a resumption should be scheduled if the backup completes before the cloud-backup stage
		add_filter('updraftplus_initial_jobdata', array($this, 'initial_jobdata'));
		add_filter('updraftplus_initial_jobdata', array($this, 'initial_jobdata2'));
		
		add_filter('updraftplus_autobackup_extralog', array($this, 'autobackup_extralog'));

		// Reschedule the real background update for 10 minutes from now (i.e. lessen the risk of a timeout by chaining it).
		$this->reschedule(600);

		global $updraftplus;

		$backup_database = !in_array('db', $this->already_backed_up);

		if ('core' == $type) {
			$entities = $updraftplus->get_backupable_file_entities();
			if (isset($entities['wpcore'])) {
				$backup_files = true;
				$backup_files_array = array('wpcore');
			} else {
				$backup_files = false;
				$backup_files_array = false;
			}
		} else {
			$backup_files = true;
			$backup_files_array = array($type);
		}

		if ('core' == $type) {
			$this->is_autobackup_core = true;
		}

		$updraftplus->boot_backup((int) $backup_files, (int) $backup_database, $backup_files_array, true);

		$this->already_backed_up[] = $type;
		if ($backup_database) $this->already_backed_up[] = 'db';

		// The backup apparently completed. Reschedule for very soon, in case not enough PHP time remains to complete an update too.
		$this->reschedule(120);

		// But then, also go ahead anyway, in case there's enough time (we want to minimise the time between the backup and the update)
		return $update;
	}

	public function updraftplus_dirlist_wpcore_override($l, $whichdir) {

		global $updraftplus;
		$is_autobackup = $updraftplus->jobdata_get('is_autobackup', false);
		if (empty($this->is_autobackup_core) && !$is_autobackup) return $l;

		// This does not need to include everything - only code
		$possible = array('wp-admin', 'wp-includes', 'index.php', 'xmlrpc.php', 'wp-config.php', 'wp-activate.php', 'wp-app.php', 'wp-atom.php', 'wp-blog-header.php', 'wp-comments-post.php', 'wp-commentsrss2.php', 'wp-cron.php', 'wp-feed.php', 'wp-links-opml.php', 'wp-load.php', 'wp-login.php', 'wp-mail.php', 'wp-pass.php', 'wp-rdf.php', 'wp-register.php', 'wp-rss2.php', 'wp-rss.php', 'wp-settings.php', 'wp-signup.php', 'wp-trackback.php', '.htaccess');

		$wpcore_dirlist = array();
		$whichdir = trailingslashit($whichdir);

		foreach ($possible as $file) {
			if (file_exists($whichdir.$file)) $wpcore_dirlist[] = $whichdir.$file;
		}

		return (!empty($wpcore_dirlist)) ? $wpcore_dirlist : $l;
	}

	/**
	 * Reschedule the automatic update check event
	 *
	 * @param Integer $how_long - how many seconds in the future from now to reschedule for
	 */
	private function reschedule($how_long) {
		wp_clear_scheduled_hook('ud_wp_maybe_auto_update');
		if (!$how_long) return;
		global $updraftplus;
		$updraftplus->log("Rescheduling WP's automatic update check for $how_long seconds ahead");
		$lock_result = get_option($this->lock_name);
		wp_schedule_single_event(time() + $how_long, 'ud_wp_maybe_auto_update', array($lock_result));
	}

	/**
	 * This appears on the page listing several updates
	 */
	public function updraftplus_autobackup_blurb() {
		$ret = '<div class="updraft-ad-container updated" style="display:block;">';
		$ret .= '<h3 style="margin-top: 2px;">'. __('Be safe with an automatic backup', 'updraftplus').'</h3>';
		$ret .= '<input '.((UpdraftPlus_Options::get_updraft_option('updraft_autobackup_default', true)) ? 'checked="checked"' : '').' type="checkbox" id="updraft_autobackup" value="doit" name="updraft_autobackup"> <label for="updraft_autobackup">'.
		__('Automatically backup (where relevant) plugins, themes and the WordPress database with UpdraftPlus before updating', 'updraftplus').
		'</label><br><input checked="checked" type="checkbox" value="set" name="updraft_autobackup_setdefault" id="updraft_autobackup_sdefault"> <label for="updraft_autobackup_sdefault">'.
		__('Remember this choice for next time (you will still have the chance to change it)', 'updraftplus').
		'</label><br><em><a href="https://updraftplus.com/automatic-backups/" target="_blank">'.__('Read more about how this works...', 'updraftplus').'</a></em>';
		// New-style widgets
		add_action('admin_footer', array($this, 'admin_footer_inpage_backup'));
		add_action('admin_footer', array($this, 'admin_footer_insertintoform'));
		$ret .= '</div>';
		return $ret;
	}

	public function admin_footer_insertintoform() {
		$def = UpdraftPlus_Options::get_updraft_option('updraft_autobackup_default', true);
		$godef = $def ? 'yes' : 'no';
		// Note - now, in the new-style widgetised setup (Feb 2015), we always set updraftplus_noautobackup=1 - because the actual backup will be done in-page. But that is not done here - it is done when the form is submitted, in updraft_try_inpage();
		echo <<<ENDHERE
		<script>
		jQuery(function($) {
			$('form.upgrade').append('<input type="hidden" name="updraft_autobackup" class="updraft_autobackup_go" value="$godef">');
			$('form.upgrade').append('<input type="hidden" name="updraft_autobackup_setdefault" class="updraft_autobackup_setdefault" value="yes">');
			$('#updraft_autobackup').on('click', function() {
				var doauto = $(this).attr('checked');
				if ('checked' == doauto) {
					$('.updraft_autobackup_go').attr('value', 'yes');
				} else {
					$('.updraft_autobackup_go').attr('value', 'no');
				}
			});
			$('#updraft_autobackup_sdefault').on('click', function() {
				var sdef = $(this).attr('checked');
				if ('checked' == sdef) {
					$('.updraft_autobackup_setdefault').attr('value', 'yes');
				} else {
					$('.updraft_autobackup_setdefault').attr('value', 'no');
				}
			});
		});
		</script>
ENDHERE;
	}

	public function admin_footer() {
		if (!current_user_can('update_'.$this->internaltype)) return;
		$creating = esc_js(sprintf(__('Creating %s and database backup with UpdraftPlus...', 'updraftplus'), $this->type).' '.__('(logs can be found in the UpdraftPlus settings page as normal)...', 'updraftplus'));
		$lastlog = esc_js(__('Last log message', 'updraftplus')).':';
		
		global $updraftplus;
		$updraftplus->log(__('Starting automatic backup...', 'updraftplus'));

		$unexpected_response = esc_js(__('Unexpected response:', 'updraftplus'));

		echo <<<ENDHERE
			<script>
				jQuery('h2').first().after('<p>$creating</p><p>$lastlog <span id="updraft_lastlogcontainer"></span></p><div id="updraft_activejobs"></div>');
				var lastlog_sdata = {
					oneshot: 'yes'
				};
				setInterval(function() {updraft_autobackup_showlastlog(true);}, 3000);
				function updraft_autobackup_showlastlog(repeat) {
					updraft_send_command('activejobs_list', lastlog_sdata, function(response) {
						try {
							resp = ud_parse_json(response);
							if (resp.l != null) { jQuery('#updraft_lastlogcontainer').html(resp.l); }
							if (resp.j != null && resp.j != '') {
								jQuery('#updraft_activejobs').html(resp.j);
							} else {
								if (!jQuery('#updraft_activejobs').is(':hidden')) {
									jQuery('#updraft_activejobs').hide();
								}
							}
						} catch(err) {
							console.log('$unexpected_response '+response);
						}
					}, { type: GET, json_parse: false });
				}
			</script>
ENDHERE;
	}

	private function process_form() {
		// We use 0 instead of false, because false is the default for get_option(), and thus setting an unset value to false with update_option() actually sets nothing (since update_option() first checks for the existing value) - which is unhelpful if you want to call get_option() with a different default (as we do)
		$autobackup = (isset($_POST['updraft_autobackup']) && 'yes' == $_POST['updraft_autobackup']) ? 1 : 0;
		if (!empty($_POST['updraft_autobackup_setdefault']) && 'yes' == $_POST['updraft_autobackup_setdefault']) UpdraftPlus_Options::update_updraft_option('updraft_autobackup_default', $autobackup);

		// Having dealt with the saving, now see if we really wanted to do it
		if (!empty($_REQUEST['updraftplus_noautobackup'])) $autobackup = 0;
		UpdraftPlus_Options::update_updraft_option('updraft_autobackup_go', $autobackup);

		if ($autobackup) add_action('admin_footer', array($this, 'admin_footer'));
	}

	/**
	 * The initial form submission from the updates page
	 */
	public function admin_action_do_plugin_upgrade() {
		if (!current_user_can('update_plugins')) return;
		$this->type = __('plugins', 'updraftplus');
		$this->internaltype = 'plugins';
		$this->process_form();
	}

	public function admin_action_do_theme_upgrade() {
		if (!current_user_can('update_themes')) return;
		$this->type = __('themes', 'updraftplus');
		$this->internaltype = 'themes';
		$this->process_form();
	}

	/**
	 * Into the updating iframe...
	 */
	public function admin_action_update_selected() {
		if (!current_user_can('update_plugins')) return;
		$autobackup = UpdraftPlus_Options::get_updraft_option('updraft_autobackup_go');
		if ($autobackup) $this->autobackup_go('plugins');
	}

	public function admin_action_update_selected_themes() {
		if (!current_user_can('update_themes')) return;
		$autobackup = UpdraftPlus_Options::get_updraft_option('updraft_autobackup_go');
		if ($autobackup) $this->autobackup_go('themes');
	}

	public function admin_action_do_core_upgrade() {
		if (!isset($_POST['upgrade'])) return;
		if (!empty($_REQUEST['updraftplus_noautobackup'])) return;
		if (!current_user_can('update_core')) wp_die(__('You do not have sufficient permissions to update this site.'));
		check_admin_referer('upgrade-core');

		// It is important to not use (bool)false here, as that conflicts with using get_option() with a non-false default value
		$autobackup = (isset($_POST['updraft_autobackup']) && 'yes' == $_POST['updraft_autobackup']) ? 1 : 0;

		if (!empty($_POST['updraft_autobackup_setdefault']) && 'yes' == $_POST['updraft_autobackup_setdefault']) UpdraftPlus_Options::update_updraft_option('updraft_autobackup_default', $autobackup);

		if ($autobackup) {
			include_once(ABSPATH . 'wp-admin/admin-header.php');

			$creating = __('Creating database backup with UpdraftPlus...', 'updraftplus').' '.__('(logs can be found in the UpdraftPlus settings page as normal)...', 'updraftplus');

			$lastlog = __('Last log message', 'updraftplus').':';
			
			$unexpected_response = esc_js(__('Unexpected response:', 'updraftplus'));

			global $updraftplus;
			$updraftplus->log(__('Starting automatic backup...', 'updraftplus'));

			echo '<div class="wrap"><h2>'.__('Automatic Backup', 'updraftplus').'</h2>';

			echo "<p>$creating</p><p>$lastlog <span id=\"updraft_lastlogcontainer\"></span></p><div id=\"updraft_activejobs\" style=\"clear:both;\"></div>";

			echo <<<ENDHERE
				<script>
					var lastlog_sdata = {
						oneshot: 'yes'
					};
					setInterval(function() {updraft_autobackup_showlastlog(true);}, 3000);
					function updraft_autobackup_showlastlog(repeat) {
						updraft_send_command('activejobs_list', lastlog_sdata, function(response) {
							try {
								resp = ud_parse_json(response);
								if (resp.l != null) { jQuery('#updraft_lastlogcontainer').html(resp.l); }
								if (resp.j != null && resp.j != '') {
									jQuery('#updraft_activejobs').html(resp.j);
								} else {
									if (!jQuery('#updraft_activejobs').is(':hidden')) {
										jQuery('#updraft_activejobs').hide();
									}
								}
							} catch(err) {
								console.log('$unexpected_response '+response);
							}
						}, { type: GET, json_parse: false });
					}
				</script>
ENDHERE;

			$this->type = 'core';
			$this->internaltype = 'core';
			$this->autobackup_go('core', true);
			echo '</div>';
		}

	}

	/**
	 * This is in WP 3.9 and later as a global function (but we support earlier)
	 *
	 * @param  string $filter null
	 * @return array
	 */
	private function doing_filter($filter = null) {
		if (function_exists('doing_filter')) return doing_filter($filter);
		global $wp_current_filter;
		if (null === $filter) {
			return !empty($wp_current_filter);
		}
		return in_array($filter, $wp_current_filter);
	}

	private function autobackup_go($entity, $jquery = false) {
		define('UPDRAFTPLUS_BROWSERLOG', true);
		echo '<p style="clear:left; padding-top:6px;">'.__('Creating backup with UpdraftPlus...', 'updraftplus')."</p>";
		@ob_end_flush();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		echo '<pre id="updraftplus-autobackup-log">';
		global $updraftplus;

		if ('core' == $entity) {
			$entities = $updraftplus->get_backupable_file_entities();
			if (isset($entities['wpcore'])) {
				$backup_files = true;
				$backup_files_array = array('wpcore');
			} else {
				$backup_files = false;
				$backup_files_array = false;
			}
		} else {
			$backup_files = true;
			$backup_files_array = array($entity);
		}

		if ('core' == $entity) {
			$this->is_autobackup_core = true;
		}

		add_filter('updraftplus_initial_jobdata', array($this, 'initial_jobdata2'));
		add_filter('updraftplus_autobackup_extralog', array($this, 'autobackup_extralog'));

		$updraftplus->boot_backup((int) $backup_files, 1, $backup_files_array, true);
		echo '</pre>';
		if ($updraftplus->error_count() >0) {
			echo '<h2>'.__("Errors have occurred:", 'updraftplus').'</h2>';
			$updraftplus->list_errors();
			if ($jquery) include(ABSPATH . 'wp-admin/admin-footer.php');
			die;
		}
		$this->autobackup_finish($jquery);
	}

	private function autobackup_finish($jquery = false) {

		global $updraftplus, $wpdb;
		if (method_exists($wpdb, 'check_connection') && !$wpdb->check_connection(false) && (!defined('UPDRAFTPLUS_SUPPRESS_CONNECTION_CHECKS') || !UPDRAFTPLUS_SUPPRESS_CONNECTION_CHECKS)) {
			$updraftplus->log("It seems the database went away, and could not be reconnected to");
			die;
		}

		echo "<script>var h = document.getElementById('updraftplus-autobackup-log'); h.style.display='none';</script>";

		if ($jquery) {
			echo '<p>'.__('Backup succeeded', 'updraftplus').' <a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'#updraftplus-autobackup-log" onclick="jQuery(\'#updraftplus-autobackup-log\').slideToggle();">'.__('(view log...)', 'updraftplus').'</a> - '.__('now proceeding with the updates...', 'updraftplus').'</p>';
		} else {
			echo '<p>'.__('Backup succeeded', 'updraftplus').' <a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'#updraftplus-autobackup-log" onclick="var s = document.getElementById(\'updraftplus-autobackup-log\'); s.style.display = \'block\';">'.__('(view log...)', 'updraftplus').'</a> - '.__('now proceeding with the updates...', 'updraftplus').'</p>';
		}

	}

	public function admin_action_upgrade_plugin() {
		if (!current_user_can('update_plugins')) return;

		$plugin = isset($_REQUEST['plugin']) ? trim($_REQUEST['plugin']) : '';
		check_admin_referer('upgrade-plugin_' . $plugin);

		$autobackup = $this->get_setting_and_check_default_setting_save();

		if (!empty($_REQUEST['updraftplus_noautobackup'])) return;

		$title = __('Update Plugin');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$parent_file = 'plugins.php';// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$submenu_file = 'plugins.php';// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		include_once(ABSPATH . 'wp-admin/admin-header.php');

		$this->inpage_restrict = 'plugins';

		// Did the user get the opportunity to indicate whether they wanted a backup?
		if (!isset($_POST['updraft_autobackup_answer'])) $this->auto_backup_form_and_die();

		if ($autobackup) {
			echo '<div class="wrap"><h2>'.__('Automatic Backup', 'updraftplus').'</h2>';
			$this->autobackup_go('plugins', true);
			echo '</div>';
		}

		// Now, the backup is (if chosen) done... but the upgrade may not directly proceed. If WP needed filesystem credentials, then it may put up an intermediate screen, which we need to insert a field in to prevent an endless circle
		add_filter('request_filesystem_credentials', array($this, 'request_filesystem_credentials'));

	}

	public function get_setting_and_check_default_setting_save() {
		// Do not use bools here - conflicts with get_option() with a non-default value
		$autobackup = (isset($_REQUEST['updraft_autobackup']) && 'yes' == $_REQUEST['updraft_autobackup']) ? 1 : 0;

		if (!empty($_REQUEST['updraft_autobackup_setdefault']) && 'yes' == $_REQUEST['updraft_autobackup_setdefault']) UpdraftPlus_Options::update_updraft_option('updraft_autobackup_default', $autobackup);

		return $autobackup;
	}

	public function request_filesystem_credentials($input) {
		echo <<<ENDHERE
<script>
	jQuery(function() {
		jQuery('#upgrade').before('<input type="hidden" name="updraft_autobackup_answer" value="1">');
	});
</script>
ENDHERE;
		return $input;
	}

	public function admin_action_upgrade_theme() {

		if (!current_user_can('update_themes')) return;
		$theme = isset($_REQUEST['theme']) ? urldecode($_REQUEST['theme']) : '';
		check_admin_referer('upgrade-theme_' . $theme);

		$autobackup = $this->get_setting_and_check_default_setting_save();

		if (!empty($_REQUEST['updraftplus_noautobackup'])) return;

		$title = __('Update Theme');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$parent_file = 'themes.php';// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$submenu_file = 'themes.php';// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		include_once(ABSPATH.'wp-admin/admin-header.php');

		$this->inpage_restrict = 'themes';

		// Did the user get the opportunity to indicate whether they wanted a backup?
		if (!isset($_POST['updraft_autobackup_answer'])) $this->auto_backup_form_and_die();

		if ($autobackup) {
			echo '<div class="wrap"><h2>'.__('Automatic Backup', 'updraftplus').'</h2>';
			$this->autobackup_go('themes', true);
			echo '</div>';
		}

		// Now, the backup is (if chosen) done... but the upgrade may not directly proceed. If WP needed filesystem credentials, then it may put up an intermediate screen, which we need to insert a field in to prevent an endless circle
		add_filter('request_filesystem_credentials', array($this, 'request_filesystem_credentials'));

	}

	private function auto_backup_form_and_die() {
		$this->auto_backup_form();
		// Prevent rest of the page - unnecessary since we die() anyway
		// unset($_GET['action']);
		add_action('admin_footer', array($this, 'admin_footer_inpage_backup'));
		include(ABSPATH . 'wp-admin/admin-footer.php');
		die;
	}
	
	public function admin_footer_possibly_network_themes() {
		$hook_suffix = $GLOBALS['hook_suffix'];
		if ('themes.php' == $hook_suffix && is_multisite() && is_network_admin() && current_user_can('update_themes')) {
			$this->inpage_restrict = 'themes';
			// Don't add an action - we're already in the footer action; just do it
			$this->admin_footer_inpage_backup();
		}
	}

	public function pre_current_active_plugins() {
		if (!current_user_can('update_plugins')) return;
		$this->inpage_restrict = 'plugins';
		add_action('admin_footer', array($this, 'admin_footer_inpage_backup'));
	}

	/**
	 * Inserts the HTML and JavaScript for in-page backup pre-update scaffolding. Should be called during admin_footer. Since Feb 2015.
	 * Basically, this function renders the minimum necessary of the admin furniture to be able to get everything up and running. It is an _alternative_ to the full set of furniture.
	 * Mar 2015: Tweaks added for WP's new "shiny updates" method (wp-admin/js/updates.js) - principally, the update lock.
	 */
	public function admin_footer_inpage_backup() {
		if (!empty($this->inpage_restrict) && !current_user_can('update_'.$this->inpage_restrict)) return;
		
		static $already_printed = false;
		if ($already_printed) return;
		$already_printed = true;
		
		global $updraftplus_admin;
		
		// Import original value of $wp_version
		include(ABSPATH.WPINC.'/version.php');
		
		$lock_variable = version_compare($wp_version, "4.5.999", "<") ? 'updateLock' : 'ajaxLocked';// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
		$queue_variable = version_compare($wp_version, "4.5.999", "<") ? 'updateQueue' : 'queue';// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable

		?>
			<script type="text/javascript">
				var updraft_credentialtest_nonce='<?php echo wp_create_nonce('updraftplus-credentialtest-nonce');?>';
				var updraft_siteurl = '<?php echo esc_js(site_url('', 'relative'));?>';
				var updraft_autobackup_cleared_to_go = 0;
				var updraft_actually_proceeding = false;
				// This is brought in for WP 4.7, which won't unload the page if the queue is locked. If we locked it, but don't need to keep it locked, in this situation we need to unlock it - but not otherwise.
				var updraft_we_locked_it = false;
				
				jQuery(function($) {

				
					var is_network_multisite_bulk_themes_page = ($('body.multisite.network-admin.themes-php').length > 0) ? true : false;
				
					var updraft_bulk_updates_proceed = false;
					
					// This variable indicates whether we should ask for confirmation on page unload
					var something_happening = false;

					// Shiny updates in WP 4.2+ . We are particularly interested in wp.updates.ajaxLocked (updateLock before 4.6.0) and wp.updates.queueChecker();
					window.wp = window.wp || {};
					var wp = window.wp;
					var shiny_updates = (wp.hasOwnProperty('updates') && wp.updates.hasOwnProperty('<?php echo $lock_variable; ?>')) ? 1 : 0;

					if (shiny_updates) {
					
						console.log('UpdraftPlus: WP shiny updates (4.2+) detected: lock (lock variable: <?php echo $lock_variable; ?>)');
						
						// We lock at this early stage, because jQuery doesn't give us a way (without fiddling with internals) to change the event order to make our click handler go first and lock then.
						wp.updates.<?php echo $lock_variable; ?> = true;
						updraft_we_locked_it = true;

						$(window).off('beforeunload', wp.updates.beforeunload);

						$(window).on('beforeunload', function() {
							if (something_happening) { return wp.updates.beforeunload(); }
							// Otherwise: let the unload proceed
							if (updraft_we_locked_it) {
								updraft_we_locked_it = false;
								if (wp.updates.<?php echo $lock_variable; ?>) {
									wp.updates.<?php echo $lock_variable; ?> = false;
									console.log("UpdraftPlus: unlocking shiny updates queue (which we locked) before page unload");
								}
							}
							return wp.updates.beforeunload();
						});

						jQuery(document).on('wp-plugin-update-success wp-theme-update-success wp-plugin-delete-success wp-theme-delete-success wp-plugin-delete-error wp-theme-delete-error wp-plugin-install-error wp-theme-install-error', function(e) {
							var event_type = e.type;
							if (wp.updates.<?php echo $queue_variable;?>.length == 0) {
								console.log("UpdraftPlus: detected newly-empty queue (via "+event_type+"): locking");
								wp.updates.<?php echo $lock_variable; ?> = true;
								updraft_we_locked_it = true;
								something_happening = false;
							}
						});

					}

					// This is called if something causes the update to be cancelled
					function shiny_updates_cancel() {
						console.log("UpdraftPlus: WP shiny updates: shiny_updates_cancel()");
						updraft_actually_proceeding = false;
						if (!shiny_updates) { return; }
						// This function does everything needed
						if (wp.updates.<?php echo $queue_variable;?>.length > 0) { wp.updates.requestForCredentialsModalCancel(); }

						wp.updates.<?php echo $lock_variable; ?> = true;
						updraft_we_locked_it = true;
						something_happening = false;
					}
					
					function shiny_updates_complete() {
						console.log("UpdraftPlus: WP shiny updates: shiny_updates_complete()");
						if (!shiny_updates || updraft_actually_proceeding) { return; }
						if (wp.updates.<?php echo $queue_variable;?>.length > 0) { return shiny_updates_cancel(); }
						wp.updates.<?php echo $queue_variable;?> = [];
						something_happening = false;
						wp.updates.<?php echo $lock_variable; ?> = true;
						updraft_we_locked_it = true;
					}
					
					// This is called when a previously-delayed (i.e. delayed because we interposed to ask about, and perhaps perform, an automatic backup) update can now go ahead
					function shiny_updates_proceed() {
						something_happening = true;
						var qlen = wp.updates.<?php echo $queue_variable;?>.length;
						console.log('UpdraftPlus: WP shiny updates: shiny_updates_proceed(): release lock; queue length: '+qlen);
						// FTP credentials, if necessary
						wp.updates.<?php echo $lock_variable; ?> = false;
						updraft_we_locked_it = false;
						if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.<?php echo $lock_variable; ?> ) {
							// This will set the lock back to true, if necessary
							wp.updates.requestFilesystemCredentials();
						}
						// This won't do anything if the lock is set
						wp.updates.queueChecker();
// 						qlen = wp.updates.<?php echo $queue_variable;?>.length;
// 						if (qlen == 0) { updraft_actually_proceeding = false;} 
						updraft_actually_proceeding = false;
					}

					function updates_intercept(e, passthis, checklink, via_shiny_updates, type) {

						// 'theme' not yet handled
						type = ('undefined' === typeof type) ? 'plugin' : type;

						updraft_actually_proceeding = false;

						if (via_shiny_updates && !wp.updates.<?php echo $lock_variable; ?>) {
							console.log('UpdraftPlus: WP shiny updates ('+type+'): lock');
							something_happening = true;
							wp.updates.<?php echo $lock_variable; ?> = true;
							updraft_we_locked_it = true;
						}

						// Unused
// 						var detecting_pluginfo = jQuery(passthis).parents('#plugin-information-footer');
						var detecting_entity_info_len;
						if ('plugin' == type) {
							// This appears to be sent back by wordpress.org - it appears nowhere in the WP code base except in stylesheets.
							detecting_entity_info_len = jQuery(passthis).parents('#plugin-information-footer').length;
						} else {
							// This doesn't exist at all
							// detecting_entity_info_len = jQuery(passthis).parents('#theme-information-footer').length;
							detecting_entity_info_len = 0;
						}

						var link;
						if (checklink) {
							link = jQuery(passthis).attr('href');
							if (link.indexOf('action=upgrade-'+type) < 0) { return; }
						} else {
							//  (previous comment is no longer true - Irrelevant: checklink = false is only called with shiny updates)
// 							link = '';
							link = document.location;
						}

						e.preventDefault();
						var updraft_inpage_modal_buttons = {};
						updraft_inpage_modal_buttons[updraftlion.cancel] = function() {
							updraft_actually_proceeding = false;
							if (via_shiny_updates) { shiny_updates_cancel(); }
							jQuery(this).dialog('close');
						};
						updraft_inpage_modal_buttons[updraftlion.proceedwithupdate] = function() {
							// Don't let the old-style autobackup fire as well
							var newlink = link+'&updraftplus_noautobackup=1';
							var $dialog = jQuery(this);
							if (jQuery('#updraft_autobackup_setdefault').is(':checked')) {
								newlink = newlink + '&updraft_autobackup_setdefault=yes';
								var autobackup;
								if (jQuery('#updraft_autobackup').is(':checked')) {
									newlink = newlink + '&updraft_autobackup=yes';
									autobackup = 1;
								} else  {
									newlink = newlink + '&updraft_autobackup=';
									autobackup = 0;
								}
								
								updraft_send_command('set_autobackup_default', {default: autobackup}, function(response) {
									console.log(response);
								});
							}
							
							// Is an autobackup wanted?
							if (jQuery('#updraft_autobackup').is(':checked')) {
								// Run the backup, and then run the specified callback when it is complete
								updraft_backupnow_inpage_go(function() {
									updraft_actually_proceeding = true;
									$dialog.dialog('close');
									if (via_shiny_updates && detecting_entity_info_len == 0) {
										shiny_updates_proceed();
									} else {

										// Proceed to update via standard form submission/redirection
										if (jQuery(passthis).find('#bulk-action-selector-top').length > 0) {
											updraft_bulk_updates_proceed = true;
											jQuery(passthis).trigger('submit');
										} else {
											window.location.href = newlink;
										}
									}
								}, '<?php echo esc_js($this->inpage_restrict);?>', 'autobackup');
							} else {
								// No auto backup wanted - just proceed
								updraft_actually_proceeding = true;
								$dialog.dialog('close');
								// Proceed to update
								if (via_shiny_updates && detecting_entity_info_len == 0) {
									shiny_updates_proceed();
								} else {
									// Proceed via standard form submission
									if (jQuery(passthis).find('#bulk-action-selector-top').length > 0) {
										updraft_bulk_updates_proceed = true;
										jQuery(passthis).trigger('submit');
									} else {
										window.location.href = newlink;
									}
								}
							}
						};
						jQuery('#updraft-backupnow-inpage-modal').dialog({
							autoOpen: false,
							modal: true,
							resizeOnWindowResize: true,
							scrollWithViewport: true,
							resizeAccordingToViewport: true,
							useContentSize: false,
							open: function(event, ui) {
								jQuery(this).dialog('option', 'width', 580);
								jQuery(this).dialog('option', 'minHeight', 261);
								jQuery(this).dialog('option', 'height', 380);
							},
							buttons: updraft_inpage_modal_buttons});
						jQuery('#updraft_inpage_backup').hide();
						jQuery('#updraft-backupnow-inpage-modal').on('dialogclose', function(event) {
							if (updraft_actually_proceeding) { return; }
							//shiny_updates_cancel();
							shiny_updates_complete();
						});
						jQuery('#updraft-backupnow-inpage-modal').on('dialogopen', function(event) {
							var $dialog = jQuery('#updraft-backupnow-inpage-modal').parent();
							var z_index = $dialog.css('z-index');
							if (z_index < 10001) {
								$dialog.css('z-index', 10002);
							}
						});
						jQuery('#updraft-backupnow-inpage-modal').dialog('open');
						jQuery('#updraft_inpage_prebackup').show();

					}

					<?php if (version_compare($wp_version, '3.3', '>=')) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable ?>
					// Bulk action form
					var $bulk_action_form = jQuery('#bulk-action-form');
					// The multisite network themes page - the bulk action form has no ID
					// N.B. - There aren't yet any shiny updates for themes (at time of coding - WP 4.4) - so, this is for the future
					var $theme_bulk_form = jQuery('body.themes-php.multisite.network-admin form #bulk-action-selector-top');
					if ($theme_bulk_form.length > 0) {
						$theme_bulk_form = $theme_bulk_form.parents('form').first();
						jQuery.extend($bulk_action_form, $theme_bulk_form);
					}
					
					$bulk_action_form.on('submit', function(e) {
						if ((!shiny_updates && $theme_bulk_form.length == 0) || updraft_bulk_updates_proceed) { return; }
						var $checkbox, plugin, slug;

						if (jQuery('#bulk-action-selector-top').val() == 'update-selected') {

							var are_there_any = false;
							jQuery('input[name="checked[]"]:checked').each(function(index, elem) {
								$checkbox = jQuery(elem);
								plugin = $checkbox.val();
								slug = $checkbox.parents('tr').prop('id');
								are_there_any = true;
							});
							// Shiny updates unchecks the check boxes. So, we also need to check the queue.
							if (!are_there_any && shiny_updates && wp.updates.<?php echo $queue_variable;?>.length == 0) { return; }

							// The 0 here is because shiny updates have been disabled on bulk action forms for now
							// And they also don't exist on themes at all. So, some things may need to change here when they do, or when they differ
							// TODO: Need to check whether a) that statement about themes is still true on WP 4.6, and b) whether this still works for plugins and c) make it work for themes, if they have now been added
							updates_intercept(e, this, false, 0, 'plugin');
							// Remove lock, for the same reason - otherwise, the "do you really want to move away?" message pops up.
							something_happening = false;
							if (shiny_updates) {
								wp.updates.<?php echo $lock_variable; ?> = false;
								updraft_we_locked_it = false;
							}
						}
					});

					$('tr.plugin-update-tr').on('click', 'a', function(e) {
						var type = 'plugin';
						// Yes, the network multisite bulk admin theme really does use this CSS class for the rows
						if (is_network_multisite_bulk_themes_page) type = 'theme';
						updates_intercept(e, this, true, shiny_updates, type);
					});
					
					$(window).on('message', function(event) {
						var originalEvent = event.originalEvent, expectedOrigin = document.location.protocol + '//' + document.location.host, message, selector = '';
						if ( originalEvent.origin !== expectedOrigin ) return;
						try {
							message = JSON.parse(originalEvent.data);
						} catch (e) {
							return;
						}
						if (!message || 'undefined' === typeof message.action) return;
						switch (message.action) {
							case 'update-plugin-via-update-now-link-text':
								window.tb_remove();
								if (wp.updates) wp.updates.<?php echo $lock_variable; ?> = true;
								if (message.data.slug) selector = 'tr.plugin-update-tr[data-slug="'+message.data.slug+'"] a.update-link';
								if (message.data.plugin) {
									if (selector) selector += ', ';
									selector += 'tr.plugin-update-tr a[href*="action=upgrade-plugin&plugin='+message.data.plugin+'"]';
								}
								$(selector).trigger('click');
							break;
						}
					});
					if (-1 !== window.location.pathname.indexOf('plugin-install.php')) {
						$(window).on('load', function(e) {
							var plugin_update_from_iframe_events = $._data(document.querySelector('div#plugin-information-footer a#plugin_update_from_iframe, div#plugin-information-footer a.button'), 'events'), plugin_update_from_iframe_event_handlers = [];
							if ("object" === typeof plugin_update_from_iframe_events && Object.prototype.hasOwnProperty.call(plugin_update_from_iframe_events, 'click') && "[object Array]" === Object.prototype.toString.call(plugin_update_from_iframe_events.click)) {
								for (var idx in plugin_update_from_iframe_events.click) {
									// store all event handlers that are bound to $('#plugin_update_from_iframe') to an array variable (plugin_update_from_iframe_event_handlers)
									plugin_update_from_iframe_event_handlers.push(plugin_update_from_iframe_events.click[idx].handler);
								}
							}
							$('div#plugin-information-footer a#plugin_update_from_iframe, div#plugin-information-footer a.button').off('click');
							$('div#plugin-information-footer a#plugin_update_from_iframe, div#plugin-information-footer a.button').on('click', function(e) {
								e.preventDefault();
								var target = window.parent === window ? null : window.parent;
								$.support.postMessage = !! window.postMessage;
								if (false === $.support.postMessage || null === target || -1 !== window.parent.location.pathname.indexOf('update-core.php')) return;
								for	(var idx in plugin_update_from_iframe_event_handlers) {
									// it's going to execute all event handlers that previously were bound to $('div#plugin-information-footer a#plugin_update_from_iframe, div#plugin-information-footer a.button') and were set to off
									if ("function" === typeof plugin_update_from_iframe_event_handlers[idx]) plugin_update_from_iframe_event_handlers[idx].call(this, e);
								}
								message = {
									action: 'update-plugin-via-update-now-link-text',
									data: {
										// get and extract query string args from the popup window and look for a plugin arg value and send it along with other data in a message
										// e.g. window.location.search returns ?action=upgrade-plugin&plugin=updraftplus%2Fupdraftplus.php&_wpnonce=108e986d01
										plugin: $(this).data('plugin') ? $(this).data('plugin') : decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent('plugin').replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1")),
										slug: $(this).data('slug')
									}
								};
								target.postMessage(JSON.stringify(message), window.location.origin);
							});
						});
					}
					
					// See: https://core.trac.wordpress.org/ticket/37512
					
					var events_to_hook = 'wp-theme-updating wp-plugin-deleting wp-theme-deleting wp-plugin-bulk-update-selected wp-plugin-bulk-delete-selected';
					
					// On the network admin themes page, there are bulk updates, and we want to hook those - but not wp-theme-updating, as that interferes (we have our own way of handling the bulk updates).
					// wp-theme-updating 
					if (is_network_multisite_bulk_themes_page) events_to_hook = 'wp-plugin-deleting wp-theme-deleting wp-plugin-bulk-update-selected wp-theme-bulk-update-selected wp-plugin-bulk-delete-selected';
					$(document).on(events_to_hook, function(e) {
						var event_type = e.type;
						// We don't suggest an automatic backup on deleting events; we only need to catch those in order to 
						// https://core.trac.wordpress.org/ticket/37216#comment:6
						if (event_type != 'wp-theme-updating' && event_type != 'wp-plugin-bulk-update-selected' && event_type != 'wp-theme-bulk-update-selected') {
							console.log('UpdraftPlus: '+event_type+' event triggered: letting pass (we do not get involved on these)');
							updraft_actually_proceeding = true;
							shiny_updates_proceed();
						} else if (updraft_actually_proceeding) {
							console.log("UpdraftPlus: "+event_type+" event triggered: letting pass (already handled)");
						} else {
							console.log("UpdraftPlus: "+event_type+" event triggered: intercepting");
							
							var entity_type = ('wp-theme-updating' == event_type || 'wp-theme-bulk-update-selected' == event_type) ? 'theme' : 'plugin';

							updates_intercept(e, this, false, shiny_updates, entity_type);
						}
						
					});

					<?php } ?>

					$('form.upgrade').on('submit', function() {
						var name = $(this).attr('name');
						var entity = 'plugins';
						if ('upgrade' == name) {
							entity = 'wpcore';
						} else if ('upgrade-themes' == name) {
							entity = 'themes';
						} else if ('upgrade-plugins' == name) {
							entity = 'plugins';
						} else {
							console.log("UpdraftPlus Error: do not know which entity to backup (will default to plugins): "+name);
						}
						console.log("UpdraftPlus: upgrade form submitted; form="+name+", entity="+entity);
						var doit = updraft_try_inpage('form[name="'+name+'"]', entity);
						if (doit) {
							$('form[name="'+name+'"]').append('<input type="hidden" name="updraftplus_noautobackup" value="1">');
						}
						return doit;
					});

				});

				function updraft_try_inpage(which_form_to_finally_submit, restrict) {
					if (updraft_autobackup_cleared_to_go) { return true; }
					var doit = jQuery('#updraft_autobackup').is(':checked');
					// If no auto-backup, then just carry on
					if (!doit) { return true;}
					if ('' == restrict) { restrict = '<?php echo esc_js($this->inpage_restrict);?>'; }
					updraft_backupnow_inpage_go(function() {
						jQuery(which_form_to_finally_submit).append('<input type="hidden" name="updraftplus_noautobackup" value="1">');
						// Prevent infinite backup loop
						updraft_autobackup_cleared_to_go = 1;
						if ('wpcore' == restrict) {
							jQuery(which_form_to_finally_submit).append('<input type="hidden" name="upgrade" value="Update Now">');
							jQuery(which_form_to_finally_submit).trigger('submit');
						} else {
							jQuery(which_form_to_finally_submit).trigger('submit');
						}
					}, restrict, 'autobackup');
					// Don't proceed with form submission yet - that's done in the callback
					return false;
				}
			</script>
			
			<?php
				if (is_object($updraftplus_admin)) {
					$updraftplus_admin->add_backup_scaffolding(__('Automatic backup before update', 'updraftplus'), array($this, 'backupnow_modal_contents'));
				} else {
					error_log("UpdraftPlus_Addon_Autobackup::admin_footer_inpage_backup() - unexpected failure for accessing UpdraftPlus_Admin object");
				}
			?>
			
		<?php
	}

	/**
	 * This is a callback function
	 */
	public function backupnow_modal_contents() {
		$this->auto_backup_form(true, 'updraft_autobackup', 'yes', false);
	}
	
	private function auto_backup_form($include_wrapper = true, $id = 'updraft_autobackup', $value = 'yes', $form_tags = true) {

		if ($include_wrapper) {
			if ($form_tags) {
			?>
				<h2>
				<?php
					echo __('UpdraftPlus Automatic Backups', 'updraftplus');
				?>
				</h2>
				<?php
			}
			
			if ($form_tags) {
			?>
				<form method="post" id="updraft_autobackup_form" onsubmit="return updraft_try_inpage('#updraft_autobackup_form', '');">
			<?php
			}
			?>
			<div id="updraft-autobackup" 
			<?php
				if ($form_tags) {
					echo 'class="updated"';
					?> style="
					<?php
						if ($form_tags) {
							echo 'border: 1px dotted; ';
						}
				}
			?>
			padding: 6px; margin:8px 0px; max-width: 540px;">
			<h3 style="margin-top: 0px;">
			<?php
				_e('Be safe with an automatic backup', 'updraftplus');
			?>
			</h3>
			<?php
		}
		?>
		<input <?php if (UpdraftPlus_Options::get_updraft_option('updraft_autobackup_default', true)) echo 'checked="checked"';?> type="checkbox" id="<?php echo $id;?>" value="<?php echo $value;?>" name="<?php echo $id;?>">
		<?php if (!$include_wrapper) echo '<br>'; ?>
		<label for="<?php echo $id;?>"><?php echo __('Backup (where relevant) plugins, themes and the WordPress database with UpdraftPlus before updating', 'updraftplus');?></label><br>
		<?php
		if ($include_wrapper) {
			?>
			<input checked="checked" type="checkbox" value="yes" name="updraft_autobackup_setdefault" id="updraft_autobackup_setdefault"> <label for="updraft_autobackup_setdefault"><?php
				_e('Remember this choice for next time (you will still have the chance to change it)', 'updraftplus');
			?></label><br><em>
			<?php
		}
		?>
		<p><a href="https://updraftplus.com/automatic-backups/" target="_blank"><?php _e('Read more about how this works...', 'updraftplus'); ?></a></p>
		<?php
		if ($include_wrapper) {
		?></em>
		<?php
			if ($form_tags) {
			?>
				<p><em><?php _e('Do not abort after pressing Proceed below - wait for the backup to complete.', 'updraftplus'); ?></em></p>
				<?php
			}
		?>
		<?php
			if ($form_tags) {
			?>
				<input class="button button-primary" style="clear:left; margin-top: 6px;" name="updraft_autobackup_answer" type="submit" value="<?php _e('Proceed with update', 'updraftplus');?>">
			<?php
			}
		?>
		</div>
		<?php
		if ($form_tags) echo '</form>';
		}
	}
}
