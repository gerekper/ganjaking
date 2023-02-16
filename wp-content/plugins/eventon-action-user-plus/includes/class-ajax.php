<?php
/**
 * All AJAX Calls
 */
class evoaup_ajax{
	public function __construct(){		
		$ajax_events = array(
			'evoaup_get_form'=>'evoaup_get_form',
			'evoaup_save_form'=>'evoaup_save_form',
			'evoaup_save_data'=>'evoaup_save_data',
			'evoaup_delete_data'=>'evoaup_delete_data',
			'evoaup_add_cart'=>'evoaup_add_cart',
			'evoaup_get_submission_form'=>'evoaup_get_submission_form',
			'evoaup_update_wccart'=>'evoaup_update_wccart',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}	

	// get AU submission form
		function evoaup_get_submission_form(){
			$opt = get_option('evcal_options_evoau_1');	
			$sub_levels = !empty($opt['evoaup_levels'])? $opt['evoaup_levels']: false;
				
			$level = $_POST['level'];
			$submission_format = $_POST['sformat'];
			$wcid = $_POST['wcid'];
			$msg = '';
			$status = 'good';			
		
			if(isset($sub_levels[$level]) && !empty($sub_levels[$level]['fields']) && $submission_format!= 'regular'){
				$form_field_permissions = $sub_levels[$level]['fields'];
			}else{
				// for regular submission types
				$form_field_permissions = array();
			}
		
			$form_args = array(
				'hidden_fields'=>array(
					'submission_type'=>'paid_submission',
					'submission_level'=>$level,
					'submission_format'=>$submission_format,
				)
			);

			// append form parameters into arg
			if(isset($_POST['d']) && isset($_POST['d']['lightbox'])){
				unset($_POST['d']['lightbox']);
				$form_args = array_merge($form_args,  $_POST['d']);
			}

			$form = new evoau_form();
			$form_html = $form->get_content('',$form_args, $form_field_permissions, true);
			$msg = 'Submission form retrieved!';

			echo json_encode(array(
				'html'=>$form_html,
				'status'=>$status,
				'msg'=>$msg, 
			)); exit;
		}

	// add item to cart
	function evoaup_add_cart(){		
		// get level information
		$opt = get_option('evcal_options_evoau_1');	
		$sub_levels = !empty($opt['evoaup_levels'])? $opt['evoaup_levels']: false;
		$level_index = isset($_POST['level'])? $_POST['level']: false;
		$cart_item_key = $html =   '';
		$status = 'good';

		// required field validation check
			if(empty($_POST['wcid'])){ echo json_encode(array('status'=>'bad','msg'=>'Missing WCID!')); exit;}
			if(empty($_POST['sformat'])){ echo json_encode(array('status'=>'bad','msg'=>'Missing product type!')); exit;}

		// old method with no levels
		if($_POST['sformat'] == 'regular'){
			$cart_item_data = array();
			$cart_item_data['evo_wcprod_type'] = 'paid event submissions';
			$cart_item_data['_producttype'] = 'evo_submission';
			if(isset($_POST['url'])) 	$cart_item_data['evoaup_url'] = $_POST['url'];
			
			$quantity = (int)$_POST['qty'];
			$cart_item_key = WC()->cart->add_to_cart(
				$_POST['wcid'],
				$quantity,0,array(),
				$cart_item_data
			);
		}else{
			if($level_index && $sub_levels && !empty($sub_levels[$level_index])){
				$level_data = $sub_levels[$level_index];
				$cart_item_data = array();
				$cart_item_data['evo_wcprod_type'] = 'paid event submissions';
				$cart_item_data['_producttype'] = 'evo_submission';
				$cart_item_data['evoaup_level'] = $level_index;
				$cart_item_data['evoaup_name'] = $level_data['name'];
				$cart_item_data['evoaup_price'] = $level_data['price'];
				$cart_item_data['evoaup_submissions'] = $level_data['submissions'];
				if(isset($_POST['url'])) $cart_item_data['evoaup_url'] = $_POST['url'];

				$quantity = (int)$_POST['qty'];

				$cart_item_key = WC()->cart->add_to_cart(
					$_POST['wcid'],
					$quantity,0,array(),
					$cart_item_data
				);
			}else{
				echo json_encode(array('status'=>'bad','msg'=>'Submission Levels Missing!')); exit;
			}
		}

		
		// if added to cart get success message HTML
		if(!empty($cart_item_key)){
			ob_start();?>
			<p class='evoaup_success' >
				<b></b>
				<span><?php evo_lang_e('Successfully Added to cart');?>!</span><br/>
				<span>
					<a href='<?php echo wc_get_cart_url();?>' class='evcal_btn'><?php evo_lang_e('View Cart');?></a>
					<a href='<?php echo wc_get_checkout_url();?>' class='evcal_btn'><?php evo_lang_e('Checkout');?></a>
				</span><br/>
				<em><?php evo_lang_e('Once order is placed and processed, please revisit this page to submit your purchased event.');?></em>
			</p>
			<?php

			$html = ob_get_clean();
			$msg = evo_lang('Successfully added to cart!');
		}else{
			$status = 'bad';
			$msg = evo_lang('Could not add to cart, please try later!');
		}

		echo json_encode(array(
			'status'=>$status,
			'html'=>$html,
			'msg'=>$msg, 
			'cart_url'=> wc_get_cart_url(),
			//'redirect'=> get_option( 'woocommerce_cart_redirect_after_add' ),
			'redirect'=> 'no',
			'is_user_logged_in'=>$this->is_user_logged(),
			'wc_cart_hash'=> (!empty($cart_item_key)? $cart_item_key:'')
		)); exit;

	}

	// update WC mini cart
		function evoaup_update_wccart(){

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


// ADMIN
	// get Form
	function evoaup_get_form(){
		ob_start();

		$curSYM = get_woocommerce_currency_symbol();

		$AU_FORM = new evoau_form();
		$SELECT_FIELDS = $AU_FORM->_get_form_selected_fields();

		// data for edit form
			$values = array();
			if($_POST['type'] == 'edit'){
				if(!empty($_POST['edata'])){
					$values = $_POST['edata'];
				}
			}

		?>

		<div class="evoaup_submission_form" >			
			<?php 
				$form_fields = apply_filters('evoaup_form_fields',array(
					'name'=>array(
						'label'=>'Submission Level Name',
						'req'=>true, 'type'=>'text'					
					),
					'color'=>array(
						'label'=> __('Color for the level','evoaup'),
						'type'=>'color'					
					),
					'price'=>array(
						'label'=>'Cost to submit events in this level  ('.$curSYM.')',
						'req'=>true, 'type'=>'text'					
					),
					'submissions'=>array(
						'label'=>'Event submission quantity included in this level (eg. 5)',
						'req'=>true, 'type'=>'text'					
					),
					/*'wordcount'=>array(
						'label'=>'Maximum word count allowed for event details (leave blank or 0 for unlimited)',
						'req'=>false, 'type'=>'text'					
					)
					/*'stock'=>array(
						'label'=>'Submissions Stock (leave blank for unlimited)',
						'req'=>true, 'type'=>'text'					
					),'stock_status'=>array(
						'label'=>'Submissions Stock Status',
						'type'=>'select',
						'options'=>	array(
							'instock'=>'In Stock',
							'outofstock'=>'Out of Stock'
						)			
					),*/
				), $_POST);

				foreach($form_fields as $key=>$data):

					if($data['type'] == 'select'):
				?>
					<p><label><?php echo $data['label'];?></label>
						<select class='input' name="<?php echo $key;?>">
						<?php 

						foreach($data['options'] as $key_=>$val){
							echo "<option value='$key_' ". ( (!empty($values[$key]) &&  $key_== $values[$key]) ? 'selected="selected"':'') .">{$val}</option>";
						}
						?>
						</select>
					</p> 

				<?php elseif($data['type'] == 'color'): ?>
					<p class='evoaup_color'><label><?php echo $data['label'];?> <?php echo ($data['req'])?'*':'';?></label>
						<span style='background-color:#<?php echo isset($values[$key])? $values[$key]:'808080';?>' class='evoaup_color_i'></span> 
						<input class='input' name='<?php echo $key;?>' type="hidden" value='<?php echo $this->check_v($values,$key);?>'>
					</p>
				<?php else:?>
					<p><label><?php echo $data['label'];?> <?php echo ($data['req'])?'*':'';?></label>
						<input class='input <?php echo ($data['req'])?'req':'';?>' name='<?php echo $key;?>' type="text" value='<?php echo $this->check_v($values,$key);?>'>
					</p>					
				<?php endif;?>
			<?php endforeach;?>

			<p>
			<label><?php _e('Select Supported Event Fields for this Level','evoaup');?></label>
			<i>(<?php _e('NOTE: Fields shown below are the fields enabled in ActionUser Settings > Form Fields, in the order they are saved.','evoaup');?></i><br/><br/>
			<?php

				$FIELDS = EVOAU()->frontend->au_form_fields('additional');
				unset($FIELDS['event_html']);
				unset($FIELDS['yourname']);
				unset($FIELDS['youremail']);

				do_action('evoaup_before_showing_form_fields', $FIELDS);

				//print_r($SELECT_FIELDS);
				//print_r($FIELDS);
				
				foreach($SELECT_FIELDS as $i=>$fieldvar){

					if(!isset($FIELDS[ $fieldvar])) continue;

					$name = $FIELDS[ $fieldvar][0];

					echo "<span class='checkbox_row'><input class='checkfields' ".( (!empty($values['fields']) &&  in_array($fieldvar, $values['fields']) )? 'checked':'') ." type='checkbox' name='eventfields[]' value='{$fieldvar}'/> {$name}</span>";
				}
			?>
			</p>

			<?php
				$attrs = '';

				foreach(array(
					'data-type'=>$_POST['type'],
					'data-index'=> (!empty($_POST['index'])? $_POST['index']:''),
				) as $key=>$val){
					$attrs .= $key .'="'. $val .'" ';
				}

			?>
			<p><a class='evoaup_form_submission evo_btn' <?php echo $attrs;?>><?php echo $_POST['type']=='new'? __('Add New','evoaup'): __('Save','evoaup');?></a></p>
		</div>
		<?php
		
		echo json_encode(array(
			'content'=> ob_get_clean(),			
			'status'=>'good'
		)); exit;
	}

	function evoaup_save_form(){
		if(!is_user_logged_in()) return;

		$post = array();
		foreach($_POST as $key=>$val){
			$post[$key] = !is_array($val)? urldecode($val): $val;
		}

		// process submissions as quantity
			if(!empty($post['submissions'])){
				$post['submissions'] = (int)$post['submissions'];
			}else{
				$post['submissions'] = 1;
			}

		$fnc = new evoaup_fnc();

		$html = $fnc->get_admin_submission_level_html($post, $post['index']);

		echo json_encode(array(
			'html'=>	$html, 
			'status'=>	'good',
			'msg'=>	__('Successfully Added New Submission Level','eventon')
		)); exit;
	}

	function evoaup_save_data(){
		if(!is_user_logged_in()) return;

		if(sizeof($_POST['leveldata'])>0){
			$opt = get_option('evcal_options_evoau_1');

			$level_data_array = array();
			if(isset($_POST['levelorder']) && count($_POST['levelorder'])>0){
				foreach($_POST['levelorder'] as $level){
					if(!isset($_POST['leveldata'][$level]) ) continue;
					$level_data_array[$level] = $_POST['leveldata'][$level];

					// process submissions as quantity
						if(!empty($level_data_array[$level]['submissions'])){
							$level_data_array[$level]['submissions'] = (int)$level_data_array[$level]['submissions'];
						}else{
							$level_data_array[$level]['submissions'] = 1;
						}
				}
			}

			$level_data_array = sizeof($level_data_array)>0 ? $level_data_array: $_POST['leveldata'];

			$opt['evoaup_levels'] = $level_data_array;

			update_option('evcal_options_evoau_1', $opt);
		}
		
		echo json_encode(array( 
			'status'=>	'good',
			'msg'=>	__('Successfully Added New Submission Level','eventon')
		)); exit;
		

	}

	function evoaup_delete_data(){
		if(!is_user_logged_in()) return;

		$opt = get_option('evcal_options_evoau_1');

		unset($opt['evoaup_levels'] );
		update_option('evcal_options_evoau_1', $opt);
		
		echo json_encode(array( 
			'status'=>	'good',
			'msg'=>	__('Successfully deleted all levels','eventon')
		)); exit;
	}

	function check_v($arr, $field){
		return isset($arr[$field])? $arr[$field]:'';
	}

	function is_user_logged(){
		$user = wp_get_current_user();
 
    	return $user->exists();
	}

}

new evoaup_ajax();