<?php
/**
 * Dashboard template: Support Functions > System Info
 *
 * Displays details about the current WordPress setup.
 *
 * @since   4.0.0
 * @package WPMUDEV_Dashboard
 */

// Collect system details to display.
global $wpdb, $wp_version;

// 1. PHP ---------------------------------------------------------------------
$dump_php = array();
$php_vars = array(
	'max_execution_time',
	'open_basedir',
	'memory_limit',
	'upload_max_filesize',
	'post_max_size',
	'display_errors',
	'log_errors',
	'track_errors',
	'session.auto_start',
	'session.cache_expire',
	'session.cache_limiter',
	'session.cookie_domain',
	'session.cookie_httponly',
	'session.cookie_lifetime',
	'session.cookie_path',
	'session.cookie_secure',
	'session.gc_divisor',
	'session.gc_maxlifetime',
	'session.gc_probability',
	'session.referer_check',
	'session.save_handler',
	'session.save_path',
	'session.serialize_handler',
	'session.use_cookies',
	'session.use_only_cookies',
);

$dump_php['Version'] = phpversion();
foreach ( $php_vars as $setting ) {
	$dump_php[ $setting ] = ini_get( $setting );
}
$dump_php['Error Reporting'] = implode( '<br>', _error_reporting() );
$extensions                  = get_loaded_extensions();
natcasesort( $extensions );
$dump_php['Extensions'] = implode( '<br>', $extensions );

// 2. MySQL -------------------------------------------------------------------
$dump_mysql = array();
$mysql_vars = array(
	'key_buffer_size'    => true,   // Key cache size limit.
	'max_allowed_packet' => false,  // Individual query size limit.
	'max_connections'    => false,  // Max number of client connections.
	'query_cache_limit'  => true,   // Individual query cache size limit.
	'query_cache_size'   => true,   // Total cache size limit.
	'query_cache_type'   => 'ON',   // Query cache on or off.
);
$extra_info = array();
$variables  = $wpdb->get_results( "
	SHOW VARIABLES
	WHERE Variable_name IN ( '" . implode( "', '", array_keys( $mysql_vars ) ) . "' )
" );
$dbh        = $wpdb->dbh;
if ( is_resource( $dbh ) ) {
	$driver = 'mysql';
	// @codingStandardsIgnoreStart: This IS PHP7+ compatible, mysql_get_server_info is included for backwards compatibility
	$version = function_exists( 'mysqli_get_server_info' ) ? mysqli_get_server_info( $dbh ) : mysql_get_server_info( $dbh );
	// @codingStandardsIgnoreEnd
} elseif ( is_object( $dbh ) ) {
	$driver = get_class( $dbh );
	if ( method_exists( $dbh, 'db_version' ) ) {
		$version = $dbh->db_version();
	} elseif ( isset( $dbh->server_info ) ) {
		$version = $dbh->server_info;
	} elseif ( isset( $dbh->server_version ) ) {
		$version = $dbh->server_version;
	} else {
		$version = __( 'Unknown', 'wpmudev' );
	}
	if ( isset( $dbh->client_info ) ) {
		$extra_info['Driver version'] = $dbh->client_info;
	}
	if ( isset( $dbh->host_info ) ) {
		$extra_info['Connection info'] = $dbh->host_info;
	}
} else {
	$version = $driver = __( 'Unknown', 'wpmudev' );
}
$extra_info['Database']     = $wpdb->dbname;
$extra_info['Charset']      = $wpdb->charset;
$extra_info['Collate']      = $wpdb->collate;
$extra_info['Table Prefix'] = $wpdb->prefix;

$dump_mysql['Server Version'] = $version;
$dump_mysql['Driver']         = $driver;
foreach ( $extra_info as $key => $val ) {
	$dump_mysql[ $key ] = $val;
}
foreach ( $mysql_vars as $key => $val ) {
	$dump_mysql[ $key ] = $val;
}
foreach ( $variables as $item ) {
	$dump_mysql[ $item->Variable_name ] = _value_format( $item->Value );
}

// 3. WordPress ---------------------------------------------------------------
$dump_wp                      = array();
$wp_consts                    = array(
	'ABSPATH',
	'WP_CONTENT_DIR',
	'WP_PLUGIN_DIR',
	'WPINC',
	'WP_LANG_DIR',
	'UPLOADBLOGSDIR',
	'UPLOADS',
	'WP_TEMP_DIR',
	'SUNRISE',
	'WP_ALLOW_MULTISITE',
	'MULTISITE',
	'SUBDOMAIN_INSTALL',
	'DOMAIN_CURRENT_SITE',
	'PATH_CURRENT_SITE',
	'SITE_ID_CURRENT_SITE',
	'BLOGID_CURRENT_SITE',
	'BLOG_ID_CURRENT_SITE',
	'COOKIE_DOMAIN',
	'COOKIEPATH',
	'SITECOOKIEPATH',
	'DISABLE_WP_CRON',
	'ALTERNATE_WP_CRON',
	'DISALLOW_FILE_MODS',
	'WP_HTTP_BLOCK_EXTERNAL',
	'WP_ACCESSIBLE_HOSTS',
	'WP_DEBUG',
	'WP_DEBUG_LOG',
	'WP_DEBUG_DISPLAY',
	'ERRORLOGFILE',
	'SCRIPT_DEBUG',
	'WP_LANG',
	'WP_MAX_MEMORY_LIMIT',
	'WP_MEMORY_LIMIT',
	'WPMU_ACCEL_REDIRECT',
	'WPMU_SENDFILE',
);
$dump_wp['WordPress Version'] = $wp_version;
foreach ( $wp_consts as $const ) {
	$dump_wp[ $const ] = _const_format( $const );
}

// 4. Server ------------------------------------------------------------------
$dump_server = array();
$server      = explode( ' ', $_SERVER['SERVER_SOFTWARE'] );
$server      = explode( '/', reset( $server ) );

if ( isset( $server[1] ) ) {
	$server_version = $server[1];
} else {
	$server_version = 'Unknown';
}
$lt = localtime();

$dump_server['Software Name']     = $server[0];
$dump_server['Software Version']  = $server_version;
$dump_server['Server IP']         = @$_SERVER['SERVER_ADDR'];
$dump_server['External IP']       = @$_SERVER['SERVER_ADDR'];
$dump_server['Server Hostname']   = @$_SERVER['SERVER_NAME'];
$dump_server['Server Admin']      = @$_SERVER['SERVER_ADMIN'];
$dump_server['Server local time'] = date( 'Y-m-d H:i:s (\U\T\C P)' );
$dump_server['Operating System']  = @php_uname( 's' );
$dump_server['OS Hostname']       = @php_uname( 'n' );
$dump_server['OS Version']        = @php_uname( 'v' );

// 5. HTTP Requests -----------------------------------------------------------
$dump_http = array();
$options   = array();
if ( WPMUDEV_API_UNCOMPRESSED ) {
	$options['decompress'] = false;
}

$remote_url  = WPMUDEV_Dashboard::$api->get_test_url();
$url         = parse_url( $remote_url );
$remote_get  = wp_remote_get( $remote_url, $options );
$remote_post = wp_remote_post( $remote_url, $options );
$remote_ip   = wp_remote_get( 'https://ipinfo.io/ip', $options );

if ( is_wp_error( $remote_get ) ) {
	$remote_get = $remote_get->get_error_message();
} else {
	$remote_get = wp_remote_retrieve_response_message( $remote_get );
}

if ( is_wp_error( $remote_post ) ) {
	$remote_post = $remote_post->get_error_message();
} else {
	$remote_post = wp_remote_retrieve_response_message( $remote_post );
}

if ( is_wp_error( $remote_ip ) ) {
	$remote_ip = $remote_ip->get_error_message();
} else {
	$remote_ip = wp_remote_retrieve_body( $remote_ip );
}

$dump_http['WPMU DEV API Server'] = $url['scheme'] . '://' . $url['host'];
$dump_http['WPMU DEV: GET']       = $remote_get;
$dump_http['WPMU DEV: POST']      = $remote_post;
$dump_http['External IP']         = $remote_ip;
$dump_server['External IP']       = $remote_ip;

/* -------------------------------------------------------------------------- */
?>

	<div class="sui-tabs sui-tabs-flushed"
		style="border-top: 1px solid #E6E6E6;">

		<div data-tabs="">
			<div class="active" data-index="php"><?php esc_html_e( 'PHP', 'wpmudev' ); ?></div>
			<div data-index="mysql"><?php esc_html_e( 'MySQL', 'wpmudev' ); ?></div>
			<div data-index="wordpress"><?php esc_html_e( 'WordPress', 'wpmudev' ); ?></div>
			<div data-index="server"><?php esc_html_e( 'Server', 'wpmudev' ); ?></div>
			<div data-index="http"><?php esc_html_e( 'HTTP Requests', 'wpmudev' ); ?></div>

			<?php if ( ! empty( $_COOKIE['wpmudev_is_staff'] ) || ! empty( $_GET['staff'] ) ) : // wpcs csrf.ok ?>
				<div data-index="notifications"><?php esc_html_e( 'Notification', 'wpmudev' ); ?></div>
			<?php endif; ?>
		</div>

		<div data-panes="">

			<div class="active" data-index="php">
				<table class="dashui-table">
					<?php foreach ( $dump_php as $item => $value ): ?>
						<tr>
							<th><?php echo esc_html( $item ); ?></td>
							<td><?php echo wp_kses_post( $value ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>

			<div data-index="mysql">
				<table class="dashui-table">
					<?php foreach ( $dump_mysql as $item => $value ): ?>
						<tr>
							<th><?php echo esc_html( $item ); ?></td>
							<td><?php echo wp_kses_post( $value ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>

			<div data-index="wordpress">
				<table class="dashui-table">
					<?php foreach ( $dump_wp as $item => $value ): ?>
						<tr>
							<th><?php echo esc_html( $item ); ?></td>
							<td><?php echo wp_kses_post( $value ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>

			<div data-index="server">
				<table class="dashui-table">
					<?php foreach ( $dump_server as $item => $value ): ?>
						<tr>
							<th><?php echo esc_html( $item ); ?></td>
							<td><?php echo wp_kses_post( $value ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>

			<div data-index="http">
				<table class="dashui-table">
					<?php foreach ( $dump_http as $item => $value ): ?>
						<tr>
							<th><?php echo esc_html( $item ); ?></th>
							<td><?php echo wp_kses_post( $value ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>

			<?php if ( ! empty( $_COOKIE['wpmudev_is_staff'] ) || ! empty( $_GET['staff'] ) ) : // wpcs csrf.ok ?>
				<div data-index="notifications">
					<?php
					// @codingStandardsIgnoreStart: Dump is HTML code, no escaping!
					echo WPMUDEV_Dashboard::$notice->dump_queue();
					// @codingStandardsIgnoreEnd
					?>
				</div>
			<?php endif; ?>

		</div>

	</div>

<?php

/**
 * Helper function.
 *
 * @since  4.0.0
 * @return array
 */
function _error_reporting() {
	$levels          = array();
	$error_reporting = error_reporting();

	$constants = array(
		'E_ERROR',
		'E_WARNING',
		'E_PARSE',
		'E_NOTICE',
		'E_CORE_ERROR',
		'E_CORE_WARNING',
		'E_COMPILE_ERROR',
		'E_COMPILE_WARNING',
		'E_USER_ERROR',
		'E_USER_WARNING',
		'E_USER_NOTICE',
		'E_STRICT',
		'E_RECOVERABLE_ERROR',
		'E_DEPRECATED',
		'E_USER_DEPRECATED',
		'E_ALL',
	);

	foreach ( $constants as $level ) {
		if ( defined( $level ) ) {
			$c = constant( $level );
			if ( $error_reporting & $c ) {
				$levels[ $c ] = $level;
			}
		}
	}

	return $levels;
}

/**
 * Helper function.
 *
 * @since  4.0.0
 *
 * @param  mixed $val Value to format.
 *
 * @return string
 */
function _value_format( $val ) {
	if ( is_numeric( $val ) && ( $val >= ( 1024 * 1024 ) ) ) {
		$val = size_format( $val );
	}

	return $val;
}

/**
 * Helper function.
 *
 * @since  4.0.0
 *
 * @param  string $constant Name of a PHP const.
 *
 * @return string
 */
function _const_format( $constant ) {
	if ( ! defined( $constant ) ) {
		return '<em>undefined</em>';
	}

	$val = constant( $constant );
	if ( ! is_bool( $val ) ) {
		return $val;
	} elseif ( ! $val ) {
		return 'FALSE';
	} else {
		return 'TRUE';
	}
}
