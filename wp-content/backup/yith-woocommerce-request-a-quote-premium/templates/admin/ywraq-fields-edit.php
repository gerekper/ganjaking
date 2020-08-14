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
 * Admin View: Fields Table Edit Form
 *
 * @version 2.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wpdb;
$upload_max_filesize = isset( $wpdb->qm_php_vars['upload_max_filesize'] ) ? str_replace( 'M', '', $wpdb->qm_php_vars['upload_max_filesize'] ) : '';
?>

<div id="ywraq_field_add_edit_form" style="display: none;">
	<form>
		<table>
			<tr class="remove_default">
				<td class="label"><?php esc_html_e( 'Name', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td><input type="text" name="field_name"/></td>
			</tr>
			<tr class="remove_default">
				<td class="label"><?php esc_html_e( 'Type', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td>
					<select name="field_type">
						<?php foreach ( $field_types as $value => $label ) : ?>
							<option value="<?php echo wp_kses_post( $value ); ?>"><?php echo wp_kses_post( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr class="remove_default" data-hide="text,password,tel,textarea,,checkbox,select,ywraq_multiselect,ywraq_datepicker,ywraq_timepicker,ywraq_heading,ywraq_acceptance,ywraq_upload,country">
				<td class="label"><?php esc_html_e( 'ID', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td>
					<select name="field_id">
						<option value="billing_state">billing_state</option>
						<option value="shipping_state">shipping_state</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="label"><?php esc_html_e( 'Label', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td><input type="text" name="field_label"/></td>
			</tr>
			<tr data-hide="ywraq_heading,checkbox,radio,heading,ywraq_upload,state,country,ywraq_acceptance">
				<td class="label"><?php esc_html_e( 'Placeholder', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td><input type="text" name="field_placeholder"/></td>
			</tr>
			<tr data-hide="text,password,tel,textarea,radio,checkbox,select,ywraq_multiselect,ywraq_datepicker,ywraq_timepicker,ywraq_heading,state,country,ywraq_upload">
				<td class="label"><?php esc_html_e( 'Description', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td><textarea name="field_description" columns="10" rows="5"></textarea>
					<small><?php esc_html_e( 'You can use the shortcode [terms] and [privacy_policy] (from WooCommerce 3.4.0)', 'yith-woocommerce-request-a-quote' ); ?></small>
				</td>
			</tr>
			<tr class="remove_default" data-hide="text,password,tel,textarea,radio,checkbox,select,ywraq_multiselect,ywraq_datepicker,ywraq_timepicker,ywraq_heading,state,country,ywraq_acceptance">
				<td class="label"><?php esc_html_e( 'Allowed extensions', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td><input type="text" name="field_upload_allowed_extensions" placeholder="" value="<?php echo esc_html( apply_filters( 'ywraq_allowed_extension_for_upload_field', 'jpg,doc,png' ) ); ?>"/>
					<small><?php esc_html_e( 'Add a list of allowed extensions comma separated.', 'yith-woocommerce-request-a-quote' ); ?></small>
				</td>
			</tr>
			<tr class="remove_default" data-hide="text,password,tel,textarea,radio,checkbox,select,ywraq_multiselect,ywraq_datepicker,ywraq_timepicker,ywraq_heading,state,country,ywraq_acceptance">
				<td class="label"><?php esc_html_e( 'Max filesize (MB):', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td><input type="text" name="field_max_filesize" placeholder="" value="<?php echo esc_html( $upload_max_filesize ); ?>"/>
					<small><?php esc_html_e( 'Add the max filesize of upload file', 'yith-woocommerce-request-a-quote' ); ?></small>
				</td>
			</tr>
			<tr class="remove_default" data-hide="text,password,tel,textarea,ywraq_datepicker,checkbox,ywraq_heading,ywraq_timepicker,ywraq_upload,state,country,ywraq_acceptance">
				<td class="label"><?php esc_html_e( 'Options', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td><input type="text" name="field_options" placeholder=""/>
					<small><?php esc_html_e( 'Separate options with pipes (|) and key from value using (::). Es. key::value|', 'yith-woocommerce-request-a-quote' ); ?></small>
				</td>
			</tr>
			<?php if ( isset( $positions ) && is_array( $positions ) ) : ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Position', 'yith-woocommerce-request-a-quote' ); ?></td>
					<td>
						<select name="field_position" />
						<?php foreach ( $positions as $pos => $pos_label ) : ?>
							<option value="<?php echo esc_html( $pos ); ?>"><?php echo esc_html( $pos_label ); ?></option>
						<?php endforeach; ?>
						</select>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<td class="label"><?php esc_html_e( 'Class', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td><input type="text" name="field_class" placeholder=""/>
					<small><?php esc_html_e( 'Separate classes with commas', 'yith-woocommerce-request-a-quote' ); ?></small>
				</td>
			</tr>
			<tr data-hide="ywraq_heading">
				<td class="label"><?php esc_html_e( 'Label class', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td><input type="text" name="field_label_class" placeholder=""/>
					<small><?php esc_html_e( 'Separate classes with commas', 'yith-woocommerce-request-a-quote' ); ?></small>
				</td>
			</tr>
			<tr data-hide="ywraq_heading,ywraq_upload,ywraq_acceptance">
				<td class="label"><?php esc_html_e( 'Connect field to', 'yith-woocommerce-request-a-quote' ); ?></td>
				<td>
					<select name="field_connect_to_field"/>
					<?php foreach ( $connect_to_fields as $connect_to_field ) : ?>
						<option value="<?php echo esc_html( $connect_to_field ); ?>"><?php echo esc_html( $connect_to_field ); ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<?php if ( isset( $validation ) && is_array( $validation ) ) : ?>
				<tr data-hide="ywraq_heading">
					<td class="label"><?php esc_html_e( 'Validation', 'yith-woocommerce-request-a-quote' ); ?></td>
					<td>
						<select name="field_validate"/>
						<?php foreach ( $validation as $valid_rule => $valid_label ) : ?>
							<option value="<?php echo esc_html( $valid_rule ); ?>"><?php echo esc_html( $valid_label ); ?></option>
						<?php endforeach; ?>
						</select>
					</td>
				</tr>
			<?php endif; ?>
			<tr data-hide="text,password,tel,textarea,ywraq_datepicker,ywraq_heading,ywraq_timepicker,ywraq_upload,state,country,ywraq_acceptance,select,ywraq_multiselect,radio">
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" name="field_checked" value="1" checked/>
					<label for="field_checked"><?php esc_html_e( 'Checked', 'yith-woocommerce-request-a-quote' ); ?></label>
				</td>
			</tr>
			<tr data-hide="ywraq_heading">
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" name="field_required" value="1" checked/>
					<label for="field_required"><?php esc_html_e( 'Required', 'yith-woocommerce-request-a-quote' ); ?></label>
				</td>
			</tr>

		</table>
	</form>
</div>
