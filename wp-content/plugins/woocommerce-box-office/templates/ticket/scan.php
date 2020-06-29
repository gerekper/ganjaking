<div class="woocommerce">
	<div id="ticket-scan-form">
		<form name="ticket-scan" action="" method="post">
			<select name="scan-action" id="scan-action" class="scan_action" required>
				<option value=""<?php selected( $action, '' ); ?>><?php _e( 'Select action', 'woocommerce-box-office' ); ?></option>
				<option value="lookup"<?php selected( $action, 'lookup' ); ?>><?php _e( 'Look up', 'woocommerce-box-office' ); ?></option>
				<option value="attended"<?php selected( $action, 'attended' ); ?>><?php _e( 'Mark as attended', 'woocommerce-box-office' ); ?></option>
			</select>

			<input type="text" name="scan-code" id="scan-code" value="" placeholder="<?php _e( 'Scan or enter ticket barcode', 'woocommerce-box-office' ); ?>" required />
			<input type="submit" value="<?php _e( 'Go', 'woocommerce-box-office' ); ?>" />
		</form>
	  </div>

	<div id="ticket-scan-loader"><?php _e( 'Processing ticket...', 'woocommerce-box-office' ); ?></div>
	<div id="ticket-scan-result"></div>
</div>
