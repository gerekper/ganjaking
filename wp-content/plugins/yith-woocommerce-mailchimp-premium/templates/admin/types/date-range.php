<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
	</th>
	<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
		<label><?php _e( 'From', 'yith-woocommerce-mailchimp' ) ?></label>
		<input type="date" name="<?php echo esc_attr( $value['id'] ); ?>[from]" id="<?php echo esc_attr( $value['id'] ); ?>_from" placeholder="<?php _e( 'From', 'yith-woocommerce-mailchimp' ) ?>" />
		<label><?php _e( 'To', 'yith-woocommerce-mailchimp' ) ?></label>
		<input type="date" name="<?php echo esc_attr( $value['id'] ); ?>[to]" id="<?php echo esc_attr( $value['id'] ); ?>_to" placeholder="<?php _e( 'To', 'yith-woocommerce-mailchimp' ) ?>" />
	</td>
</tr>