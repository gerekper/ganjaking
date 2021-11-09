<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: importer:Import a WordPress backup made by another backup plugin
Description: Import a backup made by other supported WordPress backup plugins (see shop page for a list of supported plugins)
Version: 3.1
Shop: /shop/importer/
Latest Change: 1.12.19
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

new UpdraftPlus_Addons_Importer;

class UpdraftPlus_Addons_Importer {

	public function __construct() {
		add_filter('updraftplus_accept_archivename', array($this, 'accept_archivename'));
		add_filter('updraftplus_accept_archivename_js', array($this, 'accept_archivename_js'));
		add_filter('updraftplus_accept_foreign', array($this, 'accept_foreign'), 10, 2);
		add_filter('updraftplus_importforeign_backupable_plus_db', array($this, 'importforeign_backupable_plus_db'), 10, 2);
		add_filter('updraftplus_foreign_gettime', array($this, 'foreign_gettime'), 10, 3);
		add_filter('updraftplus_foreign_dbfilename', array($this, 'foreign_dbfilename'), 10, 5);
		add_filter('updraftplus_accepted_foreign_index', array($this, 'accepted_foreign_index'), 10, 3);
		add_filter('updraftplus_foreign_allow_missing_entity', array($this, 'foreign_allow_missing_entity'), 10, 3);
		add_filter('updraftplus_select_wpcore_file_with_db', array($this, 'select_wpcore_file_with_db'), 10, 2);
		add_filter('updraftplus_if_foreign_then_premium_message', array($this, 'if_foreign_then_premium_message'));
	}

	public function foreign_allow_missing_entity($allow, $type, $foreign) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		// This plugin splits the backup over various sets
		return ('dropbox-wpadm' == $foreign) ? true : $allow;
	}
	
	/**
	 * This only does something with multi-zip sets
	 *
	 * @param  string $files
	 * @param  string $foreign
	 * @return string
	 */
	public function select_wpcore_file_with_db($files, $foreign) {
		switch ($foreign) {
			case 'dropbox-wpadm':
				// The database gets put in the last one. If they ever change this, we can just scan all the files instead.
				sort($files);
				$last_one = array_pop($files);
				return array($last_one);
				break;
		}
		return $files;
	}
	
	public function accepted_foreign_index($index, $entry, $accepted_foreign) {
		switch ($accepted_foreign) {
			case 'dropbox-wpadm':
			// e.g. example_com-full-2015_10_21_10_41-69.zip - where the last numeral runs from 1 upwards (they use multi-zip sets)
			if (preg_match('/^(.*)-full-([0-9]{4})_([0-9]{2})_([0-9]{2})_([0-9]{2})_([0-9]{2})-([0-9]+)\.zip$/i', $entry, $tmatch)) {
				$index = $tmatch[7] - 1;
			}
		}
		return $index;
	}
	
	public function if_foreign_then_premium_message() {

		$plugins = $this->accept_archivename(array());
		$supported = '';
		$already_added = array();
		foreach ($plugins as $plug) {
			if (!empty($plug['desc']) && !in_array($plug['desc'], $already_added)) {
				$supported .= ($supported) ? ', '.$plug['desc'] : $plug['desc'];
				$already_added[] = $plug['desc'];
			}
		}

		return '<p><a href="https://updraftplus.com/support/using-third-party-backups/" target="_blank">'.__('Was this a backup created by a different backup plugin? If so, then you might first need to rename it so that it can be recognized - please follow this link.', 'updraftplus').'</a></p><p>'.sprintf(__('Supported backup plugins: %s', 'updraftplus'), $supported).'</p>';
	}

	/**
	 * Given a backup type and filename, get the time
	 *
	 * @param  string $btime
	 * @param  string $accepted_foreign
	 * @param  string $entry
	 * @return string
	 */
	public function foreign_gettime($btime, $accepted_foreign, $entry) {
		$plugins = $this->accept_archivename(array());
		if (empty($plugins[$accepted_foreign])) return $btime;
		// mktime(): H, M, S, M, D, Y
		switch ($accepted_foreign) {
			case 'infinitewp':
				if (preg_match('/_(([0-9]{4})-([0-9]{2})-([0-9]{2})_).*\.zip$/i', $entry, $tmatch)) {
					return mktime(12, 0, 0, $tmatch[3], $tmatch[4], $tmatch[2]);
				}
				break;
			case 'backupwordpress':
			case 'backupwordpress2':
				// e.g. example-com-default-1-complete-2014-03-10-11-44-57.zip
				if (preg_match('/(([0-9]{4})-([0-9]{2})-([0-9]{2})-([0-9]{2})-([0-9]{2})-([0-9]{2}))\.zip$/i', $entry, $tmatch)) {
					return mktime($tmatch[5], $tmatch[6], $tmatch[7], $tmatch[3], $tmatch[4], $tmatch[2]);
				}
				break;
			case 'simple_backup':
				// e.g. db_backup_2014-03-15_133344.sql.gz | backup-2014-03-15-133345.zip
				// Note that a backup of both files and DB started at the same time may not have the same timestamp on both entities
				// Can also do tar and tar.gz and tar.bz2
				if (preg_match('/^(db_)?backup.([0-9]{4})-([0-9]{2})-([0-9]{2}).([0-9]{2})([0-9]{2})([0-9]{2})\.(zip|tar(\.(bz2|gz))?|sql(\.(gz))?)$/i', $entry, $tmatch)) {
					$btime = mktime($tmatch[5], $tmatch[6], $tmatch[7], $tmatch[3], $tmatch[4], $tmatch[2]);
					return $btime - ($btime % 60);
				}
				break;
			case 'backwpup':
				// e.g. backwpup_430908_2014-03-30_11-41-05.tar
				if (preg_match('/^backwpup_[0-9a-f]+_([0-9]{4})-([0-9]{2})-([0-9]{2})_([0-9]{2})-([0-9]{2})-([0-9]{2})\.(zip|tar|tar\.gz|tar\.bz2)/i', $entry, $tmatch)) {
					return mktime($tmatch[4], $tmatch[5], $tmatch[6], $tmatch[2], $tmatch[3], $tmatch[1]);
				}
				break;
			case 'dropbox-wpadm':
				// e.g. example_com-full-2015_10_21_10_41-69.zip - where the last numeral runs from 1 upwards (they use multi-zip sets)
				if (preg_match('/^(.*)-full-([0-9]{4})_([0-9]{2})_([0-9]{2})_([0-9]{2})_([0-9]{2})-([0-9]+)\.zip$/i', $entry, $tmatch)) {
					return mktime($tmatch[5], $tmatch[6], 0, $tmatch[3], $tmatch[4], $tmatch[2]);
				}
				break;
			case 'wpb2d':
				if (!class_exists('UpdraftPlus_PclZip') && file_exists(UPDRAFTPLUS_DIR.'/includes/class-zip.php')) include_once(UPDRAFTPLUS_DIR.'/includes/class-zip.php');
				global $updraftplus;
				$updraft_dir = trailingslashit($updraftplus->backups_dir_location());
				if (file_exists($updraft_dir.$entry) && class_exists('UpdraftPlus_PclZip')) {

					$transkey = 'ud_forgt_'.md5($entry.filesize($updraft_dir.$entry));
					$trans = get_transient($transkey);
					if ($trans > 0) return $trans;

					$zip = new UpdraftPlus_PclZip();
					$zip->ud_include_mtime();
					if (!$zip->open($updraft_dir.$entry)) {
						$updraftplus->log("Could not open zip file to examine (".$zip->last_error."); will remove: ".$entry);
						$btime = time();
					} else {

						// Don't put this in the for loop, or the magic __get() method gets called every time the loop goes round
						$numfiles = $zip->numFiles;

						$latest_mtime = -1;

						for ($i=0; $i < $numfiles; $i++) {
							$si = $zip->statIndex($i);
							if ('wp-content/backups/wordpress-db-backup.sql' == $si['name']) {
								@$zip->close();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
								$btime = $si['mtime'];
							} elseif (preg_match('#wp-content/backups/(.*)\.sql$#i', $si['name'], $matches)) {
								if ($si['mtime'] > $latest_mtime) {
									$latest_mtime = $si['mtime'];
									$btime = $si['mtime'];
								}
							}
						}
						@$zip->close();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					}
					set_transient($transkey, $btime, 86400*365);

					return (false !== $btime) ? $btime : filemtime($updraft_dir.$entry);
				}
				return time();
			break;
			case 'genericsql':
				global $updraftplus;
				$updraft_dir = $updraftplus->backups_dir_location();
				// Using filemtime prevents a new backup being discovered each time 'rescan' is pressed
				return file_exists(trailingslashit($updraft_dir).$entry) ? filemtime(trailingslashit($updraft_dir).$entry) : time();
			break;
		}
		return $btime;
	}

	public function foreign_dbfilename($db_basename, $fsource, $backupinfo, $working_dir_localpath, $separatedb) {

		if ('backupwordpress2' == $fsource || 'backupwordpress' == $fsource) {
			if (is_array($backupinfo)) {
				if ($separatedb) {
					$filename = (is_array($backupinfo['db'])) ? $backupinfo['db'][0] : $backupinfo['db'];
				} else {
					$filename = (is_array($backupinfo['wpcore'])) ? $backupinfo['wpcore'][0] : $backupinfo['wpcore'];
				}
				if (preg_match('/^(.*)-(\d+)-(database|complete)-\d/i', $filename, $matches)) {
					$try_filename = 'database-'.$matches[1].'-'.$matches[2].'.sql';
					if (file_exists($working_dir_localpath.'/'.$try_filename)) {
						$db_basename = $try_filename;
					}
				}
			}
		} elseif ('dropbox-wpadm' == $fsource) {
			// wp-content/Dropbox_Backup/mysqldump.sql in the last zip file
			return 'wp-content/Dropbox_Backup/mysqldump.sql';
		} elseif ('backwpup' == $fsource || 'infinitewp' == $fsource) {
			// Infinite WP has no manifest - that's only in BackWPUp
			if (is_file($working_dir_localpath.'/manifest.json')) {
				$manifest = file_get_contents($working_dir_localpath.'/manifest.json');
				if (false != $manifest) {
					$decode = json_decode($manifest);
					if (!empty($decode) && is_object($decode) && is_object($decode->job_settings)) {
						$js = $decode->job_settings;
						if (!empty($js->dbdumptype) && 'sql' == $js->dbdumptype && !empty($js->dbdumpfile) && file_exists($working_dir_localpath.'/'.$js->dbdumpfile.'.sql')) return $js->dbdumpfile.'.sql';
					}
				}
			} else {
				$found_sql = false;
				$add_dir = ('infinitewp' == $fsource) ? '/iwp_db' : '';
				if ($handle = opendir($working_dir_localpath.$add_dir)) {
					while (($file = readdir($handle)) !== false) {
						if (strtolower(substr($file, -4, 4)) == '.sql') {
							if (is_string($found_sql)) {
								trigger_error("Multiple .sql files found in backwpup backup - don't know which to use ($found_sql, $file)", E_USER_WARNING);
								return false;
							} else {
								$found_sql = 'iwp_db/'.(string) $file;
							}
						}
					}
					closedir($handle);
					if (is_string($found_sql)) return $found_sql;
				}

			}
			return false;
		} elseif ('wpb2d' == $fsource) {

			$latest_mtime = -1;
			$found_sql = false;

			// Rather hack-ish
			if (file_exists($working_dir_localpath.'/wp-config.php') || file_exists($working_dir_localpath.'/wpb2d/wp-config.php')) {
			
				$wp_config_file = file_exists($working_dir_localpath.'/wp-config.php') ? $working_dir_localpath.'/wp-config.php' : $working_dir_localpath.'/wpb2d/wp-config.php';
			
				$wp_config = file($wp_config_file);
				foreach ($wp_config as $line) {
					if (!defined('UPDRAFTPLUS_OVERRIDE_IMPORT_PREFIX') && preg_match("#\\\$table_prefix\s+=\s+'(.*)';#", $line, $matches)) {
						global $updraftplus;
						$updraftplus->log("Import table prefix is: ".$matches[1]);
						define('UPDRAFTPLUS_OVERRIDE_IMPORT_PREFIX', $matches[1]);
					}
				}
			}

			$backups_dir = file_exists($working_dir_localpath.'/wpb2d/wp-content/backups') ? 'wpb2d/wp-content/backups' : 'wp-content/backups';
			
			if ($handle = opendir($working_dir_localpath.'/'.$backups_dir)) {
				while (($file = readdir($handle)) !== false) {
					if (strtolower(substr($file, -4, 4)) == '.sql') {
						if (filemtime($working_dir_localpath.'/'.$backups_dir.'/'.$file) > $latest_mtime) {
							$latest_mtime = filemtime($working_dir_localpath.'/'.$backups_dir.'/'.$file);
							$found_sql = (string) $file;
						}
					}
				}
				closedir($handle);
				if (is_string($found_sql)) return $backups_dir.'/'.$found_sql;
			}

			$db_basename = $backups_dir.'/wordpress-db-backup.sql';
		} elseif (!$separatedb) {
			$db_basename = $backupinfo['wpcore'];
			if (is_array($db_basename)) $db_basename = array_shift($db_basename);
			$db_basename = basename($db_basename, '.zip').'.sql';
		}
		return $db_basename;
	}

	public function importforeign_backupable_plus_db($backupable_plus_db, $args) {
		$foinfo = $args[0];
		$mess = &$args[1];
		$mess[] = sprintf(__('Backup created by: %s.', 'updraftplus'), $foinfo['desc']);
		return array('wpcore' => $backupable_plus_db['wpcore']);
	}

	/**
	 * Scan filename and see if we recognise its pattern
	 *
	 * @param  string $accepted_foreign
	 * @param  string $entry
	 * @return string
	 */
	public function accept_foreign($accepted_foreign, $entry) {

		$accept = $this->accept_archivename(array());
		foreach ($accept as $fsource => $acc) {
			if (preg_match('/'.$acc['pattern'].'/i', $entry)) $accepted_foreign = $fsource;
		}
		return $accepted_foreign;
	}

	/**
	 * Return array of supported backup types
	 *
	 * @param  string $x [description]
	 * @return string
	 */
	public function accept_archivename($x) {
		if (!is_array($x)) return $x;

		$x['backupwordpress'] = array(
			'desc' => 'BackUpWordPress',
			'pattern' => 'complete-[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}\\.zip$',
			'separatedb' => false
		);

		$x['backupwordpress2'] = array(
			'desc' => 'BackUpWordPress',
			'pattern' => 'database-[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}\\.zip$',
			'separatedb' => true
		);

		$x['simple_backup'] = array(
			'desc' => 'Simple Backup',
			'pattern' => '^(db_)?backup.([0-9]{4})-([0-9]{2})-([0-9]{2}).([0-9]{2})([0-9]{2})([0-9]{2})\\.(zip|tar(\\.(bz2|gz))?|sql(\\.(gz))?)$',
			'separatedb' => true
		);

		$x['backwpup'] = array(
			'desc' => 'BackWPup',
			'pattern' => '^backwpup_[0-9a-f]+_([0-9]{4})-([0-9]{2})-([0-9]{2})_([0-9]{2})-([0-9]{2})-([0-9]{2})\\.(zip|tar(\\.(gz|bz2))?)$',
			'separatedb' => false
		);

		$x['dropbox-wpadm'] = array(
			'desc' => 'Dropbox Backup by WPAdm',
			'pattern' => '^.*-full-([0-9]{4})_([0-9]{2})_([0-9]{2})_([0-9]{2})_([0-9]{2})-([0-9]+)\\.zip$',
			'separatedb' => false
		);
		
		$x['wpb2d'] = array(
			'desc' => 'WordPress Backup To Dropbox',
			'pattern' => 'wpb2d.*\\.zip$',
			'separatedb' => false
		);

		$x['infinitewp'] = array(
			'desc' => 'InfiniteWP',
			'pattern' => '^.*_backup_(files|db)_([0-9]{4})-([0-9]{2})-([0-9]{2})_([0-9a-f]+)\\.zip$',
			'separatedb' => true
		);

		$x['genericsql'] = array(
			'desc' => '(Generic SQL backup)',
			'pattern' => '\\.sql(\.(bz2|gz))?$',
			'separatedb' => true
		);

		return $x;
	}

	/**
	 * Return JavaScript array of supported backup types
	 *
	 * @param  string $x [description]
	 * @return string
	 */
	public function accept_archivename_js($x) {
		// backup_([\-0-9]{15})_.*_([0-9a-f]{12})-[\-a-z]+([0-9]+(of[0-9]+)?)?\.(zip|gz|gz\.crypt)
		$accepted = $this->accept_archivename(array());
		$x = '[ ';
		$ind = 0;
		foreach ($accepted as $acc) {
			if ($ind > 0) $x .= ', ';
			$x .= "/".esc_js($acc['pattern'])."/i";
			$ind++;
		}
		return $x.' ]';
	}
}
