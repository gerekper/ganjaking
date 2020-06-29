<?php
/**
 * Dashboard popup template: Info on last update-check
 *
 * Will output a single line of text that displays the last update time and
 * a link to check again.
 *
 * Following variables are passed into the template:
 *   - (none)
 *
 * @since   4.0.0
 * @package WPMUDEV_Dashboard
 */

$url_check  = add_query_arg( 'action', 'check-updates' );
$last_check = WPMUDEV_Dashboard::$site->get_option( 'last_run_updates' );

if ( isset( $_GET['success-action'] ) ) { // wpcs csrf ok. ?>

	<?php switch ( $_GET['success-action'] ) { // wpcs csrf ok.

		case 'check-updates': ?>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss">
				<div class="sui-notice-content">
					<p><?php esc_html_e( 'Data successfully updated.', 'wpmudev' ); ?></p>
				</div>
				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>
			</div>

			<?php
			break;

		default:
			break;
	}
}

if ( $last_check ) { ?>

	<p class="dashui-note-refresh refresh-infos">
		<?php
		printf(
			esc_html( _x( 'We last checked for updates %1$s ago %2$sCheck again%3$s', 'Placeholders: time-ago, link-open, link-close', 'wpmudev' ) ),
			'<strong>' . esc_html( human_time_diff( $last_check ) ) . '</strong>',
			' - <a href="' . esc_url( $url_check ) . '" class="has-spinner">',
			' </a>'
		);
		?>
	</p>

<?php } else { ?>

	<div class="sui-description sui-block-content-center refresh-infos">
		<?php
		printf(
			esc_html( _x( 'We did not check for updates yet... %1$sCheck now%2$s', 'Placeholders: link-open, link-close', 'wpmudev' ) ),
			'<a href="' . esc_url( $url_check ) . '" class="has-spinner">',
			' </a>'
		);
		?>
	</div>

<?php }
