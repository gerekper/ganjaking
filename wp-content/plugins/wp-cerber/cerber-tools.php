<?php
/*
	Copyright (C) 2015-20 CERBER TECH INC., https://cerber.tech
	Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com

    Licenced under the GNU GPL

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

function cerber_show_imex() {
	$form = '<h3>' . __( 'Export settings to the file', 'wp-cerber' ) . '</h3>';
	$form .= '<p>' . __( 'When you click the button below you will get a configuration file, which you can upload on another site.', 'wp-cerber' ) . '</p>';
	$form .= '<p>' . __( 'What do you want to export?', 'wp-cerber' ) . '</p><form action="" method="get">';
	$form .= '<input id="exportset" name="exportset" value="1" type="checkbox" checked> <label for="exportset">' . __( 'Settings', 'wp-cerber' ) . '</label>';
	$form .= '<p><input id="exportacl" name="exportacl" value="1" type="checkbox" checked> <label for="exportacl">' . __( 'Access Lists', 'wp-cerber' ) . '</label>';
	$form .= '<p><input type="submit" name="cerber_export" id="submit" class="button button-primary" value="' . __( 'Download file', 'wp-cerber' ) . '"></form>';

	$nf = wp_nonce_field( 'crb_import', 'crb_field' );

	$form .= '<h3 style="margin-top:2em;">' . __( 'Import settings from the file', 'wp-cerber' ) . '</h3>';
	$form .= '<p>' . __( 'When you click the button below, file will be uploaded and all existing settings will be overridden.', 'wp-cerber' ) . '</p>';
	$form .= '<p>' . __( 'Select file to import.', 'wp-cerber' ) . ' ' . sprintf( __( 'Maximum upload file size: %s.' ), esc_html( size_format( wp_max_upload_size() ) ) );
	$form .= '<form action="" method="post" enctype="multipart/form-data">' . $nf;
	$form .= '<p><input type="file" name="ifile" id="ifile" required="required">';
	$form .= '<p>' . __( 'What do you want to import?', 'wp-cerber' ) . '</p><p><input id="importset" name="importset" value="1" type="checkbox" checked> <label for="importset">' . __( 'Settings', 'wp-cerber' ) . '</label>';
	$form .= '<p><input id="importacl" name="importacl" value="1" type="checkbox" checked> <label for="importacl">' . __( 'Access Lists', 'wp-cerber' ) . '</label>';
	$form .= '<p><input type="submit" name="cerber_import" id="submit" class="button button-primary" value="' . __( 'Upload file', 'wp-cerber' ) . '"></p></form>';

	$form .= '<h3 style="margin-top:2em;">' . __( 'Load the default plugin settings', 'wp-cerber' ) . '</h3>';
	$form .= '<p>' . __( 'When you click the button below, the default WP Cerber settings will be loaded. The Custom login URL and Access Lists will not be changed.', 'wp-cerber' ) . '</p>';
	$form .= '<p>' . __( 'To get the most out of WP Cerber, follow these steps:', 'wp-cerber' ) . ' <a target="_blank" href="https://wpcerber.com/getting-started/">Getting Stared Guide</a></p>';

	$form .= '<p>
				<input type="button" class="button button-primary" value="' . __( 'Load default settings', 'wp-cerber' ) . '" onclick="button_default_settings()" />
				<script type="text/javascript">function button_default_settings(){
		if (confirm("' . __( 'Are you sure?', 'wp-cerber' ) . '")) {
			let click_url = "' . cerber_admin_link_add( array( 'load_settings' => 'default', 'cerber_admin_do' => 'load_defaults' ) ) . '";
			window.location = click_url.replace(/&amp;|&#038;/g,"&");
					}
	}</script>
			</p>';

	$form .= '<h3 style="margin-top:2em;">Bulk load access list entries</h3>';

	$form .= '<form method="post"><input type="hidden" name="acl_text" value="1">' . $nf;
	$form .= '<p><input type="radio" name="target_acl" value="W" checked="checked">Load to ' . __( 'White IP Access List', 'wp-cerber' ) . '</p>';
	$form .= '<p><input type="radio" name="target_acl" value="B">Load to ' . __( 'Black IP Access List', 'wp-cerber' ) . '</p>';
	$form .= '<p><textarea class="crb-monospace" name="import_acl_entries" rows="8" cols="70" placeholder="Enter access list entries, one item per line. To add entry comments, use the CSV format."></textarea></p>';
	$form .= '<p><input type="submit" name="cerber_import" id="submit" class="button button-primary" value="' . __( 'Load entries', 'wp-cerber' ) . '"></p></form>';

	echo $form;
}
/*
	Create export file
*/
function cerber_export() {
	global $wpdb;

	if ( ! cerber_is_http_get() || ! isset( $_GET['cerber_export'] ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Error!' );
	}
	$p    = cerber_plugin_data();
	$data = array( 'cerber_version' => $p['Version'], 'home' => cerber_get_home_url(), 'date' => date( 'd M Y H:i:s' ) );
	if ( ! empty( $_GET['exportset'] ) ) {
		$data ['options']   = crb_get_settings();
		$data ['geo-rules'] = cerber_get_geo_rules();
	}
	if ( ! empty( $_GET['exportacl'] ) ) {
		//$data ['acl'] = cerber_acl_all( 'ip, tag, comments, acl_slice' );
		$data ['acl'] = $wpdb->get_results( 'SELECT ip, tag, comments, acl_slice FROM ' . CERBER_ACL_TABLE, ARRAY_N );
	}
	$file = json_encode( $data );
	$file .= '==/' . strlen( $file ) . '/' . crc32( $file ) . '/EOF';

	crb_file_headers( 'wpcerber.config' );

	echo $file;
	exit;
}

/**
 * Import plugin settings from a file
 *
 */
function cerber_import() {
	global $wpdb, $wp_cerber;

	if ( ! isset( $_POST['cerber_import'] ) || ! cerber_is_http_post() ) {
		return;
	}

	check_admin_referer( 'crb_import', 'crb_field' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Import failed.' );
	}

	// Bulk load ACL
	if ( isset( $_POST['acl_text'] ) ) {
		if ( ! ( $text = crb_get_post_fields( 'import_acl_entries' ) )
		     || ! ( $tag = crb_get_post_fields( 'target_acl', false, 'W|B' ) ) ) {
			cerber_admin_notice( 'No data provided' );

			return;
		}

		$text  = sanitize_textarea_field( $text );
		$list  = explode( PHP_EOL, $text );
		$count = 0;

		foreach ( $list as $line ) {
			if ( ! $line ) {
				continue;
			}

			list( $ip, $comment ) = explode( ',', $line . ',', 3 );
			$ip      = preg_replace( CRB_IP_NET_RANGE, ' ', $ip );
			$ip      = preg_replace( '/\s+/', ' ', $ip );

			if ( ! $ip ) {
				continue;
			}

			if ( $tag == 'B' ) {
				if ( ! cerber_can_be_listed( $ip ) ) {
					cerber_admin_notice( 'Cannot be blacklisted: ' . $ip );

					continue;
				}
			}

			$comment = trim( strip_tags( stripslashes( $comment ) ) );
			$result  = cerber_acl_add( $ip, $tag, $comment );

			if ( $result !== true ) {
				$msg = 'SKIPPED: ' . $ip . ' ' . $comment;
				if ( is_wp_error( $result ) ) {
					$msg .= ' - ' . $result->get_error_message();
				}

				cerber_admin_notice( $msg );
			}
			else {
				$count ++;
			}
		}

		if ( $count ) {
			$msg = $count . ' access list entries were loaded. <a href="' . cerber_admin_link( 'acl' ) . '">Manage access lists</a>.';
		}
		else {
			$msg = 'No entries were loaded';
		}

		cerber_admin_message( $msg );

		return;
	}

	// Import from a file
	$ok = true;
	if ( ! is_uploaded_file( $_FILES['ifile']['tmp_name'] ) ) {
		cerber_admin_notice( __( 'No file was uploaded or file is corrupted', 'wp-cerber' ) );

		return;
	}
    elseif ( $file = file_get_contents( $_FILES['ifile']['tmp_name'] ) ) {
		$p    = strrpos( $file, '==/' );
		$data = substr( $file, 0, $p );
		$sys  = explode( '/', substr( $file, $p ) );
		if ( $sys[3] == 'EOF' && crc32( $data ) == $sys[2] && ( $data = json_decode( $data, true ) ) ) {

			if ( isset( $_POST['importset'] ) && $data['options'] && ! empty( $data['options'] ) && is_array( $data['options'] ) ) {
				$data['options']['loginpath'] = urldecode( $data['options']['loginpath'] ); // needed for filter cerber_sanitize_m()
				if ( $data['home'] != cerber_get_home_url() ) {
					$data['options']['sitekey']   = $wp_cerber->getSettings( 'sitekey' );
					$data['options']['secretkey'] = $wp_cerber->getSettings( 'secretkey' );
				}
				cerber_save_settings( $data['options'] ); // @since 2.0
				if ( isset( $data['geo-rules'] ) ) {
					update_site_option( CERBER_GEO_RULES, $data['geo-rules'] );
				}
				if ( ! empty( $data['options']['crb_role_policies'] ) ) {
					update_site_option( CERBER_SETTINGS, array( 'crb_role_policies' => $data['options']['crb_role_policies'] ) );
				}
			}

			if ( isset( $_POST['importacl'] )
			     && ! empty( $data['acl'] )
			     && is_array( $data['acl'] ) ) {
				$acl_ok = true;
				if ( false === $wpdb->query( "DELETE FROM " . CERBER_ACL_TABLE ) ) {
					$acl_ok = false;
				}
				foreach ( $data['acl'] as $row ) {
					if ( ! cerber_acl_add( $row[0], $row[1], crb_array_get( $row, 2, '' ), crb_array_get( $row, 3, 0 ) ) ) {
						$acl_ok = false;
						break;
					}
				}
				if ( ! $acl_ok ) {
					cerber_admin_notice( __( 'A database error occurred while importing access list entries', 'wp-cerber' ) );
				}

				cerber_acl_fixer();
			}

			cerber_upgrade_settings(); // In case it was settings from an older version

			cerber_admin_message( __( 'Settings has imported successfully from', 'wp-cerber' ) . ' ' . $_FILES['ifile']['name'] );
		}
		else {
			$ok = false;
		}
    }
	if ( ! $ok ) {
		cerber_admin_notice( __( 'Error while parsing file', 'wp-cerber' ) );
	}
}

/**
 * Displays admin diagnostic page
 */
function cerber_show_diag(){
	$sections = array();

	cerber_cache_enable();

	if ( $d = cerber_environment_diag() ) {
		$sections [] = $d;
	}

    ?>

    <form id="diagnostic">

        <?php

        foreach ($sections as $section){
	        echo '<div class="crb-diag-section">';
	        echo '<h3>'.$section[0].'</h3>';
	        echo $section[1];
	        echo '</div>';
        }

        ?>

	    <?php

	    cerber_show_wp_diag();

        $button = '<p style="text-align: right;"><a class="button button-secondary" href="' . wp_nonce_url( add_query_arg( array( 'force_repair_db' => 1 ) ), 'control', 'cerber_nonce' ) . '">Repair Cerber\'s Tables</a></p>';
	    crb_show_diag_section( 'Database Info', cerber_db_diag() . $button );

	    $server = $_SERVER;
	    if ( ! empty( $server['HTTP_COOKIE'] ) ) {
		    unset( $server['HTTP_COOKIE'] );
	    }
	    if ( ! empty( $server['HTTP_X_COOKIES'] ) ) {
		    unset( $server['HTTP_X_COOKIES'] );
	    }
	    ksort( $server );
	    $se = array();
	    foreach ( $server as $key => $value ) {
		    $se[] = array( $key, @strip_tags( $value ) );
	    }

	    crb_show_diag_section( 'Server Environment Variables', cerber_make_plain_table( $se ) );

	    $buttons = '<p style="text-align: right;">
                <a class="button button-secondary" href="' . wp_nonce_url( add_query_arg( array( 'clear_up_the_cache' => 1 ) ), 'control', 'cerber_nonce' ) . '">Clear Up Cache</a>
                <a class="button button-secondary" href="' . wp_nonce_url( add_query_arg( array( 'force_check_nodes' => 1 ) ), 'control', 'cerber_nonce' ) . '">Recheck Status</a>
            </p>';
	    crb_show_diag_section( 'Cerber Security Cloud Status', lab_status() . $buttons );

	    crb_show_diag_section( 'Maintenance Tasks', cerber_cron_diag() );

	    if ( $report = get_site_option( '_cerber_report' ) ) {
		    $rep = cerber_ago_time( $report[0] ) . ' (' . cerber_date( $report[0] ) . ')';
		    if ($report[1]) {
			    $rep .= ' OK | '.get_site_transient( 'crb_hourly_2' );
		    }
		    else {
			    $rep .= ' Unable to send email';
		    }

		    crb_show_diag_section( 'Weekly Reports', $rep );
	    }

	    if ( $subs = get_site_option( '_cerber_subs' ) ) {

		    $rep = '<ol>';
		    foreach ( $subs as $hash => $sub ) {
			    $rep .= '<li>' . $hash . ' | <a href = "' . cerber_admin_link( 'activity' ) . '&amp;unsubscribeme=' . $hash . '">' . __( 'Unsubscribe', 'wp-cerber' ) . '</a></li>';
		    }
		    $rep .= '</ol>';
		    $rep .= '<p><a target="_blank" href="https://wpcerber.com/wordpress-notifications-made-easy/">Read more on alerts and notifications</a></p>';

		    crb_show_diag_section( 'Alerts', $rep );
	    }

	    if ( $status = CRB_DS::get_status() ) {
		    crb_show_diag_section( 'Data Shield Status', '<ul><li>' . implode( '</il><li>', $status ) . '</li></ul>' );
	    }

	    ?>
    </form>
    <script type="text/javascript">
        function toggle_visibility(id) {
            var e = document.getElementById(id);
            if(e.style.display === 'block')
                e.style.display = 'none';
            else
                e.style.display = 'block';
        }
    </script>
	<?php
}

function crb_show_diag_section( $title, $content ) {
	echo '<div class="crb-diag-section"><h3>' . $title . '</h3><div class="crb-diag-inner">' . $content . '</div></div>';
}

function cerber_show_lic() {
	$key = lab_get_key();
	$valid = '';
	if ( ! empty( $key[2] ) ) {
		$lic = $key[2];
		if ( lab_validate_lic( $lic, $expires ) ) {
			$valid = '
                <p><span style="color: green;">This key is valid until ' . $expires . '</span></p>
                <p>To move the key to another website or web server, please follow these steps: <a href="https://my.wpcerber.com/how-to-move-license-key/" target="_blank">https://my.wpcerber.com/how-to-move-license-key/</a></p>';
		}
		else {
			$valid = '<p><span style="color: red;">This license key is invalid or expired ' . $expires . '</span></p>
			<p>If you believe this key is valid, please follow these steps: <a href="https://my.wpcerber.com/how-to-fix-invalid-or-expired-key/" target="_blank">https://my.wpcerber.com/how-to-fix-invalid-or-expired-key/</a></p>';
		}
	}
	else {
		$lic = '';
	}
	?>
    <form method="post">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">License key for the PRO version</th>
                <td>
                    <input name="cerber_license" value="<?php echo $lic; ?>" size="<?php echo LAB_KEY_LENGTH; ?>" maxlength="<?php echo LAB_KEY_LENGTH; ?>" type="text" class="crb-monospace" placeholder="Enter the license key here">
                    <?php echo $valid; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">Site ID</th>
                <td>
		            <?php echo '<p class="crb-monospace">'.$key[0].'</p>'; ?>
                </td>
            </tr>
            <tbody>
        </table>
        <div style="padding-left: 220px">
            <input type="hidden" name="cerber_admin_do" value="install_key">
			<?php
			cerber_nonce_field( 'control', true );
            submit_button();
            ?>
        </div>
    </form>
	<?php
}

function cerber_show_wp_diag(){
	global $wpdb;

	$tz = date_default_timezone_get();
	$tz = ( $tz !== 'UTC' ) ? '<span style="color: red;">' . $tz . '!</span>' : $tz;

	if ( $c = CRB_Cache::checker() ) {
		$c = 'Yes | ' . cerber_date( $c ) . ' | ' . cerber_ago_time( $c );

		if ( $stat = CRB_Cache::get_stat( true ) ) {
			$c .= ' | Cerber\'s entries: ' . count( $stat[1] );
			$c .= ' | '.crb_confirmation_link( cerber_admin_link_add( array(
					'cerber_admin_do' => 'clear_cache',
				) ), 'Clear the cache' );
		}
	}
	else {
		$c = 'Not detected';
	}

	$sys = array(
		array( 'Web Server', $_SERVER['SERVER_SOFTWARE'] ),
		array( 'PHP version', phpversion() ),
		array( 'Server API', php_sapi_name() ),
		array( 'Server platform', PHP_OS ),
		array( 'Memory limit', @ini_get( 'memory_limit' ) ),
		array( 'Default PHP timezone', $tz ),
		array( 'Disabled PHP functions', @ini_get( 'disable_functions' ) ),
		array( 'WordPress version', cerber_get_wp_version() ),
		array( 'WordPress locale', get_locale() ),
		array( 'WordPress options DB table', $wpdb->prefix . 'options' ),
		array( 'MySQLi', ( function_exists( 'mysqli_connect' ) ) ? '<span style="color: green;">YES</span>' : '<span style="color: red;">NO</span>' ),
		array( 'MySQL Native Driver (mysqlnd)', ( function_exists( 'mysqli_fetch_all' ) ) ? '<span style="color: green;">YES</span>' : 'NO' ),
		array( 'PHP allow_url_fopen', ( ini_get( 'allow_url_fopen' ) ) ? '<span style="color: red;">Enabled</span>' : '<span style="color: green;">Disabled</span>' ),
		array( 'PHP allow_url_include', ( ini_get( 'allow_url_include' ) ) ? '<span style="color: red;">Enabled</span>' : '<span style="color: green;">Disabled</span>' ),
		array( 'Persistent object cache', $c ),
	);

	if ( 2 < substr_count( cerber_get_site_url(), '/' ) ) {
		$sys[] = array( 'Subfolder WP installation', 'YES' );
		$sys[] = array( 'Site URL', cerber_get_site_url() );
		$sys[] = array( 'Home URL', cerber_get_home_url() );
	}

	if ( nexus_is_valid_request() ) {
		$sys[] = array( 'The IP address of the master is detected as', cerber_get_remote_ip() );
	}
	else {
		$sys[] = array( 'Your IP address is detected as', cerber_get_remote_ip() . ' (check it on the <a href="https://wpcerber.com/what-is-my-ip/" target="_blank">What Is My IP Address</a> page)' );
	}

	crb_show_diag_section( 'System Info', cerber_make_plain_table( $sys ) );

	$folder = cerber_get_my_folder();
	if ( is_wp_error( $folder ) ) {
		$folder = $folder->get_error_message();
	}
	else {
		$folder .= 'quarantine' . DIRECTORY_SEPARATOR;
	}

	if ( file_exists( ABSPATH . 'wp-config.php' )) {
		$config = ABSPATH . 'wp-config.php';
	}
    elseif ( file_exists( dirname( ABSPATH ) . '/wp-config.php' ) ) {
		$config = dirname( ABSPATH ) . '/wp-config.php';
	}
	else {
		$config = 'None?';
	}

	$folders = array(
		array( 'WordPress root folder (ABSPATH) ', ABSPATH ),
		array( 'WordPress uploads folder', cerber_get_upload_dir() ),
		array( 'WordPress content folder', dirname( cerber_get_plugins_dir() ) ),
		array( 'WordPress plugins folder', cerber_get_plugins_dir() ),
		array( 'WordPress themes folder', cerber_get_themes_dir() ),
		array( 'WordPress must use plugin folder (WPMU_PLUGIN_DIR) ', WPMU_PLUGIN_DIR ),
		array( 'WordPress config file', $config ),
		array( 'PHP folder for uploading files', ini_get( 'upload_tmp_dir' ) ),
		array( 'Server folder for temporary files', sys_get_temp_dir() ),
		array( 'Server folder for user session data', session_save_path() ),
		array( 'WP Cerber\'s quarantine folder', $folder ),
		array( 'WP Cerber\'s diagnostic log', cerber_get_diag_log() )
	);

	//$folders[] = array( 'WordPress config file', $config );

	if ( file_exists( ABSPATH . '.htaccess' ) ) {
		$folders[] = array( 'Main .htaccess file', ABSPATH . '.htaccess' );
	}

	foreach ( $folders as &$folder ) {
		$folder[2] = '';
		$folder[3] = '';
		if ( @file_exists( $folder[1] ) ) {
			if ( wp_is_writable( $folder[1] ) ) {
				$folder[2] = 'Writable';
			}
			else {
				$folder[2] = 'Write protected';
			}
			$folder[3] = cerber_get_chmod( $folder[1] );
		}
		else {
			$folder[2] = 'Not found (no access)';
		}
	}


	$folders[] = array( 'Directory separator', DIRECTORY_SEPARATOR );

	crb_show_diag_section( 'File system', cerber_make_plain_table( $folders ) );

	if ( is_multisite() ) {
		$mu = array();
		if ( defined( 'UPLOADS' ) ) {
			$mu[] = array( 'UPLOADS', UPLOADS );
		}
		if ( defined( 'BLOGUPLOADDIR' ) ) {
			$mu[] = array( 'BLOGUPLOADDIR', BLOGUPLOADDIR );
		}
		if ( defined( 'UPLOADBLOGSDIR' ) ) {
			$mu[] = array( 'UPLOADBLOGSDIR', UPLOADBLOGSDIR );
		}

		$mu[] = array( 'Uploads folder for sites', cerber_get_upload_dir_mu() );

		crb_show_diag_section( 'Multisite Constants', cerber_make_plain_table( $mu ) );
	}

	$pls = array();
	$list = get_option('active_plugins');
	foreach($list as $plugin) {
		$data = get_plugin_data(WP_PLUGIN_DIR.'/'.$plugin);
		$pls[] = array($data['Name'], $data['Version']);
	}

	crb_show_diag_section( 'Active Plugins', cerber_make_plain_table( $pls ) );
}

function cerber_make_plain_table( $data, $header = null, $first_header = false, $eq = false ) {
	$class = 'crb-monospace ';
	if ( $first_header ) {
		$class .= ' crb-plain-fh ';
	}
	if ( ! $eq ) {
		$class .= ' crb-plain-fcw ';
	}
	$ret = '<div class="crb-plain-table"><table class="' . $class . '">';
	if ( $header ) {
		$ret .= '<tr class="crb-plain-header"><td>' . implode( '</td><td>', $header ) . '</td></tr>';
	}
	foreach ( $data as $row ) {
		$ret .= '<tr><td>' . implode( '</td><td>', $row ) . '</td></tr>';
	}
	$ret .= '</table></div>';

	return $ret;
}

/*
 * Create database diagnostic report
 *
 *
 */
function cerber_db_diag(){
    global $wpdb;

	$ret = array();

	$db_info = array();

	$db_info[] = array( 'Database name', DB_NAME );

	$var       = crb_get_mysql_var( 'innodb_buffer_pool_size' );
	$pool_size = round( $var / 1048576 );
	$inno      = $pool_size . ' MB';
	if ( $pool_size < 16 ) {
		$inno .= ' Your pool size is extremely small!';
	}
    elseif ( $pool_size < 64 ) {
		$inno .= ' It seems your pool size is too small.';
	}
	$db_info[] = array( 'InnoDB buffer pool size', $inno );

	$var   = crb_get_mysql_var( 'max_allowed_packet' );
	$db_info[] = array( 'Max allowed packet size', round( $var / 1048576 ) . ' MB' );

	$db_info[] = array( 'Charset', $wpdb->charset );
	$db_info[] = array( 'Collate', $wpdb->collate );

	$ret[] = cerber_make_plain_table($db_info);

	$ret[] = cerber_table_info( CERBER_LOG_TABLE );
	$ret[] = cerber_table_info( CERBER_ACL_TABLE );
	$ret[] = cerber_table_info( CERBER_BLOCKS_TABLE );
	$ret[] = cerber_table_info( CERBER_TRAF_TABLE );

	if ( cerber_get_remote_ip() === CERBER_NO_REMOTE_IP ) {
		$ret[] = '<p style="color: #DF0000;">It seems that we are unable to get IP addresses.</p>';
	}

	$err = '';
	if ( $errors = get_site_option( '_cerber_db_errors' ) ) {
		$err = '<p style="color: #DF0000;">Some minor DB errors were detected</p><textarea>' . print_r( $errors, 1 ) . '</textarea>';
		update_site_option( '_cerber_db_errors', '' );
	}

	return $err . implode( '<br />', $ret );
}

/**
 * Creates mini report about given database table
 *
 * @param $table
 *
 * @return string
 */
function cerber_table_info( $table ) {
	global $wpdb;
	if (!cerber_is_table($table)){
		return '<p style="color: #DF0000;">ERROR. Database table ' . $table . ' not found! Click repair button below.</p>';
	}
	$cols = $wpdb->get_results( "SHOW FULL COLUMNS FROM " . $table );

	$tb = array();
	//$columns    = '<table><tr><th style="width: 30%">Field</th><th style="width: 30%">Type</th><th style="width: 30%">Collation</th></tr>';
	foreach ( $cols as $column ) {
		$column    = obj_to_arr_deep( $column );
		$field     = array_shift( $column );
		$type      = array_shift( $column );
		$collation = array_shift( $column );
		$tb[] = array( $field, $type, $collation );

		//$columns  .= '<tr><td><b>' . $field . '</b></td><td>' . $type . '</td><td>' . $collation . '</td></tr>';
	}
	//$columns .= '</table>';

	$columns = cerber_make_plain_table( $tb, array( 'Field', 'Type', 'Collation' ) );

	$rows = absint( cerber_db_get_var( 'SELECT COUNT(*) FROM ' . $table ) );

	$sts = $wpdb->get_row( 'SHOW TABLE STATUS WHERE NAME = "' . $table .'"');
	$tb = array();
	foreach ( $sts as $key => $value ) {
		$tb[] = array( $key, $value );
	}
	$status = cerber_make_plain_table( $tb, null, true );

	$truncate = '';
	if ($rows) {
		$truncate = ' <a href="'.wp_nonce_url( add_query_arg( array( 'truncate' => $table ) ), 'control', 'cerber_nonce' ).'" class="crb-button-tiny" onclick="return confirm(\'Confirm emptying the table. It cannot be rolled back.\')">Delete all rows</a>';
	}

	return '<p style="font-size: 110%;">Table: <b>' . $table . '</b>, rows: ' . $rows . $truncate. '</p><table class="diag-table"><tr><td class="diag-td">' . $columns . '</td><td class="diag-td">'. $status.'</td></tr></table>';
}


function cerber_environment_diag() {
	$issues = array();
	if ( version_compare( '7.0', phpversion(), '>' ) ) {
		$issues[] = 'Your site run on an outdated (unsupported) version of PHP which is ' . phpversion() . '. We strongly encourage you to upgrade PHP to a newer version. See more at: <a target="_blank" href="http://php.net/supported-versions.php">http://php.net/supported-versions.php</a>';
	}
	if ( ! function_exists( 'http_response_code' ) ) {
		$issues[] = 'The PHP function http_response_code() is not found or disabled.';
	}
	if ( ! function_exists( 'mb_convert_encoding' ) ) {
		$issues[] = 'A PHP extension <b>mbstring</b> is not enabled on this website. Some plugin features will not work properly. 
			You need to enable the PHP mbstring extension (multibyte strings support) in your hosting control panel.';
	}
	if ( ! is_numeric( $_SERVER['REQUEST_TIME_FLOAT'] ) ) {
		$issues[] = 'The server environment variable $_SERVER[\'REQUEST_TIME_FLOAT\'] is not set correctly.';
	}

	$ret = null;
	if ( $issues ) {
		$issues = '<p>' . implode( '</p><p>', $issues ) . '</p>';
		$ret = array(
			'<h3><span style="color: red;" class="dashicons dashicons-warning"></span> Some issues detected. They might affect plugin functionality.</h3>',
			$issues
		);
	}

	return $ret;
}

function cerber_cron_diag() {

	$planned = array();
	$crb_crons = array(
		'cerber_hourly_1' => 'Hourly task #1',
		'cerber_hourly_2' => 'Hourly task #2',
		'cerber_daily'    => 'Daily task',
		//'cerber_bg_launcher' => 'Background tasks'
	);
	foreach ( _get_cron_array() as $time => $item ) {
		foreach ( $crb_crons as $key => $val ) {
			if ( ! empty( $item[ $key ] ) ) {
				$planned[ $key ] = $val . ' scheduled for ' . cerber_date( $time ) . ' (' . cerber_ago_time( $time ) . ')';
			}
		}
	}

	unset( $crb_crons['cerber_daily'] );
	$crb_crons['cerber_daily_1'] = 'Daily task';

	$errors = array();
	$ok = array();
	$no_cron = false;
	foreach ( $crb_crons as $key => $task ) {
		$h = get_site_transient( $key );
		if ( ! $h || ! is_array( $h ) ) {
			$errors[] = $task . ' has never been executed';
			if ( $oldest = cerber_db_get_var( 'SELECT MIN(stamp) FROM ' . CERBER_LOG_TABLE ) ) {
				if ( $oldest < ( time() - 24 * 3600 ) ) {
					$no_cron = true;
				}
			}
			continue;
		}
		if ( empty( $h[1] ) ) {
			$errors[] = $task . ' has not finished correctly';
			continue;
		}
		$end = $h[1];
		/*
		if ( $end < ( time() - 2 * 3600 ) ) {
			$errors[] = $val . ' has been executed ' . cerber_ago_time( $end );
		}
		else {
			$ok[] = $val . ' has been executed ' . cerber_ago_time( $end );
		}
		*/
		$dur = $end - $h[0];
		if ( $dur > 60 ) {
			$errors[] = $task . ' has been executed ' . cerber_ago_time( $end ) . ' and it took ' . $dur . ' seconds.';
		}
		else {
			$ok[] = $task . ' has been executed ' . cerber_ago_time( $end ) . ' and it took ' . $dur . ' seconds.';
		}
	}

	$ret = '';

	if ( $errors ) {
		$ret .= '<p style="color: red;">' . implode( '<br/>', $errors ) . '</p>';
	}
	if ( $ok ) {
		$ret .= '<p>' . implode( '<br/>', $ok ) . '</p>';
	}
	if ( $planned ) {
		$ret .= '<p>' . implode( '<br/>', $planned ) . '</p>';
	}

	$num = 0;
	if ( $bg = cerber_bg_task_get_all() ) {
		$num = count( $bg );
	}
	$ret .= '<p>Background tasks: ' . $num . '</p>';

	if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
		$ret .= '<p>Note: the internal WordPress cron launcher is disabled on this site.</p>';
		if ( $no_cron ) {
			$ret .= '<p>An external cron launcher has not been configured or does not work properly.</p>';
		}
	}

	return $ret;
}

function cerber_show_diag_log() {
	$file = cerber_get_diag_log();
	if ( ! is_file( $file ) ) {
		echo '<p>The log file has not been created yet.</p>';

		return;
	}
	if ( ! filesize( $file ) ) {
		echo '<p>The diagnostic log file is empty.</p>';

		return;
	}

	$reverse_log = crb_get_query_params( 'reverse_log', '\d' );

	$clear = crb_confirmation_link( cerber_admin_link_add( array(
		'cerber_admin_do' => 'manage_diag_log',
		'do_this'         => 'clear_it',
	) ), 'Clear the log' );

	$dnl = '<a href="' . cerber_admin_link_add( array(
			'cerber_admin_do' => 'export',
			'type'            => 'get_diag_log',
		) ) . '">Download as a file</a>';

	$reverse = '<a href="' . cerber_admin_link_add( array(
			'reverse_log' => ( $reverse_log ) ? 0 : 1,
		), false ) . '">Reverse the order</a>';

	$nav     = '<div style="text-align: right; padding-bottom: 1em;">' . $reverse . ' | ' . $dnl . ' | ' . $clear . '</div>';

	if ( empty( $reverse_log ) ) {
		$log  = @fopen( $file, 'r' );
		$text = fread( $log, 10000000 );
		if ( ! $text ) {
			return;
		}
		fclose( $log );
		/*$p    = strpos( $text, PHP_EOL );
		$text = substr( $text, $p + 1 );*/
		echo $nav;
		echo '<div id="crb-log-viewer"><pre>' . nl2br( htmlentities( $text ) ) . '</pre></div>';
	}
	else {
		$lines = file( $file );
		if ( ! $lines ) {
			return;
		}
		echo $nav;
		echo '<div id="crb-log-viewer"><pre>';
		for ( $i = count( $lines ) - 1; $i >= 0; $i -- ) {
			echo htmlentities( $lines[ $i ] ) . '<br/>';
		}
		echo '</pre></div>';
	}

}

function cerber_manage_diag_log( $v ) {
	if ( $v == 'clear_it' ) {
		cerber_truncate_log( 0 );
	}
    elseif ( $v == 'download' ) {
		crb_file_headers( 'wpcerber.log' );
		readfile( cerber_get_diag_log() );
		exit;
	}
}

function cerber_show_change_log() {

	echo '<div id="crb-change-log-view" class="">';

	if ( ! $log = cerber_parse_change_log() ) {
		echo 'File changelog.txt not found';
	}

	echo implode( '<br/>', $log );

	echo '</div>';
}
