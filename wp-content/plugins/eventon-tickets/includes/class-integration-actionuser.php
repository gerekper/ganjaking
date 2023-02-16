<?php
/**
 * Intergration with ActionUser Addon
 * @version 1.7.7
 * @actionuser_version 2.0.10
 */
class evotx_actionuser{
	public function __construct(){

		// stop doing anything if actionUser is not there
		if(!class_exists('eventon_au')) return false;

		add_filter('evoau_form_fields', array($this, 'fields_to_form'), 10, 1);

		// only for frontend
		// actionUser intergration
		add_action('evoau_frontform_evotx', array($this, 'fields'), 10, 7);	

		add_action('evoau_save_formfields', array($this, 'save_values'), 10, 3);
		add_action('evoau_frontend_scripts_enqueue', array($this, 'enqueue_scripts'), 10);

		// event manager
		add_action('evoau_manager_row_title', array($this, 'event_manager_row_title'), 10, 1);
		add_action('evoau_manager_row', array($this, 'event_manager_row'), 10, 1);
		add_action('evoauem_custom_action', array($this, 'event_manager_show_data'), 10, 1);
		add_filter('evoau_event_manager_delete_btn', array($this, 'event_manager_delete_btn'),10,2);

		// ajax filters
		add_action( 'wp_ajax_evotx_ajax_get_auem_stats', array( $this, 'evors_ajax_get_auem_stats' ) );
		add_action( 'wp_ajax_nopriv_evotx_ajax_get_auem_stats', array( $this, 'evors_ajax_get_auem_stats' ) );

		// user capability
		add_filter('eventon_core_capabilities',array($this, 'capability'),10, 1);

		// only admin fields
		if(is_admin()){
			add_filter('eventonau_language_fields', array($this, 'language'), 10, 1);
		}
	}

	// capabilities support for AU
		function capability($array){
			//$array[] = '';
			return $array;
		}
	// include ticket script
		function enqueue_scripts(){
			wp_enqueue_script('tx_wc_tickets');
		}

	// include fields to submission form array
		function fields_to_form($array){
			$array['evotx']=array('Ticket Fields', 'evotx_tix', 'evotx','custom','');
			return $array;
		}

	// Frontend showing fields and saving values  
		function fields($field, $event_id, $default_val, $EPMV, $opt2, $lang, $auform){

			$form = $auform;

			$evotx_tix = ($EPMV && !empty($EPMV['evotx_tix']) && $EPMV['evotx_tix'][0]=='yes')? true: false;


			echo $auform->get_form_html(
				'evotx_tix',
				array(
					'type'=>'yesno',
					'yesno_args'=>array(
						'id'=>'evotx_tix',
						'input'=>true,
						'label'=>evo_lang('Sell tickets for this event'),
						'var'=> ($auform->EVENT && $auform->EVENT->check_yn('evotx_tix')?'yes':'no'),
						'afterstatement'=>'evotx_data_section',
						'lang'=>$lang,
					)
				)
			);

			// for editting
				$_regular_price = $_sale_price = $_stock = $_sku = $product = $woometa = '';
				$wc_ticket_product_id = !empty($EPMV['tx_woocommerce_product_id'])? $EPMV['tx_woocommerce_product_id'][0]: false;
				if($wc_ticket_product_id){
					$woometa = get_post_custom($wc_ticket_product_id);

					$product = wc_get_product($wc_ticket_product_id);
					
					if(!empty($woometa['_regular_price']) )	$_regular_price = $woometa['_regular_price'][0];
					if(!empty($woometa['_sale_price']) )	$_sale_price = $woometa['_sale_price'][0];
					if(!empty($woometa['_stock']) )	$_stock = $woometa['_stock'][0];
					if(!empty($woometa['_sku']) )	$_sku = $woometa['_sku'][0];
				}

				$au_tx_fields_array = array();

				// non simple item notice
				if( ($product && !$product->is_type('simple'))){
					$au_tx_fields_array['non_simple_notice']= array(
						'content'=>evo_lang('This is a non-simple WC Ticket, must contact admin to make further edits!')
					);
				}
				$au_tx_fields_array['tx_product_type']=array(
					'type'=>'hidden',
					'value'=>'simple',
					'form_type'=>'new',						
				);
				$au_tx_fields_array['visibility']=array(
					'type'=>'hidden',
					'value'=>'visible',
					'form_type'=>'new',						
				);
				$au_tx_fields_array['tx_woocommerce_product_id']=array(
					'type'=>'hidden',
					'value'=>$wc_ticket_product_id,
					'form_type'=>'edit',						
				);
				$au_tx_fields_array['tx_woocommerce_product_id'] = array(
					'type'=>'hidden',
					'value'=>$wc_ticket_product_id,
					'form_type'=>'edit',						
				);
				$au_tx_fields_array['_regular_price'] = array(
					'type'=>	'text',
					'name'=>	evo_lang('Ticket Price',$lang, $opt2),
					'value'=>	evo_var_val($woometa, '_regular_price'),
					'required_html'=> 	' *',
					'required_class'=>	' req',
					'req_dep'=>	array('name'=>'evotx_tix','value'=>'yes')
				);
				$au_tx_fields_array['_sale_price']=array(
					'type'=>	'text',
					'name'=>	evo_lang('Ticket Sales Price',$lang, $opt2),
					'value'=>	evo_var_val($woometa, '_sale_price'),
				);
				$au_tx_fields_array['_sold_individually']=array(
					'type'=>	'yesno',
					'yesno_args'=> array(
						'id'=>'_sold_individually',
						'input'=>true,
						'label'=>evo_lang('Sold Individually'),
						'var'=> (evo_check_yn($woometa, '_sold_individually')?'yes':'no'),
						'lang'=>$lang,
						'guide'=> evo_lang('Enable this to only allow one ticket per person')
					)
				);
				$au_tx_fields_array['_sku'] = array(
					'type'=>	'text',
					'name'=>	evo_lang('SKU',$lang, $opt2),
					'value'=>	evo_var_val($woometa, '_sku'),
					'tooltip'=>	evo_lang('SKU refers to a Stock-keeping unit, a unique identifier for each distinct menu item that can be ordered. You must enter a SKU or else the tickets might not function correct.')
				);

				$au_tx_fields_array['_stock']= array(
					'type'=>	'text',
					'name'=>	evo_lang('Ticket Stock Capacity'),
					'value'=>	evo_var_val($woometa, '_stock'),
				);
				$au_tx_fields_array['_show_remain_tix']=array(
					'type'=>	'yesno',
					'yesno_args'=> array(
						'id'=>'_show_remain_tix',
						'input'=>true,
						'label'=>evo_lang('Show remaining tickets'),
						'var'=> (evo_check_yn($EPMV, '_show_remain_tix')?'yes':'no'),
						'lang'=>$lang,
						'guide'=> evo_lang('This will show remaining tickets for this event on front-end')
					)
				);
				$au_tx_fields_array['remaining_count']= array(
					'type'=>	'text',
					'name'=>	evo_lang('Show remaining count at',$lang, $opt2),
					'value'=>	evo_var_val($EPMV, 'remaining_count'),
					'tooltip'=>	evo_lang('Show remaining count when remaining count go below this number.')
				);
				

			
			echo "<div id='evotx_data_section' class='row evoau_sub_formfield' style='display:".($evotx_tix?'':'none')."'>";


			// print all fields using actionUser function
			foreach($au_tx_fields_array as $field=>$data){
				echo $form->get_form_html($field, $data);
			}

			echo EVO()->elements->process_multiple_elements(
				array(	
				array(
					'type'=>'yesno',
					'name'=>evo_lang('Show guest list for event on eventCard'),
					'id'=> '_tx_show_guest_list',
					'value'=> ($auform->EVENT && $auform->EVENT->check_yn('_tx_show_guest_list')?'yes':'no'),
					'nesting'=>'row',					
				),

				array(
					'type'=>'yesno',
					'name'=>evo_lang('Allow customers to submit inquiries.'),
					'tooltip'=>'With this customers can submit inquiries via this form before buying tickets on front-end.',
					'id'=> '_allow_inquire',
					'value'=> ($auform->EVENT && $auform->EVENT->check_yn('_allow_inquire')?'yes':'no'),
					'nesting'=>'row',
					'afterstatement'=>'_allow_inquire__',				
				),
				array(
					'type'=>'begin_afterstatement',
					'id'=> '_allow_inquire__',
					'value'=> ($auform->EVENT && $auform->EVENT->check_yn('_allow_inquire')?'yes':'no'),				
				),
				array(
					'type'=>'text',
					'name'=>evo_lang('Email Address to receive Inquiries'),
					'id'=> '_tx_inq_email',
					'value'=> ($auform->EVENT ? $auform->EVENT->get_prop('_tx_inq_email') :''),
					'nesting'=>'row',		
				),array(
					'type'=>'text',
					'name'=>evo_lang('Subject for Inquiries Email'),
					'id'=> '_tx_inq_subject',
					'value'=> ($auform->EVENT ? $auform->EVENT->get_prop('_tx_inq_subject') :''),
					'nesting'=>'row',		
				),

				array(
					'type'=>'end_afterstatement',								
				),
			));

			echo "</div>";
		}

		// save form submission values
		function save_values($field, $fn, $event_id){
			if( $field =='evotx'){					
				if(!empty($_POST['evotx_tix']) && $_POST['evotx_tix']=='yes'){

					// adjust $_POST array
						if(!empty($_POST['_stock']))	$_POST['_manage_stock'] = 'yes';

					update_post_meta($event_id, 'evotx_tix', $_POST['evotx_tix']);
					
					// Update Event
					foreach(array(
						'_show_remain_tix',
						'_tx_show_guest_list',
						'remaining_count',
						'_allow_inquire',
						'_tx_inq_email',
						'_tx_inq_subject'
					) as $field){
						if(empty($_POST[$field])) continue;

						update_post_meta($event_id, $field, $_POST[$field]);
					}

					// Update associated WC object
					if( !empty($_POST['tx_woocommerce_product_id'])){
						$post_exists = $this->post_exist($_POST['tx_woocommerce_product_id']);

						if($post_exists){
							global $evotx_admin;

							EVOTX()->functions->save_product_meta_values($_POST['tx_woocommerce_product_id'], $event_id);
						}else{
							EVOTX()->functions->add_new_woocommerce_product($event_id);		
						}
					}else{ // add new 
						EVOTX()->functions->add_new_woocommerce_product($event_id);		
					}
				}
			}
		}

		function post_exist($ID){
			global $wpdb;

			$post_id = $ID;
			$post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $post_id . "'", 'ARRAY_A');
			return $post_exists;
		}
	
	// event manager additions
		function event_manager_row_title($EVENT){
			$wc_ticket_product_id = $EVENT->get_prop('tx_woocommerce_product_id');
			
			if($EVENT->check_yn('evotx_tix') && $wc_ticket_product_id){
				echo "<tags style='background-color:#8BDBEC'>".evo_lang('Ticket Sales On')."</tags>";
			}
		}
		function event_manager_row($EVENT){
			$wc_ticket_product_id = $EVENT->get_prop('tx_woocommerce_product_id');

			if($EVENT->check_yn('evotx_tix') && $wc_ticket_product_id){
				echo "<a class='evoauem_additional_buttons load_tix_stats' data-eid='{$EVENT->ID}' data-ri='{$EVENT->ri}'>".evo_lang('View Ticket Stats')."</a>";
			}
		}

		function evors_ajax_get_auem_stats(){
			$html = $this->event_manager_show_data($_POST['eid']);
			echo json_encode(array(
				'status'=>'good',
				'html'=>$html
			));exit;
		}

		function event_manager_show_data($event_id){

			ob_start();
			$EPMV = get_post_custom($event_id);
			$wc_ticket_product_id = !empty($EPMV['tx_woocommerce_product_id'])? $EPMV['tx_woocommerce_product_id'][0]: false;

			if(!$wc_ticket_product_id) return;
			global $evotx;

			$woometa = get_post_custom($wc_ticket_product_id);
			$product_type = EVOTX()->functions->get_product_type($wc_ticket_product_id);
			$__woo_currencySYM = get_woocommerce_currency_symbol();

			$evotx_opt = get_option('evcal_options_evcal_tx');


			?>
			<div id='' class='evoau_manager_continer' style='padding:15px;'>
				<h3 class="evoauem_section_subtitle" style='margin-bottom: 10px;'><?php evo_lang_e('Event');?>: <b><?php echo get_the_title($event_id);?></b> <i class='evoau_information_bubble' style=''><?php evo_lang_e('Event Ticket Information & Stats');?></i></h3>


				<div id="" class='evoau_tile' style=''>
					<h4 style='margin:0'><?php evo_lang_e('Ticket Information');?></h4>

					<?php $_sty_1 = 'padding:0 0 5px;margin:0';?>
					<div style=''>
						<p style='<?php echo $_sty_1;?>'><b><?php evo_lang_e('Price');?>:</b>
							<span><?php
							if($product_type=='variable'){
								echo $__woo_currencySYM . ' '. evo_meta($woometa, '_min_variation_price') .' - '.evo_meta($woometa, '_max_variation_price');
							}else{
								echo $__woo_currencySYM . ' '. evo_meta($woometa, '_regular_price');
							}
							?></span>
						<?php 
						// ticket stock status
						if(evo_check_yn($woometa,'_manage_stock')):?>
							<?php
								if($product_type == 'simple'):
									$tix_inStock = EVOTX()->functions->event_has_tickets($EPMV, $woometa, 0);
							?>
								<p><b><?php evo_lang_e('Tickets in stock');?></b>
									<span><?php echo  evo_lang($tix_inStock);?></span></p>
							<?php endif;?>
						<?php endif;?>
						</p>
						<p style='<?php echo $_sty_1;?>'><b><?php evo_lang_e('Ticket Type');?>:</b> <i><?php echo $product_type;?></i></p>
						<p style='<?php echo $_sty_1;?>'><b><?php evo_lang_e('SKU');?>:</b> <i><?php echo evo_meta($woometa, '_sku');?></i></p>
						<?php if(evo_check_yn($woometa,'_manage_stock')):?>
							<p style='<?php echo $_sty_1;?>'><b><?php evo_lang_e('Stock Status');?>:</b> <i><?php echo evo_lang(evo_meta($woometa, '_stock_status'));?></i></p>
						<?php endif;?>
					</div>
				</div>

				<div id="" class='evoau_tile' style=''>
					<h4 style='margin:0'><?php evo_lang_e('Attendees');?></h4>
					<?php 							
						$EA = new EVOTX_Attendees();
						$TH = $EA->get_tickets_for_event($event_id);

						// can user check guests for event tickets
							$_can_check = false;

							// if allow event creator to checkin guests enabled via tickets settings
							if(evo_settings_check_yn($evotx_opt, 'evotx_checkin_guests')) $_can_check = true; 

							// can user edit event via AU function
							$_au_can_user_edit_event = ( EVOAU()->frontend->functions->can_currentuser_edit_event($event_id, $EPMV) );

							// override actionUser permission
							if( !$_au_can_user_edit_event ) $_can_check = false;

							// if admin of the site override all and allow
							if($EA->_user_can_check()) $_can_check = true;

						if($TH && count($TH)>0){
							echo "<div class='event_tix_attendee_list'>";

							foreach($TH as $tn=>$td){

								echo $EA->__display_one_ticket_data($tn, $td, array(
									'showStatus'=> $_can_check,
									'showOrderStatus'=>true,
									'guestsCheckable'=>$_can_check,
									'show_signin'=> true,
								));
							}

							echo "</div>";
						}

					?>
				</div>
			</div>
			<?php

			return ob_get_clean();
		}

		public function event_manager_delete_btn($html, $EVENT){

			if(empty($html)) return $html;
			
			// get tickets sold
			$wc_ticket_product_id = $EVENT->get_prop('tx_woocommerce_product_id');

			if($wc_ticket_product_id){
				$sold = get_post_meta( $wc_ticket_product_id, 'total_sales',true);
				
				if($sold>0) return "<a class='fa fa-trash deleteEvent disa' data-s='disable' title='". evo_lang('Cannot delete because there are sales on this event') ."'></a>";
			}

			return $html;
		}

	// language
		function language($array){
			$newarray = array(
				array('label'=>'Ticket Fields','type'=>'subheader'),
					array('label'=>'Ticket Sales On','var'=>'1'),		
					array('label'=>'View Ticket Stats','var'=>'1'),		
					array('label'=>'Sell tickets for this event','var'=>'1'),		
					array('label'=>'Ticket Price','var'=>'1'),		
					array('label'=>'Ticket Sales Price','var'=>'1'),		
					array('label'=>'Enable this to only allow one ticket per person','var'=>1),		
					array('label'=>'Sold Individually','var'=>1),		
					array('label'=>'SKU','var'=>'1'),		
					array('label'=>'Ticket Stock Capacity','var'=>'1'),		
					array('label'=>'Event Ticket Information & Stats','var'=>'1'),				
					array('label'=>'Price','var'=>'1'),				
					array('label'=>'Tickets in stock','var'=>'1'),				
					array('label'=>'Tickets Type','var'=>'1'),	
					array('label'=>'Stock Status','var'=>'1'),				
					array('label'=>'Show remaining tickets','var'=>'1'),				
					array('label'=>'This will show remaining tickets for this event on front-end','var'=>'1'),				
					array('label'=>'Show remaining count at','var'=>'1'),				
					array('label'=>'Show remaining count when remaining count go below this number.','var'=>'1'),				
					array('label'=>'Show guest list for event on eventCard','var'=>'1'),				
					array('label'=>'Attendees','var'=>'1'),				
					array('label'=>'Confirmed Attendance','var'=>'1'),				
					array('label'=>'This is a non-simple WC Ticket, must contact admin to make further edits!','var'=>'1'),				
					array('label'=>'Cannot delete because there are sales on this event','var'=>'1'),				
				array('type'=>'togend'),
			);
			return array_merge($array, $newarray);
		}
}
new evotx_actionuser();