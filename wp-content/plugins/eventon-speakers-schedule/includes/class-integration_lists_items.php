<?php 
/**
 * Integration with Lists and Items addon
 * @version 
 */

class EVOSS_Int_LI{
	public function __construct(){
		add_filter('evoli_shortcodegen_cat_type',array($this, 'cat_types'), 10,1);
		add_filter('evoli_translated_tax_names',array($this, 'tax_names'), 10,1);
	}
	function cat_types($array){
		$array['event_speaker'] ='Event Speakers';
		return $array;
	}
	function tax_names($array){
		$array['event_speaker'] ='evospk';
		return $array;
	}
}
new EVOSS_Int_LI();