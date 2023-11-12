<?php
/**
 * System Info File.
 *
 * @author Brainstorm Force
 * @package bsf-core
 */

/**
 *  Display system info.
 */
function bsf_systeminfo() {

	?>

	<table class="wp-list-table widefat fixed bsf-sys-info">
		<tbody>
		<tr class="alternate">
			<th colspan="2"><?php esc_html_e( 'WordPress Environment', 'bsf' ); ?></th>
		</tr>
		<tr>
			<td>Home URL</td>
			<td><?php echo esc_url( site_url() ); ?></td>
		</tr>
		<tr>
			<td>Site URL</td>
			<td><?php echo esc_url( site_url() ); ?></td>
		</tr>
		<tr>
			<?php global $wp_version; ?>
			<td>WP Version</td>
			<td><?php echo floatval( $wp_version ); ?></td>
		</tr>
		<tr>
			<td>Multisite</td>
			<td><?php echo ( is_multisite() ) ? 'Yes' : 'No'; ?></td>
		</tr>
		<?php
		$limit = (int) ini_get( 'memory_limit' );
		$usage = function_exists( 'memory_get_usage' ) ? round( memory_get_usage() / 1024 / 1024, 2 ) : 0;
		?>
		<tr>
			<td>Memory Usage</td>
			<td>
				<?php echo floatval( $usage ); ?>
				MB of
				<?php echo intval( $limit ); ?>
				MB
			</td>
		</tr>
		<tr>
			<td>WP Memory Limit</td>
			<td>
				<?php echo intval( WP_MEMORY_LIMIT ); ?>
			</td>
		</tr>
		<tr>
			<td>WP Debug</td>
			<td><?php echo ( WP_DEBUG ) ? 'Enabled' : 'Disabled'; ?></td>
		</tr>
		<tr>
			<td>WP Lang</td>
			<?php $currentlang = get_bloginfo( 'language' ); ?>
			<td><?php echo esc_html( $currentlang ); ?></td>
		</tr>
		<tr>
			<td>WP Uploads Directory</td>
			<td>
				<?php
				$wp_up = wp_upload_dir();
				echo ( is_writable( $wp_up['basedir'] ) ) ? 'Writable' : 'Readable';
				?>
			</td>
		</tr>
		<tr>
			<td>BSF Updater Path</td>
			<td>
				<?php echo '(v' . esc_attr( BSF_UPDATER_VERSION ) . ') ' . esc_attr( BSF_UPDATER_PATH ); ?>
			</td>
		</tr>
		<?php if ( defined( 'WPB_VC_VERSION' ) ) : ?>
			<tr>
				<td>vc_shortcode_output Filter</td>
				<td>
					<?php echo ( has_filter( 'vc_shortcode_output' ) ) ? 'Available' : 'Not Available'; ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php
		$mix           = bsf_get_brainstorm_products( true );
		$temp_constant = '';
		if ( ! empty( $mix ) ) :
			foreach ( $mix as $key => $product ) :
				$constant = strtoupper( str_replace( '-', '_', $product['id'] ) );
				$constant = 'BSF_' . $constant . '_CHECK_UPDATES';
				if ( defined( $constant ) && ( constant( $constant ) === 'false' || constant( $constant ) === false ) ) {
					$temp_constant .= $constant . '<br/>';
					continue;
				}
			endforeach;
		endif;
		if ( defined( 'BSF_CHECK_PRODUCT_UPDATES' ) && false === BSF_CHECK_PRODUCT_UPDATES ) {
			$temp_constant .= 'BSF_CHECK_PRODUCT_UPDATES';
		}
		if ( '' !== $temp_constant ) {
			if ( ! defined( 'BSF_RESTRICTED_UPDATES' ) ) {
				define( 'BSF_RESTRICTED_UPDATES', $temp_constant );
			}
		}
		?>
		<?php if ( defined( 'BSF_RESTRICTED_UPDATES' ) ) : ?>
			<tr>
				<td>Restrited Updates Filter</td>
				<td>
					<?php echo esc_html( BSF_RESTRICTED_UPDATES ); ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
	<table class="wp-list-table widefat fixed bsf-sys-info">
		<tbody>
		<tr class="alternate">
			<th colspan="2"><?php esc_html_e( 'Server Environment', 'bsf' ); ?></th>
		</tr>
		<tr>
			<td>Server Info</td>
			<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
		</tr>
		<tr>
			<td>PHP Version</td>
			<td><?php echo ( function_exists( 'phpversion' ) ) ? floatval( phpversion() ) : 'Not sure'; ?></td>
		</tr>
		<tr>
			<td>MYSQL Version</td>
			<td>
			<?php
				global $wpdb;
				echo floatval( $wpdb->db_version() );
			?>
				</td>
		</tr>
		<tr>
			<td>PHP Post Max Size</td>
			<td><?php echo esc_attr( ini_get( 'post_max_size' ) ); ?></td>
		</tr>
		<tr>
			<td>PHP Max Execution Time</td>
			<td><?php echo esc_attr( ini_get( 'max_execution_time' ) ); ?> Seconds</td>
		</tr>
		<tr>
			<td>PHP Max Input Vars</td>
			<td><?php echo intval( ini_get( 'max_input_vars' ) ); // PHPCS:ignore:PHPCompatibility.IniDirectives.NewIniDirectives.max_input_varsFound ?></td>
		</tr>
		<tr>
			<td>Max Upload Size</td>
			<td><?php echo intval( ini_get( 'upload_max_filesize' ) ); ?></td>
		</tr>
		<tr>
			<td>Default Time Zone</td>
			<td>
				<?php
				if ( date_default_timezone_get() ) {
					echo esc_html( date_default_timezone_get() );
				}
				if ( ini_get( 'date.timezone' ) ) {
					echo ' ' . esc_html( ini_get( 'date.timezone' ) );
				}
				?>
			</td>
		</tr>
		<tr class="<?php echo ( ! function_exists( 'curl_version' ) ) ? 'bsf-alert' : ''; ?>">
			<td>SimpleXML</td>
			<td>
				<?php
				if ( extension_loaded( 'simplexml' ) ) {
					echo 'SimpleXML extension is installed';
				} else {
					echo 'SimpleXML extension is not enabled.';
				}
				?>
			</td>
		</tr>
		<tr class="<?php echo ( ! function_exists( 'curl_version' ) ) ? 'bsf-alert' : ''; ?>">
			<td>cURL</td>
			<td>
				<?php
				if ( function_exists( 'curl_version' ) ) {
					$curl_info = curl_version();
					?>

					<div>Version : <strong><?php echo floatval( $curl_info['version'] ); ?></strong></div>
					<div>SSL Version : <strong><?php echo floatval( $curl_info['ssl_version'] ); ?></strong></div>
					<div>Host : <strong><?php echo esc_html( $curl_info['host'] ); ?></strong></div>

					<?php
				} else {
					echo 'Not Enabled';
				}
				?>
			</td>
		</tr>
		<?php
		$connection    = wp_remote_get( bsf_get_api_site() );
		$support_class = ( is_wp_error( $connection ) || 200 !== wp_remote_retrieve_response_code( $connection ) ) ? 'bsf-alert' : '';

		?>
		<tr class="<?php echo esc_attr( $support_class ); ?>">
			<td>Connection to Support API</td>
			<td>
				<?php
				if ( is_wp_error( $connection ) || 200 !== wp_remote_retrieve_response_code( $connection ) ) {
					echo 'Connection to Support API has error';
					echo '<p class="description">Status Code: ' . esc_attr( wp_remote_retrieve_response_code( $connection ) ) . '</p>';
					echo '<p class="description">Error Message: ' . esc_attr( $connection->get_error_message() ) . '</p>';
				} else {
					echo 'Connecion to Support API was successful';
					echo '<p class="description">Status Code: ' . esc_attr( wp_remote_retrieve_response_code( $connection ) ) . '</p>';
				}
				?>
			</td>
		</tr>
		</tbody>
	</table>
	<table class="wp-list-table widefat fixed bsf-sys-info">
		<tbody>
		<tr class="alternate">
			<th colspan="2"><?php esc_html_e( 'Theme Information', 'bsf' ); ?></th>
		</tr>
		<?php $theme_data = wp_get_theme(); ?>
		<tr>
			<td>Name</td>
			<td><?php echo esc_html( $theme_data->Name ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase ?></td>
		</tr>
		<tr>
			<td>Version</td>
			<td><?php echo floatval( $theme_data->Version ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase ?></td>
		</tr>
		<tr>
			<td>Author</td>
			<td>
				<?php echo wp_kses_post( $theme_data->Author ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase ?>
			</td>
		</tr>
		</tbody>
	</table>
	<table class="wp-list-table widefat fixed bsf-sys-info bsf-table-active-plugin">
		<tbody>
		<tr class="alternate">
			<th colspan="4"><?php esc_html_e( 'Installed Plugins', 'bsf' ); ?></th>
		</tr>
		<?php
		$plugins = get_plugins();
		asort( $plugins );
		foreach ( $plugins as $plugin_file => $plugin_data ) {
			?>
			<tr>
				<td><?php echo esc_html( str_pad( $plugin_data['Title'], 30 ) ); ?></td>
				<td>
					<?php
					if ( is_plugin_active( $plugin_file ) ) {
						echo esc_html( str_pad( 'Active', 10 ) );
					} else {
						echo esc_html( str_pad( 'Inactive', 10 ) );
					}
					?>
				</td>
				<td><?php echo esc_html( str_pad( $plugin_data['Version'], 10 ) ); ?></td>
				<td><?php echo esc_html( $plugin_data['Author'] ); ?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>

	<?php
}

/**
 *  Get BSF systeminfo.
 */
function get_bsf_systeminfo() {
	$table = '<div class="bsf-system-info-wrapper">';
	ob_start();
	bsf_systeminfo();
	$table .= ob_get_clean();
	$table .= '</div>';

	return $table;

}
