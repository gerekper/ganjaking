<tr valign="top">
	<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?> <?php echo sanitize_title( $value['type'] ) ?>" colspan="2">
		<div class="advanced-integration-header">
			<a href="#" class="button button-primary" id="add_options_set"><?php _e( '+ Add option set', 'yith-woocommerce-mailchimp' )?></a>
			<span><?php _e( 'Click "Add option set" button to add a bunch of options; don\'t forget to save, when you\'re done', 'yith-woocommerce-mailchimp'  )?></span>
			<input type="hidden" name="yith_wcmc_advanced_integration" value=""/>
		</div>
		<div class="advanced-integration-content">
			<?php
			if( ! empty( $advanced_options ) ){
				$counter = 1;
				foreach( $advanced_options as $section ){
					$args = array(
						'item_id' => $counter,
						'selected_list' => isset( $section['list'] ) ? $section['list'] : array(),
						'selected_groups' => isset( $section['groups'] ) ? $section['groups'] : array(),
						'fields' => isset( $section['fields'] ) ? $section['fields'] : array(),
						'conditions' => isset( $section['conditions'] ) ? $section['conditions'] : array()
					);
					YITH_WCMC_Admin_Premium()->print_advanced_integration_item( $args );
					$counter ++;
				}
			}
			?>
		</div>
	</td>
</tr>