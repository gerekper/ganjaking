<?php
// Google Maps Elements
if(!class_exists("ThePlus_adv_gmap")){
	class ThePlus_adv_gmap{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_adv_gmap') );
			add_shortcode( 'tp_adv_gmap',array($this,'tp_adv_gmap_shortcode'));
		}
		function tp_adv_gmap_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				'gmap_address'	=> '',	
				'zoom' 	=> '14',
				'full_height'=>'',
				'map_type'=>'ROADMAP',
				'overlay_toggle'=>'',
				'bg_color'=> 'rgba(0, 0, 0, 0.42)',
				'font_color'=> '#fff',
				'desc_color' => '#fff',
				'title_text'=>'',
				'toggle_btn_color'=>'rgba(0, 0, 0, 0.4)',
				'toggle_ative_color'=>'#81d742',
				'map_height'=>'300px',
				'gmap_option'=>'pan_control,draggable,zoom_control,map_type_control,scale_control',
				'adv_modify_json'=>'false',
				'modify_json'=> 'false',
				'map_style'=> '',
				'modify_coloring'=> 'false',
				'hue'=> '#ccc',
				'saturation'=> '1',
				'lightness'=> '1',
				  'animation_effects'=>'no-animation',
				  'animation_delay'=>'50',
				  
				'el_class'=>'',
			), $atts ) );

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

			$uid = uniqid("AdvGmap");
			$content = wpb_js_remove_wpautop($content, true);
			$json = array();
			$json1 = array();
			$json['places']  = array();
			$json['options'] = array();
			$json['style']   = array();


			$pin_icon= $class ='';
			$style=array();
			if( $adv_modify_json == 'true' ) $modify_coloring = 'false';

			$gmap_address = (array) vc_param_group_parse_atts( $gmap_address);
			foreach($gmap_address as $item) {
				if(!empty($item['longitude'])){
					$longitude = $item['longitude'];
				}else{
					$longitude ='';
				}
				if(!empty($item['latitude'])){
					$latitude = $item['latitude'];
				}else{
						$latitude ='';
				}
				if(!empty($item['address'])){
					$address = $item['address'];
				}else{
						$address ='';
				}
				   
			if(!empty($item['pin_icon'])){
			 $pin_icon=$item['pin_icon'];
			$icon_src = wp_get_attachment_image_src($pin_icon,'full');
					$icon_url = $icon_src[0];
			}else{
			$icon_url='';
			}
					if(!empty($longitude) || !empty($latitude)){
						$json['places'][] = array(
							"address"   => htmlentities($address),
							"latitude"  => $latitude,
							"longitude" => $longitude,		
							"pin_icon" => $icon_url
						);
					}
			}
			$draggable='false';
			$pan_control='false';
			$zoom_control ='false';
			$scale_control ='false';
			$map_type_control ='false';
			$scrollwheel='false';
			$options= explode(',', $gmap_option);
			foreach($options as $key => $val) {
				if($val=='draggable'){
				$draggable='true';
				}
				if($val=='scroll_wheel'){
				$scrollwheel='true';
				}
				if($val=='pan_control'){
				$pan_control ='true';
				}
				if($val=='zoom_control'){
				$zoom_control='true';
				}
				if($val=='scale_control'){
				$scale_control ='true';
				}
				if($val=='map_type_control'){
				$map_type_control='true';
				}	
			}

			$json['options'] = array(
				"zoom"      		=> intval($zoom),
				"scrollwheel"		=> $scrollwheel == 'true' ? true : false,
				"draggable"		=> $draggable == 'true' ? true : false,
				"panControl"		=> $pan_control == 'true' ? true : false,
				"zoomControl"		=> $zoom_control == 'true' ? true : false,
				"scaleControl"		=> $scale_control == 'true' ? true : false,
				"mapTypeControl"	=> $map_type_control == 'true' ? true : false,
					"mapTypeId"		=> $map_type
			);
			$maps_style='';
			if( $modify_coloring != 'false' ) {
				$json['style'][] = array(
					"stylers" => array(
						array(  "hue" => $hue ),
					array(  "saturation" 	 => $saturation ),
						array(  "lightness"   	=> $lightness ),
					array(  "featureType" 	=> "landscape.man_made",
						"stylers" 		=> array(
							array( "visibility" => "on" )
							)
					)
					)
				);
				$maps_style='';
			}elseif( $adv_modify_json != 'false' ) {
			$maps_style=$map_style;
			}



			$json = str_replace("'", "&apos;", json_encode( $json ) );
			$gmap ='<div class="pt-plus-adv-gmap">';
			$gmap .='<div id="pt-plus-adv-gmaps-'.esc_attr($uid).'" class="pt-plus-adv-map '.esc_attr($class).' '.esc_attr($animated_class).' '.esc_attr($el_class).' js-el" data-id="pt-plus-adv-gmaps-'.esc_attr($uid).'" style="height: '.esc_attr($map_height).';" data-component="'.esc_attr($full_height).'"  data-adv-maps="'.htmlentities($json, ENT_QUOTES, "UTF-8").'" data-map-style="'.$maps_style.'"  data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'"></div>';
			if($overlay_toggle=='overlay_show'){
				$gmap .='<div class="pt-plus-overlay-map-content '.esc_attr($uid).'"  style="background: '.esc_attr($bg_color).';"  data-uid="'.esc_attr($uid).'" data-toggle-btn-color="'.esc_attr($toggle_btn_color).'" data-toggle-active-color="'.esc_attr($toggle_ative_color).'" data-desc_color="'.esc_attr($desc_color).'">';
					$gmap .='<div class="gmap-title" style="color:'.esc_attr($font_color).'">'.esc_html($title_text).'</div>';
					$gmap .='<div class="gmap-desc">'.$content.'</div>';
						$gmap .='<div class="overlay-list-item">
								<input id="toggle_overlay_'.esc_attr($uid).'" type="checkbox" class="pt-plus-overlay-gmap pt-plus-overlay-gmap-tgl checked-'.esc_attr($uid).'"/>
							<label for="toggle_overlay_'.esc_attr($uid).'" class="pt-plus-overlay-gmap-btn check-label-'.esc_attr($uid).'"></label>
							</div>';
				$gmap .='</div>';
			}
			$gmap .='</div>';

			return $gmap;
		}
		function init_tp_adv_gmap(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => esc_html__("Advance Google Map", "pt_theplus"),
					"base" => "tp_adv_gmap",
					"icon" => "tp-google-maps",
					"category" => esc_html__("The Plus", "pt_theplus"),
					'description' => esc_html__('Beautiful Maps', 'pt_theplus'),
					"params" => array(
						array(
							'type' => 'param_group',
							'heading' => esc_html__('Add Single Or Multiple Addresses from Here', 'pt_theplus'),
							'param_name' => 'gmap_address',
							'params' => array(
								array(
									"type" => "textfield",
									"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Latitude value of your location of Google map. You can find that using','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://www.latlong.net/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Latitude Value', 'pt_theplus')), 
									"param_name" => "latitude",
									'edit_field_class' => 'vc_col-xs-3',
									"value" => "",
									"description" => ""
								),
								array(
									"type" => "textfield",
									"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Longitude value of your location of Google map. You can find that using','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://www.latlong.net/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Longitude Value', 'pt_theplus')),
									"param_name" => "longitude",
									'edit_field_class' => 'vc_col-xs-3',
									"value" => "",
									"description" => ""
								),
								array(
									"type" => "textarea",
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add text you want to show on Pin Icon as a Tooltip for this Location using this option.','pt_theplus').'</span></span>'.esc_html__('Address text for Tooltip', 'pt_theplus')), 
									"param_name" => "address",
									'edit_field_class' => 'vc_col-xs-6',
									"value" => "",
									"description" => ""
								),
								array(
									"type" => "attach_image",
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Upload your address Pin Icon using this option. If blank We will use default icon.','pt_theplus').'</span></span>'.esc_html__('Upload Pin Icon', 'pt_theplus')), 
									"param_name" => "pin_icon",
									"value" => "",
									"description" => ""
								)
							)
						),
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('MAP OVERLAY', 'pt_theplus'),
						'param_name' => 'map_overlay',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),	
						array(
							'type' => 'checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Put toggle on off button with content over the map using this option.','pt_theplus').'</span></span>'.esc_html__('Content Over the Map', 'pt_theplus')),
							'param_name' => 'overlay_toggle',
							'std' => '',
							'value' => array(
								__('OverLay Toggle ON/Off', 'pt_theplus') => 'overlay_show'
							),
							'group' => esc_html__('OverLay Options', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add title of map using this option.','pt_theplus').'</span></span>'.esc_html__('Title', 'pt_theplus')),
							"param_name" => "title_text",
							"value" => "",
							"description" => "",
							'group' => esc_html__('OverLay Options', 'pt_theplus'),
							"dependency" => array(
								'element' => "overlay_toggle",
								'value' => array(
									'overlay_show'
								)
							)
						),
						array(
							"type" => "textarea_html",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add description of map using this option.','pt_theplus').'</span></span>'.esc_html__('Description', 'pt_theplus')),
							"param_name" => "content",
							"value" => "",
							"description" => "",
							'group' => esc_html__('OverLay Options', 'pt_theplus'),
							"dependency" => array(
								'element' => "overlay_toggle",
								'value' => array(
									'overlay_show'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for overlay background color using this option.','pt_theplus').'</span></span>'.esc_html__('Overlay Background Color', 'pt_theplus')),
							'edit_field_class' => 'vc_col-xs-4',
							'param_name' => 'bg_color',
							'value' => 'rgba(0, 0, 0, 0.42)',
							'group' => esc_html__('OverLay Options', 'pt_theplus'),
							"dependency" => array(
								'element' => "overlay_toggle",
								'value' => array(
									'overlay_show'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for title using this option.','pt_theplus').'</span></span>'.esc_html__('Tilte Color', 'pt_theplus')),
							'edit_field_class' => 'vc_col-xs-4',
							'param_name' => 'font_color',
							'value' => '#fff',
							'group' => esc_html__('OverLay Options', 'pt_theplus'),
							"dependency" => array(
								'element' => "overlay_toggle",
								'value' => array(
									'overlay_show'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for description using this option.','pt_theplus').'</span></span>'.esc_html__('Description Color', 'pt_theplus')),
							'edit_field_class' => 'vc_col-xs-4',
							'param_name' => 'desc_color',
							'value' => '#fff',
							'group' => esc_html__('OverLay Options', 'pt_theplus'),
							"dependency" => array(
								'element' => "overlay_toggle",
								'value' => array(
									'overlay_show'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Toggle button using this option.','pt_theplus').'</span></span>'.esc_html__('Toggle Button Color', 'pt_theplus')),
							'edit_field_class' => 'vc_col-xs-6',
							'param_name' => 'toggle_btn_color',
							'value' => 'rgba(0, 0, 0, 0.4)',
							'group' => esc_html__('OverLay Options', 'pt_theplus'),
							"dependency" => array(
								'element' => "overlay_toggle",
								'value' => array(
									'overlay_show'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Toggle active using this option.','pt_theplus').'</span></span>'.esc_html__('Toggle Active Color', 'pt_theplus')),
							'edit_field_class' => 'vc_col-xs-6',
							'param_name' => 'toggle_ative_color',
							'value' => '#81d742',
							'group' => esc_html__('OverLay Options', 'pt_theplus'),
							"dependency" => array(
								'element' => "overlay_toggle",
								'value' => array(
									'overlay_show'
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter values from 1 to 19 to zoom google map as per requirement.','pt_theplus').'</span></span>'.esc_html__('Map Zoom', 'pt_theplus')), 
							"param_name" => "zoom",
							"value" => "14",
							"description" => ""
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Height of Google Map Section using this option.','pt_theplus').'</span></span>'.esc_html__('Map Height', 'pt_theplus')), 
							"param_name" => "map_height",
							"value" => "300px",
							"description" => ""
						),
						array(
							'type' => 'checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can customise many google map options using this list.','pt_theplus').'</span></span>'.esc_html__('Map Options', 'pt_theplus')),
							'param_name' => 'gmap_option',
							'std' => 'pan_control,draggable,zoom_control,map_type_control,scale_control,scroll_wheel',
							'value' => array(
								__('Scroll Wheel', 'pt_theplus') => 'scroll_wheel',
								__('Pan Control', 'pt_theplus') => 'pan_control',
								__('Draggable', 'pt_theplus') => 'draggable',
								__('Zoom Control', 'pt_theplus') => 'zoom_control',
								__('Map Type Control', 'pt_theplus') => 'map_type_control',
								__('Scale Control', 'pt_theplus') => 'scale_control'
							)
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Google map variations using this options.','pt_theplus').'</span></span>'.esc_html__('Google Map Variations', 'pt_theplus')),
							"param_name" => "map_type",
							"value" => array(
								__("ROADMAP (Displays a normal, default 2D map)", "pt_theplus") => "ROADMAP",
								__("HYBRID (Displays a photographic map + roads and city names)", "pt_theplus") => "HYBRID",
								__("SATELLITE (Displays a photographic map)", "pt_theplus") => "SATELLITE",
								__("TERRAIN (Displays a map with mountains, rivers, etc.)", "pt_theplus") => "TERRAIN"
							),
							"description" => ""
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose our creative google map styles using this option.','pt_theplus').'</span></span>'.esc_html__('Custom Styled Maps', 'pt_theplus')),
							"param_name" => "adv_modify_json",
							"value" => array(
								__("No", "pt_theplus") => "false",
								__("Yes", "pt_theplus") => "true"
							),
							"description" => ""
						),        
						array(
								'type'        => 'radio_select_image',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose one from these creative styles, which are with different color combinations and styles.','pt_theplus').'</span></span>'.esc_html__('Creative Map Style', 'pt_theplus')),
								"param_name" => "map_style",
								'simple_mode' => false,
								'value'		=> '',
								'options'     => array(
									'' => array(
										'tooltip' => esc_attr__('Default','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/google-map/style-default.jpg'
									),
									'style-1' => array(
										'tooltip' => esc_attr__('Style 1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/google-map/style-1.jpg'
									),
									'style-2' => array(
										'tooltip' => esc_attr__('Style 2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/google-map/style-2.jpg'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Style 3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/google-map/style-3.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style 4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/google-map/style-4.jpg'
									),
									'style-5' => array(
										'tooltip' => esc_attr__('Style 5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/google-map/style-5.jpg'
									),
									'style-6' => array(
										'tooltip' => esc_attr__('Style 6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/google-map/style-6.jpg'
									),
									'style-7' => array(
										'tooltip' => esc_attr__('Style 7','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/google-map/style-7.jpg'
									),
								),
								"dependency" => array(
									'element' => "adv_modify_json",
									'value' => array(
										'true'
									)
								),
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose one from these Modify Google Maps Hue, Saturationstyles.','pt_theplus').'</span></span>'.esc_html__('Modify Google Maps Hue, Saturation, Lightness', 'pt_theplus')),
							"param_name" => "modify_coloring",
							"value" => array(
								__("No", "pt_theplus") => "false",
								__("Yes", "pt_theplus") => "true"
							),
							"description" => "",
							"dependency" => array(
								'element' => "adv_modify_json",
								'value' => array(
									'false'
								)
							)
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Sets the hue of the feature to match the hue of the color supplied. Note that the saturation and lightness of the feature is conserved, which means, the feature will not perfectly match the color supplied .','pt_theplus').'</span></span>'.esc_html__('Hue', 'pt_theplus')),
							"heading" => __("Hue", "pt_theplus"),
							"param_name" => "hue",
							"value" => "#ccc",
							"description" => "",
							"dependency" => array(
								'element' => "modify_coloring",
								'value' => array(
									'true'
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Shifts the saturation of colors by a percentage of the original value if decreasing and a percentage of the remaining value if increasing. Valid values: [-100, 100].','pt_theplus').'</span></span>'.esc_html__('Saturation', 'pt_theplus')),
							"param_name" => "saturation",
							"value" => "1",
							"description" => "",
							"dependency" => array(
								'element' => "modify_coloring",
								'value' => array(
									'true'
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Shifts lightness of colors by a percentage of the original value if decreasing and a percentage of the remaining value if increasing. Valid values: [-100, 100].','pt_theplus').'</span></span>'.esc_html__('Lightness', 'pt_theplus')),
							"param_name" => "lightness",
							"value" => "1",
							"description" => "",
							"dependency" => array(
								'element' => "modify_coloring",
								'value' => array(
									'true'
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
							"type" => "dropdown",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
							"param_name" => "animation_effects",
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => false,
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
							"value" => "",
							"description" => ""
						)
						
					)
				));
			}
		}
	}
	new ThePlus_adv_gmap;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_adv_gmap'))
	{
		class WPBakeryShortCode_tp_adv_gmap extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}



