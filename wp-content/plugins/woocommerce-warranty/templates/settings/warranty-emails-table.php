<style type="text/css">
    span.status-label {line-height: 30px;}
</style>
<p class="description">
    <?php _e('Available variables:', 'wc_warranty'); ?>
    <code>{order_id}</code>, <code>{rma_code}</code>, <code>{shipping_code}</code>,
    <code>{product_id}</code>, <code>{product_name}</code>, <code>{warranty_status}</code>,
    <?php
    foreach ($custom_vars as $custom_var) {
        $custom_var = str_replace( '-', '_', sanitize_title( strtolower($custom_var) ) );
        echo '<code>{'. $custom_var .'}</code>, ';
    }
    ?>
    <code>{coupon_code}</code>, <code>{refund_amount}</code>,
    <code>{customer_name}</code>, <code>{customer_email}</code>, <code>{customer_shipping_code}</code>,
    <code>{store_shipping_code}</code>, <code>{warranty_request_url}</code>, <code>{store_url}</code>
</p>

<table class="wp-list-table widefat fixed posts generic-table striped">
    <thead>
    <tr>
        <th scope="col" id="trigger" class="manage-column column-trigger" width="17%"><?php _e('Trigger', 'wc_warranty'); ?></th>
        <th scope="col" id="settings" class="manage-column column-settings" style=""><?php _e('Settings', 'wc_warranty'); ?></th>
        <th scope="col" id="message" class="manage-column column-message" width="35%"><?php _e('Message', 'wc_warranty'); ?></th>
        <th scope="col" id="delete" class="manage-column column-delete" width="30"></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="4">
            <a class="button add-email" href="#"><?php _e('+ Add Email', 'wc_warranty'); ?></a>
        </td>
    </tr>
    </tfoot>
    <tbody id="emails_tbody">
    <?php
    $admin_email = get_option('admin_email');
    if (! empty($emails) ):
        $idx = 0;

        foreach ( $emails as $email_status => $status_email ):
            foreach ( $status_email as $email ):
                if ( !isset( $email['from_status'] ) ) {
                    $email['from_status'] = 'any';
                }

                if ( !isset( $email['trigger'] ) ) {
                    $email['trigger'] = 'status';
                }

                if ( $email_status == 'Request Tracking' ) {
                    $email['trigger'] = 'request_tracking';
                }
                ?>
                <tr id="email_<?php echo $idx; ?>">
                    <td>
                        <p>
                            <label for="trigger_<?php echo $idx; ?>"><?php _e('Trigger', 'wc_warranty'); ?></label>
                            <br/>
                            <select name="trigger[<?php echo $idx; ?>]" class="trigger" id="trigger_<?php echo $idx; ?>">
                                <option value="status" <?php selected( 'status', $email['trigger'] ); ?>><?php _e('Status change', 'wc_warranty'); ?></option>
                                <option value="request_tracking" <?php selected( 'request_tracking', $email['trigger'] ); ?>><?php _e('Request Tracking', 'wc_warranty'); ?></option>
                                <option value="item_refunded" <?php selected( 'item_refunded', $email['trigger'] ); ?>><?php _e('Item Refunded', 'wc_warranty'); ?></option>
                                <option value="coupon_sent" <?php selected( 'coupon_sent', $email['trigger'] ); ?>><?php _e('Coupon Sent', 'wc_warranty'); ?></option>
                            </select>
                        </p>
                        <div class="trigger_status">
                            <p>
                                <label for="from_status_<?php echo $idx; ?>"><?php _e('From', 'wc_warranty'); ?></label>
                                <br/>
                                <select name="from_status[<?php echo $idx; ?>]" id="from_status_<?php echo $idx; ?>" >
                                    <option value="any"><?php _e('Any status', 'wc_warranty'); ?></option>
                                    <?php foreach ( $all_statuses as $status ): ?>
                                        <option value="<?php echo $status->slug; ?>" <?php selected( $email['from_status'], $status->slug ); ?>><?php echo $status->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>

                            <p>
                                <label for="to_status_<?php echo $idx; ?>"><?php _e('To', 'wc_warranty'); ?></label>
                                <br/>
                                <select name="status[<?php echo $idx; ?>]" id="to_status_<?php echo $idx; ?>">
                                    <?php foreach ( $all_statuses as $status ): ?>
                                        <option value="<?php echo $status->slug; ?>" <?php selected($email_status, $status->slug); ?>><?php echo $status->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                        </div>
                    </td>
                    <td>
                        <div>
                            <label for="recipient_<?php echo $idx; ?>"><?php _e('Recipient', 'wc_warranty'); ?></label>
                            <br/>
                            <select name="send_to[<?php echo $idx; ?>]" id="recipient_<?php echo $idx; ?>" class="recipient-select">
                                <option value="customer" <?php echo ($email['recipient'] == 'customer') ? 'selected' : ''; ?>><?php _e('Customer', 'wc_warranty'); ?></option>
                                <option value="admin" <?php echo ($email['recipient'] == 'admin') ? 'selected' : ''; ?>><?php _e('Admin', 'wc_warranty'); ?></option>
                                <option value="both" <?php echo ($email['recipient'] == 'both') ? 'selected' : ''; ?>><?php _e('Customer &amp; Admin', 'wc_warranty'); ?></option>
                            </select>
                            <br />
                            <div class="search-container">
                                <?php
                                $recipient_emails = array_filter( array_map( 'trim', explode( ',', @$email['admin_recipients'] ) ) );
                                $json = array();
                                foreach ( $recipient_emails as $recipient_email ) {
                                    $json[ $recipient_email ] = $recipient_email;
                                }
                                $email_data = wp_json_encode( $json );
                                $email_data = function_exists( 'wc_esc_json' ) ? wc_esc_json( $email_data ) : _wp_specialchars( $email_data, ENT_QUOTES, 'UTF-8', true );
                                ?>
                                <?php if ( version_compare( WC_VERSION, '3.0', '<' ) ): ?>
                                    <input
                                        class="admin-recipients email-search-select"
                                        data-multiple="true"
                                        name="admin_recipients[<?php echo $idx; ?>]"
                                        type="hidden"
                                        placeholder="<?php echo esc_attr( $admin_email ); ?>"
                                        value="<?php echo implode( ',', array_keys( $json ) ); ?>"
                                        data-selected="<?php echo email_data; ?>"
                                    />
                                <?php else: ?>
                                    <select
                                        class="admin-recipients email-search-select"
                                        name="admin_recipients[<?php echo $idx; ?>]"
                                        multiple="multiple"
                                        placeholder="<?php echo esc_attr( $admin_email ); ?>"
                                        style="width: 400px">
                                <?php
                                foreach ( $json as $id => $name ):
                                ?>
                                        <option value="<?php echo $id; ?>" selected="selected"><?php echo $name; ?></option>
                                <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p>
                            <label for="subject_<?php echo $idx; ?>"><?php _e('Subject', 'wc_warranty'); ?></label>
                            <br/>
                            <input type="text" name="subject[<?php echo $idx; ?>]" id="subject_<?php echo $idx; ?>" value="<?php echo esc_attr($email['subject']); ?>" class="" style="width:100%;" />
                        </p>
                    </td>
                    <td>
                        <textarea name="message[<?php echo $idx; ?>]" rows="5" style="width: 99%;"><?php echo esc_attr($email['message']); ?></textarea>
                    </td>
                    <td><a class="button delete-row" href="#">&times;</a></td>
                </tr>
                <?php       $idx++;
            endforeach;
        endforeach;
    else:
        ?>
        <tr id="email_0">
            <td>
                <p>
                    <label for="trigger_0"><?php _e('Trigger', 'wc_warranty'); ?></label>
                    <br/>
                    <select name="trigger[0]" class="trigger">
                        <option value="status"><?php _e('Status change', 'wc_warranty'); ?></option>
                        <option value="request_tracking"><?php _e('Request Tracking', 'wc_warranty'); ?></option>
                        <option value="item_refunded"><?php _e('Item Refunded', 'wc_warranty'); ?></option>
                        <option value="coupon_sent"><?php _e('Coupon Sent', 'wc_warranty'); ?></option>
                    </select>
                </p>
                <div class="trigger_status">
                    <p>
                        <label for="from_status_0"><?php _e('From', 'wc_warranty'); ?></label>
                        <br/>
                        <select name="from_status[0]" id="from_status__id">
                            <option value="any"><?php _e('Any status', 'wc_warranty'); ?></option>
                            <?php foreach ( $all_statuses as $status ): ?>
                                <option value="<?php echo $status->slug; ?>"><?php echo $status->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="to_status_0"><?php _e('To', 'wc_warranty'); ?></label>
                        <br/>
                        <select name="status[0]" id="to_status_0">
                            <?php foreach ( $all_statuses as $status ): ?>
                                <option value="<?php echo $status->slug; ?>"><?php echo $status->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                </div>
            </td>
            <td>
                <p>
                    <label for="recipient_0"><?php _e('Recipient', 'wc_warranty'); ?></label>
                    <br/>
                    <select name="send_to[0]" id="recipient_0">
                        <option value="customer"><?php _e('Customer', 'wc_warranty'); ?></option>
                        <option value="admin"><?php _e('Admin', 'wc_warranty'); ?></option>
                        <option value="both"><?php _e('Customer &amp; Admin', 'wc_warranty'); ?></option>
                    </select>
                    <div class="search-container">
                        <?php if ( version_compare( WC_VERSION, '3.0', '<' ) ): ?>
                            <input
                                class="admin-recipients email-search-select"
                                data-multiple="true"
                                name="admin_recipients[0]"
                                type="hidden"
                                placeholder="<?php echo esc_attr( $admin_email ); ?>"
                            />
                        <?php else: ?>
                            <select
                                class="admin-recipients email-search-select"
                                name="admin_recipients[0]"
                                multiple="multiple"
                                placeholder="<?php echo esc_attr( $admin_email ); ?>"
                                style="width: 400px">
                            </select>
                        <?php endif; ?>
                    </div>
                </p>

                <p>
                    <label for="subject_0"><?php _e('Subject', 'wc_warranty'); ?></label>
                    <br/>
                    <input type="text" name="subject[0]" id="subject_0" value="" class="" style="width:100%;" />
                </p>
            </td>
            <td>
                <textarea name="message[0]" rows="5" style="width: 99%;"></textarea>
            </td>
            <td></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<div style="display:none;">
    <table id="email-row-template"><tbody>
        <tr id="email__id_">
            <td>
                <p>
                    <label for="trigger__id_"><?php _e('Trigger', 'wc_warranty'); ?></label>
                    <br/>
                    <select name="trigger[_id_]" class="trigger">
                        <option value="status"><?php _e('Status change', 'wc_warranty'); ?></option>
                        <option value="request_tracking"><?php _e('Request Tracking', 'wc_warranty'); ?></option>
                        <option value="item_refunded"><?php _e('Item Refunded', 'wc_warranty'); ?></option>
                        <option value="coupon_sent"><?php _e('Coupon Sent', 'wc_warranty'); ?></option>
                    </select>
                </p>
                <div class="trigger_status">
                    <p>
                        <label for="from_status__id_"><?php _e('From', 'wc_warranty'); ?></label>
                        <br/>
                        <select name="from_status[_id_]" id="from_status__id">
                            <option value="any"><?php _e('Any status', 'wc_warranty'); ?></option>
                            <?php foreach ( $all_statuses as $status ): ?>
                                <option value="<?php echo $status->slug; ?>"><?php echo $status->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="to_status__id_"><?php _e('To', 'wc_warranty'); ?></label>
                        <br/>
                        <select name="status[_id_]" id="to_status__id_">
                            <?php foreach ( $all_statuses as $status ): ?>
                                <option value="<?php echo $status->slug; ?>"><?php echo $status->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                </div>
            </td>
            <td>
                <p>
                    <label for="recipient__id_"><?php _e('Recipient', 'wc_warranty'); ?></label>
                    <br/>
                    <select name="send_to[_id_]" id="recipient__id_">
                        <option value="customer"><?php _e('Customer', 'wc_warranty'); ?></option>
                        <option value="admin"><?php _e('Admin', 'wc_warranty'); ?></option>
                        <option value="both"><?php _e('Customer &amp; Admin', 'wc_warranty'); ?></option>
                    </select>
                    <div class="search-container">
                        <?php if ( version_compare( WC_VERSION, '3.0', '<' ) ): ?>
                            <input
                                class="admin-recipients email-search-select_noenhance_"
                                data-multiple="true"
                                name="admin_recipients[_id_]"
                                type="hidden"
                                placeholder="<?php echo esc_attr( $admin_email ); ?>"
                            />
                        <?php else: ?>
                            <select
                                class="admin-recipients email-search-select_noenhance_"
                                name="admin_recipients[_id_]"
                                multiple="multiple"
                                placeholder="<?php echo esc_attr( $admin_email ); ?>"
                                style="width: 400px">
                            </select>
                        <?php endif; ?>
                    </div>
                </p>

                <p>
                    <label for="subject__id_"><?php _e('Subject', 'wc_warranty'); ?></label>
                    <br/>
                    <input type="text" name="subject[_id_]" id="subject__id_" value="" class="" style="width:100%;" />
                </p>
            </td>
            <td>
                <textarea name="message[_id_]" rows="5" style="width: 99%;"></textarea>
            </td>
            <td><a class="button delete-row" href="#">&times;</a></td>
        </tr>
        </tbody></table>
</div>
<script type="text/javascript">
    <?php
    $js_statuses = array();
    foreach ( $all_statuses as $status ) {
        if ( !isset($status->slug) || empty($status->slug) ) $status->slug = $status->name;
        $js_statuses[] = array('slug' => $status->slug, 'name' => $status->name);
    }
    ?>
    var statuses = JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( $js_statuses ) ); ?>' ) );
    jQuery(document).ready(function($) {
        $(".add-email").click(function(e) {
            e.preventDefault();

            var idx = 1;

            while ( $("#email_"+ idx).length > 0 ) {
                idx++;
            }

            var src = $("#email-row-template tbody").html();
            src = src.replace(/_id_/g, idx);
            // Need to replace noenhance with empty string, otherwise Select2 will be initialized for the template.
            src = src.replace(/_noenhance_/g, '');

            $("#emails_tbody").append(src);
            $('body').trigger('wc-enhanced-select-init');
        });

        $(".delete-row").live("click", function(e) {
            e.preventDefault();

            $(this).parents("tr").remove();
        });

        $("#emails_tbody").on("change", ".trigger", function() {
            var tr = $(this).closest("tr");

            if ( $(this).val() == "status" ) {
                $(tr).find(".trigger_status").show();
            } else {
                $(tr).find(".trigger_status").hide();
            }
        });
        $(".trigger").change();
    });
</script>
