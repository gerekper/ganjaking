<?php
/**
 * The template for displaying warranty options on product edit.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<style type="text/css">
	span.input {float: left; margin-top: 4px;}
	p.addon-row {margin-left: 25px;}
</style>
<div id="warranty_product_data" class="panel woocommerce_options_panel">

	<div class="options_group show_if_variable">
		<p class="form-field">
			<label for="variable_warranty_control">
				<?php esc_html_e( 'Warranty Control', 'wc_warranty' ); ?>
			</label>
			<select id="variable_warranty_control" name="variable_warranty_control">
				<option value="parent" <?php selected( $control_type, 'parent' ); ?>><?php esc_html_e( 'Define warranty for all variations', 'wc_warranty' ); ?></option>
				<option value="variations" <?php selected( $control_type, 'variations' ); ?>><?php esc_html_e( 'Define warranty per variation', 'wc_warranty' ); ?></option>
			</select>
		</p>
	</div>

	<div class="options_group grouping hide_if_control_variations">
		<p class="form-field">
			<label for="product_warranty_default">
				<?php esc_html_e( 'Default Product Warranty', 'wc_warranty' ); ?>
			</label>
			<input type="checkbox" name="product_warranty_default" id="product_warranty_default" <?php checked( true, $default_warranty ); ?> value="yes" />
		</p>

		<p class="form-field product_warranty_type_field">
			<label for="product_warranty_type"><?php esc_html_e( 'Product Warranty', 'wc_warranty' ); ?></label>
			<select id="product_warranty_type" name="product_warranty_type" class="select warranty_field">
				<option value="no_warranty" <?php selected( 'no_warranty', $warranty['type'] ); ?>><?php esc_html_e( 'No Warranty', 'wc_warranty' ); ?></option>
				<option value="included_warranty" <?php selected( 'included_warranty', $warranty['type'] ); ?>><?php esc_html_e( 'Warranty Included', 'wc_warranty' ); ?></option>
				<option value="addon_warranty" <?php selected( 'addon_warranty', $warranty['type'] ); ?>><?php esc_html_e( 'Warranty as Add-On', 'wc_warranty' ); ?></option>
			</select>
		</p>

		<p class="form-field show_if_included_warranty show_if_addon_warranty">
			<label for="warranty_label"><?php esc_html_e( 'Warranty Label', 'wc_warranty' ); ?></label>

			<input type="text" name="warranty_label" value="<?php echo esc_attr( $warranty_label ); ?>" class="input-text sized warranty_field" />
		</p>
	</div>

	<div class="options_group grouping show_if_included_warranty hide_if_control_variations">
		<p class="form-field included_warranty_length_field">
			<label for="included_warranty_length"><?php esc_html_e( 'Warranty Length', 'wc_warranty' ); ?></label>

			<select id="included_warranty_length" name="included_warranty_length" class="select short warranty_field">
				<option value="lifetime" <?php selected( 'included_warranty_lifetime', $warranty['type'] . '_' . $warranty['length'] ); ?>><?php esc_html_e( 'Lifetime', 'wc_warranty' ); ?></option>
				<option value="limited" <?php selected( 'included_warranty_limited', $warranty['type'] . '_' . $warranty['length'] ); ?>><?php esc_html_e( 'Limited', 'wc_warranty' ); ?></option>
			</select>
		</p>

		<p class="form-field limited_warranty_length_field">
			<label for="limited_warranty_length_value"><?php esc_html_e( 'Warranty Duration', 'wc_warranty' ); ?></label>
			<input type="text" class="input-text sized warranty_field" size="3" name="limited_warranty_length_value" value="<?php echo 'included_warranty' === $warranty['type'] ? esc_attr( $warranty['value'] ) : ''; ?>" />
			<select name="limited_warranty_length_duration" class=" warranty_field">
				<option value="days" <?php selected( 'included_warranty_days', $warranty['type'] . '_' . $warranty['duration'] ); ?>><?php esc_html_e( 'Days', 'wc_warranty' ); ?></option>
				<option value="weeks" <?php selected( 'included_warranty_weeks', $warranty['type'] . '_' . $warranty['duration'] ); ?>><?php esc_html_e( 'Weeks', 'wc_warranty' ); ?></option>
				<option value="months" <?php selected( 'included_warranty_months', $warranty['type'] . '_' . $warranty['duration'] ); ?>><?php esc_html_e( 'Months', 'wc_warranty' ); ?></option>
				<option value="years" <?php selected( 'included_warranty_years', $warranty['type'] . '_' . $warranty['duration'] ); ?>><?php esc_html_e( 'Years', 'wc_warranty' ); ?></option>
			</select>
		</p>
	</div>

	<div class="options_group grouping show_if_addon_warranty hide_if_control_variations">
		<p class="form-field">
			<label for="addon_no_warranty">
				<?php esc_html_e( '"No Warranty" option', 'wc_warranty' ); ?>
			</label>
			<input type="checkbox" name="addon_no_warranty" id="addon_no_warranty" value="yes"<?php echo isset( $warranty['no_warranty_option'] ) && 'yes' === $warranty['no_warranty_option'] ? ' checked' : ''; ?> class="checkbox warranty_field" />
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
					<a href="#" class="button btn-add-warranty"><?php esc_html_e( 'Add Row', 'wc_warranty' ); ?></a>
				</th>
			</tr>
			</tfoot>
			<tbody id="warranty_addons">
			<?php
			if ( isset( $warranty['addons'] ) ) {
				foreach ( $warranty['addons'] as $addon ) :
					?>
				<tr>
					<td valign="middle">
						<span class="input"><b>+</b> <?php echo esc_html( $currency ); ?></span>
						<input type="text" name="addon_warranty_amount[]" class="input-text sized warranty_field" size="4" value="<?php echo esc_attr( $addon['amount'] ); ?>" />
					</td>
					<td valign="middle">
						<input type="text" class="input-text sized warranty_field" size="3" name="addon_warranty_length_value[]" value="<?php echo esc_attr( $addon['value'] ); ?>" />
						<select name="addon_warranty_length_duration[]" class=" warranty_field">
							<option value="days" <?php selected( 'days', $addon['duration'] ); ?>><?php esc_html_e( 'Days', 'wc_warranty' ); ?></option>
							<option value="weeks" <?php selected( 'weeks', $addon['duration'] ); ?>><?php esc_html_e( 'Weeks', 'wc_warranty' ); ?></option>
							<option value="months" <?php selected( 'months', $addon['duration'] ); ?>><?php esc_html_e( 'Months', 'wc_warranty' ); ?></option>
							<option value="years" <?php selected( 'years', $addon['duration'] ); ?>><?php esc_html_e( 'Years', 'wc_warranty' ); ?></option>
						</select>
					</td>
					<td><a class="button warranty_addon_remove" href="#">&times;</a></td>
				</tr>
							<?php
			endforeach;
			};
			?>
			</tbody>

		</table>
	</div>
</div>
