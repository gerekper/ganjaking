<?php
/**
 * Admin View: Fields Table Settings
 *
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Checkout Manager
 */

defined( 'YWCCP' ) || exit; // Exit if accessed directly.
?>

<div id="ywccp-add-field">
	<h4><?php esc_html_e( 'Add new field', 'yith-woocommerce-checkout-manager' ); ?></h4>
	<label for="add-new-name"><?php esc_html_e( 'Enter field name', 'yith-woocommerce-checkout-manager' ); ?></label>
	<input type="text" id="add-new-name" name="add-new-name" value=""/>
	<input type="button" value="<?php esc_html_e( 'Add field', 'yith-woocommerce-checkout-manager' ); ?>" id="add-new" class="button-primary" name="add-new">
</div>


<form method="post" id="ywccp_checkout_fields_form">
	<div class="fields_table_bulk_actions">
		<select name="bulk_action" id="bulk_actions_select">
			<option value=""><?php esc_html_e( 'Select an action', 'yith-woocommerce-checkout-manager' ); ?></option>
			<option value="enable"><?php esc_html_e( 'Enable', 'yith-woocommerce-checkout-manager' ); ?></option>
			<option value="disable"><?php esc_html_e( 'Disable', 'yith-woocommerce-checkout-manager' ); ?></option>
		</select>
		<input type="submit" id="ywcc_doaction_bulk" class="button action" value="Apply">
	</div>
	<table id="ywccp_checkout_fields" class="wc_gateways widefat" cellspacing="0" data-prepend="<?php echo esc_attr( $current ); ?>_">
		<thead>
			<tr>
				<th class="sort is_responsive" style="width:15px;"></th>
				<th class="check-column is_responsive" style="padding-left:0 !important;"><input type="checkbox" style="margin-left:7px;" /></th>
				<th class="name is_responsive"><?php esc_html_e( 'Name', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="type"><?php esc_html_e( 'Type', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="label"><?php esc_html_e( 'Label', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="placeholder"><?php esc_html_e( 'Placeholder', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="validation-rules"><?php esc_html_e( 'Validation rules', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="status"><?php esc_html_e( 'Required', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="actions is_responsive"><?php esc_html_e( 'Actions', 'yith-woocommerce-checkout-manager' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="sort is_responsive"></th>
				<th class="check-column is_responsive" style="padding-left:0 !important;"><input type="checkbox" style="margin-left:7px;" /></th>
				<th class="name is_responsive"><?php esc_html_e( 'Name', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="type"><?php esc_html_e( 'Type', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="label"><?php esc_html_e( 'Label', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="placeholder"><?php esc_html_e( 'Placeholder', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="validation-rules"><?php esc_html_e( 'Validation rules', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="status"><?php esc_html_e( 'Required', 'yith-woocommerce-checkout-manager' ); ?></th>
				<th class="actions is_responsive"><?php esc_html_e( 'Actions', 'yith-woocommerce-checkout-manager' ); ?></th>
			</tr>
		</tfoot>
		<tbody class="ui-sortable">
		<?php
		$i = 0; // Init counter.
		foreach ( $fields as $name => $field ) :
			// Check if is custom.
			$custom = ! in_array( $name, $default_fields_key, true );
			$class  = apply_filters(
				'ywcc_fields_main_table_row_class',
				array(
					$custom ? 'is_custom' : '',
					! $field['enabled'] ? 'disabled-row' : '',
				),
				$field
			);
			$class  = array_filter( $class );
			?>
			<tr data-row="<?php echo absint( $i ); ?>" class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
				<td width="1%" class="sort ui-sortable-handle is_responsive">
					<input type="hidden" name="field_name[]" class="field_name" data-name="field_name" value="<?php echo esc_attr( $name ); ?>"/>
					<input type="hidden" name="field_type[]" data-name="field_type" value="<?php echo esc_attr( $field['type'] ); ?>"/>
					<input type="hidden" name="field_label[]" data-name="field_label" value="<?php echo esc_attr( $field['label'] ); ?>"/>
					<input type="hidden" name="field_placeholder[]" data-name="field_placeholder" value="<?php echo esc_attr( $field['placeholder'] ); ?>"/>
					<input type="hidden" name="field_options[]" data-name="field_options" value="<?php echo esc_attr( $field['options'] ); ?>"/>

					<input type="hidden" name="field_position[]" data-name="field_position" value="<?php echo esc_attr( $field['position'] ); ?>"/>
					<input type="hidden" name="field_class[]" data-name="field_class" value="<?php echo esc_attr( $field['class'] ); ?>"/>
					<input type="hidden" name="field_label_class[]" data-name="field_label_class" value="<?php echo esc_attr( $field['label_class'] ); ?>"/>

					<input type="hidden" name="field_required[]" data-name="field_required" value="<?php echo esc_attr( $field['required'] ); ?>"/>
					<input type="hidden" name="field_enabled[]" data-name="field_enabled" value="<?php echo esc_attr( $field['enabled'] ); ?>"/>
					<input type="hidden" name="field_validate[]" data-name="field_validate" value="<?php echo esc_attr( $field['validate'] ); ?>"/>
					<input type="hidden" name="field_show_in_email[]" data-name="field_show_in_email" value="<?php echo esc_attr( $field['show_in_email'] ); ?>"/>
					<input type="hidden" name="field_show_in_order[]" data-name="field_show_in_order" value="<?php echo esc_attr( $field['show_in_order'] ); ?>"/>

					<input type="hidden" name="field_tooltip[]" data-name="field_tooltip" value="<?php echo esc_attr( $field['tooltip'] ); ?>"/>
					<input type="hidden" class="condition" name="field_condition_input_name[]" data-name="field_condition_input_name" value="<?php echo isset( $field['condition_input_name'] ) ? esc_attr( $field['condition_input_name'] ) : ''; ?>"/>
					<input type="hidden" class="condition" name="field_condition_type[]" data-name="field_condition_type" value="<?php echo isset( $field['condition_type'] ) ? esc_attr( $field['condition_type'] ) : ''; ?>"/>
					<input type="hidden" class="condition" name="field_condition_value[]" data-name="field_condition_value" value="<?php echo isset( $field['condition_value'] ) ? esc_attr( $field['condition_value'] ) : ''; ?>"/>
					<input type="hidden" class="condition" name="field_condition_action[]" data-name="field_condition_action" value="<?php echo isset( $field['condition_action'] ) ? esc_attr( $field['condition_action'] ) : ''; ?>"/>
					<input type="hidden" class="condition" name="field_condition_required[]" data-name="field_condition_required" value="<?php echo isset( $field['condition_required'] ) ? esc_attr( $field['condition_required'] ) : ''; ?>"/>

					<input type="hidden" name="field_deleted[]" data-name="field_deleted" value="" />

					<span class="dashicons dashicons-move"></span>
				</td>
				<td class="td_select is_responsive"><input type="checkbox" name="select_field[<?php echo absint( $i ); ?>]"/></td>
				<td class="td_field_name is_responsive"><?php echo esc_html( $name ); ?></td>
				<td class="td_field_type"><?php echo esc_html( $field['type'] ); ?></td>
				<td class="td_field_label"><?php echo esc_html( $field['label'] ); ?></td>
				<td class="td_field_placeholder"><?php echo esc_html( $field['placeholder'] ); ?></td>
				<td class="td_field_validate"><?php echo esc_html( $field['validate'] ); ?></td>
				<td class="td_field_required status"><?php echo( 1 === absint( $field['required'] ) ? '<span class="status-enabled tips" data-tip="' . esc_html__( 'Yes', 'yith-woocommerce-checkout-manager' ) . '"></span>' : '-' ); ?></td>
				<td class="td_field_action is_responsive">
					<button type="button" class="button edit_field"><?php esc_html_e( 'Edit', 'yith-woocommerce-checkout-manager' ); ?></button>
					<?php
					$enable_button_label = 1 === absint( $field['enabled'] ) ? esc_html__( 'Disable', 'yith-woocommerce-checkout-manager' ) : esc_html__( 'Enable', 'yith-woocommerce-checkout-manager' );
					$enable_button_data  = 1 !== absint( $field['enabled'] ) ? esc_html__( 'Disable', 'yith-woocommerce-checkout-manager' ) : esc_html__( 'Enable', 'yith-woocommerce-checkout-manager' );
					?>
					<button type="button" class="button enable_field" data-label="<?php echo esc_attr( $enable_button_data ); ?>"><?php echo esc_html( $enable_button_label ); ?></button>

					<button type="button" class="button remove_field"><?php esc_html_e( 'Remove', 'yith-woocommerce-checkout-manager' ); ?></button>
				</td>
			</tr>
			<?php
			$i ++;
endforeach;
		?>
		</tbody>
	</table>
	<div class="fields_table_bulk_actions">
		<select name="bulk_action_bottom" id="bulk_actions_select_bottom">
			<option value=""><?php esc_html_e( 'Select an action', 'yith-woocommerce-checkout-manager' ); ?></option>
			<option value="enable"><?php esc_html_e( 'Enable', 'yith-woocommerce-checkout-manager' ); ?></option>
			<option value="disable"><?php esc_html_e( 'Disable', 'yith-woocommerce-checkout-manager' ); ?></option>
		</select>
		<input type="submit" id="ywcc_doaction_bulk_bottom" class="button action" value="Apply">
	</div>
	<p class="submit">
		<input type="hidden" name="ywccp-admin-action" value="fields-save" />
		<input type="hidden" name="ywccp-admin-section" value="<?php echo esc_attr( $current ); ?>" />
		<input class="button-primary" type="submit" value="<?php esc_html_e( 'Save changes', 'yith-woocommerce-checkout-manager' ); ?>"/>
	</p>
</form>
