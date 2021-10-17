<?php 
/**
 * Admin Ajax
 * @version 0.1
 */
class evovo_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'evovo_new_options_form'=>'evovo_new_options_form',
			'evovo_save_dataset'=>'evovo_save_options',
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

		$curSYM = get_woocommerce_currency_symbol();
	
		$json = $_POST['json'];
		$values = array();
		?>

		<div class="evovo_add_block_form evovo_add_options_form">		
			<input type="hidden" name='option_type' value='<?php echo $json['method'];?>'>				
			<?php 

			$form_go = true;
			switch($json['method']){
				case 'variation':

					$EVENT = new EVO_Event( $json['event_id'] );					

					if($json['type'] == 'edit'  && isset($json['vo_id'])){
						$Variation_Types = new EVOVO_Var_opts($EVENT, $json['wcid'], 'variation');
						$Variation_Types->set_item_data($json['vo_id']);
						$values = $Variation_Types->item_data;
					}	

					// fields
						$Variation_Types = new EVOVO_Var_opts($EVENT, $json['wcid'], 'variation_type');
						$fields = array();
						$variation_options = false;

						if( $Variation_Types->is_set()){
							$variation_options = true;
							foreach($Variation_Types->dataset as $index=>$data){
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
								'label'=>'Ticket Variation Price  ('.$curSYM.')',
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
								'label'=>'Ticket Variation Stock Quantity (Leave blank for unlimited)',
								'req'=>false, 'type'=>'text'					
							);
							$fields['stock_status'] =array(
								'label'=>'Ticket Variation Stock Status',
								'type'=>'select',
								'options'=>	array(
									'instock'=>'In Stock',
									'outofstock'=>'Out of Stock'
								),
								'value'=> (isset($values['stock_status'])? $values['stock_status']:'')	
							);
							

							$fields['loggedin'] =array(
								'label'=>'Who can purchase this variation',
								'type'=>'select',	
								'options'=>	array(
									'nonmember'=>'Everyone',
									'member'=>'Only loggedin members'
								),							
								'value'=> (isset($values['loggedin'])? $values['loggedin']:'no')				
							);

							$form_fields = apply_filters('evovo_variations_form_fields',$fields, $_POST);
						}

				break;
				case 'variation_type':
					if($json['type'] == 'edit' && isset($json['vo_id'])){
						$EVENT = new EVO_Event( $json['event_id'] );
						$Variation_Types = new EVOVO_Var_opts($EVENT, $json['wcid'], 'variation_type');
						$Variation_Types->set_item_data($json['vo_id']);
						$values = $Variation_Types->item_data;
					}

					$form_fields = apply_filters('evovo_variationtype_form_fields',array(
						'name'=>array(
							'label'=>'Ticket Variation Name',
							'req'=>true, 'type'=>'text'					
						),'options'=>array(
							'label'=>'Ticket Variation Options (separated by comma) Do not use - or , as part of a option value',
							'req'=>true, 'type'=>'textarea'	,
							'description'=> ( ($json['type'] == 'edit' && isset($json['vo_id']))? __('If you change the ticket variation option values, you will need to re-create ticket variations.','evovo'):'')			
						)
					), $_POST);

				break;
				case 'option':
					if($json['type'] == 'edit' && isset($json['vo_id'])){
						$EVENT = new EVO_Event( $json['event_id'] );
						$VOoption = new EVOVO_Var_opts($EVENT, $json['wcid'], 'option');
						$VOoption->set_item_data($json['vo_id']);
						$values = $VOoption->item_data;
					}

					$form_fields = apply_filters('evovo_variations_form_fields',array(
						'name'=>array(
							'label'=>"Ticket Price Option Name (DO not use ' aphostrophe sign)",	'req'=>true, 'type'=>'text'					
						),'regular_price'=>array(
							'label'=>'Ticket Option Price  ('.$curSYM.')',	'req'=>true, 'type'=>'text'					
						),'description'=>array(
							'label'=>'Ticket Option Description',	'type'=>'text'					
						),'stock'=>array(
							'label'=>'Stock (Leave blank for unlimited)',
							'req'=>false, 'type'=>'text'					
						),
						'stock_status' => array(
							'label'=>'Stock Status',
							'type'=>'select',
							'options'=>	array(
								'instock'=>'In Stock',
								'outofstock'=>'Out of Stock'
							),
							'value'=> (isset($values['stock_status'])? $values['stock_status']:'')				
						),
						'sold_style' => array(
							'label'=>'Sold Style',
							'type'=>'select',
							'options'=>	array(
								'one'=>'Individually',
								'mult'=>'Multiples'
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
						<?php elseif($data['type'] == 'textarea'):?>
							<p><label><?php echo $data['label'];?> <?php echo ($required)?'*':'';?></label>
								<textarea name="<?php echo $key;?>" class='input <?php echo $required?'req':'';?>' style='width:100%'><?php echo $this->check_v($values,$key);?></textarea>
								<?php if(isset($data['description'])):?>
									<span style='padding-top:5px;font-style:italic;font-size:13px'><?php echo $data['description'];?></span>
								<?php endif;?>
							</p>
						<?php else:?>
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

// Save the variation options data to event
	function evovo_save_options(){
		$post = array();

		$json = $_POST['json'];

		// Process each submitted post values
		foreach($_POST as $key=>$val){
			if(in_array($key, array('vo_id','action','json'))) continue;			

			$post[$key] = !is_array($val)? urldecode($val): $val;

			// remove dash from variation type option values
			if(isset($_POST['json']) && $_POST['json']['method'] == 'variation_type' && $key == 'options'){
				$post[$key] = str_replace('-', ' ', urldecode($val) );
			}
		}

		$vo_id = rand(100000, 900000);
		if(!empty($json['vo_id'])) $vo_id = $json['vo_id'] ;
	
		// include other values for saving into VO item
			$post['method'] = $json['method'];
			$post['parent_type'] = isset($json['parent_type'])? $json['parent_type']:'';
			$post['parent_id'] = isset($json['parent_id'])? $json['parent_id']:'';

		$EVENT = new EVO_Event($json['event_id']);
		$VO = new EVOVO_Var_opts( $EVENT, $json['wcid'], $json['method'] );

		$result = $VO->save_item( $vo_id, $post);

		
		// if adding variation disable manage ticket stock and remove stock
		if( $post['method'] == 'variation'){
			global $product;
			$product = wc_get_product($json['wcid']);
			if(!is_bool($product)){
				$product->set_manage_stock(false);
				$product->set_stock_quantity('');
				$product->save();
			}			
		}

		do_action('evovo_save_vo_before_echo', '', $vo_id, $json, $EVENT);		

		$html = $VO->get_all_vos_html('', 'event');

		echo json_encode(array(
			'html'=>	$html, 
			'status'=>	'good',
			'msg'=>	($json['type'] == 'edit')?__('Successfully Updated!','evovo'):__('Successfully Added!','evovo')
		)); exit;
	}
	
	function delete_item(){
		$json = $_POST['json'];

		$EVENT = new EVO_Event($json['event_id']);
		$VO = new EVOVO_Var_opts( $EVENT, $json['wcid'], $json['method'] );

		$result = $VO->delete_item( $json['vo_id'] );

		echo json_encode(array(
			'status'=>	$result?'good':'bad',
			'msg'=>	__('Successfully Deleted!','evovo')
		)); exit;
	}
}
new evovo_admin_ajax();