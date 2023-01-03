<?php
/**
 * Bulk compress dashboard meta box.
 *
 * @since 3.8.6
 * @package WP_Smush
 *
 * @var int    $uncompressed                  Number of uncompressed attachments.
 * @var string $upsell_url                    Upsell URL.
 * @var bool   $background_processing_enabled Whether background processing is enabled or not.
 * @var bool   $background_in_processing      Whether BO is in processing or not.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( $background_processing_enabled ) {
	$msg = __( 'Bulk smush detects images that can be optimized and allows you to compress them in bulk in the background without any quality loss.', 'wp-smushit' );
} else {
	$msg = __( 'Bulk smush detects images that can be optimized and allows you to compress them in bulk.', 'wp-smushit' );
}
?>
<p><?php echo esc_html( $msg ); ?></p>
<?php
if ( $background_in_processing ) {
	$this->view( 'background-in-processing', array(), 'views/dashboard/bulk' );
} elseif ( 0 === $total_count ) {
	$this->view( 'media-lib-empty', array(), 'views/dashboard/bulk' );
} elseif ( 0 === $uncompressed ) {
	$this->view( 'all-images-smushed-notice', array( 'all_done' => true ), 'common' );
} else {
	$this->view( 'exists-uncompressed', array( 'uncompressed' => $uncompressed ), 'views/dashboard/bulk' );
}
