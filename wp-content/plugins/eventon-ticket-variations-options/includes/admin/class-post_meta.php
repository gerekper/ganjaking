<?php 
/** 
 * Post Meta Boxes
 */

class evovo_meta_boxes{
	public function __construct(){
		add_action('evotx_event_metabox_end',array($this, 'event_tickets_metabox'), 10, 5);
		add_filter('evotx_save_eventedit_page',array($this, 'event_ticket_save'), 10, 1);
		add_filter('evotx_after_saving_ticket_data',array($this, 'after_main_save'), 10, 1);
	}

	function event_tickets_metabox($eventid, $epmv, $wooproduct_id, $product_type, $EVENT){
		
		$show_metabox_content = true;

		// not for simple wc products
		if( $product_type != 'simple') $show_metabox_content = false;

		// check if wc product exists for this event
		if( empty($wooproduct_id)) $show_metabox_content = __('Woocommerce product not associated with this event!','evovo');

		// disable for repeating events
		if( $EVENT->is_repeating_event() ) $show_metabox_content = __('Variation options are not available for repeating events!','evovo');

		$show_metabox_content = apply_filters('evovo_before_tickets_meta_box', $show_metabox_content, $EVENT);

		
		// if good to show the metabox content
		if(  $show_metabox_content === true ):
			$__woo_currencySYM = get_woocommerce_currency_symbol();

			$fnc = new evovo_fnc();

			?>
			<tr ><td colspan='2'>
				<p class='yesno_leg_line ' >
					<?php echo eventon_html_yesnobtn(array(
						'id'=>		'_evovo_activate',
						'var'=>		evo_meta_yesno($epmv, '_evovo_activate'), 
						'afterstatement'=>'evovo_section',
						'input'=>	true,
						'label'=>	__('Enable ticket variations & options','evovo'),
						'guide'=>	__('Create ticket variations and options for this event.','evovo')
					)); ?>
				</p>
			</td></tr>
			<tr class='innersection' id='evovo_section' style='display:<?php echo evo_meta_yesno($epmv,'_evovo_activate','yes','','none' );?>'>
				<td style='padding:20px 25px;' colspan='2'>
					
					<div id='evovo_options_selection' style='display:block'>
						<div class='evovo_vos_container_event'>
							<?php
							$VO = new EVOVO_Var_opts($EVENT, $wooproduct_id);
							echo $VO->get_all_vos_html('', 'event');
							?>		
						</div>
						
						<p class='yesno_leg_line ' >
							<?php echo eventon_html_yesnobtn(array(
								'id'=>		'_evovo_po_sep_sold',
								'var'=>		evo_meta_yesno($epmv, '_evovo_po_sep_sold'), 
								'input'=>	true,
								'label'=>	__('Sell Price Options as Separate Tickets','evovo'),
								'guide'=>	__('This will enable you to sell price options as separate tickets instead of a single ticket. Variations will be disabled when this is active.','evovo')
							)); ?>
						</p>
						<p class='yesno_leg_line ' >
							<?php echo eventon_html_yesnobtn(array(
								'id'=>		'_evovo_v_hide_sold',
								'var'=>		evo_meta_yesno($epmv, '_evovo_v_hide_sold'), 
								'input'=>	true,
								'label'=>	__('Hide variations that are out of stock','evovo'),
								'guide'=>	__('This option will not show variations that are sold out, when page first loads.','evovo')
							)); ?>
						</p>

						<p style='opacity:0.5;display:none'><i><?php _e('NOTE: ','evovo');?></i></p>				
						<p style='margin-top:20px'><?php	echo $VO->get_vos_action_btn_html($eventid,'event'); ?></p>					
					</div>
				</td>
			</tr>
			<?php

			global $ajde;
			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evovo_lightbox', 
				'content'=>"<p class='evo_lightbox_loading'></p>",
				'title'=>__('Ticket Options','evovo'),
				'width'=>'500',
				'outside_click'=>false
				)
			);

		else:
			?>
			<tr class='' id='evovo_section' >
				<td style='padding:5px 25px;' colspan='2'>
					<p><i><?php echo  $show_metabox_content != false? $show_metabox_content: __('NOTE: Ticket variations and options are not available based on other activated ticket options!', 'evovo'); ?></i></p>
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
			$array[] = '_evovo_activate';
			$array[] = '_evovo_po_sep_sold';
			$array[] = '_evovo_v_hide_sold';
			return $array;
		}
		// save block capacities to sync with ticket data
		function after_main_save($event_id){
			
			if(!isset($_POST['tx_woocommerce_product_id'])) return false;

			$wcid = (int)$_POST['tx_woocommerce_product_id'];
			 
			
		}
}
new evovo_meta_boxes();