<?php
if( !isset( $upw_default_options ) ){
	$upw_default_options = new UPWDefaultOptions();
}
?>

<form method="post" action="">
<h3><?php _e('Woocommerce Purchase Code','userpro-woocommerce'); ?></h3>
<table class="form-table">


	
	<tr>
		<th scope="row"><label for="upw_purchases_code"><?php _e('Enter your Item Purchase Code','userpro-woocommerce'); ?></label></th>
		<td>
		<input type="text" name = "upw_purchases_code" id = "upw_purchases_code" readonly="readonly" value="13z89fdcmr2ia646kphzg3bbz0jdpdja">
		</td>
	</tr>

</table>
</form>
