<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_BlogBoxed')) {
	class GT3_Core_Elementor_Widget_BlogBoxed extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name(){
			return 'gt3-core-blogboxed';
		}

		public function get_title(){
			return esc_html__('Blog Boxed', 'gt3_themes_core');
		}

		public function get_icon(){
			return 'gt3-core-elementor-icon eicon-archive-title';
		}

		public $POST_TYPE = 'post';
		public $TAXONOMY = 'category';

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

	}
}
