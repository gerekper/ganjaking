<?php global $wc_catalog_restrictions; ?>

<h3><?php _e( 'Catalog Visibility Location', 'wc_catalog_restrictions' ) ?></h3>

<table class="form-table">

	<?php if ( current_user_can( 'administrator' ) || $can_change ) : ?>
		<tr>
			<th><label><?php _e( 'Location', 'wc_catalog_restrictions' ) ?></label></th>
			<td>
				<?php woocommerce_catalog_restrictions_country_input( $location ); ?>
				<span class="description"><?php __( 'The location for the user', 'wc_catalog_restrictions' ); ?>.</span>
			</td>
		</tr>
	<?php endif; ?>

	<?php if ( current_user_can( 'administrator' ) ) : ?>
		<tr>
			<th><label><?php _e( 'Allow User to Change?', 'wc_catalog_restrictions' ) ?></label></th>
			<td>
				<select name="can_change">

					<option value="yes" <?php selected( $can_change, 'yes' ); ?>><?php _e( 'Yes', 'wc_catalog_restrictions' ) ?></option>
					<option value="no" <?php selected( $can_change, 'no' ); ?>><?php _e( 'No', 'wc_catalog_restrictions' ) ?></option>

				</select>
				<span class="description"><?php __( 'The location for the user', 'wc_catalog_restrictions' ); ?>.</span>
			</td>
		</tr>
	<?php endif; ?>
</table>