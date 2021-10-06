<?php

/**
 * Promotion - Total Saved - Table Row
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="rp_wcdpd_promotion_total_saved_div <?php echo apply_filters('rp_wcdpd_promotion_total_saved_div_classes', 'woocommerce-message'); ?>" style="display: none;">

    <div class="rp_wcdpd_promotion_total_saved_div_label">
        <span class="rp_wcdpd_promotion_total_saved_label">
            <?php echo $label; ?>
        </span>
    </div>

    <div class="rp_wcdpd_promotion_total_saved_div_amount">
        <span class="rp_wcdpd_promotion_total_saved_amount">
            <?php echo $formatted_amount; ?>
        </span>
    </div>

    <div class="rp_wcdpd_clear_both"></div>

</div>
