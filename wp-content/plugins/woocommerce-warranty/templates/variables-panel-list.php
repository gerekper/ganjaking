<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="warranty-variation show_if_control_variations" data-loop="<?php echo esc_attr( $loop ); ?>">
	<div>
		<p class="form-row form-row-full options">
			<label for="variable_product_warranty_default_<?php echo esc_attr( $loop ); ?>">
				<input type="checkbox" class="checkbox warranty_default_checkbox" data-id="<?php echo esc_attr( $loop ); ?>" name="variable_product_warranty_default[<?php echo esc_attr( $loop ); ?>]" id="variable_product_warranty_default_<?php echo esc_attr( $loop ); ?>" <?php checked( true, $warranty_default ); ?> />
				<?php esc_html_e( 'Default Product Warranty', 'wc_warranty' ); ?>
			</label>
		</p>

		<p class="form-row form-row-full">
			<label for="variable_product_warranty_type_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Product Warranty', 'wc_warranty' ); ?></label>

			<select id="variable_product_warranty_type_<?php echo esc_attr( $loop ); ?>" name="variable_product_warranty_type[<?php echo esc_attr( $loop ); ?>]" class="select warranty_<?php echo esc_attr( $loop ); ?> variable-warranty-type">
				<option value="no_warranty" <?php selected( 'no_warranty', $warranty['type'] ); ?>><?php esc_html_e( 'No Warranty', 'wc_warranty' ); ?></option>
				<option value="included_warranty" <?php selected( 'included_warranty', $warranty['type'] ); ?>><?php esc_html_e( 'Warranty Included', 'wc_warranty' ); ?></option>
				<option value="addon_warranty" <?php selected( 'addon_warranty', $warranty['type'] ); ?>><?php esc_html_e( 'Warranty as Add-On', 'wc_warranty' ); ?></option>
			</select>
		</p>

		<p class="form-row form-row-full">
			<label for="variable_warranty_label_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Warranty Label', 'wc_warranty' ); ?></label>
			<input type="text" id="variable_warranty_label_<?php echo esc_attr( $loop ); ?>" name="variable_warranty_label[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $warranty_label ); ?>" class="input-text sized warranty_<?php echo esc_attr( $loop ); ?> variable-warranty-label" />
		</p>
	</div>

	<div class="variable_show_if_included_warranty_<?php echo esc_attr( $loop ); ?> hidden">
		<p class="form-row form-row-first">
			<label for="variable_included_warranty_length_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Warranty Length', 'wc_warranty' ); ?></label>

			<select id="variable_included_warranty_length_<?php echo esc_attr( $loop ); ?>" name="variable_included_warranty_length[<?php echo esc_attr( $loop ); ?>]" class="select short warranty_<?php echo esc_attr( $loop ); ?> variable-included-warranty-length">
				<option value="lifetime" <?php selected( 'included_warranty_lifetime', $warranty['type'] . '_' . $warranty['length'] ); ?>><?php esc_html_e( 'Lifetime', 'wc_warranty' ); ?></option>
				<option value="limited" <?php selected( 'included_warranty_limited', $warranty['type'] . '_' . $warranty['length'] ); ?>><?php esc_html_e( 'Limited', 'wc_warranty' ); ?></option>
			</select>
		</p>

		<p class="form-row form-row-last variable_limited_warranty_length_field_<?php echo esc_attr( $loop ); ?>">
			<label for="variable_limited_warranty_length_value_<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Warranty Duration', 'wc_warranty' ); ?></label>
			<input type="text" class="input-text sized warranty_<?php echo esc_attr( $loop ); ?> variable-limited-warranty-length-value" size="3" style="width: 50px;" name="variable_limited_warranty_length_value[<?php echo esc_attr( $loop ); ?>]" value="<?php echo 'included_warranty' === $warranty['type'] ? esc_attr( $warranty['value'] ) : ''; ?>" />
			<select name="variable_limited_warranty_length_duration[<?php echo esc_attr( $loop ); ?>]" class="warranty_<?php echo esc_attr( $loop ); ?> variable-limited-warranty-length-duration" style="width: auto !important;">
				<option value="days" <?php selected( 'included_warranty_days', $warranty['type'] . '_' . $warranty['duration'] ); ?>><?php esc_html_e( 'Days', 'wc_warranty' ); ?></option>
				<option value="weeks" <?php selected( 'included_warranty_weeks', $warranty['type'] . '_' . $warranty['duration'] ); ?>><?php esc_html_e( 'Weeks', 'wc_warranty' ); ?></option>
				<option value="months" <?php selected( 'included_warranty_months', $warranty['type'] . '_' . $warranty['duration'] ); ?>><?php esc_html_e( 'Months', 'wc_warranty' ); ?></option>
				<option value="years" <?php selected( 'included_warranty_years', $warranty['type'] . '_' . $warranty['duration'] ); ?>><?php esc_html_e( 'Years', 'wc_warranty' ); ?></option>
			</select>
		</p>
	</div>

	<div class="variable_show_if_addon_warranty_<?php echo esc_attr( $loop ); ?> hidden">
		<p class="form-row form-row-full">
			<label for="variable_addon_no_warranty_<?php echo esc_attr( $loop ); ?>">
				<input type="checkbox" name="variable_addon_no_warranty[<?php echo esc_attr( $loop ); ?>]" id="variable_addon_no_warranty_<?php echo esc_attr( $loop ); ?>" value="yes"<?php echo isset( $warranty['no_warranty_option'] ) && 'yes' === $warranty['no_warranty_option'] ? ' checked' : ''; ?> class="checkbox warranty_<?php echo esc_attr( $loop ); ?>" />
				<?php esc_html_e( '"No Warranty" option', 'wc_warranty' ); ?>
			</label>
		</p>

		<table class="widefat">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Cost', 'wc_warranty' ); ?></th>
				<th><?php esc_html_e( 'Duration', 'wc_warranty' ); ?></th>
				<th width="50">&nbsp;</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th colspan="3">
					<a href="#" class="button btn-add-warranty-variable warranty_<?php echo esc_attr( $loop ); ?>" data-loop="<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Add Row', 'wc_warranty' ); ?></a>
				</th>
			</tr>
			</tfoot>
			<tbody id="variable_warranty_addons_<?php echo esc_attr( $loop ); ?>">
			<?php
			if ( isset( $warranty['addons'] ) ) {
				foreach ( $warranty['addons'] as $addon ) :
					?>
				<tr>
					<td valign="middle">
						<span class="input"><b>+</b> <?php echo esc_html( $currency ); ?></span>
						<input type="text" name="variable_addon_warranty_amount[<?php echo esc_attr( $loop ); ?>][]" class="input-text sized warranty_<?php echo esc_attr( $loop ); ?>" size="4" value="<?php echo esc_attr( $addon['amount'] ); ?>" style="min-width: 50px; width: 50px; " />
					</td>
					<td valign="middle">
						<input type="text" class="input-text sized warranty_<?php echo esc_attr( $loop ); ?>" size="3" name="variable_addon_warranty_length_value[<?php echo esc_attr( $loop ); ?>][]" value="<?php echo esc_attr( $addon['value'] ); ?>" style="width:50px;" />
						<select name="variable_addon_warranty_length_duration[<?php echo esc_attr( $loop ); ?>][]" class=" warranty_<?php echo esc_attr( $loop ); ?>" style="width: auto !important;">
							<option value="days" <?php selected( 'days', $addon['duration'] ); ?>><?php esc_html_e( 'Days', 'wc_warranty' ); ?></option>
							<option value="weeks" <?php selected( 'weeks', $addon['duration'] ); ?>><?php esc_html_e( 'Weeks', 'wc_warranty' ); ?></option>
							<option value="months" <?php selected( 'months', $addon['duration'] ); ?>><?php esc_html_e( 'Months', 'wc_warranty' ); ?></option>
							<option value="years" <?php selected( 'years', $addon['duration'] ); ?>><?php esc_html_e( 'Years', 'wc_warranty' ); ?></option>
						</select>
					</td>
					<td><a class="button warranty_addon_remove warranty_addon_remove_variable_<?php echo esc_attr( $loop ); ?> warranty_<?php echo esc_attr( $loop ); ?>" data-loop="<?php echo esc_attr( $loop ); ?>" href="#">&times;</a></td>
				</tr>
							<?php
			endforeach;
			};
			?>
			</tbody>

		</table>
	</div>
</div>
