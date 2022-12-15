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
		<a href="#" class="button btn-add-warranty"><?php esc_html_e( 'Add Add-on', 'wc_warranty' ); ?></a>
		<table class="wp-list-table widefat fixed posts generic-table <?php echo esc_attr( $value['class'] ); ?>" style="min-width: 200px; width: 200px;">
			<thead>
			<tr>
				<th class="manage-column" style="padding-left: 20px;"><?php esc_html_e( 'Cost', 'wc_warranty' ); ?></th>
				<th class="manage-column"><?php esc_html_e( 'Duration', 'wc_warranty' ); ?></th>
				<th class="manage-column" width="50">&nbsp;</th>
			</tr>
			</thead>
			<tbody class="addons-tbody">
			<?php foreach ( $addons as $idx => $addon ) : ?>
				<tr>
					<td valign="middle">
						<span class="input"><b>+</b> <?php echo esc_html( $currency ); ?></span>
						<input type="text" name="addon_warranty_amount[]" id="addon_warranty_amount_<?php echo esc_attr( $idx ); ?>" class="input-text sized" size="2" value="<?php echo esc_attr( $addon['amount'] ); ?>" />
					</td>
					<td valign="middle">
						<input type="text" class="input-text sized" size="2" name="addon_warranty_length_value[]" value="<?php echo esc_attr( $addon['value'] ); ?>" />
						<select name="addon_warranty_length_duration[]">
							<option <?php selected( $addon['duration'], 'days' ); ?> value="days"><?php esc_html_e( 'Days', 'wc_warranty' ); ?></option>
							<option <?php selected( $addon['duration'], 'weeks' ); ?> value="weeks"><?php esc_html_e( 'Weeks', 'wc_warranty' ); ?></option>
							<option <?php selected( $addon['duration'], 'months' ); ?> value="months"><?php esc_html_e( 'Months', 'wc_warranty' ); ?></option>
							<option <?php selected( $addon['duration'], 'years' ); ?> value="years"><?php esc_html_e( 'Years', 'wc_warranty' ); ?></option>
						</select>
					</td>
					<td align="right"><a class="button warranty_addon_remove" href="#">&times;</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>

		</table>
		<script type="text/javascript">
			var tmpl = '<tr>\
							<td valign=\"middle\">\
								<span class=\"input\"><b>+</b> <?php echo esc_html( $currency ); ?></span>\
								<input type=\"text\" name=\"addon_warranty_amount[]\" class=\"input-text sized\" size=\"2\" value=\"\" />\
							</td>\
							<td valign=\"middle\">\
								<input type=\"text\" class=\"input-text sized\" size=\"2\" name=\"addon_warranty_length_value[]\" value=\"\" />\
								<select name=\"addon_warranty_length_duration[]\">\
									<option value=\"days\"><?php esc_html_e( 'Days', 'wc_warranty' ); ?></option>\
									<option value=\"weeks\"><?php esc_html_e( 'Weeks', 'wc_warranty' ); ?></option>\
									<option value=\"months\"><?php esc_html_e( 'Months', 'wc_warranty' ); ?></option>\
									<option value=\"years\"><?php esc_html_e( 'Years', 'wc_warranty' ); ?></option>\
								</select>\
							</td>\
							<td align=\"right\"><a class=\"button warranty_addon_remove\" href=\"#\">&times;</a></td>\
						</tr>';
			jQuery( document ).ready( function( $ ) {
				$( '.btn-add-warranty' ).click( function( e ) {
					e.preventDefault();

					var t = tmpl;
					$( this ).parents( 'tr' ).find( '.addons-tbody' ).append( t );
				} );

				$( '.warranty_addon_remove' ).on( 'click', function( e ) {
					e.preventDefault();

					$( this ).parents( 'tr' ).eq( 0 ).remove();
				} );
			} );
		</script>
	</td>
</tr>
