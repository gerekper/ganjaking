<?php
/* EventON Shortcode Generator
* @version 2.9
* use: EVO()->shortcode_gen
*/

class EVO_Shortcode_Generator{
	private $_in_select_step=false;

	public function __construct(){
		include_once('class-shortcode-data.php');
		$this->data = new EVO_Shortcode_Data();
	}

	// GET Generator 
		public function get_content(){
			$EVO = new EVO_Product('eventon');

			if(!$EVO->kriyathmakada()) 
				return '<p style="padding:10px;text-align:center">'.$EVO->akriyamath_niwedanaya() .'</p>';
			
			return $this->get_inside(
				$this->data->get_shortcode_field_array(),
				'add_eventon'
			);
		}

	// depreciation connects
		function shortcode_default_field($A){
			return $this->data->shortcode_default_field($A);
		}


	// Get HTML of inside the generator
		public function get_inside_html($var){
			global $ajde;

			// initial values
				$line_class = array('fieldline');

			ob_start();		
			
			// GUIDE popup
			$guide = (!empty($var['guide']))? EVO()->elements->tooltips($var['guide'], 'L',false):null;

			// afterstatemnt class
			if(!empty($var['afterstatement'])){	$line_class[]='trig_afterst'; }

			// select step class
			if($this->_in_select_step){ $line_class[]='ss_in'; }

			if(!empty($var['type'])):

			switch($var['type']){
				// custom type and its html pluggability
				case has_action("ajde_shortcode_box_interpret_{$var['type']}"):
					do_action("ajde_shortcode_box_interpret_{$var['type']}", $var, $guide);
				break;
				case 'YN':
					$line_class[]='ajdeYN_row';

					echo "<div class='".implode(' ', $line_class)."'>";
					echo EVO()->elements->yesno_btn(array(
						'var'=>$var['var'],
						'default'=>( ($var['default']=='no')? 'NO':null ),
						'guide'=>(!empty($var['guide'])? $var['guide']:''), 
						'guide_position'=>(!empty($var['guide_position'])? $var['guide_position']:'L'),
						'label'=>$var['name'],
						'abs'=>'yes',
						'attr'=>array('codevar'=>$var['var'])
						));
					echo "</div>";					
				break;

				case 'customcode':	echo !empty($var['value'])? $var['value']:'';	break;
				
				case 'note':
					$line_class[]='note';
					echo 
					"<div class='".implode(' ', $line_class)."'><p class='label'>".$var['name']."</p></div>";
				break;
				case 'collapsable':
					$line_class[] = 'collapsable';
					if( isset($var['closed']) && $var['closed'] ) $line_class[] = 'closed';
					echo 
					"<div style='' class='".implode(' ', $line_class)."'><p class='label subheader'>".$var['name']."</p></div><div class='collapsable_fields' style='display:". ( ( isset($var['closed']) && $var['closed'] )? 'none':'')."'>";
				break;
				case 'subheader':
					echo 
					"<div style='background-color:#f7f7f7' class='".implode(' ', $line_class)."'><p class='label subheader'>".$var['name']."</p></div>";
				break;
				case 'text':
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'><input class='ajdePOSH_input' type='text' codevar='".$var['var']."' placeholder='".( (!empty($var['placeholder']))?$var['placeholder']:null) ."'/> ".$var['name']."".$guide."</p>
					</div>";
				break;

				case 'fmy':
					$line_class[]='fmy';
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'>
							<input class='ajdePOSH_input short' type='text' codevar='fixed_month' placeholder='eg. 11' title='Month'/><input class='ajdePOSH_input short' type='text' codevar='fixed_year' placeholder='eg. 2014' title='Year'/> ".$var['name']."".$guide."</p>
					</div>";
				break;
				case 'fdmy':
					$line_class[]='fdmy';
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'>
							<input class='ajdePOSH_input short shorter' type='text' codevar='fixed_date' placeholder='eg. 31' title='Date'/><input class='ajdePOSH_input short shorter' type='text' codevar='fixed_month' placeholder='eg. 11' title='Month'/><input class='ajdePOSH_input short shorter' type='text' codevar='fixed_year' placeholder='eg. 2014' title='Year'/> ".$var['name']."".$guide."</p>
					</div>";
				break;
				
				case 'taxonomy':
					echo EVO()->elements->get_element( array(
						'type'=> 'lightbox_select_vals',
						'field_class'=> 'ajdePOSH_input',
						'name'=> $var['name'],
						'default'=> (!empty($var['placeholder'])? $var['placeholder']:null),
						'taxonomy'=> $var['var'],
						'reverse_field'=>true,
						'row_class'=> 'fieldline',
						'field_attr'=> array(
							'codevar'=>$var['var']
						)
					));
				break;
				case 'select_in_lightbox':
					echo EVO()->elements->get_element( array(
						'type'=> 'lightbox_select_cus_vals',
						'field_class'=> 'ajdePOSH_input',
						'name'=> $var['name'],
						'default'=> (!empty($var['placeholder'])? $var['placeholder']:null),
						'options'=> $var['options'],
						'reverse_field'=>true,
						'row_class'=> 'fieldline',
						'field_attr'=> array(
							'codevar'=>$var['var']
						)
					));
				break;
				
				case 'select':
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'>
							<select class='ajdePOSH_select' codevar='".$var['var']."'>";
							$default = (!empty($var['default']))? $var['default']: null;
							foreach($var['options'] as $valf=>$val){
								echo "<option value='".$valf."' ".( $default==$valf? 'selected="selected"':null).">".$val."</option>";
							}						
							echo 
							"</select> ".$var['name']."".$guide."</p>
					</div>";
				break;

				// select steps
				case 'select_step':
					$line_class[]='select_step_line';
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label '>
							<select class='ajdePOSH_select_step' codevar='".$var['var']."'>";
							
							foreach($var['options'] as $f=>$val){
								echo (!empty($val))? "<option value='".$f."'>".$val."</option>":null;
							}		
							echo 
							"</select> ".__($var['name'],'eventon')."".$guide."</p>
					</div>";
				break;

				case 'open_select_steps':
					echo "<div id='".$var['id']."' class='ajde_open_ss select_step_".$var['id']."' style='display:none' data-step='".$var['id']."' >";
					$this->_in_select_step=true;	// set select step section to on
				break;

				case 'close_select_step':	echo "</div>";	$this->_in_select_step=false; break;
				case 'close_div':	echo "</div>"; break;
				
			}// end switch

			endif;

			// afterstatement
			if(!empty($var['afterstatement'])){
				echo "<div class='ajde_afterst ".$var['afterstatement']."' style='display:none'>";
			}

			// closestatement
			if(!empty($var['closestatement'])){
				echo "</div>";
			}
			
			return ob_get_clean();
		}
	// get the HTML content for the shortcode generator
		public function get_inside($shortcode_guide_array, $base_shortcode){
			global $ajde;
				
			$__text_a = __('Select option below to customize shortcode variable values','eventon');
			ob_start();

			?>		
				<div id='ajdePOSH_outter' class='<?php echo $base_shortcode;?>'>
					<h3 class='notifications '><em id='ajdePOSH_back' class='fa'></em><span id='ajdePOSH_subtitle' data-section='' data-bf='<?php echo $__text_a;?>'><?php echo $__text_a;?></span></h3>
					<div class='ajdePOSH_inner'>
						<div class='step1 steps'>
						<p style='    background-color: #ff896e; color: #fff;padding: 10px; font-size: 12px;display: none'><?php _e('WARNING! If you are interchangeably using shortcode parameters between other calendar shortcodes, bare in mind, that the shortcode parameters not available in its shortcode options may not be fully supported!','eventon');?></p>
						<?php					
							foreach($shortcode_guide_array as $options){
								$__step_2 = (empty($options['variables']))? ' nostep':null;
								
								echo "<div class='ajdePOSH_btn{$__step_2}' step2='".$options['id']."' code='".$options['code']."'>".$options['name']."</div>";
							}	
						?>				
						</div>
						<div class='step2 steps' >
							<?php
								foreach($shortcode_guide_array as $options){
									if(!empty($options['variables']) ) {
										echo "<div id='".$options['id']."' data-code='{$options['code']}' class='step2_in' style='display:none'>";										
										// each shortcode option variable row
										foreach($options['variables'] as $var){
											echo $this->get_inside_html($var);
										}	echo "</div>";
									}
								}						
							?>					
						</div><!-- step 2-->
						<div class='clear'></div>
					</div>
					<div class='ajdePOSH_footer'>
						<p id='ajdePOSH_var_'></p>
						<p id='ajdePOSH_code' data-defsc='<?php echo $base_shortcode;?>' data-curcode='<?php echo $base_shortcode;?>' code='<?php echo $base_shortcode;?>' >[<?php echo $base_shortcode;?>]</p>
						<span class='ajdePOSH_insert' title='Click to insert shortcode'></span>
					</div>
				</div>
			
			<?php
			return ob_get_clean();
		
		}

}

//$GLOBALS['evo_shortcode_box'] = EVO()->shortcode_gen;