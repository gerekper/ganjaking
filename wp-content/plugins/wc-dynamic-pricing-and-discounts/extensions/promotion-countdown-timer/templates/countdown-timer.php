<?php

/**
 * Promotion - Countdown Timer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php do_action('rp_wcdpd_promotion_countdown_timer_before_timer', $product_id, $seconds_remaining); ?>

<div class="rp_wcdpd_promotion_countdown_timer" data-seconds="<?php echo $seconds_remaining; ?>">

    <?php do_action('rp_wcdpd_promotion_countdown_timer_before_label', $product_id, $seconds_remaining); ?>

    <div class="rp_wcdpd_promotion_countdown_timer_label">
        <span>
            <?php echo $label; ?>
        </span>
    </div>

    <?php do_action('rp_wcdpd_promotion_countdown_timer_after_label', $product_id, $seconds_remaining); ?>

    <div class="rp_wcdpd_promotion_countdown_timer_value">

        <div class="rp_wcdpd_promotion_countdown_timer_days">
            <span class="rp_wcdpd_promotion_countdown_timer_days_value"><?php echo $days; ?></span>
            <span class="rp_wcdpd_promotion_countdown_timer_days_label"><?php esc_html_e('DAYS', 'rp_wcdpd') ?></span>
        </div>

        <div class="rp_wcdpd_promotion_countdown_timer_hours">
            <span class="rp_wcdpd_promotion_countdown_timer_hours_value"><?php echo $hours; ?></span>
            <span class="rp_wcdpd_promotion_countdown_timer_hours_label"><?php esc_html_e('HOURS', 'rp_wcdpd') ?></span>
        </div>

        <div class="rp_wcdpd_promotion_countdown_timer_minutes">
            <span class="rp_wcdpd_promotion_countdown_timer_minutes_value"><?php echo $minutes; ?></span>
            <span class="rp_wcdpd_promotion_countdown_timer_minutes_label"><?php esc_html_e('MINUTES', 'rp_wcdpd') ?></span>
        </div>

        <div class="rp_wcdpd_promotion_countdown_timer_seconds">
            <span class="rp_wcdpd_promotion_countdown_timer_seconds_value"><?php echo $seconds; ?></span>
            <span class="rp_wcdpd_promotion_countdown_timer_seconds_label"><?php esc_html_e('SECONDS', 'rp_wcdpd') ?></span>
        </div>

    </div>

    <?php do_action('rp_wcdpd_promotion_countdown_timer_after_time', $product_id, $seconds_remaining); ?>

</div>

<?php do_action('rp_wcdpd_promotion_countdown_timer_after_timer', $product_id, $seconds_remaining); ?>
