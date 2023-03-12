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

$bg_optimization    = WP_Smush::get_instance()->core()->mod->bg_optimization;
$can_use_background = $bg_optimization->can_use_background();
?>

<p>
	<?php esc_html_e( 'Get our full WordPress image optimization suite with Smush Pro and the additional benefits of a WPMU DEV membership.', 'wp-smushit' ); ?>
</p>

<ol class="sui-upsell-list">
	<li>
		<span class="sui-icon-check sui-md" aria-hidden="true"></span>
		<?php esc_html_e( 'Fix Google PageSpeed image recommendations', 'wp-smushit' ); ?>
	</li>
	<li>
		<span class="sui-icon-check sui-md" aria-hidden="true"></span>
		<?php esc_html_e( '10 GB Smush CDN', 'wp-smushit' ); ?>
	</li>
	<?php if( ! $can_use_background ) :?>
	<li>
		<span class="sui-icon-check sui-md" aria-hidden="true"></span>
		<?php esc_html_e( 'Background optimization', 'wp-smushit' ); ?>
	</li>
	<li>
		<span class="sui-icon-check sui-md" aria-hidden="true"></span>
		<?php esc_html_e( 'Unlimited image optimization', 'wp-smushit' ); ?>
	</li>
	<?php endif;?>
	<li>
		<span class="sui-icon-check sui-md" aria-hidden="true"></span>
		<?php esc_html_e( 'Serve next-gen formats with WebP conversion', 'wp-smushit' ); ?>
	</li>
	<li>
		<span class="sui-icon-check sui-md" aria-hidden="true"></span>
		<?php esc_html_e( '24/7 live WordPress support', 'wp-smushit' ); ?>
	</li>
	<li>
		<span class="sui-icon-check sui-md" aria-hidden="true"></span>
		<?php esc_html_e( '30-day money-back guarantee', 'wp-smushit' ); ?>
	</li>
</ol>

<a href="<?php echo esc_url( $upsell_url ); ?>" target="_blank" class="sui-button sui-button-purple sui-margin-top">
	<?php esc_html_e( 'Try SMUSH Pro today!', 'wp-smushit' ); ?>
</a>