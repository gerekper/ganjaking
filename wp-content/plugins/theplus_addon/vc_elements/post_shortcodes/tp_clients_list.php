<?php
// Clients List Elements
if(!class_exists("ThePlus_clients_list")){
	class ThePlus_clients_list{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_clients_list') );
			add_shortcode( 'tp_clients_list',array($this,'tp_clients_list_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_clients_list_scripts' ), 1 );
		}
		function tp_clients_list_scripts() {
			wp_register_style( 'theplus-client-style', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-client-style.css', false, '1.0.0' );
		}
		function tp_clients_list_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
					'client_style'=>'style-1',
					'layout'=>'grid',
					
					'display_category' => '',	 
					'desktop_column'=>'3',
					'tablet_column'=>'6',
					'mobile_column'=>'12',
					'display_post'=>'10',
					'order_by'=>'date',
					'post_sort'=>'DESC',
					'filter_category'=>'',
					'filter_align'=>'text-center',
					'post_options'=>'',
					'load_more_text'=>'Load More',
					'post_load_more'=>'4',
					
					'filter_btn_style'=>'style-1',
					'filter_hover_style'=>'style-1',
					'filter_text_font_size'=>'12px',
					'filter_text_line_height'=>'19px',
					'filter_text_letter_space'=>'0px',
					'filter_text_color'=>'#313131',
					'filter_text_hover_color'=>'#ff214f',
					'filter_color_1'=>'#d3d3d3',
					'filter_color_2'=>'#313131',
					
					'animation_effects'=>'transition.fadeIn',
					'animation_delay'=>'50',
					'animated_column_list'=>'',
					'animation_stagger'=>'150',
					
					'title_size' =>'24px',
					'title_line' => '1.4',
					'title_space' =>'1px',
					'title_use_theme_fonts'=>'custom-font-family',
					'title_font_family'=>'',
					'title_font_weight'=>'400',
					'title_google_fonts'=>'',
					'title_clr' => '#313131',
					'title_hvr_clr' => '#313131',
					'title_on' =>'on',
					
					'subtitle_size' =>'18px',
					'subtitle_line' => '1.4',
					'subtitle_space' =>'1px',
					'subtitle_use_theme_fonts'=>'custom-font-family',
					'subtitle_font_family'=>'',
					'subtitle_font_weight'=>'400',
					'subtitle_google_fonts'=>'',
					'sub_clr' =>'#313131',
					'sub_hvr_clr' =>'#313131',
					'subtitle_on' =>'on',
					
					'desc_size' =>'16px',
					'desc_line' => '1.4',
					'desc_space' =>'1px',
					'desc_weight' =>'400',
					'desc_clr' => '#888888',
					'desc_hvr_clr' => '#888888',
					'desc_on' =>'on',
					
					'bg_clr' => '',
					'bg_hvr_clr' => '',
					'bd_hvr_clr' => '',
					
					'show_arrows'=>'true',
					'show_dots'=>'true',
					'show_draggable'=>'false',
					'slide_loop'=>'false',
					'slide_autoplay'=>'false',
					'autoplay_speed'=>'3000',
					'steps_slide'=>'1',
					'dots_style'=>'style-3',
					'arrows_style'=>'style-1',
					'arrows_position'=>'top-right',
					'carousel_column'=>'4',
					'carousel_tablet_column'=>'3',
					'carousel_mobile_column'=>'2',
					
					'dots_border_color'=>'#000',
					'dots_bg_color'=>'#fff',
					'dots_active_border_color'=>'#000',
					'dots_active_bg_color'=>'#000',
					
					'arrow_bg_color'=>'#c44d48',
					'arrow_icon_color'=>'#fff',
					'arrow_hover_bg_color'=>'#fff',
					'arrow_hover_icon_color'=>'#c44d48',
					'arrow_text_color'=>'#fff',
					
					
					'column_space' =>'',
					'column_space_pading' => '10px',
					
					'el_class' =>'',
					
					), $atts ) );
					wp_enqueue_style( 'theplus-client-style');
					global $paged;
					if ( get_query_var('paged') ) {
						$paged = get_query_var('paged');
					}
					elseif ( get_query_var('page') ) {
						$paged = get_query_var('page');
					}
					else {
						$paged = 1;
					}
					
					if($display_category == '') {
						$display_category = null;
					}		
					
					
					 $post_name=pt_plus_client_post_name();
					 $taxonomy=pt_plus_client_post_category();
					
					$args = array(
					'post_type' => $post_name,
					$taxonomy => $display_category,
					'posts_per_page' => $display_post,
					'paged' => $paged,
					'orderby'	=>$order_by,
					'post_status' =>'publish',
					'order'	=>$post_sort
					);
					$post_qry=new WP_Query($args);
					
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
					$animated_attr .='data-animate-type="'.esc_attr($animation_effects).'"';
					$animated_attr .=' data-animate-delay="'.esc_attr($animation_delay_time).'"';
					
$animated_columns='';
					if($animated_column_list==''){
						$animated_columns='';
					}else if($animated_column_list=='columns'){
						if($layout=='grid' || $layout=='masonry' || $layout=='carousel'){
							$animated_columns='animated-columns';
							$animated_attr .=' data-animate-columns="columns"';
						}else{
							$animated_columns='';
						}
					}else if($animated_column_list=='stagger'){
						if($layout=='grid' || $layout=='masonry' || $layout=='carousel'){
							$animated_columns='animated-columns';
							$animated_attr .=' data-animate-columns="stagger"';
							$animated_attr .=' data-animate-stagger="'.esc_attr($animation_stagger).'"';
						}else{
							$animated_columns='';
						}
					}
					$attr=$data_column='';
					
					if($filter_category=='true'){
						$attr .=' data-filter_btn="filter_btn-'.esc_attr($rand_no).'" ';
						$attr .=' data-filter_btn_style="'.esc_attr($filter_btn_style).'" ';
						$attr .=' data-filter_hover_style="hover-'.esc_attr($filter_hover_style).'" ';
						$attr .=' data-filter_text_font_size="'.esc_attr($filter_text_font_size).'" ';
						$attr .=' data-filter_text_line_height="'.esc_attr($filter_text_line_height).'" ';
						$attr .=' data-filter_text_letter_space="'.esc_attr($filter_text_letter_space).'" ';
						$attr .=' data-filter_text_color="'.esc_attr($filter_text_color).'" ';
						$attr .=' data-filter_text_hover_color="'.esc_attr($filter_text_hover_color).'" ';
						$attr .=' data-filter_color_1="'.esc_attr($filter_color_1).'" ';
						$attr .=' data-filter_color_2="'.esc_attr($filter_color_2).'" ';
						$attr .=' data-enable-isotope="1" ';
						$filter_class=' pt-plus-filter-post-category ';
					}else{
						$attr .=' data-enable-isotope="1" ';
						$filter_class='';
					}
					
					if($layout=='grid'){
						$attr .=' data-layout-type="fitRows" ';
						$isotope=' list-isotope ';
					}else if($layout=='masonry'){
						$attr .=' data-layout-type="masonry" ';
						$isotope=' list-isotope ';
					}else if($layout=='carousel'){
						$isotope=' list-carousel-slick';
					}
					
					$attr .=' data-id="client-list-'.esc_attr($rand_no).'" ';
					$attr .=' data-style="client-'.esc_attr($client_style).'" ';
					
					
					if($layout=='carousel'){
						$attr .=' data-show_arrows="'.esc_attr($show_arrows).'"';
						$attr .=' data-show_dots="'.esc_attr($show_dots).'"';
						$attr .=' data-show_draggable="'.esc_attr($show_draggable).'"';
						$attr .=' data-slide_loop="'.esc_attr($slide_loop).'"';
						$attr .=' data-slide_autoplay="'.esc_attr($slide_autoplay).'"';
						$attr .=' data-autoplay_speed="'.esc_attr($autoplay_speed).'"';
						$attr .=' data-steps_slide="'.esc_attr($steps_slide).'"';
						$attr .=' data-carousel_column="'.esc_attr($carousel_column).'"';
						$attr .=' data-carousel_tablet_column="'.esc_attr($carousel_tablet_column).'"';
						$attr .=' data-carousel_mobile_column="'.esc_attr($carousel_mobile_column).'"';
						$attr .=' data-dots_style="slick-dots '.esc_attr($dots_style).'" ';
						$attr .=' data-arrows_style="'.esc_attr($arrows_style).'" ';
						$attr .=' data-arrows_position="'.esc_attr($arrows_position).'" ';
						
						$attr .=' data-dots_border_color="'.esc_attr($dots_border_color).'" ';
						$attr .=' data-dots_bg_color="'.esc_attr($dots_bg_color).'" ';
						$attr .=' data-dots_active_border_color="'.esc_attr($dots_active_border_color).'" ';
						$attr .=' data-dots_active_bg_color="'.esc_attr($dots_active_bg_color).'" ';
						
						$attr .=' data-arrow_bg_color="'.esc_attr($arrow_bg_color).'" ';
						$attr .=' data-arrow_icon_color="'.esc_attr($arrow_icon_color).'" ';
						$attr .=' data-arrow_hover_bg_color="'.esc_attr($arrow_hover_bg_color).'" ';
						$attr .=' data-arrow_hover_icon_color="'.esc_attr($arrow_hover_icon_color).'" ';
						$attr .=' data-arrow_text_color="'.esc_attr($arrow_text_color).'" ';
						
					}
					$space_attr ='';
				if($title_use_theme_fonts=='google-fonts'){
					$text_font_data = pt_plus_getFontsData( $title_google_fonts );
					$title_font_family = pt_plus_googleFontsStyles( $text_font_data );  
					$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
				}elseif($title_use_theme_fonts=='custom-font-family'){
					$title_font_family='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
				}else{
					$title_font_family='';
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
					
					if($column_space == 'on'){
						$column_padding=$column_space_pading;	
					}else{
						$column_padding="0px";
					}
					
					
					if(!empty($display_category)){
						$array_category=explode(',',$display_category);
					}
					$arrow_class='';
					if($arrows_style=='style-4' || $arrows_style=='style-5'){
						$arrow_class=$arrows_position;
					}
					$uid=uniqid('client_grid');
					$lazy_load_class='';
					if($post_options=='lazy_load'){
						$lazy_load_class='pt_theplus_lazy_load';
					}
					$client_listing = '<div id="clients-post-list" class="clients-list '.esc_attr($isotope).' '.esc_attr($lazy_load_class).' '.esc_attr($arrow_class).'  '.esc_attr($el_class).' clients-'.esc_attr($client_style).' '.esc_attr($filter_class).' '.esc_attr($animated_class).'  client-list-'.esc_attr($rand_no).' '.esc_attr($uid).'" data-uid="'.esc_attr($uid).'" '.$animated_attr.' '.$attr.'>';
					
					if($filter_category=='true'){
					$terms = get_terms( array('taxonomy' => $taxonomy, 'hide_empty' => true) );
					$all_category=$category_post_count='';
						if($filter_btn_style=='style-1'){
							$count=$post_qry->post_count;
							$all_category='<span class="all_post_count">'.esc_html($count).'</span>';
						}
						if($filter_btn_style=='style-2' || $filter_btn_style=='style-3'){
							$count=$post_qry->post_count;
							$category_post_count='<span class="all_post_count">'.esc_html($count).'</span>';
						}
						
							$client_listing .='<div class="post-filter-data '.esc_attr($filter_btn_style).' '.$filter_align.'">';
								if($filter_btn_style=='style-4'){
									$client_listing .= '<span class="filters-toggle-link">'.esc_html__('Filters','pt_theplus').'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
								}
								$client_listing .='<ul class="category-filters '.esc_attr($filter_btn_style).' hover-'.esc_attr($filter_hover_style).'">';
									$client_listing .= '<li><a href="#" class="filter-category-list active all" data-filter="*" >'.$category_post_count.'<span data-hover="'.esc_attr('All','pt_theplus').'">'.esc_html__('All','pt_theplus').'</span>'.$all_category.'</a></li>';
									
									if ( $terms != null ){
										foreach( $terms as $term ) {
											$category_post_count='';
											if($filter_btn_style=='style-2' || $filter_btn_style=='style-3'){
												$category_post_count='<span class="all_post_count">'.esc_html($term->count).'</span>';
											}
											if(!empty($array_category)){
												if(in_array($term->slug,$array_category)){
													$client_listing .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
													unset($term);
												}
											}else{
												$client_listing .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
												unset($term);
											}
										}
									}
								$client_listing .= '</ul>';
							$client_listing .= '</div>';
					}
					$client_listing .= '<div class="post-inner-loop clients-'.esc_attr($rand_no).'">';
					$css_loop='';
					if($post_qry->have_posts()) :
					while($post_qry->have_posts()) : $post_qry->the_post(); 
					
					
						$category_filter='';
						if($filter_category=='true'){				
							$terms = get_the_terms( $post_qry->ID,$taxonomy);
							if ( $terms != null ){
								foreach( $terms as $term ) {
									$category_filter .=' '.esc_attr($term->slug).' ';
									unset($term);
								}
							}
						}
						$uid_loop=uniqid('client');
						$client_listing .= '<div class="grid-item client-grid-item '.esc_attr($desktop_class).' '.esc_attr($tablet_class).' '.esc_attr($mobile_class).' '.esc_attr($animated_columns).' '.$category_filter.'">';
							
							if(!empty($client_style)){
							$clients_bg_color= get_post_meta(get_the_ID(), 'theplus_clients_bg_color', true );
							$clients_bg_hover_color= get_post_meta(get_the_ID(), 'theplus_clients_bg_hover_color', true );
							$clients_title_hvr_color= get_post_meta(get_the_ID(), 'theplus_clients_title_hvr_color', true );
							$clients_title_color= get_post_meta(get_the_ID(), 'theplus_clients_title_color', true );
							$clients_sub_color= get_post_meta(get_the_ID(), 'theplus_clients_sub_color', true );
							$clients_sub_hvr_color= get_post_meta(get_the_ID(), 'theplus_clients_sub_hvr_color', true );
							$clients_desc_color= get_post_meta(get_the_ID(), 'theplus_clients_desc_color', true );
							$clients_desc_hvr_color= get_post_meta(get_the_ID(), 'theplus_clients_desc_hvr_color', true );
							$clients_border_hover= get_post_meta( get_the_ID(), 'theplus_clients_border_hover_color', true );
							ob_start();
								include THEPLUS_PLUGIN_PATH. 'vc_elements/clients/client-'.$client_style.'.php'; 
							$client_listing .= ob_get_contents();							
							ob_end_clean();
							if($client_style=='style-2' || $client_style=='style-4' || $client_style=='style-5' || $client_style=='style-6'){
								$css_loop .= '.'.esc_js($uid_loop).'{background: '.esc_js($clients_bg_color).' !important; } .'.esc_js($uid_loop).':hover,.'.esc_js($uid_loop).'.pt-plus-client-list-style-content.client-content-2 .blur{background: '.esc_js($clients_bg_hover_color).' !important;}';
							}
							if($client_style=='style-3'){
								$css_loop .='.'.esc_js($uid_loop).'.client-content-3 .post-content{border-color: '.esc_js($clients_border_hover).'  !important; } .'.esc_js($uid_loop).'.client-content-3 .post-content:hover:before{border-top-color: '.esc_js($clients_border_hover).'  !important; border-right-color :'.esc_js($clients_border_hover).'  !important;} .'.esc_js($uid_loop).'.client-content-3 .post-content:hover:after{ border-bottom-color:  '.esc_js($clients_border_hover).'  !important;  border-left-color: '.esc_js($clients_border_hover).'  !important; }';
							}
							if($title_on=='on' && $title_on!=''){
								$css_loop .='.'.esc_js($uid_loop).'.pt-plus-client-list-style-content .client-title a{color:'.esc_js($clients_title_color).' !important}.'.esc_js($uid_loop).'.pt-plus-client-list-style-content:hover .client-title a{color:'.esc_js($clients_title_hvr_color).' !important}';
							}
							if($subtitle_on=='on' && $subtitle_on!=''){
								$css_loop .='.'.esc_js($uid_loop).'.pt-plus-client-list-style-content .client-subtitle a{color:'.esc_js($clients_sub_color).' !important}.'.esc_js($uid_loop).'.pt-plus-client-list-style-content:hover .client-subtitle a{color:'.esc_js($clients_sub_hvr_color).' !important}';
							}
							
							if($desc_on=='on' && $desc_on!=''){
								$css_loop .='.'.esc_js($uid_loop).'.pt-plus-client-list-style-content .client-desc p{color:'.esc_js($clients_desc_color).' !important}.'.esc_js($uid_loop).'.pt-plus-client-list-style-content:hover .client-desc p{color:'.esc_js($clients_desc_hvr_color).' !important}';
							}
							}
					
						$client_listing .= '</div>';
					endwhile;
					endif;
					$client_listing .= '</div>';
					
					if($post_options=='pagination'){
						$client_listing .= pt_plus_pagination($post_qry->max_num_pages,'4');
					}
					
					if($post_options=='load_more'){
							$client_listing .= '<div class="ajax_load_more">';
							$client_listing .= '<a class="post-load-more" data-load="clients" data-post_type="'.$post_name.'" data-texonomy_category="'.$taxonomy.'" data-load-class="clients-'.esc_attr($rand_no).'" data-layout="'.esc_attr($layout).'" data-style="'.esc_attr($client_style).'" data-desktop-column="'.esc_attr($desktop_column).'" data-tablet-column="'.esc_attr($tablet_column).'" data-mobile-column="'.esc_attr($mobile_column).'" data-category="'.esc_attr($display_category).'" data-order_by="'.esc_attr($order_by).'" data-post_sort="'.esc_attr($post_sort).'" data-filter_category="'.esc_attr($filter_category).'" data-display_post="'.esc_attr($display_post).'" data-animated_columns="'.esc_attr($animated_columns).'" data-post_load_more="'.esc_attr($post_load_more).'" data-page="1" data-total_page="'.esc_attr($post_qry->max_num_pages).'">'.esc_html($load_more_text).'</a>';
							$client_listing .= '</div>';
					}
					if($post_options=='lazy_load'){
						$client_listing .= '<div class="ajax_lazy_load">';
							$client_listing .= '<a class="post-lazy-load" data-load="clients" data-post_type="'.$post_name.'" data-texonomy_category="'.$taxonomy.'" data-load-class="clients-'.esc_attr($rand_no).'" data-layout="'.esc_attr($layout).'" data-style="'.esc_attr($client_style).'" data-desktop-column="'.esc_attr($desktop_column).'" data-tablet-column="'.esc_attr($tablet_column).'" data-mobile-column="'.esc_attr($mobile_column).'" data-category="'.esc_attr($display_category).'" data-order_by="'.esc_attr($order_by).'" data-post_sort="'.esc_attr($post_sort).'" data-filter_category="'.esc_attr($filter_category).'" data-display_post="'.esc_attr($display_post).'" data-animated_columns="'.esc_attr($animated_columns).'" data-post_load_more="'.esc_attr($post_load_more).'" data-page="1" data-total_page="'.esc_attr($post_qry->max_num_pages).'"><img src="'.THEPLUS_PLUGIN_URL. 'vc_elements/images/lazy_load.gif" /></a>';
							$client_listing .= '</div>';
					}
					
					$client_listing .= '</div>';
					$css_rule='';
					$css_rule .= '<style >';
					$css_rule .= '.'.esc_js($uid).'.clients-'.esc_js($client_style).' .grid-item{padding : '.esc_js($column_padding).'; }';	
					if($title_on!='on' && $title_on==''){
						$css_rule .= '.'.esc_js($uid).' .client-title, .'.esc_js($uid).' .client-title a{display:none;}';
					}else{
						$css_rule .= '.'.esc_js($uid).' .client-title, .'.esc_js($uid).' .client-title a{font-size: '.esc_js($title_size).';line-height: '.esc_js($title_line).';letter-spacing: '.esc_js($title_space).';color : '.esc_js($title_clr).';'.esc_js($title_font_family).'}.'.esc_js($uid).' .pt-plus-client-list-style-content:hover .client-title a{color:'.esc_js($title_hvr_clr).'}';
					}
					if($subtitle_on!='on' && $subtitle_on==''){
						$css_rule .= '.'.esc_js($uid).' .client-subtitle,.'.esc_js($uid).' .client-subtitle a{display:none;}';
					}else{
						$css_rule .= '.'.esc_js($uid).' .client-subtitle,.'.esc_js($uid).' .client-subtitle a{font-size: '.esc_js($subtitle_size).';line-height: '.esc_js($subtitle_line).';letter-spacing: '.esc_js($subtitle_space).';color : '.esc_js($sub_clr).';'.esc_js($subtitle_font_family).'}.'.esc_js($uid).' .pt-plus-client-list-style-content:hover .client-subtitle a{color:'.esc_js($sub_hvr_clr).'}';
					}
					if($desc_on=='on' && $desc_on==''){
						$css_rule .= '.'.esc_js($uid).' .client-desc,.'.esc_js($uid).' .client-desc p{display:none;}';
					}else{
						$css_rule .= '.'.esc_js($uid).' .client-desc p{font-size: '.esc_js($desc_size).';font-weight: '.esc_js($desc_weight).';line-height: '.esc_js($desc_line).';letter-spacing: '.esc_js($desc_space).';color: '.esc_js($desc_clr).';}.'.esc_js($uid).' .pt-plus-client-list-style-content:hover .client-desc p{color:'.esc_js($desc_hvr_clr).'}';
					}
					$css_rule .= '.'.esc_js($uid).' .pt-plus-client-list-style-content{';
					if($bg_clr !=''){	
						$css_rule .= 'background: '.esc_js($bg_clr).';';
					}
					$css_rule .= '} .'.esc_js($uid).' .pt-plus-client-list-style-content:hover ,.'.esc_js($uid).' .pt-plus-client-list-style-content.client-content-2 .blur{';
					if($bg_hvr_clr !=''){						
						$css_rule .= 'background: '.esc_js($bg_hvr_clr).';';
					}					
					$css_rule .= '} .'.esc_js($uid).' .client-content-3 .post-content{';
					if($bd_hvr_clr !=''){
						$css_rule .= 'border-color: '.esc_js($bd_hvr_clr).' ;';
					}
					$css_rule .= '} .'.esc_js($uid).' .client-content-3 .post-content:hover:before{';
					if($bd_hvr_clr !=''){
						$css_rule .= 'border-top-color: '.esc_js($bd_hvr_clr).' ; border-right-color :'.esc_js($bd_hvr_clr).';';
					}					
					$css_rule .= '} .'.esc_js($uid).' .client-content-3 .post-content:hover:after{';
					if($bd_hvr_clr !=''){
						$css_rule .= 'border-bottom-color:  '.esc_js($bd_hvr_clr).';  border-left-color: '.esc_js($bd_hvr_clr).';';
					}
					$css_rule .= '}';
					$css_rule .=$css_loop;
					$css_rule .= '</style>';
					wp_reset_postdata();
					return $css_rule.$client_listing;
		}
		function init_tp_clients_list(){
			if(function_exists("vc_map"))
			{
			$taxonomy=pt_plus_client_post_category();
				$client_post=pt_plus_get_option('post_type','client_post_type');
						if((!isset($client_post) || $client_post=='') || (!empty($client_post) && $client_post=='disable')){
							$custom_post_type=array(
									"type" => "pt_theplus_post_notice",
									"heading" => "",
									"param_name" => "custom_post_notice",
									"description" => "",
									"admin_label" => false,
									"show_notice" => 'yes',
									"value" => '',
								);
						}else{
							$custom_post_type=array(
									"type" => "pt_theplus_post_notice",
									"heading" => "",
									"param_name" => "custom_post_notice",
									"description" => "",
									"admin_label" => false,
									"show_notice" => '',
									"value" => '',
								);
						}
				vc_map(array(
					"name" => __("Client Post", 'pt_theplus'),
					"base" => "tp_clients_list",
					"icon" => "tp-client-list",
					"category" => __("The Plus", "pt_theplus"),
					"description" => __('Various Listing and Carousel Options', 'pt_theplus'),
					"params" => array(
					$custom_post_type,
					array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Clients Style', 'pt_theplus'),
								'param_name'  => 'client_style',
								'admin_label' => true,
								'simple_mode' => false,
								'value'		=> 'style-1',
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/client_post/style-1.jpg'
									),
									'style-2' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/client_post/style-2.jpg'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/client_post/style-3.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/client_post/style-4.jpg'
									),
									'style-5' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/client_post/style-5.jpg'
									),
									'style-6' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/client_post/style-6.jpg'
									),
								),
								
							),
						 array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Listing Layout', 'pt_theplus'),
								'param_name'  => 'layout',
								'admin_label' => true,
								'simple_mode' => false,
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
									'carousel' => array(
										'tooltip' => esc_attr__('Carousel Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/carousel.jpg'
									),
								),
								),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Clients Setting', 'pt_theplus'),
							'param_name'		=> 'post_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
								'type' => 'pt_theplus_taxonomy_multicheck',
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose categories from which you want to show posts by marking one or multiple.','pt_theplus').'</span></span>'.esc_html__('Choose Categories', 'pt_theplus')),
								'param_name' => 'display_category',
								'taxonomy' => $taxonomy,
								'edit_field_class' => 'vc_column vc_col-sm-12 pt-plus-taxonomy-multicheck',
								'group' => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
							"type" => "textfield",
							"admin_label" => true,
							"heading" => __("Maximum Posts", 'pt_theplus'),
							"param_name" => "display_post",
							"value" => '10',
							"description" => __("Please enter number of posts you want to display.", 'pt_theplus'),
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => __("Order By", 'pt_theplus'),
							"param_name" => "order_by",
							"value" => array(
								'Date' => 'date',
								'Order by post ID' => 'ID',
								'Title' => 'title',
								'Last modified date' => 'modified',
								'Random order' => 'rand'
							),
							'std' => 'date',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							"type" => "dropdown",
							"heading" => __("Sorting Order", 'pt_theplus'),
							"param_name" => "post_sort",
							"value" => array(
								'Descending' => 'DESC',
								'Ascending' => 'ASC'
							),
							'std' => 'DESC',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Columns Setting', 'pt_theplus'),
							'param_name'		=> 'columns_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Content', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("grid","masonry"),
							),
						),
					   array(
							"type"        => "dropdown",
							"heading"     => __("Desktop Columns", 'pt_theplus'),
							"param_name"  => "desktop_column",
							"admin_label" => false,
							"value"       => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'6 column' => '2',
								'12 column' => '1',
								),
							'std' =>'3',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array("grid","masonry"),
							),
							"group" => esc_attr__('Content', 'pt_theplus'), 
						),
						
						array(
							"type"        => "dropdown",
							"heading"     => __("Tablet Columns", 'pt_theplus'),
							"param_name"  => "tablet_column",
							"admin_label" => false,
							"value"       => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'6 column' => '2',
							),
							'std' =>'2',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array("grid","masonry"),
							), 
							"group" => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
							"type"        => "dropdown",
							"heading"     => __("Mobile Columns", 'pt_theplus'),
							"param_name"  => "mobile_column",
							"admin_label" => false,
							"value"       => array(
								'1 column' => '12',
								'2 column' => '6',
								'3 column' => '4',
								'4 column' => '3',
								'6 column' => '2',
								),
							'std' =>'12',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array("grid","masonry"),
							), 
							"group" => esc_attr__('Content', 'pt_theplus'), 
						),	
							
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Extra Options', 'pt_theplus'),
							'param_name'		=> 'extra_settings',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Content', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("grid","masonry","metro"),
							),
						),
						
						array(
						'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn On or Off Category wise Filtration option using this option.','pt_theplus').'</span></span>'.esc_html__('Category Wise Filter', 'pt_theplus')),
							'param_name' => 'filter_category',
							'description' => '',
							'value' => 'false',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
								"edit_field_class" => "vc_col-xs-6",
							"group" => esc_attr__('Content', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("grid","masonry","metro"),
							),
						),		
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose alignment position of Filter block using this option.','pt_theplus').'</span></span>'.esc_html__('Filter Block Alignment', 'pt_theplus')),
							"param_name" => "filter_align",
							"value" => array(
								__("Left", "pt_theplus") => "text-left",
								__("Center", "pt_theplus") => "text-center",
								__("Right", "pt_theplus") => "text-right",
							),    
							"std" =>'text-center',
							"description" => "",  
							'dependency' => array('element' => 'filter_category','value' => array('true')),
							"edit_field_class" => "vc_col-xs-6",
							"group" => esc_attr__('Content', 'pt_theplus'), 
							
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose more post loading using this option.','pt_theplus').'</span></span>'.esc_html__('More Post Loading Options', 'pt_theplus')),
							"param_name" => "post_options",
							"value" => array(
								__("Select Options", "pt_theplus") => "",
								__("Pagination", "pt_theplus") => "pagination",
								__("Load More", "pt_theplus") => "load_more",
								__("Lazy Load", "pt_theplus") => "lazy_load",
							),
							"group" => esc_attr__('Content', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("grid","masonry","metro"),
							),
						),
						
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set Button Text of Load More functionality from here.','pt_theplus').'</span></span>'.esc_html__('Button Text', 'pt_theplus')),
								"param_name" => "load_more_text",
								"value" => 'Load More',
							   "dependency" => array(
									'element' => "post_options",
									'value' => 'load_more',
								),
								"group" => esc_attr__('Content', 'pt_theplus'), 
								
							),
							
						array(
								"type" => "textfield",
								'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set number of post needs to be add on press of button for load more.','pt_theplus').'</span></span>'.esc_html__('More Posts on Click', 'pt_theplus')),
								"param_name" => "post_load_more",
								"value" => '4',
							   "dependency" => array(
									'element' => "post_options",
									'value' => 'load_more',
								),
								'group' => esc_attr__('Content', 'pt_theplus'), 
								
							),	
							
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Display Setting', 'pt_theplus'),
							'param_name'		=> 'display_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
						'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On Titles Section of Clients Section using this option.','pt_theplus').'</span></span>'.esc_html__('Clients Title', 'pt_theplus')), 
							'param_name' => 'title_on',
							'value' => 'on',
							'options' => array(
								'on' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
						'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On Sub Titles Section of Clients Section using this option.','pt_theplus').'</span></span>'.esc_html__('Clients Sub Title', 'pt_theplus')), 
							'param_name' => 'subtitle_on',
							'value' => 'on',
							'options' => array(
								'on' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus'), 
						),
						array(
						'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On Sub Description Section of Clients Section using this option.','pt_theplus').'</span></span>'.esc_html__('Clients Description', 'pt_theplus')), 
							'param_name' => 'desc_on',
							'value' => 'on',
							'options' => array(
								'on' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus'), 
						),	
						
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Carousel Setting', 'pt_theplus'),
							'param_name'		=> 'carousel_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Carousel', 'pt_theplus'),
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							),
						),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Number of carousel Columns in Desktop screen size( More than 768px width).','pt_theplus').'</span></span>'.esc_html__('Desktop Columns', 'pt_theplus')), 
							"param_name"  => "carousel_column",
							"admin_label" => false,
							"value"       => array(
								'1 column' => '1',
								'2 column' => '2',
								'3 column' => '3',
								'4 column' => '4',
								'5 column' => '5',
								'6 column' => '6',
								),
							'std' =>'4',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
						),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Number of carousel Columns in Tablet screen size( In between 768px and 480px width).','pt_theplus').'</span></span>'.esc_html__('Tablet Columns', 'pt_theplus')), 
							"param_name"  => "carousel_tablet_column",
							"admin_label" => false,
							"value"       => array(
								'1 column' => '1',
								'2 column' => '2',
								'3 column' => '3',
								'4 column' => '4',
								'5 column' => '5',
								'6 column' => '6',
								),
							'std' =>'3',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
						),
						array(
							"type"        => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Number of carousel Columns in Mobile screen size( Less than 480px width).','pt_theplus').'</span></span>'.esc_html__('Mobile Columns', 'pt_theplus')), 
							"param_name"  => "carousel_mobile_column",
							"admin_label" => false,
							"value"       => array(
								'1 column' => '1',
								'2 column' => '2',
								'3 column' => '3',
								'4 column' => '4',
								'5 column' => '5',
								'6 column' => '6',
								),
							'std' =>'2',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can display or hide arrows of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Arrows', 'pt_theplus')),
							'param_name' => 'show_arrows',
							'description' => '',
							'value' => 'true',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
								"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							),
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can display or hide navigation dots of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Navigation Dots', 'pt_theplus')),
							'param_name' => 'show_dots',
							'description' => "",
							'value' => 'true',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
								"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							),
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Turn on or Off Mouse Draggable functionality of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Draggable', 'pt_theplus')),
							'param_name' => 'show_draggable',
							'description' => "",
							'value' => 'false',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							),
						),
						array(
						'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Loop or Infinite style of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Infinite Mode', 'pt_theplus')),
							'param_name' => 'slide_loop',
							'description' => "",
							'value' => 'false',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							),
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Turn on Auto play functionality of Carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Auto Play', 'pt_theplus')),
							'param_name' => 'slide_autoplay',
							'description' => "",
							'value' => 'false',
							'options' => array(
								'true' => array(
										'label' => '',
										'on' => 'Yes',
										'off' => 'No',
									),
								),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							),
						),
						array(
							  "type"        => "textfield",
							  'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Enter speed of autoplay carousel functionality. e.g. 2000,3000 etc.','pt_theplus').'</span></span>'.esc_html__('Autoplay Speed', 'pt_theplus')),
							  "param_name"  => "autoplay_speed",
							  "value"       => '3000',
							  "description" => "",
							  "edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							),
							"dependency" => array(
								"element" => "slide_autoplay",
								"value" => array("true"),
							),
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Select option of column scroll on previous or next in carousel.','pt_theplus').'</span></span>'.esc_html__('Next Previous', 'pt_theplus')),
							"param_name" => "steps_slide",
						   "value" => array(
							__("One Column", "pt_theplus") => "1",
							__("All Visible Columns", "pt_theplus") => "2",
							),    
							"std" =>'1',
							"description" => "", 
							  "edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
						),
						array(
								'type'        => 'radio_select_image',
								'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('You can select styles of navigation dots using this option.', 'pt_theplus') . '</span></span>' . esc_html__('Navigation Dots Style', 'pt_theplus')),
								'param_name'  => 'dots_style',
								'simple_mode' => false,
								'value' => 'style-3',
								'options'     => array(
									'style-3' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-1.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-2.jpg'
									),
									'style-6' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-3.jpg'
									),
									'style-7' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-4.jpg'
									),
									'style-10' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-5.jpg'
									),
									'style-9' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-6.jpg'
									),
									'style-11' => array(
										'tooltip' => esc_attr__('Style-7','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-7.jpg'
									),
								),
								'group' => esc_attr__('Carousel', 'pt_theplus'),
								"dependency" => array(
									"element" => "layout",
									"value" => array(
										"carousel"
									)
								),
								"dependency" => array(
									"element" => "show_dots",
									"value" => array(
										"true"
									)
								)
							),
						
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for navigation dot border using this option.','pt_theplus').'</span></span>'.esc_html__('Navigation Dots Border Color', 'pt_theplus')),
							'param_name' => 'dots_border_color',			
							'value' =>'#000',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "show_dots",
								"value" => array("true"),
							), 
							"dependency" => array(
								"element" => "dots_style",
								"value" => array("style-1","style-2","style-3","style-4","style-6","style-7","style-10"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for navigation dot background using this option.','pt_theplus').'</span></span>'.esc_html__('Navigation Dots Background Color', 'pt_theplus')),
							'param_name' => 'dots_bg_color',			
							'value' =>'#fff',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "show_dots",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "dots_style",
								"value" => array("style-1","style-2","style-4","style-5","style-6","style-7","style-8","style-9","style-11","style-12","style-13"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active navigation dot Border using this option.','pt_theplus').'</span></span>'.esc_html__('Active Navigation Dots Border Color', 'pt_theplus')),
							'param_name' => 'dots_active_border_color',			
							'value' =>'#000',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "show_dots",
								"value" => array("true"),
							), 
							"dependency" => array(
								"element" => "dots_style",
								"value" => array("style-1","style-4","style-7","style-10"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for active navigation dot background using this option.','pt_theplus').'</span></span>'.esc_html__('Active Navigation Dots Background Color', 'pt_theplus')),
							'param_name' => 'dots_active_bg_color',			
							'value' =>'#000',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "show_dots",
								"value" => array("true"),
							), 
							"dependency" => array(
								"element" => "dots_style",
								"value" => array("style-1","style-4","style-5","style-7","style-8","style-9","style-11","style-12","style-13"),
							),
						),
						 array(
								'type'        => 'radio_select_image',
								'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('You can select styles of navigation dots using this option.', 'pt_theplus') . '</span></span>' . esc_html__('Arrow Style', 'pt_theplus')),
								'param_name'  => 'arrows_style',
								'simple_mode' => false,
								"admin_label" => false,
								'value' => 'style-1',
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-1.jpg'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-2.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-3.jpg'
									),
									'style-5' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-4.jpg'
									),
									'style-6' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-5.jpg'
									),
									'style-7' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/ts-navigation/ts-dot-navigation-style-6.jpg'
									),
								),
								'group' => esc_attr__('Carousel', 'pt_theplus'),
								"dependency" => array(
									"element" => "layout",
									"value" => array(
										"carousel"
									)
								),
								"dependency" => array(
								"element" => "show_arrows",
								"value" => array(
									"true"
									)
								)
							),
						array(
							"type" => "dropdown",
							"heading" => __("Arrow Position", "pt_theplus"),
							"param_name" => "arrows_position",
							"value" => array(
								__("Top-Right", "pt_theplus") => "top-right",
								__("Bottom-Left", "pt_theplus") => "bottm-left",
								__("Bottom-Center", "pt_theplus") => "bottom-center",
								__("Bottom-Right", "pt_theplus") => "bottom-right",
							),    
							"std" =>'top-right',
							"description" => "", 
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							), 
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-4","style-5","style-8"),
							),
							
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for arrow background using this option.','pt_theplus').'</span></span>'.esc_html__('Arrow Background Color', 'pt_theplus')),
							'param_name' => 'arrow_bg_color',			
							'value' =>'#c44d48',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-1","style-3","style-4","style-5","style-7"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for arrow icon using this option.','pt_theplus').'</span></span>'.esc_html__('Arrow Icon Color', 'pt_theplus')),
							'param_name' => 'arrow_icon_color',			
							'value' =>'#fff',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-1","style-3","style-4","style-5","style-6","style-7"),
							),
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for arrow hover background using this option.','pt_theplus').'</span></span>'.esc_html__('Arrow Hover Background Color', 'pt_theplus')),
							'param_name' => 'arrow_hover_bg_color',			
							'value' =>'#fff',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-1","style-3","style-4","style-5"),
							),
						),
						
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for arrow hover icon using this option.','pt_theplus').'</span></span>'.esc_html__('Arrow Hover Icon Color', 'pt_theplus')),
							'param_name' => 'arrow_hover_icon_color',			
							'value' =>'#c44d48',
							"edit_field_class" => "vc_col-xs-6",
							'group' => esc_attr__('Carousel', 'pt_theplus'), 
							"dependency" => array(
								"element" => "layout",
								"value" => array("carousel"),
							), 
							"dependency" => array(
								"element" => "show_arrows",
								"value" => array("true"),
							),
							"dependency" => array(
								"element" => "arrows_style",
								"value" => array("style-1","style-3","style-4","style-5","style-6","style-7"),
							),
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Title Setting', 'pt_theplus'),
							'param_name'		=> 'title_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus'), 
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "title_size",
							"value" => '24px',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "title_line",
							"value" => '1.4',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "title_space",
							"value" => '1px',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
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
								'group' => esc_attr__('Styling', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'title_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styling', 'pt_theplus'),	
							'dependency' => array(
									'element' => 'title_use_theme_fonts',
									'value' => 'custom-font-family',
								),
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add font weight using this Option. E.g. 200,400,700,900 etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							'param_name' => 'title_font_weight',
							'value' => __('400','pt_theplus'),
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styling', 'pt_theplus'),	
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
								'group' => esc_attr__('Styling', 'pt_theplus'),	
						),	
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "title_clr",
							"value" => '#313131',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Font Hover Color', 'pt_theplus')),
							"param_name" => "title_hvr_clr",
							"value" => '#313131',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Subtitle Setting', 'pt_theplus'),
							'param_name'		=> 'title_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus'), 
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "subtitle_size",
							"value" => '18px',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "subtitle_line",
							"value" => '1.4',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "subtitle_space",
							"value" => '1px',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
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
								'group' => esc_attr__('Styling', 'pt_theplus'),	
						),
						array(
							'type' => 'textfield',
							'heading' =>  __('<span class="pt_theplus-vc-toolip "><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Custom Font family using this Option. E.g. Arial,Open sans etc.','pt_theplus').'</span></span>'.esc_html__('Font Family', 'pt_theplus')),
							'param_name' => 'subtitle_font_family',
							'value' => "",
							'edit_field_class' => 'vc_col-xs-6',
							'description' => '',
							'group' => esc_attr__('Styling', 'pt_theplus'),	
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
							'group' => esc_attr__('Styling', 'pt_theplus'),	
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
								'group' => esc_attr__('Styling', 'pt_theplus'),	
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "sub_clr",
							"value" => '#313131',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Font Hover Color', 'pt_theplus')),
							"param_name" => "sub_hvr_clr",
							"value" => '#313131',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							'type'				=> 'pt_theplus_heading_param',
							'text'				=> esc_html__('Description Setting', 'pt_theplus'),
							'param_name'		=> 'title_setting',
							'edit_field_class'	=> 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus'), 
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "desc_size",
							"value" => '16px',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Line Height in Pixels using this Option. E.g. 12px, 10px, etc','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "desc_line",
							"value" => '1.4',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "desc_space",
							"value" => '1px',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Font Weight using this Option. E.g. 400, 700, etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							"param_name" => "desc_weight",
							"value" => '400',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							"param_name" => "desc_clr",
							"value" => '#888888',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Font Hover Color', 'pt_theplus')),
							"param_name" => "desc_hvr_clr",
							"value" => '#888888',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Background Setting', 'pt_theplus'),
							'param_name' => 'back_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus'),
						   
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
							"param_name" => "bg_clr",
							"value" => '',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
							'dependency' => array(
								'element' => 'client_style',
								'value' => array(
									'style-2','style-4','style-5','style-6',
								)
							)
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Backgroundt Hover Color', 'pt_theplus')),
							"param_name" => "bg_hvr_clr",
							"value" => '',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
							'dependency' => array(
								'element' => 'client_style',
								'value' => array(
									'style-2','style-4','style-5','style-6',
								)
							)
						),
						array(
							"type" => "colorpicker",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add font size in Pixels using this option. E.g. 14px, 20px, etc','pt_theplus').'</span></span>'.esc_html__('Border Hover Color', 'pt_theplus')),
							"param_name" => "bd_hvr_clr",
							"value" => '',
							"description" => "",
							"group" =>'Styling',
							"edit_field_class" => "vc_col-xs-6",
							 'dependency' => array(
								'element' => 'client_style',
								'value' => array(
									'style-3'
								)
							)
						),
						 array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Category Filter Setting', 'pt_theplus'),
							'param_name' => 'filter_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus'),
							'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							)
						),
						array(
							"type" => "dropdown",
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Choose Filter Section Style from this Options.', 'pt_theplus') . '</span></span>' . esc_html__('Filter Section Style ', 'pt_theplus')),
							"param_name" => "filter_btn_style",
							"value" => array(
								__("Style-1", "pt_theplus") => "style-1",
								__("Style-2", "pt_theplus") => "style-2",
								__("Style-3", "pt_theplus") => "style-3",
								__("Style-4", "pt_theplus") => "style-4"
							),
							"std" => 'style-1',
							'edit_field_class' => 'vc_col-xs-6',
							"description" => "",
							'group' => esc_attr__('Styling', 'pt_theplus'),
							'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							)
						),
						array(
							"type" => "dropdown",
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Choose Filter Hover Style from this Options.', 'pt_theplus') . '</span></span>' . esc_html__('Filter Hover Style ', 'pt_theplus')),
							"param_name" => "filter_hover_style",
							"value" => array(
								__("Style-1", "pt_theplus") => "style-1",
								__("Style-2", "pt_theplus") => "style-2",
								__("Style-3", "pt_theplus") => "style-3",
								__("Style-4", "pt_theplus") => "style-4"
							),
							"std" => 'style-1',
							'edit_field_class' => 'vc_col-xs-6',
							"description" => "",
							'group' => esc_attr__('Styling', 'pt_theplus'),
							'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Choose Category Text of Filter&#39;s Font size for using this option.', 'pt_theplus') . '</span></span>' . esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "filter_text_font_size",
							"value" => '12px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus'),
							 'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Choose Category Text of Filter&#39;s Line Height for using this option.', 'pt_theplus') . '</span></span>' . esc_html__('Line Height', 'pt_theplus')),
							"heading" => __("Line Height", 'pt_theplus'),
							"param_name" => "filter_text_line_height",
							"value" => '19px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus'),
							 'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Choose Category Text of Filter&#39;s Letter Spacing for using this option.', 'pt_theplus') . '</span></span>' . esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "filter_text_letter_space",
							"value" => '0px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus'),
							 'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Choose Category Text of Filter Section&#39;s Color.', 'pt_theplus') . '</span></span>' . esc_html__('Filter Category Text Color', 'pt_theplus')),
							'param_name' => 'filter_text_color',
							"description" => "",
							'value' => '#313131',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Styling', 'pt_theplus'),
							'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Choose Category Text of Filter Section&#39;s Color.', 'pt_theplus') . '</span></span>' . esc_html__('Filter Category Text Hover Color', 'pt_theplus')),
							'param_name' => 'filter_text_hover_color',
							"description" => "",
							'value' => '#ff214f',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Styling', 'pt_theplus'),
						   
							'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Choose Category Text Color 1.', 'pt_theplus') . '</span></span>' . esc_html__('Filter Category Color 1', 'pt_theplus')),
							'param_name' => 'filter_color_1',
							"description" => "",
							'value' => '#d3d3d3',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Styling', 'pt_theplus'),
							
							'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('Choose Category Text Color 2.', 'pt_theplus') . '</span></span>' . esc_html__('Filter Category Color 2', 'pt_theplus')),
							'param_name' => 'filter_color_2',
							"description" => "",
							'value' => '#313131',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Styling', 'pt_theplus'),
							
							'dependency' => array(
								'element' => 'filter_category',
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
							  "type"        => "dropdown",
							  "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Choose Animation Effect When This Element will be load on scroll. It have many modern options for you to choose from. ','pt_theplus').'</span></span>'.esc_html__('Choose Animation Effect', 'pt_theplus')),
							  "param_name"  => "animation_effects",
							  'edit_field_class' => 'vc_col-sm-6',
							  "admin_label" => false,
							  "value"       => array(
								__( 'No-animation', 'pt_theplus' )             => 'no-animation',
								__( 'FadeIn', 'pt_theplus' )             => 'transition.fadeIn',
								__( 'FlipXIn', 'pt_theplus' )            => 'transition.flipXIn',
							   __( 'FlipYIn', 'pt_theplus' )            => 'transition.flipYIn',
							   __( 'FlipBounceXIn', 'pt_theplus' )      => 'transition.flipBounceXIn',
							   __( 'FlipBounceYIn', 'pt_theplus' )      => 'transition.flipBounceYIn',
							   __( 'SwoopIn', 'pt_theplus' )            => 'transition.swoopIn',
							   __( 'WhirlIn', 'pt_theplus' )            => 'transition.whirlIn',
							   __( 'ShrinkIn', 'pt_theplus' )           => 'transition.shrinkIn',
							   __( 'ExpandIn', 'pt_theplus' )           => 'transition.expandIn',
							   __( 'BounceIn', 'pt_theplus' )           => 'transition.bounceIn',
							   __( 'BounceUpIn', 'pt_theplus' )         => 'transition.bounceUpIn',
							   __( 'BounceDownIn', 'pt_theplus' )       => 'transition.bounceDownIn',
							   __( 'BounceLeftIn', 'pt_theplus' )       => 'transition.bounceLeftIn',
							   __( 'BounceRightIn', 'pt_theplus' )      => 'transition.bounceRightIn',
							   __( 'SlideUpIn', 'pt_theplus' )          => 'transition.slideUpIn',
							   __( 'SlideDownIn', 'pt_theplus' )        => 'transition.slideDownIn',
							   __( 'SlideLeftIn', 'pt_theplus' )        => 'transition.slideLeftIn',
							   __( 'SlideRightIn', 'pt_theplus' )       => 'transition.slideRightIn',
							   __( 'SlideUpBigIn', 'pt_theplus' )       => 'transition.slideUpBigIn',
							   __( 'SlideDownBigIn', 'pt_theplus' )     => 'transition.slideDownBigIn',
							   __( 'SlideLeftBigIn', 'pt_theplus' )     => 'transition.slideLeftBigIn',
							   __( 'SlideRightBigIn', 'pt_theplus' )    => 'transition.slideRightBigIn',
							   __( 'PerspectiveUpIn', 'pt_theplus' )    => 'transition.perspectiveUpIn',
							   __( 'PerspectiveDownIn', 'pt_theplus' )  => 'transition.perspectiveDownIn',
							   __( 'PerspectiveLeftIn', 'pt_theplus' )  => 'transition.perspectiveLeftIn',
							   __( 'PerspectiveRightIn', 'pt_theplus' ) => 'transition.perspectiveRightIn',
							   ),
							  'std' =>'transition.fadeIn',
						),		
						array(
							  "type"        => "textfield",
							  "heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' Add value of delay in transition on scroll in millisecond. 1 sec = 1000 Millisecond ','pt_theplus').'</span></span>'.esc_html__('Animation Delay', 'pt_theplus')),	
							  "param_name"  => "animation_delay",
							  'edit_field_class' => 'vc_col-sm-6',
							  "value"       => '50',
							  "description" => "",
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
									"carousel"
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
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can add Extra Class here to use for Customisation Purpose.','pt_theplus').'</span></span>'.esc_html__('Extra Class', 'pt_theplus')),
							"param_name" => "el_class",
							'edit_field_class' => 'vc_col-sm-6',
						),
							   
					  )
					));
			}
		}
	}
	new ThePlus_clients_list;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_clients_list'))
	{
		class WPBakeryShortCode_tp_clients_list extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}