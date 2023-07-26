<?php
/**
 * eventon tickets front end class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-tickets/Classes
 * @version     2.2
 */

class evotx_front{
	public $opt1, $opt2, $eotx;
	
	function __construct(){
		global $evotx;
		// event top inclusion
		//add_filter('evo_eventtop_adds', array($this, 'eventtop_adds'), 10, 1);
		//add_filter('eventon_eventtop_evotx', array($this, 'eventtop_content'), 10, 2);
		
		add_action('evo_addon_styles', array($this, 'styles'),10 );

		$this->opt1 = get_option('evcal_options_evcal_1');
		$this->opt2 = EVOTX()->opt2;
		$this->eotx = EVOTX()->evotx_opt;
		
		// event card inclusion
		add_filter('eventon_eventCard_evotx', array($this, 'frontend_box'), 10, 3);
		add_filter('eventon_eventcard_array', array($this, 'eventcard_array'), 10, 4);
		add_filter('evo_eventcard_adds', array($this, 'eventcard_adds'), 10, 1);

			// event top above title
				add_filter('eventon_eventtop_abovetitle', array($this,'eventtop_above_title'),10, 3);
		
		// scripts and styles 
			add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	
			add_action( 'eventon_enqueue_styles', array( $this, 'load_styles' ), 10 );
		
		// front-end template redirect
		add_action('template_redirect', array($this, 'template_redirect'), 10, 1);

		// shortcode
		add_shortcode('evotx_btn', array($this,'ticket_button'));
		add_shortcode('evotx_attendees', array($this,'attendees_list_anywhere'));
		add_filter('evo_frontend_lightbox', array($this, 'ligthbox'),10,1);

		//echo get_post_meta(3075, '_stock_status',true);

	}


	// template redirect
		function template_redirect(){
			if( !evo_settings_check_yn($this->eotx,'evotx_wc_prod_redirect')) return false;

			if(is_product()) {
				$event_id = get_post_meta(get_queried_object_id(), '_eventid',true);
				if($event_id) {
					$event_url = get_permalink($event_id);
					if($event_url !== false) {
						wp_redirect($event_url, 301);
						exit();
					}
				}
			}
		}

	// Event TOP inclusion		
		public function eventtop_content($object, $helpers){
			$output = '';
			$emeta = get_post_custom($object->vals['eventid']);


			// if tickets and enabled for the event
			if( !empty($emeta['evotx_tix']) && $emeta['evotx_tix'][0]=='yes'
				&& $object->vals['fields_'] && in_array('organizer',$object->vals['fields'])
			){

				global $product;
				$woo_product_id = $emeta['tx_woocommerce_product_id'][0];
				$product = wc_get_product($woo_product_id);

				if(!$product->is_type( 'simple' ) ) return $output;
						
				$output .= "<span class='evotx_add_to_cart' data-product_id='{$woo_product_id}' data-event_id='{$object->vals['eventid']}' data-ri='{$object->vals['ri']}'><em>Add to cart</em></span>";
			}	

			return $output;
		}
		// event card inclusion functions		
			function eventtop_adds($array){
				$array[] = 'evotx';
				return $array;
			}

		// above title - sold out tag
			function eventtop_above_title($var, $object, $EVENT){
				$epmv = $object->evvals;

				// event have tickets enabled
				if($EVENT->check_yn('evotx_tix') && $EVENT->get_prop('tx_woocommerce_product_id')){		

					if( $EVENT->get_event_status() == 'postponed')	return $var;

					// dismiss if set in ticket settings not to show sold out tag on eventtop
					$hide_soldout_tag = (evo_settings_check_yn($this->eotx, 'evotx_eventop_soldout_hide'));

					$wcid = $EVENT->get_prop('tx_woocommerce_product_id');
					$woometa = get_post_custom($wcid);

					$haveTix = EVOTX()->functions->event_has_tickets($epmv, $woometa, $object->ri);

					$isEventEnded = $EVENT->is_past_event('end');
					
					if(!$haveTix && !$isEventEnded){
						if($hide_soldout_tag) return $var;
						return $var."<span class='evo_soldout'>".$this->langX('Sold Out!', 'evoTX_012')."</span>";
					}

					// check with settings if event over to be hidden
					$hide_eventover_tag = (evo_settings_check_yn($this->eotx, 'evotx_eventop_eventover_hide'));

					$isEventStarted = $EVENT->is_past_event('start');
					

					if( $hide_eventover_tag ) return $var;

					if($isEventStarted && !$isEventEnded)
						return $var."<span class='eventover'>". evo_lang('Event has started')."</span>";
					if($isEventEnded) 
						return $var."<span class='eventover'>".$this->langX('Event Over', 'evoTX_012b')."</span>";	
				}
				return $var;
			}

	// STYLES / SCRIPTS
		function styles(){
			ob_start();
			include_once(EVOTX()->plugin_path.'/assets/tx_styles.css');
			echo ob_get_clean();
		}
		public function register_styles_scripts(){	
			
			// load style file to page if concatenation is not enabled
			if( !EVO()->cal->check_yn('evcal_concat_styles','evcal_1')){
				wp_register_style( 'evo_TX_styles',EVOTX()->assets_path.'tx_styles.css', array(), EVOTX()->version);
			}
			
			wp_register_script('tx_wc_variable', EVOTX()->assets_path.'tx_wc_variable.js', array('jquery'), EVOTX()->version, true);
			wp_register_script('tx_wc_tickets', EVOTX()->assets_path.'tx_script.js', array('jquery'), EVOTX()->version, true);

			// localize script data
			$script_data = array_merge(array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' )
				), $this->get_script_data());
			
			wp_localize_script( 
				'tx_wc_tickets', 
				'evotx_object',$script_data	
			);
			

		}

		public function load_styles(){	
			wp_enqueue_style( 'evo_TX_styles');			
			wp_enqueue_script('tx_wc_variable');
			wp_enqueue_script('tx_wc_tickets');	
		}
		
		
		/**
		 * Return data for script handles
		 */
		function get_script_data(){
			
			$ticket_redirect = EVO()->cal->get_prop('evotx_wc_addcart_redirect','evcal_tx');
			$wc_redirect_cart = get_option( 'woocommerce_cart_redirect_after_add' );
			if( empty($ticket_redirect) && $wc_redirect_cart == 'yes') 
				$ticket_redirect = 'cart';

			return apply_filters('evotx_js_script_data', array(
				'cart_url'=> wc_get_cart_url(), 
				'checkout_url'=> wc_get_checkout_url(), 
				//'cart_url'=>get_permalink( wc_get_page_id( 'cart' ) ),
				'redirect_to_cart'=> $ticket_redirect,
				'text'=> array(
					'002' => eventon_get_custom_language(
						'', 'evoTX_inq_06','Required Fields are Missing, Please Try Again!'),
					'003' => evo_lang('Invalid verification code'),
				)
			));
		}

	// attendees list anywhere
		function attendees_list_anywhere($atts){
			extract(shortcode_atts(array(
		     'id' => false,
		     'ri' => '0',
		     'event_details'=>'no',
		    ), $atts));

		    $EVENT = new evotx_event($id, '', $ri);

		    $EA = new EVOTX_Attendees();

		    $json = $EA->get_tickets_for_event( $id );
		    //print_r($json);

		    $content = '';

		    // event details
		    if( $event_details == 'yes'){
		    	$LOC = $EVENT->get_location_name();

		    	$event_color = $EVENT->get_hex();
		    	$light = '';
		    	if( $event_color && eventon_is_hex_dark( $event_color ) ){
		    		$light = 'light';
		    	}

		    	$content .= "<div class='evotx_ed {$light}' style='background-color:#". $event_color ."'>";
		    	$content .= "<h3><a href='". $EVENT->get_permalink() ."'>". $EVENT->get_title() . "</a></h3>";
		    	$content .= "<p>". evo_lang('Event Attendees List') . "</p>";
		    	$content .= "<p><i class='fa fa-clock marr10'></i>". $EVENT->get_readable_formatted_date(  $EVENT->get_start_time() ) . 
		    		( $LOC ? "<i class='fa fa-location-pin marl10 marr10'></i>". $LOC."</i>":'') ."</p>";
		    	
		    	$content .= "</div>";
		    }

		    $content .= "<div class='evotx_ea'>";

		    // attendee list
		    if(!count($json)>0){
		    	$content .= "<p class=''>". evo_lang('This event has no attendees at the moment.') ."</p>";
		    }else{

		    	$names = array();
		    	$count = 0;
		    	foreach( $json as $tid => $td){
		    		if( $td['oS'] != 'completed') continue;
		    		if( in_array($td['name'], $names)) continue;

		    		$LL = '';
		    		$NN = $td['name'];
		    		$NN2 = explode(' ', $NN);
		    		$x = 1;
		    		foreach( $NN2 as $NN3){
		    			if( $x > 2) continue;
		    			$LL .= substr( $NN3, 0 , 1);
		    			$x++;
		    		}

		    		$content .= "<div class='evotx_att'>";
		    		$content .= "<p><span>{$LL}</span>". $td['name'] . "</p>";

		    		$content .= "</div>";

		    		$names[] = $td['name'];
		    		$count ++;
		    	}

		    	// if there are no completed orders
		    	if( $count <1 ){
		    		$content .= "<p class=''>". evo_lang('This event has no attendees at the moment.') ."</p>";
		    	}

		    }

		    $content .= "</div>";

		    return "<div class='evotx_attendees_anywhere'>{$content}</div>";
		}

	// standalone ticket button
		function ticket_button($atts){
			extract(shortcode_atts(array(
		     'id' => false,
		     'ri' => '0',
		     'btn_txt'=>'Buy Ticket Now',
		     'date_time'=>'no',
		     'location'=>'no'
		    ), $atts));

		    return "<div class='evotx_standalone'><a class='evcal_btn trig_evotx_btn' data-eid='{$id}' data-ri='{$ri}'>{$btn_txt}</a></div>";
		}

		function ligthbox($array){
			$array['evotx_lightbox']= array(
				'id'=>'evotx_lightbox',
				'CLclosebtn'=> 'evotx_lightbox',
			);return $array;
		}

	// FRONT END event card inclusion
		function frontend_box($object, $helpers, $EVENT){

			// debug
			//print_r( get_post_meta(1,'aa', true));
			//print_r( get_post_meta(1,'aa2', true));
			//print_r( get_post_meta(3825,'_tixholders', true));

			
			// globals
				// pass global evo lang to ticket extensions - which use AJAX to load content
				if(!isset($GLOBALS['EVOLANG'])) $GLOBALS['EVOLANG'] = evo_get_current_lang(); 

				global $evotx, $woocommerce, $EVOLANG;

			$eventPMV = $EVENT->get_data();

			// if only loggedin users can see
			if( evo_settings_check_yn($evotx->evotx_opt, 'evotx_loggedinuser')  &&  !is_user_logged_in() && 
				$EVENT->check_yn('evotx_tix') ){
				return $this->for_none_loggedin($helpers, $object, $EVENT);
				return;
			}

			
			// initiate event
			$event = new evotx_event($EVENT->ID, $eventPMV, $object->repeat_interval);

			$wcid = $event->wcid;


			// if event ticets enable
			if( $event->is_ticket_active() ):

				// get options array
				$woo_product_id = $wcid;
				$woometa = $event->wcmeta;

				// SET UP Global WC Product
				// get the woocommerce product
					$product = $event->product;
					
				// check if repeat interval is active for this event
					$ri_count_active = $event->is_ri_count_active();
					
					// check if tickets in stock for this instance of the event
					// returns the capacity for this repeating instance of the event
					// if variable event then dont check for this
					$tix_inStock = apply_filters('evotx_is_ticket_in_stock', ( $product && $product->is_type( 'variable' ) )? true: $event->has_tickets(), $event );

				
				// get if stop ticket sales now
					$stopSelling = apply_filters('evotx_stop_selling', $event->is_stop_selling_now() , $object);

					$can_sell_tickets = $stopSelling ? false: true;

				
				$opt = $helpers['evoOPT2'];	

				//print_r(get_post_meta(728));

			ob_start();


				$data_attr = array(
					'event_id'=>$EVENT->ID,
					'wcid'=>$woo_product_id,
					'tx'=>'',
					'ri'=>$object->repeat_interval,
				);
				$str = '';
				foreach($data_attr as $k=>$v){
					$str .= "data-".$k."='". (empty($v)?'':$v)."' ";
				}

				$class_names = array('evorow','evcal_evdata_row', 'evcal_evrow_sm','evo_metarow_tix');
				$class_names[] = $helpers['end_row_class'];

				// show remaining stock
					if(!$evotx->functions->show_remaining_stock($eventPMV, $woometa))
						$class_names[] = 'hide_remains';

				$helper = new evo_helper();
				$tix_helper = new evotx_helper($event);
			?>

				<div class='<?php echo implode(' ', $class_names);?>' <?php echo $str;?>>

					<span class='evcal_evdata_icons'><i class='fa <?php echo get_eventON_icon('evcal__evotx_001', 'fa-tags',$helpers['evOPT'] );?>'></i></span>
					<div class='evcal_evdata_cell'>							
						<h3 class='evo_h3'><?php $this->langEX('Ticket Section Title', 'evoTX_001');?></h3>
						
						<?php if( $event->get_wc_prop('_tx_text')) : // subtitle text ?>
							<p class='evo_data_val'><?php echo $event->get_wc_prop('_tx_text');?></p>	
						<?php endif;?>
								
						<?php
							// ticket image id - if exists
							$_tix_image_id = $event->get_prop('_tix_image_id');
						?>
						<div class='evoTX_wc <?php echo ($_tix_image_id)? 'tximg':'';?>' data-si='<?php echo !empty($woometa['_sold_individually'])? $woometa['_sold_individually'][0]: '-';?>' >
							
							<?php 
								// content for ticket image seciton
								if($_tix_image_id):
								$img_src = ($_tix_image_id)? 
									wp_get_attachment_image_src($_tix_image_id,'full'): null;
								$tix_img_src = (!empty($img_src))? $img_src[0]: null;
							?>
								<div class='evotx_image'>
									<img src='<?php echo $tix_img_src;?>'/>
									<?php if($event->get_prop('_tx_img_text' )):?>
										<p class='evotx_caption'><?php echo $event->get_prop('_tx_img_text' );?></p>
									<?php endif;?>
								</div>
							<?php endif;?>

							<?php 
							$show_card_content = true;

							// check if ticket already in cart ONLY for sold individually - @2.2
							if( $event->is_sold_individually() ){
								if( $event->is_ticket_in_cart_already() ){
									$show_card_content = false;

									$this->_print_already_in_cart_html( $event );
								}
							}



							?>

							<?php if($show_card_content):?>
							<div class='evoTX_wc_section'>
								
								<?php if( $event->get_wc_prop('_tx_subtiltle_text')) : ?>
									<p class='evo_data_val evotx_description'><?php echo $event->get_wc_prop('_tx_subtiltle_text');?></p>	
								<?php endif;?>

								<?php
									// if show whos coming enabled
									if( $event->check_yn('_tx_show_guest_list')):
										
										$guest_list = $event->get_guest_list();  

										if($guest_list):
									?>
									<div class='evotx_guest_list marb10'>
										<h4 class='evo_h4'><?php $this->langE('Guest List');?>  <em>(<?php $this->langE('Attending');?>: <?php echo ' '.$guest_list['count'];?>)</em></h4>
										<?php								
											echo "<p class='evotx_whos_coming' style='padding-top:5px;margin:0'><em class='tooltip'></em>" . $guest_list['guests'] . "</p>";
										?>
									</div>
								<?php endif; endif;?>
								
								<div class='evotx_ticket_purchase_section'>
								<?php 

								// if event is not cancelled & can sell tickets now							
								if(!$event->is_cancelled() && !$stopSelling && !empty($woometa['_price'])):

									if ( !$tix_inStock || $event->is_sold_out() ) :
										echo "<p class='evotx_soldout'>";
										$this->langEX('Sold Out!', 'evoTX_012');
										echo "</p>";
									else:										
										// SIMPLE product
										if( $product && $product->is_type( 'simple' ) ):

											// pluggable for ticket addons
											$use_default_temp = apply_filters('evotx_single_product_temp', true, $event, $product);

											if( $use_default_temp === true){
												// Use default single ticket add to cart template
												// deprecating the filter
												$template = $helper->template_locator(
													apply_filters('evotx_single_addtocart_templates', array(
														$evotx->addon_data['plugin_path'].'/templates/'
													), $EVENT->ID, $woo_product_id),
													'template-add-to-cart-single.php',
													$evotx->addon_data['plugin_path'].'/templates/template-add-to-cart-single.php'
												);

												include($template);
											}else{
												echo $use_default_temp;
											}

																						
										endif; // end simple product

										// VARIABLE Product
										if( $product && $product->is_type( 'variable' ) ):																						
											include($evotx->addon_data['plugin_path'].'/templates/template-add-to-cart-variable.php');

										endif;

										// inquier before buy button
										if( $event->check_yn('_allow_inquire')):
											EVO()->elements->print_trigger_element(array(
												'ajax_data'=>array(
													'event_id'=>$event->ID,
													'event_ri'=>$event->ri
												),
												'uid'=>'evotx_inquire_form',
												'ajax_action'=> 'evotx_inquire_before_buy_form',
												'ajax_type'=>'endpoint',
												'lb_class'=>'evotx_inqure_form',
												'title'=> eventon_get_custom_language($opt, 'evoTX_inq_01','Inquire before buy'),
												'class_attr'=>'evcal_btn evolb_trigger',
												'end'=>'client',
											), 'trig_lb');
											
										endif;

									endif; // is_in_stock()	


							
								// event is cancelled or can not sell tickets
								else: 

									// cancel event tag
										if( $event->is_cancelled() ){
											echo "<p><span class='evo_event_body_tag cancelled'>";
											evo_lang_e('Cancelled');
											echo "</span></p>";
										}

									echo "<p class='evotx_pastevent'>";
									$this->langEX('Tickets are not available for sale any more for this event!', 'evoTX_012a');
									echo "</p>";

									// if show next available for repeating event
									if($event->is_repeating_event() && $event->check_yn('_evotx_show_next_avai_event')){
										$next_available_repeat = $event->next_available_ri($object->repeat_interval);

										if($next_available_repeat){
											echo "<p class='evotx_next_event'>";
											echo "<a class='evcal_btn' href='".$event->get_permalink($next_available_repeat['ri']) ."'>". evo_lang('Next Available Event') . "</a>";
											echo "</p>";
										}
									}

								endif; // end current event check							
								?>


								<?php 

								// event globals
								echo $this->print_ticket_footer_content( $event );
								?>

								</div><!-- evotx_ticket_purchase_section-->
							</div><!-- .evoTX_wc_section -->
							<div class="clear"></div>
							<?php endif;?>
						</div>						
					</div>
										
				<?php echo $helpers['end'];?> 
				</div>


			<?php 
			$output = ob_get_clean();

			return $output;
			endif;
		}

		// return the ticket global data section
		// @since 2.2
		function print_ticket_footer_content($event){

			$tix_helper = new evotx_helper();

			// footer message section
			$tix_helper->__get_addtocart_msg_footer();			

			$data = array();
			$data['pf'] = $tix_helper->get_price_format_data();
			$data['t'] = $tix_helper->get_text_strings();

			$ticket_redirect = evo_settings_value(EVOTX()->evotx_opt,'evotx_wc_addcart_redirect');
			$wc_redirect_cart = get_option( 'woocommerce_cart_redirect_after_add' );
			
			// if redirect is not set use wc redirect value
			if( empty($ticket_redirect) && $wc_redirect_cart == 'yes') 
				$ticket_redirect = 'cart';

			
			$data['msg_interaction']['redirect'] = $ticket_redirect;
			$data['msg_interaction']['hide_after'] = false;

			global $EVOLANG;
			$data['event_data']['l'] = $EVOLANG;
			$data['event_data']['eid'] = $event->ID;
			$data['event_data']['ri'] = $event->ri;
			$data['event_data']['wcid'] = $event->wcid;
	 
			$data['select_data']= array(); // selection
				
			// plug for new data from addons		
			$tx_data = apply_filters('evotx_add_to_cart_evotxdata', $data, $event);

			$str = '';
			foreach( $tx_data as $field=>$val){
				$str .= ' data-'.$field."='". json_encode($val)."'";
			}
			?>
		 	<div class='evotx_data' <?php echo $str;?>></div>
			<?php
		}

		// print already in cart message - @2.2
			function _print_already_in_cart_html( $event){
				$tix_helper = new evotx_helper();

				$tix_helper->__get_addtocart_msg_footer('standalone', evo_lang('Ticket already in cart') );
			}

		// for not loggedin users
			function for_none_loggedin($helpers, $object, $EVENT){
				global $eventon;
				$lang = (!empty($eventon->evo_generator->shortcode_args['lang'])? $eventon->evo_generator->shortcode_args['lang']:'L1');
				ob_start();
				
				?>
				<div class='evorow evcal_evdata_row bordb evcal_evrow_sm evo_metarow_tix <?php echo $helpers['end_row_class']?>' data-tx='' data-event_id='<?php echo $object->event_id ?>' data-ri='<?php echo $object->repeat_interval; ?>'>
					<span class='evcal_evdata_icons'><i class='fa <?php echo get_eventON_icon('evcal__evotx_001', 'fa-tags',$helpers['evOPT'] );?>'></i></span>
					<div class='evcal_evdata_cell'>							
						<h3 class='evo_h3'><?php $this->langEX('Ticket Section Title', 'evoTX_001');?></h3>
						
					<?php
						$txt_1 = evo_lang('You must login to buy tickets!',$lang, $helpers['evoOPT2']);
						$txt_2 = evo_lang('Login Now',$lang, $helpers['evoOPT2']);
						echo "<p>{$txt_1}  ";

						$login_link = wp_login_url( $EVENT->get_permalink() );

						// check if custom login lin kprovided
							if(!empty($this->opt1['evo_login_link']))
								$login_link = $this->opt1['evo_login_link'];

						echo apply_filters('evo_login_button',"<a class='evotx_loginnow_btn evcal_btn' href='".$login_link ."'>{$txt_2}</a>", $login_link, $txt_2);
						echo "</p>";

				?></div></div><?php

				return ob_get_clean();
			}


		// event card inclusion functions
			function eventcard_array($array, $pmv, $eventid, $repeat_interval){
				$array['evotx']= array(
					'event_id' => $eventid,
					'repeat_interval'=>$repeat_interval,
					'epmv'=>$pmv
				);
				return $array;
			}
			function eventcard_adds($array){
				$array[] = 'evotx';
				return $array;
			}

	// Inquire Form
		function inqure_form_fields(){
			$opt = EVOTX()->opt2;
			return  apply_filters('evotx_inquiry_fields', array(
				'name'=>array('text',eventon_get_custom_language($opt, 'evoTX_inq_02','Your Name')),	
				'email'=>array('text',eventon_get_custom_language($opt, 'evoTX_inq_03','Email Address')),
				'phone'=>array('text',eventon_get_custom_language($opt, 'evoTX_inq_04a','Phone Number')),		
				'message'=>array('textarea',eventon_get_custom_language($opt, 'evoTX_inq_04','Question'))
			));
		}
	// get language fast for evo_lang
		function lang($text){	return evo_lang($text, '', $this->opt2);}
		function langE($text){ echo $this->lang($text); }
		function langX($text, $var){	return eventon_get_custom_language($this->opt2, $var, $text);	}
		function langEX($text, $var){	echo eventon_get_custom_language($this->opt2, $var, $text);		}
	// get event neat times - 1.1.10
		function get_proper_time($event_id, $ri){
			global $evotx;
			$time = $evotx->functions->get_event_time('', $ri, $event_id);			
	    	return $time;
		}
}