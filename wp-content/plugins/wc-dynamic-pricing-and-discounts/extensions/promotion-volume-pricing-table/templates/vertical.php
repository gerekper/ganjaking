<?php

/**
 * Volume Pricing Table - Vertical
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="rp_wcdpd_product_page_title"><?php echo $title; ?></div>

<div class="rp_wcdpd_pricing_table">

    <?php do_action('rp_wcdpd_volume_pricing_table_before_table'); ?>

    <table>
        <tbody>

            <?php if (count($data) > 1): ?>
                <tr>

                    <td class="rp_wcdpd_longer_cell">&nbsp;</td>

                    <?php foreach ($data as $single): ?>
                        <?php if ($single['table_data'] !== false): ?>
                            <td class="rp_wcdpd_longer_cell">
                                <span class="rp_wcdpd_pricing_table_product_name" data-rp-wcdpd-variation-attributes="<?php echo RP_WCDPD_Promotion_Volume_Pricing_Table::get_variation_attributes_string($single['product']); ?>">
                                    <?php echo apply_filters('rp_wcdpd_volume_pricing_table_variation_attributes', wc_get_formatted_variation($single['product'], true, false), $single['product']); ?>
                                </span>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </tr>
            <?php endif; ?>

            <?php foreach ($data as $single): ?>
                <?php if ($single['table_data'] !== false): ?>
                    <?php foreach ($single['table_data'] as $range_key => $range): ?>

                        <tr>

                            <td class="rp_wcdpd_longer_cell">
                                <span class="rp_wcdpd_pricing_table_quantity <?php echo (count($data) > 1 ? 'rp_wcdpd_pricing_table_quantity_multiple' : ''); ?>" data-rp-wcdpd-from="<?php echo $range['from']; ?>">
                                    <?php echo $range['range_label']; ?>
                                </span>
                            </td>

                            <?php foreach ($data as $current_single): ?>
                                <?php if ($current_single['table_data'] !== false): ?>
                                    <td class="rp_wcdpd_longer_cell">
                                        <span class="amount rp_wcdpd_pricing_table_product_price" data-rp-wcdpd-from="<?php echo $range['from']; ?>" data-rp-wcdpd-variation-attributes="<?php echo RP_WCDPD_Promotion_Volume_Pricing_Table::get_variation_attributes_string($current_single['product']); ?>">
                                            <?php echo $current_single['table_data'][$range_key]['range_price']; ?>
                                        </span>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>

                        </tr>

                    <?php endforeach; ?>
                    <?php break; ?>
                <?php endif; ?>
            <?php endforeach; ?>

        </tbody>
    </table>

    <?php do_action('rp_wcdpd_volume_pricing_table_after_table'); ?>

</div>
