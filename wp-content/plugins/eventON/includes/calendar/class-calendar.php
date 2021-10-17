<?php
/**
 * DEP
 * EVO Calendar
 * @version 2.6.8
 *
 * ** DEPRECATING to EVO()->cal
 */

class EVO_Calendar{
	private $props = false;

	// @v2.6.13 -- to be merged with calendar_gen
	public function __construct($tab='', $options_pre = 'evcal_options_', $init=false, $options_values=''){
		if(!empty($tab)){		
			$this->tab = $tab;
			$this->pre = $options_pre;

			if($init){
				$this->init();
			}else{
				if(!empty($options_values)){
					$this->props = $options_values;
				}else{
					$this->props = get_option( $this->pre .$this->tab);
				}			
			}
		}
	}

// INIT
	private function init(){	
		if(array_key_exists('EVO_Settings', $GLOBALS) && isset($GLOBALS['EVO_Settings'][$this->pre .$this->tab])){
			global $EVO_Settings;
			$this->props  = $EVO_Settings[$this->pre .$this->tab];
		}else{
			$this->props = get_option( $this->pre .$this->tab);
			$GLOBALS['EVO_Settings'][$this->pre .$this->tab] = $this->props;
		}
		
	}

// GETTERS
// @2.6.8 switched to public
	public function set_prop($options_field_name, $reload = true){
		if( !$reload){
			global $EVO_Settings;
			$props  = $EVO_Settings[$options_field_name];
			if(!isset($props) || empty($props)){
				$reload = true;
			}else{
				$this->props = $props;
			}
		}

		if($reload){
			$this->props = get_option( $options_field_name );
		}
	}

	function get_prop($field){
		if(!isset($this->props[$field])) return false;
		return maybe_unserialize($this->props[$field]);
	}

	// @since 2.6.7
	function is_yes($field){
		if(!isset($this->props[$field])) return false;
		if( $this->props[$field]!= 'yes') return false;
		return true;
	}

// DATE & TIMES
	// return an array of full, three, one months
	// @2.6.8
	function get_all_months($lang='L1', $length='full'){
		$props = $this->props;

		$def_months = array(1=>'january','february','march','april','may','june','july','august','september','october','november','december');

		$return = false;
		if(!$props) $return = $def_months;
		if(!isset($props[$lang])) $return = $def_months;

		// if values are not set return cropped values
		if($return && $length=='full') return $return;
		if($return){
			$O = array();
			foreach($return as $i=>$r){
				$O[$i] = $length=='three' ? substr($r,0,3): substr($r,0,1);
			}
			return $O;
		}
		
		$months = array();
		for($x=1; $x<13; $x++){
			switch($length){
				case 'full':
					$months[$x] = !empty($props[$lang]['evcal_lang_'.$x])? $props[$lang]['evcal_lang_'.$x]: $def_months[$x];
				break;
				case 'three':
					$months[$x] = !empty($props[$lang]['evo_lang_3Lm_'.$x]) && isset($props[$lang]['evo_lang_3Lm_'.$x])? 
						$props[$lang]['evo_lang_3Lm_'.$x]: 
						substr($def_months[$x],0,3);
				break;
				case 'one':
					$months[$x] = !empty($props[$lang]['evo_lang_1Lm_'.$x])? $props[$lang]['evo_lang_1Lm_'.$x]: 
						substr($def_months[$x],0,1);
				break;
			}			
		}

		return $months;
	}

	// return an array of full, three letter days
	// index_universal - create array based on universal index of 0 values
	// @2.6.8
	function get_all_days($lang='L1', $length='full', $index_universal = false){
		$def_days = array(1=>'monday','tuesday','wednesday','thursday','friday','saturday','sunday');

		$return = false;
		$props = $this->props;
		if(!$props) $return = $def_days;
		if(!isset($props[$lang])) $return = $def_days;

		// if values are not set return cropped values
		if($return && $length=='full') return $return;
		if($return){
			$O = array();
			foreach($return as $i=>$r){
				$i = ( $index_universal && $i==7)? 0: $i;
				$O[$i] = substr($r,0,3);
			}
			return $O;
		}
		
		$days = array();
		for($x=1; $x<8; $x++){
			$index = ( $index_universal && $x==7)? 0: $x;


			if($length=='full'){
				$days[$index] = !empty($props[$lang]['evcal_lang_day'.$x])? $props[$lang]['evcal_lang_day'.$x]: $def_days[$x] ;
			}elseif($length=='three'){// 3 letter
				$days[$index] = !empty($props[$lang]['evo_lang_3Ld_'.$x])? $props[$lang]['evo_lang_3Ld_'.$x]: substr($def_days[$x], 0, 3) ;
			}else{// 1 letter

				if( !empty( $props[$lang]['evo_lang_1Ld_'.$x] )){
					$days[$index] = $props[$lang]['evo_lang_1Ld_'.$x];
				}else{
					$D = !empty($props[$lang]['evo_lang_3Ld_'.$x])? $props[$lang]['evo_lang_3Ld_'.$x]: $def_days[$x];
					$days[$index] = substr($D, 0,1);
				}
				
			}
			
		}

		return $days;
	}

}