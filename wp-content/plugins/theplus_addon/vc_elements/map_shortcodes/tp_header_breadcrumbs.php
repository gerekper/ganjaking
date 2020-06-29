<?php 
// Header Breadcrumb Elements
if(!class_exists("ThePlus_header_breadcrumbs")){
	class ThePlus_header_breadcrumbs{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_header_breadcrumbs') );
			add_shortcode( 'tp_header_breadcrumbs',array($this,'tp_header_breadcrumbs_shortcode'));
		}
		function tp_header_breadcrumbs_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'layout' => 'style-1',
					'title_layout' => 'style-1',


					'breadcrumb_page_title'=>'page_title',
					'parallax_on' => "true",
					'page_custom_title'=>'',
'breadcrumb_width'=>'container',
					'bg_height'=>'400px',
					'vertical_position' =>'pos-center',
					'subtitle'=>'',
					'subtitle_color'=>'#ff0000',
					'title_color'=>'#ffffff',
					'breadcrumbs_enable'=>'on',
					'breadcrumbs_styles'=>'style-1',
					'breadcrumbs_bar_position'=>'style-1',

					'breadcrumbs_alignments'=>'text-left',
					'select_anim'=>'',
					'normal_bg_color'=>'#d3d3d3',
					'gradient_color1'=>'',
					'gradient_color2'=>'',
					'gradient_style'=>'horizontal',
					'overlay_color_opacity'=>'1',
					'columns_bg_colors' =>'#5c5c5c',
					'column_anim_bg_duration'=>'3000',
					'column_bg_image_new'=>'',
					'columns_parallax_style'=>'columns_simple_image',
					'columns_animation_direction'=>'',
					'columns_parallax_sense' =>'',
					'column_bg_image_position'=>'',
					'column_bg_image_size' =>'cover',
					'column_bg_img_attach' =>'scroll',
					'columns_video_variant' =>'self-hosted',
					'columns_video_url_mp4' =>'',
					'columns_video_url_webm' =>'',
					'columns_youtube_video_id' =>'',
					'columns_vimeo_video_id' =>'',
					'columns_video_opts' =>'',
					'columns_video_poster' =>'',
					'overlay_style' =>'',
					'normal_overlay_color' =>'',
					'overlay_gradient_color1'=>'',
					'overlay_gradient_color2'=>'',
					'overlay_gradient_style'=>'horizontal',
					'overlay_gradient_opacity'=>'0.5',
					'texture_image'=>'',
					'opacity_texture_image'=>'0.5',
					'title_font_size'=>'35px',
					'title_line_height'=>'40px',
					'title_letter_spacing'=>'1px',
					'title_use_theme_fonts'=>'custom-font-family',
						'title_font_family'=>'',
						'title_font_weight'=>'600',
						'title_google_fonts'=>'',
						
					'subtitle_font_size'=>'23px',
					'subtitle_line_height'=>'25px',
					'subtitle_letter_spacing'=>'1px',
					'subtitle_use_theme_fonts'=>'custom-font-family',
						'subtitle_font_family'=>'',
						'subtitle_font_weight'=>'400',
						'subtitle_google_fonts'=>'',
						
					'breadcrumbs_font_size'=>'20px',
					'breadcrumbs_line_height'=>'22px',
					'breadcrumbs_letter_spacing'=>'1px',
					'breadcrumb_bar_bg_color'=>'#fff',
					'bread_use_theme_fonts'=>'custom-font-family',
						'bread_font_family'=>'',
						'bread_font_weight'=>'400',
						'bread_google_fonts'=>'',

					'image_icon'=>'',
					'select_image'=>'',
					'icon_type'=>'fontawesome',
					'icon_fontawesome'=> 'fa fa-adjust',
					'icon_openiconic'=>'vc-oi vc-oi-dial',
					'icon_typicons'=>'typcn typcn-adjust-brightness',
					'icon_entypo'=>'entypo-icon entypo-icon-note',
					'icon_linecons'=>'vc_li vc_li-heart',
					'icon_monosocial'=>'vc-mono vc-mono-fivehundredpx',
					'icon_color'=>'#0099CB',
					'icon_size'=>'medium',
					'svg_icon'=>'icon',
					'svg_d_icon'=>'app.svg',
					'svg_image'=>'',
					'border_stroke_color' =>'#ff0000',
					'breadcrumb_bar_text_color'=>'#313131',
					'el_class' =>'',
					   ), $atts ) );
					   
					$uniqid = uniqid('Background');
					$uniqid1 = uniqid('pt-plus-bg-image-');
					$keyframe_pref = array('@-webkit-keyframes','@-moz-keyframes','@-ms-keyframes','@-o-keyframes','@keyframes');
					$keframe_css=$class1=$data_atts=$css_rules1='';



					if($parallax_on == 'true'){
						$parralex_class="parallax-on";
					}else{
						$parralex_class="parallax-on-off";
					}
					/*------title breadcrumbs------------*/
					$styleposition=$title_content=$subtitle_content=$icon_content=$title_layout_content=$layout_style=$content='';
					$styleclass='';

					/*title style*/
					$titlestyle='';
					 if($title_font_size!=''){
					$titlestyle .='font-size:'.esc_attr($title_font_size).';';
					}
					if($title_line_height!=''){
					$titlestyle .='line-height:'.esc_attr($title_line_height).';';
					}
					if($title_letter_spacing!=''){
					$titlestyle .='letter-spacing:'.esc_attr($title_letter_spacing).';';
					}
					if($title_use_theme_fonts=='google-fonts'){
						$text_font_data = pt_plus_getFontsData( $title_google_fonts );
						$title_font_family = pt_plus_googleFontsStyles( $text_font_data );  
						$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
					}elseif($title_use_theme_fonts=='custom-font-family'){
						$title_font_family='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
					}else{
						$title_font_family='';
					}
					$titlestyle .=$title_font_family;

					/*subtitle style*/
					$subtitlestyle='';
					 if($subtitle_font_size!=''){
					$subtitlestyle .='font-size:'.esc_attr($subtitle_font_size).';';
					}
					if($subtitle_line_height!=''){
					$subtitlestyle .='line-height:'.esc_attr($subtitle_line_height).';';
					}
					if($subtitle_letter_spacing!=''){
					$subtitlestyle .='letter-spacing:'.esc_attr($subtitle_letter_spacing).';';
					}
					if($subtitle_use_theme_fonts=='google-fonts'){
						$text_font_data = pt_plus_getFontsData( $subtitle_google_fonts );
						$subtitle_font_family = pt_plus_googleFontsStyles( $text_font_data );  
						$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
					}elseif($subtitle_use_theme_fonts=='custom-font-family'){
						$subtitle_font_family='font-family:'.$subtitle_font_family.';font-weight:'.$subtitle_font_weight.';';
					}else{
						$subtitle_font_family='';
					}
					$subtitlestyle .=$subtitle_font_family;

					/*breadcrumb bar style*/
					$breadbstyle='';
					 if($breadcrumb_bar_text_color!=''){
					$breadbstyle .='color:'.esc_attr($breadcrumb_bar_text_color).';';
					} 
					if($breadcrumbs_font_size!=''){
					$breadbstyle .='font-size:'.esc_attr($breadcrumbs_font_size).';';
					}
					if($breadcrumbs_line_height!=''){
					$breadbstyle .='line-height:'.esc_attr($breadcrumbs_line_height).';';
					$breadbstyle .='height:'.esc_attr($breadcrumbs_line_height).';';
					}
					if($breadcrumbs_letter_spacing!=''){
					$breadbstyle .='letter-spacing:'.esc_attr($breadcrumbs_letter_spacing).';';
					}
					if($bread_use_theme_fonts=='google-fonts'){
						$text_font_data = pt_plus_getFontsData( $bread_google_fonts );
						$bread_font_family = pt_plus_googleFontsStyles( $text_font_data );  
						$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
					}elseif($bread_use_theme_fonts=='custom-font-family'){
						$bread_font_family='font-family:'.$bread_font_family.';font-weight:'.$bread_font_weight.';';
					}else{
						$bread_font_family='';
					}
					$breadbstyle .=$bread_font_family;

							/* title */
							$title_content .='<h1 class="pt-plus-page-title" style="color: ' . esc_attr($title_color) . ';'.$titlestyle.'">';
							if(!empty($page_custom_title)){
								$title_content .= $page_custom_title;
							}else{
								$title_content .= get_the_title();
							}
							$title_content .='</h1>';
							
							/* subtitle */
							if(!empty($subtitle)){
								$subtitle_content .='<div class="pt-plus-page-subtitle" style="color: ' . esc_attr($subtitle_color) . ';'.$subtitlestyle.'">'.esc_html($subtitle).'</div>';
							}
							
							/* icon */
							if(isset($image_icon) && $image_icon == 'image'){
								if(isset($select_image) && !empty($select_image)){
									$img = wp_get_attachment_image_src($select_image, "full");
									$imgSrc = $img[0];
									$icon_content .='<div class="breadcrumbs-icon"><img src="'.esc_url($imgSrc).'" alt=""></div>';
								}
							}
							
							$uid=uniqid('svg');
							if(isset($image_icon) && $image_icon == 'icon'){		
											$icon_css = ' style="';
											if($icon_color != "") {
											$icon_css .= 'color: '.esc_attr($icon_color).';';
											}	
											$icon_css .= '"'; 
											vc_icon_element_fonts_enqueue( $icon_type );
											$icon_class = isset( ${'icon_' . $icon_type} ) ? esc_attr( ${'icon_' . $icon_type} ) : 'fa fa-adjust';
											$icon_content .= '<div class="breadcrumbs-icon '.esc_attr($icon_size).'"><i class=" '.esc_attr($icon_class).' stylish-icon" '.$icon_css.'></i></div>';
							}
							if(!empty($image_icon) && $image_icon=='svg'){
								if($svg_icon == 'svg'){
									$svg_attach = wp_get_attachment_image_src( $svg_image,true);
									$svg_url = $svg_attach[0];
								}else{
									$svg_url = THEPLUS_PLUGIN_URL.'vc_elements/images/svg/'.esc_attr($svg_d_icon); 
								}
								if(!empty($border_stroke_color)){
								$border_stroke_color=$border_stroke_color;
								}else{
								$border_stroke_color='none';
							}
							
								$icon_content .='<div  class="breadcrumbs-icon ts-hover-draw-svg  '.esc_attr($uid).'"  data-type="oneByOne" data-duration="380" data-id="'.esc_attr($uid).'" data-stroke="'.esc_attr($border_stroke_color).'" data-fill_color="none">
									<object id="'.esc_attr($uid).'" type="image/svg+xml" data="'.esc_url($svg_url).'" ></object>
								</div>';
							}
							
							/* title layout */
							if(!empty($title_layout)){
								$title_layout_content .='';
									if($title_layout=='style-1'){
										$title_layout_content .= $title_content.$subtitle_content;
									}else if($title_layout=='style-2'){
										$title_layout_content .= $subtitle_content.$title_content;
									}else if($title_layout=='style-3'){
										$title_layout_content .= '<div class="breadcrumbs-title-layout-3"><div class="table-cell">'.$title_content.'</div><div class="table-cell">'.$subtitle_content.'</div></div>';
									}else if($title_layout=='style-4'){
										$title_layout_content .= '<div class="breadcrumbs-title-layout-4"><div class="table-cell">'.$subtitle_content.'</div><div class="table-cell">'.$title_content.'</div></div>';
									}
								$title_layout_content .='';
								}
								
							/* breadcrumb position */
							$breadcrumbs_position_1=$breadcrumbs_position_2=$breadcrumbs_position='';
								if(!empty($breadcrumbs_bar_position) && $breadcrumbs_bar_position=='style-2' && $breadcrumbs_styles=='style-1' && ($layout=='style-1' || $layout=='style-3' || $layout=='style-6' || $layout=='style-8')){
									if(!empty($breadcrumbs_enable) && $breadcrumbs_enable=='on' && $breadcrumbs_styles=='style-1'){
									   $breadcrumbs_position_2 .='<div class="pt-plus-breadcrumbs style-1" style="'.$breadbstyle.'">';
										if (function_exists('pt_plus_breadcrumbs')) {
											$breadcrumbs_position_2 .= pt_plus_breadcrumbs();
										}
										$breadcrumbs_position_2 .='</div>';
										$breadcrumbs_position .='bread-position-2';
									 }
								}else{
									if(!empty($breadcrumbs_enable) && $breadcrumbs_enable=='on' && $breadcrumbs_styles=='style-1'){
									   $breadcrumbs_position_1 .='<div class="pt-plus-breadcrumbs style-1" style="'.$breadbstyle.'">';
										if (function_exists('pt_plus_breadcrumbs')) {
											$breadcrumbs_position_1 .= pt_plus_breadcrumbs();
										}
										$breadcrumbs_position_1 .='</div>';
										$breadcrumbs_position .='bread-position-1';
									 }
								}
								
							if(!empty($layout) && $layout=='style-4'){
				$content .='<div class="bread-title-content">'.$title_layout_content.$breadcrumbs_position_1.'</div>';
if(!empty($icon_content)){
				$content .='<div class="breadcrumbs-icon-wrap">'.$icon_content.'</div>';
}
			}else{
if(!empty($icon_content)){
			$content .='<div class="breadcrumbs-icon-wrap">'.$icon_content.'</div>';
}
				$content .='<div class="bread-title-content">'.$title_layout_content.$breadcrumbs_position_1.'</div>';
			}
							
						if(!empty($layout) && isset($layout)){
								$layout_style='layout-'.esc_attr($layout);
							}else{
								$layout_style= 'layout-style-1';
							}
						
					/*------------background color ----------*/
					$output='';
					if(isset($select_anim) && !empty($select_anim) && $select_anim=='bg_normal_color') {
					if(isset($normal_bg_color) && !empty($normal_bg_color)) {
					$output .='<div class="pt-plus-columns-bg-wrap" style="background:'.esc_attr($normal_bg_color).'"></div>';
					}
					}
					/*------------background color ----------*/
					/*------------------------------gradient color-----------------------*/
					$css_rules='';
					if(isset($select_anim) && !empty($select_anim) && $select_anim=='bg_gradientcolor'){
					if(!empty($gradient_color1) && !empty($gradient_color2)){
					$css_rules .='opacity: '.esc_attr($overlay_color_opacity).';';
					$css_rules .= pt_plus_gradient_color($gradient_color1,$gradient_color2,$gradient_style);
					$output .='<div class="pt-plus-columns-bg-wrap columns-bg-anim-colors" id="'.esc_attr($uniqid).'" style="'.$css_rules.'"></div>';
					}
					}
					/*------------------------------gradient color-----------------------*/
					/*---------------------------- background Animation color---------------------------*/
					$anim_value='';
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
					/*---------------------------- background Animation color---------------------------*/
					/*------------------------------bg image-----------------------*/
					if(isset($select_anim) && !empty($select_anim) && $select_anim=='bg_image') {
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



					 if(isset($column_bg_image_new) && !empty($column_bg_image_new)) {
							$bg_image_src = wp_get_attachment_image_src($column_bg_image_new, 'full');
							$bg_image = $bg_image_src[0];
							$css_rules1 .= 'background-image: url('.esc_url($bg_image).');';
						}
						if(isset($column_bg_image_position) && !empty($column_bg_image_position))
							$css_rules1 .= 'background-position: '.esc_attr($column_bg_image_position).';';
						
						if(isset($column_bg_image_size) && !empty($column_bg_image_size))
							$css_rules1 .= '-webkit-background-size: '.esc_attr($column_bg_image_size).';-moz-background-size: '.esc_attr($column_bg_image_size).';-o-background-size: '.esc_attr($column_bg_image_size).';background-size: '.esc_attr($column_bg_image_size).';';

						if(isset($column_bg_img_attach) && !empty($column_bg_img_attach))
							$css_rules1 .= 'background-attachment: '.esc_attr($column_bg_img_attach).';';
						
						

					$output .= '<div class="pt-plus-columns-bg-wrap columns-bg-anim-colors columns-bg-image '.esc_attr($class1).'" id="'.esc_attr($uniqid1).'" '.$data_atts.' src="'.esc_url($bg_image).'" style="'.$css_rules1.'"></div>';

					}
					/*------------------------------bg image-----------------------*/
					/*------------------------------bg video-----------------------*/
					$data_atts2 = $video_atts2 = $controller_css2 =$poster_url='';
					 
					$uniqid2 = uniqid('pt_plus_video_bg_');
					if(isset($columns_video_variant) && !empty($columns_video_variant) && $select_anim=='bg_video') {
						   if(isset($columns_video_poster) && !empty($columns_video_poster)) {
							$poster_src = wp_get_attachment_image_src($columns_video_poster,'full');
							$poster_url = $poster_src[0];
						} 
							if($columns_video_variant== 'self-hosted' && (isset($columns_video_url_mp4) || isset($columns_video_url_mp4))) {
							
							$video_atts2 .= 'poster="'. esc_url($poster_url) .'"';

							if(isset($columns_video_opts) && !empty($columns_video_opts)) {
								if(substr_count($columns_video_opts, 'loop') == 1) {
									$video_atts2 .= ' loop="true" ';
								}
								if(substr_count($columns_video_opts, 'muted') == 1) {
									$video_atts2 .= ' muted="true" ';	
								}		
							}

							$output .= '<div class="pt-plus-bg-video pt-plus-columns-bg-wrap columns-video-bg" id="wrapper-'.esc_attr($uniqid2).'" '.$data_atts2.' style="background-image: url('.esc_js($poster_url).');">';
							$output .= '<video id="'.esc_attr($uniqid2).'" class="video-js vjs-default-skin columns_vc_hidden-md columns_vc_hidden-sm columns_vc_hidden-xs" controls
								   preload="auto"
								   width="100%"
								   height="100%"
								   autoplay="true"
								   '.$video_atts2.'
								   data-setup="{}">';

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
									$extra_url_prop .= '&amp;loop=1&amp;playlist='.esc_attr($columns_youtube_video_id);
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
					/*------------------------------bg video-----------------------*/
					/*-------------------overlay normal color--------------*/
					if($overlay_style=='normal_color' && $normal_overlay_color!=''){
					$output .='<div class="pt-plus-row-overlay" style="background:'.esc_attr($normal_overlay_color).'"></div>';
					}
					/*----------------overlay normal color-----------------*/
					/*------------------overlay gradient color-----------------*/
					if(isset($overlay_style) && !empty($overlay_style) && $overlay_style=='gradient_color'){
					if(!empty($overlay_gradient_color1) && !empty($overlay_gradient_color2)){
					$gradient_color='';
					$gradient_color .='opacity: '.esc_attr($overlay_gradient_opacity).';';
					$gradient_color .= pt_plus_gradient_color($overlay_gradient_color1,$overlay_gradient_color2,$overlay_gradient_style);
					$output .='<div class="pt-plus-row-overlay" style="'.$gradient_color.'"></div>';
					}
					}
					/*-----------------overlay gradient color-----------------------*/
					/*------------------------texture image-------------------------*/
					if($texture_image!=''){
					$texture_css='';
					$img = wp_get_attachment_image_src($texture_image, "full");
								$imgSrc = $img[0];
					if($imgSrc){
					$texture_css .= 'background: url('.esc_url($imgSrc).') repeat;opacity: '.esc_attr($opacity_texture_image).';';
					$output .='<div class="pt-plus-row-overlay" style="'.$texture_css.'"></div>';
					}
					}
					/*-----------------------texture image-------------------------*/
					$uid =uniqid("breadcrumb");
					$breadcrumbs='<div id="pt-plus-header-breadcrumbs" class="pt-plus-header-breadcrumbs '.esc_attr($el_class).' '.esc_attr($uid).'">';      
						  /*background options*/
							$breadcrumbs.='<div class="pt-plus-header-bg-container">';
							$breadcrumbs.=$output;
							$breadcrumbs.='</div>';
						   /*background options*/
						$breadcrumbs.='<div class="row">';
						$breadcrumbs.='<div class="'.esc_attr($breadcrumb_width).'">';
							$breadcrumbs.='<div class="'.esc_attr($parralex_class).' pt-plus-page-title-inner" style="min-height:'.esc_attr($bg_height).';height:'.esc_attr($bg_height).';">';
											
									$breadcrumbs.='<div class="pt-plus-page-title-inner-wrap  '.esc_attr($breadcrumbs_position).' '.esc_attr($layout_style).' '. esc_attr($vertical_position).' ">';
										if(!empty($breadcrumbs_styles) && $breadcrumbs_styles!='style-3'){
											if(!empty($breadcrumbs_bar_position) && $breadcrumbs_bar_position=='style-2' && $breadcrumbs_styles=='style-1' && ($layout=='style-3' || $layout=='style-8')){
												 $breadcrumbs.= $breadcrumbs_position_2;
												$breadcrumbs.='<div class="breadcrumbs-layout-style">';
													  $breadcrumbs.= $content;
												$breadcrumbs.='</div>';
											 }else{
												$breadcrumbs.='<div class="breadcrumbs-layout-style">';
													  $breadcrumbs.= $content;
												$breadcrumbs.='</div>';
												  $breadcrumbs.= $breadcrumbs_position_2;
											 }
										}
									$breadcrumbs.='</div>';
								if($breadcrumbs_enable=='on' && $breadcrumbs_styles=='style-2'){ 				
									$breadcrumbs.='<div class="pt-plus-breadcrumbs style-2 '.esc_attr($breadcrumbs_alignments).'" style="'.$breadbstyle.'">';													
										if (function_exists('pt_plus_breadcrumbs')) {
											$breadcrumbs.= pt_plus_breadcrumbs();
										}
										$breadcrumbs.='</div>';				
								}
								if($breadcrumbs_enable=='on' && $breadcrumbs_styles=='style-3'){
									$breadcrumbs.='<div class="pt-plus-breadcrumbs style-3 '.esc_attr($breadcrumbs_alignments).'" style="'.$breadbstyle.'">';
										$breadcrumbs.='<div class="breadcrumbs-layout-style">';
										 if (function_exists('pt_plus_breadcrumbs')) {
											$breadcrumbs.= pt_plus_breadcrumbs();
										}
										 $breadcrumbs.= $content;
										$breadcrumbs.='</div>';
									$breadcrumbs.='</div>';
								}
							$breadcrumbs.='</div>';
							
						$breadcrumbs.='</div>';
						$breadcrumbs.='</div>';
						
						$breadcrumbs .='</div>';
						$css_rule='';
						if($breadcrumb_bar_text_color!=''){
							$css_rule='<style >';
							$css_rule .='.'.esc_js($uid).' .pt-plus-page-title-inner .pt-plus-breadcrumbs > nav#crumbs a,.'.esc_js($uid).'  .pt-plus-page-title-inner .pt-plus-breadcrumbs > nav#crumbs span{color: '.esc_js($breadcrumb_bar_text_color).';}';
							$css_rule .='.'.esc_js($uid).' .pt-plus-breadcrumbs nav#crumbs a,.'.esc_js($uid).' .pt-plus-breadcrumbs nav#crumbs a span{'.$breadbstyle.'}.'.esc_js($uid).' .pt-plus-breadcrumbs.style-3 .breadcrumbs-layout-style,.'.esc_js($uid).' .pt-plus-breadcrumbs.style-2 > nav{background: '.esc_js($breadcrumb_bar_bg_color).';}.'.esc_js($uid).' .pt-plus-breadcrumbs.style-3{	border-color: '.esc_js($breadcrumb_bar_bg_color).';}';
							$css_rule .='</style>';
						}
					  return $css_rule.$breadcrumbs;
		}
		function init_tp_header_breadcrumbs(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => __("Breadcrumbs", "pt_theplus"),
					"base" => "tp_header_breadcrumbs",
					"category" => __("The Plus", "pt_theplus"),
					"description" => esc_html__('Setup Stylish Breadcrumbs', 'pt_theplus'),
					"icon" =>"tp-breadcrumb",
					"params" => array(
						array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Layout', 'pt_theplus'),
								'param_name'  => 'layout',
								'admin_label' => true,
								'simple_mode' => false,
								'value'		=> 'style-1',
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Layout 1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/layout-style-1.jpg'
									),
									'style-2' => array(
										'tooltip' => esc_attr__('Layout 2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/layout-style-2.jpg'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Layout 3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/layout-style-3.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Layout 4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/layout-style-4.jpg'
									),
									'style-5' => array(
										'tooltip' => esc_attr__('Layout 5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/layout-style-5.jpg'
									),
									'style-6' => array(
										'tooltip' => esc_attr__('Layout 6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/layout-style-6.jpg'
									),
									'style-7' => array(
										'tooltip' => esc_attr__('Layout 7','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/layout-style-7.jpg'
									),
									'style-8' => array(
										'tooltip' => esc_attr__('Layout 8','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/layout-style-8.jpg'
									),
								),
							),
						array(
								'type'        => 'radio_select_image',
								'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Breadcrumbs Title Style Layout using this option.','pt_theplus').'</span></span>'.esc_html__('Title Style Layout', 'pt_theplus')),
								'param_name'  => 'title_layout',
								'simple_mode' => false,
								'value'		=> 'style-1',
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Style 1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/title-style-1.jpg'
									),
									'style-2' => array(
										'tooltip' => esc_attr__('Style 2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/title-style-2.jpg'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Style 3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/title-style-3.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style 4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/title-style-4.jpg'
									),
								),
							),
						array(
								'type'        => 'radio_select_image',
								'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Breadcrumbs Title Vertical Position Layout using this option.','pt_theplus').'</span></span>'.esc_html__('Vertical Position', 'pt_theplus')),
								'param_name'  => 'vertical_position',
								'simple_mode' => false,
								'value'		=> 'pos-center',
								'options'     => array(
									'pos-center' => array(
										'tooltip' => esc_attr__('Center','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/position-middle.jpg'
									),
									'pos-bottom' => array(
										'tooltip' => esc_attr__('Bottom','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/position-bottom.jpg'
									),
								),
						),
						array(
						  "type" => "dropdown",
						  'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select Icon, Custom Image or SVG using this option.','pt_theplus').'</span></span>'.esc_html__('Select Icon', 'pt_theplus')),
						  "param_name" => "image_icon",
						  "value" => array(
								__( 'None', 'pt_theplus' ) => '',
								__( 'Icon', 'pt_theplus' ) => 'icon',
								__( 'Image', 'pt_theplus' ) => 'image',
								__( 'Svg', 'pt_theplus' ) => 'svg',
							),
							'group' => __( 'Icon', 'pt_theplus' ),
							"std" => "",
						),
					array(
							"type" => "attach_image",
							"heading" => esc_html__("Use Image As icon", 'pt_theplus') ,
							"value" => "",
							"description" => '',  
							"param_name" => 'select_image',
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'image',
							),
							'group' => __( 'Icon', 'pt_theplus' ),
						),
						array(
						  "type" => "dropdown",
						  "heading" => __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Pre Built SVG Icon / Custom Upload ?You can use our Pre Built Drawable SVG icons or You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Svg Type', 'pt_theplus')),
						  "param_name" => "svg_icon",
						   "value" => array(
								__('Pre Built SVG Icon', 'pt_theplus') => 'img',
								__('Custom Upload', 'pt_theplus') => 'svg'
							),
							'group' => __( 'Icon', 'pt_theplus' ),
							"std" => "icon",
							'dependency' => array(
									'element' => 'image_icon',
									'value' =>'svg',
							),
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose our tested drawable SVGs from this list.','pt_theplus').'</span></span>'.esc_html__('Pre Built SVG icon', 'pt_theplus')),
							'param_name' => 'svg_d_icon',
							"value" => array(
								__("1. App", "pt_theplus") => "app.svg",
								__("2. Arrow", "pt_theplus") => "arrow.svg",
								__("3. Art", "pt_theplus") => "art.svg",
								__("4. Banknote", "pt_theplus") => "banknote.svg",
								__("5. Building", "pt_theplus") => "building.svg",
								__("6. Bulb-idea", "pt_theplus") => "bulb-idea.svg",
								__("7. Calendar", "pt_theplus") => "calendar.svg",
								__("8. Call", "pt_theplus") => "call.svg",
								__("9. Camera", "pt_theplus") => "camera.svg",
								__("10. Cart", "pt_theplus") => "cart.svg",
								__("11. Cd", "pt_theplus") => "cd.svg",
								__("12. Clip", "pt_theplus") => "clip.svg",
								__("13. Clock", "pt_theplus") => "clock.svg",
								__("14. Cloud", "pt_theplus") => "cloud.svg",
								__("15. Comment", "pt_theplus") => "comment.svg",
								__("16. Content-board", "pt_theplus") => "content-board.svg",
								__("17. Cup", "pt_theplus") => "cup.svg",
								__("18. Diamond", "pt_theplus") => "diamond.svg",
								__("19. Earth", "pt_theplus") => "earth.svg",
								__("20. Eye", "pt_theplus") => "eye.svg",
								__("21. Finger", "pt_theplus") => "finger.svg",
								__("22. Fingerprint", "pt_theplus") => "fingerprint.svg",
								__("23. Food", "pt_theplus") => "food.svg",
								__("24. Foundation", "pt_theplus") => "foundation.svg",
								__("25. Gear", "pt_theplus") => "gear.svg",
								__("26. Graphics-design", "pt_theplus") => "graphics-design.svg",
								__("27. Handshakeandshake", "pt_theplus") => "handshake.svg",
								__("28. Hard-disk", "pt_theplus") => "hard-disk.svg",
								__("29. Heart", "pt_theplus") => "heart.svg",
								__("30. Hook", "pt_theplus") => "hook.svg",
								__("31. Image", "pt_theplus") => "image.svg",
								__("32. Key", "pt_theplus") => "key.svg",
								__("33. Laptop", "pt_theplus") => "laptop.svg",
								__("34. Layers", "pt_theplus") => "layers.svg",
								__("35. List", "pt_theplus") => "list.svg",
								__("36. Location", "pt_theplus") => "location.svg",
								__("37. Loudspeaker", "pt_theplus") => "loudspeaker.svg",
								__("38. Mail", "pt_theplus") => "mail.svg",
								__("39. Map", "pt_theplus") => "map.svg",
								__("40. Mic", "pt_theplus") => "mic.svg",
								__("41. Mind", "pt_theplus") => "mind.svg",
								__("42. Mobile", "pt_theplus") => "mobile.svg",
								__("43. Mobile-comment", "pt_theplus") => "mobile-comment.svg",
								__("44. Music", "pt_theplus") => "music.svg",
								__("45. News", "pt_theplus") => "news.svg",
								__("46. Note", "pt_theplus") => "note.svg",
								__("47. Offer", "pt_theplus") => "offer.svg",
								__("48. Paperplane", "pt_theplus") => "paperplane.svg",
								__("49. Pendrive", "pt_theplus") => "pendrive.svg",
								__("50. Person", "pt_theplus") => "person.svg",
								__("51. Photography", "pt_theplus") => "photography.svg",
								__("52. Posisvg", "pt_theplus") => "posisvg.svg",
								__("53. Recycle", "pt_theplus") => "recycle.svg",
								__("54. Ruler", "pt_theplus") => "ruler.svg",
								__("55. Satelite", "pt_theplus") => "satelite.svg",
								__("56. Search", "pt_theplus") => "search.svg",
								__("57. Secure", "pt_theplus") => "secure.svg",
								__("58. Server", "pt_theplus") => "server.svg",
								__("59. Setting", "pt_theplus") => "setting.svg",
								__("60. Share", "pt_theplus") => "share.svg",
								__("61. Smiley", "pt_theplus") => "smiley.svg",
								__("62. Sound", "pt_theplus") => "sound.svg",
								__("63. Stack", "pt_theplus") => "stack.svg",
								__("64. Star", "pt_theplus") => "star.svg",
								__("65. Study", "pt_theplus") => "study.svg",
								__("66. Suitcase", "pt_theplus") => "suitcase.svg",
								__("67. Tag", "pt_theplus") => "tag.svg",
								__("68. Tempsvg", "pt_theplus") => "tempsvg.svg",
								__("69. Thumbsup", "pt_theplus") => "thumbsup.svg",
								__("70. Tick", "pt_theplus") => "tick.svg",
								__("71. Trash", "pt_theplus") => "trash.svg",
								__("72. Truck", "pt_theplus") => "truck.svg",
								__("73. Tv", "pt_theplus") => "tv.svg",
								__("74. User", "pt_theplus") => "user.svg",
								__("75. Video", "pt_theplus") => "video.svg",
								__("76. Video-production", "pt_theplus") => "video-production.svg",
								__("77. Wallet", "pt_theplus") => "wallet.svg"
							),
							'description' => "",
							'admin_label' => false,
							'dependency' => array(
								'element' => 'svg_icon',
								'value' => array(
										"img"
								)
							),
							'group' => __('Icon', 'pt_theplus'),
							'std' => 'app.svg'
						),
						array(
								'type' => 'attach_image',
								"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can upload your custom SVG from this option. You can check Drawable functionality of your SVG icon from','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://maxwellito.github.io/vivus-instant/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Upload Custom SVG', 'pt_theplus')),
								'param_name' => 'svg_image',
								'value' => '',
								'description' => "",
								'admin_label' => false,
								'dependency' => array(
									'element' => 'svg_icon',
									'value' =>'svg',
								),
								'group' => __( 'Icon', 'pt_theplus' ),
							),
									array(
								'type' => 'colorpicker',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose SVG&#39;s Stroke in Normal Terms Border Color Using This Option.','pt_theplus').'</span></span>'.esc_html__('Stroke(Border) Color', 'pt_theplus')),
								'edit_field_class' => 'vc_col-xs-6',
								'param_name' => 'border_stroke_color',
								"value" => '#ff0000',
								'dependency' => array(
									'element' => 'svg_icon',
									'value' => array(
										"svg",
										"img"
									),
								),
								'group' => __('Icon', 'pt_theplus')
							),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' We have given options of icons from Font Awesome, Open Iconic, Linecons, Typicons, Entypo, and Mono Social.','pt_theplus').'</span></span>'.esc_html__('Icon Library', 'pt_theplus')),
							'value' => array(
								__( 'Font Awesome', 'pt_theplus' ) => 'fontawesome',
								__( 'Open Iconic', 'pt_theplus' ) => 'openiconic',
								__( 'Typicons', 'pt_theplus' ) => 'typicons',
								__( 'Entypo', 'pt_theplus' ) => 'entypo',
								__( 'Linecons', 'pt_theplus' ) => 'linecons',
								__( 'Mono Social', 'pt_theplus' ) => 'monosocial',
							),
							'admin_label' => false,
							'param_name' => 'icon_type',
							'dependency' => array(
									'element' => 'image_icon',
									'value' => array(
										"icon"
									),
								),
							"std" => 'fontawesome',
							'group' => __( 'Icon', 'pt_theplus' ),
							'description' => "",
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'icon_fontawesome',
							'value' => 'fa fa-adjust', 
							'settings' => array(
								'emptyIcon' => false,
								'iconsPerPage' => 100,
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'fontawesome',
							),
							'group' => __( 'Icon', 'pt_theplus' ),
							'description' => "",
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'icon_openiconic',
							'value' => 'vc-oi vc-oi-dial', 
							'settings' => array(
								'emptyIcon' => false,
								'type' => 'openiconic',
								'iconsPerPage' => 100,
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'openiconic',
							),
							'group' => __( 'Icon', 'pt_theplus' ),
							'description' => "",
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'icon_typicons',
							'value' => 'typcn typcn-adjust-brightness',
							'settings' => array(
								'emptyIcon' => false, 
								'type' => 'typicons',
								'iconsPerPage' => 100,
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'typicons',
							),
							'group' => __( 'Icon', 'pt_theplus' ),
							'description' => "",
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'icon_entypo',
							'value' => 'entypo-icon entypo-icon-note',
							'settings' => array(
								'emptyIcon' => false,
								'type' => 'entypo',
								'iconsPerPage' => 100,
							),
							'group' => __( 'Icon', 'pt_theplus' ),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'entypo',
							),
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'icon_linecons',
							'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
							'settings' => array(
								'emptyIcon' => false, // default true, display an "EMPTY" icon?
								'type' => 'linecons',
								'iconsPerPage' => 100, // default 100, how many icons per/page to display
							),
							'group' => __( 'Icon', 'pt_theplus' ),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'linecons',
							),
							'description' => "",
						),
						array(
							'type' => 'iconpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Your selected icon from selected Icon Library.','pt_theplus').'</span></span>'.esc_html__('Icon', 'pt_theplus')),
							'param_name' => 'icon_monosocial',
							'value' => 'vc-mono vc-mono-fivehundredpx', // default value to backend editor admin_label
							'settings' => array(
								'emptyIcon' => false, // default true, display an "EMPTY" icon?
								'type' => 'monosocial',
								'iconsPerPage' => 100, // default 100, how many icons per/page to display
							),
							'group' => __( 'Icon', 'pt_theplus' ),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'monosocial',
							),
							'description' => "",
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for icon using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Color', 'pt_theplus')),
							'param_name' => 'icon_color',
							'value' => '#0099CB',
							'description' => "",
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							"edit_field_class" =>'vc_col-xs-6',
							'group' => __( 'Icon', 'pt_theplus' ),
							),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select icon Size using this option.','pt_theplus').'</span></span>'.esc_html__('Icon Size', 'pt_theplus')),
							"param_name" => "icon_size",
							'value' => array(
								__( 'Small', 'pt_theplus' ) => 'small',
								__( 'Medium', 'pt_theplus' ) => 'medium',
								__( 'Large', 'pt_theplus' ) => 'large',
								__( 'X-Large', 'pt_theplus' ) => 'x-large',
							),
							"std" =>'medium',
							"description" => '',
							"edit_field_class" => "vc_col-xs-6",
							'dependency' => array(
								'element' => 'image_icon',
								'value' => 'icon',
							),
							'group' => __( 'Icon', 'pt_theplus' ),
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can turn on/off breadcrumb bar using this option.','pt_theplus').'</span></span>'.esc_html__('Breadcrumb Bar Display', 'pt_theplus')),
							'param_name' => 'breadcrumbs_enable',
							'description' => '',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-12",
							
						),
						array(
								'type'        => 'radio_select_image',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Breadcrumbs Styles using this option.','pt_theplus').'</span></span>'.esc_html__('Breadcrumbs Styles', 'pt_theplus')),				
								'param_name'  => 'breadcrumbs_styles',
								'simple_mode' => false,
								'value'		=> 'style-1',
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Style 1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/bread-style-1.jpg'
									),
									'style-2' => array(
										'tooltip' => esc_attr__('Style 2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/bread-style-2.jpg'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Style 3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/bread-style-3.jpg'
									),
								),
								'dependency' => array(
									'element' => 'breadcrumbs_enable',
									'value' => array(
										'on'
									)
								),
						),
						array(
								'type'     => 'radio_select_image',
								'param_name'       => 'breadcrumbs_bar_position',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Breadcrumbs Bar Position using this option.','pt_theplus').'</span></span>'.esc_html__('Breadcrumbs Bar Position', 'pt_theplus')),
							   'value'		=> 'style-1',
							   'simple_mode' => false,
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Style 1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/bread-position-1.jpg'
									),
									'style-2' => array(
										'tooltip' => esc_attr__('Style 2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/breadcrumb/bread-position-2.jpg'
									),
								),
								'dependency' => array(
									'element' => 'layout',
									'value' => array(
										'style-1','style-3','style-6','style-8'
									)
								),
								'dependency' => array(
									'element' => 'breadcrumbs_styles',
									'value' => array(
										'style-1'
									)
								),
							),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Choose Breadcrumbs Alignment from Right, Left or Center.','pt_theplus').'</span></span>'.esc_html__('Breadcrumbs Alignments', 'pt_theplus')),
							'param_name' => 'breadcrumbs_alignments',
							'value' => array(
								__('Left', 'pt_theplus') => 'text-left',
								__('Center', 'pt_theplus') => 'text-center',
								__('Right', 'pt_theplus') => 'text-right'
							),
							 'dependency' => array(
								'element' => 'breadcrumbs_styles',
								'value' => array(
									'style-2','style-3'
								)
							),
							 "edit_field_class" => "vc_col-xs-6",
						),
						
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select that as custom page title or default page title of page.','pt_theplus').'</span></span>'.esc_html__('Page Title Options ', 'pt_theplus')),
							'param_name' => 'breadcrumb_page_title',
							'value' => array(
								__('Page Title', 'pt_theplus') => 'page_title',
								__('Custom Title', 'pt_theplus') => 'custom_title'
							)
						),
						array(
							'heading' => esc_html__('Main Title', 'pt_theplus'),
							'param_name' => 'page_custom_title',
							'type' => 'textfield',
							'value' => '',
							'admin_label' => true,
							'description' => '',
							'dependency' => array(
								'element' => 'breadcrumb_page_title',
								'value' => array(
									'custom_title'
								)
							)
						),
						array(
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can enter value of sub title using this option.','pt_theplus').'</span></span>'.esc_html__('Sub Title ', 'pt_theplus')),
							'param_name' => 'subtitle',
							'type' => 'textfield',
							'value' => '',
							'description' => '',
						),
array(
            'type' => 'dropdown',
            'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select a width of this section Container box or Full width','pt_theplus').'</span></span>'.esc_html__('Breadcrumb Width', 'pt_theplus')),
            'param_name' => 'breadcrumb_width',
            'value' => array(
                __('Container', 'pt_theplus') => 'container',
                __('Full-Width', 'pt_theplus') => 'container-fluid',
            ),
            "description" => "",
"edit_field_class" => "vc_col-xs-6",
        ),
						array(
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can enter a height of this section in pixels using this option. e.g. 300px,700px','pt_theplus').'</span></span>'.esc_html__('Breadcrumb Height ', 'pt_theplus')),
							'param_name' => 'bg_height',
							'type' => 'textfield',
							'value' => __('400px', 'pt_theplus'),
							'description' => '',
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can turn on/off on scroll parallax effect of whole breadcrumb section using this option.','pt_theplus').'</span></span>'.esc_html__('On Scroll Effect', 'pt_theplus')),
							'param_name' => 'parallax_on',
							'value' => 'true',
							'options' => array(
								'true' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-4"
						),
						
						array(
								'type'        => 'radio_select_image',
								'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Background Options styles using this option.','pt_theplus').'</span></span>'.esc_html__('Background Options', 'pt_theplus')),
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
								),
								 'group' => esc_attr__('Background Options', 'pt_theplus'),
								
							),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select a background color from unlimited options.','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')), 
							'param_name' => 'normal_bg_color',
							'value' => '#d3d3d3',
							'group' => esc_attr__('Background Options', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_normal_color'
							)
						),
						/*--------background gradient---------------------------*/
						array(
							'type' => 'colorpicker',
							'heading' => __('First Color', 'pt_theplus'),
							'param_name' => 'gradient_color1',
							'group' => esc_attr__('Background Options', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gradientcolor'
							),
							'edit_field_class' =>'vc_col-xs-6',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Second Color', 'pt_theplus'),
							'param_name' => 'gradient_color2',
							'group' => esc_attr__('Background Options', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gradientcolor'
							),
							'edit_field_class' =>'vc_col-xs-6',
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('Gradient Style', 'pt_theplus'),
							'param_name' => 'gradient_style',
							'value' => array(
								__('Horizontal', 'pt_theplus') => 'horizontal',
								__('Vertical', 'pt_theplus') => 'vertical',
								__('Diagonal', 'pt_theplus') => 'diagonal',
								__('Radial', 'pt_theplus') => 'radial'
							),
							"description" => "",
							'group' => esc_attr__('Background Options', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gradientcolor'
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select opacity of Gradient color layer.','pt_theplus').'</span></span>'.esc_html__('Opacity', 'pt_theplus')),
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
							'group' => esc_attr__('Background Options', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_gradientcolor'
							)
						),
						/*--------background gradient---------------------------*/
						/*--------Animated Gradient Color---------------------------*/
						array(
							'type' => 'param_group',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Add as many as you want colors from this box.','pt_theplus').'</span></span>'.esc_html__('Multiple Colors', 'pt_theplus')),
							'param_name' => 'columns_bg_colors',
							'description' => '',
							'params' => array(
								array(
									'type' => 'colorpicker',
									'heading' => __('Color', 'pt_theplus'),
									'param_name' => 'column_bg_single_color',
									'admin_label' => true
								)
							),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_color'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Time interval between color change effect. Use values like 3000,5000, etc. That Number will be in milliseconds.','pt_theplus').'</span></span>'.esc_html__('Animation Duration', 'pt_theplus')),
							'param_name' => 'column_anim_bg_duration',
							'value' => '3000',
							'description' => '',
							'group' => esc_attr__('Background Options', 'pt_theplus'),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_color'
							)
						),
						/*--------Animated Gradient Color---------------------------*/
						/*--------background image---------------------------*/
						array(
							'type' => 'attach_image',
							'class' => '',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Directly upload or select image from media library.','pt_theplus').'</span></span>'.esc_html__('Upload Background Image', 'pt_theplus')), 
							'param_name' => 'column_bg_image_new',
							'value' => '',
							'description' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Background Image effect from given options.','pt_theplus').'</span></span>'.esc_html__('Background Effect', 'pt_theplus')), 
							'param_name' => 'columns_parallax_style',
							'value' => array(
								__('Normal Background Image', 'pt_theplus') => 'columns_simple_image',
								__('Auto Moving Background Image', 'pt_theplus') => 'columns_animated_bg'
							),
							'description' => "",
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						array(
							'type' => 'dropdown',
							'class' => '',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can direction of auto moving background image.','pt_theplus').'</span></span>'.esc_html__('Direction', 'pt_theplus')), 
							'param_name' => 'columns_animation_direction',
							'value' => array(
								__('None', 'pt_theplus') => '',
								__('Left to Right', 'pt_theplus') => 'right',
								__('Right to Left', 'pt_theplus') => 'left',
								__('Top to Bottom', 'pt_theplus') => 'top',
								__('Bottom to Top', 'pt_theplus') => 'bottom'
								
							),
							'description' => "",
							'dependency' => array(
								'element' => 'columns_parallax_style',
								'value' => 'columns_animated_bg'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Speed of background image transition . Use values like 30,50, etc.','pt_theplus').'</span></span>'.esc_html__('Transition Speed', 'pt_theplus')), 
							'param_name' => 'columns_parallax_sense',
							'value' => '30',
							'description' => "",
							'dependency' => array(
								'element' => 'columns_parallax_style',
								'value' => 'columns_animated_bg'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
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
							'description' => "",
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
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
							'description' => "",
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_image'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
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
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						
						/*--------background image---------------------------*/
						/*--------background video ---------------------------*/
						array(
							'type' => 'dropdown',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('IE, Chrome & Safari support MP4 format, while Firefox & Opera prefer WebM / Ogg formats. You can upload the video through Wordpress Media Library and paste link of it here.','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://www.w3schools.com/html/html5_video.asp">'.esc_html__('Check Support link','pt_theplus').'</a></span></span>'.esc_html__('Video Source ', 'pt_theplus')),
							'param_name' => 'columns_video_variant',
							'value' => array(
								__('Self Hosted', 'pt_theplus') => 'self-hosted',
								__('YouTube', 'pt_theplus') => 'youtube',
								__('Vimeo', 'pt_theplus') => 'vimeo'
							),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_video'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'class' => '',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('IE, Chrome & Safari support MP4 format, while Firefox & Opera prefer WebM / Ogg formats. You can upload the video through WordPress Media Library','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://www.w3schools.com/html/html5_video.asp">'.esc_html__('Check Support link','pt_theplus').'</a></span></span>'.esc_html__('URL of Video (MP4) ', 'pt_theplus')),
							'param_name' => 'columns_video_url_mp4',
							'value' => '',
							'dependency' => Array(
								'element' => 'columns_video_variant',
								'value' => array(
									'self-hosted'
								)
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('IE, Chrome & Safari support MP4 format, while Firefox & Opera prefer WebM / Ogg formats. You can upload the video through WordPress Media Library','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://www.w3schools.com/html/html5_video.asp">'.esc_html__('Check Support link','pt_theplus').'</a></span></span>'.esc_html__('URL of Video (WebM/Ogg) ', 'pt_theplus')),
							'param_name' => 'columns_video_url_webm',
							'value' => '',
							'dependency' => Array(
								'element' => 'columns_video_variant',
								'value' => array(
									'self-hosted'
								)
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
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
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter Vimeo ID from Vimeo URL. Example: 67628182','pt_theplus').'</span></span>'.esc_html__('Enter Vimeo video ID', 'pt_theplus')),
							'param_name' => 'columns_vimeo_video_id',
							'value' => '',
							'description' => '',
							'dependency' => Array(
								'element' => 'columns_video_variant',
								'value' => array(
									'vimeo'
								)
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						array(
							'type' => 'checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Video optoin using this option.','pt_theplus').'</span></span>'.esc_html__('Extra Options', 'pt_theplus')),
							'param_name' => 'columns_video_opts',
							'value' => array(
								__('Loop', 'pt_theplus') => 'loop',
								__('Muted', 'pt_theplus') => 'muted'
							),
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_video'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						array(
							'type' => 'attach_image',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('We highly Recommend to use Placeholder image Because Some older versions of browser and Some Mobile OS doesn&#39;t support video backgrounds.','pt_theplus').'</span></span>'.esc_html__('Place Holder Image', 'pt_theplus')),
							'param_name' => 'columns_video_poster',
							'value' => '',
							'dependency' => array(
								'element' => 'select_anim',
								'value' => 'bg_video'
							),
							'group' => esc_attr__('Background Options', 'pt_theplus')
						),
						/*--------background video ---------------------------*/
						/*---------background Top layer overlay color---------------------------*/
						
						array(
								'type'        => 'radio_select_image',
								'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select Top layer styles using this option.','pt_theplus').'</span></span>'.esc_html__('Top Layer', 'pt_theplus')),
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
								 'group' => esc_attr__('Texture Background', 'pt_theplus'),
								
							),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip tooltip-bottom"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Color', 'pt_theplus')),
							'param_name' => 'normal_overlay_color',
							"description" => "",
							'group' => esc_attr__('Texture Background', 'pt_theplus'),
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
							'group' => esc_attr__('Texture Background', 'pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
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
							'group' => esc_attr__('Texture Background', 'pt_theplus'),
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
							'value' => array(
								__('Horizontal', 'pt_theplus') => 'horizontal',
								__('Vertical', 'pt_theplus') => 'vertical',
								__('Diagonal', 'pt_theplus') => 'diagonal',
								__('Radial', 'pt_theplus') => 'radial'
							),
							"description" => "",
							'group' => esc_attr__('Texture Background', 'pt_theplus'),
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
							'group' => esc_attr__('Texture Background', 'pt_theplus'),
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'gradient_color'
								)
							)
						),
						array(
							'type' => 'attach_image',
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('This image will be on a repeat mode. Just upload small texture image. Check some texture options.','pt_theplus').'</br><a target="_blank" class="tootip-link" href="http://subtlepatterns.com/">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Upload Image', 'pt_theplus')),
							'param_name' => 'texture_image',
							'group' => esc_attr__('Texture Background', 'pt_theplus'),
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'texture_image'
								)
							)
						),
						array(
							'type' => 'dropdown',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Select opacity of texture image layer.','pt_theplus').'</span></span>'.esc_html__('Opacity', 'pt_theplus')),
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
							'group' => esc_attr__('Texture Background', 'pt_theplus'),
							'dependency' => array(
								'element' => 'overlay_style',
								'value' => array(
									'texture_image'
								)
							)
						),
						/*---------------------------texture_image----------------------*/
						/*---------------------------typography font--------------------*/
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Title Settings', 'pt_theplus'),
						'param_name' => 'extra_effect',
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						'group' => esc_attr__('Typography', 'pt_theplus'),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							'param_name' => 'title_color',
							'value' => '#FFFFFF',
							"edit_field_class" => "vc_col-xs-6",
							 'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							'param_name' => 'title_font_size',
							"edit_field_class" => "vc_col-xs-6",
							'value' => '35px',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							'param_name' => 'title_line_height',
							"edit_field_class" => "vc_col-xs-6",
							'value' => '40px',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							'param_name' => 'title_letter_spacing',
							"edit_field_class" => "vc_col-xs-6",
							'value' => '1px',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Title Custom font family', 'pt_theplus'),
								'param_name' => 'title_use_theme_fonts',
								 "value" => array(
									esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
									esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
								),
								'std' =>  'custom-font-family',
								'group' => esc_attr__('Typography', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'title_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus'),	
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
							'group' => esc_attr__('Typography', 'pt_theplus'),	
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
								'group' => esc_attr__('Typography', 'pt_theplus'),	
						),
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Sub Title Settings', 'pt_theplus'),
						'param_name' => 'extra_effect',
						'group' => esc_attr__('Typography', 'pt_theplus'),
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),
						 array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can select color and Opacity for font using this option.','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							'param_name' => 'subtitle_color',
							'value' => '#FF0000',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							'param_name' => 'subtitle_font_size',
							"edit_field_class" => "vc_col-xs-6",
							'value' => '23px',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							'heading' => __('Sub-Title Line Height', 'pt_theplus'),
							'param_name' => 'subtitle_line_height',
							"edit_field_class" => "vc_col-xs-6",
							'value' => '25px',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							'param_name' => 'subtitle_letter_spacing',
							"edit_field_class" => "vc_col-xs-6",
							'value' => '1px',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Subtitle Custom font family', 'pt_theplus'),
								'param_name' => 'subtitle_use_theme_fonts',
								 "value" => array(
									esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
									esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
								),
								'std' =>  'custom-font-family',
								'group' => esc_attr__('Typography', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'subtitle_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'subtitle_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'subtitle_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'subtitle_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'subtitle_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'subtitle_use_theme_fonts',
									'value' => 'google-fonts',
								),
								'group' => esc_attr__('Typography', 'pt_theplus'),	
						),
						array(
						'type' => 'pt_theplus_heading_param',
						'text' => esc_html__('Breadcrumbs Settings', 'pt_theplus'),
						'param_name' => 'extra_effect',
						'group' => esc_attr__('Typography', 'pt_theplus'),
						'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font size in Pixels using this option. E.g. 14px, 20px, etc.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							'param_name' => 'breadcrumbs_font_size',
							"edit_field_class" => "vc_col-xs-6",
							'value' => '20px',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							'param_name' => 'breadcrumbs_line_height',
							"edit_field_class" => "vc_col-xs-6",
							'value' => '22px',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							'param_name' => 'breadcrumbs_letter_spacing',
							"edit_field_class" => "vc_col-xs-6",
							'value' => '1px',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for breadcrumbs bar text using this option.','pt_theplus').'</span></span>'.esc_html__('Breadcrumbs Bar text Color', 'pt_theplus')),
							'param_name' => 'breadcrumb_bar_text_color',
							'value' =>	'#313131',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Typography', 'pt_theplus')
						),						
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for breadcrumbs bar background using this option.','pt_theplus').'</span></span>'.esc_html__('Breadcrumbs Bar Background Color', 'pt_theplus')),
							'param_name' => 'breadcrumb_bar_bg_color',
							'value' =>	'#fff',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Typography', 'pt_theplus')
						),
					   array(
								'type' => 'dropdown',
								'heading' => '<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Allows you to use custom Google font','pt_theplus').'</span></span>'.esc_html__('Breadcrumb Bar Custom font family', 'pt_theplus'),
								'param_name' => 'bread_use_theme_fonts',
								 "value" => array(
									esc_html__("Custom font family", 'pt_theplus') => "custom-font-family",
									esc_html__("Google fonts", 'pt_theplus') => "google-fonts",
								),
								'std' =>  'custom-font-family',
								'group' => esc_attr__('Typography', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'bread_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'bread_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'bread_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Typography', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'bread_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
								'type' => 'google_fonts',
								'param_name' => 'bread_google_fonts',
								'value' => '',
								'settings' => array(
									'fields' => array(
										'font_family_description' => __( 'Select font family.', 'pt_theplus' ),
										'font_style_description' => __( 'Select font styling.', 'pt_theplus' ),
									),
								),
								'dependency' => array(
									'element' => 'bread_use_theme_fonts',
									'value' => 'google-fonts',
								),
								'group' => esc_attr__('Typography', 'pt_theplus'),	
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
	new ThePlus_header_breadcrumbs;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_header_breadcrumbs'))
	{
		class WPBakeryShortCode_tp_header_breadcrumbs extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
			}
		}
	}
}