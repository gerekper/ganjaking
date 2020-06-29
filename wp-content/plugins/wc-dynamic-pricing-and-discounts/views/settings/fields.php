<?php

/**
 * View for Settings page fields
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="rp_wcdpd_settings">
    <div class="rp_wcdpd_settings_container">

        <input type="hidden" name="current_tab" value="<?php echo $current_tab; ?>" />
        <?php settings_fields('rp_wcdpd_settings_group_' . $current_tab); ?>

        <?php if (RP_WCDPD_Settings::settings_page_uses_templates()): ?>

            <div class="rp_wcdpd_rules_header">
                <div class="rp_wcdpd_rules_title">
                    <h2>
                        <?php echo RP_WCDPD_Settings::get_tab_title($current_tab); ?>
                    </h2>
                </div>
                <div class="rp_wcdpd_rules_settings">
                    <?php RP_WCDPD_Settings::print_settings_field($current_tab . '_rule_selection_method'); ?>
                    <div class="rp_wcdpd_top_rule_setting_separator">&nbsp;</div>
                    <?php RP_WCDPD_Settings::print_settings_field($current_tab . '_total_limit'); ?>
                    <?php RP_WCDPD_Settings::print_settings_field($current_tab . '_total_limit_value'); ?>
                </div>
                <div style="reset: both;"></div>
            </div>

            <div id="rp_wcdpd_<?php echo $current_tab; ?>" class="rp_wcdpd_rules"></div>

        <?php else: ?>
            <?php do_settings_sections('rp-wcdpd-admin-' . str_replace('_', '-', $current_tab)); ?>
        <?php endif; ?>

        <div></div>
        <?php submit_button(); ?>

    </div>
</div>
