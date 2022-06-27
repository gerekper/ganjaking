<?php
/**
 * Bulk Smush meta box.
 *
 * @since 2.9.0
 * @package WP_Smush
 *
 * @var Smush\Core\Core $core                  Instance of Smush\Core\Core
 * @var bool            $is_pro                Check if PRO user or not.
 * @var integer         $unsmushed_count       Count of the images that need smushing.
 * @var integer         $resmush_count         Count of the images that need re-smushing.
 * @var integer         $total_images_to_smush Total count of all images to smush. Unsmushed images + images to re-smush.
 * @var string          $bulk_upgrade_url      Bulk Smush upgrade to PRO url.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

?>

<?php if ( 0 !== absint( $core->total_count ) ) : ?>
<p><?php esc_html_e( 'Bulk smush detects images that can be optimized and allows you to compress them in bulk.', 'wp-smushit' ); ?></p>
<?php endif; ?>

<?php
// If there are no images in media library.
if ( 0 === absint( $core->total_count ) ) {
	?>
	<div class="sui-message">
		<?php if ( ! apply_filters( 'wpmudev_branding_hide_branding', false ) ) : ?>
			<img src="<?php echo esc_url( WP_SMUSH_URL . 'app/assets/images/smush-no-media.png' ); ?>"
				alt="<?php esc_attr_e( 'No attachments found - Upload some images', 'wp-smushit' ); ?>"
				class="sui-image"
			>
		<?php endif; ?>

		<div class="sui-message-content">
			<p>
				<?php esc_html_e( 'We haven’t found any images in your media library yet so there’s no smushing to be done!', 'wp-smushit' ); ?><br>
				<?php esc_html_e( 'Once you upload images, reload this page and start playing!', 'wp-smushit' ); ?>
			</p>

			<a class="sui-button sui-button-blue" href="<?php echo esc_url( admin_url( 'media-new.php' ) ); ?>">
				<?php esc_html_e( 'UPLOAD IMAGES', 'wp-smushit' ); ?>
			</a>
		</div>
	</div>
	<?php
	return;
}
?>

<?php $this->view( 'progress-bar', array( 'count' => $total_images_to_smush ), 'common' ); ?>

<div class="smush-final-log sui-hidden">
	<div class="smush-bulk-errors"></div>
	<div class="smush-bulk-errors-actions sui-hidden">
		<a href="<?php echo esc_url( apply_filters( 'smush_unsmushed_media_filter_url', admin_url( 'upload.php?smush-filter=unsmushed' ) ) ); ?>" class="sui-button sui-button-ghost">
			<i class="sui-icon-photo-picture" aria-hidden="true"></i>
			<?php esc_html_e( 'View all in media library', 'wp-smushit' ); ?>
		</a>
	</div>
</div>

<?php $this->view( 'all-images-smushed-notice', array( 'all_done' => empty( $total_images_to_smush ) ), 'common' ); ?>

<div class="wp-smush-bulk-wrapper sui-border-frame<?php echo empty( $total_images_to_smush ) ? ' sui-hidden' : ''; ?>">
	<div id="wp-smush-bulk-content">
		<?php WP_Smush::get_instance()->admin()->print_pending_bulk_smush_content( $total_images_to_smush, $resmush_count, $unsmushed_count ); ?>
	</div>

	<button type="button" class="wp-smush-all sui-button sui-button-blue" title="<?php esc_attr_e( 'Click to start Bulk Smushing images in Media Library', 'wp-smushit' ); ?>">
		<?php esc_html_e( 'BULK SMUSH NOW', 'wp-smushit' ); ?>
	</button>
</div>

<?php if ( ! $is_pro ) : ?>
	<?php
	if ( ! WP_Smush::is_pro() && $total_images_to_smush > \Smush\Core\Core::$max_free_bulk ) {
		/* translators: %1$s - opening <strong> tag, %2$s - closing </strong> tag, %3$s - opening <a> tag, %4$s - closing </a> tag */
		$notice = esc_html__( "Upgrade to Pro to bulk compress all images with just one click. Free users can smush 50 images per batch. As you're using the free version of Smush, we'll give you a %1\$s30%% Welcome Discount%2\$s to try the Smush Pro plugin! %3\$sUpgrade to Pro for FREE%4\$s", 'wp-smushit' );
	} else {
		/* translators: %1$s - opening <strong> tag, %2$s - closing </strong> tag, %3$s - opening <a> tag, %4$s - closing </a> tag */
		$notice = esc_html__( 'As a Smush free user we give you %1$s30%% Welcome Discount%2$s to try Smush Pro plugin! %3$sTry Pro absolutely FREE%4$s', 'wp-smushit' );
	}
	?>
	<div class="sui-notice sui-notice-purple">
		<div class="sui-notice-content">
			<div class="sui-notice-message">
				<i class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></i>
				<p>
					<?php
					printf(
						esc_html( $notice ),
						'<strong>',
						'</strong>',
						'<a href=' . esc_url( $bulk_upgrade_url ) . ' class="smush-upsell-link" target="_blank">',
						'</a>'
					);
					?>
				</p>
			</div>
		</div>
	</div>
<?php endif; ?>