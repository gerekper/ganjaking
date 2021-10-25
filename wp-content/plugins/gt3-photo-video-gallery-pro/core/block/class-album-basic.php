<?php

namespace GT3\PhotoVideoGalleryPro\Block;
defined('ABSPATH') OR exit;

use GT3_Post_Type_Gallery;
use WP_Query;
use GT3\PhotoVideoGalleryPro\Lazy_Images;

abstract class Album_Basic extends Isotope_Gallery {
	const POST_TYPE = GT3_Post_Type_Gallery::post_type;
	const TAXONOMY = GT3_Post_Type_Gallery::taxonomy;
	const TAXONOMY_TAG = null;

	protected function getDefaultsAttributes(){
		return array_merge(
			parent::getDefaultsAttributes(),
			array(
				'paginationType' => array(
					'type'    => 'string',
					'default' => 'default',
				),
			)
		);
	}

	protected function construct(){
		add_action("wp_ajax_gt3pg_pro/load_more/{$this->name}", array( $this, 'ajax_handler' ));
		add_action("wp_ajax_nopriv_gt3pg_pro/load_more/{$this->name}", array( $this, 'ajax_handler' ));

		$this->add_script_depends('imageloaded');
		$this->add_script_depends('isotope');
		$this->add_script_depends('youtube_api');
		$this->add_script_depends('vimeo_api');
	}

	protected function getDefaultSettings(){
		return GT3_Post_Type_Gallery::instance()->getSettings();
	}

	public function get_taxonomy($args){
		$terms  = get_terms(array(
			'taxonomy'   => self::TAXONOMY,
			'hide_empty' => false,
			'slug'       => $args,
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

	public static function buildQuery($value){
		$value_args = array(
			'post_status' => array( 'publish' ),
			'post_type'   => static::POST_TYPE,
			'taxonomy'    => array(),
			'tags'        => array(),
		);

		if(!empty($value['posts_per_page'])) {
			$value_args['posts_per_page'] = $value['posts_per_page'];
		}
		if(!empty($value['orderby'])) {
			$value_args['orderby'] = $value['orderby'];
		}
		if(!empty($value['order'])) {
			$value_args['order'] = $value['order'];
		}
		if(!empty($value['post__in'])) {
			$value_args['post__in'] = $value['post__in'];
		} else {
			if(!empty($value['ignore_sticky_posts']) && (bool) $value['ignore_sticky_posts']) {
				$value_args['ignore_sticky_posts'] = '1';
			}

			if(!empty($value['author__in'])) {
				$value_args['author__in'] = $value['author__in'];
			}

			if(!empty($value['taxonomy']) || !empty($value['tags'])) {
				$value_args['tax_query'] = array(
					'relation' => 'AND',
				);
			}

			if(null !== static::TAXONOMY && static::isIds($value['taxonomy'])) {
				$value['taxonomy'] = static::getSlugById(static::TAXONOMY, $value['taxonomy']);
			}
			if(!empty($value['taxonomy'])) {
				$value_args['tax_query'][] = array(
					'field'    => 'slug',
					'taxonomy' => static::TAXONOMY,
					'operator' => 'IN',
					'terms'    => $value['taxonomy'],
				);
			}

			if(null !== static::TAXONOMY && static::isIds($value['tags'])) {
				$value['tags'] = static::getSlugById(static::TAXONOMY_TAG, $value['tags']);
			}
			if(!empty($value['tags'])) {
				$value_args['tax_query'][] = array(
					'field'    => 'slug',
					'taxonomy' => static::TAXONOMY_TAG,
					'operator' => 'IN',
					'terms'    => $value['tags'],
				);
			}
		}

		return $value_args;
	}

	public static function getSlugById($taxonomy, $ids){
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

	public static function isIds($ids){
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

	public function ajax_handler(){
		header('Content-Type: application/json');
		if(!isset($_POST['query']) || !is_array($_POST['query'])) {
			$this->ajaxResponse(array(
				'error'   => true,
				'message' => 'Query not found',
			));
		}
		$query_args = array_merge(
			array(
				'post_status'    => array( 'publish' ),
				'posts_per_page' => 4,
				'post__not_in'   => array(),
			),
			$_POST['query'],
			array(
				'post_type' => $this::POST_TYPE,
				'paged'     => 1,
			)
		);

		if(!isset($_POST['settings']) || !is_array($_POST['settings'])) {
			$_POST['settings'] = array();
		}

		$default_settings = $this->getDefaultSettings();
		$default_settings = array_merge(
			$default_settings['basic'],
			key_exists($this->name, $default_settings) ? $default_settings[$this->name] : array()
		);

		$settings = array_merge(array(
			'imageSize' => $default_settings['imageSize'],
			'showTitle' => $default_settings['showTitle'],
			'lazyLoad'  => $default_settings['lazyLoad'],
		), $_POST['settings']);
		$settings = $this->checkTypeSettings($settings);

		$query    = new WP_Query($query_args);
		$response = '';
		if($settings['lazyLoad']) {
			Lazy_Images::instance()->setup_filters();
		}
		if($query->found_posts) {
			while($query->have_posts()) {
				$query->the_post();
				$response                     .= $this->renderItem($query->post, $settings);
				$query_args['post__not_in'][] = $query->post->ID;
			}
		}
		if($settings['lazyLoad']) {
			Lazy_Images::instance()->remove_filters();
		}

		$this->ajaxResponse(array(
			'post_count'   => $query->post_count,
			'render'       => $response,
			'hasMore'      => !!($query->max_num_pages-1),
			'maxPages'     => $query->max_num_pages,
			'post__not_in' => $query_args['post__not_in'],
		));
	}

	private function ajaxResponse($response){
		die(wp_json_encode($response));
	}
}
