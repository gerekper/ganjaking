<?php
// Image Factory/Animated Image Elements
if(!class_exists("ThePlus_animated_image")){
	class ThePlus_animated_image{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_animated_image') );
			add_shortcode( 'tp_animated_image',array($this,'tp_animated_image_shortcode'));
		}
		function tp_animated_image_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				  'animated_style'=>'creative-simple-image',
				  'image_source' => 'media_library',
				  'image'=>'',
				  'external_img' => '',
				  'img_size'=>'full',
				  'alignment'=>'left',
				  'style'=>'',
				  'border_color'=>'#d3d3d3',
				  'onclick'=>'',
				  'link'=>'',
				  'img_link_target'=>'',
				  'image_hover'=>'',
				  'on_hover_style'=>'',
				  'animation_effects'=>'no-animation',
				'animation_delay'=>'50',
				'bg_image_parallax'=>'',
				'magic_scroll' => 'off',
				'scroll_type'	=> 'position',
				'distance_scroll_x' => '0',
				'distance_scroll_y' => '50',
				'scale_scroll'=> '1',
				
				'slide_show'=>'off',				
				"slide_change_opt"=>'onclick',
				'interval_time'=>'4000',
				'mouse_move_parallax'=>'',
				'move_speed_x'=>'30',
				'move_speed_y'=>'30',
				
				'special_effect' => 'off',
				'effect_color_1' => '#313131',
				'effect_color_2' => '#ff214f',
				
				'hover_parallax'=>'',
				'parallax_axis'=>'null',
				'min_height'=>'400px',
				'tablet_min_height'=>'',
				'mobile_min_height'=>'',
				'image_cascading'=>'',
				'bg_color'=>'#d3d3d3',
				'animated_direction' => 'left',
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
			
			if($animation_effects=='no-animation'){
			$animated_class=$animation_effects=$animation_delay=$animation_delay_time='';
			}else{
				$animated_class='animate-general';
				$animation_effects=$animation_effects;
				$animation_delay_time=$animation_delay;
			}
		 $css='';
		 $default_src = vc_asset_url( 'vc/no_image.png' );
		 
			if ( empty( $onclick ) && isset( $img_link_large ) && 'yes' === $img_link_large ) {
				$onclick = 'img_link_large';
			} elseif ( empty( $atts['onclick'] ) && ( ! isset( $atts['img_link_large'] ) || 'yes' !== $atts['img_link_large'] ) ) {
				$onclick = 'custom_link';
			}
			
			$img_id = preg_replace( '/[^\d]/', '', $image );
			
			if ( preg_match( '/_circle_2$/', $style ) ) {
					$style = preg_replace( '/_circle_2$/', '_circle', $style );
					$img_size = pt_plus_getImageSquareSize( $img_id, $img_size );
				}

				if ( ! $img_size ) {
					$img_size = 'medium';
				}

			$content_image = '';
			if ($image_source == 'media_library') {
			
				if ( $img_id != '' ) {
						$full_image=wp_get_attachment_image_src( $img_id, $img_size );
						$content_image .='<img src="'.esc_url($full_image[0]).'" class="vc_single_image-img hover__img info_img " alt="">';
				}else{ 
							$content_image .= pt_plus_loading_image_grid(get_the_ID());
				}
			}else if ($image_source == 'externals_link') {
				$content_image .='<img class="vc_single_image-img hover__img info_img " src="'.esc_url($external_img).'" alt="">';
			}
			$el_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $el_class, ' ' ), "tp_animated_image", $atts );
			
			if ( vc_has_class( 'prettyphoto', $el_class ) ) {
				$onclick = 'link_image';
			}
			if ( ! empty( $atts['img_link'] ) ) {
				$link = $atts['img_link'];
				if ( ! preg_match( '/^(https?\:\/\/|\/\/)/', $link ) ) {
					$link = 'http://' . $link;
				}
			}
			
			
			switch ( $onclick ) {
				case 'img_link_large':
				if ( $image_source == 'externals_link') {
					$link = $external_img; 
					}else{
					$link = wp_get_attachment_image_src( $img_id, 'large',array('class' => 'hover__img') );
					$link = $link[0];
					}
				break;

			case 'link_image':
				wp_enqueue_script( 'prettyphoto' );
				wp_enqueue_style( 'prettyphoto' );

				$a_attrs['class'] = 'prettyphoto';
				$a_attrs['data-rel'] = 'prettyPhoto[rel-' . esc_attr(get_the_ID()) . '-' . rand() . ']';

				// backward compatibility
				if ( vc_has_class( 'prettyphoto', $el_class ) ) {
					// $link is already defined
					
				} else {
					if ( $image_source == 'externals_link') {
					$link = $external_img; 
					} else{
					$link = wp_get_attachment_image_src( $img_id, 'large',array('class' => 'hover__img') );
					$link = $link[0];
					}
				}
				break;

			case 'custom_link':
				// $link is already defined
				break;
				
			case 'zoom':
				wp_enqueue_script( 'vc_image_zoom' );
				if ( $image_source == 'externals_link') {
					$large_img_src = $external_img; 
				} else {
					$large_img_src = wp_get_attachment_image_src( $img_id, 'large' );
					if ( $large_img_src ) {
						$large_img_src = $large_img_src[0];
					}
				}
				
				$content_image = str_replace( '<img ', '<img data-vc-zoom="' . esc_url($large_img_src) . '" ', $content_image );

				break;
			}
			
			if(!empty($hover_parallax) && $hover_parallax=='yes'){
			
				$hover_tilt='hover-tilt';
			}else{
			
				$hover_tilt='';
			}
			
			// backward compatibility
			if ( vc_has_class( 'prettyphoto', $el_class ) ) {
				$el_class = vc_remove_class( 'prettyphoto', $el_class );
			}
			$hover_class='';
			if(!empty($on_hover_style)){
				$hover_class=' hover_'.esc_attr($image_hover);
			}else{
				$hover_class=$image_hover;
			}
			
			
			if($animated_style!='animate-image'){
				$wrapperClass = 'vc_single_image-wrapper ' . esc_attr($style) . ' '.esc_attr($hover_class);
			}else{
				$wrapperClass = 'vc_single_image-wrapper '.esc_attr($hover_class);
			}
			$border_css='';
			if($style=='vc_box_border'){
				$border_css="background-color: ".esc_attr($border_color).';';
			}else if($style=='vc_box_outline'){
				$border_css="border-color: ".esc_attr($border_color).';';
			}else if($style=='vc_box_outline'){
				$border_css="border-color: ".esc_attr($border_color).';';
			}
			
			$data_image='';
			if ($image_source == 'media_library') {
				if ( $img_id != '' ) {
							$full_image=wp_get_attachment_image_src( $img_id, $img_size );
							
							$data_image='background:url('.esc_url($full_image[0]).') #f7f7f7;';
								
				}else{ 
							$data_image = pt_plus_loading_image_grid('','background');
				}
			} else if ($image_source == 'externals_link') {

					$data_image .='background:url('.esc_url($external_img).') #f7f7f7;';
			}
			
			$reveal_effects=$effect_attr='';
			if(!empty($special_effect) && $special_effect=='on'){
				$effect_rand_no =uniqid('reveal');
				$effect_attr .=' data-reveal-id="'.esc_attr($effect_rand_no).'" ';
				$effect_attr .=' data-effect-color-1="'.esc_attr($effect_color_1).'" ';
				$effect_attr .=' data-effect-color-2="'.esc_attr($effect_color_2).'" ';
				$reveal_effects=' pt-plus-reveal '.esc_attr($effect_rand_no).' ';
			}
			
			if ( $link ) {
				$a_attrs['href'] = $link;
				$a_attrs['target'] = $img_link_target;
				if ( ! empty( $a_attrs['class'] ) ) {
					$wrapperClass .= ' ' . $a_attrs['class'];
					unset( $a_attrs['class'] );
				}
				if($animated_style=='animate-image'){			
					$html = '<a ' . vc_stringify_attributes( $a_attrs ) . ' class="' . $wrapperClass . ' pt-plus-bg-image-animated '.esc_attr($animated_direction).'"  style="'.$data_image.''.$border_css.'" >' .$content_image. '</a>';
				}else{
					$html = '<a ' . vc_stringify_attributes( $a_attrs ) . ' class="' . esc_attr($wrapperClass) . ' '.esc_attr($reveal_effects).' " '.$effect_attr.' style="'.$border_css.'">' .$content_image. '</a>';
				}
			} else {
				if($animated_style=='animate-image'){			
					$html = '<div class="' . $wrapperClass . ' pt-plus-bg-image-animated '.esc_attr($animated_direction).'" style="'.$data_image.''.$border_css.'" >' .$content_image. '</div>';
				}else{
					$html = '<div class="' . esc_attr($wrapperClass) . ' '.esc_attr($reveal_effects).'" '.$effect_attr.' style="'.$border_css.'">' .$content_image. '</div>';
				}
			}
			
			
			/*--------------cascading image ----------------------------*/
			$cascading_loop=$css_loop='';
			if(isset($image_cascading) && !empty($image_cascading) && function_exists('vc_param_group_parse_atts')) {
		$position='';
		$effects='';
		$animate_speed='';
		$cascading_move_parallax=$move_parallax_attr=$parallax_move='';
			$image_cascading= (array) vc_param_group_parse_atts( $image_cascading);		
			foreach($image_cascading as $item) {
				
if(empty($item['pos_xposition'])){
$xpos='auto';
}else if(!empty($item['pos_xposition']) && $item['pos_xposition']!='auto'){
$xpos=$item['pos_xposition'].'%';
}else{
$xpos='auto';
}
				$ypos=$item['pos_yposition'];
if(empty($item['pos_rightposition'])){
$rpos='auto';
}else if(!empty($item['pos_rightposition']) && $item['pos_rightposition']!='auto'){
$rpos=$item['pos_rightposition'].'%';
}else{
$rpos='auto';
}
				$width=$item['pos_width'];
				$animation_effects=$item['animation_effects'];
				if(!empty($item['animation_delay'])){
				$animation_delay=$item['animation_delay'];
				}else{
				$animation_delay='';
				}
				
				if($animation_effects=='no-animation'){
					$animated_class='';
					$animation_effects='';
					$animation_delay_time='';
				}else{
					$animated_class='animate-general';
					$animation_effects=$animation_effects;
					$animation_delay_time=$animation_delay;
				}
				$image_effect='';
				$image_style='';
				if(!empty($item['image_effect'])){
					$image_effect=$item['image_effect'];
				}
				if(!empty($item['image_style'])){
					$image_style=$item['image_style'];
				}
				$magic_class = $magic_attr = $parallax_scroll = '';
				if (!empty($item['magic_scroll']) && !empty($item["scroll_type"]) && $item['magic_scroll'] == 'on') {
					$magic_attr .= ' data-scroll_type="' . esc_attr($item["scroll_type"]) . '" ';
					$distance_scroll_y=$distance_scroll_x=$scale_scroll='';
					if(!empty($item["distance_scroll_y"])){
						$distance_scroll_y=$item["distance_scroll_y"];
					}
					if(!empty($item["distance_scroll_x"])){
						$distance_scroll_x=$item["distance_scroll_x"];
					}
					if(!empty($item["scale_scroll"])){
						$scale_scroll=$item["scale_scroll"];
					}
					if(!empty($item["scroll_type"]) && $item["scroll_type"]== 'position' ){
						$magic_attr .= ' data-scroll_x="' . esc_attr($distance_scroll_x) . '" ';
						$magic_attr .= ' data-scroll_y="' . esc_attr($distance_scroll_y) . '" ';
						$parallax_scroll .= ' parallax-scroll ';
					}
					if(!empty($item["scroll_type"]) && $item["scroll_type"]== 'scale'){
						$magic_attr .= ' data-scale_scroll="' . esc_attr($scale_scroll) . '" ';
						$parallax_scroll .= ' scale-scroll ';
					}
					if(!empty($item["scroll_type"]) && $item["scroll_type"]== 'both'){
						$magic_attr .= ' data-scroll_x="' . esc_attr($distance_scroll_x) . '" ';
						$magic_attr .= ' data-scroll_y="' . esc_attr($distance_scroll_y) . '" ';
						$magic_attr .= ' data-scale_scroll="' . esc_attr($scale_scroll) . '" ';
						$parallax_scroll .= ' both-scroll ';
					}
					$magic_class .= ' magic-scroll ';
				}
				
				$rand_no=rand(1000000, 1500000);
				
				if(!empty($hover_parallax) && $hover_parallax=='yes'){
					$css_loop .='.parallax-hover-'.esc_js($rand_no).'{-webkit-transform:translateZ('.esc_js($item['parallax_translatez']).') !important;-ms-transform:translateZ('.esc_js($item['parallax_translatez']).') !important;-moz-transform:translateZ('.esc_js($item['parallax_translatez']).') !important;-o-transform:translateZ('.esc_js($item['parallax_translatez']).') !important; transform: translateZ('.esc_js($item['parallax_translatez']).') !important;}';		
				}
				
				$cascading_move_parallax=$move_parallax_attr=$parallax_move='';
				if(!empty($item['cascading_move_parallax']) && $item['cascading_move_parallax']=='on' ){
					$cascading_move_parallax='pt-plus-move-parallax';
					$parallax_move='parallax-move';
					if(!empty($item['cascading_move_speed_x'])){
						$move_parallax_attr .= ' data-move_speed_x="' . esc_attr($item['cascading_move_speed_x']) . '" ';
					}else{
						$move_parallax_attr .= ' data-move_speed_x="0" ';
					}
					if(!empty($item['cascading_move_speed_y'])){
						$move_parallax_attr .= ' data-move_speed_y="' . esc_attr($item['cascading_move_speed_y']) . '" ';
					}else{
						$move_parallax_attr .= ' data-move_speed_y="0" ';
					}
				}
				$reveal_effects=$effect_attr='';
					if(!empty($item['special_effect']) && $item['special_effect']=='on'){
						$effect_rand_no =uniqid('reveal');
						$effect_attr .=' data-reveal-id="'.esc_attr($effect_rand_no).'" ';
						if(!empty($item['effect_color_1'])){
							$effect_attr .=' data-effect-color-1="'.esc_attr($item['effect_color_1']).'" ';
						}else{
							$effect_attr .=' data-effect-color-1="#313131" ';
						}
						if(!empty($item['effect_color_2'])){
							$effect_attr .=' data-effect-color-2="'.esc_attr($item['effect_color_2']).'" ';
						}else{
							$effect_attr .=' data-effect-color-2="#ff214f" ';
						}
						$reveal_effects=' pt-plus-reveal '.esc_attr($effect_rand_no).' ';
					}
				if($item['select_option']=='image'){
					
					if(empty($item['mulitple_image_source']) || $item['mulitple_image_source']=='media_library'){
						if(!empty($item['multiple_image'])){
							$multiple_image=$item['multiple_image'];
							$img = wp_get_attachment_image_src($multiple_image,$item['image_size']);
							$imgSrc = $img[0];
							$content_image ='<img class="parallax_image " src="'.esc_url($imgSrc).'" alt="pt-plus-row-image-1">';						
							
							$cascading_loop .= '<div class="cascading-image ' . esc_attr($magic_class) . ' '.esc_attr($parallax_move).'" style="max-width:'.esc_attr( $width ).';top:'.esc_attr($ypos).'%;left:'.esc_attr($xpos).';right:'.esc_attr($rpos).';" '.$move_parallax_attr.'>';
								$cascading_loop .= '<div class="' . esc_attr($parallax_scroll) . '" ' . $magic_attr . '>';
									$cascading_loop .= '<div class="cascading-inner-content parallax-hover-'.esc_attr($rand_no).' vc_single_image-wrapper '.$image_style.' '.$image_effect.' '.esc_attr($animated_class).' '.esc_attr($reveal_effects).'" '.$effect_attr.' data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">';
											$cascading_loop .=$content_image;										
									$cascading_loop .='</div>';
								$cascading_loop .='</div>';
							$cascading_loop .='</div>';
						}
					}else if(!empty($item['mulitple_image_source']) && $item['mulitple_image_source']=='externals_link'){
						$mulitple_external_img = '';
						if(!empty($item['mulitple_external_img'])){
							$mulitple_external_img=$item["mulitple_external_img"];
							$content_image ='<img class="parallax_image" src="'.esc_url($mulitple_external_img).'" alt="pt-plus-row-image-1">';
							
							$cascading_loop .= '<div class="cascading-image ' . esc_attr($magic_class) . ' '.esc_attr($parallax_move).'" style="max-width:'.esc_attr( $width ).';top:'.esc_attr($ypos).'%;left:'.esc_attr($xpos).';right:'.esc_attr($rpos).';" '.$move_parallax_attr.'>';
								$cascading_loop .= '<div class="' . esc_attr($parallax_scroll) . '" ' . $magic_attr . '>';
									$cascading_loop .= '<div class="cascading-inner-content parallax-hover-'.esc_attr($rand_no).' vc_single_image-wrapper '.$image_style.' '.$image_effect.' '.esc_attr($animated_class).' '.esc_attr($reveal_effects).'" '.$effect_attr.' data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'">';
											$cascading_loop .=$content_image;										
									$cascading_loop .='</div>';
								$cascading_loop .='</div>';
							$cascading_loop .='</div>';
						}
					}
					if(!empty($item['img_shadow'])){
						$css_loop.='.wpb_single_image .cascading-inner-content.parallax-hover-'.esc_attr($rand_no).' img,.cascading-inner-content.parallax-hover-'.esc_attr($rand_no).' img{-webkit-box-shadow: '.esc_attr($item["img_shadow"]).';-moz-box-shadow: '.esc_attr($item["img_shadow"]).';box-shadow: '.esc_attr($item["img_shadow"]).';}';
					}
					if(!empty($item['img_hover_shadow'])){
						$css_loop.='.wpb_single_image .cascading-inner-content.parallax-hover-'.esc_attr($rand_no).':hover img,.cascading-inner-content.parallax-hover-'.esc_attr($rand_no).':hover img{-webkit-box-shadow: '.esc_attr($item["img_hover_shadow"]).';-moz-box-shadow: '.esc_attr($item["img_hover_shadow"]).';box-shadow: '.esc_attr($item["img_hover_shadow"]).';}';
					}
					if(!empty($item['img_shadow']) || !empty($item['img_hover_shadow'])){
						$css_loop.='.wpb_single_image .cascading-inner-content.parallax-hover-'.esc_attr($rand_no).' img,.cascading-inner-content.parallax-hover-'.esc_attr($rand_no).' img{-webkit-transition: .3s ease-in-out;-moz-transition: .3s ease-in-out;-o-transition: background-color 2s ease-out;transition: .3s ease-in-out;}';
					}	
				}else{
					$font_weight=$text_title=$font_size=$line_height=$text_color='';
					$title_use_theme_fonts='custom-font-family';
						$title_font_family='';
						if(!empty($item['title_use_theme_fonts'])){
							$title_use_theme_fonts= $item['title_use_theme_fonts'];
						if(!empty($item['title_google_fonts']) && $title_use_theme_fonts=='google-fonts'){
							$text_font_data = pt_plus_getFontsData( $item['title_google_fonts'] );
							$title_font_family = pt_plus_googleFontsStyles( $text_font_data );  
							$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
						}elseif($title_use_theme_fonts=='custom-font-family'){
						if(!empty($item['title_font_family'])){
							$title_font_family .='font-family:'.$item['title_font_family'].';';
						}
						if(!empty($item['title_font_weight'])){
							$title_font_family .='font-weight:'.$item['title_font_weight'].';';
						}
						}else{
							$title_font_family ='';
						}
					}
								
					if(!empty($item['font_size'])){
						$font_size=$item['font_size'];
					}
					if(!empty($item['line_height'])){
						$line_height=$item['line_height'];
					}
					if(!empty($item['text_color'])){
						$text_color=$item['text_color'];
					}
					if(!empty($item['text_title'])){
						$text_title=$item['text_title'];
					}
					$cascading_loop .= '<div class="cascading-text ' . esc_attr($magic_class) . '" style="max-width:'.esc_attr( $width ).';top:'.esc_attr($ypos).'%;left:'.esc_attr($xpos).';right:'.esc_attr($rpos).';">';
						$cascading_loop .= '<div class="' . esc_attr($parallax_scroll) . '" ' . $magic_attr . '>';
							$cascading_loop .= '<div class="cascading-inner-content  '.$image_effect.' '.esc_attr($animated_class).' '.esc_attr($reveal_effects).'" '.$effect_attr.' data-animate-type="'.esc_attr($animation_effects).' " data-animate-delay="'.esc_attr($animation_delay_time).'" >';
								$cascading_loop .='<div class="parallax_image" style="font-size:'.esc_attr($font_size).';line-height:'.esc_attr($line_height).';color:'.esc_attr($text_color).';'.esc_attr($title_font_family).'">'.esc_html($text_title).'</div>';
							$cascading_loop .='</div>';
						$cascading_loop .='</div>';
					$cascading_loop .='</div>';
				}
			}
		}
			/*--------------cascading image ----------------------------*/
			
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
			
			
			
			$uid=uniqid('bg-image');
			$css_rule=$css_data='';
			if($animated_style=='animate-image'){
				$bg_animated=' background-image-animated ';
				$bg_anim=' bg-img-animated ';
				$animated_class='animate-general';
				$css_data ='.'.esc_js($uid).' .pt-plus-bg-image-animated:after{background:'.esc_js($bg_color).';}';
			}else{
				$bg_animated=$bg_anim='';
			}
			$css_class='';
				$class_to_filter = 'wpb_single_image wpb_content_element vc_align_' . $alignment . ' '.esc_attr($animated_class);
				$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $el_class;
				$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, 'tp_animated_image', $atts );
				
			$parallax_image_scroll='';
			if(!empty($bg_image_parallax) && $bg_image_parallax=='on' && $animated_style=='creative-simple-image'){
				$parallax_image_scroll='section-parallax-img';
				$html .='<figure class="creative-simple-img-parallax"><figure class="pt-plus-parallax-img-parent"><div class="parallax-img-container">';
					$image=wp_get_attachment_image_src( $img_id, $img_size );
				$html .='<img class="simple-parallax-img" src="'.esc_url($image[0]).'"  title="">';
				$html .='</div></figure></figure>';
			}
			$move_parallax=$move_parallax_attr=$parallax_move='';
			if(!empty($mouse_move_parallax) && $mouse_move_parallax=='on' && ($animated_style=='creative-simple-image' || $animated_style=='animate-image')){
				$move_parallax='pt-plus-move-parallax';
				$parallax_move='parallax-move';
				$move_parallax_attr .= ' data-move_speed_x="' . esc_attr($move_speed_x) . '" ';
				$move_parallax_attr .= ' data-move_speed_y="' . esc_attr($move_speed_y) . '" ';
			}
			
			$uid_cascading=uniqid("cascading_");
			if($animated_style=='creative-simple-image' || $animated_style=='animate-image'){
				$output = '<div class="pt-plus-animated-image-wrapper   ' . esc_attr($magic_class) . ' '.esc_attr($desktop_hide).' '.esc_attr($tablet_hide).' '.esc_attr($mobile_hide).'">';
					$output .= '<div class="animated-image-parallax  '.esc_attr($move_parallax).' ' . esc_attr($parallax_scroll) . '" ' . $magic_attr . '>';
						$output .= '<div class="pt_plus_animated_image '.esc_attr($uid).' ' .  trim( $css_class ) . ' '.esc_attr($bg_anim).' " data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay_time).'" >
							<figure class="'.esc_attr($parallax_image_scroll).' wpb_wrapper vc_figure '.esc_attr($bg_animated).' '.esc_attr($parallax_move).'  '.esc_attr($hover_tilt).' " '.$move_parallax_attr.'>
								' . $html . '								
							</figure>
						</div>';
					$output .= '</div>';
				$output .= '</div>';
			}else{	
					 $uid=uniqid("slide"); $attr='';
					if(!empty($slide_show) && $slide_show=='on'){
						$wrapperClass .=' slide_show_image '.esc_attr($uid);
						$attr .=' data-play="'.esc_attr($slide_change_opt).'"';
						$attr .=' data-uid="'.esc_attr($uid).'"';
						$attr .=' data-interval_time="'.esc_attr($interval_time).'"';						
					}
					$output = '<div class="pt_plus_animated_image cascading-block  wpb_single_image '.esc_attr($uid_cascading).' ' . $wrapperClass . ' '.esc_attr($cascading_move_parallax).' '.esc_attr($hover_tilt).' '.esc_attr($desktop_hide).' '.esc_attr($tablet_hide).' '.esc_attr($mobile_hide).'" '.$attr.'>';
					$output .= '<div class="cascading-inner-loop ">';
						$output .=$cascading_loop;
						$output .='</div>';
					$output .='</div>';
			}
			$css_rule='<style >';
			$css_rule .=$css_data;
			$css_rule .=$css_loop;
			$css_rule .='.pt_plus_animated_image.cascading-block.'.esc_js($uid_cascading).'{min-height:'.esc_attr($min_height).'}';
if(!empty($tablet_min_height)){
$css_rule .='@media (max-width:800px){.pt_plus_animated_image.cascading-block.'.esc_js($uid_cascading).'{min-height:'.esc_attr($tablet_min_height).'}}';
}
if(!empty($mobile_min_height)){
$css_rule .='@media (max-width:600px){.pt_plus_animated_image.cascading-block.'.esc_js($uid_cascading).'{min-height:'.esc_attr($mobile_min_height).'}}';
}
			$css_rule .='</style>';
			return $css_rule.$output;
		}
		function init_tp_animated_image(){
			if(function_exists("vc_map"))
			{
				
				vc_map(array(
					"name" => esc_html__("Image Factory", 'pt_theplus'),
					"base" => "tp_animated_image",
					"icon" => "tp-animated-image",
					"category" => esc_html__("The Plus", "pt_theplus"),
					"description" => esc_html__('Add Single Image with Creativity', 'pt_theplus'),
					"params" => array(
					array(
								'type'        => 'radio_select_image',
								"heading" => esc_html__("Animated Image Style", "pt_theplus"),
								'param_name'  => 'animated_style',
								'simple_mode' => false,
								'admin_label' => true,
								'value'		=> 'creative-simple-image',
								'options'     => array(
									'creative-simple-image' => array(
										'tooltip' => esc_attr__('Creative Image','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/animated-image/ts-creative-images.jpg'
									),
									'cascading-image' => array(
										'tooltip' => esc_attr__('Cascading Image','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/animated-image/ts-cascading-image.jpg'
									),
									'animate-image' => array(
										'tooltip' => esc_attr__('Animate Image','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/animated-image/ts-animated-image.jpg'
									),
								),
							),
						/*-------------------cascading image multiple group-------------------*/
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You need to mention minimum height of section due to technical reasons. If your section height is more than this, It will work pretty well, So Try to add lowest possible height here. Ex. 500px,400px...','pt_theplus').'</span></span>'.esc_html__('Desktop Section Minimum Height', 'pt_theplus')), 
							"description" => "",
							'param_name' => 'min_height',
							'admin_label' => false,
							'value' => '400px',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'cascading-image'
								)
							)
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You need to mention minimum height of section due to technical reasons. If your section height is more than this, It will work pretty well, So Try to add lowest possible height here. Ex. 500px,400px...','pt_theplus').'</span></span>'.esc_html__('Tablet Responsive Section Minimum Height', 'pt_theplus')), 
							"description" => "",
							'param_name' => 'tablet_min_height',
							'admin_label' => false,
							'value' => '',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'cascading-image'
								)
							)
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You need to mention minimum height of section due to technical reasons. If your section height is more than this, It will work pretty well, So Try to add lowest possible height here. Ex. 500px,400px...','pt_theplus').'</span></span>'.esc_html__('Mobile Responsive Section Minimum Height', 'pt_theplus')), 
							"description" => "",
							'param_name' => 'mobile_min_height',
							'admin_label' => false,
							'value' => '',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'cascading-image'
								)
							)
						),
						array(
							'type' => 'param_group',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add multiple Cascading Sections using this option.','pt_theplus').'</span></span>'.esc_html__('Add Cascading Sections with Positions', 'pt_theplus')), 
							'heading' => esc_html__('No of Pieces On Position', 'pt_theplus'),
							'param_name' => 'image_cascading',
							'params' => array(
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of box position from left. You can enter value in percentage or pixels . e.g. auto,10,20,50 etc.','pt_theplus').'</span></span>'.esc_html__('Left', 'pt_theplus')), 
									"description" => "",
									'param_name' => 'pos_xposition',
									'admin_label' => true,
									'value' => '45'
									
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of box position from top. You can enter value in percentage or pixels . e.g. 10,20,50 etc.','pt_theplus').'</span></span>'.esc_html__('Top', 'pt_theplus')), 
									"description" => "",
									'param_name' => 'pos_yposition',
									'admin_label' => true,
									'value' => '20'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of box position from right. You can enter value in percentage or pixels . e.g. auto,10,20,50 etc.','pt_theplus').'</span></span>'.esc_html__('Right', 'pt_theplus')), 
									"description" => "",
									'param_name' => 'pos_rightposition',
									'admin_label' => true,
									'value' => 'auto'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Please Enter width of this in pixels. e.g. 50px ,70px etc.','pt_theplus').'</span></span>'.esc_html__('Width', 'pt_theplus')),
									'param_name' => 'pos_width',
									'admin_label' => true,
									'value' => '50px'
								),
								
								array(
									'type' => 'dropdown',
									'heading' => esc_html__('Select Option', 'pt_theplus'),
									'param_name' => 'select_option',
									'value' => array(
										esc_html__('Image', 'pt_theplus') => 'image',
										esc_html__('Text Content', 'pt_theplus') => 'text'
									),
									'description' => '',
									
								),
								array(
									'type' => 'textfield',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add title of Cascading using this option.','pt_theplus').'</span></span>'.esc_html__('Cascading of Title', 'pt_theplus')),
									'param_name' => 'text_title',
									'value' => 'Test Demo',
									'description' => '',
									'dependency' => array(
										'element' => 'select_option',
										'value' => array(
											'text'
										)
									)
								),
								array(
									'type' => 'textfield',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font size', 'pt_theplus')),
									'param_name' => 'font_size',
									'value' => '20px',
									'description' => '',
									'edit_field_class' => 'vc_col-xs-3',
									'dependency' => array(
										'element' => 'select_option',
										'value' => array(
											'text'
										)
									)
								),
								array(
									'type' => 'textfield',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
									'param_name' => 'line_height',
									'value' => '24px',
									'description' => '',
									'edit_field_class' => 'vc_col-xs-3',
									'dependency' => array(
										'element' => 'select_option',
										'value' => array(
											'text'
										)
									)
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
									'param_name' => 'text_color',
									'value' => '#252525',
									'description' => '',
									'edit_field_class' => 'vc_col-xs-3',
									'dependency' => array(
										'element' => 'select_option',
										'value' => array(
											'text'
										)
									)
								),
								array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Title Custom font family', 'pt_theplus'),
								'param_name' => 'title_use_theme_fonts',
								 "value" => array(
									esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
									esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
								),
								'dependency' => array(
										'element' => 'select_option',
										'value' => array(
											'text'
										)
								),
								'std' =>  'custom-font-family',
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'title_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
									'element' => 'title_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'title_font_weight',
							'value' => __('600','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
									'element' => 'title_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'title_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'title_use_theme_fonts',
									'value' => 'google-fonts',
								),
						),
						 array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose the Image Source from below options.','pt_theplus').'</span></span>'.esc_html__('Image Source', 'pt_theplus')),
							"param_name" => "mulitple_image_source",
							"value" => array(
								esc_html__('Media library', 'pt_theplus') => 'media_library',
								esc_html__('External link', 'pt_theplus') => 'externals_link',
							),
							'std' => 'media_library',
							"description" => '',
							'dependency' => array(
										'element' => 'select_option',
										'value' => array(
											'image'
										)
									),
							),
								array(
									'type' => 'attach_image',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Upload image of Cascading Image using this option. You can upload .jpg, .png, .gif formats.','pt_theplus').'</span></span>'.esc_html__('Cascading Image', 'pt_theplus')),
									'param_name' => 'multiple_image',
									'value' => '',
									'description' => '',
									'admin_label' => true,
									'dependency' => array(
										'element' => 'mulitple_image_source',
										'value' => array(
											'media_library'
										)
									),
									'edit_field_class' => 'vc_col-xs-6'
								),
								array(
									'type' => 'textfield',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Example: thumbnail, medium, large, full or other sizes defined by theme(Example: 200x100 (Width x Height)).','pt_theplus').'</span></span>'.esc_html__('Image size', 'pt_theplus')),
									'param_name' => 'image_size',
									'value' => 'full',
									'dependency' => array(
										'element' => 'mulitple_image_source',
										'value' => array(
											'media_library'
										)
									),
									'edit_field_class' => 'vc_col-xs-6'
								),
								array(
									'type' => 'textfield',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select external link.','pt_theplus').'</span></span>'.esc_html__('External Image', 'pt_theplus')),
									'param_name' => 'mulitple_external_img',
									'value' => '',
									'description' => '',
									'dependency' => array(
										'element' => 'mulitple_image_source',
										'value' => array( 'externals_link')
									),
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Image Effect using this option.','pt_theplus').'</span></span>'.esc_html__('Image Effect', 'pt_theplus')),
									'param_name' => 'image_effect',
									'value' => array(
										esc_html__('None', 'pt_theplus') => '',
										esc_html__('Pulse', 'pt_theplus') => 'pulse',
										esc_html__('Floating', 'pt_theplus') => 'floating',
										esc_html__('Tossing', 'pt_theplus') => 'tossing'
									),
									'edit_field_class' => 'vc_col-xs-6'
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Image Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Image Style', 'pt_theplus')),
									'param_name' => 'image_style',
									'value' => array(
										esc_html__('Default', 'pt_theplus') => '',
										esc_html__('Rounded', 'pt_theplus') => 'vc_box_rounded',
										esc_html__('Border', 'pt_theplus') => 'vc_box_border',
										esc_html__('Outline', 'pt_theplus') => 'vc_box_outline',
										esc_html__('Shadow', 'pt_theplus') => 'vc_box_shadow',
										esc_html__('Bordered shadow', 'pt_theplus') => 'vc_box_shadow_border',
										esc_html__('3D Shadow', 'pt_theplus') => 'vc_box_shadow_3d',
										esc_html__('Circle', 'pt_theplus') => 'vc_box_circle'
									),
									'description' => '',
									'edit_field_class' => 'vc_col-xs-6',
									'dependency' => array(
										'element' => 'select_option',
										'value' => array(
											'image'
										)
									)
								),
								
						array(
							'type' => 'pt_theplus_checkbox',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can put animation on scroll for your section using this option.','pt_theplus').'</span></span>'.esc_html__('Magic Scroll', 'pt_theplus')), 
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
									"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose options of animation based on position and scale for section.','pt_theplus').'</span></span>'.esc_html__('Scroll Type', 'pt_theplus')), 
									'param_name' => 'scroll_type',
									'value' => array(
										esc_html__('Position', 'pt_theplus') => 'position',
										esc_html__('Scale', 'pt_theplus') => 'scale',
										esc_html__('Position and Scale', 'pt_theplus') => 'both',
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
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Horizontal Distance. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('(X) / Horizontal Distance', 'pt_theplus')),
							'param_name' => 'distance_scroll_x',
							'value' => '0',
							'description' => '',
							
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
							'description' => '',
							
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
							'description' => '',
							
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
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This effect will create two color animation ok this when someone scroll and reach to this section. For more information check our demo.','pt_theplus').'</span></span>'.esc_html__('Overlay Special effect', 'pt_theplus')), 
							'param_name' => 'special_effect',
							'description' => '',
							'edit_field_class' => 'vc_col-xs-12',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can can select color and Opacity for effect using this option.','pt_theplus').'</span></span>'.esc_html__('Effect Color 1', 'pt_theplus')),
							'param_name' => 'effect_color_1',
							"description" => "",
							'value' => '#313131',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'special_effect',
								'value' => array(
									'on',
								)
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can can select color and Opacity for effect using this option.','pt_theplus').'</span></span>'.esc_html__('Effect Color 2', 'pt_theplus')),
							'param_name' => 'effect_color_2',
							"description" => "",
							'value' => '#ff214f',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'special_effect',
								'value' => array(
									'on',
								)
							),
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This effect will be parallax on scroll effect. It will move image as you scroll your page.','pt_theplus').'</span></span>'.esc_html__('Parallax Move', 'pt_theplus')),
							'param_name' => 'cascading_move_parallax',
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
							'param_name' => 'cascading_move_speed_x',
							'value' => '30',
							'description' => '',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'cascading_move_parallax',
								'value' => array(
									'on'
								)
							)
						),
						array(
							'type' => 'textfield',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Vertical Speed move parallax. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('Move Parallax (Y)', 'pt_theplus')),
							'param_name' => 'cascading_move_speed_y',
							'value' => '30',
							'description' => '',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'cascading_move_parallax',
								'value' => array(
									'on'
								)
							)
						),
								array(
									'type' => 'textfield',
									"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('By Entering value of this in positive or negative, You can turn on Z axis 3D Parallax on this section. e.g. 100px, -200px, etc.','pt_theplus').'</span></span>'.esc_html__('3D Parallax Value', 'pt_theplus')),
									'param_name' => 'parallax_translatez',
									'value' => '30px',
									'description' => '',
									'dependency' => array(
										'element' => 'hover_parallax',
										'value' => array(
											'yes'
										)
									),
									'edit_field_class' => 'vc_col-xs-12'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of Image box-shadow.. e.g. 0px 0px 10px 20px #d3d3d3 etc.','pt_theplus').'</span></span>'.esc_html__('Image Shadow', 'pt_theplus')), 
									"description" => "",
									'param_name' => 'img_shadow',
									'value' => '',
									'edit_field_class' => 'vc_col-xs-6',
									'dependency' => array(
										'element' => 'select_option',
										'value' => array(
											'image'
										)
									),
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of Image hover box-shadow.. e.g. 0px 0px 10px 20px #d3d3d3 etc.','pt_theplus').'</span></span>'.esc_html__('Image Hover Shadow', 'pt_theplus')), 
									"description" => "",
									'param_name' => 'img_hover_shadow',
									'value' => '',
									'edit_field_class' => 'vc_col-xs-6',
									'dependency' => array(
										'element' => 'select_option',
										'value' => array(
											'image'
										)
									),
								),
								array(
									"type" => "dropdown",
									"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
									"param_name" => "animation_effects",
									'edit_field_class' => 'vc_col-xs-6',
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
									'std' => 'no-animation'
								),
								array(
									"type" => "textfield",
									"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),
									"param_name" => "animation_delay",
									"value" => '50',
									"description" => '',
									'edit_field_class' => 'vc_col-xs-6'
								)
							),
							'dependency' => array(
								'element' => 'animated_style',
								'value' => 'cascading-image'
							)
							
						),
						/*-------------------cascading image multiple group-------------------*/
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This effect will be SlideShow cascading Image gallery.','pt_theplus').'</span></span>'.esc_html__('Slide Show', 'pt_theplus')),
							'param_name' => 'slide_show',
							'description' => '',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							'dependency' => array(
								'element' => 'animated_style',
								'value' => 'cascading-image',
							),
							"edit_field_class" => "vc_col-xs-12"
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose option slide Image on Click / Autoplay.','pt_theplus').'</span></span>'.esc_html__('Slide Image Play', 'pt_theplus')),
							"param_name" => "slide_change_opt",
							"value" => array(
								esc_html__('On Click', 'pt_theplus') => 'onclick',
								esc_html__('SetInterval', 'pt_theplus') => 'setinterval',
							),
							'std' => 'onclick',
							'dependency' => array(
								'element' => 'slide_show',
								'value' => 'on',
							),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Cascading Images Set Interval Time AutoPlay. Ex. 4000,5000,..','pt_theplus').'</span></span>'.esc_html__('Autoplay Set Interval Time', 'pt_theplus')),
							'param_name' => 'interval_time',
							'value' => '4000',
							'dependency' => array(
								'element' => 'slide_change_opt',
								'value' => array( 'setinterval')
							),
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose the Image Source from below options.','pt_theplus').'</span></span>'.esc_html__('Image Source', 'pt_theplus')),
							"param_name" => "image_source",
							"value" => array(
								esc_html__('Media library', 'pt_theplus') => 'media_library',
								esc_html__('External link', 'pt_theplus') => 'externals_link',
							),
							'std' => 'media_library',
							"description" => '',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
								)
							),
							),
						array(
							'type' => 'attach_image',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Upload image of Image using this option. You can upload .jpg, .png, .gif formats.','pt_theplus').'</span></span>'.esc_html__('Image', 'pt_theplus')),
							'param_name' => 'image',
							'value' => '',
							'description' => '',
							'admin_label' => false,
							'dependency' => array(
								'element' => 'image_source',
								'value' => array( 'media_library')
							),
							'edit_field_class' => 'vc_col-xs-12'
						),
						 array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select external link.','pt_theplus').'</span></span>'.esc_html__('External Image', 'pt_theplus')),
							'param_name' => 'external_img',
							'value' => '',
							'description' => '',
							'dependency' => array(
								'element' => 'image_source',
								'value' => array( 'externals_link')
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your Selected color will be load first and then Image will be loaded in this Style of animated Image.','pt_theplus').'</span></span>'.esc_html__('Animated Background Color', 'pt_theplus')),
							'param_name' => 'bg_color',
							"description" => "",
							'value' => '#d3d3d3',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'animate-image'
								)
							),
							'edit_field_class' => 'vc_col-xs-6'
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose the direction of Animation from below options.','pt_theplus').'</span></span>'.esc_html__('Animation Direction', 'pt_theplus')),
							"param_name" => "animated_direction",
							"value" => array(
								esc_html__('Left', 'pt_theplus') => 'left',
								esc_html__('Right', 'pt_theplus') => 'right',
								esc_html__('Top', 'pt_theplus') => 'top',
								esc_html__('Bottom', 'pt_theplus') => 'bottom',
							),
							"description" => '',
							'std' => 'left',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'animate-image'
								)
							),
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('(Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme)(Example: 200x100 (Width x Height)).','pt_theplus').'</span></span>'.esc_html__('Image size', 'pt_theplus')),
							'param_name' => 'img_size',
							'value' => 'full',
							'description' => '',
							'dependency' => array(
								'element' => 'image_source',
								'value' => array( 'media_library')
							),
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose image alignment from Right, Left or Center.','pt_theplus').'</span></span>'.esc_html__('Image Alignment ', 'pt_theplus')),
							'param_name' => 'alignment',
							'value' => array(
								esc_html__('Left', 'pt_theplus') => 'left',
								esc_html__('Right', 'pt_theplus') => 'right',
								esc_html__('Center', 'pt_theplus') => 'center'
							),
							'description' => '',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
								)
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Image Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Image style', 'pt_theplus')),
							'param_name' => 'style',
							'value' => array(
								esc_html__('Default', 'pt_theplus') => '',
								esc_html__('Rounded', 'pt_theplus') => 'vc_box_rounded',
								esc_html__('Border', 'pt_theplus') => 'vc_box_border',
								esc_html__('Outline', 'pt_theplus') => 'vc_box_outline',
								esc_html__('Shadow', 'pt_theplus') => 'vc_box_shadow',
								esc_html__('Bordered shadow', 'pt_theplus') => 'vc_box_shadow_border',
								esc_html__('3D Shadow', 'pt_theplus') => 'vc_box_shadow_3d',
								esc_html__('Circle', 'pt_theplus') => 'vc_box_circle'
							),
							'description' => '',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can can select color and Opacity for border using this option.','pt_theplus').'</span></span>'.esc_html__('Border Color', 'pt_theplus')),
							'param_name' => 'border_color',
							"description" => "",
							'value' => '#d3d3d3',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
								)
							),
							'dependency' => array(
								'element' => 'style',
								'value' => array(
									'vc_box_border',
									'vc_box_border_circle',
									'vc_box_outline'
								)
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select action for click action.','pt_theplus').'</span></span>'.esc_html__('On Click Action', 'pt_theplus')),
							'param_name' => 'onclick',
							'value' => array(
								esc_html__('None', 'pt_theplus') => '',
								esc_html__('Link to large image', 'pt_theplus') => 'img_link_large',
								esc_html__('Open prettyPhoto', 'pt_theplus') => 'link_image',
								esc_html__('Open custom link', 'pt_theplus') => 'custom_link',
								esc_html__('Zoom Image', 'pt_theplus') => 'zoom'
							),
							'description' => '',
							'std' => '',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
								)
							)
							
						),
						array(
							'type' => 'href',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter URL if you want this image to have a link (Note: parameters like "mailto:" are also accepted).','pt_theplus').'</span></span>'.esc_html__('Image link', 'pt_theplus')),
							'param_name' => 'link',
							'dependency' => array(
								'element' => 'onclick',
								'value' => 'custom_link'
							),
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Link Open Traget Option for using this option.','pt_theplus').'</span></span>'.esc_html__('Link Target', 'pt_theplus')),
							'param_name' => 'img_link_target',
							'value' => array(
								esc_html__('Same window', 'pt_theplus') => '_self',
								esc_html__('New window', 'pt_theplus') => '_blank'
							),
							'dependency' => array(
								'element' => 'onclick',
								'value' => array(
									'custom_link',
									'img_link_large'
								)
							),
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Link Open Traget Option for using this option.','pt_theplus').'</span></span>'.esc_html__('Image Hover Style', 'pt_theplus')),
							'param_name' => 'image_hover',
							'value' => array(
								esc_html__('None', 'pt_theplus') => '',
								esc_html__('Pulse', 'pt_theplus') => 'pulse',
								esc_html__('Floating', 'pt_theplus') => 'floating',
								esc_html__('Tossing', 'pt_theplus') => 'tossing'
							),
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
								)
							),
							'edit_field_class' => 'vc_col-xs-8'
						),
						array(
							'type' => 'checkbox',
							"heading" => esc_html__("On Hover Style", 'pt_theplus'),
							'param_name' => 'on_hover_style',
							'value' => array(
								__('yes', 'pt_theplus') => 'yes'
							),
							'edit_field_class' => 'vc_col-xs-4',
							"dependency" => array(
								"element" => "image_hover",
								"value" => array(
									'pulse',
									'floating',
									'tossing'
								)
							),
							'description' => '',
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This effect will be parallax on scroll effect. It will move image as you scroll your page.','pt_theplus').'</span></span>'.esc_html__('Super Parallax', 'pt_theplus')),
							'param_name' => 'bg_image_parallax',
							'description' => '',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
								)
							),
							"edit_field_class" => "vc_col-xs-12"
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
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
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
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can put animation on scroll for your section using this option.','pt_theplus').'</span></span>'.esc_html__('Magic Scroll', 'pt_theplus')), 
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
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
								)
							),
							"edit_field_class" => "vc_col-xs-12"
						),
						array(
									'type' => 'dropdown',
									"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose options of animation based on position and scale for section.','pt_theplus').'</span></span>'.esc_html__('Scroll Type', 'pt_theplus')), 
									'param_name' => 'scroll_type',
									'value' => array(
										esc_html__('Position', 'pt_theplus') => 'position',
										esc_html__('Scale', 'pt_theplus') => 'scale',
										esc_html__('Position and Scale', 'pt_theplus') => 'both',
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
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Horizontal Distance. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('(X) / Horizontal Distance', 'pt_theplus')),
							'param_name' => 'distance_scroll_x',
							'value' => '0',
							'description' => '',
							
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
							'description' => '',            
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
							'description' => '',
							
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'scroll_type',
								'value' => array(
									'scale','both'
								)
							)
						),
						array(
							'type' => 'checkbox',
							 "heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can put option of on hover tilt effect on section using this option.','pt_theplus').'</span></span>'.esc_html__('On Hover Tilt', 'pt_theplus')),
							'param_name' => 'hover_parallax',
							 'edit_field_class' => 'vc_col-xs-12',
							'value' => array(
								__('yes', 'pt_theplus') => 'yes'
							),
							'description' => '',
						),
						array(
							'type' => 'pt_theplus_checkbox',
							"heading" =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This effect will create two color animation ok this when someone scroll and reach to this section. For more information check our demo.','pt_theplus').'</span></span>'.esc_html__('Overlay Special effect', 'pt_theplus')),
							'param_name' => 'special_effect',
							'description' => '',
							'edit_field_class' => 'vc_col-xs-12',
							'value' => 'off',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
								)
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can can select color and Opacity for effect using this option.','pt_theplus').'</span></span>'.esc_html__('Effect Color 1', 'pt_theplus')),
							'param_name' => 'effect_color_1',
							"description" => "",
							'value' => '#313131',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'special_effect',
								'value' => array(
									'on',
								)
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can can select color and Opacity for effect using this option.','pt_theplus').'</span></span>'.esc_html__('Effect Color 2', 'pt_theplus')),
							'param_name' => 'effect_color_2',
							"description" => "",
							'value' => '#ff214f',
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'special_effect',
								'value' => array(
									'on',
								)
							),
						),
					   array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Animation Settings', 'pt_theplus'),
						'param_name' => 'annimation_effect',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
								)
							)
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
							'std' => 'no-animation',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
								)
							)
							
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),	
							"param_name" => "animation_delay",
							"value" => '50',
							"description" => '',
							'dependency' => array(
								'element' => 'animated_style',
								'value' => array(
									'creative-simple-image',
									'animate-image'
								)
							)
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
							'param_name' => 'el_class',
							'description' => '',
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
					)
				));
			}
		}
	}
	new ThePlus_animated_image;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_animated_image'))
	{
		class WPBakeryShortCode_tp_animated_image extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}
