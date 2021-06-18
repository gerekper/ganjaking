<?php
/**
 * Local WebP meta box.
 *
 * @since 3.8.6
 * @package WP_Smush
 *
 * @var bool          $htaccess_written  Is htaccess written.
 * @var bool|WP_Error $is_configured     Is local WebP module configured.
 * @var bool          $is_webp_active    Is local WebP module enabled.
 * @var string        $server_type       Server type.
 * @var string        $upsell_url        Upsell URL.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<p>
	<?php esc_html_e( "Serve WebP versions of your images to supported browsers, and gracefully fall back on JPEGs and PNGs for browsers that don't support WebP.", 'wp-smushit' ); ?>
</p>

<?php if ( ! WP_Smush::is_pro() ) : ?>
	<a href="<?php echo esc_url( $upsell_url ); ?>" target="_blank" class="sui-button sui-button-purple">
		<?php esc_html_e( 'Upgrade to Pro', 'wp-smushit' ); ?>
	</a>
<?php elseif ( ! $is_webp_active ) : ?>
	<button class="sui-button sui-button-blue" id="smush-toggle-webp-button" data-action="enable">
		<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wp-smushit' ); ?></span>
		<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
	</button>
<?php else : ?>
	<?php if ( true === $is_configured ) : ?>
		<div class="sui-notice sui-notice-success">
			<div class="sui-notice-content">
				<div class="sui-notice-message">
					<span class="sui-notice-icon sui-icon-check-tick sui-md" aria-hidden="true"></span>
					<p><?php esc_html_e( 'WebP conversion is active and working well.', 'wp-smushit' ); ?></p>
				</div>
			</div>
		</div>
	<?php else : ?>
		<div class="sui-notice sui-notice-warning">
			<div class="sui-notice-content">
				<div class="sui-notice-message">
					<span class="sui-notice-icon sui-icon-warning-alert sui-md" aria-hidden="true"></span>
					<p>
						<?php
						if ( is_wp_error( $is_configured ) ) {
							printf( /* translators: 1. error code, 2. error message. */
								esc_html__( "We couldn't check the WebP server rules status because there was an error with the test request. Please contact support for assistance. Code %1\$s: %2\$s.", 'wp-smushit' ),
								esc_html( $is_configured->get_error_code() ),
								esc_html( $is_configured->get_error_message() )
							);
						} elseif ( 'apache' === $server_type && $htaccess_written ) {
							esc_html_e( 'The rules have been applied, however, the images have still not been converted to WebP.  We recommend to contact your server provider to know more about the cause of this issue. ', 'wp-smushit' );
						} else {
							esc_html_e( "Server configurations aren't applied. Please configure to start serving images in WebP format.", 'wp-smushit' );
						}
						?>
					</p>
					<!-- <p><?php esc_html_e( 'The rules have been applied, however, the images have still not been converted to WebP.  We recommend to contact your server provider to know more about the cause of this issue.', 'wp-smushit' ); ?></p> -->
				</div>
			</div>
		</div>
	<?php endif; ?>
	<a href="<?php echo esc_url( $this->get_url( 'smush-webp' ) ); ?>" class="sui-button sui-button-ghost">
		<span class="sui-icon-wrench-tool" aria-hidden="true"></span>
		<?php esc_html_e( 'Configure', 'wp-smushit' ); ?>
	</a>
<?php endif; ?>