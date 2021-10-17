<?php
/** 
 * Event Seats Post Meta
 * @version 0.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evost_meta_boxes{
	public function __construct(){
		add_action('evotx_event_metabox_end',array($this, 'event_tickets_metabox'), 10, 5);
		add_filter('evotx_save_eventedit_page',array($this, 'event_ticket_save'), 10, 1);

	}

	// Event Tickets
		function event_tickets_metabox($eventid, $epmv, $wooproduct_id, $product_type, $EVENT){
			global $eventon, $evost, $ajde;

			$show_content = apply_filters('evost_before_tickets_meta_box', true, $EVENT);

			//echo $product_type.'yyy';
			// check if simple product with no repeat events data				
			if(!empty($wooproduct_id) && $product_type =='simple' && (empty($epmv['evcal_repeat']) || (!empty($epmv['evcal_repeat']) && $epmv['evcal_repeat'][0] =='no') ) 	&& $show_content === true		
			):

			// Enable setting seat chart for event tickets
				$_enable_seat_chart = evo_meta_yesno($epmv,'_enable_seat_chart','yes','yes','no' );

			// get lightbox content
				$this->lightbox_content($eventid, $epmv, $wooproduct_id, $product_type);

				$SYM = get_woocommerce_currency_symbol();

			?>
			<!-- SEATING SECTION -->
				<tr ><td colspan='2'>
					<p class='yesno_leg_line ' >
						<?php echo eventon_html_yesnobtn(array(
							'id'=>'evotx_seating',
							'var'=>$_enable_seat_chart, 
							'attr'=>array('afterstatement'=>'evotx_seat_chart')
						)); ?>
						<input type='hidden' name='_enable_seat_chart' value="<?php echo $_enable_seat_chart;?>"/>
						<label for='_enable_seat_chart'><?php _e('Enable seat charts for this event','evost'); echo $eventon->throw_guide( __('This will allow you to create seat charts with custom prices per seat to allow customers to buy seat of their choice.','evost'),'',false)?></label>
					</p>
				</td></tr>
				<tr class='innersection' id='evotx_seat_chart' style='display:<?php echo evo_meta_yesno($epmv,'_enable_seat_chart','yes','','none' );?>'>

					<td style='padding:5px 25px;' colspan='2'>
						<div style='padding:5px 0 9px'>
							<p><?php _e('Set up seat chart with sections', 'evost'); ?></p>
							<a class='button_evo ajde_popup_trig evost_open_seat_map_editor' data-eid='<?php echo $eventid;?>' data-product_id='<?php echo $wooproduct_id;?>' data-popc='evost_lightbox'><?php _e('Open Seat Map Editor');?></a>

							<p class='yesno_leg_line ' style='margin-top:10px;'>
								<?php 

								$_allow_direct_add = $EVENT->check_yn('_allow_direct_add');

								echo $ajde->wp_admin->html_yesnobtn(array(
									'id'=>'_allow_direct_add',
									'var'=> ($_allow_direct_add ? 'yes':'no'), 
									'input'=>true,
									'label'=> __('Enable one-click adding seats direct to cart','evost'),
									'guide'=> __('Add seats to cart with one-click, this is only available for regular seats and NOT for unassigned seating areas.','evost')
								)); ?>
							</p>
						</div>
					</td>
				</tr>
			<?php

			else:
				?>
				<tr class='' id='evotx_seat_chart' >
					<td style='padding:5px 25px;' colspan='2'>
						<p><i><?php echo  (!is_bool($show_content) ) ? $show_content: __('NOTE: Seat Charts are only available for simple ticket product with no repeat instances at the moment. The event ticket basic information must be saved first before adding seat charts.', 'evost'); ?></i></p>
					</td>

				</tr>
				<?php
			endif;
		}

	// lightbox content for seat map editor
		function lightbox_content($eventid, $epmv, $wooproduct_id, $product_type){
			
			$SEATSETTINGS = !empty($epmv['_evost_settings'])? unserialize($epmv['_evost_settings'][0]): false;
			$IMG_ID = !empty($SEATSETTINGS['seat_bg_img_id'])? $SEATSETTINGS['seat_bg_img_id']: false;


			global $ajde;

			$CLASSNAMES = array();
			foreach(array('map_area', 'seat_size') as $item){
				if(!empty($SEATSETTINGS[$item])){
					$CLASSNAMES[] = $item . $SEATSETTINGS[$item];
				}
			}

			$CLASSNM = ( sizeof($CLASSNAMES)>0? implode(' ', $CLASSNAMES) : '');

			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evost_lightbox evost_seating_map '.$CLASSNM, 
				'content'=>	"<p class='evo_lightbox_loading'></p>", 
				'title'=>__('Seat Map Editor for Event','evost'), 
				'max_height'=>500,
				'outside_click'=>false
			));

			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evost_lightbox_secondary ', 
				'content'=> "<p class='evo_lightbox_loading'></p>", 
				'title'=>__('Seat Map Settings','evost'), 
				'max_height'=>400,
				'outside_click'=>false
			));
		}

	// save fields
		function event_ticket_save($array){
			$array[] = '_enable_seat_chart';
			$array[] = '_allow_direct_add';
			return $array;
		}
}
new evost_meta_boxes();	
	