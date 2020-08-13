<div class="betterdocs-analytics-header-counter-wrapper <?php echo esc_attr( $class ); ?>">
    <div class="betterdocs-header-analytics-counter-wrapper">
        <div>
            <div class="betterdocs-header-analytics-counter">
                <a href="<?php echo esc_url( $views_link );?> ">
                    <span class="betterdocs-counter-icon">
                        <img src="<?php echo NOTIFICATIONX_PRO_ADMIN_URL . 'assets/images/views-icon.png'; ?>" alt="<?php _e( 'Total Views' ); ?>">
                    </span>
                    <div>
                        <span class="betterdocs-counter-number"><?php _e( $views ) ?></span>
                        <span class="betterdocs-counter-label"><?php _e( 'Total Views', 'notificationx-pro' ); ?></span>
                    </div>
                </a>
            </div>
        </div>
        <div>
            <div class="betterdocs-header-analytics-counter">
                <a href="<?php echo esc_url( $clicks_link );?> ">
                    <span class="betterdocs-counter-icon">
                        <img src="<?php echo BETTERDOCS_PRO_ADMIN_URL . 'assets/images/clicks-icon.png'; ?>" alt="<?php _e( 'Total Clicks' ); ?>">
                    </span>
                    <div>
                        <span class="betterdocs-counter-number"><?php _e( $clicks ) ?></span>
                        <span class="betterdocs-counter-label"><?php _e( 'Total Clicks', 'notificationx-pro' ); ?></span>
                    </div>
                </a>
            </div>
        </div>
        <div>
            <div class="betterdocs-header-analytics-counter">
                <a href="<?php echo esc_url( $ctr_link );?> ">
                    <span class="betterdocs-counter-icon">
                        <img src="<?php echo NOTIFICATIONX_PRO_ADMIN_URL . 'assets/images/ctr-icon.png'; ?>" alt="<?php _e( 'Click-Through-Rate' ); ?>">
                    </span>
                    <div>
                        <span class="betterdocs-counter-number"><?php _e( $ctr ) ?></span>
                        <span class="betterdocs-counter-label"><?php _e( 'Click-Through-Rate', 'notificationx-pro' ); ?></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
