<?php
/**
 * Post meta boxes
 * @version 
 */
class evodp_meta_boxes{
	public function __construct(){
		add_action('evotx_event_metabox_end',array($this, 'event_tickets_metabox'), 10, 5);
		add_filter('evotx_save_eventedit_page',array($this, 'event_ticket_save'), 10, 1);
	}

	function event_tickets_metabox($eventid, $epmv, $wooproduct_id, $product_type, $EVENT){
		global $eventon, $evodp;

		$event_edit_allow_dynamic_pricing = apply_filters('evodp_event_edit_enable_dp',true, $EVENT);

		// only for simple tickets types
		
		if($product_type == 'simple' && !empty($wooproduct_id) && (empty($epmv['evcal_repeat']) || (!empty($epmv['evcal_repeat']) && $epmv['evcal_repeat'][0] =='no') ) && $event_edit_allow_dynamic_pricing ):
		

		$__woo_currencySYM = get_woocommerce_currency_symbol();
		$_evodp_member_pricing = evo_meta_yesno($epmv,'_evodp_member_pricing','yes','yes','no' );
		$_evodp_time_pricing = evo_meta_yesno($epmv,'_evodp_time_pricing','yes','yes','no' );
		
		$fnc = new evodp_fnc();
		
		?>
		<tr ><td colspan='2'>
			<p class='yesno_leg_line ' >
				<?php echo eventon_html_yesnobtn(array(
					'id'=>		'_evodp_activate',
					'var'=>		evo_meta_yesno($epmv, '_evodp_activate'), 
					'afterstatement'=>'evodp_pricing',
					'input'=>	true,
					'label'=>	__('Enable dynamic ticket pricing options for this event','eventon'),
					'guide'=>	__('This will allow you to set dynamic ticket pricing options.','eventon')
				)); ?>
			</p>
		</td></tr>
		<tr class='innersection' id='evodp_pricing' style='display:<?php echo evo_meta_yesno($epmv,'_evodp_activate','yes','','none' );?>'>
			<td style='padding:20px 25px;' colspan='2'>
				
				<!-- regular price -->
				<p class='yesno_leg_line ' >
					<?php echo eventon_html_yesnobtn(array(
						'id'=>			'_evodp_show_regularp',
						'var'=>			evo_meta_yesno($epmv,'_evodp_show_regularp','yes','yes','no' ), 
						'input'=>		true,
						'label'=>		__('Show strikedthrough regular price as well on EventCard','eventon'),
						'guide'=>		__('This will show regular ticket price strikedthrough next to the discounted dynamic price you will set below.','eventon'),
					)); ?>
				</p>

				<!-- Member pricing -->
				<p class='yesno_leg_line ' >
					<?php echo eventon_html_yesnobtn(array(
						'id'=>			'_evodp_member_pricing',
						'var'=>			$_evodp_member_pricing, 
						'afterstatement'=>'evodp_member_pricing',
						'input'=>		true,
						'label'=>		__('Activate Separate Logged-in Member Pricing','eventon'),
						'guide'=>		__('This will allow you to set separate price for members of your website that have logged into your site.','eventon'),
					)); ?>
				</p>
	
				<div id='evodp_member_pricing' class="evodp_member_pricing" style='display:<?php echo evo_meta_yesno($epmv,'_evodp_member_pricing','yes','','none' );?>'>
					<p><label ><?php printf( __('Default Ticket Price Only for Members (%s)','eventon'), $__woo_currencySYM);?></label><br/>
						<input name='_evodp_member_def_price' style='width:100%; margin-top:5px;'type="text" value='<?php echo evo_meta($epmv,'_evodp_member_def_price');?>'>
					</p>
					<?php /*<p><label ><?php _e('Text to show (below price) when memeber price is active (Leave blank to show nothing)','eventon');?></label><br/>
						<input name='_evodp_member_msg' style='width:100%; margin-top:5px;'type="text" value='<?php echo evo_meta($epmv,'_evodp_member_msg');?>'>
					</p>*/?>
				</div>
				
				<!-- time based pricing blocks -->
				<p class='yesno_leg_line ' >
					<?php echo eventon_html_yesnobtn(array(
						'id'=>		'_evodp_time_pricing',
						'var'=>		$_evodp_time_pricing, 
						'afterstatement'=>'evodp_time_pricing_section',
						'input'=>	 true,
						'label'=>	 __('Activate Time Based Ticket Pricing Blocks','eventon'),
						'guide'=>	__('This will allow you to set dynamic ticket pricing options.','eventon')
					)); ?>
				</p>
				
				<div id='evodp_time_pricing_section' style='display:<?php echo evo_meta_yesno($epmv,'_evodp_time_pricing','yes','','none' );?>'>
					<p><label ><?php _e('Text to show when time based price is active (Leave blank to show nothing)','eventon');?></label><br/>
						<input name='_evodp_tbp_msg' style='width:100%; margin-top:5px;'type="text" value='<?php echo evo_meta($epmv,'_evodp_tbp_msg');?>'>
					</p>
					<ul class="evodp_dpblocks evodp_blocks_tbp evodp_blocks_list">
					<?php
						$_wp_date_format = get_option('date_format');

						if(!empty($epmv['_evodp_prices'])){
							$tb_prices = unserialize($epmv['_evodp_prices'][0]);

							if(sizeof($tb_prices)>0){						

								foreach($tb_prices as $index=>$data){
									$data['date_format'] = $_wp_date_format;
									$data['time_format'] = $this->get_time_format();
									$data['block_key'] = '_evodp_prices';
									$data['block'] = 'tbp';
									$data['eid'] = $eventid;
									echo $fnc->get_time_based_block_html($data, $index);
								}
							}
						}else{
							echo "<p class='none' style='padding:8px;'>".__('You do not have any pricing blocks yet!','eventon')."</p>";
						}
					?>
					</ul>
		
					<p style='opacity:0.5'><i><?php _e('NOTE: You can use this to create earlybird pricing and price increases as you get closer to event.','eventon');?></i></p>
					
					<?php
						$attrs = '';
						foreach(array(
							'data-popc'=>'evodp_lightbox',
							'data-type'=>'new',
							'data-block'=>'tbp',
							'data-eid'=>$eventid,
							'title'=>__('Add a New Alternate Price Block','eventon')
						) as $key=>$val){
							$attrs .= $key .'="'. $val .'" ';
						}
					?>
					<p><a class='evodp_block_item ajde_popup_trig button_evo' <?php echo $attrs;?>><?php _e('Add New Pricing Block','eventon');?></a></p>
				</div>

				<!-- unavailable blocks -->
				<?php	$_evodp_unavailables = evo_meta_yesno($epmv,'_evodp_unavailables','yes','yes','no' );	?>
				<p class='yesno_leg_line ' >
					<?php echo eventon_html_yesnobtn(array(
						'id'=>'_evodp_unavailables',
						'var'=>$_evodp_unavailables, 
						'attr'=>array('afterstatement'=>'evodp_una_section')
					)); ?>
					<input type='hidden' name='_evodp_unavailables' value="<?php echo $_evodp_unavailables;?>"/>
					<label for='_evodp_unavailables'><?php _e('Activate Tickets Unavailable for Sale Time Blocks'); echo $eventon->throw_guide('This will allow you to set dynamic ticket pricing options.','',false)?></label>
				</p>
				

				<div id='evodp_una_section' style='display:<?php echo evo_meta_yesno($epmv,'_evodp_unavailables','yes','','none' );?>'>
					<ul class="evodp_blocks_una evodp_blocks_list">
					<?php
						$_wp_date_format = get_option('date_format');

						if(!empty($epmv['_evodp_una'])){
							$tb_prices = unserialize($epmv['_evodp_una'][0]);

							if(sizeof($tb_prices)>0){							

								foreach($tb_prices as $index=>$data){
									$data['date_format'] = $_wp_date_format;
									$data['time_format'] = $this->get_time_format();
									$data['block_key'] = '_evodp_una';
									$data['block'] = 'una';
									$data['eid'] = $eventid;
									echo $fnc->get_time_based_block_html($data, $index);
								}
							}
						}else{
							echo "<p class='none' style='padding:8px;'>".__('You do not have any unavailable time blocks yet!','eventon')."</p>";
						}
					?>
					</ul>
					
					<?php
						$attrs = '';
						foreach(array(
							'data-popc'=>'evodp_lightbox',
							'data-type'=>'new',
							'data-block'=>'una',
							'data-eid'=>$eventid,
							'title'=>__('Add New Unavailable Time Block','eventon')
						) as $key=>$val){
							$attrs .= $key .'="'. $val .'" ';
						}
					?>
					<p><a class='evodp_block_item ajde_popup_trig button_evo' <?php echo $attrs;?>><?php _e('Add New Unavailable Time Block','eventon');?></a></p>
				</div>
				

				<?php
				global $ajde;
				echo $ajde->wp_admin->lightbox_content(array(
					'class'=>'evodp_lightbox', 
					'content'=>"<p class='evo_lightbox_loading'></p>",
					'title'=>__('Time/Price Blocks','eventon'),
					'width'=>'500'
					)
				);
				?>
			</td>
		</tr>
		<?php

		else:
			?>
			<tr class='' id='evodp_tr' >
				<td style='padding:5px 25px;' colspan='2'>
					<?php if(!$event_edit_allow_dynamic_pricing):?>
						<p><i><?php _e('NOTE: Dynamic Pricing is not available for current event ticket configurations.', 'eventon'); ?></i></p>
					<?php else:?>	
						<p><i><?php _e('NOTE: Dynamic Pricing is only available for simple ticket product with no repeat instances at the moment. The event ticket basic information must be saved first before configuring dynamic prices.', 'eventon'); ?></i></p>
					<?php endif;?>
				</td>

			</tr>
			<?php
		endif;
	}

	// get time format
		function get_time_format(){
			$wp_time_format = get_option('time_format');
			return (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)? 'H:i':'h:i:A';
		}

	// save fields
		function event_ticket_save($array){
			$array[] = '_evodp_activate';
			$array[] = '_evodp_member_pricing';
			$array[] = '_evodp_time_pricing';
			$array[] = '_evodp_member_def_price';
			$array[] = '_evodp_unavailables';
			$array[] = '_evodp_prices';
			$array[] = '_evodp_una';
			$array[] = '_evodp_show_regularp';
			$array[] = '_evodp_tbp_msg';
			return $array;
		}
}
new evodp_meta_boxes();