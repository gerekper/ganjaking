<?php
/**
 * Bulk Smush meta box.
 *
 * @since 2.9.0
 * @package WP_Smush
 *
 * @var Smush\Core\Core $core                           Instance of Smush\Core\Core
 * @var bool            $is_pro                         Check if PRO user or not.
 * @var integer         $unsmushed_count                Count of the images that need smushing.
 * @var integer         $resmush_count                  Count of the images that need re-smushing.
 * @var integer         $total_images_to_smush          Total count of all images to smush. Unsmushed images + images to re-smush.
 * @var string          $bulk_upgrade_url               Bulk Smush upgrade to PRO url.
 * @var bool            $background_processing_enabled  Background optimization is enabled.
 * @var bool            $background_in_processing       Background optimization in progressing or not.
 * @var string          $background_in_processing_notice
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( 0 !== absint( $core->total_count ) ) :
	if ( $background_processing_enabled ) {
		$msg = __( 'Bulk smush detects images that can be optimized and allows you to compress them in bulk in the background without any quality loss.', 'wp-smushit' );
	} else {
		$msg = __( 'Bulk smush detects images that can be optimized and allows you to compress them in bulk.', 'wp-smushit' );
	}
	?>
<p><?php echo esc_html( $msg ); ?></p>
<?php endif; ?>

<?php
// If there are no images in media library.
if ( 0 === absint( $core->total_count ) ) {
	$this->view( 'media-lib-empty', array(), 'views/bulk' );
	return;
}
// Progress bar.
$this->view(
	'progress-bar',
	array(
		'count'                           => $total_images_to_smush,
		'background_in_processing_notice' => $background_in_processing_notice,
		'background_processing_enabled'   => $background_processing_enabled,
	),
	'common'
);

// All images are smushed.
$this->view( 'all-images-smushed-notice', array( 'all_done' => empty( $total_images_to_smush ) ), 'common' );

// List errors.
$this->view( 'list-errors', array(), 'views/bulk' );

?>
<div class="wp-smush-bulk-wrapper sui-border-frame<?php echo empty( $total_images_to_smush ) || $background_in_processing ? ' sui-hidden' : ''; ?>">
	<div id="wp-smush-bulk-content">
		<?php WP_Smush::get_instance()->admin()->print_pending_bulk_smush_content( $total_images_to_smush, $resmush_count, $unsmushed_count ); ?>
	</div>
	<?php $bulk_smush_class = $background_processing_enabled ? 'wp-smush-bo-start' : 'wp-smush-all'; ?>
	<button type="button" class="<?php echo esc_attr( $bulk_smush_class ); ?> sui-button sui-button-blue" title="<?php esc_attr_e( 'Click to start Bulk Smushing images in Media Library', 'wp-smushit' ); ?>">
		<?php esc_html_e( 'BULK SMUSH NOW', 'wp-smushit' ); ?>
	</button>
</div>
<?php
if ( ! $is_pro ) {
	$this->view(
		'cdn-upsell',
		array(
			'background_in_processing' => $background_in_processing,
			'bulk_upgrade_url'         => $bulk_upgrade_url,
		),
		'views/bulk'
	);
}
