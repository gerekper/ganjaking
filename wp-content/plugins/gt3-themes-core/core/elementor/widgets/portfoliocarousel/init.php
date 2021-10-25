<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;
use Elementor\Utils;
use WP_Query;
use Elementor\Modules;
use Elementor\GT3_Core_Elementor_Control_Query;

if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_PortfolioCarousel')) {
	class GT3_Core_Elementor_Widget_PortfolioCarousel extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		protected function get_main_script_depends(){
			return array_merge(
				parent::get_main_script_depends(),
				array(
					'slick',
				)
			);
		}

		public function get_name(){
			return 'gt3-core-portfoliocarousel';
		}

		public function get_title(){
			return wp_sprintf( __('%s Carousel', 'gt3_themes_core'), apply_filters( "gt3_portfolio_single_label_filter", esc_html__('Portfolio', 'gt3_themes_core')) );
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-slider-3d';
		}

		protected function construct(){
//			$this->add_script_depends('slick');
//			$this->add_style_depends('slick');
		}

		public $POST_TYPE = 'portfolio';
		public $TAXONOMY = 'portfolio_category';
		public $render_index = 1;

		public $packery_grids = array(
			1 => array(
				'lap'  => 12,
				'grid' => 4,
				'elem' => array(
					1  => array( 'w' => 2, ),
					4  => array( 'w' => 2, ),
					9  => array( 'w' => 2, ),
					12 => array( 'w' => 2, ),
				)
			),
			2 => array(
				'lap'  => 8,
				'grid' => 4,
				'elem' => array(
					1 => array( 'w' => 2, 'h' => 2, ),
					4 => array( 'w' => 2, ),
					7 => array( 'w' => 2, 'h' => 2, ),
					8 => array( 'w' => 2, ),
				)
			),
			3 => array(
				'lap'  => 10,
				'grid' => 5,
				'elem' => array(
					2  => array( 'h' => 2, ),
					3  => array( 'w' => 2, ),
					4  => array( 'h' => 2, ),
					6  => array( 'w' => 2, 'h' => 2, ),
					7  => array( 'w' => 2, 'h' => 2, ),
					10 => array( 'w' => 2, ),
				)
			),
			4 => array(
				'lap'  => 8,
				'grid' => 4,
				'elem' => array(
					1 => array( 'w' => 2, 'h' => 2, ),
					3 => array( 'h' => 2 ),
					5 => array( 'h' => 2 ),
					7 => array( 'w' => 2, 'h' => 2, ),
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
				'taxonomy'   => $this->TAXONOMY,
				'hide_empty' => false,
				'slug'    => $args,
			));
			$return = array();

			if(is_array($terms) && count($terms)) {
				foreach($terms as $term) {
					/* @var \WP_Term $term */
					$return[$term->slug] = array( 'slug' => $term->slug, 'name' => $term->name );
				}
			}
			ksort($return);

			return $return;
		}

		public function renderItem($use_filter = false, $show_title = false, $show_category = false, $render_index, $settings = false){
			$item_class    = '';
			$item_category = '';
			if($use_filter || $show_category) {
				$categories = get_the_terms(get_the_ID(), $this->TAXONOMY);
				if(!$categories || is_wp_error($categories)) {
					$categories = array();
				}
				if(count($categories)) {
					$item_class    = array();
					$item_category = array();

					foreach($categories as $category) {
						/* @var \WP_Term $category */
						$item_class[]    = $category->slug;
						$item_category[] = '<span>'.$category->name.'</span>';
					}
					$item_class    = implode(' ', $item_class);
					$item_category = implode(' ', $item_category);
				}
			}
			$image_id = get_post_thumbnail_id();

			if (!empty($settings['show_text']) && $settings['show_text'] == 'yes') {
				ob_start();
				if(has_excerpt(get_the_ID()) && trim(get_the_excerpt())) {
					the_excerpt();
				} else {
					the_content();
				}
				$post_excerpt = ob_get_clean();
			}

			$symbol_count = 100;

			if(!empty($settings['show_text']) && !empty($settings['content_cut']) && $settings['show_text'] == 'yes' && $settings['content_cut'] == 'yes') {
				$symbol_count = 100;
			}

			if(!empty($settings['show_text']) && $settings['show_text'] == 'yes'){
				$post_excerpt              = preg_replace('~\[[^\]]+\]~', '', $post_excerpt);
				$post_excerpt_without_tags = strip_tags($post_excerpt);
				$post_descr                = gt3_smarty_modifier_truncate($post_excerpt_without_tags, $symbol_count, "...");
			}else{
				$post_descr = '';
			}

			if(!$image_id) {
				$image     = '<img src="'.Utils::get_placeholder_image_src().'" width="1200" height="800" alt="'.esc_attr__('Set featured image', 'gt3_themes_core').'" />';
				$image_src = Utils::get_placeholder_image_src();
			} else {
				if ($settings) {
					$image_src = wp_get_attachment_image_src($image_id, 'full');
					$title = get_the_title($image_id);
					$image = $this::get_img_url($image_src,$settings,$title,$render_index);
				}else{
					$image = wp_get_attachment_image($image_id, 'full');
				}
				$image_src = $image_src[0];
			}

			$title = get_the_title();

			$render = '';
			$render .= '<div class="portfolio_item '.$item_class.' packery_blog_item_'.$render_index.'"><div class="wrapper">';
			if(empty($settings['portfolio_btn_link']) || $settings['portfolio_btn_link'] !== 'yes' ){
				$render .= '<a href="'.esc_url(get_permalink()).'" class="lightbox" title="'.esc_attr($title).'"></a>';
			}
			$render .= '<div class="img_wrap"><div class="img" >';
			$render .= $image;
			$render .= '</div></div>';
			if((bool) $show_title || (bool) $show_category && (!empty($title) || !empty($item_category))) {
				$render .= '<div class="text_wrap">';
				if((bool) $show_title && !empty($title)) {
					$render .= '<h4 class="title"><a href="'.esc_url(get_permalink()).'" title="'.esc_attr($title).'">'.esc_html($title).'</a></h4>';
				}
				if((bool) $show_category && !empty($item_category)) {
					$render .= '<div class="categories">'.$item_category.'</div>';
				}
				if(!empty($settings['show_text']) && $settings['show_text'] == 'yes' && !empty($post_descr)) {
					$render .= '<div class="portfolio_item_desc">'.wp_kses_post( $post_descr ) .'</div>';
				}

				if(!empty($settings['portfolio_btn_link']) && $settings['portfolio_btn_link'] == 'yes' ) {
					$render .= '<div class="gt3_module_button_list"><a href="'. esc_url(get_permalink()) .'" title="'.esc_attr($title).'">'. $settings['portfolio_btn_link_title'] .'</a></div>';
				}

				$render .= apply_filters( 'gt3_portfolio_carousel_content_after', '', $settings );
				$render .= '</div>';
			}

			$render .= '</div></div>';

			return $render;
		}

		public function get_img_url ($image_src = false,$settings = false,$title = false,$render_index = false){
			if ($settings) {

				$cols = !empty($settings['items_per_line']) ? $settings['items_per_line'] : '1';

				if (!empty($image_src[0]) && strlen($image_src[0])) {
					$wp_get_attachment_url = $image_src[0];

					if (!empty($settings['image_ration'])) {
						$ration = apply_filters('gt3/core/elementor/widgets/portfoliocarousel/init/image_ration',$settings['image_ration'], $settings);
		            }else{
		                $ration = null;
		            }
		            switch ($cols) {
		            	case '1':
		            		if (function_exists('gt3_get_image_srcset')) {
	                            $responsive_dimensions = array(
	                                array('1200','1600'),
	                                array('992','1200'),
	                                array('768','992'),
	                                array('600','768'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1600','1920'));
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions);
		            		}else{
		            			$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "1170", null, true, true, true).'"';
		            		}
		            		break;
		            	case '2':
		            		if (function_exists('gt3_get_image_srcset')) {
	                            $responsive_dimensions = array(
	                                array('1200','800'),
	                                array('992','500'),
	                                array('768','496'),
	                                array('600','384'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('1920','1200'), array('1600','960'));
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions);
		            		}else{
		            			$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "570", "570", true, true, true).'"';
		            		}
		            		break;
		            	case '3':
		            		if (function_exists('gt3_get_image_srcset')) {
	                            $responsive_dimensions = array(
	                                array('1200','540'),
	                                array('992','400'),
	                                array('768','496'),
	                                array('600','384'),
	                                array('420','600')
	                            );
		                        array_unshift($responsive_dimensions, array('2000','1200'), array('1920','670'), array('1620','640'));
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions);
		            		}else{
		            			$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "400", "400", true, true, true).'"';
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
			                        $ration = $packery_extra_size;
	                            }else if (!empty($packery_extra_size) && $packery_extra_size == 2) {
	                             	$responsive_dimensions = array(
		                                array('1200','400'),
		                                array('992','300'),
		                                array('768','496'),
		                                array('600','384'),
		                                array('420','600')
		                            );
			                        array_unshift($responsive_dimensions, array('2000','800'), array('1921','500'), array('1600','480'));
			                        $ration = $packery_extra_size;
	                            }else if (!empty($packery_extra_size) && $packery_extra_size == 1) {
	                             	$responsive_dimensions = array(
		                                array('1200','800'),
                                        array('992','600'),
                                        array('768','992'),
                                        array('420','600')
		                            );
			                        array_unshift($responsive_dimensions, array('1921','1170'), array('1600','960'));
			                        $ration = $packery_extra_size;
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
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions);
		            		}else{
		            			$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "300", "300", true, true, true).'"';
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
			                        $ration = $packery_extra_size;
	                            }else if (!empty($packery_extra_size) && $packery_extra_size == 2) {
	                             	$responsive_dimensions = array(
		                                array('1200','320'),
		                                array('992','240'),
		                                array('768','768'),
		                                array('420','600')
		                            );
			                        array_unshift($responsive_dimensions, array('1921','600'),  array('1600','384'));
			                        $ration = $packery_extra_size;
	                            }else if (!empty($packery_extra_size) && $packery_extra_size == 1) {
	                             	$responsive_dimensions = array(
		                                array('1200','640'),
                                        array('992','480'),
                                        array('768','500'),
                                        array('420','600')
		                            );
			                        array_unshift($responsive_dimensions, array('1921','1200'), array('1600','768'));
			                        $ration = $packery_extra_size;
	                            }else{
	                            	$responsive_dimensions = array(
		                                array('1200','320'),
		                                array('992','240'),
		                                array('768','768'),
		                                array('420','600')
		                            );
			                        array_unshift($responsive_dimensions, array('1921','600'), array('1600','384'));
	                            }
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions);
		            		}else{
		            			$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "384", "384", true, true, true).'"';
		            		}
		            		break;
		            	default:
	                    	$gt3_featured_image_url = 'src="'.aq_resize($wp_get_attachment_url, "1170", $ration, true, true, true).'"';
		            }

	            	$featured_image = '<img ' . $gt3_featured_image_url . (!empty($title) ? ' title="'.esc_attr($title).'"' : '') . ' alt="" />';

				}else{
					$featured_image = '';
				}
				return $featured_image;
			}
			return false;
		}

	}
}











