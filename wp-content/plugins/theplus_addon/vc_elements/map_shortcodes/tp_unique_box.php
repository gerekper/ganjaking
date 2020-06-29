<?php
// Unique Box Elements
if(!class_exists("ThePlus_unique_box")){
	class ThePlus_unique_box{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_unique_box') );
			add_shortcode( 'tp_unique_box',array($this,'tp_unique_box_shortcode'));
		}
		function tp_unique_box_shortcode($atts,$content = null){
			extract( shortcode_atts( array(         
				'position_left'=>'5%',
				'position_right'=>'auto',
				'position_top'=>'5%',
				'box_width'=>'200px',
				'box_height'=>'auto',       
				'box_shadow'=>'2px 2px 2px 3px #222',
				'hover_box_shadow'=>'',        
				'box_padding'=>'30px',
				'box_bgcolor'=>'#fff',
				'magic_scroll' => 'off',
				'scroll_type'	=> 'position',
				'distance_scroll_x' => '0',
				'distance_scroll_y' => '50',
				'scale_scroll'=> '1',
				
				'mouse_move_parallax'=>'',
					'move_speed_x'=>'30',
					'move_speed_y'=>'30',
					
				'cursor_icon'=>'',
				'cursor_image'=>'',	
				
				'el_class'=>'',
				
				'tablet_hide' => 'off',
				'desktop_hide' =>'off',
				'mobile_hide' => 'off',
			   ), $atts ) );
			  
			  
			 if($desktop_hide == 'on') {
					$desktop_hide = 'desktop-hide';
				}else{
					$desktop_hide = '';
				}
				if($tablet_hide == 'on') {
					$tablet_hide = 'tablet-hide';
				}else{
					$tablet_hide = '';
				}
				if($mobile_hide == 'on') {
					$mobile_hide = 'mobile-hide';
				}else{
					$mobile_hide = '';
				}

			$styles='';
			if($box_padding!=''){
			$styles .='padding: '.esc_attr($box_padding).';';
			}
			if($box_bgcolor!=''){
			$styles .='background: '.esc_attr($box_bgcolor).';';
			}

				$magic_class = $magic_attr = $parallax_scroll = '';
					if (!empty($magic_scroll) && $magic_scroll == 'on') {
						$magic_attr .= ' data-scroll_type="' . esc_attr($scroll_type) . '" ';
						if(!empty($scroll_type) && $scroll_type== 'position' ){
							$magic_attr .= ' data-scroll_x="' . esc_attr($distance_scroll_x) . '" ';
							$magic_attr .= ' data-scroll_y="' . esc_attr($distance_scroll_y) . '" ';
							$parallax_scroll .= ' parallax-scroll ';
						}
						if(!empty($scroll_type) && $scroll_type== 'scale'){
							$magic_attr .= ' data-scale_scroll="' . esc_attr($scale_scroll) . '" ';
							$parallax_scroll .= ' scale-scroll ';
						}
						if(!empty($scroll_type) && $scroll_type== 'both'){
							$magic_attr .= ' data-scroll_x="' . esc_attr($distance_scroll_x) . '" ';
							$magic_attr .= ' data-scroll_y="' . esc_attr($distance_scroll_y) . '" ';
							$magic_attr .= ' data-scale_scroll="' . esc_attr($scale_scroll) . '" ';
							$parallax_scroll .= ' both-scroll ';
						}
						$magic_class .= ' magic-scroll ';
					}
				$move_parallax=$move_parallax_attr=$parallax_move='';
				if(!empty($mouse_move_parallax) && $mouse_move_parallax=='on' ){
					$move_parallax='pt-plus-move-parallax';
					$parallax_move='parallax-move';
					$move_parallax_attr .= ' data-move_speed_x="' . esc_attr($move_speed_x) . '" ';
					$move_parallax_attr .= ' data-move_speed_y="' . esc_attr($move_speed_y) . '" ';
				}
				
				$cursor_class=$cursor_attr='';
				$cursor_uid = uniqid('cursor');
				if (!empty($cursor_icon) && $cursor_icon == 'on') {
					if(!empty($cursor_image)){
						$cursor_image = wp_get_attachment_image_src( $cursor_image,true);
						$cursor_icon_url = $cursor_image[0];
						$cursor_attr .= 'data-cursor_uid="' . esc_attr($cursor_uid) . '" ';
						$cursor_attr .= ' data-cursor_icon_url="' . esc_url($cursor_icon_url) . '" ';
						$cursor_class .= ' ' . $cursor_uid . ' pt-plus-cursor-icon';
					}
				}
				
			   $content = wpb_js_remove_wpautop($content, true);
			   $uid=uniqid('unique_box');
				$box ='<div class="pt-plus-unique-box-wrapper '.esc_attr($uid).' '.esc_attr($magic_class).' '.esc_attr($cursor_class).' '.esc_attr($el_class).' '.esc_attr($desktop_hide).' '.esc_attr($tablet_hide).' '.esc_attr($mobile_hide).'" '.$cursor_attr.'>';
					$box .='<div class="unique_box_parallax   '.esc_attr($parallax_scroll).' '.esc_attr($move_parallax).'" '.$magic_attr.' >';
						$box .= '<div class="pt-plus-unique-box '.esc_attr($parallax_move).'" data-uid="'.esc_attr($uid).' " style="'.$styles.'" '.$move_parallax_attr.'>';
							$box .= do_shortcode($content);   
						$box .='</div>';
					$box .='</div>';
				$box .='</div>';
				
			$css_rule='';
			$css_rule .= '<style >';
			$css_rule .= '@media only screen and (min-width:768px){ .'.esc_js($uid).'{left:'.esc_js($position_left).';right:'.esc_js($position_right).';margin-top:'.esc_js($position_top).';max-width:'.esc_js($box_width).';height:'.esc_js($box_height).';}}@media only screen and (min-width:768px){ .'.esc_js($uid).' .pt-plus-unique-box{-webkit-box-shadow:'.esc_js($box_shadow).';-moz-box-shadow:'.esc_js($box_shadow).';box-shadow:'.esc_js($box_shadow).';}.'.esc_js($uid).':hover .pt-plus-unique-box{-webkit-box-shadow:'.esc_js($hover_box_shadow).';-moz-box-shadow:'.esc_js($hover_box_shadow).';box-shadow:'.esc_js($hover_box_shadow).';}}';
			$css_rule .= '</style>';
					
		return $css_rule.$box;
		}
		function init_tp_unique_box(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("Unique Box", 'pt_theplus'),
					"base" => "tp_unique_box",
					"icon" => 'tp-unique-box',
					'as_parent' => array(
						'except' => 'vc_gmaps'
					),
					"description" => esc_html__('Modern Layouts in Seconds', 'pt_theplus'),
					'content_element' => true,
					'controls' => 'full',
					"category" => __("The Plus", "pt_theplus"),
					'show_settings_on_create' => true,
					"params" => array(
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Box Left', 'pt_theplus'),
							"description" => __("E.g. auto,10%,20% etc..", "pt_theplus"),
							'param_name' => 'position_left',
							'admin_label' => true,
							'value' => '5%'
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Box Right', 'pt_theplus'),
							"description" => __("E.g. auto,10%,20% etc..", "pt_theplus"),
							'param_name' => 'position_right',
							'admin_label' => true,
							'value' => 'auto'
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Box Top', 'pt_theplus'),
							"description" => __("E.g. 10,20 etc..", "pt_theplus"),
							'param_name' => 'position_top',
							'admin_label' => true,
							'value' => '5%'
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Box Width', 'pt_theplus'),
							"description" => __("E.g. 10%,20% etc..", "pt_theplus"),
							'param_name' => 'box_width',
							'admin_label' => true,
							'value' => '200px'
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Box Height', 'pt_theplus'),
							"description" => __("E.g. 10,20 etc..", "pt_theplus"),
							'param_name' => 'box_height',
							'admin_label' => true,
							'value' => 'auto'
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' => __('Magic Scroll', 'pt_theplus'),
							'param_name' => 'magic_scroll',
							'description' => '',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-12"
						),
						array(
									'type' => 'dropdown',
									'heading' => __('Scroll Type', 'pt_theplus'),
									'param_name' => 'scroll_type',
									'value' => array(
										__('Position', 'pt_theplus') => 'position',
										__('Scale', 'pt_theplus') => 'scale',
										__('Position and Scale', 'pt_theplus') => 'both',
									),
									'description' => '',
									'edit_field_class' => 'vc_col-xs-12',
									'std' => 'position',
									'dependency' => array(
										'element' => 'magic_scroll',
										'value' => array(
											'on'
										)
									)
						 ),
						array(
							'type' => 'textfield',
							'heading' => __('(X) / Horizontal Distance', 'pt_theplus'),
							'param_name' => 'distance_scroll_x',
							'value' => '0',
							'description' => __('Example: Ex.. Bottom: 100,200...-100,-200', 'pt_theplus'),
							
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'scroll_type',
								'value' => array(
									'position','both'
								)
							)
						),
						array(
							'type' => 'textfield',
							'heading' => __('(Y) / Vertical Distance', 'pt_theplus'),
							'param_name' => 'distance_scroll_y',
							'value' => '50',
							'description' => __('Example: Ex.. 100,200..,-100,-200...', 'pt_theplus'),
							
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'scroll_type',
								'value' => array(
									'position','both'
								)
							)
						),
						 array(
							'type' => 'textfield',
							'heading' => __('Scale On Scroll', 'pt_theplus'),
							'param_name' => 'scale_scroll',
							'value' => '1',
							'description' => __('Example: Ex.. 1, 1.5 , -0.7...', 'pt_theplus'),
							
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'scroll_type',
								'value' => array(
									'scale','both'
								)
							)
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This effect will be parallax on mouse move. It will move image as you move your mouse hover.','pt_theplus').'</span></span>'.esc_html__('Mouse Move Parallax', 'pt_theplus')),
							'param_name' => 'mouse_move_parallax',
							'description' => '',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-12"
						),
						array(
							'type' => 'textfield',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Horizontal Speed move parallax. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('Move Parallax (X)', 'pt_theplus')),
							'param_name' => 'move_speed_x',
							'value' => '30',
							'description' => '',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'mouse_move_parallax',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'textfield',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Vertical Speed move parallax. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('Move Parallax (Y)', 'pt_theplus')),
							'param_name' => 'move_speed_y',
							'value' => '30',
							'description' => '',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'mouse_move_parallax',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This option will be Cursor Icon on Content.','pt_theplus').'</span></span>'.esc_html__('Cursor Icon', 'pt_theplus')),
							'param_name' => 'cursor_icon',
							'description' => '',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-12"
						),
						 array(
							'type' => 'attach_image',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Upload image of Cursor Image using this option. You can upload .jpg, .png, .gif formats.','pt_theplus').'</span></span>'.esc_html__('Select Cursor Icon', 'pt_theplus')),
							'param_name' => 'cursor_image',
							'value' => '',
							'description' => '',
							'admin_label' => false,
							'dependency' => array(
								'element' => 'cursor_icon',
								'value' => 'on'
							),
							'edit_field_class' => 'vc_col-xs-12'
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Box Background Color', 'pt_theplus'),
							'param_name' => 'box_bgcolor',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Styling', 'pt_theplus'),
							'value' => '#fff',
						),
						array(
							'type' => 'textfield',
							'heading' => __('Box Padding', 'pt_theplus'),
							"description" => __("E.g. 30px,20px... etc..", "pt_theplus"),
							'param_name' => 'box_padding',
							'value' => '30px',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Box Shadow', 'pt_theplus'),
							"description" => __("E.g. 0px 0px 6px 1px #9a9a9a... etc..", "pt_theplus"),
							'param_name' => 'box_shadow',
							'value' => '0px 0px 6px 1px #9a9a9a',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Hover Box Shadow', 'pt_theplus'),
							"description" => __("E.g. 0px 0px 6px 1px #9a9a9a... etc..", "pt_theplus"),
							'param_name' => 'hover_box_shadow',
							'value' => '',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Extra Class here to use for Customisation Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
							"param_name" => "el_class",
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On whole Meta Section of Blog Post using this option.','pt_theplus').'</span></span>'.esc_html__('Desktop Hide', 'pt_theplus')),
							'param_name' => 'desktop_hide',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No',
							),
							),
							"edit_field_class" => "vc_col-xs-4",
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On whole Meta Section of Blog Post using this option.','pt_theplus').'</span></span>'.esc_html__('Tablet Hide', 'pt_theplus')),
							'param_name' => 'tablet_hide',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No',
							),
							),
							"edit_field_class" => "vc_col-xs-4",
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On whole Meta Section of Blog Post using this option.','pt_theplus').'</span></span>'.esc_html__('Mobile Hide', 'pt_theplus')),
							'param_name' => 'mobile_hide',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No',
							),
							),
							"edit_field_class" => "vc_col-xs-4",
						),
					),
					"js_view" => 'VcColumnView'
				));
			}
		}
	}
	new ThePlus_unique_box;

	if(class_exists('WPBakeryShortCodesContainer') && !class_exists('WPBakeryShortCode_tp_unique_box'))
	{
		class WPBakeryShortCode_tp_unique_box extends WPBakeryShortCodesContainer
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}

