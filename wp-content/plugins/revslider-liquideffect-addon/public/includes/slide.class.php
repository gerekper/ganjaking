<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsLiquidEffectSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
	
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
	
	private function removeSuffix($st) {
	
		return str_replace(array('px', 'ms', 'deg', '%'), '', $st);
	
	}
	
	public function write_slide_attributes($slider, $slide) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;
		
		$addOn = $this->isEnabled($slide);
		if(empty($addOn)) return;
		
		$imagesize = 'external';
		$params = $slide->get_params();
		$bg = $this->get_val($params, 'bg', array());
		$bgtype = $this->get_val($bg, 'type', 'trans');
		
		if($bgtype !== 'image' && $bgtype !== 'external') return;
		if($bgtype !== 'external') {
		
			$imageURL = $this->get_val($bg, 'image', false);
			if(empty($imageURL)) return;
			
			$imageID = $this->get_image_id_by_url($imageURL);
			if($imageID) {
				$bgsize = $this->get_val($slide, 'imageSourceType', 'full');
				$image = wp_get_attachment_image_src($imageID, $bgsize);
				if($image) $imagesize = $image[1] . '|' . $image[2];
			}
			
		}
		
		$map_ar = $this->get_val($addOn, 'map', array());
		$animation_ar = $this->get_val($addOn, 'animation', array());
		$transition_ar = $this->get_val($addOn, 'transition', array());
		$interaction_ar = $this->get_val($addOn, 'interaction', array());
		
		$imagemap = $this->get_val($map_ar, 'image', 'ripple');
		$autoplay = $this->get_val($animation_ar, 'enable', false);
		$transition  = $this->get_val($transition_ar, 'enable', false);
		$interactive = $this->get_val($interaction_ar, 'enable', false);
		
		$autoplay = !$this->isFalse($autoplay);
		$transition = !$this->isFalse($transition);
		$interactive = !$this->isFalse($interactive);
		
		if($imagemap !== 'Custom Map') {
			
			$size = $this->get_val($map_ar, 'size', 'Large');
			$imagemap = plugins_url('assets/images/' . strtolower($imagemap) . '_' . strtolower($size) . '.jpg', dirname(__FILE__));
			
		}
		else {
			
			$imagemap = $this->get_val($map_ar, 'custom', '');
			if(empty($imagemap)) $imagemap = plugins_url('assets/images/ripple.jpg', dirname(__FILE__));
			
		}
		
		$settings = array(
			
			'image' => $imagemap,
			'imagesize' => $imagesize,
			
		);
		
		if($autoplay) {
			
			$speedx = $this->get_val($animation_ar, 'speedx', '10');
			$speedy = $this->get_val($animation_ar, 'speedy', '3');
			
			$scalex = $this->get_val($animation_ar, 'scalex', '20');
			$scaley = $this->get_val($animation_ar, 'scaley', '20');
			
			$rotationx = $this->get_val($animation_ar, 'rotationx', '10');
			$rotationy = $this->get_val($animation_ar, 'rotationy', '0');
			$rotation  = $this->get_val($animation_ar, 'rotation', '0');
			
			$scalex = $this->removeSuffix($scalex);
			if(!is_numeric($scalex)) $scalex = '20';
			$scalex = floatval($scalex);
			
			$scaley = $this->removeSuffix($scaley);
			if(!is_numeric($scaley)) $scaley = '20';
			$scaley = floatval($scaley);
			
			$speedx = $this->removeSuffix($speedx);
			if(!is_numeric($speedx)) $speedx = '10';
			$speedx = floatval($speedx);
			
			$speedy = $this->removeSuffix($speedy);
			if(!is_numeric($speedy)) $speedy = '3';
			$speedy = floatval($speedy);
			
			$rotationx = $this->removeSuffix($rotationx);
			if(!is_numeric($rotationx)) $rotationx = '10';
			$rotationx = floatval($rotationx);
			
			$rotationy = $this->removeSuffix($rotationy);
			if(!is_numeric($rotationy)) $rotationy = '0';
			$rotationy = floatval($rotationy);
			
			$rotation = $this->removeSuffix($rotation);
			if(!is_numeric($rotation)) $rotation = '0';
			$rotation = floatval($rotation);
			
			$settings['autoplay']  = true;
			$settings['scalex']    = $scalex;
			$settings['scaley']    = $scaley;
			$settings['speedx']    = $speedx;
			$settings['speedy']    = $speedy;
			$settings['rotationx'] = $rotationx;
			$settings['rotationy'] = $rotationy;
			$settings['rotation']  = $rotation;
			
		}
		else {
			
			$settings['autoplay']  = false;
			$settings['scalex']    = 0;
			$settings['scaley']    = 0;
			$settings['speedx']    = 0;
			$settings['speedy']    = 0;
			$settings['rotationx'] = 0;
			$settings['rotationy'] = 0;
			$settings['rotation']  = 0;
			
		}
		
		if($transition) {
			
			$transcross = $this->get_val($transition_ar, 'cross', false);
			$transpower = $this->get_val($transition_ar, 'power', false);
			
			$transcross = !$this->isFalse($transcross);
			$transpower = !$this->isFalse($transpower);
			
			$transtime  = $this->get_val($transition_ar, 'duration', '1000');
			$easing     = $this->get_val($transition_ar, 'easing', 'Power3.easeOut');
			
			$transitionx = $this->get_val($transition_ar, 'scalex', '200');
			$transitiony = $this->get_val($transition_ar, 'scaley', '70');
			
			$transpeedx = $this->get_val($transition_ar, 'speedx', '0');
			$transpeedy = $this->get_val($transition_ar, 'speedy', '100');
			
			$transrotx = $this->get_val($transition_ar, 'rotationx', '20');
			$transroty = $this->get_val($transition_ar, 'rotationy', '0');
			$transrot  = $this->get_val($transition_ar, 'rotation',  '0');
			
			$transtime = $this->removeSuffix($transtime);
			if(!is_numeric($transtime)) $transtime = '1000';
			$transtime = intval($transtime);
			if(!$transtime) $transtime = 1000;
			
			$transitionx = $this->removeSuffix($transitionx);
			if(!is_numeric($transitionx)) $transitionx = '200';
			$transitionx = floatval($transitionx);
			
			$transitiony = $this->removeSuffix($transitiony);
			if(!is_numeric($transitiony)) $transitiony = '70';
			$transitiony = floatval($transitiony);
			
			$transpeedx = $this->removeSuffix($transpeedx);
			if(!is_numeric($transpeedx)) $transpeedx = '0';
			$transpeedx = floatval($transpeedx);
			
			$transpeedy = $this->removeSuffix($transpeedy);
			if(!is_numeric($transpeedy)) $transpeedy = '100';
			$transpeedy = floatval($transpeedy);
			
			$transrotx = $this->removeSuffix($transrotx);
			if(!is_numeric($transrotx)) $transrotx = '20';
			$transrotx = floatval($transrotx);
			
			$transroty = $this->removeSuffix($transroty);
			if(!is_numeric($transroty)) $transroty = '0';
			$transroty = floatval($transroty);
			
			$transrot = $this->removeSuffix($transrot);
			if(!is_numeric($transrot)) $transrot = '0';
			$transrot = floatval($transrot);
			
			$settings['transtime']   = $transtime;
			$settings['easing']      = $easing;
			$settings['transcross']  = $transcross;
			$settings['transpower']  = $transpower;
			$settings['transitionx'] = $transitionx;
			$settings['transitiony'] = $transitiony;
			$settings['transpeedx']  = $transpeedx;
			$settings['transpeedy']  = $transpeedy;
			$settings['transrotx']   = $transrotx;
			$settings['transroty']   = $transroty;
			$settings['transrot']    = $transrot;
			
		}
		else {
			
			$settings['transtime']   = 2000;
			$settings['easing']      = 'Power3.easeOut';
			$settings['transcross']  = false;
			$settings['transpower']  = false;
			$settings['transitionx'] = 0;
			$settings['transitiony'] = 0;
			$settings['transpeedx']  = 0;
			$settings['transpeedy']  = 0;
			$settings['transrotx']   = 0;
			$settings['transroty']   = 0;
			$settings['transrot']    = 0;
			
		}
		
		if($interactive) {
			
			$event        = $this->get_val($interaction_ar, 'event', 'click');
			$intertime    = $this->get_val($interaction_ar, 'duration', '1000');
			$intereasing  = $this->get_val($interaction_ar, 'easing', 'Power3.easeOut');
			$interscalex  = $this->get_val($interaction_ar, 'scalex', '200');
			$interscaley  = $this->get_val($interaction_ar, 'scaley', '70');
			$interotation = $this->get_val($interaction_ar, 'rotation', '180');
			$interspeedx  = $this->get_val($interaction_ar, 'speedx', '0');
			$interspeedy  = $this->get_val($interaction_ar, 'speedy', '0');
			$mobile       = $this->get_val($interaction_ar, 'disablemobile', false);
			
			$mobile = !$this->isFalse($mobile);
			
			$intertime = $this->removeSuffix($intertime);
			if(!is_numeric($intertime)) $intertime = '1000';
			$intertime = intval($intertime);
			if(!$intertime) $intertime = 1000;
			
			$interscalex = $this->removeSuffix($interscalex);
			if(!is_numeric($interscalex)) $interscalex = '200';
			$interscalex = floatval($interscalex);
			
			$interscaley = $this->removeSuffix($interscaley);
			if(!is_numeric($interscaley)) $interscaley = '70';
			$interscaley = floatval($interscaley);
			
			$interotation = $this->removeSuffix($interotation);
			if(!is_numeric($interotation)) $interotation = '180';
			$interotation = floatval($interotation);
			
			$interspeedx = $this->removeSuffix($interspeedx);
			if(!is_numeric($interspeedx)) $interspeedx = '0';
			$interspeedx = floatval($interspeedx);
			
			$interspeedy = $this->removeSuffix($interspeedy);
			if(!is_numeric($interspeedy)) $interspeedy = '0';
			$interspeedy = floatval($interspeedy);
			
			$settings['interactive']  = true;
			$settings['event']        = $event;
			$settings['mobile']       = $mobile;
			$settings['intertime']    = $intertime;
			$settings['intereasing']  = $intereasing;
			$settings['interscalex']  = $interscalex;
			$settings['interscaley']  = $interscaley;
			$settings['interotation'] = $interotation;
			$settings['interspeedx']  = $interspeedx;
			$settings['interspeedy']  = $interspeedy;
			
		}
		
		echo " data-liquideffect='" . json_encode($settings) . "'";
		
	}
	
}
?>