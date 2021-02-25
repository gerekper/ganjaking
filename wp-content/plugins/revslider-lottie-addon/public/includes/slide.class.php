<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2020 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsLottieSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 3);
		add_filter('revslider_putCreativeLayer', array($this, 'check_lottie'), 10, 3);
		add_filter('rs_action_output_layer_action', array($this, 'write_layer_actions'), 10, 6);
	}
	
	// HANDLE ALL TRUE/FALSE
	private function isFalse($val) {
	
		if(empty($val)) return true;
		if($val === true || $val === 'on' || $val === 1 || $val === '1' || $val === 'true') return false;
		return true;
	
	}
	
	private function isEnabled($slider) {
		
		$settings = $slider->get_params();
		if(empty($settings)) return false;
		
		$addOns = $this->get_val($settings, 'addOns', false);
		if(empty($addOns)) return false;
		
		$addOn = $this->get_val($addOns, 'revslider-' . $this->title . '-addon', false);
		if(empty($addOn)) return false;
		
		$enabled = $this->get_val($addOn, 'enable', false);
		if($this->isFalse($enabled)) return false;
		
		return $addOn;
	
	}
	
	// removes lottie layers that may exist if the AddOn is not officially enabled
	public function check_lottie($layers, $output, $static_slide) {
		
		$slider = $this->get_val($output, 'slider', false);
		if(empty($slider)) return;
			
		// addon enabled
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) {
			
			$ar = array();
			foreach($layers as $layer) {
				
				$isLottie = false;
				if(array_key_exists('subtype', $layer)) {
					
					$lottie = $this->get_val($layer, 'subtype', false);
					$isLottie = $lottie === 'lottie';
					
				}
				
				if(!$isLottie) $ar[] = $layer;
				
			}
			
			return $ar;
			
		}

		return $layers;
		
	}
	
	private function convertSizes($val, $def) {
		
		$d = $this->get_val($val, 'd', array());
		$m = $this->get_val($val, 'm', array());
		$n = $this->get_val($val, 'n', array());
		$t = $this->get_val($val, 't', array());
		
		$d = $this->get_val($d, 'v', $def);
		$m = $this->get_val($m, 'v', $def);
		$n = $this->get_val($n, 'v', $def);
		$t = $this->get_val($t, 'v', $def);
		
		return array($d, $m, $n, $t);
		
	}
	
	public function write_layer_actions($events, $action, $all_actions, $num, $slide,$output) {		
		$act = $this->get_val($action, 'action');
		$target = $this->get_val($action, 'layer_target', '');
    	$layer_attribute_id = $output->get_layer_attribute_id($target);
		switch($act) {
			case "lottie_play":
				$events[] = array(							
							'o'		=> $this->get_val($action, 'tooltip_event', ''),
							'a'		=> 'lottieplay',
							'layer'	=> $layer_attribute_id,
							'd'		=> $this->get_val($action, 'action_delay', '')							
						);
			break;
			case "lottie_pause":
				$events[] = array(							
							'o'		=> $this->get_val($action, 'tooltip_event', ''),
							'a'		=> 'lottiepause',
							'layer'	=> $layer_attribute_id,
							'd'		=> $this->get_val($action, 'action_delay', '')							
						);
			break;
			case "lottie_restart":
				$events[] = array(							
							'o'		=> $this->get_val($action, 'tooltip_event', ''),
							'a'		=> 'lottierestart',
							'layer'	=> $layer_attribute_id,
							'd'		=> $this->get_val($action, 'action_delay', '')							
						);
			break;
		}
		
		return $events;
	}

	public function write_layer_attributes($layer, $slide, $slider) {
		
		// addon enabled
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$subtype = $this->get_val($layer, 'subtype', '');
		if($subtype && $subtype === 'lottie') {
			
			$addOns = $this->get_val($layer, 'addOns', array());
			$addOn = $this->get_val($addOns, 'revslider-' . $this->title . '-addon', false);
			if(empty($addOn)) return;
				
			$config = $this->get_val($addOn, 'config', array());
			$interaction = $this->get_val($addOn, 'interaction', array());
			$renderer   = $this->get_val($addOn, 'renderer', array());
			$editor   = $this->get_val($addOn, 'editor', array());

			$jsonUrl = $this->get_val($config, 'jsonUrl', '');
			$duration = $this->get_val($config, 'duration', 1000);
			$autoplay = $this->get_val($config, 'autoplay', true);
			$respectTlStart = $this->get_val($config, 'respectTlStart', false);
			$reverse = $this->get_val($config, 'reverse', 'false');
			$endlessLoop = $this->get_val($config, 'endlessLoop', 0);
			$repeat = $this->get_val($config, 'repeat', false);
			

			$type = $this->get_val($renderer, 'type', 'SVG');
			$size = $this->get_val($renderer, 'size', "cover");
			$progressiveLoad = $this->get_val($renderer, 'progressiveLoad', false);
			$hideTransparent = $this->get_val($renderer, 'hideTransparent', false);
			
			$interType = $this->get_val($interaction, 'type', "disabled");
			$lerp = $this->get_val($interaction, 'lerp', 0);
			$easeType = $this->get_val($interaction, 'easeType', "default");
			$continue = $this->get_val($interaction, 'continue', false);			
			$scrollActions = $this->get_val($interaction, 'actions', false);
			$scrollLerp = $this->get_val($interaction, 'scrollLerp', 0.1);

			$editorEnabled = $this->get_val($editor, 'enabled', false);
			$editorMeta = $this->get_val($editor, 'meta', false);

			$options = array();

			$options["jsonUrl"] = $jsonUrl;
			$options["duration"] = $duration;
			$options["autoplay"] = $autoplay;
			$options["respectTlStart"] = $respectTlStart;
			$options["endlessLoop"] = $endlessLoop;
			$options["reverse"] = $reverse;
			$options["repeat"] = $repeat;

			$options["type"] = $type;
			$options["size"] = $size;
			$options["progressiveLoad"] = $progressiveLoad;
			$options["hideTransparent"] = $hideTransparent;

			$options["interType"] = $interType;
			$options["lerp"] = $lerp;
			$options["easeType"] = $easeType;
			$options["continuePlaying"] = $continue;
			$options['scrollActions'] = $scrollActions;
			$options['scrollLerp'] = $scrollLerp;

			$options['editorEnabled'] = $editorEnabled;
			if($editorEnabled){
				$options['meta'] = $editorMeta;
			}			
			
			echo "\t\t\t\t\t\t\t\t" . "data-lottie='" . json_encode($options, JSON_HEX_APOS) . "' " . "\n";
		
		}
	
	}
	
}
?>