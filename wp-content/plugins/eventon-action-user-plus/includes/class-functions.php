<?php
/**
 * ActionUser Plus functions
 */

class evoaup_fnc{

// checkers
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

// HTML
	// submission level HTML data
	function print_submission_level_html($index, $level, $wcid){

		ob_start();
		$curSYM = get_woocommerce_currency_symbol();

		// no submission levels
		if(!isset($level['name'])){ 
			?>
			<div class='evoaup_slevel one_level'>
				<i></i>
				<div class='evoaup_details'>
					<p class='slevel_name'><?php evo_lang_e('General Event Submissions');?></p>
					<p class='slevel_price'><?php evo_lang_e('Price Per Event Submission');?> <?php echo $curSYM.$level['price'];?></p>
				</div>
				<div class='evoaup_purchase' style='display:none'>
					<p class="evoaup_quantity">
				 		<span class="label"><?php evo_lang_e('Quantity');?></span>
						<span class="qty">
							<b class="min evoaup_qty_change">-</b>
							<em>1</em>
							<b class="plu evoaup_qty_change">+</b>
							<input type="hidden" name='quantity' value='1' max='<?php echo !empty($max_quantity)? $max_quantity:'na';?>'/>
						</span>
					</p>
					<p><a class='evcal_btn evoaup_add_to_cart'  data-wcid='<?php echo $wcid;?>' data-sformat='regular'><?php evo_lang_e('Add to Cart'); ?></a></p>
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

			foreach( $level['fields'] as $in=>$field){

				$field_var = isset($FIELDS[$field]) ? $FIELDS[$field]: array(0=> $field);
				$field_name = $field_var[0];
				$field_name = str_replace('_', ' ', $field_name);
				$field_name = evo_lang($field_name);

				$_fields .= '<em >'.$field_name.'</em>';
			}


			?>
			<div class='evoaup_slevel'>
				<i></i>
				<div class='evoaup_details'>
					<p class='slevel_name'><?php echo $level['name'];?></p>
					<p class='slevel_price'><?php evo_lang_e('Price');?> <b><?php echo $curSYM.$level['price'];?></b></p>
					<p class='slevel_details'><?php evo_lang_e('Number of Event Submissions included');?> <b><?php echo $included_submissions;?></b></p>
				</div>
				<div class='evoaup_purchase' style='display:none'>
					<?php if($fields):?>
						<p class='fieldsincluded'><?php evo_lang_e('Included Event Fields');?> <span><?php echo $_fields;?></span></p>
					<?php endif;?>
					<p class="evoaup_quantity">
				 		<span class="label"><?php evo_lang_e('Quantity');?></span>
						<span class="qty">
							<b class="min evoaup_qty_change">-</b>
							<em>1</em>
							<b class="plu evoaup_qty_change">+</b>
							<input type="hidden" name='quantity' value='1' max='na'/>
						</span>
					</p>
					<p><a class='evcal_btn evoaup_add_to_cart' data-wcid='<?php echo $wcid;?>' data-level='<?php echo $index;?>' data-sformat='level_based'><?php evo_lang_e('Add to Cart'); ?></a></p>
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

		foreach($data['level_data'] as $order){
			foreach($order as $level=>$count)
				$new_array[$level] = isset($new_array[$level])? $new_array[$level] + $count: $count;
		}

		foreach($new_array as $level=>$count){
			$count = (int)$count;

			// skip levels with less than 1 submisisons left
			if($count<1) continue;

			if($level == $wcid){// old method
				echo "<p data-level='{$level}' data-wcid='{$wcid}' data-sformat='regular'>". evo_lang('Regular Submission'). " <span>(". evo_lang('Event Submissions Remaining'). ": {$count})</span></p>";
			}else{
				$level_data = $this->get_submission_level_data($level);

				if(!$level_data) continue;

				echo "<p data-level='{$level}' data-wcid='{$wcid}' data-sformat='level_based'>".(isset($level_data['name'])? $level_data['name']: $level) ." <span>(". evo_lang('Event Submissions Remaining'). ": {$count})</span></p>";
			}
		}
		echo "</div>";
	}
}