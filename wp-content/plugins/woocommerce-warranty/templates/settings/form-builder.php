<tr valign="top">
    <td colspan="2">
        <div class="warranty_form">
            <ul id="warranty_form">
                <?php
                foreach($inputs as $input):
                    $src    = '';
                    $key    = $input->key;
                    $type   = $input->type;

                    if ( array_key_exists($key, $form['fields']) ) {
                        $field = $form['fields'][$key];

                        $src = '<div class="wfb-field wfb-field-'. $type .'" data-key="'. $key .'" id="wfb-field-'. $key .'">
                                            <div class="wfb-field-title">
                                                <h3>'. $types[$type]['label'] .'</h3>

                                                <div class="wfb-field-controls">
                                                    <a class="toggle-field wfb-toggle" data-key="'. $key .'" href="#">&#9652;</a>
                                                    <a class="remove-field wfb-remove" href="#">&times;</a>
                                                </div>
                                            </div>

                                            <div class="wfb-content" id="wfb-content-'. $key .'">
                                                <div class="wfb-field-content">
                                                    <table class="form-table">
                                ';

                        $options = explode('|', $types[$type]['options']);

                        foreach ( $options as $option ) {
                            $src .= '<tr>';

                            $value = (isset($field[$option])) ? $field[$option] : '';

                            if ($option == "name") {
                                $src .= '<th>Name <img class="help_tip" data-tip="'. wc_sanitize_tooltip( WooCommerce_Warranty::$tips[$option] ) .'" src="'. plugins_url() .'/woocommerce/assets/images/help.png" height="16" width="16" /></th><td><input type="text" name="fb_field['. $key .'][name]" value="'. $value .'" /></td>';
                            } elseif ($option == "label") {
                                $src .= '<th>Label <img class="help_tip" data-tip="'. wc_sanitize_tooltip( WooCommerce_Warranty::$tips[$option] ) .'" src="'. plugins_url() .'/woocommerce/assets/images/help.png" height="16" width="16" /></th><td><input type="text" name="fb_field['. $key .'][label]" value="'. $value .'" /></td>';
                            } elseif ($option == "text") {
                                $src .= '<th>Text</th><td><textarea name="fb_field['. $key .'][text]" rows="5" cols="35">'. $value .'</textarea></td>';
                            } elseif ($option == "default") {
                                $src .= '<th>Default Value <img class="help_tip" data-tip="'. wc_sanitize_tooltip( WooCommerce_Warranty::$tips[$option] ) .'" src="'. plugins_url() .'/woocommerce/assets/images/help.png" height="16" width="16" /></th><td><input type="text" name="fb_field['. $key .'][default]" value="'. $value .'" /></td>';
                            } elseif ($option == "rowscols") {
                                $rows = (isset($field['rows'])) ? $field['rows'] : '';
                                $cols = (isset($field['cols'])) ? $field['cols'] : '';
                                $src .= '<th>Size</th><td><input type="text" size="2" name="fb_field['. $key .'][rows]" value="'. $rows .'" /><span class="description">Rows</span> <input type="text" size="2" name="fb_field['. $key .'][cols]" value="'. $cols .'" /><span class="description">Columns</span>';
                            } elseif ($option == "options") {
                                $src .= '<th>Options <img class="help_tip" data-tip="'. wc_sanitize_tooltip( WooCommerce_Warranty::$tips[$option] ) .'" src="'. plugins_url() .'/woocommerce/assets/images/help.png" height="16" width="16" /></th><td><textarea name="fb_field['. $key .'][options]" rows="3" cols="35">'. $value .'</textarea></td>';
                            } elseif ($option == "multiple") {
                                $checked = ($value == 'yes') ? 'checked' : '';
                                $src .= '<th>Allow Multiple <img class="help_tip" data-tip="'. wc_sanitize_tooltip( WooCommerce_Warranty::$tips[$option] ) .'" src="'. plugins_url() .'/woocommerce/assets/images/help.png" height="16" width="16" /></th><td><input type="checkbox" name="fb_field['. $key .'][multiple]" value="yes" '. $checked .' /></td>';
                            } elseif ($option == "required") {
                                $checked = ($value == 'yes') ? 'checked' : '';
                                $src .= '<th>Required <img class="help_tip" data-tip="'. wc_sanitize_tooltip( WooCommerce_Warranty::$tips[$option] ) .'" src="'. plugins_url() .'/woocommerce/assets/images/help.png" height="16" width="16" /></th><td><input type="checkbox" name="fb_field['. $key .'][required]" value="yes" '. $checked .' /></td>';
                            }

                            $src .= '</tr>';
                        }

                        $src .= '       </table>
                                            </div>
                                        </div>';
                        echo '<li class="wfb-field" data-key="'. $key .'" data-type="'. $type .'">'. $src .'</li>';

                    }
                    ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="warranty_fields">
            <h4><?php _e('Available Form Fields', 'wc_warranty'); ?></h4>

            <ul id="warranty_form_fields">
                <?php foreach ( $types as $key => $type ): ?>
                    <li><a class="control button" href="#" data-type="<?php echo $key; ?>" data-options="<?php echo $type['options']; ?>"><?php echo $type['label']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <input type="hidden" name="form_fields" id="form_fields" value="<?php echo esc_attr($form['inputs']); ?>" />
    </td>
</tr>