<?php
/**
 * Links in the footer.
 *
 * @package WP_Smush
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$hide_footer = false;
$footer_text = sprintf( /* translators: %s - icon */
	esc_html__( 'Made with %s by WPMU DEV', 'wp-smushit' ),
	'<span aria-hidden="true" class="sui-icon-heart"></span>'
);

if ( WP_Smush::is_pro() ) {
	$hide_footer = apply_filters( 'wpmudev_branding_change_footer', $hide_footer );
	$footer_text = apply_filters( 'wpmudev_branding_footer_text', $footer_text );
}

?>

<div class="sui-footer">
	<?php echo wp_kses_post( $footer_text ); ?>
</div>

<ul class="sui-footer-nav">
	<?php if ( ! WP_Smush::is_pro() ) : ?>
		<li><a href="https://profiles.wordpress.org/wpmudev#content-plugins" target="_blank">
				<?php esc_html_e( 'Free Plugins', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/roadmap/?utm_source=smush&utm_medium=plugin&utm_campaign=smush_footer_roadmap" target="_blank">
				<?php esc_html_e( 'Roadmap', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wordpress.org/support/plugin/wp-smushit" target="_blank">
				<?php esc_html_e( 'Support', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/docs/?utm_source=smush&utm_medium=plugin&utm_campaign=smush_footer_docs" target="_blank">
				<?php esc_html_e( 'Docs', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/hub-welcome/?utm_source=smush&utm_medium=plugin&utm_campaign=smush_footer_hub" target="_blank">
				<?php esc_html_e( 'The Hub', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/terms-of-service/?utm_source=smush&utm_medium=plugin&utm_campaign=smush_footer_tos" target="_blank">
				<?php esc_html_e( 'Terms of Service', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://incsub.com/privacy-policy/" target="_blank">
				<?php esc_html_e( 'Privacy Policy', 'wp-smushit' ); ?>
			</a></li>
	<?php elseif ( ! $hide_footer ) : ?>
		<li><a href="https://wpmudev.com/hub2/" target="_blank">
				<?php esc_html_e( 'The Hub', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/projects/category/plugins/" target="_blank">
				<?php esc_html_e( 'Plugins', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/roadmap/" target="_blank">
				<?php esc_html_e( 'Roadmap', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/hub2/support/" target="_blank">
				<?php esc_html_e( 'Support', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/docs/" target="_blank">
				<?php esc_html_e( 'Docs', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/hub2/community/" target="_blank">
				<?php esc_html_e( 'Community', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/terms-of-service/" target="_blank">
				<?php esc_html_e( 'Terms of Service', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://incsub.com/privacy-policy/" target="_blank">
				<?php esc_html_e( 'Privacy Policy', 'wp-smushit' ); ?>
			</a></li>
	<?php endif; ?>
</ul>
<?php if ( ! $hide_footer ) : ?>
<ul class="sui-footer-social">
	<li>
		<a href="https://www.facebook.com/wpmudev" target="_blank">
			<i class="sui-icon-social-facebook" aria-hidden="true"></i>
			<span class="sui-screen-reader-text">Facebook</span>
		</a>
	</li>
	<li>
		<a href="https://twitter.com/wpmudev" target="_blank">
			<i class="sui-icon-social-twitter" aria-hidden="true"></i>
		</a>
		<span class="sui-screen-reader-text">Twitter</span>
	</li>
	<li>
		<a href="https://www.instagram.com/wpmu_dev/" target="_blank">
			<i class="sui-icon-instagram" aria-hidden="true"></i>
			<span class="sui-screen-reader-text">Instagram</span>
		</a>
	</li>
</ul>
<?php endif; ?>