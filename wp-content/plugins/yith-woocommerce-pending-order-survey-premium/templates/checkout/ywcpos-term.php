<?php
if( !defined( 'ABSPATH' ) ){
	exit;
}

$description = get_option( 'ywcpos_user_privacy_description' );
?>
<p class="form-row">
	<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
		<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="ywcpos_term" id="ywcpos_terms" />
		<span class="woocommerce-terms-and-conditions-checkbox-text"><?php echo $description;?></span>&nbsp;
	</label>
