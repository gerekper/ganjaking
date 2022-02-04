<?php
/**
 * Admin View: Fields Table Edit Form
 *
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Checkout Manager
 */

defined( 'YWCCP' ) || exit; // Exit if accessed directly.

$field_types = ywccp_get_field_type();

$billing_fields    = ywccp_get_checkout_fields( 'billing' );
$shipping_fields   = ywccp_get_checkout_fields( 'shipping' );
$additional_fields = ywccp_get_checkout_fields( 'additional' );
$checkout_fields   = array_merge( $billing_fields, $shipping_fields, $additional_fields );

?>

<div id="ywccp_field_add_edit_form" style="display: none;">
	<form>
		<table>
			<tr class="remove_default">
				<td class="label"><?php esc_html_e( 'Name', 'yith-woocommerce-checkout-manager' ); ?></td>
				<td><input type="text" name="field_name[]"/></td>
			</tr>
			<tr class="remove_default">
				<td class="label"><?php esc_html_e( 'Type', 'yith-woocommerce-checkout-manager' ); ?></td>
				<td>
					<select name="field_type[]">
						<?php foreach ( $field_types as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="label"><?php esc_html_e( 'Label', 'yith-woocommerce-checkout-manager' ); ?></td>
				<td><input type="text" name="field_label[]"/></td>
			</tr>
			<tr data-hide="checkbox,radio,heading,number">
				<td class="label"><?php esc_html_e( 'Placeholder', 'yith-woocommerce-checkout-manager' ); ?></td>
				<td><input type="text" name="field_placeholder[]"/></td>
			</tr>
			<?php if ( 'yes' === get_option( 'ywccp-enable-tooltip-check', 'no' ) ) : ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Tooltip', 'yith-woocommerce-checkout-manager' ); ?></td>
					<td><input type="text" name="field_tooltip[]"/></td>
				</tr>
			<?php endif; ?>
			<tr class="remove_default" data-hide="text,number,password,tel,textarea,datepicker,checkbox,heading,timepicker">
				<td class="label">
					<?php esc_html_e( 'Options', 'yith-woocommerce-checkout-manager' ); ?>
				</td>
				<td>
					<input type="text" name="field_options[]" placeholder="<?php esc_attr_e( 'Separate options with pipes (|) and key from value using (::). Es. key::value|', 'yith-woocommerce-checkout-manager' ); ?>"/>
				</td>
			</tr>
			<?php if ( isset( $positions ) && is_array( $positions ) ) : ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Position', 'yith-woocommerce-checkout-manager' ); ?></td>
					<td>
						<select name="field_position[]"/>
						<?php foreach ( $positions as $pos => $pos_label ) : ?>
							<option value="<?php echo esc_attr( $pos ); ?>"><?php echo esc_html( $pos_label ); ?></option>
						<?php endforeach; ?>
						</select>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<td class="label"><?php esc_html_e( 'Class', 'yith-woocommerce-checkout-manager' ); ?></td>
				<td><input type="text" name="field_class[]" placeholder="<?php esc_attr_e( 'Separate classes with commas', 'yith-woocommerce-checkout-manager' ); ?>"/></td>
			</tr>
			<tr data-hide="heading">
				<td class="label"><?php esc_html_e( 'Label class', 'yith-woocommerce-checkout-manager' ); ?></td>
				<td><input type="text" name="field_label_class[]" placeholder="<?php esc_attr_e( 'Separate classes with commas', 'yith-woocommerce-checkout-manager' ); ?>"/></td>
			</tr>
			<?php if ( isset( $validation ) && is_array( $validation ) ) : ?>
				<tr data-hide="heading">
					<td class="label"><?php esc_html_e( 'Validation', 'yith-woocommerce-checkout-manager' ); ?></td>
					<td>
						<select name="field_validate[]"/>
						<?php foreach ( $validation as $valid_rule => $valid_label ) : ?>
							<option value="<?php echo esc_attr( $valid_rule ); ?>"><?php echo esc_html( $valid_label ); ?></option>
						<?php endforeach; ?>
						</select>
					</td>
				</tr>
			<?php endif; ?>

			<tr data-hide="heading">
				<td></td>
				<td>
					<input type="checkbox" name="field_required[]" value="1" checked/>
					<label for="field_required">
						<?php esc_html_e( 'Required', 'yith-woocommerce-checkout-manager' ); ?>
						<small>
							<?php
							// translators: %s stand for html tag <br>.
							echo sprintf( esc_html__( 'Please keep it disabled if you need to set at least one condition. %sThe field will be set as required according to the conditions configured', 'yith-woocommerce-checkout-manager' ), '<br>' );
							?>
						</small>
					</label>
				</td>
			</tr>

			<tr class="remove_default" data-hide="heading">
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" name="field_show_in_email[]" value="1" checked/>
					<label for="field_show_in_email"><?php esc_html_e( 'Display in emails', 'yith-woocommerce-checkout-manager' ); ?></label><br/>
					<input type="checkbox" name="field_show_in_order[]" value="1" checked/>
					<label for="field_show_in_order"><?php esc_html_e( 'Display in Order Detail Pages', 'yith-woocommerce-checkout-manager' ); ?></label>
				</td>
			</tr>

			<tr data-hide="heading" class="conditions">
				<table class="wrap-conditions">
					<tr>
						<th><?php esc_html_e( 'Field', 'yith-woocommerce-checkout-manager' ); ?></th>
						<th><?php esc_html_e( 'Condition', 'yith-woocommerce-checkout-manager' ); ?></th>
						<th class="value">
							<?php esc_html_e( 'Value *', 'yith-woocommerce-checkout-manager' ); ?>
							<small class="ywccp-tooltip">
								<?php
								// translators: %s stand for html tags like ul,li,p.
								echo sprintf( esc_html__( 'If the condition is %1$s%3$sselected products in cart%4$s%3$sat least one product in cart%4$s %3$sall selected categories in cart%4$s%3$sat least one selected category in cart%4$s%2$s please insert product/category IDs separated by comma', 'yith-woocommerce-checkout-manager' ), '<ul>', '</ul>', '<li>', '</li>', '<p>', '</p>' );
								?>
							</small>
						</th>
						<th><?php esc_html_e( 'Action', 'yith-woocommerce-checkout-manager' ); ?></th>
						<th class="required">
							<?php esc_html_e( 'Required *', 'yith-woocommerce-checkout-manager' ); ?>
							<small class="ywccp-tooltip"><?php esc_html_e( 'check if the current field has to be set as Required only if the condition is fulfilled. Make sure the default Required value (above) is not checked, otherwise this setting will not take effect.', 'yith-woocommerce-checkout-manager' ); ?></small>
						</th>
						<th></th>
					</tr>
					<tr class="single-condition">
						<td class="field-name">
							<select name="field_condition_input_name[]" class="condition-field first field_condition_input_name">
								<option disabled selected></option>
								<option value="products"><?php esc_html_e( 'Products in cart', 'yith-woocommerce-checkout-manager' ); ?></option>
								<?php foreach ( $checkout_fields as $key => $property ) : ?>
									<?php
									$label = '';
									if ( empty( $property['label'] ) ) {
										$label = str_replace( '_', ' ', $key );
									} elseif ( false !== strpos( $key, 'billing' ) ) {
										$label = '(billing) - ' . $property['label'];
									} elseif ( false !== strpos( $key, 'shipping' ) ) {
										$label = '(shipping) - ' . $property['label'];
									} elseif ( false !== strpos( $key, 'additional' ) ) {
										$label = '(additional) - ' . $property['label'];
									}
									?>
									<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
						<td class="condition-type">
							<select name="field_condition_type[]" class="condition-field condition-type">
								<option disabled selected></option>
								<option value="is-set"><?php esc_html_e( 'Is set', 'yith-woocommerce-checkout-manager' ); ?></option>
								<option value="is-empty"><?php esc_html_e( 'Is empty', 'yith-woocommerce-checkout-manager' ); ?></option>
								<option value="has-value"><?php esc_html_e( 'Value is', 'yith-woocommerce-checkout-manager' ); ?></option>
								<option value="has-not-value"><?php esc_html_e( 'Value is not', 'yith-woocommerce-checkout-manager' ); ?></option>
								<option value="all-in-cart"><?php esc_html_e( 'All selected products in cart', 'yith-woocommerce-checkout-manager' ); ?></option>
								<option value="at-least-one-product-in-cart"><?php esc_html_e( 'At least one selected product in cart', 'yith-woocommerce-checkout-manager' ); ?></option>
								<option value="all-categories-in-cart"><?php esc_html_e( 'All selected categories in cart', 'yith-woocommerce-checkout-manager' ); ?></option>
								<option value="at-least-one-category-in-cart"><?php esc_html_e( 'At least one selected category in cart', 'yith-woocommerce-checkout-manager' ); ?></option>
							</select>
						</td>
						<td class="condition-value">
							<input class="condition-field field_condition_value" type="text"
								name="field_condition_value[]" value="">
						</td>
						<td class="condition-action">
							<select class="condition-field" name="field_condition_action[]">
								<option disabled selected></option>
								<option value="show"><?php esc_html_e( 'Show', 'yith-woocommerce-checkout-manager' ); ?></option>
								<option value="hide"><?php esc_html_e( 'Hide', 'yith-woocommerce-checkout-manager' ); ?></option>
							</select>
						</td>
						<td class="condition-required">
							<input class="condition-field" type="checkbox" name="field_condition_required[]" value=""/>
						</td>
						<td class="wrap-plus-remove">
							<button class="add-new"></button>
							<button class="remove"></button>
						</td>
					</tr>
				</table>
			</tr>
		</table>
	</form>
</div>
