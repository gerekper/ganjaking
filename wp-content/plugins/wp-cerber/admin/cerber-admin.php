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

// Scan dashboard ===========================================================

function cerber_scanner_dashboard( $msg = '' ) {
	?>
    <div id="crb-scan-display">
        <div id="crb-the-table">
            <div class="crb-scan-info scan-tile">
                <table>
                    <tr>
                        <td><?php _e( 'Started', 'wp-cerber' ); ?></td>
                        <td id="crb-started" data-init="-">-</td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Finished', 'wp-cerber' ); ?></td>
                        <td id="crb-finished" data-init="-">-</td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Duration', 'wp-cerber' ); ?></td>
                        <td id="crb-duration" data-init="-">-</td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Performance', 'wp-cerber' ); ?></td>
                        <td id="crb-performance" data-init="-">-</td>
                    </tr>
                    <tr>
                        <td>Mode</td>
                        <td id="crb-smode" data-init="-">-</td>
                    </tr>
                </table>
            </div>
            <div id="crb-scan-filter" class="crb-scan-info scan-tile">
                <table>
                    <!--<tr id="crb-numbers-4">
                        <td><span><?php _e( 'Vulnerabilities', 'wp-cerber' ); ?></span></td>
                        <td class="crb-scan-number" data-init="-">-</td>
                    </tr> -->
                    <tr id="crb-numbers-51">
                        <td><span data-itype-list="[51]"><?php _e( 'New files', 'wp-cerber' ); ?></span></td>
                        <td class="crb-scan-number" data-init="-">-</td>
                    </tr>
                    <tr id="crb-numbers-50">
                        <td><span data-itype-list="[50]"><?php _e( 'Changed files', 'wp-cerber' ); ?></span></td>
                        <td class="crb-scan-number" data-init="-">-</td>
                    </tr>
                    <tr id="crb-numbers-15">
                        <td><span data-itype-list="[15]"><?php _e( 'Checksum mismatch', 'wp-cerber' ); ?></span></td>
                        <td class="crb-scan-number" data-init="-">-</td>
                    </tr>
                    <tr id="crb-numbers-30">
                        <td><span data-itype-list="[30]"><?php _e( 'Unwanted extensions', 'wp-cerber' ); ?></span></td>
                        <td class="crb-scan-number" data-init="-">-</td>
                    </tr>
                    <tr id="crb-numbers-18">
                        <td><span data-itype-list="[18]" data-setype-list="[21]"><?php _e( 'Unattended files', 'wp-cerber' ); ?></span></td>
                        <td class="crb-scan-number" data-init="-">-</td>
                    </tr>
                </table>
            </div>
            <div class="scan-tile">
                <div><p><span id="crb-scanned-files" data-init="0">0</span> / <span id="crb-total-files"
                                                                                    data-init="0">0</span>
                    </p>
                    <p><?php echo __( 'Scanned', 'wp-cerber' ) . ' / ' . __( 'Files to scan', 'wp-cerber' ); ?></p>
                </div>
            </div>

            <div class="scan-tile">
                <div><p><span id="crb-critical" data-init="0">0</span> / <span id="crb-warning" data-init="0">0</span>
                    </p>
                    <p><?php _e( 'Critical issues', 'wp-cerber' ); ?> / <?php _e( 'Issues total', 'wp-cerber' ); ?></p>
                </div>
            </div>

        </div>

        <div id="crb-scan-progress">
            <div>
                <div id="the-scan-bar"></div>
            </div>
        </div>

        <p id="crb-scan-message"><?php echo $msg; ?></p>

    </div>
    <div id="crb-scan-details">
        <table class="crb-table" id="crb-browse-files">
			<?php
			$rows = array();
			$rows[] = '<tr class="crb-scan-container" id="crb-wordpress" style=""><td colspan="6">WordPress</td></tr>';
			$rows[] = '<tr class="crb-scan-container" id="crb-muplugins" style=""><td colspan="6">Must use plugins</td></tr>';
			$rows[] = '<tr class="crb-scan-container" id="crb-dropins" style=""><td colspan="6">Drop-ins</td></tr>';
			$rows[] = '<tr class="crb-scan-container" id="crb-plugins" style=""><td colspan="6">Plugins</td></tr>';

			/*
			$plugins = get_plugins();
			foreach ( $plugins as $plugin ) {
				$rows[] = '<tr class="crb-scan-section" id="' . sha1( $plugin['Name'] ) . '" style="display:none;"></tr>';
			}
			*/
			$rows[] = '<tr class="crb-scan-container" id="crb-themes" style=""><td colspan="6">Themes</td></tr>';

			/*$themes = wp_get_themes();
			foreach ( $themes as $theme_folder => $theme ) {
				$rows[] = '<tr class="crb-scan-section" id="' . sha1( $theme->get( 'Name' ) ) . '" style="display:none;"></tr>';
			}*/

			$rows[] = '<tr class="crb-scan-container" id="crb-uploads" style=""><td colspan="6">Uploads folder</td></tr>';
			$rows[] = '<tr class="crb-scan-container" id="crb-unattended" style=""><td colspan="6">Unattended files</td></tr>';
			echo implode( "\n", $rows );
			?>
        </table>
    </div>

	<?php

	cerber_ref_upload_form();
}

function cerber_show_scanner() {
	// http://www.adequatelygood.com/JavaScript-Module-Pattern-In-Depth.html

	$msg = '';
	$status = 0;

	if ( $scan = cerber_get_scan() ) {
		if ( ! $scan['finished'] ) {
			if ( $scan['cloud']
			     && cerber_is_cloud_enabled()
			     && $scan['started'] > ( time() - 900 )
			) {
				$msg = __( 'Currently a scheduled scan in progress. Please wait until it is finished.', 'wp-cerber' );
				$status = 1;
			}
			else {
				$msg = sprintf( __( 'Previous scan started %s has not been completed. Continue scanning?', 'wp-cerber' ), cerber_date( $scan['started'], false ) );
				$status = 2;
			}
		}
		else {

		}
	}
	else {
		$msg = __( 'It seems this website has never been scanned. To start scanning click the button below.', 'wp-cerber' );
	}

	$start_quick = '<input data-control="start_scan" data-mode="quick" type="button" value="' . __( 'Start Quick Scan', 'wp-cerber' ) . '" class="button button-primary">';
	$start_full = '<input data-control="start_scan" data-mode="full" type="button" value="' . __( 'Start Full Scan', 'wp-cerber' ) . '" class="button button-primary">';
	$stop = '<input id="crb-stop-scan" style="display: none;" data-control="stop_scan" type="button" value="' . __( 'Stop Scanning', 'wp-cerber' ) . '" class="button button-primary">';
	$continue = '<input id="crb-continue-scan" data-control="continue_scan" type="button" value="' . __( 'Continue Scanning', 'wp-cerber' ) . '" class="button button-primary">';
	$controls = '';

	switch ( $status ) {
		case 0:
			$controls = $start_quick . $start_full;
			break;
		case 1:
			$controls = '';
			break;
		case 2:
			$controls = $start_quick . $start_full . $continue;
			break;
	}

	$controls .= $stop;


	echo '<div id="crb-scanner">';

	cerber_scanner_dashboard( $msg );

	$d = '';
	if ( nexus_is_valid_request() && ! nexus_is_granted( 'submit' ) ) {
		$d = 'disabled="disabled"';
	}

	?>
    <div id="crb-scan-area">
        <form>
            <table id="crb-scan-controls">
                <tr>
                    <td id="crb-file-controls">
                        <input data-control="delete_file" type="button"
                               class="button button-secondary"
							<?php echo $d; ?>
                               value="<?php _e( 'Delete', 'wp-cerber' ); ?>"/>
                        <input data-control="ignore_add_file" type="button" class="button button-secondary"
							<?php echo $d; ?>
                               value="<?php _e( 'Ignore', 'wp-cerber' ); ?>"/>
                    </td>
                    <td>
						<?php echo $controls; ?>
                    </td>
                    <!-- <td><a href="#" data-control="full-paths">Show full paths</a></td> -->
                    <td><a href="#" class="dashicons dashicons-list-view" data-control="full-paths"
                           title="Toggle full/relative paths"></a></td>
                </tr>
            </table>
        </form>
    </div>

	<?php

	echo '</div>';
}

function cerber_ref_upload_form() {
	?>
    <div id="crb-ref-upload-dialog" style="display: none;">
        <p><?php _e( 'We have not found any integrity data to verify', 'wp-cerber' ); ?> <span
                    id="ref-section-name"></span>.</p>
        <p><?php _e( "You have to upload a ZIP archive from which you've installed it. This enables the security scanner to verify the integrity of the code and detect malware.", 'wp-cerber' ); ?></p>
        <p><?php echo sprintf( __( 'Maximum upload file size: %s.' ), esc_html( size_format( wp_max_upload_size() ) ) ); ?></p>
        <form enctype="multipart/form-data">
            <input type="file" name="refile" id="refile" required="required" accept=".zip">
            <input type="submit" name="submit" value="<?php _e( 'Upload file', 'wp-cerber' ); ?>"
                   class="button button-primary">
            <ul style="list-style: none;">
                <li style="display:none;" class="crb-status-msg">Uploading the file, please wait&#8230;</li>
                <li style="display:none;" class="crb-status-msg">Processing the file, please wait&#8230;</li>
            </ul>
        </form>
    </div>

	<?php
}

add_action( 'wp_ajax_cerber_scan_control', 'cerber_manual_scan' );
function cerber_manual_scan() {

	cerber_check_ajax_permissions();

	ob_start(); // Collecting possible junk warnings and notices cause we need clean JSON to be sent

	$scanner = array();
	$console_log = array();
	$scan_do = '';

	if ( cerber_is_http_post() && $scan_do = crb_get_post_fields( 'cerber_scan_do' ) ) {
		$scan_do = preg_replace( '/[^a-z_\-\d]/i', '', $scan_do );
		$mode = ( $mode = crb_get_post_fields( 'cerber_scan_mode' ) ) ? preg_replace( '/[^a-z_\-\d]/i', '', $mode ) : 'quick';

		$scanner = cerber_scanner( $scan_do, $mode );

	}
	else {
		$console_log[] = 'Unknown HTTP request';
	}

	$next_do = ( ! empty( $scanner['cerber_scan_do'] ) ) ? $scanner['cerber_scan_do'] : 'stop';

	$console_log = array_merge( $console_log, cerber_db_get_errors() );

	$console_log[] = 'PHP MEMORY ' . @ini_get( 'memory_limit' );

	$ret = array(
		'console_log'    => $console_log,
		'cerber_scan_do' => $next_do,
		'cerber_scanner' => $scanner,
		//'scan'           => cerber_get_scan(), // debug only
	);

	if ( $scan_do != 'continue_scan' ) {
		$ret['strings'] = cerber_get_strings();
	}

	ob_end_clean();

	echo json_encode( $ret );

	crb_admin_stop_ajax();
}

/**
 * File viewer, server side AJAX
 *
 */
add_action( 'wp_ajax_cerber_view_file', function () {
	global $crb_assets_url;

	cerber_check_ajax_permissions();

	$get = crb_get_query_params();
	$file_name = $get['file'];

	if ( ! @is_file( $file_name ) ) {
		crb_admin_stop_ajax( 'I/O Error' );

		return;
	}

	$file_size = filesize( $file_name );

	if ( $file_size > 8000000 ) {
		crb_admin_stop_ajax( 'Error: This file is too large to display.' );

		return;
	}

	if ( $file_size <= 0 ) {
		crb_admin_stop_ajax( 'The file is empty.' );

		return;
	}

	$scan_id = absint( $get['scan_id'] );

	$the_file = cerber_db_get_row( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan_id . ' AND file_name = "' . $file_name . '"' );

	if ( ! $the_file ) {
		crb_admin_stop_ajax( __( 'File access error. Possibly scan results are outdated. Please run Quick or Full Scan.', 'wp-cerber' ) );

		return;
	}

	if ( ! $source = file_get_contents( $file_name ) ) {
		crb_admin_stop_ajax( 'Error: Unable to load file.' );

		return;
	}

	$source = htmlspecialchars( $source, ENT_SUBSTITUTE );

	if ( ! $source ) {
		$source = 'Unable to display the contents of the file. This file contains non-printable characters.';
	}

	if ( cerber_detect_exec_extension( $file_name )
	     || cerber_check_extension( $file_name, array( 'js', 'css', 'inc' ) )
	     || cerber_is_htaccess( $file_name )
	) {
		$paint = true;
	}
	else {
		$paint = false;
	}

	$overlay = '';
	if ( $paint ) {
		$overlay = '<div id="crb-overlay">Loading, please wait...</div>';
	}

	//$sh_url   = plugin_dir_url( __FILE__ ) . 'assets/sh/';
	$sh_url = $crb_assets_url . 'sh/';
	$sheight = absint( $get['sheight'] ) - 100; // highlighter is un-responsible, so we need tell him the real height

	?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <script type="text/javascript" src="<?php echo $sh_url ?>scripts/shCore.js"></script>
        <script type="text/javascript" src="<?php echo $sh_url; ?>scripts/shBrushPhp.js"></script>
        <link href="<?php echo $sh_url; ?>styles/shCore.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $sh_url; ?>styles/shThemeDefault.css" rel="stylesheet" type="text/css"/>
        <style type="text/css" media="all">
            body {
                overflow: hidden;
                font-family: 'Roboto', sans-serif;
                font-size: 14px;
            }

            #crb-overlay {
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                background-color: #fff;
                position: fixed;
                width: 100%;
                height: 100%
                z-index: 2;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
            }

            #crb-issue {
                border-left: 3px solid crimson;
                background-color: #eee;
                padding: 1em;
                overflow: auto;
            }

            #crb-file-content {
            <?php
            if (!$paint) {
                echo '
                max-height: '.$sheight .'px;
                overflow: auto;
                padding: 15px;
                ';
            }
            else {
                echo 'overflow: hidden;';
            }
            ?>
            }

            .syntaxhighlighter {
                max-height: <?php echo $sheight; ?>px;
            }

            .syntaxhighlighter code {
                font-family: Menlo, Consolas, Monaco, monospace !important;
                font-size: 13px !important;
            }

            .syntaxhighlighter .gutter .line {
                border-right: 3px solid #c7c7c7 !important;
            }

        </style>
    </head>

    <body>

	<?php

	echo $overlay;

	echo '<pre id="crb-file-content" class="brush: php; toolbar: false;">' . $source . '</pre>';

	if ( $the_file ) {
		echo '<div id="crb-issue">Issue: ' . cerber_get_issue_label( $the_file['scan_status'] ) . '</div>';
	}

	if ( $paint ) :
		?>

        <script type="text/javascript">
            SyntaxHighlighter.defaults["highlight"];
            SyntaxHighlighter.all();

            function crb_waitUntilRender() {
                var overlay = document.getElementById("crb-overlay").style.visibility = "hidden";
            }

            var intervalID = setInterval(crb_waitUntilRender, 200);


        </script>

	<?php

	endif;

	?>

    </body>
    </html>

	<?php

	crb_admin_stop_ajax();

} );

/**
 * Upload a reference ZIP archive for a theme or a plugin
 *
 */
add_action( 'wp_ajax_cerber_ref_upload', function () {

	cerber_check_ajax_permissions();

	//ob_start(); // Collecting possible junk warnings and notices cause we need clean JSON to be sent

	$error = '';

	$folder = cerber_get_tmp_file_folder();
	if ( is_wp_error( $folder ) ) {
		cerber_end_ajax( array( 'error' => $folder->get_error_message() ) );
	}

	if ( isset( $_FILES['refile'] ) ) {

		// Step 1, saving file

		if ( ! is_uploaded_file( $_FILES['refile']['tmp_name'] ) ) {
			$error = 'Unable to read uploaded file';
		}

		if ( ! cerber_check_extension( $_FILES['refile']['name'], array( 'zip' ) ) ) {
			$error = 'Incorrect file format';
		}

		if ( cerber_detect_exec_extension( $_FILES['refile']['name'] ) ) {
			$error = 'Incorrect file format';
		}

		if ( false !== strpos( $_FILES['refile']['name'], '/' ) ) {
			$error = 'Incorrect filename';
		}

		if ( $error ) {
			cerber_end_ajax( array( 'error' => $error ) );
		}

		if ( false === @move_uploaded_file( $_FILES['refile']['tmp_name'], $folder . $_FILES['refile']['name'] ) ) {
			cerber_end_ajax( array( 'error' => 'Unable to copy file to ' . $folder ) );
		}

	}
	else {

		// Step 2, creating hash

		$result = cerber_need_for_hash();
		if ( is_wp_error( $result ) ) {
			cerber_end_ajax( array( 'error' => $result->get_error_message() ) );
		}
	}

	cerber_end_ajax();

} );

/**
 * Deleting files, server side AJAX
 *
 */
add_action( 'wp_ajax_cerber_scan_bulk_files', function () {

	cerber_check_ajax_permissions();

	$post = crb_get_post_fields();

	if ( empty( $post['files'] ) || empty( $post['scan_id'] ) ) {
		crb_admin_stop_ajax( 'Error!' );

		return;
	}

	$scan_id = absint( $post['scan_id'] );

	if ( ! cerber_get_scan( $scan_id ) ) {
		crb_admin_stop_ajax( 'Error!' );

		return;
	}

	$operation = $post['scan_file_operation'];

	if ( ( ! $ignore = cerber_get_set( 'ignore-list' ) ) || ! is_array( $ignore ) ) {
		$ignore = array();
	}

	global $crb_list;
	$crb_list = array();
	$i = 0;
	$errors = array();
	$time = time();
	$user_id = get_current_user_id();

	foreach ( $post['files'] as $file_name ) {

		if ( ! is_file( $file_name ) ) {
			continue;
		}

		$the_file = cerber_db_get_row( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan_id . ' AND file_name = "' . $file_name . '"', MYSQL_FETCH_OBJECT );
		if ( ! $the_file || ! is_file( $the_file->file_name ) ) {
			$errors[] = 'Unknown file: ' . $file_name;
			continue;
		}

		switch ( $operation ) {
			case 'delete_file':
				$result = cerber_quarantine_file( $file_name, $scan_id );
				break;
			case 'ignore_add_file':
				$ignore[ $the_file->file_name_hash ] = array(
					$the_file->file_name,
					@hash_file( 'sha256', $the_file->file_name ),
					$user_id,
					$time,
				);
				$result = true;
				break;
		}

		if ( is_wp_error( $result ) ) {
			$errors[] = $result->get_error_message();
		}
        elseif ( ! $result ) {
			$errors[] = 'Unknown error 55';
		}
		else {
			$i ++;
			$crb_list[] = $file_name;
		}

	}

	if ( $operation == 'ignore_add_file' ) {
		// Update the last scan results to keep it up to date and avoid user confusing
		if ( $scan = cerber_get_scan() ) {
			$scan['issues'] = crb_issue_filer( $scan['issues'], function ( $file_name ) {
				global $crb_list;
				if ( in_array( $file_name, $crb_list ) ) {
					return false;
				}

				return true;
			} );
			cerber_update_scan( $scan );
		}
		if ( ! cerber_update_set( 'ignore-list', $ignore ) ) {
			$errors [] = 'Unable to update the ignore list';
		}
	}

	crb_scan_debug( $errors );

	cerber_end_ajax( array( 'errors' => $errors, 'number' => $i, 'processed' => $crb_list ) );

} );

/**
 * Finalizes current AJAX request and sends data to the client
 *
 * @param $data array
 */
function cerber_end_ajax( $data = array() ) {

	if ( ! $data ) {
		$data = array();
	}

	$data['cerber_db_errors'] = cerber_db_get_errors();

	if ( ! $data['cerber_db_errors'] ) {
		$data['OK'] = 'OK!';
	}

	echo json_encode( $data );

	if ( ! nexus_is_valid_request() ) {
		wp_die();
	}
}

function crb_admin_stop_ajax( $msg = '' ) {
	if ( $msg ) {
		echo $msg;
	}
	if ( ! nexus_is_valid_request() ) {
		//wp_die();
		exit;
	}
}

function cerber_show_quarantine() {

	$folder = cerber_get_the_folder( true );
	if ( is_wp_error( $folder ) ) {
		echo $folder->get_error_message();

		return;
	}

	$no_files = '<p>' . __( 'There are no files in the quarantine at the moment.', 'wp-cerber' ) . '</p>';
	$per_page = crb_admin_get_per_page();
	$first = ( cerber_get_pn() - 1 ) * $per_page;
	$last = $first + $per_page;
	$list = array();

	$filter_scan = crb_get_query_params( 'scan', '\d+' );

	list ( $list, $count, $scan_list ) = cerber_quarantine_get_files( $first, $last, $filter_scan );

	_crb_qr_total_sync( $count );

	if ( ! $list ) {
		if ( ! $filter_scan ) {
			echo $no_files;
		}
		else {
			echo __( 'No files match the specified filter.', 'wp-cerber' ) . ' <a href="' . cerber_admin_link( 'scan_quarantine' ) . '">' . __( 'Click here to see the full list of files', 'wp-cerber' ) . '</a>.';
		}

		return;
	}

	$rows = array();
	$ofs = get_option( 'gmt_offset' ) * 3600;
	$confirm = ' onclick="return confirm(\'' . __( 'Are you sure?', 'wp-cerber' ) . '\');"';

	foreach ( $list as $file ) {
		$p = array(
			'cerber_admin_do' => 'scan_tegrity',
			'crb_scan_id'     => $file['scan_id'],
			'crb_file_id'     => $file['qfile']
		);

		$p['crb_scan_adm'] = 'delete';
		$delete = '<a ' . $confirm . ' href="' . cerber_admin_link_add( $p ) . '">' . __( 'Delete permanently', 'wp-cerber' ) . '</a>';

		$p['crb_scan_adm'] = 'restore';
		$restore = ( ! $file['can'] ) ? '' : ' | <a ' . $confirm . ' href="' . cerber_admin_link_add( $p ) . '">' . __( 'Restore', 'wp-cerber' ) . '</a>';

		$moved = strtotime( $file['date'] ) - $ofs;
		$will = cerber_auto_date( $file['scan_id'] + DAY_IN_SECONDS * crb_get_settings( 'scan_qcleanup' ) );

		$file_name = str_replace( DIRECTORY_SEPARATOR, '<wbr>' . DIRECTORY_SEPARATOR, $file['source'] );

		$rows[] = array(
			'<span title="' . cerber_date( $file['scan_id'] ) . '">' . cerber_auto_date( $file['scan_id'] ) . '</span>',
			'<span title="' . cerber_date( $moved ) . '">' . cerber_auto_date( $moved ) . '</span>',
			$will,
			$file['size'],
			$file_name,
			'<span style="white-space: pre;">' . $delete . $restore . '</span>'
		);
	}

	$heading = array(
		__( 'Scanned', 'wp-cerber' ),
		__( 'Quarantined', 'wp-cerber' ),
		__( 'Automatic deletion', 'wp-cerber' ),
		__( 'Size', 'wp-cerber' ),
		__( 'File', 'wp-cerber' ),
		__( 'Action', 'wp-cerber' ),
	);

	$table = cerber_make_table( $rows, $heading, 'crb-quarantine' );

	$table .= cerber_page_navi( $count, $per_page );

	$filter = '';
	if ( count( $scan_list ) > 1 ) {
		krsort( $scan_list );
		$list = array( 0 => __( 'All scans', 'wp-cerber' ) );
		foreach ( $scan_list as $s ) {
			$list[ $s ] = cerber_date( $s, false );
		}
		$filter = '<div style="text-align: right; margin-bottom: 1em;"><form style="width: auto;" action="">' . cerber_select( 'scan', $list, $filter_scan ) . ' <input value="Filter" class="button" type="submit"><input name="page" value="cerber-integrity" type="hidden"><input name="tab" value="scan_quarantine" type="hidden"></form></div>';
	}

	echo $filter . $table;
}

function cerber_quarantine_do( $what, $scan_id, $qfile ) {
	$scan_id = absint( $scan_id );
	if ( ! $scan_id ) {
		cerber_admin_notice( 'Error: Wrong scan parameters.' );

		return;
	}
	//$dir = cerber_get_the_folder() . 'quarantine' . DIRECTORY_SEPARATOR . $scan_id;
	$dir = cerber_get_the_folder( true );
	if ( is_wp_error( $dir ) ) {
		cerber_admin_notice( $dir->get_error_message() );

		return;
	}

	$dir .= 'quarantine' . DIRECTORY_SEPARATOR . $scan_id;

	$file = $dir . DIRECTORY_SEPARATOR . $qfile;
	if ( ! @is_file( $file ) || is_link( $file ) ) {
		cerber_admin_notice( 'Error: No file to process' );

		return;
	}

	$rst = $dir . '/.restore';
	if ( ! file_exists( $rst ) || ! $handle = @fopen( $rst, 'r' ) ) {
		cerber_admin_notice( 'Error: A restore registry file is corrupt or missing.' );

		return;
	}

	$data = null;
	while ( ( $line = fgets( $handle ) ) !== false ) {
		if ( $p = crb_parse_qline( $dir, $line ) ) {
			if ( $p['qfile'] == $qfile ) {
				$data = $p;
				break;
			}
		}
	}

	if ( ! $data ) {
		cerber_admin_notice( 'Error: No information about this file. Unable to proceed.' );

		return;
	}

	$err = null;
	$msg = null;
	switch ( $what ) {
		case 'delete':
			if ( unlink( $file ) ) {
				$msg = __( 'The file has been deleted permanently.', 'wp-cerber' );
				crb_qr_total_update( -1 );
			}
			else {
				$err = 'Unable to delete the file: ' . $file;
			}
			break;
		case 'restore':
			if ( $data['can'] ) {
				$target_dir = dirname( $data['source'] );
				if ( ! file_exists( $target_dir ) && ! mkdir( $target_dir, 0755, true ) ) {
					$err = 'Unable to create the folder <b>' . $target_dir . '</b>. Check permissions of parent folders.';
				}
				if ( ! $err ) {
					if ( @rename( $file, $data['source'] ) ) {
						$msg = __( 'The file has been restored to its original location.', 'wp-cerber' );
						crb_qr_total_update( -1 );
					}
					else {
						$err = 'A file error occurred while restoring the file. Check permissions of folders.';
					}
				}
			}
			else {
				$err = 'This file cannot be restored and needs to be manually copied. <p>See instructions in this file: ' . $rst . '</p>';
			}
			break;
	}
	if ( $err ) {
		cerber_admin_notice( __( 'ERROR:', 'wp-cerber' ) . ' ' . $err );
	}
	if ( $msg ) {
		cerber_admin_message( $msg );
	}
}

function cerber_show_ignore() {

	// For translators
	__( 'Apply', 'wp-cerber' );
	__( 'Remove from the list', 'wp-cerber' );
	__( 'User Insights', 'wp-cerber' );
	__( 'Traffic Insights', 'wp-cerber' );
	__( 'Activity Insights', 'wp-cerber' );

	$no_files = __( 'The list is empty.', 'wp-cerber' );
	$per_page = crb_admin_get_per_page();
	$first = ( cerber_get_pn() - 1 ) * $per_page;

	if ( ! $list = cerber_get_set( 'ignore-list' ) ) {
		echo '<p>' . $no_files . '</p>';

		return;
	}

	$count = count( $list );
	$list = array_slice( $list, $first, $per_page );

	$rows = array();
	$confirm = ' onclick="return confirm(\'' . __( 'Are you sure?', 'wp-cerber' ) . '\');"';

	foreach ( $list as $key => $file ) {

		$delete = '<a ' . $confirm . ' href="' . cerber_admin_link_add( array(
				'cerber_admin_do' => 'scan_tegrity',
				'crb_scan_adm'    => 'remove_ignore',
				'crb_file_id'     => $key
			) ) . '">' . __( 'Remove from the list', 'wp-cerber' ) . '</a>';

		$rows[] = array(
			cerber_date( $file[3] ),
			cerber_date( cerber_get_date( $file[0] ) ),
			crb_size_format( cerber_get_size( $file[0] ) ),
			$file[0],
			'<span style="white-space: pre;">' . $delete . '</span>'
		);
	}

	$heading = array(
		__( 'Added', 'wp-cerber' ),
		__( 'Modified', 'wp-cerber' ),
		__( 'Size', 'wp-cerber' ),
		__( 'File', 'wp-cerber' ),
		__( 'Action', 'wp-cerber' ),
	);

	$table = cerber_make_table( $rows, $heading );

	$table .= cerber_page_navi( $count, $per_page );

	echo $table;
}

function crb_remove_ignore( $id ) {
	if ( ! $list = cerber_get_set( 'ignore-list' ) ) {
		return false;
	}
	if ( ! isset( $list[ $id ] ) ) {
		return false;
	}

	unset( $list[ $id ] );

	return cerber_update_set( 'ignore-list', $list );

}

// Scan analytics ===========================================================

function cerber_scan_insights() {

	if ( ! $scan_id = crb_scan_last_full() ) {
		echo 'No data for generating reports. Please run the Full Scan. After the scan is completed, the report will be generated.';

		return;
	}

	if ( $ext = crb_get_query_params( 'fext' ) ) {
		cerber_show_files( $ext, $scan_id );

		return;
	}

	?>

    <div style="padding-bottom: 1.5em;" class="crb_async_content" data-ajax_route="scanner_analytics" data-scan_id="<?php echo $scan_id; ?>" data-itype="1">
    </div>
    <div style="padding-bottom: 1.5em;" class="crb_async_content" data-ajax_route="scanner_analytics" data-scan_id="<?php echo $scan_id; ?>" data-itype="2">
    </div>
    <div id="crb_ins_ext_list" style="padding-bottom: 1.5em;" class="crb_async_content" data-ajax_route="scanner_analytics" data-scan_id="<?php echo $scan_id; ?>" data-itype="3">
    </div>

	<?php

}

/**
 * @param string $ext
 * @param int $scan_id
 */
function cerber_show_files( $ext, $scan_id ) {

	$ext = cerber_real_escape( $ext );
	$per_page = 25;
	$limit = cerber_get_sql_limit( $per_page );
	$files = cerber_db_get_results( 'SELECT SQL_CALC_FOUND_ROWS file_name, file_mtime, file_size FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan_id . ' AND file_ext = "' . $ext . '" ' . $limit );
	$total = cerber_db_get_var( 'SELECT FOUND_ROWS()' );

	if ( ! $files ) {
		echo '<p>No files found. Please run the Full Scan.</p>';

		return;
	}

	$title = ( $ext == '.' ) ? __( 'Files without extension', 'wp-cerber' ) : 'Files with the "' . $ext . '" extension';
	echo '<div style="display: table-cell"><h3>' . $title . '</h3></div><div style="display: table-cell; vertical-align: middle;">  &nbsp; <a href="' . cerber_admin_link( 'scan_insights' ) . '#crb_ins_ext_list" class="page-title-action">' . __( 'Back to list', 'wp-cerber' ) . '</a></div>';

	echo crb_make_file_table( $files ) . cerber_page_navi( $total, $per_page );
}

/**
 * @param string $type
 *
 * @return string[]
 *
 * @since 8.6.4
 */
function cerber_generate_insights( $type ) {

	if ( ! $scan_id = crb_scan_last_full() ) {
		return array( 'html' => 'No data for generating reports. Please run the Full Scan. After the scan is completed, the report will be generated.' );
	}

	$key = 'scan_insights_' . $type;

	// Cache
	if ( $report = cerber_get_set( $key, $scan_id, false ) ) {
		return array( 'html' => $report );
	}

	switch ( $type ) {
		case 1:
			$report = crb_scan_insights_brief( $scan_id );
			break;
		case 2:
			$report = crb_scan_insights_lrgst( $scan_id );
			break;
		case 3:
			$report = crb_scan_insights_exts( $scan_id );
			break;
	}

	// Cache
	if ( $report ) {
		cerber_update_set( $key, $report, $scan_id, false, time() + 24 * 3600 );
	}
	else {
		$report = 'ERROR: Unknown report';
	}

	$response = array( 'html' => $report, 'error' => '' );

	/*if ( $_REQUEST['request'] < 2 ) {
		$response['continue'] = 1;
	}*/

	return $response;
}

function crb_scan_insights_brief( $scan_id ) {

	$scan_id = absint( $scan_id );

	$table = cerber_get_db_prefix() . CERBER_SCAN_TABLE;

	$result = '<h3>' . __( 'Brief summary', 'wp-cerber' ) . '</h3>';

	$list = array(
		array( 'WordPress root folder (ABSPATH) ', ABSPATH ),
		array( 'WordPress uploads folder', cerber_get_upload_dir() ),
		array( 'WordPress content folder', dirname( cerber_get_plugins_dir() ) ),
		array( 'WordPress plugins folder', cerber_get_plugins_dir() ),
		array( 'WordPress themes folder', cerber_get_themes_dir() ),
		array( 'WordPress must use plugin folder (WPMU_PLUGIN_DIR) ', WPMU_PLUGIN_DIR ),
		array( 'PHP folder for uploading files', ini_get( 'upload_tmp_dir' ) ),
		array( 'Server folder for temporary files', sys_get_temp_dir() ),
		array( 'Server folder for users\' session data', session_save_path() ),
	);

	/*
	It's not scanned by the scanner
	$cerber_folder = cerber_get_my_folder();
	if ( ! is_wp_error( $cerber_folder ) ) {
		$list[] = array( 'WP Cerber\'s folder', $cerber_folder );
	}*/

	$folders = array();
	foreach ( $list as $item ) {
		if ( ! $item[1] || ! @file_exists( $item[1] ) ) {
			continue;
		}
		$item[2] = 0;
		$item[3] = 0;
		$item[1] = rtrim( $item[1], DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
		if ( $files = cerber_db_get_col( 'SELECT file_size FROM ' . $table . ' WHERE scan_id = ' . $scan_id . ' AND file_name LIKE "' . $item[1] . '%"' ) ) {
			$item[2] = count( $files );
			if ( $sum = array_sum( $files ) ) {
				$item[3] = crb_size_format( $sum );
			}
		}
		$folders[] = $item;
	}

	$sql = 'SELECT file_size FROM ' . $table . ' WHERE scan_id = ' . $scan_id . ' AND file_name NOT LIKE "' . ABSPATH . '%"';
	$files = cerber_db_get_col( $sql );
	if ( $files ) {
		if ( $sum = array_sum( $files ) ) {
			$sum = crb_size_format( $sum );
		}
		$folders[] = array( 'Beyond the WordPress root folder', '', count( $files ), $sum );
	}

	$column = array_column( $folders, 2 );
	array_multisort( $column, SORT_DESC, $folders );

	return $result . cerber_make_table( $folders, array(
			__( 'Folder', 'wp-cerber' ),
			__( 'Path', 'wp-cerber' ),
			__( 'Files', 'wp-cerber' ),
			__( 'Space Occupied', 'wp-cerber' )
		), '', 'crb_align_right crb_align_left_2', 'crb-monospace' );

}

function crb_scan_insights_exts( $scan_id ) {

	$scan_id = absint( $scan_id );

	$table = cerber_get_db_prefix() . CERBER_SCAN_TABLE;
	$list = array();

	$offset = 0;
	$total = 0;
	$done = false;
	//cerber_exec_timer( 15 );

	while ( true ) {

		if ( ! $files = cerber_db_get_results( 'SELECT file_name, file_name_hash, file_mtime, file_size, file_ext FROM ' . $table . ' WHERE scan_id = ' . $scan_id . ' LIMIT ' . CRB_SQL_CHUNK . ' OFFSET ' . $offset ) ) {
			$done = true;
			break;
		}

		$offset += CRB_SQL_CHUNK;

		foreach ( $files as $file ) {
			if ( ! $ext = crb_get_extension( $file['file_name'] ) ) {
				$ext = '.';
			}


			if ( empty( $file['file_ext'] ) ) {
				cerber_db_query( 'UPDATE ' . $table . ' SET file_ext = "' . $ext . '" WHERE scan_id = ' . $scan_id . ' AND file_name_hash = "' . $file['file_name_hash'] . '"' );
			}

			if ( ! isset( $list[ $ext ] ) ) {
				$list[ $ext ] = array( 0, 0, array(), array() );
			}

			$list[ $ext ][0] ++;
			$list[ $ext ][1] += $file['file_size'];

			$list[ $ext ][2][] = $file['file_size'];
			$list[ $ext ][3][] = $file['file_mtime'];

			$total ++;
		}

	}

	if ( ! $done && ( count( $files ) < CRB_SQL_CHUNK ) ) {
		$done = true;
	}

	if ( ! $done ) {
		return array( 'continue' => 1 );
	}

	if ( ! $list ) {
		return 'No data for generating reports. Please run the Full Scan. After the scan is completed, the report will be generated.';
	}

	$base = cerber_admin_link( 'scan_insights' );
	$none = __( 'No extension', 'wp-cerber' );
	$result = array();

	foreach ( $list as $ext => $data ) {
		$text = ( $ext == '.' ) ? $none : $ext;
		$result[] = array(
			'<a href="' . $base . esc_attr( '&fext=' . $ext ) . '">' . $text . '</a>',
			$data[0],
			crb_size_format( $data[1] ),
			crb_size_format( min( $data[2] ) ),
			crb_size_format( max( $data[2] ) ),
			crb_size_format( round( $data[1] / $data[0], 0 ) ),
			cerber_date( min( $data[3] ) ),
			cerber_date( max( $data[3] ) ),
		);
	}

	// Sorting by a column in a multidimensional array

	$column = array_column( $result, 1 );
	array_multisort( $column, SORT_DESC, $result );

	// Create table

	//$report = '<h3>The file system report is based on the last full scan.</h3>';
	$report = '<h3>' . __( 'File extensions statistics', 'wp-cerber' ) . '</h3>';

	$report .= cerber_make_table( $result, array(
		__( 'Extension', 'wp-cerber' ),
		__( 'Files', 'wp-cerber' ),
		__( 'Space Occupied', 'wp-cerber' ),
		__( 'Smallest', 'wp-cerber' ),
		__( 'Largest', 'wp-cerber' ),
		__( 'Average Size', 'wp-cerber' ),
		__( 'Oldest', 'wp-cerber' ),
		__( 'Newest', 'wp-cerber' ),
	), '', 'crb_align_right', 'crb-monospace crb-anchor-decorated' );

	return $report;
}

function crb_scan_insights_lrgst( $scan_id ) {

	$scan_id = absint( $scan_id );

	$table = cerber_get_db_prefix() . CERBER_SCAN_TABLE;

	if ( ! $files = cerber_db_get_results( 'SELECT file_name, file_mtime, file_size FROM ' . $table . ' WHERE scan_id = ' . $scan_id . ' ORDER BY file_size DESC LIMIT 10' ) ) {
		return 'No data for generating reports. Please run the Full Scan. After the scan is completed, the report will be generated.';
	}

	$title = '<h3>' . __( 'Top 10 largest files', 'wp-cerber' ) . '</h3>';

	return $title . crb_make_file_table( $files );
}

function crb_scan_insights_duplicates( $scan_id ) {

	$scan_id = absint( $scan_id );

	// SQL Doesn't work
	/* ( ! $files = cerber_db_get_results( 'SELECT file_name, file_mtime, file_size, file_hash FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan_id . ' GROUP BY file_hash HAVING COUNT(*) > 1 LIMIT 100' ) ) {
		return '';
	}*/

	if ( ! $files = cerber_db_get_col( 'SELECT file_hash FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan_id . ' AND file_size !=0 AND file_hash !="" ' ) ) {
		return 'No data for generating reports. Please run the Full Scan. After the scan is completed, the report will be generated.';
	}

	$dup = array_unique( array_diff_assoc( $files, array_unique( $files ) ) );

	rsort( $dup );

	$list = array();
	foreach ( $dup as $hash ) {
		if ( $fl = cerber_db_get_results( 'SELECT file_name, file_mtime, file_size FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_id = ' . $scan_id . ' AND file_hash = "' . $hash . '"' ) ) {
			$list = array_merge( $list, $fl );
		}
	}

	$result = '<h3>Duplicate files</h3>';

	return $result . crb_make_file_table( $list );

}

function crb_scan_last_full() {

	$scans = cerber_db_get_col( 'SELECT DISTINCT scan_id FROM ' . cerber_get_db_prefix() . CERBER_SCAN_TABLE . ' WHERE scan_mode = 1 ORDER BY scan_id DESC LIMIT 1' );

	if ( $scans ) {
		$scan_id = $scans[0];
		$scan = cerber_get_scan( $scan_id );
		if ( $scan['finished']
		     || ( $scan['aborted'] && $scan['next_step'] > 5 ) ) { // Step 5 is enough for analysis
			return $scan_id;
		}
	}

	return false;
}

// Miscellaneous admin routines ===========================================================


/**
 * Detects file extension
 *
 * @param string $file_name
 *
 * @return string
 */
function crb_get_extension( $file_name ) {

	$name = basename( $file_name );

	if ( $name == '.htaccess' ) {
		return '';
	}

	if ( false === ( $last_dot = strrpos( $name, '.' ) ) ) {
		return '';
	}

	/*$first_dot = strpos( $name, '.' );

	if ( $last_dot !== $first_dot
	     && cerber_detect_exec_extension( $name ) ) {
		$ext = substr( $name, $first_dot + 1 );
	}
	else {
		$ext = substr( $name, $last_dot + 1 );
	}*/

	$ext = substr( $name, $last_dot + 1 );

	if ( ! $ext ) {
		$ext = '';
	}

	return $ext;
}

function crb_make_file_table( $files ) {

	$result = array();
	foreach ( $files as $file ) {
		$result[] = array(
			$file['file_name'],
			crb_size_format( $file['file_size'] ),
			cerber_date( $file['file_mtime'] ),
		);
	}

	return cerber_make_table( $result, array(
		__( 'File Name', 'wp-cerber' ),
		__( 'Size', 'wp-cerber' ),
		__( 'Modified', 'wp-cerber' ),
	), '', 'crb_align_right', 'crb-monospace crb-anchor-decorated' );

}

/**
 * Generates an HTML table
 *
 * @param $heading
 * @param $rows
 * @param string $id
 * @param $class
 * @param string $body_class
 * @param string $head_class
 * @param bool $show_footer
 *
 * @return string
 * @since 8.6.4
 *
 */
function cerber_make_table( $rows, $heading = array(), $id = '', $class = '', $body_class = '', $head_class = '', $show_footer = true ) {

	$tr = array();
	foreach ( $rows as $row ) {
		$tr[] = '<td>' . implode( '</td><td>', $row ) . '</td>';
	}

	$tfoot = '';
	$thead = '';

	if ( $heading ) {
		$titles = '<tr><th>' . implode( '</th><th>', $heading ) . '</th></tr>';
		$thead = '<thead class="' . $head_class . '">' . $titles . '</thead>';

		if ( $show_footer ) {
			$tfoot = '<tfoot class="' . $head_class . '">' . $titles . '</tfoot>';
		}
	}

	$id = ( $id ) ? 'id="' . $id . '"' : '';

	return '<table ' . $id . ' class="widefat crb-table cerber-margin ' . $class . '">' . $thead . $tfoot . '<tbody class="' . $body_class . '"><tr>' . implode( '</tr><tr>', $tr ) . '</tr></tbody></table>';

}

function cerber_get_chmod( $file ) {
	return substr( sprintf( '%o', @fileperms( $file ) ), - 4 );
}

function cerber_get_size( $file ) {
	$size = @filesize( $file );

	return ( is_numeric( $size ) ) ? $size : 0;
}

function cerber_get_date( $file ) {
	$mtime = @filemtime( $file );

	return ( is_numeric( $mtime ) ) ? $mtime : 0;
}

function crb_show_admin_announcement( $text = '', $big = true ) {
	$class = ( $big ) ? 'crb-cerber-logo-big' : 'crb-cerber-logo-small';

	echo '<div class="cerber-msg crb-announcement ' . $class . '"><table><tr><td></td>' .
	     '<td><div>' . $text . '</div></td></tr></table></div>';
}

