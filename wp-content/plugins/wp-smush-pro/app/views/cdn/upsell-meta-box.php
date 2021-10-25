<?php
/**
 * Upsell CDN meta box.
 *
 * @since 3.0
 * @package WP_Smush
 *
 * @var string $upgrade_url  Upgrade URL.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="sui-block-content-center">
	<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/graphic-smush-cdn-default.png' ); ?>"
		srcset="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/graphic-smush-cdn-default@2x.png' ); ?> 2x"
		alt="<?php esc_html_e( 'Smush CDN', 'wp-smushit' ); ?>">

	<p>
		<?php
		esc_html_e(
			'Multiply the speed and savings! Upload huge images and the Smush CDN will perfectly resize the files, safely convert to a Next-Gen format (WebP), and delivers them directly to your visitors from our blazing-fast multi-location globe servers.',
			'wp-smushit'
		);
		?>
	</p>

	<ul class="smush-pro-features">
		<li class="smush-pro-feature-row">
			<div class="smush-pro-feature-title"><?php esc_html_e( 'Fix Google PageSpeeds ‘properly size images’ suggestion', 'wp-smushit' ); ?></div>
		</li>
		<li class="smush-pro-feature-row">
			<div class="smush-pro-feature-title"><?php esc_html_e( 'WebP conversion with CDN', 'wp-smushit' ); ?></div>
		</li>
		<li class="smush-pro-feature-row">
			<div class="smush-pro-feature-title"><?php esc_html_e( 'Serve background images from the CDN', 'wp-smushit' ); ?></div>
		</li>
	</ul>

	<a href="<?php echo esc_url( $upgrade_url ); ?>" class="sui-button sui-button-purple" target="_blank">
		<?php esc_html_e( 'Try CDN for free', 'wp-smushit' ); ?>
	</a>
</div>