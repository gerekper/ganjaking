<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$system_info = get_option( 'yith_system_info' );
$saved_ip    = get_transient( 'yith-sysinfo-ip' );
$output_ip   = ( '' === (string) $saved_ip ? 'n/a' : $saved_ip );
$labels      = YITH_System_Status()->_requirement_labels;

if ( 'n/a' === $output_ip && function_exists( 'curl_init' ) && apply_filters( 'yith_system_status_check_ip', true ) ) {
	//Get Output IP Address
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, 'https://ifconfig.co/ip' );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$data = curl_exec( $ch );
	curl_close( $ch );

	//CHECK IF IS IPv4
	preg_match( '/((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])/', $data, $matches );
	//CHECK IF IS IPv6
	if ( empty( $matches ) ) {
		preg_match( '/(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/', $data, $matches );
	}
	$output_ip = ! empty( $matches ) ? $matches[0] : 'n/a';

	set_transient( 'yith-sysinfo-ip', $output_ip, 300 );
}
?>
<div id="yith-sysinfo" class="wrap yith-system-info yith-plugin-ui">
	<h2 class="yith-sysinfo-title">
		<span class="yith-logo"><img src="<?php echo yith_plugin_fw_get_default_logo(); ?>" /></span> <?php _e( 'YITH System Information', 'yith-plugin-fw' ); ?>
	</h2>
	<?php

	$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

	switch ( $tab ) {
		case 'error-log':
			$debug_files = array(
				//debug.log file
				'debug.log' => array(
					'label' => esc_html__( 'WP debug.log file', 'yith-plugin-fw' ),
					'path'  => WP_CONTENT_DIR . '/debug.log',
				),
				'error_log' => array(
					'label' => esc_html__( 'PHP error_log file', 'yith-plugin-fw' ),
					'path'  => ABSPATH . 'error_log',
				),
			);
			?>
			<a href="<?php echo add_query_arg( array( 'tab' => 'main' ) ); ?> "><?php esc_html_e( 'Back to System panel', 'yith-plugin-fw' ); ?></a>
			<table class="widefat striped">
				<?php
				foreach ( $debug_files as $debug_file ) :

					if ( ! file_exists( $debug_file['path'] ) ) {
						continue;
					}

					?>
					<tr>
						<th>
							<?php echo $debug_file['label']; ?>
						</th>
						<td>
							<textarea class="yith-system-info-debug" readonly> <?php include $debug_file['path']; ?></textarea>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php
			break;
		case 'php-info':
			?>
			<a href="<?php echo add_query_arg( array( 'tab' => 'main' ) ); ?> "><?php esc_html_e( 'Back to System panel', 'yith-plugin-fw' ); ?></a>
			<?php
			ob_start();
			phpinfo( 61 );
			$pinfo = ob_get_contents();
			ob_end_clean();

			$pinfo = preg_replace( '%^.*<div class="center">(.*)</div>.*$%ms', '$1', $pinfo );
			$pinfo = preg_replace( '%(^.*)<a name=\".*\">(.*)</a>(.*$)%m', '$1$2$3', $pinfo );
			$pinfo = str_replace( '<table>', '<table class="widefat striped yith-phpinfo">', $pinfo );
			$pinfo = str_replace( '<td class="e">', '<th class="e">', $pinfo );
			echo $pinfo;
			?>
			<a href="#yith-sysinfo"><?php esc_html_e( 'Back to top', 'yith-plugin-fw' ); ?></a>
			<?php
			break;
		default:
			?>
			<table class="widefat striped">
				<tr>
					<th>
						<?php esc_html_e( 'Site URL', 'yith-plugin-fw' ); ?>
					</th>
					<td class="requirement-value">
						<?php echo get_site_url(); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Output IP Address', 'yith-plugin-fw' ); ?>
					</th>
					<td class="requirement-value">
						<?php echo $output_ip; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Defined WP_CACHE', 'yith-plugin-fw' ); ?>
					</th>
					<td class="requirement-value">
						<?php echo( defined( 'WP_CACHE' ) && WP_CACHE ? esc_html__( 'Yes', 'yith-plugin-fw' ) : esc_html__( 'No', 'yith-plugin-fw' ) ); ?>
					</td>
				</tr>
			</table>

			<table class="widefat striped">
				<?php foreach ( $system_info['system_info'] as $key => $item ) : ?>
					<?php
					$has_errors   = isset( $item['errors'] );
					$has_warnings = isset( $item['warnings'] );
					?>
					<tr>
						<th class="requirement-name">
							<?php echo $labels[ $key ]; ?>
						</th>
						<td class="requirement-value <?php echo( $has_errors ? 'has-errors' : '' ); ?> <?php echo( $has_warnings ? 'has-warnings' : '' ); ?>">
							<span class="dashicons dashicons-<?php echo( $has_errors || $has_warnings ? 'warning' : 'yes' ); ?>"></span>
							<?php
							echo YITH_System_Status()->format_requirement_value( $key, $item['value'] );
							?>
						</td>
						<td class="requirement-messages">
							<?php
							if ( $has_errors ) {
								YITH_System_Status()->print_error_messages( $key, $item, $labels[ $key ] );
								YITH_System_Status()->print_solution_suggestion( $key, $item, $labels[ $key ] );
							} elseif ( $has_warnings ) {
								YITH_System_Status()->print_warning_messages( $key );
							}

							if ( 'min_php_version' === $key ) {

								if ( $has_errors || $has_warnings ) {
									echo '<br />';
								}
								?>
								<a href="<?php echo add_query_arg( array( 'tab' => 'php-info' ) ); ?> "><?php esc_html_e( 'Show full PHPInfo', 'yith-plugin-fw' ); ?></a>
								<?php
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<a href="<?php echo add_query_arg( array( 'tab' => 'error-log' ) ); ?> "><?php esc_html_e( 'Show log files', 'yith-plugin-fw' ); ?></a>

			<?php
			break;
	}

	?>
</div>
