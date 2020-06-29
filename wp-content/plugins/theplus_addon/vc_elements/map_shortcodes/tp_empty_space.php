<?php
// Empty Space Elements
if(!class_exists("ThePlus_empty_space")){
	class ThePlus_empty_space{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_empty_space') );
			add_shortcode( 'tp_empty_space',array($this,'tp_empty_space_shortcode'));
		}
		function tp_empty_space_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'dp_height'=> '32px',
					'tab_height'=>'22px',
					'mob_height'=>'15px',
					'extra_class'=>'',	  
			   ), $atts ) );
			  
			
			$desktop_css='';
			if(!empty($dp_height)) {
					$desktop_css .= 'padding-top: '.esc_attr($dp_height).';';
			}
			$tablet_css='';
			if(!empty($tab_height)) {
					$tablet_css .= 'padding-top: '.esc_attr($tab_height).';';
			}
			$mobile_css='';
			if(!empty($mob_height)) {
					$mobile_css .= 'padding-top: '.esc_attr($mob_height).';';
			}
	
			  $uid=uniqid('empty_space');
			  
				$space ='<div class="pt-plus-empty-space '.esc_attr($uid).' '.esc_attr($extra_class).'" data-uid="'.esc_attr($uid).'" >';
				$space .='</div>';
			  
			
			$css_rule='';
			$css_rule .= '<style >';
			$css_rule .= '@media (min-width:992px){.'.esc_js($uid).' {'.$desktop_css.'}}';
			$css_rule .= '@media (max-width:991px){.'.esc_js($uid).' {'.$tablet_css.'}}';
			$css_rule .= '@media (max-width:600px){.'.esc_js($uid).' {'.$mobile_css.'}}';
			$css_rule .= '</style>';
								
			return $css_rule.$space ;
		}
		function init_tp_empty_space(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("TP Empty Space", "pt_theplus"),
					"base" => "tp_empty_space",
					"icon" => "tp-empty-space",
					"description" => esc_html__('Responsive White Space', 'pt_theplus'),
					"category" => __("The Plus", "pt_theplus"),
					"params" => array(
						array(
							'type' => 'textfield',
						   'heading' =>  __('Desktop Empty Space', 'pt_theplus'),
							'param_name' => 'dp_height',            
							'admin_label'=>true,
							'value' => '32px',
							"description" => __( "More than 768px Empty space for screens above 50px , which considered as a Desktop Screen size.", "pt_theplus" )
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('Tablet Empty Space', 'pt_theplus'),
						   'admin_label'=>true,
							'param_name' => 'tab_height',
							'value' => '22px',
							"description" => __( "In between 768px and 480px width Empty space for screens above 40px , which considered as a Desktop Screen size.", "pt_theplus" )
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('Mobile Empty Space', 'pt_theplus'),
							'admin_label'=>true,
							'param_name' => 'mob_height',
							'value' => '15px',
							"description" => __( "Less than 480px width Empty space for screens above 20px , which considered as a Desktop Screen size.", "pt_theplus" )
						),
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Extra Settings', 'pt_theplus'),
						'param_name' => 'extra_effect',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Extra Class here to use for Customisation Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
							'param_name' => 'extra_class',
							'admin_label' => true,
							'value' => ''
							
						)
					)
				));
			}
		}
	}
	new ThePlus_empty_space;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_empty_space'))
	{
		class WPBakeryShortCode_tp_empty_space extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}


