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
 * @var int    $total_count                   Total count.
 */
?>
<div class="sui-notice sui-notice-warning">
    <div class="sui-notice-content">
        <div class="sui-notice-message">
            <span class="sui-notice-icon sui-icon-warning-alert sui-md" aria-hidden="true"></span>
            <p>
                <?php
                printf( /* translators: %d - number of uncompressed attachments */
                    esc_html__( 'You have %d images that needs compressing!', 'wp-smushit' ),
                    (int) $uncompressed
                );
                ?>
            </p>
        </div>
    </div>
</div>
<?php
$bg_optimization        = WP_Smush::get_instance()->core()->mod->bg_optimization;
$show_bulk_limit_notice = ! $bg_optimization->can_use_background();
if ( $show_bulk_limit_notice && $uncompressed > \Smush\Core\Core::MAX_FREE_BULK ) : ?>
    <div class="sui-notice sui-notice-upsell">
        <div class="sui-notice-content">
            <div class="sui-notice-message">
                <span class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></span>
                <p>
                    <?php
                    printf( /* translators: %1$s - opening <a> tag, %2$s - closing </a> tag, %3$s - number of images */
                        esc_html__( '%1$sUpgrade to Pro%2$s to bulk smush all images in one click. Free users can smush %3$s images per batch.', 'wp-smushit' ),
                        '<a href="' . esc_url( $upsell_url ) . '" target="_blank" class="smush-upsell-link">',
                        '</a>',
                        (int) \Smush\Core\Core::MAX_FREE_BULK
                    );
                    ?>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>
<a href="<?php echo esc_url( $this->get_url( 'smush-bulk' ) ); ?>" class="sui-button sui-button-blue wp-smush-bulk-smush-link">
    <?php esc_html_e( 'Bulk Smush', 'wp-smushit' ); ?>
</a>