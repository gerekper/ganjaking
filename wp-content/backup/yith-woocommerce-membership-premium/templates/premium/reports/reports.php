<?php
/*
 * Template for Reports Page
 */
?>
<div class="wrap yith-wcmbs-with-menu">
    <h1><?php _e( 'Reports', 'yith-woocommerce-membership' ) ?></h1>

    <ul class="yith-wcmbs-menu">
        <li data-show="#yith-wcmbs-download-reports"><?php _e( 'Downloads', 'yith-woocommerce-membership' ) ?></li>
        <li data-show="#yith-wcmbs-membership-reports"><?php _e( 'Memberships', 'yith-woocommerce-membership' ) ?></li>
    </ul>

    <div id="poststuff">
        <div id="yith-wcmbs-membership-reports">
            <?php do_action( 'yith_wcmbs_membership_reports' ); ?>
        </div>

        <div id="yith-wcmbs-download-reports">
            <?php do_action( 'yith_wcmbs_download_reports' ); ?>
        </div>
    </div>

</div>