<?php
/**
 * ActionUser Plus functions
 */

class evoaup_fnc{

// checkers
	function have_valid_event_submissions($wcid=''){
			
		// Get all customer orders
		$customer_orders = new WP_Query(
			array(
				'numberposts' => -1,
				'post_type'   => 'shop_order',
				'post_status' => 'wc-completed',
				'meta_query' => array(
					array(
						'key'     => '_customer_user',
						'value'   => get_current_user_id(),
					),
					array(
						'relation' =>'OR',
						array(
							'key' => '_order_type',
							'value'   => 'evo_submission',
						),array(
							'key' => '_order_type',
							'value'   => 'evotix',
						)
					)						
				),
			)
		);

		//print_r($customer_orders);

		if($customer_orders->have_posts()){
			$orderID = '';
			$total_submission_left = 0;
			$current_submissions_left = 0;
			$submission_order_data = array();
			$submission_order_data_bylevel = array();
			$has_levelbased_submissions = false;

			$existing_submission_levels = $this->get_submission_levels();

			foreach($customer_orders->posts as $post){

				$order_id = $post->ID;
				
				$submission_data = get_post_meta($order_id, '_submission_data',true);
				//print_r($order_id);

				// level based submission
				if(!empty($submission_data) && sizeof($submission_data)>0 ){

					// if there are no submission level -- keep going
					if(!$existing_submission_levels) continue;	
				
					// check if submission levels in order also exist in settings
					$level_exists = $this->order_has_submission_levels_from_existing($submission_data);

					if(!$level_exists) continue;

					foreach($submission_data as $level_index=>$count){
						$total_submission_left += (int)$count;

						// tally submissions by level
						$submission_order_data_bylevel[$level_index] = isset($submission_order_data_bylevel[$level_index])?
							(int)$submission_order_data_bylevel[$level_index] + (int)$count: (int)$count;
					}

					$submission_order_data[$order_id] = isset($submission_order_data[$order_id])?
						array_merge($submission_order_data[$order_id],$submission_data): $submission_data;
					$has_levelbased_submissions = true;

				// No level
				}else{
					$submission_count = get_post_meta($order_id, '_submission_count',true);
					
					// skip orders with no submissions leftß
					if($submission_count < 1) continue;
					
					$current_submissions_left = (int)$submission_count;
					$total_submission_left += (int)$submission_count;

					$submission_order_data[$order_id][$wcid] = $submission_count;
				}
				
			}

			return array(
				'allcount' =>$total_submission_left,
				'level_data'	=>$submission_order_data,
				'submission_data' => $submission_order_data_bylevel,
				'submission_format'		=> ($has_levelbased_submissions? 'level_based':'regular'),
			);
						
		}
	    return false;
	}

	function is_paid_submission_enable($OPT){
		return (!empty($OPT['evoaup_create_product']) && $OPT['evoaup_create_product']=='yes' &&
				!empty($OPT['evoaup_product_id']))? true: false;
	}
	function is_submission_level_active($level){
		$opt = get_option('evcal_options_evoau_1');

		if(empty($opt['evoaup_levels'])) return false;
		if(!isset($opt['evoaup_levels'][$level])) return false;

		return true;	
	}
	function is_submission_stock_available($level, $qty){
		return true;
	}
	function get_admin_submission_level_html($args, $index=''){

		if(empty($args)) return false;

		ob_start();

		$curSYM = get_woocommerce_currency_symbol();

		// common attrs
			

		// if new ticket option generate a random index
			if(!empty($args['type']) && $args['type']=='new'){
				$index = rand(100000, 900000);
			}

			unset($args['action']);
			unset($args['index']);
			unset($args['type']);
		?>
		<li data-cnt="<?php echo $index;?>" class="new" >
			<span style='display:none' class='data' data-var='<?php echo json_encode($args);?>'></span>
			<?php echo $this->get_hidden_html_fields_for_admin($args, $index);?>
			<span class='actions'>
				<em alt="Edit" class='edit ajde_popup_trig evoaup_sl_form' data-popc='evoaup_lightbox' data-type='edit'><i class='fa fa-pencil'></i></em>
				<em alt="Delete" class='delete'>x</em>
			</span>
			Name: <b><?php echo $args['name'];?></b> Price: <b><?php echo $curSYM.$args['price'];?></b> Event Submissions Included: <b><?php echo $args['submissions'];?></b>
		</li>
		<?php

		return ob_get_clean();
	}

	function get_hidden_html_fields_for_admin($args, $index){
		if(empty($args)) return false;

		$output = array();
		foreach($args as $key=>$val){
			if($key=='fields' && is_array($val)){
				foreach($val as $fid=>$field){
					$output[] = "<input type='hidden' name='evoaup_levels[{$index}][fields][{$fid}]' value='{$field}'/>";
				}
			}else{
				$output[] = "<input type='hidden' name='evoaup_levels[{$index}][{$key}]' value='{$val}'/>";
			}
			
		}
		return implode('', $output);
	}

	function get_submission_levels($level='', $levels='', $usewcid= false){

		if(!empty($levels)){ 
			$sub_levels = $levels;
		}else{
			$opt = get_option('evcal_options_evoau_1');	
			$sub_levels = !empty($opt['evoaup_levels'])? $opt['evoaup_levels']: false;
		}	

		// For previous method without levels
		if(!$sub_levels && $usewcid){
			$wcpmv = get_post_custom($level);
			return array(
				$level=>array(
					'price'=> (!empty($wcpmv['_regular_price'])? $wcpmv['_regular_price'][0]:''),
					'type'=>'singular'
				)
			);
		}
		return $sub_levels;
	}
	function get_submission_level_data($level, $levels='', $usewcid= false){
		$levels = $this->get_submission_levels($level, $levels, $usewcid);
		return (isset($levels[$level]))? $levels[$level]: false;
	}
	function order_has_submission_levels_from_existing( $submission_in_order){
		$existing_submission_levels = $this->get_submission_levels();

		foreach($submission_in_order as $level=>$count){
			if(!empty($existing_submission_levels[$level]))
				return true;
		}
		return false;

	}

// Woocommerce helpers
	function get_wc_product_id($type=''){
		EVO()->cal->set_cur('evoau_1');
		$id = false;
		if($type =='ft_event'){
			$id = EVO()->cal->get_prop('evoaup_ft_product_id');
		}else{
			$id = EVO()->cal->get_prop('evoaup_product_id');
		}

		return $id;
	}
	function convert_to_currency($price, $symbol = true){		

		extract( apply_filters( 'wc_price_args', wp_parse_args( array(), array(
	        'ex_tax_label'       => false,
	        'currency'           => '',
	        'decimal_separator'  => wc_get_price_decimal_separator(),
	        'thousand_separator' => wc_get_price_thousand_separator(),
	        'decimals'           => wc_get_price_decimals(),
	        'price_format'       => get_woocommerce_price_format(),
	    ) ) ) );

		$sym = $symbol? html_entity_decode(get_woocommerce_currency_symbol($currency)):'';

		$negative = $price < 0;
		$price = floatval($negative? $price *-1: $price);
		$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

		

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
	        $price = wc_trim_zeros( $price );
	    }

	    $return = ( $negative ? '-' : '' ) . sprintf( $price_format, $sym, $price );

	    if ( $ex_tax_label && wc_tax_enabled() ) {
	        $return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
	    }


		return $return;
	}
	function get_price_format_data(){
		return array(
			'currencySymbol'=>get_woocommerce_currency_symbol(),
			'thoSep'=> get_option('woocommerce_price_thousand_sep'),
			'curPos'=> get_option('woocommerce_currency_pos'),
			'decSep'=> get_option('woocommerce_price_decimal_sep'),
			'numDec'=> get_option('woocommerce_price_num_decimals')
		);
	}

// HTML	
	// submission level HTML data
	function print_submission_level_html($index, $level, $wcid){

		ob_start();
		$curSYM = get_woocommerce_currency_symbol();
		$pfd = json_encode($this->get_price_format_data());

		// no submission levels
		if(!isset($level['name'])){ 
			?>
			<div class='evoaup_slevel one_level'>
				<i class='fa fa-check'></i>
				<div class='evoaup_details'>
					<p class='slevel_name'><?php evo_lang_e('General Event Submissions');?></p>
					<p class='slevel_price'><?php evo_lang_e('Price Per Event Submission');?> <?php echo $this->convert_to_currency($level['price']);?></p>
				</div>
				<div class='evoaup_purchase' style='display:none'>
					<p class='brb'>
						<span class='label'><?php evo_lang_e('Price Per Event Submission');?></span>
						<span class='right'><?php echo $this->convert_to_currency($level['price']);?></span>
					</p>
					<p class="evoaup_quantity brb">
				 		<span class="label"><?php evo_lang_e('Quantity');?></span>
						<span class="qty" data-p='<?php echo $level['price'];?>' data-pfd='<?php echo $pfd;?>'>
							<b class="min evoaup_qty_change">-</b>
							<em>1</em>
							<b class="plu evoaup_qty_change">+</b>
							<input type="hidden" name='quantity' value='1' max='<?php echo !empty($max_quantity)? $max_quantity:'na';?>'/>
						</span>
					</p>
					<p class='total'>
						<span class='label'><?php evo_lang_e('Total Price');?></span>
						<span class='right value'><?php echo $this->convert_to_currency($level['price']);?></span>
					</p>
					<p><a class='evcal_btn evoaup_add_to_cart'  data-wcid='<?php echo $wcid;?>' data-sformat='regular' data-type='paidev'><?php evo_lang_e('Add to Cart'); ?></a></p>
				</div>
			</div>
			<?php
		}else{
		// level based submissions
			if(empty($level['submissions']) || (int)$level['submissions']<1) return false;

			$included_submissions = !empty($level['submissions'])? (int)$level['submissions'] .' '. ((int)$level['submissions']<2? evo_lang('Event'): evo_lang('Events')): evo_lang('Unlimited');
			
			//print_r($level['fields']);
			$fields = !empty($level['fields'])? str_replace('_',' ', implode(', ', $level['fields'])):false;

			// included event fields
				$_fields = '';

				$FIELDS = EVOAU()->frontend->au_form_fields('additional');

				if(isset($level['fields'])){
					foreach( $level['fields'] as $in=>$field){
						$field_var = isset($FIELDS[$field]) ? $FIELDS[$field]: array(0=> $field);
						$field_name = $field_var[0];
						$field_name = str_replace('_', ' ', $field_name);
						$field_name = evo_lang($field_name);

						$_fields .= '<em >'.$field_name.'</em>';
					}
				}

				// Level color
				$color = isset($level['color'])? '#'. $level['color'] : false;

			?>
			<div class='evoaup_slevel' style='border-left-color:<?php echo $color? $color: false;?>'>
				<i class='fa fa-check'></i>
				<div class='evoaup_details'>
					<p class='slevel_name'><?php echo $level['name'];?></p>
					<p class='slevel_price'><?php evo_lang_e('Price');?> <b><?php echo $this->convert_to_currency($level['price']);?></b></p>
					<p class='slevel_details'><?php evo_lang_e('Number of Event Submissions included');?> <b><?php echo $included_submissions;?></b></p>
				</div>
				<div class='evoaup_purchase' style='display:none'>
					<p class='brb'>
						<span class='label'><?php evo_lang_e('Price');?></span>
						<span class='right'><?php echo $this->convert_to_currency($level['price']);?></span>
					</p>
					<p class='brb'> 
						<span class='label'><?php evo_lang_e('Number of Event Submissions included');?></span>
						<span class='right'><?php echo $included_submissions;?></span>
					</p>
					<?php if($fields):?>
						<p class='fieldsincluded brb'><?php evo_lang_e('Included Event Fields');?> <span><?php echo $_fields;?></span></p>
					<?php endif;?>
					<p class="evoaup_quantity brb">
				 		<span class="label"><?php evo_lang_e('Quantity');?></span>
						<span class="qty" data-p='<?php echo $level['price'];?>' data-pfd='<?php echo $pfd;?>'>
							<b class="min evoaup_qty_change">-</b>
							<em>1</em>
							<b class="plu evoaup_qty_change">+</b>
							<input type="hidden" name='quantity' value='1' max='na'/>
						</span>
					</p>
					<p class='total'>
						<span class='label'><?php evo_lang_e('Total Price');?></span>
						<span class='right value'><?php echo $this->convert_to_currency($level['price']);?></span>
					</p>
					<p class='addtocart'>
						<a class='evcal_btn evoaup_add_to_cart' data-wcid='<?php echo $wcid;?>' data-level='<?php echo $index;?>' data-sformat='level_based' data-type='paidev'><?php evo_lang_e('Add to Cart'); ?></a>
					</p>
				</div>
			</div>
			<?php

		}
		echo ob_get_clean();

	}

	// submission level selection for frontend form
	function print_submission_level_selection_html($data, $wcid){

		//print_r($data);
		
		$new_array = array();
		echo "<div class='evoaup_submission_level_selection'>";

		$submission_levels = $this->get_submission_levels();
		
		foreach($data['level_data'] as $order){
			foreach($order as $level=>$count)
				$new_array[$level] = isset($new_array[$level])? $new_array[$level] + $count: $count;
		}

		// order by submission level
		if($submission_levels){
			foreach($submission_levels as $SL_index=>$SL_data){
				if(!$SL_data) continue;
				if( array_key_exists($SL_index, $new_array)){
					$count = (int)$new_array[$SL_index];

					if($count <1) continue;

					// Level color
					$color = isset($SL_data['color'])? '#'. $SL_data['color'] : false;

					echo "<p data-level='{$SL_index}' data-wcid='{$wcid}' data-sformat='level_based' style='border-left-color:". ($color? $color:'') ."'>".(isset($SL_data['name'])? $SL_data['name']: $SL_index) ." <span>". evo_lang('Event Submissions Remaining'). "<b style='background-color:{$color}'>{$count}</b></span></p>";
				}
			}
		}

		foreach($new_array as $level=>$count){
			$count = (int)$count;

			// skip levels with less than 1 submisisons left
			if($count<1) continue;

			if($level == $wcid){// old method
				echo "<p data-level='{$level}' data-wcid='{$wcid}' data-sformat='regular'>". evo_lang('Regular Submission'). " <span>". evo_lang('Event Submissions Remaining'). "<b>{$count}</b></span></p>";
			}
		}
		
		
		echo "</div>";
	}
}