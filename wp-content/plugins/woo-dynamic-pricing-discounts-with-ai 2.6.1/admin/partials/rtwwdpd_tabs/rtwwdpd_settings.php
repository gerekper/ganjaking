<?php

if(isset($_POST['rtwwdpd_save_setting']))
{
	$rtwwdpd_prod = $_POST;
   
	$rtwwdpd_products = array();
	$rtwwdpd_msgs = array();

	foreach($rtwwdpd_prod as $key => $val){
		if( $key == 'rtwwdpd_enable_message' || $key == 'rtwwdpd_message_text' || $key == 'rtwwdpd_message_position' || $key == 'rtwwdpd_message_pos_propage' )
		{
			$rtwwdpd_msgs[$key] = $val;
		}else{
			$rtwwdpd_products[$key] = $val;
		}
	}
	
	update_option('rtwwdpd_message_settings',$rtwwdpd_msgs);
	$rtwwdpd_products_option = $rtwwdpd_products;
	update_option('rtwwdpd_setting_priority',$rtwwdpd_products_option);

	?><div class="notice notice-success is-dismissible">
		<p><strong><?php esc_html_e('Settings saved.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notices.','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
		</button>
	</div>
	<?php
}
?>
<!-- <span class="dashicons dashicons-editor-help"></span> -->
<?php 
	$Apply_Coupon_setting = apply_filters('rtwwdpd_re-activate_coupon', '');
	echo $Apply_Coupon_setting;
?>
<form method="post" action="" enctype="multipart/form-data">
	<?php  ?>
	<div class="rtw_setting_order_cls">
	<caption class="rtw_set_cap"><h2 class="rtwcenter"><b><?php esc_html_e('Set Order for Rules','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></b></h2></caption>

		<table id="rtw_setting_tbl">
			<thead>
				<tr>
					<th class="rtwtenty"><?php esc_html_e('Set Priority','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
					<th><?php esc_html_e('All Rules','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
					<th><?php esc_html_e('Permission','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></th>
				</tr>
			</thead>
			<tbody id="rtw_set_body_tbl">
				<?php
				$rtwwdpd_setting_array = array();
				$rtwwdpd_setting_array = get_option('rtwwdpd_setting_priority');
				
				if( !is_array($rtwwdpd_setting_array) || empty($rtwwdpd_setting_array))
					{
						
						?>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Product Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="pro_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="1" name="pro_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Product Combination Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="pro_com_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="2" name="pro_com_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Bogo Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="bogo_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="3" name="bogo_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Bogo Category Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="bogo_cat_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="4" name="bogo_cat_rule_row"/>
							</td>
						</tr>

						

						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Cart Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="cart_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="5" name="cart_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Category Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="cat_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="6" name="cat_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Category Combination Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="cat_com_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="7" name="cat_com_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Tiered Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="tier_rule" type='checkbox' value="1"/>
								<input type="hidden" class="rtwrow_no" value="8" name="tier_rule_row"/> 
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Tier Category Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="tier_cat_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="9" name="tier_cat_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Variation Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="var_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="10" name="var_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Payment Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="pay_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="11" name="pay_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Attribute Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="attr_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="12" name="attr_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Product Tag Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="prod_tag_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="13" name="prod_tag_rule_row"/>
							</td>
						</tr>
						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Shipping Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="ship_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="14" name="ship_rule_row"/>
							</td>
						</tr>

						<tr>
							<td class="rtwupdwn"><img class="rtwdragimg" src="<?php echo esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png' ); ?>"></td>
							<td>
								<?php esc_html_e('Bogo Tag Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
							</td>
							<td>
								<input name="bogo_tag_rule" type='checkbox' value="1"/> 
								<input type="hidden" class="rtwrow_no" value="15" name="bogo_tag_rule_row"/>
							</td>
						</tr>
					<?php 
					}else
					{
						
						foreach ($rtwwdpd_setting_array as $key => $value) {

							if($value == 'on'){
								$checked = 'checked';
							}
							else{
								$checked ='';
							}
							echo '<tr>';

							if($key == 'var_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Variation Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</td>';

								echo '<td><input name="var_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['var_rule']) && $rtwwdpd_setting_array['var_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="var_rule_row"/></td>';

							}
							elseif($key == 'tier_cat_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Tier Category Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="tier_cat_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['tier_cat_rule']) && $rtwwdpd_setting_array['tier_cat_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="tier_cat_rule_row"/></td>';

							}
							elseif($key == 'tier_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Tiered Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="tier_rule" type="checkbox"  value="1" ';
								if(isset($rtwwdpd_setting_array['tier_rule']) && $rtwwdpd_setting_array['tier_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="tier_rule_row"/></td>';

							}
							elseif($key == 'cat_com_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Category Combination Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="cat_com_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['cat_com_rule']) && $rtwwdpd_setting_array['cat_com_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="cat_com_rule_row"/></td>';

							}
							elseif($key == 'cat_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Category Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="cat_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['cat_rule']) && $rtwwdpd_setting_array['cat_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="cat_rule_row"/></td>';

							}
							elseif($key == 'cart_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Cart Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="cart_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['cart_rule']) && $rtwwdpd_setting_array['cart_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="cart_rule_row"/></td>';

							}
							elseif($key == 'bogo_cat_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Bogo Category Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="bogo_cat_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['bogo_cat_rule']) && $rtwwdpd_setting_array['bogo_cat_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="bogo_cat_rule_row"/></td>';

							}
							elseif($key == 'bogo_tag_rule_row'){
								
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Bogo Tag Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="bogo_tag_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['bogo_tag_rule']) && $rtwwdpd_setting_array['bogo_tag_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="bogo_tag_rule_row"/></td>';

							}
							elseif($key == 'bogo_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Bogo Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="bogo_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['bogo_rule']) && $rtwwdpd_setting_array['bogo_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="bogo_rule_row"/></td>';

							}
							elseif($key == 'pro_com_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Product Combination Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="pro_com_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['pro_com_rule']) && $rtwwdpd_setting_array['pro_com_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="pro_com_rule_row"/></td>';

							}
							elseif($key == 'pro_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Product Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="pro_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['pro_rule']) && $rtwwdpd_setting_array['pro_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="pro_rule_row"/></td>';

							}
							elseif($key == 'pay_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Payment Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="pay_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['pay_rule']) && $rtwwdpd_setting_array['pay_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="pay_rule_row"/></td>';

							}
							elseif($key == 'ship_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Shipping Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="ship_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['ship_rule']) && $rtwwdpd_setting_array['ship_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="ship_rule_row"/></td>';

							}
							elseif($key == 'prod_tag_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Product Tag Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="prod_tag_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['prod_tag_rule']) && $rtwwdpd_setting_array['prod_tag_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="prod_tag_rule_row"/></td>';

							}
							elseif($key == 'attr_rule_row'){
								echo '<td class="rtwupdwn"><img class="rtwdragimg" src="'.esc_url( RTWWDPD_URL . 'assets/Datatables/images/dragndrop.png').'"></td>';
								echo '<td>' .esc_html__('Attribute Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'). '</td>';

								echo '<td><input name="attr_rule" type="checkbox" value="1" ';
								if(isset($rtwwdpd_setting_array['attr_rule']) && $rtwwdpd_setting_array['attr_rule'] == 1){
									echo 'checked';
								} 
								echo '/>';

								echo '<input type="hidden" class="rtwrow_no" value="" name="attr_rule_row"/></td>';
							}
							echo '</tr>';
						}
					}
					?>

				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php $rtwwdpd_gnrl_set = get_option('rtwwdpd_setting_priority'); 
		$message_settings = get_option('rtwwdpd_message_settings', array());
		?>
		<div class="rtw_general_setting_cls rtwwdpd_active">
			<h2 class="rtwcenter"><b><?php esc_html_e('General Setting','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></b></h2>
			<div id="woocommerce-product-data" class="postbox ">
				<div class="inside">
					<div class="panel-wrap product_data">
						<ul class="product_data_tabs wc-tabs">
							<li class="rtwwdpd_active active">
								<a class="rtwwdpd_link" id="rtwgnrl_set">
									<span><?php esc_html_e('General','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li>
								<a class="rtwwdpd_link" id="rtwoffer_set">
									<span><?php esc_html_e('Offer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li>
								<a class="rtwwdpd_link" id="rtwbogo_set">
									<span><?php esc_html_e('BOGO','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li>
								<a class="rtwwdpd_link" id="rtwmsg_set">
									<span><?php esc_html_e('Custom Message','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
							<li>
								<a class="rtwwdpd_link" id="rtwtimer_set">
									<span><?php esc_html_e('Countdown Timer','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></span>
								</a>
							</li>
						</ul>

						<div class="panel woocommerce_options_panel">
							<div class="options_group rtwwdpd_active" id="rtwgnrl_set_tab">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label><?php esc_html_e('Offer banner page Shortcode', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>
										<td>
											<label id="rtwwdpd_adj_shortcode"><?php  esc_html_e('[ShowOfferBanner]'); ?>
											</label>
											<p><?php esc_html_e('Use Shortcode to display discounted product on seperate page', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</p>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Apply Offer', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>
										<td>
											<select name="rtw_offer_select">
												<option value="rtw_first_match" <?php selected(isset($rtwwdpd_gnrl_set['rtw_offer_select']) ? $rtwwdpd_gnrl_set['rtw_offer_select'] : '' , 'rtw_first_match'); ?>>
													<?php esc_html_e('First Matched Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_best_discount" <?php selected(isset($rtwwdpd_gnrl_set['rtw_offer_select']) ? $rtwwdpd_gnrl_set['rtw_offer_select'] :'' , 'rtw_best_discount'); ?>>
													<?php esc_html_e('Best Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												<option value="rtw_all_mtch" <?php selected(isset($rtwwdpd_gnrl_set['rtw_offer_select']) ? $rtwwdpd_gnrl_set['rtw_offer_select'] : '' , 'rtw_all_mtch');?>><?php esc_html_e('All Matched Rules', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
											</select>
											<div class="rtwwdpd_description">
												<i><?php sprintf( '%s' ,
													esc_html_e( 'Rule to be applied.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
												);?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Display discounted price on Shop & Product page.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>
										<td>
											<input <?php checked(isset($rtwwdpd_gnrl_set['rtwwdpd_discounted_price']) ? $rtwwdpd_gnrl_set['rtwwdpd_discounted_price'] : 0 , 1); ?> type="checkbox" value="1" name="rtwwdpd_discounted_price">
											<div class="rtwwdpd_description">
												<i><?php esc_html_e( 'Display discounted price on Shop & Product page (This discounted price can be seen when applying All Match Rule or First Match Rule)..', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) ?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Discount On', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>
										<td>
											<select name="rtw_dscnt_on" value="<?php isset($rtwwdpd_gnrl_set['rtw_dscnt_on']) ? $rtwwdpd_gnrl_set['rtw_dscnt_on'] : ''; ?>">
												<option value="rtw_sale_price" 
													<?php selected(isset($rtwwdpd_gnrl_set['rtw_dscnt_on']) ? $rtwwdpd_gnrl_set['rtw_dscnt_on'] : '' , 'rtw_sale_price');?>>
													<?php esc_html_e('Sale Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_regular_price" 
													<?php selected(isset($rtwwdpd_gnrl_set['rtw_dscnt_on']) ? $rtwwdpd_gnrl_set['rtw_dscnt_on'] : '' , 'rtw_regular_price');?>>
													<?php esc_html_e('Regular Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '%s' ,
													esc_html_e( 'Apply discount on sale price/regular price.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ));?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Show come back on browser tab', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>									
										<td>
											<select name="rtw_show_combck">
												<option value="rtw_price_yes" <?php selected( isset($rtwwdpd_gnrl_set['rtw_show_combck']) ? $rtwwdpd_gnrl_set['rtw_show_combck'] : '' , 'rtw_price_yes');?>>
													<?php esc_html_e( 'Yes', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
												<option value="rtw_price_no" <?php selected( isset($rtwwdpd_gnrl_set['rtw_show_combck']) ? $rtwwdpd_gnrl_set['rtw_show_combck'] : '' , 'rtw_price_no');?>>
													<?php esc_html_e( 'No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '<u>%s:</u>' ,
														esc_html_e( 'Display offer table on shop page or not.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
													)	;?>
												</i>
											</div>
										</td>
									</tr>
									<?php 
									$rtwwdpd_site_pages = get_pages();
									$rtwwpdp_pages = array();
									foreach ($rtwwdpd_site_pages as $pages => $page) {
										$rtwwpdp_pages[ $page->ID ] = $page->post_title;
									} 
									$rtw_show_pages = isset( $rtwwdpd_gnrl_set['rtw_show_pages'] ) ? $rtwwdpd_gnrl_set['rtw_show_pages'] : array();
									?>								
									<tr>
										<td>
											<label><?php esc_html_e('Show on pages', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>									
										<td>
											<select id="rtwwdpd_selected_pages" class="rtwwdpd_select_roles" name="rtw_show_pages[]" multiple="multiple">
											<?php foreach ( $rtwwpdp_pages as $pages => $page) {
												if( in_array($pages, $rtw_show_pages) )
												{ ?>
													<option selected="selected" value="<?php echo esc_attr( $pages ); ?>">
													<?php esc_html_e( $page, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>

													</option>
												<?php 
												}else
												{ ?>
												
													<option value="<?php echo esc_attr( $pages ); ?>">

													<?php esc_html_e( $page, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>

													</option>

												<?php } } ?>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '<u>%s:</u>' ,
														esc_html_e( 'Select pages on which you want to show come back message.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
													)	;?>
												</i>
											</div>
										</td>
									</tr>	
									<tr>
										<td>
											<label><?php esc_html_e('Text to show on browser tab', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>

										</td>
										<td>
											<textarea id="rtwwdpd_text_to_show" name="rtwwdpd_text_combck"><?php echo esc_attr(isset( $rtwwdpd_gnrl_set['rtwwdpd_text_combck']) && $rtwwdpd_gnrl_set['rtwwdpd_text_combck'] != '' ? $rtwwdpd_gnrl_set['rtwwdpd_text_combck'] : 'Get Best Offer here..'); ?></textarea>
											<div class="rtwwdpd_description"/>
												<i>
													<?php
													esc_html_e( 'ex. Get Best Offer here..', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?>
												</i>
											</div>
										</td>
									</tr>
								</table>
							</div>
							<div class="options_group rtwwdpd_inactive" id="rtwoffer_set_tab">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label><?php esc_html_e('Display Offer on Shop Page', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>									
										<td>
											<select name="rtw_offer_show">
												<option value="rtw_price_yes" <?php selected( isset($rtwwdpd_gnrl_set['rtw_offer_show']) ? $rtwwdpd_gnrl_set['rtw_offer_show'] : '' , 'rtw_price_yes');?>>
													<?php esc_html_e( 'Yes', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
												<option value="rtw_price_no" <?php selected( isset($rtwwdpd_gnrl_set['rtw_offer_show']) ? $rtwwdpd_gnrl_set['rtw_offer_show'] : '' , 'rtw_price_no');?>>
													<?php esc_html_e( 'No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '<u>%s:</u>' ,
														esc_html_e( 'Display offer table on shop page or not.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
													)	;?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Position of Offer Table on Shop Page', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>
										<td>
											<select name="rtwwdpd_offer_tbl_pos">
												<option value="rtw_bfore_pro" <?php selected( isset($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos']) ? $rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos'] : '' , 'rtw_bfore_pro'); ?>>
													<?php esc_html_e( 'Before Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_pro" <?php selected( isset($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos']) ? $rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos'] : '', 'rtw_aftr_pro'); ?>>
													<?php esc_html_e( 'After Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_bfore_pro_sum" <?php selected( isset($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos']) ? $rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos'] : '', 'rtw_bfore_pro_sum'); ?>>
													<?php esc_html_e( 'Before Product Summary', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_in_pro_sum" <?php selected( isset($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos']) ? $rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos'] : '', 'rtw_in_pro_sum'); ?>>
													<?php esc_html_e( 'In Product Summary', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_pro_sum" <?php selected( isset($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos']) ? $rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_pos'] : '', 'rtw_aftr_pro_sum'); ?>>
													<?php esc_html_e( 'After Product Summary', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '<u>%s:</u>' ,
													esc_html_e( 'Specify price table position on shop page.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
													)	;?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Display Offer on Product Page', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>									
										<td>
											<select name="rtw_offer_on_product">
												<option value="rtw_price_yes" <?php selected( isset($rtwwdpd_gnrl_set['rtw_offer_on_product']) ? $rtwwdpd_gnrl_set['rtw_offer_on_product'] : '' , 'rtw_price_yes');?>>
													<?php esc_html_e( 'Yes', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
												<option value="rtw_price_no" <?php selected( isset($rtwwdpd_gnrl_set['rtw_offer_on_product']) ? $rtwwdpd_gnrl_set['rtw_offer_on_product'] : '', 'rtw_price_no');?>>
													<?php esc_html_e( 'No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '<u>%s:</u>' ,
														esc_html_e( 'Display offer table on shop page or not.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
													)	;?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Position of Offer Table on Product Page', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>
										<td>
											<?php 
											if(!isset($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct']))
											{
												$rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] = '';
											} 
											?>
											<select name="rtwwdpd_offer_tbl_prodct">
												<option value="rtw_bfore_pro" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_bfore_pro'); ?>>
													<?php esc_html_e( 'Before Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_pro" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_aftr_pro'); ?>>
													<?php esc_html_e( 'After Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_bfore_pro_sum" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_bfore_pro_sum'); ?>>
													<?php esc_html_e( 'Before Product Summary', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_in_pro_sum" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_in_pro_sum'); ?>>
													<?php esc_html_e( 'In Product Summary', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_pro_sum" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_aftr_pro_sum'); ?>>
													<?php esc_html_e( 'After Product Summary', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_bfre_add_cart_btn" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_bfre_add_cart_btn'); ?>>
													<?php esc_html_e( 'Before Add To Cart Button', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_add_cart_btn" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_aftr_add_cart_btn'); ?>>
													<?php esc_html_e( 'After Add To Cart Button', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_bfre_add_cart_frm" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_bfre_add_cart_frm'); ?>>
													<?php esc_html_e( 'Before Add To Cart Form', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_add_cart_frm" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_aftr_add_cart_frm'); ?>>
													<?php esc_html_e( 'After Add To Cart Form', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_pro_meta_strt" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_pro_meta_strt'); ?>>
													<?php esc_html_e( 'Product Meta Start', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_pro_meta_end" <?php selected($rtwwdpd_gnrl_set['rtwwdpd_offer_tbl_prodct'] , 'rtw_pro_meta_end'); ?>>
													<?php esc_html_e( 'Product Meta End', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '<u>%s:</u>' ,
													esc_html_e( 'Specify price table position on shop page.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
													)	;?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Display Cart Offer on cart Page', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>									
										<td>
											<select name="rtw_offer_on_cart">
												<option value="rtw_price_yes" <?php selected( isset($rtwwdpd_gnrl_set['rtw_offer_on_cart']) ? $rtwwdpd_gnrl_set['rtw_offer_on_cart'] : '' , 'rtw_price_yes');?>>
													<?php esc_html_e( 'Yes', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
												<option value="rtw_price_no" <?php selected( isset($rtwwdpd_gnrl_set['rtw_offer_on_cart']) ? $rtwwdpd_gnrl_set['rtw_offer_on_cart'] : '', 'rtw_price_no');?>>
													<?php esc_html_e( 'No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '<u>%s:</u>' ,
														esc_html_e( 'Display offer table on shop page or not.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
													)	;?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Display Tier Offer on cart Page', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>									
										<td>
											<select name="rtw_tier_offer_on_cart">
												<option value="rtw_price_yes" <?php selected( isset($rtwwdpd_gnrl_set['rtw_tier_offer_on_cart']) ? $rtwwdpd_gnrl_set['rtw_tier_offer_on_cart'] : '' , 'rtw_price_yes');?>>
													<?php esc_html_e( 'Yes', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
												<option value="rtw_price_no" <?php selected( isset($rtwwdpd_gnrl_set['rtw_tier_offer_on_cart']) ? $rtwwdpd_gnrl_set['rtw_tier_offer_on_cart'] : '', 'rtw_price_no');?>>
													<?php esc_html_e( 'No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>
												</option>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '<u>%s:</u>' ,
														esc_html_e( 'Display offer table on shop page or not.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
													)	;?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Show offer as', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>
										<td>
											<textarea name="rtwwdpd_text_to_show"><?php echo esc_attr(isset($rtwwdpd_gnrl_set['rtwwdpd_text_to_show']) && $rtwwdpd_gnrl_set['rtwwdpd_text_to_show'] != '' ? $rtwwdpd_gnrl_set['rtwwdpd_text_to_show'] : 'Get [discounted] Off'); ?></textarea>
											<div class="rtwwdpd_description"/>
												<i>
													<?php
													esc_html_e( 'ex. Get [discounted] Off', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?>
												</i>
												<p><?php esc_html_e('Use ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												<b><?php echo esc_html('[discounted]') ; ?></b>
												<?php esc_html_e('as shortcode for discounted value.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</p>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Show Cart Rule offer on Cart Page as', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>

										</td>
										<td>
											<textarea name="rtwwdpd_cart_text_show"><?php echo esc_attr(isset($rtwwdpd_gnrl_set['rtwwdpd_cart_text_show']) && $rtwwdpd_gnrl_set['rtwwdpd_cart_text_show'] != '' ? $rtwwdpd_gnrl_set['rtwwdpd_cart_text_show'] : 'Buy from [from_quant] to [to_quant] Get [discounted] Off'); ?></textarea>
											<div class="rtwwdpd_description"/>
												<i>
													<?php
													esc_html_e( 'ex. Buy from [from_quant] to [to_quant] Get [discounted] Off', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?>
												</i>
												<p><?php esc_html_e('Use ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												<b><?php echo esc_html('[from_quant], [to_quant], [discounted]') ; ?></b>
												<?php esc_html_e('as shortcode for quantity & discounted value.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</p>
											</div>
										</td>
									</tr>
								</table>
							</div>
							<div class="options_group rtwwdpd_inactive" id="rtwbogo_set_tab">
								<table class="rtwwdpd_table_edit">
									<tr>
										<td>
											<label><?php esc_html_e('Automatically add free products to cart', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>
										</td>
										<td>
											<select name="rtw_auto_add_bogo">
												<option value="rtw_yes" 
												<?php selected($rtwwdpd_gnrl_set['rtw_auto_add_bogo'] , 'rtw_yes'); ?>>
													<?php esc_html_e( 'Yes', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_no" 
												<?php selected($rtwwdpd_gnrl_set['rtw_auto_add_bogo'] , 'rtw_no'); ?>>
													<?php esc_html_e( 'No', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
											</select>
											<div class="rtwwdpd_description">
												<i>
													<?php sprintf( '<u>%s:</u>' ,
													esc_html_e( 'Automatically add free product to cart.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' )
												)	;?>
												</i>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<label><?php esc_html_e('Show offer as', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
											</label>

										</td>
										<td>
											<textarea name="rtwwdpd_bogo_text"><?php echo esc_attr(isset($rtwwdpd_gnrl_set['rtwwdpd_bogo_text']) && $rtwwdpd_gnrl_set['rtwwdpd_bogo_text'] != '' ? $rtwwdpd_gnrl_set['rtwwdpd_bogo_text'] : 'Buy [quantity1] [the-product] Get [quantity2] [free-product]'); ?></textarea>
											<div class="rtwwdpd_description">
												<i>
													<?php
													esc_html_e( 'ex. Buy [quantity1] [the-product] Get [quantity2] [free-product]', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' );?>
												</i>
												<p><?php esc_html_e('Use ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?><b><?php echo esc_html('
												[quantity1], [the-product], [quantity2], [free-product]'); ?></b>
												<?php esc_html_e('as shortcode for quantity as well as products.', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</p>
											</div>
										</td>
									</tr>
								</table>
							</div>
							<div class="options_group rtwwdpd_inactive" id="rtwmsg_set_tab">
								<caption>
									<b><?php esc_html_e('Show Message to Logged Out Users about your Offers','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></b>
								</caption>
							<table class="rtwwdpd_table_edit">
								<tbody id="rtw_set_body_tbls">
									<tr>
										<td><label><?php esc_html_e('Enable','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label></td>
										<td>
											<input <?php checked(isset($message_settings['rtwwdpd_enable_message']) ? $message_settings['rtwwdpd_enable_message'] : 0, 1 ); ?> type="checkbox" name="rtwwdpd_enable_message" value="1">
										</td>
									</tr>
									<tr>
										<td><label><?php esc_html_e('Enter Message','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label></td>
										<td>
										<?php 
										$message_content = isset( $message_settings['rtwwdpd_message_text'] ) ? $message_settings['rtwwdpd_message_text'] : 'Log In to get the best Offers';
										$rtwwdpd_setting = array(
											'wpautop' => false,
											'media_buttons' => true,
											'textarea_name' => 'rtwwdpd_message_text',
											'textarea_rows' => 7
										);

										wp_editor( stripcslashes($message_content), 'rtwwdpd_editor', $rtwwdpd_setting );
										?>
										</td>
									</tr>
									<tr>
										<td><label><?php esc_html_e('Message Position (on Shop Page)','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label></td>
										<td>
											<select name="rtwwdpd_message_position">
												<option value="0" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , '0'); ?>><?php esc_html_e('None','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												<option <?php selected( isset( $message_settings['rtwwdpd_message_position'] ) ? $message_settings['rtwwdpd_message_position'] : 0, 1 ); ?> value="1"><?php esc_html_e('Before Shop Content','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												<option <?php selected( isset( $message_settings['rtwwdpd_message_position'] ) ? $message_settings['rtwwdpd_message_position'] : 0, 2 ); ?> value="2"><?php esc_html_e('After Shop Content','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												<option <?php selected( isset( $message_settings['rtwwdpd_message_position'] ) ? $message_settings['rtwwdpd_message_position'] : 0, 3 ); ?> value="3"><?php esc_html_e('In Archive Description','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												<option <?php selected( isset( $message_settings['rtwwdpd_message_position'] ) ? $message_settings['rtwwdpd_message_position'] : 0, 4 ); ?> value="4"><?php esc_html_e('After Main Content','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label><?php esc_html_e('Message Position (on Product Page)','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label></td>
										<td>
											<select name="rtwwdpd_message_pos_propage">
												<option value="0" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , '0'); ?>><?php esc_html_e('None','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></option>
												<option value="rtw_bfore_pro" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_bfore_pro'); ?>>
													<?php esc_html_e( 'Before Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_pro" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_aftr_pro'); ?>>
													<?php esc_html_e( 'After Product', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_bfore_pro_sum" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_bfore_pro_sum'); ?>>
													<?php esc_html_e( 'Before Product Summary', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_in_pro_sum" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_in_pro_sum'); ?>>
													<?php esc_html_e( 'In Product Summary', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_pro_sum" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_aftr_pro_sum'); ?>>
													<?php esc_html_e( 'After Product Summary', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_bfre_add_cart_btn" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_bfre_add_cart_btn'); ?>>
													<?php esc_html_e( 'Before Add To Cart Button', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_add_cart_btn" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_aftr_add_cart_btn'); ?>>
													<?php esc_html_e( 'After Add To Cart Button', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_bfre_add_cart_frm" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_bfre_add_cart_frm'); ?>>
													<?php esc_html_e( 'Before Add To Cart Form', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_aftr_add_cart_frm" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_aftr_add_cart_frm'); ?>>
													<?php esc_html_e( 'After Add To Cart Form', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_pro_meta_strt" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_pro_meta_strt'); ?>>
													<?php esc_html_e( 'Product Meta Start', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
												<option value="rtw_pro_meta_end" <?php selected( isset( $message_settings['rtwwdpd_message_pos_propage'] ) ? $message_settings['rtwwdpd_message_pos_propage'] : '' , 'rtw_pro_meta_end'); ?>>
													<?php esc_html_e( 'Product Meta End', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?>
												</option>
											</select>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="options_group rtwwdpd_inactive" id="rtwtimer_set_tab">
								<caption>
									<b><?php esc_html_e('Show Time Offer Message ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></b>
								</caption>
							 <table class="rtwwdpd_table_edit">
								<tbody id="rtw_set_body_tbls">
									<tr>
										<td><label><?php esc_html_e('Enable','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label></td>
										<td>
											<input <?php checked(isset($rtwwdpd_gnrl_set ['rtwwdpd_enable_message_timer']) ? $rtwwdpd_gnrl_set ['rtwwdpd_enable_message_timer'] : 0, 1 ); ?> type="checkbox" name="rtwwdpd_enable_message_timer" value="1" id="rtwwdpd_enable_message_timer_id">
										</td>
									</tr>
									<tr>
										<td><label><?php esc_html_e(' Offer Message','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label></td>
										<td>
											<input  type="text" name="rtwwdpd_offr_msg" value="<?php echo isset($rtwwdpd_gnrl_set ['rtwwdpd_offr_msg']) ? $rtwwdpd_gnrl_set ['rtwwdpd_offr_msg'] : '' ; ?>" id="rtwwdpd_offer_msg_id">
										</td>
									</tr>
									
									<tr>
									<td><label><?php esc_html_e('Sale End Date And Time','rtwwdpd-woo-dynamic-pricing-discounts-with-ai'); ?></label></td>
									<td>
											<input type="date" name="rtwwdpd_end_sale_date" value="<?php esc_attr_e($rtwwdpd_gnrl_set ['rtwwdpd_end_sale_date'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>" id="rtwwdpd_end_date_id">
											
										</td>
										<td>
											<input type="time" id="appt" name="end_time"
											value="<?php esc_attr_e($rtwwdpd_gnrl_set ['end_time'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai') ?>"></td>
									</tr>
								
									
								</tbody>
							 </table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input class="rtw-button rtw_set_btn" type="submit" name="rtwwdpd_save_setting" value="<?php esc_attr_e( 'Save Rule', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ); ?>" />
</form>

