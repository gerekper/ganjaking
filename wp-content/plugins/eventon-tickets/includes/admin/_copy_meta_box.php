<?php
/**
 * Ticket meta box for events
 */

class Evo_Tx_Meta_Boxes{
	public function __construct(){
		// ajde_events
		add_action( 'add_meta_boxes', array($this, 'meta_boxes') );
		add_action('eventon_save_meta',  array($this, 'save_eventticket_info'), 10, 2);
		add_filter('evo_event_columns', array($this, 'add_column_title'), 10, 1);
		add_filter('evo_column_type_woo', array($this, 'column_content'), 10, 1);

		// evo-tix
		add_filter( 'request', array($this,'ticket_order') );
		add_filter( 'manage_edit-evo-tix_sortable_columns', array($this,'ticket_sort') );
		add_action('manage_evo-tix_posts_custom_column', array($this,'evo_tx_custom_event_columns'), 2 );
		add_filter( 'manage_edit-evo-tix_columns', array($this,'evo_tx_edit_event_columns') );
			add_action("admin_init", array($this,"_evo_tx_remove_box"));
	}

	public function meta_boxes(){
		global $post, $pagenow;
		add_meta_box('evotx_mb1', __('Event Tickets','evotx'), array($this,'evotx_metabox_content'),'ajde_events', 'normal', 'high');

		add_meta_box('evo_mb1',__('Event Ticket','evotx'), array($this,'evotx_metabox_002'),'evo-tix', 'normal', 'high');
	}

// Event Tickets
	// CONTENT
	public function evotx_metabox_content(){
		global $post, $evotx, $eventon, $ajde;
		$woometa='';

		$event_id = $post->ID;

		$helper = new evo_helper();
		$EVENT = new Evo_Event_Ticket($post->ID);
		$fmeta = $EVENT->get_data();
		
		$ticket_admin = EVO()->tickets->admin;		
		
		$woo_product_id = $EVENT->get_wcid();

		// if the wc product exists
			$the_product = '';
			$__woo_currencySYM = get_woocommerce_currency_symbol();

			if($woo_product_id){
				
				if( !$helper->post_exist($woo_product_id) ) $woo_product_id = null;

				$woometa =  get_post_custom($woo_product_id);
				$the_product = wc_get_product($woo_product_id);
			}

		ob_start();

		$evotx_tix = $EVENT->check_yn('evotx_tix');
		
		
		?>
		<div class='eventon_mb' data-eid='<?php echo $event_id;?>'>
		<div class="evotx">
			<input type='hidden' name='tx_woocommerce_product_id' value="<?php echo $woo_product_id;?>"/>
			<p class='yesno_leg_line ' style='padding:10px'>
				<?php echo eventon_html_yesnobtn(array('id'=>'evotx_activate','var'=> ($evotx_tix?'yes':'no'), 
					'attr'=>array('afterstatement'=>'evotx_details'))); ?>				
				<input type='hidden' name='evotx_tix' value="<?php echo ($evotx_tix)?'yes':'no';?>"/>
				<label for='evotx_tix'><?php _e('Activate tickets for this Event','evotx'); echo $eventon->throw_guide('You can allow ticket selling via Woocommerce for this event in here.','',false); ?></label>
			</p>
			<div id='evotx_details' class='evotx_details evomb_body ' <?php echo ( $evotx_tix)? null:'style="display:none"'; ?>>
				<?php
					$product_type = 'simple';

					// product type
					$product_type = $ticket_admin->get_product_type($woo_product_id);
					$product_type = (!empty($product_type))? $product_type: 'simple';
				?>
				
				<div class="evotx_tickets " style=''>

					<div class='evo_meta_elements'>
					<?php

					echo EVO()->elements->process_multiple_elements(
						array(
							array(
								'type'=>'notice',
								'name'=> __('Ticket Pricing Type','eventon') . ': <b>'. $product_type .'</b>',
							),
							array(
								'type'=>'hidden',
								'id'=>'tx_product_type','value'=> $product_type
							)
						)
					);

					// variable product
					if(!empty($product_type) && !empty($the_product) && $product_type=='variable'):
						echo EVO()->elements->get_element(
							array(
								'type'=>'notice',
								'name'=> __(sprintf('Ticket price (%s)', $__woo_currencySYM ),'eventon') . ': '. $the_product->get_price_html() ? $the_product->get_price_html() : '<span class="na">&ndash;</span>' . "<a href='". get_edit_post_link($woo_product_id) ."
								' style='color:#fff'>".__('Edit Price Variations')."</a>",
							)
						);

					else:
						echo EVO()->elements->process_multiple_elements(
							array(
								array(
									'type'=>'text',
									'name'=> __(sprintf('Ticket price (%s) (Required*)', $__woo_currencySYM ),'eventon'),
									'tooltip'=>'Ticket price is required for tickets product to add to cart otherwise it will return an undefined error.',
									'id'=>'_regular_price',
									'value'=> $EVENT->get_wc_prop('_regular_price')
								),
								array(
									'type'=>'text',
									'name'=> __(sprintf('Sale price (%s)', $__woo_currencySYM ),'eventon'),
									'id'=>'_sale_price',
									'value'=> $EVENT->get_wc_prop('_sale_price')
								)
							)
						);
					endif;

					$_stock_status = $EVENT->get_wc_prop('_stock_status','instock');
					$_stock_status_yesno = ( $_stock_status=='outofstock')? 'yes':'no';

					echo EVO()->elements->process_multiple_elements(
						array(
							array(
								'type'=>'text',
								'name'=> __('Ticket SKU','eventon').' '.__('(Required*)', 'evotx'),
								'tooltip'=>'SKU refers to a Stock-keeping unit, a unique identifier for each distinct menu item that can be ordered. You must enter a SKU or else the tickets might not function correct.',
								'id'=>'_sku',
								'value'=> $EVENT->get_wc_prop('_sku')
							),
							array(
								'type'=>'text',
								'name'=> __('Short Ticket Detail','eventon'),
								'id'=>'_tx_desc',
								'value'=> $EVENT->get_wc_prop('_tx_desc')
							),
							array(
								'type'=>'yesno_btn',
								'label'=> __('Manage Ticket Stock','eventon'),
								'id'=>'_manage_stock',
								'value'=> $EVENT->get_wc_prop('_manage_stock'),
								'afterstatement'=>'exotc_cap'
							),
							array(
								'type'=>'begin_afterstatement',
								'id'=>'exotc_cap',
								'value'=> $EVENT->get_wc_prop('_manage_stock')
							),
								array(
									'type'=>'text',
									'name'=> __('Total Tickets in Stock','eventon'),
									'tooltip'=> __('This is how many tickets you have currently in stock','eventon'),
									'id'=>'_stock',
									'value'=> $EVENT->get_wc_prop('_stock')
								),
							array(
								'type'=>'end_afterstatement',
							),

							array(
								'type'=>'yesno_btn',
								'label'=> __('Place ticket on out of stock','eventon'),
								'tooltip'=>'Set stock status of tickets. Setting this to yes would make tickets not available for sale anymore. This will also add sold out tag into event top, if not disabled in eventon settings.',
								'id'=>'_stock_status',
								'value'=> $_stock_status_yesno,
							),
							array(
								'type'=>'yesno_btn',
								'label'=> __('Sold Individually','eventon'),
								'tooltip'=>'Enable this to only allow one ticket per person',
								'id'=>'_sold_individually',
								'value'=> $EVENT->get_wc_prop('_sold_individually')
							),

							array(
								'type'=>'textarea',
								'name'=> __('Ticket Section Subtitle','eventon'),
								'tooltip'=>'This text will appear right under the ticket section title in eventcard',
								'id'=>'_tx_text',
								'value'=> $EVENT->get_wc_prop('_tx_text')
							),
							array(
								'type'=>'textarea',
								'name'=> __('Ticket Field description','eventon'),
								'tooltip'=>'Use this to type instruction text that will appear above add to cart section on calendar.',
								'id'=>'_tx_subtiltle_text',
								'value'=> $EVENT->get_wc_prop('_tx_subtiltle_text')
							),
						)
					);


					?>
					</div>



					
					
					<table class='eventon_settings_table' width='100%' border='0' cellspacing='0'>
																		
						<?php 

						// promote variations and options addon 

						if( $product_type != 'simple' && class_exists('evovo')){
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
							do_action('evotx_event_metabox_end', $event_id, $fmeta,  $woo_product_id, $product_type, $EVENT);
						?>	
					</table>
	
				</div>	
				
				<?php 					

					// Lightboxes
					global $ajde;
					
					echo $ajde->wp_admin->lightbox_content(array(
						'class'=>'evotx_lightbox_def', 
						'content'=> "<p class='evo_lightbox_loading'></p>",
						'title'=>__('Ticket','evotx'), 
						'max_height'=>500 
					));
					echo $ajde->wp_admin->lightbox_content(array(
						'class'=>'evotx_lightbox', 
						'content'=> "<p class='evo_lightbox_loading'></p>", 
						'title'=>__('View Attendee List','evotx'), 
						'type'=>'padded', 
						'max_height'=>500 
					));


					// DOWNLOAD CSV link 
						$exportURL = add_query_arg(array(
						    'action' => 'the_ajax_evotx_a3',
						    'e_id' => $post->ID,
						    'pid'=> $woo_product_id
						), admin_url('admin-ajax.php'));
				?>

				<!-- Attendee section -->
					<div class='evoTX_metabox_attendee_other' style='background-color: #e4e4e4; border-radius: 8px;padding: 5px 10px; margin: 8px 0;'>

						<p><?php _e('Other Ticket Actions','eventon');?></p>
						<p class="actions">
							<?php if($woo_product_id):?><a class='button_evo edit' href='<?php echo get_edit_post_link($woo_product_id);?>'  title='<?php _e('Further Edit ticket product from woocommerce product page','evotx');?>'> <?php _e('Further Edit','evotx');?></a><?php endif;?>
							 <a id='evotx_attendees' data-eid='<?php echo $event_id;?>' data-wcid='<?php echo evo_meta($fmeta, 'tx_woocommerce_product_id');?>' data-popc='evotx_lightbox' class='button_evo attendees ajde_popup_trig' title='<?php _e('View Attendees','evotx');?>'><?php _e('View Attendees','evotx');?></a>
							
						</p>

						<?php do_action('evo_ticket_edit_actions_end');?>

					</div>
			</div>			
		</div>
		</div>

		<?php
		echo ob_get_clean();
	}

	// SAVE
	function save_eventticket_info($arr, $post_id){			

		global $evotx_admin, $evotx;

		// if allowing woocommerce ticketing
		if(!empty($_POST['evotx_tix']) && $_POST['evotx_tix']=='yes'){
			// check if woocommerce product id exist
			if(isset($_POST['tx_woocommerce_product_id'])){

				$HELP = new evo_helper();
				$EVENT = new Evo_Event_Ticket( $post_id);

				$post_exists = $HELP->post_exist($_POST['tx_woocommerce_product_id']);

				// make sure woocommerce stock management is turned on
					update_option('woocommerce_manage_stock','yes');
									
				// add new
				if(!$post_exists){
					$wcid = $EVENT->add_new_woocommerce_product($post_id);
				}else{
					$wcid = (int)$_POST['tx_woocommerce_product_id'];
					$EVENT->update_woocommerce_product($wcid, $post_id);
				}	

				$EVENT->save_stock_status($wcid);
				
			// if there isnt a woo product associated to this - add new one
			}else{
				$EVENT->add_new_woocommerce_product($post_id);
			}
		}

		foreach(apply_filters('evotx_save_eventedit_page', array(
			'_tx_img_text',
			'evotx_tix', 
			'_show_remain_tix', 
			'remaining_count', 
			'_manage_repeat_cap', 
			'_tix_image_id', 
			'_allow_inquire',
			'_tx_inq_email',
			'_tx_inq_subject',
			'_xmin_stopsell',
			'_tx_show_guest_list',
			'_tx_add_info',
			'_evotx_show_next_avai_event',
			'_already_purchased',
		)) as $variable){
			if(!empty($_POST[$variable])){
				update_post_meta( $post_id, $variable,$_POST[$variable]);
			}elseif(empty($_POST[$variable])){

				if($variable == '_tix_image_id' && !empty($_POST['evotx_tix']) && $_POST['evotx_tix']=='yes' && !empty($_POST['tx_woocommerce_product_id'])){
					delete_post_thumbnail( (int)$_POST['tx_woocommerce_product_id']);
				}
				delete_post_meta($post_id, $variable);
			}
		}

		// after saving event tickets data
		do_action('evotx_after_saving_ticket_data', $post_id);

		// repeat interval capacities
			if(!empty($_POST['ri_capacity']) && evo_settings_check_yn($_POST, '_manage_repeat_cap')){

				// get total
				$count = 0; 
				foreach($_POST['ri_capacity'] as $cap){
					$count = $count + ( (int)$cap);
				}
				// update product capacity
				update_post_meta( $_POST['tx_woocommerce_product_id'], '_stock',$count);
				update_post_meta( $post_id, 'ri_capacity',$_POST['ri_capacity']);
			}
	}

	// add new column to menu items
		function add_column_title($columns){
			$columns['woo']= '<i title="Connected to woocommerce">'.__('TIX','evotx').'</i>';
			return $columns;
		}
		function column_content($post_id){				
			$evotx_tix = get_post_meta($post_id, 'evotx_tix', true);

			if(!empty($evotx_tix) && $evotx_tix=='yes'){
				global $evotx_admin;

				$__woo = get_post_meta($post_id, 'tx_woocommerce_product_id', true);
				//$__wo_perma = (!empty($__woo))? get_edit_post_link($__woo):null;
				
				
				$product_type = 'simple';
				$product_type = EVO()->tickets->admin->get_product_type($__woo);

				$_stock = "<i title='".__('Tickets are active','evotx')."'><b></b></i>";
				if($product_type == 'simple'){
					$_stockC = (int)get_post_meta($__woo, '_stock',true);
					if($_stockC) $_stock =  "<i title='".__('Tickets in Stock','evotx')."'>". $_stockC."</i>";
				}

				return (!empty($__woo))?
					"<span class='yeswootix' title='".apply_filters('evotx_admin_events_column_title',$product_type, $post_id)."'>".$_stock."</span>":
					"<span class='nowootix'>".__('No','evotx') . "</span>";
			}else{
				return "<span class='nowootix'>".__('No','evotx') . '</span>';
			}
		}

// evo-tix CPT
	// remove the main editor box
		function _evo_tx_remove_box(){
			remove_post_type_support('evo-tix', 'title');
			remove_post_type_support('evo-tix', 'editor');
		}
	// meta boxes
		function evotx_metabox_002(){

		}

	// make ticket columns sortable
		function ticket_sort($columns) {
			$custom = array(
				'event'		=> 'event',
			);
			return wp_parse_args( $custom, $columns );
		}
		function ticket_order( $vars ) {
			if (isset( $vars['orderby'] )) :
				if ( 'event' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> '_eventid',
						'orderby' 	=> 'meta_value'
					) );
				endif;
				
			endif;

			return $vars;
		}

	// Columns
		function evo_tx_edit_event_columns( $existing_columns ) {
			global $eventon;
			
			// GET event type custom names
			
			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) )
				$existing_columns = array();
			if($_GET['post_type']!='evo-tix')
				return;

			unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

			$columns = array();
			$columns["cb"] = "<input type=\"checkbox\" />";	

			$columns['tix'] = __( 'Event Ticket(s)', 'evotx' );
			$columns['tix_status'] = __( 'Status', 'evotx' );
			$columns['tix_wcid'] = __( 'Order ID', 'evotx' );
			
			$columns["tix_event"] = __( 'Event', 'evotx' );
			$columns["tix_type"] = __( 'Ticket Type', 'evotx' );
			$columns["date"] = __( 'Date', 'evotx' );				
			

			return array_merge( $columns, $existing_columns );
		}	

	// Fields		
		function evo_tx_custom_event_columns( $column ) {
			global $post, $eventon, $evotx;

			$meta = get_post_meta($post->ID); // ticket item meta
			
			$ET = new Evo_Tix_CPT();
			$ET->post_id = $post->ID;


			switch ($column) {	
				case 'tix_wcid':
					$wcid = $ET->get_prop('_orderid');
					echo '<a class="row-title" href="'.get_edit_post_link( $wcid ).'">' . $wcid.'</a>';
				break;
				case "tix":
					// new method 1.7
					if( $ET->get_prop('_ticket_number') ){

						echo "<strong><a class='row-title' href='". get_edit_post_link( $post->ID ) ."'>#".$ET->get_prop('_ticket_number')."</a></strong> ".$ET->get_prop('email');
						echo "</span>";
					}else{
						$edit_link = get_edit_post_link( $post->ID );
						$cost = $ET->get_prop('cost');

						echo "<strong><a class='row-title' href='".$edit_link."'>#{$post->ID}</a></strong> by ".$meta['name'][0]." ".$meta['email'][0];

						// get ticket ids
						$tix_id_ar = $ET->get_ticket_numbers_by_evotix($post->ID, 'string');

						echo '<br/><em class="lite">Ticket ID(s):</em> <i>'.$tix_id_ar.'</i>';

						echo '<br/><span class="evotx_intrim">'. $ET->get_prop('qty') .' <em class="lite">(Qty)</em> - '. ((!empty($cost))? get_woocommerce_currency_symbol().apply_filters('woocommerce_get_price', $cost): '-').'<em class="lite"> (Total)</em></span>';
					}
					
				break;
				case "tix_event":
					$e_id = (!empty($meta['_eventid']))? $meta['_eventid'][0]: null;

					if($e_id){
						echo '<strong><a class="row-title" href="'.get_edit_post_link( $e_id ).'">' . get_the_title($e_id).'</a></strong>';
					}else{ echo '--';}

				break;
				case "tix_type":
					$type = get_post_meta($post->ID, 'type', true);						
					echo (!empty($type))? $type: '-';
				break;
				
				case "tix_status":
					// order
						$order_id = $ET->get_prop('_orderid');
						$order_status = 'n/a';	
						$_o_status = get_post_status($order_id);						
						if($order_id && $_o_status){	
							$order = new WC_Order( $order_id );
							$order_status = $order->get_status();
						}
				
					echo "<p class='evotx_status_list {$order_status}'><em class='lite'>".__('Order','evotx').":</em> <span class='evotx_wcorderstatus {$order_status}'>".$order_status ."</span></p>";

				break;
			}
		}


}
new Evo_Tx_Meta_Boxes();