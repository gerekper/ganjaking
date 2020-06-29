<div class="advanced-panel-item opened" id="item_<?php echo esc_attr( $item_id ) ?>" data-id="<?php echo esc_attr( $item_id ) ?>">
    <div class="panel-item-handle">
        <a href="#" class="collapse-button"><?php _e( 'toggle', 'yith-woocommerce-active-campaign' ) ?></a>
        <a href="#" class="remove-button"><?php _e( 'remove', 'yith-woocommerce-active-campaign' ) ?></a>
        <h3><?php _e( 'Set options #' . $item_id, 'yith-woocommerce-active-campaign' ) ?></h3>
    </div>

    <div class="panel-item-content">
        <div class="section">
            <h4><?php _e( 'Lists & Tags', 'yith-woocommerce-active-campaign' ) ?></h4>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="yith_wcac_advanced_integration[items][<?php echo esc_attr( $item_id ) ?>][list]"><?php _e( 'Active Campaign list', 'yith-woocommerce-active-campaign' ) ?></label>
                    </th>
                    <td>
                        <select name="yith_wcac_advanced_integration[items][<?php echo esc_attr( $item_id ) ?>][list]" id="yith_wcac_advanced_integration_<?php echo esc_attr( $item_id ) ?>_list" class="list-select" style="min-width: 300px;">
							<?php

                            if ( ! empty( $lists ) ):
								foreach ( $lists as $list_id => $list_name ): ?>
                                    <option value="<?php echo esc_attr( $list_id ) ?>" <?php selected( $list_id, $selected_list ) ?>><?php echo $list_name ?></option>
								<?php endforeach;
							endif; ?>
                        </select>
                        <a href="#" class="button button-secondary ajax-active-campaign-updater ajax-active-campaign-updater-list"><?php _e( 'Update Lists', 'yith-woocommerce-active-campaign' ) ?></a>
                        <span class="description"><?php _e( 'Select a list for new users', 'yith-woocommerce-active-campaign' ) ?></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="yith_wcac_advanced_integration_<?php esc_attr( $item_id ) ?>_tags"><?php _e( 'Auto-subscribe tags', 'yith-woocommerce-active-campaign' ) ?></label>
                    </th>
                    <td>
                        <select multiple="multiple" name="yith_wcac_advanced_integration[items][<?php echo esc_attr( $item_id ) ?>][tags][]" id="yith_wcac_advanced_integration_<?php echo esc_attr( $item_id ) ?>_tags" class="chosen_select" style="width: 300px;">
							<?php
							if ( ! empty( $tags ) ): ?>
								<?php foreach ( $tags as $tag_id => $tag_name ): ?>
                                    <option value="<?php echo esc_attr( $tag_id ) ?>" <?php selected( in_array( $tag_id, $selected_tags ) ) ?>><?php echo $tag_name ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
                        </select>
                        <a href="#" class="button button-secondary ajax-active-campaign-updater ajax-active-campaign-updater-tags"><?php _e( 'Update Tags', 'yith-woocommerce-active-campaign' ) ?></a>
                        <span class="description"><?php _e( 'Select tags for the new user', 'yith-woocommerce-active-campaign' ) ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="section">
            <h4><?php _e( 'Fields', 'yith-woocommerce-active-campaign' ) ?></h4>
            <div class="fields-header">
                <a href="#" class="button button-secondary add-field"><?php _e( '+ Add New Field', 'yith-woocommerce-active-campaign' ) ?></a>
            </div>

            <p class="description"><?php _e( 'Select the checkout field to connect with the Active Campaign list fields', 'yith-woocommerce-active-campaign' ) ?></p>

            <div class="fields-content">
				<?php
				if ( ! empty( $fields ) ) {
					$counter = 1;
					foreach ( $fields as $field ) {
						$args = array(
							'item_id'            => $item_id,
							'field_id'           => $counter,
							'selected_list'      => isset( $selected_list ) ? $selected_list : '',
							'selected_checkout'  => $field['checkout'],
							'selected_merge_var' => $field['merge_var'],
						);
						YITH_WCAC_Admin()->print_advanced_integration_field( $args );
						$counter ++;
					}
				}
				?>
            </div>
        </div>
        <div class="section">
            <h4><?php _e( 'Conditions', 'yith-woocommerce-active-campaign' ) ?></h4>
            <div class="conditions-header">
                <a href="#" class="button button-secondary add-condition"><?php _e( '+ Add New Condition', 'yith-woocommerce-active-campaign' ) ?></a>
            </div>
            <p class="description"><?php _e( 'Select order matching conditions for user subscription; all conditions selected must match in order
            to complete the subscription', 'yith-woocommerce-active-campaign' ) ?></p>
            <div class="conditions-content">
				<?php
				if ( ! empty( $conditions ) ) {
					$counter = 1;
					foreach ( $conditions as $condition ) {
						$args = array(
							'item_id'      => $item_id,
							'condition_id' => $counter,
							'condition'    => $condition['condition'],
							'op_set'       => $condition['op_set'],
							'op_number'    => $condition['op_number'],
							'products'     => isset( $condition['products'] ) ? $condition['products'] : array(),
							'prod_cats'    => isset( $condition['prod_cats'] ) ? $condition['prod_cats'] : array(),
							'order_total'  => $condition['order_total'],
							'custom_key'   => $condition['custom_key'],
							'op_mixed'     => $condition['op_mixed'],
							'custom_value' => $condition['custom_value']
						);
						YITH_WCAC_Admin()->print_advanced_integration_condition( $args );
						$counter ++;
					}
				}
				?>
            </div>
        </div>
    </div>
</div>