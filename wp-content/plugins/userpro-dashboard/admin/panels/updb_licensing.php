<?php
	if( !isset( $updb_default_options ) ){
		$updb_default_options = new UPDBDefaultOptions();
	}
?>
<form method="post" action="">

<h3><?php _e('Activate UserPro Dashboard','userpro-dashboard'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="userpro_dashboard_code"><?php _e('Enter your Item Purchase Code','userpro-dashboard'); ?></label></th>
		<td>
			<input type="text" name="userpro_dashboard_code" id="userpro_dashboard_code" readonly="readonly" value="13z89fdcmr2ia646kphzg3bbz0jdpdja" class="regular-text" />
		</td>
	</tr>
</table>
</form>
