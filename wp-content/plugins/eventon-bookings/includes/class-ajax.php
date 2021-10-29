<?php
/**
 * AJAX
 */
class evobo_ajax{
	public function __construct(){
		$ajax_events = array(
			'evobo_get_prices'=>'get_prices',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	// get pricing HTML
	function get_prices(){
		$fnc = new evobo_fnc();
		$args = $_POST;

		$block_index = $args['index'];

		$event_id = $args['dataset']['event_id'];
		$wcid = $args['dataset']['wcid'];
		$lang = $args['dataset']['l'];
		
		// setting language as global
			EVO()->lang = $lang;
			//EVO()->evo_generator->shortcode_args['lang'] = $lang;


		$BLOCKS = new EVOBO_Blocks($event_id, $wcid);
		$EVOTX = new evotx_event($event_id);
		$BLOCKS->set_block_data($block_index);

		$has_stock = $BLOCKS->has_stock();

		$Helper = new evotx_helper();
		$EVO_Help = new evo_helper();


		ob_start();
	

		if($has_stock){		

			// check if block is in cart already
				$blocks_in_cart = $BLOCKS->get_blocks_in_cart();				

				if( $blocks_in_cart >= $has_stock){
					echo json_encode(array(
						'content'=> evo_lang('Can not add more! You have already added all the available spaces to your cart!'),			
						'status'=>'good'
					)); exit;
				}

			?>

			<div class="evobo_selction_stage_time_qty evotx_hidable_box evotx_hidable_section">	
				<p class='evobo_selected_slot evotx_ticket_other_data_line'>
					<span class="label"><?php evo_lang_e('Your selected time');?></span>	
					<span class="value"><?php 
						echo date($BLOCKS->date_format.' '.$BLOCKS->time_format, $BLOCKS->get_item_prop('start'));
						echo $BLOCKS->event->check_yn('_evobo_hide_end' )? '':
							' - '.date($BLOCKS->date_format.' '.$BLOCKS->time_format, $BLOCKS->get_item_prop('end'));
					?></span>
				</p>

				<?php 
				// show duration of the slot
				if($BLOCKS->event->check_yn('_evobo_show_dur')):
					$duration = $BLOCKS->get_block_duration();
				?>
					<p class='evobo_selected_slot evotx_ticket_other_data_line'>
						<span class="label"><?php evo_lang_e('Duration');?></span>	
						<span class="value"><?php echo $duration;?></span>
					</p>

				<?php endif;?>
				<?php 

				//pluggability
					$plug = apply_filters('evobo_block_preview' ,true, $BLOCKS);
					if( !is_bool($plug) ) echo $plug;
				

				// base price 
					$_price = $BLOCKS->get_item_price();
					$base_price = apply_filters('evobo_base_price',  $_price, $BLOCKS);

				// capacity
					$_cap = $BLOCKS->get_item_prop('capacity') - $blocks_in_cart;
					$capacity = apply_filters('evobo_base_capacity', $_cap, $BLOCKS );

				echo "<div class='evotx_add_to_cart_bottom ". (!$capacity? 'outofstock':'') ."'>";
					$Helper->base_price_html( $base_price );
					$Helper->ticket_qty_html( $capacity );

					$Helper->total_price_html( $base_price ,'evobo_total_price' );	
					$Helper->add_to_cart_btn_html( 'evotx_addtocart');

				// show remaining
					if($EVOTX->is_show_remaining_stock()) $Helper->remaining_stock_html($has_stock);

					$evotx_data = array();
					$evotx_data['event_data']['booking_index'] = $block_index;
					$evotx_data['event_data']['eid'] = $event_id;
					$evotx_data['event_data']['wcid'] = $wcid;
					$evotx_data['event_data']['l'] = $lang;
					$evotx_data['msg_interaction']['hide_after'] = 'false';
					
					$Helper->print_add_to_cart_data($evotx_data);

				echo "</div>";

				?>				
			</div>
			<?php

			// show footer based on capacity
			if(!$capacity){
				$Helper->__get_addtocart_msg_footer('bad','Out of stock!');
			}else{
				$Helper->__get_addtocart_msg_footer();
			}
		}else{
			$Helper->__get_addtocart_msg_footer('bad', evo_lang('Out of stock') );
			echo "no stock";
		}

		echo json_encode(array(
			'content'=> ob_get_clean(),			
			'status'=>'good'
		)); exit;
	}
}
new evobo_ajax();