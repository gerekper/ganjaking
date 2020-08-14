<?php

//  if a capability is set, check if the current have the capability before going on
if ( $fields['capability'] && ( $fields['capability'] != 'all' ) && ! current_user_can( $fields['capability'] ) ) {
	return;
}

$actions = array_map( 'trim', explode( ",", $fields['actions'] ) );
if ( ! $actions ) {
	return;
} ?>
<div class="yith-barcode-actions">
	<h2><?php echo $title; ?></h2>
	<form name="yith-barcodes-form"
	      method="post"
	      data-barcode-type="<?php echo $fields['search_type']; ?>"
	      data-barcode-actions='<?php echo json_encode( $actions ); ?>'>
		<label><?php _ex( 'Type or scan the barcode value here...',
				'placeholder for barcode text field',
				'yith-woocommerce-barcodes' ); ?>
		</label>
		<input type="text"
		       name="yith-barcode-value"
		       value=""
		       placeholder="<?php esc_html_e( 'Enter the barcode here', 'yith-woocommerce-barcodes' ); ?>">
	</form>
</div>