<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Settings page header view
 */

?>

<h2 class="rightpress-plugin-settings-notices-fix">
    <!-- Fix for WordPress notices jumping in between header and settings area -->
</h2>

<h2 id="rightpress-plugin-settings-tabs" class="nav-tab-wrapper">
    <?php foreach ($this->get_structure() as $tab_key => $tab): ?>
        <?php if (RightPress_Plugin_Settings::tab_has_settings($tab)): ?>
            <a class="nav-tab <?php echo ($tab_key == $current_tab ? 'nav-tab-active' : ''); ?>" href="<?php echo $this->get_settings_page_url(array('tab' => $tab_key)); ?>"><?php echo $tab['title']; ?></a>
        <?php endif; ?>
    <?php endforeach; ?>
</h2>
