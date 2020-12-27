<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* REGISTER PAGE */
add_action( 'admin_menu', 'mpc_register_system_page' );
function mpc_register_system_page() {
	add_submenu_page( 'ma-panel', __( 'System Info', 'mpc' ),  __( 'System Info', 'mpc' ), 'manage_options', 'mpc-panel-system', 'mpc_panel_system' );
}

add_action( 'admin_enqueue_scripts', 'mpc_register_system_page_scripts' );
function mpc_register_system_page_scripts( $hook ) {
	if ( $hook != 'massive-panel_page_mpc-panel-system' ) {
		return;
	}

	wp_enqueue_style( 'mpc-panel-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/mpc-panel.css' );

	wp_enqueue_script( 'mpc-panel-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-panel.js', array( 'jquery', 'underscore' ), MPC_MASSIVE_VERSION, true );
}

function mpc_let_to_num( $size ) {
	$l   = substr( $size, -1 );
	$ret = substr( $size, 0, -1 );
	switch ( strtoupper( $l ) ) {
		case 'P':
			$ret *= 1024;
		case 'T':
			$ret *= 1024;
		case 'G':
			$ret *= 1024;
		case 'M':
			$ret *= 1024;
		case 'K':
			$ret *= 1024;
	}
	return $ret;
}

function mpc_clean( $var ) {
	return sanitize_text_field( $var );
}

add_action( 'wp_ajax_mpc_export_info', 'mpc_ajax_export_info' );
function mpc_ajax_export_info() {
	if ( ! isset( $_GET[ 'system_info' ] ) || ! isset( $_GET[ '_wpnonce' ] ) ) {
		die( '-1' );
	}

	check_ajax_referer( 'mpc-ma-system-info' );

	header('Content-Disposition: attachment; filename="ma_system_info.txt"');

	echo $_GET[ 'system_info' ];

	die();
}

function mpc_panel_system() {
	$status_info = array();

	?>
	<div id="mpc_panel" class="mpc-panel">
		<header class="mpc-panel__header">
			<img class="mpc-panel__logo" src="<?php echo mpc_get_plugin_path( __FILE__ ); ?>/assets/images/logo_dark.png" alt="Logo" width="56" height="56">
			<h1 class="mpc-panel__name">
				<?php _e( 'System Info', 'mpc' ); ?>
			</h1>
		</header>

		<div class="mpc-section mpc-section--wp-env">
			<h2 class="mpc-section__title"><?php _e( 'WordPress Environment', 'mpc' ); ?></h2>
			<?php $status_info[ 'WordPress Environment' ] = '{separator}'; ?>
			<div class="mpc-section__content">
				<table class="mpc-table--status widefat" cellspacing="0">
					<tbody>
					<tr>
						<td><?php _e( 'Home URL', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The URL of your site\'s homepage.', 'mpc' ); ?></span></span>
						</td>
						<td><?php echo $status_info[ 'Home URL' ] = esc_url( home_url() ); ?></td>
					</tr>
					<tr>
						<td><?php _e( 'Site URL', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The root URL of your site.', 'mpc' ); ?></span></span>
						</td>
						<td><?php echo $status_info[ 'Site URL' ] = esc_url( get_site_url() ); ?></td>
					</tr>
					<tr>
						<td><?php _e( 'Massive Addons Version', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The version of Massive Addons installed <br/>on your site.', 'mpc' ); ?></span></span>
						</td>
						<td><?php echo $status_info[ 'MA Version' ] = esc_html( MPC_MASSIVE_VERSION ); ?></td>
					</tr>
					<tr>
						<td><?php _e( 'WP Version', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The version of WordPress installed on your site.', 'mpc' ); ?></span></span>
						</td>
						<td><?php echo $status_info[ 'WP Version' ] = get_bloginfo( 'version' ); ?></td>
					</tr>
					<tr>
						<td><?php _e( 'WP Multisite', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'Whether or not you have <br/>WordPress Multisite enabled.', 'mpc' ); ?></span></span>
						</td>
						<td><?php $status_info[ 'WP Multisite' ] = is_multisite(); echo $status_info[ 'WP Multisite' ] ? '&#x2713;' : '&#x2717;'; ?></td>
					</tr>
					<tr>
						<td><?php _e( 'WP Memory Limit', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The maximum amount of memory (RAM) that <br/>your site can use at one time.', 'mpc' ); ?></span></span>
						</td>
						<td><?php
							$memory = mpc_let_to_num( WP_MEMORY_LIMIT );
							$status_info[ 'WP Memory Limit' ] = size_format( $memory );

							if ( $memory < 67108864 ) {
								echo '<mark class="error">' . sprintf( __( '%s - We recommend setting memory to at least 64MB. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'mpc' ), size_format( $memory ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
							} else {
								echo '<mark class="yes">' . size_format( $memory ) . '</mark>';
							}
							?></td>
					</tr>
					<tr>
						<td><?php _e( 'WP Debug Mode', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'Displays whether or not WordPress <br/>is in Debug Mode.', 'mpc' ); ?></span></span>
						</td>
						<td><?php $status_info[ 'WP Debug Mode' ] = defined( 'WP_DEBUG' ) && WP_DEBUG;  echo $status_info[ 'WP Debug Mode' ] ? '<mark class="yes">&#x2713;</mark>' : '<mark class="no">&#x2717;</mark>'; ?></td>
					</tr>
					<tr>
						<td><?php _e( 'Language', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The current language used by WordPress. <br/>Default = English', 'mpc' ); ?></span></span>
						</td>
						<td><?php echo $status_info[ 'Language' ] = get_locale(); ?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="mpc-section mpc-section--server-env">
			<h2 class="mpc-section__title"><?php _e( 'Server Environment', 'mpc' ); ?></h2>
			<?php $status_info[ 'Server Environment' ] = '{separator}'; ?>
			<div class="mpc-section__content">
				<table class="mpc-table--status widefat" cellspacing="0">
					<tbody>
					<tr>
						<td><?php _e( 'Server Info', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'Information about the web server that <br/>is currently hosting your site.', 'mpc' ); ?></span></span>
						</td>
						<td><?php echo $status_info[ 'Server Info' ] = esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
					</tr>
					<tr>
						<td><?php _e( 'PHP Version', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The version of PHP installed <br/>on your hosting server.', 'mpc' ); ?></span></span>
						</td>
						<td><?php
							// Check if phpversion function exists
							if ( function_exists( 'phpversion' ) ) {
								$php_version = phpversion();
								$status_info[ 'PHP Version' ] = $php_version;

								if ( version_compare( $php_version, '5.4', '<' ) ) {
									echo '<mark class="error">' . __( 'We recommend a minimum PHP version of 5.4' ) . '</mark>';
								} else {
									echo '<mark class="yes">' . esc_html( $php_version ) . '</mark>';
								}
							} else {
								_e( "Couldn't determine PHP version because phpversion() doesn't exist.", 'mpc' );
								$status_info[ 'Server Info' ] = 'undefined';
							}
							?></td>
					</tr>
					<?php if ( function_exists( 'ini_get' ) ) : ?>
						<tr>
							<td><?php _e( 'PHP Post Max Size', 'mpc' ); ?>:
								<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The largest filesize that can be <br/>contained in one post.', 'mpc' ); ?></span></span>
							</td>
							<td><?php echo $status_info[ 'PHP Post Max Size' ] = size_format( mpc_let_to_num( ini_get( 'post_max_size' ) ) ); ?></td>
						</tr>
						<tr>
							<td><?php _e( 'PHP Time Limit', 'mpc' ); ?>:
								<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The amount of time (in seconds) that <br/>your site will spend on a single operation <br/>before timing out (to avoid server lockups)', 'mpc' ); ?></span></span>
							</td>
							<td><?php echo $status_info[ 'PHP Time Limit' ] = ini_get( 'max_execution_time' ); ?></td>
						</tr>
						<tr>
							<td><?php _e( 'PHP Max Input Vars', 'mpc' ); ?>:
								<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The maximum number of variables your <br/>server can use for a single function <br/>to avoid overloads.', 'mpc' ); ?></span></span>
							</td>
							<td><?php echo $status_info[ 'PHP Max Input Vars' ] = ini_get( 'max_input_vars' ); ?></td>
						</tr>
					<?php else: ?>
						<?php $status_info[ 'PHP INI' ] = 'undefined'; ?>
					<?php endif; ?>
					<tr>
						<td><?php _e( 'MySQL Version', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The version of MySQL installed on <br/>your hosting server.', 'mpc' ); ?></span></span>
						</td>
						<td>
							<?php
							/** @global wpdb $wpdb */
							global $wpdb;
							echo $status_info[ 'MySQL Version' ] = $wpdb->db_version();
							?>
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Max Upload Size', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The largest filesize that can be uploaded to <br/>your WordPress installation.', 'mpc' ); ?></span></span>
						</td>
						<td><?php echo $status_info[ 'Max Upload Size' ] = size_format( wp_max_upload_size() ); ?></td>
					</tr>
					<?php
					$posting = array();

					// GZIP
					$posting['gzip']['name'] = 'GZip';
					$posting['gzip']['help'] = __( 'GZip (gzopen) is used to <br/>open the GEOIP database from MaxMind.', 'mpc' );

					if ( is_callable( 'gzopen' ) ) {
						$posting['gzip']['success'] = true;
					} else {
						$posting['gzip']['success'] = false;
						$posting['gzip']['note']    = sprintf( __( 'Your server does not support the <a href="%s">gzopen</a> function - this is required to use the GeoIP database from MaxMind. The API fallback will be used instead for geolocation.', 'mpc' ), 'http://php.net/manual/en/zlib.installation.php' ) . '</mark>';
					}

					$posting['mb']['name'] = 'MB Strings';
					$posting['mb']['help'] = 'Multibyte Strings library for proper encoding.';

					if ( function_exists( 'mb_convert_encoding' ) && function_exists( 'mb_detect_encoding' ) ) {
						$posting['mb']['success'] = true;
					} else {
						$posting['mb']['success'] = false;
						$posting['mb']['note']    = sprintf( __( 'Your server does not support the <a href="%s">MB Strings</a> function - this is required for proper encoding of your website content.', 'mpc' ), 'http://php.net/manual/en/ref.mbstring.php' ) . '</mark>';
					}

					$posting = apply_filters( 'mpc_debug_posting', $posting );

					foreach ( $posting as $post ) {
						$mark = ! empty( $post['success'] ) ? 'yes' : 'error';
						$status_info[ $post['name'] ] = $post['success'];

						?>
						<tr>
							<td><?php echo esc_html( $post['name'] ); ?>:
								<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php echo isset( $post['help'] ) ? $post['help'] : ''; ?></span></span>
							</td>
							<td>
								<mark class="<?php echo $mark; ?>">
									<?php echo ! empty( $post['success'] ) ? '&#x2713;' : '&#x2717;'; ?> <?php echo ! empty( $post['note'] ) ? wp_kses_data( $post['note'] ) : ''; ?>
								</mark>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="mpc-section mpc-section--theme">
			<h2 class="mpc-section__title"><?php _e( 'Theme', 'mpc' ); ?></h2>
			<?php $status_info[ 'Theme' ] = '{separator}'; ?>
			<div class="mpc-section__content">
				<table class="mpc-table--status widefat" cellspacing="0">
					<?php
					include_once( ABSPATH . 'wp-admin/includes/theme-install.php' );

					$active_theme         = wp_get_theme();
					$theme_version        = $active_theme->Version;
					$update_theme_version = $active_theme->Version;
					$api                  = themes_api( 'theme_information', array( 'slug' => get_template(), 'fields' => array( 'sections' => false, 'tags' => false ) ) );

					// Check .org
					if ( $api && ! is_wp_error( $api ) ) {
						if ( is_object( $api ) && isset( $api->version ) ) {
							$update_theme_version = $api->version;
						} elseif ( is_array( $api ) && isset( $api['version'] ) ) {
							$update_theme_version = $api['version'];
						}
					}
					?>
					<tbody>
					<tr>
						<td><?php _e( 'Name', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The name of the current active theme.', 'mpc' ); ?></span></span>
						</td>
						<td><?php echo $status_info[ 'Name' ] = $active_theme->Name; ?></td>
					</tr>
					<tr>
						<td><?php _e( 'Version', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The installed version of the current active theme.', 'mpc' ); ?></span></span>
						</td>
						<td><?php
							echo $status_info[ 'Version' ] = esc_html( $theme_version );

							if ( version_compare( $theme_version, $update_theme_version, '<' ) ) {
								echo ' - <strong style="color:red;">' . sprintf( __( '%s is available', 'mpc' ), esc_html( $update_theme_version ) ) . '</strong>';
							}
							?></td>
					</tr>
					<tr>
						<td><?php _e( 'Author URL', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The theme developers URL.', 'mpc' ); ?></span></span>
						</td>
						<td><?php echo $status_info[ 'Author URL' ] = $active_theme->{'Author URI'}; ?></td>
					</tr>
					<tr>
						<td><?php _e( 'Child Theme', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'Displays whether or not the current theme <br/>is a child theme.', 'mpc' ); ?></span></span>
						</td>
						<td><?php
							$status_info[ 'Child Theme' ] = is_child_theme(); echo $status_info[ 'Child Theme' ] ? '<mark class="yes">&#x2713;</mark>' : '&#x2717; - ' . sprintf( __( 'See: <a href="%s" target="_blank">How to create a child theme</a>', 'mpc' ), 'http://codex.wordpress.org/Child_Themes' );
							?></td>
					</tr>
					<?php
					if( is_child_theme() ) :
						$parent_theme = wp_get_theme( $active_theme->Template );
						?>
						<tr>
							<td><?php _e( 'Parent Theme Name', 'mpc' ); ?>:
								<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The name of the parent theme.', 'mpc' ); ?></span></span>
							</td>
							<td><?php echo $status_info[ 'Parent Theme Name' ] = $parent_theme->Name; ?></td>
						</tr>
						<tr>
							<td><?php _e( 'Parent Theme Version', 'mpc' ); ?>:
								<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The installed version of the parent theme.', 'mpc' ); ?></span></span>
							</td>
							<td><?php echo $status_info[ 'Parent Theme Version' ] = $parent_theme->Version; ?></td>
						</tr>
						<tr>
							<td><?php _e( 'Parent Theme Author URL', 'mpc' ); ?>:
								<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'The parent theme developers URL.', 'mpc' ); ?></span></span>
							</td>
							<td><?php echo $status_info[ 'Parent Theme Author URL' ] = $parent_theme->{'Author URI'}; ?></td>
						</tr>
					<?php endif ?>
					<tr>
						<td><?php _e( 'MA Support', 'mpc' ); ?>:
							<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span><?php _e( 'Displays whether or not the current active<br/> theme declares Massive Addons support.', 'mpc' ); ?></span></span>
						</td>
						<td><?php $status_info[ 'MA Support' ] = current_theme_supports( 'massive-addons' ); echo $status_info[ 'MA Support' ] ? '<mark class="yes">&#x2713;</mark>' : '<mark class="error">' . __( 'Not Declared', 'mpc' ) . '</mark>'; ?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="mpc-section mpc-section--plugins">
			<h2 class="mpc-section__title"><?php _e( 'Active Plugins', 'mpc' ); ?> (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)</h2>
			<?php $status_info[ 'Active Plugins' ] = '{separator}'; ?>
			<div class="mpc-section__content">
				<table class="mpc-table--status mpc-table--plugins widefat" cellspacing="0">
					<tbody>
					<?php
					$active_plugins = (array) get_option( 'active_plugins', array() );

					if ( is_multisite() ) {
						$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
						$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
					}

					foreach ( $active_plugins as $plugin ) {
						$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
						$dirname        = dirname( $plugin );
						$version_string = '';
						$network_string = '';

						if ( ! empty( $plugin_data['Name'] ) ) {

							// link the plugin name to the plugin url if available
							$plugin_name = esc_html( $plugin_data['Name'] );

							if ( ! empty( $plugin_data['PluginURI'] ) ) {
								$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . esc_attr__( 'Visit plugin homepage' , 'mpc' ) . '" target="_blank">' . $plugin_name . '</a>';
							}

							if ( ! empty( $version_data['version'] ) && version_compare( $version_data['version'], $plugin_data['Version'], '>' ) ) {
								$version_string = ' - <strong style="color:red;">' . esc_html( sprintf( _x( '%s is available', 'Version info', 'mpc' ), $version_data['version'] ) ) . '</strong>';
							}

							if ( $plugin_data['Network'] != false ) {
								$network_string = ' - <strong style="color:black;">' . __( 'Network enabled', 'mpc' ) . '</strong>';
							}

							$status_info[ esc_html( $plugin_data[ 'Name' ] ) ] = sprintf( _x( 'by %s', 'by author', 'mpc' ), strip_tags( $plugin_data[ 'Author' ] ) ) . ' - ' . esc_html( $plugin_data[ 'Version' ] ) . $version_string . $network_string;

							?>
							<tr>
								<td><?php echo $plugin_name; ?></td>
								<td><?php echo sprintf( _x( 'by %s', 'by author', 'mpc' ), $plugin_data['Author'] ) . ' - ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?></td>
							</tr>
							<?php
						}
					}
					?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- FOOTER -->
		<footer class="mpc-panel__footer">
			<a href="#show_info" id="mpc_panel__show_info" class="mpc-panel__show_info mpc-panel__primary">
				<span><?php _e( 'Show Info', 'mpc' ); ?></span>
			</a>
			<a href="#show_file" id="mpc_panel__info_file" class="mpc-panel__info_file mpc-panel__primary">
				<span><?php _e( 'Get Info File', 'mpc' ); ?></span>
			</a>
		</footer>

		<!-- SYSTEM INFO -->
		<div id="mpc_panel__system_wrap" class="mpc-status-wrap" style="max-height: 0;">
			<div class="mpc-status-output">
				<label><?php _e( 'Please paste this in your support ticket :)', 'mpc' ) ?><textarea rows="10" readonly><?php
				foreach ( $status_info as $name => $value ) {
					if ( is_bool( $value ) ) {
						$value = $value ? 'true' : 'false';
					}

					if ( $value == '{separator}' ) {
						echo "\n### " . strtoupper( $name ) . " ###\n\n";
					} else {
						echo $name . ": " . $value . PHP_EOL;
					}
				}
				?></textarea></label></div>
		</div>
	</div>

	<?php wp_nonce_field( 'mpc-ma-system-info' );
}