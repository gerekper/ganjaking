<?php
/**
 * Reset settings modal.
 *
 * @since 3.2.0
 * @package WP_Smush
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="sui-dialog sui-dialog-sm smush-onboarding-dialog" aria-hidden="true" tabindex="-1" id="resizing-update">
	<div class="sui-dialog-overlay sui-fade-in"></div>
	<div class="sui-dialog-content" aria-labelledby="resetSettings" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document" style="background-color: #ffffff">
			<div class="sui-box-header sui-dialog-with-image">
				<div class="sui-dialog-image" aria-hidden="true">
					<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/onboarding/graphic-onboarding-start.png' ); ?>"
						srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/onboarding/graphic-onboarding-start.png' ); ?> 1x, <?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/onboarding/graphic-onboarding-start@2x.png' ); ?> 2x"
						alt="<?php esc_attr_e( 'Smush Onboarding Modal', 'wp-smushit' ); ?>" class="sui-image sui-image-center">
				</div>

				<h3 class="sui-box-title" id="resetSettings">
					<?php esc_html_e( 'Resizing & backup updates', 'wp-smushit' ); ?>
				</h3>
			</div>

			<div class="sui-box-body">
				<p class="sui-description" style="margin-top: 10px; margin-bottom: 0; max-width: 430px">
					<?php
					printf(
						/* translators: %s - name */
						esc_html__( 'Hey %s, we have a wee update to notify you about.', 'wp-smushit' ),
						'some name'
					);
					?>
				</p>
				<p class="sui-description" style="margin-top: 10px; margin-bottom: 0; max-width: 430px">
					<?php esc_html_e( 'WordPress v5.3 and newer automatically stores backup copies of all new images  you upload, as well as generating a new ‘big image’ max sized thumbnail to use as your full size version. That means you now have two extra images for every upload, and Smush no longer needs to back up your originals.', 'wp-smushit' ); ?>
				</p>
				<p class="sui-description" style="margin-top: 10px; max-width: 430px">
					<?php
					printf(
						/* translators: %1$s - opening a tag, %2$s - closing a tag. */
						esc_html__( 'You’ll note these changes in the Bulk Smush Settings area, we’ve done our best to explain the change. You can also %1$sread all about it%2$s on the WordPress Core trac threads.', 'wp-smushit' ),
						'<a href="https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/" target="_blank">',
						'</a>'
					);
					?>
				</p>

				<div class="sui-block-content-center">
					<a class="sui-button sui-button-ghost" data-a11y-dialog-hide>
						<?php esc_html_e( 'Close', 'wp-smushit' ); ?>
					</a>
					&nbsp;&nbsp;
					<a class="sui-button sui-button-blue" id="close-resize-update-dialog" data-a11y-dialog-hide>
						<?php esc_html_e( 'Review Settings', 'wp-smushit' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
