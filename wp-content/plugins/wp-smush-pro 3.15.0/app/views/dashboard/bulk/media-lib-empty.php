<div class="sui-notice sui-notice-info">
    <div class="sui-notice-content">
        <div class="sui-notice-message">
            <span class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></span>
            <p><?php esc_html_e( 'We haven’t found any images in your media library yet so there’s no compression to be done!', 'wp-smushit' ); ?></p>
        </div>
    </div>
</div>
<a class="sui-button sui-button-blue" href="<?php echo esc_url( admin_url( 'media-new.php' ) ); ?>">
    <?php esc_html_e( 'UPLOAD IMAGES', 'wp-smushit' ); ?>
</a>