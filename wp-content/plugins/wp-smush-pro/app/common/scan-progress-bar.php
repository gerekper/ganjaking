<?php
/**
 * Progress bar block.
 *
 * @package WP_Smush
 *
 * @var integer $count          Total number of images to smush.
 * @var string  $background_in_processing_notice
 * @var bool $background_processing_enabled
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>
<div class="wp-smush-scan-progress-bar-wrapper sui-hidden">
	<div class="wp-smush-scan-progress-bar-inner">
		<div class="wp-smush-progress-status">
			<div class="wp-smush-scan-description">
				<h4><?php esc_html_e( 'Scanning Media Library', 'wp-smushit' ); ?></h4>
				<span class="wp-smush-progress-percent">0%</span>
				<p>
				<?php
					/* translators: 1: Open span tag <span> 2: Close span tag </span> */
					printf( esc_html__( 'Image re-check in progress - %1$s0 seconds%2$s remaining', 'wp-smushit' ), '<span class="wp-smush-remaining-time">', '</span>' );
				?>
				</p>
				<p class="wp-smush-scan-hold-on-notice sui-hidden">
					<?php
						/* translators: 1: <strong> 2: </strong> */
						printf( esc_html__( '%1$sNote:%2$s This is taking longer than expected, please hold on.', 'wp-smushit' ), '<strong>', '</strong>' );
					?>
				</p>
			</div>
		</div>
		<button type="button" class="sui-button wp-smush-cancel-scan-progress-btn">
			<?php esc_html_e( 'Cancel Scan', 'wp-smushit' ); ?>
		</button>
	</div>
	<div class="sui-progress">
		<div class="sui-progress-bar">
			<span class="wp-smush-progress-inner" style="width: 0.1%"></span>
		</div>
	</div>
</div>
<?php
	$this->view(
		'stop-scanning',
		array(),
		'modals'
	);

	$this->view(
		'retry-scan-notice',
		array(),
		'modals'
	);