<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
?>
<div class="woocommerce_product_addon wc-metabox closed">
	<h3>
		<button type="button" class="remove_addon button"><?php _e( 'Remove', 'woocommerce-product-addons' ); ?></button>
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'woocommerce-product-addons' ); ?>"></div>
		<strong><?php _e( 'Group', 'woocommerce-product-addons' ); ?> <span class="group_name"><?php if ( $addon['name'] ) echo '"' . esc_attr( $addon['name'] ) . '"'; ?></span> &mdash; </strong>
		<select name="product_addon_type[<?php echo $loop; ?>]" class="product_addon_type">
			<option <?php selected('custom_price', $addon['type']); ?> value="custom_price"><?php _e('Additional custom price input', 'woocommerce-product-addons'); ?></option>
			<option <?php selected('input_multiplier', $addon['type']); ?> value="input_multiplier"><?php _e('Additional price multiplier', 'woocommerce-product-addons'); ?></option>
			<option <?php selected('checkbox', $addon['type']); ?> value="checkbox"><?php _e('Checkboxes', 'woocommerce-product-addons'); ?></option>
			<option <?php selected('custom_textarea', $addon['type']); ?> value="custom_textarea"><?php _e('Custom input (textarea)', 'woocommerce-product-addons'); ?></option>
			<optgroup label="<?php esc_attr_e('Custom input (text)', 'woocommerce-product-addons'); ?>">
				<option <?php selected( 'custom', $addon['type']); ?> value="custom"><?php _e('Any text', 'woocommerce-product-addons'); ?></option>
				<option <?php selected( 'custom_email', $addon['type'] ); ?> value="custom_email"><?php _e( 'Email address', 'woocommerce-product-addons' ); ?></option>
				<option <?php selected( 'custom_letters_only', $addon['type'] ); ?> value="custom_letters_only"><?php _e( 'Only letters', 'woocommerce-product-addons' ); ?></option>
				<option <?php selected( 'custom_letters_or_digits', $addon['type'] ); ?> value="custom_letters_or_digits"><?php _e( 'Only letters and numbers', 'woocommerce-product-addons' ); ?></option>
				<option <?php selected( 'custom_digits_only', $addon['type'] ); ?> value="custom_digits_only"><?php _e( 'Only numbers', 'woocommerce-product-addons' ); ?></option>
			</optgroup>
			<option <?php selected('file_upload', $addon['type']); ?> value="file_upload"><?php _e('File upload', 'woocommerce-product-addons'); ?></option>
			<option <?php selected('radiobutton', $addon['type']); ?> value="radiobutton"><?php _e('Radio buttons', 'woocommerce-product-addons'); ?></option>
			<option <?php selected('select', $addon['type']); ?> value="select"><?php _e('Select box', 'woocommerce-product-addons'); ?></option>
		</select>
		<input type="hidden" name="product_addon_position[<?php echo $loop; ?>]" class="product_addon_position" value="<?php echo $loop; ?>" />
	</h3>
	<table cellpadding="0" cellspacing="0" class="wc-metabox-content">
		<tbody>
			<tr>
				<td class="addon_name" width="50%">
					<label for="addon_name_<?php echo $loop; ?>">
						<?php
							_e( 'Name', 'woocommerce-product-addons' );
						?>
					</label>
					<input type="text" id="addon_name_<?php echo $loop; ?>" name="product_addon_name[<?php echo $loop; ?>]" value="<?php echo esc_attr( $addon['name'] ) ?>" />
				</td>
				<td class="addon_required" width="50%">
					<label for="addon_required_<?php echo $loop; ?>"><?php _e( 'Required fields?', 'woocommerce-product-addons' ); ?></label>
					<input type="checkbox" id="addon_required_<?php echo $loop; ?>" name="product_addon_required[<?php echo $loop; ?>]" <?php checked( $addon['required'], 1 ) ?> />
				</td>
			</tr>
			<tr>
				<td class="addon_description" colspan="2">
					<label for="addon_description_<?php echo $loop; ?>">
						<?php
							_e( 'Description', 'woocommerce-product-addons' );
						?>
					</label>
					<textarea cols="20" id="addon_description_<?php echo $loop; ?>" rows="3" name="product_addon_description[<?php echo $loop; ?>]"><?php echo esc_textarea( $addon['description'] ) ?></textarea>
				</td>
			</tr>
			<?php do_action( 'woocommerce_product_addons_panel_before_options', $post, $addon, $loop ); ?>
			<tr>
				<td class="data" colspan="3">
					<table cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th><?php _e('Label', 'woocommerce-product-addons'); ?></th>
								<th class="price_column"><?php _e('Price', 'woocommerce-product-addons'); ?></th>
								<th class="minmax_column"><span class="column-title"><?php _e('Min / Max', 'woocommerce-product-addons'); ?></span></th>
								<?php do_action( 'woocommerce_product_addons_panel_option_heading', $post, $addon, $loop ); ?>
								<th width="1%"></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5"><button type="button" class="add_addon_option button"><?php _e('New&nbsp;Option', 'woocommerce-product-addons'); ?></button></td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							foreach ( $addon['options'] as $option )
								include( dirname( __FILE__ ) . '/html-addon-option.php' );
							?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
