<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: morefiles:Back up more files, including WordPress core
Description: Creates a backup of WordPress core (including everything in that directory WordPress is in), and any other file/directory you specify too.
Version: 2.6
Shop: /shop/more-files/
Latest Change: 1.14.3
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

new UpdraftPlus_Addons_MoreFiles;

class UpdraftPlus_Addons_MoreFiles {

	private $wpcore_foundyet = 0;

	private $more_paths = array();

	public function __construct() {
		add_filter('updraft_backupable_file_entities', array($this, 'backupable_file_entities'), 10, 2);
		add_filter('updraft_backupable_file_entities_final', array($this, 'backupable_file_entities_final'), 10, 2);

		add_filter('updraftplus_restore_movein_wpcore', array($this, 'restore_movein_wpcore'), 10, 2);
		add_filter('updraftplus_backup_makezip_wpcore', array($this, 'backup_makezip_wpcore'), 10, 3);
		add_filter('updraftplus_restore_movein_more', array($this, 'restore_movein_more'), 10, 3);
		add_filter('updraftplus_backup_makezip_more', array($this, 'backup_makezip_more'), 10, 3);

		add_filter('updraftplus_defaultoption_include_more', '__return_false');
		add_filter('updraftplus_defaultoption_include_wpcore', '__return_false');

		add_filter('updraftplus_admin_directories_description', array($this, 'admin_directories_description'));
		
		add_filter('updraftplus_fileinfo_more', array($this, 'fileinfo_more'), 10, 2);

		add_filter('updraftplus_config_option_include_more', array($this, 'config_option_include_more'), 10, 2);
		add_filter('updraftplus_config_option_include_wpcore', array($this, 'config_option_include_wpcore'), 10, 2);

		add_action('updraftplus_restore_form_wpcore', array($this, 'restore_form_wpcore'));
		add_filter('updraftplus_checkzip_wpcore', array($this, 'checkzip_wpcore'), 10, 4);
		add_filter('updraftplus_checkzip_end_wpcore', array($this, 'checkzip_end_wpcore'), 10, 3);
		
		add_filter('updraftplus_browse_download_link', array($this, 'updraftplus_browse_download_link'));
		add_filter('updraftplus_command_get_zipfile_download', array($this, 'updraftplus_command_get_zipfile_download'), 10, 2);

		add_filter('updraftplus_dirlist_more', array($this, 'backup_more_dirlist'));
		add_filter('updraftplus_dirlist_wpcore', array($this, 'backup_wpcore_dirlist'));
		add_filter('updraftplus_get_disk_space_used_none', array($this, 'get_disk_space_used_none'), 10, 3);
		
		add_filter('updraftplus_include_wpcore_exclude', array($this, 'include_wpcore_exclude'));

		add_filter('updraftplus_include_manifest', array($this, 'more_include_manifest'), 10, 2);
		add_filter('updraftplus_more_rebuild', array($this, 'more_rebuild'), 10, 1);

		add_filter('updraftplus_restore_all_downloaded_postscan', array($this, 'restore_all_downloaded_postscan_more'), 10, 7);
		add_filter('updraftplus_restore_all_downloaded_postscan', array($this, 'restore_all_downloaded_postscan_selective_restore'), 10, 7);
		add_filter('updraft_backupable_file_entities_on_restore', array($this, 'backupable_file_entities_on_restore'), 10, 3);
		add_filter('updraftplus_restore_path', array($this, 'restore_path_more'), 10, 4);

		add_action('updraftplus_admin_enqueue_scripts', array($this, 'updraftplus_admin_enqueue_scripts'));
	}

	/**
	 * Runs upon the WP action updraftplus_admin_enqueue_scripts
	 */
	public function updraftplus_admin_enqueue_scripts() {
		add_action('admin_footer', array($this, 'admin_footer_more_files_js'));
	}

	/**
	 * WP filter updraftplus_get_disk_space_used_none
	 *
	 * @param String       $result              - the unfiltered value to return
	 * @param String       $entity              - the entity type
	 * @param Array|String $backupable_entities - a path or list of paths
	 *
	 * @return String                           - filtered result
	 */
	public function get_disk_space_used_none($result, $entity, $backupable_entities) {
		return ('more' == $entity && empty($backupable_entities['more'])) ? __('(None configured)', 'updraftplus') : $result;
	}
	
	public function updraftplus_browse_download_link() {
		return '<a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'" id="updraft_zip_download_item">'._x('Download', '(verb)', 'updraftplus').'</a>';
	}
	
	public function updraftplus_command_get_zipfile_download($result, $params) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		global $updraftplus;

		$zip_object = $updraftplus->get_zip_object_name();

		// Retrieve the information from our backup history
		$backup_history = UpdraftPlus_Backup_History::get_history();

		// Base name
		$file = $backup_history[$params['timestamp']][$params['type']];

		// Deal with multi-archive sets
		if (is_array($file)) $file = $file[$params['findex']];

		// Where it should end up being downloaded to
		$fullpath = $updraftplus->backups_dir_location().'/'.$file;

		$path = substr($params['path'], strpos($params['path'], DIRECTORY_SEPARATOR) + 1);

		if (file_exists($fullpath) && is_readable($fullpath) && filesize($fullpath)>0) {

			$zip = new $zip_object;

			if (!$zip->open($fullpath)) {
				return array('error' => 'UpdraftPlus: opening zip (' . $fullpath . '): failed to open this zip file.');
			} else {

				if ('UpdraftPlus_PclZip' == $zip_object) {
					$extracted = $zip->extract($updraftplus->backups_dir_location() . DIRECTORY_SEPARATOR . 'ziptemp' . DIRECTORY_SEPARATOR, $path);
				} else {
					$replaced_dir_sep_path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
					$extracted = $zip->extractTo($updraftplus->backups_dir_location() . DIRECTORY_SEPARATOR . 'ziptemp' . DIRECTORY_SEPARATOR, $replaced_dir_sep_path);
				}
				
				@$zip->close();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

				if ($extracted) {
					return array('path' => 'ziptemp'.DIRECTORY_SEPARATOR.$path);
				} else {
					return array('error' => 'UpdraftPlus: failed to extract (' . $path . ')');
				}
			}
		}

		return array('error' => 'UpdraftPlus: no such file or diretory (' . $fullpath . '): if the file does exist please make sure it is readable by the server.');
	}
	
	public function fileinfo_more($data, $ind) {
		if (!is_array($data) || !is_numeric($ind) || empty($this->more_paths) || !is_array($this->more_paths) || empty($this->more_paths[$ind])) return $data;

		global $updraftplus;
		$file_entities = $updraftplus->jobdata_get('job_file_entities');
		if (!isset($file_entities['more'])) return $data;

		return array(
			'html'=> '<br>'.__('Contains:', 'updraftplus').' '.htmlspecialchars($this->more_paths[$ind]),
			'text'=> '\r\n'.__('Contains:', 'updraftplus').' '.$this->more_paths[$ind]
		);
	}
	
	public function restore_form_wpcore() {

		?>
		<div id="updraft_restorer_wpcoreoptions" style="display:none; padding:12px; margin: 8px 0 4px; border: dashed 1px;"><h4 style="margin: 0px 0px 6px; padding:0px;"><?php echo sprintf(__('%s restoration options:', 'updraftplus'), __('WordPress Core', 'updraftplus')); ?></h4>

			<?php

			echo '<input name="updraft_restorer_wpcore_includewpconfig" id="updraft_restorer_wpcore_includewpconfig" type="checkbox" value="1"><label for="updraft_restorer_wpcore_includewpconfig"> '.__('Over-write wp-config.php', 'updraftplus').'</label> <a href="https://updraftplus.com/faqs/when-i-restore-wordpress-core-should-i-include-wp-config-php-in-the-restoration/" target="_blank">'.__('(learn more about this significant option)', 'updraftplus').'</a>';

			?>

			<script>
				jQuery('#updraft_restore_wpcore').on('change', function(){
					if (jQuery('#updraft_restore_wpcore').is(':checked')) {
						jQuery('#updraft_restorer_wpcoreoptions').slideDown();
					} else {
						jQuery('#updraft_restorer_wpcoreoptions').slideUp();
					}
				});
			</script>

			</div>
		<?php
	}

	public function admin_directories_description() {
		return '<div>'.__('The above files comprise everything in a WordPress installation.', 'updraftplus').'</div>';
	}

	public function backupable_file_entities($arr, $full_info) {
		if ($full_info) {
			$arr['wpcore'] = array(
				'path' => untrailingslashit(ABSPATH),
				'description' => apply_filters('updraft_wpcore_description', __('WordPress core (including any additions to your WordPress root directory)', 'updraftplus')),
				'htmltitle' => sprintf(__('WordPress root directory server path: %s', 'updraftplus'), ABSPATH)
			);
		} else {
			$arr['wpcore'] = untrailingslashit(ABSPATH);
		}
		return $arr;
	}

	/**
	 * N.B. &$err is also available as a fourth parameter if needed
	 *
	 * @param string $zipfile
	 * @param array  $mess
	 * @param array  $warn
	 * @return void
	 */
	public function checkzip_wpcore($zipfile, &$mess, &$warn) {
		if (!empty($this->wpcore_foundyet) && 3 == $this->wpcore_foundyet) return;

		if (!is_readable($zipfile)) {
			$warn[] = sprintf(__('Unable to read zip file (%s) - could not pre-scan it to check its integrity.', 'updraftplus'), basename($zipfile));
			return;
		}

		if ('.zip' == strtolower(substr($zipfile, -4, 4))) {

			if (!class_exists('UpdraftPlus_PclZip')) include(UPDRAFTPLUS_DIR.'/includes/class-zip.php');
			$zip = new UpdraftPlus_PclZip;

			if (!$zip->open($zipfile)) {
				$warn[] = sprintf(__('Unable to open zip file (%s) - could not pre-scan it to check its integrity.', 'updraftplus'), basename($zipfile));
				return;
			}

			// Don't put this in the for loop, or the magic __get() method gets called every time the loop goes round
			$numfiles = $zip->numFiles;

			if (false === $numfiles) {
				$warn[] = sprintf(__('Unable to read any files from the zip (%s) - could not pre-scan it to check its integrity. Zip error: (%s)', 'updraftplus'), basename($zipfile), $zip->last_error);
				return;
			}

			for ($i=0; $i < $numfiles; $i++) {
				$si = $zip->statIndex($i);
				if ('wp-admin/index.php' == $si['name']) {
					$this->wpcore_foundyet = $this->wpcore_foundyet | 1;
					if (3 == $this->wpcore_foundyet) return;
				}
				if ('xmlrpc.php' == $si['name'] || 'xmlrpc.php/xmlrpc.php' == $si['name']) {
					$this->wpcore_foundyet = $this->wpcore_foundyet | 2;
					if (3 == $this->wpcore_foundyet) return;
				}
			}

			@$zip->close();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		} elseif (preg_match('/\.tar(\.(gz|bz2))$/i', $zipfile)) {

			if (!class_exists('UpdraftPlus_Archive_Tar')) {
				if (false === strpos(get_include_path(), UPDRAFTPLUS_DIR.'/includes/PEAR')) set_include_path(UPDRAFTPLUS_DIR.'/includes/PEAR'.PATH_SEPARATOR.get_include_path());
				include_once(UPDRAFTPLUS_DIR.'/includes/PEAR/Archive/Tar.php');
			}

			$p_compress = null;
			if ('.tar.gz' == strtolower(substr($zipfile, -7, 7))) {
				$p_compress = 'gz';
			} elseif ('.tar.bz2' == strtolower(substr($zipfile, -8, 8))) {
				$p_compress = 'bz2';
			}

			$tar = new UpdraftPlus_Archive_Tar($zipfile, $p_compress);
			$list = $tar->listContent();

			foreach ($list as $file) {
				if (is_array($file) && isset($file['filename'])) {
					if ('wp-admin/index.php' == $file['filename']) {
						$this->wpcore_foundyet = $this->wpcore_foundyet | 1;
						if (3 == $this->wpcore_foundyet) return;
					} elseif ('xmlrpc.php' == $file['filename']) {
						$this->wpcore_foundyet = $this->wpcore_foundyet | 2;
						if (3 == $this->wpcore_foundyet) return;
					}
				}
			}
		}
	}

	public function checkzip_end_wpcore(&$mess, &$warn, &$err) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if (!empty($this->wpcore_foundyet) && 3 == $this->wpcore_foundyet) return;
		if (0 == ($this->wpcore_foundyet & 1)) $warn[] = sprintf(__('This does not look like a valid WordPress core backup - the file %s was missing.', 'updraftplus'), 'wp-admin/index.php').' '.__('If you are not sure then you should stop; otherwise you may destroy this WordPress installation.', 'updraftplus');
		if (0 == ($this->wpcore_foundyet & 2)) $warn[] = sprintf(__('This does not look like a valid WordPress core backup - the file %s was missing.', 'updraftplus'), 'xmlrpc.php').' '.__('If you are not sure then you should stop; otherwise you may destroy this WordPress installation.', 'updraftplus');
	}

	public function backupable_file_entities_final($arr, $full_info) {
		$path = UpdraftPlus_Options::get_updraft_option('updraft_include_more_path');
		if (is_array($path)) {
			$path = array_map('untrailingslashit', $path);
			if (1 == count($path)) $path = array_shift($path);
		} else {
			$path = untrailingslashit($path);
		}
		if ($full_info) {
			$arr['more'] = array(
				'path' => $path,
				'description' => __('Any other file/directory on your server that you wish to backup', 'updraftplus'),
				'shortdescription' => __('More Files', 'updraftplus'),
				'restorable' => true
			);
		} else {
			$arr['more'] = $path;
		}
		return $arr;
	}

	public function config_option_include_more($ret, $prefix) {

		if ($prefix) return $ret;

		$display = UpdraftPlus_Options::get_updraft_option('updraft_include_more') ? '' : 'style="display:none;"';
		$class = $display ? 'updraft-hidden' : '';
		
		$paths = UpdraftPlus_Options::get_updraft_option('updraft_include_more_path');
		
		if (!is_array($paths)) $paths = array($paths);

		$ret .= "<div id=\"updraft_include_more_options\" $display class=\"updraft_include_container $class\"><p class=\"updraft-field-description\">";

		$ret .= __('If you are not sure what this option is for, then you will not want it, and should turn it off.', 'updraftplus').' '.__('If using it, select a path from the directory tree below and then press confirm selection.', 'updraftplus');
		
		$ret .= ' '.__('Be careful what you select - if you select / then it really will try to create a zip containing your entire webserver.', 'updraftplus');

		$ret .= '</p>';

		$ret .= '<p id="updraft_include_more_paths_error"></p>';

		$ret .= '<div id="updraft_include_more_paths">';

		// Stops default empty path input being output to screen
		
		if (empty($paths)) {
			$paths = array('');
		} else {
			foreach ($paths as $ind => $path) {
				$ret .= '<div class="updraftplus-morefiles-row"><label for="updraft_include_more_path_'.$ind.'"></label>';
				$ret .= '<input type="text" data-mp_index="'.$ind.'" id="updraft_include_more_path_'.$ind.'" class="updraft_include_more_path" name="updraft_include_more_path[]" size="54" value="'.htmlspecialchars($path).'" title="'.htmlspecialchars($path).'"/> <a href="#" title="'.__('Edit', 'updraftplus').'" class="updraftplus-morefiles-row-edit dashicons dashicons-edit hidden-in-updraftcentral"></a> <a href="#" title="'.__('Remove', 'updraftplus').'" class="updraftplus-morefiles-row-delete dashicons dashicons-no"></a>';
				$ret .= '</div>';
			}
		}
		
		$ret .= '</div>';

		$ret .= $this->get_jstree_ui('options');

		$ret .= '</div>';
		
		return $ret;
	}

	/**
	 * Gives html for the wp core exclude settings. Called by the updraftplus_config_option_include_wpcore filter
	 *
	 * @param String $ret    the value passed by filter. by default, it is empty string
	 * @param String $prefix Prefix for the ID
	 * @return String html for exclude wp core
	 */
	public function config_option_include_wpcore($ret, $prefix) {
		global $updraftplus, $updraftplus_admin;
		
		$for_updraftcentral = defined('UPDRAFTCENTRAL_COMMAND') && UPDRAFTCENTRAL_COMMAND;

		if ($prefix) return $ret;

		$display = UpdraftPlus_Options::get_updraft_option('updraft_include_wpcore') ? '' : 'style="display:none;"';
		$exclude_container_class = 'updraft_include_wpcore_exclude';
		if (!$for_updraftcentral)  $exclude_container_class .= '_container';

		$ret .= "<div id=\"".$exclude_container_class."\" class=\"updraft_exclude_container\" $display>";

		$ret .= '<label class="updraft-exclude-label" for="updraft_include_wpcore_exclude">'.__('Exclude these:', 'updraftplus').'</label>';

		$exclude_input_type = $for_updraftcentral ? "text" : "hidden";
		$exclude_input_extra_attr = $for_updraftcentral ? 'title="'.__('If entering multiple files/directories, then separate them with commas. For entities at the top level, you can use a * at the start or end of the entry as a wildcard.', 'updraftplus').'" size="54"' : '';
		$ret .= '<input type="'.$exclude_input_type.'" id="updraft_include_wpcore_exclude" name="updraft_include_wpcore_exclude" value="'.esc_attr(UpdraftPlus_Options::get_updraft_option('updraft_include_wpcore_exclude')).'" '.$exclude_input_extra_attr.' />';
		if (!$for_updraftcentral) {
			$backupable_file_entities = $updraftplus->get_backupable_file_entities();
			$path = UpdraftPlus_Manipulation_Functions::wp_normalize_path($backupable_file_entities['wpcore']);
			$ret .= $updraftplus_admin->include_template('wp-admin/settings/file-backup-exclude.php', true, array(
				'prefix' => $prefix,
				'key' => 'wpcore',
				'include_exclude' => UpdraftPlus_Options::get_updraft_option('updraft_include_wpcore_exclude'),
				'path' => $path,
				'show_exclusion_options' => true
			));
		}
		$ret .= '</div>';

		return $ret;
	}

	/**
	 * Called via the WP filter updraftplus_dirlist_more
	 *
	 * @param String|Array $whichdirs - a path, or list of paths. Ultimately comes from the option updraft_include_more_path
	 *
	 * @return String|Array - filtered value
	 */
	public function backup_more_dirlist($whichdirs) {
		// Need to properly analyse the plugins, themes, uploads, content paths in order to strip them out (they may have various non-default manual values)

		global $updraftplus;

		$possible_backups = $updraftplus->get_backupable_file_entities(false);
		// We don't want to exclude the very thing we are backing up
		unset($possible_backups['more']);
		// We do want to exclude everything in WordPress and in wp-content
		$possible_backups['wp-content'] = WP_CONTENT_DIR;
		$possible_backups['wordpress'] = untrailingslashit(ABSPATH);

		$possible_backups_dirs = array();
		foreach ($possible_backups as $possback) {
			if (is_array($possback)) {
				foreach ($possback as $pb) $possible_backups_dirs[] = $pb;
			} else {
				$possible_backups_dirs[] = $possback;
			}
		}

		$possible_backups_dirs = array_unique($possible_backups_dirs);
		// $possible_backups_dirs = array_flip($possible_backups); // old

		$orig_was_array = is_array($whichdirs);
		if (!$orig_was_array) $whichdirs = array($whichdirs);
		$dirlist = array();

		foreach ($whichdirs as $whichdir) {

			if (!empty($whichdir) && (is_dir($whichdir) || is_file($whichdir))) {
				// Removing the slash is important (though ought to be redundant by here); otherwise path matching does not work
				$dirlist[] = $updraftplus->compile_folder_list_for_backup(untrailingslashit($whichdir), $possible_backups_dirs, array());
			} else {
				$dirlist[] = array();
				if (!empty($whichdir)) {
					$updraftplus->log("We expected to find something to back up at: ".$whichdir);
					$updraftplus->log($whichdir.': '.__("No backup of location: there was nothing found to back up", 'updraftplus'), 'warning');
				}
			}

		}

		return $orig_was_array ? $dirlist : array_shift($dirlist);

	}

	/**
	 * This function will build and keep track of a list of more files that will be backed up
	 *
	 * @param array   $whichdirs            - an array of directories that need to be backed up
	 * @param string  $backup_file_basename - the backup file basename
	 * @param integer $index                - the backup index
	 *
	 * @return array|boolean               - returns an array of created more file zips or false if none are created
	 */
	public function backup_makezip_more($whichdirs, $backup_file_basename, $index) {

		global $updraftplus, $updraftplus_backup;

		if (!is_array($whichdirs)) $whichdirs = array($whichdirs);

		$this->more_paths = array();

		$final_created = $updraftplus->jobdata_get('morefiles_temporary_final_created');
		if (!is_array($final_created)) $final_created = array();

		$first_linked_index = 0;
		
		// Oct 2018: changed the way more files are tracked, there are now two arrays:
		// more_locations: a numerical array of unique more file locations
		// more_map: a numerical array where array keys match the backup file and array values match an array key in the more_locations array
		// For tracking which "more files" configuration entry goes into which zip, to avoid useless activity (or worse, duplicate backups)
		$more_map = $updraftplus->jobdata_get('morefiles_linked_indexes');
		$more_locations = $updraftplus->jobdata_get('morefiles_more_locations');
		if (!is_array($more_map)) $more_map = array();
		if (!is_array($more_locations)) $more_locations = array();

		foreach ($whichdirs as $whichdir) {
			if (in_array($whichdir, $more_locations)) continue;

			// Actually create the thing
			$dirlist = $this->backup_more_dirlist($whichdir);

			if (count($dirlist)>0) {
				$this->more_paths[] = $whichdir;
				
				if (!in_array($whichdir, $more_locations)) {
					$more_locations[] = $whichdir;
					$updraftplus->jobdata_set('morefiles_more_locations', $more_locations);
				}
				
				if (!empty($more_map) && isset($more_map[$first_linked_index])) {
					$first_linked_index = $index = count($more_map);
				}

				$created = $updraftplus_backup->create_zip($dirlist, 'more', $backup_file_basename, $index, $first_linked_index);
				
				if (!empty($created)) {

					foreach ($created as $key => $name) {
						$more_map[$key] = array_search($whichdir, $more_locations);
					}
					$updraftplus->jobdata_set('morefiles_linked_indexes', $more_map);

					$keys = array_keys($created);
					$index = end($keys);
					$index++;
					$first_linked_index = $index;
				}
				
				if (is_string($created)) {
					$final_created[] = $created;
				} elseif (is_array($created)) {
					$final_created = array_merge($final_created, $created);
				} else {
					$updraftplus->log("$whichdir: More files backup: create_zip returned an error", 'warning', 'morefiles-'.md5($whichdir));
					// return false;
				}
			} else {
				$updraftplus->log("$whichdir: No backup of 'more' directory: there was nothing found to back up", 'warning', 'morefiles-empty-'.md5($whichdir));
				// return false;
			}

			$final_created = array_unique($final_created);
			$updraftplus->jobdata_set('morefiles_temporary_final_created', $final_created);
		}

		return (empty($final_created)) ? false : $final_created;
	}

	public function include_wpcore_exclude() {
		return explode(',', UpdraftPlus_Options::get_updraft_option('updraft_include_wpcore_exclude', ''));
	}

	public function backup_wpcore_dirlist($whichdir, $logit = false) {

		// Need to properly analyse the plugins, themes, uploads, content paths in order to strip them out (they may have various non-default manual values)

		global $updraftplus;

		if (false !== ($wpcore_dirlist = apply_filters('updraftplus_dirlist_wpcore_override', false, $whichdir))) return $wpcore_dirlist;

		$possible_backups = $updraftplus->get_backupable_file_entities(false);
		// We don't want to exclude the very thing we are backing up
		unset($possible_backups['wpcore']);
		// We do want to exclude everything in wp-content
		$possible_backups['wp-content'] = WP_CONTENT_DIR;
		
		$possible_backups_dirs = array();

		foreach ($possible_backups as $key => $dir) {
			if (is_array($dir)) {
				foreach ($dir as $ind => $rdir) {
					if (!empty($rdir)) $possible_backups_dirs[$rdir] = $key.$ind;
				}
			} else {
				if (!empty($dir)) $possible_backups_dirs[$dir] = $key;
			}
		}

		// Create an array of directories to be skipped
		$exclude = UpdraftPlus_Options::get_updraft_option('updraft_include_wpcore_exclude', '');
		if ($logit) $updraftplus->log("Exclusion option setting (wpcore): ".$exclude);
		// Make the values into the keys
		$wpcore_skip = array_flip(preg_split("/,/", $exclude));
		$wpcore_skip['wp_content'] = 0;

		// Removing the slash is important (though ought to be redundant by here); otherwise path matching does not work
		$wpcore_dirlist = $updraftplus->compile_folder_list_for_backup(untrailingslashit($whichdir), $possible_backups_dirs, $wpcore_skip);

		// This is not required to be a perfect test. The point is to make sure we do get WP core.
		// Not using this approach for now.
// if (true == apply_filters('updraftplus_backup_wpcore_dirlist_strict', false)) {
// $wpcore_valid = array('wp-admin', 'wp-includes', 'index.php', 'xmlrpc.php');
// foreach ($wpcore_dirlist as $dir) {
//
// }
// }

		return $wpcore_dirlist;

	}

	/**
	 * $whichdir will equal untrailingslashit(ABSPATH) (is ultimately sourced from our backupable_file_entities filter callback)
	 *
	 * @param  string $whichdir
	 * @param  string $backup_file_basename
	 * @param  string $index
	 * @return array
	 */
	public function backup_makezip_wpcore($whichdir, $backup_file_basename, $index) {

		global $updraftplus, $updraftplus_backup;

		// Actually create the thing

		$wpcore_dirlist = $this->backup_wpcore_dirlist($whichdir, true);

		if (count($wpcore_dirlist)>0) {
			$created = $updraftplus_backup->create_zip($wpcore_dirlist, 'wpcore', $backup_file_basename, $index);
			if (is_string($created) || is_array($created)) {
				return $created;
			} else {
				$updraftplus->log("WP Core backup: create_zip returned an error");
				return false;
			}
		} else {
			$updraftplus->log("No backup of WP core directories: there was nothing found to back up");
			$updraftplus->log(sprintf(__("No backup of %s directories: there was nothing found to back up", 'updraftplus'), __('WordPress Core', ' updraftplus')), 'error');
			return false;
		}

	}


	/**
	 * $wp_dir is trailingslashit($wp_filesystem->abspath())
	 * Must only use $wp_filesystem methods
	 * $working_dir is the directory which contains the backup entity/ies. It is a child of wp-content/upgrade
	 * We need to make sure we do not over-write any entities that are restored elsewhere. i.e. Don't touch plugins/themes etc. - but use backupable_file_entities in order to be fully compatible, but with an additional over-ride of touching nothing inside WP_CONTENT_DIR. Can recycle code from the 'others' handling to assist with this.
	 *
	 * @param  string $working_dir
	 * @param  string $wp_dir
	 * @return array
	 */
	public function restore_movein_wpcore($working_dir, $wp_dir) {

		global $updraftplus_restorer;

		// On subsequent archives of a multi-archive set, don't move anything; but do on the first
		$preserve_existing = isset($updraftplus_restorer->been_restored['wpcore']) ? Updraft_Restorer::MOVEIN_COPY_IN_CONTENTS : Updraft_Restorer::MOVEIN_OVERWRITE_NO_BACKUP;

		return $updraftplus_restorer->move_backup_in($working_dir, $wp_dir, $preserve_existing, array(basename(WP_CONTENT_DIR)), 'wpcore');

	}

	/**
	 * This function is called via a filter and will restore the more files backups
	 * Must only use $wp_filesystem methods
	 * We need to make sure we do not over-write any entities that are restored elsewhere. i.e. Don't touch plugins/themes etc. - but use backupable_file_entities in order to be fully compatible, but with an additional over-ride of touching nothing inside WP_CONTENT_DIR. Can recycle code from the 'others' handling to assist with this.
	 *
	 * @param string $working_dir       - the directory which contains the backup entity/ies. it is a child of wp-content/upgrade
	 * @param string $wp_dir            - is trailingslashit($wp_filesystem->abspath())
	 * @param string $wp_filesystem_dir - the location we want to restore the more file backup
	 *
	 * @return boolean|WP_Error - boolean for success or a wordpress error
	 */
	public function restore_movein_more($working_dir, $wp_dir, $wp_filesystem_dir) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		global $updraftplus_restorer;

		// On subsequent archives of a multi-archive set, don't move anything; but do on the first
		$preserve_existing = isset($updraftplus_restorer->been_restored['more']) ? Updraft_Restorer::MOVEIN_COPY_IN_CONTENTS : Updraft_Restorer::MOVEIN_OVERWRITE_NO_BACKUP;

		return $updraftplus_restorer->move_backup_in($working_dir, trailingslashit($wp_filesystem_dir), $preserve_existing, array(basename(WP_CONTENT_DIR)), 'more');

	}

	/**
	 * This function is called via a filter and will search the backup set for the correct more files path for that backup file
	 *
	 * @param array  $path        - the path to be filtered
	 * @param string $backup_file - the backup file we are restoring
	 * @param array  $backup_set  - the backup set being restored
	 * @param string $type        - the type of backup file
	 *
	 * @return string - the filtered path
	 */
	public function restore_path_more($path, $backup_file, $backup_set, $type) {
		
		if ('more' != $type) return $path;

		if (!isset($backup_set['morefiles_linked_indexes']) || !isset($backup_set['morefiles_more_locations'])) return $path;

		if (false !== ($file_key = array_search($backup_file, $backup_set['more']))) {

			$location_key = $backup_set['morefiles_linked_indexes'][$file_key];

			return $backup_set['morefiles_more_locations'][$location_key];
		}

		return $path;
	}

	/**
	 * This function will filter and return a boolean to indicate if the backup should include a manifest or not
	 *
	 * @param boolean $include  - a boolean to indicate if we should include a manifest in the backup
	 * @param string  $whichone - the entity that this backup is
	 *
	 * @return boolean          - returns a boolean to indicate if we should include a manifest in the backup
	 */
	public function more_include_manifest($include, $whichone) {
		return ('more' == $whichone) ? true : $include;
	}

	/**
	 * This function will rebuild the more files linked indexes and more locations array if the backup history is missing this information and return the backup history otherwise returns false
	 *
	 * @param array $backup_history - the backup history
	 *
	 * @return array|boolean        - the modified backup history or false if theres no changes
	 */
	public function more_rebuild($backup_history) {

		$changes = false;
		
		foreach ($backup_history as $btime => $bdata) {
			if (!isset($bdata['more'])) continue;
			foreach ($bdata['more'] as $key => $filename) {
				if (!isset($bdata['morefiles_linked_indexes'])) $bdata['morefiles_linked_indexes'] = array();
				if (!isset($bdata['morefiles_more_locations'])) $bdata['morefiles_more_locations'] = array();
				if (isset($bdata['morefiles_linked_indexes'][$key])) continue;

				$morefile_path = $this->more_manifest_file_directory('', $filename);

				if ('' == $morefile_path) continue;

				$changes = true;

				$morefile_path_key = array_search($morefile_path, $bdata['morefiles_more_locations']);

				if (false !== $morefile_path_key) {
					$bdata['morefiles_linked_indexes'][$key] = $morefile_path_key;
				} else {
					if (!in_array($morefile_path, $bdata['morefiles_more_locations'])) $bdata['morefiles_more_locations'][] = $morefile_path;
					$bdata['morefiles_linked_indexes'][$key] = count($bdata['morefiles_more_locations']) - 1;
				}
			}

			// We sort these here so that they appear in order in the backup history which makes for easier debugging
			ksort($bdata['morefiles_more_locations']);
			ksort($bdata['morefiles_linked_indexes']);
			
			$backup_history[$btime] = $bdata;
		}

		if ($changes) return $backup_history;
		
		return false;
	}

	/**
	 * This function will check the passed in more files zip to see if it includes a manifest file and if so it will extract the directory that them files belong to and return it, otherwise it will return the default value passed in.
	 *
	 * @param string $path     - the default passed in path
	 * @param string $filename - the more file zip name
	 *
	 * @return string          - the default path if no manifest is found or the location of the more files if it is found
	 */
	private function more_manifest_file_directory($path, $filename) {
		global $updraftplus;

		$zip_object = $updraftplus->get_zip_object_name();
		$fullpath = $updraftplus->backups_dir_location() . DIRECTORY_SEPARATOR . $filename;

		if (file_exists($fullpath) && is_readable($fullpath) && filesize($fullpath) > 0) {
			$zip = new $zip_object;
			
			$zip_opened = $zip->open($fullpath);

			if (true !== $zip_opened) {
				return array('error' => 'UpdraftPlus: opening zip (' . $fullpath . '): failed to open this zip file (object='.$zip_object.', code: '.$zip_opened.')');
			} else {

				if ('UpdraftPlus_PclZip' == $zip_object) {
					$zip->extract($updraftplus->backups_dir_location() . DIRECTORY_SEPARATOR . 'ziptemp' . DIRECTORY_SEPARATOR, 'updraftplus-manifest.json');
				} else {
					$zip->extractTo($updraftplus->backups_dir_location() . DIRECTORY_SEPARATOR . 'ziptemp' . DIRECTORY_SEPARATOR, 'updraftplus-manifest.json');
				}
				
				$manifest_path = $updraftplus->backups_dir_location() . DIRECTORY_SEPARATOR . 'ziptemp' . DIRECTORY_SEPARATOR . 'updraftplus-manifest.json';
				$manifest = json_decode(file_get_contents($manifest_path), true);

				$path = $manifest['directory'];

				@$zip->close();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			}
		}

		return $path;
	}

	/**
	 * WordPress action updraftplus_restore_all_downloaded_postscan called during the restore process.
	 *
	 * The last four parameters can be edited in-place.
	 *
	 * @param Array   $backups   - list of backups
	 * @param Integer $timestamp - the timestamp (epoch time) of the backup being restored
	 * @param Array   $entities  - elements being restored (as the keys of the array)
	 * @param Array   $info      - information about the backup being restored
	 * @param Array   $mess      - array of informational-level messages
	 * @param Array   $warn      - array of warning-level messages
	 * N.B. An extra parameter $err is also available after $warn
	 */
	public function restore_all_downloaded_postscan_more($backups, $timestamp, $entities, &$info, &$mess, &$warn) {
		if (!isset($entities['more']) || !isset($backups[$timestamp]['more'])) return;

		$not_found = false;
		$more_ui = '<div class="updraft_include_more_paths">';

		if (empty($backups[$timestamp]['morefiles_linked_indexes']) || empty($backups[$timestamp]['morefiles_more_locations'])) {
			$not_found = true;
			foreach ($backups[$timestamp]['more'] as $key => $value) {
				$more_ui .= $this->get_jstree_row_ui($key, $value, '', true, false, $key);
			}
		} else {
			
			$last_index = -1;
			$split_set = false;

			foreach ($backups[$timestamp]['more'] as $key => $value) {
				if (!isset($backups[$timestamp]['morefiles_linked_indexes'][$key])) {
					$not_found = true;
					$more_ui .= $this->get_jstree_row_ui($key, $value, '', true, false, $key);
				}
				
				$index = $backups[$timestamp]['morefiles_linked_indexes'][$key];

				$split_set = $last_index === $index ? true : false;
				
				if (!file_exists(dirname($backups[$timestamp]['morefiles_more_locations'][$index]))) {
					$not_found = true;
					$more_ui .= $this->get_jstree_row_ui($key, $value, $backups[$timestamp]['morefiles_more_locations'][$index], true, $split_set, $index);
				} else {
					$more_ui .= $this->get_jstree_row_ui($key, $value, $backups[$timestamp]['morefiles_more_locations'][$index], false, $split_set, $index);
				}
				
				$last_index = $index;
			}
		}
		$more_ui .= $this->get_jstree_ui('restore');
		$more_ui .= '</div>';

		if ($not_found) {
			$warn[] = __('The original filesystem location for some of the following items was not found. Please select where you want these backups to be restored to.', 'updraftplus');
		}
		$mess[] = __('Please select the more files backups that you wish to restore:', 'updraftplus');
		$info['addui'] = empty($info['addui']) ? $more_ui : $info['addui'] . '<br>' . $more_ui;
	}

	/**
	 * WordPress action updraftplus_restore_all_downloaded_postscan called during the restore process.
	 *
	 * The last three parameters can be edited in-place.
	 *
	 * @param Array   $backups   - list of backups
	 * @param Integer $timestamp - the timestamp (epoch time) of the backup being restored
	 * @param Array   $entities  - elements being restored (as the keys of the array)
	 * @param Array   $info      - information about the backup being restored
	 * @param Array   $mess      - array of informational-level messages
	 * @param Array   $warn      - array of warning-level messages
	 * N.B. An extra parameter $err is also available after $warn
	 */
	public function restore_all_downloaded_postscan_selective_restore($backups, $timestamp, $entities, &$info, &$mess, &$warn) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		$selective_restore_types = array(
			'plugins',
			'themes',
		);

		foreach ($selective_restore_types as $type) {
			if (!isset($entities[$type]) || !isset($backups[$timestamp][$type])) continue;

			$backup_entities = $this->get_backup_contents_list($backups[$timestamp][$type]);
	
			if (empty($backup_entities)) continue;
			$selective_restore_ui = $this->get_entity_selective_restore_ui($backup_entities, $type);
	
			$info['addui'] = empty($info['addui']) ? $selective_restore_ui : $info['addui'] . '<br>' . $selective_restore_ui;
		}
	}

	/**
	 * This function will build the selective entity restore UI and return it
	 *
	 * @param Array  $entities - an array of entities to restore (plugins, themes)
	 * @param String $type     - the type of entity (plugins, themes)
	 *
	 * @return String - returns the selective restore UI for this entity
	 */
	private function get_entity_selective_restore_ui($entities, $type) {

		global $updraftplus;

		$backupable_entities = $updraftplus->get_backupable_file_entities(false, true);
		$description = isset($backupable_entities[$type]['singular_description']) ? $backupable_entities[$type]['singular_description'] : $type;
		
		$php_max_input_vars = ini_get("max_input_vars"); // phpcs:ignore PHPCompatibility.IniDirectives.NewIniDirectives.max_input_varsFound -- does not exist in PHP 5.2

		$php_max_input_vars_exceeded = false;
		if (!empty($php_max_input_vars) && count($entities) >= 0.90 * $php_max_input_vars) {
			$php_max_input_vars_exceeded = true;
			// If the amount of tables exceed 90% of the php max input vars then truncate the list to 50% of the php max input vars value
			$entities = array_splice($entities, 0, $php_max_input_vars / 2);
		}

		$selective_restore_ui = '<div class="notice below-h2 updraft-restore-option">';
		$selective_restore_ui .= '<p>'.sprintf(__('If you do not want to restore all your %s files, then de-select the unwanted ones here. Files not chosen will not be replaced.', 'updraftplus'), strtolower($description)).'(<a href="#" id="updraftplus_restore_'.$type.'_showmoreoptions">...</a>)</p>';

		$selective_restore_ui .= '<div class="updraftplus_restore_'.$type.'_options_container" style="display:none;">';

		$selective_restore_ui .= '<a class="updraft_restore_select_all_'.$type.'" href="#">'.__('Select all', 'updraftplus').'</a>';
		$selective_restore_ui .= ' | <a class="updraft_restore_deselect_all_'.$type.'" href="#">'.__('Deselect all', 'updraftplus').'</a><br><br>';

		if ($php_max_input_vars_exceeded) {
			$all_other_entity_title = sprintf(__('The amount of %s files scanned is near or over the php_max_input_vars value so some %s files maybe truncated. This option will ensure all %s files not found will be restored.', 'updraftplus'), strtolower($description));
			$selective_restore_ui .= '<input class="updraft_restore_'.$type.'_options" id="updraft_restore_'.$type.'_udp_all_other_'.$type.'" checked="checked" type="checkbox" name="updraft_restore_'.$type.'_options[]" value="udp_all_other_'.$type.'"> ';
			$selective_restore_ui .= '<label for="updraft_restore_'.$type.'_udp_all_other_'.$type.'"  title="'.$all_other_entity_title.'">'.sprintf(__('Restore all %s not listed below', 'updraftplus'), strtolower($description)).'</label><br>';
		}

		foreach ($entities as $entity) {
			$selective_restore_ui .= '<input class="updraft_restore_'.$type.'_options" id="updraft_restore_'.$type.'_'.$entity.'" checked="checked" type="checkbox" name="updraft_restore_'.$type.'_options[]" value="'.$entity.'"> ';
			$selective_restore_ui .= '<label for="updraft_restore_'.$type.'_'.$entity.'">'.$entity.'</label><br>';
		}
		$selective_restore_ui .= '</div></div>';

		return $selective_restore_ui;
	}

	/**
	 * This function will get the top level folders (plugins, themes) from the passed in backup archives
	 *
	 * @param Array $backups - an array of backup archives
	 *
	 * @return Array - an array of top level entities inside the backups (plugin, themes)
	 */
	private function get_backup_contents_list($backups) {

		global $updraftplus;
		
		if (!class_exists('UpdraftPlus_PclZip')) include(UPDRAFTPLUS_DIR.'/includes/class-zip.php');

		$updraft_dir = $updraftplus->backups_dir_location();

		$entities = array();

		foreach ($backups as $file) {
			$zip = new UpdraftPlus_PclZip;

			$zipfile = $updraft_dir . '/' . $file;

			if (!$zip->open($zipfile)) return $entities;
	
			// Don't put this in the for loop, or the magic __get() method gets called every time the loop goes round
			$numfiles = $zip->numFiles;

			if (false === $numfiles) $updraftplus->log("get_backup_contents_list(): could not read any files from the zip: (".basename($zipfile).") Zip error: (".$zip->last_error.")");
	
			for ($i=0; $i < $numfiles; $i++) {
				$si = $zip->statIndex($i);
				$folders = explode('/', $si['name']);
				$entity = isset($folders[1]) ? $folders[1] : false;

				// We don't want hidden file system files showing up in the list of entities to restore
				if (!$entity || "." === substr($entity, 0, 1) || "index.php" == $entity) continue;

				if (!in_array($entity, $entities)) {
					$entities[] = $entity;
				}
			}
	
			@$zip->close();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		}

		sort($entities);

		return $entities;
	}

	/**
	 * This function is called via the filter updraft_backupable_file_entities_on_restore and is used to filter the restore paths found in $backupable_entities and replace them with the paths saved in the backup set.
	 *
	 * @param  array $backupable_entities - an array of backupable entities and their restore paths
	 * @param  array $restore_options     - the restore options
	 * @param  array $backup_set          - the backup set being restored
	 *
	 * @return array - the filtered backupable_entities array
	 */
	public function backupable_file_entities_on_restore($backupable_entities, $restore_options, $backup_set) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		if (isset($backupable_entities['more']) && isset($backup_set['morefiles_more_locations'])) $backupable_entities['more']['path'] = $backup_set['morefiles_more_locations'];

		return $backupable_entities;
	}

	/**
	 * This function will output the more files jstree row ui for each entity thats not found
	 *
	 * @param integer $key       - the backup file index
	 * @param string  $file      - the backup file name
	 * @param string  $path      - the path to the backup location
	 * @param boolean $not_found - a bool to indicate if the backup location was found or not
	 * @param boolean $split_set - a bool to indicate if this is part of a split set
	 * @param integer $index     - the more files linked indexes index
	 *
	 * @return string - returns the ui
	 */
	public function get_jstree_row_ui($key, $file, $path, $not_found, $split_set, $index) {

		$html = '';
		$hidden = $split_set ? 'style="display:none;"' : '';
		$location = empty($path) ? $file : $path;
		
		$index_checkbox = '<input type="checkbox"  ' . $hidden . ' data-mp_index="' . esc_attr($key) . '" data-set_index="' . esc_attr($index) . '" id="updraft_include_more_index_' . esc_attr($key) . '" class="updraft_include_more_index" name="updraft_include_more_index[]" value="' . esc_attr($key) . '"/>';
		
		if ($split_set) return $index_checkbox;

		if ($not_found) {
			$html .= '<div class="updraftplus-morefiles-row"><label for="updraft_include_more_path_restore_' . esc_attr($key) . '">' . __('Restore location does not exist on the filesystem for:', 'updraftplus') . '<br>' . esc_attr($location) . '</label>';
		} else {
			$html .= '<div class="updraftplus-morefiles-row"><label for="updraft_include_more_path_restore_' . esc_attr($key) . '">' . __('Restore location found for:', 'updraftplus') . '<br>' . esc_attr($location) . '</label>';
		}
		$html .= '<br>'.$index_checkbox;
		$html .= '<input type="text" data-mp_index="' . esc_attr($key) . '" data-set_index="' . esc_attr($index) . '" id="updraft_include_more_path_restore_' . esc_attr($key) . '" class="updraft_include_more_path" name="updraft_include_more_path[]" size="54" value="' . esc_attr($path) . '" title="' . esc_attr($path) . '" readonly="readonly"/> <a href="#" title="' . __('Edit', 'updraftplus') . '" class="updraftplus-morefiles-row-edit dashicons dashicons-edit hidden-in-updraftcentral"></a></div>';
		
		return $html;
	}

	/**
	 * This function will output the more file jstree ui and assign unique id's using the passed in page
	 *
	 * @param string $page - the page we are displaying the ui on
	 *
	 * @return string - the more files jstree ui
	 */
	public function get_jstree_ui($page) {
		$ret = '';
		if ('options' == $page) $ret .= '<div><a href="#" id="updraft_include_more_paths_another" class="updraft_icon_link"><span class="dashicons dashicons-plus"></span>' . __('Add directory...', 'updraftplus') . '</a></div>';
		$ret .= '<div id="updraft_more_files_container_'. esc_attr($page) .'" class="hidden-in-updraftcentral" style="clear:left;">
					<div id="updraft_jstree_container_'. esc_attr($page) .'">
						<button class="button" id="updraft_parent_directory_'. esc_attr($page) .'" title="' . __('Go up a directory', 'updraftplus') . '"><span class="dashicons dashicons-arrow-up-alt"></span>' . __('Go up a directory', 'updraftplus') . '</button>
						<div id="updraft_more_files_jstree_'. esc_attr($page).'"></div>
					</div>
					<div id="updraft_jstree_buttons_'. esc_attr($page).'">
						<button class="button" id="updraft_jstree_cancel_'. esc_attr($page).'">' . __('Cancel', 'updraftplus') . '</button> 
						<button class="button button-primary" id="updraft_jstree_confirm_'. esc_attr($page) .'">' . __('Confirm', 'updraftplus') . '</button>
					</div>
				</div>';
		return $ret;
	}

	/**
	 * This function will output any needed js for the more files addon.
	 *
	 * @return void
	 */
	public function admin_footer_more_files_js() {
		?>
		<script>
		jQuery(function() {
			<?php
				$paths = UpdraftPlus_Options::get_updraft_option('updraft_include_more_path');
				if (!is_array($paths)) $paths = array($paths);
				$maxind = max(count($paths) - 1, 1);
				$maxind++;
				$edit = esc_js(__('Edit', 'updraftplus'));
				$remove = esc_js(__('Remove', 'updraftplus'));
				$placeholder = esc_js(__('Please choose a file or directory', 'updraftplus'));
			?>
			var updraftplus_morefiles_lastind = <?php echo $maxind; ?>;
			var edit = "<?php echo $edit; ?>";
			var remove = "<?php echo $remove; ?>";
			var placeholder = "<?php echo $placeholder; ?>";
			jQuery('#updraft_include_more').on('click', function() {
				if (jQuery('#updraft_include_more').is(':checked')) {
					jQuery('#updraft_include_more_options').slideDown();
				} else {
					jQuery('#updraft_include_more_options').slideUp();
				}
			});

			jQuery('#updraft_include_more_paths_another').on('click', function(e) {
				e.preventDefault();
				updraftplus_morefiles_lastind++;
				jQuery('#updraft_include_more_paths').append('<div class="updraftplus-morefiles-row"><label for="updraft_include_more_path_' + updraftplus_morefiles_lastind + '"></label><input type="text" class="updraft_more_path_editing" id="updraft_include_more_path_' + updraftplus_morefiles_lastind + '" name="updraft_include_more_path[]" size="54" placeholder="' + placeholder + '" value="" title="' + placeholder + '" readonly/> <a href="#" title="' + edit + '" class="updraftplus-morefiles-row-edit dashicons dashicons-edit hidden-in-updraftcentral"></a> <a href="#" title="' + remove + '" class="updraftplus-morefiles-row-delete dashicons dashicons-no"></a></div>');
				more_files_jstree('filebrowser', '', false, 'options');
			});

			/**
			 * Creates the jstree and makes a call to the backend to dynamically get the tree nodes
			 * 
			 * @param {string} entity - the type of jstree this is
			 * @param {string} path - Optional path parameter if not passed in then ABSPATH will be used
			 * @param {bool} drop_directory - Optional parameter that if passed will remove the last directory level from the path, used for if you want to move up the directory tree from the root node
			 * @param {string} page - the page this jstree is being created on
			 */
			function more_files_jstree(entity, path, drop_directory, page) {
				if ('options' == page) jQuery('#updraft_include_more_paths_another').hide();
				jQuery('#updraft_more_files_container_'+page).insertAfter(jQuery('.updraft_more_path_editing').closest('.updraftplus-morefiles-row')).show();
				jQuery('#updraft_jstree_cancel_'+page).show();
				jQuery('#updraft_jstree_confirm_'+page).show();
				jQuery('.updraft_more_path_editing').data('previous-value', jQuery('.updraft_more_path_editing').val());
				jQuery('#updraft_more_files_jstree_'+page).jstree({
					"core": {
						"multiple": false,
						"data": function (nodeid, callback) {
							updraft_send_command('get_jstree_directory_nodes', {entity:entity, node:nodeid, path:path, drop_directory:drop_directory, page:page}, function(response) {
								if (response.hasOwnProperty('error')) {
									jQuery('#updraft_include_more_paths_error').text(response.error);
								} else {
									jQuery('#updraft_include_more_paths_error').text('');
									callback.call(this, response.nodes);
								}
							});
						}
					},
					'plugins' : ['sort','types'],
					'sort' : function(a, b) {
						a1 = this.get_node(a);
						b1 = this.get_node(b);
						if (a1.icon == b1.icon){
							return (a1.text > b1.text) ? 1 : -1;
						} else {
							return (a1.icon < b1.icon) ? 1 : -1;
						}
					},
				});

				// Detect change on the tree and update the input that has been marked as editing
				jQuery('#updraft_more_files_jstree_'+page).on("changed.jstree", function (e, data) {
					if ('restore' == page) {
						var group = jQuery('.updraft_more_path_editing').data('set_index');
						var textboxes = jQuery('input[type="text"][data-set_index="' + group + '"]');
						textboxes.val(data.selected[0]);
						textboxes.attr('title', data.selected[0]);
					} else {
						jQuery('.updraft_more_path_editing').val(data.selected[0]);
						jQuery('.updraft_more_path_editing').attr('title', data.selected[0]);
					}
				});
			}

			// Cancel the selection and clean up the UI
			jQuery('#updraft-restore-modal-stage2a').on('click', '#updraft_jstree_cancel_restore', function(e) {
				e.preventDefault();
				// reset value on cancel
				jQuery('.updraft_more_path_editing').val(jQuery('.updraft_more_path_editing').data('previous-value'));
				cleanup_jstree_ui('restore');
			});

			// Cancel the selection and clean up the UI
			jQuery('#updraft_jstree_cancel_options').on('click', function(e) {
				e.preventDefault();
				// reset value on cancel
				jQuery('.updraft_more_path_editing').val(jQuery('.updraft_more_path_editing').data('previous-value'));
				cleanup_jstree_ui('options');
			});

			// Grabs all selected paths and outputs them to the page ready to be saved then updates the UI and removes the tree object
			jQuery('#updraft-restore-modal-stage2a').on('click', '#updraft_jstree_confirm_restore', function(e) {
				e.preventDefault();
				cleanup_jstree_ui('restore');
			});

			// Grabs all selected paths and outputs them to the page ready to be saved then updates the UI and removes the tree object
			jQuery('#updraft_jstree_confirm_options').on('click', function(e) {
				e.preventDefault();
				cleanup_jstree_ui('options');
			});

			/**
			 * Cleans the UI and removes the jstree
			 */
			function cleanup_jstree_ui(page) {
				jQuery('#updraft_more_files_container_'+page).hide();
				jQuery('#updraft_jstree_cancel_'+page).hide();
				jQuery('#updraft_jstree_confirm_'+page).hide();
				if ('options' == page) jQuery('#updraft_include_more_paths_another').show();
				
				// if the new item is cancelled, remove the row
				if ('' == jQuery('.updraft_more_path_editing').val() && 'restore' != page) {
					jQuery('.updraft_more_path_editing').closest('.updraftplus-morefiles-row').remove();
				}

				jQuery('#updraft-restore-modal-stage2a .updraftplus-morefiles-row > .updraft_more_path_editing').removeClass('updraft_more_path_editing');
				jQuery('#updraft_include_more_paths > .updraftplus-morefiles-row > .updraft_more_path_editing').removeClass('updraft_more_path_editing');
				jQuery('#updraft_more_files_jstree_'+page).jstree("destroy").empty();
			}

			// Removes the current tree object and creates a new tree one directory above
			jQuery('#updraft-restore-modal-stage2a').on('click', '#updraft_parent_directory_restore', function(e) {
				e.preventDefault();
				jstree_parent_directory('restore');
			});

			// Removes the current tree object and creates a new tree one directory above
			jQuery('#updraft_parent_directory_options').on('click', function(e) {
				e.preventDefault();
				jstree_parent_directory('options');
			});

			/**
			 * Moves the jstree up a directory
			 *
			 * @param {string} page - the page this jstree is being created on
			 */
			function jstree_parent_directory(page) {
				var parent_node_id = jQuery('#updraft_more_files_jstree_'+page+' ul > li').first().attr('id');
				jQuery('#updraft_more_files_jstree_'+page).jstree("destroy").empty();
				more_files_jstree('filebrowser', parent_node_id, true, page);
			}

			jQuery('#updraft-restore-modal-stage2a').on('click', '.updraftplus-morefiles-row-edit', function(e) {
				e.preventDefault();
				// Clean up the UI just in case
				cleanup_jstree_ui('restore');

				var prow = jQuery(this).parent('.updraftplus-morefiles-row').children('input[type="text"]');
				jQuery(prow).addClass('updraft_more_path_editing');

				var drop_directory = true
				if (jQuery(prow).val() == '') drop_directory = '';
				more_files_jstree('filebrowser', jQuery(prow).val(), drop_directory, 'restore');
			});
			
			jQuery('#updraft_include_more_options').on('click', '.updraftplus-morefiles-row-edit', function(e) {
				e.preventDefault();
				
				// Clean up the UI just in case
				cleanup_jstree_ui('options');

				var prow = jQuery(this).parent('.updraftplus-morefiles-row').children('input');
				jQuery(prow).addClass('updraft_more_path_editing');

				var drop_directory = true
				if (jQuery(prow).val() == '') drop_directory = '';
				more_files_jstree('filebrowser', jQuery(prow).val(), drop_directory, 'options');
			});

			jQuery('#updraft_include_more_options').on('click', '.updraftplus-morefiles-row-delete', function(e) {
				e.preventDefault();

				var prow = jQuery(this).parent('.updraftplus-morefiles-row');

				// Check if the one being deleted is being edited if so cleanup the UI
				if (jQuery(prow).children('input').hasClass('updraft_more_path_editing')) {
					cleanup_jstree_ui('options');
				}
				jQuery(prow).slideUp().delay(400).remove();
			});

			add_readonly();

			function add_readonly() {
				jQuery('#updraft_include_more_paths').find('input').each(function() {
					jQuery(this).attr('readonly','readonly');
				});
			}

			jQuery('#updraft-restore-modal-stage2a').on('click', '.updraft_include_more_paths input[type="checkbox"][data-set_index]', function() {
				var checked = jQuery(this).prop('checked');
				var group = jQuery(this).data('set_index');
				var checkboxes = jQuery('input[type="checkbox"][data-set_index="' + group + '"]');
				var othercheckboxes = checkboxes.not(this);
				othercheckboxes.prop('checked', checked);
			});
		});
		</script>
		<?php
	}
}
