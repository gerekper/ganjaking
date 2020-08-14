<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
	</th>
	<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> <?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
		<div class="store-banner">
			<div class="store-details">
				<p class="store-info">
					<span class="label"><b><?php esc_html_e( 'Status:', 'yith-woocommerce-active-campaign' ); ?></b></span>

					<mark class="completed tips" data-tip="<?php esc_attr_e( 'Correctly synchronized', 'yith-woocommerce-active-campaign' ); ?>"><?php esc_attr_e( 'OK', 'yith-woocommerce-active-campaign' ); ?></mark>
				</p>

				<p class="store-info">
					<span class="label"><b><?php esc_html_e( 'Name:', 'yith-woocommerce-active-campaign' ); ?></b></span>

					<?php echo ! empty( $connection->name ) ? esc_html( $connection->name ) : esc_html__( '&lt; Not Found &gt;', 'yith-woocommerce-active-campaign' ); ?>
				</p>
			</div>
			<div class="store-deactivate">
				<button id="yith_wcac_deep_data_delete_connection" class="button"><?php esc_html_e( 'Delete connection', 'yith-woocommerce-active-campaign' ); ?></button>
			</div>
		</div>
	</td>
</tr>