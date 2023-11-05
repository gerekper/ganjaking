<?php
/**
 * Progress bar block.
 *
 * @package WP_Smush
 *
 * @var integer $count          Total number of images to smush.
 * @var string  $background_in_processing_notice
 * @var bool $background_processing_enabled
 * @var string  $in_processing_notice
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="wp-smush-bulk-progress-bar-wrapper sui-hidden">
	<div class="sui-notice sui-notice-warning sui-hidden"></div>

	<div id="wp-smush-running-notice" class="sui-notice">
		<div class="sui-notice-content">
			<div class="sui-notice-message">
				<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
				<p>
					<?php
					if ( $background_processing_enabled ) {
						$desc = $background_in_processing_notice;
					} else {
						$desc = $in_processing_notice;
					}
					echo wp_kses_post( $desc );
					?>
				</p>
			</div>
		</div>
	</div>

	<div class="sui-progress-block sui-progress-can-close">
		<div class="sui-progress">
			<span class="sui-progress-icon" aria-hidden="true">
				<i class="sui-icon-loader sui-loading"></i>
			</span>
			<div class="sui-progress-text">
				<span class="wp-smush-images-percent">0%</span>
			</div>
			<div class="sui-progress-bar">
				<span class="wp-smush-progress-inner" style="width: 0%"></span>
			</div>
		</div>
		<?php
			// $cancel_btn_class = $background_processing_enabled ? 'wp-smush-bo-cancel-bulk' : 'wp-smush-cancel-bulk';
			$cancel_btn_class = 'wp-smush-cancel-btn';
		?>
		<button class="sui-progress-close <?php echo esc_attr( $cancel_btn_class ); ?>" type="button">
			<?php esc_html_e( 'Cancel', 'wp-smushit' ); ?>
		</button>
		<button class="sui-progress-close sui-button-icon sui-tooltip wp-smush-all sui-hidden" type="button" data-tooltip="<?php esc_html_e( 'Resume scan.', 'wp-smushit' ); ?>">
			<i class="sui-icon-play"></i>
		</button>
	</div>

	<div class="sui-progress-state">
		<span class="sui-progress-state-text"><span>0</span>/<span class="wp-smush-total-count"><?php echo absint( $count ); ?></span> </span>
		<span class="sui-progress-state-unit"><?php esc_html_e( 'images optimized', 'wp-smushit' ); ?></span>
	</div>

	<div id="bulk-smush-resume-button" class="sui-hidden">
		<a class="wp-smush-all sui-button wp-smush-started wp-smush-resume-bulk-smush">
			<i class="sui-icon-play" aria-hidden="true"></i>
			<?php esc_html_e( 'Resume', 'wp-smushit' ); ?>
		</a>
	</div>
</div>