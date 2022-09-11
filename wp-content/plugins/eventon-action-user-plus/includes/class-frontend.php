<?php
/**
 * ActionUser Plus Frontend
 * @version 0.5
 */

class evoaup_frontend{
	public $status;
	public $purchased_data;

	public function __construct(){
		add_action( 'init', array( $this, 'register_frontend_scripts' ) ,15);
		add_action( 'eventon_enqueue_styles', array( $this, 'styles' ) ,1);
		add_filter('eventon_extra_tax',array($this,'extra_tax'),10,1);

		$this->opt = get_option('evcal_options_evcal_1');
		$this->opt2 = get_option('evcal_options_evcal_2');
		EVO()->cal->load_more('evoau_1');
		$this->optau = EVO()->cal->get_op('evoau_1');

		$this->fnc = new evoaup_fnc();

		// woocommerce	
			// AT CART
				add_filter('woocommerce_get_cart_item_from_session', array($this,'get_cart_item_meta_values'), 1, 3 );
				add_filter('woocommerce_cart_item_name',array($this,'add_to_cart_item_names'),1,3);
				add_filter('woocommerce_cart_item_permalink',array($this,'cart_item_permalink'),1,3);

			// saving meta data
				add_action('woocommerce_checkout_create_order_line_item',array($this,'order_item_meta_update_new'),1,4);
				add_action('woocommerce_before_cart_item_quantity_zero',array($this,'update_removed_cart_items'),1,1);
			// cart validation
				add_action('woocommerce_check_cart_items', array($this, 'cart_validation'), 10, 3);
			// checkout
				//add_action('woocommerce_checkout_order_processed', array($this, 'order_process'), 10, 1);
				add_filter( 'pre_option_woocommerce_enable_guest_checkout', array($this,'conditional_guest_checkout') );
				add_action('woocommerce_checkout_update_order_meta',array($this,'update_order_meta'),10,2);

			// order status change
				// when orders are cancelled
				foreach(array(
					array('old'=>'completed','new'=>'cancelled'),
					array('old'=>'completed','new'=>'failed'),
					array('old'=>'completed','new'=>'refunded'),
				) as $status){
					add_action('woocommerce_order_status_'.$status['old'] .'_to_'. $status['new'], array($this, 'order_revert'), 10,1);
				}


			// display meta data
				add_filter('woocommerce_order_items_meta_display', array($this, 'ordermeta_display'), 10,2);
				add_filter('woocommerce_display_item_meta', array($this, 'ordermeta_display'), 10,2);
				add_filter('woocommerce_order_item_permalink', array($this, 'orderdetails_permalink'), 10,3);
				add_action('woocommerce_view_order', array( $this, 'wc_order_aup' ), 10 ,1);
				add_action('woocommerce_thankyou', array( $this, 'wc_order_aup' ), 10 ,1);
				add_filter('woocommerce_hidden_order_itemmeta', array($this,'hidden_order_itemmeta'), 10, 1);


		// event submission info in email
		add_action('woocommerce_email_customer_details', array( $this, 'wc_order_aup_email' ), 10 ,4);

		// Frontend form
			add_filter('evoau_form_display_check', array($this, 'form_check'), 10,4);
			add_action('evoau_form_before', array($this, 'before_form'), 10, 1);			
			add_filter('evoau_form_field_permissions_array', array($this, 'field_permissions'), 10,3);
			add_filter('evoau_get_edit_form_args', array($this, 'edit_event_form_args'), 10,3);
			add_filter('evoau_submit_form_under_title', array($this, 'under_title'), 10,2);
			//add_filter('evoau_before_form_submission', array($this, 'before_form_submission'), 10,2);
			add_action('eventonau_save_form_submissions', array($this, 'form_submission'), 10,2);
			add_action('evoau_frontend_scripts_enqueue', array($this, 'scripts'), 10);
		
		// editing after submissions
		add_filter('evoau_datetime_editing', array($this, 'disable_editing'), 10, 3);
		add_action('evoau_before_submission_form_fields', array($this, 'before_form_fields'), 10, 2);
	}

	// Scripts and styles
		function register_frontend_scripts(){
			global $eventon_aup;
			wp_register_script( 'evoaup_script',$eventon_aup->assets_path.'script.js','', $eventon_aup->version, true );
			wp_localize_script( 
				'evoaup_script', 
				'evoaup_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evoaup_nonce' )
				)
			);
			wp_register_style( 'evoaup_styles',$eventon_aup->assets_path.'styles.css','', $eventon_aup->version);
			
		}
		function styles(){
			wp_enqueue_style('evoaup_styles');	
		}
		function scripts(){ wp_enqueue_script('evoaup_script'); }
		
	// form filtering
		// under title
		function under_title($_EDITFORM, $EVENT){
			if(!$_EDITFORM && isset($_POST['sformat']) && $_POST['sformat'] == 'level_based' && isset($_POST['level'])){
				$submission_level = (int)$_POST['level'];
				$submission_level_data = $this->fnc->get_submission_level_data($submission_level);

				if(!$submission_level_data) return;
				if(!isset($submission_level_data['name'])) return;

				$color = isset($submission_level_data['color'])? '#'.$submission_level_data['color']: false;

				echo "<p class='evoaup_under_title'>". evo_lang('Event Submission Level')." <span style='background-color:{$color}'>". $submission_level_data['name'] ."</span></p>";
			}

			// for the edit form
			if($_EDITFORM && isset($_POST['sformat']) && $_POST['sformat'] == 'level_based'){
				$SL = $EVENT->get_prop('_evoaup_submission_level');

				if(!$SL) return;

				$submission_level_data = $this->fnc->get_submission_level_data($SL);
				if(!isset($submission_level_data['name'])) return;

				$color = isset($submission_level_data['color'])? '#'.$submission_level_data['color']: false;

				echo "<p class='evoaup_under_title'>". evo_lang('Event Submission Level')." <span style='background-color:{$color}'>". $submission_level_data['name'] ."</span></p>";
			}

		}

		// for ajax based event form
		function edit_event_form_args($args, $event_id, $sformat=''){
			$event_type = get_post_meta($event_id,'_evoaup_event_type',true);	

			if(!is_array($args))  $args = array();		

			if($event_type == 'paid_submission'){
				$args['hidden_fields']['submission_type'] = 'paid_submission';
			}	

			$submission_format = !empty($sformat)? $sformat:'regular';

			$args['hidden_fields']['submission_format'] = $submission_format;

			$submission_level = get_post_meta($event_id,'_evoaup_submission_level',true);
			if(!empty($submission_level)){
				$args['hidden_fields']['submission_level'] = $submission_level;
			}

			return $args;
		}

		// before form load send submission level field permissions array
			function field_permissions($permission_array, $editform, $event_id){
					
				$submission_level = false;
				$submission_level = get_post_meta($event_id, '_evoaup_submission_level', true);

				if(isset( $_POST['level'])) $submission_level = (int)$_POST['level'];

				if( !$submission_level) return $permission_array;

				$submission_format = isset( $_POST['sformat'])? $_POST['sformat']:'regular';

				// Regular submission
				if($submission_format == 'regular') return $permission_array;

				// event was level_based submission
				$submission_level_data = $this->fnc->get_submission_level_data($submission_level);

				$basic_fields = EVOAU()->frontend->au_form_fields('default');

				// return default fields if submission level is not found in settings
				if(!$submission_level_data) return $basic_fields;
				if(!isset($submission_level_data['fields'])) return $basic_fields;



				// return submission level approved fields
				return $submission_level_data['fields'];
			}

		// validation before form submission
			function before_form_submission($boolean, $post){
				// check for pre-conditions
				if(!isset($_POST['submission_type'])) return $boolean;
				if($_POST['submission_type'] !='paid_submission') return $boolean;

				$opt = $this->optau;

				// check if paid submissions active and wcid created  
				if(!evo_settings_check_yn($opt, 'evoaup_create_product')) return $boolean;
				if(empty($opt['evoaup_product_id'])) return $boolean;

				if(empty($_POST['submission_level'])) return $boolean;
				if(empty($opt['evoaup_levels'])) return $boolean;

			}

		// after event created and post meta values saved	
			function form_submission($eventid, $formtype){
				if( !empty($_POST['submission_type']) && $_POST['submission_type']=='paid_submission'){
					$opt = $this->optau;

					if(!evo_settings_check_yn($opt, 'evoaup_create_product')) return;
					if(empty($opt['evoaup_product_id'])) return;

					$purchase_submissions_data = $this->fnc->have_valid_event_submissions($opt['evoaup_product_id']);
					$order_id = '';

					// if this is a level based submission
					if(!empty($_POST['submission_level']) && !empty($_POST['submission_format']) && $_POST['submission_format']=='level_based'){
		
						$sub_levels = !empty($opt['evoaup_levels'])? $opt['evoaup_levels']: false;

						// if submission levels exist
						if($sub_levels){
							if(!empty($sub_levels[$_POST['submission_level']])){

								$reducable_order_id = '';

								foreach($purchase_submissions_data['level_data'] as $order_id=>$data){
									foreach($data as $level=>$count){
										if($level == $_POST['submission_level'] && $count>0){
											$reducable_order_id = $order_id;
										}
									}
								}

								$order_id = $reducable_order_id;

								// get order submission level data
								$order_data = get_post_meta($reducable_order_id, '_submission_data',true);
								$stock = (int)$order_data[$_POST['submission_level']];
								$order_data[$_POST['submission_level']] = $stock-1;							
								update_post_meta($reducable_order_id, '_submission_data', $order_data);

								add_post_meta($eventid,'_evoaup_submission_level',$_POST['submission_level']);
							}
						}

					// regular submission
					}else{		
					
						if($purchase_submissions_data['allcount']>0){

							$reducable_order_id = '';
							$reducable_count = 0;
							foreach($purchase_submissions_data['level_data'] as $order_id=>$data){
								foreach($data as $level=>$count){
									if($level == $_POST['submission_level'] && $count>0){
										$reducable_order_id = $order_id;
										$reducable_count = $count;										
									}
								}
								if(!empty($reducable_order_id)) break;
							}
							$order_id = $reducable_order_id;

							// reduce submissions left from order
							$new_count = (int)$reducable_count - 1;
							$new_count = ($new_count<0)? 0: $new_count;
							update_post_meta($order_id, '_submission_count', $new_count);
						}
					}

					// Order post
					if(!empty($order_id)){					
						$submitted_events = get_post_meta($order_id, '_submitted_events',true);
						if(!empty($submitted_events) && is_array($submitted_events) && sizeof($submitted_events)>0){
							$submitted_events = array_merge($submitted_events, $eventid);
							update_post_meta($order_id, '_submitted_events', $submitted_events);
						}else{
							add_post_meta($order_id,'_submitted_events',array($eventid));
						}
					}

					// event post
						add_post_meta($eventid,'_evoaup_event_type','paid_submission');			
					
				}
			}

		// checking form before loading
			function form_check($boolean, $eid, $_EDITFORM, $atts){
				if($_EDITFORM) return true;
				
				$opt = $this->optau;

				if(!$this->fnc->is_paid_submission_enable($opt)) return true;
				
				// check if current user have event submissions left
				$this->purchased_data = $this->fnc->have_valid_event_submissions($opt['evoaup_product_id']);

				// no purchases founds for user
				if(!$this->purchased_data){
					$this->status   = 'nopurchase';
					return false;
				}

				if($this->purchased_data['allcount']==0){
					$this->status = 'usedall';	
				}else{
					$this->status = 'level_selection';
				}				
				
				return false;
			}
		
		// messages if not qualify for form display
			function before_form($atts){
				$opt = $this->optau;
				$product_id = $opt['evoaup_product_id'];

				if(!$this->fnc->is_paid_submission_enable($opt)) return true;

				echo "<div class='evoaup_section'>";

				echo "<input type='hidden' class='evoau_form_atts' data-d='". json_encode($atts)."'/>";

				$this->purchased_data = $this->fnc->have_valid_event_submissions($product_id);
				//print_r($this->purchased_data );

				?>
				<?php if(!is_user_logged_in()):

					$login_link = wp_login_url(get_permalink());
					if(!empty( $this->opt['evo_login_link'])) $login_link = $this->opt['evo_login_link'];

				?>
					<h3><?php evo_lang_e('Purchase Submissions or Login to use your already purchased submission');?></h3>
					<p class='evoaup_desc'><?php evo_lang_e('Purchase event submissions using the below section. After purchased, please revisit this page to submit your purchased events.');?></p>
					<p style="padding-bottom: 20px"><a class='evcal_btn' href='<?php echo $login_link;?>'><?php evo_lang_e('Login to use purchased submissions');?></a></p>
				<?php endif;?>
				
				
				<?php if(is_user_logged_in()):?>
					<?php if($this->status=='nopurchase'):?>
						<h3><?php evo_lang_e('You do not have any paid submissions left');?></h3>
						<p class='evoaup_desc'><?php evo_lang_e('Purchase event submissions using the below section. After purchased, please revisit this page to submit your purchased events.');?></p>
					<?php elseif($this->status=='usedall'):?>
						<h3><?php evo_lang_e('You have used all your paid submissions');?></h3>
						<p class='evoaup_desc'><?php evo_lang_e('Purchase event submissions using the below section. After purchased, please revisit this page to submit your purchased events.');?></p>
					<?php else:?>
						<h3><?php evo_lang_e('Select Event Submission Type');?></h3>
						<p><?php evo_lang_e('In order to submit an event please select your purchased event submission level type');?></p>
						<?php 
						// get purchased level information for select to submit event
						$this->fnc->print_submission_level_selection_html($this->purchased_data, $product_id);
						?>

						
						<div class='evoaup_purchase_additionals evoaup_purchase_submissions'>
							<h3><?php evo_lang_e('Purchase additional event submission packages');?></h3>
							<p><?php evo_lang_e('You can also purchase additional event submission packages from below list.');?></p>								
						</div>
					<?php endif;?>
				<?php endif;?>

					<div class='evoaup_purchase_form'>						
						<?php
							$submission_levels = $this->fnc->get_submission_levels($product_id, '', true);

							if($submission_levels){
								foreach($submission_levels as $index=>$submission_level){
									$this->fnc->print_submission_level_html($index, $submission_level, $product_id);
								}							
							}
							$this->purchase_form_messages();
						?>
						</div>		
				<?php 
			
				echo "</div>";
			}

		function purchase_form_messages(){
			echo "<p class='evoaup_msg'></p>";
		}

	// Woocomerce related functions

		// CART
			function get_cart_item_meta_values($session_data, $values, $key){
				//print_r($values);
				//print_r( get_post_meta(1941));
				if (array_key_exists( 'evoaup_price', $values ) ){
		       		$session_data['evoaup_price'] = $values['evoaup_price'];
		       		$session_data['data']->set_price( $values['evoaup_price'] );
		        }
		        return $session_data;
			}
			function add_to_cart_item_names($product_name, $values, $cart_item_key){
				//print_r($values);
				if(isset($values['evoaup_name'])){		

		        	$return_string = $product_name . "<br/><span class='item_meta_data'>";
		        	
		        	$return_string .= "<b>".evo_lang('Event Submission Level').":</b> <i>" . $values['evoaup_name'] . "</i>";
		        	if(isset($values['evoaup_submissions'])){
		        		$submissions_qty = (int)$values['evoaup_submissions'] * (int)$values['quantity'];
		        		$return_string .= "<br/><b>".evo_lang('Event Submissions Qty').":</b> <i>" . $submissions_qty . "</i>";
		        	}
		           
		            $return_string .= "</span>";
		            return $return_string;
				}

				return $product_name;
			}
			function cart_item_permalink($link, $cart_item, $cart_item_key){
				if(empty($cart_item['evoaup_url'])) return $link;
				return $cart_item['evoaup_url'];
			}


		// CHECKOUT
			// add custom data as meta data to order item	    
				function order_item_meta_update_new($item, $cart_item_key, $values, $order){
					foreach(array(
						'evoaup_name'=>'Submission-Level-Name',
						'evoaup_level'=>'_evoaup_submission_level',
					) as $value=>$var){
						if(isset($values[$value]))
							$item->add_meta_data($var, $values[$value],true); 
					}				
				}

			// remove custom data if item removed from cart
				function update_removed_cart_items($cart_item_key){
			        global $woocommerce;
			        // Get cart
			        $cart = $woocommerce->cart->get_cart();
			        
			        // For each item in cart, if item is upsell of deleted product, delete it
		            foreach( $cart as $key => $values){
			        	if(empty($values['evoaup_name']) ) continue;
				        if ( $values['evoaup_name'] == $cart_item_key ){
				            unset( $woocommerce->cart->cart_contents[ $key ] );
				        }
			        }
			    }

			// cart validation
				function cart_validation(){
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						if(empty($cart_item['evoaup_level'])) continue;

						if ( $cart_item['product_id'] > 0 ) {
							
							$submission_level = $cart_item['evoaup_level'];

							$submission_active = $this->fnc->is_submission_level_active( $submission_level);
							$stock_available = $this->fnc->is_submission_stock_available($submission_level, $cart_item['quantity']);
							// submission not active and no stock
							if(!$submission_active && !$stock_available){
								WC()->cart->remove_cart_item($cart_item_key);
								wc_add_notice( 'Item removed from cart, no longer available for sale!', 'error' );
							}						
						}					
					}
				}
			// Order details meta display
				function ordermeta_display($output, $obj){
					$output = str_replace('Submission-Level-Name', evo_lang('Submission Level Name'), $output); 
					return $output;
				}
				function orderdetails_permalink($link='', $item, $order ){
					$order_item_id = $item->get_id();
					$url = wc_get_order_item_meta($order_item_id, '_evoaup_url');
					if(empty($url)) return $link;
					return $url;
				}

		// ORDER Post created on checkout
			function update_order_meta($order_id){
				$order = new WC_Order( $order_id );	
			    $items = $order->get_items();

			    $paidevent = false;
			    $submission_count = 0;
			    $submission_data = array();
			    $url = '';

			    foreach(WC()->cart->get_cart() as $order_item_id=>$item) {	
			    	if(!isset($item['_producttype'])) continue;
			    	if($item['_producttype'] != 'evo_submission') continue;

			    	$submission_level = isset($item['evoaup_level'])? $item['evoaup_level']:false;
			    	
			    	// LEVELS
			    	if( $submission_level ){
			    		$submission_level_data = $this->fnc->get_submission_level_data($submission_level);
			    		
			    		if(!$submission_level_data) continue;

			    		$submission_level_qty = isset($submission_level_data['submissions'])? (int)$submission_level_data['submissions']:1;

			    		// get multiple of submissions in level times item qty
			    		$submission_data[$submission_level] = ((int)$item['quantity']) * $submission_level_qty;
			    	// NO LEVELS
			    	}else{
			    		$submission_count += (int)$item['quantity'];
			    	}		    		

			    	if(isset($item['evoaup_url'])) $url = $item['evoaup_url'];
			    	$paidevent = true;	    	  
			    }

			    // there are order items with paid event submissions
			    	if($paidevent){
			    		update_post_meta($order_id, '_order_type','evo_submission');
			    		update_post_meta($order_id, '_evoaup_url',$url);
			    	}

			    // save purchased submission count as separate data
			    if(count($submission_data)>0){
			    	update_post_meta($order_id, '_submission_data',$submission_data);
			    }else{
			    // old method
			    	if($submission_count>0) 
			    		update_post_meta($order_id, '_submission_count',$submission_count);
			    }		    
			}
		

		// ORDER REVERT
			function order_revert($order_id){
				$order = new WC_Order( $order_id );	
		    	$items = $order->get_items();
		    	$index = 1;

		    	// each order item in the order
		    	foreach ($items as $item) {
		    		$type = get_post_meta( $item['product_id'], '_producttype', true);  
		    		if(empty($type)) continue; 
		    		if($type != 'evo_submission') continue;

		    		$current_stock = get_post_meta($item['product_id'], '_stock', true);
		    		$new_capacity = $current_stock + $item['qty'];
		    		update_post_meta($item['product_id'], '_stock',$new_capacity);
		    	}

		    	// mark woocommerce product back in stock
		    	update_post_meta($item['product_id'], '_stock_status','instock');
			}

		// SUPPORTIVE
		// hide order item meta
			function hidden_order_itemmeta($array){
				$array[] = '_evoaup_submission_level';
				$array[] = '_evoaup_url';
    			return $array;
			}

		// Disable guest checkout with event submission in cart
			function conditional_guest_checkout($value){
				$opt = $this->optau;
				$product_id = (!empty($opt['evoaup_product_id'])? $opt['evoaup_product_id']: false);

				if(!$product_id) return $value;

				if ( WC()->cart ) {
			    	$cart = WC()->cart->get_cart();
		    		foreach ( $cart as $item ) {
		      			if (  $item['product_id'] == $product_id ) {
			        		$value = "no";
			        		break;
		     			}
			    	}
			  	}
			  	return $value;
			}

		// show event submissions info on order details after purchase in my account
			function wc_order_aup($order_id){
				$opt = $this->optau;
				$submissions_page = (!empty($opt['evoaup_submission_page']))? $opt['evoaup_submission_page']:false;

				// if submission page is not saved in settings go through each order item and find _evoaup_url
					if(!$submissions_page){
						$submissions_page = get_post_meta($order_id, '_evoaup_url',true);

						if(!$submissions_page){
							$order = new WC_Order( $order_id );	
			    			$items = $order->get_items();
			    			foreach($items as $item){
			    				$order_item_id = $item->get_id();
								$submissions_page = wc_get_order_item_meta($order_item_id, '_evoaup_url');
								if(!empty($submissions_page)) break;
			    			}
			    		}
					} 

				if(empty($submissions_page)) return;

		
				$order_type = get_post_meta($order_id, '_order_type', true);

				if( $order_type == 'evo_submission'){

					$order = new WC_Order( $order_id );
					?><h2><?php echo evo_lang_e('Your Event Submissions');?></h2><?php

					$btn_color = '#'.(!empty($this->opt['evcal_gen_btn_bgc'])? $this->opt['evcal_gen_btn_bgc']:'58a1d6');
					$btn_text_color = '#'.(!empty($this->opt['evcal_gen_btn_fc'])? $this->opt['evcal_gen_btn_fc']:'ffffff');

					if (  $order->has_status('completed') ) {											
					?>
						
						<p><?php evo_lang_e('You can submit your purchased event submissions from below link.');?></p>
						<p><a href='<?php echo $submissions_page;?>' class='evcal_btn' style='background-color:<?php echo $btn_color;?>; color:<?php echo $btn_text_color;?>;'><?php echo evo_lang_e('Submit Events');?></a></p>
						<?php

					} else{
						?><p><?php evo_lang_e('Your order is still in process, once the order is completed you can submit your purchased event submissions');?>!</p>
						<p><a href='<?php echo $submissions_page;?>' class='evcal_btn' style='background-color:<?php echo $btn_color;?>; color:<?php echo $btn_text_color;?>;'><?php echo evo_lang_e('Submit Events');?></a></p><?php

					}
				}
			}

		// in the email
			function wc_order_aup_email($order, $sent_to_admin, $plain_text, $email){

				$opt = $this->optau;
				$submissions_page = (!empty($opt['evoaup_submission_page']))? $opt['evoaup_submission_page']:false;

				if(!$submissions_page) return;

				$order_type = get_post_meta($order->get_id(), '_order_type', true);

				if( $order_type == 'evo_submission'){

					$order = new WC_Order( $order_id );
					?><h2><?php echo evo_lang_e('Your Event Submissions');?></h2><?php

					if ( $order->has_status('completed') ) {
					
						// colors
						$btn_color = '#'.(!empty($this->opt['evcal_gen_btn_bgc'])? $this->opt['evcal_gen_btn_bgc']:'58a1d6');
						$btn_text_color = '#'.(!empty($this->opt['evcal_gen_btn_fc'])? $this->opt['evcal_gen_btn_fc']:'ffffff');
						?>
						
						<p><?php evo_lang_e('You can submit your purchased event submissions from below link.');?></p>
						<p><a href='<?php echo $submissions_page;?>' class='evcal_btn' style='background-color:<?php echo $btn_color;?>; color:<?php echo $btn_text_color;?>;'><?php echo evo_lang_e('Submit Events');?></a></p>
						<?php

					} else{
						?><p><?php evo_lang_e('Your order is still in process, once the order is completed you can submit your purchased event submissions');?>!</p><?php
					}
				}

			}

	// Taxonomies
		function extra_tax($array){
			$array['evoaur']='event_user_roles';
			return $array;
		}

	// disable date time editing
		function before_form_fields($_EDITFORM, $EPMV){

			$disabled = $this->disable_editing(false, $_EDITFORM, $EPMV);

			if($disabled){
				echo "<p style='padding:10px 10px 20px; margin:0; text-align:center; font-style:italic' class='evoaup_uneditable_notice'>".evo_lang('Event date and time editing is disabled! Please contact us regarding date/time changes!'). "</p>";
			}
		}

		function disable_editing($boolean, $_EDITFORM, $EPMV=''){

			if($_EDITFORM && !empty($EPMV)){
				date_default_timezone_set('UTC');

				if($EPMV['evcal_erow'][0] < time() && !empty($EPMV['_evoaup_event_type']) && $EPMV['_evoaup_event_type'][0] =='paid_submission'
				){
					return true;
				}
			}
			return $boolean;

		}

	// Current user roles
		function get_current_userrole(){
			global $current_user;

			$user_roles = $current_user->roles;
			$user_role = array_shift($user_roles);
			return $user_role;
		}
}