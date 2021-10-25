<?php

namespace ElementorModal\Widgets;

use ElementorModal\Widgets\GT3_Core_Widget_Base;
use GT3_Post_Type_Gallery;


if(!defined('ABSPATH')) {
	exit;
}


if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_GalleryPackery')) {
	class GT3_Core_Elementor_Widget_GalleryPackery extends GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-gallerypackery';
		}

		public function get_title(){
			return esc_html__('GalleryPackery', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-gallery-justified';
		}

		public $packery_grids = array(
			1 => array(
				'lap'  => 12,
				'grid' => 4,
				'elem' => array(
					1 => array( 'w' => 2, ),
					4 => array( 'w' => 2, ),
					9 => array( 'w' => 2, ),
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
			5 => array(
				'lap'  => 4,
				'grid' => 12,
				'elem' => array(
					1 => array( 'w' => 7, 'h' => 10, ),
					2 => array( 'w' => 5, 'h' => 5, ),
					3 => array( 'w' => 5, 'h' => 5, ),
					4 => array( 'w' => 12, 'h' => 12, ),
				)
			),
			6 => array(
				'lap'  => 9,
				'grid' => 4,
				'elem' => array(
					5 => array( 'w' => 2, 'h' => 2, ),
				)
			),
			7 => array(
				'lap'  => 12,
				'grid' => 15,
				'elem' => array(
					1 => array( 'w' => 5, 'h' => 5, ),
					2 => array( 'w' => 5, 'h' => 5, ),
					3 => array( 'w' => 5, 'h' => 7, ),
					4 => array( 'w' => 5, 'h' => 7, ),
					5 => array( 'w' => 5, 'h' => 5, ),
					6 => array( 'w' => 5, 'h' => 5, ),
					7 => array( 'w' => 5, 'h' => 5, ),
					8 => array( 'w' => 5, 'h' => 5, ),
					9 => array( 'w' => 5, 'h' => 5, ),
					10 => array( 'w' => 5, 'h' => 7, ),
					11 => array( 'w' => 5, 'h' => 5, ),
					12 => array( 'w' => 5, 'h' => 5, ),
				)
			),
		);

		public function get_script_depends(){
			return array(
				'gt3_isotope_js',
				'elementor-blueimp-gallery',
				'imagesloaded',
			);
		}

		protected function construct() {
			add_action('wp_ajax_gt3_core_packery_load_images', array( $this, 'ajax_handler' ));
			add_action('wp_ajax_nopriv_gt3_core_packery_load_images', array( $this, 'ajax_handler' ));
		}

		public function ajax_handler(){
			header('Content-Type: application/json');

			$respond = '';
			if(isset($_POST['lightbox']) && ($_POST['lightbox'] === true || $_POST['lightbox'] == 'true')) {
				$_POST['lightbox'] = true;
			} else {
				$_POST['lightbox'] = false;
			}

			if(isset($_POST['title']) && ($_POST['title'] === true || $_POST['title'] == 'true')) {
				$_POST['title'] = true;
			} else {
				$_POST['title'] = false;
			}

			if(isset($_POST['show_category']) && ($_POST['show_category'] === true || $_POST['show_category'] == 'true')) {
				$_POST['show_category'] = true;
			} else {
				$_POST['show_category'] = false;
			}
			$gallery_items = array();

			foreach($_POST['images'] as $image) {
				if($_POST['lightbox']) {
					$image           = wp_prepare_attachment_for_js($image['id']);
					$gallery_items[] = array(
						'href'        => $image['url'],
						'title'       => $image['title'],
						'thumbnail'   => $image['sizes']['thumbnail']['url'],
						'description' => $image['caption'],
						'is_video'    => 0,
						'image_id'    => $image['id'],
					);
				}
				$respond .= $this->renderItem($image, $_POST['source'], $_POST['lightbox'], $_POST['title'], $_POST['show_category']);
			}

			die(wp_json_encode(array(
				'post_count'    => count($_POST['images']),
				'respond'       => $respond,
				'gallery_items' => $gallery_items,
			)));
		}

		public function serializeImages(&$settings){
			$array = array();
			switch($settings['select_source']) {
				case 'gallery':
					$settings['slides'] = GT3_Post_Type_Gallery::get_gallery_images($settings['gallery']);
				case 'module':
					if(is_array($settings['slides']) && count($settings['slides'])) {
						foreach($settings['slides'] as $key => $image) {
							$array[] = array( 'id' => $image );
						}
					}
					break;
				case 'categories':
					$args = array(
						'post_status'    => 'publish',
						'post_type'      => GT3_Post_Type_Gallery::post_type,
						'order'          => 'desc',
						'paged'          => 1,
						'posts_per_page' => -1,
					);
					if(isset($settings['categories']) && !empty($settings['categories'])) {
						$args['tax_query']   = array(
							'relation' => 'AND',
						);
						$args['tax_query'][] = array(
							'field'    => 'slug',
							'taxonomy' => GT3_Post_Type_Gallery::taxonomy,
							'operator' => 'IN',
							'terms'    => $settings['categories'],
						);
					}
					$module_wp_query = new \WP_Query($args);
					$slides          = array();
					if($module_wp_query->post_count) {
						$max_count = 0;
						while($module_wp_query->have_posts()) {
							$module_wp_query->the_post();
							/* @var \WP_Post $image_post */
							$gallery_id     = get_the_ID();
							$images_gallery = GT3_Post_Type_Gallery::get_gallery_images($gallery_id);
							if(isset($images_gallery) && is_array($images_gallery) && count($images_gallery)) {
								$categories = get_the_terms($gallery_id, GT3_Post_Type_Gallery::taxonomy);
								if(!$categories || is_wp_error($categories)) {
									$categories = array();
								}
								if(count($categories)) {
									foreach($categories as $category) {
										/* @var \WP_Term $category */
										if(!isset($settings['filter_array'][$category->slug])
											&& is_array($settings['categories'])
											&& count($settings['categories'])
											&& in_array($category->slug, $settings['categories'])) {
											$settings['filter_array'][$category->slug] = array(
												'slug' => $category->slug,
												'name' => $category->name,
											);
										}
									}
								}
								foreach($images_gallery as $slide) {
									$slides[$gallery_id][] = array(
										'id' => $slide,
										'p'  => $gallery_id,
									);
								}
								if($max_count < count($slides[$gallery_id])) {
									$max_count = count($slides[$gallery_id]);
								}
							}
						}
						for($i = 0; $i < $max_count; $i++) {
							foreach($slides as $slide_array) {
								if(isset($slide_array[$i])) {
									$array[] = $slide_array[$i];
								}
							}
						}

						wp_reset_postdata();
					}
					break;
			}
			$settings['slides'] = $array;
		}


		public function renderItem($image, $source, $lightbox, $title, $show_category){
			$item_class    = '';
			$item_category = '';
			if($source == 'categories' && isset($image['p'])) {
				$categories = get_the_terms($image['p'], GT3_Post_Type_Gallery::taxonomy);
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
			$image = wp_prepare_attachment_for_js($image['id']);

			$render = '';
			$render .= '<div class="isotope_item loading '.$item_class.'"><div class="wrapper">';
			if((bool) $lightbox) {
				$render .= '<a href="'.esc_url($image['url']).'" class="lightbox">';
			}

			$render .= '<div class="img_wrap"><div class="img">';
			$render .= wp_get_attachment_image($image['id'], 'full');
			$render .= '</div></div>';

			if((bool) $title || (bool) $show_category && (!empty($image['title']) || !empty($item_category))) {
				$render .= '<div class="text_wrap">';
				if((bool) $title) {
					$render .= '<h4 class="title">'.esc_html($image['title']).'</h4>';
				}
				if((bool) $show_category && !empty($item_category)) {
					$render .= '<div class="categories">'.$item_category.'</div>';
				}
				$render .= '</div>';
			}

			if((bool) $lightbox) {
				$render .= '</a>';
			}
			$render .= '</div></div>';

			return $render;
		}

	}
}











