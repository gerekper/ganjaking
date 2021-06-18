<div class="wrap">
    <h2>{page_header}</h2>
    <form action="admin.php?page=woocommerce-gpf-manage-feeds&gpf_action=delete" method="POST">
        <input type="hidden" name="feed_id" value="{feed_id}">
        <?php wp_nonce_field( 'gpf_delete_feed'); ?>
        <p>
            Are you sure that you want to delete the {type} - &quot;{name}&quot;?
        </p>
        <p>
            <strong>This means that it will no-longer be available to fetch by any third parties you have shared the URL with.</strong>
        </p>
        <p>
            <a href="admin.php?page=woocommerce-gpf-manage-feeds" class="button button-primary"><?php _e( 'No, keep it', 'woocommerce_gpf' ); ?></a>
            <input type="submit" name="yes" value="<?php _e( "Yes, I'm sure. DELETE this feed", 'woocommerce_gpf' ); ?>" class="button button-secondary">
        </p>
    </form>
</div>
