<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
*/

if(!defined('ABSPATH')) exit();

class RsTransitionpackSliderFront extends RevSliderFunctions {
	
	private $version,
			$pluginUrl,
			$pluginTitle;
			
	public function __construct($version, $pluginUrl, $pluginTitle, $isAdmin = false) {
		$this->version     = $version;
		$this->pluginUrl   = $pluginUrl;
		$this->pluginTitle = $pluginTitle;
		
		add_action('revslider_slider_init_by_data_post', array($this, 'check_addon_active'), 10, 1); //load always, for previews in backend
		if($isAdmin){
			add_action('admin_footer', array($this, 'write_footer_scripts'));
			add_action('wp_footer', array($this, 'write_footer_scripts')); //needed for previews
		}
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 2);
		add_action('revslider_get_slider_wrapper_div', array($this, 'check_if_ajax_loaded'), 10, 2);
		add_filter('revslider_get_slider_html_addition', array($this, 'add_html_script_additions'), 10, 2);
		add_action('revslider_export_html_write_footer', array($this, 'write_export_footer'), 10, 1);
		add_filter('revslider_export_html_file_inclusion', array($this, 'add_addon_files'), 10, 2);
		/*add_filter('script_loader_tag', array($this, 'add_attribute'), 10, 2);*/
	}
	
	public function write_export_footer($export){
		$output = $export->slider_output;
		$array = $this->add_html_script_additions(array(), $output);
		$toload = $this->get_val($array, 'toload', array());
		if(!empty($toload)){
			foreach($toload as $script){
				echo $script;
			}
		}
		$this->write_footer_scripts();
	}

	public function add_addon_files($html, $export){
		$output = $export->slider_output;
		$addOn = $this->isEnabled($output->slider);
		if(empty($addOn)) return $html;

		$_jsPathMin = file_exists(RS_TRANSITIONPACK_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		if(!$export->usepcl){
			$export->zip->addFile(RS_PLUGIN_PATH . 'public/assets/js/libs/three.min.js', 'js/three.min.js');
			$export->zip->addFile(RS_TRANSITIONPACK_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js');
			$export->zip->addFile(RS_TRANSITIONPACK_PLUGIN_PATH . 'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css', 'css/revolution.addon.' . $this->pluginTitle . '.css');
		}else{
			$export->pclzip->add(RS_PLUGIN_PATH.'public/assets/js/libs/three.min.js', PCLZIP_OPT_REMOVE_PATH, RS_PLUGIN_PATH.'public/assets/js/libs/', PCLZIP_OPT_ADD_PATH, 'js/');
			$export->pclzip->add(RS_TRANSITIONPACK_PLUGIN_PATH.'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', PCLZIP_OPT_REMOVE_PATH, RS_TRANSITIONPACK_PLUGIN_PATH.'public/assets/js/', PCLZIP_OPT_ADD_PATH, 'js/');
			$export->pclzip->add(RS_TRANSITIONPACK_PLUGIN_PATH.'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css', PCLZIP_OPT_REMOVE_PATH, RS_TRANSITIONPACK_PLUGIN_PATH.'public/assets/css/', PCLZIP_OPT_ADD_PATH, 'css/');
		}

		$html = str_replace($this->pluginUrl.'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css', 'css/revolution.addon.' . $this->pluginTitle . '.css', $html);
		$html = str_replace($this->pluginUrl.'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin .'.js', $export->path_js .'revolution.addon.' . $this->pluginTitle . $_jsPathMin .'.js', $html);
		$html = str_replace(RS_PLUGIN_URL.'public/assets/js/libs/three.min.js', $export->path_js .'three.min.js', $html);
		
		return $html;
	}


	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		
		return true;
	}
	
	private function isEnabled($slider) {
		$slides = $slider->get_slides();
		if(empty($slides)) return false;
		
		$transitions = json_decode(RsAddOnTransitionpackBase::add_transitions(''), true);
		$tpack = $this->get_val($transitions, 'tpack', array());

		foreach($slides as $slide){
			if($this->get_val($slide, array('params', 'slideChange', 'eng'), false) === 'transitionPack') return true;
		}
		foreach($slides as $slide){
			$altSlideChange = $this->get_val($slide, array('params', 'slideChange', 'alt'), false);
			if($altSlideChange === false || empty($altSlideChange)) continue;

			foreach($tpack as $k => $v){
				if(!is_array($v)) continue;
				foreach($altSlideChange as $asc){
					if(isset($v[$asc]) && $this->get_val($v, array($asc, 'eng'), false) === 'transitionPack') return true;
				}
			}
		}
		
		return false;
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
		$handle = 'rs-' . $this->pluginTitle;
		$base = RS_TRANSITIONPACK_PLUGIN_URL . 'public/assets/';		
		$_jsPathMin = file_exists(RS_TRANSITIONPACK_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';

		//ADDON CORE CSS AND JS
		wp_enqueue_style($handle.'-css',$base . 'css/revolution.addon.' . $this->pluginTitle . '.css', array(), $this->version);				
		wp_enqueue_script($handle.'-js',$base . 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', array('revmin'), $this->version);
		
		wp_enqueue_script('revbuilder-threejs', RS_PLUGIN_URL . 'public/assets/js/libs/three.min.js', array('revmin'), RS_REVISION);
		
		add_action('wp_footer', array($this, 'write_footer_scripts'));
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
		$_jsPathMin = file_exists(RS_TRANSITIONPACK_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		$return['toload']['tpack'] = '<script'. $addition .' src="'. RS_TRANSITIONPACK_PLUGIN_URL . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js"></script>';
		$return['toload']['threejs'] = '<script'. $addition .' src="'. RS_PLUGIN_URL . 'public/assets/js/libs/three.min.js"></script>';
		
		return $return;
	}
	
	public function add_waiting_script_slugs($wait){
		$wait[] = 'tpack';
		$wait[] = 'threejs';
		return $wait;
	}
	
	public function write_footer_scripts(){
		echo '<script type="text/javascript">window.RVS = window.RVS || {}; window.RVS.ENV = window.RVS.ENV || {}; window.RVS.ENV.TRANSITIONPACK_URL = "'.RS_TRANSITIONPACK_PLUGIN_URL.'";</script>'."\n";
	}
	
	public function check_if_ajax_loaded($r, $output) {
		$me = $output->get_markup_export();
		if($me !== true && $output->ajax_loaded !== true) return $r;
		
		$addOn = $this->isEnabled($output->slider);
		if(empty($addOn)) return $r;
		
		$html = '<link rel="stylesheet" href="'. RS_TRANSITIONPACK_PLUGIN_URL . 'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css">'."\n";
		
		return $html . $r;
	}
	
	public function write_init_script($slider, $id) {
		$addOn = $this->isEnabled($slider);
		if(empty($addOn)) return false;
		
		$id = $slider->get_id();
		
		echo "\n";
		echo "\t\t\t\t\t\t" . 'if (revapi'.$id.' !== undefined) jQuery.fn.revolution.transitionpackInit(revapi'.$id.'[0].id, {url:"'.RS_TRANSITIONPACK_PLUGIN_URL.'"});' . "\n";
		echo "\t\t\t\t\t\t window.TP_SHDRS = window.TP_SHDRS || {};". "\n";
		
		//add script by slide settings
		$slides = $slider->get_slides();
		if(empty($slides)) return false;
		
		$transitions = json_decode(RsAddOnTransitionpackBase::add_transitions(''), true);
		$transitions = $this->get_val($transitions, 'tpack', array());
		
		$shaders = RsAddOnTransitionpackBase::get_shaders();
		$add_shaders = array();
		foreach($slides as $slide){
			$tpack = $slide->get_param(array('slideChange','addOns','tpack'), array());
			$eng = $slide->get_param(array('slideChange','eng'), '');
			if($eng === 'transitionPack' && !empty($tpack)){
				$ef = $this->get_val($tpack, 'ef', 'fade');
				$ef = $ef==="fadeb" ? "fade" : $ef;
				$add_shaders[$ef] = $ef;
			}

			$altSlideChange = $this->get_val($slide, array('params', 'slideChange', 'alt'), false);
			if(!empty($altSlideChange)){
				foreach($altSlideChange as $asc){
					foreach($transitions as $k => $v){
						if(!is_array($v)) continue;
						if(isset($v[$asc]) && $this->get_val($v, array($asc, 'eng'), false) === 'transitionPack'){
							$ef = $this->get_val($v[$asc], array('addOns','tpack','ef'), 'fade');
							$ef = $ef==="fadeb" ? "fade" : $ef;
							$add_shaders[$ef] = $ef;
							break;
						}
					}
				}
			}
		}
		
		if(!empty($add_shaders)){
			foreach($add_shaders as $_shader){
				$b = "\t\t\t\t\t\t window.TP_SHDRS.".$_shader." =    window.TP_SHDRS.".$_shader." || ";
				$shader = $this->get_val($shaders, $_shader, '');
				if(!empty($shader)) {						
					echo $b.$shader.";\n";
				}
			}
		}
	}
	
	public function add_attribute($tag, $handle){
		return (!in_array($handle, array('rs-transitionpack-front-js'))) ? $tag : str_replace(' src', ' type="module" src', $tag);
	}
}
?>