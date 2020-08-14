<?php
if( !defined( 'ABSPATH' ) ){
	exit;
}

$description = get_option( 'ywcdd_user_privacy_description' );

?>
<p class="form-row">
	<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
		<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="ywcdd_send_email" id="ywcdd_send_email" />
		<span class="woocommerce-terms-and-conditions-checkbox-text"><?php echo $description;?></span>&nbsp;
	</label>
</p>