<div class="profileDashboard dashboardRight" id = "upw-shipping-address">
<div class="userpro-section userpro-column userpro-collapsible-1 userpro-collapsed-0" >
	<div class="upw-orders-tab-text">
		<?php _e( $upw_default_options->userpro_woocommerce_get_option( 'upw_shipping_address_tab_text' ), 'userpro-woocommerce');?>
	</div>
</div>
<?php
$user_id = get_current_user_id();
			if (!isset($user_id)) $user_id = 0;
			if (!isset($unique_id)) $unique_id = 0;
			$hook_args = array_merge($arg, array('user_id' => $user_id, 'unique_id' => $unique_id));

$woo_billing_shipping_fields = get_option('userpro_fields_woo');
?>

	<div class="userpro-dashboard userpro-<?php echo $unique_id; ?> userpro-id-<?php echo $user_id; ?> userpro-<?php echo $arg['layout']; ?>" <?php userpro_args_to_data( $arg ); ?>>

		<form action="" method="post" data-action="<?php echo 'edit'; ?>">
	<input type="hidden" name="user_id-<?php echo $unique_id; ?>" id="user_id-<?php echo $unique_id; ?>" value="<?php echo $user_id; ?>" />
	<?php
		do_action('userpro_before_fields', $hook_args);
			foreach( $woo_billing_shipping_fields as $key => $array ) {
				$array_key = explode("_",$key);
				if($array_key[0] == 'shipping'){
					if ($array) echo userpro_edit_field( $key, $array, $unique_id, $arg, $user_id );
				}				
			} ?>
			<div class="userpro-field userpro-submit userpro-column">		
					<?php if (isset($arg["edit_button_primary"]) ) { ?>
					<input type="submit" value="<?php echo $arg["edit_button_primary"]; ?>" class="userpro-button" />
					<?php } ?>
			</div>
		</form>

	</div>
</div>
