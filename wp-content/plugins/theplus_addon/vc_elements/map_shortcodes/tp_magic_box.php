<?php 
// Magic Box Elements
if(!class_exists("ThePlus_magic_box")){
	class ThePlus_magic_box{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_magic_box') );
			add_shortcode( 'tp_magic_box',array($this,'tp_magic_box_shortcode'));
		}
		function tp_magic_box_shortcode($atts,$content = null){
			extract(shortcode_atts(array(
					'display_border' => 'on',
					'border_width' => '1px',
					'border_style' => 'solid',
					'border_radius' => '0px',
					'border_color' => '#252525',
					
					'display_border_hover' => 'on',
					'hover_border_animated' => '',
					'border_hover_width' => '1px',
					'border_hover_style' => 'solid',
					'border_hover_radius' => '0px',
					'border_hover_color' => '#ff214f',
					
					'display_boxshadow' => 'on',
					'boxshadow' => '0px 1px 7px 0',
					'boxshadow_color' => 'rgba(0,0,0,0.4)',
					'display_boxshadow_hover' => 'on',
					'boxshadow_hover' => '3px 3px 4px 0',
					'boxshadow_hover_color' => 'rgba(0,0,0,0.4)',
					
					'cursor_icon'=>'',
					'cursor_image'=>'',
					
					'magic_scroll' => 'off',
				'scroll_type'	=> 'position',
				'distance_scroll_x' => '0',
				'distance_scroll_y' => '50',
				'scale_scroll'=> '1',
					
					'mouse_move_parallax'=>'',
					'move_speed_x'=>'30',
					'move_speed_y'=>'30',
					
					'inner_padding'=>'10px',
					
					'content_hover_effects' => '',
					'hover_shadow_color' => 'rgba(0, 0, 0, 0.6)',
					
					'continuous_effect' => '',
					
					'hover_parallax' => '',
					
					'bg_color_animate' => '',
					'bg_animated_color' => '#d3d3d3',
					'animated_direction' => 'left',
					
					'animation_effects' => 'no-animation',
					'animation_delay' => '400',
					'el_class' => '',
					'tablet_hide' => 'off',
					'desktop_hide' =>'off',
					'mobile_hide' => 'off',
					'css_box' => ''
			), $atts));


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
				
				$uniqid     = uniqid('borderbox');
				$attr_class = $inner_class = '';

				$attr_class .= ' ' . esc_attr($el_class) . ' ';
				$attr_class .= ' ' . esc_attr($uniqid) . ' ';

				$inner_class .= ' border-box-normal ';
				$inner_class .= ' border-box-shadow ';
				
				if ($animation_effects == 'no-animation') {
					$animated_class       = '';
					$animation_effects    = '';
					$animation_delay      = '';
					$animation_delay_time = '';
				} else {
					$animated_class       = 'animate-general';
					$animation_effects    = $animation_effects;
					$animation_delay_time = $animation_delay;
				}

				$css_class = vc_shortcode_custom_css_class($css_box, ' ');
				$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_class, 'tp_magic_box', $atts);

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
				
				$hover_class  = $hover_attr = '';
				$hover_uniqid = uniqid('hover-effect');
				if ($content_hover_effects == "float_shadow" || $content_hover_effects == "grow_shadow" || $content_hover_effects == "shadow_radial") {
					$hover_attr .= 'data-hover_uniqid="' . esc_attr($hover_uniqid) . '" ';
					$hover_attr .= ' data-hover_shadow="' . esc_attr($hover_shadow_color) . '" ';
					$hover_attr .= ' data-content_hover_effects="' . esc_attr($content_hover_effects) . '" ';
				}
				if ($content_hover_effects == "grow") {
					$hover_class .= 'content_hover_grow';
				} elseif ($content_hover_effects == "push") {
					$hover_class .= 'content_hover_push';
				} elseif ($content_hover_effects == "bounce-in") {
					$hover_class .= 'content_hover_bounce_in';
				} elseif ($content_hover_effects == "float") {
					$hover_class .= 'content_hover_float';
				} elseif ($content_hover_effects == "wobble_horizontal") {
					$hover_class .= 'content_hover_wobble_horizontal';
				} elseif ($content_hover_effects == "wobble_vertical") {
					$hover_class .= 'content_hover_wobble_vertical';
				} elseif ($content_hover_effects == "float_shadow") {
					$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_float_shadow';
				} elseif ($content_hover_effects == "grow_shadow") {
					$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_grow_shadow';
				} elseif ($content_hover_effects == "shadow_radial") {
					$hover_class .= '' . esc_attr($hover_uniqid) . ' content_hover_radial';
				}

				$continuous_class = '';
				if ($continuous_effect == 'pulse') {
					$continuous_class .= ' content_effect_pulse ';
				} elseif ($continuous_effect == "floating") {
					$continuous_class .= ' content_effect_floating ';
				} elseif ($continuous_effect == "tossing") {
					$continuous_class .= ' content_effect_tossing ';
				}

				$hover_tilt = '';
				if (!empty($hover_parallax) && $hover_parallax == 'on') {
					
					$hover_tilt = 'hover-tilt';
				}
				
				$bg_attr=$bg_animated='';
				$bg_uniqid = uniqid('bg-animate');
				if (!empty($bg_color_animate) && $bg_color_animate == 'on') {
					$attr_class .= ' pt_plus_animated_bg ';
					$bg_animated = ' pt-plus-bg-color-animated '.esc_attr($animated_direction).' ' . esc_attr($bg_uniqid) . ' ';
					$animated_class = 'animate-general';
					$bg_attr .= ' data-bg_uniqid="' . esc_attr($bg_uniqid) . '" ';
					$bg_attr .= ' data-bg_animated_color="' . esc_attr($bg_animated_color) . '" ';
				}
				
				$cursor_class=$cursor_attr='';
				$cursor_uid = uniqid('cursor');
				if (!empty($cursor_icon) && $cursor_icon == 'on') {
					if(!empty($cursor_image)){
						$cursor_image = wp_get_attachment_image_src( $cursor_image,true);
						$cursor_icon_url = $cursor_image[0];
						$cursor_attr .= 'data-cursor_uid="' . esc_attr($cursor_uid) . '" ';
						$cursor_attr .= ' data-cursor_icon_url="' . esc_url($cursor_icon_url) . '" ';
						$cursor_class .= ' ' . esc_attr($cursor_uid) . ' pt-plus-cursor-icon';
					}
				}
				
				$padding_style ='style="';
				if(!empty($inner_padding)){
					$padding_style .='padding:'.esc_attr($inner_padding).';';
				}
				$padding_style .='"';
				$output = '<div class="pt-plus-magic-box-wrapper '.esc_attr($cursor_class).' ' . esc_attr($magic_class) . ' '.esc_attr($desktop_hide).' '.esc_attr($tablet_hide).' '.esc_attr($mobile_hide).'" '.$cursor_attr.'>';
					$output .= '<div class="magic_box_parallax ' . esc_attr($parallax_scroll) . ' content_hover_effect ' . esc_attr($hover_class) . ' ' . esc_attr($continuous_class) . '" ' . $magic_attr . ' ' . $hover_attr . '>';
						$output .= '<div class="pt-plus-hover-parallax-tilt ' . esc_attr($hover_tilt) . '">';
							$output .= '<div class="pt-plus-magic-box ' . esc_attr($animated_class) . ' ' . esc_attr($attr_class) . ' ' . esc_attr($css_class) . '" data-animate-type="' . esc_attr($animation_effects) . '" data-animate-delay="' . esc_attr($animation_delay_time) . '">';
								$output .= '<div class="animted-content-inner ' . esc_attr($bg_animated) . '" '.$bg_attr.'>';
									$output .= '<div class="magic-box-inner ' . esc_attr($inner_class) . '">';
										$output .= '<div class="magic-box-inner-block '.esc_attr($move_parallax).'" >';
											$output .= '<div class="magic-box-inner-block-content '.esc_attr($parallax_move).'" '.$move_parallax_attr.' '.$padding_style.'>';
												$output .= do_shortcode($content);
											$output .= '</div>';
										$output .= '</div>';
									$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
				$css_rule = '';	
					$css_rule .= '<style >';
					if($hover_border_animated!='yes'){
						if($display_border=='on'){
							$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal{border-width: '. esc_js($border_width) .';border-style: '. esc_js($border_style) .';-webkit-border-radius:'. esc_js($border_radius) .';-moz-border-radius: '. esc_js($border_radius) .';border-radius: '. esc_js($border_radius) .';border-color: '. esc_js($border_color) .';}';
						}else{
							$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal{border-width: '. esc_js($border_hover_width) .';border-style: '. esc_js($border_hover_style) .';-webkit-border-radius:'. esc_js($border_hover_radius) .';-moz-border-radius: '. esc_js($border_hover_radius) .';border-radius: '. esc_js($border_hover_radius) .';border-color:transparent;}';
						}
						if($display_border_hover=='on'){
							$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal:hover{border-width: '. esc_js($border_hover_width) .';border-style: '. esc_js($border_hover_style) .';-webkit-border-radius:'. esc_js($border_hover_radius) .';-moz-border-radius: '. esc_js($border_hover_radius) .';border-radius: '. esc_js($border_hover_radius) .';border-color: '. esc_js($border_hover_color) .';}';
						}
								
					}else{
						if($display_border=='on'){
							$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal{border-width: '. esc_js($border_width) .';border-style: '. esc_js($border_style) .';-webkit-border-radius:'. esc_js($border_radius) .';-moz-border-radius: '. esc_js($border_radius) .';border-radius: '. esc_js($border_radius) .';border-color: '. esc_js($border_color) .';}';
						}else{
							$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal{border-width: '. esc_js($border_hover_width) .';border-style: '. esc_js($border_hover_style) .';-webkit-border-radius:'. esc_js($border_hover_radius) .';-moz-border-radius: '. esc_js($border_hover_radius) .';border-radius: '. esc_js($border_hover_radius) .';border-color:transparent;}';
						}
						if($display_border_hover=='on'){
							$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal:after,.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal:before,.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal .magic-box-inner-block:before,.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal .magic-box-inner-block:after{-moz-border-radius: '. esc_js($border_hover_radius) .';-webkit-border-radius:'. esc_js($border_hover_radius) .';border-radius: '. esc_js($border_hover_radius) .';border-color: '. esc_js($border_hover_color) .';}.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal:after{border-left-width: '. esc_js($border_hover_width) .';border-left-style: '. esc_js($border_hover_style) .';}.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal:before{border-top-width: '. esc_js($border_hover_width) .';border-top-style: '. esc_js($border_hover_style) .';}.'. esc_js($uniqid) .' .magic-box-inner-block:before{border-bottom-width: '. esc_js($border_hover_width) .';border-bottom-style: '. esc_js($border_hover_style) .';}.'. esc_js($uniqid) .' .magic-box-inner-block:after{border-right-width: '. esc_js($border_hover_width) .';border-right-style: '. esc_js($border_hover_style) .';}.'. esc_js($uniqid) .'.border-box-normal:hover{border: none;}';
						}
					}
					if($display_border!='on' && $display_border_hover!='on'){
						$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-normal{border: none;}';
					}
					if($display_boxshadow=='on'){
						$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-shadow{-webkit-box-shadow:'. esc_js($boxshadow) .' '. esc_js($boxshadow_color) .'; -moz-box-shadow: '. esc_js($boxshadow) .' '. esc_js($boxshadow_color) .';box-shadow: '. esc_js($boxshadow) .' '. esc_js($boxshadow_color) .';}';
					}else{
						$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-shadow{-webkit-box-shadow:none;-moz-box-shadow: none;box-shadow: none;}';
					}
								
					if($display_boxshadow_hover=='on'){
						$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-shadow:hover{-webkit-box-shadow:'. esc_js($boxshadow_hover) .' '. esc_js($boxshadow_hover_color) .';-moz-box-shadow: '. esc_js($boxshadow_hover) .' '. esc_js($boxshadow_hover_color) .';box-shadow: '. esc_js($boxshadow_hover) .' '. esc_js($boxshadow_hover_color) .';}';
					}else{
						$css_rule .='.'. esc_js($uniqid) .' .magic-box-inner.border-box-shadow:hover{-webkit-box-shadow:none;-moz-box-shadow: none;box-shadow: none;}';
					}
					
					$css_rule .= '</style>';
				return $css_rule.$output;
		}
		function init_tp_magic_box(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("Magic Box", "pt_theplus"),
					"base" => "tp_magic_box",
					"as_parent" => array(
						'except' => 'tp_header_breadcrumbs'
					),
					"content_element" => true,
					"show_settings_on_create" => true,
					"icon" => "tp-magic-box",
					"category" => __("The Plus", "pt_theplus"),
					"description" => esc_html__('Time to do Web Magic', 'pt_theplus'),
					"params" => array(
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can put animation on scroll for your section using this option.','pt_theplus').'</span></span>'.esc_html__('Magic Scroll', 'pt_theplus')), 
							'param_name' => 'magic_scroll',
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
									"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose options of animation based on position and scale for section.','pt_theplus').'</span></span>'.esc_html__('Scroll Type', 'pt_theplus')), 
									'param_name' => 'scroll_type',
									'value' => array(
										__('Position', 'pt_theplus') => 'position',
										__('Scale', 'pt_theplus') => 'scale',
										__('Position and Scale', 'pt_theplus') => 'both',
									),
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
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Horizontal Distance. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('(X) / Horizontal Distance', 'pt_theplus')),
							'param_name' => 'distance_scroll_x',
							'value' => '0',
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
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Vertical Distance. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('(Y) / Vertical Distance', 'pt_theplus')),
							'param_name' => 'distance_scroll_y',
							'value' => '50',
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
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of scale of section. e.g. 2 = 200%, 1.5 = 150%','pt_theplus').'</span></span>'.esc_html__('Scale Value', 'pt_theplus')),
							'param_name' => 'scale_scroll',
							'value' => '1',
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
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Border Settings', 'pt_theplus'),
							'param_name' => 'border_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Border Box', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' => __('Border', 'pt_theplus'),
							'param_name' => 'display_border',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"group" => 'Border Box',
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set border width using this option. E.g. 1px, 2px, 3px, etc.','pt_theplus').'</span></span>'.esc_html__('Width', 'pt_theplus')),
							"param_name" => "border_width",
							"value" => __("1px", "pt_theplus"),
							"group" => 'Border Box',
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'display_border',
								'value' => array(
									'on'
								)
							)
						),
						array(
							"type" => "dropdown",
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose style for border using this option. E.g. Solid, Dotted, etc.','pt_theplus').'</span></span>'.esc_html__('Style', 'pt_theplus')), 
							"param_name" => "border_style",
							"value" => array(
								__("Solid", "pt_theplus") => "solid",
								__("None", "pt_theplus") => "none",
								__("Dotted", "pt_theplus") => "dotted",
								__("Dashed", "pt_theplus") => "dashed",
								__("Hidden", "pt_theplus") => "hidden",
								__("Double", "pt_theplus") => "double",
								__("Groove", "pt_theplus") => "groove",
								__("Ridge", "pt_theplus") => "ridge",
								__("Inset", "pt_theplus") => "inset",
								__("Outset", "pt_theplus") => "outset"
							),
							"group" => 'Border Box',
							'edit_field_class' => 'vc_col-sm-6',
							"description" => "",
							"std" => 'solid',
							'dependency' => array(
								'element' => 'display_border',
								'value' => array(
									'on'
								)
							)
						),
						array(
							"type" => "dropdown",
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose radius for border using this option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Radius ', 'pt_theplus')), 
							"param_name" => "border_radius",
							"value" => array(
								__("None", "pt_theplus") => "0px",
								__("1px", "pt_theplus") => "1px",
								__("2px", "pt_theplus") => "2px",
								__("3px", "pt_theplus") => "3px",
								__("4px", "pt_theplus") => "4px",
								__("5px", "pt_theplus") => "5px",
								__("6px", "pt_theplus") => "6px",
								__("8px", "pt_theplus") => "8px",
								__("10px", "pt_theplus") => "10px",
								__("15px", "pt_theplus") => "15px",
								__("20px", "pt_theplus") => "20px",
								__("25px", "pt_theplus") => "25px",
								__("30px", "pt_theplus") => "30px",
								__("35px", "pt_theplus") => "35px"
							),
							"group" => 'Border Box',
							'edit_field_class' => 'vc_col-sm-6',
							"description" => "",
							"std" => "",
							'dependency' => array(
								'element' => 'display_border',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')), 
							'param_name' => 'border_color',
							'value' => '#252525',
							'edit_field_class' => 'vc_col-sm-6',
							'description' => '',
							"group" => 'Border Box',
							'dependency' => array(
								'element' => 'display_border',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Hover Border Settings', 'pt_theplus'),
							'param_name' => 'border_hover_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Border Box', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' => __('Show Hover Border', 'pt_theplus'),
							'param_name' => 'display_border_hover',
							'description' => '',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"group" => 'Border Box',
							'edit_field_class' => 'vc_col-sm-6',
							"edit_field_class" => "vc_col-xs-6"
						),
						array(
							'type' => 'checkbox',
							'class' => '',
							'heading' => __('Hover Border Animated', 'pt_theplus'),
							'param_name' => 'hover_border_animated',
							'value' => array(
								__('Yes, please', 'pt_theplus') => 'yes'
							),
							'edit_field_class' => 'vc_col-sm-6',
							'description' => '',
							"group" => 'Border Box'
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set border width using this option. E.g. 1px, 2px, 3px, etc.','pt_theplus').'</span></span>'.esc_html__('Width', 'pt_theplus')),
							"param_name" => "border_hover_width",
							"value" => __("1px", "pt_theplus"),
							"group" => 'Border Box',
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'display_border_hover',
								'value' => array(
									'on'
								)
							)
						),
						array(
							"type" => "dropdown",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose style for border using this option. E.g. Solid, Dotted, etc.','pt_theplus').'</span></span>'.esc_html__('Style', 'pt_theplus')),
							"param_name" => "border_hover_style",
							"value" => array(
								__("Solid", "pt_theplus") => "solid",
								__("None", "pt_theplus") => "none",
								__("Dotted", "pt_theplus") => "dotted",
								__("Dashed", "pt_theplus") => "dashed",
								__("Hidden", "pt_theplus") => "hidden",
								__("Double", "pt_theplus") => "double",
								__("Groove", "pt_theplus") => "groove",
								__("Ridge", "pt_theplus") => "ridge",
								__("Inset", "pt_theplus") => "inset",
								__("Outset", "pt_theplus") => "outset"
							),
							"group" => 'Border Box',
							'edit_field_class' => 'vc_col-sm-6',
							"std" => 'solid',
							'dependency' => array(
								'element' => 'display_border_hover',
								'value' => array(
									'on'
								)
							)
						),
						array(
							"type" => "dropdown",
						   "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose radius for border using this option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Radius', 'pt_theplus')),
							"param_name" => "border_hover_radius",
							"value" => array(
								__("None", "pt_theplus") => "0px",
								__("1px", "pt_theplus") => "1px",
								__("2px", "pt_theplus") => "2px",
								__("3px", "pt_theplus") => "3px",
								__("4px", "pt_theplus") => "4px",
								__("5px", "pt_theplus") => "5px",
								__("6px", "pt_theplus") => "6px",
								__("8px", "pt_theplus") => "8px",
								__("10px", "pt_theplus") => "10px",
								__("15px", "pt_theplus") => "15px",
								__("20px", "pt_theplus") => "20px",
								__("25px", "pt_theplus") => "25px",
								__("30px", "pt_theplus") => "30px",
								__("35px", "pt_theplus") => "35px"
							),
							"group" => 'Border Box',
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "",
							'dependency' => array(
								'element' => 'display_border_hover',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
							'param_name' => 'border_hover_color',
							'value' => '#ff214f',
							'edit_field_class' => 'vc_col-sm-6',
							"group" => 'Border Box',
							'dependency' => array(
								'element' => 'display_border_hover',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Box Shadow Settings', 'pt_theplus'),
							'param_name' => 'boxshadow_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Box Shadow', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' => __('Show Box Shadow', 'pt_theplus'),
							'param_name' => 'display_boxshadow',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"group" => 'Box Shadow'
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset ,none','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Main Value ', 'pt_theplus')),
							"param_name" => "boxshadow",
							"value" => __("0px 1px 7px 0", "pt_theplus"),
							"group" => 'Box Shadow',
							"edit_field_class" => "vc_col-xs-6",
							'dependency' => array(
								'element' => 'display_boxshadow',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							 "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can can select box shadow color and Opacity for using this option.','pt_theplus').'</span></span>'.esc_html__('Box Shadow Color', 'pt_theplus')),
							'param_name' => 'boxshadow_color',
							'value' => 'rgba(0,0,0,0.4)',
							'edit_field_class' => 'vc_col-sm-6',
							"group" => 'Box Shadow',
							'dependency' => array(
								'element' => 'display_boxshadow',
								'value' => array(
									'on'
								)
							)
						),
						
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Hover Box Shadow Settings', 'pt_theplus'),
							'param_name' => 'boxshadow_hover_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Box Shadow', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' => __('Show Hover Box Shadow ', 'pt_theplus'),
							'param_name' => 'display_boxshadow_hover',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"group" => 'Box Shadow'
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset ,none','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Main Value ', 'pt_theplus')),
							"param_name" => "boxshadow_hover",
							"value" => __("3px 3px 4px 0", "pt_theplus"),
							"group" => 'Box Shadow',
							"edit_field_class" => "vc_col-xs-6",
							'dependency' => array(
								'element' => 'display_boxshadow_hover',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can can select box shadow color and Opacity for using this option.','pt_theplus').'</span></span>'.esc_html__('Box Shadow Color', 'pt_theplus')),
							'param_name' => 'boxshadow_hover_color',
							'value' => 'rgba(0,0,0,0.4)',
							'edit_field_class' => 'vc_col-sm-6',
							"group" => 'Box Shadow',
							'dependency' => array(
								'element' => 'display_boxshadow_hover',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Animation Settings', 'pt_theplus'),
							'param_name' => 'annimation_effect',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can put option of on hover tilt effect on section using this option.','pt_theplus').'</span></span>'.esc_html__('On Hover Tilt', 'pt_theplus')),
							'param_name' => 'hover_parallax',
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
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Using this option You can get animated background color before your content will be loaded.','pt_theplus').'</span></span>'.esc_html__('On Load Animated Background ', 'pt_theplus')),
							'param_name' => 'bg_color_animate',
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
							'type' => 'colorpicker',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Background color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Background color', 'pt_theplus')),
							'param_name' => 'bg_animated_color',
							'value' => '#d3d3d3',
							"edit_field_class" => "vc_col-xs-6",
							'dependency' => array(
								'element' => 'bg_color_animate',
								'value' => array(
									'on'
								)
							)
						),
						array(
							"type" => "dropdown",
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select direction of on Load background color animation using this options.','pt_theplus').'</span></span>'.esc_html__('Animation Direction', 'pt_theplus')),
							"param_name" => "animated_direction",
							"value" => array(
								__('Left', 'pt_theplus') => 'left',
								__('Right', 'pt_theplus') => 'right',
								__('Top', 'pt_theplus') => 'top',
								__('Bottom', 'pt_theplus') => 'bottom',
							),
							'std' => 'left',
							'dependency' => array(
								'element' => 'bg_color_animate',
								'value' => array(
									'on'
								)
							),
							"edit_field_class" => "vc_col-xs-6",
						),
						 array(
							"type" => "dropdown",
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This Effects will be applied when you hover on this section.','pt_theplus').'</span></span>'.esc_html__('Content Hover Effects', 'pt_theplus')),
							"param_name" => "content_hover_effects",
							"value" => array(
								__('Select Hover Effect', 'pt_theplus') => '',
								__('Grow', 'pt_theplus') => 'grow',
								__('Push', 'pt_theplus') => 'push',
								__('Bounce In', 'pt_theplus') => 'bounce-in',
								__('Float', 'pt_theplus') => 'float',
								__('wobble horizontal', 'pt_theplus') => 'wobble_horizontal',
								__('Wobble Vertical', 'pt_theplus') => 'wobble_vertical',
								__('Float Shadow', 'pt_theplus') => 'float_shadow',
								__('Grow Shadow', 'pt_theplus') => 'grow_shadow',
								__('Shadow Radial', 'pt_theplus') => 'shadow_radial'
							),
							'edit_field_class' => 'vc_col-sm-6',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Shadow Color', 'pt_theplus'),
							'param_name' => 'hover_shadow_color',
							'value' => 'rgba(0, 0, 0, 0.6)',
							'edit_field_class' => 'vc_col-sm-6',
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'content_hover_effects',
								'value' => array(
									'float_shadow',
									'grow_shadow',
									'shadow_radial'
								)
							)
						),
						array(
								'type' => 'dropdown',
								"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This Effect will be applied on section as a continuous effect in normal and hover state.','pt_theplus').'</span></span>'.esc_html__('Continuous Effect', 'pt_theplus')),
								'param_name' => 'continuous_effect',
								'value' => array(
									__('None', 'pt_theplus') => '',
									__('Pulse', 'pt_theplus') => 'pulse',
									__('Floating', 'pt_theplus') => 'floating',
									__('Tossing', 'pt_theplus') => 'tossing'
								),
								'edit_field_class' => 'vc_col-sm-6',
						),		
						array(
							"type" => "dropdown",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
							"param_name" => "animation_effects",
							"value" => array(
								__('No-animation', 'pt_theplus') => 'no-animation',
								__('FadeIn', 'pt_theplus') => 'transition.fadeIn',
								__('FlipXIn', 'pt_theplus') => 'transition.flipXIn',
								__('FlipYIn', 'pt_theplus') => 'transition.flipYIn',
								__('FlipBounceXIn', 'pt_theplus') => 'transition.flipBounceXIn',
								__('FlipBounceYIn', 'pt_theplus') => 'transition.flipBounceYIn',
								__('SwoopIn', 'pt_theplus') => 'transition.swoopIn',
								__('WhirlIn', 'pt_theplus') => 'transition.whirlIn',
								__('ShrinkIn', 'pt_theplus') => 'transition.shrinkIn',
								__('ExpandIn', 'pt_theplus') => 'transition.expandIn',
								__('BounceIn', 'pt_theplus') => 'transition.bounceIn',
								__('BounceUpIn', 'pt_theplus') => 'transition.bounceUpIn',
								__('BounceDownIn', 'pt_theplus') => 'transition.bounceDownIn',
								__('BounceLeftIn', 'pt_theplus') => 'transition.bounceLeftIn',
								__('BounceRightIn', 'pt_theplus') => 'transition.bounceRightIn',
								__('SlideUpIn', 'pt_theplus') => 'transition.slideUpIn',
								__('SlideDownIn', 'pt_theplus') => 'transition.slideDownIn',
								__('SlideLeftIn', 'pt_theplus') => 'transition.slideLeftIn',
								__('SlideRightIn', 'pt_theplus') => 'transition.slideRightIn',
								__('SlideUpBigIn', 'pt_theplus') => 'transition.slideUpBigIn',
								__('SlideDownBigIn', 'pt_theplus') => 'transition.slideDownBigIn',
								__('SlideLeftBigIn', 'pt_theplus') => 'transition.slideLeftBigIn',
								__('SlideRightBigIn', 'pt_theplus') => 'transition.slideRightBigIn',
								__('PerspectiveUpIn', 'pt_theplus') => 'transition.perspectiveUpIn',
								__('PerspectiveDownIn', 'pt_theplus') => 'transition.perspectiveDownIn',
								__('PerspectiveLeftIn', 'pt_theplus') => 'transition.perspectiveLeftIn',
								__('PerspectiveRightIn', 'pt_theplus') => 'transition.perspectiveRightIn'
							),
							'edit_field_class' => 'vc_col-sm-6',
							'std' => 'no-animation'
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),
							"param_name" => "animation_delay",
							"value" => '50',
							'edit_field_class' => 'vc_col-sm-6',
						),
						 array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Extra Settings', 'pt_theplus'),
							'param_name' => 'extra_option',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Extra Class here to use for Customisation Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
							"param_name" => "el_class",
							'edit_field_class' => 'vc_col-sm-6',
						),		
						array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of padding in between box and content.','pt_theplus').'</span></span>'.esc_html__('Box Inner Padding', 'pt_theplus')),
							'param_name' => 'inner_padding',
							'value' => '10px',
							'edit_field_class' => 'vc_col-sm-6',
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
						array(
							'type' => 'css_editor',
							'heading' => __('CSS box', 'pt_theplus'),
							'param_name' => 'css_box',
							'group' => __('Design Options', 'pt_theplus')
						)
					),
					"js_view" => 'VcColumnView'
				));
			}
		}
	}
	new ThePlus_magic_box;

	if(class_exists('WPBakeryShortCodesContainer') && !class_exists('WPBakeryShortCode_tp_magic_box'))
	{
		class WPBakeryShortCode_tp_magic_box extends WPBakeryShortCodesContainer
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}