<?php
/**
 * Bulk Smush meta box.
 *
 * @since 2.9.0
 * @package WP_Smush
 *
 * @var bool            $all_done          Check if all items are smushed.
 * @var string          $bulk_upgrade_url  Bulk Smush upgrade to PRO url.
 * @var Smush\Core\Core $core              Instance of Smush\Core\Core
 * @var bool            $hide_pagespeed    Check whether to show PageSpeed recommendation or not.
 * @var bool            $is_pro            Check if PRO user or not.
 * @var bool            $lossy_enabled     Is lossy enabled.
 * @var string          $pro_upgrade_url   Upgrade to PRO link.
 * @var string          $upgrade_url       Upgrade to PRO link.
 */

use Smush\Core\Helper;

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<?php if ( 0 !== absint( $core->total_count ) ) : ?>
<p><?php esc_html_e( 'Bulk smush detects images that can be optimized and allows you to compress them in bulk.', 'wp-smushit' ); ?></p>
<?php endif; ?>

<?php
// Show re-smush notice.
echo wp_kses_post( WP_Smush::get_instance()->admin()->bulk_resmush_content() );

// If there are no images in media library.
if ( 0 === absint( $core->total_count ) ) {
	?>
	<?php if ( ! apply_filters( 'wpmudev_branding_hide_branding', false ) ) : ?>
		<span class="wp-smush-no-image tc">
			<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/smush-no-media.png' ); ?>"
				alt="<?php esc_attr_e( 'No attachments found - Upload some images', 'wp-smushit' ); ?>">
		</span>
	<?php endif; ?>
	<p class="wp-smush-no-images-content tc">
		<?php esc_html_e( 'We haven’t found any images in your media library yet so there’s no smushing to be done!', 'wp-smushit' ); ?><br>
		<?php esc_html_e( 'Once you upload images, reload this page and start playing!', 'wp-smushit' ); ?>
	</p>
	<span class="wp-smush-upload-images sui-no-padding-bottom tc">
		<a class="sui-button sui-button-blue tc" href="<?php echo esc_url( admin_url( 'media-new.php' ) ); ?>">
			<?php esc_html_e( 'UPLOAD IMAGES', 'wp-smushit' ); ?>
		</a>
	</span>
	<?php
	return;
}
?>

<div class="sui-notice sui-notice-success wp-smush-all-done <?php echo $all_done ? '' : 'sui-hidden'; ?>">
	<div class="sui-notice-content">
		<div class="sui-notice-message">
			<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
			<p><?php esc_html_e( 'All attachments have been smushed. Awesome!', 'wp-smushit' ); ?></p>
		</div>
	</div>
</div>

<?php $this->view( 'progress-bar', array( 'count' => $core ), 'common' ); ?>

<div class="smush-final-log sui-hidden">
	<div class="smush-bulk-errors"></div>
	<div class="smush-bulk-errors-actions sui-hidden">
		<a href="<?php echo esc_url( admin_url( 'upload.php' ) ); ?>" class="sui-button sui-button-icon sui-button-ghost">
			<i class="sui-icon-photo-picture" aria-hidden="true"></i>
			<?php esc_html_e( 'View all', 'wp-smushit' ); ?>
		</a>
	</div>
</div>

<?php if ( ! $hide_pagespeed ) : ?>
	<div class="wp-smush-pagespeed-recommendation <?php echo $all_done ? '' : 'sui-hidden'; ?>">
		<span class="smush-recommendation-title">
			<?php esc_html_e( 'Still having trouble with PageSpeed tests? Give these a go…', 'wp-smushit' ); ?>
		</span>
		<ol class="smush-recommendation-list">
			<?php if ( ! $is_pro ) : ?>
				<li class="smush-recommendation-lossy">
					<?php
					printf(
						/* translators: %1$s: opening a tag, %2$s: closing a tag */
						esc_html__( 'Upgrade to Smush Pro for advanced lossy compression. %1$sTry pro free%2$s.', 'wp-smushit' ),
						'<a href="' . esc_url( $upgrade_url ) . '" target="_blank">',
						'</a>'
					);
					?>
				</li>
			<?php elseif ( ! $this->settings->get( 'lossy' ) ) : ?>
				<li class="smush-recommendation-lossy">
					<?php
					printf(
						/* translators: %1$s: opening a tag, %2$s: closing a tag */
						esc_html__( 'Enable %1$sSuper-Smush%2$s for advanced lossy compression to optimize images further with almost no visible drop in quality.', 'wp-smushit' ),
						'<a href="#" class="wp-smush-lossy-enable">',
						'</a>'
					);
					?>
				</li>
			<?php endif; ?>
			<li class="smush-recommendation-resize">
				<?php
				printf(
					/* translators: %1$s: opening a tag, %2$s: closing a tag */
					esc_html__( 'Make sure your images are the right size for your theme. %1$sLearn more%2$s.', 'wp-smushit' ),
					'<a href="' . esc_url( 'https://goo.gl/kCqWxS' ) . '" target="_blank">',
					'</a>'
				);
				?>
			</li>
			<?php if ( ! $this->settings->get( 'resize' ) ) : ?>
				<li class="smush-recommendation-resize-original">
					<?php
					printf(
						/* translators: %1$s: opening a tag, %2$s: closing a tag */
						esc_html__( 'Enable %1$sResize Full Size Images%2$s to scale big images down to a reasonable size and save a ton of space.', 'wp-smushit' ),
						'<a href="#" class="wp-smush-resize-enable">',
						'</a>'
					);
					?>
				</li>
			<?php endif; ?>
		</ol>
		<span class="dismiss-recommendation">
			<?php esc_html_e( 'DISMISS', 'wp-smushit' ); ?>
		</span>
	</div>
<?php endif; ?>

<div class="wp-smush-bulk-wrapper <?php echo $all_done ? ' sui-hidden' : ''; ?>">
	<?php
	if ( $core->remaining_count > 0 ) :
		$class = count( $core->resmush_ids ) > 0 ? ' sui-hidden' : '';
		?>
		<div class="sui-notice sui-notice-warning<?php echo esc_attr( $class ); ?>">
			<div class="sui-notice-content">
				<div class="sui-notice-message">
					<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
					<p>
						<?php
						printf(
							/* translators: %1$s: user name, %2$s: starting strong tag, %3$s: starting span tag, %4$d: remaining image count, %5$s: ending span tag, %6$s: ending strong tag */
							_n( '%1$s, you have %2$s%3$s%4$d%5$s attachment%6$s that needs smushing!', '%1$s, you have %2$s%3$s%4$d%5$s attachments%6$s that need smushing!', $core->remaining_count, 'wp-smushit' ),
							esc_attr( Helper::get_user_name() ),
							'<strong>',
							'<span class="wp-smush-remaining-count">',
							absint( $core->remaining_count ),
							'</span>',
							'</strong>'
						);

						if ( ! $is_pro && $core->remaining_count > 50 ) {
							printf(
							/* translators: %1$s: opening a tag, %2$s: closing a tag */
								esc_html__( ' %1$sUpgrade to Pro%2$s to bulk smush all your images with one click.', 'wp-smushit' ),
								'<a href="' . esc_url( $bulk_upgrade_url ) . '" target="_blank" title="' . esc_html__( 'Smush Pro', 'wp-smushit' ) . '">',
								'</a>'
							);
							esc_html_e( ' Free users can smush 50 images with each click.', 'wp-smushit' );
						}
						?>
					</p>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<button type="button" class="wp-smush-all sui-button sui-button-blue" title="<?php esc_attr_e( 'Click to start Bulk Smushing images in Media Library', 'wp-smushit' ); ?>">
		<?php esc_html_e( 'BULK SMUSH NOW', 'wp-smushit' ); ?>
	</button>
</div>

<?php
if ( $is_pro && $lossy_enabled ) {
	?>
	<p class="wp-smush-enable-lossy tc sui-hidden">
		<?php esc_html_e( 'Tip: Enable Super-Smush in the Settings area to get even more savings with almost no visible drop in quality.', 'wp-smushit' ); ?>
	</p>
	<?php
} elseif ( ! $is_pro ) {
	?>
	<div class="sui-upsell-row">
		<img class="sui-image sui-upsell-image sui-upsell-image-smush" src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/smush-graphic-bulksmush-upsell@2x.png' ); ?>" alt="">

		<div class="sui-notice sui-notice-purple smush-upsell-notice">
			<div class="sui-notice-content">
				<div class="sui-notice-message">
					<p>
						<?php
						printf(
						/* translators: %1$s: opening a tag, %2$s: closing a tag */
							esc_html__( 'Did you know %1$sSmush Pro%2$s delivers up to 2x better compression, allows you to smush your originals and removes any bulk smushing limits?', 'wp-smushit' ),
							'<strong>',
							'</strong>'
						);
						?>
					</p>
					<p>
						<a href="<?php echo esc_url( $pro_upgrade_url ); ?>" class="sui-button sui-button-purple" target="_blank">
							<?php esc_html_e( 'Try it absolutely FREE', 'wp-smushit' ); ?>
						</a>
					</p>
				</div>
			</div>
		</div>
	</div>
	<?php
}