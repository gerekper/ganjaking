<?php
/**
 * Upsell meta box.
 *
 * @since 3.8.6
 * @package WP_Smush
 *
 * @var string $upsell_url  Upsell URL.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<p>
	<?php esc_html_e( 'Get our full WordPress image optimization suite with Smush Pro and additional benefits of WPMU DEV membership.', 'wp-smushit' ); ?>
</p>

<ul class="smush-pro-features">
	<li class="smush-pro-feature-row">
		<div class="smush-pro-feature-title">
			<?php esc_html_e( 'Fix Google PageSpeed image recommendations', 'wp-smushit' ); ?>
		</div>
	</li>
	<li class="smush-pro-feature-row">
		<div class="smush-pro-feature-title">
			<?php esc_html_e( '10 GB Smush CDN', 'wp-smushit' ); ?>
		</div>
	</li>
	<li class="smush-pro-feature-row">
		<div class="smush-pro-feature-title">
			<?php esc_html_e( 'Compress original images', 'wp-smushit' ); ?>
		</div>
	</li>
	<li class="smush-pro-feature-row">
		<div class="smush-pro-feature-title">
			<?php esc_html_e( '2x better compression', 'wp-smushit' ); ?>
		</div>
	</li>
	<li class="smush-pro-feature-row">
		<div class="smush-pro-feature-title">
			<?php esc_html_e( 'Serve a next-gen format with WebP conversion', 'wp-smushit' ); ?>
		</div>
	</li>
	<li class="smush-pro-feature-row">
		<div class="smush-pro-feature-title">
			<?php esc_html_e( 'Copy your full size images', 'wp-smushit' ); ?>
		</div>
	</li>
	<li class="smush-pro-feature-row">
		<div class="smush-pro-feature-title">
			<?php esc_html_e( '24/7 live WordPress support', 'wp-smushit' ); ?>
		</div>
	</li>
	<li class="smush-pro-feature-row">
		<div class="smush-pro-feature-title">
			<?php esc_html_e( 'The WPMU DEV Guarantee', 'wp-smushit' ); ?>
		</div>
	</li>
</ul>

<a href="<?php echo esc_url( $upsell_url ); ?>" target="_blank" class="sui-button sui-button-purple">
	<?php esc_html_e( 'Try Pro for FREE today!', 'wp-smushit' ); ?>
</a>