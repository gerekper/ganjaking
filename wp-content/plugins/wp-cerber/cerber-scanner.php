<?php
/*
	Copyright (C) 2015-20 CERBER TECH INC., https://cerber.tech
	Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com

    Licenced under the GNU GPL.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/*

*========================================================================*
|                                                                        |
|	       ATTENTION!  Do not change or edit this file!                  |
|                                                                        |
*========================================================================*

*/

define( 'CERBER_PK_WP', 'wordpress' );
define( 'CERBER_PK_PLUGIN', 'plugin' );
define( 'CERBER_PK_THEME', 'theme' );

define( 'CERBER_FT_WP', 1 );
define( 'CERBER_FT_PLUGIN', 2 );
define( 'CERBER_FT_THEME', 3 );
define( 'CERBER_FT_ROOT', 4 );
define( 'CERBER_FT_UPLOAD', 5 );
define( 'CERBER_FT_LNG', 6 );
define( 'CERBER_FT_MUP', 7 );
define( 'CERBER_FT_CNT', 8 );
define( 'CERBER_FT_CONF', 10 );
define( 'CERBER_FT_DRIN', 11 );
define( 'CERBER_FT_OTHER', 12 );

define( 'CERBER_MAX_SECONDS', 5 );
define( 'CERBER_MAX_SECONDS_CLOUD', 25 );

define( 'CERBER_FOK', 1 );
define( 'CERBER_VULN', 4 );
define( 'CERBER_NOHASH', 5 );
define( 'CERBER_IMD', 15 );
define( 'CERBER_SCF', 16 );
define( 'CERBER_PMC', 17 );
define( 'CERBER_USF', 18 );
define( 'CERBER_EXC', 20 );
define( 'CERBER_DIR', 26 );
define( 'CERBER_UXT', 30 );
define( 'CERBER_MOD', 50 );
define( 'CERBER_NEW', 51 );

define( 'CERBER_FDUN', 300 );
define( 'CERBER_FDLD', 301 );
define( 'CERBER_FRCV', 311 );

define( 'CERBER_MALWR_DETECTED', 1000 );

define( 'CRB_HASH_THEME', 'hash_tm_' );
define( 'CRB_HASH_PLUGIN', 'hash_pl_' );
define( 'CRB_LAST_FILE', 'tmp_last_file' );

define( 'CRB_SCAN_GO',   '__CERBER__SECURITY_SCAN_GO__' );
define( 'CRB_SCAN_STOP', '__CERBER__SECURITY_SCAN_STOP__' );
define( 'CRB_SCAN_DTB',  '__CERBER__SECURITY_SCAN_DATA_B' );
define( 'CRB_SCAN_DTE',  '__CERBER__SECURITY_SCAN_DATA_E' );

define( 'CRB_SCAN_RCV_DIR', 'recovery' );

define( 'CRB_SQL_CHUNK', 10000 ); // @since 8.6.4 Split queries into chunks to reduce memory consumption

add_action( 'plugins_loaded', function () {

	if ( ! cerber_is_cloud_request() ) {
		return;
	}

	ob_start(); // Collecting possible junk warnings and notices cause we need clean JSON to be sent

    // Load dependencies
	if ( ! function_exists( '_get_dropins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	$scanner = array();
	$errors  = array();
	$do      = '';

	if ( isset( $_POST['scan_mode'] ) ) {

	    $mode = ( isset( $_POST['scan_mode'] ) ) ? preg_replace( '/[^a-z_\-\d]/i', '', $_POST['scan_mode'] ) : 'quick';

		if ( cerber_is_cloud_enabled( $mode ) ) {
			if ( $scan = cerber_get_scan() ) {
				if ( $scan['finished'] || $scan['aborted'] ) {
					if ( $scan['finished'] < ( time() - 900 ) ) {
						$do = 'start_scan';
					}
					else {
						$errors['p'] = 'Scan protection interval';
					}
				}
                elseif ( $scan['cloud'] ) {
					if ( $scan['cloud'] == lab_get_real_node_id() ) {
						$do = 'continue_scan';
					}
					else {
					    // Restart a hung scan
                        if ( $scan['started'] < ( time() - 900 ) ) {
	                        $do = 'start_scan';
                        }
                        else {
	                        $errors['d'] = 'Scan from different node in progress';
                        }
					}
				}
				// Restart a hung/abandoned scan
                elseif ( $scan['started'] < ( time() - 900 ) ) {
					$do = 'start_scan';
				}
			}
			else {
				$do = 'start_scan';
			}

			if ( $do ) {
				$scanner           = cerber_scanner( $do, $mode );
				$scanner['errors'] = array(); // We don't process each error
			}

		}
		else {
			$errors['m'] = 'Mode is disabled';
		}

	}
	else {
		$errors['u'] = 'Unknown cloud request';
	}

	if ( ! empty( $scanner['cerber_scan_do'] ) ) {
		$do = $scanner['cerber_scan_do'];
	}
	else {
		$do = 'stop';
	}

	$db_errors = array_map( function ( $err ) {
		return substr( $err, 0, 1000 );
	}, cerber_db_get_errors() );

	$ret = array(
		'cerber_scanner' => $scanner,
		'client_errors'  => array( $errors, $db_errors ),
		'mem_limit'      => @ini_get( 'memory_limit' ),
		'ver'            => CERBER_VER
		//'scan'           => cerber_get_scan(), // debug only
	);

	ob_end_clean();

	if ( $do == 'continue_scan' ) {
		echo CRB_SCAN_GO;
	}
	else {
	    echo CRB_SCAN_STOP;
    }

	echo CRB_SCAN_DTB;
	echo json_encode( $ret );
	echo CRB_SCAN_DTE;

	die();

} );

function cerber_scanner( $control, $mode ) {
	global $cerber_scan_mode;

	if ( crb_get_settings( 'scan_debug' ) ) {
		register_shutdown_function( function () {
			if ( http_response_code() != 200 ) {
				crb_scan_debug( 'ERROR: Unexpected software errors detected. Check the server error log.' );
				if ( $err = error_get_last() ) {
					crb_scan_debug( print_r( $err, 1 ) );
				}
			}
		} );
	}

	$errors = array();

	if ( function_exists( 'wp_raise_memory_limit' ) ) {
		if ( ! wp_raise_memory_limit( 'admin' ) ) {
			//$m = 'WARNING: Unable to raise memory limit';
			//crb_scan_debug( $m );
			//$errors[] = $m;
		}
	}

	if ( ! $mode ) {
		$mode = 'quick';
	}

	$cerber_scan_mode = $mode;
	$status = null;
	$ret = array();

	switch ( $control ) {
		case 'start_scan':
			cerber_update_set( CRB_LAST_FILE, '', 0, false );
			cerber_init_scan( $mode );
			crb_scan_debug( '>>>>>>>>>>>>>>> START SCANNING: ' . $mode .', memory: '.@ini_get( 'memory_limit' ) );
			cerber_step_scanning();
			break;
		case 'continue_scan':
			if ( $scan = cerber_get_scan() ) {
				$cerber_scan_mode = $scan['mode'];
				cerber_step_scanning();
			}
			else {
				$errors[] = 'No scan in progress';
            }
			break;
        case 'get_last_scan':
	        if ( $scan = cerber_get_scan() ) {
		        $ret['issues'] = crb_issue_filer( $scan['issues'], 'file_exists' );
	        }
            break;
	}

	if ($scan = cerber_get_scan()) {

		$ret['scan_id'] = $scan['id'];
		$ret['mode']    = $scan['mode'];
		$ret['cloud']   = $scan['cloud'];

		if ( $scan['finished'] || $scan['aborted'] ) {
			$ret['cerber_scan_do'] = 'stop';
		}
		else {
			$ret['cerber_scan_do'] = 'continue_scan';
		}

		$ret['step']    = $scan['next_step'];
		$ret['aborted'] = $scan['aborted'];
		$ret['errors']       = array_merge( $errors, cerber_get_scan_errors() );
		$ret['errors_total'] = count( $ret['errors'] );

		$ret['total']   = $scan['total'];
		$ret['scanned'] = $scan['scanned'];

		if ( ! cerber_is_cloud_request() ) {
			$ret['step_issues'] = $scan['step_issues'];
			$ret['scanned']     = $scan['scanned'];
			$ret['numbers']     = $scan['numbers'];

			$ret['started'] = cerber_date( $scan['started'] );
			$ret['elapsed'] = time() - $scan['started'];
			$duration            = $ret['elapsed'];

			$ret['finished'] = '';
			$ret['duration'] = '';

			if ( $scan['finished'] ) {
				$ret['finished'] = cerber_date( $scan['finished'] );
				$duration        = $scan['finished'] - $scan['started'];
				$ret['step']     = '';
			}

			if ( $duration < 60 ) {
				$ret['duration'] = $duration . ' seconds';
			}
			else {
				$ret['duration'] = round( $duration / 60, 2 ) . ' minutes';
			}

			if ( $duration && ! empty( $scan['scanned']['bytes'] ) ) {
				$ret['performance'] = number_format( round( ( $scan['scanned']['bytes'] / $duration ) / 1024, 0 ), 0, '.', ' ' );
			}
			else {
				$ret['performance'] = 0;
			}
			$ret['performance'] .= ' KB/sec';

		}
	}
	else {
		$ret['cerber_scan_do'] = 'stop';
	}

	if ( cerber_db_get_errors() ) {
		cerber_watchdog( true );
	}

	return $ret;
}

function cerber_step_scanning() {
    global $cerber_scan_mode;

	ignore_user_abort( true );

	cerber_exec_timer();

	if ( ! $scan = cerber_get_scan() ) {
		return false;
	}

	if ( $scan['finished'] || $scan['aborted'] ) {
		return true;
	}

	cerber_update_scan( array( 'step_issues' => array() ) );

	$update    = array();
	$next_step = $scan['next_step'];
	$aborted   = 0;
	$remain    = 0;
	$exceed    = false;

	crb_scan_debug( cerber_step_desc( $scan['next_step'] ) . ' (step ' . $scan['next_step'] . ')' );

	switch ( $scan['next_step'] ) {
		case 0:
		    cerber_before_scan();
		    break;
		case 1:
			if ( $result = cerber_scan_directory( ABSPATH, null, '_crb_save_file_names' ) ) {
				$above = dirname( cerber_get_abspath() ) . DIRECTORY_SEPARATOR;
				_crb_save_file_names( array( $above . 'wp-config.php', $above . '.htaccess' ) );
				$update['total']['files']   = cerber_get_num_files( $scan['id'] );
				$update['total']['folders'] = $result[0];
				crb_scan_debug( array(
					'Folders: ' . $update['total']['folders']
				) );
			}
			else {
				$aborted = 1;
			}
			break;
		case 2:
			if ( crb_get_settings( 'scan_tmp' ) ) {
				$tmp_dir = @ini_get( 'upload_tmp_dir' );
				if ( is_dir( $tmp_dir ) && $result = cerber_scan_directory( $tmp_dir, null, '_crb_save_file_names' ) ) {
					//$update['total']['folders'] += $result[0];
				}
				$update['total']['files'] = cerber_get_num_files( $scan['id'] );
			}
			break;
		case 3:
			if ( crb_get_settings( 'scan_tmp' ) ) {
				$tmp_dir = @ini_get( 'upload_tmp_dir' );
				$another_dir = sys_get_temp_dir();
				if ( $another_dir !== $tmp_dir && @is_dir( $another_dir ) && $result = cerber_scan_directory( $another_dir, null, '_crb_save_file_names' ) ) {
					//$update['total']['folders'] += $result[0];
				}
				$update['total']['files'] = cerber_get_num_files( $scan['id'] );
			}
			break;
        case 4:
			if ( crb_get_settings( 'scan_sess' ) ) {
				$another_dir = session_save_path();
				if ( @is_dir( $another_dir )
				     && $result = cerber_scan_directory( $another_dir, null, '_crb_save_file_names' ) ) {
					//$update['total']['folders'] += $result[0];
				}
				$update['total']['files'] = cerber_get_num_files( $scan['id'] );
			}
			break;
		case 5:
			$x = 0;
			//if ( $result = cerber_db_get_results( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan['id'] . ' AND file_hash = ""' ) ) {
			$done = false;
			while ( ! $aborted && ! $exceed && ! $done ) {
			    // Split into several SQL requests to avoid memory exhausted error on a website with hundreds of thousands files
				if ( $result = cerber_db_get_results( 'SELECT file_name, scan_id, file_name_hash FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan['id'] . ' AND file_hash = "" LIMIT ' . CRB_SQL_CHUNK ) ) {
					//cerber_log_scan_error( 'CHUNK ' . count( $result ) );
					foreach ( $result as $row ) {
						if ( ! cerber_add_file_info( $row ) ) {
							cerber_log_scan_error( 'Unable to update file info. Scanning has been aborted.' );
							$aborted = 1;
							break;
						}
						if ( 0 === ( $x % 100 ) ) {
							if ( cerber_exec_timer() ) {
								$exceed = true;
								break;
							}
						}
						$x ++;
					}
				}
				else {
					//$aborted = 1;
					$done = true;
				}
			}
			// Some files might be symlinks
			$update['total']['files'] = cerber_get_num_files( $scan['id'] );
			$update['total']['parsed'] = cerber_db_get_var( 'SELECT COUNT(scan_id) FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan['id'] . ' AND file_type !=0' );
			break;
		case 6:
			if ( cerber_is_check_fs() ) {
				cerber_check_fs_changes();
			}
			break;
		case 7:
			cerber_verify_wp();
			break;
		case 8:
			$remain = cerber_recover_files( CERBER_PK_WP );
			break;
		case 9:
			$remain = cerber_verify_plugins();
			break;
		case 10:
			$remain = cerber_recover_files( CERBER_PK_PLUGIN );
			break;
		case 11:
			$remain = cerber_verify_themes();
			break;
		case 12:
			$remain = cerber_process_files();
			break;
		case 13:
			// Finalizing scan
            cerber_apply_scan_policies();
			break;
	}

	if ( ! $remain && ! $exceed && ! $aborted ) {
		$next_step = cerber_next_step( $scan['next_step'] );
	}

	if ( $next_step > 13 ) {
		$update['finished'] = time();
		$update['step_issues'] = array();
	}

	if ( $aborted ) {
		$update['aborted'] = time();
	}

	$update['next_step'] = $next_step;
	$update['scanned']['files'] = cerber_db_get_var( 'SELECT COUNT(scan_id) FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan['id'] . ' AND scan_status > 0' );
	$update['scanned']['bytes'] = cerber_db_get_var( 'SELECT SUM(file_size) FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan['id'] . ' AND scan_status > 0' );

	if ( isset( $update['total']['files'] ) ) {
		crb_scan_debug( 'Files: ' . $update['total']['files'] );
	}
	if ( $update['scanned']['files'] ) {
		crb_scan_debug( 'Scanned files: ' . $update['scanned']['files'] );
	}

	if ( ! $scan = cerber_get_scan() ) {
		return false;
	}

	$update['issues'] = cerber_merge_issues( $scan['issues'], $scan['step_issues'] );

	cerber_make_numbers( $update, $scan );

	$ret = cerber_update_scan( $update );

	if ( isset( $update['finished'] ) || isset( $update['aborted'] ) ) {
		crb_scan_debug( '>>>>>>>>>>>>>>> SCANNING IS COMPLETED.' );
		cerber_scan_completed();
		$cr = cerber_cleanup_recovery();
		if ( is_wp_error( $cr ) ) {
			crb_scan_debug( 'ERROR: ' . $cr->get_error_message() );
		}
	}

	return $ret;

}

// Calculate the next step according to settings
function cerber_next_step( $current_step ) {
	//$current_step = $scan['next_step'];
	$next_step = $current_step;
	switch ( $current_step ) {
		case 1:
			if ( crb_get_settings( 'scan_tmp' ) ) {
				$next_step += 1;
			}
			else {
				$next_step += ( crb_get_settings( 'scan_sess' ) ) ? 3 : 4;
			}
			break;
		case 3:
			$next_step += ( crb_get_settings( 'scan_sess' ) ) ? 1 : 2;
			break;
		case 5:
			$next_step += ( cerber_is_check_fs() ) ? 1 : 2;
			break;
        case 7:
	        $next_step += ( crb_get_settings( 'scan_recover_wp' ) ) ? 1 : 2;
            break;
		case 9:
			$next_step += ( crb_get_settings( 'scan_recover_pl' ) ) ? 1 : 2;
			break;
		default:
			$next_step ++;
	}

	return $next_step;
}

function cerber_scan_completed() {
	if ( ! cerber_is_cloud_request()
	     || ! lab_lab()
	     || ! cerber_is_cloud_enabled() ) {
		return;
	}

	if ( ! ( $scan = cerber_get_scan() ) || ! $scan['cloud'] ) {
		return;
	}

	$report = cerber_scan_report( $scan );

	if ( ! $report ) {
		crb_scan_debug( 'No issues for email reporting.' );

		return;
	}

	if ( ! cerber_send_email( 'scan', $report ) ) {
		// Send alert via cloud?
	}
	else {
		crb_scan_debug( 'Email report has been sent.' );
	}

}

function cerber_before_scan() {
	$dir = session_save_path();
	if ( @is_dir( $dir ) &&
	     crb_get_settings( 'scan_sess' ) &&
	     ! crb_get_settings( 'scan_nodelsess' ) ) {
		crb_scan_debug( 'Cleaning up in the session directory ' . $dir );
		cerber_empty_folder( $dir );
	}
	$dir = @ini_get( 'upload_tmp_dir' );
	if ( @is_dir( $dir ) &&
	     crb_get_settings( 'scan_tmp' ) &&
	     ! crb_get_settings( 'scan_nodeltemp' ) ) {
		crb_scan_debug( 'Cleaning up in the temp directory ' . $dir );
		cerber_empty_folder( $dir );
	}
	$dir = @sys_get_temp_dir();
	if ( @is_dir( $dir ) &&
	     crb_get_settings( 'scan_tmp' ) &&
	     ! crb_get_settings( 'scan_nodeltemp' ) ) {
		crb_scan_debug( 'Cleaning up in the temp directory ' . $dir );
		cerber_empty_folder( $dir );
	}
}

function cerber_empty_folder( $dir ) {
	$dir = rtrim( $dir, '/\\' ) . DIRECTORY_SEPARATOR;
	$ex  = crb_get_settings( 'scan_delexdir' );
	if ( $ex && in_array( $dir, $ex ) ) {
		return;
	}

	if ( ! wp_is_writable( $dir ) ) {
		cerber_log_scan_error( 'The directory is write protected: ' . $dir );

		return;
	}

	$r = cerber_empty_dir( $dir );

	if ( is_wp_error( $r ) ) {
		cerber_log_scan_error( 'Unable to delete files in the directory: ' . $dir );
		crb_scan_debug( 'ERROR: ' . $r->get_error_message() );
	}
	else {
		crb_scan_debug( 'Directory has been emptied: ' . $dir );
	}
}

function cerber_apply_scan_policies() {
	if ( ! cerber_is_cloud_request() && ! lab_lab() ) {
		return; // TODO: !uncomment for production
	}
	if ( ! $scan = cerber_get_scan() ) {
		return;
	}
	$opt            = crb_get_settings();
	$sess_dir       = rtrim( session_save_path(), '/\\' );
	$tmp_dir1       = rtrim( @ini_get( 'upload_tmp_dir' ), '/\\' );
	$tmp_dir2       = rtrim( sys_get_temp_dir(), '/\\' );
	$scan_delupl    = ( ! empty( $opt['scan_delupl'] ) ) ? array_keys( $opt['scan_delupl'] ) : array();
	$may_be_deleted = array( CERBER_SCF, CERBER_PMC, CERBER_USF, CERBER_EXC, CERBER_UXT );
	$update         = false;

	crb_scan_debug( 'Cleaning up...' );

	foreach ( $scan['issues'] as $id => &$set ) {
		foreach ( $set['issues'] as $key => &$issue ) {
			if ( empty( $issue['data']['fd_allowed'] )
			     || isset( $issue['data']['prced'] )
			     || ! in_array( $issue[0], $may_be_deleted )
			     || ! is_file( $issue['data']['name'] ) ) {
				continue;
			}

			$file_name = $issue['data']['name'];
			$dir       = dirname( $file_name );
			$delete    = false;

			if ( $opt['scan_delexdir'] && in_array( $dir, $opt['scan_delexdir'] ) ) {
				continue;
			}

			if ( $opt['scan_delexext'] && cerber_has_extension( $file_name, 'scan_delexext' ) ) {
				continue;
			}

			if ( $dir == $sess_dir ) {
				if ( $opt['scan_nodelsess'] ) {
					continue;
				}
				$delete = true;
			}
            elseif ( $dir == $tmp_dir1 || $dir == $tmp_dir2 ) {
				if ( $opt['scan_nodeltemp'] ) {
					continue;
				}
				$delete = true;
			}
			elseif ( $issue['data']['type'] == CERBER_FT_UPLOAD ) {
				if ( ! in_array( $issue[2], $scan_delupl ) ) {
					continue;
				}
				$delete = true;
			}

			if ( ! $delete ) {
				if ( $set['setype'] == 21 || $issue[0] == CERBER_USF ) {
					if ( ! empty( $opt['scan_delunatt'] ) ) {
						$delete = true;
					}
				}
				if ( ! $delete && ! empty( $opt['scan_delunwant'] ) ) {
					if ( cerber_has_extension( $file_name, 'scan_uext' ) ) {
						$delete = true;
					}
				}
			}

			if ( $delete ) {
			    $update = true;
				$result = cerber_quarantine_file( $file_name, $scan['id'] );
				if ( is_wp_error( $result ) ) {
					cerber_log_scan_error( $result->get_error_message() );
					$issue['data']['prced'] = CERBER_FDUN;
				}
				else {
					crb_scan_debug( 'File deleted: ' . $file_name );
					$issue['data']['prced'] = CERBER_FDLD;
				}
			}
		}
	}

	if ( $update ) {
		crb_scan_debug( 'Updating scan...' );
		cerber_update_scan( $scan );
	}
}

function cerber_recover_files( $package_type ) {
	if ( ! cerber_is_cloud_request() && ! lab_lab() ) {
		return false;
	}
	if ( ! $scan = cerber_get_scan() ) {
		return false;
	}

	$mapping = array(
		CERBER_FT_WP     => CERBER_PK_WP,
		CERBER_FT_ROOT   => CERBER_PK_WP,
		CERBER_FT_PLUGIN => CERBER_PK_PLUGIN,
		CERBER_FT_THEME  => 'theme',
	);

	$update = false;
	$ret = 0;

	foreach ( $scan['issues'] as $id => &$set ) {
		foreach ( $set['issues'] as $key => &$issue ) {
			if ( $issue[0] != CERBER_IMD
			     || isset( $issue['data']['prced'] ) ) {
				continue;
			}

			$file_type = $issue['data']['type'];

			if ( ! isset( $mapping[ $file_type ] )
                 || $mapping[ $file_type ] != $package_type ) {
				continue;
			}

			$file_name = $issue['data']['name'];
			if ( ! is_file( $file_name ) ) {
				continue;
			}

			$data = array();
			if ( $package_type == CERBER_PK_PLUGIN ) {
				$data = $set['sec_details']['plugin'];
			}

			$source_file = cerber_get_the_source( $package_type, $file_name, $data );

			if ( cerber_exec_timer() ) { // TODO: should be checked separately for downloading and unziping
				$ret = 1;
				break 2;
			}

			if ( is_wp_error( $source_file ) ) {
				crb_scan_debug( 'ERROR: ' . $source_file->get_error_message() );
				continue;
			}

			cerber_quarantine_file( $file_name, $scan['id'], false );

			if ( ! @copy( $source_file, $file_name ) ) {
				$err = error_get_last();
				crb_scan_debug( 'ERROR: Unable to recover the file: ' . $file_name );
				if ( $err ) {
					crb_scan_debug( 'I/O ERROR: ' . $err['message'] );
				}
				$issue['data']['prced'] = CERBER_FRCV - 1;
			}
			else {
				crb_scan_debug( 'The file has been recovered: ' . $file_name );
				$issue['data']['prced'] = CERBER_FRCV;
				$update = true;
			}

		}
	}

	if ( $update ) {
		crb_scan_debug( 'Updating scan...' );
		cerber_update_scan( $scan );
	}

	return $ret;
}

function cerber_get_the_source( $package_type, $file_name, $data = array() ) {

	switch ( $package_type ) {
		case CERBER_PK_WP:
			$file_name  = mb_substr( $file_name, mb_strlen( cerber_get_abspath() ) );
			$version    = cerber_get_wp_version();
			$locale     = get_locale();
			$arc_folder = 'wordpress/';
			$slug     = $locale . '-';
			// See do_core_upgrade();
			if ( $locale == 'en_US' ) {
				$url    = 'https://downloads.wordpress.org/release/wordpress-' . $version . '.zip';
				$zip_name = 'wordpress-' . $version . '.zip';
			}
			else {
				$url    = 'https://downloads.wordpress.org/release/' . $locale . '/wordpress-' . $version . '.zip';
				$zip_name = 'wordpress-' . $version . '-' . $locale . '.zip';
			}
			break;
		case CERBER_PK_PLUGIN:
			$file_name  = mb_substr( $file_name, mb_strlen( cerber_get_plugins_dir() ) );
			list( $slug ) = explode( '/', $data['slug'] );
			$version = trim( $data['Version'], '.' );
			$arc_folder = '';
			$url = 'https://downloads.wordpress.org/plugin/' . $slug . '.' . $version . '.zip';
			$zip_name = $slug . '.' . $version . '.zip';
			break;
		default:
			return false;
	}

	$folder     = cerber_get_tmp_file_folder();
	if ( is_wp_error( $folder ) ) {
		return $folder;
	}

	$tmp_folder = $folder . CRB_SCAN_RCV_DIR . '/' . $package_type . '/' . $slug . $version . '/';
	$ret        = $tmp_folder . $arc_folder . $file_name;

	if ( file_exists( $ret ) ) {
		return $ret;
	}

	crb_scan_debug( 'Downloading source: ' . $url );

	$zip_file = cerber_download_file( $url, $zip_name );

	if ( ! $zip_file || is_wp_error( $zip_file ) ) {
		return $zip_file;
	}

	$result = cerber_unzip( $zip_file, $tmp_folder );

	if ( is_wp_error( $result ) ) {
		return new WP_Error( 'cerber-zip', 'Unable to unzip file ' . $zip_file . ' ' . $result->get_error_message() );
	}

	unlink( $zip_file );

	if ( ! file_exists( $ret ) ) {
		return new WP_Error( 'scan_no_source', 'No source file found' );
	}

	return $ret;

}

function cerber_cleanup_recovery() {
	$folder = cerber_get_tmp_file_folder();
	if ( is_wp_error( $folder ) ) {
		return $folder;
	}

	if ( ! file_exists( $folder . CRB_SCAN_RCV_DIR ) ) {
		return true;
	}

	$fs = cerber_init_wp_filesystem();
	if ( is_wp_error( $fs ) ) {
		return $fs;
	}

	if ( ! $fs->rmdir( $folder . CRB_SCAN_RCV_DIR, true ) ) {
		return new WP_Error( 'cerber-dir', 'ERROR: Unable to clean up recovery folder' );
	}

	return true;
}

/**
 * Initialize data structure for a new Scan
 *
 * @param string $mode  quick|fool
 *
 * @return array|bool
 */
function cerber_init_scan( $mode = 'quick' ) {
	cerber_delete_old_scans();

	if ( ! $mode ) {
		$mode = 'quick';
    }

	$data                = array();
	$data['mode']        = $mode;     // Quick | Full
	$data['id']          = time();
	$data['started']     = $data['id'];
	$data['finished']    = 0;
	$data['aborted']     = 0;         // If > 0, the scan has been aborted due to unrecoverable errors
	$data['scanned']     = array();
	$data['issues']      = array();   // The list of issues
	$data['step_issues'] = array();   // The list of issues during the current step
	$data['total']       = array();    // Counters
	$data['integrity']   = array();
	$data['ip']          = cerber_get_remote_ip();
	$data['cloud']       = cerber_is_cloud_request();
	$data['next_step']   = 0;
	$data['numbers']   = array();

	if ( ! cerber_update_set( 'scan', $data, $data['id'] ) ) {

		return false;
	}

	return $data;
}

/**
 * Return ID for the Scan in progress (the latest scan started)
 *
 * @return bool|integer Scan ID false if no scan in progress (no files to scan)
 */
function cerber_get_scan_id() {

	$scan_id = null;

	if ( $all = cerber_db_get_col( 'SELECT the_id FROM ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' WHERE the_key = "scan"' ) ) {
		$scan_id = max( $all ); // There is no index for the_id column, so it should be faster
	}

	if ( ! $scan_id ) {
		$scan_id = false;
	}

	return $scan_id;
}

/**
 * Return Scan data
 *
 * @param integer $scan_id if not specified the last Scan data is returned
 *
 * @return array|bool
 */
function cerber_get_scan( $scan_id = null ) {

    // If no ID is specified look for the latest one
	if ( $scan_id === null ) {
		$scan_id = cerber_get_scan_id();
	}

	if ( ! $scan_id ) {
		return false;
	}

	$scan = cerber_get_set( 'scan', $scan_id );
	$scan['mode_h'] = ( $scan['mode'] == 'full' ) ? __( 'Full Scan', 'wp-cerber' ) : __( 'Quick Scan', 'wp-cerber' );

	// Chunked data
	if ( ! empty( $scan['chunked'] ) ) {
		$in    = '("scan_chunk_' . implode( '","scan_chunk_', range( 1, $scan['chunked'] ) ) . '")';
		$order = ' ORDER BY FIELD (the_key, "scan_chunk_' . implode( '","scan_chunk_', range( 0, $scan['chunked'] ) ) . '")';
		if ( $values = cerber_db_get_col( 'SELECT the_value FROM ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' WHERE the_key IN ' . $in . ' AND the_id = ' . $scan_id . $order ) ) {
			if ( ! empty( $scan['compressed'] ) && extension_loaded( 'zlib' ) ) {
				/*$values = implode( '', $values );
				$values = hex2bin( $values );
				$values = gzuncompress( $values );
				$values = json_decode( $values, true );*/
				$values = unserialize( gzuncompress( hex2bin( implode( '', $values ) ) ) );
			}
			else {
				$values = unserialize( implode( '', $values ) );
				//$values  = json_decode( implode( '', $values ), true );
            }

			//$values              = unserialize( implode( '', $values ) );
			$scan['issues']      = $values[0];
			$scan['step_issues'] = $values[1];
			unset( $values );
		}
	}
	// ---

	return $scan;

}

/**
 * Update scan data by simply merging values in array
 *
 * @param array $new_data
 *
 * @return bool
 */
function cerber_update_scan( $new_data ) {
	if ( ! $old_data = cerber_get_scan() ) {
		return false;
	}

	if ( isset( $new_data['id'] ) ) {
		unset( $new_data['id'] );
	}

	$data = array_merge( $old_data, $new_data );

	unset( $old_data );
	unset( $new_data );

	// Split massive data sets into chunks

	$data['chunked'] = false;
	$data['compressed'] = 0;

	if ( ! $p = crb_get_mysql_var( 'max_allowed_packet' ) ) {
		$p = 1048576;
	}
	$chunk_size = intval( 0.9 * $p );

	if ( ! isset( $data['step_issues'] ) ) {
		$data['step_issues'] = array();
	}
	if ( ! isset( $data['issues'] ) ) {
		$data['issues'] = array();
	}

	$issues = serialize( array( $data['issues'], $data['step_issues'] ) );
    //$issues = json_encode( array( $data['issues'], $data['step_issues'] ) );
	$length = strlen( $issues );

	if ( $length > $chunk_size ) {
		unset( $data['issues'] );
		unset( $data['step_issues'] );
		$start           = 0;
		$index           = 1;

		if ( extension_loaded( 'zlib' ) ) {
			if ( $issues = bin2hex( gzcompress( $issues, 2 ) ) ) {
			//if ( $issues = bin2hex( gzencode( $issues, 2 ) ) ) {
			//if ( $issues = bin2hex( gzdeflate( $issues, 2 ) ) ) {
				$gzlength           = strlen( $issues );
				crb_scan_debug( "Chunk is compressed {$length} {$gzlength} " . ( $length / $gzlength ) );
				$length             = $gzlength;
				$data['compressed'] = 1;
			}
		}

		while ( $length > 0 ) {
			$chunk = substr( $issues, $start, $chunk_size );
			if ( ! cerber_update_set( 'scan_chunk_' . $index, $chunk, $data['id'], false ) ) {
				cerber_log_scan_error( 'Unable to save a scan chunk' );
			}
			$index ++;
			$start += $chunk_size;
			$length -= $chunk_size;
		}
		$data['chunked'] = $index - 1;
		unset( $issues );
		crb_scan_debug( 'Split data into ' . $data['chunked'] . ' chunks, chunk size '.$chunk_size );
	}

    // --

	$ret = cerber_update_set( 'scan', $data, $data['id'] );

	if ( ! $ret ) {
		cerber_log_scan_error( 'Unable to update the scan' );
	}

	unset( $issues );
	unset( $data );
	unset( $old_data );

	return $ret;

}

/**
 * Update scan data and preserve existing keys in array (scan structure)
 *
 * @param array $new_data
 *
 * @return bool
 */
function cerber_set_scan( $new_data ) {
	if ( ! $scan_data = cerber_get_scan() ) {
		return false;
	}

	$data = cerber_array_merge_recurively( $scan_data, $new_data );

	return cerber_update_scan( $data );
}

/**
 * Delete all outdated scans and their results except the several ones
 *
 */
function cerber_delete_old_scans() {
	if ( ! $scans = cerber_db_get_results( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' WHERE the_key = "scan" ORDER BY the_id DESC' ) ) {
		return;
	}

	$limit = 2; // How many results we keep in the DB as a history
	$q_list = array();
	$q = 0;
	$f_list = array();
	$f = 0;

	foreach ( $scans as $item ) {
		$scan = unserialize( $item['the_value'] );
		if ( $scan['mode'] == 'quick' && $q < $limit ) {
			$q_list[] = $scan['id'];
			$q ++;
		}
        elseif ( $scan['mode'] == 'full' && $f < $limit ) {
			$f_list[] = $scan['id'];
			$f ++;
		}
		elseif ($q >= $limit && $f >= $limit ){
		    break;
        }
	}

	$keep   = array_merge( $q_list, $f_list );
	//$all    = array_column( $scans, 'the_id' );
	$all    = crb_array_column( $scans, 'the_id' );
	$delete = array_diff( $all, $keep );

	if ( ! $delete ) {
		return;
	}

	foreach ( $delete as $scan_id ) {
		cerber_delete_scan( $scan_id );
	}

	// TODO: cleanup junk parts if no realted scan data exists
    /*
	if ( ! $scans = cerber_db_get_results( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' WHERE the_key = "scan" ORDER BY the_id DESC' ) ) {
		return;
	}
	cerber_delete_set( 'scan_errors', $scan_id );
	cerber_delete_set( 'tmp_verify_plugins', $scan_id );
    */

}

/**
 * Delete a single scan
 *
 * @return bool
 */
function cerber_delete_scan( $scan_id ) {
	$scan_id = absint( $scan_id );
	if ( $scan = cerber_get_scan( $scan_id ) ) {
		if ( ! empty( $scan['chunked'] ) ) {
			for ( $n = 0; $n <= $scan['chunked']; $n ++ ) {
				if ( ! cerber_delete_set( 'scan_chunk_' . $n, $scan_id ) ) {
					return false;
				}
			}
		}

		cerber_delete_set( 'scan_errors', $scan_id );
		cerber_delete_set( 'tmp_verify_plugins', $scan_id );
		cerber_db_query( 'DELETE FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan_id );

		cerber_delete_set( 'scan', $scan_id );

		return true;
	}

	return false;
}

function cerber_get_num_files( $scan_id ) {
	return cerber_db_get_var( 'SELECT COUNT(scan_id) FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . absint( $scan_id ) );
}

/**
 * Save issues (for end user reporting) during the scanning
 *
 * @param string $section
 * @param array $issues
 * @param string $container Top level container for the section
 *
 * @return bool
 */
function cerber_push_issues( $section, $issues = array(), $container = '', $sec_details = array() ) {
	if ( empty( $issues ) || empty( $section ) ) {
		return false;
	}

	$sec_details = array_merge( array( 'vul_list' => false ), $sec_details );

	$list = array();

	// Add some details

	$setype = 0;

	foreach ( $issues as $issue ) {

		$data = array();
		$extra_issue = 0;

		if ( isset( $issue['file'] ) ) {

			$file          = $issue['file'];
			$data['bytes'] = $file['file_size'];
			$data['size']  = crb_size_format( $file['file_size'] );
			$ftime         = $file['file_mtime'];
			$data['time']  = cerber_auto_date( $ftime );
			$data['name']  = $file['file_name'];
			$data['type']  = $file['file_type'];

			$status = crb_array_get( $file, 'file_status', 0 );
			if ( 0 < $status && $status != $issue[0] ) {
				$extra_issue = $status;
			}

			// Can the file be deleted safely?

			$allowed = 0;
			if ( $file['file_type'] != CERBER_FT_CONF
			     && ! empty( $file['fd_allowed'] )
			     && true === cerber_can_be_deleted( $file['file_name'] ) ) {
				$allowed = 1;
			}

			$data['fd_allowed'] = $allowed;

		}
		//elseif ( isset( $issue['plugin'] ) ) {
		//	$data['version'] = $issue['plugin']['Version'];
        elseif ( isset( $sec_details['plugin'] ) ) {
			$data['version'] = $sec_details['plugin']['Version'];
			$setype          = 3;
		}
        elseif ( isset( $issue['theme'] ) ) {
			$data['version'] = $issue['theme']->get( 'Version' );
			$setype          = 2;
		}
        elseif ( isset( $issue[ CERBER_PK_WP ] ) ) {
			$data['version'] = $issue[ CERBER_PK_WP ];
			$setype          = 1;
		}

		$issue_type = $issue[0];
		$details = ( isset( $issue[2] ) ) ? $issue[2] : '';
		$short_name = ( isset( $issue[1] ) ) ? $issue[1] : '';

		// Single issue data set

		$list[] = array(
			$issue_type, // 0 - Type of issue
			$short_name, // 1 - Object name
			cerber_calculate_risk( $issue ), // 2 - Severity
			$extra_issue, // 3 - Extra issue
			'data'    => $data,
			'details' => $details,
		);
	}

	// Some stuff for better end-user report displaying

	if ( $section == 'WordPress' ) {
		$container = 'crb-wordpress';
	}
	if ( $section == 'Uploads folder' ) {
		$setype = 20;
	}
	if ( $section == 'Unattended files' ) {
		$container = 'crb-unattended';
		$setype = 21;
	}

	// TODO: $container Should be refactored

	if ( ! $container ) {
		//$container = sha1( $section );
		if ( isset( $issues[0]['file'] ) ) {
			switch ( $issues[0]['file']['file_type'] ) {
				case CERBER_FT_WP:
				case CERBER_FT_CONF:
					$container = 'crb-wordpress';
					break;
				case CERBER_FT_PLUGIN:
					$container = 'crb-plugins';
					break;
				case CERBER_FT_THEME:
					$container = 'crb-themes';
					break;
				case CERBER_FT_UPLOAD:
					$container = 'crb-uploads';
					break;
                case CERBER_FT_MUP:
	                $container = 'crb-muplugins';
	                break;
				case CERBER_FT_DRIN:
					$container = 'crb-dropins';
					break;
				default:
					$container = 'crb-unattended';
			}
		}
		else {
			if ( $section == 'WordPress' ) {
				$container = 'crb-wordpress';
			}
		}
	}

	if ( ! $container ) {
		$container = 'crb-unattended';
		$setype    = 21;
	}


	// Save all

    // TODO: save section issues as a separate row with cerber_update_set()

	$id = sha1( $section );

	$scan = cerber_get_scan();

	$scan['step_issues'] = cerber_merge_issues( $scan['step_issues'], array(
		$id =>
			array(
				'name'        => $section,
				'container'   => $container,
				'sec_details' => $sec_details,
				'setype'      => $setype,
				'issues'      => $list,
			)
	) );

	$ret = cerber_update_scan( $scan );

	if ( ! $ret ) {
		cerber_log_scan_error( 'Unable to save the list of issues!' );
	}

	return $ret;
}

/**
 * Merge two lists of issues in a correct way
 *
 * @param $issues1
 * @param $issues2
 *
 * @return array
 */
function cerber_merge_issues( $issues1, $issues2 ) {
	if ( ! $issues1 ) {
		$issues1 = array();
	}
	foreach ( $issues2 as $id => $item ) {
		if ( ! isset( $issues1[ $id ] ) ) {
			//$issues1[ $id ] = array( 'name' => $item['name'], 'issues' => $item['issues'] );
			$issues1[ $id ] = $item;
		}
		else {
			$issues1[ $id ]['issues'] = array_merge( $issues1[ $id ]['issues'], $item['issues'] );
		}
	}

	return $issues1;
}

/**
 * Indicator for end-user
 *
 * @param $issue
 *
 * @return int|mixed
 */
function cerber_calculate_risk( $issue ) {
	$risk = array(
		CERBER_FOK  => 0,
		CERBER_VULN => 3,
		10          => 1,
		11          => 2,
		5           => 3,
		6           => 3,
		7           => 3,
		8           => 3
	);

	if ( isset( $risk[ $issue[0] ] ) ) {
		return $risk[ $issue[0] ];
	}

	$file = $issue['file'];

	// Small junk files?
	$size_factor = null;
	if ( isset( $file['file_size'] ) ) {
		if ( $file['file_size'] < 10 ) {
			$size_factor = 1;
		}
		elseif ( $file['file_size'] < 30 ) {
			$size_factor = 2;
		}
	}

	// TODO: convert into a formula with metrics
	switch ( $issue[0] ) {
		case 14:
			if ( $size_factor ) {
				return $size_factor;
			}
			return 2;
			break;
		case CERBER_EXC:
			if ( $size_factor ) {
				return $size_factor;
			}
			if ( $file['file_type'] == CERBER_FT_UPLOAD ) {
				return 2;
			}
			return 2;
			break;
		case CERBER_IMD:
		case CERBER_USF:
		case CERBER_SCF:
		case CERBER_PMC:
		case CERBER_DIR:
			if ( $size_factor ) {
				return $size_factor;
			}

			if ( ! cerber_detect_exec_extension( $file['file_name'], array( 'js', 'inc' ) ) ) {
				return 2;
			}

			return 3;
			break;
	}

	return 1;
}

function cerber_get_risk_label() {
	return array(
		'',
		'Low',
		'Medium',
		'High',
	);
}

function cerber_get_issue_label( $id = null ) {
	$issues = array(
		0 => 'To be scanned',
		CERBER_FOK => __( 'Verified', 'wp-cerber' ),

		// 2-3 are prohibited! See: 'scan_reinc' - overlap with risk levels

		// >3 is a real issue

        CERBER_VULN   => __( 'Vulnerability found', 'wp-cerber' ),
		CERBER_NOHASH => __( 'Integrity data not found', 'wp-cerber' ),
		6 => __( 'Unable to check the integrity of the plugin due to a network error', 'wp-cerber' ),
		7 => __( 'Unable to check the integrity of WordPress files due to a network error', 'wp-cerber' ),
		8 => __( 'Unable to check the integrity of the theme due to a network error', 'wp-cerber' ),
		9 => __( 'Unable to check the integrity due to a DB error', 'wp-cerber' ),

		10 => __( "Local file doesn't exist", 'wp-cerber' ),
		11 => 'No local hash found',
		13 => __( 'Unable to process file', 'wp-cerber' ),
		14 => __( 'Unable to open file', 'wp-cerber' ),

		CERBER_IMD => __( 'Checksum mismatch', 'wp-cerber' ), // Integrity

		// 16-25 PHP related -------------------------
		CERBER_SCF => __( 'Suspicious code found', 'wp-cerber' ),
		CERBER_PMC => __( 'Malicious code found', 'wp-cerber' ),
		CERBER_USF => __( 'Unattended suspicious file', 'wp-cerber' ),
		CERBER_EXC => __( 'Executable code found', 'wp-cerber' ),

		// Other -------------------------------------
		CERBER_DIR => __( 'Suspicious directives found', 'wp-cerber' ),
		CERBER_UXT => __( 'Unwanted file extension', 'wp-cerber' ),

		CERBER_MOD => __( 'Content has been modified', 'wp-cerber' ), // Previous scan
		CERBER_NEW => __( 'New file', 'wp-cerber' ),

		CERBER_FDUN => __( 'Unable to delete', 'wp-cerber' ),
		CERBER_FDLD => __( 'File deleted', 'wp-cerber' ),
		CERBER_FRCV => __( 'File recovered', 'wp-cerber' ),

	);

	if ( $id !== null ) {
		if ( is_array( $id ) ) {

			return array_intersect_key( $issues, array_flip( $id ) );
		}

		return $issues[ $id ];
	}

	return $issues;
}


function cerber_get_qs( $v = null ) {
	$q = array(
		0 => __( 'Disabled', 'wp-cerber' ),
		1 => __( 'Every hour', 'wp-cerber' ),
		3 => __( 'Every 3 hours', 'wp-cerber' ),
		6 => __( 'Every 6 hours', 'wp-cerber' ),
	);
	if ( $v ) {
		return $q[ $v ];
	}

	return $q;
}

/**
 * Log system errors for the current scan
 *
 * @param string $msg
 *
 * @return bool
 */
function cerber_log_scan_error( $msg = '' ) {

	$scan_id  = cerber_get_scan_id();
	$errors   = cerber_get_scan_errors();
	$errors[] = $msg;

	crb_scan_debug( 'ERROR: ' . $msg );

	return cerber_update_set( 'scan_errors', $errors, $scan_id );

}

function cerber_get_scan_errors() {

	$scan_id = cerber_get_scan_id();
	if ( ! $errors = cerber_get_set( 'scan_errors', $scan_id ) ) {
		$errors = array();
	}

	return $errors;

}

/**
 * Check the integrity of installed plugins
 *
 * @return int The number of plugins to process
 */
function cerber_verify_plugins() {
	if ( ! $scan_id = cerber_get_scan_id() ) {
		return 0;
	}

	$key = 'tmp_verify_plugins';
    $done = cerber_get_set( $key, $scan_id );

	$plugins = get_plugins();

	if ( $done ) {
		$to_scan = array_diff( array_keys( $plugins ), array_keys( $done ) );
	}
	else {
		$done    = array();
		$to_scan = array_keys( $plugins );
	}

	if ( empty( $to_scan ) ) {
		return 0;
	}

	$plugins_dir = cerber_get_plugins_dir() . DIRECTORY_SEPARATOR;
	$file_count  = 0;
	$bytes = 0;

	$max_files = 100;

	while ( ! empty( $to_scan ) ) {
		$plugin = array_shift( $to_scan );
		$issues = array();

		if ( false === strpos( $plugin, '/' ) ) {
			// A single-file plugin with no plugin folder (no hash on wordpress.org)
			$done[ $plugin ] = 1;

			if ( $plugin == 'hello.php' ) { // It's checked with WP hash
				continue;
			}

			$plugin_folder = $plugin;
		}
		else {
			$plugin_folder = dirname( $plugin );
		}

		crb_scan_debug( 'Verifying ' . $plugins[ $plugin ]['Name'] . ' ' . $plugins[ $plugin ]['Version'] );

		$plugin_hash = cerber_get_plugin_hash( $plugin_folder, $plugins[ $plugin ]['Version'] );

		if ( $plugin_hash && ! is_wp_error( $plugin_hash ) ) {
			foreach ( $plugin_hash->files as $file => $hash ) {

				if ( ! cerber_is_file_type_scan( $file ) ) {
					continue;
				}

				$file_name = $plugins_dir . $plugin_folder . DIRECTORY_SEPARATOR . cerber_normal_path( $file );
				$file_name_hash = sha1( $file_name );
				$where          = 'scan_id = ' . $scan_id . ' AND file_name_hash = "' . $file_name_hash . '"';
				$local_file     = cerber_db_get_row( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE ' . $where );

				if ( ! $local_file ) {
					$issues[] = array( 10, DIRECTORY_SEPARATOR . $plugin_folder . DIRECTORY_SEPARATOR . $file );
					continue;
				}

				if ( $local_file['scan_status'] != 0 ) {
					continue;
				}

				$short_name = cerber_get_short_name( $local_file['file_name'], $local_file['file_type'] );

				if ( empty( $local_file['file_hash'] ) ) {
					$issues[] = array( 11, $short_name, 'file' => $local_file );
					continue;
				}
				$hash_match = 0;
				if ( isset( $hash->sha256 ) ) {
					$repo_hash = $hash->sha256;
					if ( is_array( $repo_hash ) ) {
						$file_hash_repo = 'REPO provides multiple values, none match';
						foreach ( $repo_hash as $item ) {
							if ( $local_file['file_hash'] == $item ) {
								$hash_match     = 1;
								$file_hash_repo = $item;
								break;
							}
						}
					}
					else {
						$file_hash_repo = $repo_hash;
						if ( $local_file['file_hash'] == $repo_hash ) {
							$hash_match = 1;
						}
					}
				}
				else {
					$file_hash_repo = 'SHA256 hash not found';
				}

				$status = ( $hash_match ) ? CERBER_FOK : CERBER_IMD;

				if ( $status > CERBER_FOK ) {
					$issues[] = array( $status, $short_name, 'file' => $local_file );
				}

				cerber_db_query( 'UPDATE ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' SET file_hash_repo = "' . $file_hash_repo . '", hash_match = ' . $hash_match . ', scan_status = ' . $status . ' WHERE ' . $where );

				$file_count ++;
				$bytes += absint( $local_file['file_size'] );

			}

			$verified = 1;
		}
		else {
			$verified = cerber_verify_plugin( $plugin_folder, $plugins[ $plugin ] );
		}

		if ( ! $verified ) {
			$verified = 0;
			$status = CERBER_NOHASH;
		}
        else {
	        $verified = 1;
			$status = CERBER_FOK;
		}

		//$issues[] = array( $status, '', 'plugin' => $plugins[ $plugin ] );
		$issues[] = array( $status );

		$vuln = cerber_check_vulnerabilities( $plugin_folder, $plugins[ $plugin ] );

		if ( $vuln ) {
			foreach ( $vuln as $v ) {
				$issues[] = array( CERBER_VULN, $v['vu_info'] );
			}
		}

		$sec_details = array(
			$status,
			'plugin' => array( 'slug' => $plugin, 'Version' => $plugins[ $plugin ]['Version'] ),
			'vul_list' => $vuln
		);

		cerber_push_issues( $plugins[ $plugin ]['Name'], $issues, 'crb-plugins', $sec_details );

		cerber_set_scan( array( 'integrity' => array( 'plugins' => array( $plugin => $verified ) ) ) );

		$done[ $plugin ] = 1;

		if ( $file_count > $max_files || cerber_exec_timer() ) {
			break;
		}

	}

	cerber_update_set( $key, $done, $scan_id );

	return count( $to_scan );
}

/**
 * Checking the integrity of a plugin if there is no hash on wordpress.org
 *
 * @param string $plugin_folder Just folder, no full path, no slashes
 * @param array $plugin_data
 *
 * @return bool If true the plugin was verified by using an alternative source of hash
 */
function cerber_verify_plugin( $plugin_folder, $plugin_data ) {
	$ret  = false;
	$hash = null;

	// Is there local hash?

	$hash = cerber_get_local_hash( CRB_HASH_PLUGIN . sha1( $plugin_data['Name'] . $plugin_folder ), $plugin_data['Version'] );

	// Possibly remote hash?

	if ( ! $hash ) {

		$hash_url = null;

		if ( in_array( $plugin_folder, array( 'wp-cerber', 'wp-cerber-buddypress', 'wp-cerber-cloudflare-addon', 'jetflow' ) ) ) {
			$hash_url = 'https://my.wpcerber.com/downloads/checksums/' . $plugin_folder . '/' . $plugin_data['Version'] . '.json';
		}

		if ( $hash_url ) {
			$response = cerber_obtain_hash( $hash_url );
			if ( ! $response['error'] ) {
				$hash = get_object_vars( $response['server_data'] );
			}
			else {
				if ( ! empty( $response['curl_error'] ) ) {
					$msg = 'CURL ' . $response['curl_error'];
				}
                elseif ( ! empty( $response['json_error'] ) ) {
					$msg = 'JSON ' . $response['json_error'];
				}
				else {
					$msg = 'Unknown network error';
				}
				//$ret = new WP_Error( 'net_issue', $msg );
				cerber_log_scan_error( $msg );
			}
		}
	}

	if ( $hash ) {
		crb_scan_debug( 'Using local hash...' );
		$local_prefix = cerber_get_plugins_dir() . DIRECTORY_SEPARATOR;
		if ( ! strpos( $plugin_folder, '.' ) ) { // Not a single file plugin
			$local_prefix .= $plugin_folder . DIRECTORY_SEPARATOR;
		}
		list( $issues, $errors ) = cerber_verify_files( $hash, 'file_hash', $local_prefix );

		$sec_details = array(
			'plugin' => array( 'slug' => $plugin_folder, 'Version' => $plugin_data['Version'] ),
		);

		cerber_push_issues( $plugin_data['Name'], $issues, 'crb-plugins', $sec_details );
		if ( ! $errors ) {
			$ret = true;
		}
	}

	return $ret;
}

/**
 * Verifying the integrity of the WordPress
 *
 * @return int
 */
function cerber_verify_wp() {

	$wp_version = cerber_get_wp_version();
	$ret        = 0;
	$verified   = 0;
	$wp_hash    = cerber_get_wp_hash();

	if ( ! is_wp_error( $wp_hash ) ) {

	    // In case the default name 'plugins' of the plugins folder has been changed
		$wp_plugins_dir = basename( cerber_get_plugins_dir() );
		if ( $wp_plugins_dir != 'plugins' ) {
			$new_data = array();
			foreach ( $wp_hash as $key => $item ) {
				if ( 0 === strpos( $key, 'wp-content/plugins/' ) ) {
					$new_data[ 'wp-content/' . $wp_plugins_dir . '/' . substr( $key, 19 ) ] = $item;
				}
				else {
					$new_data[ $key ] = $item;
				}
			}
			$wp_hash = $new_data;
		}

		// In case the default name 'wp-content' of the CONTENT folder has been changed
		$wp_content_dir = basename( cerber_get_content_dir() );
		if ( $wp_content_dir != 'wp-content' ) {
			$new_data = array();
			foreach ( $wp_hash as $key => $item ) {
				if ( 0 === strpos( $key, 'wp-content/' ) ) {
					$new_data[ $wp_content_dir . '/' . substr( $key, 11 ) ] = $item;
				}
				else {
					$new_data[ $key ] = $item;
				}
			}
			$wp_hash = $new_data;
		}

		list( $issues, $errors ) = cerber_verify_files( $wp_hash, 'file_md5', ABSPATH, array(CERBER_FT_PLUGIN, CERBER_FT_THEME), CERBER_FT_WP, '_crb_not_existing' );
		if ( ! $errors ) {
			$verified = 1;
			$status   = CERBER_FOK;
		}
		else {
			$status = 9;
		}
		cerber_push_issues( 'WordPress', array( array( $status, CERBER_PK_WP => $wp_version ) ) );
		cerber_push_issues( 'WordPress', $issues );
	}
	else {
		cerber_push_issues( 'WordPress', array( array( 7, CERBER_PK_WP => $wp_version ) ) );
	}

	cerber_set_scan( array( 'integrity' => array( CERBER_PK_WP => $verified ) ) );

	return $ret;
}

// Themes and plugin will be checked separately, not as a part of WP
function _crb_not_existing( $file_name ) {
	static $themes_prefix, $plugins_prefix;

	if ( $themes_prefix == null ) {
		$themes_prefix = basename( cerber_get_content_dir() ) . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR;
	}
	if ( 0 === strpos( $file_name, $themes_prefix ) ) {
		return false;
	}

	if ( $plugins_prefix == null ) {
		$plugins_prefix = basename( cerber_get_content_dir() ) . DIRECTORY_SEPARATOR . basename( cerber_get_plugins_dir() ) . DIRECTORY_SEPARATOR;
	}
	if ( 0 === strpos( $file_name, $plugins_prefix ) ) {
		return false;
	}

	return true;
}

/**
 * Verifying the integrity of the themes
 *
 * @return int
 */
function cerber_verify_themes() {

	$themes = wp_get_themes();

	foreach ( $themes as $theme_folder => $theme ) {
		$issues = array();
		$hash = cerber_get_theme_hash( $theme_folder, $theme );
		$verified = 0;

		if ( $hash && ! is_wp_error( $hash ) ) {
			$local_prefix = cerber_get_themes_dir() . DIRECTORY_SEPARATOR . $theme_folder . DIRECTORY_SEPARATOR;
			list( $issues, $errors ) = cerber_verify_files( $hash, 'file_hash', $local_prefix, null, CERBER_FT_THEME );
			if ( ! $errors ) {
				$verified = 1;
				$status   = CERBER_FOK;
			}
			else {
				$status = 9;
			}
		}
		else {
			if ( is_wp_error( $hash ) ) {
				crb_scan_debug( $hash->get_error_message() );
				//cerber_log_scan_error( $hash->get_error_message() );
			}
			$status = CERBER_NOHASH;
		}

		$issues[] = array( $status, $theme_folder, 'theme' => $theme );

		cerber_set_scan( array( 'integrity' => array( 'themes' => array( $theme_folder => $verified ) ) ) );

		if ( $issues ) {
			cerber_push_issues( $theme->get( 'Name' ), $issues, 'crb-themes' );
		}
	}

	return 0;
}

/**
 * Inspecting unattended files (remain after integrity checking) for traces of malware and other issue
 *
 * @return int
 */
function cerber_process_files(){

	if ( ! $scan = cerber_get_scan() ) {
		return 0;
	}

	$not_in = CERBER_FOK . ',14,' . CERBER_IMD;

	/*
	if ( !$files = cerber_db_get_results( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE .
                                         ' WHERE scan_id = ' . $scan['id'] . ' AND scan_status NOT IN ('.$not_in.')' ) ) {
		return 0;
	}*/

	$in = '0';
	$remain = 0;

/*	if ( ! $files = cerber_db_get_results( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE .
	                                       ' WHERE scan_id = ' . $scan['id'] . ' AND scan_status IN (' . $in . ')' ) ) {
		return 0;
	}
*/

	if ( ! $files = cerber_db_get_results( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE .
	                                       ' WHERE scan_id = ' . $scan['id'] . ' AND scan_status IN (' . $in . ') LIMIT ' . CRB_SQL_CHUNK ) ) {
		return 0;
	}

	if ( count( $files ) >= CRB_SQL_CHUNK ) {
		$remain = 1;
	}

	// Plugins data -------------------

	$plugins = array();
	foreach ( get_plugins() as $key => $item ) {
		if ( $pos = strpos( $key, DIRECTORY_SEPARATOR ) ) {
			$new_key = substr( $key, 0, strpos( $key, DIRECTORY_SEPARATOR ) );
		}
		else {
			$new_key = $key;
		}

		$plugins[ $new_key ] = $item;
		if ( ! empty( $scan['integrity']['plugins'][ $key ] ) ) {
			$plugins[ $new_key ]['integrity'] = true;
		}
	}

	// ---------------------------------------------------------------------------

	// Themes data -------------------

    $themes = wp_get_themes();

	// ---------------------------------------------------------------------------

	$can_be_deleted = array( CERBER_FT_UPLOAD, CERBER_FT_CNT, CERBER_FT_OTHER, CERBER_FT_LNG );

	$issues = array();

	// Prevent hanging
	if ( $f = cerber_get_set( CRB_LAST_FILE, 0, false ) ) {
		cerber_update_fscan_status( sha1( $f ), 13, $scan['id'] );
		cerber_update_set( CRB_LAST_FILE, '', 0, false );
		$m = cerber_get_issue_label( 13 ) . ' ' . $f . ' size: ' . @filesize( $f ) . ' bytes';
		cerber_log_scan_error( $m );
	}

	$counter = 0;

	foreach ( $files as $file ) {

		$integrity_verified = false;
		$severity_limit     = 6;
		$status             = CERBER_USF;
		$section            = '';
		$do_not_del         = false;

		switch ( $file['file_type'] ) {
			case CERBER_FT_WP:
				$section    = 'WordPress';
				$do_not_del = true;
				if ( ! empty( $scan['integrity'][ CERBER_PK_WP ] ) ) {
					$integrity_verified = true;
				}
				break;
			case CERBER_FT_PLUGIN:
				$f = cerber_get_file_folder( $file['file_name'], cerber_get_plugins_dir() );
				if ( isset( $plugins[ $f ] ) ) {
					$section    = $plugins[ $f ]['Name'];
					$do_not_del = true;
					if ( ! empty( $plugins[ $f ]['integrity'] ) ) {
						$integrity_verified = true;
					}
				}
				else {
					$severity_limit = 1;
				}
				break;
			case CERBER_FT_THEME:
				$f = cerber_get_file_folder( $file['file_name'], cerber_get_themes_dir() );
				if ( isset( $themes[ $f ] ) ) {
					$section    = $themes[ $f ]->get( 'Name' ); // WP_Theme object
					$do_not_del = true;
					if ( ! empty( $scan['integrity']['themes'][ $f ] ) ) {
						$integrity_verified = true;
					}
					$severity_limit = 5;
				}
				else {
					$severity_limit = 1;
				}
				//$status = 1;
				break;
			case CERBER_FT_ROOT:
				if ( cerber_is_htaccess( $file['file_name'] ) ) {
					$section = 'WordPress';
					$status = CERBER_FOK;
				}
				if ( ! empty( $scan['integrity'][ CERBER_PK_WP ] ) ) {
					//$integrity_verified = true;
					$do_not_del = false;
				}
				else {
					$do_not_del = true;
				}
				$severity_limit = 1;
				break;
			case CERBER_FT_CONF:
				$section        = 'WordPress';
				$do_not_del     = true;
				$severity_limit = 2;
				break;
			case CERBER_FT_UPLOAD:
				$section        = 'Uploads folder';
				$severity_limit = 1;
				break;
			case CERBER_FT_MUP:
				$section    = 'Must-use plugins';
				$do_not_del = true;
				break;
			case CERBER_FT_OTHER:
				$severity_limit = 1;
				break;
			case CERBER_FT_DRIN:
				$section = 'Drop-ins';
				break;
			default:
				$severity_limit = 2;
				break;

		}

		// Now we're ready to perform inspection

		$result = array();

		if ( ! $integrity_verified ) {

			$result = cerber_inspect_file( $file['file_name'] );

			// TODO: refactor this!
			if ( ! is_wp_error( $result ) ) {
				$status = CERBER_FOK;
				if ( $result['severity'] == CERBER_MALWR_DETECTED ) {
					$status = CERBER_PMC;
				}
				/*
				elseif ( $result['severity'] == $severity_limit ) {
					$status = CERBER_USF;
				}*/
                elseif ( $result['severity'] >= $severity_limit ) {
					if ( $result['severity'] == 1 ) {
						$status = CERBER_EXC;
					}
					else {
						if ( cerber_is_htaccess( $file['file_name'] ) ) {
							$status = CERBER_DIR;
						}
						else {
							$status = CERBER_SCF;
						}
					}
				}
			}
			else {
				$status = 14;
			}

		}

		// An exception for wp-config.php
		if ( $status == CERBER_USF && $file['file_type'] == CERBER_FT_CONF ) {
			$status = CERBER_FOK;
		}

		// Unwanted extensions
		if ( $status == CERBER_FOK ) {
			if ( cerber_has_extension( $file['file_name'], 'scan_uext' ) ) {
				$status = CERBER_UXT;
			}
		}


		if ( $status == CERBER_FOK && $file['file_status'] > 0 ) {
			$status = $file['file_status'];
		}

		// This file must be included in the list of issue
		if ( $status > CERBER_FOK ) {

			if ( ! $section ) {
				$section = 'Unattended files';

				$len = 0;
				if ( 0 === strpos( $file['file_name'], rtrim( cerber_get_abspath(), '/\\' ) ) ) {
					$len = mb_strlen( cerber_get_abspath() ) - 1;
				}
				if ( $len ) {
					$short_name = mb_substr( $file['file_name'], $len );
				}
				else {
					$short_name = $file['file_name'];
				}
			}
			else {
				$short_name = cerber_get_short_name( $file['file_name'], $file['file_type'] );
			}

			// Is file can be deleted?

			if ( $status >= CERBER_SCF ) {
				if ( $integrity_verified ) {
					$file['fd_allowed'] = 1;
				}
                elseif ( ! $do_not_del || in_array( $file['file_type'], $can_be_deleted ) ) {
					$file['fd_allowed'] = 1;
				}
			}

			$issues[ $section ][] = array( $status, $short_name, $result, 'file' => $file );
		}

		cerber_update_fscan_status( $file['file_name_hash'], $status , $scan['id'] );

		// Limits on time and the number of files per single step
		if ( 0 === ( $counter % 100 ) ) {
			if ( cerber_exec_timer() ) {
				$remain = 1;
				break;
			}
		}
		if ( $counter > 1000 ) {
			$remain = 1;
			break;
		}
		$counter ++;
	}


	if ( $issues ) {
		foreach ( $issues as $section => $list ) {
			cerber_push_issues( $section, $list );
		}
	}

	return $remain;
}

/**
 * Scan a file for suspicious and malicious code
 *
 * @param string $file_name
 *
 * @return array|bool|WP_Error
 */
function cerber_inspect_file( $file_name = '' ) {
	global $cerber_scan_mode, $wp_cerber;

	if ( !@is_file( $file_name ) ) {
		return false;
	}

	if ( cerber_is_htaccess( $file_name ) ) {
		return cerber_inspect_htaccess( $file_name );
	}

	if ( ! cerber_check_extension( $file_name, array( 'php', 'phtm', 'phtml', 'phps', 'php2', 'php3', 'php4', 'php5', 'php6', 'php7', 'inc' ) ) ) {
		$php = false;

		if ( $cerber_scan_mode == 'full' ) {
			// Try to find an PHP open tag in the content
			if ( $f = @fopen( $file_name, 'r' ) ) {
				$str = fread( $f, 100000 );
				if ( false !== strrpos( $str, '<?php' ) ) {
					$php = true;
				}
				fclose( $f );
			}
			else {
				cerber_log_scan_error( cerber_scan_msg( 0, $file_name ) );
            }
		}

		if ( ! $php ) {
			return array( 'severity' => 0 );
		}
	}

	cerber_update_set( CRB_LAST_FILE, $file_name, 0, false );
	$result = cerber_inspect_php( $file_name );
	cerber_update_set( CRB_LAST_FILE, '', 0, false );

	if ( is_wp_error( $result ) ) {
		cerber_log_scan_error( $result->get_error_message() );
		return $result;
	}



	return $result;
}

/**
 * Scan a file for suspicious and malicious PHP code
 *
 * @param string $file_name
 *
 * @return array|bool|WP_Error
 */
function cerber_inspect_php( $file_name = '' ) {
    static $patterns;

	if ( false === ( $content = @file_get_contents( $file_name ) ) ) {
		return new WP_Error( 'cerber-file', cerber_scan_msg( 0, $file_name ) );
	}

	$important = array( T_STRING, T_EVAL );

	$tokens = @token_get_all( $content );
	unset( $content );
	if ( ! $tokens ) {
		return array( 'severity' => 0 ); // weird
	}

	$code_found = 0; // Any PHP code in the file = 1
	$severity = array();
	$xdata = array();
	$pos  = array();
	$open = null;
	$list = cerber_get_php_unsafe();

	foreach ( $tokens as $token ) {
		if ( ! is_array( $token ) ) {
			continue;
		}
		if ( in_array( $token[0], $important ) ) {
			$code_found = 1;
			if ( isset( $list[ $token[1] ] ) ) {
				$xdata[]    = array( 1, $token[1], $token[2], $token[0] );
				$severity[] = $list[ $token[1] ][0];
			}
		}
		if ( $token[0] == T_CONSTANT_ENCAPSED_STRING ) {
			if ( $val = cerber_is_base64_encoded( trim( $token[1], '\'"' ) ) ) {
				if ( cerber_inspect_value( $val ) ) {
					$xdata[] = array( 1, 'base64_encoded_php', $token[2], $token[0], $token[1] );
					$severity[] = CERBER_MALWR_DETECTED;
				}
				/*
				else { // obsolete since 7.6.4
					$xdata[] = array( 1, 'base64_encoded_string', $token[2], $token[0], $token[1] );
				}
				$severity[] = 10;*/
			}
		}
		if ( $token[0] == T_OPEN_TAG ) {
			$open = $token[2] - 1;
		}
		if ( $open && ( $token[0] == T_CLOSE_TAG ) ) {
			$pos[] = array( $open, $token[2] - 1 );
			$open  = null;
		}
	}
	if ( $open !== null ) { // No closing tag till the end of the file
		$pos[] = array( $open, null );
	}

	if ( empty( $pos ) ) {
		return false;
	}
	if ( ! $lines = @file( $file_name ) ) {
		return new WP_Error( 'cerber-file', cerber_scan_msg( 0, $file_name ) );
	}

	$code  = array();
	$last  = count( $pos ) - 1;

	foreach ( $pos as $k => $p ) {
		if ( $last == $k ) {
			$length = null;
		}
		else {
			$length = $p[1] - $p[0] + 1;
		}
		$code = $code + array_slice( $lines, $p[0], $length, true );
	}

	//unset( $lines );
	$code = implode( "\n", $code );
	$code = cerber_remove_comments( $code );
	$code = preg_replace( "/[\r\n\s]+/", '', $code );

	if ( ! $code ) {
		return false;
	}

	// Check for malicious code patterns

	if ( ! $patterns ) {
		$patterns = cerber_get_php_patterns();
	}

	/*
	foreach ( $patterns as $pa ) {
	    if ($pa[1] == 2) { // 2 = REGEX
		    $matches = array();
		    if ( preg_match_all( '/' . $pa[2] . '/i', $code, $matches, PREG_OFFSET_CAPTURE ) ) {

		        if ( ! empty( $pa['not_func'] ) && function_exists( $pa['not_func'] ) ) {
				    foreach ( $matches[0] as $key => $match ) {
					    if ( call_user_func( $pa['not_func'], $match[0] ) ) {
						    unset( $matches[0][ $key ] );
					    }
				    }
			    }

			    if ( ! empty( $pa['func'] ) && function_exists( $pa['func'] ) ) {
				    foreach ( $matches[0] as $key => $match ) {
					    if ( ! call_user_func( $pa['func'], $match[0] ) ) {
						    unset( $matches[0][ $key ] );
					    }
				    }
			    }

			    if ( ! empty( $matches[0] ) ) {
				    $xdata[]    = array( 2, $pa[0], array_values( $matches[0] ) );
				    $severity[] = $pa[3];
			    }
		    }
	    }
	    else {
		    if ( false !== stripos( $code, $pa[2] ) ) {
			    $xdata[]    = array( 2, $pa[0], array( array( $pa[2] ) ) );
			    $severity[] = $pa[3];
		    }
	    }
	}
    */

	list ($x, $s) = cerber_process_patterns($code, $patterns);
	if ( ! empty( $x ) ) {
		$xdata    = array_merge( $xdata, $x );
		$severity = array_merge( $severity, $s );
	}

	// Try to find line numbers for matches
	if ( $xdata ) {
		foreach ( $xdata as $x => $d ) {
			if ( $d[0] != 2 || ! isset( $d[2] ) ) {
				continue;
			}
			foreach ( $d[2] as $y => $m ) {
				foreach ( $lines as $i => $line ) {
					if ( false !== strrpos( $line, $m[0] ) ) {
						$xdata[ $x ][2][ $y ][2] = $i + 1;
						break;
					}
				}
				if ( ! isset( $xdata[ $x ][2][ $y ][2] ) ) {
					$xdata[ $x ][2][ $y ][2] = '?';
				}
			}
		}
	}

	unset( $lines );

	// An attempt to interpret the results

	$max = 0;

	if ( $severity ) {
		$malwr_found        = false;
		$malwr_combinations = array( array( 10, 7 ), array( 9, 7 ) );
		foreach ( $malwr_combinations as $malwr ) {
			if ( $int = array_intersect( $malwr, $severity ) ) {
				if ( count( $malwr ) == count( $int ) ) {
					$malwr_found = true;
				}
			}
		}

		$max = ( $malwr_found ) ? CERBER_MALWR_DETECTED : max( $severity );
	}

	if ( $code_found && ! $max ) {
		$max = $code_found;
	}

	return array( 'severity' => $max, 'xdata' => $xdata );

}

/**
 * Unsafe code tokens
 *
 * @return array
 */
function cerber_get_php_unsafe(){
	return array(
		'base64_encoded_string' => array( 3, 'Base64 encoded string found.' ),
		'base64_encoded_php' => array( 10, 'Base64 encoded malware found.' ),

		'system' => array( 10, 'May be used to get/change vital system information or to run arbitrary server software.' ),
		'shell_exec' => array(10, 'Executes arbitrary command via shell and returns the complete output as a string.'),
		'exec' => array(10, 'Executes arbitrary programs on the web server.'),
		'assert' => array(10, 'Allows arbitrary code execution.'),
		'passthru' => array(10,'Executes arbitrary programs on the web server and displays raw output.'),
		'pcntl_exec' => array(10, 'Executes arbitrary programs on the web server in the current process space.'),
		'proc_open' => array(10, 'Executes an arbitrary command on the web server and open file pointers for input/output.'),
		'popen' => array(10, 'Opens a process (execute an arbitrary command) file pointer on the web server.'),
		'dl' => array(10, 'Loads a PHP extension on the web server at runtime.'),
		'eval' => array( 9, 'May be used to execute malicious code on the web server. Pairing with base64_decode function indicates malicious code.' ),
		'str_rot13' => array(9, 'Perform the rot13 transform on a string. Can be used to obfuscate malware.'),
		'mysql_connect' => array(9, 'Open a new connection to the MySQL server'),
		'mysqli_connect' => array(9, 'Open a new connection to the MySQL server'),
		'mysql_query' => array(9, 'Performs a query on the database'),
		'mysqli_query' => array(9, 'Performs a query on the database'),

        'base64_decode' => array(7, 'May be used to obfuscate and hinder detection of malicious code. Pairing with eval function indicates malicious code.'),
		'socket_create' => array(6, 'Creates a network connection with any remote host. May be used to load malicious code from any web server with no restrictions.'),
		'create_function' => array(6, 'Create an anonymous (lambda-style) function. Deprecated. A native anonymous function must be used instead.'),

		'hexdec' => array(5, 'Hexadecimal to decimal. Can be used to obfuscate malware.'),
		'dechex' => array(5, 'Decimal to hexadecimal. Can be used to obfuscate malware.'),

		'chmod' => array(5, 'Changes file access mode.'),
		'chown' => array(5, 'Changes file owner.'),
		'chgrp' => array(5, 'Changes file group.'),
		'symlink' => array(5, 'Creates a symbolic link to the existing file.'),
		'unlink' => array(5, 'Deletes a file.'),

		'gzinflate' => array(4, 'Inflate a deflated string. Can be used to obfuscate malware.'),
		'gzdeflate' => array(4, 'Deflate a string. Can be used to obfuscate malware.'),

		'curl_init' => array(4, 'Load external data from any web server. May be used to load malicious code from any web server with no restrictions.'),
		'curl_exec' => array(4, 'Load external data from any web server. May be used to load malicious code from any web server with no restrictions.'),
		'file_get_contents' => array(4, 'Read the entire file into a string. May be used to load malicious code from any web server with no restrictions.'),

        'wp_remote_request' => array(3, 'Load data from any web server. May be used to load malicious code from an external source.'),
		'wp_remote_get' => array(3, 'Load external data from any web server. May be used to load malicious code from an external source.'),
		'wp_remote_post' => array(3, 'Upload or download data from/to any web server. May be used to load malicious code from an external source.'),
		'wp_safe_remote_post' => array(3, 'Upload or download data from/to any web server. May be used to load malicious code from an external source.'),
		'wp_remote_head' => array(3, 'Load data from any web server. May be used to load malicious code from an external source.'),

		'call_user_func' => array(2, 'Call any function given by the first parameter. May be used to run malicious code or hinder code inspection.'),
		'call_user_func_array' => array(2, 'Call any function with an array of parameters. May be used to run malicious code or hinder code inspection.'),

        'fputs' => array(1, ''),
		'flock' => array(1, ''),
		'getcwd' => array(1, ''),
		'setcookie' => array(1, ''),
		'php_uname' => array(1, ''),
		'get_current_user' => array(1, ''),
		'fileperms' => array(1, ''),
		'getenv' => array(1, ''),
		'phpinfo' => array(1, ''),
		'header' => array(1, ''),
		'add_filter' => array(1, 'Can alter any website data or website settings'),
		'add_action' => array(1, ''),
        'unserialize' => array(1, 'Can pose a serious security threat if it processes unfiltered user input'),

	);
}

/**
 * Unsafe code patterns/signatures
 *
 * @return array
 */
function cerber_get_php_patterns() {
	$list = array(
		array( 'VARF', 2, '(?<!\w)\$[a-z0-9\_]+?\((?!\))', 9, 'A variable function call. Usually is used to hinder malware detection.' ), // pattern with function parameter(s): $example(something)
		//array( 'IPV4', 2, '(?:[0-9]{1,3}\.){3}[0-9]{1,3}', 6, 'A suspicious external IPv4 address found. Can cause data leakage.', 'func' => '_is_ip_external' ),
		//array( 'IPV6', 2, '(?:[A-F0-9]{1,4}:){7}[A-F0-9]{1,4}', 6, 'A suspicious external IPv6 address found. Can cause data leakage.', 'func' => '_is_ip_external' ),
		array( 'BCTK', 2, '`[a-z]+`', 10, 'Execute arbitrary command on the web server' ),
		array( 'PIDT', 2, 'data:\/\/[A-Z0-9]+', 6, 'Process data in a non-standard way. Can be used to obfuscate malware.' ),

		array( 'PIDT', 3, 'php://input', 6, 'Get data or commands from the Internet. Should be used in trusted or verified software only' ),
		array( 'NGET', 3, '$_GET', 3, 'Get data or commands from the Internet. Should be used in trusted or verified software only' ),
		array( 'NPST', 3, '$_POST', 3, 'Get data or commands from the Internet. Should be used in trusted or verified software only' ),
		array( 'NREQ', 3, '$_REQUEST', 3, 'Get data or commands from the Internet. Should be used in trusted or verified software only' ),

        // Should be in a separate data set for non-php files
        //array( 'SHL1', 3, '#!/bin/sh', 6, 'Executable shell script' ),
	);

	$list = array_merge( cerber_get_ip_patterns(), $list );

	if ( $custom = crb_get_settings( 'scan_cpt' ) ) {
		foreach ( $custom as $i => $p ) {
			if ( substr( $p, 0, 1 ) == '{' && substr( $p, - 1 ) == '}' ) {
				$p = substr( $p, 1, - 1 );
				$t = 2;
			}
			else {
				$t = 3;
			}
			$list[] = array( 'CUS' . $i, $t, $p, 4, __( 'Custom signature found', 'wp-cerber' ) );
		}
	}

	return $list;
}

function cerber_get_ip_patterns() {
    return array(
	    array( 'IPV4', 2, '(?:[0-9]{1,3}\.){3}[0-9]{1,3}', 6, 'A suspicious external IPv4 address found. Can cause data leakage.', 'func' => '_is_ip_external' ),
	    array( 'IPV6', 2, '(?:[A-F0-9]{1,4}:){7}[A-F0-9]{1,4}', 6, 'A suspicious external IPv6 address found. Can cause data leakage.', 'func' => '_is_ip_external' ),
    );
}

function cerber_get_js_patterns() {
	$list = array(
		array( 'EWEB', 2, '(https?:\/\/[^\s]+)', 10, 'An obfuscated external link found.' ),
		array( 'EFTP', 2, '(ftps?:\/\/[^\s]+)', 10, 'An obfuscated external link found.' ),
	);
	$list = array_merge( cerber_get_ip_patterns(), $list );

	return $list;
}

function cerber_get_ht_patterns() {
	static $ret;
	if ( $ret !== null ) {
		return $ret;
	}
	$ret = array(
		//array( 'R4IP', 2, '(?:[0-9]{1,3}\.){3}[0-9]{1,3}', 6, 'A suspicious redirection to another, probably phishing website.', 'func' => '_is_rewrite_rule' ),
		//array( 'R6IP', 2, '(?:[A-F0-9]{1,4}:){7}[A-F0-9]{1,4}', 6, 'A suspicious redirection to another, probably phishing website.', 'func' => '_is_rewrite_rule' ),
		array( 'IPV4', 2, '(?:[0-9]{1,3}\.){3}[0-9]{1,3}', 6, 'A suspicious external IPv4 address found. Can cause data leakage.', 'func' => '_is_ip_external' ),
		array( 'IPV6', 2, '(?:[A-F0-9]{1,4}:){7}[A-F0-9]{1,4}', 6, 'A suspicious external IPv6 address found. Can cause data leakage.', 'func' => '_is_ip_external' ),
		array( 'RWEB', 2, '(https?:\/\/[^\s]+\.?)', 6, 'A suspicious redirection to another, probably phishing website.', 'func' => '_is_unsafe_redirect_rule' ),
		array( 'RFTP', 2, '(ftps?:\/\/[^\s]+\.?)', 10, 'A suspicious redirection to another, probably phishing website.', 'func' => '_is_unsafe_redirect_rule' ),
		array( 'PHPC', 2, 'php_value\s+(.+)', 10, 'An unsafe, suspicious PHP configuration command. Normally must not be here.', 'func' => '_is_unsafe_php_value' ),
	);

	return $ret;
}

function cerber_inspect_htaccess( $file_name = '' ) {
	if ( false === ( $lines = @file( $file_name ) ) ) {
		return new WP_Error( 'cerber-file', cerber_scan_msg( 0, $file_name ) );
	}

	$pats = cerber_get_ht_patterns();
	$severity = array();
	$xdata = array();

	foreach ( $lines as $n => $line ) {
		if ( false !== ( $p = strpos( $line, '#' ) ) ) {
			$line = substr( $line, 0, $p );
		}
		$line = trim( $line );
		if ( ! $line ) {
			continue;
		}

		foreach ( $pats as $pa ) {
			if ($pa[1] == 2) { // 2 = REGEX
				$matches = array();
				if ( preg_match_all( '/' . $pa[2] . '/i', $line, $matches, PREG_OFFSET_CAPTURE ) ) {

					if ( ! empty( $pa['not_func'] ) && function_exists( $pa['not_func'] ) ) {
						foreach ( $matches[0] as $key => $match ) {
							if ( call_user_func( $pa['not_func'], $match[0], $line ) ) {
								unset( $matches[0][ $key ] );
							}
						}
					}

					if ( ! empty( $pa['func'] ) && function_exists( $pa['func'] ) ) {
						foreach ( $matches[0] as $key => $match ) {
							if ( ! call_user_func( $pa['func'], $match[0], $line ) ) {
								unset( $matches[0][ $key ] );
							}
						}
					}

					if ( ! empty( $matches[0] ) ) {
						$m = array_values( $matches[0] );
						$m[0][2] = $n + 1;
						$xdata[]    = array( 2, $pa[0], $m );
						$severity[] = $pa[3];
					}
				}
			}
			else {
				if ( false !== stripos( $line, $pa[2] ) ) {
					$xdata[]    = array( 2, $pa[0], array( array( $pa[2], 0, $n + 1 ) ) );
					$severity[] = $pa[3];
				}
			}
		}

	}

	$max = 0;
	if ( $severity ) {
		$max = max( $severity );
	}

	return array( 'severity' => $max, 'xdata' => $xdata );

}

function _is_unsafe_php_value( $found, $line ) {
	$cmd_list = array( 'asp_tags', 'auto_append_file', 'auto_prepend_file', 'register_globals', 'include_path', 'open_basedir', 'user_ini', 'upload_tmp_dir' );
	if ( false !== crb_stripos_multi( $found, $cmd_list ) ) {
		return true;
	}

	return false;
}

function _is_unsafe_redirect_rule( $found, $line ) {
    static $allowed, $coms;

	$line = trim( $line );

	if ( ! $coms ) {
		$coms = array( 'RewriteRule', 'RewriteMap', 'ErrorDocument' );
	}

	if ( 0 !== crb_stripos_multi( $line, $coms ) ) {
		return false;
	}

	if ( ! $allowed ) {
		$allowed = array( cerber_get_home_url(), 'https://%{HTTP_HOST}', 'http://%{HTTP_HOST}' );
	}

	if ( 0 !== crb_stripos_multi( $found, $allowed ) ) {
		return true;
	}

	return false;
}

function crb_stripos_multi( &$str, &$list ) {
	foreach ( $list as $item ) {
		$pos = stripos( $str, $item );
		if ( false !== $pos ) {
			return $pos;
		}
	}

	return false;
}

function _is_ip_external( $ip ) {
	if ( is_ip_private( $ip ) ) {
		return false;
	}
	if ( defined( 'DB_HOST' ) && DB_HOST === $ip ) {
		return false;
	}

	return true;
}


function cerber_get_strings() {
	$data    = array();
	$data[1] = cerber_get_php_unsafe();
	$list    = array();
	$pats    = array_merge( cerber_get_php_patterns(), cerber_get_ht_patterns() );
	foreach ( $pats as $p ) {
		$list[ $p[0] ] = $p[4];
	}
	$data[2] = $list;

	$data['explain'] = array(
		__( 'This file contains executable code and may contain obfuscated malware. If this file is a part of a theme or a plugin, it must be located in the theme or the plugin folder. No exception, no excuses.' ),
		__( 'The scanner recognizes this file as "ownerless" or "not bundled" because it does not belong to any known part of the website and should not be here.' ),
		__( 'It may remain after upgrading to a newer version of %s. It also may be a piece of obfuscated malware. In a rare case it might be a part of a custom-made (bespoke) plugin or theme.' ),
		__( 'Suspicious code instruction found', 'wp-cerber' ),
		__( 'Suspicious code signatures found', 'wp-cerber' ),
		__( 'Suspicious directives found', 'wp-cerber' ),
		__( 'The contents of the file have been changed and do not match what exists in the official WordPress repository or a reference file you have uploaded earlier. The file may have been altered by malware, infected by a virus or has been tampered with.' ),
		__( 'To solve this issue you have to reinstall %s or update it to the latest version.', 'wp-cerber' ),
		__( 'Please upload a reference ZIP archive', 'wp-cerber' ),
		__( 'Resolve issue', 'wp-cerber' ),
	);

	$data['complete'] = 1;

	return $data;
}

/**
 * Verify files using hash data provided as array of $file_name => $hash
 *
 * @param array $hash_data Hash
 * @param string $field Name of DB table field with local hash
 * @param string $local_prefix  Local filename prefix
 * @param int $set_type If set, the file type will be set to this value
 * @param callable $func If a local file doesn't exist it will be saved as an issue if return true
 *
 * @return array Possibly DB Errors + List of issues found
 */
function cerber_verify_files( $hash_data, $field = 'file_hash', $local_prefix = '', $type_not_in = array(), $set_type = null, $func = null ) {
	if ( ! $scan = cerber_get_scan() ) {
		return array();
	}

	$set_type = absint( $set_type );
	$issues = array();
	$errors = 0;
	$file_count = 0;

	if ( !$func || !function_exists( $func ) ) {
		$func = null;
	}

	$table = cerber_get_db_prefix() . CERBER_SCAN_TABLE;

	$local_prefix = cerber_normal_path( $local_prefix );

	foreach ( $hash_data as $file_name => $hash ) {

		if ( ! cerber_is_file_type_scan( $file_name ) ) {
			continue;
		}

		$file_name = cerber_normal_path( $file_name );

		$file_name_hash = sha1( $local_prefix . $file_name );
		$where          = 'scan_id = ' . $scan['id'] . ' AND file_name_hash = "' . $file_name_hash . '"';

        $local_file = cerber_db_get_row( 'SELECT * FROM ' . $table . ' WHERE ' . $where );

		if ( ! $local_file ) {
			if ( $func ) {
				if ( ! call_user_func( $func, $file_name ) ) {
					continue;
				}
			}
			$issues[] = array( 10, DIRECTORY_SEPARATOR . ltrim( $file_name, DIRECTORY_SEPARATOR ) );
			continue;
		}

		if ( $local_file['scan_status'] != 0 ) {
			continue;
		}

		if ( ! empty( $type_not_in ) && in_array( $local_file['file_type'], $type_not_in ) ) {
			continue;
		}

		$short_name = cerber_get_short_name( $local_file['file_name'], $local_file['file_type'] );

		if ( empty( $local_file[ $field ] ) ) {
			$issues[] = array( 11, $short_name, 'file' => $local_file );
			continue;
		}

		$hash_match = ( $local_file[ $field ] === $hash ) ? 1 : 0;

		$status = ( $hash_match ) ? CERBER_FOK : CERBER_IMD;

		if ( $status > CERBER_FOK ) {
			$issues[] = array( $status, $short_name, 'file' => $local_file );
		}

		$file_type = ( ! empty( $set_type ) ) ? $set_type : $local_file['file_type'];

		if ( ! cerber_db_query( 'UPDATE ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' SET file_type = ' . $file_type . ', file_hash_repo = "' . $hash . '", hash_match = ' . $hash_match . ', scan_status = ' . $status . ' WHERE ' . $where ) ) {
			$errors++;
		}

		$file_count ++;

	}

	return array( $issues, $errors );
}

/**
 * Retrieve hash for a given plugin from wordpress.org
 *
 * @param $plugin string  Plugin folder
 * @param $ver string Plugin version
 * @param $nocache bool If true, do not use data from the local cache (refresh one)
 *
 * @return WP_Error|array|mixed
 */
function cerber_get_plugin_hash( $plugin, $ver, $nocache = false ) {

	if ( !$plugin = preg_replace( '/[^a-z\-\d]/i', '', $plugin ) ) {
		return false;
	}

	$response = cerber_obtain_hash( 'https://downloads.wordpress.org/plugin-checksums/' . $plugin . '/' . $ver . '.json', $nocache );

	if ( ! $response['error'] ) {
		return $response['server_data'];
	}

	if ( $response['http_code'] == 404 ) {
		$ret = new WP_Error( 'no_remote_hash', 'The plugin is not found on wordpress.org' );
	}
	else {
		if ( ! empty( $response['curl_error'] ) ) {
			$msg = 'CURL ' . $response['curl_error'];
		}
        elseif ( ! empty( $response['json_error'] ) ) {
			$msg = 'JSON ' . $response['json_error'];
		}
		else {
			$msg = 'Unknown network error';
		}
		$ret = new WP_Error( 'net_issue', $msg );
	}


	return $ret;

}

/**
 * @param $theme_folder
 * @param $theme object WP_Theme
 *
 * @return bool|WP_Error|array  false if no local hash or theme is not publicly hosted on on the wordpress.org
 */
function cerber_get_theme_hash( $theme_folder, $theme ) {

	if ( $hash = cerber_get_local_hash( CRB_HASH_THEME . sha1( $theme->get( 'Name' ) . $theme_folder ), $theme->get('Version') ) ) {
		return $hash;
	}

	$tmp_file_name = $theme_folder . '.' . $theme->get( 'Version' ) . '.zip';
	$url           = 'https://downloads.wordpress.org/theme/' . $theme_folder . '.' . $theme->get( 'Version' ) . '.zip';
	$tmp_zip_file  = cerber_download_file( $url, $tmp_file_name );
	if ( is_wp_error( $tmp_zip_file ) ) {
		return $tmp_zip_file;
	}

	$result = cerber_need_for_hash( $tmp_zip_file, true, time() + DAY_IN_SECONDS );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	if ( $hash = cerber_get_local_hash( CRB_HASH_THEME . sha1( $theme->get( 'Name' ) . $theme_folder ), $theme->get('Version') ) ) {
		return $hash;
	}

	return false;
}

function cerber_download_file( $url, $file_name, $folder = null ) {
    static $errors = array();

	$url_id = sha1( $url );
	if ( isset( $errors[ $url_id ] ) ) {
		return $errors[ $url_id ];
	}

	$tmp = false;
	if ( ! $folder ) {
		$folder = cerber_get_tmp_file_folder();
		if ( is_wp_error( $folder ) ) {
			return $folder;
		}
		$tmp = true;
	}
    elseif ( ! file_exists( $folder ) ) {
		return new WP_Error( 'cerber-file', 'Target folder does not exist: ' . $folder );
	}

	$dst_file = $folder . $file_name;

	if ( ! $tmp && file_exists( $dst_file ) ) {
		return new WP_Error( 'cerber-file', 'Aborted. Target file exists: ' . $dst_file );
	}

	if ( ! $fp = fopen( $dst_file, 'w' ) ) {
		return new WP_Error( 'cerber-file', 'Unable to create file: ' . $dst_file );
	}

	$curl = @curl_init();
	if ( ! $curl ) {
		return new WP_Error( 'cerber-curl', 'CURL library is disabled or not installed on this web server.');
	}

	curl_setopt_array( $curl, array(
		CURLOPT_URL               => $url,
		CURLOPT_POST              => false,
		CURLOPT_USERAGENT         => 'Cerber Security Plugin',
		CURLOPT_FILE              => $fp,
		CURLOPT_FAILONERROR       => true,
		CURLOPT_CONNECTTIMEOUT    => 5,
		CURLOPT_TIMEOUT           => 25, // including CURLOPT_CONNECTTIMEOUT
		CURLOPT_DNS_CACHE_TIMEOUT => 3 * 3600,
		CURLOPT_SSL_VERIFYHOST    => 2,
		CURLOPT_SSL_VERIFYPEER    => true,
		CURLOPT_CAINFO            => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
	) );

	$exec = curl_exec( $curl );
	$code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
	curl_close( $curl );
	fclose( $fp );

	if ( ! $exec ) {
		unlink( $dst_file );

		$ret = new WP_Error( 'cerber-curl', 'Unable (HTTP ' . $code . ') to download file: ' . $url );

		$errors[ $url_id ] = $ret;

		return $ret;
	}

	return $dst_file;

}

/**
 * Retrieve MD5 hash from wordpress.org
 * See also: get_core_checksums();
 *
 * @param bool $nocache if true, do not use the local cache
 *
 * @return array|object|WP_Error
 */
function cerber_get_wp_hash( $nocache = false ) {

    $wp_version = cerber_get_wp_version();

	$locale = get_locale();

	$response = cerber_obtain_hash( 'https://api.wordpress.org/core/checksums/1.0/?version=' . $wp_version . '&locale=' . $locale, $nocache );

	if ( ! $response['error'] ) {
		$ret = $response['server_data'];
		if ( ! empty( $ret->checksums ) ) {
			return get_object_vars( $ret->checksums );
		}
        elseif ( isset( $ret->checksums ) ) {
	        $err = 'WordPress integrity data not found. Version: ' . $wp_version . ', locale: ' . $locale;
        }
        else {
	        $err = 'WordPress integrity data has invalid format. Version: ' . $wp_version . ', locale: ' . $locale;
        }
	}
	else {
		if ( ! empty( $response['curl_error'] ) ) {
			$err = 'CURL ' . $response['curl_error'];
		}
        elseif ( ! empty( $response['json_error'] ) ) {
			$err = 'JSON ' . $response['json_error'];
		}
		else {
			$err = 'Unknown network error';
		}
	}

	$ret = new WP_Error( 'net_issue', $err );
	cerber_log_scan_error( $err );

	return $ret;

}

/**
 * Download hash from the given URL. Network level.
 *
 * @param $url
 * @param bool $nocache If true, do not use data from the local cache (refresh one)
 *
 * @return array|bool
 */
function cerber_obtain_hash( $url, $nocache = false ) {

	$key = 'tmp_hashcache_' . sha1( $url );

	if ( ! $nocache && $cache = cerber_get_set( $key ) ) {
		return $cache;
	}

	$ret = array( 'error' => 1 );
	$err = null;

	$curl = @curl_init();
	if ( ! $curl ) {
	    $ret['curl_error'] = 'CURL library is disabled or not installed on this web server.';
		return $ret;
	}

	curl_setopt_array( $curl, array(
		CURLOPT_URL               => $url,
		CURLOPT_POST              => false,
		CURLOPT_USERAGENT         => 'Cerber Security Plugin',
		CURLOPT_RETURNTRANSFER    => true,
		CURLOPT_CONNECTTIMEOUT    => 5,
		CURLOPT_TIMEOUT           => 10, // including CURLOPT_CONNECTTIMEOUT
		CURLOPT_DNS_CACHE_TIMEOUT => 3 * 3600,
		CURLOPT_SSL_VERIFYHOST    => 2,
		CURLOPT_SSL_VERIFYPEER    => true,
		CURLOPT_CAINFO            => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
	) );

	crb_scan_debug( 'Launching CURL to download integrity data from: ' . $url );
	$result = curl_exec( $curl );

	$ret['curl_status'] = curl_getinfo( $curl );
	$http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
	$ret['http_code'] = $http_code;

	if ( $result ) {
		if ( 200 === $http_code ) {
			crb_scan_debug( 'Integrity data is downloaded from: ' . $url );
			crb_scan_debug( 'SIZE: ' . strlen( $result ) );
			$ret['server_data'] = json_decode( $result );
			if ( JSON_ERROR_NONE != json_last_error() ) {
				$ret['server_data'] = '';
				$ret['json_error']  = json_last_error();
				$ret['error']       = json_last_error();
			}
			else {
				$ret['error'] = 0;
				cerber_update_set( $key, $ret, 0, true, time() + DAY_IN_SECONDS );
			}
		}
        elseif ( 404 === $http_code ) {
	        $err = 'Remote server return 404 URL not found';
	        $ret['curl_error'] = $err;
	        $ret['error'] = $err;
			// There is no information about the plugin or this version of the plugin
		}
		else {
			if ( ! $err = curl_error( $curl ) ) {
				$err = 'Unknown CURL (network) error. Code ' . $http_code;
			}
			$ret['curl_error'] = $err;
			$ret['error'] = $err;
		}
	}
	else {
		if ( ! $err = curl_error( $curl ) ) {
			$err = 'Unknown CURL (network) error. Code ' . $http_code;
		}
		$ret['curl_error'] = $err;
		$ret['error'] = $err;
	}

	if ( ! empty( $ret['curl_error'] ) ) {
		$err = '#' . curl_errno( $curl ) . ' ' . $ret['curl_error'] . ' while attempting to retrieve: ' . $url;
		$ret['curl_error'] = $err;
	}

	curl_close( $curl );

	if ( $err ) {
		if ( $http_code == 404 ) {
			crb_scan_debug( $err );
		}
		else {
			cerber_log_scan_error( $err );
		}
	}

	return $ret;
}

function cerber_detect_file( $file_name ) {
	static $abspath = null;
	static $upload_dir = null;
	static $upload_dir_mu = null;
	static $plugin_dir = null;
	static $theme_dir = null;
	static $content_dir = null;
	static $len = null;

	if ( $abspath === null ) {
		$abspath       = cerber_get_abspath();
		$len           = strlen( $abspath );
		$content_dir   = cerber_get_content_dir() . DIRECTORY_SEPARATOR;
		$upload_dir    = cerber_get_upload_dir() . DIRECTORY_SEPARATOR;
		$upload_dir_mu = cerber_get_upload_dir_mu() . DIRECTORY_SEPARATOR;
		$plugin_dir    = cerber_get_plugins_dir() . DIRECTORY_SEPARATOR;
		$theme_dir     = cerber_get_themes_dir() . DIRECTORY_SEPARATOR;
	}

	// Check in a particular order for a better performance

	if ( 0 === strpos( $file_name, $abspath . 'wp-admin' . DIRECTORY_SEPARATOR ) ) {
		return CERBER_FT_WP; // WP
	}
	if ( 0 === strpos( $file_name, $abspath . WPINC . DIRECTORY_SEPARATOR ) ) {
		return CERBER_FT_WP; // WP
	}

	if ( 0 === strpos( $file_name, $plugin_dir ) ) {
		return CERBER_FT_PLUGIN; // Plugin
	}

	if ( 0 === strpos( $file_name, $theme_dir ) ) {
		return CERBER_FT_THEME; // Theme
	}

	if ( 0 === strpos( $file_name, $upload_dir ) ) {
		return CERBER_FT_UPLOAD; // Upload folder
	}

	if ( is_multisite() ) {
		if ( 0 === strpos( $file_name, $upload_dir_mu ) ) {
			return CERBER_FT_UPLOAD; // Upload folder
		}
	}

	if ( 0 === strpos( $file_name, $content_dir ) ) {
		if ( 0 === strpos( $file_name, $content_dir . 'languages' . DIRECTORY_SEPARATOR ) ) {
			return CERBER_FT_LNG; // Translations
		}
		if ( 0 === strpos( $file_name, $content_dir . 'mu-plugins' . DIRECTORY_SEPARATOR ) ) {
			return CERBER_FT_MUP; // A file in MU plugins folder
		}
		if ( $file_name === $content_dir . 'index.php' ) {
			return CERBER_FT_WP; // WP
		}

		if ( cerber_is_dropin( $file_name ) ) {
			return CERBER_FT_DRIN;
		}

		return CERBER_FT_CNT; // WP Content
	}

	if ( strrpos( $file_name, DIRECTORY_SEPARATOR ) === ( $len - 1 ) ) {
		//if ( strrchr( $file_name, DIRECTORY_SEPARATOR ) === DIRECTORY_SEPARATOR . 'wp-config.php' ) {
		if ( basename( $file_name ) == 'wp-config.php' ) {
			return CERBER_FT_CONF;
		}

		return CERBER_FT_ROOT; // File in the root folder
	}

	if ( basename( $file_name ) == 'wp-config.php' ) {
		if ( ! file_exists( $abspath . '/wp-config.php' ) ) {
			return CERBER_FT_CONF;
		}
	}

	return CERBER_FT_OTHER; // Some subfolder in the root folder

}

function cerber_is_htaccess( $file_name ) {
	if ( strrchr( $file_name, DIRECTORY_SEPARATOR ) === DIRECTORY_SEPARATOR . '.htaccess' ) {
		return true;
	}

	return false;
}

function cerber_is_dropin( $file_name ) {
	static $dropins;
	if ( ! $dropins ) {
		$dropins = _get_dropins();
	}
	if ( isset( $dropins[ basename( $file_name ) ] ) ) {
		if ( cerber_get_content_dir() == dirname( $file_name ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Return theme or plugin main folder
 *
 * @param $file_name
 * @param $path
 *
 * @return string
 */
function cerber_get_file_folder( $file_name, $path ) {
	$p_start = mb_strlen( $path ) + 1;
	$folder = mb_substr( $file_name, $p_start );
	if ( $pos = mb_strpos( $folder, DIRECTORY_SEPARATOR ) ) {
		$folder = mb_substr( $folder, 0, $pos );
	}

	return $folder;
}

/**
 * Prepare and save file data to the DB
 *
 * @param array $file A row from the cerber_files table
 *
 * @return bool
 */
function cerber_add_file_info( $file ) {
	static $md5;
	static $hash;

	if ( $md5 === null ) {
		$md5 = array( CERBER_FT_WP, CERBER_FT_PLUGIN, CERBER_FT_THEME, CERBER_FT_LNG, CERBER_FT_ROOT );
	}

	if ( $hash === null ) {
		$hash = array( CERBER_FT_PLUGIN, CERBER_FT_THEME );
	}

	$type = cerber_detect_file( $file['file_name'] );
	$file_name = $file['file_name'];
	$update_file_name = '';

	// A symbolic link in the content folder? Transform it to a real file name
	if ( $type == CERBER_FT_CNT && is_link( $file['file_name'] ) ) {

	    $file_name  = @readlink( $file['file_name'] );

	    if ( is_dir( $file_name ) ) {
			$delete_it = true;
		}
		else {
			$delete_it = cerber_db_get_var( 'SELECT COUNT(scan_id) FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $file['scan_id'] . ' AND file_name = "' . cerber_real_escape( $file_name ) . '"' );
		}

		if ( $delete_it ) {
			return cerber_db_query( 'DELETE FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $file['scan_id'] . ' AND file_name_hash = "' . $file['file_name_hash'] . '"' );
		}

		$update_file_name = ' file_name="'.cerber_real_escape( $file_name ).'",';
	}

	$file_hash = '';
	$file_md5 = '';

	if ( @is_readable( $file_name ) ) {
		if ( in_array( $type, $md5 ) ) {
			if ( ! $file_md5 = @md5_file( $file_name ) ) {
				$file_md5 = '';
			}
		}
		//if ( cerber_is_check_fs() || in_array( $type, $hash ) || cerber_is_htaccess( $file_name ) ) {
		if ( cerber_is_check_fs() || in_array( $type, $hash ) ) {
			if ( ! $file_hash = @hash_file( 'sha256', $file_name ) ) {
				$file_hash = '';
			}
		}
	}
	else {
		cerber_log_scan_error( cerber_scan_msg( 0, $file_name ) );
	}

	$size = @filesize( $file_name );
	$size = ( is_numeric( $size ) ) ? $size : 0;

	$perms = @fileperms( $file_name );
	$perms = ( is_numeric( $perms ) ) ? $perms : 0;

	$mtime = @filemtime( $file_name );
	$mtime = ( is_numeric( $mtime ) ) ? $mtime : 0;

	$is_writable = ( is_writable( $file_name ) ) ? 1 : 0;

	//if ( ! cerber_db_query( 'UPDATE ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' SET file_name = "' . $file_name . '", file_hash = "' . $file_hash . '", file_md5 = "' . $file_md5 . '", file_size = ' . $size . ', file_type = ' . $type . ', file_perms = ' . $perms . ', file_writable = ' . $is_writable . ', file_mtime = ' . $mtime .
	if ( ! cerber_db_query( 'UPDATE ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' SET '.$update_file_name.' file_hash = "' . $file_hash . '", file_md5 = "' . $file_md5 . '", file_size = ' . $size . ', file_type = ' . $type . ', file_perms = ' . $perms . ', file_writable = ' . $is_writable . ', file_mtime = ' . $mtime .
                            ' WHERE scan_id = ' . $file['scan_id'] . ' AND file_name_hash = "' . $file['file_name_hash'] . '"' ) ) {
		return false;
	}

	return true;
}

/**
 * @param string $file_name_hash
 * @param int $status
 * @param int $scan_id
 *
 * @return bool|mysqli_result|resource
 */
function cerber_update_fscan_status( $file_name_hash, $status, $scan_id ) {
	return cerber_db_query( 'UPDATE ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' SET scan_status = ' . $status . ' WHERE scan_id = ' . $scan_id . ' AND file_name_hash = "' . $file_name_hash . '"' );
}

function cerber_is_check_fs() {
	if ( crb_get_settings( 'scan_imod' ) || crb_get_settings( 'scan_inew' ) ) {
		return true;
	}

	return false;
}

/**
 * Are there any changes/new files
 *
 * @return int
 */
function cerber_check_fs_changes() {

	$scan_id = cerber_get_scan_id();

	$prev_id = cerber_get_prev_scan_id( $scan_id );

	if ( $prev_id ) {
		cerber_cmp_scans( $prev_id, $scan_id );
	}

	return 0;
}

function cerber_get_prev_scan_id( $scan_id = 0 ) {
    global $cerber_scan_mode;

	if ( ! $scans = cerber_db_get_results( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SETS_TABLE
	                                       . ' WHERE the_key = "scan" AND the_id < ' . $scan_id . ' ORDER BY the_id DESC' )
	) {
		return 0;
	}

	$prev_id = 0;
	foreach ( $scans as $item ) {
		$scan = unserialize( $item['the_value'] );
		if ( $scan['finished'] && $scan['mode'] == $cerber_scan_mode ) {
			$prev_id = $scan['id'];
			break;
		}
	}

	return $prev_id;
}

function cerber_cmp_scans( $prev_id, $scan_id ) {

	$p_files = cerber_db_get_results( 'SELECT file_name, file_name_hash, file_hash, file_md5, file_size FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $prev_id);

	$n_files = cerber_db_get_results( 'SELECT file_name, file_name_hash, file_hash, file_md5, file_size FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan_id);

	if ( ! $p_files || ! $n_files ) {
		return 0;
	}

	$prev_files = array();
	foreach ( $p_files as $file ) {
		$prev_files[$file['file_name_hash']] = $file;
	}

	$new_files = array();
	foreach ( $n_files as $file ) {
		$new_files[$file['file_name_hash']] = $file;
	}

	$inew = crb_get_settings( 'scan_inew' );
	$imod = crb_get_settings( 'scan_imod' );

	$update = array();
	foreach ( $new_files as $key => $file ) {
		$status    = 0;
		if ( ! isset( $prev_files[ $key ] ) ) {
			if ( $inew ) {
				if ( $inew != 2 ) {
					if ( cerber_detect_exec_extension( $file['file_name'] ) ) {
						$status = CERBER_NEW;
					}
				}
				else {
					$status = CERBER_NEW;
				}
			}
		}
		elseif ( $imod ) {
			$status = cerber_cmp_files( $prev_files[ $key ], $new_files[ $key ] );
			if ( $status && ( $imod != 2 ) ) {
				if ( ! cerber_detect_exec_extension( $file['file_name'] ) ) {
					$status = 0;
				}
			}
		}

		if ( $status > 0 ) {
			$update[ $key ] = $status;
		}
	}

	if ( ! $update ) {
		return 0;
	}

	$table = cerber_get_db_prefix() . CERBER_SCAN_TABLE;
	foreach ( $update as $key => $status ) {
		cerber_db_query( 'UPDATE ' . $table . ' SET file_status = ' . $status . ' WHERE scan_id = ' . $scan_id . ' AND file_name_hash = "' . $key . '"' );
	}

	return 0;

}

function cerber_cmp_files( $prev, $new ) {
	if ( ! empty( $prev['file_hash'] ) && ! empty( $new['file_hash'] ) ) {
		if ( $prev['file_hash'] != $new['file_hash'] ) {
			return CERBER_MOD;
		}
	}
    elseif ( ! empty( $prev['file_md5'] ) && ! empty( $new['file_md5'] ) ) {
		if ( $prev['file_md5'] != $new['file_md5'] ) {
			return CERBER_MOD;
		}
	}
    elseif ( $prev['file_size'] != $new['file_size'] ) {
		return CERBER_MOD;
	}

	return 0;
}

/**
 * Recursively creates a list of files in a given folder with a given filename pattern
 *
 * @param string $root The starting folder with trailing slash
 * @param string $pattern Pattern for filenames to include
 * @param callable $function The function to save the list of files that are passed as an array
 *
 * @return array The total number of folders and files
 */
function cerber_scan_directory( $root, $pattern = null, $function ) {
    static $history = array();
    static $exclude = null;

    // Prevent infinite recursion
	if ( isset( $history[ $root ] ) ) {
		return array( 0, 0 );
	}
	$history[ $root ] = 1;

	// Must be excluded
	if ( $exclude === null ) {
		$list = crb_get_settings( 'scan_exclude' );
		if ( ! $list || ! is_array( $list ) ) {
			$list = array();
		}

		$d = cerber_get_the_folder();
		if ( is_dir( $d ) ) {
			$list[] = $d;
		}

		$exclude = array();
		foreach ( $list as $dir ) {
			if ( ! is_dir( $dir ) ) {
				continue;
			}

			$exclude[] = rtrim( $dir, '/\\' );
		}

		/*$exclude   = array_map( function ( $item ) {
			return rtrim( $item, '/\\' );
		}, $exclude );*/
	}

	if ( ! $pattern ) {
		$pattern = '{*,.*}';
	}

	$dir_counter  = 1;
	$file_counter = 0;
	$root = rtrim( $root, '/\\' ) . DIRECTORY_SEPARATOR;
	$list         = array();

	//if ( $files = glob( $root . $pattern, GLOB_BRACE ) ) {
	if ( $files = cerber_glob_brace( $root, $pattern ) ) {
		foreach ( $files as $file_name ) {
			if ( @is_dir( $file_name ) || ! is_readable( $file_name ) ) {
				continue;
			}
			$file_counter ++;
			$list[]    = $file_name;
			if ( count( $list ) > 200 ) { // packet size, can affect the DB performance if $function saves file names to the DB
				call_user_func( $function, $list );
				$list = array();
			}
		}
		if ( ! empty( $list ) ) {
			call_user_func( $function, $list );
		}
	}
    elseif ( $files === false ) {
		cerber_log_scan_error( 'PHP glob got error while accessing ' . $root . $pattern );
	}

	//if ( $dirs = glob( $root . '{*,.*}', GLOB_ONLYDIR | GLOB_BRACE ) ) {
	if ( $dirs = cerber_glob_brace( $root, '{*,.*}', GLOB_ONLYDIR ) ) {
		foreach ( $dirs as $dir ) {
			if ( in_array( $dir, $exclude ) ) {
				continue;
			}
			$b = basename( $dir );
			if ( $b == '.' || $b == '..' ) {
				continue;
			}
			list ( $dc, $fc ) = cerber_scan_directory( $dir, $pattern, $function );
			$dir_counter  += $dc;
			$file_counter += $fc;
		}
	}
    elseif ( $files === false ) {
		cerber_log_scan_error( 'PHP glob got error while accessing ' . $root . '*' );
	}

	return array( $dir_counter, $file_counter );
}

/**
 * A PHP glob() implementation that works with no GLOB_BRACE available
 *
 * @param string $dir With the trailing directory delimiter
 * @param string $patterns We expect '{pattern1,pattern2,etc.}'
 * @param int $flags Standard glob() flags except GLOB_BRACE
 *
 * @return array|false
 */
function cerber_glob_brace( $dir, $patterns, $flags = 0 ) {

	if ( $patterns[0] != '{' ) { // No GLOB_BRACE is needed
		return glob( $dir . $patterns, $flags );
	}

	if ( defined( 'GLOB_BRACE' ) ) {
		$flags = ( $flags ) ? $flags | GLOB_BRACE : GLOB_BRACE;

		return glob( $dir . $patterns, $flags );
	}

	// GLOB_BRACE is not supported

	$list = explode( ',', substr( $patterns, 1, strlen( $patterns ) - 2 ) );
	$list = array_map( 'trim', $list );

	$ret = array();

	foreach ( $list as $pt ) {
		if ( $glob = glob( $dir . $pt, $flags ) ) {
			$ret = array_merge( $ret, $glob );
		}
	}

	return $ret;
}

/**
 * @param $file_name string
 *
 * @return string
 */
function cerber_normal_path( $file_name ) {
	return str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $file_name );
}

/**
 * Packet saving of file names
 *
 * @param array $list
 *
 * @return bool|mysqli_result
 */
function _crb_save_file_names( $list ) {
    global $cerber_scan_mode;
	static $scan_id;
	static $ignore;

	$list = array_filter( $list );
	if ( empty( $list ) ) {
		return true;
	}

	if ( ! isset( $scan_id ) ) {
		$scan_id = cerber_get_scan_id();
		if ( ! $scan_id ) {
			return false;
		}
	}

	if ( ! isset( $ignore ) ) {
		$ignore = cerber_get_set( 'ignore-list' );
		if ( ! $ignore || ! is_array( $ignore ) ) {
			$ignore = array();
		}
	}

	if ( $cerber_scan_mode == 'full' ) {
		$scan_mode = 1;
	}
	else {
		$scan_mode = 0;
	}

	$sql = '';

	$table = cerber_get_db_prefix() . CERBER_SCAN_TABLE;

	foreach ( $list as $filename ) {
		if ( ! @is_file( $filename ) || ! cerber_is_file_type_scan( $filename ) ) {
			continue;
		}
		$filename = cerber_normal_path( $filename );

		$file_name_hash = sha1( $filename );
		if ( cerber_db_get_var( 'SELECT COUNT(scan_id) FROM ' . $table . ' WHERE scan_id = ' . $scan_id . ' AND file_name_hash = "' . $file_name_hash . '"' ) ) {
			continue;
		}

		$status = 0;
		if ( isset( $ignore[ $file_name_hash ] ) ) {
			$status = 1;
			crb_scan_debug( 'The file is in the ignore list: ' . $filename );
		}

		$filename = cerber_real_escape( $filename );

		//$sql .= '(' . $scan_id . ',' . $scan_mode . ',"' . $file_name_hash . '","' . $filename . '"),';
		$sql .= '(' . $scan_id . ',' . $scan_mode . ',"' . $file_name_hash . '","' . $filename . '",'.$status.'),';
	}

	if ( ! $sql ) {
		return true;
	}

	$sql = rtrim( $sql, ',' );

	$ret = cerber_db_query( 'INSERT INTO ' . $table . ' (scan_id, scan_mode, file_name_hash, file_name, scan_status) VALUES ' . $sql );
	if ( ! $ret ) {
		cerber_log_scan_error( 'DB Error occurred while saving filenames' );
	}

	return $ret;
}

/**
 * Return true if a given file must be checked (scanned)
 *
 * @param $filename
 *
 * @return bool
 */
function cerber_is_file_type_scan( $filename ) {
	global $cerber_scan_mode;

	if ( $cerber_scan_mode == 'full' ) {
		return true;
	}
	else {

	    // @since 8.2 the list includes additional php extensions
		if ( cerber_check_extension( $filename, array( 'php', 'phtm', 'phtml', 'phps', 'php2', 'php3', 'php4', 'php5', 'php6', 'php7', 'inc' ) ) ) {
			return true;
		}

		$pos = strrpos( $filename, DIRECTORY_SEPARATOR );
		if ( $pos ) {
			$filename = substr( $filename, $pos + 1 );
		}

		if ( $filename == '.htaccess' ) {
			return true;
		}

		return false;

	}

	return false;
}

/**
 * Check if a filename has an extension from a given list
 *
 * @param $filename
 * @param array $ext_list
 *
 * @return bool
 */
function cerber_check_extension( $filename, $ext_list = array() ) {
	if ( ! is_array( $ext_list ) || empty( $ext_list ) ) {
		return false;
	}

	//$d = cerber_detect_exec_extension();

    $pos = mb_strrpos( $filename, DIRECTORY_SEPARATOR );
	if ( $pos !== false ) {
		$filename = mb_substr( $filename, $pos + 1 );
	}

	$pos = mb_strpos( $filename, '.' );
	if ( $pos === false ) {
		return false;
	}

	$ext = mb_substr( $filename, $pos + 1 );
	$ext = strtolower( $ext );

	// A normal, single extension

	if ( in_array( $ext, $ext_list ) ) {
		return true;
	}

	// No more additional extensions

	if ( substr_count( $ext, '.' ) == 0 ) {
		return false;
	}

	// Multiple "extensions"

	$last = substr( $ext, strrpos( $ext, '.' ) + 1 );
	if ( in_array( $last, $ext_list ) ) {
		return true;
	}

	$first = substr( $ext, 0, strpos( $ext, '.' ) );
	if ( in_array( $first, $ext_list ) ) {
		return true;
	}

	return false;

}

function cerber_step_desc( $step = null ) {
	$steps = array(
		__( 'Preparing for the scan', 'wp-cerber' ),
		__( 'Scanning folders for files', 'wp-cerber' ),
		__( 'Scanning the upload folder for files', 'wp-cerber' ),
		__( 'Scanning the temp folder for files', 'wp-cerber' ),
		__( 'Scanning the session folder for files', 'wp-cerber' ),
		__( 'Parsing the list of files', 'wp-cerber' ),
		__( 'Checking for new and modified files', 'wp-cerber' ),
		__( 'Verifying the integrity of WordPress', 'wp-cerber' ),
		__( 'Recovering WordPress files', 'wp-cerber' ),
		__( 'Verifying the integrity of the plugins', 'wp-cerber' ),
		__( 'Recovering plugins files', 'wp-cerber' ),
		__( 'Verifying the integrity of the themes', 'wp-cerber' ),
		__( 'Searching for malicious code', 'wp-cerber' ),
		__( 'Finalizing the scan', 'wp-cerber' ),
	);

	if ( $step !== null && isset( $steps[ $step ] ) ) {
		return $steps[ $step ];
	}

    return $steps;
}

/**
 * Overwrites values and preserve array hierarchy (keys)
 *
 * @param array $a1
 * @param array $a2
 *
 * @return mixed
 */
function cerber_array_merge_recurively( $a1, $a2 ) {
	foreach ( $a2 as $key => $value ) {
		if ( isset( $a1[ $key ] ) && is_array( $a1[ $key ] ) && is_array( $value ) ) {
			$a1[ $key ] = cerber_array_merge_recurively( $a1[ $key ], $value );
		}
		else {
			$a1[ $key ] = $value;
		}
	}

	return $a1;
}

//function cerber_get_short_name( $file_row ) {
function cerber_get_short_name( $file_name, $file_type ) {
	if ( ! $file_name || ! $file_type ) {
		return '';
	}
	//$file_name = $file_row['file_name'];
	$len = null;
	//switch ( $file_row['file_type'] ) {
	switch ( $file_type ) {
		case CERBER_FT_PLUGIN:
			$len = mb_strlen( cerber_get_plugins_dir() );
			break;
		case CERBER_FT_THEME:
			$len = mb_strlen( cerber_get_themes_dir() );
			break;
		case CERBER_FT_UPLOAD:
			if ( is_multisite() && false !== strpos( $file_name, cerber_get_upload_dir_mu() . DIRECTORY_SEPARATOR ) ) {
				$len = mb_strlen( dirname( cerber_get_upload_dir_mu() ) );
			}
			else {
				$len = mb_strlen( dirname( cerber_get_upload_dir() ) );
			}
			break;
		default:
			if ( 0 === strpos( $file_name, rtrim( cerber_get_abspath(), '/\\' ) ) ) {
				$len = mb_strlen( cerber_get_abspath() ) - 1;
			}
	}

	if ( $len ) {
		$ret = mb_substr( $file_name, $len );
	}
	else {
		$ret = $file_name;
    }

	return $ret;
}

// ======================================================================================================

// Process a manually installed/upgraded plugin/theme, part 1
add_filter( 'wp_insert_attachment_data', function ( $data, $postarr ) {
	global $crb_new_zip_file;
	if ( $postarr['context'] == 'upgrader' && $postarr['post_status'] == 'private' && isset( $postarr['file'] ) ) {
		$crb_new_zip_file = $postarr['file'];
	}

	return $data;
}, 10, 2 );

// Process a manually installed/upgraded plugin/theme, part 2
add_action( 'upgrader_process_complete', function ( $object, $extra ) {
	global $crb_new_zip_file;
	if ( empty( $crb_new_zip_file ) ) {
		return;
	}
	switch ( $extra['type'] ) {
		case 'plugin':
		case 'theme':
			if ( file_exists( $crb_new_zip_file ) ) {
				$tmp = cerber_get_tmp_file_folder();
				if ( ! is_wp_error( $tmp ) ) {
					$target_zip = $tmp . basename( $crb_new_zip_file );
					if ( copy( $crb_new_zip_file, $target_zip ) ) {
						wp_schedule_single_event( time() + 5 * MINUTE_IN_SECONDS, 'cerber_scheduled_hash', array( $target_zip ) );
						cerber_need_for_hash( $target_zip );
					}
					else {
					    // Error
                    }
				}
				else {
					// Error
                }
			}
			break;
	}

}, 10, 2 );

// Process a manually installed/upgraded plugin/theme, part 3
add_action( 'cerber_scheduled_hash', 'cerber_scheduled_hash' );
function cerber_scheduled_hash( $zip_file = '' ) {
	$result = cerber_need_for_hash( $zip_file );
	if ( is_wp_error( $result ) ) {
		//cerber_log( $result->get_error_message() );
	}
}

/**
 * Generate hash for an uploaded theme/plugin ZIP archive or for a specified ZIP file.
 * Hash will not be created if a theme/plugin is not installed on the website.
 *
 * @param string $zip_file Be used if set
 * @param bool $delete If true the source ZIP will be deleted
 * @param int $expires Timestamp when hash will expire, 0 = never
 *
 * @return bool|WP_Error
 */
function cerber_need_for_hash( $zip_file = '', $delete = true, $expires = 0 ) {
	$folder     = cerber_get_tmp_file_folder();
	$tmp_folder1 = $folder . 'zip' . DIRECTORY_SEPARATOR;
	$tmp_folder2 = $folder . 'nested_zip' . DIRECTORY_SEPARATOR;

	crb_raise_limits();

	if ( ! $zip_file ) {
		if ( ! $files = glob( $folder . '*.zip' ) ) {
			return false;
		}
	}
	else {
		if ( ! is_array( $zip_file ) ) {
			$files = array( $zip_file );
		}
		else {
			$files = $zip_file;
		}
	}

	$fs = cerber_init_wp_filesystem();
	$result = true;

	foreach ( $files as $zip_file ) {

		if ( ! file_exists( $zip_file ) ) {
			continue;
		}

		crb_scan_debug( 'Processing ZIP: ' . basename( $zip_file ) );

		$result = crb_hash_maker( $zip_file, $tmp_folder1, false, $expires );

		if ( is_wp_error( $result ) ) {

			crb_scan_debug( 'Processing ZIP: ' . $result->get_error_message() );

		    // It's possible that there is a nested ZIP archive

			if ( $nested_zip_list = glob( $tmp_folder1 . '*.zip' ) ) {

			    crb_scan_debug( 'Processing ZIP: trying to find the reference code in the nested zip archive' );

				foreach ( $nested_zip_list as $nested_zip ) {
					$result = crb_hash_maker( $nested_zip, $tmp_folder2, true, $expires );
					if ( ! is_wp_error( $result ) ) {
						break; // Yay, we found it!
					}
				}
			}

		}
		else {
			crb_scan_debug( 'Processing ZIP: ' . basename( $zip_file ) . ' - OK!' );
		}

		if ( $delete ) {
			unlink( $zip_file );
		}

		if ( is_wp_error( $result ) ) {
			break;
		}

	}

	$fs->delete( $tmp_folder1, true );
	$fs->delete( $tmp_folder2, true );

	crb_scan_debug( 'Processing ZIP: Completed' );

	return $result;
}

/**
 * @param string $zip_file ZIP file to process
 * @param string $zip_folder Temporary folder for unpacking ZIP
 * @param bool $delete If true, the temp folder will be deleted afterward
 * @param int $expires HASH expiration time, Unix timestamp, 0 = never
 *
 * @return bool|WP_Error
 */
function crb_hash_maker( $zip_file, $zip_folder, $delete = true, $expires = 0 ) {

    $fs = cerber_init_wp_filesystem();

	if ( file_exists( $zip_folder ) && ! $fs->delete( $zip_folder, true ) ) {
		return new WP_Error( 'cerber-zip', 'Unable to clean up temporary zip folder ' . $zip_folder );
	}

	$result = cerber_unzip( $zip_file, $zip_folder );

	if ( is_wp_error( $result ) ) {
		return new WP_Error( 'cerber-zip', 'Unable to unzip file ' . $zip_file . ' ' . $result->get_error_message() );
	}

	if ( ! $obj = cerber_detect_object( $zip_folder ) ) {
		return new WP_Error( 'cerber-file', 'File ' . basename( $zip_file ) . ' cannot be used. Proper program code not found or version mismatch. Please upload another file.' );
	}

	$dir = $obj['src'] . DIRECTORY_SEPARATOR;
	$len = mb_strlen( $dir );

	global $the_file_list;
	$the_file_list = array();

	cerber_scan_directory( $dir, null, function ($list){
		global $the_file_list;
		$the_file_list = array_merge( $the_file_list, $list );
	} );

	if ( empty( $the_file_list ) ) {
		return new WP_Error( 'cerber-dir', 'No files found in ' . $zip_file );
	}

	$hash = array();

	foreach ( $the_file_list as $file_name ) {
		$hash[ mb_substr( $file_name, $len ) ] = hash_file( 'sha256', $file_name );
	}

	if ( !$obj['single'] ) {
		$b = $obj['src'];
	}
	else {
		$b = $obj['file'];
	}

	//$key = $obj['type'] . sha1( $obj['name'] . basename( $obj['src'] ) );
	$key = $obj['type'] . sha1( $obj['name'] . basename( $b ) );

	if ( ! cerber_update_set( $key, array(
		'name' => $obj['name'],
		'ver'  => $obj['ver'],
		'hash' => $hash,
		'time' => time()
	), 0, true, $expires )
	) {
		return new WP_Error( 'cerber-zip', 'Database error occurred while saving hash' );
	}

	if ( $delete ) {
		$fs->delete( $zip_folder, true );
	}

	unset( $the_file_list );

	return true;
}

/**
 * Retrieve local hash for plugin or theme
 *
 * @param $key
 * @param $version
 *
 * @return bool|mixed
 */
function cerber_get_local_hash( $key, $version ) {
	if ( $local_hash = cerber_get_set( $key ) ) {
		if ( $local_hash['ver'] == $version ) {
			return $local_hash['hash'];
		}
	}

	return false;
}

/**
 * @return string|WP_Error Full path to the folder with trailing slash
 */
function cerber_get_tmp_file_folder() {
	$folder = cerber_get_the_folder( true );
	if ( is_wp_error( $folder ) ) {
		return $folder;
	}

	$folder = $folder . 'tmp' . DIRECTORY_SEPARATOR;

	if ( ! is_dir( $folder ) ) {
		if ( ! mkdir( $folder, 0755, true ) ) {
			// TODO: try to set permissions for the parent folder
			return new WP_Error( 'cerber-dir', 'Unable to create the tmp directory ' . $folder );
		}
	}

	return $folder;
}

/**
 * Return Cerber's folder. If there is no folder, creates it.
 *
 * @return string|bool|WP_Error  Full path to the folder with trailing slash
 */
function cerber_get_the_folder( $asis = false ) {
	$ret = cerber_get_my_folder();
	if ( is_wp_error( $ret ) ) {
		crb_scan_debug( 'ERROR: ' . $ret->get_error_message() );
		if ( $asis ) {
			return $ret;
		}

		return false;
	}

	return $ret;
}

/**
 * Return Cerber's folder. If there is no folder, creates it.
 *
 * @return string|WP_Error  Full path to the folder with trailing slash
 */
function cerber_get_my_folder() {
    static $ret;

	if ( $ret !== null ) {
		return $ret;
	}

	$opt = cerber_get_set( '_cerber_mnemosyne' );

	if ( $opt && isset( $opt[4] ) && isset( $opt[ $opt[4] ] ) ) {
		$key = preg_replace( '/[^a-z0-9]/i', '', $opt[ $opt[4] ] );
		if ( $key ) {
			$folder = cerber_get_upload_dir() . DIRECTORY_SEPARATOR . 'wp-cerber-' . $key . DIRECTORY_SEPARATOR;
			if ( is_dir( $folder ) ) {
				if ( ! wp_is_writable( $folder ) ) {
					if ( ! chmod( $folder, 0755 ) ) {
						return new WP_Error( 'cerber-dir', 'The directory is not writable: ' . $folder );
					}
				}
				cerber_lock_the_folder( $folder );

				$ret = $folder;
				return $ret;
			}
		}
	}

	// Let's create the folder

	$key    = substr( str_shuffle( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, rand( 16, 20 ) );
	$folder = cerber_get_upload_dir() . DIRECTORY_SEPARATOR . 'wp-cerber-' . $key . DIRECTORY_SEPARATOR;

	if ( ! mkdir( $folder, 0755, true ) ) {
		// TODO: try to set permissions for the parent folder
		return new WP_Error( 'cerber-dir', 'Unable to create WP CERBER directory: ' . $folder );
	}

	if ( ! cerber_lock_the_folder( $folder ) ) {
		return new WP_Error( 'cerber-dir', 'Unable to lock the directory ' . $folder );
	}

	$k      = substr( str_shuffle( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, rand( 16, 20 ) );
	$i      = rand( 5, 10 );
	if ( ! cerber_update_set( '_cerber_mnemosyne', array( rand( 0, 3 ) => $k, 4 => $i, $i => $key ) ) ) {
		return new WP_Error( 'cerber-dir', 'Unable to save WP CERBER directory info' );
	}

	$ret = $folder;
	return $ret;
}

/**
 * Make a folder not accessible from the web
 *
 * @param $folder string
 *
 * @return bool
 */
function cerber_lock_the_folder( $folder ) {
	if ( $f = fopen( $folder . '.htaccess', 'w' ) ) {
		if ( fwrite( $f, 'deny from all' ) ) {
			fclose( $f );

			return true;
		}
	}

	return false;
}

/**
 * @param $file
 * @since 8.6.1
 *
 * @return bool
 */
function cerber_set_writable( $file ) {
	static $chmod_file, $chmod_dir;

	if ( ! $chmod_file ) {
		$chmod_file = ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 );
	}
	if ( ! $chmod_dir ) {
		$chmod_dir = ( fileperms( ABSPATH ) & 0777 | 0755 );
	}

	if ( @is_file( $file ) ) {
		return @chmod( $file, $chmod_file );
	}
    elseif ( @is_dir( $file ) ) {
		return @chmod( $file, $chmod_dir );
	}

	return false;
}

function cerber_unzip( $file_name, $folder ) {
	cerber_init_wp_filesystem();

	return unzip_file( $file_name, $folder );

}

function cerber_detect_object( $folder = '' ) {

    // Look for a theme

	$the_folder = false;

	$dirs = glob( $folder . '*', GLOB_ONLYDIR );
	if ( $dirs ) {
		$the_folder = $dirs[0]; // we expect only one subfolder
		if ( ! file_exists( $the_folder ) ) {
			$the_folder = false;
		}
	}

	if ( $result = cerber_check_theme_data( $the_folder ) ) {
		return array(
			'type'   => CRB_HASH_THEME,
			'name'   => $result->get( 'Name' ),
			'ver'    => $result->get( 'Version' ),
			'src'    => $the_folder,
			'single' => false,
		);
	}

	// Look for a plugin

	$files = glob( $folder . '*.php' ); // single file plugin
	if ( ! $files && $the_folder ) { // plugin with folder
		$files = glob( $the_folder . DIRECTORY_SEPARATOR . '*.php' );
		$single = false;
	}
	else {
	    $single = true;
    }

	if ( ! $files ) {
		return false;
	}

	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	foreach ( $files as $file_name ) {
		$plugin_data = get_plugin_data( $file_name );
		if ( ! empty ( $plugin_data['Name'] ) && ! empty ( $plugin_data['Version'] ) ) {
			foreach ( get_plugins() as $key => $plugin ) {
				if ( $plugin['Name'] == $plugin_data['Name'] && $plugin['Version'] == $plugin_data['Version'] ) {

					return array(
						'type'   => CRB_HASH_PLUGIN,
						'name'   => $plugin_data['Name'],
						'ver'    => $plugin_data['Version'],
						'data'   => $plugin_data,
						'src'    => dirname( $file_name ),
						'single' => $single,
						'file'   => $file_name
					);
				}
			}

		}
	}


	return false;
}

/**
 * @param string $folder A folder with theme files
 *
 * @return bool|WP_Theme
 */
function cerber_check_theme_data( $folder ) {

	$style = $folder . DIRECTORY_SEPARATOR . 'style.css';
	if ( ! file_exists( $style ) ) {
		return false;
	}

    // See class-wp-theme.php
	static $theme_headers = array(
		'Name'        => 'Theme Name',
		'ThemeURI'    => 'Theme URI',
		'Description' => 'Description',
		'Author'      => 'Author',
		'AuthorURI'   => 'Author URI',
		'Version'     => 'Version',
		'Template'    => 'Template',
		'Status'      => 'Status',
		'Tags'        => 'Tags',
		'TextDomain'  => 'Text Domain',
		'DomainPath'  => 'Domain Path',
	);
	$theme_folder = basename( $folder );
	$headers = get_file_data( $style, $theme_headers, 'theme' );
	// $headers['Version'] means just theme, $headers['Template'] means child theme
	if ( ! empty ( $headers['Name'] ) && ( ! empty ( $headers['Version'] ) || ! empty ( $headers['Template'] ) ) ) {
		$themes = wp_get_themes();
		foreach ( $themes as $the_folder => $theme ) {
			if ( $the_folder != $theme_folder ) {
				continue;
			}
			if ( $headers['Name'] == $theme->get( 'Name' ) ) {
				if ( ! empty ( $headers['Version'] ) && ( $headers['Version'] == $theme->get( 'Version' ) ) ) {
					return $theme;
				}
				if ( ! empty ( $headers['Template'] ) && ( $headers['Template'] == $theme->get( 'Template' ) ) ) {
					return $theme;
				}
			}
		}
	}

	return false;
}

/**
 * @param int $first
 * @param int $last
 * @param int $filter_scan
 *
 * @return array|WP_Error
 *
 * @since 8.6.4
 */
function cerber_quarantine_get_files( $first = 0, $last = null, $filter_scan = null ) {
	$folder = cerber_get_the_folder( true );
	if ( is_wp_error( $folder ) ) {
		return $folder;
	}

	$list = array();
	$count = 0;
	$scan_list = array();

	if ( ! $dirs = glob( $folder . 'quarantine' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR ) ) {
		return array( $list, $count, $scan_list );
	}

	foreach ( $dirs as $dir ) {
		$f = $dir . '/.restore';
		$scan_id = basename( $dir );
		$inc = false;
		if ( file_exists( $f ) && $handle = @fopen( $f, "r" ) ) {
			$ln = 0;
			$included = array();
			while ( ( $line = fgets( $handle ) ) !== false ) {
				$ln ++;
				if ( $ln <= 4 || empty( $line ) ) {
					continue;
				}
				$line = trim( $line );
				if ( empty( $line ) ) {
					continue;
				}
				$v = crb_parse_qline( $dir, $line );
				if ( $v ) {
					if ( in_array( $v['qfile'], $included ) ) {
						continue; // Prevent listing the same file several times
					}
					$inc = true;
					if ( ! $filter_scan || $filter_scan == $scan_id ) {
						if ( $count >= $first && ( ! $last || $count <= $last ) ) {
							$v['scan_id'] = $scan_id;
							$list[] = $v;
							$included[] = $v['qfile'];
						}
						$count ++;
					}
					else {
						continue; // skip the rest of the lines
					}
				}
			}
			if ( ! feof( $handle ) ) {
				echo "Error: unexpected I/O Error";
			}
			fclose( $handle );
		}
		if ( $inc ) {
			$scan_list[] = $scan_id;
		}
	}

	return array( $list, $count, $scan_list );
}

function crb_parse_qline( $dir, $line ) {
	if ( ! $line
	     || ! strpos( $line, '|' )
	     || ! strpos( $line, '=>' ) ) {
		return false;
	}
	list( $date, $info ) = explode( '|', $line );
	list( $qfile, $source ) = explode( '=>', $info );
	$date = trim( $date );
	$qfile = trim( $qfile );
	$source = trim( $source );
	if ( ! $qfile ) {
		return false;
	}
	$fname = $dir . '/' . $qfile;
	if ( ! @is_file( $fname ) ) {
		return false;
	}
	$size = @filesize( $fname );
	$size = ( is_numeric( $size ) ) ? $size : 0;
	//$sdir   = dirname( $source ) . DIRECTORY_SEPARATOR;
	//$can    = ( file_exists( $sdir ) ) ? true : false;
	//$can = ( file_exists( $source ) ) ? false : true;

	$ret = array(
		'date'   => $date,
		'size'   => crb_size_format( $size ),
		'qfile'  => $qfile,
		'source' => $source,
		//'sdir'   => $sdir,
		//'can'    => $can
		'can'    => true
	);

	return $ret;
}

/**
 * Move files to the quarantine folder
 *
 * @param string $file_name
 * @param integer $scan_id
 * @param bool $move true to delete the file in its original location @since 8.6.1
 *
 * @return bool|WP_Error
 */
function cerber_quarantine_file( $file_name, $scan_id, $move = true ) {
	static $folder;

	$scan_id = absint( $scan_id );

	if ( ! is_file( $file_name ) || ! $scan_id ) {
		return false;
	}

	if ( $move ) {
		$can = cerber_can_be_deleted( $file_name, true );

		if ( is_wp_error( $can ) ) {
			return $can;
			//return new WP_Error( 'cerber-del', "This file can't be deleted: " . $file_name );
		}
	}

	if ( $folder === null ) {
		$folder = cerber_get_the_folder( true );
	}
	if ( is_wp_error( $folder ) ) {
		return $folder;
	}

	$quarantine = $folder . 'quarantine' . DIRECTORY_SEPARATOR . $scan_id . DIRECTORY_SEPARATOR;

	if ( ! is_dir( $quarantine ) ) {
		if ( ! mkdir( $quarantine, 0755, true ) ) {
			// TODO: try to set permissions for the parent folder
			return new WP_Error( 'cerber-dir', 'Unable to create the quarantine directory ' . $quarantine );
		}
	}
	else {
		if ( ! chmod( $quarantine, 0755 ) ) {
			return new WP_Error( 'cerber-dir', 'Unable to set directory permissions for ' . $quarantine );
		}
	}

	if ( ! cerber_lock_the_folder( $quarantine ) ) {
		return new WP_Error( 'cerber-dir', 'Unable to lock the directory ' . $quarantine );
	}

	// Preserve original paths for deleted files in a restore file
	$restore = $quarantine . '.restore';
	if ( ! file_exists( $restore ) ) {
		if ( ! $f = fopen( $restore, 'w' ) ) {
			return new WP_Error( 'cerber-quar', 'Unable to create a restore file.' );
		}
		fwrite( $f, 'Information for restoring files.' . PHP_EOL
		            . 'Deletion date | Deleted file => Original file to copy to restore.' . PHP_EOL
		            . '-----------------------------------------------------------------'
		            //. PHP_EOL . `` );
		            . PHP_EOL );
	}
	else {
		if ( ! $f = fopen( $restore, 'a' ) ) {
			return new WP_Error( 'cerber-quar', 'Unable to write to the restore file.');
		}
	}

	// Avoid file name collisions
	$new_name = $quarantine . basename( $file_name );
	if ( file_exists( $new_name ) ) {
		$i = 2;
		while ( file_exists( $new_name ) ) {
			$new_name = $quarantine . basename( $file_name ) . '.' . $i;
			$i ++;
		}
	}

	if ( ! crb_move_copy( $file_name, $new_name, $move ) ) {
		$dir = dirname( $file_name );

		if ( $move ) {
			$msg = 'Unable to move file to the quarantine: ' . $file_name . '. Check permissions (owner) of this folder: ' . $dir;
		}
		else {
			$msg = 'Unable to copy file to the quarantine: ' . $file_name . '. Check permissions (owner) of this folder: ' . $dir;
		}

		return new WP_Error( 'cerber-quar-fail', $msg );
	}

	// Save restoring info
	//fwrite( $f, PHP_EOL . cerber_date( time(), false ) . ' | ' . basename( $new_name ) . ' => ' . $file_name );
	static $gmt_offset;
	if ( ! isset( $gmt_offset ) ) {
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	}
	fwrite( $f, PHP_EOL . date( 'Y-m-d H:i:s', time() + $gmt_offset ) . ' | ' . basename( $new_name ) . ' => ' . $file_name );
	fclose( $f );

	crb_qr_total_update( 1 );

	return true;
}

// @since 8.6.1
function crb_move_copy( $file_name, $new_name, $move = true ) {
	$abort = false;
	do {
		if ( $move ) {
			$ok = @rename( $file_name, $new_name );
		}
		else {
			$ok = @copy( $file_name, $new_name );
		}

		if ( $ok ) {
			return true;
		}

		if ( $abort ) {
			return false;
		}

		if ( ! crb_get_settings( 'scan_chmod' ) ) {
			return false;
		}

		cerber_set_writable( dirname( $file_name ) );
		cerber_set_writable( $file_name );
		$abort = true;

	} while ( true );
}

/**
 * Some files cannot be deleted
 *
 * @param $file_name
 * @param bool $check_inclusion
 *
 * @return true|WP_Error true if a file can be safely deleted
 */
function cerber_can_be_deleted( $file_name, $check_inclusion = false ) {

	if ( ! file_exists( $file_name ) || ! is_file( $file_name ) || is_link( $file_name ) ) {
		return new WP_Error( 'cerber_no_file', 'This file cannot be deleted because it doesn\'t exist: ' . $file_name );
		//return false;
	}

	if ( cerber_is_htaccess( $file_name ) || cerber_is_dropin( $file_name ) ) {
		return new WP_Error( 'cerber_file_not_allowed', 'This file is not allowed to be deleted: ' . $file_name );
		//return false;
	}

	if ( $check_inclusion && in_array( $file_name, get_included_files() ) ) {
		return new WP_Error( 'cerber_file_active', 'This file cannot be deleted because it \'s loaded and in use: ' . $file_name );
		//return false;
	}

	if ( basename( $file_name ) == 'wp-config.php' ) {
		$abspath = cerber_get_abspath();
		$file_name = cerber_normal_path( $file_name );

		if ( ( $file_name == $abspath . 'wp-config.php' )
		     || ( ! file_exists( $abspath . 'wp-config.php' ) && $file_name == dirname( $abspath ) . DIRECTORY_SEPARATOR . 'wp-config.php' ) ) {

			return new WP_Error( 'cerber_file_not_allowed', 'This file is not allowed to be deleted: ' . $file_name );
			//return false;
		}
		/*if ( ! file_exists( $abspath . 'wp-config.php' ) && $file_name == dirname( $abspath ) . DIRECTORY_SEPARATOR . 'wp-config.php' ) {
			return false;
		}*/
	}

	return true;
}

/**
 * Is time for current step is over?
 *
 * @param int $limit
 *
 * @return bool True if the time of execution of the current step is over
 */
function cerber_exec_timer( $limit = CERBER_MAX_SECONDS) {
	static $start;
	if ( $start === null ) {
		$start = time();
	}

	if ( $limit == CERBER_MAX_SECONDS && cerber_is_cloud_request() ) {
		$limit = CERBER_MAX_SECONDS_CLOUD;
	}

	if ( ( time() - $start ) > $limit ) {
		return true;
	}

	return false;
}

function cerber_scan_msg( $id, $txt = '' ) {
	$m = array( __( 'Unable to open file', 'wp-cerber' ) );

	$ret = '???';
	if ( isset( $m[ $id ] ) ) {
		$ret = $m[ $id ];
	}
	if ( $txt ) {
		//sprintf()
		$ret .= ' ' . $txt;
	}

	return $ret;
}

/**
 * Return the number of node if the request is originated from the Cerber Cloud, false otherwise
 *
 * @return bool|integer
 */
function cerber_is_cloud_request() {
	static $ret = null;

	if ( $ret !== null) {
		return $ret;
	}

	if ( ! cerber_is_http_post() || empty( $_POST['cerber-cloud-key'] ) ) {
		$ret = false;

		return $ret;
	}

	$key = lab_get_key();
	if ( empty( $key[4] ) ) {
		$key = lab_get_key( true );
	}
	if ( $key[4] != $_POST['cerber-cloud-key'] ) {
		$ret = false;

		return $ret;
	}

	$ret = lab_get_real_node_id();

    return $ret;
}

/**
 * Creates a user report
 *
 * @param array $scan
 *
 * @return bool|string False if there is nothing to report
 */
function cerber_scan_report( $scan ) {
	global $cerber_scan_mode;

	$include = crb_get_settings( 'scan_reinc' );

	$severity = array_intersect_key( array( 0, 1, 2, 3 ), $include ); // Severity are 0-4
	$types    = array_keys( $include );

	if ( ! $last_filtered = cerber_filter_issues( $scan, $types, $severity ) ) {
		return false;
	}

	$for_report = $last_filtered;

	if ( ! $cerber_scan_mode ) {
		$cerber_scan_mode = $scan['mode'];
	}
	if ( $prev_id = cerber_get_prev_scan_id( $scan['id'] ) ) {
		$prev_scan = cerber_get_scan( $prev_id );
	}
	else {
		$prev_scan = null;
    }

	$re = crb_get_settings( 'scan_relimit' );
	$prev_filtered = null;
	if ( $re > 1 ) {
		if ( $prev_scan ) {
			$prev_filtered = cerber_filter_issues( $prev_scan, $types, $severity );
		}
	}

	if ( $prev_filtered ) {
		switch ( $re ) {
			case 3:
				$last_comp = $last_filtered;
				// Remove "xx ago" that always changing from scan to scan and affect checksum
				array_walk_recursive( $last_comp, function ( &$e, $key ) {
					if ( $key === 'time' ) {
						$e = '';
					}
				} );
				array_walk_recursive( $prev_filtered, function ( &$e, $key ) {
					if ( $key === 'time' ) {
						$e = '';
					}
				} );
				$hash1 = sha1( serialize( $last_comp ) );
				$hash2 = sha1( serialize( $prev_filtered ) );
				if ( $hash1 == $hash2 ) {
					return false;
				}
				break;
			case 5:
				$for_report = cerber_get_new_issues( $prev_filtered, $last_filtered );
				break;
		}
	}

	if ( ! $for_report ) {
		return false;
	}


	// Generating the report

	$base_url = cerber_admin_link( 'scan_main' );
	$site_name = ( is_multisite() ) ? get_site_option( 'site_name' ) : get_option( 'blogname' );

	$css_table = 'width: 95%; max-width: 1000px; margin:0 auto; margin-bottom: 10px; background-color: #f5f5f5; text-align: center; color: #000; font-family: Arial, Helvetica, sans-serif;';
	$css_td = 'padding: 0.5em 0.5em 0.5em 1em; text-align: left;';
	$css_border = 'border-bottom: solid 2px #f9f9f9;';

	$ret = '';

	$mode = ( $scan['mode'] == 'full' ) ? __( 'Full Scan Report', 'wp-cerber' ) : __( 'Quick Scan Report', 'wp-cerber' );
	$mode = '<a href="' . $base_url . '">' . $mode . '</a>';

	// Summary
	$summary = array();

	$diff = '';
	if ( ! empty( $prev_scan['scanned']['files'] ) ) {
		$d = $scan['scanned']['files'] - $prev_scan['scanned']['files'];
		if ( absint( $d ) > 0 ) {
			$diff = ' (' . ( ( $d > 0 ) ? '+' . $d : $d ) . ')';
		}
	}
	$summary[] = __( 'Files scanned', 'wp-cerber' ) . '&nbsp;<b>' . $scan['scanned']['files'] . '</b>' . $diff;

	// TODO: refactor, keep the counter it in $scan
	$tot = 0;
	foreach ( $scan['issues'] as $sec ) {
		foreach ( $sec['issues'] as $i ) {
			if ( $i[0] > CERBER_FOK ) {
				$tot ++;
			}
		}
	}

	$diff = '';
	if ( ! empty( $prev_scan['issues'] ) ) {
		$prev_tot = 0;
		foreach ( $prev_scan['issues'] as $sec ) {
			foreach ( $sec['issues'] as $i ) {
				if ( $i[0] > CERBER_FOK ) {
					$prev_tot ++;
				}
			}
		}

		if ( $prev_tot ) {
			$d = $tot - $prev_tot;
			if ( absint( $d ) > 0 ) {
				$diff = ' (' . ( ( $d > 0 ) ? '+' . $d : $d ) . ')';
			}
		}
	}

	$summary[] = __( 'Issues total', 'wp-cerber' ) . '&nbsp;<b>' . $tot . '</b>'.$diff;

	// Issues

	$isize     = crb_get_settings( 'scan_isize' );
	$cols      = ( $isize ) ? 3 : 2;
	$table     = cerber_get_db_prefix() . CERBER_SCAN_TABLE;
	$deleted   = 0;
	$recovered = 0;

	if ( $for_report ) {
		$conames        = array( 'crb-plugins' => 'plugin', 'crb-themes' => 'theme', 'crb-wordpress' => 'files' );
		$rows           = array();
		//$rows[] = '<td style="'.$css_td.$css_border.'" colspan="2"><p style="line-height: 1.5em; font-weight: bold;">Issues</p></td>';
		foreach ( $for_report as $section_id => $section ) {
			$section_items = array();
			$extra         = '';
			$vlist         = '';
			$c             = ( isset( $conames[ $section['container'] ] ) ) ? ' ' . $conames[ $section['container'] ] : '';
			//$rows[] = '<td style="' . $css_border . $css_td . '" colspan="'.$cols.'"><b>' . $section['name'] . $c . '</b></td>';
			$i              = 0;
			foreach ( $section['issues'] as $issue ) {
				if ( $issue[0] < 10 ) {
					if ( $issue[0] == CERBER_VULN ) {
						$vlist .= $issue[1] . '<br/>';
					}
					else {
						$extra .= ' '.cerber_get_html_label( $issue[0] );
					}
					continue;
				}
				$i ++;
				$color = ( $issue[2] > 2 ) ? ' color: #dd1320;' : '';
				//$bgcolor = ( $i % 2 == 0 ) ? ' bgcolor: #fff;' : '';
				$size = '';
				if ( $isize ) {
					$size_diff = '';
					if ( $issue[0] !== CERBER_NEW && $prev_id ) {
						$psize = cerber_db_get_var( 'SELECT file_size FROM ' . $table . ' WHERE scan_id = ' . $prev_id . ' AND file_name_hash = "' . sha1( $issue['data']['name'] ) . '"' );
						if ( is_numeric( $psize ) ) {
							$diff = $issue['data']['bytes'] - $psize;
							if ( absint( $diff ) > 0 ) {
								$size_diff = crb_size_format( $diff );
								$size_diff = ' (' . ( ( $diff > 0 ) ? '+' . $size_diff : '-' . $size_diff ) . ')';
							}
						}
					}
					$size = '<td>' . $issue['data']['size'] . $size_diff . '</td>';
				}

				$status = '';
				if ( isset( $issue['data']['prced'] ) ) {
					switch ( $issue['data']['prced'] ) {
						case CERBER_FDLD:
							$status = ' <span style="background-color: #333; color: #fff; padding: 2px;">' . __( 'Deleted', 'wp-cerber' ) . '</span> ';
							$deleted ++;
							break;
						case CERBER_FRCV:
							$status = ' <span style="background-color: #0963d5;; color: #fff; padding: 2px;">' . __( 'Recovered', 'wp-cerber' ) . '</span> ';
							$recovered ++;
							break;
					}
				}
				//$rows[] = '<td style="' . $css_border . $css_td . ' font-size:94%; font-family:  Menlo, Consolas, Monaco, monospace;">' . $single_issue[1] . '</td><td style="padding: 0.5em; text-align: center; ' . $color . $css_border . '">' . cerber_get_issue_label( $single_issue[0] ) . '</td>'.$size;
				$section_items[] = '<td style="' . $css_border . $css_td . ' font-size:94%; font-family:  Menlo, Consolas, Monaco, monospace;">' . $issue[1] . $status . '</td><td style="padding: 0.5em; text-align: center; ' . $color . $css_border . '">' . cerber_get_issue_label( $issue[0] ) . '</td>' . $size;
			}
			/*
			if ( ! $i ) {
				array_pop( $rows );
			}*/
			if ( $section_items || $vlist ) {
				if ( $vlist ) {
					$extra = cerber_get_html_label( CERBER_VULN ) . $extra;
				}
				$rows[] = '<td style="' . $css_border . $css_td . '" colspan="' . $cols . '"><b>' . $section['name'] . $c . '</b> ' . $extra . ' <p>' . $vlist . '</p></td>';
				$rows   = array_merge( $rows, $section_items );
			}
		}

		if ( ! $rows ) {
			return false;
		}

		$ret .= '<table style="border-collapse: collapse; ' . $css_table . '"><tr>' . implode( '</tr><tr>', $rows ) . '</tr></table>';
	}

	// Errors

	if ( crb_get_settings( 'scan_ierrors' ) && $ers = cerber_get_scan_errors()) {
		$ret .= '<table style="' . $css_table . '"><tr><td style="' . $css_td . ' font-size:80%;" ><p style="font-weight: bold; margin:0;">Some errors occurred during the scan</p><ol style="padding-left: 1em;"><li>' . implode( '</li><li>', $ers ) . '</li></ol></td></tr></table>';
	}


	// Some KPI numbers
	$inc = array( CERBER_VULN, CERBER_NEW, CERBER_MOD, CERBER_USF, CERBER_UXT );
	foreach ( $inc as $id ) {
		if ( isset( $scan['numbers'][ $id ] ) ) {
			$css = '';
			if ( $id == CERBER_VULN ) {
				$css = 'color:red;';
			}
			$summary[] = '<span style="' . $css . '">' . cerber_get_issue_label( $id ) . '&nbsp;<b>' . $scan['numbers'][ $id ] . '</b></span>';
		}
	}

	$qu = cerber_admin_link( 'scan_quarantine', array( 'scan' => $scan['id'] ) );
	if ( $deleted ) {
		__( 'Automatically moved to quarantine', 'wp-cerber' );
		$summary[] = '<a href="' . $qu . '">' . __( 'Automatically deleted', 'wp-cerber' ) . '&nbsp;<b>' . $deleted . '</b></a>';
	}
	if ( $recovered ) {
		$summary[] = '<a href="' . $qu . '">' . __( 'Automatically recovered', 'wp-cerber' ) . '&nbsp;<b>' . $recovered . '</b></a>';
	}

	//$summary = implode( ' &nbsp;|&nbsp; ', $summary );
	//$summary = '<div style="display:inline-block; background-color: #1DA1F2; padding: 3px;">'.implode( '</div><div style="display:inline-block; background-color: #1DA1F2; padding: 3px;">', $summary ).'</div>';
	$summary = '<p>'.implode( '</p><p>', $summary ).'</p>';
	$header  = '<div style="' . $css_table . '"><div style="margin:0 auto; text-align: center;"><p style="font-size: 130%; padding-top: 0.5em;">' . $site_name . '</p><p style="">' . $mode . '</p><div style="padding-bottom: 1em;">' . $summary . '</div></div></div>';

	$ret = $header . $ret;

	$ret = '<div style="width:100%; padding: 1em; margin:0; text-align: center; background-color: #f9f9f9;">' . $ret . '</div>';

	$ret .= '<p>' . __( 'To view full report visit', 'wp-cerber' ) . ' <a href="' . $base_url . '">' . $base_url . '</a></p>';

	return $ret;

}

/**
 * Filter out a list of issues for a user report
 *
 * @param array $scan
 * @param array $types
 * @param array $severity
 *
 * @return array
 */
function cerber_filter_issues( $scan, $types, $severity ) {
	//$scan = cerber_get_scan($scan_id);
	$result = array();
	if ( empty( $scan['issues'] ) ) {
		return $result;
	}
	if ( $scan['issues'] ) {
		foreach ( $scan['issues'] as $section_id => $section ) {
		    $list = array();
			$sec_details = array();
			foreach ( $section['issues'] as $issue ) {
			    if ( in_array( $issue[2], $severity ) ) {
				    $list[] = $issue;
				    continue;
				}
                if ( in_array( $issue[0], $types ) ) {
	                $list[] = $issue;
	                continue;
				}
                if ( $issue[3] && in_array( $issue[3], $types ) ) {
	                $list[] = $issue;
	                continue;
				}
				if ( $issue[0] < 10 ) {
					$sec_details[] = $issue;
				}
			}

			if ( $list ) {
				$list = array_merge( $sec_details, $list );
				$result[ $section_id ]           = $section;
				$result[ $section_id ]['issues'] = $list;
			}
		}
	}

	return $result;
}

function cerber_get_new_issues( $list_a, $list_b ) {
	$ret = array();
	foreach ( $list_b as $key => $new ) {
		if ( ! isset( $list_a[ $key ] ) ) {
			$ret[ $key ] = $new;
			continue;
		}

		$new_elements = array();
		foreach ( $new['issues'] as $i => $b_issue ) {
			if ( ! empty( $b_issue[1] ) ) {
				$found = 0;
				foreach ( $list_a[ $key ]['issues'] as $a_issue ) {
					if ( $a_issue['data']['name'] == $b_issue['data']['name'] ) {
						$found = 1;
						break;
					}
				}
				if ( ! $found ) {
					$new_elements[] = $i;
				}
			}
		}

		if ( $new_elements ) {
			$ret[ $key ] = $new;
			$all         = array_keys( $new['issues'] );
			$diff        = array_diff( $all, $new_elements );

			foreach ( $diff as $i ) {
				unset( $ret[ $key ]['issues'][ $i ] );
			}
		}

	}

	return $ret;
}

function cerber_check_vulnerabilities( $plugin_slug, $plugin ) {
	if ( strpos( $plugin_slug, '.' ) ) {
		return false;
	}
	$ret = cerber_get_vulnerabilities( $plugin_slug, $plugin );
	if ( ! $ret ) {
		$ret = false;
	}
    elseif ( is_wp_error( $ret ) ) {
		crb_scan_debug( 'ERROR: ' . $ret->get_error_message() );
		$ret = false;
	}

	return $ret;
}

/**
 * @param $plugin_slug string
 * @param $plugin array
 *
 * @return array|bool|WP_Error
 */
function cerber_get_vulnerabilities( $plugin_slug, $plugin ) {

	if ( ! lab_lab() ) {
		return false;
	}

	$key = '_crb_vu_plugins';
	$vu_list = cerber_get_set( $key );

	if ( ! $vu_list
	     || ( ! isset( $vu_list['plugins'][ $plugin_slug ] ) && ! isset( $vu_list['cloud_error'] ) ) ) {
		crb_scan_debug( 'Getting vulnerability data from the cloud.' );

		$plugins = array_keys( get_plugins() );
		array_walk( $plugins, function ( &$e ) {
			$e = dirname( $e );
		} );
		$plugins = array_filter( $plugins, function ( $e ) {
			return ( false === strpos( $e, '.' ) );
		} );

		if ( ! $vu_list = lab_api_send_request( array(
			'get_vu_list' => array(
				'plugins' => $plugins,
			)
		), 'vu_list' ) ) {
			$vu_list = array( 'cloud_error' => 1 );
			$t  = 120; // Network error
		}
		else {
			$t = 3600; // OK
		}

		cerber_update_set( $key, $vu_list, null, true, time() + $t );
	}

	if ( isset( $vu_list['cloud_error'] ) ) {
		return new WP_Error( 'network_error', 'Unable to get the list of vulnerabilities' );
	}

	$ret = array();
	$lst = crb_array_get( $vu_list['plugins'], $plugin_slug );

	if ( empty( $lst ) ) {
		return $ret;
	}

	foreach ( $lst as $v ) {
		if ( version_compare( $v['fixed_in'], $plugin['Version'], '>' ) ) {
			$ret[] = array(
				'vu_info' => $v['short_desc'] . ' ' . 'Fixed in version: ' . $v['fixed_in']
			);
		}
	}

	return $ret;
}

/**
 * Check a filename has a specific extension
 *
 * @param $file_name
 * @param $setting string Setting slug with a set of file extensions to check for
 *
 * @return bool
 */
function cerber_has_extension( $file_name, $setting ) {
	static $list = null;

	if ( ! isset( $list[ $setting ] ) ) {
		if ( $list[ $setting ] = crb_get_settings( $setting ) ) {
			$list[ $setting ] = array_map( function ( $ext ) {
				return strtolower( trim( $ext, '. *' ) );
			}, $list[ $setting ] );
		}
		else {
			$list[ $setting ] = false;
		}
	}

	if ( false === $list[ $setting ] ) {
		return false;
	}

	$f = strtolower( basename( $file_name ) );
	$e = explode( '.', $f );
	array_shift( $e );
	if ( $e && array_intersect( $list[ $setting ], $e ) ) {
		return true;
	}

	return false;

}

function cerber_make_numbers( &$update = array(), &$scan = array() ) {
	$update['numbers'] = ( ! isset( $scan['numbers'] ) ) ? array() : $scan['numbers'];
	foreach ( $scan['step_issues'] as $set ) {
		foreach ( $set['issues'] as $issue ) {
			if ( ! isset( $update['numbers'][ $issue[0] ] ) ) {
				$update['numbers'][ $issue[0] ] = 0;
			}
			$update['numbers'][ $issue[0] ] ++;
			// extra issue
			if ( ! empty( $issue[3] ) ) {
				if ( ! isset( $update['numbers'][ $issue[3] ] ) ) {
					$update['numbers'][ $issue[3] ] = 0;
				}
				$update['numbers'][ $issue[3] ] ++;
			}
		}
		if ( $set['setype'] == 21 ) {
			if ( ! isset( $update['numbers'][ CERBER_USF ] ) ) {
				$update['numbers'][ CERBER_USF ] = 0;
			}
			$update['numbers'][ CERBER_USF ] += count( $set['issues'] );
		}
	}
}

function crb_scan_debug( $msg ) {

	if ( crb_get_settings( 'scan_debug' ) ) {
		cerber_diag_log( cerber_db_get_errors( true ), 'Scanner' );
		cerber_diag_log( $msg, 'Scanner' );
	}
}

/**
 * array_filter( $list )
 *
 * @param $list array
 * @param $function callable
 *
 * @return array
 */
function crb_issue_filer( $list, $function ) {
	//$filtered = $scan['issues'];
	$filtered = $list;
	foreach ( $list as $key => $item ) {
		if ( isset( $item['issues'] ) ) {
			foreach ( $item['issues'] as $id => $issue ) {
				if ( isset( $issue['data']['name'] ) ) {
					if ( ! call_user_func( $function, $issue['data']['name'] ) ) {
						unset( $filtered[ $key ]['issues'][ $id ] );
					}
				}
			}
			// We have to refresh indexes for JS code in the user browser
			$filtered[ $key ]['issues'] = array_values( $filtered[ $key ]['issues'] );
		}
	}
	//$response['issues'] = $filtered;
	return $filtered;
}

function crb_qr_total_update( $diff ) {
	if ( ! $numq = cerber_get_set( 'quarantined_total', null, false ) ) {
		$numq = 0;
	}
	$numq = $numq + $diff;
	if ( $numq < 0 ) {
		$numq = 0;
	}

	cerber_update_set( 'quarantined_total', $numq, null, false );
}

function _crb_qr_total_sync( $total = null ) {
	if ( ! $total ) {
		$q = cerber_quarantine_get_files();
		if ( is_wp_error( $q ) ) {
			return;
		}
		$total = $q[1];
	}

	cerber_update_set( 'quarantined_total', $total, null, false );
}