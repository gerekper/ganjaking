<?php 
/**
 * Admin Ajax
 * @version 1.0.4
 */
class evovo_admin_ajax{
	public $help, $postdata;
	public function __construct(){
		$ajax_events = array(
			'evovo_get_vo_form'=>'get_vo_form',
			'evovo_new_options_form'=>'get_vo_form', // legacy
			'evovo_save_vo_form'=>'evovo_save_vo_data',
			'evovo_save_dataset'=>'evovo_save_vo_data', // legacy
			'evovo_save_neworder'=>'evovo_save_order',
			'evovo_delete_item'=>'delete_item',
			'evovo_get_settings'=>'get_settings',
			'evovo_save_settings'=>'save_settings',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}

		$this->help = new evo_helper();
		$this->postdata = $this->help->process_post( $_POST);
	}

// Settings
	function get_settings(){

		$event_id = $this->postdata['event_id'];
		$wcid = $this->postdata['wcid'];
		$EVENT = new EVO_Event( $event_id );

		$VO = new EVOVO_Var_opts($EVENT, $wcid);

		?>
		<div id='evovo_settings_content' class='evopad20'>

			<?php 
			echo $this->get_vo_items_content( $VO, $EVENT );
			?>
			
			<div class='evopadb20'>
				<?php
				echo $VO->get_vos_action_btn_html( $EVENT->ID, 'event');
				?>
			</div>
			<form class='evovo_settings_form'>
				<?php

				EVO()->elements->print_hidden_inputs( array(
					'eid'=> $event_id,
					'action'=> 'evovo_save_settings'
				));			

				echo EVO()->elements->process_multiple_elements(array(
					array(
						'id'=>		'_evovo_var_sep_sold',
						'type'=>'yesno_btn',
						'value'=>		$EVENT->get_prop('_evovo_var_sep_sold'), 
						'input'=>	true,
						'label'=>	__('Sell Variations as separate ticket (Only when you have single variation type)','evovo'),
						'tooltip'=>	__('This will allow customers to add each variation type to cart as separate tickets, this is only available when there is only single variation type. When this is enabled price options will not display.','evovo')
					),
					array(
						'type'=>'yesno_btn',
						'id'=>		'_evovo_po_sep_sold',
						'value'=>		$EVENT->get_prop('_evovo_po_sep_sold'), 
						'input'=>	true,
						'label'=>	__('Sell Price Options as Separate Tickets','evovo'),
						'tooltip'=>	__('This will enable you to sell price options as separate tickets instead of a single ticket. Variations will be disabled when this is active.','evovo')
					),
					/*
					array(
						'type'=>'yesno_btn',
						'id'=>		'_evovo_po_uncor_qty',
						'value'=>		$EVENT->get_prop('_evovo_po_uncor_qty'), 
						'input'=>	true,
						'label'=>	__('Set Price Options quantity uncorrelated to ticket quantity','evovo'),
						'tooltip'=>	__('Setting this will, make price options quantity not change with the ticket quantity. Not supported with Price Options as separate tickets','evovo')
					),
					*/
					array(
						'type'=>'yesno_btn',
						'id'=>		'_evovo_v_hide_sold',
						'value'=>		$EVENT->get_prop( '_evovo_v_hide_sold'), 
						'input'=>	true,
						'label'=>	__('Hide variations that are out of stock','evovo'),
						'tooltip'=>	__('This will not show variations that are sold out, when page first loads.','evovo')
					),array(
						'type'=>'yesno_btn',
						'id'=>		'_evovo_op_hide_sold',
						'value'=>		$EVENT->get_prop( '_evovo_op_hide_sold'), 
						'input'=>	true,
						'label'=>	__('Hide options that are out of stock','evovo'),
						'tooltip'=>	__('This will not show options that are sold out, when page first loads.','evovo')
					)
				));

			?>
			<p><?php
				// save changes
				EVO()->elements->print_trigger_element(array(
					'title'=>__('Save Changes','evotx'),
					'uid'=>'evovo_save_settings',
					'lb_class' =>'evovo_lightbox',
					'lb_loader'=> true,
					'lb_hide'=> 2000,
				), 'trig_form_submit');
			?></p>
		</form>
		</div>

		<?php

		echo json_encode(array(
			'content'=> ob_get_clean(),			
			'status'=>'good'
		)); exit;
	}

	function get_vo_items_content( $VO , $EVENT){
		ob_start();

		$VO->print_all_vos_container_html( $EVENT->ID, 'event',false );
		
		return ob_get_clean();
	}

	function save_settings(){
		
		$EVENT = new EVO_Event( $this->postdata['eid']);
		
		foreach(array(
			'_evovo_var_sep_sold','_evovo_po_sep_sold','_evovo_v_hide_sold','_evovo_op_hide_sold','_evovo_po_uncor_qty'
		) as $key){
			if( !isset( $this->postdata[ $key ])) continue;
			$EVENT->save_meta( $key, $this->postdata[ $key ] );
		}

		echo json_encode(array(
			'msg'=> __("Settings Successfully Saved",'evovo'),			
			'status'=>'good'
		)); exit;
	}

// GET FORM
	function get_vo_form(){
		ob_start();
		$fnc = new evovo_fnc();

		$PP = $this->postdata;

		// legacy
			$PP['json'] = array(); 
			$PP['json']['type'] = $PP['type'];
		
		extract($PP);

		$curSYM = get_woocommerce_currency_symbol();
		
		$method = $PP['method'];
		$vo_id = !empty($vo_id) ? $vo_id: rand(100000, 900000);;

		$EVENT = new EVO_Event( $event_id );		
		$VO = new EVOVO_Var_opts($EVENT, $wcid );

		
		$values = array();

		// if passed all vo data -> create this vo item data array
			$vo_data = array();
			if( $vo_id 
				&& isset($PP['all_vo_data'])
				&& isset($PP['all_vo_data'][$method] )
				&& isset($PP['all_vo_data'][$method][$vo_id] )
			){
				$vo_data = $PP['all_vo_data'][$method][$vo_id];
			}
		?>

		<div class="evovo_add_block_form evovo_add_options_form evopad20">	
		<form class='evovo_vo_form'>	
			<?php
				EVO()->elements->print_hidden_inputs( array(
					'save'=>isset($PP['save'])? $PP['save']:'yes',
					'event_id'=> $event_id,
					'wcid'=> $wcid,
					'parent_id'	=> $parent_id,
					'vo_id'	=> $vo_id,
					'parent_type'	=> $parent_type,					
					'method'	=> $method,
					'type'	=> !empty($type) ? $type : null,
					'save'	=>  !empty($save) ? $save: null,
					'action'=> 'evovo_save_vo_form'
				));

			echo EVO()->elements->get_element(array(
				'type'=>'notice','row_class'=>'evopadb10',
				'name'=>__("VO Item ID") .': <b>'. $vo_id .'</b>'
			));
			echo EVO()->elements->get_element(array(
				'type'=>'notice','row_class'=>'evopadb10',
				'name'=>__("VO Parent Type") .': <b>'. $parent_type .' (#'. $parent_id.')</b>'
			));
			
			

			$form_go = true;
			

			// if editing load passed on or saved values
			if(( $PP['type'] == 'temp' || $PP['type'] == 'edit')  && $vo_id ){
				$VO->set_new_method( $PP['method'] );
				$VO->set_item_data($vo_id , $vo_data);
				$values = $VO->item_data;
			}

			switch($PP['method']){
				case 'variation':							
				
					// fields
						$VO->set_new_method('variation_type');

						$fields = array();
						$variation_options = false;

						if( $VO->is_set()){
							$variation_options = true;
							foreach($VO->dataset as $index=>$data){
								$options = $data['options'];
								$options = str_replace(', ', ',', $options);
								$options = str_replace(' ,', ',', $options);
								$options = explode(',', $options);
								array_unshift($options , __('All','evovo'));

								$opt_ = array();
								foreach($options as $opt){
									$opt_[ str_replace(' ', '-', $opt)] = $opt;
								}

								$fields[ 'variations['.$index.']']= array(
									'label'=> $data['name'],
									'type'=>'select',
									'options'=> $opt_,
									'value'=> (isset($values['variations'][$index])? 
											$values['variations'][$index]:'')
								);
							}
						}

						// if there are no variation types 
						if(empty($variation_options)){
							$form_go = false;
							echo "<p>".__('You must add variation types first!','evovo')."</p>";
						}else{

							$fields['regular_price'] =array(
								'label'=> sprintf(__('Ticket Variation Price  (%s)','evovo'), $curSYM),
								'req'=>true, 'type'=>'text'					
							);
							/*$fields['sales_price'] =array(
								'label'=>'Ticket Variation Sales Price ('.$curSYM.')',
								'req'=>false, 'type'=>'text'					
							);
							$fields['fees'] =array(
								'label'=>'Ticket Variation Fee Amount ('. $curSYM.' or %, if % type % sign in field)',
								'req'=>false, 'type'=>'text'			
							);
							*/
							$fields['stock'] =array(
								'label'=>__('Ticket Variation Stock Quantity (Leave blank for unlimited)','evovo'),
								'req'=>false, 'type'=>'text'					
							);
							$fields['stock_status'] =array(
								'label'=> __('Ticket Variation Stock Status','evovo'),
								'type'=>'select',
								'options'=>	array(
									'instock'=>__('In Stock','evovo'),
									'outofstock'=>__('Out of Stock','evovo')
								),
								'value'=> (isset($values['stock_status'])? $values['stock_status']:'')	
							);
							

							$fields['loggedin'] =array(
								'label'=> __('Who can purchase this variation','evovo'),
								'type'=>'select',	
								'options'=>	array(
									'nonmember'=>	__('Everyone','evovo'),
									'member'=>		__('Only loggedin members','evovo')
								),							
								'value'=> (isset($values['loggedin'])? $values['loggedin']:'no')			
							);

							$form_fields = apply_filters('evovo_variations_form_fields',$fields, $_POST);
						}

				break;
				case 'variation_type':
					
					$form_fields = apply_filters('evovo_variationtype_form_fields',array(
						'name'=>array(
							'label'=> __('Ticket Variation Name','evovo'),
							'req'=>true, 'type'=>'text'					
						),
						'options'=>array(
							'label'=> __('Ticket Variation Options (separated by comma) Do not use - or , as part of a option value','evovo'),
							'req'=>true, 'type'=>'textarea'	,
							'description'=> ( ($PP['type'] == 'edit' && isset($PP['vo_id']))? __('If you change the ticket variation option values, you will need to re-create ticket variations.','evovo'):'')			
						),
						
					), $PP);

				break;
				case 'option':
					
					$form_fields = apply_filters('evovo_variations_form_fields',array(
						'name'=>array(
							'label'=> __("Ticket Price Option Name (DO not use ' aphostrophe sign)",'evovo'),	
								'req'=>true, 'type'=>'text'					
						),'regular_price'=>array(
							'label'=> sprintf(__('Ticket Option Price  (%s)','evovo'), $curSYM),	'req'=>true, 'type'=>'text'					
						),'description'=>array(
							'label'=>__('Ticket Option Description','evovo'),	
							'type'=>'text'					
						),'stock'=>array(
							'label'=>__('Stock (Leave blank for unlimited)','evovo'),
							'req'=>false, 'type'=>'text'					
						),
						'stock_status' => array(
							'label'=>__('Stock Status','evovo'),
							'type'=>'select',
							'options'=>	array(
								'instock'=>__('In Stock','evovo'),
								'outofstock'=>__('Out of Stock','evovo')
							),
							'value'=> (isset($values['stock_status'])? $values['stock_status']:'')				
						),
						'sold_style' => array(
							'label'=>__('Sold Style','evovo'),
							'type'=>'select',
							'options'=>	array(
								'one'=>__('Individually','evovo'),
								'mult'=>__('Multiples','evovo')
							),
							'value'=> (isset($values['sold_style'])? $values['sold_style']:'')				
						),
						
						/*'pricing_type' => array(
							'label'=>'Options Pricing Type',
							'type'=>'select',
							'options'=>	array(
								'include'=>'Include as part of each ticket',
								'extra'=>'In addition to tickets'
							),
							'value'=> (isset($values['pricing_type'])? $values['pricing_type']:'')				
						)*/
						
					), $_POST);
				break;

			}

			//print_r($form_fields);
			if( $form_go):
				foreach($form_fields as $key=>$data):
					$required = (isset($data['req']) && $data['req'])?true: false;

					// select field type
						if($data['type'] == 'select'):?>
							<p><label><?php echo $data['label'];?></label>
								<select class='input' name='<?php echo $key;?>'>
								<?php 
								foreach($data['options'] as $key_=>$val){
									?><option value="<?php echo addslashes($key_);?>" <?php echo (!empty($data['value']) && $data['value']==$key_)? 'selected="selected"':'' ;?> ><?php echo $val;?></option><?php
								}
								?>
								</select>
							</p> 

						<?php 

					// populate button
						elseif($data['type'] == 'populate_button' && $PP['type']=='new'):
							$attrs = '';				
							foreach(array(
								'data-vos' => $data['data'],
								'data-vn' => $data['vn'],
							) as $key=>$val){
								$attrs .= $key .'="'. htmlentities($val) .'" ';
							}

						?>
							<p><a class='evovo_vt_popupate_with evo_admin_btn btn_triad' <?php echo $attrs;?>><?php echo $data['label'];?></a></p>

					<?php 

					// textarea field type
					elseif($data['type'] == 'textarea'):?>
						<p><label><?php echo $data['label'];?> <?php echo ($required)?'*':'';?></label>
							<textarea name="<?php echo $key;?>" class='input <?php echo $required?'req':'';?>' style='width:100%'><?php echo $this->check_v($values,$key);?></textarea>
							<?php if(isset($data['description'])):?>
								<span style='padding-top:5px;font-style:italic;font-size:13px'><?php echo $data['description'];?></span>
							<?php endif;?>
						</p>
					<?php 

					// regular input field
					else:?>
						<p><label><?php echo $data['label'];?> <?php echo ($required)?'*':'';?></label>
							<input class='input <?php echo $required?'req':'';?>' name='<?php echo $key;?>' type="text" value='<?php echo $this->check_v($values,$key);?>'>
							<?php if(isset($data['description'])):?>
								<span style='padding-top:5px;font-style:italic;font-size:13px'><?php echo $data['description'];?></span>
							<?php endif;?>
						</p>
						
				<?php endif;
				endforeach;

			endif;				

			do_action('evovo_new_edit_form',$values, $PP);
			
			// show save form
			if( $form_go): 
				?>
				<p><?php
					// save changes
					EVO()->elements->print_trigger_element(array(
						'class_attr'=> 'evo_btn evovo_form_submission',
						'title'=>__('Save Changes','evotx'),
						'uid'=>'evovo_save_vo_form',
						'lb_class' =>'evovo_editor',
						'lb_loader'=>true,
						'lb_hide'=> 2000,
						'lb_load_new_content'=>true,
						'load_new_content_id'=>'evovo_items_content'
					), 'trig_form_submit');
				?></p>
			<?php endif;?>

			</form>
		</div>
		
		<?php
		
		echo json_encode(array(
			'content'=> ob_get_clean(),			
			'status'=>'good'
		)); exit;
	}

	function check_v($arr, $field){
		return isset($arr[$field])? $arr[$field]:'';
	}

// Save or generate the variation options data 
	function evovo_save_vo_data(){

		$HELP = new evo_helper();

		$PP = $this->postdata;
		extract($PP);


		$EVENT = new EVO_Event( $event_id );
		$VO = new EVOVO_Var_opts( $EVENT, $wcid, $method );

		
		$new_vo_data = $this->evovo_get_new_vo_data( $PP );
		
		// if editting 
		if( !empty($vo_id)) $new_vo_data[ 'vo_id' ]= $vo_id;


		/*
		// combine other vo_data with new one
			$all_vo_data = isset($_POST['all_vo_data']) && !empty($_POST['all_vo_data'])? $_POST['all_vo_data']: array();
			
			if(!isset($all_vo_data[$vo_data['method']])) $all_vo_data[$vo_data['method']] = array();
			$all_vo_data[$vo_data['method']][ $vo_data['vo_id'] ] = $vo_data;
		*/	


		// pass newly created id and all vo data
		//do_action('evovo_save_vo_before_save', $vo_data, $all_vo_data, $EVENT, $VO);	
		
		
		// save the new values
		$VO->save_item( $new_vo_data['vo_id'], $new_vo_data);

		do_action('evovo_after_save', $new_vo_data , $EVENT, $VO, $PP);
		
		// if adding variation, disable manage ticket stock and remove stock
		if( $method == 'variation'){
			global $product;
			$product = wc_get_product( $wcid );
			if(!is_bool($product)){
				$product->set_manage_stock(false);
				$product->set_stock_quantity('');
				$product->save();
			}			
		}

		do_action('evovo_after_save_wc', $new_vo_data , $EVENT, $VO, $PP);

		//do_action('evovo_save_vo_before_echo', $vo_data, $EVENT, $json, $all_vo_data);		


		echo json_encode(array(
			'content'=>	$this->get_vo_items_content( $VO, $EVENT ),
			'status'=>	'good',
			'msg'=>	($type == 'edit')?__('Successfully Updated!','evovo'):__('Successfully Added!','evovo'),
			
		)); exit;
	}


	// return the VO values with a new ID
	public function evovo_get_new_vo_data( $data ){				

		$new_data = array(
			'method'=>'',
			'parent_type'=>'',
			'parent_id'=>'',			
		);

		// Process each submitted post values
		foreach($data as $key=>$val){
			if(in_array($key, array('action','json', 'all_vo_data','save','type'))) continue;			

			// remove dash from variation type option values
			if(isset($new_data['method']) && $new_data['method'] == 'variation_type' && $key == 'options'){
				$new_data[$key] = str_replace('-', ' ', urldecode($val) );
			}else{
				$new_data[$key] = $val;
			}
		}

		return $new_data;
	}

// Save new order
	public function evovo_save_order(){
		
		$PP = $this->postdata;

		$new_order = $PP['data'];
		$DD = $PP['d'];

		$VO = new EVOVO_Var_opts( $DD['eid'], $DD['wcid'] );

		$R = $VO->save_parent_vo_data_order( $DD['parent_id'], $DD['parent_type'], $new_order);

		echo json_encode(array(
			'status'=>	'good',
			'msg'=>	__('New Order Saved','evovo'),
		)); exit;
	}
	
// Delete an item
	function delete_item(){

		extract( $this->postdata );

		$EVENT = new EVO_Event( $event_id );
		$VO = new EVOVO_Var_opts( $EVENT, $wcid, $method );

		$result = $VO->delete_item( $vo_id );

		do_action('evovo_after_delete', $EVENT, $VO,  $this->postdata );

		echo json_encode(array(
			'status'=>	$result?'good':'bad',
			'msg'=>	__('Successfully Deleted!','evovo'),
			'content'=> $this->get_vo_items_content( $VO, $EVENT ),
		)); exit;
	}
}
new evovo_admin_ajax();