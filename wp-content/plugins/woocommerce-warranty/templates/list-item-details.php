<tr id="inline-edit-<?php echo $request['ID']; ?>" class="inline-edit-row inline-edit-row-post inline-edit-product quick-edit-row quick-edit-row-post inline-edit-product" style="display: none">
    <td colspan="<?php echo $this->num_columns; ?>" class="colspanchange">
        <div class="warranty-update-message warranty-updated hidden"><p></p></div>

        <fieldset class="inline-edit-col">
            <div class="warranty-comments" style="float: right;">
                <h4><?php _e('Admin Notes', 'wc_warranty'); ?></h4>

                <ul class="admin-notes">
                    <?php
                    include WooCommerce_Warranty::$base_path .'templates/list-item-notes.php';
                    ?>
                </ul>
                <div class="add-note">
                    <h4><?php _e('Add Note', 'wc_warranty'); ?></h4>

                    <p>
                        <textarea rows="3" cols="35" class="input-text" id="admin_note_<?php echo $request['ID']; ?>" name="" type="text"></textarea>
                    </p>

                    <p>
                        <a class="add_note button" data-request="<?php echo esc_attr( $request['ID'] ); ?>" href="#"><?php _e('Add', 'wc_warranty'); ?></a>
                    </p>
                </div>
            </div>

            <div class="inline-edit-col">
                <div class="codes_form closeable">
                    <?php if ( !empty( $this->inputs ) ): ?>
                        <h4><?php _e('Additional Details', 'wc_warranty'); ?></h4>
                        <?php
                        foreach ( $this->inputs as $input ) {
                            $key    = $input->key;
                            $type   = $input->type;
                            $field  = $this->form['fields'][$input->key];

                            if ( $type == 'paragraph' ) {
                                continue;
                            }

                            $value = get_post_meta( $request['ID'], '_field_'.$key, true );

                            if ( is_array($value) )
                                $value = implode( ',<br/>', $value );

                            if ($type == 'file' && !empty($value)) {
                                $wp_uploads = wp_upload_dir();
                                $value = '<a href="'. $wp_uploads['baseurl'] . $value .'">'. basename($value) .'</a>';
                            }


                            if ( empty( $value ) && !empty( $item['reason'] ) && !$this->row_reason_injected ) {
                                $value = $item['reason'];
                                $this->row_reason_injected = true;
                            }

                            if (! $value )
                                $value = '-';

                            ?>
                            <p>
                                <strong><?php echo $field['name']; ?></strong>
                                <br/>
                                <?php echo wp_kses_post($value); ?>
                            </p>
                        <?php
                        }
                        ?>
                    <?php endif; ?>

                    <h4><?php _e('Return shipping details', 'wc_warranty'); ?></h4>
                    <?php

                    $shipping_label_id = get_post_meta( $request['ID'], '_warranty_shipping_label', true );

                    if ( $shipping_label_id ) {
                        $lnk = wp_get_attachment_url( $shipping_label_id );
                        echo '<a href="'  .$lnk  .'"><strong>Download the Shipping Label</strong></a>';
                    } else {
                        ?>
                        <input name="shipping_label_image" id="shipping_label_<?php echo $request['ID']; ?>" class="shipping-label-url short-text" type="text" value="" />
                        <input name="shipping_label_image_id" id="shipping_label_id_<?php echo $request['ID']; ?>" type="hidden" value="" />
                        <input class="rma-upload-button button" type="button" data-id="<?php echo $request['ID']; ?>" data-uploader_title="<?php _e('Set Shipping Label', 'wc_warranty'); ?>" data-uploader_button_text="<?php _e('Set Shipping Label', 'wc_warranty'); ?>" value="<?php _e('Select Shipping Label', 'wc_warranty'); ?>" />
                    <?php
                    } // End final If Checking the attachment :)
                    ?>
                </div>
            </div>

            <div class="inline-edit-col warranty-tracking">
                <h4><?php _e('Return tracking details', 'wc_warranty'); ?></h4>

                <?php
                // if tracking code is being requested, notify the admin
                $class = 'hidden';
                if ( $request['request_tracking_code'] == 'y' && empty($request['tracking_code']) ):
                    $class = '';
                endif;
                ?>
                <div class="codes_form closeable">
                    <div class="wc-tracking-requested wc-updated <?php echo $class; ?>"><p><?php _e('Tracking information requested from customer', 'wc_warranty'); ?></p></div>

                    <?php
                    // Tracking code hasnt been requested yet
                    if ($request['request_tracking_code'] != 'y'):
                        ?>
                        <div class="request-tracking-div">
                            <label>
                                <input type="checkbox" name="request_tracking" value="1" />
                                <strong><?php _e('Request tracking code from the Customer', 'wc_warranty'); ?></strong>
                            </label>
                        </div>
                    <?php
                    else: // tracking code requested
                        // if tracking code is not empty, it has already been provided
                        if (! empty($request['tracking_code']) ) {
                            echo '<strong>'. __('Customer Provided Tracking', 'wc_warranty') .':</strong>&nbsp;';

                            if ( !empty( $request['tracking_provider'] ) ) {
                                $all_providers = array();

                                foreach ( WooCommerce_Warranty::get_providers() as $providers ) {
                                    foreach ( $providers as $provider => $format ) {
                                        $all_providers[sanitize_title( $provider )] = $format;
                                    }
                                }

                                $provider   = $request['tracking_provider'];
                                $link       = $all_providers[$provider];
                                $link       = str_replace('%1$s', $request['tracking_code'], $link);
                                $link       = str_replace('%2$s', '', $link);
                                printf( __('%s via %s (<a href="'. $link .'" target="_blank">Track Shipment</a>)', 'wc_warranty'), $request['tracking_code'], $provider, $link );
                            } else {
                                echo $request['tracking_code'];
                            }
                        }
                    endif;
                    ?>
                </div>

                <div class="codes_form closeable">
                    <div class="wc-tracking-saved wc-updated hidden"><p><?php _e('Shipping/Tracking data saved', 'wc_warranty'); ?></p></div>
                    <?php
                    if ( !empty( $request['return_tracking_provider'] ) ): ?>
                        <p>
                            <label for="return_tracking_provider_<?php echo $request['ID']; ?>"><strong><?php _e('Shipping Provider', 'wc_warranty'); ?></strong></label>
                            <select class="return_tracking_provider" name="return_tracking_provider" id="return_tracking_provider_<?php echo $request['ID']; ?>">
                                <?php
                                foreach ( WooCommerce_Warranty::get_providers() as $provider_group => $providers ) {
                                    echo '<optgroup label="' . $provider_group . '">';
                                    foreach ( $providers as $provider => $url ) {
                                        $selected = (sanitize_title($provider) == $request['return_tracking_provider']) ? 'selected' : '';
                                        echo '<option value="' . sanitize_title( $provider ) . '" '. $selected .'>' . $provider . '</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>
                        </p>
                        <p>
                            <label for="return_tracking_code_<?php echo $request['ID']; ?>"><strong><?php _e('Tracking details ', 'wc_warranty'); ?></strong></label>
                            <input type="text" class="tracking_code regular-text" name="return_tracking_code" id="return_tracking_code_<?php echo $request['ID']; ?>" value="<?php echo $request['return_tracking_code']; ?>" placeholder="<?php _e('Enter the shipment tracking number', 'wc_warranty'); ?>" />
                            <span class="description"><?php _e('Shipping Details/Tracking', 'wc_warranty'); ?></span>
                        </p>
                    <?php else: ?>
                        <p>
                            <label for="return_tracking_code_<?php echo $request['ID']; ?>"><strong><?php _e('Tracking details ', 'wc_warranty'); ?></strong></label>
                            <input type="text" class="tracking_code regular-text" name="return_tracking_code" id="return_tracking_code_<?php echo $request['ID']; ?>" value="<?php echo $request['return_tracking_code']; ?>" placeholder="Enter the shipment tracking number " />
                            <span class="description"><?php _e('Shipping Details/Tracking', 'wc_warranty'); ?></span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </fieldset>

        <div class="submit inline-edit-save">
            <div class="alignright">
                <a class="button-primary" target="_blank" href="<?php echo wp_nonce_url( 'admin-post.php?action=warranty_print&request='. $request['ID'], 'warranty_print' ); ?>"><?php _e('Print', 'wc_warranty'); ?></a>
                <input type="button" class="button-primary rma-update" data-id="<?php echo $request['ID']; ?>" data-security="<?php echo $update_nonce; ?>" value="<?php _e('Update', 'wc_warranty'); ?>" />
            </div>
            <input type="button" class="button close_tr" value="<?php _e('Close', 'wc_warranty'); ?>" />
        </div>
    </td>
</tr>