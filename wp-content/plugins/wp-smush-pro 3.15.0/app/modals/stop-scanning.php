<?php
/**
 * Stop scanning media library modal.
 */
?>

<div class="sui-modal sui-modal-md">
	<div
		role="dialog"
		id="smush-stop-scanning-dialog"
		class="sui-modal-content smush-stop-scanning-dialog"
		aria-modal="true"
		aria-labelledby="smush-stop-scanning-dialog-title"
	>
		<div class="sui-box">
			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-top--60">
				<button type="button" class="sui-button-icon sui-button-float--right" data-modal-close="">
					<span class="sui-icon-close sui-md" aria-hidden="true"></span>
					<span class="sui-screen-reader-text">
						<?php esc_html_e( 'Close this dialog.', 'wp-smushit' ); ?>
					</span>
				</button>
				<h3 class="sui-box-title sui-lg"><?php esc_html_e( 'Stop Scanning Media Library', 'wp-smushit' ); ?></h3>
				<p class="sui-description">
					<?php esc_html_e( 'Are you sure you want to cancel the media library scan? This is an irreversible process and will require a rescan for accurate statistics.', 'wp-smushit' ); ?>
				</p>
			</div>
			<div class="sui-box-footer sui-flatten sui-content-center">
				<button type="button" class="sui-button sui-button-ghost" data-modal-close="">
					<?php esc_html_e( 'Cancel', 'wp-smushit' ); ?>
				</button>
				<button type="button" class="sui-button smush-stop-scanning-dialog-button" data-modal-close="">
					<?php esc_html_e( 'Stop Scan', 'wp-smushit' ); ?>
				</button>
			</div>
			<?php if ( ! apply_filters( 'wpmudev_branding_hide_branding', false ) ) : ?>
			<div class="smush-stop-scanning-dialog-footer-background">
				<figure class="sui-box-banner" aria-hidden="true">
					<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/stop-scanning/background.png' ); ?>"
						srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/stop-scanning/background.png' ); ?> 1x, <?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/stop-scanning/background' ); ?>@2x.png 2x"
						alt="<?php esc_attr_e( 'Background stop scanning modal', 'wp-smushit' ); ?>" class="sui-image sui-image-center">
				</figure>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>