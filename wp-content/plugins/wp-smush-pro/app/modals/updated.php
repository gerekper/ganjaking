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

				<button class="sui-button-icon sui-button-float--right" data-modal-close="" onclick="WP_Smush.onboarding.hideUpgradeModal()">
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
				</button>
			</div>

			<div class="sui-box-body sui-content-center sui-spacing-sides--30 sui-spacing-top--0 sui-spacing-bottom--50">
				<h3 class="sui-box-title sui-lg" id="smush-title-updated-dialog" style="white-space: normal">
					<?php esc_html_e( 'Preset configurations are here!', 'wp-smushit' ); ?>
				</h3>

				<p class="sui-description">
					<?php esc_html_e( 'You can now save your Smush settings, download them and reapply them on another site in just a few clicks! No more having to repeat setting up Smush on all your new client sites, just upload your config and go.', 'wp-smushit' ); ?>
				</p>

				<p style="color: #333; text-align: left; margin-bottom: 5px; margin-top: 30px;">
					<span class="sui-icon-hub sui-md" aria-hidden="true" style="margin-right: 5px;"></span>
					<span style="font-size: 13px; font-weight: 700;"><?php esc_html_e( 'Apply presets to multiple sites via The Hub', 'wp-smushit' ); ?></span>
				</p>

				<p class="sui-description" style="text-align: left;">
					<?php esc_html_e( "If you're a WPMU DEV member, you get it one better - all your configs are automatically uploaded and shared across all your sites, ready to be applied whenever you like from either The Hub or the plugin.", 'wp-smushit' ); ?>
				</p>

				<a href="<?php echo esc_url( $cta_url ); ?>" class="sui-button" onclick="WP_Smush.onboarding.hideUpgradeModal()">
					<?php esc_html_e( "Awesome, let's go!", 'wp-smushit' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>