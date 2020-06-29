<?php
// Blogs List Elements
if(!class_exists("ThePlus_blog_list")){
	class ThePlus_blog_list{
		function __construct(){
			add_action( 'init', array($this, 'init_tp_blog_list') );
			add_shortcode( 'tp_blog_list',array($this,'tp_blog_list_shortcode'));
			add_action( 'wp_enqueue_scripts', array( $this, 'tp_blog_list_scripts' ), 1 );
		}
		function tp_blog_list_scripts() {
			wp_register_style( 'theplus-blog-style', THEPLUS_PLUGIN_URL . 'vc_elements/css/main/theplus-blog-style.css', false, '1.0.0' );
		}
		function tp_blog_list_shortcode($atts,$content = null){
			extract( shortcode_atts( array(
				'blog_style'=>'style-1',
				'layout'=>'grid',
				'animation_effects'=>'transition.fadeIn',
				'animation_delay'=>'50',
				'animated_column_list'=>'',
				'animation_stagger'=>'150',
				
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
				
				'post_meta'=>'on',
				'meta_category'=>'on',
				'meta_date'=>'on',
				'meta_comment'=>'on',
				'meta_excerpt'=>'on',
				'meta_author'=>'on',
				'read_button'=>'on',
				
				'title_font_size'=>'25px',
				'title_line_height'=>'30px',
				'title_letter_space'=>'0px',
				'title_color'=>'#2e2e2e',
				'title_hover_color'=>'#ff214f',
				'title_use_theme_fonts'=>'custom-font-family',
				'title_font_family'=>'',
				'title_font_weight'=>'400',
				'title_google_fonts'=>'',
			'date_hover_color'=>'#2e2e2e',
				
				'content_color'=>'#888',
				'content_font_size'=>'15px',
				'content_line_height'=>'30px',
				
				'meta_color'=> '#777777',
				'meta_hvr_color'=> '#ff004b',
				
				'filter_btn_style'=>'style-1',
				'filter_hover_style'=>'style-1',
				'filter_text_font_size'=>'12px',
				'filter_text_line_height'=>'19px',
				'filter_text_letter_space'=>'0px',
				'filter_text_color'=>'#313131',
				'filter_text_hover_color'=>'#ff214f',
				'filter_color_1'=>'#d3d3d3',
				'filter_color_2'=>'#313131',
				
				'btn_text_font_size'=>'15px',
				'btn_font_weight'=>'400',
				'btn_text_letter_space'=>'1px',
				'btn_text_color'=>'#888',
				'btn_text_hover_color'=>'#888',
				'btn_bg_color'=>'#2e2e2e',
				'btn_bg_hover_color'=>'#ff214f',
				'btn_border_color'=>'#2e2e2e',
				'btn_border_hover_color'=>'#ff214f',
				
				'box_bg_color'=>'#fff',
				'box_bg_hover_color'=>'#ffffff',
				'box_border_color'=>'#d3d3d3',
				'box_border_hover_color'=>'#d3d3d3',
				'column_shadow'=>'0px 0px 2px 0px rgba(0,0,0,0.25)',
				'column_hover_shadow'=>'0px 2px 15px rgba(0,0,0,0.17)',
				
				'carousel_image'=>'full',
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
				
				
				wp_enqueue_style( 'theplus-blog-style');

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

				$args = array(
				'post_type' => 'post',
				'category_name' => $display_category,
				'posts_per_page' => $display_post,
				'paged' => $paged,
				'orderby'	=>$order_by,
				'post_status' =>'publish',
				'ignore_sticky_posts'=>true,
				'order'	=>$post_sort
				);
				
				$post_qry=new WP_Query($args);
				
				$rand_no=rand(1000000, 1500000);
				
				$desktop_class='vc_col-md-'.esc_attr($desktop_column);
				$tablet_class='vc_col-sm-'.esc_attr($tablet_column);
				$mobile_class='vc_col-xs-'.esc_attr($mobile_column);
				
				if(!empty($carousel_image) && $carousel_image!='full'){
					$carousel_image='tp-image-grid';
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
					$attr .=' data-columns="'.esc_attr($desktop_column).'" ';
					$attr .=' data-pad="30px" ';
				}else if($layout=='carousel'){
					$isotope=' list-carousel-slick';
				}
				
				
			if($title_use_theme_fonts=='google-fonts'){
				$text_font_data = pt_plus_getFontsData( $title_google_fonts );
				$title_style = pt_plus_googleFontsStyles( $text_font_data );  
				$font_data= pt_plus_enqueueGoogleFonts( $text_font_data );
			}elseif($title_use_theme_fonts=='custom-font-family'){
				$title_style='font-family:'.$title_font_family.';font-weight:'.$title_font_weight.';';
			}else{
				$title_style='';
			}
				
				$attr .=' data-id="blog-list-'.esc_attr($rand_no).'"';
				$attr .=' data-style="blog-'.esc_attr($blog_style).'"';
				if($column_space == 'on'){
					$column_padding =$column_space_pading;	
				}else{
					$column_padding ='0px';	
				}
				if($read_button!='on' && $read_button==''){
					$read_button='none';
				}else{
					$read_button='block';
				}
				if($meta_category!='on' && $meta_category==''){
					$meta_category='none';
				}else{
					$meta_category='initial';
				}
							
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
				
				
				
				if(!empty($display_category)){
					$array_category=explode(',',$display_category);
				}
				
				$arrow_class='';
				if($arrows_style=='style-4' || $arrows_style=='style-5'){
					$arrow_class=$arrows_position;
				}
				$lazy_load_class='';
				if($post_options=='lazy_load'){
					$lazy_load_class='pt_theplus_lazy_load';
				}
				$blog_listing = '<div id="pt-plus-blog-post-list" class="blog-list '.esc_attr($isotope).' '.esc_attr($lazy_load_class).' blog-'.esc_attr($blog_style).' '.esc_attr($filter_class).' '.esc_attr($animated_class).' blog-list-'.esc_attr($rand_no).' '.esc_attr($arrow_class).' '.esc_attr($el_class).' " '.$animated_attr.'  '.$attr.' >';
				
					if($filter_category=='true'){
				$terms = get_terms( array('taxonomy' => 'category', 'hide_empty' => true) );
				$all_category=$category_post_count='';
					if($filter_btn_style=='style-1'){
						$count=$post_qry->post_count;
						$all_category='<span class="all_post_count">'.esc_html($count).'</span>';
					}
					if($filter_btn_style=='style-2' || $filter_btn_style=='style-3'){
						$count=$post_qry->post_count;
						$category_post_count='<span class="all_post_count">'.esc_attr($count).'</span>';
					}
					
						$blog_listing .='<div class="post-filter-data '.esc_attr($filter_btn_style).' '.esc_attr($filter_align).'">';
							if($filter_btn_style=='style-4'){
								$blog_listing .= '<span class="filters-toggle-link">'.esc_html__('Filters','pt_theplus').'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
							}
							$blog_listing .='<ul class="category-filters '.esc_attr($filter_btn_style).' hover-'.esc_attr($filter_hover_style).'">';
								$blog_listing .= '<li><a href="#" class="filter-category-list active all" data-filter="*" >'.$category_post_count.'<span data-hover="'.esc_attr('All','pt_theplus').'">'.esc_html__('All','pt_theplus').'</span>'.$all_category.'</a></li>';
								
								if ( $terms != null ){
									foreach( $terms as $term ) {
										$category_post_count='';
										if($filter_btn_style=='style-2' || $filter_btn_style=='style-3'){
											$category_post_count='<span class="all_post_count">'.esc_html($term->count).'</span>';
										}
										if(!empty($array_category)){
											if(in_array($term->slug,$array_category)){
												$blog_listing .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
												unset($term);
											}
										}else{
											$blog_listing .= '<li><a href="#" class="filter-category-list"  data-filter=".'.esc_attr($term->slug).'">'.$category_post_count.'<span data-hover="'.esc_attr($term->name).'">'.esc_html($term->name).'</span></a></li>';
											unset($term);
										}
									}
								}
							$blog_listing .= '</ul>';
						$blog_listing .= '</div>';
				}
				
				$blog_listing .= '<div class="post-inner-loop blog-'.esc_attr($rand_no).' ">';
				$i=1;
				if($blog_style=='style-9'){
					$desktop_class='col-md-12 col-sm-12 col-xs-12';
					$tablet_class=$mobile_class='';
				}
				if($layout=='metro' || $layout=='carousel'){
						$desktop_class=$tablet_class=$mobile_class='';
				}
				
				
				if($post_qry->have_posts()) :
				while($post_qry->have_posts()) : $post_qry->the_post(); 
				
					$category_filter='';
					if($filter_category=='true'){				
						$terms = get_the_terms( $post_qry->ID,'category');
						if ( $terms != null ){
							foreach( $terms as $term ) {
								$category_filter .=' '.esc_attr($term->slug).' ';
								unset($term);
							}
						}
					}
					
					$blog_listing .= '<div class="grid-item metro-item'.esc_attr($i).' '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.esc_attr($animated_columns).' '.$category_filter.'" >';
						if(!empty($blog_style)){
						ob_start();
						include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/blog-'.$blog_style.'.php'; 
						$blog_listing .= ob_get_contents();
						ob_end_clean();
						}
					$blog_listing .= '</div>';
					$i++;
				endwhile;
				endif;
				$blog_listing .= '</div>';
				
				if($post_options=='pagination'){
					$blog_listing .= pt_plus_pagination($post_qry->max_num_pages,'4');
				}
				
				if($post_options=='load_more'){
						$blog_listing .= '<div class="ajax_load_more">';
						$blog_listing .= '<a class="post-load-more" data-load="blogs" data-post_type="post" data-texonomy_category="category" data-load-class="blog-'.esc_attr($rand_no).'" data-layout="'.$layout.'" data-style="'.esc_attr($blog_style).'" data-desktop-column="'.esc_attr($desktop_column).'" data-tablet-column="'.esc_attr($tablet_column).'" data-mobile-column="'.esc_attr($mobile_column).'" data-category="'.esc_attr($display_category).'" data-order_by="'.esc_attr($order_by).'" data-post_sort="'.esc_attr($post_sort).'" data-filter_category="'.esc_attr($filter_category).'" data-display_post="'.esc_attr($display_post).'" data-animated_columns="'.esc_attr($animated_columns).'" data-post_load_more="'.esc_attr($post_load_more).'" data-page="1" data-total_page="'.esc_attr($post_qry->max_num_pages).'">'.esc_html($load_more_text).'</a>';
						$blog_listing .= '</div>';
				}
				if($post_options=='lazy_load'){
					$blog_listing .= '<div class="ajax_lazy_load">';
						$blog_listing .= '<a class="post-lazy-load" data-load="blogs" data-post_type="post" data-texonomy_category="category" data-load-class="blog-'.esc_attr($rand_no).'" data-layout="'.$layout.'" data-style="'.esc_attr($blog_style).'" data-desktop-column="'.esc_attr($desktop_column).'" data-tablet-column="'.esc_attr($tablet_column).'" data-mobile-column="'.esc_attr($mobile_column).'" data-category="'.esc_attr($display_category).'" data-order_by="'.esc_attr($order_by).'" data-post_sort="'.esc_attr($post_sort).'" data-filter_category="'.esc_attr($filter_category).'" data-display_post="'.esc_attr($display_post).'" data-animated_columns="'.esc_attr($animated_columns).'" data-post_load_more="'.esc_attr($post_load_more).'" data-page="1" data-total_page="'.esc_attr($post_qry->max_num_pages).'"><img src="'.THEPLUS_PLUGIN_URL. 'vc_elements/images/lazy_load.gif" /></a>';
						$blog_listing .= '</div>';
				}
				
				$blog_listing .= '</div>';
				wp_reset_postdata();
				$css_rule='';
				$css_rule .= '<style >';
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-list .post-inner-loop .grid-item{padding : '.esc_js($column_padding).'; }';
					
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-list.blog-'.esc_js($blog_style).' .post-title,.blog-list-'.esc_js($rand_no).'.blog-list.blog-'.esc_js($blog_style).' .post-title a{font-size: '.esc_js($title_font_size).';line-height:'.esc_js($title_line_height).';letter-spacing:'.esc_js($title_letter_space).';color:'.esc_js($title_color).';'.esc_js($title_style).'}.blog-list-'.esc_js($rand_no).'.blog-list.blog-'.esc_js($blog_style).' .grid-item:hover .post-title a{color:'.esc_js($title_hover_color).';}';
					
					$css_rule .='.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .entry-content p{font-size: '.esc_js($content_font_size).';line-height:'.esc_js($content_line_height).';color:'.esc_js($content_color).';}';
					if($meta_excerpt!='on' && $meta_excerpt==''){
						$css_rule .='.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .entry-content{display:none;}';
					}
					if($meta_comment!='on' && $meta_comment==''){
						$css_rule .='.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-meta-info .meta-comments{display:none;}';
					}
					if($meta_author!='on' && $meta_author==''){
						$css_rule .='.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-meta-info .post-author{display:none;}';
					}
					if($post_meta!='on' && $post_meta==''){
						$css_rule .='.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-meta-info,.blog-list-'.esc_js($rand_no).'.blog-style-5 .post-category-list,.blog-list-'.esc_js($rand_no).'.blog-style-9 .column-date,.blog-list-'.esc_js($rand_no).'.blog-style-6 .column-date{display:none;}';
					}
					if($meta_date!='on' && $meta_date==''){
						$css_rule .='.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-meta-info .meta-date{display:none;}.blog-list-'.esc_js($rand_no).'.blog-style-9 .meta-date,.blog-list-'.esc_js($rand_no).'.blog-style-6 .meta-date,.blog-list-'.esc_js($rand_no).'.blog-style-10 .meta-date{display: none;}';
					}
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-list .post-inner-loop .post-meta-info ,.blog-list-'.esc_js($rand_no).'.blog-list .post-inner-loop .post-meta-info a,.blog-style-4 .post-category-list,.blog-style-4 .post-category-list a{color : '.esc_js($meta_color).'; }.blog-list-'.esc_js($rand_no).'.blog-list .post-inner-loop .post-meta-info a:hover{color : '.esc_js($meta_hvr_color).'; }';
				
				if($blog_style=='style-1'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button.button-style-7 a.button-link-wrap{font-size: '.esc_js($btn_text_font_size).';font-weight:'.esc_js($btn_font_weight).';letter-spacing:'.esc_js($btn_text_letter_space).';color:'.esc_js($btn_text_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button.button-style-7 .button-link-wrap:after{border-color:'.esc_js($btn_border_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button.button-style-7 a.button-link-wrap:hover{color:'.esc_js($btn_text_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button.button-style-7 a.button-link-wrap .btn-arrow:before{color:'.esc_js($btn_text_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button.button-style-7 a.button-link-wrap:hover .btn-arrow:before{color:'.esc_js($btn_text_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt_plus_button.button-style-7 .button-link-wrap .btn-arrow:after{color:'.esc_js($btn_text_hover_color).';}';
				}else if($blog_style=='style-2'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .content-column.column-date{border-color:'.esc_js($box_border_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .content-column.column-date{border-color:'.esc_js($box_border_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .content-column.column-date a{color:'.esc_js($title_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .content-column.column-date a{color:'.esc_js($date_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .content-column.column-date:after{background: '.esc_js($box_border_hover_color).';}';
				}else if($blog_style=='style-3'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button .read-more-btn{font-size: '.esc_js($btn_text_font_size).';font-weight:'.esc_js($btn_font_weight).';letter-spacing:'.esc_js($btn_text_letter_space).';color:'.esc_js($btn_text_color).';background:'.esc_js($btn_bg_color).';border-color:'.esc_js($btn_border_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button .read-more-btn:hover{color:'.esc_js($btn_text_hover_color).';background:'.esc_js($btn_bg_hover_color).';border-color:'.esc_js($btn_border_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .column-date a{background:'.esc_js($title_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .column-date a{background:'.esc_js($title_hover_color).';}';
				}else if($blog_style=='style-4'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .blog-list-style-content{border-color:'.esc_js($box_border_color).';-webkit-box-shadow:'.esc_js($column_shadow).';-moz-box-shadow:'.esc_js($column_shadow).';box-shadow:'.esc_js($column_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .blog-list-style-content{border-color:'.esc_js($box_border_hover_color).';-webkit-box-shadow:'.esc_js($column_hover_shadow).';-moz-box-shadow:'.esc_js($column_hover_shadow).';box-shadow:'.esc_js($column_hover_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button.button-style-1 .button-link-wrap{font-size: '.esc_js($btn_text_font_size).';font-weight:'.esc_js($btn_font_weight).';letter-spacing:'.esc_js($btn_text_letter_space).';color:'.esc_js($btn_text_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .pt-theplus-post-read-more-button.button-style-1 .button-link-wrap{color:'.esc_js($btn_text_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button.button-style-1 .button-link-wrap .button_line{background-color:'.esc_js($btn_border_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .pt-theplus-post-read-more-button.button-style-1 .button-link-wrap .button_line{background-color:'.esc_js($btn_border_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button{display: '.esc_js($read_button).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .post-category-list,.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .post-category-list a{color:'.esc_js($meta_hvr_color).';}';
				}else if($blog_style=='style-5'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .post-category-list{color:'.esc_js($content_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item .post-category-list a{color:'.esc_js($meta_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .post-category-list a{color:'.esc_js($meta_hvr_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-block-inner .hover-button{display:'.esc_js($read_button).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .blog-list-style-content{background:'.esc_js($box_bg_color).';border-color:'.esc_js($box_border_color).';-webkit-box-shadow:'.esc_js($column_shadow).';-moz-box-shadow:'.esc_js($column_shadow).';box-shadow:'.esc_js($column_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .blog-list-style-content{border-color:'.esc_js($box_border_hover_color).';-webkit-box-shadow:'.esc_js($column_hover_shadow).';-moz-box-shadow:'.esc_js($column_hover_shadow).';box-shadow:'.esc_js($column_hover_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-featured-image:before{background:'.esc_js($box_bg_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .post-featured-image:before{background:'.esc_js($box_bg_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-block-inner{background:'.esc_js($box_bg_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).'  .post-block-inner .hover-button{color:'.esc_js($btn_text_color).';background:'.esc_js($btn_bg_color).';border-color:'.esc_js($btn_border_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-block-inner .hover-button:hover{color:'.esc_js($btn_text_hover_color).';background:'.esc_js($btn_bg_hover_color).';border-color:'.esc_js($btn_border_hover_color).';}';
				}else if($blog_style=='style-6'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .blog-list-style-content{border-color:'.esc_js($box_border_color).';-webkit-box-shadow:'.esc_js($column_shadow).';-moz-box-shadow:'.esc_js($column_shadow).';box-shadow:'.esc_js($column_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .blog-list-style-content{border-color:'.esc_js($box_border_hover_color).';-webkit-box-shadow:'.esc_js($column_hover_shadow).';-moz-box-shadow:'.esc_js($column_hover_shadow).';box-shadow:'.esc_js($column_hover_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-featured-image:before{background:'.esc_js($btn_bg_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-list.blog-'.esc_js($blog_style).' .grid-item .post-title-content{background:'.esc_js($box_bg_color).';}.blog-list-'.esc_js($rand_no).'.blog-list.blog-'.esc_js($blog_style).' .grid-item:hover .post-title-content{background:'.esc_js($box_bg_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .meta-category{display: '.esc_js($meta_category).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid_overlay:before,.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid_overlay:after{background: '.esc_js($title_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .column-date,.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .column-date a{color:'.esc_js($title_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .column-date,.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .column-date a{color:'.esc_js($title_hover_color).';}';
				}else if($blog_style=='style-7'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button .read-more-btn{font-size: '.esc_js($btn_text_font_size).';font-weight:'.esc_js($btn_font_weight).';letter-spacing:'.esc_js($btn_text_letter_space).';color:'.esc_js($btn_text_color).';background:'.esc_js($btn_bg_color).';border-color:'.esc_js($btn_border_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt-theplus-post-read-more-button .read-more-btn:hover{color:'.esc_js($btn_text_hover_color).';background:'.esc_js($btn_bg_hover_color).';border-color:'.esc_js($btn_border_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .blog-list-style-content{background:'.esc_js($box_bg_color).';-webkit-box-shadow:'.esc_js($column_shadow).';-moz-box-shadow:'.esc_js($column_shadow).';box-shadow:'.esc_js($column_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .blog-list-style-content{background:'.esc_js($box_bg_hover_color).';-webkit-box-shadow:'.esc_js($column_hover_shadow).';-moz-box-shadow:'.esc_js($column_hover_shadow).';box-shadow:'.esc_js($column_hover_shadow).';}';
				}else if($blog_style=='style-9'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .content-column.column-date div{background:'.esc_js($title_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .content-column.column-date div{background:'.esc_js($title_hover_color).';}';
				}else if($blog_style=='style-10'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item .blog-list-style-content{border-color:'.esc_js($box_border_color).';-webkit-box-shadow:'.esc_js($column_shadow).';-moz-box-shadow:'.esc_js($column_shadow).';box-shadow:'.esc_js($column_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item .blog-list-style-content:hover{border-color:'.esc_js($box_border_hover_color).';-webkit-box-shadow:'.esc_js($column_hover_shadow).';-moz-box-shadow:'.esc_js($column_hover_shadow).';box-shadow:'.esc_js($column_hover_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .post-featured-image:before{background:'.esc_js($btn_bg_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-list.blog-'.esc_js($blog_style).' .grid-item .post-title-content{background:'.esc_js($box_bg_color).';}.blog-list-'.esc_js($rand_no).'.blog-list.blog-'.esc_js($blog_style).' .grid-item:hover .post-title-content,.blog-list-'.esc_js($rand_no).'.blog-list.blog-'.esc_js($blog_style).' .grid-item:hover .post-format-gallery:after{background:'.esc_js($box_bg_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .meta-category{display: '.esc_js($meta_category).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt_plus_button.button-style-3{display: '.esc_js($read_button).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .blog-media:after{background:'.esc_js($box_bg_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .blog-media:after{background:'.esc_js($box_bg_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt_plus_button.button-style-3 .button-link-wrap .arrow path{stroke:'.esc_js($btn_border_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt_plus_button.button-style-3 .button-link-wrap .arrow-1 path{stroke:'.esc_js($btn_border_hover_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt_plus_button.button-style-3 a.button-link-wrap:before{background:'.esc_js($btn_bg_color).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .pt_plus_button.button-style-3 a.button-link-wrap:before{background:'.esc_js($btn_bg_hover_color).';}';
				}
				if($blog_style=='style-2' || $blog_style=='style-3' || $blog_style=='style-7' || $blog_style=='style-8' || $blog_style=='style-9'){
					$css_rule .= '.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item .blog-list-style-content{background:'.esc_js($box_bg_color).';border-color:'.esc_js($box_border_color).';-webkit-box-shadow:'.esc_js($column_shadow).';-moz-box-shadow:'.esc_js($column_shadow).';box-shadow:'.esc_js($column_shadow).';}.blog-list-'.esc_js($rand_no).'.blog-'.esc_js($blog_style).' .grid-item:hover .blog-list-style-content{background:'.esc_js($box_bg_hover_color).';border-color:'.esc_js($box_border_hover_color).';-webkit-box-shadow:'.esc_js($column_hover_shadow).';-moz-box-shadow:'.esc_js($column_hover_shadow).';box-shadow:'.esc_js($column_hover_shadow).';}';
				}
				$css_rule .= '</style>';
				return $css_rule.$blog_listing;
		}
		function init_tp_blog_list(){
			if(function_exists("vc_map"))
			{
				vc_map(array(
					"name" => esc_html__("Blog Post", 'pt_theplus'),
					"base" => "tp_blog_list",
					"icon" => "tp-blog-list",
					"category" => esc_html__("The Plus", "pt_theplus"),
					"description" => esc_html__('Various Listing and Carousel Options', 'pt_theplus'),
					"params" => array(
						array(
								'type'        => 'radio_select_image',
								'heading' =>  esc_html__('Blog Style ', 'pt_theplus'), 
								'param_name'  => 'blog_style',
								'simple_mode' => false,
								"admin_label" => true,
								'value' => 'style-1',
								"class" => "blog_style_field",
								'options'     => array(
									'style-1' => array(
										'tooltip' => esc_attr__('Style-1','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/1.jpg'
									),									
									
									'style-2' => array(
										'tooltip' => esc_attr__('Style-2','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/2.jpg'
									),
									'style-3' => array(
										'tooltip' => esc_attr__('Style-3','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/3.jpg'
									),
									'style-4' => array(
										'tooltip' => esc_attr__('Style-4','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/4.jpg'
									),
									'style-5' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/5.jpg'
									),
									'style-6' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/6.jpg'
									),
									'style-7' => array(
										'tooltip' => esc_attr__('Style-7','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/7.jpg'
									),
									'style-8' => array(
										'tooltip' => esc_attr__('Style-8','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/8.jpg'
									),
									'style-9' => array(
										'tooltip' => esc_attr__('Style-9','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/9.jpg'
									),
									'style-10' => array(
										'tooltip' => esc_attr__('Style-10','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/blog_posts/10.jpg'
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
								"class" => "blog_layout_field",
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
									'carousel' => array(
										'tooltip' => esc_attr__('Carousel Layout','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/layout/carousel.jpg'
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
							'text' => esc_html__('Post Setting', 'pt_theplus'),
							'param_name' => 'post_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_taxonomy_multicheck',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose categories from which you want to show posts by marking one or multiple.', 'pt_theplus').'</span></span>'.esc_html__('Choose Categories', 'pt_theplus')),
							'param_name' => 'display_category',
							'taxonomy' => 'category',
							'edit_field_class' => 'vc_column vc_col-sm-12 pt_theplus-taxonomy-multicheck',
							'group' => esc_attr__('Content', 'pt_theplus')
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
									"metro"
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
								'6 column' => '2',
								'12 column' => '1'
							),
							'std' => '3',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-2",
									"style-3",
									"style-4",
									"style-5",
									"style-6",
									"style-7",
									"style-8",
									"style-10",
								)
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
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
								'6 column' => '2'
							),
							'std' => '6',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-2",
									"style-3",
									"style-4",
									"style-5",
									"style-6",
									"style-7",
									"style-8",
									"style-10",
								)
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
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
								'6 column' => '2'
							),
							'std' => '12',
							"edit_field_class" => "vc_col-xs-4",
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-2",
									"style-3",
									"style-4",
									"style-5",
									"style-6",
									"style-7",
									"style-8",
									"style-10",
								)
							),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							),
							"group" => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Extra Options', 'pt_theplus'),
							'param_name' => 'extra_settings',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							"group" => esc_attr__('Content', 'pt_theplus'),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							)
						),
						
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn On or Off Category wise Filtration option using this option.','pt_theplus').'</span></span>'.esc_html__('Category Wise Filter', 'pt_theplus')),
							'param_name' => 'filter_category',
							'description' => '',
							'value' => 'false',
							'options' => array(
								'true' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-6",
							"group" => esc_attr__('Content', 'pt_theplus'),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							)
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose alignment position of Filter block using this option.','pt_theplus').'</span></span>'.esc_html__('Filter Block Alignment', 'pt_theplus')),
							"param_name" => "filter_align",
							"value" => array(
								esc_html__("Left", "pt_theplus") => "text-left",
								esc_html__("Center", "pt_theplus") => "text-center",
								esc_html__("Right", "pt_theplus") => "text-right"
							),
							"std" => 'text-center',
							"description" => "",
							'dependency' => array(
								'element' => 'filter_category',
								'value' => array(
									'true'
								)
							),
							"edit_field_class" => "vc_col-xs-6",
							"group" => esc_attr__('Content', 'pt_theplus')
							
						),
						array(
							"type" => "dropdown",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can choose more post loading using this option.','pt_theplus').'</span></span>'.esc_html__('More Post Loading Options', 'pt_theplus')),
							"param_name" => "post_options",
							"value" => array(
								esc_html__("Select Options", "pt_theplus") => "",
								esc_html__("Pagination", "pt_theplus") => "pagination",
								esc_html__("Load More", "pt_theplus") => "load_more",
								esc_html__("Lazy Load", "pt_theplus") => "lazy_load"
							),
							"group" => esc_attr__('Content', 'pt_theplus'),
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"grid",
									"masonry",
									"metro"
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set Button Text of Load More functionality from here.','pt_theplus').'</span></span>'.esc_html__('Button Text', 'pt_theplus')),
							"param_name" => "load_more_text",
							"value" => 'Load More',
							 "edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								'element' => "post_options",
								'value' => 'load_more'
							),
							"group" => esc_attr__('Content', 'pt_theplus')
							
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can set number of post needs to be add on press of button for load more.','pt_theplus').'</span></span>'.esc_html__('More Posts on Click', 'pt_theplus')),
							"param_name" => "post_load_more",
							"value" => '4',
							 "edit_field_class" => "vc_col-xs-6",
							"dependency" => array(
								'element' => "post_options",
								'value' => 'load_more'
							),
							'group' => esc_attr__('Content', 'pt_theplus')
							
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Display Setting', 'pt_theplus'),
							'param_name' => 'display_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On whole Meta Section of Blog Post using this option.','pt_theplus').'</span></span>'.esc_html__('Overall Post Meta', 'pt_theplus')),
							'param_name' => 'post_meta',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On category of Blog Post Meta Section using this option.','pt_theplus').'</span></span>'.esc_html__('Category', 'pt_theplus')),
							'param_name' => 'meta_category',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-2",
									"style-3",
									"style-4",
									"style-5",
									"style-6",
									"style-7",
									"style-8",
									"style-10",
								)
							),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On whole Meta Section of Blog Post using this option.','pt_theplus').'</span></span>'.esc_html__('Date - Post Meta', 'pt_theplus')),
							'param_name' => 'meta_date',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On Comments Counter and Icon of Blog Post Meta Section using this option.','pt_theplus').'</span></span>'.esc_html__('Comments - Post Meta', 'pt_theplus')),
							'param_name' => 'meta_comment',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On Excerpts Section of Blog Post Meta Section using this option.','pt_theplus').'</span></span>'.esc_html__('Excerpt - Post Meta', 'pt_theplus')),
							'param_name' => 'meta_excerpt',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'class' => '',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can Turn Off/On Author Info of Blog Post Meta Section using this option.','pt_theplus').'</span></span>'.esc_html__('Author Info - Post Meta', 'pt_theplus')),
							'param_name' => 'meta_author',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_checkbox',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can Turn Off/On Button using this option.','pt_theplus').'</span></span>'.esc_html__('Button Section', 'pt_theplus')),
							'param_name' => 'read_button',
							'value' => 'on',
							'options' => array(
								'on' => array(
									'label' => '',
									'on' => 'Yes',
									'off' => 'No'
								)
							),
							"edit_field_class" => "vc_col-xs-4",
							'group' => esc_attr__('Content', 'pt_theplus')
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Title Setting', 'pt_theplus'),
							'param_name' => 'title_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Blog&#39;s Title&#39;s Font size for using this option.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "title_font_size",
							"value" => '25px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Blog&#39;s Title&#39;s Line Height for using this option.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "title_line_height",
							"value" => '30px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Blog&#39;s Title&#39;s Letter Spacing for using this option.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"param_name" => "title_letter_space",
							"value" => '0px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for title using this option.','pt_theplus').'</span></span>'.esc_html__('Title Color', 'pt_theplus')),
							'param_name' => 'title_color',
							"description" => "",
							'value' => '#2e2e2e',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for title hover using this option.','pt_theplus').'</span></span>'.esc_html__('Title Hover Color', 'pt_theplus')),
							'param_name' => 'title_hover_color',
							"description" => "",
							'value' => '#ff214f',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for title using this option.','pt_theplus').'</span></span>'.esc_html__('Date Hover Color', 'pt_theplus')),
							'param_name' => 'date_hover_color',
							"description" => "",
							'value' => '#2e2e2e',
							'edit_field_class' => 'vc_col-xs-4',
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-2",
									"style-7"
								)
							),
							'group' => esc_attr__('Styling', 'pt_theplus')
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
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Excerpt Setting', 'pt_theplus'),
							'param_name' => 'excerpt_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for content using this option.','pt_theplus').'</span></span>'.esc_html__('Content Color', 'pt_theplus')),
							'param_name' => 'content_color',
							"description" => "",
							'value' => '#888',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Blog&#39;s Content Font size for using this option.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "content_font_size",
							"value" => '15px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Blog&#39;s Content Line Height for using this option.','pt_theplus').'</span></span>'.esc_html__('Line Height', 'pt_theplus')),
							"param_name" => "content_line_height",
							"value" => '30px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						  array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Meta value Setting', 'pt_theplus'),
							'param_name' => 'meta_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for post meta using this option.','pt_theplus').'</span></span>'.esc_html__('Post Meta Color', 'pt_theplus')),
							'param_name' => 'meta_color',
							"description" => "",
							'value' => '#777777',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for post meta hover using this option.','pt_theplus').'</span></span>'.esc_html__('Post Meta Hover Color', 'pt_theplus')),
							'param_name' => 'meta_hvr_color',
							"description" => "",
							'value' => '#ff004b',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Styling', 'pt_theplus')
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
							),
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
							),
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
							),
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
							'text' => esc_html__('Button Style', 'pt_theplus'),
							'param_name' => 'button_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-4",
									"style-5",
									"style-7",
									"style-10",
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Choose Blog&#39;s button Font size for using this option.','pt_theplus').'</span></span>'.esc_html__('Font Size', 'pt_theplus')),
							"param_name" => "btn_text_font_size",
							"value" => '15px',
							"description" => '',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-4",
									"style-5",
									"style-7",
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Font Weight using this Option. E.g. 400, 700, etc.','pt_theplus').'</span></span>'.esc_html__('Font Weight', 'pt_theplus')),
							"param_name" => "btn_font_weight",
							"value" => '400',
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-4",
									"style-5",
									"style-7",
								)
							)
						),
						array(
							"type" => "textfield",
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can add Letter Spacing in Pixels using this Option. E.g. 1px, 2px, etc.','pt_theplus').'</span></span>'.esc_html__('Letter Spacing', 'pt_theplus')),
							"heading" => __("Text Letter Spacing", 'pt_theplus'),
							"param_name" => "btn_text_letter_space",
							"value" => '1px',
							"description" => "",
							'edit_field_class' => 'vc_col-xs-4',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-4",
									"style-5",
									"style-7",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for button title using this option','pt_theplus').'</span></span>'.esc_html__('Font Color', 'pt_theplus')),
							'param_name' => 'btn_text_color',
							"description" => "",
							'value' => '#888',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-4",
									"style-5",
									"style-7",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for button title Hover using this option','pt_theplus').'</span></span>'.esc_html__('Font Hover Color', 'pt_theplus')),
							'param_name' => 'btn_text_hover_color',
							"description" => "",
							'value' => '#888',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-4",
									"style-5",
									"style-7",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for button background Hover using this option','pt_theplus').'</span></span>'.esc_html__('Background Color', 'pt_theplus')),
							'param_name' => 'btn_bg_color',
							"description" => "",
							'value' => '#2e2e2e',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-5",
									"style-7",
									"style-10",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for button background Hover using this option','pt_theplus').'</span></span>'.esc_html__('Background Hover Color', 'pt_theplus')),
							'param_name' => 'btn_bg_hover_color',
							"description" => "",
							'value' => '#ff214f',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-5",
									"style-7",
									"style-10",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for button border using this option','pt_theplus').'</span></span>'.esc_html__('Border Color', 'pt_theplus')),
							'param_name' => 'btn_border_color',
							"description" => "",
							'value' => '#2e2e2e',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-4",
									"style-5",
									"style-7",
									"style-10",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('You can select color and Opacity for button border hover color using this option','pt_theplus').'</span></span>'.esc_html__('Border Hover Color', 'pt_theplus')),
							'param_name' => 'btn_border_hover_color',
							"description" => "",
							'value' => '#ff214f',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Button', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-1",
									"style-3",
									"style-4",
									"style-5",
									"style-7",
									"style-10",
								)
							)
						),
						array(
							'type' => 'pt_theplus_heading_param',
							'text' => esc_html__('Columns Style', 'pt_theplus'),
							'param_name' => 'column_setting',
							'edit_field_class' => 'pt_theplus-heading-param-style vc_col-sm-12',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-2",
									"style-5",
									"style-3",
									"style-4",
									"style-5",
									"style-6",
									"style-7",
									"style-8",
									"style-9",
									"style-10",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Background Color', 'pt_theplus'),
							'param_name' => 'box_bg_color',
							"description" => "",
							'value' => '#fff',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-2",
									"style-3",
									"style-5",
									"style-6",
									"style-7",
									"style-8",
									"style-9",
									"style-10",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Background Hover Color', 'pt_theplus'),
							'param_name' => 'box_bg_hover_color',
							"description" => "",
							'value' => '#fff',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-2",
									"style-3",
									"style-5",
									"style-6",
									"style-7",
									"style-8",
									"style-9",
									"style-10",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Border Color', 'pt_theplus'),
							'param_name' => 'box_border_color',
							"description" => "",
							'value' => '#d3d3d3',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-2",
									"style-3",
									"style-4",
									"style-5",
									"style-6",
									"style-8",
									"style-9",
									"style-10",
								)
							)
						),
						array(
							'type' => 'colorpicker',
							'heading' => __('Border Hover Color', 'pt_theplus'),
							'param_name' => 'box_border_hover_color',
							"description" => "",
							'value' => '#d3d3d3',
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-2",
									"style-3",
									"style-4",
									"style-5",
									"style-6",
									"style-8",
									"style-9",
									"style-10",
								)
							)
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Column Box Shadow ', 'pt_theplus')),
							"param_name" => "column_shadow",
							"value" => '0px 0px 2px 0px rgba(0,0,0,0.25)',
							"description" => "",
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-2",
									"style-3",
									"style-4",
									"style-5",
									"style-6",
									"style-7",
									"style-8",
									"style-9",
									"style-10",
								)
							)
						),
						array(
							"type" => "textfield",
							"heading" => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__(' You can set Box Shadow Value here with all options. E.g. 0px 1px 7px 0 outset/inset #212121','pt_theplus').'</br><a target="_blank" class="tootip-link" href="https://www.cssmatic.com/box-shadow">'.esc_html__(' Check link','pt_theplus').'</a></span></span>'.esc_html__('Column Hover Box Shadow', 'pt_theplus')),
							"param_name" => "column_hover_shadow",
							"value" => '0px 2px 15px rgba(0,0,0,0.17)',
							"description" => "",
							'edit_field_class' => 'vc_col-xs-6',
							'group' => esc_attr__('Box Style', 'pt_theplus'),
							"dependency" => array(
								"element" => "blog_style",
								"value" => array(
									"style-2",
									"style-3",
									"style-4",
									"style-5",
									"style-6",
									"style-7",
									"style-8",
									"style-9",
									"style-10",
								)
							)
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
							"type" => "dropdown",
							'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('If you will choose grid layout, All images will be auto crop and setup there in same size. Full layout will use your image\'s original aspect ratio, It can be used for creative carousels.', 'pt_theplus') . '</span></span>' . esc_html__('Carousel Image Size', 'pt_theplus')),
							"param_name" => "carousel_image",
							"admin_label" => false,
							"value" => array(
								'Full Layout' => 'full',
								'Grid Layout' => 'grid',
							),
							'std' => 'full',
							"dependency" => array(
								"element" => "layout",
								"value" => array(
									"carousel"
								)
							),
							'group' => esc_attr__('Carousel', 'pt_theplus')
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
							'heading' =>  __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">'.esc_html__('Turn on or Off Mouse Draggable functionality of carousel using this option.','pt_theplus').'</span></span>'.esc_html__('Draggable', 'pt_theplus')),
							'param_name' => 'show_draggable',
							'description' => '',
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
							'description' => '',
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
							'description' => '',
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
								__("One By One slide", "pt_theplus") => "1",
								__("Column Slide", "pt_theplus") => "2",
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
									'style-9' => array(
										'tooltip' => esc_attr__('Style-5','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-6.jpg'
									),
									'style-10' => array(
										'tooltip' => esc_attr__('Style-6','pt_theplus'),
										'src' => THEPLUS_PLUGIN_URL. 'vc_elements/images/dots/ts-dot-style-5.jpg'
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
								"value" => array("style-1","style-2","style-4","style-5","style-6","style-7","style-9","style-11","style-12","style-13"),
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
								"value" => array("style-1","style-4","style-5","style-7","style-9","style-11","style-12","style-13"),
							),
						),
						 array(
								'type'        => 'radio_select_image',
								'heading' => __('<span class="pt_theplus-vc-toolip"><i class="fa fa-question" aria-hidden="true"></i><span class="pt_theplus-vc-tooltip-text">' . esc_html__('You can select styles of navigation dots using this option.', 'pt_theplus') . '</span></span>' . esc_html__('Arrow Style', 'pt_theplus')),
								'param_name'  => 'arrows_style',
								'simple_mode' => false,
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
								"value" => array("style-4","style-5"),
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
					)
				));
			}
		}
	}
	new ThePlus_blog_list;

	if(class_exists('WPBakeryShortCode') && !class_exists('WPBakeryShortCode_tp_blog_list'))
	{
		class WPBakeryShortCode_tp_blog_list extends WPBakeryShortCode
		{
			protected function contentInline($atts, $content = null)
			{
				
			}
		}
	}
}