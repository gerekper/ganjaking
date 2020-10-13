<style type="text/css">
    span.input {float: left; margin-top: 4px;}
    p.addon-row {margin-left: 25px;}
</style>
<div id="warranty_product_data" class="panel woocommerce_options_panel">

    <div class="options_group show_if_variable">
        <p class="form-field">
            <label for="variable_warranty_control">
                <?php _e('Warranty Control', 'wc_warranty'); ?>
            </label>
            <select id="variable_warranty_control" name="variable_warranty_control">
                <option value="parent" <?php selected( $control_type, 'parent' ); ?>><?php _e('Define warranty for all variations', 'wc_warranty'); ?></option>
                <option value="variations" <?php selected( $control_type, 'variations' ); ?>><?php _e('Define warranty per variation', 'wc_warranty'); ?></option>
            </select>
        </p>
    </div>

    <div class="options_group grouping hide_if_control_variations">
        <p class="form-field">
            <label for="product_warranty_default">
                <?php _e('Default Product Warranty', 'wc_warranty'); ?>
            </label>
            <input type="checkbox" name="product_warranty_default" id="product_warranty_default" <?php checked(true, $default_warranty); ?> value="yes" />
        </p>

        <p class="form-field product_warranty_type_field">
            <label for="product_warranty_type"><?php _e('Product Warranty', 'wc_warranty'); ?></label>

            <select id="product_warranty_type" name="product_warranty_type" class="select warranty_field">
                <option value="no_warranty" <?php if ($warranty['type'] == 'no_warranty') echo 'selected'; ?>><?php _e('No Warranty', 'wc_warranty'); ?></option>
                <option value="included_warranty" <?php if ($warranty['type'] == 'included_warranty') echo 'selected'; ?>><?php _e('Warranty Included', 'wc_warranty'); ?></option>
                <option value="addon_warranty" <?php if ($warranty['type'] == 'addon_warranty') echo 'selected'; ?>><?php _e('Warranty as Add-On', 'wc_warranty'); ?></option>
            </select>
        </p>

        <p class="form-field show_if_included_warranty show_if_addon_warranty">
            <label for="warranty_label"><?php _e('Warranty Label', 'wc_warranty'); ?></label>

            <input type="text" name="warranty_label" value="<?php echo esc_attr($warranty_label); ?>" class="input-text sized warranty_field" />
        </p>
    </div>

    <div class="options_group grouping show_if_included_warranty hide_if_control_variations">
        <p class="form-field included_warranty_length_field">
            <label for="included_warranty_length"><?php _e('Warranty Length', 'wc_warranty'); ?></label>

            <select id="included_warranty_length" name="included_warranty_length" class="select short warranty_field">
                <option value="lifetime" <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'lifetime') echo 'selected'; ?>><?php _e('Lifetime', 'wc_warranty'); ?></option>
                <option value="limited" <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'limited') echo 'selected'; ?>><?php _e('Limited', 'wc_warranty'); ?></option>
            </select>
        </p>

        <p class="form-field limited_warranty_length_field">
            <label for="limited_warranty_length_value"><?php _e('Warranty Duration', 'wc_warranty'); ?></label>
            <input type="text" class="input-text sized warranty_field" size="3" name="limited_warranty_length_value" value="<?php if ($warranty['type'] == 'included_warranty') echo $warranty['value']; ?>" />
            <select name="limited_warranty_length_duration" class=" warranty_field">
                <option value="days" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'days') echo 'selected'; ?>><?php _e('Days', 'wc_warranty'); ?></option>
                <option value="weeks" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'weeks') echo 'selected'; ?>><?php _e('Weeks', 'wc_warranty'); ?></option>
                <option value="months" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'months') echo 'selected'; ?>><?php _e('Months', 'wc_warranty'); ?></option>
                <option value="years" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'years') echo 'selected'; ?>><?php _e('Years', 'wc_warranty'); ?></option>
            </select>
        </p>
    </div>

    <div class="options_group grouping show_if_addon_warranty hide_if_control_variations">
        <p class="form-field">
            <label for="addon_no_warranty">
                <?php _e( '"No Warranty" option', 'wc_warranty'); ?>
            </label>
            <input type="checkbox" name="addon_no_warranty" id="addon_no_warranty" value="yes" <?php if (isset($warranty['no_warranty_option']) && $warranty['no_warranty_option'] == 'yes') echo 'checked'; ?> class="checkbox warranty_field" />
        </p>

        <table class="widefat">
            <thead>
            <tr>
                <th><?php _e('Cost', 'wc_warranty'); ?></th>
                <th><?php _e('Duration', 'wc_warranty'); ?></th>
                <th width="50">&nbsp;</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th colspan="3">
                    <a href="#" class="button btn-add-warranty"><?php _e('Add Row', 'wc_warranty'); ?></a>
                </th>
            </tr>
            </tfoot>
            <tbody id="warranty_addons">
            <?php
            if ( isset($warranty['addons']) ) foreach ( $warranty['addons'] as $addon ):
                ?>
                <tr>
                    <td valign="middle">
                        <span class="input"><b>+</b> <?php echo $currency; ?></span>
                        <input type="text" name="addon_warranty_amount[]" class="input-text sized warranty_field" size="4" value="<?php echo $addon['amount']; ?>" />
                    </td>
                    <td valign="middle">
                        <input type="text" class="input-text sized warranty_field" size="3" name="addon_warranty_length_value[]" value="<?php echo $addon['value']; ?>" />
                        <select name="addon_warranty_length_duration[]" class=" warranty_field">
                            <option value="days" <?php if ($addon['duration'] == 'days') echo 'selected'; ?>><?php _e('Days', 'wc_warranty'); ?></option>
                            <option value="weeks" <?php if ($addon['duration'] == 'weeks') echo 'selected'; ?>><?php _e('Weeks', 'wc_warranty'); ?></option>
                            <option value="months" <?php if ($addon['duration'] == 'months') echo 'selected'; ?>><?php _e('Months', 'wc_warranty'); ?></option>
                            <option value="years" <?php if ($addon['duration'] == 'years') echo 'selected'; ?>><?php _e('Years', 'wc_warranty'); ?></option>
                        </select>
                    </td>
                    <td><a class="button warranty_addon_remove" href="#">&times;</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>