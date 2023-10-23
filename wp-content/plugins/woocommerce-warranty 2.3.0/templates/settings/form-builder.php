<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<tr valign="top">
	<td colspan="2">
		<div class="warranty_form">
			<ul id="warranty_form">
				<?php
				$inputs = is_array( $inputs ) ? $inputs : array();

				foreach ( $inputs as $input ) :
					$src        = '';
					$key        = esc_attr( $input->key );
					$input_type = esc_attr( $input->type );

					if ( ! array_key_exists( $key, $form['fields'] ) ) {
						continue;
					}

					$field = $form['fields'][ $key ];

					$src = '<div class="wfb-field wfb-field-' . $input_type . '" data-key="' . $key . '" id="wfb-field-' . $key . '">
										<div class="wfb-field-title">
											<h3>' . $types[ $input_type ]['label'] . '</h3>

											<div class="wfb-field-controls">
												<a class="toggle-field wfb-toggle" data-key="' . $key . '" href="#">&#9652;</a>
												<a class="remove-field wfb-remove" href="#">&times;</a>
											</div>
										</div>

										<div class="wfb-content" id="wfb-content-' . $key . '">
											<div class="wfb-field-content">
												<table class="form-table">
							';

					$options = explode( '|', $types[ $input_type ]['options'] );

					foreach ( $options as $option ) {
						$src .= '<tr>';

						$value = ( isset( $field[ $option ] ) ) ? $field[ $option ] : '';

						$img = '';
						if ( array_key_exists( $option, WooCommerce_Warranty::$tips ) ) {
							$img = '<img class="help_tip" data-tip="' . wc_sanitize_tooltip( WooCommerce_Warranty::$tips[ $option ] ) . '" src="' . plugins_url() . '/woocommerce/assets/images/help.png" height="16" width="16" />';
						}

						switch ( $option ) {
							case 'name':
								$src .= '<th>Name ' . $img . '</th><td><input type="text" name="fb_field[' . $key . '][name]" value="' . $value . '" /></td>';
								break;
							case 'label':
								$src .= '<th>Label ' . $img . '</th><td><input type="text" name="fb_field[' . $key . '][label]" value="' . $value . '" /></td>';
								break;
							case 'text':
								$src .= '<th>Text</th><td><textarea name="fb_field[' . $key . '][text]" rows="5" cols="35">' . $value . '</textarea></td>';
								break;
							case 'default':
								$src .= '<th>Default Value ' . $img . '</th><td><input type="text" name="fb_field[' . $key . '][default]" value="' . $value . '" /></td>';
								break;
							case 'rowscols':
								$rows = isset( $field['rows'] ) ? esc_attr( $field['rows'] ) : '';
								$cols = isset( $field['cols'] ) ? esc_attr( $field['cols'] ) : '';
								$src .= '<th>Size</th><td><input type="text" size="2" name="fb_field[' . $key . '][rows]" value="' . $rows . '" /><span class="description">Rows</span> <input type="text" size="2" name="fb_field[' . $key . '][cols]" value="' . $cols . '" /><span class="description">Columns</span>';
								break;
							case 'options':
								$src .= '<th>Options ' . $img . '</th><td><textarea name="fb_field[' . $key . '][options]" rows="3" cols="35">' . $value . '</textarea></td>';
								break;
							case 'multiple':
								$src .= '<th>Allow Multiple ' . $img . '</th><td><input type="checkbox" name="fb_field[' . $key . '][multiple]" value="yes" ' . checked( 'yes', $value, false ) . ' /></td>';
								break;
							case 'required':
								$src .= '<th>Required ' . $img . '</th><td><input type="checkbox" name="fb_field[' . $key . '][required]" value="yes" ' . checked( 'yes', $value, false ) . ' /></td>';
								break;
						}

						$src .= '</tr>';
					}

					$src .= '       </table>
										</div>
									</div>';
					echo '<li class="wfb-field" data-key="' . esc_attr( $key ) . '" data-type="' . esc_attr( $input_type ) . '">' . $src . '</li>'; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped.

					?>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="warranty_fields">
			<h4><?php esc_html_e( 'Available Form Fields', 'wc_warranty' ); ?></h4>

			<ul id="warranty_form_fields">
				<?php foreach ( $types as $key => $input_type ) : ?>
					<li>
						<a class="control button" href="#" data-type="<?php echo esc_attr( $key ); ?>" data-options="<?php echo esc_attr( $input_type['options'] ); ?>"><?php echo esc_html( $input_type['label'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<input type="hidden" name="form_fields" id="form_fields" value="<?php echo esc_attr( $form['inputs'] ); ?>" />
	</td>
</tr>
