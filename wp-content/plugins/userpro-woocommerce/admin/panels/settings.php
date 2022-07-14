<?php
	$upw_default_options = new UPWDefaultOptions();
?>

<form method="post" action="">



<h3><?php _e('Order Tab Settings','userpro-woocommerce'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="upw_hide_orders"><?php _e('Hide orders tab','userpro-woocommerce'); ?></label></th>
		<td>
			<select name="upw_hide_orders" id="upw_hide_orders" class="chosen-select" style="width:300px">
				<option value="y" <?php selected('y', $upw_default_options->userpro_woocommerce_get_option('upw_hide_orders')); ?>><?php _e('Yes','userpro-woocommerce'); ?></option>
				<option value="n" <?php selected('n', $upw_default_options->userpro_woocommerce_get_option('upw_hide_orders')); ?>><?php _e('No','userpro-woocommerce'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr>
		<th scope="row"><label for="upw_order_tab_text"><?php _e('Order tab text','userpro-woocommerce'); ?></label></th>
		<td>
		<input type="text" name = "upw_order_tab_text" id = "upw_order_tab_text" value="<?php echo $upw_default_options->userpro_woocommerce_get_option('upw_order_tab_text'); ?>">
		</td>
	</tr>

</table>

<h3><?php _e('Purchase Tab Settings','userpro-woocommerce'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="upw_hide_purchases"><?php _e('Hide purchase tab','userpro-woocommerce'); ?></label></th>
		<td>
			<select name="upw_hide_purchases" id="upw_hide_purchases" class="chosen-select" style="width:300px">
				<option value="y" <?php selected('y', $upw_default_options->userpro_woocommerce_get_option('upw_hide_purchases')); ?>><?php _e('Yes','userpro-woocommerce'); ?></option>
				<option value="n" <?php selected('n', $upw_default_options->userpro_woocommerce_get_option('upw_hide_purchases')); ?>><?php _e('No','userpro-woocommerce'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr>
		<th scope="row"><label for="upw_purchase_tab_text"><?php _e('Purchase tab text','userpro-woocommerce'); ?></label></th>
		<td>
		<input type="text" name = "upw_purchase_tab_text" id = "upw_purchase_tab_text" value="<?php echo $upw_default_options->userpro_woocommerce_get_option('upw_purchase_tab_text'); ?>">
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="upw_total_products_show"><?php _e('Total Number of products to be shown in purchase tab','userpro-woocommerce'); ?></label></th>
		<td>
		<input type="text" name = "upw_total_products_show" id = "upw_total_products_show" value="<?php echo $upw_default_options->userpro_woocommerce_get_option('upw_total_products_show'); ?>">
		</td>
	</tr>	
	
</table>

<h3><?php _e('UserPro Login / Registration','userpro-woocommerce'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="upw_userprologin"><?php _e('Enable UserPro Login / Registration','userpro-woocommerce'); ?></label></th>
		<td>
			<select name="upw_userprologin" id="upw_userprologin" class="chosen-select" style="width:300px">
				<option value="y" <?php selected('y', $upw_default_options->userpro_woocommerce_get_option('upw_userprologin')); ?>><?php _e('Yes','userpro-woocommerce'); ?></option>
				<option value="n" <?php selected('n', $upw_default_options->userpro_woocommerce_get_option('upw_userprologin')); ?>><?php _e('No','userpro-woocommerce'); ?></option>
			</select>
		</td>
	</tr>	
</table>
<h3><?php _e('Wishlist Tab Settings','userpro-woocommerce'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="upw_show_wishlist"><?php _e('Show Wishlist tab','userpro-woocommerce'); ?></label></th>
		<td>
			<select name="upw_show_wishlist" id="upw_show_wishlist" class="chosen-select" style="width:300px">
				<option value="n" <?php selected('n', $upw_default_options->userpro_woocommerce_get_option('upw_show_wishlist')); ?>><?php _e('No','userpro-woocommerce'); ?></option>
                                <option value="y" <?php selected('y', $upw_default_options->userpro_woocommerce_get_option('upw_show_wishlist')); ?>><?php _e('Yes','userpro-woocommerce'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr>
		<th scope="row"><label for="upw_wishlist_tab_text"><?php _e('Wishlist tab text','userpro-woocommerce'); ?></label></th>
		<td>
		<input type="text" name = "upw_wishlist_tab_text" id = "upw_wishlist_tab_text" value="<?php echo $upw_default_options->userpro_woocommerce_get_option('upw_wishlist_tab_text'); ?>">
		</td>
	</tr>

</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-woocommerce'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-woocommerce'); ?>"  />
</p>

</form>
