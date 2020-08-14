<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

/**
 * Admin View: Request a Quote Form Table
 *
 * @version 2.0.11
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$types = ywraq_get_form_field_type();
?>
<div class="ywraq-form-wrapper">
	<div class="ywraq-edit-form-content">
		<div id="ywraq-add-field">
			<h4><?php echo esc_html__( 'Add new field', 'yith-woocommerce-request-a-quote' ); ?></h4>
			<label for="add-new-name"><?php esc_html_e( 'Enter field name', 'yith-woocommerce-request-a-quote' ); ?></label>
			<input type="text" id="add-new-name" name="add-new-name" pattern="[A-Za-z]"/>
			<input type="button" value="<?php esc_html_e( 'Add field', 'yith-woocommerce-request-a-quote' ); ?>" id="add-new" class="button button-primary" name="add-new">
		</div>

		<form method="post" id="ywraq_form_fields_form">
			<div class="fields_table_bulk_actions">
				<select name="bulk_action" id="bulk_actions_select">
					<option value=""><?php esc_html_e( 'Select an action', 'yith-woocommerce-request-a-quote' ); ?></option>
					<option value="enable"><?php esc_html_e( 'Enable', 'yith-woocommerce-request-a-quote' ); ?></option>
					<option value="disable"><?php esc_html_e( 'Disable', 'yith-woocommerce-request-a-quote' ); ?></option>
				</select>
				<input type="submit" id="ywraq_doaction_bulk" class="button action" value="Apply">
			</div>
			<table id="ywraq_form_fields" class="wc_gateways widefat" cellspacing="0">
				<thead>
				<tr>
					<th class="sort is_responsive"></th>
					<th class="check-column is_responsive" style="padding-left:0px !important;"><input type="checkbox" style="margin-left:7px;"/>
					</th>
					<th class="name is_responsive"><?php esc_html_e( 'Name', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="type"><?php esc_html_e( 'Type', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="label"><?php esc_html_e( 'Label', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="placeholder"><?php esc_html_e( 'Placeholder', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="validation-rules"><?php esc_html_e( 'Validation rules', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="connection-to"><?php esc_html_e( 'Connected to', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="status"><?php esc_html_e( 'Required', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="actions is_responsive"><?php esc_html_e( 'Actions', 'yith-woocommerce-request-a-quote' ); ?></th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<th class="sort is_responsive"></th>
					<th class="check-column is_responsive" style="padding-left:0px !important;"><input type="checkbox" style="margin-left:7px;"/>
					</th>
					<th class="name is_responsive"><?php esc_html_e( 'Name', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="type"><?php esc_html_e( 'Type', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="label"><?php esc_html_e( 'Label', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="placeholder"><?php esc_html_e( 'Placeholder', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="validation-rules"><?php esc_html_e( 'Validation rules', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="connection-to"><?php esc_html_e( 'Connected to', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="status"><?php esc_html_e( 'Required', 'yith-woocommerce-request-a-quote' ); ?></th>
					<th class="actions is_responsive"><?php esc_html_e( 'Actions', 'yith-woocommerce-request-a-quote' ); ?></th>
				</tr>
				</tfoot>
				<tbody class="ui-sortable">
				<?php

				$i = 0; // init counter.
				foreach ( $fields as $name => $field ) :
					// check if is custom.
					$custom = ! in_array( $name, $default_fields_key, true ) ? true : false;
					$class  = apply_filters(
						'ywraq_fields_main_table_row_class',
						array(
							$custom ? 'is_custom' : '',
							! $field['enabled'] ? 'disabled-row' : '',
						),
						$field
					);
					$class  = array_filter( $class );
					?>
					<tr data-row="<?php echo esc_attr( $i ); ?>" class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
						<td width="1%" class="sort ui-sortable-handle is_responsive">
							<input type="hidden" name="field_name[]" class="field_name" data-name="field_name" value="<?php echo esc_attr( $name ); ?>"/>
							<input type="hidden" name="field_type[]" data-name="field_type" value="<?php echo esc_attr( $field['type'] ); ?>"/>
							<input type="hidden" name="field_label[]" data-name="field_label" value="<?php echo esc_attr( $field['label'] ); ?>"/>
							<input type="hidden" name="field_id[]" data-name="field_id" value="<?php echo esc_attr( $field['id'] ); ?>"/>
							<input type="hidden" name="field_placeholder[]" data-name="field_placeholder" value="<?php echo esc_attr( $field['placeholder'] ); ?>"/>
							<input type="hidden" name="field_description[]" data-name="field_description" value="<?php echo isset( $field['description'] ) ? wp_kses_post( $field['description'] ) : ''; ?>"/>
							<input type="hidden" name="field_options[]" data-name="field_options" value="<?php echo esc_attr( $field['options'] ); ?>"/>
							<input type="hidden" name="field_position[]" data-name="field_position" value="<?php echo esc_attr( $field['position'] ); ?>"/>
							<input type="hidden" name="field_class[]" data-name="field_class" value="<?php echo esc_attr( $field['class'] ); ?>"/>
							<input type="hidden" name="field_label_class[]" data-name="field_label_class" value="<?php echo esc_attr( $field['label_class'] ); ?>"/>
							<input type="hidden" name="field_required[]" data-name="field_required" value="<?php echo esc_attr( $field['required'] ); ?>"/>
							<input type="hidden" name="field_checked[]" data-name="field_checked" value="<?php echo isset( $field['default'] ) ? esc_attr( $field['default'] ) : ''; ?>"/>

							<input type="hidden" name="field_upload_allowed_extensions[]" data-name="field_upload_allowed_extensions" value="<?php echo esc_attr( $field['upload_allowed_extensions'] ); ?>"/>
							<input type="hidden" name="field_max_filesize[]" data-name="field_max_filesize" value="<?php echo esc_attr( $field['max_filesize'] ); ?>"/>
							<input type="hidden" name="field_enabled[]" data-name="field_enabled" value="<?php echo esc_attr( $field['enabled'] ); ?>"/>
							<input type="hidden" name="field_validate[]" data-name="field_validate" value="<?php echo esc_attr( $field['validate'] ); ?>"/>
							<input type="hidden" name="field_connect_to_field[]" data-name="field_connect_to_field" value="<?php echo esc_attr( $field['connect_to_field'] ); ?>"/>
							<input type="hidden" name="field_deleted[]" data-name="field_deleted" value=""/>
						</td>
						<td class="td_select is_responsive"><input type="checkbox" name="select_field[<?php echo esc_attr( $i ); ?>]"/></td>
						<td class="td_field_name is_responsive"><?php echo esc_attr( $name ); ?></td>
						<td class="td_field_type"><?php echo isset( $types[ $field['type'] ] ) ? esc_attr( $types[ $field['type'] ] ) : esc_attr( $field['type'] ); ?></td>
						<td class="td_field_label"><?php echo esc_attr( $field['label'] ); ?></td>
						<td class="td_field_placeholder"><?php echo esc_attr( $field['placeholder'] ); ?></td>
						<td class="td_field_validate"><?php echo esc_attr( $field['validate'] ); ?></td>
						<td class="td_field_connect_to_field"><?php echo esc_attr( $field['connect_to_field'] ); ?></td>
						<td class="td_field_required status"><?php echo ( 1 === $field['required'] ) ? '<span class="status-enabled tips" data-tip="' . esc_html__( 'Yes', 'yith-woocommerce-request-a-quote' ) . '"></span>' : '-'; ?></td>
						<td class="td_field_action is_responsive">
							<button type="button" class="button edit_field button-primary"><?php esc_html_e( 'Edit', 'yith-woocommerce-request-a-quote' ); ?></button>
							<?php
							$enable_button_label = 1 !== $field['enabled'] ? esc_html__( 'Disable', 'yith-woocommerce-request-a-quote' ) : esc_html__( 'Enable', 'yith-woocommerce-request-a-quote' );
							$enable_button_data  = 1 === $field['enabled'] ? esc_html__( 'Disable', 'yith-woocommerce-request-a-quote' ) : esc_html__( 'Enable', 'yith-woocommerce-request-a-quote' );
							?>

							<button type="button" class="button enable_field button-secondary" data-label="<?php echo esc_attr( $enable_button_data ); ?>"><?php echo esc_attr( $enable_button_label ); ?></button>
							<button type="button" class="button remove_field"><?php esc_html_e( 'Remove', 'yith-woocommerce-request-a-quote' ); ?></button>
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
					<option value=""><?php esc_html_e( 'Select an action', 'yith-woocommerce-request-a-quote' ); ?></option>
					<option value="enable"><?php esc_html_e( 'Enable', 'yith-woocommerce-request-a-quote' ); ?></option>
					<option value="disable"><?php esc_html_e( 'Disable', 'yith-woocommerce-request-a-quote' ); ?></option>
				</select>
				<input type="submit" id="ywraq_doaction_bulk_bottom" class="button action" value="Apply">
			</div>
			<input type="hidden" name="ywraq-admin-action" value="fields-save"/>
			<input style="float: left; margin-right: 10px;" class="button-primary save-form" type="submit" value="<?php esc_html_e( 'Save changes', 'yith-woocommerce-request-a-quote' ); ?>"/>
		</form>
	</div>
</div>
