<?php 
/** 
 * Post Meta Boxes
 * @version 1.1.2
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
						'var'=>		$EVENT->get_prop('_evovo_activate'), 
						'afterstatement'=>'evovo_section',
						'input'=>	true,
						'label'=>	__('Enable ticket variations & options','evovo'),
						'guide'=>	__('Create ticket variations and options for this event.','evovo')
					)); ?>
				</p>
			</td></tr>
			<tr class='innersection' id='evovo_section' style='display:<?php echo $EVENT->check_yn('_evovo_activate') ? 'block':'none';?>'>
				<td style='padding:20px 25px;' colspan='2'>
					
					<div id='evovo_options_selection' style='display:block' >						
						<p>
						<?php
							EVO()->elements->print_trigger_element(array(
								'title'=>__('Variations & Options Settings','evovo'),
								'dom_element'=> 'span',
								'uid'=>'evovo_settings',
								'lb_class' =>'evovo_lightbox',
								'lb_title'=>__('Variations & Options Settings','evovo'),	
								'ajax_data'=>array(					
									'event_id'=> $EVENT->ID,
									'wcid'=> $wooproduct_id,
									'a'=> 'evovo_get_settings',
								),
							), 'trig_lb');
						?></p>										
					</div>
				</td>
			</tr>
			<?php

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
			return $array;
		}
		// save block capacities to sync with ticket data
		function after_main_save($event_id){
			
			if(!isset($_POST['tx_woocommerce_product_id'])) return false;

			$wcid = (int)$_POST['tx_woocommerce_product_id'];
			 
			
		}
}
new evovo_meta_boxes();