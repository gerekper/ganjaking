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
            <?php _e('<strong>User Interface Loading</strong>', 'rp_wcdpd'); ?>
        </p>

        <p class="rp_wcdpd_preloader_text">
            <?php printf(__('This plugin uses a JavaScript-driven user interface. If this notice does not disappear in a few seconds, you should check Console for any JavaScript errors or get in touch with <a href="%s">RightPress Support</a>.', 'rp_wcdpd'), 'http://url.rightpress.net/7119279-support'); ?><br>
        </p>

        <p class="rp_wcdpd_preloader_text">
            <?php _e('Note: User interface load time depends on a number of entries. Load time will increase by a fraction with each additional entry.', 'rp_wcdpd'); ?><br>
        </p>

    </div>
</div>
