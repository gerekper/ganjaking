<?php
/**
 * Event Tickets Ajax Handle
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-TX/classes/AJAX
 * @vetxion     1.8
 */

class evo_tix_ajax{
	/**
	 * Hook into ajax events
	 */
	public function __construct(){
		$ajax_events = array(
			'evoTX_ajax_06'=>'submit_inquiry',
			'evoTX_ajax_08'=>'evoTX_ajax_08',
			'the_ajax_evotx_a5'=>'evoTX_checkin_',
			'evoTX_ajax_09'=>'wc_cart_updates',
			'evotx_add_to_cart'=>'evotx_add_to_cart',
			'evotx_standalone_form'=>'evotx_standalone_form',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	// Add event ticket to cart custom AJAX
	// @since 1.6.7 @U 1.7.2
		function evotx_add_to_cart(){			
			if( !isset($_POST['data'])) return false;

			// validate nonce
			
			
			$DATA = $_POST['data'];
			$event_data = $DATA['event_data'];
			$DATA['qty'] = $qty = (int)$_POST['qty'];

			// name your price
			if(isset($_POST['nyp'])) $DATA['nyp'] = sanitize_text_field( $_POST['nyp']);

			$event_id = $event_data['eid'];
			$wcid = $event_data['wcid'];
			$RI = isset($event_data['ri'])?$event_data['ri']:'0';			

			$TICKET = new evotx_event($event_id, '', $RI, $wcid);

			$adding = $TICKET->add_ticket_to_cart($DATA);

			echo $adding;
			exit;
		}

	// standalone form
		function evotx_standalone_form(){
			$event_id = $_POST['eid'];

			$EVENT = new EVO_Event( $event_id);

			$helpers = array(
				'evOPT'=> EVOTX()->frontend->opt1,
				'evoOPT2'=> EVOTX()->opt2,
				'end_row_class'=>null,
				'end'=>null,
			);

			$object = (object)array(
				'repeat_interval'=> (int)$_POST['ri'],
				'event_id'=> $event_id,
				'epmv'=> $EVENT->get_data()
			);


			$html =  EVOTX()->frontend->frontend_box($object, $helpers, $EVENT);
			$return_content = array(
				'status'=>'good',
				'html'=>$html,
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// for evo-tix post page and from event edit page
		function evoTX_checkin_(){
			global $evotx;

			$ticketNumber = $_POST['tid'];
			$msg = '';

			// split ticket number
			$tixNum = explode('-', $ticketNumber);
			$OrderComplete = $evotx->functions->is_order_complete($tixNum[1]);
			$CheckinLang = $evotx->functions->get_statuses_lang(); // get both check status lang

			// order is not complete
			if($OrderComplete){
				$tixID = $tixNum[0];

				$current_status = $_POST['status'];

				$evotx_tix = new evotx_tix();

				$other_status = $evotx_tix->get_other_status($current_status);
				$evotx_tix->change_ticket_number_status($other_status[0], $ticketNumber, $tixID);

				$newTixStaus = $other_status[0];

			}else{
				$msg = 'Order not completed';
				$newTixStaus = $_POST['status'];
			}			

			$return_content = array(
				'msg'=>$msg,
				'new_status'=>$newTixStaus,
				'new_status_lang'=>$CheckinLang[$newTixStaus],
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// submit inquiry form
		function submit_inquiry(){
			global $evotx;

			$evoOpt = get_evoOPT_array('tx');

			$event_id = $_POST['event_id'];
			$ri = $_POST['ri'];

			$_event_pmv = get_post_custom($event_id);
			
			// get email address
			$_to_mail = (!empty($_event_pmv['_tx_inq_email']))? $_event_pmv['_tx_inq_email'][0]:
				( !empty($evoOpt['evotx_tix_inquiries_def_email'])? $evoOpt['evotx_tix_inquiries_def_email']:
					get_option('admin_email'));
			// get subject
			$subject = (!empty($_event_pmv['_tx_inq_subject']))? 
				$_event_pmv['_tx_inq_subject'][0]:
				( !empty($evoOpt['evotx_tix_inquiries_def_subject'])? $evoOpt['evotx_tix_inquiries_def_subject']:'New Ticket Sale Inquery');

			$from_email = $_POST['email'];
			$headers = 'From: '.$_POST['email'];	


			$helper = new evo_helper();

			ob_start();
			?>
				<div style='padding:20px;color:#777777'>
				<p><?php evo_lang_e('Event');?>: <b><?php echo get_the_title( $event_id ); ?></b></p>
				<p><?php evo_lang_e('From');?>: <b><?php echo $_POST['name'].' ('.$_POST['email'].')';?></b></p>
				<p><?php evo_lang_e('Message');?>: <br/><?php echo $_POST['message'];?></p>
			<?php
				// Other data collected from the form
				foreach($evotx->frontend->inquire_fields() as $key=>$val){
					if(in_array($key, array('name','email','message'))) continue;
					if(empty($_POST[$key])) continue;

					echo "<p>".$val[1].": <br/>".$_POST[$key] . "</p>";
				}
			?>
				</div>
			<?php
			$body = ob_get_clean();

			$email_body = $helper->get_email_body_content($body);
			$send_email = $helper->send_email(array(
				'to'=> $_to_mail,
				'from'=>$from_email,
				'subject'=>$subject,
				'html'=>'yes',
				'message'=> $email_body,
				'reply-to'=> $from_email
			));

		}

	// WC Cart updated data
		function wc_cart_updates(){

			if(!function_exists('woocommerce_mini_cart')) return false;

			ob_start();

	        woocommerce_mini_cart();

	        $mini_cart = ob_get_clean();

	        // Fragments and mini cart are returned
	       $data = array(
	            'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
	                    'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
	                )
	            ),
	            'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
	        );
	       
	       	echo wp_json_encode($data);
			wp_die();
		}

	// ADD to cart for variable items
	// @deprecating
		function evotx_woocommerce_ajax_add_to_cart() {
			global $woocommerce;
			 
			// Initial values
				$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
				$variation_id     = apply_filters( 'woocommerce_add_to_cart_variation_id', absint( $_POST['variation_id'] ) );
				$quantity  = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
				
			// if variations are sent
				if(isset($_POST['variations'])){
					$att=array();
					foreach($_POST['variations'] as $varF=>$varV){
						$att[$varF]=$varV;
					}
				}
			

			if($passed_validation && !empty($variation_id)){
				$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ,$att);
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );

				$frags = new WC_AJAX( );
	        	$frags->get_refreshed_fragments( );
			}

			/*
				// if variation ID is given
				if(!empty($variation_id) && $variation_id > 0){
					
					$cart_item_key = $woocommerce->cart->add_to_cart( $product_id, $quantity, $variation_id ,$att);
					 
					do_action( 'woocommerce_ajax_added_to_cart', $product_id ,$quantity, $variation_id ,$variation);

					// Return fragments
					//$frags = new WC_AJAX( );
		        	//$frags->get_refreshed_fragments( );


					// if WC settings set to redirect after adding to cart
					if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
						// show cart notification
					 	wc_add_to_cart_message( $product_id );
					 	$woocommerce->set_messages();
					}
				}else{
				 
					if ( $passed_validation && $woocommerce->cart->add_to_cart( $product_id, $quantity) ) {
						do_action( 'woocommerce_ajax_added_to_cart', $product_id );
						 
						if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
						 	woocommerce_add_to_cart_message( $product_id );
						 	$woocommerce->set_messages();
						}
						 
						// Return fragments
						// $frags = new WC_AJAX( );
		        		// $frags->get_refreshed_fragments( );
					 
					} else {
					 
						header( 'Content-Type: application/json; charset=utf-8' );
						 
						// If there was an error adding to the cart, redirect to the product page to show any errors
						$data = array(
						 	'error' => true,
						 	'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
						);
						 
						$woocommerce->set_messages();
						 
						echo json_encode( $data );
					 
					}
					die();
				} // endif
			
			*/
		
			$output = array(
				'key'=>$cart_item_key,
				'variation'=>WC()->cart->cart_contents_total
			);
			echo json_encode( $output );
			exit;
		 }
	
	// make sure proper amount of tickets are created for all past shop_orders
	// @deprecating
		function evoTX_ajax_08(){
			$shop_orders = new WP_Query(array(
				'post_type'=>'shop_order',
				'posts_per_page'=>-1,				
			));

			if($shop_orders->have_posts()):
				while($shop_orders->have_posts()): $shop_orders->the_post();
					if($shop_orders->post->post_status!='wc-completed') continue;

					$orderPMV = get_post_custom($shop_orders->post->ID);

					if(!empty($orderPMV['_tixids'])){
						$ticketnumbers = unserialize($orderPMV['_tixids'][0]);
						if(is_array($ticketnumbers)){

						}else{
							$ticketnumbers;
						}
					}else{
						// create tickets
					}
				endwhile;
			endif;
			wp_reset_postdata();
		}
}
new evo_tix_ajax();


?>