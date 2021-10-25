<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_BlogPackery')) {
	class GT3_Core_Elementor_Widget_BlogPackery extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		protected function get_main_script_depends(){
			return array_merge(
				parent::get_main_script_depends(),
				array(
					'gt3-core/isotope',
					'imagesloaded'
				)
			);
		}

		public function get_name(){
			return 'gt3-core-blog-packery';
		}

		public function get_title(){
			return esc_html__('Blog Packery', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-post-list';
		}

		protected function construct(){
//			$this->add_script_depends( 'gt3_isotope_js' );
//			$this->add_script_depends( 'imagesloaded' );

			$this->add_style_depends('gt3-core/widgets/gt3-core-button');
			$this->add_style_depends('gt3-theme/widgets/gt3-core-button');

			add_action('wp_ajax_gt3_themes_core_blogpackery_load_items', array( $this, 'ajax_handler' ));
			add_action('wp_ajax_nopriv_gt3_themes_core_blogpackery_load_items', array( $this, 'ajax_handler' ));
		}

		public $POST_TYPE = 'post';
		public $TAXONOMY = 'category';
		public $render_index = 1;

		public $packery_grids = array(
			1 => array(
				'lap'  => 7,
				'grid' => 3,
				'elem' => array(
					2  => array( 'w' => 2, ),
					3  => array( 'w' => 2, ),
				)
			),
			2 => array(
				'lap'  => 9,
				'grid' => 3,
				'elem' => array(
					5 => array( 'w' => 2, ),
					6 => array( 'w' => 2, 'h' => 2, ),
					7 => array( 'w' => 2, 'h' => 2, ),
					8 => array( 'w' => 2, ),
				)
			),
			3 => array(
				'lap'  => 6,
				'grid' => 3,
				'elem' => array(
					2 => array( 'w' => 2, ),
					3 => array( 'w' => 2, 'h' => 2, ),
					4 => array( 'w' => 2, 'h' => 2, ),
					5 => array( 'w' => 2, ),
				)
			),
		);

		function getSlugById($taxonomy, $ids){
			$slugs = array();

			$terms = get_terms(array(
				'taxonomy' => $taxonomy,
				'include'  => $ids,
			));
			if(!is_wp_error($terms)) {
				if(is_array($terms) && count($terms)) {
					foreach($terms as $term) {
						$slugs[] = $term->slug;
					}
				}
			}

			return $slugs;
		}

		function isIds($ids){
			if(is_array($ids) && count($ids)) {
				foreach($ids as $id) {
					if(!is_numeric($id)) {
						return false;
					}
				}

				return true;
			}

			return false;
		}

		public function get_taxonomy($args){
			if ($this->isIds($args)) {
				$args = $this->getSlugById($this->TAXONOMY, $args);
			}

			$terms  = get_terms(array(
				'taxonomy'   => 'category',
				'hide_empty' => false,
				'slug'    => $args,
			));
			$return = array();
			if(is_array($terms) && count($terms)) {
				foreach($terms as $term) {
					/* @var \WP_Term $term */
					$return[$term->term_id] = array( 'slug' => $term->slug, 'name' => $term->name );
				}
			}

			return $return;
		}


		public function get_tax_query_fields(){
			$terms  = get_terms(array(
				'taxonomy'   => $this->TAXONOMY,
				'hide_empty' => false,
			));
			$return = array();
			if(is_array($terms) && count($terms)) {
				foreach($terms as $term) {
					/* @var \WP_Term $term */
					$return[$term->term_id] = $term->name;
				}
			}

			return $return;
		}

		public function get_tags_fields(){
			$terms  = get_tags();
			$return = array();
			if(is_array($terms) && count($terms)) {
				foreach($terms as $term) {
					/* @var \WP_Term $term */
					$return[$term->term_id] = $term->name;
				}
			}

			return $return;
		}

		public function get_authors_fields(){
			$users = get_users();

			$return = array();
			foreach($users as $user) {
				$return[$user->ID] = $user->display_name;
			}

			return $return;
		}


		public function get_isotope_item_size($render_index, $packery_grids){
			$item_class = '';
			if (!empty($packery_grids['lap']) && $render_index > $packery_grids['lap']) {
				$render_index = $render_index - (floor($render_index / $packery_grids['lap']) * $packery_grids['lap']);
			}
			if (!empty($packery_grids['elem'][$render_index])) {
				if (!empty($packery_grids['elem'][$render_index]['h']) && !empty($packery_grids['elem'][$render_index]['w'])) {
					$item_class .= ' packery_extra_size-large_width_height';
				}else if(!empty($packery_grids['elem'][$render_index]['h'])){
					$item_class .= ' packery_extra_size-large_height';
				}else if(!empty($packery_grids['elem'][$render_index]['w'])){
					$item_class .= ' packery_extra_size-large_width';
				}else{
					$item_class .= ' packery_extra_size-default';
				}
			}else{
				$item_class .= ' packery_extra_size-default';
			}
			return $item_class;

		}

		public function get_img_url ($image_src = false,$settings = false,$title = false,$render_index = false){
			if ($settings) {
				$cols = 1;
				$lazyload = $settings['lazyload'];
				$natural_ratio = !empty($settings['natural_ratio']) ? $settings['natural_ratio'] : 1;

				$grid_gap = (int)$settings['grid_gap'];
				$gap = 0;

				if (!empty($settings['packery_type'])) {
					$packery_grids = $this->packery_grids[$settings['packery_type']];
				}else{
					$packery_grids = $this->packery_grids[1];
				}



				if (!empty($packery_grids['grid'])) {
					$cols = $packery_grids['grid'];
				}

				if (!empty($packery_grids['lap']) && $render_index > $packery_grids['lap']) {
					$render_index = $render_index - (floor($render_index / $packery_grids['lap']) * $packery_grids['lap']);
				}

				$packery_extra_size = '';
				if (!empty($packery_grids['elem'][$render_index])) {
					$packery_extra_size = !empty($packery_grids['elem'][$render_index]['w']) ? $packery_grids['elem'][$render_index]['w'] : 1;
					if (!empty($packery_grids['elem'][$render_index]['h']) && !empty($packery_grids['elem'][$render_index]['w'])) {
						$packery_extra_size = $packery_grids['elem'][$render_index]['h'] / $packery_grids['elem'][$render_index]['w'];
					}else if(!empty($packery_grids['elem'][$render_index]['h'])){
						$packery_extra_size = $packery_grids['elem'][$render_index]['h'] / 1;
					}else if(!empty($packery_grids['elem'][$render_index]['w'])){
						$packery_extra_size = 1 / $packery_grids['elem'][$render_index]['w'];
					}
				}




				if (!empty($image_src[0]) && strlen($image_src[0])) {
					$wp_get_attachment_url = $image_src[0];
				}

            	$ration = 1.2;
            	if ($settings['packery_type'] == 7 || $settings['packery_type'] == 6) {
            		$ration = 0.8;
            	}

	            switch ($cols) {
	            	case '1':
		            	if (!empty($wp_get_attachment_url)) {
		            		if (function_exists('gt3_get_image_srcset')) {
	                            $responsive_dimensions = array(
	                                array('1200','1600'),
	                                array('992','1200'),
	                                array('768','992'),
	                                array('600','768'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1600','1920'));
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,$lazyload);
		            		}else{
		            			if ($lazyload) {
		            				$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "1170", null, true, true, true).'"';
		            			}else{
		            				$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "1170", null, true, true, true).'"';
		            			}

		            		}
		            	}
	            		break;
	            	case '2':
	            		if (!empty($wp_get_attachment_url)) {
		            		if (function_exists('gt3_get_image_srcset')) {
	                            $responsive_dimensions = array(
	                                array('1200','800'),
	                                array('992','500'),
	                                array('768','496'),
	                                array('600','384'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1920','1200'), array('1600','960'));
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,$lazyload);
		            		}else{
		            			if ($lazyload) {
		            				$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "570", "570", true, true, true).'"';
		            			}else{
		            				$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "570", "570", true, true, true).'"';
		            			}

		            		}
		            	}
	            		break;
	            	case '3':
	            		if (function_exists('gt3_get_image_srcset')) {
	            			if (!empty($packery_extra_size) && $packery_extra_size == 0.5) {
                             	$responsive_dimensions = array(
	                                array('1200','1080'),
                                    array('992','800'),
                                    array('768','992'),
                                    array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1921','1170'), array('1600','960'));
		                        $ration = $packery_extra_size*$ration;
                            }else if (!empty($packery_extra_size) && $packery_extra_size == 2) {
                             	$responsive_dimensions = array(
	                                array('1200','540'),
	                                array('992','400'),
	                                array('768','496'),
	                                array('600','384'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('2000','800'), array('1921','500'), array('1600','480'));
		                        $ration = $packery_extra_size*$ration;
		                        $gap = $grid_gap;
                            }else if (!empty($packery_extra_size) && $packery_extra_size == 1) {
                             	$responsive_dimensions = array(
	                                array('1200','1080'),
                                    array('992','800'),
                                    array('768','992'),
                                    array('420','600')
	                            );
	                            $ration = $ration/1.5;
		                        array_unshift($responsive_dimensions, array('1921','1170'), array('1600','960'));
		                        $ration = $packery_extra_size*$ration;
                            }else{
                            	$responsive_dimensions = array(
	                                array('1200','540'),
	                                array('992','400'),
	                                array('768','496'),
	                                array('600','384'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('2000','1200'), array('1920','670'), array('1620','640'));
                            }
                            if (!empty($wp_get_attachment_url)) {
	                        	$gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,$lazyload,$gap);
	                        }
	            		}else{
	            			if (!empty($wp_get_attachment_url)) {
		            			if ($lazyload) {
		            				$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "400", "400", true, true, true).'"';
		            			}else{
		            				$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "400", "400", true, true, true).'"';
		            			}
		            		}
	            		}
	            		break;
	            	case '4':
	            		if (function_exists('gt3_get_image_srcset')) {
	                        if (!empty($packery_extra_size) && $packery_extra_size == 0.5) {
                             	$responsive_dimensions = array(
	                                array('1200','800'),
                                    array('992','600'),
                                    array('768','992'),
                                    array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1921','1170'), array('1600','960'));
		                        $ration = $packery_extra_size*$ration;
                            }else if (!empty($packery_extra_size) && $packery_extra_size == 2) {
                             	$responsive_dimensions = array(
	                                array('1200','400'),
	                                array('992','300'),
	                                array('768','496'),
	                                array('600','384'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('2000','800'), array('1921','500'), array('1600','480'));
		                        $ration = $packery_extra_size*$ration;
                            }else if (!empty($packery_extra_size) && $packery_extra_size == 1) {
                             	$responsive_dimensions = array(
	                                array('1200','800'),
                                    array('992','600'),
                                    array('768','992'),
                                    array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1921','1170'), array('1600','960'));
		                        $ration = $packery_extra_size*$ration;
                            }else{
                            	$responsive_dimensions = array(
	                                array('1200','400'),
	                                array('992','300'),
	                                array('768','496'),
	                                array('600','384'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('2000','800'), array('1921','500'), array('1600','480'));
                            }
                            if (!empty($wp_get_attachment_url)) {
	                        	$gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,$lazyload);
	                    	}
	            		}else{
	            			if (!empty($wp_get_attachment_url)) {
	            				$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "300", "300", true, true, true).'"';
	            			}
	            		}
	            		break;
	            	case '5':
	            		if (function_exists('gt3_get_image_srcset')) {
	                        if (!empty($packery_extra_size) && $packery_extra_size == 0.5) {
                             	$responsive_dimensions = array(
	                                array('1200','640'),
                                    array('992','480'),
                                    array('768','992'),
                                    array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1921','1200'), array('1600','768'));
		                        $ration = $packery_extra_size*$ration;
                            }else if (!empty($packery_extra_size) && $packery_extra_size == 2) {
                             	$responsive_dimensions = array(
	                                array('1200','320'),
	                                array('992','240'),
	                                array('768','768'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1921','600'),  array('1600','384'));
		                        $ration = $packery_extra_size*$ration;
                            }else if (!empty($packery_extra_size) && $packery_extra_size == 1) {
                             	$responsive_dimensions = array(
	                                array('1200','640'),
                                    array('992','480'),
                                    array('768','500'),
                                    array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1921','1200'), array('1600','768'));
		                        $ration = $packery_extra_size*$ration;
                            }else{
                            	$responsive_dimensions = array(
	                                array('1200','320'),
	                                array('992','240'),
	                                array('768','768'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1921','600'), array('1600','384'));
                            }
                            if (!empty($wp_get_attachment_url)) {
	                        	$gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,$lazyload);
	                        }
	            		}else{
	            			if (!empty($wp_get_attachment_url)) {
		            			if ($lazyload) {
		            				$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "384", "384", true, true, true).'"';
		            			}else{
		            				$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "384", "384", true, true, true).'"';
		            			}
		            		}
	            		}
	            		break;
	            	default:
	            		if (!empty($wp_get_attachment_url)) {
		            		if ($lazyload) {
		            			$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "1170", $ration, true, true, true).'"';
		            		}else{
		            			$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "1170", $ration, true, true, true).'"';
		            		}
		            	}

	            	}


	            $featured_image = '';
	            $gt3_featured_image_class = '';

	            if ($lazyload) {
	            	$gt3_featured_image_class .= 'gt3_lazyload';
	            }

	            if ($ration == null && !empty($natural_ratio)) {
	                $ration = round( $natural_ratio, 2, PHP_ROUND_HALF_DOWN);
	            }

	            if (!empty($wp_get_attachment_url)) {
		            if (function_exists('getSolidColorFromImage')) {
		            	$mainColor = getSolidColorFromImage($wp_get_attachment_url);
	        			$featured_image .= '<div class="gt3_blog_packery__image-placeholder'.(!empty($gt3_featured_image_class) ? ' gt3_lazyload__placeholder' : '').'" style="padding-bottom:'.(100*$ration).'%;'.(!$lazyload && !$packery_type ? 'margin-bottom:-'.(100*$ration).'%;' : '' ).'background-color:#'.$mainColor.';"></div>';
		            }
		            if ($lazyload) {
		            	$gt3_featured_image_url .= ' src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"';
		            }
	            	$featured_image .= '<img ' . $gt3_featured_image_url . (!empty($title) ? ' title="'.esc_attr($title).'"' : '') . ' alt="" '.(!empty($gt3_featured_image_class) ? ' class="'.esc_attr($gt3_featured_image_class).'"' : '').'/>';
	            }else{
	            	$featured_image .= '<div class="gt3_blog_packery__image-placeholder'.(!empty($gt3_featured_image_class) ? ' gt3_lazyload__placeholder' : '').'" style="padding-bottom:'.(100*$ration).'%;"></div>';
	            }

				return $featured_image;
			}
			return false;
		}



		public function renderItem($settings,$render_index){

			ob_start();
			$pf = get_post_format();
			$permalink = get_permalink();
			$ID = get_the_ID();
			$item_class  = array();
			$post_date = $post_author = $post_category_compile = $post_comments = '';

			// get title
			$post_title = get_the_title();
			$pf_icon = is_sticky() ? '<i class="fa fa-thumb-tack"></i>' : '';
            $listing_title = strlen( $post_title ) > 0 ? '<h3 class="blogpost_title">'.$pf_icon.'<a href="'.esc_url( $permalink ).'">'.esc_html( $post_title ).'</a></h3>' : '';

			// get post cats
			if($settings['use_filter'] || $settings['meta_categories']) {
				$categories = get_the_category();
				if(!$categories || is_wp_error($categories)) {
					$categories = array();
				}
				if(count($categories)) {
					$item_class    = array();
					$item_category = array();

					foreach($categories as $category) {
						/* @var \WP_Term $category */
						$item_class[]    = $category->slug.'_filter';
						$item_category[] = '<a href="'.get_category_link( $category->term_id ).'">'.$category->name.'</a>';
					}
					$item_category = implode(' ', $item_category);
				}
			}




			// get post cats
			if($settings['meta_categories'] != '' && !empty($categories)) {
				$post_category_compile = '<span class="post_category">';
				$post_category_compile .= $item_category;
				$post_category_compile .= '</span>';
			}

			// get post date
			if ($settings['meta_date'] == 'yes') {
				$post_date = '<span class="post_date"><a href="'. esc_url($permalink) .'">'.esc_html(get_the_time(get_option('date_format'))).'</a></span>';
			}

			// get author meta
			$icon_post_user = '';
			if(!empty($settings['meta_author'])) {
				if (!empty($settings['packery_en']) && (bool)$settings['packery_en']) {
					$icon_post_user = function_exists('gt3_svg_icons_name') ? gt3_svg_icons_name('user') : '';
				}
				$post_author = apply_filters( 'gt3/core/render/blogpackery/post_author', '<span class="post_author"><a href="'.esc_url(get_author_posts_url(get_the_author_meta('ID'))).'">' . $icon_post_user . esc_html(get_the_author_meta('display_name')).'</a></span>' );
			}

			$icon_post_comments = '';
			if(!empty($settings['meta_comments']) && (int)get_comments_number($ID) != 0) {

				$comments_num = get_comments_number(get_the_ID());

				if($comments_num == 1) {
					$comments_text = esc_html__('comment', 'gt3_themes_core');
				} else {
					$comments_text = esc_html__('comments', 'gt3_themes_core');
				}

				$icon_post_comments = function_exists('gt3_svg_icons_name') ? gt3_svg_icons_name('chat') : '';

				$post_comments = apply_filters( 'gt3/core/render/blogpackery/post_comments', '<span class="post_comments"><a href="'.esc_url(get_comments_link()).'" title="'.esc_html(get_comments_number($ID)).' '.$comments_text.'">'. $icon_post_comments . esc_html(get_comments_number($ID)) .' '.$comments_text.'</a></span>' );
			}


			$meta = apply_filters('gt3/core/render/blogpackery/listing_meta_order', array(
				'date'     => $post_date,
				'author'   => $post_author,
				'category' => $post_category_compile,
				'comments' => $post_comments,
			));
			if (!is_array($meta)) {
				$meta = array();
			}
			$post_meta = join('',$meta);

			$listing_meta = (!empty( $post_meta )) ? '<div class="listing_meta">'.$post_meta.'</div>' : '';
            $listing_meta = apply_filters( 'gt3/core/render/blogpackery/listing_meta', $listing_meta );



            $image_id = get_post_thumbnail_id();

			if ($settings) {
				if ( !empty($image_id) ) {
					$title = esc_html( $post_title );
					$image_array = image_downsize($image_id, 'full');
					if (!empty($image_array) && is_array($image_array)) {
						$image_src = array();
				        $image_src[0] = !empty($image_array[0]) ? $image_array[0] : wp_get_attachment_url($image_id);
				        if (!empty($image_array[1]) && !empty($image_array[2])) {
				            $settings['natural_ratio'] = $image_array[2] / $image_array[1];
				        }
				    }else{
				        $image_src = wp_get_attachment_image_src($image_id, 'full');
				        $ratio = null;
				    }
				    $image = $this->get_img_url($image_src,$settings,$title,$render_index);
				}else{
					$image = $this->get_img_url('',$settings,'',$render_index);
				}
			}else{
				$image = wp_get_attachment_image($image_id, 'full');
			}


			ob_start();
			if(has_excerpt($ID) && trim(get_the_excerpt())) {
				the_excerpt();
			} else {
				echo get_the_content();
			}
			$post_excerpt = ob_get_clean();

			$width  = '1170';

			$symbol_count = 120;

			$post_excerpt              = preg_replace('~\[[^\]]+\]~', '', $post_excerpt);
			$post_excerpt_without_tags = strip_tags($post_excerpt);

			$post_descr                = gt3_smarty_modifier_truncate($post_excerpt_without_tags, $symbol_count, "...");

			$key = 'key'.$render_index;

			$packery_grids = $this->packery_grids[$settings['packery_type']];

			$item_class[] = $this->get_isotope_item_size($render_index,$packery_grids);

			$item_class[] = ' render_index_'.$render_index;

			$item_class  = implode(' ', $item_class);

			if (!empty($pf)) {
    			$pf_out = '';
        		switch ($pf) {
        			case 'video':
        				$pf_post_meta = get_post_meta(get_the_ID(), 'post_format_video_oEmbed');

        				if (is_array($pf_post_meta) && !empty($pf_post_meta[0])) {
        					$this->add_script_depends( 'swipebox_js' );
							$this->add_style_depends( 'swipebox_style' );
	                        $video_src = $pf_post_meta[0];

	                        $pf_out .= '<div class="gt3_video_wrapper__thumb">';
	                        	$pf_out .= '<a href="'.esc_url($pf_post_meta[0]).'" class="swipebox-video">';
			                        $pf_out .= '<div class="gt3_video__play_button">';
			                            $pf_out .= '<svg viewBox="0 0 13 18" width="23" height="30">
                                   <polygon points="1,1 1,16 11,9" stroke-width="2" />
                               </svg>';
			                        $pf_out .= '</div>';
			                    $pf_out .= '</a>';
		                    $pf_out .= '</div>';
	                    }

        				break;


        			case 'gallery':

        				$pf_post_content = rwmb_meta('post_format_gallery_images');

    					if (!empty($pf_post_content)) {

		                    if (count($pf_post_content) == 1 || (count($pf_post_content) == 0 && !empty($image_id))) {
		                        $onlyOneImage = "format-gallery--oneImage";
		                    } else {
		                        $onlyOneImage = "";
		                    }

		                    if (count($pf_post_content) > 1) {
		                    	$image = '<div class="slider-wrapper theme-default"><div class="slides slick_wrapper">';
		                    }else{
		                    	$image = '';
		                    }

		                    foreach ($pf_post_content as $gallery_image) {

		                    	$image_id = $gallery_image["ID"];
		                    	$image .= '<div class="blog_gallery_item">';
		                    	if ($settings) {
									if ( !empty($image_id) ) {
										$title = esc_html( $post_title );
										$image_array = image_downsize($image_id, 'full');
										if (!empty($image_array) && is_array($image_array)) {
											$image_src = array();
									        $image_src[0] = !empty($image_array[0]) ? $image_array[0] : wp_get_attachment_url($image_id);
									        if (!empty($image_array[1]) && !empty($image_array[2])) {
									            $settings['natural_ratio'] = $image_array[2] / $image_array[1];
									        }
									    }else{
									        $image_src = wp_get_attachment_image_src($image_id, 'full');
									        $ratio = null;
									    }
									    $image .= $this->get_img_url($image_src,$settings,$title,$render_index);
									}else{
										$image .= $this->get_img_url('',$settings,'',$render_index);
									}
								}else{
									$image .= wp_get_attachment_image($image_id, 'full');
								}
								$image .= '</div>';
		                    }

		                    if (count($pf_post_content) > 1) {
		                    	$image .= '</div></div>';
		                    }

		                    $this->add_script_depends( 'jquery-slick' );
		                }else{
		                	$onlyOneImage = !empty($image_id) ? "format-gallery--oneImage" : '';
		                }

        				break;

        			case 'link':

        				$pf_link_out = '';

        				$link = rwmb_meta('post_format_link');
	                    $link_text = rwmb_meta('post_format_link_text');
	                    if (empty($link)) {
	                    	$link = get_permalink();
	                    }
	                    if (empty($link_text)) {
	                    	$link_text = $link;
	                    }

	                    $pf_link_out = strlen( $link_text ) > 0 ? '<h3 class="blogpost_title"><a href="'.esc_url( $link ).'">'.esc_html( $link_text ).'</a></h3>' : '';

	                    break;

	                case 'quote':
	                	$pf_quote_out = '';

	                	$quote_author = rwmb_meta('post_format_qoute_author');
	                    $quote_author_image = rwmb_meta('post_format_qoute_author_image');
	                    if (!empty($quote_author_image)) {
	                        $quote_author_image = array_values($quote_author_image);
	                        $quote_author_image = $quote_author_image[0];
	                        $quote_author_image = $quote_author_image['url'];
	                        $quote_author_image = aq_resize($quote_author_image, 150, 150, true, true, true);
	                    }else{
	                        $quote_author_image = '';
	                    }

	                    $quote_text = rwmb_meta('post_format_qoute_text');

	                    $pf_quote_out = '<div class="blog_post_media--quote">';
	                    	$pf_quote_out .= !empty($quote_text) ? '<div class="quote_text"><a href="' . esc_url( $permalink ) . '">' . esc_attr($quote_text) . '</a></div>' : '';
	                    	$pf_quote_out .= strlen($quote_author) ? '<div class="quote_author">'.(strlen($quote_author) && !empty($quote_author_image) ? '<div class="post_media_info">' . (!empty($quote_author_image) ? '<img src="'.esc_url($quote_author_image).'"  class="quote_image" alt="'.esc_attr($quote_author).'" >' : '') . '</div>' : '').'<span class="quote_author_name">' . esc_attr($quote_author) . '</span></div>' : '';
	                    $pf_quote_out .= '</div>';

	                    break;

        		}

        	}

			$this->add_render_attribute( $key, 'class', array(
				'blog_post_preview',
				'isotope_item',
				!empty($item_class) ? $item_class : '',
                is_sticky() ? 'gt3_sticky_post' : '',
                empty($image_id) ? 'empty_post_image' : '',
                'loading',
                !$settings['lazyload'] || empty($image_id) ? '' : 'lazy_loading',
                !empty($pf) ? 'format-'.$pf : 'format-standard',
                !empty($onlyOneImage) ? $onlyOneImage : ''
			) );



			echo '<div '.$this->get_render_attribute_string($key).'>';
				if(!empty($settings['post_btn_link'])) {
                    $post_btn_link = '<div class="gt3_module_button_list"><a href="'. esc_url($permalink) .'">'. $settings['post_btn_link_title'] .'</a></div>';
                } else {
                    $post_btn_link = '<div class="gt3_module_button_empty"></div>';
                }

                echo '<div class="item_wrapper">';
                	// media
                	$featured_image = '<div class="gt3_blog_packery__img_wrap">';
                		$featured_image .= '<div class="gt3_blog_packery__img">';
                		if ($pf == 'gallery') {
                			$featured_image .= $image;
                		}else{
                			if (!empty($image)) {
                    			$featured_image .= '<a href="'. esc_url($permalink) .'">'.$image.'</a>';
                    		}else{
                    			$featured_image .= '<a href="'. esc_url($permalink) .'"></a>';
                    		}
                		}


                		$featured_image .= '</div>';

                	$featured_image .= '</div>';


                	echo apply_filters( 'gt3/core/render/blogpackery/featured_image', $featured_image );

                	echo '<div class="gt3_blog_packery__text_wrap">';

                    	if (!empty($pf_out)) {
                    		echo $pf_out;
                    	}

                    	if (!empty($listing_meta) && isset($settings['meta_position']) && $settings['meta_position'] == 'before_title') {
                            echo wp_kses_post( $listing_meta );
						}

						if (!empty($pf_link_out) && $pf_link_out) {
							echo wp_kses_post($pf_link_out);
						}elseif (!empty($pf_quote_out) && $pf_quote_out) {
                        	echo wp_kses_post($pf_quote_out);
                        }else{
                        	echo wp_kses_post( apply_filters( 'gt3/core/render/blogpackery/listing_title', $listing_title ) );
                        }


                        if (!empty($listing_meta) && isset($settings['meta_position']) && $settings['meta_position'] == 'after_title') {
                            echo wp_kses_post( $listing_meta );
						}


						$listing_btn = apply_filters( 'gt3/core/render/blogpackery/listing_btn', $post_btn_link, $settings);

                        $listing_descr = strlen( $post_descr ) > 0 ? '<div class="blog_item_description">'.$post_descr.'</div>' : '';

                        $listing_share = '';


                        if (!empty($settings['meta_sharing']) && $settings['meta_sharing'] == 'yes') {
                        	if (function_exists('gt3_blog_post_sharing')) {
								ob_start();
									gt3_blog_post_sharing(true,!empty($image));
								$listing_share .= ob_get_clean();
							}
                        }

                        echo wp_kses_post( apply_filters( 'gt3/core/render/blogpackery/listing_descr', $listing_descr ) );
                        echo "<div class='gt3_blog_packery__post_footer'>".$listing_btn.$listing_share."</div>";


                    if (!empty($permalink)) {
                    	echo "<a class='gt3_blog_packery__text_wrap_link' href='".esc_url($permalink)."'></a>";
                    }

                	echo '</div>';

                echo '</div>';

            echo '</div>';
            $item = ob_get_clean();
            return $item;
		}



		public function ajax_handler(){
			header('Content-Type: application/json');

			$settings = $_POST['settings'];
			$query_args = $settings['query'];


			if(!isset($_POST['settings']['query']['exclude']) || !is_array($_POST['settings']['query']['exclude'])) {
				$_POST['settings']['query']['exclude'] = array();
				$query_args['exclude'] = array();
			}

			$query_args['post__not_in'] = $_POST['settings']['query']['exclude'];

			$query                          = new \WP_Query($query_args);
			$respond                        = '';
			$render_index = (int)$_POST['render_index'] + 1;


			while($query->have_posts()) {
				$query->the_post();
				$respond .= $this->renderItem($settings,$render_index);
				if(isset($query_args['orderby']) && $query_args['orderby'] == 'rand') {
					$_POST['settings']['query']['exclude'][] = $query->post->ID;
				}
				$render_index++;
			}

			die(wp_json_encode(array(
				'post_count' => $query->post_count,
				'respond'    => $respond,
				'max_page'   => $query->max_num_pages,
				'exclude'    => $_POST['settings']['query']['exclude'],
				'raw'        => $_POST,
			)));
		}



	}
}
