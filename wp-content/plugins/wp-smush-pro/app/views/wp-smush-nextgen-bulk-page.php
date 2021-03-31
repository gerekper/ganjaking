<?php
/**
 * Render Smush NextGen pages.
 *
 * @package WP_Smush
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$this->do_meta_boxes( 'summary' );
$this->do_meta_boxes( 'bulk' );

?>

<div class="sui-footer">
	<?php esc_html_e( 'Made with', 'wp-smushit' ); ?> <i class="sui-icon-heart" aria-hidden="true"></i> <?php esc_html_e( 'by WPMU DEV', 'wp-smushit' ); ?>
</div>

<ul class="sui-footer-nav">
	<li><a href="https://wpmudev.com/hub2/" target="_blank">
			<?php esc_html_e( 'The Hub', 'wp-smushit' ); ?>
		</a></li>
	<li><a href="https://wpmudev.com/projects/category/plugins/" target="_blank">
			<?php esc_html_e( 'Plugins', 'wp-smushit' ); ?>
		</a></li>
	<li><a href="https://wpmudev.com/roadmap/" target="_blank">
			<?php esc_html_e( 'Roadmap', 'wp-smushit' ); ?>
		</a></li>
	<li><a href="https://wpmudev.com/hub/support/" target="_blank">
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
			<?php esc_html_e( 'Terms', 'wp-smushit' ); ?></a>  &  <a href="https://incsub.com/privacy-policy/" target="_blank">
			<?php esc_html_e( 'Privacy', 'wp-smushit' ); ?>
		</a></li>
</ul>