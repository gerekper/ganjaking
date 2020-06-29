<?php

/**
 * Volume Pricing Table - Horizontal
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

            <tr>

                <?php if (count($data) > 1): ?>
                    <td>&nbsp;</td>
                <?php endif; ?>

                <?php foreach ($data as $single): ?>
                    <?php if ($single['table_data'] !== false): ?>
                        <?php foreach ($single['table_data'] as $range): ?>
                            <td>
                                <span class="rp_wcdpd_pricing_table_quantity <?php echo (count($data) > 1 ? 'rp_wcdpd_pricing_table_quantity_multiple' : ''); ?>" data-rp-wcdpd-from="<?php echo $range['from']; ?>">
                                    <?php echo $range['range_label']; ?>
                                </span>
                            </td>
                        <?php endforeach; ?>
                        <?php break; ?>
                    <?php endif; ?>
                <?php endforeach; ?>

            </tr>

            <?php foreach ($data as $single): ?>
                <?php if ($single['table_data'] !== false): ?>

                    <tr>

                        <?php if (count($data) > 1): ?>
                            <td>
                                <span class="rp_wcdpd_pricing_table_product_name" data-rp-wcdpd-variation-attributes="<?php echo RP_WCDPD_Promotion_Volume_Pricing_Table::get_variation_attributes_string($single['product']); ?>">
                                    <?php echo apply_filters('rp_wcdpd_volume_pricing_table_variation_attributes', wc_get_formatted_variation($single['product'], true, false), $single['product']); ?>
                                </span>
                            </td>
                        <?php endif; ?>

                        <?php foreach ($single['table_data'] as $range): ?>
                            <td>
                                <span class="amount rp_wcdpd_pricing_table_product_price" data-rp-wcdpd-from="<?php echo $range['from']; ?>" data-rp-wcdpd-variation-attributes="<?php echo RP_WCDPD_Promotion_Volume_Pricing_Table::get_variation_attributes_string($single['product']); ?>">
                                    <?php echo $range['range_price']; ?>
                                </span>
                            </td>
                        <?php endforeach; ?>

                    </tr>

                <?php endif; ?>
            <?php endforeach; ?>

        </tbody>
    </table>

    <?php do_action('rp_wcdpd_volume_pricing_table_after_table'); ?>

</div>
