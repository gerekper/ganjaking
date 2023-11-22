<?php
/**
 *	EventON Settings Main Object
 *	@version 4.5.2
 */

class EVO_Settings{
	
	public $focus_tab, $current_section, $options_pre;
	private $tab_props = false;

	public function __construct(){
		$this->focus_tab = (isset($_GET['tab']) )? sanitize_text_field( urldecode($_GET['tab'])):'evcal_1';
		$this->current_section = (isset($_GET['section']) )? sanitize_text_field( urldecode($_GET['section'])):'';
		$this->options_pre = 'evcal_options_';
	}

// Styles and scripts
	public function load_styles_scripts(){
		wp_enqueue_media();	

		wp_enqueue_style('settings_styles');
		wp_enqueue_script('settings_script');

		EVO()->elements->load_colorpicker();
	}

	public function register_ss(){
		$this->register_styles();
		$this->register_scripts();

		EVO()->elements->register_colorpicker();
	}
	public function register_styles(){
		wp_register_style( 'settings_styles',EVO()->assets_path.'lib/settings/settings.css','',EVO()->version);		
	}

	public function register_scripts(){
		wp_register_script('settings_script',EVO()->assets_path.'lib/settings/settings.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), EVO()->version, true );
		
		EVO()->elements->register_shortcode_generator_styles_scripts();
	}

// CONTENT
	function print_page(){
		// Settings Tabs array
			$evcal_tabs = apply_filters('eventon_settings_tabs',array(
				'evcal_1'=>__('Settings', 'eventon'), 
				'evcal_2'=>__('Language', 'eventon'),
				'evcal_3'=>__('Styles', 'eventon'),
				'evcal_4'=>__('Licenses', 'eventon'),
				'evcal_5'=>__('Support', 'eventon'),
			));		
			
			// Get current tab/section
				$this->focus_tab = (isset($_GET['tab']) )? sanitize_text_field( urldecode($_GET['tab'])):'evcal_1';
				$this->current_section = (isset($_GET['section']) )? sanitize_text_field( urldecode($_GET['section'])):'';	

			// Update or add options
				$this->evo_save_settings();
				
			// Load eventon settings values for current tab
				$evcal_opt = $this->get_current_tab_values();	
			
			// activation notification
				$EVO_prod = new EVO_Product_Lic('eventon');
				if(!$EVO_prod->kriyathmakada()){

					$link = get_admin_url().'admin.php?page=eventon&tab=evcal_4';

					echo sprintf('<div class="update-nag updated"><p>%s! <a href="%s">%s</a></p></div>', 
						__('EventON is not activated, it must be activated to use','eventon'),
						$link,
						__('Enter License Now','eventon')
					);
								}

			// OTHER options
				$genral_opt = get_option('evcal_options_evcal_1');

		// TABBBED HEADER	
		$this->header_wraps(array(
			'version'=>get_option('eventon_plugin_version'),
			'title'=>__('EventON Settings','eventon'),
			'tabs'=>$evcal_tabs,
			'tab_page'=>'?page=eventon&tab=',
			'tab_attr_field'=>'evcal_meta',
			'tab_attr_pre'=>'evcal_',
			'tab_id'=>'evcal_settings'
		));	

		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings-content.php');
	}


// INITIATION
	function get_current_tab_values(){		
		$current_tab_number = substr($this->focus_tab, -1);
		EVO()->cal->reload_option_data( $this->focus_tab );

		$tab_props = $this->tab_props = EVO()->cal->get_op( $this->focus_tab  );

		return array( $current_tab_number => $tab_props );
	}
	
	function get_prop($field){
		if(!isset($this->tab_props[$field])) return false;
		return $this->tab_props[$field];
	}

// OTHER
	function header_wraps($args){
		?>
		<div class="wrap ajde_settings <?php echo $this->focus_tab;?>" id='<?php echo $args['tab_id'];?>'>
			<div class='evo_settings_header'>
				<h2 class='settings_m_header'><?php echo $args['title'];?> (ver <?php echo $args['version'];?>) <span class='evo_trig_form_save evo_admin_btn btn_blue'><?php _e('Save Changes','eventon');?></span></h2>
				<h2 class='nav-tab-wrapper' id='meta_tabs'>
					<?php					
						foreach($args['tabs'] as $key=>$val){
							
							echo "<a href='{$args['tab_page']}".$key."' class='nav-tab ".( ($this->focus_tab == $key)? 'nav-tab-active':null)." {$key}' ". 
								( (!empty($args['tab_attr_field']) && !empty($args['tab_attr_pre']))? 
									$args['tab_attr_field'] . "='{$args['tab_attr_pre']}{$key}'":'') . ">".$val."</a>";
						}			
					?>		
				</h2>
			</div>
		<?php
	}

	function settings_tab_start($args){
		?>
		<form class='evo_settings_form' method="post" action="">
			<?php settings_fields($args['field_group']); ?>
			<?php wp_nonce_field( $args['nonce_key'], $args['nonce_field'] );?>
		<div id="<?php echo $args['tab_id'];?>" class="<?php implode(' ', $args['classes']);?>">
			<div class="<?php implode(' ', $args['inside_classes']);?>">
				<?php
	}
	function settings_tab_end(){
		?></div></div><?php
	}
 

	// @updated 4.5
	function evo_save_settings(){
		$focus_tab = $this->focus_tab;
		$current_section = $this->current_section;

		if( isset($_POST['evcal_noncename']) && isset( $_POST ) ){
			if ( wp_verify_nonce( $_POST['evcal_noncename'], AJDE_EVCAL_BASENAME ) ){

				$evcal_options = array();

				
				// run through all post values
				foreach($_POST as $pf=>$pv){
					// skip fields
					if(in_array($pf, array('option_page', 'action','_wpnonce','_wp_http_referer','evcal_noncename',
					))) continue;

					// fields to skip sanitization @u 4.5.2
					$none_san_fields = apply_filters('evo_settings_non_san_fields', array('evo_ecl','evo_etl','evcal_top_fields','evcal_sort_options'), $focus_tab);

					// skip styles tab and licenes tab and non san fields
					if( $pf!='evcal_styles' && $focus_tab!='evcal_4' && !in_array($pf, $none_san_fields)){
						
						// none array values
						if( !is_array($pv) )	$pv = sanitize_text_field( $pv );

						$evcal_options[$pf] = $pv;
					}

					// non san fields
					if( in_array($pf, $none_san_fields) ){
						$evcal_options[$pf] = $_POST[ $pf ];
					}				
				}
				
				
				// General settings page - write styles to head option
				if($focus_tab=='evcal_1' && isset($_POST['evcal_css_head']) && $_POST['evcal_css_head']=='yes'){
					EVO()->evo_admin->update_dynamic_styles();
				}		

				// Hook
					do_action('evo_before_settings_saved', $focus_tab, $current_section,  $evcal_options);
					
				//language tab
					if($focus_tab=='evcal_2'){						
						
						$new_lang_opt = array();
						$_lang_version = (!empty($_GET['lang']))? sanitize_text_field($_GET['lang']): 'L1';

						// process duplicates
						foreach($evcal_options as $F=>$V){
							if(strpos($F, '_v_') !== false && !empty($V) ){
								$_F = str_replace('_v_', '', $F);

								$evcal_options[ $_F ] = $V;
							}
						}

						$lang_opt = get_option('evcal_options_evcal_2');
						if(!empty($lang_opt) ){
							$new_lang_opt[$_lang_version] = $evcal_options;
							$new_lang_opt = array_merge($lang_opt, $new_lang_opt);
						}else{
							$new_lang_opt[$_lang_version] =$evcal_options;
						}
						
						update_option('evcal_options_evcal_2', $new_lang_opt);
						
					}

				elseif($focus_tab == 'evcal_1' || empty($focus_tab)){
					// store custom meta box count
					$cmd_count = evo_calculate_cmd_count();
					$evcal_options['cmd_count'] = $cmd_count;

					update_option('evcal_options_'.$focus_tab, $evcal_options);

				// all other settings tabs
				}else{
					//do_action('evo_save_settings',$focus_tab, $evcal_options);

					$evcal_options = apply_filters('evo_save_settings_optionvals', $evcal_options, $focus_tab);
					update_option('evcal_options_'.$focus_tab, $evcal_options);
				}
				
				// STYLES
				if( isset($_POST['evcal_styles']) )
					update_option('evcal_styles', strip_tags(stripslashes($_POST['evcal_styles'])) );

				// PHP Codes
				if( isset($_POST['evcal_php']) ){
					update_option('evcal_php', strip_tags(stripslashes($_POST['evcal_php'])) );
				}

				// Hoook for when settings are saved
					do_action('evo_after_settings_saved', $focus_tab, $current_section,  $evcal_options);
				
				$_POST['settings-updated']='true';			
			
				// update dynamic styles file
					EVO()->evo_admin->generate_dynamic_styles_file();

				// update the global values with new saved settings values
				$this->tab_props = $evcal_options;
				$GLOBALS['EVO_Settings'][$this->options_pre .$focus_tab] = $this->tab_props;

			// nonce check
			}else{
				echo '<div class="notice error"><p>'.__('Settings not saved, nonce verification failed! Please try again later!','eventon').'</p></div>';
			}	
		}
	}
	
// Print Form Content
	function print_ajde_customization_form($cutomization_pg_array, $ajdePT, $extra_tabs=''){
		
		$textdomain = 'eventon';
		
		// initial variables
			$font_sizes = array('10px','11px','12px','13px','14px','16px','18px','20px', '22px', '24px','28px','30px','36px','42px','48px','54px','60px');
			$opacity_values = array('0.0','0.1','0.2','0.3','0.4','0.5','0.6','0.7','0.8','0.9','1',);
			$font_styles = array('normal','bold','italic','bold-italic');
			
			$__no_hr_types = array('begin_afterstatement','end_afterstatement','hiddensection_open','hiddensection_close','sub_section_open','sub_section_close');
		
			//define variables
			$leftside=$rightside='';
			$count=1;

		// icon selection
			$rightside.= EVO()->elements->get_icon_html();
		
		// different types of content
			/*
				notice, image, icon, subheader, note, checkbox, text. textarea, font_size, font_style, border_radius, color, fontation, multicolor, radio, dropdown, checkboxes, yesno, begin_afterstatement, end_afterstatement, hiddensection_open, hiddensection_close, customcode
			*/

		foreach($cutomization_pg_array as $cpa=>$cpav){								
			// left side tabs with different level colors
			$ls_level_code = (isset($cpav['level']))? 'class="'.$cpav['level'].'"': null;
			
			$leftside .= "<li ".$ls_level_code."><a class='".( ($count==1)?'focused':null)."' data-c_id='".$cpav['id']."' title='".$cpav['tab_name']."'><i class='fa fa-".( !empty($cpav['icon'])? $cpav['icon']:'edit')."'></i>".__($cpav['tab_name'],$textdomain)."</a></li>";								
			$tab_type = (isset($cpav['tab_type'] ) )? $cpav['tab_type']:'';
			if( $tab_type !='empty'){ // to not show the right side

				
				// RIGHT SIDE
				$display_default = (!empty($cpav['display']) && $cpav['display']=='show')?'':'display:none';
				
				$rightside.= "<div id='".$cpav['id']."' style='".$display_default."' class='nfer'>
					<h3>".__($cpav['name'],$textdomain)."</h3>";

					if(!empty($cpav['description']))
						$rightside.= "<p class='tab_description'>".$cpav['description']."</p>";
				
				$rightside.="<em class='hr_line'></em>";					
					

				// EACH field
				foreach($cpav['fields'] as $field){


					if( !isset($field['type'])) continue;

					if($field['type']=='text' || $field['type']=='textarea'){
						$FIELDVALUE = (!empty($ajdePT[ $field['id']]))? 
							htmlspecialchars( stripslashes($ajdePT[ $field['id']]) ): 
								null;
					}
					
					// LEGEND or tooltip
					$legend_code = (!empty($field['legend']) )? EVO()->elements->tooltips($field['legend'], 'L', false):
						( (!empty($field['tooltip']) )? EVO()->elements->tooltips($field['tooltip'], 'L', false): null );

					// new label
					if( isset($field['ver']) && $field['ver'] == EVO()->version){
						$legend_code .= "<span class='new' title='".__('New in version','eventon') .' '. EVO()->version."'>new</span>";
					}
									
					switch ($field['type']){
						// notices
						case 'notice':
							$rightside.= "<div class='ajdes_notice'>".__($field['name'],$textdomain)."</div>";
						break;
						//IMAGE
						case 'image':
							$image = ''; 
							$meta = isset($ajdePT[$field['id']])? $ajdePT[$field['id']]:false;
							
							$preview_img_size = (empty($field['preview_img_size']))?'medium'
								: $field['preview_img_size'];
							
							$rightside.= "<div id='pa_".$field['id']."'><p>".$field['name'].$legend_code."</p>";
							
							if ($meta) { $image = wp_get_attachment_image_src($meta, $preview_img_size); $image = $image[0]; } 
							
							$display_saved_image = (!empty($image))?'block':'none';
							$opp = ($display_saved_image=='block')? 'none':'block';

							$rightside.= "<p class='ajde_image_selector'>";
							$rightside.= "<span class='ajt_image_holder' style='display:{$display_saved_image}'><b class='ajde_remove_image'>X</b><img src='{$image}'/></span>";
							$rightside.= "<input type='hidden' class='ajt_image_id' name='{$field['id']}' value='{$meta}'/>";
							$rightside.= "<input type='button' class='ajt_choose_image button' style='display:{$opp}' value='".__('Choose an Image','ajde')."'/>";
							$rightside.= "</p></div>";
							
						break;
						
						case 'icon':
							$field_value = (!empty($ajdePT[ $field['id']]) )? 
								$ajdePT[ $field['id']]:$field['default'];

							$rightside.= "<div class='row_faicons'><p class='fieldname'>".__($field['name'],$textdomain)."</p>";
							
							// code
							$rightside .= EVO()->elements->get_element(array(
								'type'=>'icon_select',
								'id'=> $field['id'],
								'value'=> $field_value,
								'close'=>false,
							));

							/*$rightside.= "<p class='acus_line faicon'>
								<i class='fa ".$field_value."'></i>
								<input name='".$field['id']."' class='backender_colorpicker evocolorp_val' type='hidden' value='".$field_value."' /></p>";*/
							$rightside.= "<div class='clear'></div></div>";
						break;

						case 'subheader':
							$rightside.= "<h4 class='acus_subheader'>".__($field['name'],$textdomain)."</h4>";
						break;
						case 'note':
							$rightside.= "<p class='ajde_note'><i>".__($field['name'],$textdomain)."</i></p>";
						break;
						case 'hr': $rightside.= "<em class='hr_line'></em>"; break;
						case 'checkbox':
							$this_value= (!empty($ajdePT[ $field['id']]))? $ajdePT[ $field['id']]: null;						
							$rightside.= "<p><input type='checkbox' name='".$field['id']."' value='yes' ".(($this_value=='yes')?'checked="/checked"/':'')."/> ".$field['name']."</p>";
						break;
						case 'text':
							$placeholder = (!empty($field['default']) )? 'placeholder="'.$field['default'].'"':null;

							$show_val = false; $hideable_text = '';
							if(isset($field['hideable']) && $field['hideable'] && !empty($FIELDVALUE)){
								$show_val = true;
								$hideable_text = "<span class='evo_hideable_show' data-t='". __('Hide', $textdomain) ."'>". __('Show',$textdomain). "</span>";
							}
							
							$rightside.= "<p>".__($field['name'],$textdomain).$legend_code. $hideable_text. "</p><p class='field_container'><span class='nfe_f_width'>";

							if($show_val ){
								$rightside.= "<input type='password' style='' name='".$field['id']."'";
								$rightside.= 'value="'. $FIELDVALUE .'"';
							}else{
								$rightside.= "<input type='text' name='".$field['id']."'";
								$rightside.= 'value="'. $FIELDVALUE .'"';
							}
							
							$rightside.= $placeholder."/></span></p>";
						break;
						case 'password':
							$default_value = (!empty($field['default']) )? 'placeholder="'.$field['default'].'"':null;
							
							$rightside.= "<p>".__($field['name'],$textdomain).$legend_code."</p><p><span class='nfe_f_width'><input type='password' name='".$field['id']."'";
							$rightside.= 'value="'.$FIELDVALUE.'"';
							$rightside.= $default_value."/></span></p>";
						break;
						case 'textarea':
							$default_value = (!empty($field['default']) )? 'placeholder="'.$field['default'].'"':null;
							$rightside.= "<p>".__($field['name'],$textdomain).$legend_code."</p><p><span class='nfe_f_width'><textarea name='".$field['id']."' {$default_value}>".$FIELDVALUE."</textarea></span></p>";
						break;
						case 'font_size':
							$rightside.= "<p>".__($field['name'],$textdomain)." <select name='".$field['id']."'>";
								$ajde_fval = $ajdePT[ $field['id'] ];
								
								foreach($font_sizes as $fs){
									$selected = ($ajde_fval == $fs)?"selected='selected'":null;	
									$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
								}
							$rightside.= "</select></p>";
						break;
						case 'opacity_value':
							$rightside.= "<p>".__($field['name'],$textdomain)." <select name='".$field['id']."'>";
								$ajde_fval = $ajdePT[ $field['id'] ];
								
								foreach($opacity_values as $fs){
									$selected = ($ajde_fval == $fs)?"selected='selected'":null;	
									$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
								}
							$rightside.= "</select></p>";
						break;
						case 'font_style':
							$rightside.= "<p>".__($field['name'],$textdomain)." <select name='".$field['id']."'>";
								$ajde_fval = $ajdePT[ $field['id'] ];
								foreach($font_styles as $fs){
									$selected = ($ajde_fval == $fs)?"selected='selected'":null;	
									$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
								}
							$rightside.= "</select></p>";
						break;
						case 'border_radius':
							$rightside.= "<p>".__($field['name'],$textdomain)." <select name='".$field['id']."'>";
									$ajde_fval = $ajdePT[ $field['id'] ];
									$border_radius = array('0px','2px','3px','4px','5px','6px','8px','10px');
									foreach($border_radius as $br){
										$selected = ($ajde_fval == $br)?"selected='selected'":null;	
										$rightside.=  "<option value='$br' ".$selected.">$br</option>";
									}
							$rightside.= "</select></p>";
						break;
						case 'color':

							// default hex color
							$hex_color = (!empty($ajdePT[ $field['id']]) )? 
								$ajdePT[ $field['id']]:$field['default'];
							$hex_color_val = (!empty($ajdePT[ $field['id'] ]))? $ajdePT[ $field['id'] ]: null;

							// RGB Color for the color box
							$rgb_color_val = (!empty($field['rgbid']) && !empty($ajdePT[ $field['rgbid'] ]))? $ajdePT[ $field['rgbid'] ]: null;
							$__em_class = (!empty($field['rgbid']))? ' rgb': null;

							$rightside.= "<p class='acus_line color'>
								<em><span class='colorselector{$__em_class}' style='background-color:#".$hex_color."' hex='".$hex_color."' title='".$hex_color."'></span>
								<input name='".$field['id']."' class='backender_colorpicker evocolorp_val' type='hidden' value='".$hex_color_val."' default='".$field['default']."'/>";
							if(!empty($field['rgbid'])){
								$rightside .= "<input name='".$field['rgbid']."' class='rgb' type='hidden' value='".$rgb_color_val."' />";
							}
							$rightside .= "</em>".__($field['name'],$textdomain)." </p>";					
						break;					

						case 'fontation':

							$variations = $field['variations'];
							$rightside.= "<div class='row_fontation'><p class='fieldname'>".__($field['name'],$textdomain)."</p>";

							foreach($variations as $variation){
								switch($variation['type']){
									case 'color':
										// default hex color
										$hex_color = (!empty($ajdePT[ $variation['id']]) )? 
											$ajdePT[ $variation['id']]:$variation['default'];
										$hex_color_val = (!empty($ajdePT[ $variation['id'] ]))? $ajdePT[ $variation['id'] ]: null;
										
										$title = (!empty($variation['name']))? $variation['name']:$hex_color;
										$_has_title = (!empty($variation['name']))? true:false;

										// code
										$rightside.= "<p class='acus_line color'>
											<em><span id='{$variation['id']}' class='colorselector ".( ($_has_title)? 'hastitle': '')."' style='background-color:#".$hex_color."' hex='".$hex_color."' title='".$title."' alt='".$title."'></span>
											<input name='".$variation['id']."' class='backender_colorpicker evocolorp_val' type='hidden' value='".$hex_color_val."' default='".$variation['default']."'/></em></p>";

									break;

									case 'font_style':
										$rightside.= "<p style='margin:0'><select title='".__('Font Style',$textdomain)."' name='".$variation['id']."'>";
												$f1_fs = (!empty($ajdePT[ $variation['id'] ]))?
													$ajdePT[ $variation['id'] ]:$variation['default'] ;
												foreach($font_styles as $fs){
													$selected = ($f1_fs == $fs)?"selected='selected'":null;	
													$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
												}
										$rightside.= "</select></p>";
									break;

									case 'font_size':
										$rightside.= "<p style='margin:0'><select title='".__('Font Size',$textdomain)."' name='".$variation['id']."'>";
												
												$f1_fs = (!empty($ajdePT[ $variation['id'] ]))?
													$ajdePT[ $variation['id'] ]:$variation['default'] ;
												
												foreach($font_sizes as $fs){
													$selected = ($f1_fs == $fs)?"selected='selected'":null;	
													$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
												}
										$rightside.= "</select></p>";
									break;
									
									case 'opacity_value':
										$rightside.= "<p style='margin:0'><select title='".__('Opacity Value',$textdomain)."' name='".$variation['id']."'>";
												
												$f1_fs = (!empty($ajdePT[ $variation['id'] ]))?
													$ajdePT[ $variation['id'] ]:$variation['default'] ;
												
												foreach($opacity_values as $fs){
													$selected = ($f1_fs == $fs)?"selected='selected'":null;	
													$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
												}
										$rightside.= "</select></p>";
									break;
								}

								
							}
							$rightside.= "<div class='clear'></div></div>";
						break;

						case 'multicolor':

							$variations = $field['variations'];

							$rightside.= "<div class='row_multicolor' style='padding-top:10px'>";

							foreach($variations as $variation){
								// default hex color
								$hex_color = (!empty($ajdePT[ $variation['id']]) )? 
									$ajdePT[ $variation['id']]:$variation['default'];
								$hex_color_val = (!empty($ajdePT[ $variation['id'] ]))? $ajdePT[ $variation['id'] ]: null;

								$rightside.= "<p class='acus_line color'>
								<em data-name='".__($variation['name'],$textdomain)."'><span id='{$variation['id']}' class='colorselector' style='background-color:#".$hex_color."' hex='".$hex_color."' title='".$hex_color."'></span>
								<input name='".$variation['id']."' class='backender_colorpicker evocolorp_val' type='hidden' value='".$hex_color_val."' default='".$variation['default']."'/></em></p>";
							}

							$rightside.= "<p class='multicolor_alt'></p></div>";

						break;

						case 'radio':
							$rightside.= "<p class='acus_line acus_radio'>".__($field['name'],$textdomain)."</br>";
							$cnt =0;
							foreach($field['options'] as $option=>$option_val){
								$this_value = (!empty($ajdePT[ $field['id'] ]))? $ajdePT[ $field['id'] ]:null;
								
								$checked_or_not = ((!empty($this_value) && ($option == $this_value) ) || (empty($this_value) && $cnt==0) )?
									'checked=\"checked\"':null;

								$option_id = $field['id'].'_'. (str_replace(' ', '_', $option_val));
								
								$rightside.="<em><input id='".$option_id."' type='radio' name='".$field['id']."' value='".$option."' "
								.  $checked_or_not  ."/><label class='ajdebe_radio_btn' for='".$option_id."'><span class='fa'></span>".__($option_val,$textdomain)."</label></em>";
								
								$cnt++;
							}						
							$rightside.= $legend_code."</p>";
							
						break;
						case 'dropdown':
							
							$dropdown_opt = (!empty($ajdePT[ $field['id'] ]))? $ajdePT[ $field['id'] ]
								:( !empty($field['default'])? $field['default']:null);
							
							$rightside.= "<p class='acus_line {$field['id']}'>".__($field['name'],$textdomain)." <select class='ajdebe_dropdown' name='".$field['id']."'>";
							
							if(is_array($field['options'])){
								foreach($field['options'] as $option=>$option_val){
									$rightside.="<option name='".$field['id']."' value='".$option."' "
									.  ( ($option == $dropdown_opt)? 'selected=\"selected\"':null)  ."> ".$option_val."</option>";
								}	
							}					
							$rightside.= "</select>";

								// description text for this field
								if(!empty( $field['desc'] )){
									$rightside.= "<br/><i style='opacity:0.6'>".$field['desc']."</i>";
								}
							$rightside.= $legend_code."</p>";						
						break;
						case 'checkboxes':
							
							$meta_arr= (!empty($ajdePT[ $field['id'] ]) )? $ajdePT[ $field['id'] ]: null;
							$default_arr= (!empty($field['default'] ) )? $field['default']: null;

							ob_start();
							
							echo "<p class='acus_line acus_checks'><span style='padding-bottom:10px;'>".__($field['name'],$textdomain)."</span>";
							
							// foreach checkbox
							foreach($field['options'] as $option=>$option_val){
								$checked='';
								if(!empty($meta_arr) && is_array($meta_arr)){
									$checked = (in_array($option, $meta_arr))?'checked':'';
								}elseif(!empty($default_arr)){
									$checked = (in_array($option, $default_arr))?'checked':'';
								}

								// option ID
								$option_id = $field['id'].'_'. (str_replace(' ', '_', $option_val));
								
								echo "<span><input id='".$option_id."' type='checkbox' 
								name='".$field['id']."[]' value='".$option."' ".$checked."/>
								<label for='".$option_id."'><span class='fa'></span>".$option_val."</label></span>";
							}						
							echo  "</p>";

							$rightside.= ob_get_clean();
						break;

						// rearrange field
							// fields_array - array(key=>var)
							// order_var
							// selected_var
							// title
							// (o)notes
						case 'rearrange':

							ob_start();
								$_ORDERVAR = $field['order_var'];
								$_SELECTEDVAR = $field['selected_var'];
								$_FIELDSar = $field['fields_array']; // key(var) => value(name)

								
								// saved order
								if(!empty($ajdePT[$_ORDERVAR])){								
									
									$allfields_ = explode(',',$ajdePT[$_ORDERVAR]);
									$fieldsx = array();
									//print_r($allfields_);
									foreach($allfields_ as $fielders){									
										if(!in_array($fielders, $fieldsx)){
											$fieldsx[]= $fielders;
										}
									}
									//print_r($fieldsx);
									$allfields = implode(',', $fieldsx);

									$SAVED_ORDER = array_filter(explode(',', $allfields));
									
								}else{
									$SAVED_ORDER = false;
									$allfields = '';
								}

								$SELECTED = (!empty($ajdePT[$_SELECTEDVAR]))?
									( (is_array( $ajdePT[$_SELECTEDVAR] ))?
										$ajdePT[$_SELECTEDVAR]:
										array_filter( explode(',', $ajdePT[$_SELECTEDVAR]))):
									false;

								$SELECTED_VALS = (is_array($SELECTED))? implode(',', $SELECTED): $SELECTED;

								echo '<h4 class="acus_subheader">'.$field['title'].'</h4>';
								echo !empty($field['notes'])? '<p><i>'.$field['notes'].'</i></p>':'';
								echo '<input class="ajderearrange_order" name="'.$_ORDERVAR.'" value="'.$allfields.'" type="hidden"/>
									<input class="ajderearrange_selected" type="hidden" name="'.$_SELECTEDVAR.'" value="'.( (!empty($SELECTED_VALS))? $SELECTED_VALS:null).'"/>
									<div id="ajdeEVC_arrange_box" class="ajderearrange_box '.$field['id'].'">';


								// if an order array exists already
								if($SAVED_ORDER){
									// for each saved order
									foreach($SAVED_ORDER as $VAL){
										if(!isset($_FIELDSar[$VAL])) continue;

										$FF = (is_array($_FIELDSar[$VAL]))? 
											$_FIELDSar[$VAL][1]:
											$_FIELDSar[$VAL];
										echo (array_key_exists($VAL, $_FIELDSar))? 
											"<p val='".$VAL."' class='evo_data_item'><span class='fa ". ( !empty($SELECTED) && in_array($VAL, $SELECTED)?
												'':'hide') ."'></span>".$FF.
												//"<input type='hidden' name='_evo_data_fields[]' value='{$VAL}'/>".
											"</p>":	null;
									}	
									
									// if there are new values in possible items add them to the bottom
									foreach($_FIELDSar as $f=>$v){
										$FF = (is_array($v))? $v[1]:$v;
										echo (!in_array($f, $SAVED_ORDER))? 
											"<p val='".$f."'><span class='fa ". ( !empty($SELECTED) && in_array($f, $SELECTED)?'':'hide') ."'></span>".$FF."</p>": null;
									}
										
								}else{
								// if there isnt a saved order	
									foreach($_FIELDSar as $f=>$v){
										$FF = (is_array($v))? $v[1]:$v;
										echo "<p val='".$f."'><span class='fa ". ( !empty($SELECTED) && in_array($f, $SELECTED)?'':'hide') ."'></span>".$FF."</p>";
									}				
								}

								echo "</div>";

							$rightside .= ob_get_clean();

						break;
						
						case 'yesno':						
							$yesno_value = (!empty( $ajdePT[$field['id'] ]) )? 
								$ajdePT[$field['id']]:'no';
							
							$after_statement = (isset($field['afterstatement']) )?$field['afterstatement']:'';

							$__default = (!empty( $field['default'] ) && $ajdePT[$field['id'] ]!='yes' )? 
								$field['default']
								:$yesno_value;

							$rightside.= "<p class='yesno_row'>".
							EVO()->elements->yesno_btn(array('var'=>$__default,'attr'=>array('afterstatement'=>$after_statement) )).
							"<input type='hidden' name='".$field['id']."' value='".(($__default=='yes')?'yes':'no')."'/><span class='field_name'>".__($field['name'],$textdomain)."{$legend_code}</span>";

								// description text for this field
								if(!empty( $field['desc'] )){
									$rightside.= "<i style='opacity:0.6; padding-top:8px; display:block'>".$field['desc']."</i>";
								}
							$rightside .= '</p>';
						break;

						case 'yesnoALT':
						   $__default = (!empty( $field['default'] ) )?
						      $field['default']
						      :'no';

						   $yesno_value = (!empty( $ajdePT[$field['id'] ]) )?
						      $ajdePT[$field['id']]:$__default;
						   
						   $after_statement = (isset($field['afterstatement']) )?$field['afterstatement']:'';

						   $rightside.= "<p class='yesno_row'>".EVO()->elements->yesno_btn(array('var'=>$yesno_value,'attr'=>array('afterstatement'=>$after_statement) ))."<input type='hidden' name='".$field['id']."' value='".$yesno_value."'/><span class='field_name'>".__($field['name'],$textdomain)."{$legend_code}</span>";

						      // description text for this field
						      if(!empty( $field['desc'] )){
						         $rightside.= "<i style='opacity:0.6; padding-top:8px; display:block'>".$field['desc']."</i>";
						      }
						   $rightside .= '</p>';
						break;

						case 'begin_afterstatement': 
							
							$yesno_val = (!empty($ajdePT[$field['id']]))? $ajdePT[$field['id']]:'no';
							
							$rightside.= "<div class='backender_yn_sec' id='".$field['id']."' style='display:".(($yesno_val=='yes')?'block':'none')."'><div class='evosettings_field_child'>";
						break;
						case 'end_afterstatement': $rightside.= "</div><em class='hr_line evosettings_end_field'></em></div>"; break;
						
						// hidden section open
						case 'hiddensection_open':
							
							$__display = (!empty($field['display']) && $field['display']=='none')? 'style="display:none"':null;
							$__diclass = (!empty($field['display']) && $field['display']=='none')? '':'open';
							
							$rightside.="<div class='ajdeSET_hidden_open {$__diclass}'><h4>{$field['name']}{$legend_code}</h4></div>
							<div class='ajdeSET_hidden_body' {$__display}><div class='evo_in'>";
							
						break;					
						case 'hiddensection_close':	$rightside.="</div></div>";	break;

						case 'sub_section_open':
							$rightside.="<div class='evo_settings_subsection'><h4 class='acus_subheader'>".__($field['name'],$textdomain)."</h4><div class='evo_in'>";
						break;
						case 'sub_section_close': $rightside.="</div></div>";
							if( isset($field['em']) && $field['em'])	$rightside.= "<em class='hr_line'></em>'";
						break;
						
						// custom code
						case 'customcode':						
							$rightside .= (!empty($field['code'])? $field['code']:'');						
						break;
					}
					if(!empty($field['type']) && !in_array($field['type'], $__no_hr_types) ){ 
						$rightside.= "<em class='hr_line'></em>";}
					
				}		
				$rightside.= "</div>";//<!-- nfer-->
			}
			$count++;
		}
		
		//built out the backender section
		ob_start();
		?>
		<table id='ajde_customization'>
			<tr><td class='backender_left' valign='top'>
				<div id='acus_left'>
					<ul><?php echo $leftside ?></ul>								
				</div>
				<div class="ajde-collapse-menu" id='collapse-button'>
					<span class="collapse-button-icon"></span>
					<span class="collapse-button-label" style='font-size:12px;'><?php _e('Collapse Menu','eventon');?></span>
				</div>
				</td><td class='evo_settings_right' width='100%'  valign='top'>
					<div id='acus_right' class='ajde_backender_uix'>
						<p id='acus_arrow' style='top:14px'></p>
						<div class='customization_right_in'>
							<div style='display:none' id='ajde_color_guide'>Loading</div>
							<div id='ajde_clr_picker' class="cp cp-default" style='display:none'></div>
							<?php echo $rightside.$extra_tabs;?>
						</div>

						<div class='evo_diag actual'>
							<!-- save settings -->
							<input type="submit" class="evo_admin_btn btn_prime btn_blue" value="<?php _e('Save Changes') ?>" /> <a id='resetColor' style='display:none' class='evo_admin_btn btn_secondary'><?php _e('Reset to default colors','eventon')?></a>
						</div>	

					</div>
				</td>
			</tr>
		</table>	
		<?php
		echo ob_get_clean();
		
	}

// Event Edit Settings
// @since 4.2.1 @updated 4.5
	function get_event_edit_settings($data){
		ob_start();

		$args = array(
			'hidden_fields'=> array(),
			'form_class'=>'',
			'container_class'=>'',
			'fields'=> array(),
			'save_btn_data'=> array(),
			'footer_btns'=> array(
				'save_changes'=> array(
					'label'=> __('Save Changes','eventon'),
					'data'=> array(),
					'class'=> 'evo_btn evolb_trigger_save',
					'href'=>'',
					'target'=> ''
				)
			)
		);

		$args = array_merge($args, $data);
		extract($args);
		?>
		<div class='<?php echo $container_class;?>'>
			<form class='<?php echo $form_class;?>'>
				<?php 
				foreach($hidden_fields as $k=>$v){
					echo "<input type='hidden' name='{$k}' value='{$v}'>";
				}

				echo EVO()->elements->process_multiple_elements( $fields );

				?>
				<p>					
					<?php 
					foreach( $footer_btns as $btn):
						if(!isset( $btn['label'] )) continue;
						$href = isset($btn['href']) && !empty( $btn['href'] )? 'href="'. $btn['href'] .'"':'';
						$target = isset($btn['target']) && !empty( $btn['target'] ) ? 'target="'. $btn['target'] .'"' : '';

						?><a <?php echo $href; echo $target;?> class='<?php echo $btn['class'];?>' data-d='<?php echo json_encode($btn['data']);?>' style=''><?php echo $btn['label'];?></a>
					<?php endforeach;?>
					
				</p>	
			</form>
		</div>
		<?php 
		return ob_get_clean();
	}
	function print_event_edit_box_yn_header($data){
		extract( array_merge(array(
			'value'=> '',
			'id'=>'',
			'name'=>'',
			'tooltip'=>'',
			'reload_btn'=>false,
			'afterstatement'=>'',
			'reload_id'=> '',
			'eid'=>''
		), $data));
		?>
		<p class='evoadmin_eventedit_boxhead yesno_leg_line ' style='padding:10px'>
			<?php 
			echo EVO()->elements->yesno_btn(array(
				'id'=> $id,
				'var'=> $value, 
				'guide'=> $tooltip,
				'label'=> $name,
				'afterstatement'=> $afterstatement,
				'input'=> true,
			));

			if( !empty($reload_id)){
				echo "<span class='evoadmin_eventedit_reloadbox' data-id='$reload_id' data-eid='{$eid}'><i class='fa fa-refresh'></i></span>";
			}

			?>			
		</p>
		<?php 
	}

// deprecating
	function save_settings($nonce_key, $nonce_field, $options_pre){
		if( isset($_POST[$nonce_field]) && isset( $_POST ) ){
			if ( wp_verify_nonce( $_POST[$nonce_field], $nonce_key ) ){
				foreach($_POST as $pf=>$pv){
					$pv = (is_array($pv))? $pv: addslashes(esc_html(stripslashes(($pv)))) ;
					$options[$pf] = $pv;
				}
				EVO()->cal->set_cur( $this->focus_tab );
				EVO()->cal->set_option_values( $options );
			}
		}
	}

}