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
		class="sui-modal-content smush-updated-dialog wp-smush-modal-dark-background"
		aria-modal="true"
		aria-labelledby="smush-title-updated-dialog"
	>
		<div class="sui-box">
			<div class="sui-box-header sui-flatten sui-content-center sui-spacing-sides--20">
				<figure class="sui-box-banner" aria-hidden="true">
					<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/bulk-smush/smush-ultra-compression.jpg' ); ?>"
						srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/bulk-smush/smush-ultra-compression.jpg' ); ?> 1x, <?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/bulk-smush/smush-ultra-compression' ); ?>@2x.jpg 2x"
						alt="<?php esc_attr_e( 'Smush Updated Modal', 'wp-smushit' ); ?>" class="sui-image sui-image-center">
				</figure>

				<button class="sui-button-icon sui-button-float--right sui-button-white" style="box-shadow:none!important" onclick="WP_Smush.onboarding.hideUpgradeModal(event, this)">
					<i class="sui-icon-close sui-md" aria-hidden="true"></i>
				</button>
			</div>

			<div class="sui-box-body sui-content-center sui-spacing-sides--30 sui-spacing-top--40 sui-spacing-bottom--40">
				<h3 class="sui-box-title sui-lg" id="smush-title-updated-dialog" style="white-space: normal">
					<?php esc_html_e( 'New: Serve images faster with Ultra Smush', 'wp-smushit' ); ?>
				</h3>

				<p class="sui-description">
					<?php esc_html_e( 'Experience up to 5x better compression than Super Smush. Optimize your images even further and make your pages load faster than ever. Level up your site performance with Ultra Smush now.', 'wp-smushit' ); ?>
				</p>
				<?php
				if ( $cta_url ) {
					?>
						<a href="<?php echo esc_js( $cta_url ); ?>" class="sui-button sui-button-blue" onclick="WP_Smush.onboarding.hideUpgradeModal(event, this)">
						<?php esc_html_e( 'Go to Settings', 'wp-smushit' ); ?>
						</a>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>