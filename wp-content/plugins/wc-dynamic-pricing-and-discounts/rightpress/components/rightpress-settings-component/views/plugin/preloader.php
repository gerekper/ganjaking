<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * View for preloader
 */

?>

<div id="rightpress-plugin-settings-preloader">
    <div id="rightpress-plugin-settings-preloader-content">

        <p class="rightpress-plugin-settings-preloader-icon">
            <span class="dashicons dashicons-admin-generic"></span>
        </p>

        <p class="rightpress-plugin-settings-preloader-header">
            <?php _e('<strong>User Interface Loading</strong>', 'rightpress'); ?>
        </p>

        <p class="rightpress-plugin-settings-preloader-text">
            <?php printf(__('This plugin uses a JavaScript-driven user interface. If this notice does not disappear within a few seconds, you should check Console for any JavaScript errors or get in touch with <a href="%s">RightPress Support</a>.', 'rightpress'), 'http://url.rightpress.net/support-site'); ?><br>
        </p>

        <?php do_action(($this->get_plugin_private_prefix() . 'settings_after_preloader_content')); ?>

    </div>
</div>
