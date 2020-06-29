<?php

/**
 * View for Settings page header (tabs)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<h2 style="padding: 0; margin: 0; height: 0;">
    <!-- Fix for WordPress notices jumping in between header and settings area -->
</h2>

<h2 class="rp_wcdpd_tabs_container nav-tab-wrapper">
    <?php foreach (RP_WCDPD_Settings::get_structure() as $tab_key => $tab): ?>
        <?php if (RP_WCDPD_Settings::tab_has_settings($tab)): ?>
            <a class="nav-tab <?php echo ($tab_key == $current_tab ? 'nav-tab-active' : ''); ?>" href="admin.php?page=rp_wcdpd_settings&tab=<?php echo $tab_key; ?>"><?php echo $tab['title']; ?></a>
        <?php endif; ?>
    <?php endforeach; ?>
</h2>
