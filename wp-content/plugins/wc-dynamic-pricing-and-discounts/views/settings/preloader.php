<?php

/**
 * View for preloader
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="rp_wcdpd_preloader">
    <div id="rp_wcdpd_preloader_content">

        <p class="rp_wcdpd_preloader_icon">
            <span class="dashicons dashicons-admin-generic"></span>
        </p>

        <p class="rp_wcdpd_preloader_header">
            <?php esc_html_e('User Interface Loading', 'rp_wcdpd'); ?>
        </p>

        <p class="rp_wcdpd_preloader_text">
            <?php printf(esc_html__('This plugin uses a JavaScript-driven user interface. If this notice does not disappear in a few seconds, you should check Console for any JavaScript errors or get in touch with %s.', 'rp_wcdpd'), '<a href="http://url.rightpress.net/7119279-support">' . esc_html__('RightPress Support', 'rp_wcdpd') . '</a>'); ?><br>
        </p>

        <p class="rp_wcdpd_preloader_text">
            <?php esc_html_e('Note: User interface load time depends on a number of entries. Load time will increase by a fraction with each additional entry.', 'rp_wcdpd'); ?><br>
        </p>

    </div>
</div>
