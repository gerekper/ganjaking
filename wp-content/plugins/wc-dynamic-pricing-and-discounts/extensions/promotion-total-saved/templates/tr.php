<?php

/**
 * Promotion - Total Saved - Table Row
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<tr class="rp_wcdpd_promotion_total_saved_table_row <?php echo apply_filters('rp_wcdpd_promotion_total_saved_tr_classes', ''); ?>">

    <th>
        <span class="rp_wcdpd_promotion_total_saved_label">
            <?php echo $label; ?>
        </span>
    </th>

    <td data-title="<?php echo $label; ?>">
        <span class="rp_wcdpd_promotion_total_saved_amount">
            <?php echo $formatted_amount; ?>
        </span>
    </td>
</tr>
