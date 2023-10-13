<?php
/**
 * Ticket meta boxes for event page
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/evo-tix
 * @version     2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVOTX_post_meta_boxes{
	public function __construct(){
		add_action( 'add_meta_boxes', array($this, 'evotx_meta_boxes') );
		add_action('eventon_save_meta',  array($this, 'evotx_save_ticket_info'), 10, 4);
		add_action('save_post',array($this, 'save_evotix_post'), 10, 2);
		add_action('save_post', array($this, 'evotx_new_ticket_order_save'), 20,2);
		//add_filter('evo_repeats_admin_notice', array($this, 'repeat_notice'), 10,2);

		// event repeat additions
		add_action('evo_eventedit_repeat_metabox_top', array($this, 'repeat_metabox_adds'), 10, 1);

		// event edit ajax content
		add_filter('evo_eventedit_pageload_data', array($this, 'eventedit_content'), 12,4);
		add_filter('evo_eventedit_pageload_dom_ids', array($this, 'eventedit_dom_values'), 12,4);
	}

	/** Init the meta boxes. */
		function evotx_meta_boxes(){
			global $post, $pagenow;
			add_meta_box('evotx_mb1', __('Event Tickets','evotx'), array($this,'evotx_metabox_content'),'ajde_events', 'normal', 'high');
			add_meta_box('evo_mb1',__('Event Ticket Data','evotx'), array($this,'evotx_metabox_002'),'evo-tix', 'normal', 'high');
			
			// check if the order post is a ticket order before showing meta box
			if($post->post_type=='shop_order'){
				$order_type = get_post_meta($post->ID, '_order_type', true);
				if(!empty($order_type) && $order_type=='evotix'){
					add_meta_box('evotx_mb1_shoporder','Event Tickets', array($this,'evotx_metabox_003'),'shop_order', 'side', 'default');
				}
			}

			// when adding a new ticket order from backend
			if($post->post_type=='shop_order' && $pagenow=='post-new.php'){
				add_meta_box('evotx_mb1x','Event Ticket Order Settings', array($this,'evotx_metabox_003x'),'shop_order', 'side', 'default');
			}
			add_meta_box('evotx_mb2',__('Event Ticket Confirmation','evotx'), array($this,'evoTX_notifications_box'),'evo-tix', 'side', 'default');
			do_action('evotx_add_meta_boxes');	
		}

	// Event edit ajax content
		function eventedit_dom_values( $array, $postdata, $EVENT, $id){
			$array['evotx'] = 'evotx_pageload_data';
			return $array;
		}
		function eventedit_content($array, $postdata, $EVENT, $id){

			// if id is passed and its not evotx -> skip
			if( $id && $id != 'evotx') return $array;

			global $evotx_admin; 
			
			$EVENT = new evotx_event($postdata['eid']);
			$help = new evo_helper();

			ob_start();


			if( $EVENT->is_repeating_event() && $EVENT->is_current_event()){
				echo "<p style='padding: 10px 25px;border-bottom:1px solid #e4e4e4'><i>".__('IMPORTANT: Event must have current or future event date for ticket purchasing information to display on front-end!','evotx')."</i></p>";
			}

			$woometa='';
			$woo_product_id = $EVENT->get_wcid();
			if($woo_product_id){
				$woometa =  get_post_custom($woo_product_id);
			}

			// product type
			$product_type = 'simple';
			$product_type = $evotx_admin->get_product_type($woo_product_id);
			$product_type = (!empty($product_type))? $product_type: 'simple';

			
			// stats
				$tickets_instock = EVOTX()->functions->get_tix_instock($woometa);

				$TA = new EVOTX_Attendees();
				$TH = $TA->_get_tickets_for_event($EVENT->ID, 'order_status_tally');
				
				if($TH):
					ksort($TH);
					$denominator = (int)$tickets_instock + (int)$TH['total'];
														
				?>
				<div class="evotx_ticket_data">								
					<div class="evotx_stats_bar">
						<p class='evotx_stat_subtitle' ><?php _e('Event Ticket Order Data','evotx'); echo ' - '.  (int)$TH['total'].' '. __('Tickets','evotx'); ?></p>
						<p class='stat_bar'>
						<?php
							foreach($TH as $st=>$td){
								if($st == 'total') continue;
								$status = $st;
								$W = ($td!=0)? (($td/$denominator)*100) :0;	
								?><span class="<?php echo $st;?>" style='width:<?php echo $W;?>%'></span><?php											
							}
						?>
						</p>

						<p class="evotx_stat_text">
							<?php
							foreach($TH as $st=>$td){
								if($st == 'total') continue;
								?><span class="<?php echo $st;?>" style='width:<?php echo $W;?>%'></span>
								<span><em class='<?php echo $st;?>'></em><?php echo $st;?>: <?php echo $td;?></span><?php											
							}
							?>
						</p>
					</div>
				</div>
				<?php endif; 

			// lightbox event settings
				$btn_data = array(
					'lbvals'=> array(
						'lbc'=>'config_tix_data',
						't'=>__('Configure Event Ticket Settings','eventon'),
						'ajax'=>'yes',
						'd'=> array(					
							'eid'=> $EVENT->ID,
							'action'=> 'evotx_get_event_tix_settings',
							'uid'=>'evo_get_tix_settings',
							'load_lbcontent'=>true
						)
					)
				);

				?><p class='pad20'><span class='evo_btn evolb_trigger' <?php echo $help->array_to_html_data($btn_data);?>  style='margin-right: 10px'><?php _e('Configure Ticket Settings','eventon');?></span></p>

			<?php

			do_action('evotx_admin_before_settings', $EVENT);	?>
						
			<table class='eventon_settings_table' width='100%' border='0' cellspacing='0'>				
				<?php // promote variations and options addon 

					if( $product_type != 'simple' && !function_exists('EVOVO')){
						?>
						<tr><td colspan="2">
						<p style='padding:15px 25px; margin:-5px -25px; background-color:#f9d29f; color:#474747; text-align:center; ' class="evomb_body_additional">
							<span style='text-transform:uppercase; font-size:18px; display:block; font-weight:bold'><?php 
							_e('Do you want to make ticket variations look better?','eventon');
							?></span>
							<span style='font-weight:normal'><?php echo __( sprintf('Check out our EventON Variations & Options addon and sell tickets with an ease like a boss!<br/> <a class="evo_btn button_evo" href="%s" target="_blank" style="margin-top:10px;">Check out eventON Variations & Options Addon</a>', 'http://www.myeventon.com/addons/'),'eventon');?></span>
						</p>
						</td></tr>
						<?php
					}

				?>
				<?php 
					// pluggable hook
					do_action('evotx_event_metabox_end', $EVENT->ID, $EVENT->get_data(),  $woo_product_id, $product_type, $EVENT);
				?>	
			</table>
			<?php if($woo_product_id):?>
				<p class='actions'>
					<a class='button_evo edit' href='<?php echo get_edit_post_link($woo_product_id);?>'  title='<?php _e('Further Edit ticket product from woocommerce product page','evotx');?>'> <?php _e('Further Edit','evotx');?>: <?php echo $woo_product_id;?></a> 

					<?php if(!class_exists('evovo')):?>
						<i style=''><?php _e('Learn More','evotx');?>: <a style='' href='http://www.myeventon.com/documentation/set-variable-prices-tickets/' target='_blank'><?php _e('How to add Woocommerce variable price tickets','evotx');?></a></i>
					<?php endif;?>
				</p>
					
				<p class='actions'>
					<?php 
					EVO()->elements->print_trigger_element(array(
						'title'=>__('Assign Different WC Product as Ticket Product','evotx'),
						'dom_element'=> 'span',
						'uid'=>'evotx_assign_wc',
						'lb_class' =>'evotx_manual_wc_product',
						'lb_title'=> __('Assign Different WC Product','evotx'),	
						'ajax_data'=>array(
							'a'=>'evotx_assign_wc_products',
							'eid'=>		$EVENT->ID,
							'wcid'=>	$woo_product_id
						),
					),'trig_lb');
					?>
				</p>
			<?php endif;

			$tx_content = ob_get_clean();

			$array['evotx'] = $tx_content;

			return $array;
		}

	// repeat notice on event edit post
		function repeat_notice($string, $EVENT){
			if( $EVENT->check_yn('_manage_repeat_cap') )
				$string .= "<em style='background-color: #fb3131;color: #fff;'>". __('IMPORTANT: Ticket stock for each repeating instances is enabled, changes made to repeating instances may effect the stock for each repeat instance!','evotx') . "</em>";
			return $string;
		}
		function repeat_metabox_adds($EVENT ){
			if( $EVENT->check_yn('_manage_repeat_cap')){
				echo "<p style='margin-bottom:10px;background-color: #f76a6a; color: #fff; padding: 10px; border-radius: 10px;'>". __('IMPORTANT: Ticket stock for each repeating instances is enabled, changes made to repeating instances may effect the stock for each repeat instance & already sold ticket dates!','evotx') . "</p>";
			}
		}

	// META box on WC Order post
		// adding manual order from backend
			function evotx_metabox_003x(){
				global $ajde;

				?><div id='evotx_new_order'>					
					<p class='yesno_row evo'><?php echo $ajde->wp_admin->html_yesnobtn(array(
						'id'=>'_order_type',
						'default'=>'',
						'label'=> __('Is this a ticket order ?','evotx'),
						'guide'=> __('Check this is the order contain event tickets. The order must NOT contain repeating events as repeating event tickets are not fully compatible for adding from backend, and they must be added from frontend only.','evotx'),
						'guide_position'=>'L',
						'input'=>true
					));
					?></p>
				</div>
				<?php

			}
			// save value
			// Manually adding ticket orders from backend
			function evotx_new_ticket_order_save($post_id, $post){
				if($post->post_type!='shop_order')	return;
				if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
				if (defined('DOING_AJAX') && DOING_AJAX) return;

				if(isset($_POST['_order_type']) ){
					update_post_meta($post_id,'_order_type', $_POST['_order_type']);

					if($_POST['_order_type'] == 'yes'){
						$ET = new evotx_tix();
						$ET->create_tickets_for_order($post_id);
						global $evotx;
						$evotx->functions->alt_initial_event_order($post_id);
					}
				}
			}
		// in WC order post
			function evotx_metabox_003(){
				global $post;

				$order_id = $post->ID;
				$order = new WC_Order( $order_id);
				
				$tixEmailSent = ($order->get_meta('_tixEmailSent') ==true)? true:false;
				$stock_reduced = ( $order->get_meta('evo_stock_reduced') =='yes')? true:false;

				//print_r( get_post_meta($order_id, '_tixids', true) );
				
				//do_action('evotx_beforesend_tix_email', array(), $order_id);

				?>
				<div class='evotx_wc_order_cpt'>
				<p style=''><?php echo __('Initial Ticket Email','evotx') .': <span style="background-color:#efefef; padding:1px 5px; border-radius:5px;">'. (($tixEmailSent)? __('Sent','evotx'): __('Not Sent','evotx'));?>
				</span></p>
				<p style=''><?php echo __('Ticket Stock Reduced','evotx') .': <span style="background-color:#efefef; padding:1px 5px; border-radius:5px;">'. (($stock_reduced)? __('Yes','evotx'): __('No','evotx'));?>
				</span></p>

				<?php 	
				//update_post_meta($order_id, 'evo_stock_reduced','yes');		

				if($post->post_status =='wc-completed'):?>
					<div class='evoTX_resend_conf'>			
						<div class='evoTX_rc_in'>
							<p><i><?php _e('You can re-send the Event Ticket confirmation email to customer if they have not received it. Make sure to check spam folder.','evotx');?></i></p>
							<a id='evoTX_resend_email' class='evoTX_resend_email button' data-orderid='<?php echo $post->ID;?>'><?php _e('Re-send Ticket(s) Email','evotx');?></a>

							<p style='padding-top:5px'>
								<span><?php _e('Send Ticket(s) Email to custom Email','evotx');?>
								<input style='width:100%' type='text' name='customemail' placeholder='<?php _e('Type Email Address','evotx');?>'/>
								<a id='evoTX_resend_email' class='evoTX_resend_email button customemail' style='margin-top:5px;' data-orderid='<?php echo $post->ID;?>'><?php _e('Send Ticket(s) Email','evotx');?></a>
							</p>

							<p class='message' style='display:none; text-align:center;' data-s='<?php _e('Ticket Email Re-send!','evotx');?>' data-f='<?php _e('Could not send email.','evotx');?>'></p>
						</div>
					</div>
				<?php
					else:
						echo '<p style="background-color:#FFEDD7; padding:1px 5px; border-radius:5px; text-align:center;">'.__('Ticket(s) Order is Not Completed Yet!','evotx')."</p>";
					endif;
				?>

				<?php
				// Tickets for this order
				$TA = new EVOTX_Attendees();
				$tickets = $TA->_get_tickets_for_order($order->get_id(), 'event');

				//print_r($tickets);
				if($tickets){
					
					echo "<p style='padding-top:10px; font-weight:bold;'>".__('Ticket Numbers for this Order','evotx');		        		        		
	        		echo "<p>";
	        		foreach($tickets as $e=>$dd){
	        			echo '<span style="display:block; text-transform:uppercase;font-weight:bold; font-size:12px;    background-color: #e8e8e8;color: #7d7d7d; padding: 5px 10px; margin: 0-12px;"><span style="opacity:0.5;">Event</span> '. get_the_title($e) . '</span>';
	        			foreach($dd as $tn=>$td){
	        				echo '<span style="display:block;font-size:12px;margin:0 -12px">';
	        				echo $TA->__display_one_ticket_data($tn, $td, array(
								'inlineStyles'=>false,
								'orderStatus'=>$order->get_status(),
								'linkTicketNumber'=>true,
								'showStatus'=>true,
								'showExtra'=>false,
								'guestsCheckable'=>$TA->_user_can_check(),				
							));
	        				
	        				echo "</span>";
	        			}
	        		}
	        		echo "</p>";
		        
				}
				

				?></div><?php
			}

		// in evo-tix post
			function evoTX_notifications_box(){
				global $post;

				$order_id = get_post_meta($post->ID, '_orderid', true);

				$order = new WC_Order( $order_id );	
				$order_status = $order->get_status();

				?>
				<div class='evoTX_resend_conf'>
					<div class='evoTX_rc_in'>
						<?php
						if($order_status != 'completed'):
						?>
							<p><?php _e('Ticket Order is not completed yet!','evotx');?></p>
						<?php
						else:
						?>
							<p><i><?php _e('You can re-send the Event Ticket confirmation email to customer if they have not received it. Make sure to check spam folder.','evotx');?></i></p>
							<a id='evoTX_resend_email' class='evoTX_resend_email button' data-orderid='<?php echo $order_id;?>'><?php _e('Re-send Ticket(s) Email','evotx');?></a>
							<p class='message' style='display:none; text-align:center;' data-s='<?php _e('Ticket Email Re-send!','evotx');?>' data-f='<?php _e('Could not send email.','evotx');?>'></p>
						<?php endif;?>
					</div>
				</div>
				<?php

				do_action('evotx_ticketpost_confirmation_end', $order_id, $order);
			}

	// EVO-TIX POST
		function evotx_metabox_002(){
			include_once('class-meta_boxes-evo-tix.php');
		}
		// save evo-tix post values
			function save_evotix_post($post_id, $post){			

				if($post->post_type!='evo-tix')	return;				
				if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
				if (defined('DOING_AJAX') && DOING_AJAX)	return;
				

				// verify this came from the our screen and with proper authorization,
				// because save_post can be triggered at other times
				if( isset($_POST['evo_noncename_tix']) && !wp_verify_nonce( $_POST['evo_noncename_tix'], 'evotx_edit_post' ) ) return;


				// Check permissions
				if ( !current_user_can( 'edit_post', $post_id ) )	return;	


				global $pagenow;
				$_allowed = array( 'post-new.php', 'post.php' );
				if(!in_array($pagenow, $_allowed)) return;

				foreach(array(
					'_admin_notes'
				) as $variable){
					if(!empty($_POST[$variable])){
						update_post_meta( $post_id, $variable,$_POST[$variable]);
					}elseif(empty($_POST[$variable])){
						delete_post_meta($post_id, $variable);
					}
				}


				// instance index
				if(isset($_POST['_ticket_number_instance']) ){
					update_post_meta($post_id, '_ticket_number_instance', (int)$_POST['_ticket_number_instance']);
				}




				// update ticket holder data
				if(!empty($_POST['_ticket_holder']) ){
					$EA = new EVOTX_Attendees();

					$EA->_update_ticket_holder( 
						array(
							'order_id'=>$_POST['order_id'],
							'event_id'=>$_POST['event_id'],
							'ri'=>$_POST['ri'],
							'Q'=>(int)$_POST['Q'],
							'event_instance'=>(int)$_POST['event_instance']
						),
						$_POST['_ticket_holder']
					);
				}
				
			}

	// EVENT META BOX for ajde_events CPT */	
		function evotx_metabox_content(){
			global $post, $ajde, $evotx_admin;
			$woometa='';

			$event_id = $post->ID;
			$help = new evo_helper();

			// need evo 2.6.1
			$EVENT = new evotx_event($post->ID);	
			$settings = new EVO_Settings();				
			
			$woo_product_id = $EVENT->get_wcid();

			// if woocommerce ticket has been created
			$the_product = '';
			if($woo_product_id){
				$woometa =  get_post_custom($woo_product_id);
			}

			ob_start();

			$evotx_tix = $EVENT->check_yn('evotx_tix');

			
			?>
			<div class='eventon_mb' data-eid='<?php echo $event_id;?>'>
			<div class="evotx">
				<?php 
				$settings->print_event_edit_box_yn_header(array(
					'id'=>'evotx_tix',
					'value'=> $EVENT->get_prop('evotx_tix'),
					'afterstatement' => 'evotx_details',
					'name'=> __('Activate tickets for this Event','evotx'),
					'tooltip'=> __('You can allow ticket selling via Woocommerce for this event in here.','evotx'),
					'reload_id'=> 'evotx',
					'eid'=> $post->ID
				));
				?>
				<div id='evotx_details' class='evotx_details evomb_body ' <?php echo $EVENT->check_yn('evotx_tix') ? null:'style="display:none"'; ?>>
					<?php
						$product_type = 'simple';

						// product type
						$product_type = $evotx_admin->get_product_type($woo_product_id);
						$product_type = (!empty($product_type))? $product_type: 'simple';
					?>
					
					<div class="evotx_tickets" >
						<div id='evotx_pageload_data'>
							<div class='evo_loading_bar_holder h100'>
								<div class="evo_loading_bar wid_40 hi_50"></div>
								<div class="evo_loading_bar hi_50"></div>
							</div>
						</div>

					</div>						
					<?php					
					
						// DOWNLOAD CSV link 
							$exportURL = add_query_arg(array(
							    'action' => 'the_ajax_evotx_a3',
							    'e_id' => $post->ID,
							    'pid'=> $woo_product_id
							), admin_url('admin-ajax.php'));
					?>

					<!-- Additional Lightboxes -->						
						<div class='evoTX_metabox_attendee_other'>
							<p><?php _e('Other ticket options','evotx');?></p>
							
							<p class="actions">
								<?php
								// sales insight button
									if(!empty($woometa['total_sales']) && $woometa['total_sales']>0):
										EVO()->elements->print_trigger_element(array(
											'id'=>'evotx_visual',
											'title'=>__('Sales Insight','evotx'),
											'dom_element'=> 'span',
											'uid'=>'evotx_salesinsight',
											'lb_class' =>'config_evotx_salesinsight',
											'lb_title'=>__('Extended insight on ticket sales','evotx'),	
											'ajax_data'=>array(					
												'event_id'=> $event_id,
												'action'=>'evotx_sales_insight',
											),
										), 'trig_lb');
									endif;
								
								// view attendees
									EVO()->elements->print_trigger_element(array(
										'title'=> "<i class='fa fa-users evomarr10'></i> " .__('View Attendees','evotx'),
										'dom_element'=> 'span',
										'uid'=>'evotx_view_attendees',
										'lb_class' =>'config_evotx_viewattend',
										'lb_title'=>__('View Attendees','evotx'),	
										'lb_padding'=> 'evopad0',	
										'ajax_data'=>array(					
											'eid'=> $event_id,
											'action'=> 'the_ajax_evotx_a1',
										),
									), 'trig_lb');

								// emailing
									EVO()->elements->print_trigger_element(array(
										'title'=> "<i class='fa fa-envelope evomarr10'></i> " .__('Emailing','evotx'),
										'dom_element'=> 'span',
										'uid'=>'evotx_emailing',
										'lb_class' =>'evotx_emailing',
										'lb_padding' =>'evopad50',
										'lb_title'=>__('Email Attendees','evotx'),		
										'ajax_data'=>array(					
											'e_id'=> $event_id,
											'action'=> 'evotx_emailing_form',
										),
									), 'trig_lb');
								?>
								
								<a class='button_evo download' href="<?php echo $exportURL;?>"><?php _e('Download (CSV)','evotx');?></a>


								<a href='<?php echo get_admin_url('','/admin.php?page=eventon&tab=evcal_5');?>'class='button_evo troubleshoot ajde_popup_trig' title='<?php _e('Troubleshoot Tickets Addon','evotx');?>'><?php _e('Troubleshoot','evotx');?></a> 
							</p>

						</div>
						
				</div>			
			</div>
			</div>

			<?php
			echo ob_get_clean();
		}

	// save new ticket and create matching WC product
		function evotx_save_ticket_info($fields_ar, $post_id, $EVENT, $post_data){			

			foreach(apply_filters('evotx_save_eventedit_page', array(
				'evotx_tix'
			)) as $variable){
				if(!empty($post_data[$variable])){
					update_post_meta( $post_id, $variable,$post_data[$variable]);
				}elseif(empty($post_data[$variable])){
					delete_post_meta($post_id, $variable);
				}
			}

			// after saving event tickets data
			do_action('evotx_after_saving_ticket_data', $post_id);			
		}

}
new EVOTX_post_meta_boxes();