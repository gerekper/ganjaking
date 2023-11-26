<?php
if(isset($_GET['delplusmem']))
{
	$rtwwdpd_products_option = get_option('rtwwdpd_add_member');
	$rtwwdpd_row_no = sanitize_post($_GET['delplusmem']);
	array_splice($rtwwdpd_products_option, $rtwwdpd_row_no, 1);
	update_option('rtwwdpd_add_member',$rtwwdpd_products_option);
	$rtwwdpd_new_url = admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_plus_member';
	header('Location: '.$rtwwdpd_new_url);
    die();
}
$sabcd = 'verification_done';
if(isset($_POST['rtwwdpd_add_member'])){
		$rtwwdpd_prod = $_POST;
		$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['edit_plusmem']);

		$rtwwdpd_products_option = get_option('rtwwdpd_add_member');
		if($rtwwdpd_products_option == '')
		{
			$rtwwdpd_products_option = array();
		}
		$rtwwdpd_products = array();
		$rtwwdpd_products_array = array();

		foreach($rtwwdpd_prod as $key => $val){
			$rtwwdpd_products[$key] = $val;
		}
		if($rtwwdpd_option_no != 'save'){
			unset($_REQUEST['edit_plusmem']);
			$rtwwdpd_products_option[$rtwwdpd_option_no] = $rtwwdpd_products;
		}
		else{
			$rtwwdpd_products_option[] = $rtwwdpd_products;
		}
		update_option('rtwwdpd_add_member',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Settings saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div>
<?php
}

if(isset($_POST['rtwwdpd_copy_combi_rule'])){
	$rtwwdpd_prod = $_POST;
	$rtwwdpd_option_no = sanitize_post($rtwwdpd_prod['copy_rule_no']);
	$rtwwdpd_products_option = get_option('rtwwdpd_add_member');
	if($rtwwdpd_products_option == '')
	{
		$rtwwdpd_products_option = array();
	}
	$rtwwdpd_products = array();
	$rtwwdpd_products_array = array();

	foreach($rtwwdpd_prod as $key => $val){
		$rtwwdpd_products[$key] = $val;
	}

	if($rtwwdpd_option_no != 'save'){
		$rtwwdpd_products_option[] = $rtwwdpd_products_option[$rtwwdpd_option_no];
	}
	
	update_option('rtwwdpd_add_member',$rtwwdpd_products_option);

	?>
	<div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Rule copied.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div><?php
}

$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
{
if(isset($_GET['edit_plusmem']))
{ 
	$rtwwdpd_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
	
	$rtwwdpd_prev_opt = get_option('rtwwdpd_add_member');
	$rtwwdpd_prev_prod = $rtwwdpd_prev_opt[$_GET['edit_plusmem']];
	$key = 'edit_plusmem';
	$filteredURL = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $rtwwdpd_url);
	$rtwwdpd_new_url = esc_url( admin_url().'admin.php?page=rtwwdpd&rtwwdpd_tab=rtwwdpd_plus_member');
?>

<div class="rtwwdpd_add_combi_rule_tab rtwwdpd_active rtwwdpd_form_layout_wrapper">
	<form action="<?php echo esc_url($rtwwdpd_new_url); ?>" method="POST" accept-charset="utf-8">
		<div id="woocommerce-product-data" class="postbox ">
			<div class="options_group rtwwdpd_active" id="rtwwdpd_plus">
				<input type="hidden" name="edit_plusmem" id="edit_plusmem" value="<?php echo esc_attr($_GET['edit_plusmem']); ?>">
        			<table class='rtw_plus_member'>
	        			<tr>
	            		<th class="tr1"><?php esc_html_e('Minimum Previous Orders', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
							<td class="tr2">
								<input type="number" name="rtwwdpd_min_orders"
								value="<?php echo esc_attr($rtwwdpd_prev_prod['rtwwdpd_min_orders']); ?>" min="0" />
								<div class="descr"><?php esc_html_e('Minimum number of previous orders done by a customer to be eligible to become a plus member.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
							</td>
						</tr>
						<tr>
							<th class="tr1"><?php esc_html_e('Minimum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
							<td class="tr2">
								<input type="number" name="rtwwdpd_purchase_amt"
								value="<?php echo esc_attr(isset($rtwwdpd_prev_prod['rtwwdpd_purchase_amt']) ? $rtwwdpd_prev_prod['rtwwdpd_purchase_amt'] : ''); ?>" min="0" />
								<div class="descr"><?php esc_html_e('Minimum amount spent by a customer to be eligible to become a plus member.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
							</td>
						</tr>
						<tr>
							<th class="tr1"><?php esc_html_e('Minimum Purschased Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
							<td class="tr2">
								<input type="number" name="rtwwdpd_purchase_prodt"
								value="<?php echo esc_attr($rtwwdpd_prev_prod['rtwwdpd_purchase_prodt']); ?>" min="0" />
								<div class="descr"><?php esc_html_e('Minimum number of purchased product done by a customer to be eligible to become a plus member.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
							</td>
						</tr>
					
						<?php
	            	global $wp_roles;
	            	$rtwwdpd_roles 	= $wp_roles->get_names();
	            	$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
	            	$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );
            		?>
            		
						<tr>
							<th class="tr1"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
							<td class="tr2">
								<select multiple="multiple" class="rtwwdpd_select_roles" name="rtwwdpd_roles[]">
									<?php
									$rtwwdpd_rolee = isset($rtwwdpd_prev_prod['rtwwdpd_roles']) ? $rtwwdpd_prev_prod['rtwwdpd_roles'] : array();
									foreach ( $rtwwdpd_roles as $key => $value ) 
									{
										?>
										<option value="<?php echo esc_attr($key);?>" 
										<?php foreach ($rtwwdpd_rolee as $k => $v) {
										 if($key == $v) { echo 'selected';}} ?>>
											<?php echo esc_html( $value);?>
										</option>
										<?php
									}
									?>
								</select>
								<div class="descr"><?php esc_html_e('Role of the customer to become plus member', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
							</td>
						</tr>
						<tr>
							<th class="tr1"><?php esc_html_e('User is registered for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
							<td class="tr2">
								<select name="rtw_user_regis_for">
									<option <?php selected($rtwwdpd_prev_prod['rtw_user_regis_for'], 'less3mnth') ?> value="less3mnth"><?php esc_html_e('Less than 3 months', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></option>
									<option <?php selected($rtwwdpd_prev_prod['rtw_user_regis_for'], 'more3mnth') ?>  value="more3mnth"><?php esc_html_e('More than 3 months', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></option>
									<option <?php selected($rtwwdpd_prev_prod['rtw_user_regis_for'], 'frm6mnth') ?> value="more6mnth"><?php esc_html_e('More than 6 months', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></option>
									<option <?php selected($rtwwdpd_prev_prod['rtw_user_regis_for'], 'more1yr') ?> value="more1yr"><?php esc_html_e('More than 1 year', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></option>
								</select>
								<div class="descr"><?php esc_html_e('User registered for minimum this time to become a plus member.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="rtwwdpd_prod_single_save_cancel">
				<input class="rtw-button rtwwdpd_save_member" type="submit" name="rtwwdpd_add_member" value="<?php esc_attr_e( 'Update Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
				<input class="rtw-button rtwwdpd_cancl_rule" type="submit" name="rtwwdpd_cancel_add_mem" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			</div>
		</form>
	</div>
<?php }
else {?>
<div class="rtwwdpd_add_combi_rule_tab rtwwdpd_form_layout_wrapper">
	<form action="" method="POST" accept-charset="utf-8">
		<div id="woocommerce-product-data" class="postbox ">
			<div class="options_group rtwwdpd_actives" id="rtwwdpd_plus">
				<input type="hidden" name="edit_plusmem" id="edit_plusmem" value="save">
         		<table class='rtw_plus_member'>
         			<tr>
            			<th class="tr1"><?php esc_html_e('Minimum Previous Orders', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
						<td class="tr2">
							<input type="number" name="rtwwdpd_min_orders"
							value="0" min="0" />
							<div class="descr"><?php esc_html_e('Minimum number of previous orders done by a customer to be eligible to become a plus member.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
						</td>
					</tr>
					<tr>
						<th class="tr1"><?php esc_html_e('Minimum Purchase Amount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
						<td class="tr2">
							<input type="number" name="rtwwdpd_purchase_amt"
							value="0" min="0" />
							<div class="descr"><?php esc_html_e('Minimum amount spent by a customer to be eligible to become a plus member.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
						</td>
					</tr>
					<tr>
						<th class="tr1"><?php esc_html_e('Minimum Purschased Products', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
						<td class="tr2">
							<input type="number" name="rtwwdpd_purchase_prodt"
							value="0" min="0" />
							<div class="descr"><?php esc_html_e('Minimum number of purchased product done by a customer to be eligible to become a plus member.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
						</td>
					</tr>
						<?php
	            	global $wp_roles;
	            	$rtwwdpd_roles 	= $wp_roles->get_names();
	            	$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
	            	$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );
	            		?>
	            		
					<tr>
						<th class="tr1"><?php esc_html_e('Allowed Roles', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
						<td class="tr2">
							<select multiple="multiple" class="rtwwdpd_select_roles" name="rtwwdpd_roles[]">
								<?php
								foreach ( $rtwwdpd_roles as $key => $value ) 
								{
									?>
									<option value="<?php echo esc_attr($key);?>">
										<?php echo esc_html( $value);?>
									</option>
									<?php
								}
								?>
							</select>
							<div class="descr"><?php esc_html_e('Role of the customer to become plus member', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
						</td>
					</tr>
					<tr>
						<th class="tr1"><?php esc_html_e('User is registered for', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></th>
						<td class="tr2">
							<select name="rtw_user_regis_for">
								<option value="less3mnth"><?php esc_html_e('Less than 3 months', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></option>
								<option value="more3mnth"><?php esc_html_e('More than 3 months', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></option>
								<option value="more6mnth"><?php esc_html_e('More than 6 months', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></option>
								<option value="more1yr"><?php esc_html_e('More than 1 year', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></option>
							</select>
							<div class="descr"><?php esc_html_e('User registered for minimum this time to become a plus member.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai');?></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="rtwwdpd_prod_single_save_cancel">
			<input class="rtw-button rtwwdpd_save_member" type="submit" name="rtwwdpd_add_member" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
			<input class="rtw-button rtwwdpd_cancl_rule" type="button" name="rtwwdpd_cancel_add_mem" value="<?php esc_attr_e( 'Cancel', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
		</div>
	</form>
</div>

<?php  }
if(isset($_GET['editplusmem']))
{
	echo '<div class="rtwwdpd_prod_c_table_edit">';
}
else{
	echo '<div class="rtwwdpd_prod_c_table">';
}
?>
	<table class="rtwtable table table-striped table-bordered dt-responsive nowrap" cellspacing="0">
		<thead>
			<tr>
		    	<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Order Done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Purchase Amt', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Purchased Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'User Role', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Registered from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		  	</tr>
		</thead>
		<?php $rtwwdpd_products_option = get_option('rtwwdpd_add_member');
		$rtwwdpd_absolute_url = esc_url( admin_url('admin.php').add_query_arg($_GET,$wp->request));
		global $wp_roles;
    	$rtwwdpd_roles 	= $wp_roles->get_names();
    	$rtwwdpd_role_all 	= esc_html__( 'All', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );
    	$rtwwdpd_roles 	= array_merge( array( 'all' => $rtwwdpd_role_all ), $rtwwdpd_roles );
		if(is_array($rtwwdpd_products_option) && !empty($rtwwdpd_products_option)){	?>
		<tbody>
			<?php
			foreach ($rtwwdpd_products_option as $key => $value) {
				echo '<tr>';
				echo '<td>'.esc_html( $key+1 ).'<form action="" enctype="multipart/form-data" method="POST" accept-charset="utf-8"><input type="hidden" name="copy_rule_no" value="'.$key.'"><input class="rtwwdpd_copy_button" type="submit" name="rtwwdpd_copy_combi_rule" value="Copy"></form></td>';
				echo '<td class="rtw_drag"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"/></td>';
				
				echo '<td>'.esc_html($value['rtwwdpd_min_orders'] ).'</td>';
				
				echo '<td>'.esc_html($value['rtwwdpd_purchase_amt'] ).'</td>';
				
				echo '<td>'.esc_html($value['rtwwdpd_purchase_prodt'] ).'</td>';

				echo '<td>';
				if(isset($value['rtwwdpd_roles']) && is_array($value['rtwwdpd_roles']) && !empty($value['rtwwdpd_roles']))
				{
					foreach ($value['rtwwdpd_roles'] as $val)
					{
						echo esc_html($rtwwdpd_roles[$val]).'<br>';
					}
				}
				echo '</td>';
				
				echo '<td>'.esc_html($value['rtw_user_regis_for'] ).'</td>';

				echo '<td><a href="'.esc_url( $rtwwdpd_absolute_url .'&edit_plusmem='.$key ).'"><input type="button" class="rtw_plus_member rtwwdpd_edit_dt_row" value="Edit" /></a>
						<a href="'.esc_url( $rtwwdpd_absolute_url .'&delplusmem='.$key ).'"><input type="button" class="rtw_delete_row rtwwdpd_delete_dt_row" value="Delete"/></a></td>';
				echo '</tr>';
			}
			?>		
		</tbody>
		<?php } ?>
		<tfoot>
			<tr>
		    	<th><?php esc_html_e( 'Rule No.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Drag', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Order Done', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Purchase Amt', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Min Purchased Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'User Role', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Registered from', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		    	<th><?php esc_html_e( 'Actions', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?></th>
		  	</tr>
		</tfoot>
	</table>
</div>
<?php }
	else{
		include_once( RTWWDPD_DIR . 'admin/partials/rtwwdpd_without_verify.php' );
	}
