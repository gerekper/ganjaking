<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if(!defined('ABSPATH')) {
	exit;
}

if (!class_exists('ElementorModal\Widgets\GT3_Core_Elementor_Widget_ShopList')) {
	class GT3_Core_Elementor_Widget_ShopList extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		protected function get_main_script_depends(){
			return array_merge(
				parent::get_main_script_depends(),
				array(
					'imagesloaded'
				)
			);
		}

		public function get_title() {
            return esc_html__('Shop List', 'gt3_themes_core');
        }

        public function get_icon() {
            return 'gt3-core-elementor-icon eicon-posts-grid';
        }


		public function get_name() {
            return 'gt3-core-shoplist';
        }

        public function get_woo_category() {
            $return = array();
            if (class_exists('WooCommerce')) {
                $product_categories = get_terms('product_cat', 'orderby=count&hide_empty=0');
                if (is_array($product_categories)) {
                    foreach ($product_categories as $cat) {
                        $return[$cat->slug] = $cat->name . ' (' . $cat->slug . ')';
                    }
                }
            }

            return $return;
        }
	}
}











