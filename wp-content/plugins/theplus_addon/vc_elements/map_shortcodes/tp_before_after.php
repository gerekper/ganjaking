<?php 
// Before After Elements
if(!class_exists("ThePlus_before_after")){
	class ThePlus_before_after{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_before_after') );
			add_shortcode( 'tp_before_after',array($this,'tp_before_after_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_before_after_scripts' ), 1 );
		}
		function tp_before_after_scripts() {
			wp_register_script( 'pt-theplus-before-after', THEPLUS_PLUGIN_URL .'vc_elements/js/main/pt-theplus-before-after.js',array(),'', true ); //before after js
		}
		function tp_before_after_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				'type' =>'horizontal',
				'before_image'=>'',
				'before_label'=>'',
				'after_image'=>'',
				'after_label'=>'',
				'click_hover_move'=>'on',
				'separate_width'=>'3',
				'separate_position'=>'50',
				'separate_switch'=>'false',
				'separator_style'=>'middle',
				'separate_color'=>'#000000',
				'image_separator'=>'',
				'animation_effects'=>'no-animation',
					'animation_delay'=>'50',
					'el_class' =>'',
				), $atts ) );
				wp_enqueue_script( 'pt-theplus-before-after');
				$uid=uniqid("bf_af");
				$attr_data=$image_sep=$middle_separator=$bottom_separator=$before_image_tag=$after_image_tag=$sep_style=$before_label_text=$after_label_text='';
				
				$attr_data .=' data-id="'.esc_attr($uid).'" ';
				$attr_data .=' data-type="'.esc_attr($type).'" ';
				$attr_data .=' data-click_hover_move="'.esc_attr($click_hover_move).'" ';
				$attr_data .=' data-separate_width="'.esc_attr($separate_width).'" ';
				$attr_data .=' data-separate_position="'.esc_attr($separate_position).'" ';
				$attr_data .=' data-separator_style="'.esc_attr($separator_style).'" ';
				$attr_data .=' data-show="1" ';
				$attr_data .=' data-responsive="yes" ';
				$attr_data .=' data-width="0" ';
				$attr_data .=' data-max-width="0" ';
				$attr_data .=' data-separate_switch="'.esc_attr($separate_switch).'" ';
				
				if(!empty($image_separator)){
					$attr_data .=' data-separate_image="2" ';
					}else{
					$attr_data .=' data-separate_image="1" ';
				}
				
				if(!empty($before_image)){
					$before_image = wp_get_attachment_image_src($before_image, "full");
					$before_imgSrc = $before_image[0];
					$before_image_tag='<img class="image-before-wrap" src="'.esc_url($before_imgSrc).'" alt="">';
				}
				if(!empty($after_image)){
					$after_image = wp_get_attachment_image_src($after_image, "full");
					$after_imgSrc = $after_image[0];
					$after_image_tag='<img class="image-after-wrap" src="'.esc_url($after_imgSrc).'" alt="">';
				}
				if(!empty($separate_switch) && $separate_switch=='true'){
					$sep_style=' style="background: '.esc_attr($separate_color).';"';
					if(!empty($image_separator)){
						$image_separator = wp_get_attachment_image_src($image_separator, "full");
						$imgSrc = $image_separator[0];
						$image_sep= '<div class="before-after-sep-icon"><img src="'.esc_url($imgSrc).'" alt=""></div>';
					}
					if(!empty($type) && ($type=='horizontal' || $type=='vertical')){
					if($separator_style=='middle'){
						$middle_separator='<div class="before-after-sep" '.$sep_style.'></div>';
					}else{
						$middle_separator='<div class="before-after-sep" '.$sep_style.'></div>';
						$bottom_separator='<div class="before-after-bottom-separate"></div>';
					}
					}
				}
				
				if(!empty($before_label)){
					$before_label_text='<div class="before_after_label before_label_text">'.esc_html($before_label).'</div>';
				}
				if(!empty($after_label)){
					$after_label_text='<div class="before_after_label after_label_text">'.esc_html($after_label).'</div>';
				}
				
				if($animation_effects=='no-animation'){
					$animated_class='';
					$animation_effects='';
					$animation_delay='';
					$animation_delay_time='';
				}else{
					$animated_class='animate-general';
					$animation_effects=$animation_effects;
					$animation_delay_time=$animation_delay;
				}
				
				$bf_af ='<div class="pt_plus_before_after '.esc_attr($el_class).' '.esc_attr($animated_class).'" '.$attr_data.' data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">';
					$bf_af .='<div class="before-after-inner">
						<div class="before-after-image image-before">
							'.$before_image_tag.'
							'.$before_label_text.'
						</div>
						<div class="before-after-image image-after">
							'.$after_image_tag.'
							'.$after_label_text.'
						</div>
						'.$middle_separator.'
						'.$image_sep.'
					</div>
					'.$bottom_separator;
				$bf_af .='</div>';
			return $bf_af;
		}
		function init_tp_before_after(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => esc_html__("Before After", "pt_theplus"),
					"base" => "tp_before_after",
					"icon" => "tp-before-after",
					"category" => esc_html__("The Plus", "pt_theplus"),
					"description" => esc_html__('Show the difference', 'pt_theplus'),
					"params" => array(
						array(
								'type'        => 'radio_select_image',
								"heading" =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Horizontal/ Vertical/ Opacity ? Choose Style of Before After Option from Most popular Horizontal or Vertical as well as Latest Opacity.','pt_theplus').'</span></span>'.esc_html__('Before After Style', 'pt_theplus')),				
								'admin_label' => true,
								'param_name'  => 'type',
								'simple_mode' => false,
								'value'		=> 'horizontal',
								'options'     => array(
									'horizontal' => array(
										'tooltip' => esc_attr__('Horizontal','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/before_after/horizontal.png'
									),
									'vertical' => array(
										'tooltip' => esc_attr__('Vertical','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/before_after/vertical.png'
									),
									'cursor' => array(
										'tooltip' => esc_attr__('Opacity','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/before_after/cursor.png'
									),
								),
							),
						array(
							'type' => 'attach_image',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Image for Before Section from this option.','pt_theplus').'</span></span>'.esc_html__('Image for Before ', 'pt_theplus')),
							'param_name' => 'before_image',
							'value' => '',
							'description' => '',
							'admin_label' => false,
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							"type" => "textfield",
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add Label in Text format for Before Section.','pt_theplus').'</span></span>'.esc_html__('Image Label for Before', 'pt_theplus')),
							"param_name" => "before_label",
							"value" => '',
							'description' => '',
							'edit_field_class' => 'vc_col-xs-6'
						),
						array(
							'type' => 'attach_image',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Image for After Section from this option.','pt_theplus').'</span></span>'.esc_html__('Image for After', 'pt_theplus')),
							'param_name' => 'after_image',
							'value' => '',
							'description' => '',
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							"type" => "textfield",
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add Label in Text format for After Section.','pt_theplus').'</span></span>'.esc_html__('Image Label for After', 'pt_theplus')),
							"param_name" => "after_label",
							"value" => '',
							'description' => '',
							'edit_field_class' => 'vc_col-xs-6'
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose Mouse hover or Mouse Drag Option for animation to take effect.','pt_theplus').'</span></span>'.esc_html__('Mouse Hover', 'pt_theplus')),
							'param_name' => 'click_hover_move',
							'description' => '',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							 "heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Show or Hide Separator line from this option.','pt_theplus').'</span></span>'.esc_html__('Separator Line on/off', 'pt_theplus')),
							'param_name' => 'separate_switch',
							'description' => '',
							'value' => 'false',
							'options' => array(
								'true' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							 'dependency' => array(
								'element' => 'type',
								'value' => array('horizontal','vertical'),
							),
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							"type" => "dropdown",
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose from options Middle or Bottom from here.','pt_theplus').'</span></span>'.esc_html__('Separator Style', 'pt_theplus')),
							"param_name" => "separator_style",
							"value" => array(
								__("Middle", "pt_theplus") => "middle",
								__("Bottom", "pt_theplus") => "bottom"
							),
							"description" => "",
							"std" => 'middle',
							'dependency' => array(
								'element' => 'separate_switch',
								'value' => 'true'
							)
						),
						array(
							"type" => "textfield",
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add width/thickness of Separator line in Pixels from here. E.g. 1px , 2px , 3px , etc','pt_theplus').'</span></span>'.esc_html__('Separator Line Width', 'pt_theplus')),
							"param_name" => "separate_width",
							"value" => '3',
							'description' => '',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'separate_switch',
								'value' => 'true'
							)
						),
						array(
							'type' => 'colorpicker',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Separator using this option.','pt_theplus').'</span></span>'.esc_html__('Separator Line Color', 'pt_theplus')),
							'param_name' => 'separate_color',
							"description" => "",
							'value' => '#000000',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'separate_switch',
								'value' => 'true'
							)
						),
						
						array(
							"type" => "textfield",
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Number to initiate effect position. If you select 50 It will start from the middle.','pt_theplus').'</span></span>'.esc_html__('Initial Effect Position', 'pt_theplus')),
							"param_name" => "separate_position",
							"value" => '50',
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
						),
						array(
							'type' => 'attach_image',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can upload Separator icon from this option.','pt_theplus').'</span></span>'.esc_html__('Separator Icon', 'pt_theplus')),
							'param_name' => 'image_separator',
							'edit_field_class' => 'vc_col-xs-6',
							'value' => '',
							'description' => '',
						),
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Animation Settings', 'pt_theplus'),
						'param_name' => 'annimation_effect',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
							"type" => "dropdown",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
							"param_name" => "animation_effects",
							"value" => array(
								esc_html__( 'No-animation', 'pt_theplus' )             => 'no-animation',
								esc_html__( 'FadeIn', 'pt_theplus' )             => 'transition.fadeIn',
								esc_html__( 'FlipXIn', 'pt_theplus' )            => 'transition.flipXIn',
							   esc_html__( 'FlipYIn', 'pt_theplus' )            => 'transition.flipYIn',
							   esc_html__( 'FlipBounceXIn', 'pt_theplus' )      => 'transition.flipBounceXIn',
							   esc_html__( 'FlipBounceYIn', 'pt_theplus' )      => 'transition.flipBounceYIn',
							   esc_html__( 'SwoopIn', 'pt_theplus' )            => 'transition.swoopIn',
							   esc_html__( 'WhirlIn', 'pt_theplus' )            => 'transition.whirlIn',
							   esc_html__( 'ShrinkIn', 'pt_theplus' )           => 'transition.shrinkIn',
							   esc_html__( 'ExpandIn', 'pt_theplus' )           => 'transition.expandIn',
							   esc_html__( 'BounceIn', 'pt_theplus' )           => 'transition.bounceIn',
							   esc_html__( 'BounceUpIn', 'pt_theplus' )         => 'transition.bounceUpIn',
							   esc_html__( 'BounceDownIn', 'pt_theplus' )       => 'transition.bounceDownIn',
							   esc_html__( 'BounceLeftIn', 'pt_theplus' )       => 'transition.bounceLeftIn',
							   esc_html__( 'BounceRightIn', 'pt_theplus' )      => 'transition.bounceRightIn',
							   esc_html__( 'SlideUpIn', 'pt_theplus' )          => 'transition.slideUpIn',
							   esc_html__( 'SlideDownIn', 'pt_theplus' )        => 'transition.slideDownIn',
							   esc_html__( 'SlideLeftIn', 'pt_theplus' )        => 'transition.slideLeftIn',
							   esc_html__( 'SlideRightIn', 'pt_theplus' )       => 'transition.slideRightIn',
							   esc_html__( 'SlideUpBigIn', 'pt_theplus' )       => 'transition.slideUpBigIn',
							   esc_html__( 'SlideDownBigIn', 'pt_theplus' )     => 'transition.slideDownBigIn',
							   esc_html__( 'SlideLeftBigIn', 'pt_theplus' )     => 'transition.slideLeftBigIn',
							   esc_html__( 'SlideRightBigIn', 'pt_theplus' )    => 'transition.slideRightBigIn',
							   esc_html__( 'PerspectiveUpIn', 'pt_theplus' )    => 'transition.perspectiveUpIn',
							   esc_html__( 'PerspectiveDownIn', 'pt_theplus' )  => 'transition.perspectiveDownIn',
							   esc_html__( 'PerspectiveLeftIn', 'pt_theplus' )  => 'transition.perspectiveLeftIn',
							   esc_html__( 'PerspectiveRightIn', 'pt_theplus' ) => 'transition.perspectiveRightIn',
							),
							'edit_field_class' => 'vc_col-sm-6',
							'std' => 'no-animation'
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),	
							"param_name" => "animation_delay",
							'edit_field_class' => 'vc_col-sm-6',
							"value" => '50',
							"description" => ""
						),
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Extra Settings', 'pt_theplus'),
						'param_name' => 'extra_effect',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Extra Class here to use for Customisation Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
							"param_name" => "el_class",
							'edit_field_class' => 'vc_col-sm-6',
						),
					)
				));
			}
		}
	}
	new ThePlus_before_after;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_before_after'))
	{
		class WPBakeryShortCode_tp_before_after extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}

