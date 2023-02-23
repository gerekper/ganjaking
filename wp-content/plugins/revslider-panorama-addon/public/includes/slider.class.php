<?php
/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2021 ThemePunch
*/

if(!defined('ABSPATH')) exit();
class RsPanoramaSliderFront extends RevSliderFunctions {
	
	private $version,
			$pluginUrl, 
			$pluginTitle;
			
	public function __construct($version, $pluginUrl, $pluginTitle, $isAdmin = false) {
		$this->version     = $version;
		$this->pluginUrl   = $pluginUrl;
		$this->pluginTitle = $pluginTitle;
		
		//add_action('revslider_add_slider_base_post', array($this, 'write_ajax_script'), 10, 1);
		
		add_action('revslider_slider_init_by_data_post', array($this, 'check_addon_active'), 10, 1);
		if($isAdmin){
			//add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
		}
		add_action('revslider_fe_javascript_output', array($this, 'write_init_script'), 10, 2);
		add_action('revslider_get_slider_wrapper_div', array($this, 'check_if_ajax_loaded'), 10, 2);
		add_filter('revslider_get_slider_html_addition', array($this, 'add_html_script_additions'), 10, 2);
		add_action('revslider_export_html_write_footer', array($this, 'write_export_footer'), 10, 1);
		add_filter('revslider_export_html_file_inclusion', array($this, 'add_addon_files'), 10, 2);
		
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
	}

	public function add_addon_files($html, $export){
		$output = $export->slider_output;
		$addOn = $this->isEnabled($output->slider);
		if(empty($addOn)) return $html;

		$_jsPathMin = file_exists(RS_PANORAMA_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		if(!$export->usepcl){
			$export->zip->addFile(RS_PLUGIN_PATH . 'public/assets/js/libs/three.min.js', 'js/three.min.js');
			$export->zip->addFile(RS_PANORAMA_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js');
			$export->zip->addFile(RS_PANORAMA_PLUGIN_PATH . 'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css', 'css/revolution.addon.' . $this->pluginTitle . '.css');
		}else{
			$export->pclzip->add(RS_PLUGIN_PATH.'public/assets/js/libs/three.min.js', PCLZIP_OPT_REMOVE_PATH, RS_PLUGIN_PATH.'public/assets/js/libs/', PCLZIP_OPT_ADD_PATH, 'js/');
			$export->pclzip->add(RS_PANORAMA_PLUGIN_PATH.'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', PCLZIP_OPT_REMOVE_PATH, RS_PANORAMA_PLUGIN_PATH.'public/assets/js/', PCLZIP_OPT_ADD_PATH, 'js/');
			$export->pclzip->add(RS_PANORAMA_PLUGIN_PATH.'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css', PCLZIP_OPT_REMOVE_PATH, RS_PANORAMA_PLUGIN_PATH.'public/assets/css/', PCLZIP_OPT_ADD_PATH, 'css/');
		}
		$html = str_replace($this->pluginUrl.'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css', 'css/revolution.addon.' . $this->pluginTitle . '.css', $html);
		$html = str_replace($this->pluginUrl.'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin .'.js', $export->path_js .'revolution.addon.' . $this->pluginTitle . $_jsPathMin .'.js', $html);
		$html = str_replace(RS_PLUGIN_URL.'public/assets/js/libs/three.min.js', $export->path_js .'three.min.js', $html);
		

		//check for all slides to add and replace images
		$slides = $output->get_current_slides();
		$front = new RsPanoramaSlideFront($this->pluginTitle);

		if(!empty($slides)){
			$plugins_url = plugins_url('', dirname(__FILE__));
			$upload_folder = wp_upload_dir();
			$upload_url = $this->get_val($upload_folder, 'baseurl');
			$upload_path = $this->get_val($upload_folder, 'basedir');
			
			foreach($slides as $slide){
				$addOn = $front->isEnabled($slide);
				if(empty($addOn)) continue;

				$params = $slide->get_params();
				$bg = $this->get_val($params, 'bg', array());
				$type = $this->get_val($bg, 'type', 'trans');
				if($type !== 'image') continue;
				$image = $this->get_val($bg, 'image', '');
				if(empty($image)) continue;

				$file = str_replace($upload_url, '', $image);
				$add = $upload_path.$file;
				$basename = basename($add);
				if(!$export->usepcl){
					$export->zip->addFile($add, 'assets/'.$basename);
				}else{
					$base = dirname($add);
					$export->pclzip->add($add, PCLZIP_OPT_REMOVE_PATH, $base, PCLZIP_OPT_ADD_PATH, 'assets/');
				}

				$image_slashed = str_replace('/', '\/', $image);
				$html = str_replace($image, 'assets/'.$basename, $html);
				$html = str_replace($image_slashed, 'assets\/'.$basename, $html);
			}
		}

		return $html;
	}

	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		
		return true;
	}
	
	private function isEnabled($slider){
		if(empty($slider)) return false;

		$settings = $slider->get_params();
		if(empty($settings)) return false;
		
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
			if($this->get_val($slide, array('params', 'addOns', 'revslider-panorama-addon', 'enable'), false) === true){
				$enabled = true;
				break;
			}
		}
		
		return $enabled;
	}
	
	/*private function isEnabled($slider) {
		$settings = $slider->get_params();
		if(empty($settings)) return false;
		
		$addOns = $this->get_val($settings, 'addOns', false);
		if(empty($addOns)) return false;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->pluginTitle . '-addon', false);
		if(empty($addOn)) return false;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return false;
		
		return $addOn;
	}*/
	
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
		$path = $base . 'js/revolution.addon.' . $this->pluginTitle . '.min.js';
		$_jsPathMin = file_exists(RS_PANORAMA_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		wp_enqueue_style($handle, $base . 'css/revolution.addon.' . $this->pluginTitle . '.css', array(), $this->version);
		wp_enqueue_script('revbuilder-threejs', RS_PLUGIN_URL . 'public/assets/js/libs/three.min.js', array(), RS_REVISION);
		
		wp_enqueue_script($handle, $base . 'js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js', array('jquery', 'revbuilder-threejs'), $this->version, true);
		
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
		$_jsPathMin = file_exists(RS_PANORAMA_PLUGIN_PATH . 'public/assets/js/revolution.addon.' . $this->pluginTitle . '.js') ? '' : '.min';
		
		$return['toload']['panorama'] = '<script'. $addition .' src="'. $this->pluginUrl . 'public/assets/js/revolution.addon.' . $this->pluginTitle . $_jsPathMin . '.js"></script>';
		$return['toload']['threejs'] = '<script'. $addition .' src="'. RS_PLUGIN_URL . 'public/assets/js/libs/three.min.js"></script>';
		
		return $return;
	}
	
	public function add_waiting_script_slugs($wait){
		$wait[] = 'panorama';
		$wait[] = 'threejs';
		return $wait;
	}
	
	public function check_if_ajax_loaded($r, $output) {
		$me = $output->get_markup_export();
		if($me !== true && $output->ajax_loaded !== true) return $r;
		
		$addOn = $this->isEnabled($output->slider);
		if(empty($addOn)) return $r;
		
		$html = '<link rel="stylesheet" href="'. $this->pluginUrl . 'public/assets/css/revolution.addon.' . $this->pluginTitle . '.css">'."\n";
		return $html . $r;
	}
	
	public function write_init_script($slider, $id) {

		// addon enabled
		$enabled = $this->isEnabled($slider);
		if(!empty($enabled)) {

			$id = $slider->get_id();
			echo "						\n";
			echo '						if(typeof RsAddonPanorama !== "undefined") RsAddonPanorama(tpj, revapi' . $id . ');'."\n";

		}

	}
	
	/*public function write_ajax_script($output){
		$enabled = $this->isEnabled($output->slider);
		
		if(!empty($enabled)) {
			if($output->ajax_loaded === true){
				$base   = $this->pluginUrl . 'public/assets/';
				echo '						<link rel="stylesheet" href="'.$base . 'css/revolution.addon.' . $this->pluginTitle . '.css?ver='.$this->version.'" type="text/css" media="alL" />'."\n";
				echo '						<script type="text/javascript" src="'.$base . 'js/three.min.js?ver='.$this->version.'"></script>'."\n";
				echo '						<script type="text/javascript" src="'.$base . 'js/revolution.addon.' . $this->pluginTitle . '.min.js?ver='.$this->version.'"></script>'."\n";
			}
		}
	}*/
	
}
?>