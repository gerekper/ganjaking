<?php

/**
 * Volume Pricing Table - Modal - Vertical
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<!-- Anchor -->
<div class="rp_wcdpd_product_page">
    <div class="rp_wcdpd_product_page_modal_link"><span><?php echo $title; ?></span></div>
</div>

<!-- Modal -->
<div class="rp_wcdpd_modal rp_wcdpd_modal_vertical">
    <?php RightPress_Help::include_extension_template('promotion-volume-pricing-table', 'vertical', RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, array('title' => $title, 'data' => $data)); ?>
</div>
