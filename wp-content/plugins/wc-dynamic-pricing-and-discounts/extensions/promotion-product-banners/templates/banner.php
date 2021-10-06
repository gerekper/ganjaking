<?php

/**
 * Promotion - Product Banners - Banner
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="rp_wcdpd_promotion_product_banners_banner">

    <?php do_action('rp_wcdpd_promotion_product_banners_before_banner_title'); ?>

    <?php if ($title): ?>
        <div class="rp_wcdpd_promotion_product_banners_banner_title"><?php echo $title; ?></div>
    <?php endif; ?>

    <?php do_action('rp_wcdpd_promotion_product_banners_after_banner_title'); ?>

    <div class="rp_wcdpd_promotion_product_banners_banner_content_container">

        <?php do_action('rp_wcdpd_promotion_product_banners_before_banner_content'); ?>

        <div class="rp_wcdpd_promotion_product_banners_banner_content">

            <?php echo $message; ?>

        </div>

        <?php do_action('rp_wcdpd_promotion_product_banners_after_banner_content'); ?>

    </div>

</div>
