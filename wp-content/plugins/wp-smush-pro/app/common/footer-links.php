<?php
/**
 * Links in the footer.
 *
 * @package WP_Smush
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<div class="sui-footer">
	<?php esc_html_e( 'Made with', 'wp-smushit' ); ?> <i class="sui-icon-heart" aria-hidden="true"></i> <?php esc_html_e( 'by WPMU DEV', 'wp-smushit' ); ?>
</div>

<ul class="sui-footer-nav">
	<?php if ( ! WP_Smush::is_pro() ) : ?>
		<li><a href="https://profiles.wordpress.org/wpmudev#content-plugins" target="_blank">
				<?php esc_html_e( 'Free Plugins', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/roadmap/" target="_blank">
				<?php esc_html_e( 'Roadmap', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wordpress.org/support/plugin/wp-smushit" target="_blank">
				<?php esc_html_e( 'Support', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/docs/" target="_blank">
				<?php esc_html_e( 'Docs', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/hub-welcome/" target="_blank">
				<?php esc_html_e( 'The Hub', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/terms-of-service/" target="_blank">
				<?php esc_html_e( 'Terms of Service', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://incsub.com/privacy-policy/" target="_blank">
				<?php esc_html_e( 'Privacy Policy', 'wp-smushit' ); ?>
			</a></li>
	<?php else : ?>
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
		<li><a href="https://wpmudev.com/academy/" target="_blank">
				<?php esc_html_e( 'Academy', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://wpmudev.com/terms-of-service/" target="_blank">
				<?php esc_html_e( 'Terms of Service', 'wp-smushit' ); ?>
			</a></li>
		<li><a href="https://incsub.com/privacy-policy/" target="_blank">
				<?php esc_html_e( 'Privacy Policy', 'wp-smushit' ); ?>
			</a></li>
	<?php endif; ?>
</ul>