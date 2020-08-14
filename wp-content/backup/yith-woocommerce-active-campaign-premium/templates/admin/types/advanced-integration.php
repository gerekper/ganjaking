<tr valign="top" class="yith_wcac_active_campaign_tags yith_wcac_active_campaign_show_tags_label">
    <th scope="row" class="titledesc">
        <label for="yith_wcac_advanced_integration_show_tags_label"><?php _e( 'Tags label', 'yith-woocommerce-active-campaign' ) ?></label>

    </th>
    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo sanitize_title( $value['type'] ) ?>" colspan="2">
        <input type="text" name="yith_wcac_advanced_integration[show_tags_label]" id="yith_wcac_advanced_integration_show_tags_label" style="width: 300px;" value="<?php echo $show_tags_label; ?>" />
        <span class="description"><?php _e( 'Type here a text that will be used as title for the Tags section on the checkout page', 'yith-woocommerce-active-campaign' ) ?></span>
    </td>
</tr>
<tr valign="top" class="yith_wcac_active_campaign_tags yith_wcac_active_campaign_show_tags">
    <th scope="row" class="titledesc">
        <label for="yith_wcac_advanced_integration_show_tags"><?php _e( 'Show tags', 'yith-woocommerce-active-campaign' ) ?></label>

    </th>
    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo sanitize_title( $value['type'] ) ?>" colspan="2">
        <select multiple="multiple" name="yith_wcac_advanced_integration[show_tags][]" id="yith_wcac_advanced_integration_show_tags" class="chosen_select" style="width: 300px;">
			<?php
			if ( ! empty( $tags ) ): ?>
				<?php foreach ( $tags as $tag_id => $tag_name ): ?>
                    <option value="<?php echo esc_attr( $tag_id ) ?>" <?php selected( in_array( $tag_id, $selected_show_tags ) ) ?>><?php echo $tag_name ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
        </select>
        <a href="#" class="button button-secondary ajax-active-campaign-updater ajax-active-campaign-updater-tags"><?php _e( 'Update Tags', 'yith-woocommerce-active-campaign' ) ?></a>
        <span class="description"><?php _e( 'Select tags among which users can choose', 'yith-woocommerce-active-campaign' ) ?></span>
    </td>
</tr>
<tr valign="top" class="yith_wcac_active_campaign_tags yith_wcac_active_campaign_show_tags_positions" style="display: table-row;">
    <th scope="row" class="titledesc">
        <label for="yith_wcac_advanced_integration_show_tags_position"><?php _e( 'Position for tags', 'yith-woocommerce-active-campaign' ); ?></label>
    </th>
    <td class="forminp forminp-select">
        <select name="yith_wcac_advanced_integration[show_tags_position]" id="yith_wcac_advanced_integration_show_tags_position" style="min-width:300px;" class="">
            <option value="above_customer" <?php selected( 'above_customer' == $selected_show_tags_position ) ?>><?php _e( 'Above customer details', 'yith-woocommerce-active-campaign' ) ?></option>
            <option value="below_customer" <?php selected( 'below_customer' == $selected_show_tags_position ) ?>><?php _e( 'Below customer details', 'yith-woocommerce-active-campaign' ) ?></option>
            <option value="above_place_order" <?php selected( 'above_place_order' == $selected_show_tags_position ) ?>><?php _e( 'Above "Place order" button', 'yith-woocommerce-active-campaign' ) ?></option>
            <option value="below_place_order" <?php selected( 'below_place_order' == $selected_show_tags_position ) ?>><?php _e( 'Below "Place order" button', 'yith-woocommerce-active-campaign' ) ?></option>
            <option value="above_total" <?php selected( 'above_total' == $selected_show_tags_position ) ?>><?php _e( 'Above "Review order" total', 'yith-woocommerce-active-campaign' ) ?></option>
            <option value="above_billing" <?php selected( 'above_billing' == $selected_show_tags_position ) ?>><?php _e( 'Above billing details', 'yith-woocommerce-active-campaign' ) ?></option>
            <option value="below_billing" <?php selected( 'below_billing' == $selected_show_tags_position ) ?>><?php _e( 'Below billing details', 'yith-woocommerce-active-campaign' ) ?></option>
            <option value="above_shipping" <?php selected( 'above_shipping' == $selected_show_tags_position ) ?>><?php _e( 'Above shipping details', 'yith-woocommerce-active-campaign' ) ?></option>
        </select> <span class="description"><?php _e( 'Select position for tags that will be display on the page', 'yith-woocommerce-active-campaign' ) ?></span>
    </td>
</tr>
<tr valign="top">
    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo sanitize_title( $value['type'] ) ?>" colspan="2">
        <div class="advanced-integration-header">
            <a href="#" class="button button-primary" id="add_options_set"><?php _e( '+ Add set option', 'yith-woocommerce-active-campaign' ) ?></a>
            <span><?php _e( 'Click "Add set option" button to add a bunch of options; don\'t forget to save, when you\'re done', 'yith-woocommerce-active-campaign' ) ?></span>
        </div>
        <div class="advanced-integration-content">
			<?php
			if ( ! empty( $advanced_items ) ) {
				$counter = 1;
				foreach ( $advanced_items as $section ) {
					$args = array(
						'item_id'       => $counter,
						'selected_list' => isset( $section['list'] ) ? $section['list'] : 0,
						'selected_tags' => isset( $section['tags'] ) ? $section['tags'] : array(),
						'fields'        => isset( $section['fields'] ) ? $section['fields'] : array(),
						'conditions'    => isset( $section['conditions'] ) ? $section['conditions'] : array()
					);
					YITH_WCAC_Admin()->print_advanced_integration_item( $args );
					$counter ++;
				}
			}
			?>
        </div>
    </td>
</tr>