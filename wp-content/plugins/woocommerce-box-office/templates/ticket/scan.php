<div class="woocommerce">
	<div id="ticket-scan-form">
		<form name="ticket-scan" action="" method="post">
			<select name="scan-action" id="scan-action" class="scan_action" required>
				<option value=""<?php selected( $action, '' ); ?>><?php esc_html_e( 'Select action', 'woocommerce-box-office' ); ?></option>
				<option value="lookup"<?php selected( $action, 'lookup' ); ?>><?php esc_html_e( 'Look up', 'woocommerce-box-office' ); ?></option>
				<option value="attended"<?php selected( $action, 'attended' ); ?>><?php esc_html_e( 'Mark as attended', 'woocommerce-box-office' ); ?></option>
			</select>

			<input type="text" name="scan-code" id="scan-code" value="" placeholder="<?php esc_attr_e( 'Scan or enter ticket barcode', 'woocommerce-box-office' ); ?>" required />
			<input type="submit" value="<?php esc_attr_e( 'Go', 'woocommerce-box-office' ); ?>" />
		</form>
	  </div>

	<div id="ticket-scan-loader"><?php esc_html_e( 'Processing ticket...', 'woocommerce-box-office' ); ?></div>
	<div id="ticket-scan-result"></div>
</div>
