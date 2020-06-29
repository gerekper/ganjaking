<?php
// Row Settings Elements
if(!class_exists("ThePlus_row_settings")){
	class ThePlus_row_settings{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_row_settings') );
			add_shortcode( 'tp_row_settings',array($this,'tp_row_settings_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_row_settings_scripts' ), 1 );
		}
		function tp_row_settings_scripts() {
			wp_register_style( 'rowvcpt_vegas_css', THEPLUS_PLUGIN_URL .'vc_elements/css/extra/vegas.css', false, '1.0.0');
			wp_register_style( 'theplus-smart-gallery', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-smart-gallery.css', false, '1.0.0' );
			wp_register_script( 'anime_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/anime.min.js'); // image segmentation		
			wp_register_script( 'segmentation_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/segmentation.js'); // image segmentation
			wp_register_script( 'particleground_js', THEPLUS_PLUGIN_URL .'vc_elements/js/extra/jquery.particleground.js'); // canvas style 6	
		}
		function tp_row_settings_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'select_anim'=>'',
					'normal_bg_color'=>'',
					'gradient_color1'=>'',
					'gradient_color2'=>'',
					'gradient_style'=>'horizontal',
					'overlay_color_opacity'=>'1',
					'image_source' => 'media_library',
					'column_bg_image_new'=>'',
					'external_img' => '',
					'columns_parallax_style'=>'columns_simple_image',
					'image_parallax_style' =>'',
					'columns_animation_direction'=>'',
					'bg_img_parallax' => 'off',
					'columns_parallax_sense' =>'',
					'column_bg_image_position'=>'',
					'column_bg_image_size' =>'cover',
					'column_bg_img_attach' =>'scroll',
					'columns_bg_colors' =>'#5c5c5c',
					'column_anim_bg_duration'=>'3000',
					'columns_video_variant' =>'self-hosted',
					'columns_video_url_mp4' =>'',
					'columns_video_url_webm' =>'',
					'columns_youtube_video_id' =>'',
					'columns_vimeo_video_id' =>'',
					'columns_video_opts' =>'',
					'columns_video_poster' =>'',
					'images_gallery'=>'',
					'gallery_slide_style'=>'fade2',
					'gallery_overlays'=>'false',
					'gallery_animation_duration'=>'5000',
					'slide_delay_time'=>'7000',
					
					'group_gallery_images'=>'',
					'layout'=>'grid',
					'desktop_column'=>'3',
					'tablet_column'=>'6',
					'mobile_column'=>'12',
					'metro_column'=>'4',
					'column_space' =>'',
					'column_space_pading' => '10px',
					
					'middle_style' => '',
					'aniamted_svg_option'=>'',

					'canvas_style'=> 'style_1',
					'canvas_multi_color'=>'',
					'canvas_style2_color_r'=>'255',
					'canvas_style2_color_g' =>'255',
					'canvas_style2_color_b' =>'255',
					'canvas_style6_color' =>'#685e52',
					'canvas_type'=>'circle',
					'mordern_images' =>'',
					'moving_images' =>'',
					'mordern_effects'=>'',
					'overlay_style' =>'',
					'normal_overlay_color' =>'',
					'overlay_gradient_color1'=>'',
					'overlay_gradient_color2'=>'',
					'overlay_gradient_style'=>'horizontal',
					'overlay_gradient_opacity'=>'0.5',
					'texture_image'=>'',
					'opacity_texture_image'=>'0.5',

					'bg_options'=>'bg_image',
					'bg_image'=>'',
					'bg_pieces_color'=>'',
					'bg_gradient1'=>'',
					'bg_gradient2'=>'',
					'bg_gradient_style'=>'horizontal',
							'style_of_pieces'=>'style-1',
						'no_of_pieces'=>'4',
							'image_clip_opt'=> '',	
							'parallax_effect'=>'false',  
							'min_parallax'=>'10',
							'max_parallax'=>'40',
							'opacity_pieces'=>'1',
							'anim_duration' => '1500',
							'easing_transition'=>'easeOutExpo',
							'delay_transition'=>'100',
							'top_shadow'=>'10',
							'left_shadow'=>'10',
							'border_width'=>'0px',
							'border_style'=>'solid',
							'border_color'=>'rgba(255,255,255,0.7)',
					'translatez_min'=>'10',
					'translatez_max'=>'100',
					'box_shadow_pieces'=>'0px 2px 15px 0px rgba(0,0,0,0.7)',
					), $atts ) );


					/* row*/
					$css_rules = $css_rules1 = $class1 =$data_atts = $anim_value = $keframe_css = $parellax_class = $parellax_inner_class= '';

					$uniqid = uniqid('pt_plus_row_animate_bg');
					$uniqid1 = uniqid('pt_plus_row_image_bg');
					$rand_no =uniqid('pt-plus-rowsetting');
					$keyframe_pref = array('@-webkit-keyframes','@-moz-keyframes','@-ms-keyframes','@-o-keyframes','@keyframes');
					/* row*/


					if(!empty($image_parallax_style) && $columns_parallax_style=='columns_simple_image'){
					$parellax_class = 'pt_plus_image_mouse_hover';
					}
					$get_name = wp_get_theme();
$img_parallax_scroll_class='';
if(isset($select_anim) && !empty($select_anim) && $select_anim=='bg_image') {
if(!empty($bg_img_parallax) && $bg_img_parallax=='on'){
$img_parallax_scroll_class=' row-parallax-bg-img';
}
}
					$output ='<div id="pt-plus-row-settings" class="pt-plus-row-set '.esc_attr($rand_no).' '.esc_attr($parellax_class).' '.esc_attr($img_parallax_scroll_class).'" data-no="'.esc_attr($rand_no).'" data-get-name="'.esc_attr($get_name).'">';

					
					
					/* row*/
					/*-------- background color-------*/
					if(isset($select_anim) && !empty($select_anim) && $select_anim=='bg_normal_color') {
					if(isset($normal_bg_color) && !empty($normal_bg_color)) {
					$output .='<div class="pt-plus-columns-bg-wrap" style="background:'.esc_attr($normal_bg_color).'"></div>';
					}
					}
					/*-------- background color-------*/
					/*-------- background Animation color-------*/
					if(isset($select_anim) && !empty($select_anim) && $select_anim=='bg_color') {
					if(isset($columns_bg_colors) && !empty($columns_bg_colors) && function_exists('vc_param_group_parse_atts')) {
						$columns_bg_colors = (array) vc_param_group_parse_atts( $columns_bg_colors );
							
					$animate_id = uniqid('pt_plus_row_animate_bg');	
					 $colors =array();	
						
							foreach($columns_bg_colors as $item) {
								if(isset($item['column_bg_single_color']) && $item['column_bg_single_color'] != '') {
											$colors[]=$item['column_bg_single_color'];
								}
							}		
						$column_anim_bg_duration = (isset($column_anim_bg_duration) && $column_anim_bg_duration != '') ? $column_anim_bg_duration : 3000;
							
						$output .='<div class="pt-plus-columns-bg-wrap columns-bg-anim-colors row-animated-bg '.esc_attr($animate_id).'" data-id="'.esc_attr($animate_id).'" data-bg-time="'.esc_attr($column_anim_bg_duration).'" data-bg="'.htmlspecialchars(json_encode($colors)).'"></div>';
						
					}
					}
					/*-------- background Animation color-------*/
					/*----------gradient color---*/
					if(isset($select_anim) && !empty($select_anim) && $select_anim=='bg_gradientcolor'){
					if(!empty($gradient_color1) && !empty($gradient_color2)){

					$css_rules .='opacity: '.esc_attr($overlay_color_opacity).';';
					$css_rules .= pt_plus_gradient_color($gradient_color1,$gradient_color2,$gradient_style);

					$output .='<div class="pt-plus-columns-bg-wrap columns-bg-anim-colors" style="'.$css_rules.'"></div>';

					}
					}
					/*----------gradient color---*/
					/*----------bg image---*/
					if(isset($select_anim) && !empty($select_anim) && $select_anim=='bg_image') {
$img_parallax_scroll='';
if(!empty($bg_img_parallax) && $bg_img_parallax=='on'){
$img_parallax_scroll=' parallax-bg-img';
}
					$class1 .= esc_attr($columns_parallax_style);
					if($columns_parallax_style== 'columns_animated_bg') {
						$columns_animation_direction = (isset($columns_animation_direction) && !empty($columns_animation_direction)) ? $columns_animation_direction : 'left';
						$class1 .= ' columns-'.esc_attr($columns_animation_direction).'-animation';
						$data_atts .= ' data-direction="'.esc_attr($columns_animation_direction).'"';
					}

						if(isset($columns_parallax_sense) && !empty($columns_parallax_sense))
							$data_atts .= ' data-parallax_sense="'.esc_attr($columns_parallax_sense).'"';
						else 
							$data_atts .= ' data-parallax_sense="30"';

					$bg_image1=$css_rules1='';
					if ($image_source == 'media_library') {
						if(isset($column_bg_image_new) && !empty($column_bg_image_new)) {
									$full_image=wp_get_attachment_image_src( $column_bg_image_new, 'full' );
										
										$bg_image1='background:url('.esc_url($full_image[0]).');';
							}
					} else if ($image_source == 'externals_link') {
								
								$bg_image1='background:url('.esc_url($external_img).');';
					}	
					
						if(isset($column_bg_image_position) && !empty($column_bg_image_position))
							$css_rules1 .= 'background-position: '.esc_attr($column_bg_image_position).';';
						
						if(isset($column_bg_image_size) && !empty($column_bg_image_size))
							$css_rules1 .= '-webkit-background-size: '.esc_attr($column_bg_image_size).';-moz-background-size: '.esc_attr($column_bg_image_size).';-o-background-size: '.esc_attr($column_bg_image_size).';background-size: '.esc_attr($column_bg_image_size).';';

						if(isset($column_bg_img_attach) && !empty($column_bg_img_attach))
							$css_rules1 .= 'background-attachment: '.esc_attr($column_bg_img_attach).';';
							
					$parallax_atts='';	
					if(!empty($image_parallax_style) && $columns_parallax_style=='columns_simple_image' && $image_parallax_style=='style_1'){
					$parellax_inner_class = 'pt_plus_image_parallax_inner_hover';
					$parallax_atts='data-type="tilt" data-amount="30" data-inverted="false" data-opacity="1"';
					if(isset($column_bg_img_attach) && !empty($column_bg_img_attach))
							$css_rules1 .= 'background-attachment: scroll;';
					}
					if(!empty($image_parallax_style) && $columns_parallax_style=='columns_simple_image' && $image_parallax_style=='style_2'){
					$parellax_inner_class = 'pt_plus_image_parallax_inner_hover';
					$parallax_atts='data-type="move" data-amount="30" data-inverted="false" data-opacity="1"';
					if(isset($column_bg_img_attach) && !empty($column_bg_img_attach))
							$css_rules1 .= 'background-attachment: scroll;';
					}

					$output .= '<div class="pt-plus-columns-bg-wrap columns-bg-anim-colors parallax-bg-img columns-bg-image '.esc_attr($parellax_inner_class).' '.esc_attr($class1).' '.esc_attr($img_parallax_scroll).'" id="'.esc_attr($uniqid1).'" '.$parallax_atts.' '.$data_atts.'  style="'.$bg_image1.$css_rules1.'"></div>';
					}
					/*----------bg image---*/
					/*----------bg video---*/
					$data_atts2 = $video_atts2 = $controller_css2 = '';
					 
					$uniqid2 = uniqid('row_video_bg_');
					if(isset($columns_video_variant) && !empty($columns_video_variant) && $select_anim=='bg_video') {
						   if(isset($columns_video_poster) && !empty($columns_video_poster)) {
							$poster_src = wp_get_attachment_image_src($columns_video_poster,'full');
							$poster_url = $poster_src[0];
						} else {
							$poster_url = THEPLUS_PLUGIN_URL.'/vc_elements/images/placeholder-grid.jpg';
						}
							if($columns_video_variant== 'self-hosted' && (isset($columns_video_url_mp4) || isset($columns_video_url_mp4))) {
							
							//$video_atts2 .= 'poster="'. $poster_url .'"';

							if(isset($columns_video_opts) && !empty($columns_video_opts)) {
								if(substr_count($columns_video_opts, 'loop') == 1) {
									$video_atts2 .= ' loop="true" ';
								}
								if(substr_count($columns_video_opts, 'muted') == 1) {
									$video_atts2 .= ' muted="true" ';	
								}		
							}

							$output .= '<div class="pt-plus-bg-video pt-plus-columns-bg-wrap columns-video-bg self-hosted-video-bg" id="wrapper-'.esc_attr($uniqid2).'" '.$data_atts2.' style="background-image: url('.esc_js($poster_url).');">';
							$output .= '<video id="'.esc_attr($uniqid2).'" class="self-hosted-videos video-js vjs-default-skin columns_vc_hidden-md columns_vc_hidden-sm columns_vc_hidden-xs" controls autoplay preload="auto" width="100%" height="100%"  autoplay="true" '.$video_atts2.' data-setup="{}">';

								if (!empty($columns_video_url_mp4)):
									$output .= '<source src="'.esc_url($columns_video_url_mp4).'" type="video/mp4">';
								endif;
								if (!empty($columns_video_url_webm)):
									$output .= '<source src="'.esc_url($columns_video_url_webm).'" type="video/webm">';
								endif;
							$output .= '</video>';

							$output .= '</div>';
						} elseif($columns_video_variant == 'youtube' || $columns_video_variant == 'vimeo') {

							$loop = false;
							if(isset($columns_video_opts) && !empty($columns_video_opts)) {
								if(substr_count($columns_video_opts, 'loop') == 1) {
									$loop = true;
								}
							if(substr_count($columns_video_opts, 'muted') == 1) {
									$data_atts2 .= ' data-muted="1"';
								}
							} else {
								$data_atts2 .= ' data-muted="0"';
							}
							if($columns_video_variant == 'youtube' && isset($columns_youtube_video_id) && !empty($columns_youtube_video_id)) {
								$extra_url_prop = '';
								if($loop) 
									$extra_url_prop .= '&amp;loop=1&amp;playlist='.$columns_youtube_video_id;
								$output .= '<div id="wrapper-'.esc_attr($uniqid2).'" class="pt-plus-columns-bg-wrap columns-video-bg columns-youtube-bg" style="background-image: url('.esc_js($poster_url).');">
												<div class="video-js columns_vc_hidden-md columns_vc_hidden-sm columns_vc_hidden-xs"><iframe id="'.esc_attr($uniqid2).'"  '.$data_atts2.' width="100%" height="100%" src="https://www.youtube.com/embed/'.esc_attr($columns_youtube_video_id).'?wmode=opaque&amp;autoplay=1'.esc_attr($extra_url_prop).'&amp;enablejsapi=1&amp;showinfo=0&amp;controls=0&amp;rel=0" frameborder="0" class="pt-plus-bg-video columns-bg-frame" allowfullscreen></iframe></div>
											</div>';
							}

							if($columns_video_variant == 'vimeo' && isset($columns_vimeo_video_id) && !empty($columns_vimeo_video_id)) {
								$extra_url_prop = '';
								if($loop) 
									$extra_url_prop .= '&amp;loop=1';
								$output .= '<div id="wrapper-'.esc_attr($uniqid2).'" class="pt-plus-columns-bg-wrap columns-video-bg columns-vimeo-bg" style="background-image: url('.esc_js($poster_url).');">
												<div class="video-js columns_vc_hidden-md columns_vc_hidden-sm columns_vc_hidden-xs"><iframe id="'.esc_attr($uniqid2).'"  '.$data_atts2.' src="https://player.vimeo.com/video/'.esc_attr($columns_vimeo_video_id).'?api=1&amp;autoplay=1;portrait=0&amp;rel=0'.esc_attr($extra_url_prop).'" width="100%" height="100%" frameborder="0" class="pt-plus-bg-video columns-bg-frame"></iframe></div>
											</div>';
							}
						}
							
					}
					/*----------bg video---*/
					/*----------bg gallery---*/
					
					$gallery_css='';
					$gallery1 ='';
					$uniq_id_gallery = uniqid('bgGallaryslide');
					if(isset($images_gallery) && !empty($images_gallery) && $select_anim=='bg_gallery') {
					wp_enqueue_script( 'vegas_js');
					wp_enqueue_style('rowvcpt_vegas_css');
					$images_gallery1 = explode(",", $images_gallery); 
					$output .='<div  id="'.$uniq_id_gallery.'" class="pt-plus-row-slideshow ">';
					foreach($images_gallery1 as $gallery){
								$img = wp_get_attachment_image_src($gallery, "full");
								$imgSrc = $img[0];
					$gallery1 .='{src: "'.esc_url($imgSrc).'",transition: "'.esc_attr($gallery_slide_style).'",transitionDuration: '.esc_attr($gallery_animation_duration).'},';
					}  
					$output .='</div>';
					if(!empty($gallery_overlays) && $gallery_overlays!='false'){
					$overlayurl=THEPLUS_PLUGIN_URL.'vc_elements/css/extra/overlays/0'.esc_attr($gallery_overlays).'.png';
					}else{
					$overlayurl=false;
					}
					$output .= '<script >
					(function($) {
					$("#'.$uniq_id_gallery.'").vegas({
							overlay: "'.$overlayurl.'",
							transitionDuration: 4e3,
							delay: '.esc_js($slide_delay_time).',
							slides: ['.$gallery1.']
						});		
					})(jQuery);
					</script>';
					}
					/*----------bg gallery---*/

					/*----------bg imageclip--*/
					if($select_anim=='bg_Image_pieces') {
					wp_enqueue_script( 'anime_js');
					wp_enqueue_script( 'segmentation_js');
					 $imgSrc=$opacity_animate='';
					$rand_no12=rand(1000, 1500000);

					if($style_of_pieces=="custom"){

					if(isset($image_clip_opt) && !empty($image_clip_opt) && function_exists('vc_param_group_parse_atts')) {
					$position='';
					$effects='';
					$animate_speed='';
						$image_clip_option= (array) vc_param_group_parse_atts( $image_clip_opt);		
						foreach($image_clip_option as $item) {
						$xpos=$item['pos_xposition'];
							$ypos=$item['pos_yposition'];
							$width=$item['pos_width'];
							$height=$item['pos_height'];
					$animate_pieces=$item['animate_pieces'];
					$animatespeed=$item['animate_speed'];
							$position .='{top:'.$ypos.', left: '.$xpos.', width: '.$width.', height: '.$height.'},';
					$effects .='{"effect" : "'.$animate_pieces.'"},';
					$animate_speed .='{"duration" : "'.$animatespeed.'"},';
						}
					}

					}elseif($style_of_pieces=="style-2"){

					$no_of_pieces='4';
					$anim_duration= '1100';
					$easing_transition= "easeInOutExpo";
					$delay_transition= '100';
					$translatez_min="100";
					$translatez_max="100";
					$opacity_pieces='1';
					$left_shadow='0';
					$top_shadow='0';
					$parallax_effect='true';
					$min_parallax='10';
					$max_parallax='40';
					$position='{top: 0, left: 0, width: 45, height: 45},{top: 55, left: 0, width: 45, height: 45},{top: 0, left: 55, width: 45, height: 45},{top: 55, left: 55, width: 45, height: 45}';
					$effects='{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""}';
					$animate_speed='{"duration" : ""},{"duration" : ""},{"duration" : ""},{"duration" : ""}';

					}elseif($style_of_pieces=="style-3"){

					$no_of_pieces='3';
					$anim_duration= '1300';
					$easing_transition= "easeInOutExpo";
					$delay_transition= '100';
					$translatez_min="10";
					$translatez_max="10";
					$opacity_pieces='1';
					$left_shadow='0';
					$top_shadow='0';
					$parallax_effect='false';
					$min_parallax='10';
					$max_parallax='10';
					$position='{top: 10, left: 25, width: 50, height: 25},{top: 40, left: 25, width: 50, height: 25},{top: 70, left: 25, width: 50, height: 25}';
					$effects='{"effect" : "rotate"},{"effect" : "rotate"},{"effect" : "rotate"}';
					$animate_speed='{"duration" : "3s"},{"duration" : "7s"},{"duration" : "5s"}';

					}elseif($style_of_pieces=="style-4"){

					$no_of_pieces='10';
					$anim_duration= '1300';
					$easing_transition= "easeOutQuad";
					$delay_transition= '50';
					$translatez_min="10";
					$translatez_max="65";
					$opacity_pieces='1';
					$left_shadow='20';
					$top_shadow='20';
					$parallax_effect='true';
					$min_parallax='10';
					$max_parallax='40';
					$position='{top: 0, left: 0, width: 30, height: 30},{top: 10, left: 10, width: 30, height: 30},{top: 20, left: 20, width: 30, height: 30},{top: 30, left: 30, width: 30, height: 30},{top: 40, left: 40, width: 30, height: 30},{top: 50, left: 50, width: 30, height: 30},{top: 60, left: 60, width: 30, height: 30},{top: 70, left: 70, width: 30, height: 30},{top: 80, left: 80, width: 30, height: 30},{top: 90, left: 90, width: 30, height: 30}';

					$effects='{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""}';
					$animate_speed='{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"}';

					$border_width="7px";
					$border_style="solid";
					$border_color="rgba(255,255,255,0.7)";

					}elseif($style_of_pieces=="style-5"){

					$no_of_pieces='7';
					$anim_duration= '1000';
					$easing_transition= "easeOutQuad";
					$delay_transition= '10';
					$translatez_min="10";
					$translatez_max="65";
					$opacity_pieces='1';
					$left_shadow='0';
					$top_shadow='0';
					$parallax_effect='true';
					$min_parallax='5';
					$max_parallax='10';
					$position='{top: 10, left: 20, width: 20, height: 30},{top: 8, left: 35, width: 30, height: 20},{top: 25, left: 18, width: 14, height: 25},{top: 23, left: 50, width: 20, height: 10},{top: 30, left: 65, width: 10, height: 30},{top: 48, left: 20, width: 10, height: 13},{top: 50, left: 67, width: 10, height: 20}';

					$effects='{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""}';
					$animate_speed='{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"}';


					}elseif($style_of_pieces=="style-6"){

					$no_of_pieces='8';
					$anim_duration= '1000';
					$easing_transition= "easeOutExpo";
					$delay_transition= '0';
					$translatez_min="10";
					$translatez_max="25";
					$opacity_pieces='0';
					$left_shadow='0';
					$top_shadow='0';
					$parallax_effect='true';
					$min_parallax='10';
					$max_parallax='30';
					$position='{top: 0, left: 0, width: 100, height: 100},{top: 0, left: 0, width: 100, height: 100},{top: 0, left: 0, width: 100, height: 100},{top: 0, left: 0, width: 100, height: 100},{top: 0, left: 0, width: 100, height: 100},{top: 0, left: 0, width: 100, height: 100},{top: 0, left: 0, width: 100, height: 100},{top: 0, left: 0, width: 100, height: 100}';

					$effects='{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""},{"effect" : ""}';
					$animate_speed='{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"},{"duration" : "0s"}';

					$opacity_animate="opacity: '0.1',";

					}else{

					$no_of_pieces='9';
					$anim_duration= '1000';
					$easing_transition= "easeInOutCubic";
					$delay_transition= '0';
					$translatez_min="-20";
					$translatez_max="20";
					$opacity_pieces='1';
					$left_shadow='0';
					$top_shadow='0';
					$parallax_effect='false';
					$min_parallax='1';
					$max_parallax='65';
					$position='{top: 30, left: 5, width: 40, height: 80},{top: 50, left: 25, width: 30, height: 30},{top: 5, left: 75, width: 40, height: 20},{top: 30, left: 45, width: 40, height: 20},{top: 45, left: 15, width: 50, height: 40},{top: 10, left: 40, width: 10, height: 20},{top: 20, left: 50, width: 30, height: 70},{top: 0, left: 10, width: 50, height: 60},{top: 70, left: 40, width: 30, height: 30}';

					$effects='{"effect" : "scale"},{"effect" : "flash"},{"effect" : "scale"},{"effect" : "float"},{"effect" : "flash"},{"effect" : "scale"},{"effect" : "float"},{"effect" : ""},{"effect" : ""}';
					$animate_speed='{"duration" : "3s"},{"duration" : "10s"},{"duration" : "5s"},{"duration" : "6s"},{"duration" : "4s"},{"duration" : "4s"},{"duration" : "5s"},{"duration" : "0s"},{"duration" : "0s"}';
					$opacity_animate="opacity: '1',";
					}
					$bg_style='';
					if(!empty($bg_options) && $bg_options=='bg_image'){
					if(isset($bg_image) && !empty($bg_image)) {
							 $img = wp_get_attachment_image_src($bg_image, "full");
							 $imgSrc = $img[0];
					$bg_style .='background-image: url('.esc_url($imgSrc).');';
					}
					}elseif(!empty($bg_options) && $bg_options=='bg_color'){
					if(isset($bg_pieces_color) && !empty($bg_pieces_color)){
					$bg_style .='background:'.esc_attr($bg_pieces_color).';';
					}
					}elseif(!empty($bg_options) && $bg_options=='bg_gradient'){
					if(!empty($bg_gradient1) && !empty($bg_gradient2)){
					$bg_style .= pt_plus_gradient_color($bg_gradient1,$bg_gradient2,$bg_gradient_style);
					}
					}

					$output .= '<div class="pt-plus-row-imageclip row-'.esc_attr($rand_no12).'" data-id="row-'.esc_attr($rand_no12).'" data-box-shadow="'.esc_attr($box_shadow_pieces).'" data-border-width="'.esc_attr($border_width).'" data-border-style="'.esc_attr($border_style).'" data-border-color="'.esc_attr($border_color).'" style="'.$bg_style.'"></div>';

						$output .='<script>
							(function($) {	
					setTimeout(function(){ 
									var trigger = document.querySelector(".row-'.esc_js($rand_no12).'");
					var rowparent = trigger.parentNode.parentNode;
									var segmenter = new Segmenter(document.querySelector(".row-'.esc_js($rand_no12).'"), {
									pieces: '.$no_of_pieces.',
										animation: {
											duration: '.esc_js($anim_duration).',
											easing: "'.esc_js($easing_transition).'",
											delay: '.esc_js($delay_transition).',
											'.$opacity_animate.'
											translateZ: {min: '.esc_js($translatez_min).', max: '.esc_js($translatez_max).'}
										},
										shadowsAnimation: {
											opacity:'.esc_js($opacity_pieces).',
											 translateX: '.esc_js($left_shadow).',
											 translateY: '.esc_js($top_shadow).',
										},
										parallax: '.$parallax_effect.',
										parallaxMovement: {min: '.esc_js($min_parallax).', max: '.esc_js($max_parallax).'},
										positions: ['.$position.'],
										animated_effects : {
											effects:['.$effects.'],
											animatespeed: ['.$animate_speed.']
										},
										onReady: function() {

											rowparent.addEventListener("mouseover", function() {
												segmenter.animate();
											});
										},
										
									});
					}, 2000);
							})(jQuery);
							</script>';
						
					}
					/*----------bg imageclip--*/
					
					/*----------bg imageclip--*/
					if(!empty($select_anim) && $select_anim=='smart_gallery') {
						wp_enqueue_style( 'theplus-smart-gallery');
						$rand_no=rand(1000000, 1500000);
						
						$desktop_class='vc_col-md-'.esc_attr($desktop_column);
						$tablet_class='vc_col-sm-'.esc_attr($tablet_column);
						$mobile_class='vc_col-xs-'.esc_attr($mobile_column);
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
						
						$gallery_listing = '<div id="pt-plus-smart-gallery" class="plus-smart-gallery '.esc_attr($isotope).'   gallery-'.esc_attr($rand_no).' " '.$attr.' >';
						$gallery_listing .= '<div class="post-inner-loop gallery-'.esc_attr($rand_no).' ">';
						$i=1;
						if($layout=='metro'){
								$desktop_class=$tablet_class=$mobile_class='';
						}
						if(isset($group_gallery_images) && !empty($group_gallery_images) && function_exists('vc_param_group_parse_atts')) {
							$group_gallery_images= (array) vc_param_group_parse_atts( $group_gallery_images);		
							foreach($group_gallery_images as $item) {
								
								if(isset($item['gallery_images']) && !empty($item['gallery_images'])){
									$image_ids=explode(',',$item['gallery_images']);
									if(isset($item['set_interval_time']) && !empty($item['set_interval_time'])){
										$set_interval_time=$item['set_interval_time'];
									}else{
										$set_interval_time='3000';
									}
									
									$gallery_listing .= '<div class="grid-item  metro-item'.esc_attr($i).' '.$desktop_class.' '.$tablet_class.' '.$mobile_class.'" >';
									$gallery_listing .='<div class="gallery-attach-list"  data-interval-time="'.esc_attr($set_interval_time).'">';
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
																$gallery_listing .='<div class="gallery-image" >';
																		$gallery_listing .='<img src="'.esc_url($images[0]).'" alt="'.esc_attr($title).'">';
																$gallery_listing .='</div>';
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
						$css_rule .= '</style>';
						$output.= $css_rule.$gallery_listing;
					}
					/*----------bg imageclip--*/
					
					/*-------overlay canvas style 1-----*/
					if(!empty($middle_style) && $middle_style=='canvas'){ 
					if($canvas_style=='style_1'){

					if($canvas_multi_color!=''){
					$canvas_multi_color = (array) vc_param_group_parse_atts( $canvas_multi_color);
					foreach($canvas_multi_color as $item) {
								if(isset($item['canvas_single_color']) && $item['canvas_single_color'] != '') {			
									$background_colors[]=$item['canvas_single_color'];
								}
							}
					}else{
					$background_colors =array('#dd3333', '#dd9933', '#eeee22', '#81d742', '#1e73be');
					}
					$output .='<div class="pt-plus-bubble-wrap">';
					for($ij=1;$ij<=50;$ij++) {
					$size= rand(1,30).'px';
					  $output .='<div class="bubble" style="height: '.esc_attr($size).';width: '.esc_attr($size).';animation-delay: -'.($ij*0.2).'s;-webkit-transform:translate3d( '.rand(-2000,2000).'px,  '.rand(-1000,2000).'px, '.rand(-1000,2000).'px);transform: translate3d( '.rand(-2000,2000).'px,  '.rand(-1000,2000).'px, '.rand(-1000,2000).'px);background: '.$background_colors[array_rand($background_colors)].';"></div>'; 
					}
					$output .='</div>';
					}else if($canvas_style=='style_2'){
					wp_enqueue_script( 'easepack_js');
					wp_enqueue_script( 'raf_js');
					wp_enqueue_script( 'particles_min_js');
					wp_enqueue_script( 'projector_js');
					/*----- overlay canvas style 1----*/
					/*--------- overlay canvas style 2----*/
					 $output .='<div id="pt-plus-row-canvas-2" class="pt-plus-row-canvas-style-2" data-no="'.esc_attr($rand_no).'" data-color="'.esc_attr($canvas_style6_color).'">
									</div>';
					}else if($canvas_style=='style_5'){
					wp_enqueue_script( 'easepack_js');
					wp_enqueue_script( 'raf_js');
					wp_enqueue_script( 'particles_min_js');
					wp_enqueue_script( 'projector_js');
					/*--------- overlay canvas style 2----*/
					/*--------- overlay canvas style 5----*/
					 $output .='<div id="pt-plus-row-canvas-5" class="pt-plus-row-canvas-style-5" data-no="'.esc_attr($rand_no).'" data-color="'.esc_attr($canvas_style6_color).'">
									</div>';
					}else if($canvas_style=='style_3'){
					wp_enqueue_script( 'easepack_js');
					wp_enqueue_script( 'raf_js');
					wp_enqueue_script( 'particles_min_js');
					wp_enqueue_script( 'projector_js');
					/*--------- overlay canvas style 2----*/
					/*--------- overlay canvas style 3----*/
					 $output .='<div id="pt-plus-row-canvas-3" class="canvas-style-3" data-no="'.esc_attr($rand_no).'" data-type="'.esc_attr($canvas_type).'" data-color="'.esc_attr($canvas_style6_color).'">
									</div>';
					}else if($canvas_style=='style_4'){
					wp_enqueue_script( 'easepack_js');
					wp_enqueue_script( 'raf_js');
					wp_enqueue_script( 'particles_min_js');
					wp_enqueue_script( 'projector_js');
					/*--------- overlay canvas style 3----*/
					/*--------- overlay canvas style 4----*/
					 $output .='<div id="pt-plus-row-canvas-4" class="canvas-style-4" data-no="'.esc_attr($rand_no).'" data-type="'.esc_attr($canvas_type).'" data-color="'.esc_attr($canvas_style6_color).'">
									</div>';
					}else if($canvas_style=='style_7'){
					wp_enqueue_script( 'easepack_js');
					wp_enqueue_script( 'raf_js');
					wp_enqueue_script( 'particles_min_js');
					wp_enqueue_script( 'projector_js');
					/*--------- overlay canvas style 3----*/
					/*--------- overlay canvas style 4----*/
					 $output .='<div id="pt-plus-row-canvas-7" class="canvas-style-7" data-no="'.esc_attr($rand_no).'" data-type="'.esc_attr($canvas_type).'" data-color="'.esc_attr($canvas_style6_color).'">
									</div>';
					}else if($canvas_style=='style_6'){					
					wp_enqueue_script( 'particleground_js');
					/*--------- overlay canvas style 4----*/
					/*--------- overlay canvas style 6----*/
					 $output .='<div id="demo-canvas-6" class="canvas-style-6" data-canvas-color="'.esc_attr($canvas_style6_color).'"></div>';
					 $output .='<script>(function($) {
"use strict";
$(window).load(function() {
var cancolor=$(".canvas-style-6").attr("data-canvas-color");
$(".canvas-style-6").particleground({
  minSpeedX: 0.1,
  maxSpeedX: 0.3,
  minSpeedY: 0.1,
  maxSpeedY: 0.3,
  directionX: "center",
  directionY: "up",
  density: 10000,
  dotColor: cancolor,
  lineColor: cancolor,
  particleRadius: 7,
  lineWidth: 1,
  curvedLines: false,
  proximity: 100,
  parallax: true,
  parallaxMultiplier: 5,
  onInit: function() {},
  onDestroy: function() {}
});
});
})(jQuery);</script>';
					}
					/*--------- overlay canvas style 6----*/
					}

						
					/*-------- parellax Image row-------*/
					if(!empty($middle_style) && $middle_style=='mordern_parallax'){ 
						if(isset($mordern_images) && !empty($mordern_images) && function_exists('vc_param_group_parse_atts')) {
							$mordern_images= (array) vc_param_group_parse_atts( $mordern_images);
							
							foreach($mordern_images as $item) {
							
							
							$magic_class = $magic_attr = $parallax_scroll = '';
							if (!empty($item['magic_scroll']) && !empty($item["scroll_type"]) && $item['magic_scroll'] == 'on') {
								$magic_attr .= ' data-scroll_type="' . esc_attr($item["scroll_type"]) . '" ';
								if(!empty($item["scroll_type"]) && $item["scroll_type"]== 'position' ){
									$magic_attr .= ' data-scroll_x="' . esc_attr($item["distance_scroll_x"]) . '" ';
									$magic_attr .= ' data-scroll_y="' . esc_attr($item["distance_scroll_y"]) . '" ';
									$parallax_scroll .= ' parallax-scroll ';
								}
								if(!empty($item["scroll_type"]) && $item["scroll_type"]== 'scale'){
									$magic_attr .= ' data-scale_scroll="' . esc_attr($item["scale_scroll"]) . '" ';
									$parallax_scroll .= ' scale-scroll ';
								}
								if(!empty($item["scroll_type"]) && $item["scroll_type"]== 'both'){
									$magic_attr .= ' data-scroll_x="' . esc_attr($item["distance_scroll_x"]) . '" ';
									$magic_attr .= ' data-scroll_y="' . esc_attr($item["distance_scroll_y"]) . '" ';
									$magic_attr .= ' data-scale_scroll="' . esc_attr($item["scale_scroll"]) . '" ';
									$parallax_scroll .= ' both-scroll ';
								}
								$magic_class .= ' magic-scroll ';
							}
							
							if(isset($item['single_layer_image']) && $item['single_layer_image'] != '') {
							 $img = wp_get_attachment_image_src($item['single_layer_image'], "full");
							 $imgSrc = $img[0];
										if($imgSrc!=''){
										$xpos=$item['image_xposition'];
										$ypos=$item['image_yposition'];
										$tablet_width=$item['image_parallax_tablet_width'];
										$mobile_width=$item['image_parallax_mobile_width'];
										$tablet=$mobile='';
										if(!empty($tablet_width)){
											$tablet='data-tablet-width="'.esc_attr($tablet_width).'"';
										}
										if(!empty($mobile_width)){
											$mobile='data-mobile-width="'.esc_attr($mobile_width).'"';
										}
										$output .= '<div class="pt_plus_mordern_image_parallax ' . esc_attr($magic_class) . '" style="top:'.esc_attr($ypos).'%;left:'.esc_attr($xpos).'%;">';
										$output .='<div class="' . esc_attr($parallax_scroll) . '" ' . $magic_attr . '>';
											$output.='<img src="'.esc_url($imgSrc).'" alt="image-1" class="parallax_image"  data-parallax="'.esc_attr($item["parallax_value"]).'" '.$tablet.' '.$mobile.'>';
										$output.='</div>';
										$output.='</div>';
									}
									}	
							}
							
						}
					}
					/*-------- parellax Image row-------*/
					/*-------- moving Image row-------*/
					if(!empty($middle_style) && $middle_style=='moving_image'){ 
						if(isset($moving_images) && !empty($moving_images) && function_exists('vc_param_group_parse_atts')) {
							$moving_images= (array) vc_param_group_parse_atts( $moving_images);
							$output .='<div class="pt_plus_moving_images">';
							
							foreach($moving_images as $item) {
							
							if(isset($item['single_image_move']) && $item['single_image_move'] != '') {
								$full_image=wp_get_attachment_image_src( $item['single_image_move'], 'full' );
										$columns_animation_direction = (isset($item['move_image_direction']) && !empty($item['move_image_direction'])) ? $item['move_image_direction'] : 'left';
										$speed=$item['move_image_speed'];
										$output.='<div class="move-image-1 columns-bg-image columns_animated_bg pt-plus-loading-bg-image" style="background-image: url('.esc_url($full_image[0]).'); " data-direction="'.esc_attr($columns_animation_direction).'" data-parallax_sense="'.esc_attr($speed).'"></div>';
									
									}	
							}
							$output.='</div>';
						}
					}
					/*-------- moving Image row-------*/
					/*--------animated svg ------------*/
					if(!empty($middle_style) && $middle_style=='animated_svg'){ 
						if(isset($aniamted_svg_option) && !empty($aniamted_svg_option) && function_exists('vc_param_group_parse_atts')) {
							$aniamted_svg_option= (array) vc_param_group_parse_atts( $aniamted_svg_option);
							
							foreach($aniamted_svg_option as $item) {
							$rand_no=rand(1000000, 1500000);
							if(isset($item['svg_image']) && $item['svg_image'] != '') {
							 $img = wp_get_attachment_image_src($item['svg_image'], "full");
							 $imgSrc = $img[0];
										if($imgSrc!=''){
										$xpos=$item['svg_xposition'];
										$ypos=$item['svg_yposition'];
										$duration=$item['duration'];
										$type=$item['type'];
										$max_width=$item['max_width'];
										if(!empty($item['border_stroke_color'])){
											$border_stroke_color=$item['border_stroke_color'];
										}else{
											$border_stroke_color='none';
										}
										
										
										$output .='<div class="pt_plus_row_bg_animated_svg svg-'.esc_attr($rand_no).'" data-id="svg-'.esc_attr($rand_no).'" data-type="'.esc_attr($type).'" data-duration="'.esc_attr($duration).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="none">';
											$output .='<div class="svg_inner_block" style="max-width:'.esc_attr($max_width).';top:'.esc_attr($ypos).'%;left:'.esc_attr($xpos).'%;">';
												$output.='<object id="svg-'.esc_attr($rand_no).'" type="image/svg+xml" data="'.esc_url($imgSrc).'" ></object>';
											$output.='</div>';
										$output.='</div>';
									}
									}	
							}
							
						}
					}
					/*--------animated svg ------------*/
					/*-------- parellax Image row-------*/
					if(!empty($middle_style) && $middle_style=='mordern_image_effect'){ 
						if(isset($mordern_effects) && !empty($mordern_effects) && function_exists('vc_param_group_parse_atts')) {
							$mordern_effects= (array) vc_param_group_parse_atts( $mordern_effects);
							
							foreach($mordern_effects as $item) {
							
							$magic_class = $magic_attr = $parallax_scroll = '';
							if (!empty($item['magic_scroll']) && !empty($item["scroll_type"]) && $item['magic_scroll'] == 'on') {
								$magic_attr .= ' data-scroll_type="' . esc_attr($item["scroll_type"]) . '" ';
								if(!empty($item["scroll_type"]) && $item["scroll_type"]== 'position' ){
									$magic_attr .= ' data-scroll_x="' . esc_attr($item["distance_scroll_x"]) . '" ';
									$magic_attr .= ' data-scroll_y="' . esc_attr($item["distance_scroll_y"]) . '" ';
									$parallax_scroll .= ' parallax-scroll ';
								}
								if(!empty($item["scroll_type"]) && $item["scroll_type"]== 'scale'){
									$magic_attr .= ' data-scale_scroll="' . esc_attr($item["scale_scroll"]) . '" ';
									$parallax_scroll .= ' scale-scroll ';
								}
								if(!empty($item["scroll_type"]) && $item["scroll_type"]== 'both'){
									$magic_attr .= ' data-scroll_x="' . esc_attr($item["distance_scroll_x"]) . '" ';
									$magic_attr .= ' data-scroll_y="' . esc_attr($item["distance_scroll_y"]) . '" ';
									$magic_attr .= ' data-scale_scroll="' . esc_attr($item["scale_scroll"]) . '" ';
									$parallax_scroll .= ' both-scroll ';
								}
								$magic_class .= ' magic-scroll ';
							}
							
							if(isset($item['single_image_effects']) && $item['single_image_effects'] != '') {
							 $img = wp_get_attachment_image_src($item['single_image_effects'], "full");
							 $imgSrc = $img[0];
										if($imgSrc!=''){
										
										if(!empty($item['effects_xposition'])){
$xpos=$item['effects_xposition'];
}else{
$xpos='20';
}
										if(!empty($item['effects_yposition'])){
$ypos=$item['effects_yposition'];
}else{
$ypos='20';
}
$effect='';										if(!empty($item['mordern_effect'])){
$effect=$item['mordern_effect'];
}
										$output .='<div class="pt_plus_mordern_image_effects ' . esc_attr($magic_class) . '" style="top:'.esc_attr($ypos).'%;left:'.esc_attr($xpos).'%;">';
										$output .='<div class="' . esc_attr($parallax_scroll) . '" ' . $magic_attr . '>';
											$output.='<img src="'.esc_url($imgSrc).'" alt="" class="mordern-image-effect morder_image_style  '.esc_attr($effect).'"  data-tablet-width="'.esc_attr($item["effect_image_tablet_width"]).'"  data-mobile-width="'.esc_attr($item["effect_image_mobile_width"]).'">';
										$output.='</div>';
										$output.='</div>';
									}
									}	
							}
							
						}
					}
					/*-------- parellax Image row-------*/
					/*-----overlay normal color--------------*/
					if($overlay_style=='normal_color' && $normal_overlay_color!=''){
					$output .='<div class="pt-plus-row-overlay" style="background:'.esc_attr($normal_overlay_color).'"></div>';
					}
					/*----------------overlay normal color---*/
					/*----overlay gradient color---*/
					if(isset($overlay_style) && !empty($overlay_style) && $overlay_style=='gradient_color'){
					if(!empty($overlay_gradient_color1) && !empty($overlay_gradient_color2)){
					$gradient_color='';
					$gradient_color .='opacity: '.esc_attr($overlay_gradient_opacity).';';
					$gradient_color .= pt_plus_gradient_color($overlay_gradient_color1,$overlay_gradient_color2,$overlay_gradient_style);
					$output .='<div class="pt-plus-row-overlay" style="'.$gradient_color.'"></div>';
					}
					}
					/*---overlay gradient color---*/
					/*----texture image-----*/
					if($texture_image!=''){
					$texture_css='';
					$img = wp_get_attachment_image_src($texture_image, "full");
								$imgSrc = $img[0];
					if($imgSrc){
					$texture_css .= 'background: url('.esc_url($imgSrc).') repeat;opacity: '.esc_attr($opacity_texture_image).';';
					$output .='<div class="pt-plus-row-overlay" style="'.$texture_css.'"></div>';
					}
					}
					/*---texture image-----*/
					/* row*/
					$output .='</div>';
					return $output;
		}
		function init_tp_row_settings(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("Row Background", "pt_theplus"),
					"base" => "tp_row_settings",
					"icon" => "tp-row-background",
					"category" => __("The Plus", "pt_theplus"),
					"description" => esc_html__('Most Advance Row Maker', 'pt_theplus'),
					"params" => array(
						array(
							'type' => '',
							'heading' => '',
							'param_name' => 'deeplayer_description',
							"description" => __("This is foundation layer of row with all classic and modern options.", "pt_theplus"),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
					  array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Deep Layer', 'pt_theplus'),
								'param_name'  => 'select_anim',
								'admin_label' => true,
								'simple_mode' => false,
								'value'		=> '',
								'options'     => array(
									'' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Blank-(this-layer-will-be-disappeared.).jpg'
									),
									'bg_normal_color' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Solid-Color.jpg'
									),
									'bg_gradientcolor' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Gradient-Color.jpg'
									),
									'bg_color' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Animated-Background-Color.jpg'
									),
									'bg_image' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Creative-Background-Image.jpg'
									),
									'bg_video' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Creative-Background-Video.jpg'
									),
									'bg_gallery' => array(
										'tooltip' => esc_attr__('Style-7','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Image-Gallery-with-Effects.jpg'
									),
									'bg_Image_pieces' => array(
										'tooltip' => esc_attr__('Style-8','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Image-Segmentation-Effects.jpg'
									),
									'smart_gallery' => array(
										'tooltip' => esc_attr__('Style-9','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/smart-gallery.jpg'
									),
								),
								 'group' => esc_attr__('Deep Layer', 'pt_theplus'),
								
							),
						/*--------background gradient-------*/
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select a background color from unlimited options.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')), 
							'param_name' => 'normal_bg_color',
							"description" => "",
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_normal_color'
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('First Color', 'pt_theplus'),
							'param_name' => 'gradient_color1',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gradientcolor'
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Second Color', 'pt_theplus'),
							'param_name' => 'gradient_color2',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gradientcolor'
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose One gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
							'param_name' => 'gradient_style',
							'edit_field_class' => 'vc_col-xs-6',
							'value' => array(
								__('Horizontal', 'pt_theplus') => 'horizontal',
								__('Vertical', 'pt_theplus') => 'vertical',
								__('Diagonal', 'pt_theplus') => 'diagonal',
								__('Radial', 'pt_theplus') => 'radial'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gradientcolor'
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select opacity of Gradient color layer.','pt_theplus').'</span></span>'.esc_html__('Opacity', 'pt_theplus')),
							'edit_field_class' => 'vc_col-xs-6',
							'param_name' => 'overlay_color_opacity',
							'value' => array(
								__('1', 'pt_theplus') => '1',
								__('0.1', 'pt_theplus') => '0.1',
								__('0.2', 'pt_theplus') => '0.2',
								__('0.3', 'pt_theplus') => '0.3',
								__('0.4', 'pt_theplus') => '0.4',
								__('0.5', 'pt_theplus') => '0.5',
								__('0.6', 'pt_theplus') => '0.6',
								__('0.7', 'pt_theplus') => '0.7',
								__('0.8', 'pt_theplus') => '0.8',
								__('0.9', 'pt_theplus') => '0.9'
							),
							"description" => "",
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gradientcolor'
							)
						),
						/*--------background gradient-------*/
						/*--------background image-------*/
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
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
							),
						array(
							'type' => 'attach_image',
							'class' => '',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Directly upload or select image from media library.','pt_theplus').'</span></span>'.esc_html__('Upload Background Image', 'pt_theplus')), 
							'param_name' => 'column_bg_image_new',
							'value' => '',
							'description' => '',
							'dependency' => array(
								'element' => 'image_source',
								'value' => array( 'media_library')
							),
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
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
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Background Image effect from given options.','pt_theplus').'</span></span>'.esc_html__('Background Effect', 'pt_theplus')), 
							'param_name' => 'columns_parallax_style',
							'value' => array(
								__('Normal Background Image', 'pt_theplus') => 'columns_simple_image',
								__('Auto Moving Background Image', 'pt_theplus') => 'columns_animated_bg'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Parallax style of background image. Which will make background image sensitive over mouse hover.','pt_theplus').'</span></span>'.esc_html__('Mouse Hover Parallax Style', 'pt_theplus')), 
							'param_name' => 'image_parallax_style',
							'value' => array(
								__('Select Style', 'pt_theplus') => '',
								__('Style 1', 'pt_theplus') => 'style_1',
								__('Style 2', 'pt_theplus') => 'style_2'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
								'element' => 'columns_parallax_style',
								'value' => 'columns_simple_image'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can direction of auto moving background image.','pt_theplus').'</span></span>'.esc_html__('Direction', 'pt_theplus')), 
							'param_name' => 'columns_animation_direction',
							'value' => array(
								__('None', 'pt_theplus') => '',
								__('Left to Right', 'pt_theplus') => 'right',
								__('Right to Left', 'pt_theplus') => 'left',
								__('Top to Bottom', 'pt_theplus') => 'top',
								__('Bottom to Top', 'pt_theplus') => 'bottom'
								
							),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
								'element' => 'columns_parallax_style',
								'value' => 'columns_animated_bg'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Speed of background image transition . Use values like 30,50, etc.','pt_theplus').'</span></span>'.esc_html__('Transition Speed', 'pt_theplus')), 
							'param_name' => 'columns_parallax_sense',
							'value' => '30',
							'description' => '',
							'dependency' => array(
								'element' => 'columns_parallax_style',
								'value' => 'columns_animated_bg'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Options to control size of the background image.','pt_theplus').'</span></span>'.esc_html__('Background Size', 'pt_theplus')), 
							'param_name' => 'column_bg_image_size',
							'value' => array(
								__('Cover', 'pt_theplus') => 'cover',
								__('Contain', 'pt_theplus') => 'contain',
								__('Initial', 'pt_theplus') => 'initial'
							),
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Options to control position of the background image.','pt_theplus').'</span></span>'.esc_html__('Background Position', 'pt_theplus')),
							'param_name' => 'column_bg_image_position',
							'value' => array(
								__('Left Top', 'pt_theplus') => 'left top',
								__('Left center', 'pt_theplus') => 'left center',
								__('Left Bottom', 'pt_theplus') => 'left bottom',
								__('Center Top', 'pt_theplus') => 'center top',
								__('Center Center', 'pt_theplus') => 'center center',
								__('Center Bottom', 'pt_theplus') => 'center bottom',
								__('Right Top', 'pt_theplus') => 'right top',
								__('Right center', 'pt_theplus') => 'right center',
								__('Right Bottom', 'pt_theplus') => 'right bottom'
							),
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Options to control position of the background image Position.','pt_theplus').'</span></span>'.esc_html__('Background Image Position', 'pt_theplus')),
							'param_name' => 'column_bg_img_attach',
							'value' => array(
								__('Normal', 'pt_theplus') => 'scroll',
								__('Fixed', 'pt_theplus') => 'fixed'
							),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('scrolling parallax effect image turning on this option.','pt_theplus').'</span></span>'.esc_html__('Image Scroll Parallax', 'pt_theplus')), 
							'param_name' => 'bg_img_parallax',
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
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							"group" => esc_attr__('Deep Layer', 'pt_theplus'),
						),
						/*--------background image-------*/
						/*--------Animated Gradient Color-------*/
						array(
							'type' => 'param_group',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add as many as you want colors from this box.','pt_theplus').'</span></span>'.esc_html__('Multiple Colors', 'pt_theplus')),
							'param_name' => 'columns_bg_colors',
							'description' => '',
							'params' => array(
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for Multi using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
									'heading' => __('Color', 'pt_theplus'),
									'param_name' => 'column_bg_single_color',
									
									'value' => '#d3d3d3'
								)
							),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_color'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Time interval between color change effect. Use values like 3000,5000, etc. That Number will be in milliseconds.','pt_theplus').'</span></span>'.esc_html__('Animation Duration', 'pt_theplus')),
							'param_name' => 'column_anim_bg_duration',
							'value' => '3000',
							'description' => '',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_color'
							)
						),
						/*--------Animated Gradient Color-------*/
						/*--------background video -------*/
					   array(
							'type' => 'dropdown',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('IE, Chrome & Safari support MP4 format, while Firefox & Opera prefer WebM / Ogg formats. You can upload the video through Wordpress Media Library and paste link of it here.','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://www.w3schools.com/html/html5_video.asp">'.esc_html__('Check Support link','pt_theplus').'</a></span></span>'.esc_html__('Video Source ', 'pt_theplus')),
							'param_name' => 'columns_video_variant',
							'value' => array(
								__('Self Hosted', 'pt_theplus') => 'self-hosted',
								__('YouTube', 'pt_theplus') => 'youtube',
								__('Vimeo', 'pt_theplus') => 'vimeo'
							),
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_video'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('IE, Chrome & Safari support MP4 format, while Firefox & Opera prefer WebM / Ogg formats. You can upload the video through WordPress Media Library','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://www.w3schools.com/html/html5_video.asp">'.esc_html__('Check Support link','pt_theplus').'</a></span></span>'.esc_html__('URL of Video (MP4) ', 'pt_theplus')),
							'param_name' => 'columns_video_url_mp4',
							'value' => '',
							'description' => '',
							'dependency' => Array(
								'element' => 'columns_video_variant',
								'value' => array(
									'self-hosted'
								)
							),
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('IE, Chrome & Safari support MP4 format, while Firefox & Opera prefer WebM / Ogg formats. You can upload the video through WordPress Media Library','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://www.w3schools.com/html/html5_video.asp">'.esc_html__('Check Support link','pt_theplus').'</a></span></span>'.esc_html__('URL of Video (WebM/Ogg) ', 'pt_theplus')),
							'param_name' => 'columns_video_url_webm',
							'value' => '',
							'description' => '',
							'dependency' => Array(
								'element' => 'columns_video_variant',
								'value' => array(
									'self-hosted'
								)
							),
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter YouTube ID from Youtube URL. Example: tSqJIIcxKZM','pt_theplus').'</span></span>'.esc_html__('Enter YouTube video ID', 'pt_theplus')),
							'param_name' => 'columns_youtube_video_id',
							'value' => '',
							'description' => '',
							'dependency' => Array(
								'element' => 'columns_video_variant',
								'value' => array(
									'youtube'
								)
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Vimeo ID from Vimeo URL. Example: 67628182','pt_theplus').'</span></span>'.esc_html__('Enter Vimeo video ID', 'pt_theplus')),
							'param_name' => 'columns_vimeo_video_id',
							'value' => '',
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => Array(
								'element' => 'columns_video_variant',
								'value' => array(
									'vimeo'
								)
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Video optoin using this option.','pt_theplus').'</span></span>'.esc_html__('Extra Options', 'pt_theplus')),
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Extra Options', 'pt_theplus'),
							'param_name' => 'columns_video_opts',
							'value' => array(
								__('Loop', 'pt_theplus') => 'loop',
								__('Muted', 'pt_theplus') => 'muted'
							),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_video'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'attach_image',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('We highly Recommend to use Placeholder image Because Some older versions of browser and Some Mobile OS doesn&#39;t support video backgrounds.','pt_theplus').'</span></span>'.esc_html__('Place Holder Image', 'pt_theplus')),
							'param_name' => 'columns_video_poster',
							'value' => '',
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_video'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						/*--------background video -------*/
						/*--------background gallery -------*/
						array(
							'type' => 'attach_images',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('For Background Image Gallery, Upload Multiple images by drag and drop or using wordpress media library.','pt_theplus').'</span></span>'.esc_html__('Upload Multiple Images', 'pt_theplus')),
							'param_name' => 'images_gallery',
							'value' => '',
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gallery'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select from Most innovative transition effects.','pt_theplus').'</span></span>'.esc_html__('Transition Effects', 'pt_theplus')),
							'param_name' => 'gallery_slide_style',
							'edit_field_class' => 'vc_col-xs-6',
							'value' => array(
								__('Fade', 'pt_theplus') => 'fade2',
								__('Blur', 'pt_theplus') => 'blur2',
								__('Flash', 'pt_theplus') => 'flash2',
								__('Negative', 'pt_theplus') => 'negative2',
								__('Burn', 'pt_theplus') => 'burn2',
								__('SlideLeft', 'pt_theplus') => 'slideLeft2',
								__('SlideRight', 'pt_theplus') => 'slideRight2',
								__('SlideUp', 'pt_theplus') => 'slideUp2',
								__('SlideDown', 'pt_theplus') => 'slideDown2',
								__('ZoomIn', 'pt_theplus') => 'zoomIn2',
								__('ZoomOut', 'pt_theplus') => 'zoomOut2',
								__('SwirlLeft', 'pt_theplus') => 'SwirlLeft2',
								__('SwirlRight', 'pt_theplus') => 'swirlRight2'
							),
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gallery'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Some Textures to choose from for overlay of image gallery.','pt_theplus').'</span></span>'.esc_html__('Texture Overlay', 'pt_theplus')),
							'param_name' => 'gallery_overlays',
							'value' => array(
								__('Off', 'pt_theplus') => 'false',
								__('Style 1', 'pt_theplus') => '1',
								__('Style 2', 'pt_theplus') => '2',
								__('Style 3', 'pt_theplus') => '3',
								__('Style 4', 'pt_theplus') => '4',
								__('Style 5', 'pt_theplus') => '5',
								__('Style 6', 'pt_theplus') => '6',
								__('Style 7', 'pt_theplus') => '7',
								__('Style 8', 'pt_theplus') => '8',
								__('Style 9', 'pt_theplus') => '9'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gallery'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter the value in millisecond for transition duration of each slide. E.g. 5000,7000 etc.','pt_theplus').'</span></span>'.esc_html__('Transition Duration', 'pt_theplus')),
							'param_name' => 'gallery_animation_duration',
							'value' => '5000',
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gallery'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter the value in millisecond for Slide delay time of each slide. E.g. 15000,10000 etc.','pt_theplus').'</span></span>'.esc_html__('Slide Delay Time', 'pt_theplus')),
							'param_name' => 'slide_delay_time',
							'value' => '7000',
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gallery'
							),
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus')
						),
						/*--------background gallery -------*/
						/*--------background image pieces---*/
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select background styles using this option.','pt_theplus').'</span></span>'.esc_html__('Background Section Options', 'pt_theplus')),
							'param_name' => 'bg_options',
							'edit_field_class' => 'vc_col-xs-6',
							'value' => array(
								__('Image', 'pt_theplus') => 'bg_image',
								__('Solid Color', 'pt_theplus') => 'bg_color',
								__('Gradient Color', 'pt_theplus') => 'bg_gradient'
							),
							'std' => 'bg_image',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_Image_pieces'
							)
						),
						
						array(
							'type' => 'attach_image',
							'heading' => __('Upload Image', 'pt_theplus'),
							'param_name' => 'bg_image',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'bg_options',
								'value' => 'bg_image'
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Select Color', 'pt_theplus'),
							'param_name' => 'bg_pieces_color',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'bg_options',
								'value' => 'bg_color'
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('First Color', 'pt_theplus'),
							'param_name' => 'bg_gradient1',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'bg_options',
								'value' => 'bg_gradient'
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Second Color', 'pt_theplus'),
							'param_name' => 'bg_gradient2',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'bg_options',
								'value' => 'bg_gradient'
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select One gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
							'heading' => __('Gradient Style', 'pt_theplus'),
							'param_name' => 'bg_gradient_style',
							'value' => array(
								__('Horizontal', 'pt_theplus') => 'horizontal',
								__('Vertical', 'pt_theplus') => 'vertical',
								__('Diagonal', 'pt_theplus') => 'diagonal',
								__('Radial', 'pt_theplus') => 'radial'
							),
							"description" => "",
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'bg_options',
								'value' => 'bg_gradient'
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select pre-generated styles or Use our custom option to create yours unique designs.','pt_theplus').'</span></span>'.esc_html__('Styles of Image Segmentation', 'pt_theplus')),
							'param_name' => 'style_of_pieces',
							'value' => array(
								__('Style-1', 'pt_theplus') => 'style-1',
								__('Style-2', 'pt_theplus') => 'style-2',
								__('Style-3', 'pt_theplus') => 'style-3',
								__('Style-4', 'pt_theplus') => 'style-4',
								__('Style-5', 'pt_theplus') => 'style-5',
								__('Style-6', 'pt_theplus') => 'style-6',
								__('Custom', 'pt_theplus') => 'custom'
							),
							'std' => '4',
							"description" => "",
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_Image_pieces'
							)
						),
						
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Each segment by using this options.(Note: Please add one by one segment and choose options of it.)','pt_theplus').'</span></span>'.esc_html__('Number of Segmentations', 'pt_theplus')),
							'heading' => __('Number of Segmentations', 'pt_theplus'),
							'param_name' => 'no_of_pieces',
							'value' => array(
								__('1', 'pt_theplus') => '1',
								__('2', 'pt_theplus') => '2',
								__('3', 'pt_theplus') => '3',
								__('4', 'pt_theplus') => '4',
								__('5', 'pt_theplus') => '5',
								__('6', 'pt_theplus') => '6',
								__('7', 'pt_theplus') => '7',
								__('8', 'pt_theplus') => '8',
								__('9', 'pt_theplus') => '9'
							),
							'std' => '4',
							"description" => "",
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'param_group',
							
							'heading' => esc_html__('No of Pieces On Position', 'pt_theplus'),
							'param_name' => 'image_clip_opt',
							'params' => array(
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' => __('Left', 'pt_theplus'),
									"description" => __(" ", "pt_theplus"),
									'param_name' => 'pos_xposition',
									'admin_label' => true,
									'value' => '45'
									
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' => __('Top', 'pt_theplus'),
									"description" => __(" ", "pt_theplus"),
									'param_name' => 'pos_yposition',
									'admin_label' => true,
									'value' => '20'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' => __('Width', 'pt_theplus'),
									"description" => __("E.g. 50,70 etc..", "pt_theplus"),
									'param_name' => 'pos_width',
									'admin_label' => true,
									'value' => '50'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' => __('height', 'pt_theplus'),
									"description" => __("E.g. 50,70 etc..", "pt_theplus"),
									'param_name' => 'pos_height',
									'admin_label' => true,
									'value' => '70'
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select segment animation by using this options.','pt_theplus').'</span></span>'.esc_html__('Segment Animation', 'pt_theplus')),
									'param_name' => 'animate_pieces',
									'value' => array(
										__('Select Animate', 'pt_theplus') => '',
										__('Scale', 'pt_theplus') => 'scale',
										__('Float', 'pt_theplus') => 'float',
										__('Rotate', 'pt_theplus') => 'rotate',
										__('Flash', 'pt_theplus') => 'flash'
									),
									"description" => ""
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select animation speed by using this options.','pt_theplus').'</span></span>'.esc_html__('Animation Speed', 'pt_theplus')),
									'param_name' => 'animate_speed',
									'value' => array(
										__('Select Animate speed', 'pt_theplus') => '',
										__('1', 'pt_theplus') => '1s',
										__('2', 'pt_theplus') => '2s',
										__('3', 'pt_theplus') => '3s',
										__('4', 'pt_theplus') => '4s',
										__('5', 'pt_theplus') => '5s',
										__('6', 'pt_theplus') => '6s',
										__('7', 'pt_theplus') => '7s',
										__('8', 'pt_theplus') => '8s',
										__('9', 'pt_theplus') => '9s',
										__('10', 'pt_theplus') => '10s'
									),
									"description" => ""
								)
								
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
							
						),
						array(
							'type' => 'dropdown',
							'heading' => __('Mouse Hover Parallax Effect', 'pt_theplus'),
							'param_name' => 'parallax_effect',
							'value' => array(
								__('False', 'pt_theplus') => 'false',
								__('True', 'pt_theplus') => 'true'
							),
							'std' => 'false',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Minimum Parallax Value', 'pt_theplus'),
							"description" => __(" ", "pt_theplus"),
							'param_name' => 'min_parallax',
							'value' => '10',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Maximum Parallax Value', 'pt_theplus'),
							"description" => __(" ", "pt_theplus"),
							'param_name' => 'max_parallax',
							'value' => '40',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'dropdown',
							'heading' => __('Segments Opacity', 'pt_theplus'),
							'param_name' => 'opacity_pieces',
							'value' => array(
								__('1', 'pt_theplus') => '1',
								__('0.1', 'pt_theplus') => '0.1',
								__('0.2', 'pt_theplus') => '0.2',
								__('0.3', 'pt_theplus') => '0.3',
								__('0.4', 'pt_theplus') => '0.4',
								__('0.5', 'pt_theplus') => '0.5',
								__('0.6', 'pt_theplus') => '0.6',
								__('0.7', 'pt_theplus') => '0.7',
								__('0.8', 'pt_theplus') => '0.8',
								__('0.9', 'pt_theplus') => '0.9'
							),
							"description" => __("Please select opacity of segments.", "pt_theplus"),
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Top Shadow', 'pt_theplus'),
							"description" => __(" ", "pt_theplus"),
							'param_name' => 'top_shadow',
							'value' => '10',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-6',
							'heading' => __('Left Shadow', 'pt_theplus'),
							"description" => __(" ", "pt_theplus"),
							'param_name' => 'left_shadow',
							'value' => '10',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'heading' => __('Border Width', 'pt_theplus'),
							"description" => __("E.g. 7px,2px.. etc..", "pt_theplus"),
							'edit_field_class' => 'vc_col-xs-4',
							'param_name' => 'border_width',
							'value' => '0px',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'dropdown',
							'heading' => __('Border Style', 'pt_theplus'),
							'edit_field_class' => 'vc_col-xs-4',
							'param_name' => 'border_style',
							'value' => array(
								__('Solid', 'pt_theplus') => 'solid',
								__('Dotted', 'pt_theplus') => 'dotted',
								__('Double', 'pt_theplus') => 'double',
								__('Dashed', 'pt_theplus') => 'dashed'
							),
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Border Color', 'pt_theplus'),
							'edit_field_class' => 'vc_col-xs-4',
							'param_name' => 'border_color',
							"value" => 'rgba(255,255,255,0.7)',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'heading' => __('Animation Time Duration', 'pt_theplus'),
							"description" => __("E.g. 1200,2000 etc..", "pt_theplus"),
							'param_name' => 'anim_duration',
							'value' => '1500',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'dropdown',
							'heading' => __('Transition Effect', 'pt_theplus'),
							'param_name' => 'easing_transition',
							'value' => array(
								__('linear', 'pt_theplus') => 'linear',
								__('swing', 'pt_theplus') => 'swing',
								__('easeInQuad', 'pt_theplus') => 'easeInQuad',
								__('easeOutQuad', 'pt_theplus') => 'easeOutQuad',
								__('easeInOutQuad', 'pt_theplus') => 'easeInOutQuad',
								__('easeInCubic', 'pt_theplus') => 'easeInCubic',
								__('easeOutCubic', 'pt_theplus') => 'easeOutCubic',
								__('easeInOutCubic', 'pt_theplus') => 'easeInOutCubic',
								__('easeInQuart', 'pt_theplus') => 'easeInQuart',
								__('easeOutQuart', 'pt_theplus') => 'easeOutQuart',
								__('easeInOutQuart', 'pt_theplus') => 'easeInOutQuart',
								__('easeInQuint', 'pt_theplus') => 'easeInQuint',
								__('easeOutQuint', 'pt_theplus') => 'easeOutQuint',
								__('easeInOutQuint', 'pt_theplus') => 'easeInOutQuint',
								__('easeInExpo', 'pt_theplus') => 'easeInExpo',
								__('easeOutExpo', 'pt_theplus') => 'easeOutExpo',
								__('easeInOutExpo', 'pt_theplus') => 'easeInOutExpo',
								__('easeInSine', 'pt_theplus') => 'easeInSine',
								__('easeOutSine', 'pt_theplus') => 'easeOutSine',
								__('easeInOutSine', 'pt_theplus') => 'easeInOutSine',
								__('easeInCirc', 'pt_theplus') => 'easeInCirc',
								__('easeOutCirc', 'pt_theplus') => 'easeOutCirc',
								__('easeInOutCirc', 'pt_theplus') => 'easeInOutCirc',
								__('easeOutElastic', 'pt_theplus') => 'easeOutElastic',
								__('easeOutBack', 'pt_theplus') => 'easeOutBack',
								__('easeOutBounce', 'pt_theplus') => 'easeOutBounce'
							),
							'std' => 'easeOutExpo',
							"description" => __("Select Animated Transistion effect.", "pt_theplus"),
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'heading' => __('Transition Delay', 'pt_theplus'),
							"description" => __("E.g. 50,100 etc..", "pt_theplus"),
							'param_name' => 'delay_transition',
							'value' => '100',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-3',
							'heading' => __('Minimum TranslateZ', 'pt_theplus'),
							"description" => __(" ", "pt_theplus"),
							'param_name' => 'translatez_min',
							'value' => '10',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'edit_field_class' => 'vc_col-xs-3',
							'heading' => __('Maximum TranslateZ', 'pt_theplus'),
							"description" => __("E.g. 100,120 etc..", "pt_theplus"),
							'param_name' => 'translatez_max',
							'value' => '100',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'style_of_pieces',
								'value' => 'custom'
							)
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #cccccc,none ','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Segments Box Shadow', 'pt_theplus')),
							'heading' => __('Segments Box Shadow', 'pt_theplus'),
							"description" => __("E.g. 0px 2px 15px 0px rgba(0,0,0,0.7) etc..", "pt_theplus"),
							'param_name' => 'box_shadow_pieces',
							'value' => '0px 2px 15px 0px rgba(0,0,0,0.7)',
							'group' => esc_attr__('Deep Layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_Image_pieces'
							)
						),
						/*--------background image pieces---*/
						/*--------smart gallery background image---*/
						array(
							'type' => 'param_group',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add multiple gallery image sections using this option.','pt_theplus').'</span></span>'.esc_html__('Add Multiple Gallery Image Sections', 'pt_theplus')),
							'param_name' => 'group_gallery_images',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'smart_gallery'
							),
							"group" => esc_attr__('Deep Layer', 'pt_theplus'),
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
								'dependency' => array(
									'element' => 'select_anim',
									'value' => 'smart_gallery'
								),
								"group" => esc_attr__('Deep Layer', 'pt_theplus'),
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
							'dependency' => array(
									'element' => 'select_anim',
									'value' => 'smart_gallery'
							),
							"group" => esc_attr__('Deep Layer', 'pt_theplus'),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Value of Column Space here in Pixels. e.g. 10px, 20px etc.','pt_theplus').'</span></span>'.esc_html__('Column Space', 'pt_theplus')), 
							'param_name' => 'column_space_pading',
							'description' => '',
							'value' => '10px',
							"edit_field_class" => "vc_col-xs-3",
							"dependency" => array(
								"element" => "column_space",
								"value" => array("on"),
							),
							"group" => esc_attr__('Deep Layer', 'pt_theplus'),
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
							'dependency' => array(
									'element' => 'select_anim',
									'value' => 'smart_gallery'
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
								)
							),
							"group" => esc_attr__('Deep Layer', 'pt_theplus'),
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
							'dependency' => array(
									'element' => 'select_anim',
									'value' => 'smart_gallery'
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
								)
							),
							"group" => esc_attr__('Deep Layer', 'pt_theplus')
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
							'dependency' => array(
									'element' => 'select_anim',
									'value' => 'smart_gallery'
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
								)
							),
							
							"group" => esc_attr__('Deep Layer', 'pt_theplus')
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
							'dependency' => array(
									'element' => 'select_anim',
									'value' => 'smart_gallery'
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"metro",
								)
							),
							"group" => esc_attr__('Deep Layer', 'pt_theplus')
						),
						/*--------smart gallery background image---*/			
						
						/*---------Middle Layer-------------*/
						
						array(
							'type' => '',
							'heading' => '',
							'param_name' => 'middlelayer_description',
							"description" => __("This is the middle layer of our row section with lots of creative and unique options.", "pt_theplus"),
							'group' => esc_attr__('Middle layer', 'pt_theplus')
						),
						array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Middle Layer', 'pt_theplus'),
								'param_name'  => 'middle_style',
								'admin_label' => true,
								'simple_mode' => false,
								'value'		=> '',
								'options'     => array(
									'' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Blank-(this-layer-will-be-disappeared.).jpg'
									),
									'canvas' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Canvas-Effects.jpg'
									),
									'mordern_parallax' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Modern-Mouse-Hover-Parallax.jpg'
									),
									'moving_image' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Auto-Moving-Layer.jpg'
									),
									'mordern_image_effect' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Modern-Image-Effect.jpg'
									),
									'animated_svg' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Animated-SVG.jpg'
									),
								),
								 'group' => esc_attr__('Middle layer', 'pt_theplus'),
								
							),
						/*------------animated svg-----*/
						array(
							'type' => 'param_group',
							'heading' => esc_html__('Animated Svg', 'pt_theplus'),
							'param_name' => 'aniamted_svg_option',
							"description" => __("Select Multiple Svg Animated for this style from above:", "pt_theplus"),
							'params' => array(
								array(
									'type' => 'attach_image',
									"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Upload Custom SVG', 'pt_theplus')),
									'param_name' => 'svg_image',
									'value' => '',
									'description' => __('Select Only .svg File from media library.', 'pt_theplus'),
									'admin_label' => true
								),
								array(
									"type" => "dropdown",
									"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose from different options of SVG draw animation. Test that here','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('SVG Animation Type', 'pt_theplus')),
									"param_name" => "type",
									"value" => array(
										__("Delayed", "pt_theplus") => "delayed",
										__("Sync", "pt_theplus") => "sync",
										__("One-By-One", "pt_theplus") => "oneByOne",
										__("Scenario-Sync", "pt_theplus") => "scenario-sync"
									),
									'edit_field_class' => 'vc_col-xs-6',
									"description" => "",
									"std" => 'delayed',
									'admin_label' => true
								),
								array(
									"type" => "textfield",
									"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Set SVG draw Animation Duration using this option. Test that here','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Animation Duration', 'pt_theplus')),
									"param_name" => "duration",
									"value" => '30',
									'edit_field_class' => 'vc_col-xs-6',
									'admin_label' => true
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-6',
									 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Horizontal  Position. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('(X) / Horizontal  Position', 'pt_theplus')),
									"description" => __(" ", "pt_theplus"),
									'param_name' => 'svg_xposition',
									'admin_label' => true,
									'value' => '45'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-6',
									 'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Vertical Position. You can use positive and negative value here. e.g. 10, -10 etc.','pt_theplus').'</span></span>'.esc_html__('(Y) / Vertical Position', 'pt_theplus')),
									"description" => __(" ", "pt_theplus"),
									'param_name' => 'svg_yposition',
									'admin_label' => true,
									'value' => '20'
								),
								array(
									"type" => "textfield",
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can setup Maximum Width of SVG here in Percentage or in Pixels from this option.','pt_theplus').'</span></span>'.esc_html__('Maximum Width', 'pt_theplus')),
									"param_name" => "max_width",
									'edit_field_class' => 'vc_col-xs-6',
									"value" => '100%',
									"description" => "",
									'admin_label' => true
								),
								array(
									'type' => 'colorpicker',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose SVG&#39;s Stroke in Normal Terms Border Color Using This Option.','pt_theplus').'</span></span>'.esc_html__('Stroke(Border) Color', 'pt_theplus')),
									'edit_field_class' => 'vc_col-xs-6',
									'param_name' => 'border_stroke_color',
									"value" => '#ff0000'
								),
								
							),
							'dependency' => array(
								'element' => 'middle_style',
								'value' => array(
									'animated_svg'
								)
							),
							'group' => esc_attr__('Middle layer', 'pt_theplus')
						),
						/*------------animated svg-----*/
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select canvas effects using this option.','pt_theplus').'</span></span>'.esc_html__('Canvas Effect', 'pt_theplus')),
							'param_name' => 'canvas_style',
							'value' => array(
								__('Style 1', 'pt_theplus') => 'style_1',
								__('Style 2', 'pt_theplus') => 'style_2',
								__('Style 3', 'pt_theplus') => 'style_5',
								__('Style 4', 'pt_theplus') => 'style_6',
								__('Style 5', 'pt_theplus') => 'style_3',
								__('Style 6', 'pt_theplus') => 'style_4',
								__('Style 7', 'pt_theplus') => 'style_7'
							),
							'group' => esc_attr__('Middle layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'middle_style',
								'value' => array(
									'canvas'
								)
							)
						),
						
						array(
							'type' => 'param_group',
							'heading' => esc_html__('Add Color', 'pt_theplus'),
							'param_name' => 'canvas_multi_color',
							"description" => "",
							'params' => array(
								array(
									'type' => 'colorpicker',
									'param_name' => 'canvas_single_color',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for canvas using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
									'admin_label' => true,
									'value' => '#d3d3d3'
								)
							),
							'dependency' => array(
								'element' => 'canvas_style',
								'value' => array(
									'style_1'
								)
							),
							'group' => esc_attr__('Middle layer', 'pt_theplus')
						),
						
						
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for canvas using this option.','pt_theplus').'</span></span>'.esc_html__('Canvas Color', 'pt_theplus')),
							'param_name' => 'canvas_style6_color',
							'value' => '#685e52',
							'group' => esc_attr__('Middle layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'canvas_style',
								'value' => array(
									'style_2',
									'style_3',
									'style_4',
									'style_5',
									'style_6',
									'style_7'
								)
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select shape of canvas style using these options.','pt_theplus').'</span></span>'.esc_html__('Shape in Canvas', 'pt_theplus')),
							'param_name' => 'canvas_type',
							'value' => array(
								__('Circle', 'pt_theplus') => 'circle',
								__('Edge', 'pt_theplus') => 'edge',
								__('Triangle', 'pt_theplus') => 'triangle',
								__('Polygon', 'pt_theplus') => 'polygon',
								__('Star', 'pt_theplus') => 'star'
							),
							'group' => esc_attr__('Middle layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'canvas_style',
								'value' => array(
									'style_3',
									'style_4',
									'style_7'
								)
							)
						),
						array(
							'type' => 'param_group',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can upload Multiple Image and Configure Simple Options.','pt_theplus').'</span></span>'.esc_html__('Add Modern Mouse Hover Parallax Layer', 'pt_theplus')),
							'param_name' => 'mordern_images',
							"description" => "",
							'params' => array(
								array(
									'type' => 'attach_image',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' => __('Upload Image', 'pt_theplus'),
									'param_name' => 'single_layer_image',
									'admin_label' => true
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Horizontal Distance. You can use positive and negative value here. e.g. 10, -10 etc. ','pt_theplus').'</span></span>'.esc_html__('(X) / Horizontal Distance', 'pt_theplus')),
									"description" => __(" ", "pt_theplus"),
									'param_name' => 'image_xposition',
									'admin_label' => true,
									'value' => '45'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Enter Value of Vertical  Distance. You can use positive and negative value here. e.g. 10, -10 etc. ','pt_theplus').'</span></span>'.esc_html__('(Y) / Vertical  Distance', 'pt_theplus')),
									"description" => __(" ", "pt_theplus"),
									'param_name' => 'image_yposition',
									'admin_label' => true,
									'value' => '20'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' => __('Effect Intensity', 'pt_theplus'),
									"description" => "",
									'param_name' => 'parallax_value',
									'admin_label' => true,
									'value' => '30'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' => __('Image Width in Tablet Screens', 'pt_theplus'),
									"description" => "",
									'param_name' => 'image_parallax_tablet_width',
									'admin_label' => true,
									'value' => '120'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' => __('Image Width in Mobile Screens', 'pt_theplus'),
									"description" => "",
									'param_name' => 'image_parallax_mobile_width',
									'admin_label' => true,
									'value' => '100'
								),
							  array(
							'type' => 'pt_theplus_checkbox',            
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can put animation on scroll for your section using this option.','pt_theplus').'</span></span>'.esc_html__('Magic Scroll', 'pt_theplus')),
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
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose options of animation based on position and scale for section.','pt_theplus').'</span></span>'.esc_html__('Scroll Type', 'pt_theplus')),
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
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Value of Horizontal Distance. You can use positive and negative value here. e.g. 10, -10 etc. ','pt_theplus').'</span></span>'.esc_html__('(X) / Horizontal Distance', 'pt_theplus')),
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
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Value of Vertical Distance. You can use positive and negative value here. e.g. 10, -10 etc. ','pt_theplus').'</span></span>'.esc_html__('(Y) / Vertical Distance', 'pt_theplus')),
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
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of scale of section. e.g. 2 = 200%, 1.5 = 150% . ','pt_theplus').'</span></span>'.esc_html__('Scale Value ', 'pt_theplus')),
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
								
							),
							'dependency' => array(
								'element' => 'middle_style',
								'value' => array(
									'mordern_parallax'
								)
							),
							'group' => esc_attr__('Middle layer', 'pt_theplus')
						),
						array(
							'type' => 'param_group',
							'heading' => esc_html__('Add Auto Moving Layer', 'pt_theplus'),
							'param_name' => 'moving_images',
							'params' => array(
								array(
									'type' => 'attach_image',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' => __('Upload Image', 'pt_theplus'),
									'param_name' => 'single_image_move',
									'admin_label' => true
								),
								array(
									'type' => 'dropdown',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Set Direction of Content part from using this option.','pt_theplus').'</span></span>'.esc_html__('Direction', 'pt_theplus')),
									'param_name' => 'move_image_direction',
									'value' => array(
										__('None', 'pt_theplus') => '',
										__('Right', 'pt_theplus') => 'right',
										__('Left', 'pt_theplus') => 'left',
										__('Top', 'pt_theplus') => 'top',
										__('Bottom', 'pt_theplus') => 'bottom'
									),
									'admin_label' => true
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Set transition speed from using this option. E.g. 30,50, etc..','pt_theplus').'</span></span>'.esc_html__('Transition Speed', 'pt_theplus')),
									'heading' => __('Transition Speed', 'pt_theplus'),
									"description" => "",
									'param_name' => 'move_image_speed',
									'admin_label' => true,
									'value' => '30'
								)
								
							),
							'dependency' => array(
								'element' => 'middle_style',
								'value' => array(
									'moving_image'
								)
							),
							'group' => esc_attr__('Middle layer', 'pt_theplus')
						),
						
						array(
							'type' => 'param_group',
							'heading' => esc_html__('Add Layer for Modern Image Effect', 'pt_theplus'),
							'param_name' => 'mordern_effects',
							'params' => array(
								array(
									'type' => 'attach_image',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' => __('Upload Image', 'pt_theplus'),
									'param_name' => 'single_image_effects',
									'admin_label' => true
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Value of Horizontal Distance. You can use positive and negative value here. e.g. 10, -10 etc. ','pt_theplus').'</span></span>'.esc_html__('(X) / Horizontal Distance', 'pt_theplus')),
									"description" => __(" ", "pt_theplus"),
									'param_name' => 'effects_xposition',
									'admin_label' => true,
									'value' => '45'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-3',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Value of Vertical Distance. You can use positive and negative value here. e.g. 10, -10 etc. ','pt_theplus').'</span></span>'.esc_html__('(Y) / Vertical Distance', 'pt_theplus')),
									"description" => __(" ", "pt_theplus"),
									'param_name' => 'effects_yposition',
									'admin_label' => true,
									'value' => '20'
								),
								array(
									'type' => 'dropdown',
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select image effects Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Image Effects', 'pt_theplus')),
									'edit_field_class' => 'vc_col-xs-3',
									'param_name' => 'mordern_effect',
									'value' => array(
										__('none', 'pt_theplus') => 'none',
										__('Pulse', 'pt_theplus') => 'pulse',
										__('Floating', 'pt_theplus') => 'floating',
										__('Tossing', 'pt_theplus') => 'tossing'
									),
									'admin_label' => true
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' => __('Image Width in Tablet Screens ', 'pt_theplus'),
									"description" => __("E.g. 100,200 etc..", "pt_theplus"),
									'param_name' => 'effect_image_tablet_width',
									'admin_label' => true,
									'value' => '120'
								),
								array(
									'type' => 'textfield',
									'edit_field_class' => 'vc_col-xs-4',
									'heading' => __('Image Width in Mobile Screens ', 'pt_theplus'),
									"description" => __("E.g. 100,200 etc..", "pt_theplus"),
									'param_name' => 'effect_image_mobile_width',
									'admin_label' => true,
									'value' => '100'
								),
								array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can put animation on scroll for your section using this option.','pt_theplus').'</span></span>'.esc_html__('Magic Scroll', 'pt_theplus')),
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
									'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose options of animation based on position and scale for section.','pt_theplus').'</span></span>'.esc_html__('Scroll Type', 'pt_theplus')),
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
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Value of Horizontal Distance. You can use positive and negative value here. e.g. 10, -10 etc. ','pt_theplus').'</span></span>'.esc_html__('(X) / Horizontal Distance', 'pt_theplus')),
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
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Value of Vertical Distance. You can use positive and negative value here. e.g. 10, -10 etc. ','pt_theplus').'</span></span>'.esc_html__('(Y) / Vertical Distance', 'pt_theplus')),
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
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter value of scale of section. e.g. 2 = 200%, 1.5 = 150% . ','pt_theplus').'</span></span>'.esc_html__('Scale Value ', 'pt_theplus')),
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
								
							),
							'dependency' => array(
								'element' => 'middle_style',
								'value' => array(
									'mordern_image_effect'
								)
							),
							'group' => esc_attr__('Middle layer', 'pt_theplus')
						),
						/*---------Middle Layer-------------*/
						/*---------background Top layer overlay color-------*/
						array(
							'type' => '',
							'heading' => '',
							'param_name' => 'toplayer_description',
							"description" => __("This is the Top Most Layer of your row background, Which helps to look your content better.", "pt_theplus"),
							'group' => esc_attr__('Top layer', 'pt_theplus')
						),
						array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Top Layer', 'pt_theplus'),
								'param_name'  => 'overlay_style',
								'admin_label' => true,
								'simple_mode' => false,
								'value'		=> '',
								'options'     => array(
									'' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Blank-(this-layer-will-be-disappeared.).jpg'
									),
									'normal_color' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Normal-Color.jpg'
									),
									'gradient_color' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/Gradient-Color.jpg'
									),
									'texture_image' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/row-background/texture-image.jpg'
									),
								),
								 'group' => esc_attr__('Top layer', 'pt_theplus'),
								
							),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
							'param_name' => 'normal_overlay_color',
							"description" => "",
							'group' => esc_attr__('Top layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'normal_color'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('First Color', 'pt_theplus'),
							'param_name' => 'overlay_gradient_color1',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Top layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'gradient_color'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Second Color', 'pt_theplus'),
							'param_name' => 'overlay_gradient_color2',
							'group' => esc_attr__('Top layer', 'pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'gradient_color'
								)
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select one gradient effect style from four beautiful options.','pt_theplus').'</span></span>'.esc_html__('Gradient Style', 'pt_theplus')),
							'param_name' => 'overlay_gradient_style',
							'edit_field_class' => 'vc_col-xs-6',
							'value' => array(
								__('Horizontal', 'pt_theplus') => 'horizontal',
								__('Vertical', 'pt_theplus') => 'vertical',
								__('Diagonal', 'pt_theplus') => 'diagonal',
								__('Radial', 'pt_theplus') => 'radial'
							),
							'group' => esc_attr__('Top layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'gradient_color'
								)
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select opacity of Gradient color layer.','pt_theplus').'</span></span>'.esc_html__('Opacity', 'pt_theplus')),
							'param_name' => 'overlay_gradient_opacity',
							'edit_field_class' => 'vc_col-xs-6',
							'value' => array(
								__('1', 'pt_theplus') => '1',
								__('0.1', 'pt_theplus') => '0.1',
								__('0.2', 'pt_theplus') => '0.2',
								__('0.3', 'pt_theplus') => '0.3',
								__('0.4', 'pt_theplus') => '0.4',
								__('0.5', 'pt_theplus') => '0.5',
								__('0.6', 'pt_theplus') => '0.6',
								__('0.7', 'pt_theplus') => '0.7',
								__('0.8', 'pt_theplus') => '0.8',
								__('0.9', 'pt_theplus') => '0.9'
							),
							"description" => "",
							'group' => esc_attr__('Top layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'gradient_color'
								)
							)
						),
						/*-------texture_image--*/
						array(
							'type' => 'attach_image',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This image will be on a repeat mode. Just upload small texture image. Check some texture options.','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://subtlepatterns.com/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Upload Image', 'pt_theplus')),
							'param_name' => 'texture_image',
							'group' => esc_attr__('Top layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'texture_image'
								)
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select opacity of texture image layer.','pt_theplus').'</span></span>'.esc_html__('Opacity', 'pt_theplus')),
							'param_name' => 'opacity_texture_image',
							'value' => array(
								__('1', 'pt_theplus') => '1',
								__('0.1', 'pt_theplus') => '0.1',
								__('0.2', 'pt_theplus') => '0.2',
								__('0.3', 'pt_theplus') => '0.3',
								__('0.4', 'pt_theplus') => '0.4',
								__('0.5', 'pt_theplus') => '0.5',
								__('0.6', 'pt_theplus') => '0.6',
								__('0.7', 'pt_theplus') => '0.7',
								__('0.8', 'pt_theplus') => '0.8',
								__('0.9', 'pt_theplus') => '0.9'
							),
							"std" => 0.5,
							"description" => "",
							'group' => esc_attr__('Top layer', 'pt_theplus'),
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'texture_image'
								)
							)
						)
						/*-------texture_image--*/
					)
				));
			}
		}
	}
	new ThePlus_row_settings;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_row_settings'))
	{
		class WPBakeryShortCode_tp_row_settings extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}