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

			<div class="sui-box-body sui-content-center sui-spacing-sides--50 sui-spacing-top--0 sui-spacing-top--0 sui-spacing-bottom--50">
				<h3 class="sui-box-title sui-lg" id="smush-title-updated-dialog" style="white-space: normal">
					<?php esc_html_e( 'Welcome Smush Tutorials!', 'wp-smushit' ); ?>
				</h3>

				<p class="sui-description">
					<?php esc_html_e( 'Do you want to get the most out of Smush? Check out our new Tutorials section which gives you access to all of our tutorial articles, right at your fingertips. Whether you want a quick run-through of how to configure Smush or you want to get into the details of a particular feature, our blogs are a powerhouse of information.', 'wp-smushit' ); ?>
				</p>

				<a href="<?php echo esc_url( $cta_url ); ?>" class="sui-button" onclick="WP_Smush.onboarding.hideUpgradeModal()">
					<?php esc_html_e( 'View tutorials', 'wp-smushit' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>