<style>
    .woocommerce-gpf-admin-notices-feedback.notice p a:not(:first-child) {margin-left: 0.8em; font-size: 85%; min-height: auto;}
    .woocommerce-gpf-admin-notices-feedback div {display:flex;justify-content: space-between;}
</style>
<div class="woocommerce-gpf-admin-notices-feedback notice notice-info">
    <p><?php esc_html_e("It looks like you've been using the Google Product Feed extension for a while now. We'd love to know how you're finding it?", 'woocommerce_gpf' ); ?></p>
    <p><?php esc_html_e("Please help us out by letting us know how you're getting on.", 'woocommerce_gpf'); ?></p>
    <div>
        <p>
            <a href="https://woocommerce.com/products/google-product-feed/#reviews" target="_blank" rel="noopener noreferrer" class="button button-primary ademti-dismiss" data-ademti-notice-slug="woocommerce-gpf-feedback">
                <strong>
                <?php esc_html_e( 'Like it? Leave a review!', 'woocommerce_gpf' ); ?>
                </strong>
            </a>
            <a href="https://woocommerce.com/my-account/create-a-ticket/" target="_blank" rel="noopener noreferrer" class="button ademti-dismiss" data-ademti-notice-slug="woocommerce-gpf-feedback">
                <?php esc_html_e( 'Having problems? Get in touch&hellip;', 'woocommerce_gpf' ); ?>
            </a>
            <a class="button ademti-snooze" data-ademti-notice-slug="woocommerce-gpf-feedback">
                <?php esc_html_e( "Remind me later", 'woocommerce_gpf' ); ?>
            </a>
        </p>
        <p style="text-align: right;margin-top: auto;">
            <small>
                <a class="ademti-dismiss" data-ademti-notice-slug="woocommerce-gpf-feedback" style="cursor: pointer; color: inherit;">
                    <?php esc_html_e( "Don't ask again", 'woocommerce_gpf' ); ?>
                </a>
            </small>
        </p>
    </div>
</div>
