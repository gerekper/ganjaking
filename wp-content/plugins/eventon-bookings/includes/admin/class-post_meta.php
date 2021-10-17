<?php 
/** 
 * Post Meta Boxes
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
		
		if($EVENT->get_prop('_evobo_activate') == 'yes') 
			return __('Seats can not be enabled while booking blocks are enabled for this event!','evobo');
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
		endif;

		// seats addon check
		if($EVENT->check_yn('_enable_seat_chart')){
			?>
			<tr class='' >
				<td style='padding:5px 25px;' colspan='2'>
					<p><i><?php _e('NOTE: Booking blocks can not be enabled when seats are enabled!', 'evobo'); ?></i></p>
				</td>
			</tr>
			<?php
			return false;
		}

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
		?>

		<tr ><td colspan='2'>
			<p class='yesno_leg_line ' >
				<?php echo eventon_html_yesnobtn(array(
					'id'=>		'_evobo_activate',
					'var'=>		evo_meta_yesno($epmv, '_evobo_activate'), 
					'afterstatement'=>'evodo_section',
					'input'=>	true,
					'label'=>	__('Enable booking blocks for this ticket','eventon'),
					'guide'=>	__('This will allow you to sell tickets by time blocks.','eventon')
				)); ?>
			</p>
		</td></tr>
		<tr class='innersection' id='evodo_section' style='display:<?php echo evo_meta_yesno($epmv,'_evobo_activate','yes','','none' );?>'>
			<td style='padding:20px 25px;' colspan='2'>

				<div id='evobo_block_selection' style='display:block'>
					<?php /*
					<ul class="evobo_blocks_list" data-eid='<?php echo $eventid;?>' data-wcid='<?php echo $wooproduct_id;?>'>
					<?php
						echo $BLOCKS->admin_get_all_blocks_html();
					?>
					</ul>
		
					<p style='opacity:0.5;display:none'><i><?php _e('NOTE: ','eventon');?></i></p>
					*/?>
					
					<?php
						$attrs = '';
						foreach(array(
							'data-popc'=>'evobo_lightbox',
							'data-type'=>'new',
							'data-eid'=>$eventid,
							'data-wcid'=>$wooproduct_id,
							'title'=>__('Add a New Booking Block','eventon')
						) as $key=>$val){
							$attrs .= $key .'="'. $val .'" ';
						}
					?>
					<p><a class='evobo_block_item ajde_popup_trig button_evo' <?php echo $attrs;?>><?php _e('Booking Block Manager','eventon');?></a></p>
				</div>
			</td>
		</tr>
		<?php

			global $ajde;
			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evobo_lightbox', 
				'content'=>"<p class='evo_lightbox_loading'></p>",
				'title'=>__('Ticket Booking Block Manager','eventon'),
				'width'=>'900'
				)
			);

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