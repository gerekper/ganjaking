<?php
/**
 * All appearance additions
 */
class evotx_appearance{
	function __construct(){
		
		add_filter( 'evo_appearance_button_elms', array($this, 'appearance_button' ), 10, 1);
		add_filter( 'evo_appearance_button_elms_hover',array($this, 'appearance_button_hover') , 10, 1);

		add_filter( 'eventon_inline_styles_array',array($this, 'evotx_dynamic_styles') , 10, 1);

		if(is_admin()){
			add_filter( 'eventon_appearance_add', array($this, 'evotx_appearance_settings' ), 10, 1);
		}
	}
	function evotx_appearance_settings($array){			
		$new[] = array('id'=>'evotx','type'=>'hiddensection_open','name'=>'Tickets Styles','display'=>'none');
		$new[] = array('id'=>'evotx','type'=>'fontation','name'=>'Success Notification',
			'variations'=>array(
				array('id'=>'evotx_1', 'name'=>'Text Color','type'=>'color', 'default'=>'ffffff'),
				array('id'=>'evotx_2', 'name'=>'Background Color','type'=>'color', 'default'=>'93d48c'),
				array('id'=>'evotx_3', 'name'=>'Checkout button background color','type'=>'color', 'default'=>'237ebd'),
				array('id'=>'evotx_4', 'name'=>'Checkout button text Color','type'=>'color', 'default'=>'ffffff'),
				array('id'=>'evotx_5', 'name'=>'View Cart button background color','type'=>'color', 'default'=>'237ebd'),
				array('id'=>'evotx_6', 'name'=>'View Cart button text color','type'=>'color', 'default'=>'ffffff'),
			)
		);
		$new[] = array('id'=>'evotx','type'=>'hiddensection_close',);
		return array_merge($array, $new);
	}

	function evotx_dynamic_styles($_existen){
		$new= array(
			array(
				'item'=>'.evcal_eventcard .evo_metarow_tix .tx_wc_notic',
				'css'=>'background-color:#$', 'var'=>'evotx_2',	'default'=>'93d48c'
			),array(
				'item'=>'#evcal_list .eventon_list_event .evo_metarow_tix .tx_wc_notic p',
				'css'=>'color:#$', 'var'=>'evotx_1',	'default'=>'ffffff'
			),array(
				'item'=>'#evcal_list .eventon_list_event .event_description .tx_wc_notic .evcal_btn.view_cart',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evotx_5',	'default'=>'237ebd'),
					array('css'=>'color:#$', 'var'=>'evotx_6',	'default'=>'ffffff'),
				)
			),array(
				'item'=>'#evcal_list .eventon_list_event .event_description .tx_wc_notic .evcal_btn.checkout',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evotx_3',	'default'=>'237ebd'),
					array('css'=>'color:#$', 'var'=>'evotx_4',	'default'=>'ffffff'),
				)
			)			
		);			

		return (is_array($_existen))? array_merge($_existen, $new): $_existen;
	}
	function appearance_button($string){
		$string .= ',.evoTX_wc .variations_button .evcal_btn, .evo_lightbox.eventon_events_list .eventon_list_event .evoTX_wc a.evcal_btn';			
		return $string;
	}
	function appearance_button_hover($string){
		$string .= ',.evoTX_wc .variations_button .evcal_btn:hover, .evo_lightbox.eventon_events_list .eventon_list_event .evoTX_wc a.evcal_btn:hover';			
		return $string;
	}
}
new evotx_appearance();