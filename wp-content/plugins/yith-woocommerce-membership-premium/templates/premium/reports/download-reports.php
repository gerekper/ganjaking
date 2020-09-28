<?php
/*
 * Template for Reports Page
 */
?>

<div class="postbox yith-wcmbs-reports-metabox opened">
    <h2><span><?php _e( 'Downloads', 'yith-woocommerce-membership' ) ?></span></h2>

    <div class="yith-wcmbs-reports-content">
        <?php wc_get_template( '/reports/download-reports-graphics.php', array(), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH ); ?>
    </div>
</div>

<div class="postbox yith-wcmbs-reports-metabox opened">
    <h2><span><?php _e( 'Membership download reports', 'yith-woocommerce-membership' ) ?></span></h2>

    <div class="yith-wcmbs-reports-content">
        <div class="yith-wcmbs-reports-downloads-menu-wrapper">
            <ul class="yith-wcmbs-reports-downloads-menu">
                <li><a href="#" class='active' data-type="downloads-by-product"><?php _e( 'Downloads by product', 'yith-woocommerce-membership' ) ?></a></li>
                <li><a href="#" data-type="downloads-by-user"><?php _e( 'Downloads by user', 'yith-woocommerce-membership' ) ?></a></li>
            </ul>
        </div>

        <div class="yith-wcmbs-reports-downloads-content-wrapper">
            <div id="yith-wcmbs-reports-downloads-content-downloads-by-product" class="yith-wcmbs-reports-downloads-content">
                <?php include YITH_WCMBS_TEMPLATE_PATH . '/reports/download-reports-downloads-by-product.php'; ?>
            </div>

            <div id="yith-wcmbs-reports-downloads-content-downloads-by-user" class="yith-wcmbs-reports-downloads-content" style="display:none">
                <?php include YITH_WCMBS_TEMPLATE_PATH . '/reports/download-reports-downloads-by-user.php'; ?>
            </div>
        </div>
    </div>
</div>