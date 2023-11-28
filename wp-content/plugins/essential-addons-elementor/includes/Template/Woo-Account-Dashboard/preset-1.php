<?php
/**
 * Template Name: Preset 1
 */
?>
<div class="eael-account-dashboard-wrapper preset-1">
    <div class="eael-account-dashboard-body">
        <div class="eael-account-dashboard-container">
            <?php $this->get_account_dashboard_navbar($current_user); ?>
            <?php $this->get_account_dashboard_content($current_user, $is_editor); ?>
        </div>
    </div>
</div>