<?php 
/** 
 * Post Meta Boxes
 * @version 1.4
 */

class evobo_meta_boxes{
	public function __construct(){
		add_action('evotx_event_metabox_end',array($this, 'event_tickets_metabox'), 10, 5);
		add_filter('evotx_save_eventedit_page',array($this, 'event_ticket_save'), 10, 1);
		add_filter('evotx_after_saving_ticket_data',array($this, 'after_main_save'), 10, 1);

		add_filter('evost_before_tickets_meta_box', array($this, 'event_ticket_metabox_before'),10, 2);
	}

	// disable seats if booking blocks are enabled
	function event_ticket_metabox_before($boolean, $EVENT){
		
		if($EVENT->get_prop('_evobo_activate') == 'yes')	return __('Seats can not be enabled while booking blocks are enabled for this event!','evobo');
		return $boolean;
	}
	function event_tickets_metabox($eventid, $epmv, $wooproduct_id, $product_type, $EVENT){
		// only for simple, non-repeating - events
		if($product_type != 'simple'):
			?>
			<tr class='' >
				<td style='padding:5px 25px;' colspan='2'>
					<p><i><?php _e('NOTE: Event Bookings are only available for simple ticket product with no repeat instances at the moment. The event ticket basic information must be saved first before adding Bookings.', 'evobo'); ?></i></p>
				</td>
			</tr>
			<?php
			return false;
		endif;
		

		// repeating event
		if($EVENT->is_repeating_event()){
			?>
			<tr class='' >
				<td style='padding:5px 25px;' colspan='2'>
					<p><i><?php _e('NOTE: Booking blocks does not support repeating events!', 'evobo'); ?></i></p>
				</td>
			</tr>
			<?php
			return false;
		}

		if($product_type == 'simple' && !empty($wooproduct_id) && !$EVENT->check_yn('_manage_repeat_cap')):

		$__woo_currencySYM = get_woocommerce_currency_symbol();

		$BLOCKS = new EVOBO_Blocks($EVENT, $wooproduct_id);

		global $ajde;
		?>

		<tr ><td colspan='2'>
			<p class='yesno_leg_line ' >
				<?php echo eventon_html_yesnobtn(array(
					'id'=>		'_evobo_activate',
					'var'=>		evo_meta_yesno($epmv, '_evobo_activate'), 
					'afterstatement'=>'evodo_section',
					'input'=>	true,
					'label'=>	__('Enable booking blocks for this ticket','evobo'),
					'guide'=>	__('This will allow you to sell tickets by time blocks.','evobo')
				)); ?>
			</p>
		</td></tr>
		<tr class='innersection' id='evodo_section' style='display:<?php echo evo_meta_yesno($epmv,'_evobo_activate','yes','','none' );?>'>
			<td style='padding:20px 25px;' colspan='2'>

				<div id='evobo_block_selection' style='display:block'>					
				
					<p>
					<?php
						$blocks_count = $BLOCKS->get_total_block_count();
						EVO()->elements->print_trigger_element(array(
							'title'=>__('Open Booking Block Manager','evobo'). ($blocks_count>0 ? "<em style='background-color: #f56f47; padding: 1px 5px;margin-left: 10px;border-radius: 15px;font-size:12px'>{$blocks_count }</em>":'' ),
							'uid'=>'evobo_manager',
							'lb_class' =>'evobo_lightbox',
							'lb_padding' =>'evopad0',
							'lb_title'=>__('Booking Block Manager','eventon'),	
							'ajax_data'=>array(					
								'eid'=> $EVENT->ID,
								'wcid'=> $wooproduct_id,
								'action'=> 'evobo_load_editor',
							),
						), 'trig_lb');
					?></p>
					
					<div style='margin-top: 20px;'>						
						<?php 

						$V = $EVENT->get_prop('_evobo_style');

						echo EVO()->elements->process_multiple_elements(
							array(
								array(
									'row_class'=>'evobo_style',
									'styles'=>'',
									'type'=>'select_row',
									'name'=>'_evobo_style',
									'value'=> ($V? $V: 'def'),
									'label'=> __('Booking display style','evobo'),
									'options'=>array(
										'def'=> __('Calendar View', 'evobo'),
										'slot'=> __('Time Slot View', 'evobo'),
									)
								),array(
									'type'=>'yesno_btn',
									'id'=>'_evobo_hide_end',
									'name'=>'_evobo_hide_end',
									'label'=> __('Hide all booking block end time','evobo'),
									'value'=> $EVENT->get_prop('_evobo_hide_end'),
									
								),array(
									'type'=>'yesno_btn',
									'id'=>'_evobo_show_dur',
									'name'=>'_evobo_show_dur',
									'label'=> __('Show booking block duration','evobo'),
									'value'=> $EVENT->get_prop('_evobo_show_dur'),
									
								)
							)
						);

						do_action('evobo_after_event_settings',$BLOCKS);

						?>
					</div>	

				</div>
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
			$array[] = '_evobo_activate';
			return $array;
		}
		// save block capacities to sync with ticket data
		function after_main_save($event_id){
			
		}
}
new evobo_meta_boxes();