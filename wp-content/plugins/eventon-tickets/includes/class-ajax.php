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
	public $help, $postdata;
	/**
	 * Hook into ajax events
	 */
	public function __construct(){
		$ajax_events = array(			
			'evoTX_ajax_08'=>'evoTX_ajax_08',
			'the_ajax_evotx_a5'=>'evoTX_checkin_',
			'evoTX_ajax_09'=>'wc_cart_updates',
			'evotx_add_to_cart'=>'evotx_add_to_cart',
			'evotx_standalone_form'=>'evotx_standalone_form',
			'evotx_my_account_ticket'=>'evotx_my_account_ticket',
			'evotx_inquire_before_buy_form'=>'evotx_inquire_before_buy_form',
			'evotx_ajax_06'=>'submit_inquiry',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}

		$this->help = new evo_helper();
		$this->postdata = $this->help->sanitize_array( $_POST );

		add_action('evo_ajax_evotx_inquire_before_buy_form', array($this, 'evotx_inquire_before_buy_form'));
	}

	// Add event ticket to cart custom AJAX
	// @since 1.6.7 @U 1.7.2
		function evotx_add_to_cart(){			
			
			$data = $this->postdata;
			extract($data);

			unset($data['action']);			

			$event_id = $event_data['eid'];
			$wcid = $event_data['wcid'];
			$RI = isset($event_data['ri']) ? $event_data['ri'] : '0';			

			$TICKET = new evotx_event($event_id, '', $RI, $wcid);

			// $data include event_data and other_data
			$adding = $TICKET->add_ticket_to_cart($data);

			echo $adding;
			exit;
		}

	// standalone form
		function evotx_standalone_form(){

			$post_data = $this->help->recursive_sanitize_array_fields( $_POST);

			$event_id = $post_data['eid'];

			$EVENT = new EVO_Event( $event_id);

			$helpers = array(
				'evOPT'=> EVOTX()->frontend->opt1,
				'evoOPT2'=> EVOTX()->opt2,
				'end_row_class'=>null,
				'end'=>null,
			);

			$object = (object)array(
				'repeat_interval'=> (int)$post_data['ri'],
				'event_id'=> $event_id,
				'epmv'=> $EVENT->get_data()
			);


			$html =  EVOTX()->frontend->frontend_box($object, $helpers, $EVENT);
			$return_content = array(
				'status'=>'good',
				'content'=>$html,
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// my account ticket view
		public function evotx_my_account_ticket(){
			$post_data = $this->help->recursive_sanitize_array_fields( $_POST);
			$ticket_number = $post_data['tn'];

			if( function_exists( 'EVOQR' ) ){
				$ticket_number = EVOQR()->checkin->decrypt_ticket_number( $ticket_number );
			}

			
			$TIX = new EVO_Evo_Tix_CPT( $ticket_number );

			$email_body_arguments = array(
				'orderid'=>$TIX->get_order_id(),
				'tickets'=> array($ticket_number), 
				'customer'=>'Test',
				'email'=>'yes'
			);

			$email = new evotx_email();
			$html = $email->get_ticket_email_body($email_body_arguments);

			$return_content = array(
				'status'=>'good',
				'html'=> $html,
			);
			
			echo json_encode($return_content);		
			exit;

		}

	// for evo-tix post page and from event edit page
		function evoTX_checkin_(){
			
			$post_data = $this->help->recursive_sanitize_array_fields( $_POST);

			$ticketNumber = $post_data['tid'];
			$msg = '';

			// split ticket number
			$tixNum = explode('-', $ticketNumber);
			$OrderComplete = EVOTX()->functions->is_order_complete($tixNum[1]);
			$CheckinLang = EVOTX()->functions->get_statuses_lang(); // get both check status lang

			// order is not complete
			if($OrderComplete){
				$tixID = $tixNum[0];

				$current_status = $post_data['status'];

				$evotx_tix = new evotx_tix();

				$other_status = $evotx_tix->get_other_status($current_status);
				$evotx_tix->change_ticket_number_status($other_status[0], $ticketNumber, $tixID);

				$newTixStaus = $other_status[0];

			}else{
				$msg = 'Order not completed';
				$newTixStaus = $post_data['status'];
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
		function evotx_inquire_before_buy_form(){

			$EVENT = new evotx_event( $this->postdata['event_id'], '', $this->postdata['event_ri']);

			if( !$EVENT->check_yn('_allow_inquire')){
				wp_send_json(array(
					'content'=> __('Inqure form not enabled') , 'status'=> 'good'
				));
			};

			ob_start();

			include_once('html-ticket-inquery.php');

			$content = ob_get_clean();

			wp_send_json(array(
				'content'=> $content, 'status'=> 'good'
			));wp_die();
		}
		function submit_inquiry(){

			$EVENT = new evotx_event( $this->postdata['event_id'], '', $this->postdata['ri']);
			
			// verify nonce
				if(! wp_verify_nonce( $this->postdata['evotx_inqure_nonce'] , 'evotx_inqure_form')){
					wp_send_json(array(
						'content'=>'Nonce Verification Failed',
						'status'=>'bad'
					)); wp_die();
				}
			
			// get email address
				$_to_mail = $EVENT->get_prop('_tx_inq_email');
				if( !$_to_mail ){
					$email = EVO()->cal->get_prop('evotx_tix_inquiries_def_email','evcal_tx');
					if( !$email ) $email = get_option('admin_email');

					$_to_mail = $email;
				}

			// get subject
				$subject = $EVENT->get_prop('_tx_inq_subject');
				if( !$subject ){
					$sub = EVO()->cal->get_prop('evotx_tix_inquiries_def_subject','evcal_tx');
					if( !$sub ) $sub = 'New Ticket Sale Inquery';

					$subject = $sub;
				}

			$from_email = $this->postdata['email'];
			$headers = 'From: '.$from_email;	


			$helper = new evo_helper();

			// email body
				ob_start();
				?>
					<div style='padding:20px;color:#777777'>
					<p><?php evo_lang_e('Event');?>: <b><?php echo $EVENT->get_title(); ?></b></p>
					<p><?php evo_lang_e('From');?>: <b><?php echo $this->postdata['name'].' ('. $from_email .')';?></b></p>
					<p><?php evo_lang_e('Message');?>: <br/><?php echo $this->postdata['message'];?></p>
				<?php
					// Other data collected from the form
					foreach(EVOTX()->frontend->inqure_form_fields() as $key=>$val){
						if(in_array($key, array('name','email','message'))) continue;
						if(empty($this->postdata[$key])) continue;

						echo "<p>".$val[1].": <br/>".$this->postdata[$key] . "</p>";
					}
				?>
					</div>
				<?php
				$body = ob_get_clean();

			// SENDING EMAIL
				$email_body = $helper->get_email_body_content($body);
				$send_email = $helper->send_email(array(
					'to'=> $_to_mail,
					'from'=>$from_email,
					'subject'=>$subject,
					'html'=>'yes',
					'message'=> $email_body,
					'reply-to'=> $from_email
				));

			ob_start();
			?>
				<div class='evotxINQ_msg_in'>
					<em></em>
					<span><?php echo eventon_get_custom_language('', 'evoTX_inq_08','GOT IT! -- We will get back to you as soon as we can.');?></span>
				</div>
			<?php 
			$content = ob_get_clean();

			wp_send_json(array(
				'content'=> $content,
				'status'=>'good'
			)); wp_die();
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
	       
	       	wp_send_json($data);
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
		
			wp_send_json( array(
				'key'=>$cart_item_key,
				'variation'=>WC()->cart->cart_contents_total
			)); wp_die();
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