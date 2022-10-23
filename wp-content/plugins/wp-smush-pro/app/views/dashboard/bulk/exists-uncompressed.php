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
<a href="<?php echo esc_url( $this->get_url( 'smush-bulk' ) ); ?>" class="sui-button sui-button-blue">
    <?php esc_html_e( 'Bulk Smush', 'wp-smushit' ); ?>
</a>