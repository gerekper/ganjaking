<?php
/**
 * DEP
 * EVO Calendar
 * @version 2.6.8
 *
 * ** DEPRECATING to EVO()->cal
 */

class EVO_Calendar{
	public $tab, $pre;
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
		return EVO()->cal->_get_all_month_names();
	}

	// return an array of full, three letter days
	// index_universal - create array based on universal index of 0 values
	// @2.6.8
	function get_all_days($lang='L1', $length='full', $index_universal = false){
		return EVO()->cal->get_all_day_names($length);
	}

}