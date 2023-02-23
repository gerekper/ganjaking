<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class RsMousetrapSlideFront extends RevSliderFunctions {
	
	private $title;
	
	public function __construct($title) {
		
		$this->title = $title;
		add_action('revslider_add_li_data', array($this, 'write_slide_attributes'), 10, 3);
		add_filter('revslider_add_layer_classes', array($this, 'write_layer_classes'), 10, 4);
		add_filter('rs_action_output_layer_action', array($this, 'write_layer_actions'), 10, 6);
		add_action('revslider_add_layer_attributes', array($this, 'write_layer_attributes'), 10, 4);
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
	
	public function write_layer_classes($c, $layer, $slide, $slider) {
		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return $c;
				
		$addOn = $this->get_val($layer, 'addOns', array());
		$addOn = $this->get_val($addOn, 'revslider-' . $this->title . '-addon', array());
		if(empty($addOn)) return $c;

		$follow = $this->get_val($addOn, array('follow', 'mode'),'disabled');
		if ($follow!=="disabled") $c[] = 'rs-mtrap';

		return $c;
	}
	
	public function write_layer_actions($events, $action, $all_actions, $num, $slide,$output) {		
		$act = $this->get_val($action, 'action');
		$target = $this->get_val($action, 'layer_target', '');
    	$layer_attribute_id = $output->get_layer_attribute_id($target);
		switch($act) {
			case "mtrap_follow":
				$events[] = array(							
							'o'		=> $this->get_val($action, 'tooltip_event', ''),
							'a'		=> 'mtrapfollow',
							'layer'	=> $layer_attribute_id,
							'd'		=> $this->get_val($action, 'action_delay', '')							
						);
			break;
			case "mtrap_unfollow":
				$events[] = array(							
							'o'		=> $this->get_val($action, 'tooltip_event', ''),
							'a'		=> 'mtrapunfollow',
							'layer'	=> $layer_attribute_id,
							'd'		=> $this->get_val($action, 'action_delay', '')							
						);
			break;
		}
		
		return $events;
	}

	public function write_layer_attributes($layer, $slide, $slider,$oclass) {
		
		$enabled = $this->isEnabled($slider);		
		
		if(empty($enabled)) return;
				
		$addOn = $this->get_val($layer, 'addOns', array());
		$addOn = $this->get_val($addOn, 'revslider-' . $this->title . '-addon', array());
		if(empty($addOn)) return;

		$follow = $this->get_val($addOn, array('follow', 'mode'),'disabled');
		if ($follow=="disabled") return;

		$rsf = new RevSliderFunctions();		
		$haseffects = false;
		$hidepointer = $this->get_val($addOn, array('follow', 'pointer'),true);
		$olayer_id = array();
		if ($follow==="olayer") {
			$layers = $oclass->get_layers();			
			$olayers = $this->get_val($addOn, array('follow', 'olayer'));			
			foreach($olayers as $olayer) {
				foreach($layers as $_layer){
					$lid = $this->get_val($_layer, 'uid');
					if($lid !== intval($olayer)) continue;
					//change current layer in output class for a second here
					$oclass->set_layer($_layer);
					$oclass->set_layer_unique_id();
					$olayer_id[] = $oclass->get_html_layer_ids(true);
					//go back to what the layer should be again in the output class
					$oclass->set_layer($layer);
					$oclass->set_layer_unique_id();
					break;
				}
			}			
		}
		
		$blockx = $this->get_val($addOn, array('follow', 'blockx'),false);
		$blocky = $this->get_val($addOn, array('follow', 'blocky'),false);
		$delay = $this->get_val($addOn, array('follow','delay'),0);		
		$ease = $this->get_val($addOn,  array('follow','ease'),'none');
		
		$mcenter = $this->get_val($addOn, array('mcenter'),false);
		$revert = $this->get_val($addOn, array('revert', 'use'),false);
		$rspeed = $this->get_val($addOn, array('revert','speed'),0);
		$rease = $this->get_val($addOn,  array('revert','ease'),'none');

		$offsetx = $this->get_val($addOn, array('offset', 'x'));
		$offsety = $this->get_val($addOn, array('offset', 'y'));
		$moveradius = $this->get_val($addOn, array('follow', 'radius'));

		if ($oclass->adv_resp_sizes) {
			$ox = $this->normalize_device_settings($offsetx, $oclass->enabled_sizes, 'html-array', array('0', '0px'));
			$oy = $this->normalize_device_settings($offsety, $oclass->enabled_sizes, 'html-array', array('0', '0px'));
			$mr = $this->normalize_device_settings($moveradius, $oclass->enabled_sizes, 'html-array', array('0', '0px'));
		} else {
			$ox = $this->get_biggest_device_setting($offsetx, $oclass->enabled_sizes);
			$oy = $this->get_biggest_device_setting($offsety, $oclass->enabled_sizes);
			$mr = $this->get_biggest_device_setting($moveradius, $oclass->enabled_sizes);
		}
		
		$settings = '';
		if ($follow==="olayer") {
			$suff = 1;
			foreach ($olayer_id as $olayerid) {
				$settings .='ola'.$suff.':'.$olayerid.';';
				$suff++;
			}
		}
		
		if ($follow!=='slider') $settings .= 'f:'.$follow.';';
		if ($delay!=0) $settings .='d:'.$delay.';';		
		if ($ease!='none') $settings .='e:'.$ease.';';
		if ($hidepointer==false) $settings .='h:f;';	
		$settings .='mr:'.$mr.';';	
		if ($blockx!==false) $settings .='bx:t;';
		if ($blocky!==false) $settings .='by:t;';	
		
		if ($revert==true) {
			$settings .='r:t;';
			if ($rspeed!=0) $settings .='rs:'.$rspeed.';';
			if ($rease!='none') $settings .='re:'.$rease.';';
		}

		if ($mcenter==true) $settings.='mc:t;';
		$rules = array("rx","ry","rz","sx","sy","op");
		foreach ($rules as $key => $value) {
			$temp_d = $this->get_val($addOn, array('rules', $value, 'axis'),'none'); 
			if ($temp_d!=='none') {
				$temp_min = $this->get_val($addOn, array('rules', $value, 'min'),0);
				$temp_max = $this->get_val($addOn, array('rules', $value, 'max'),0);
				if ($temp_min==0 && $temp_max==0) {
					if ($temp_d==='both' || $temp_d==='center') {
						$haseffects = true;
						$settings .= $value.'d:'.($temp_d==='both' ? 'b' : 'c').';';
					}
					// NOTHING TO DO
				} else {
					$haseffects = true;
					$temp_d = $temp_d === "horizontal" ? "h" : "v";
					$temp_o = $this->get_val($addOn, array('rules', $value, 'offset'),0);					
					$temp_c = $this->get_val($addOn, array('rules', $value, 'calc'),'distance');
					$temp_c = $temp_c==="distance" ? "s" : "r";
					$settings .= $value.'d:'.$temp_d.';'.$value.'min:'.$temp_min.';'.$value.'max:'.$temp_max.';'.$value.'o:'.$temp_o.';'.$value.'c:'.$temp_c.';';
				}
			}
		}
		
		if ($haseffects===true) {
			$rulesspeed = $this->get_val($addOn, array('rules','speed'),0);
			$rulesease = $this->get_val($addOn,  array('rules','ease'),'none');			
			if ($rulesspeed!=0) $settings .='rus:'.$rulesspeed.';';
			if ($rulesease!='none') $settings .='rue:'.$rulesease.';';
		}
		
		$settings .='ox:'.$ox.';';
		$settings .='oy:'.$oy.';';
		
		if ($settings!=='') echo "								data-mousetrap='".$settings. "' " . "\n";
	
	}
	
	public function write_slide_attributes($slider, $slide) {		
		$enabled = $this->isEnabled($slider);
		if(empty($enabled)) return;						
	}
	
}
?>