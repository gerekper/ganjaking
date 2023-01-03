<?php
/**
 * Output the progress dialog for the Directory smush list dialog
 *
 * @package WP_Smush
 */

use Smush\Core\Core;

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="sui-modal sui-modal-lg">
	<div
			role="dialog"
			id="wp-smush-progress-dialog"
			class="sui-modal-content wp-smush-progress-dialog"
			aria-modal="true"
			aria-labelledby="progress-dialog-title"
			aria-describedby="progress-dialog-description"
	>
		<div class="sui-box">
			<div class="sui-box-header">
				<h3 class="sui-box-title" id="progress-dialog-title">
					<?php esc_html_e( 'Choose Directory', 'wp-smushit' ); ?>
				</h3>
				<button class="sui-button-icon sui-button-float--right" data-modal-close="" id="dialog-close-div">
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
					<span class="sui-screen-reader-text"><?php esc_html_e( 'Close', 'wp-smushit' ); ?></span>
				</button>
			</div>

			<div class="sui-box-body">
				<p id="progress-dialog-description">
					<?php esc_html_e( 'Bulk smushing is in progress, you need to leave this tab open until the process completes.', 'wp-smushit' ); ?>
				</p>

				<div class="sui-progress-block sui-progress-can-close">
					<div class="sui-progress">
						<span class="sui-progress-icon" aria-hidden="true">
							<i class="sui-icon-loader sui-loading"></i>
						</span>
						<div class="sui-progress-text">
							<span>0%</span>
						</div>
						<div class="sui-progress-bar" aria-hidden="true">
							<span style="width: 0"></span>
						</div>
					</div>
					<button class="sui-button-icon sui-tooltip" id="cancel-directory-smush" type="button" data-tooltip="<?php esc_attr_e( 'Cancel', 'wp-smushit' ); ?>">
						<i class="sui-icon-close"></i>
					</button>
				</div>

				<div class="sui-progress-state">
					<span class="sui-progress-state-text">
						<?php esc_html_e( '-/- images optimized', 'wp-smushit' ); ?>
					</span>
				</div>

				<div id="smush-scan-error-notice" class="sui-notice sui-notice-error">
					<div class="sui-notice-content">
						<div class="sui-notice-message">
							<span class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></span>
							<p>
								<?php
								$support_url = WP_Smush::is_pro() ? 'https://wpmudev.com/hub2/support/#get-support' : 'https://wordpress.org/support/plugin/wp-smushit/';

								printf(
									/* translators: error message placeholder */
									esc_html__( 'Smush has encountered a %s error while attempting to compress the selected images.', 'wp-smushit' ),
									'<span id="smush-scan-error"></span>'
								)
								?>
								<span class="smush-403-error-message">
									<?php esc_html_e( 'This blockage may be caused by an active plugin, firewall, or file permission setting. Disable or reconfigure the blocker before trying again.', 'wp-smushit' ); ?>
								</span>
								<span>
									<?php
									printf(
										/* translators: 1. opening 'a' tag with the support link, 2. closing 'a' tag */
										esc_html__( 'Please contact our %1$ssupport%2$s team if the issue persists.', 'wp-smushit' ),
										'<a href="' . esc_url( $support_url ) . '" target="_blank">',
										'</a>'
									);
									?>
								</span>
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="sui-box-footer sui-content-right">
				<button class="sui-modal-close sui-button wp-smush-cancel-dir" data-modal-closez="">
					<span class="sui-loading-text"><?php esc_html_e( 'CANCEL', 'wp-smushit' ); ?></span>
					<span class="sui-icon-loader sui-loading" aria-hidden="true"></span>
				</button>

				<button class="sui-button wp-smush-resume-scan">
					<?php esc_html_e( 'RESUME', 'wp-smushit' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
