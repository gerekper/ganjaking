<?php
// Gallerys List Elements
if(!class_exists("ThePlus_smart_gallery")){
	class ThePlus_smart_gallery{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_smart_gallery') );
			add_shortcode( 'tp_smart_gallery',array($this,'tp_smart_gallery_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_smart_gallery_scripts' ), 1 );
		}
		function tp_smart_gallery_scripts() {
			wp_register_style( 'theplus-smart-gallery', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-smart-gallery.css', false, '1.0.0' );
		}
		function tp_smart_gallery_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				'group_gallery_images'=>'',
				'layout'=>'grid',
				'animation_effects'=>'transition.fadeIn',
				'animation_delay'=>'50',
				'animated_column_list'=>'',
				'animation_stagger'=>'150',
				'desktop_column'=>'3',
				'tablet_column'=>'6',
				'mobile_column'=>'12',
				'metro_column'=>'4',
				'invert_hover_opt'=>'off',
				'overlay_color'=>'#313131',
				'invert_hover_val'=>'0.4',
				'column_space' =>'',
				'column_space_pading' => '10px',
				'el_class' =>'',
				), $atts ) );
				
				wp_enqueue_style( 'theplus-smart-gallery');
				$rand_no=rand(1000000, 1500000);
				
				$desktop_class='vc_col-md-'.esc_attr($desktop_column);
				$tablet_class='vc_col-sm-'.esc_attr($tablet_column);
				$mobile_class='vc_col-xs-'.esc_attr($mobile_column);
				
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
				$animated_attr='';
				$animated_attr .=' data-animate-type="'.esc_attr($animation_effects).'"';
				$animated_attr .=' data-animate-delay="'.esc_attr($animation_delay_time).'"';
				$animated_columns='';
				if($animated_column_list==''){
					$animated_columns='';
				}else if($animated_column_list=='columns'){
					if($layout=='grid' || $layout=='masonry' || $layout=='metro'){
						$animated_columns='animated-columns';
						$animated_attr .=' data-animate-columns="columns"';
					}else{
						$animated_columns='';
					}
				}else if($animated_column_list=='stagger'){
					if($layout=='grid' || $layout=='masonry' || $layout=='metro'){
						$animated_columns='animated-columns';
						$animated_attr .=' data-animate-columns="stagger"';
						$animated_attr .=' data-animate-stagger="'.esc_attr($animation_stagger).'"';
					}else{
						$animated_columns='';
					}
				}
				$attr='';
				$attr .=' data-enable-isotope="1" ';
				$attr .=' data-id="gallery-'.esc_attr($rand_no).'"';
				$isotope=' list-isotope ';
				if($layout=='grid'){
					$attr .=' data-layout-type="fitRows" ';
					$isotope=' list-isotope ';
				}else if($layout=='masonry'){
					$attr .=' data-layout-type="masonry" ';
					$isotope=' list-isotope ';
				}else if($layout=='metro'){
					$attr .=' data-layout-type="metro" ';
					$isotope=' list-isotope-metro ';
					$attr .=' data-columns="'.esc_attr($metro_column).'" ';
					$attr .=' data-pad="30px" ';
				}
				
				
				
				if($column_space == 'on'){
					$column_padding =$column_space_pading;	
				}else{
					$column_padding ='0px';	
				}
				
				$gallery_listing = '<div id="pt-plus-smart-gallery" class="plus-smart-gallery '.esc_attr($isotope).'  '.esc_attr($animated_class).' gallery-'.esc_attr($rand_no).' '.esc_attr($el_class).' " '.$animated_attr.' '.$attr.' >';
				$gallery_listing .= '<div class="post-inner-loop gallery-'.esc_attr($rand_no).' ">';
				$i=1;
				if($layout=='metro'){
						$desktop_class=$tablet_class=$mobile_class='';
				}
				if(isset($group_gallery_images) && !empty($group_gallery_images) && function_exists('vc_param_group_parse_atts')) {
					$group_gallery_images= (array) vc_param_group_parse_atts( $group_gallery_images);		
					foreach($group_gallery_images as $item) {
						$uid=uniqid("gallery");
						if(isset($item['gallery_images']) && !empty($item['gallery_images'])){
							$image_ids=explode(',',$item['gallery_images']);
							if(isset($item['set_interval_time']) && !empty($item['set_interval_time'])){
								$set_interval_time=$item['set_interval_time'];
							}else{
								$set_interval_time='3000';
							}
							$pretty_rel_random = ' data-rel="prettyPhoto[rel-' . get_the_ID() . '-' . rand() . ']"';
							
							$gallery_listing .= '<div class="grid-item  metro-item'.esc_attr($i).' '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.esc_attr($animated_columns).'" >';
							$gallery_listing .='<div class="gallery-attach-list '.esc_attr($uid).'" data-uid="'.esc_attr($uid).'" data-interval-time="'.esc_attr($set_interval_time).'">';
							foreach( $image_ids as $image_id ){
								$images='';
								$attachment = get_post($image_id);
								if($attachment){
									$image_alt=get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
									$full_image=wp_get_attachment_image_src($image_id,'full');
									$title=$attachment->post_title;
									if(isset($layout) && $layout=='grid'){		
										$images=wp_get_attachment_image_src($image_id,'tp-image-grid');								
									}else if(isset($layout) && $layout=='masonry'){
										$images=wp_get_attachment_image_src($image_id,'full');						
									}else{
										$images=wp_get_attachment_image_src($image_id,'full');						
									}
									
											$data_attr='';
											if($layout=='metro'){
												if ( $images ) {
													$data_attr='style="background :url('.esc_url($images[0]).')";';
												}
											}
											$gallery_listing .='<div class="gallery-list-item" '.$data_attr.'>';												
													if (!empty($images) && $layout!='metro') {
														$gallery_listing .='<a href="'.esc_url($images[0]).'" class="gallery-image prettyphoto" '.$pretty_rel_random.'>';
																$gallery_listing .='<img src="'.esc_url($images[0]).'" alt="'.esc_attr($title).'">';
														$gallery_listing .='</a>';
														}												
											$gallery_listing .='</div>';
								}
								$i++;
							}
							$gallery_listing .='</div>';
							$gallery_listing .= '</div>';
						}
					}
				}
				$gallery_listing .= '</div>';
				$gallery_listing .= '</div>';
				$css_rule='';
				$css_rule .= '<style >';
					$css_rule .= '.gallery-'.esc_js($rand_no).'.plus-smart-gallery .post-inner-loop .grid-item{padding : '.esc_js($column_padding).'; }';
					if(!empty($invert_hover_opt) && $invert_hover_opt=='on'){
						$css_rule .= '#pt-plus-smart-gallery.gallery-'.esc_js($rand_no).' .grid-item .gallery-attach-list{-webkit-transition: all 0.5s ease 0.12s;-moz-transition: all 0.5s ease 0.12s;-ms-transition: all 0.5s ease 0.12s;-o-transition: all 0.5s ease 0.12s;transition: all 0.5s ease 0.12s;}.gallery-'.esc_js($rand_no).'.plus-smart-gallery .post-inner-loop:hover .gallery-attach-list{opacity:'.esc_js($invert_hover_val).';}.gallery-'.esc_js($rand_no).'.plus-smart-gallery .post-inner-loop:hover .gallery-attach-list:hover{opacity:1;}';
						$css_rule .= '.gallery-'.esc_js($rand_no).'.plus-smart-gallery .post-inner-loop:hover .grid-item{background: '.esc_js($overlay_color).';-webkit-transition: all 0.3s ease 0s;-moz-transition: all 0.3s ease 0s;-ms-transition: all 0.3s ease 0s;-o-transition: all 0.3s ease 0s;transition: background 0.3s ease 0s; }.gallery-'.esc_js($rand_no).'.plus-smart-gallery .post-inner-loop .grid-item {-webkit-transition: all 0.3s ease 0.2s;-moz-transition: all 0.3s ease 0.2s;-ms-transition: all 0.3s ease 0.2s;-o-transition: all 0.3s ease 0.2s;transition: background 0.3s ease 0.2s;}';
					}
					
					
				$css_rule .= '</style>';
				return $css_rule.$gallery_listing;
		}
		function init_tp_smart_gallery(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => esc_html__("Smart Gallery", 'pt_theplus'),
					"base" => "tp_smart_gallery",
					"icon" => "tp-gallery-list",
					"category" => esc_html__("The Plus", "pt_theplus"),
					"description" => esc_html__('Modern Auto flip gallery images', 'pt_theplus'),
					"params" => array(
						array(
							'type' => 'param_group',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add multiple gallery image sections using this option.','pt_theplus').'</span></span>'.esc_html__('Add Multiple Gallery Image Sections', 'pt_theplus')),
							'param_name' => 'group_gallery_images',
							'params' => array(
								array(
									"type"        => "attach_images",
									"heading"     => esc_html__( "Add Multiple Images", "pt_theplus" ),
									"param_name"  => "gallery_images",
									"value"       => '',
									"admin_label" => true,
								),
								array(
									"type" => "textfield",
									"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond','pt_theplus').'</span></span>'.esc_html__('Set Interval Time', 'pt_theplus')),
									"param_name" => "set_interval_time",
									"value" => '2000',
									"edit_field_class" => "vc_col-xs-6",
									"description" => "",
									"admin_label" => true,
								),
							),
						),
					   array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Listing Layout', 'pt_theplus'), 
								'param_name'  => 'layout',
								'simple_mode' => false,
								"admin_label" => true,
								'value' => 'grid',
								'options'     => array(
									'grid' => array(
										'tooltip' => esc_attr__('Grid Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/grid.jpg'
									),
									'masonry' => array(
										'tooltip' => esc_attr__('Masonry Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/masonry.jpg'
									),
									'metro' => array(
										'tooltip' => esc_attr__('Metro Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/metro.jpg'
									),
								),
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
							"admin_label" => false,
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
							"edit_field_class" => "vc_col-xs-6",
							'std' => 'transition.fadeIn'
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),
							"param_name" => "animation_delay",
							"value" => '50',
							"edit_field_class" => "vc_col-xs-6",
							"description" => ""
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('MUST : Select Animation Type from above options either It will show blank. Waypoint Based animations are scroll based, and Stagger based are one by one column animation.','pt_theplus').'</span></span>'.esc_html__('Column Load Animation', 'pt_theplus')), 
							"param_name" => "animated_column_list",
							"value" => array(
								esc_html__("Select Options", "pt_theplus") => "",
								esc_html__("Waypoint Based Animation", "pt_theplus") => "columns",
								esc_html__("Stagger Based Animation", "pt_theplus") => "stagger",
							),
							"edit_field_class" => "vc_col-xs-6",
							'description' => '',
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							),
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add Value of Stagger delay in milisecond. 1 sec = 1000 Milisecond.','pt_theplus').'</span></span>'.esc_html__('Animation Stagger', 'pt_theplus')),
							"param_name" => "animation_stagger",
							"value" => '150',
							"edit_field_class" => "vc_col-xs-6",
							"description" => "",
							"dependency" => array(
								"element" => "animated_column_list",
								"value" => array(
									"stagger",
								)
							),
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Extra Settings', 'pt_theplus'),
							'param_name' => 'extra_effect',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add space between your columns by turning on this option.','pt_theplus').'</span></span>'.esc_html__('Column Space Option', 'pt_theplus')), 
							'param_name' => 'column_space',
							'description' => '',
							'value' => 'off',
							'options' => array(
							'on' => array(
							'label' => '',
							'on' => 'Yes',
							'off' => 'No',
							),
							),
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Value of Column Space here in Pixels. e.g. 10px, 20px etc.','pt_theplus').'</span></span>'.esc_html__('Column Space', 'pt_theplus')), 
							'param_name' => 'column_space_pading',
							'description' => '',
							'value' => '10px',
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
							"element" => "column_space",
							"value" => array("on"),
							),			
						),	
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__(' You can add Extra Class here to use for Customization Purpose.', 'pt_theplus') . '</span></span>' . esc_html__('Extra Class', 'pt_theplus')),
							"param_name" => "el_class",
							'edit_field_class' => 'vc_col-sm-6'
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Columns Setting', 'pt_theplus'),
							'param_name' => 'columns_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Content', 'pt_theplus'),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
								)
							)
						),
						array(
							"type" => "dropdown",
							"heading" => esc_html__("Desktop Columns", 'pt_theplus'),
							"param_name" => "desktop_column",
							"admin_label" => false,
							"value" => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'5 column' => '15',
								'6 column' => '2',
								'7 column' => '17',
								'8 column' => '18',
								'9 column' => '19',
								'10 column' => '20',
								'11 column' => '21',
								'12 column' => '1'
							),
							'std' => '3',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
								)
							),
							"group" => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => esc_html__("Tablet Columns", 'pt_theplus'),
							"param_name" => "tablet_column",
							"admin_label" => false,
							"value" => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',								
								'4 column' => '3',
								'5 column' => '15',
								'6 column' => '2',
								'7 column' => '17',
								'8 column' => '18',
								'9 column' => '19',
								'10 column' => '20',
								'11 column' => '21',
								'10 column' => '20',
								'12 column' => '1'
							),
							'std' => '6',
							"edit_field_class" => "vc_col-xs-4",
							
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
								)
							),
							"group" => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => esc_html__("Mobile Columns", 'pt_theplus'),
							"param_name" => "mobile_column",
							"admin_label" => false,
							"value" => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'5 column' => '15',
								'6 column' => '2',
								'7 column' => '17',
								'8 column' => '18',
								'9 column' => '19',
								'10 column' => '20',
								'11 column' => '21',
								'12 column' => '1'
							),
							'std' => '12',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
								)
							),
							"group" => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => esc_html__("Metro Columns", 'pt_theplus'),
							"param_name" => "metro_column",
							"admin_label" => false,
							"value" => array(
								'3 column' => '4',
								'4 column' => '3',
							),
							'std' => '4',
							"edit_field_class" => "vc_col-xs-12",
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"metro",
								)
							),
							"group" => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('When some one focus on one image rest images have hover opacity option.','pt_theplus').'</span></span>'.esc_html__('Invert On Hover On/OFf', 'pt_theplus')), 
							'param_name' => 'invert_hover_opt',
							'description' => '',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No',
								),
							),
							"edit_field_class" => "vc_col-xs-6",
							"group" => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for overlay using this option.','pt_theplus').'</span></span>'.esc_html__('Overlay Color', 'pt_theplus')), 
							'param_name' => 'overlay_color',
							'value' => '#313131',
							'edit_field_class' => 'vc_col-sm-6',
							'description' => '',
							"group" => esc_attr__('Content', 'pt_theplus'),
							'dependency' => array(
								'element' => 'invert_hover_opt',
								'value' => array(
									'on'
								)
							)
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('When some one focus on one image rest images have hover opacity. Please select It\'s value from here.','pt_theplus').'</span></span>'.esc_html__('Invert On Hover Value', 'pt_theplus')), 
							"param_name" => "invert_hover_val",
							"admin_label" => false,
							"value" => array(
								'0.9' => '0.9',
								'0.8' => '0.8',
								'0.7' => '0.7',
								'0.6' => '0.6',
								'0.5' => '0.5',
								'0.4' => '0.4',
								'0.3' => '0.3',
								'0.2' => '0.2',
								'0.1' => '0.1',
							),
							'std' => '0.4',
							"edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								"element" => "invert_hover_opt",
								"value" => array(
									"on",
								)
							),
							"group" => esc_attr__('Content', 'pt_theplus'),
						),
					)
				));
			}
		}
	}
	new ThePlus_smart_gallery;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_smart_gallery'))
	{
		class WPBakeryShortCode_tp_smart_gallery extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}