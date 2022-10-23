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