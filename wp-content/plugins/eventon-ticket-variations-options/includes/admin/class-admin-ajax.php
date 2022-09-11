<?php 
/**
 * Admin Ajax
 * @version 1.0.1
 */
class evovo_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'evovo_new_options_form'=>'evovo_new_options_form',
			'evovo_save_dataset'=>'evovo_save_vo_data',
			'evovo_save_neworder'=>'evovo_save_order',
			'evovo_delete_item'=>'delete_item',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

// GET FORM
	function evovo_new_options_form(){
		ob_start();
		$fnc = new evovo_fnc();

		$HELP = new evo_helper();
		$PP = $HELP->recursive_sanitize_array_fields( $_POST);
		
		$curSYM = get_woocommerce_currency_symbol();
		
		$json = $PP['json'];
		$method = $json['method'];
		$vo_id = isset($json['vo_id'])? $json['vo_id']: false;
		
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

		<div class="evovo_add_block_form evovo_add_options_form">		
			<input type="hidden" name='option_type' value='<?php echo $method;?>'>				
			<input type="hidden" class='input' name='save' value='<?php echo isset($json['save'])? $json['save']:'yes';?>'>			
			<?php 

			$form_go = true;

			$EVENT = new EVO_Event( $json['event_id'] );		
			$VO = new EVOVO_Var_opts($EVENT, (int)$json['wcid']);

			// if editing load passed on or saved values
			if(( $json['type'] == 'temp' || $json['type'] == 'edit')  && $vo_id ){
				$VO->set_new_method( $json['method'] );
				$VO->set_item_data($vo_id , $vo_data);
				$values = $VO->item_data;
			}

			switch($json['method']){
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
							'description'=> ( ($json['type'] == 'edit' && isset($json['vo_id']))? __('If you change the ticket variation option values, you will need to re-create ticket variations.','evovo'):'')			
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
							elseif($data['type'] == 'populate_button' && $json['type']=='new'):
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
							
					<?php endif;?>
				<?php endforeach;?>
			<?php endif;?>

			<?php
				$attrs = '';				
				foreach(array(
					'data-json' => json_encode($json)
				) as $key=>$val){
					$attrs .= $key .'="'. htmlentities($val) .'" ';
				}

			do_action('evovo_new_edit_form',$values, $_POST);
			
			if( $form_go): ?>
				<p><a class='evovo_form_submission evo_btn' <?php echo $attrs;?>><?php echo $json['type']=='new'? 'Add New':'Save';?></a></p>
			<?php endif;?>
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


// Save or generate the variation options data to event
	function evovo_save_vo_data(){

		$HELP = new evo_helper();
		
		$vo_data = $this->evovo_get_new_vo_data();
		$all_vo_data = array();

		// if vo_id passed override with that
		$json = $HELP->recursive_sanitize_array_fields($_POST['json']);

		$vo_parent_type = isset($json['parent_type']) ? $json['parent_type']: 'event';
		$vo_parent_id = isset($json['parent_id']) ? $json['parent_id']: '';

		if(!empty($json['vo_id'])) $vo_data['vo_id'] = (int)$json['vo_id'] ;

		// combine other vo_data with new one
			$all_vo_data = isset($_POST['all_vo_data']) && !empty($_POST['all_vo_data'])? $_POST['all_vo_data']: array();
			
			if(!isset($all_vo_data[$vo_data['method']])) $all_vo_data[$vo_data['method']] = array();
			$all_vo_data[$vo_data['method']][ $vo_data['vo_id'] ] = $vo_data;
			

		$EVENT = new EVO_Event($vo_data['event_id']);
		$VO = new EVOVO_Var_opts( $EVENT, $vo_data['wcid'], $vo_data['method'] );

		// pass newly created id and all vo data
		do_action('evovo_save_vo_before_save', $vo_data, $all_vo_data, $EVENT, $VO);	

		// save options
		$save = isset($vo_data['save']) && $vo_data['save'] == 'no' ? false: true;

	
		// save the VO data only if requested
			if($save){			
				$result = $VO->save_item( $vo_data['vo_id'], $vo_data);
			} 

		// if adding variation, disable manage ticket stock and remove stock
		if( $vo_data['method'] == 'variation'){
			global $product;
			$product = wc_get_product( (int)$vo_data['wcid']);
			if(!is_bool($product)){
				$product->set_manage_stock(false);
				$product->set_stock_quantity('');
				$product->save();
			}			
		}

		do_action('evovo_save_vo_before_echo', $vo_data, $EVENT, $json);		


		echo json_encode(array(
			'html'=>	$VO->get_all_vos_html( $vo_parent_id , $vo_parent_type, false, $all_vo_data), 
			'status'=>	'good',
			'msg'=>	($json['type'] == 'edit')?__('Successfully Updated!','evovo'):__('Successfully Added!','evovo'),
			'data'=> $vo_data,
			'all_vo_data'=> $all_vo_data
		)); exit;
	}


	// just return the VO values with a new ID
	public function evovo_get_new_vo_data(){
		$post = array();
		$json = $_POST['json'];

		$new_data = array(
			'method'=>'',
			'parent_type'=>'',
			'parent_id'=>'',			
		);

		// include json values in new data
		foreach($json as $k=>$v){
			$new_data[ $k] = sanitize_text_field( $v);
		}

		// Process each submitted post values
		foreach($_POST as $key=>$val){
			if(in_array($key, array('vo_id','action','json', 'all_vo_data'))) continue;			

			$new_data[$key] = !is_array($val)? 
				sanitize_text_field(urldecode($val)): $val;

			// remove dash from variation type option values
			if(isset($new_data['method']) && $new_data['method'] == 'variation_type' && $key == 'options'){
				$new_data[$key] = str_replace('-', ' ', urldecode($val) );
			}
		}

		$new_data['vo_id'] = rand(100000, 900000);

		return $new_data;
	}

// Save new order
	public function evovo_save_order(){
		
		$HELP = new evo_helper();
		$PP = $HELP->recursive_sanitize_array_fields( $_POST);

		$new_order = $PP['data'];

		$VO = new EVOVO_Var_opts( $PP['eid'], $PP['wcid'] );

		$R = $VO->save_parent_vo_data_order( $PP['parent_id'], $PP['parent_type'], $new_order);

		echo json_encode(array(
			'status'=>	'good',
			'msg'=>	__('Order Saved','evovo'),
			'data'=> $R
		)); exit;
	}
	
// Delete an item
	function delete_item(){
		$json = $_POST['json'];

		$vo_method = $json['method'];
		$vo_id = (int)$json['vo_id'];

		$EVENT = new EVO_Event($json['event_id']);
		$VO = new EVOVO_Var_opts( $EVENT, $json['wcid'], $vo_method );

		$result = $VO->delete_item( $vo_id );


		echo json_encode(array(
			'status'=>	$result?'good':'bad',
			'msg'=>	__('Successfully Deleted!','evovo'),
			'd'=> $VO->dataset,
		)); exit;
	}
}
new evovo_admin_ajax();