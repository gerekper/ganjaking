<tr class="show_if_control_variations warranty-variation">
    <td>
        <label for="variable_product_warranty_default_<?php echo $loop; ?>">
            <input type="checkbox" class="checkbox warranty_default_checkbox" value="yes" data-id="<?php echo $loop; ?>" name="variable_product_warranty_default[<?php echo $loop; ?>]" id="variable_product_warranty_default_<?php echo $loop; ?>" <?php checked(true, $warranty_default); ?> />
        </label>
    </td>
</tr>

<tr class="show_if_control_variations" data-loop="<?php echo $loop; ?>">
    <td>
        <label for="variable_product_warranty_type_<?php echo $loop; ?>"><?php _e('Product Warranty', 'wc_warranty'); ?></label>

        <select id="variable_product_warranty_type_<?php echo $loop; ?>" name="variable_product_warranty_type[<?php echo $loop; ?>]" class="select warranty_<?php echo $loop; ?> variable-warranty-type">
            <option value="no_warranty" <?php if ($warranty['type'] == 'no_warranty') echo 'selected'; ?>><?php _e('No Warranty', 'wc_warranty'); ?></option>
            <option value="included_warranty" <?php if ($warranty['type'] == 'included_warranty') echo 'selected'; ?>><?php _e('Warranty Included', 'wc_warranty'); ?></option>
            <option value="addon_warranty" <?php if ($warranty['type'] == 'addon_warranty') echo 'selected'; ?>><?php _e('Warranty as Add-On', 'wc_warranty'); ?></option>
        </select>
    </td>
    <td>
        <label for="variable_warranty_label_<?php echo $loop; ?>"><?php _e('Warranty Label', 'wc_warranty'); ?></label>
        <input type="text" id="variable_warranty_label_<?php echo $loop; ?>" name="variable_warranty_label[<?php echo $loop; ?>]" value="<?php echo esc_attr($warranty_label); ?>" class="input-text sized warranty_<?php echo $loop; ?>" />
    </td>
</tr>

<tr class="variable_show_if_included_warranty_<?php echo $loop; ?> show_if_control_variations">
    <td>
        <label for="variable_included_warranty_length_<?php echo $loop; ?>"><?php _e('Warranty Length', 'wc_warranty'); ?></label>

        <select id="variable_included_warranty_length_<?php echo $loop; ?>" name="variable_included_warranty_length[<?php echo $loop; ?>]" class="select short warranty_<?php echo $loop; ?> variable-included-warranty-length">
            <option value="lifetime" <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'lifetime') echo 'selected'; ?>><?php _e('Lifetime', 'wc_warranty'); ?></option>
            <option value="limited" <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'limited') echo 'selected'; ?>><?php _e('Limited', 'wc_warranty'); ?></option>
        </select>
    </td>

    <td>
        <div class="variable_limited_warranty_length_field_<?php echo $loop; ?>">
            <label for="variable_limited_warranty_length_value_<?php echo $loop; ?>"><?php _e('Warranty Duration', 'wc_warranty'); ?></label>
            <input type="text" class="input-text sized warranty_<?php echo $loop; ?> variable-limited-warranty-length-value" size="3" style="width: 50px;" name="variable_limited_warranty_length_value[<?php echo $loop; ?>]" value="<?php if ($warranty['type'] == 'included_warranty') echo $warranty['value']; ?>" />
            <select name="variable_limited_warranty_length_duration[<?php echo $loop; ?>]" class=" warranty_<?php echo $loop; ?>" style="width: auto !important;">
                <option value="days" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'days') echo 'selected'; ?>><?php _e('Days', 'wc_warranty'); ?></option>
                <option value="weeks" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'weeks') echo 'selected'; ?>><?php _e('Weeks', 'wc_warranty'); ?></option>
                <option value="months" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'months') echo 'selected'; ?>><?php _e('Months', 'wc_warranty'); ?></option>
                <option value="years" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'years') echo 'selected'; ?>><?php _e('Years', 'wc_warranty'); ?></option>
            </select>
        </div>
    </td>
</tr>

<tr class="variable_show_if_addon_warranty_<?php echo $loop; ?> show_if_control_variations">
    <td colspan="2">
        <p>
            <label for="variable_addon_no_warranty_<?php echo $loop; ?>">
                <input type="checkbox" name="variable_addon_no_warranty[<?php echo $loop; ?>]" id="variable_addon_no_warranty_<?php echo $loop; ?>" value="yes" <?php if (isset($warranty['no_warranty_option']) && $warranty['no_warranty_option'] == 'yes') echo 'checked'; ?> class="checkbox warranty_<?php echo $loop; ?>" />
                <?php _e( '"No Warranty" option', 'wc_warranty'); ?>
            </label>
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
                    <a href="#" class="button btn-add-warranty-variable" data-loop="<?php echo $loop; ?>"><?php _e('Add Row', 'wc_warranty'); ?></a>
                </th>
            </tr>
            </tfoot>
            <tbody id="variable_warranty_addons_<?php echo $loop; ?>">
            <?php
            if ( isset($warranty['addons']) ) foreach ( $warranty['addons'] as $addon ):
                ?>
                <tr>
                    <td valign="middle">
                        <span class="input"><b>+</b> <?php echo $currency; ?></span>
                        <input type="text" name="variable_addon_warranty_amount[<?php echo $loop; ?>][]" class="input-text sized warranty_<?php echo $loop; ?>" size="4" value="<?php echo $addon['amount']; ?>" style="width: 50px;" />
                    </td>
                    <td valign="middle">
                        <input type="text" class="input-text sized warranty_<?php echo $loop; ?>" size="3" name="variable_addon_warranty_length_value[<?php echo $loop; ?>][]" value="<?php echo $addon['value']; ?>" />
                        <select name="variable_addon_warranty_length_duration[<?php echo $loop; ?>][]" class=" warranty_<?php echo $loop; ?>">
                            <option value="days" <?php if ($addon['duration'] == 'days') echo 'selected'; ?>><?php _e('Days', 'wc_warranty'); ?></option>
                            <option value="weeks" <?php if ($addon['duration'] == 'weeks') echo 'selected'; ?>><?php _e('Weeks', 'wc_warranty'); ?></option>
                            <option value="months" <?php if ($addon['duration'] == 'months') echo 'selected'; ?>><?php _e('Months', 'wc_warranty'); ?></option>
                            <option value="years" <?php if ($addon['duration'] == 'years') echo 'selected'; ?>><?php _e('Years', 'wc_warranty'); ?></option>
                        </select>
                    </td>
                    <td><a class="button warranty_addon_remove warranty_addon_remove_variable_<?php echo $loop; ?>" data-loop="<?php echo $loop; ?>" href="#">&times;</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    </td>
</tr>