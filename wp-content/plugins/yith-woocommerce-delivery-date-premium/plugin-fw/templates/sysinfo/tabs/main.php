<?php
/**
 * The Template for displaying the Main page of the System Information.
 *
 * @package YITH\PluginFramework\Templates\SysInfo
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$system_info = get_option( 'yith_system_info' );
$output_ip   = YITH_System_Status()->get_output_ip();
$labels      = YITH_System_Status()->requirement_labels;
?>
<h2>
	<?php esc_html_e( 'Site Info', 'yith-plugin-fw' ); ?>
</h2>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row">
			<?php esc_html_e( 'Site URL', 'yith-plugin-fw' ); ?>
		</th>
		<td class="info-value">
			<?php echo esc_html( get_site_url() ); ?>
		</td>

	</tr>
	<tr>
		<th scope="row">
			<?php esc_html_e( 'Output IP Address', 'yith-plugin-fw' ); ?>
		</th>
		<td class="info-value">
			<?php echo esc_html( $output_ip ); ?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php esc_html_e( 'Defined WP_CACHE', 'yith-plugin-fw' ); ?>
		</th>
		<td class="info-value">
			<?php echo( defined( 'WP_CACHE' ) && WP_CACHE ? esc_html__( 'Yes', 'yith-plugin-fw' ) : esc_html__( 'No', 'yith-plugin-fw' ) ); ?>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<?php esc_html_e( 'External object cache', 'yith-plugin-fw' ); ?>
		</th>
		<td class="info-value">
			<?php echo( wp_using_ext_object_cache() ? esc_html__( 'Yes', 'yith-plugin-fw' ) : esc_html__( 'No', 'yith-plugin-fw' ) ); ?>
		</td>
	</tr>
</table>

<h2>
	<?php esc_html_e( 'Plugins Requirements', 'yith-plugin-fw' ); ?>
</h2>
<table class="form-table" role="presentation">
	<?php foreach ( $system_info['system_info'] as $key => $item ) : ?>
		<?php
		$has_errors   = isset( $item['errors'] );
		$has_warnings = isset( $item['warnings'] );
		?>
		<tr>
			<th scope="row">
				<?php echo esc_html( $labels[ $key ] ); ?>
			</th>
			<td class="requirement-value <?php echo( $has_errors ? 'has-errors' : '' ); ?> <?php echo( $has_warnings ? 'has-warnings' : '' ); ?>">
				<span class="dashicons dashicons-<?php echo( $has_errors || $has_warnings ? 'warning' : 'yes' ); ?>"></span>
				<?php
				YITH_System_Status()->format_requirement_value( $key, $item['value'] );
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
				?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
