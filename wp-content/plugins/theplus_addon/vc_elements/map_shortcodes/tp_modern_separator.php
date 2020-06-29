<?php 
// Row Separator Elements
if(!class_exists("ThePlus_modern_separator")){
	class ThePlus_modern_separator{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_modern_separator') );
			add_shortcode( 'tp_modern_separator',array($this,'tp_modern_separator_shortcode'));
		}
		function tp_modern_separator_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				'top_bottom_style'=>'flat-divider-triangles',
				'top_separate_color'=>'#337ebf',
				'top_sep_color'=>'#000000',
				'bottom_sep_color'=>'#dd3333',
				'separate_image'=>'',
				'center_image'=>'',
				'hover_sep_image'=>'',
				'imagexposition'=>'50%',
				'imageyposition'=>'0px',
				'image_shadow'=>'',
				'image_radius'=>'0%',
				'image_link'=>'',
				'animated_hover_image'=>'',
				'image_sep_effect'=>'',
				   ), $atts ) );


				$separate_class=$css_rule='';
				$separate_uniqid = uniqid('pt-plus-row-separator');

					if($top_bottom_style=='flat-divider-triangles'){
						$separate_class .=' style-top-traingles';
					}
					if($top_bottom_style=='halfcircle'){
						$separate_class .=' style-top-halfcircle';
					}
					if($top_bottom_style=='multi_arrow'){
						$separate_class .=' style-top-multiarrow';
					}
					if($top_bottom_style=='3d_traingle_spikes'){
						$separate_class .=' style-top-3d-traingle';
					}
					if($top_bottom_style=='roundedsplit'){
						$separate_class .=' style-roundedsplit';
					}
					if($top_bottom_style=='diagonal_shadow'){
						$separate_class .=' style-diagonal-shadow';
					}
					if($top_bottom_style=='left_diagonal'){
						$separate_class .=' style-left-diagonal';
					}
					if($top_bottom_style=='small_boxes'){
						$separate_class .=' style-small-boxes';
					}

					if($top_bottom_style=='incizing'){
						$separate_class .=' style-inczigzag';
					}

					if($top_bottom_style=='foldedcorner'){
						$separate_class .=' style-foldedcorner';
					}

					if($top_bottom_style=='dots'){
						$separate_class .=' style-dots';
					}
					$separate_imgSrc='';
					if($top_bottom_style=='custom_image'){
						$separate_class .=' style-custom-image';
						$sep_image=wp_get_attachment_image_src($separate_image, "full");
					}

					$output ='<div id="'.esc_attr($separate_uniqid).'" class="pt-plus-row-separator pt-plus-row-separator-style-top-bottom '.$separate_class.'">';
					if($top_bottom_style=='bigtriangles'){
						$output .='<svg id="traingle_top_svg" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="70" viewBox="0 0 100 102" preserveAspectRatio="none" style="fill: '.esc_attr($top_separate_color).';stroke:none;"><path d="M0 0 L50 100 L100 0 Z" style="fill: '.esc_attr($top_separate_color).';stroke:none;"></path></svg>';
					}
					if($top_bottom_style=='up_triangles'){
						$output .='<svg xmlns="http://www.w3.org/2000/svg" version="1.1"  width="100%" height="100" viewBox="0 0 4.66666 0.333331" preserveAspectRatio="none" style="fill: '.esc_attr($top_separate_color).';stroke:none;"> <path class="fil0" d="M-0 0.333331l4.66666 0 0 -3.93701e-006 -2.33333 0 -2.33333 0 0 3.93701e-006zm0 -0.333331l4.66666 0 0 0.166661 -4.66666 0 0 -0.166661zm4.66666 0.332618l0 -0.165953 -4.66666 0 0 0.165953 1.16162 -0.0826181 1.17171 -0.0833228 1.17171 0.0833228 1.16162 0.0826181z" style="fill: '.esc_attr($top_separate_color).';stroke:none;"></path> </svg>';
					}
					if($top_bottom_style=='curveup'){
						$output .='<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="70" viewBox="0 0 100 100" preserveAspectRatio="none" style="fill: '.esc_attr($top_separate_color).';position: absolute;top: -70px;width: 100%;"><path d="M0 100 C 20 0 50 0 100 100 Z" style="fill: '.esc_attr($top_separate_color).';stroke:none;"></path></svg>';
					}
					if($top_bottom_style=='curvedown'){
						$output .='<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="70" viewBox="0 0 100 100" preserveAspectRatio="none" style="fill: '.esc_attr($top_separate_color).';stroke:none;position: absolute;top: 0px;width: 100%;"><path d="M0 0 C 50 100 80 100 100 0 Z" style="fill: '.esc_attr($top_separate_color).';stroke:none;"></path></svg>';
					}
					if($top_bottom_style=='stamp'){
						$output .='<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none" style="fill: '.esc_attr($top_separate_color).';stroke:none;"><path d="M0 0 Q 2.5 40 5 0 Q 7.5 40 10 0 Q 12.5 40 15 0 Q 17.5 40 20 0 Q 22.5 40 25 0 Q 27.5 40 30 0 Q 32.5 40 35 0 Q 37.5 40 40 0 Q 42.5 40 45 0 Q 47.5 40 50 0 Q 52.5 40 55 0 Q 57.5 40 60 0 Q 62.5 40 65 0 Q 67.5 40 70 0 Q 72.5 40 75 0 Q 77.5 40 80 0 Q 82.5 40 85 0 Q 87.5 40 90 0 Q 92.5 40 95 0 Q 97.5 40 100 0 Z" style="fill: '.esc_attr($top_separate_color).';stroke:none;"></path></svg>';
					}
					if($top_bottom_style=='clouds'){
						$output .='<svg id="clouds" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="70" viewBox="0 0 100 100" preserveAspectRatio="none" style="fill: '.esc_attr($top_separate_color).';stroke:none;position: absolute;top: -70px;width: 100%;"><path d="M-5 100 Q 0 20 5 100 Z M0 100 Q 5 0 10 100 M5 100 Q 10 30 15 100 M10 100 Q 15 10 20 100 M15 100 Q 20 30 25 100 M20 100 Q 25 -10 30 100 M25 100 Q 30 10 35 100 M30 100 Q 35 30 40 100 M35 100 Q 40 10 45 100 M40 100 Q 45 50 50 100 M45 100 Q 50 20 55 100 M50 100 Q 55 40 60 100 M55 100 Q 60 60 65 100 M60 100 Q 65 50 70 100 M65 100 Q 70 20 75 100 M70 100 Q 75 45 80 100 M75 100 Q 80 30 85 100 M80 100 Q 85 20 90 100 M85 100 Q 90 50 95 100 M90 100 Q 95 25 100 100 M95 100 Q 100 15 105 100 Z" style="fill: '.esc_attr($top_separate_color).';stroke:none;"></path></svg>';
					}

				if(!empty($center_image) && isset($center_image)){
					$image_link = ( '||' === $image_link ) ? '' : $image_link;
					$image_link= vc_build_link( $image_link);
					$a_href = $image_link['url'];
					$a_title = $image_link['title'];
					$a_target = $image_link['target'];
					$a_rel = $image_link['rel'];
					if ( ! empty( $a_rel ) ) {
						$a_rel = ' rel="' . esc_attr( trim( $a_rel ) ) . '"';
					}

					$center_sep_image=wp_get_attachment_image_src($center_image, "full");
					$image_alt=get_post_meta( $center_image, '_wp_attachment_image_alt', true);
					$top_height ="-".$center_sep_image[2]/2;
					$margin_left ="-".$center_sep_image[1]/2;
					$image_style='';

					$image_style .='top: '.$top_height.'px;';
					if(!empty($imagexposition)){
						$image_style .='left: '.esc_attr($imagexposition).';';
					}

					if(!empty($margin_left)){
						$image_style .='margin-left:'.esc_attr($margin_left).'px;';
					}

					if(!empty($imageyposition)){
						$image_style .='margin-top:'.esc_attr($imageyposition).';';
					}

					if(!empty($image_shadow)){
						$image_style .='-webkit-box-shadow:'.esc_attr($image_shadow).';-moz-box-shadow:'.esc_attr($image_shadow).';box-shadow:'.esc_attr($image_shadow).';';
					}
					if(!empty($image_radius)){
						$image_style .='-moz-border-radius:'.esc_attr($image_radius).';-webkit-border-radius: '.esc_attr($image_radius).';border-radius:'.esc_attr($image_radius).';';
					}
					
					$hover_img=$hover_class=$hover_style='';
					if(!empty($hover_sep_image)){
						$hover_image=wp_get_attachment_image_src($hover_sep_image, "full");
						$hover_img .='<img src="'.esc_url($hover_image[0]).'" class="hover-separate-image" width="'.esc_attr($hover_image[1]).'" height="'.esc_attr($hover_image[2]).'" alt="">';
						$hover_class .='hover-image';
						$hover_style.= 'style="position:absolute;"';
					}

						$output .='<a class="pt-plus-animated-hvr '.esc_attr($animated_hover_image).' '.esc_attr($image_sep_effect).' '.esc_attr($hover_class).'" href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'" target="'.esc_attr( $a_target ).'" '.$a_rel.' style="position: absolute;z-index: 11;'.$image_style.'" >';
							$output .='<img src="'.esc_url($center_sep_image[0]).'" class="normal-separate-image" width="'.esc_attr($center_sep_image[1]).'" height="'.esc_attr($center_sep_image[2]).'" '.$hover_style.' tag="'.esc_attr($image_alt).'" alt="" />';
							$output .=$hover_img;
						$output .='</a>';
				}

				$output .='</div>';
				$css_rule .= '<style >';
					if($top_bottom_style=='flat-divider-triangles' || $top_bottom_style=='halfcircle' || $top_bottom_style=='multi_arrow'){
						$css_rule .='#'.esc_js($separate_uniqid).':before{background :'.esc_js($top_separate_color).';}';
					}
					if($top_bottom_style=='multi_arrow'){
						$css_rule .='#'.esc_js($separate_uniqid).'.style-top-multiarrow:before{ -webkit-box-shadow:-50px 50px 0 '.esc_js($top_separate_color).', 50px -50px 0 '.esc_js($top_separate_color).';-moz-box-shadow:-50px 50px 0 '.esc_js($top_separate_color).', 50px -50px 0 '.esc_js($top_separate_color).';box-shadow:-50px 50px 0 '.esc_js($top_separate_color).', 50px -50px 0 '.esc_js($top_separate_color).';}';
					}
					if($top_bottom_style=='3d_traingle_spikes'){
						$css_rule .='#'.esc_js($separate_uniqid).'.style-top-3d-traingle:before{ background-image: -webkit-gradient(linear, 0 0, 300% 100%, color-stop(0.25, transparent), color-stop(0.25, '.esc_js($top_separate_color).'));background-image: linear-gradient(315deg, '.esc_js($top_separate_color).' 25%, transparent 25%), linear-gradient( 45deg, '.esc_js($top_separate_color).' 25%, transparent 25%);}';
					}
					if($top_bottom_style=='roundedsplit'){
						$css_rule .='#'.esc_js($separate_uniqid).':before{background :'.esc_js($top_separate_color).';}#'.esc_js($separate_uniqid).':after{background :'.esc_js($top_separate_color).';}';
					}
					if($top_bottom_style=='diagonal_shadow'){
						$css_rule .='#'.esc_js($separate_uniqid).'{background :'.esc_js($top_separate_color).';}';
					}
					if($top_bottom_style=='left_diagonal'){
						$css_rule .='#'.esc_js($separate_uniqid).':after{background :'.esc_js($top_separate_color).';}';
					}
					if($top_bottom_style=='small_boxes'){		
						$css_rule .='#'.esc_js($separate_uniqid).':before{background-image: -webkit-gradient(linear, 100% 0, 0 100%, color-stop(0.5, '.esc_js($top_sep_color).'), color-stop(0.5, '.esc_js($bottom_sep_color).'));background-image: linear-gradient(to right, '.esc_js($top_sep_color).' 50%, '.esc_js($bottom_sep_color).' 50%);}';
					}
					if($top_bottom_style=='incizing'){		
						$css_rule .='#'.esc_js($separate_uniqid).':before{background-image: -webkit-gradient(linear, 0 0, 10% 100%, color-stop(0.5, '.esc_js($top_sep_color).'), color-stop(0.5, '.esc_js($bottom_sep_color).'));background-image: linear-gradient(15deg, '.esc_js($bottom_sep_color).' 50%, '.esc_js($top_sep_color).' 50%);}';
					}
					if($top_bottom_style=='foldedcorner'){
						$css_rule .='#'.esc_js($separate_uniqid).':before{background-image: -webkit-linear-gradient(top left, '.esc_js($top_sep_color).' 50%, '.esc_js($bottom_sep_color).' 50%);background-image: linear-gradient(315deg, '.esc_js($bottom_sep_color).' 50%, '.esc_js($bottom_sep_color).' 50%);}#'.esc_js($separate_uniqid).':after{background-image: -webkit-linear-gradient(top left, transparent 50%, '.esc_js($top_sep_color).' 50%);background-image: linear-gradient(315deg, '.esc_js($top_sep_color).' 50%, transparent 50%);}';
					}
					if($top_bottom_style=='dots'){
						$css_rule .='#'.esc_js($separate_uniqid).':before{background :'.esc_js($top_separate_color).';-webkit-box-shadow: 30px 0 '.esc_js($top_separate_color).', -30px 0 '.esc_js($top_separate_color).';-moz-box-shadow: 30px 0 '.esc_js($top_separate_color).', -30px 0 '.esc_js($top_separate_color).';box-shadow: 30px 0 '.esc_js($top_separate_color).', -30px 0 '.esc_js($top_separate_color).';}';
					}
					if($top_bottom_style=='custom_image' && !empty($sep_image)){
						$height=$sep_image[2];
						$top_offset=$height/2;
						$css_rule .='#'.esc_js($separate_uniqid).':before{ background: url('.esc_js($sep_image[0]).') repeat-x 0 0;height:'.esc_js($height).'px;top:-'.esc_js($top_offset).'px;}';
					}
					$css_rule .= '</style>';
				return $css_rule.$output;
		}
		function init_tp_modern_separator(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("Row Separator", "pt_theplus"),
					"base" => "tp_modern_separator",
					"icon" => "tp-row-separator",
					"category" => __("The Plus", "pt_theplus"),
					"description" => esc_html__('Showcase Dividers Elegantly', 'pt_theplus'),
					"params" => array(
						
						array(
								'type'        => 'radio_select_image',
								"heading" => esc_html__("Separator Style", "pt_theplus"),
								'param_name'  => 'top_bottom_style',
								'simple_mode' => false,
								'admin_label' => true,
								'value'		=> 'flat-divider-triangles',
								'group' => esc_attr__('Top Bottom Separator', 'pt_theplus'),
								'options'     => array(
									'flat-divider-triangles' => array(
										'tooltip' => esc_attr__('Flat Divider Triangles','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/flat-divider-triangles.jpg'
									),
									'halfcircle' => array(
										'tooltip' => esc_attr__('Half Circle','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/halfcircle.jpg'
									),
									'bigtriangles' => array(
										'tooltip' => esc_attr__('Down Triangles','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/bigtriangles.jpg'
									),
									'up_triangles' => array(
										'tooltip' => esc_attr__('Up Triangles','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/up_triangles.jpg'
									),
									'multi_arrow' => array(
										'tooltip' => esc_attr__('Multi Arrow','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/multi_arrow.jpg'
									),
									'3d_traingle_spikes' => array(
										'tooltip' => esc_attr__('3D Triangle Spikes','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/3d_traingle_spikes.jpg'
									),
									'roundedsplit' => array(
										'tooltip' => esc_attr__('Round Split','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/roundedsplit.jpg'
									),
									'diagonal_shadow' => array(
										'tooltip' => esc_attr__('Diagonal Shadow','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/diagonal_shadow.jpg'
									),
									'left_diagonal' => array(
										'tooltip' => esc_attr__('Left Diagonal','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/left_diagonal.jpg'
									),
									'curveup' => array(
										'tooltip' => esc_attr__('Curve Up','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/curveup.jpg'
									),
									'curvedown' => array(
										'tooltip' => esc_attr__('Curve Down','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/curvedown.jpg'
									),
									'small_boxes' => array(
										'tooltip' => esc_attr__('Small Boxes','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/small_boxes.jpg'
									),
									'incizing' => array(
										'tooltip' => esc_attr__('Incizing','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/incizing.jpg'
									),
									'foldedcorner' => array(
										'tooltip' => esc_attr__('Folded Corner','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/foldedcorner.jpg'
									),
									'dots' => array(
										'tooltip' => esc_attr__('Dots','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/dots.jpg'
									),
									'stamp' => array(
										'tooltip' => esc_attr__('Stamp','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/stamp.jpg'
									),
									'clouds' => array(
										'tooltip' => esc_attr__('Clouds','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/clouds.jpg'
									),
									'custom_image' => array(
										'tooltip' => esc_attr__('Custom Image','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-separator/custom_image.jpg'
									),
								),
							),
						array(
							'type' => 'colorpicker',
							'heading' => __('Color', 'pt_theplus'),
							'param_name' => 'top_separate_color',
							'value' => '#337ebf',
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus'),
							'dependency' => array(
								'element' => 'top_bottom_style',
								'value' => array(
									'flat-divider-triangles',
									'halfcircle',
									'bigtriangles',
									'multi_arrow',
									'3d_traingle_spikes',
									'roundedsplit',
									'diagonal_shadow',
									'left_diagonal',
									'up_triangles',
									'curveup',
									'curvedown',
									'dots',
									'stamp',
									'clouds'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Top Color', 'pt_theplus'),
							'param_name' => 'top_sep_color',
							'value' => '#000000',
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus'),
							'dependency' => array(
								'element' => 'top_bottom_style',
								'value' => array(
									'small_boxes',
									'incizing',
									'foldedcorner'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Bottom Color', 'pt_theplus'),
							'param_name' => 'bottom_sep_color',
							'value' => '#dd3333',
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus'),
							'dependency' => array(
								'element' => 'top_bottom_style',
								'value' => array(
									'small_boxes',
									'incizing',
									'foldedcorner'
								)
							)
						),
						array(
							'type' => 'attach_image',
							'heading' => __('Upload Image', 'pt_theplus'),
							'param_name' => 'separate_image',
							"description" => "",
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus'),
							'dependency' => array(
								'element' => 'top_bottom_style',
								'value' => array(
									'custom_image'
								)
							)
						),
						array(
							'type' => 'attach_image',
							'heading' => __('Normal Image', 'pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'param_name' => 'center_image',
							"description" => "",
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus')
						),
						array(
							'type' => 'attach_image',
							'heading' => __('Hover Image', 'pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'param_name' => 'hover_sep_image',
							"description" => "",
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' => __('X-Position', 'pt_theplus'),
							'param_name' => 'imagexposition',
							'value' => array(
								__('10%', 'pt_theplus') => '10%',
								__('20%', 'pt_theplus') => '20%',
								__('30%', 'pt_theplus') => '30%',
								__('40%', 'pt_theplus') => '40%',
								__('50%', 'pt_theplus') => '50%',
								__('60%', 'pt_theplus') => '60%',
								__('70%', 'pt_theplus') => '70%',
								__('80%', 'pt_theplus') => '80%',
								__('90%', 'pt_theplus') => '90%',
								__('100%', 'pt_theplus') => '100%'
							),
							'admin_label' => true,
							'std' => '50%',
							"description" => __("Select x position of image Center of 50%", "pt_theplus"),
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' => __('Y-Position', 'pt_theplus'),
							"description" => __("E.g. 10px,-10px,20px,-25px...etc..", "pt_theplus"),
							'param_name' => 'imageyposition',
							'admin_label' => true,
							'value' => '0px',
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' => __('Image Shadow', 'pt_theplus'),
							"description" => __("E.g. 1px 1px 2px 2px #d3d3d3 etc..", "pt_theplus"),
							'param_name' => 'image_shadow',
							'value' => '',
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' => __('Border Radius', 'pt_theplus'),
							'param_name' => 'image_radius',
							'value' => array(
								__('0%', 'pt_theplus') => '0%',
								__('10%', 'pt_theplus') => '10%',
								__('20%', 'pt_theplus') => '20%',
								__('30%', 'pt_theplus') => '30%',
								__('40%', 'pt_theplus') => '40%',
								__('50%', 'pt_theplus') => '50%',
								__('60%', 'pt_theplus') => '60%',
								__('70%', 'pt_theplus') => '70%',
								__('80%', 'pt_theplus') => '80%',
								__('90%', 'pt_theplus') => '90%',
								__('100%', 'pt_theplus') => '100%'
							),
							'std' => '0%',
							"description" => __("Image Rounded Shadow of 50%", "pt_theplus"),
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' => __('Animated Hover Effect', 'pt_theplus'),
							'param_name' => 'animated_hover_image',
							'value' => array(
								__('Select Option', 'pt_theplus') => '',
								__('Zoom In', 'pt_theplus') => 'hvr-grow',
								__('Zoom Out', 'pt_theplus') => 'hvr-shrink',
								__('Rotate', 'pt_theplus') => 'hvr-rotate-image',
								__('Pulse', 'pt_theplus') => 'hvr-pulse',
								__('Pulse Grow', 'pt_theplus') => 'hvr-pulse-grow',
								__('Push', 'pt_theplus') => 'hvr-push',
								__('Bounce-In', 'pt_theplus') => 'hvr-bounce-in',
								__('Bounce-In', 'pt_theplus') => 'hvr-bounce-out',
								__('Float', 'pt_theplus') => 'hvr-float',
								__('BoB', 'pt_theplus') => 'hvr-bob',
								__('Wobble Vertical', 'pt_theplus') => 'hvr-wobble-vertical',
								__('Buzz', 'pt_theplus') => 'hvr-buzz',
								__('Buzz Out', 'pt_theplus') => 'hvr-buzz-out',
								__('Grow Rotate', 'pt_theplus') => 'hvr-grow-rotate',
								__('Wobble Horizontal', 'pt_theplus') => 'hvr-wobble-horizontal',
								__('Wobble To Bottom Right', 'pt_theplus') => 'hvr-wobble-to-bottom-right',
								__('Forward', 'pt_theplus') => 'hvr-forward',
								__('Backward', 'pt_theplus') => 'hvr-backward',
								__('Wobble Skew', 'pt_theplus') => 'hvr-wobble-skew',
								__('Flip', 'pt_theplus') => 'flip-image'
							),
							"description" => __("Animated Hover Image", "pt_theplus"),
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' => __('Image Effects', 'pt_theplus'),
							'param_name' => 'image_sep_effect',
							'value' => array(
								__('Select Option', 'pt_theplus') => '',
								__('Pulse', 'pt_theplus') => 'pulse',
								__('Floating', 'pt_theplus') => 'floating',
								__('Tossing', 'pt_theplus') => 'tossing',
								__('Rotating', 'pt_theplus') => 'rotate-continue'
							),
						   
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus')
						),
						array(
							'type' => 'vc_link',
							'heading' => __('Image URL (Link)', 'pt_theplus'),
							'param_name' => 'image_link',
							'description' => __('Add link to Image.', 'pt_theplus'),
							'group' => esc_attr__('Top Bottom Separator', 'pt_theplus')
						)
						
					)
				));
			}
		}
	}
	new ThePlus_modern_separator;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_modern_separator'))
	{
		class WPBakeryShortCode_tp_modern_separator extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}

