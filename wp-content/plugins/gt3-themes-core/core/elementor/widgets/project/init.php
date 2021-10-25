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

if(!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_Project')) {
	class GT3_Core_Elementor_Widget_Project extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-project';
		}

		public function get_title(){
			return apply_filters( "gt3_project_single_label_filter", esc_html__('Project', 'gt3_themes_core'));
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-posts-grid';
		}

		protected function construct(){
			$this->add_style_depends('gt3-core/widgets/gt3-core-button');
			$this->add_style_depends('gt3-theme/widgets/gt3-core-button');

			add_action('wp_ajax_gt3_themes_core_project_load_items', array( $this, 'ajax_handler' ));
			add_action('wp_ajax_nopriv_gt3_themes_core_project_load_items', array( $this, 'ajax_handler' ));


		}

		protected function get_main_script_depends(){
			return array_merge(
				parent::get_main_script_depends(),
				array(
					'gt3-core/isotope',
					'imagesloaded'
				)
			);
		}

		public $POST_TYPE = 'project';
		public $TAXONOMY = 'project_category';
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

		public function ajax_handler(){
			header('Content-Type: application/json');

			if(isset($_POST['use_filter']) && ($_POST['use_filter'] === true || $_POST['use_filter'] == 'true')) {
				$_POST['use_filter'] = true;
			} else {
				$_POST['use_filter'] = false;
			}

			if(isset($_POST['show_title']) && ($_POST['show_title'] === true || $_POST['show_title'] == 'true')) {
				$_POST['show_title'] = true;
			} else {
				$_POST['show_title'] = false;
			}

			if(isset($_POST['random']) && ($_POST['random'] === true || $_POST['random'] == 'true')) {
				$_POST['random'] = true;
			} else {
				$_POST['random'] = false;
			}

			if(isset($_POST['show_category']) && ($_POST['show_category'] === true || $_POST['show_category'] == 'true')) {
				$_POST['show_category'] = true;
			} else {
				$_POST['show_category'] = false;
			}

			if(!isset($_POST['query']['exclude']) || !is_array($_POST['query']['exclude'])) {
				$_POST['query']['exclude'] = array();
			}

			$_POST['query']['post__not_in'] = $_POST['query']['exclude'];

			$query                          = new WP_Query($_POST['query']);
			$respond                        = '';
			$render_index = (int)$_POST['render_index'] + 1;
			while($query->have_posts()) {
				$query->the_post();
				$respond .= $this->renderItem($_POST['use_filter'], $_POST['show_title'], $_POST['show_category'],$render_index,$_POST['settings']);
				if($_POST['random']) {
					$_POST['query']['exclude'][] = $query->post->ID;
				}
				$render_index++;
			}

			die(wp_json_encode(array(
				'post_count' => $query->post_count,
				'respond'    => $respond,
				'max_page'   => $query->max_num_pages,
				'exclude'    => $_POST['query']['exclude'],
				'raw'        => $_POST,
			)));
		}

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


		public function renderItem($use_filter = false, $show_title = false, $show_category = false, $show_description = false, $render_index, $settings = false){
			$item_class    = '';
			$item_category = '';
			$item_description = get_post_meta( get_the_id(), "project_short_desc", true );
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

			if(!$image_id) {
				$image     = '<img src="'.Utils::get_placeholder_image_src().'" width="1200" height="800" alt="'.esc_attr__('Set featured image', 'gt3_themes_core').'" />';
				$image_src = Utils::get_placeholder_image_src();
			} else {
				if ($settings) {
					$image_src = wp_get_attachment_image_src($image_id, 'full');
					$title = get_the_title($image_id);
					$image = $this::get_img_url($image_src,$settings,$title,$render_index);

				}else{
					$image     = wp_get_attachment_image($image_id, 'full');
				}
				$image_src = $image_src[0];
			}

			$title = get_the_title();

			$render = '';
			$render .= '<div class="isotope_item lazy_loading loading '.$item_class.' packery_blog_item_'.$render_index.'"><div class="wrapper">';
			$render .= '<a href="'.esc_url(get_permalink()).'" class="lightbox" title="'.esc_attr(get_the_title()).'">';
			$render .= '<div class="img_wrap"><div class="img" >';
			$render .= $image;
			$render .= '</div></div>';
			if((bool) $show_title || (bool) $show_category || (bool) $show_description && (!empty($title) || !empty($item_category) || !empty($item_description))) {
				$render .= '<div class="text_wrap">';

				$render .= apply_filters( 'gt3_module_project_before_title', '', $settings );

				if((bool) $show_title && !empty($title)) {
					$render .= '<h4 class="title">'.get_the_title().'</h4>';
				}
				if((bool) $show_category && !empty($item_category)) {
					$render .= '<div class="categories">'.$item_category.'</div>';
				}
				if((bool) $show_description && !empty($item_description)) {
					$render .= '<div class="description">'.$item_description.'</div>';
				}
				$render .= '</div>';
			}

			$render .= '</a>';
			$render .= '</div></div>';

			return $render;
		}

		public function get_img_url ($image_src = false,$settings = false,$title = false,$render_index = false){
			if ($settings) {

				$grid_type = $settings['grid_type'];
				$cols = $settings['cols'];
				$show_type = $settings['show_type'];


				if ($show_type == 'packery') {

					$packery_grids = $this->packery_grids[$settings['packery_type']];

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
				}

				if (!empty($image_src[0]) && strlen($image_src[0])) {
					$wp_get_attachment_url = $image_src[0];
					if (!empty($grid_type) && $grid_type != 'vertical' && $show_type != 'masonry') {
		                switch ($grid_type) {
		                    case 'square':
		                        $ration = 1;
		                        break;
		                    case 'rectangle':
		                        $ration = 0.8;
		                        break;
		                    default:
		                        $ration = null;
		                        break;
		                }
		            }else{
		                $ration = null;
		            }
		            if ($show_type == 'packery') {
		            	$ration = 1;
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
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,true);
		            		}else{
		            			$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "1170", null, true, true, true).'"';
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
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,true);
		            		}else{
		            			$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "570", "570", true, true, true).'"';
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
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,true);
		            		}else{
		            			$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "400", "400", true, true, true).'"';
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
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,true);
		            		}else{
		            			$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "300", "300", true, true, true).'"';
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
		                        $gt3_featured_image_url = gt3_get_image_srcset($wp_get_attachment_url,$ration,$responsive_dimensions,true);
		            		}else{
		            			$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "384", "384", true, true, true).'"';
		            		}
		            		break;
		            	default:
	                    	$gt3_featured_image_url = 'data-src="'.aq_resize($wp_get_attachment_url, "1170", $ration, true, true, true).'"';
		            }

		            $featured_image = '';

		            if ($ration == null && !empty($natural_ratio)) {
		                $ration = round( $natural_ratio, 2, PHP_ROUND_HALF_DOWN);
		            }

		            if (function_exists('getSolidColorFromImage')) {
		            	$mainColor = getSolidColorFromImage($wp_get_attachment_url);
		            	$featured_image .= '<div class="gt3_portfolio_list__image-placeholder gt3_lazyload__placeholder" style="padding-bottom:'.(100*$ration).'%;background-color:#'.$mainColor.';"></div>';
		            }

    	            $gt3_featured_image_url .= ' src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"';


	            	$featured_image .= '<img ' . $gt3_featured_image_url . (!empty($title) ? ' title="'.esc_attr($title).'"' : '') . ' alt="" class="gt3_lazyload" />';

				}else{
					$featured_image = '';
				}
				return $featured_image;
			}
			return false;
		}

	}
}











