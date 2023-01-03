<?php
/**
 * Show Updated Features modal.
 *
 * @package WP_Smush
 *
 * @since 3.7.0
 *
 * @var string $cta_url URL for the modal's CTA button.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="sui-modal sui-modal-md">
	<div
		role="dialog"
		id="smush-updated-dialog"
		class="sui-modal-content smush-updated-dialog"
		aria-modal="true"
		aria-labelledby="smush-title-updated-dialog"
	>
		<div class="sui-box">
			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-sides--20">
				<figure class="sui-box-banner" aria-hidden="true">
					<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/updated/updated.png' ); ?>"
						srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/updated/updated.png' ); ?> 1x, <?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/updated/updated' ); ?>@2x.png 2x"
						alt="<?php esc_attr_e( 'Smush Updated Modal', 'wp-smushit' ); ?>" class="sui-image sui-image-center">
				</figure>

				<a href="<?php echo is_multisite() ? 'javascript:void(0);' : esc_url( $cta_url ); ?>" class="sui-button-icon sui-button-float--right" onclick="WP_Smush.onboarding.hideUpgradeModal()">
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
				</a>
			</div>

			<div class="sui-box-body sui-content-center sui-spacing-sides--30 sui-spacing-top--0 sui-spacing-bottom--50">
				<h3 class="sui-box-title sui-lg" id="smush-title-updated-dialog" style="white-space: normal">
					<?php esc_html_e( 'NEW: Bulk Smush images in the background!', 'wp-smushit' ); ?>
				</h3>

				<p class="sui-description">
					<?php esc_html_e( 'Exciting news! You no longer need to keep the Bulk Smush tab open to complete optimizing your images, as Smush now works on processing the images in the background. Once your images are smushed, you will receive a notification email.', 'wp-smushit' ); ?>
				</p>

				<a href="<?php echo is_multisite() ? 'javascript:void(0);' : esc_url( $cta_url ); ?>" class="sui-button" onclick="WP_Smush.onboarding.hideUpgradeModal()">
					<?php esc_html_e( 'Got it', 'wp-smushit' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>
