<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsBeforeAfterSliderFront extends RevSliderFunctions {
	
	private $version,
			$pluginUrl, 
			$pluginTitle;
			
	public function __construct($version, $pluginUrl, $pluginTitle, $isAdmin = false) {
		$this->version     = $version;
		$this->pluginUrl   = $pluginUrl;
		$this->pluginTitle = $pluginTitle;
		
		add_action('revslider_slider_init_by_data_post', array($this, 'check_addon_active'), 10, 1);
		if($isAdmin){
			//add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
		}
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 2);
		//add_action('revslider_export_html_write_header', array($this, 'write_export_header'), 10, 1);
		add_action('revslider_export_html_write_footer', array($this, 'write_export_footer'), 10, 1);
		add_filter('revslider_export_html_file_inclusion', array($this, 'add_addon_files'), 10, 2);
		add_action('revslider_get_slider_wrapper_div', array($this, 'check_if_ajax_loaded'), 10, 2);
		add_filter('revslider_get_slider_html_addition', array($this, 'add_html_script_additions'), 10, 2);
		//add_filter('revslider_html_export_replace_urls', array($this, 'add_html_script_search'), 10, 1);
		//add_filter('revslider_html_export_path_replace_urls', array($this, 'add_html_script_path_search'), 10, 1);
		
	}

	/*public function add_html_script_search($search){
		$search[] = $this->pluginUrl;
		return $search;
	}*/

	/*public function add_html_script_path_search($path){
		$path[] = RS_BEFOREAFTER_PLUGIN_PATH;
		return $path;
	}*/

	public function add_addon_files($html, $export){
		$output = $export->slider_output;
		$addOn = $this->isEnabled($output->slider);
		if(empty($addOn)) return $html;

		$_jsPathMin = file_exists(RS_BEFOREAFTER_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		if(!$export->usepcl){
			$export->zip->addFile(RS_BEFOREAFTER_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js');
			$export->zip->addFile(RS_BEFOREAFTER_PLUGIN_PATH . 'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css', 'css/revolution.addon.' . $this->pluginTitle . '.css');

			$export->zip->addFile(RS_PLUGIN_PATH . 'public/assets/fonts/font-awesome/css/font-awesome.css', 'css/font-awesome.css');
		}else{
			$export->pclzip->add(RS_BEFOREAFTER_PLUGIN_PATH.'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', PCLZIP_OPT_REMOVE_PATH, RS_BEFOREAFTER_PLUGIN_PATH.'public/assets/js/', PCLZIP_OPT_ADD_PATH, 'js/');
			$export->pclzip->add(RS_BEFOREAFTER_PLUGIN_PATH.'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css', PCLZIP_OPT_REMOVE_PATH, RS_BEFOREAFTER_PLUGIN_PATH.'public/assets/css/', PCLZIP_OPT_ADD_PATH, 'css/');

			$export->pclzip->add(RS_PLUGIN_PATH.'public/assets/fonts/font-awesome/css/font-awesome.css', PCLZIP_OPT_REMOVE_PATH, RS_PLUGIN_PATH.'public/assets/css/', PCLZIP_OPT_ADD_PATH, 'css/');
		}

		$html = str_replace(RS_PLUGIN_URL.'public/assets/fonts/font-awesome/css/font-awesome.css', 'css/font-awesome.css', $html);
		$html = str_replace(RS_BEFOREAFTER_PLUGIN_URL.'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css', 'css/revolution.addon.' . $this->pluginTitle . '.css', $html);
		$html = str_replace(array(RS_BEFOREAFTER_PLUGIN_URL.'public/assets/js/revolution.addon.' . $this->pluginTitle . '.min.js', RS_BEFOREAFTER_PLUGIN_URL.'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js'), $export->path_js .'revolution.addon.' . $this->pluginTitle . '.js', $html);
		
		return $html;
	}

	/*public function write_export_header($export){
		$output = $export->slider_output;
	}*/

	public function write_export_footer($export){
		$output = $export->slider_output;
		$array = $this->add_html_script_additions(array(), $output);
		$toload = $this->get_val($array, 'toload', array());
		if(!empty($toload)){
			foreach($toload as $script){
				echo $script;
			}
		}
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		
		return true;
	}
	
	private function isEnabled($slider) {
		$settings = $slider->get_params();
		
		$addOns = $this->get_val($settings, 'addOns', false);
		if(empty($addOns)) return false;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->pluginTitle . '-addon', false);
		if(empty($addOn)) return false;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return false;

		$slides = $slider->get_slides();
		if(empty($slides)) return false;
		
		$enabled = false;
		foreach($slides as $slide){
			if($this->get_val($slide, array('params', 'addOns', 'revslider-beforeafter-addon', 'enable'), false) === true){
				$enabled = true;
				break;
			}					
		}

		if ($enabled) return $addOn;
		else return false;
	}
	
	public function check_addon_active($record) {
		if(empty($record)) return $record;
		// addon enabled
		$addOn = $this->isEnabled($record);
		if(empty($addOn)) return $record;
		
		$this->add_scripts();
		remove_action('revslider_slider_init_by_data_post', array($this, 'check_addon_active'), 10);
		
		return $record;
		
	}
	
	public function add_scripts() {
		$handle = 'rs-' . $this->pluginTitle . '-front';
		$base   = $this->pluginUrl . 'public/assets/';
		$_jsPathMin = file_exists(RS_BEFOREAFTER_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		wp_enqueue_style('rs-icon-set-fa-icon-', RS_PLUGIN_URL . 'public/assets/fonts/font-awesome/css/font-awesome.css', array(), RS_REVISION);
		
		wp_enqueue_style($handle, $base . 'css/revolution.addon.' . $this->pluginTitle . '.css', array(), $this->version);
		wp_enqueue_script($handle, $base . 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', array('jquery'), $this->version, true);

		add_filter('revslider_modify_waiting_scripts', array($this, 'add_waiting_script_slugs'), 10, 1);
	}
	
	public function add_html_script_additions($return, $output){
		if($output instanceof RevSliderSlider){
			$addOn = $this->isEnabled($output);
			if(empty($addOn)) return $return;
		}else{
			$me = $output->get_markup_export();
			if($me !== true && $output->ajax_loaded !== true) return $return;
			
			$addOn = $this->isEnabled($output->slider);
			if(empty($addOn)) return $return;
		}
		
		$waiting = array();
		$waiting = $this->add_waiting_script_slugs($waiting);
		if(!empty($waiting)){
			if(!isset($return['waiting'])) $return['waiting'] = array();
			foreach($waiting as $wait){
				$return['waiting'][] = $wait;
			}
		}
		
		$global = $output->get_global_settings();
		$addition = ($output->_truefalse($output->get_val($global, array('script', 'defer'), false)) === true) ? ' async="" defer=""' : '';
		$_jsPathMin = file_exists(RS_BEFOREAFTER_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		$return['toload']['beforeafter'] = '<script'. $addition .' src="'. $this->pluginUrl . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js"></script>';
		
		return $return;
	}

	public function add_waiting_script_slugs($wait){
		$wait[] = 'beforeafter';
		return $wait;
	}
	
	public function check_if_ajax_loaded($r, $output) {
		$me = $output->get_markup_export();
		if($me !== true && $output->ajax_loaded !== true) return $r;
		
		$addOn = $this->isEnabled($output->slider);
		if(empty($addOn)) return $r;
		
		$html = '<link rel="stylesheet" href="'. RS_PLUGIN_URL . 'public/assets/fonts/font-awesome/css/font-awesome.css">'."\n";
		$html .= '<link rel="stylesheet" href="'. $this->pluginUrl . 'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css">'."\n";
		return $html . $r;
	}
	
	public function write_init_script($slider, $id) {
		// enabled from slider settings
		$addOn = $this->isEnabled($slider);
		if(empty($addOn)) return;
		
		// check to see if at least one individual slide is enabled
		$slides = $slider->get_slides();
		foreach($slides as $slide) {
			
			$params = $slide->get_params();
			$addOns = $this->get_val($params, 'addOns', array());
			
			$beforeafter = $this->get_val($addOns, 'revslider-' . $this->pluginTitle . '-addon', array());
			$enabled = $this->get_val($beforeafter, 'enable', false);
			$enabled = !$this->isFalse($enabled);
			
			if(!empty($enabled)) break;
		}
		
		if(!empty($enabled)) {
			$icon = $this->get_val($addOn, 'icon', array());
			$drag = $this->get_val($addOn, 'drag', array());
			$divider = $this->get_val($addOn, 'divider', array());
			$onclick_ar = $this->get_val($addOn, 'onclick', array());
		
			$id             = $slider->get_id();
			$cursor         = $this->get_val($onclick_ar, 'cursor', 'pointer');
			$arrow_left     = $this->get_val($icon, 'left', 'fa-caret-left');
			$arrow_right    = $this->get_val($icon, 'right', 'fa-caret-right');
			$arrow_top      = $this->get_val($icon, 'up', 'fa-caret-up');
			$arrow_bottom   = $this->get_val($icon, 'down', 'fa-caret-down');
			
			$arrow_size     = $this->get_val($icon, 'size', '28');
			$arrow_color    = $this->get_val($icon, 'color', '#ffffff');
			$arrow_bg       = $this->get_val($drag, 'bgcolor', 'transparent');
			$arrow_padding  = $this->get_val($drag, 'padding',  '0');
			$arrow_spacing  = $this->get_val($icon, 'space',  '3');
			$arrow_radius   = $this->get_val($drag, 'radius',   '0');
			$divider_size   = $this->get_val($divider, 'size',   '1');
			$divider_color  = $this->get_val($divider, 'color',  '#ffffff');
			
			$divider_shadow_ar = $this->get_val($divider, 'shadow', array());
			$divider_shadow = $this->get_val($divider_shadow_ar, 'set', false);
			$divider_shadow = !$this->isFalse($divider_shadow);
			
			$arrow_shadow_ar = $this->get_val($icon, 'shadow', array());
			$arrow_shadow = $this->get_val($arrow_shadow_ar, 'set', false);
			$arrow_shadow = !$this->isFalse($arrow_shadow);
			
			$arrow_border_ar = $this->get_val($drag, 'border', array());
			$arrow_border = $this->get_val($arrow_border_ar, 'set', false);
			$arrow_border = !$this->isFalse($arrow_border);
			
			$arrow_boxshadow_ar = $this->get_val($drag, 'boxshadow', array());
			$arrow_boxshadow = $this->get_val($arrow_boxshadow_ar, 'set', false);
			$arrow_boxshadow = !$this->isFalse($arrow_boxshadow);
			
			$onclick = $this->get_val($onclick_ar, 'set', false);
			$onclick = !$this->isFalse($onclick);
			
			$params = $slider->get_params();
			$carousel = $this->get_val($params, 'type', 'standard') !== 'carousel' ? 'false' : 'true';
			
			echo "\n";
			echo '						if(typeof RevSliderBeforeAfter !== "undefined") {' . "\n";
			echo '    						RevSliderBeforeAfter(revapi' . $id . ', {' . "\n";
			echo '        						arrowStyles: {' . "\n";
			echo '            						leftIcon: "'     . $arrow_left    . '",' . "\n";
			echo '            						rightIcon: "'    . $arrow_right   . '",' . "\n";
			echo '            						topIcon: "'      . $arrow_top     . '",' . "\n";
			echo '            						bottomIcon: "'   . $arrow_bottom  . '",' . "\n";
			echo '            						size: "'         . $arrow_size    . '",' . "\n";
			echo '            						color: "'        . $arrow_color   . '",' . "\n";
			echo '            						bgColor: "'      . $arrow_bg      . '",' . "\n";
			echo '            						spacing: "'      . $arrow_spacing . '",' . "\n";
			echo '            						padding: "'      . $arrow_padding . '",'  . "\n";
			echo '            						borderRadius: "' . $arrow_radius  . '"'  . "\n";
			echo '        						},' . "\n";
			echo '        						dividerStyles: {' . "\n";
			echo '            						width: "' . $divider_size . '",' . "\n";
			echo '            						color: "' . $divider_color . '"' . "\n";
			echo '        						}';
			
			if(!empty($arrow_shadow)) {
				
				$color    = $this->get_val($arrow_shadow_ar, 'color', 'rgba(0, 0, 0, 0.35)');
				$blur     = $this->get_val($arrow_shadow_ar, 'blur', '10');
				
				echo ',' . "\n";
				echo '        						arrowShadow: {' . "\n";
				echo '            						color: "' . $color . '",' . "\n";
				echo '            						blur: "' . $blur . '"' . "\n";
				echo '        						}';
				
			}
			
			if(!empty($arrow_boxshadow)) {
				
				$strength = $this->get_val($arrow_boxshadow_ar, 'strength', '3');
				$color    = $this->get_val($arrow_boxshadow_ar, 'color', 'rgba(0, 0, 0, 0.35)');
				$blur     = $this->get_val($arrow_boxshadow_ar, 'blur', '10');
				
				echo '					,' . "\n";
				echo '        						boxShadow: {' . "\n";
				echo '            						strength: "' . $strength . '",' . "\n";
				echo '            						color: "' . $color . '",' . "\n";
				echo '            						blur: "' . $blur . '"' . "\n";
				echo '        						}';
				
			}
			
			if(!empty($arrow_border)) {
				
				$size  = $this->get_val($arrow_border_ar, 'size', '1');
				$color = $this->get_val($arrow_border_ar, 'color', '#000000');
				
				echo ',' . "\n";
				echo '        						arrowBorder: {' . "\n";
				echo '            						size: "' . $size . '",' . "\n";
				echo '            						color: "' . $color . '"' . "\n";
				echo '        						}';
				
			}
			
			if(!empty($divider_shadow)) {

				$strength = $this->get_val($divider_shadow_ar, 'strength', '3');
				$color    = $this->get_val($divider_shadow_ar, 'color', 'rgba(0, 0, 0, 0.35)');
				$blur     = $this->get_val($divider_shadow_ar, 'blur', '10');
				
				echo ',' . "\n";
				echo '        						dividerShadow: {' . "\n";
				echo '            						strength: "' . $strength . '",' . "\n";
				echo '            						color: "' . $color . '",' . "\n";
				echo '            						blur: "' . $blur . '"' . "\n";
				echo '        						}';
				
			}
			
			if(!empty($onclick)) {
				
				$time   = $this->get_val($onclick_ar, 'time', '300');
				$easing = $this->get_val($onclick_ar, 'easing', 'Power2.easeOut');
				
				echo ',' . "\n";
				echo '        						onClick: {' . "\n";
				echo '            						time: "'   . $time   . '",' . "\n";
				echo '            						easing: "' . $easing . '"' . "\n";
				echo '        						}';
				
			}
			
			echo ',' . "\n";
			echo '        						cursor: "' . $cursor . '",' . "\n";
			echo '        						carousel: ' . $carousel . "\n";
			echo '    						});'."\n";
			echo '						}' . "\n";
			
		}
		
	}
	
}
?>