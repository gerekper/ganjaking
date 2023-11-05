<?php
/**
 * Scan error notice for lower resource site on background dead.
 */

use Smush\Core\Helper;

$recheck_images_link = Helper::get_recheck_images_link();
?>

<div class="sui-modal sui-modal-sm">
	<div
		role="dialog"
		id="smush-retry-scan-notice"
		class="sui-modal-content smush-retry-scan-notice"
		aria-modal="true"
		aria-labelledby="smush-retry-scan-notice-title"
	>
		<div class="sui-box">
			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">
				<button type="button" class="sui-button-icon sui-button-float--right" data-modal-close="">
					<span class="sui-icon-close sui-md" aria-hidden="true"></span>
					<span class="sui-screen-reader-text">
						<?php esc_html_e( 'Close this dialog.', 'wp-smushit' ); ?>
					</span>
				</button>
				<h3 class="sui-box-title sui-lg"><?php esc_html_e( 'Smush Encountered an Error', 'wp-smushit' ); ?></h3>
			</div>
			<div class="sui-box-body sui-content-center sui-spacing-sides--30">
				<div class="smush-retry-scan-notice-content">
					<div class="sui-notice sui-notice-error">
						<div class="sui-notice-content">
							<div class="sui-notice-message">
								<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
								<p>
									<?php esc_html_e( 'Oops! Our scan hit an error due to limited resources on your site.', 'wp-smushit' ); ?>
								</p>
							</div>
						</div>
					</div>
					<p>
						<?php esc_html_e( 'No worries, we have adjusted the scan to use fewer resources the next time.', 'wp-smushit' ); ?>
					</p>
				</div>
			</div>
			<div class="sui-box-footer sui-flatten sui-content-center sui-spacing-bottom--40">
				<a href="<?php echo esc_url( $recheck_images_link ); ?>" class="sui-button sui-button-ghost smush-retry-scan-notice-button">
					<?php esc_html_e( 'Retry Scan', 'wp-smushit' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>