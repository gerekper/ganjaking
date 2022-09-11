<?php
/**
 * EventON General Calendar Elements
 * @version 4.0.6

Items //
print_date_time_selector
print_time_selector
yesno_btn
get_icon
tooltips
icons
start_table_header

register_shortcode_generator_styles_scripts
enqueue_shortcode_generator
load_colorpicker
register_colorpicker
 */

class EVO_General_Elements{	

	public $svg;

	public function __construct(){
		include_once 'class-elements-svg.php';
		$this->svg = new EVO_Elements_SVG();
	}

// standard form elements
	function get_element($A){
		$A = array_merge( array(
			'id'=>'',
			'name'=>'',	
			'label'=>'',		
			'hideable'=> false,
			'value'=>'','default'=>'','values'=> array(),'max'=>'','min'=>'','step'=>'','values_array'=> array(),
			'TD'=>'eventon', // text domain
			'legend'=>'','tooltip'=>'',
			'tooltip_position'=>'',
			'description'=>'',
			'options'=> false, 'select_multi_options'=> false,
			'type'=>'', 'field_type'=>'text','field_attr'=>array(),'field_class'=> '',
			'reverse_field' => false,
			'afterstatement'=>'',
			'row_class'=>'', 'select_option_class'=>'','unqiue_class'=>'','class_2'=>'',
			'inputAttr'=>'','attr'=>'',
			'nesting'=>'', // pass nesting class name
			'row_style'=> '',// pass styles 
			'content'=> '', 'field_after_content'=>'', 'field_before_content'=>'',
			'support_input'=>false

		), $A);
		extract($A);

		// reuses
			$legend_code = !empty($tooltip) ? $this->tooltips($tooltip, $tooltip_position, false): null;
			if(!empty($field_attr) && count($field_attr)>0){
				$field_attr = array_map(function($v,$k){
					return $k .'="'. $v .'"';
				}, array_values($field_attr), array_keys($field_attr));
				
			}
			$field_attr = !empty($field_attr) ? implode(' ', $field_attr) : null;

		// validation
			if(empty($type)) return false;


		// nesting
			$nesting_start = $nesting_end = '';
			if(!empty($nesting)){
				$nesting_start = "<div class='{$nesting}'>";
				$nesting_end = "</div>";
			}

		
		ob_start();

		echo $nesting_start;

		switch($type){
			// notices
			case 'notice':
				echo "<div class='evo_elm_row evo_elm_notice {$row_class}' style='{$row_style}'>". $name ."</div>";
			break;

			// custom code field
			case 'custom_code':
				echo $content;
			break;

			// hidden input field
			case 'hidden':
				echo "<input type='hidden' name='".$id."' value='". $value ."'/>";
			break;

			// GENERAL Text field
			case 'text':
				echo "<div class='evo_elm_row {$id}' style='{$row_style}'>";
				$placeholder = (!empty($default) )? 'placeholder="'.$default.'"':null;				

				$show_val = false; $hideable_text = '';
				if( $hideable && !empty($value)){
					$show_val = true;
					$hideable_text = "<span class='evo_hideable_show' data-t='". __('Hide', $TD) ."'>". __('Show',$TD). "</span>";
				}
				
				echo"<p class='evo_field_label'>".$name.$legend_code. $hideable_text. "</p><p class='evo_field_container'>";

				if($show_val && $hideable){
					echo "<input type='password' style='' name='".$id."'";
					echo'value="'. $value .'"';
				}else{
					echo "<input type='{$field_type}' name='{$id}' max='{$max}' min='{$min}' step='{$step}'";
					echo 'value="'. $value .'"';
				}				
				echo $placeholder."/>";

				if(!empty($description)) echo "<em>". $description ."</em>";

				echo "</p></div>";
			break;

			// color picker field
			case 'colorpicker':

				$vis_input_field = !empty($support_input) && $support_input ? true: false;

				echo "<div class='evo_elm_row {$id}' style='{$row_style}'>";

				echo"<p class='evo_field_label'>".$name.$legend_code. "</p>";
				echo "<p class='evo_field_container ". ( $vis_input_field? 'visi':'') ."'>";
				echo "<em class='evo_elm_color' style='background-color:#{$value}'></em>";

				if($vis_input_field ):
					echo "<input class='evo_elm_hex' type='text' name='{$id}' value='{$value}'/>";
				else:
					echo "<input class='evo_elm_hex' type='hidden' name='{$id}' value='{$value}'/>";
				endif;
				
				//echo "<input class='evo_elm_rgb' type='hidden' name='{$rgb_field_name}' value='{$rgb_num}'/>";

				echo "</p></div>";
			break;

			case 'plusminus':

				echo "<div class='evo_elm_row {$id} {$row_class}' style='{$row_style}'>";

				echo $field_before_content;

				echo"<p class='evo_field_label'>".$name.$legend_code. "</p><p class='evo_field_container evo_field_plusminus_container'>";
				?>
					<span class="evo_plusminus_adjuster">
						<b class="min evo_plusminus_change <?php echo $unqiue_class;?>">-</b>
						<input class='evo_plusminus_change_input <?php echo $class_2;?>' type='text' name='<?php echo $id;?>' value='<?php echo $value;?>' data-max='<?php echo $max;?>'/>
						<b class="plu evo_plusminus_change <?php echo $unqiue_class;?> <?php echo (!empty($max) && $max==1 )? 'reached':'';?>">+</b>						
					</span>
				<?php

				echo "</p>";

				echo $field_after_content;

				echo "</div>";

			break;

			// textarea
			case 'textarea':

				echo "<div class='evo_elm_row {$id}' style='{$row_style}'>";
				echo"<p class='evo_field_label'>".$name.$legend_code . "</p><p class='evo_field_container'>";

				echo "<textarea name='{$id}'>{$value}</textarea>";

				echo "</p></div>";

			break;

			// Select in a lightbox -- for taxonomy values
			case 'lightbox_select_vals':

				echo "<div class='evo_elm_row evo_elm_lb_select {$row_class}' style='{$row_style}'>";
				// get values to show
					$values = !empty($value)? explode(',', $value): array();

					if(count($values_array) == 0){
						$values_array = array();
						if(!empty($taxonomy)){
							$t = get_terms( array('taxonomy'=> $taxonomy,'hide_empty'=>false));
							if(!empty($t) && !is_wp_error($t)){
								foreach($t as $term){
									$values_array[ $term->term_id ] = $term->name;
								}
							}
						}
					}

				if(count($values_array)>0):
					echo "
					<div class='evo_elm_lb_window' style='display:none'>
						<div class='eelb_in'>
						<div class='eelb_i_i'>";
						foreach($values_array as $f=>$v){
							echo "<span class='". (in_array($f, $values)?'select':'') ."' value='{$f}'>{$v}</span>";
						}
					echo "</div></div></div>";
				endif;

				$placeholder = (!empty($default) )? 'placeholder="'.$default.'"':null;	

				echo "<div class='evo_elm_lb_fields'>";
					if(!$reverse_field) echo"<p class='evo_field_label'>".$name.$legend_code . "</p>";					
					echo "<p class='evo_field_container evo_elm_lb_field'>";
					echo "<input class='evo_elm_lb_field_input {$field_class}' type='{$field_type}' {$field_attr} name='{$id}' {$placeholder} " . 'value="'. $value .'"/>';
					echo "</p>";
					if($reverse_field) echo"<p class='evo_field_label'>".$name.$legend_code . "</p>";				
				echo "</div>";
				echo "</div>";
			break;

			// Select in a lightbox -- for other general values
			case 'lightbox_select_cus_vals':

				echo "<div class='evo_elm_row evo_elm_lb_select {$row_class}' style='{$row_style}'>";
								

				if(count($options)>0):
					echo "
					<div class='evo_elm_lb_window' style='display:none'>
						<div class='eelb_in'>
						<div class='eelb_i_i'>";
						foreach($options as $f=>$v){
							echo "<span class='". (in_array($f, $values)?'select':'') ."' value='{$f}'>{$v}</span>";
						}
					echo "</div></div></div>";
				endif;

				$placeholder = (!empty($default) )? 'placeholder="'.$default.'"':null;	

				echo "<div class='evo_elm_lb_fields'>";
					if(!$reverse_field) echo"<p class='evo_field_label'>".$name.$legend_code . "</p>";					
					echo "<p class='evo_field_container evo_elm_lb_field'>";
					echo "<input class='evo_elm_lb_field_input {$field_class}' type='{$field_type}' {$field_attr} name='{$id}' {$placeholder} " . 'value="'. $value .'"/>';
					echo "</p>";
					if($reverse_field) echo"<p class='evo_field_label'>".$name.$legend_code . "</p>";				
				echo "</div>";
				echo "</div>";
			break;

			// select row 
			case 'select_row':
				?>
				<p class='evo_elm_row evo_row_select <?php echo $row_class;?> <?php echo $select_multi_options? 'multi':'';?>' style='{$row_style}'>
					<input type='hidden' name='<?php echo $name;?>' value='<?php echo $value;?>'/>
					
					<?php if(!empty($label)):?> 
						<label style='margin-right: 10px;'><?php echo $label.' '. $legend_code;?></label>
					<?php endif;?>
					
					<span class='values <?php echo $name;?>'>
					<?php 

					$vals = array();
					if($select_multi_options && !empty($value)){
						$vals = explode(',', $value);
					}

					foreach($options as $F=>$V){

						$selected = '';
						if($select_multi_options){
							if( in_array($F, $vals)) $selected = ' select';
						}else{
							if($F==$value) $selected = ' select';
						}


						echo "<span value='{$F}' class='opt{$selected} {$select_option_class}'>{$V}</span>";
					}?>
					</span>
				</p><?php
			break;

			// DROP Down select field
			case 'dropdown':					
						
				echo "<p class='evo_elm_row evo_elm_select {$id} {$row_class}' style='{$row_style}'>";
				echo "<label>$name $legend_code</label>"; 
				echo "<select class='ajdebe_dropdown' name='".$id."'>";

				if(is_array($options)){
					$dropdown_opt = !empty($value)? $value: (!empty($default)? $default :'');		
					foreach($options as $option=>$option_val){
						echo"<option name='".$id."' value='".$option."' "
						.  ( ($option == $dropdown_opt)? 'selected=\"selected\"':null)  .">".$option_val."</option>";
					}	
				}					
				echo  "</select>";
					// legend for under the field
					if(!empty( $legend )){
						echo "<br/><i style='opacity:0.6'>".$legend."</i>";
					}
				echo "</p>";						
			break;
			// DROP Down select field -- select2
			case 'dropdownS2':					
						
				echo "<p class='evo_elm_row evo_elm_select {$id} {$row_class}' style='{$row_style}'>";
				echo "<label>$name $legend_code</label>"; 
				echo "<select class='ajdebe_dropdown evo_select2' name='".$id."' style='width:100%'>";

				if(is_array($options)){
					$dropdown_opt = !empty($value)? $value: (!empty($default)? $default :'');		
					foreach($options as $option=>$option_val){
						echo"<option name='".$id."' value='".$option."' "
						.  ( ($option == $dropdown_opt)? 'selected=\"selected\"':null)  .">".$option_val."</option>";
					}	
				}					
				echo  "</select>";
					// legend for under the field
					if(!empty( $legend )){
						echo "<br/><i style='opacity:0.6'>".$legend."</i>";
					}
				echo "</p>";						
			break;

			// YES NO
			case 'yesno':						
				if(empty( $value) ) $value = 'no';
				echo "<p class='evo_elm_row yesno_row {$id} {$row_class}' style='{$row_style}'>".$this->yesno_btn(array(
						'id'=>$id,
						'var'=> $value,
						'afterstatement'=> $afterstatement,
						'input'=> true,
						'guide'=> $tooltip
					))."<span class='field_name'>". $name ."{$legend_code}</span>";

					// description text for this field
					if(!empty( $legend )){
						echo"<i style='opacity:0.6; padding-top:8px; display:block'>".$legend."</i>";
					}
				echo'</p>';
			break;
			case 'yesno_btn':						
				if(empty( $value) ) $value = 'no';
				echo "<p class='evo_elm_row yesno_row {$id} {$row_class}' style='{$row_style}'>".

				$this->yesno_btn(array(
					'id'=>$id,
					'var'=> $value,
					'afterstatement'=> $afterstatement,
					'input'=> true,
					'guide'=> $tooltip, 'guide_position'=> $tooltip_position,
					'label'=> $label,
					'inputAttr'=>$inputAttr,
					'attr'=>$attr,
				));

				echo'</p>';	
			break;
			case 'begin_afterstatement': 						
				$yesno_val = (!empty($value))? $value:'no';				
				echo"<div class='evo_elm_afterstatement ' id='{$id}' style='display:".(($yesno_val=='yes')?'block':'none')."'>";
			break;
			case 'end_afterstatement': echo "</div>"; break;
		}

		echo $nesting_end;

		return ob_get_clean();
	}

	function process_multiple_elements($A){
		$output = '';
		foreach($A as $key=>$AD){
			$output .= $this->get_element( $AD);
		}
		return $output;
	}

// date time selector
	function print_date_time_selector($A){
		$D = array(
			'disable_date_editing'=> false,
			'minute_increment'=> 1,
			'time_format'=> 'H:i:s',
			'date_format'=> 'Y/m/d',
			'date_format_hidden'=>'Y/m/d',
			'unix'=> '',				
			'type'=>'start',
			'assoc'=>'reg',
			'names'=>true,
			'rand'=>'',
			'time_opacity'=> 1,
			'selector'=>'both', // both, date, time
		);
		$A = array_merge($D, $A);

		extract($A);

		$rand = (empty($rand))? rand(10000,99999): $rand;

		$hr24 = false;

		if(!empty($time_format) && ( strpos($time_format, 'H')!== false || strpos($time_format, 'G') !== false ) )   $hr24 = true;

		// processings
		$unix = !empty($unix)? (int)$unix : current_time('timestamp');
		
		$DD =  new DateTime();
		$DD->setTimezone( EVO()->calendar->timezone0 );
		$DD->setTimestamp( $unix);

		$date_val = $DD->format( $date_format );
		$date_val_x = $DD->format(  $date_format_hidden );
		$hour = $DD->format( ($hr24? 'H':'h') );
		$minute = $DD->format( 'i');
		$ampm = $DD->format( 'a');

		echo "<span class='evo_date_time_select {$type}' data-id='{$rand}' data-unix='{$unix}'> ";
			
		if($selector != 'time' ):
			echo " <span class='evo_date_edit'>
				<input id='evo_{$type}_date_{$rand}' class='". ($disable_date_editing?'':"datepicker{$type}date")." ". ($assoc != 'rp'? 'req':'')." {$type} evo_dpicker ' readonly='true' type='text' data-role='none' name='event_{$type}_date' value='".$date_val."' data-assoc='{$assoc}' />	
				<input type='hidden' name='event_{$type}_dateformat' value='{$date_format}'/>

				<input type='hidden' name='".($names? "event_{$type}_date_x":'')."' class='evo_{$type}_alt_date alt_date' value='{$date_val_x}'/>
				<input type='hidden' class='alt_date_format' name='event_{$type}_dateformat_alt' value='". _evo_dateformat_PHP_to_jQueryUI($date_format_hidden) ."'/>

			</span>";

		endif;

		if($selector != 'date' ):
			echo "<span class='evo_time_edit' style='opacity:{$time_opacity}'>
				<span class='time_select'>";
				if($disable_date_editing){
					echo "<span>". $hour ."</span>";
				}else{													
					echo "<select class='evo_time_select _{$type}_hour' name='".($names? "_{$type}_hour":'')."' data-role='none'>";

					for($x=1; $x< ($hr24? 25:13 );$x++){	
						$y = ($hr24)? sprintf("%02d",($x-1)): $x;							
						echo "<option value='$y'".(($hour==$y)?'selected="selected"':'').">$y</option>";
					}
					echo "</select>";
				}
				echo "</span>";

				echo "<span class='time_select'>";
				if($disable_date_editing){
					echo "<span>". $minute ."</span>";
				}else{	
					echo "<select class='evo_time_select _{$type}_minute' name='".($names? "_{$type}_minute":'')."' data-role='none'>";

					$minute_adjust = (int)(60/$minute_increment);
					for($x=0; $x<$minute_adjust;$x++){
						$min = $minute_increment * $x;
						$min = ($min<10)?('0'.$min):$min;
						echo "<option value='$min'".(($minute==$min)?'selected="selected"':'').">$min</option>";
					}
					echo "</select>";
				}
				echo "</span>";

				// AM PM
				if(!$hr24){
					echo "<span class='time_select'>";
					if($disable_date_editing){
						echo "<span>". $ampm ."</span>";
					}else{	
						echo "<select name='".($names? "_{$type}_ampm":'')."' class='_{$type}_ampm ampm_sel'>";													
						foreach(array('am'=> evo_lang_get('evo_lang_am','AM'),'pm'=> evo_lang_get('evo_lang_pm','PM') ) as $f=>$sar){
							echo "<option value='".$f."' ".(($ampm==$f)?'selected="selected"':'').">".$sar."</option>";
						}							
						echo "</select>";
						echo "</span>";
					}
				}
				
			echo "</span>";
		endif;

		echo "</span>";
	}

// ONLY time selector
	function print_time_selector($A){
		$D = array(
			'disable_date_editing'=> false,
			'minute_increment'=> 1,
			'time_format'=> 'H:i:s',
			'minutes'=> 0,		
			'var'=>'_unix',		
			'type'=> 'hm', // (hm) hour/min OR (tod) time of day
		);
		$A = array_merge($D, $A);

		extract($A);

		$hr24 = false;
		if(!empty($time_format) && strpos($time_format, 'H')!== false) $hr24 = true;

		$unix = $minutes * 60;

		// processings
		$hour = date( ($hr24? 'H':'h'), $unix);
		$minute = date( 'i', $unix);
		$ampm = date( 'a', $unix);

		echo "<span class='evo_date_time_select time_select {$type}' > 
			<span class='evo_time_edit'>
				<input type='hidden' name='{$var}' value='{$unix}'/>
				<span class='time_select'>";
				if($disable_date_editing){
					echo "<span>". $hour ."</span>";
				}else{													
					echo "<select class='evo_timeselect_only _hour' name='_hour' data-role='none'>";

					for($x=1; $x< ($hr24? 25:13 );$x++){	
						$y = ($hr24)? sprintf("%02d",($x-1)): $x;							
						echo "<option value='$y'".(($hour==$y)?'selected="selected"':'').">$y</option>";
					}
					echo "</select>";
				}
				echo " Hr </span>";

				echo "<span class='time_select'>";
				if($disable_date_editing){
					echo "<span>". $minute ."</span>";
				}else{	
					echo "<select class='evo_timeselect_only _minute' name='_minute' data-role='none'>";

					$minute_adjust = (int)(60/$minute_increment);
					for($x=0; $x<$minute_adjust;$x++){
						$min = $minute_increment * $x;
						$min = ($min<10)?('0'.$min):$min;
						echo "<option value='$min'".(($minute==$min)?'selected="selected"':'').">$min</option>";
					}
					echo "</select>";
				}
				echo " Min </span>";

				// AM PM
				if(!$hr24 && $type == 'tod'){
					echo "<span class='time_select'>";
					if($disable_date_editing){
						echo "<span>". $ampm ."</span>";
					}else{	
						echo "<select name='_ampm' class='evo_timeselect_only _ampm'>";													
						foreach(array('am'=> evo_lang_get('evo_lang_am','AM'),'pm'=> evo_lang_get('evo_lang_pm','PM') ) as $f=>$sar){
							echo "<option value='".$f."' ".(($ampm==$f)?'selected="selected"':'').">".$sar."</option>";
						}							
						echo "</select>";
						echo "</span>";
					}
				}
				
			echo "</span>
		</span>";
	}

	function _get_date_picker_data(){
		return array(
			'date_format' => (empty(EVO()->calendar->date_format)? get_option('date_format'): EVO()->calendar->date_format ),
			'js_date_format' => _evo_dateformat_PHP_to_jQueryUI( EVO()->calendar->date_format  ),
			'time_format' =>  EVO()->calendar->time_format ,
			'sow'=> get_option('start_of_week'),
		);
	}
	function _print_date_picker_values(){			
		$data_str = json_encode($this->_get_date_picker_data());

		echo "<div class='evo_dp_data' data-d='". $data_str ."'></div>";
	}

// Yes No Buttons
	function yesno_btn($args=''){
		$defaults = array(
			'id'=>'',
			'var'=>'', // the value yes/no
			'no'=>'',
			'default'=>'',
			'input'=>false,
			'inputAttr'=>'',
			'label'=>'',
			'guide'=>'',
			'guide_position'=>'',
			'abs'=>'no',// absolute positioning of the button
			'attr'=>'', // array
			'afterstatement'=>'',
			'nesting'=>false
		);
		
		$args = shortcode_atts($defaults, $args);

		extract($args);

		$_attr = $no = '';

		if(!empty($args['var'])){
			$args['var'] = (is_array($args['var']))? $args['var']: strtolower($args['var']);
			$no = ($args['var']	=='yes')? 
				 null: 
				 ( (!empty($args['default']) && $args['default']=='yes')? null:'NO');
		}else{
			$no = (!empty($args['default']) && $args['default']=='yes')? null:'NO';
		}


		if(!empty($args['attr'])){
			foreach($args['attr'] as $at=>$av){
				$_attr .= $at.'="'.$av.'" ';
			}
		}

		// afterstatement
			if(!empty($args['afterstatement'])){
				$_attr .= 'afterstatement="' . $args['afterstatement'] .'"';
			}
			
		// input field
		$input = '';
		if($args['input']){
			$input_value = (!empty($args['var']))? 
				$args['var']: (!empty($args['default'])? $args['default']:'no');

			// Attribut values for input field
			$inputAttr = '';
			if(!empty($args['inputAttr'])){
				foreach($args['inputAttr'] as $at=>$av){
					$inputAttr .= $at.'="'.$av.'" ';
				}
			}

			// input field
			$input = "<input {$inputAttr} type='hidden' name='{$args['id']}' value='{$input_value}'/>";
		}

		$guide = '';
		if(!empty($args['guide'])){
			$guide = $this->tooltips($args['guide'], $args['guide_position']);
		}

		$label = '';
		if(!empty($args['label']))
			$label = "<label class='ajde_yn_btn_label evo_elm' for='{$args['id']}'>{$args['label']}{$guide}</label>";

		// nesting
			$nesting_start = $nesting_end = '';
			if($args['nesting']){
				$nesting_start = "<p class='yesno_row'>";
				$nesting_end = "</p>";
			}

		return $nesting_start.'<span id="'.$args['id'].'" class="evo_elm ajde_yn_btn '.($no? 'NO':null).''.(($args['abs']=='yes')? ' absolute':null).'" '.$_attr.'><span class="btn_inner" style=""><span class="catchHandle"></span></span></span>'.$input.$label.$nesting_end;
	}

// SVG icons
	public function get_icon($name){
		if( $name == 'live'){
			return '<svg version="1.1" x="0px" y="0px" viewBox="0 0 73 53" enable-background="new 0 0 100 100" xmlns="http://www.w3.org/2000/svg"><g transform="matrix(1, 0, 0, 1, -13.792313, -23.832699)"><g><path  d="M75.505,25.432c-0.56-0.578-1.327-0.906-2.132-0.913c-0.008,0-0.015,0-0.022,0    c-0.796,0-1.56,0.316-2.123,0.88l-0.302,0.302c-1.156,1.158-1.171,3.029-0.033,4.206c5.274,5.451,8.18,12.63,8.18,20.214    c0,7.585-2.905,14.764-8.18,20.214c-1.141,1.178-1.124,3.054,0.037,4.211l0.303,0.302c0.562,0.561,1.324,0.875,2.118,0.875    c0.009,0,0.018,0,0.026,0c0.803-0.007,1.569-0.336,2.128-0.912C81.95,68.158,85.5,59.39,85.5,50.121    C85.5,40.853,81.95,32.085,75.505,25.432z"/><path d="M20.928,50.121c0-7.583,2.905-14.762,8.18-20.214c1.14-1.177,1.124-3.051-0.036-4.209l-0.303-0.302    c-0.563-0.562-1.325-0.877-2.12-0.877c-0.008,0-0.017,0-0.025,0c-0.804,0.007-1.571,0.335-2.13,0.913    C18.049,32.085,14.5,40.853,14.5,50.121c0,9.269,3.549,18.037,9.995,24.689c0.56,0.578,1.327,0.906,2.131,0.913    c0.008,0,0.016,0,0.024,0c0.795,0,1.559-0.315,2.121-0.879l0.303-0.303c1.158-1.158,1.174-3.03,0.035-4.207    C23.833,64.884,20.928,57.705,20.928,50.121z"/><path  d="M65.611,36.945c-0.561-0.579-1.33-0.907-2.136-0.913c-0.006,0-0.013,0-0.019,0    c-0.799,0-1.565,0.319-2.128,0.886l-0.147,0.148c-1.151,1.159-1.164,3.026-0.028,4.201c2.311,2.387,3.583,5.532,3.583,8.854    c0,3.323-1.272,6.468-3.582,8.854c-1.137,1.175-1.125,3.042,0.027,4.201l0.147,0.148c0.562,0.567,1.329,0.886,2.128,0.886    c0.006,0,0.013,0,0.019,0c0.806-0.005,1.575-0.334,2.136-0.912c3.44-3.551,5.335-8.23,5.335-13.177    C70.946,45.175,69.052,40.496,65.611,36.945z"/><path d="M38.812,37.06l-0.148-0.148c-0.562-0.563-1.326-0.879-2.121-0.879c-0.008,0-0.016,0-0.024,0    c-0.804,0.006-1.571,0.335-2.131,0.913c-3.439,3.55-5.333,8.229-5.333,13.176c0,4.947,1.894,9.627,5.334,13.177    c0.559,0.577,1.327,0.905,2.131,0.912c0.008,0,0.016,0,0.023,0c0.795,0,1.559-0.315,2.121-0.879l0.148-0.148    c1.158-1.158,1.173-3.03,0.035-4.208c-2.31-2.387-3.583-5.53-3.583-8.854c0-3.322,1.272-6.467,3.583-8.854    C39.986,40.09,39.971,38.217,38.812,37.06z"/></g><circle cx="50" cy="50.009" r="6.5"/> </g></svg>';
		}
	}

// Tool Tips updated 4.0.2
// central tooltip generating function
	function tooltips($content, $position='', $echo = false, $handleClass= false, $class = ''){
		// tool tip position
			if(!empty($position)){
				$L = ' L';
				
				if($position=='UL')
					$L = ' UL';
				if($position=='U')
					$L = ' U';
			}else{
				$L = null;
			}

		$output = "<span class='ajdeToolTip{$L} fa". ($handleClass? ' handle':'')." {$class}' data-d='{$content}' data-handle='{$handleClass}'></span>";

		if(!$echo)
			return $output;			
		
		echo $output;
	}
	function echo_tooltips($content, $position=''){
		$this->tooltips($content, $position,true);
	}

// Icon Selector
	function icons(){
		include_once( AJDE_EVCAL_PATH.'/assets/fonts/fa_fonts.php' );
		ob_start();?>			
		<div class='ajde_fa_icons_selector'>
			<div class="fai_in">
				<ul class="faicon_ul">
				<?php
				// $font_ passed from incldued font awesome file above
				if(!empty($font_)){
					foreach($font_ as $fa){
						echo "<li><i data-name='".$fa."' class='fa ".$fa."' title='{$fa}'></i></li>";
					}
				}
				?>						
				</ul>
			</div>
		</div>
		<?php return ob_get_clean();
	}
	function get_font_icons_data(){
		include_once( AJDE_EVCAL_PATH.'/assets/fonts/fa_fonts.php' );
		return $font_;
	}

// Import box +@version 4.0.2
	function print_import_box_html($args){
		$defaults = array(
			'box_id'=>'',
			'title'=>'',
			'message'=>'',
			'file_type'=>'.csv',
			'button_label'=> __('Upload','eventon')
		);
		$args = !empty($args)? array_merge($defaults, $args): $defaults;

		extract($args);

		?>
		<div class='evo_data_upload_window' data-id="<?php echo $box_id;?>" id='import_box' style='display:none'>
			<span id="close" class='evo_data_upload_window_close'>X</span>
			<form id="evo_settings_import_form" action="" method="POST" data-link='<?php echo AJDE_EVCAL_PATH;?> '>
					
				<h3 style='padding-bottom: 10px'><?php echo $title;?></h3>
				<p ><i><?php echo $message;?></i></p>
				
				<input style='padding: 10px 0'type="file" id="file-select" name="settings[]" multiple="" accept="<?php echo $file_type;?>" data-file_type='<?php echo $file_type;?>'>
				
				<button type="submit" id="upload_settings_button" class='evo_admin_btn btn_prime'><?php echo $button_label;?></button>
			</form>
			<p class="msg" style='display:none'><?php _e('File Uploading','eventon');?></p>
		</div>
		<?php
	}

// wp Admin Tables
	function start_table_header($id, $column_headers, $args=''){ 

		$defaults = array(
			'classes'=>'',
			'display'=>'table'
		);
		$args = !empty($args)? array_merge($defaults, $args): $defaults;
		?>
		<table id="<?php echo $id;?>" class='evo_admin_table <?php echo !empty($args['classes'])? implode(' ',$args['classes']):'';?>' style='display:<?php echo $args['display'];?>'>
			<thead width="100%">
				<tr>
					<?php
					foreach($column_headers as $key=>$value){
						// width for column
						$width = (!empty($args['width'][$key]))? 'width="'.$args['width'][$key].'px"':'';
						echo "<th id='{$key}' class='column column-{$key}' {$width}>".$value."</th>";
					}
					?>
				</tr>
			</thead>
			<tbody id='list_items' width="100%">
		<?php
	}
	function table_row($data='', $args=''){
		$defaults = array(
			'classes'=>'',
			'tr_classes'=>'',
			'tr_attr'=>'',
			'colspan'=>'none'
		);
		$args = !empty($args) ?array_merge($defaults, $args): $defaults;

		// attrs
			$tr_attr = '';
			if(!empty($args['tr_attr']) && sizeof($args['tr_attr'])>0){
				foreach($args['tr_attr'] as $key=>$value){
					$tr_attr .= $key ."='". $value ."' ";
				}
			}
		
		if($args['colspan']=='all'){
			echo "<tr class='colspan-row ".(!empty($args['tr_classes'])? implode(' ',$args['tr_classes']):'')."' ".$tr_attr.">";
			echo "<td class='column span_column ".(!empty($args['classes'])? implode(' ',$args['classes']):'')."' colspan='{$args['colspan_count']}'>".$args['content']."</td>";
		}else{
			echo "<tr class='regular-row ".(!empty($args['tr_classes'])? implode(' ',$args['tr_classes']):'')."' ".$tr_attr.">";
			foreach($data as $key=>$value){
			
				echo "<td class='column column-{$key} ".(!empty($args['classes'])? implode(' ',$args['classes']):'')."'>".$value."</td>";
			}
		}
		
		echo "</tr>";
	}
	function table_footer(){
		?>
		</tbody>
		</table>
		<?php
	}

// styles and scripts
	function register_styles_scripts(){
		wp_register_style( 'evo_elements',EVO()->assets_path.'css/lib/elements.css',array(), EVO()->version);
		wp_register_script( 'evo_elements_js',EVO()->assets_path.'js/lib/elements.js',array(), EVO()->version);
	}
	function enqueue(){
		wp_enqueue_style( 'evo_elements' );
		wp_enqueue_script( 'evo_elements_js' );
	}

// shortcode generator - only in admin side
	function register_shortcode_generator_styles_scripts(){
		wp_register_style( 'evo_shortcode_generator',EVO()->assets_path.'lib/shortcode_generator/shortcode_generator.css',array(), EVO()->version);
		wp_register_script( 'evo_shortcode_generator_js',EVO()->assets_path.'lib/shortcode_generator/shortcode_generator.js',array(), EVO()->version);
	}
	function enqueue_shortcode_generator(){
		wp_enqueue_style( 'evo_shortcode_generator' );
		wp_enqueue_script( 'evo_shortcode_generator_js' );
	}

// Color picker
	function load_colorpicker(){
		wp_enqueue_style('colorpicker_styles');
		wp_enqueue_script('backender_colorpicker');
	}
	function register_colorpicker(){
		wp_register_script('backender_colorpicker',EVO()->assets_path.'lib/colorpicker/colorpicker.js' ,array('jquery'),EVO()->version, true);
		wp_register_style( 'colorpicker_styles',EVO()->assets_path.'lib/colorpicker/colorpicker_styles.css','',EVO()->version);
	}

}