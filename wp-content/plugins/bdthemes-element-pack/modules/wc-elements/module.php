<?php

namespace ElementPack\Modules\WcElements;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function get_name() {
        return 'wc-elements';
    }

    public function get_widgets() {

        $widgets = ['WC_Elements'];

        return $widgets;
    }

    public function add_product_post_class( $classes ) {
		$classes[] = 'product';

		return $classes;
	}

	public function add_products_post_class_filter() {
		add_filter( 'post_class', [ $this, 'add_product_post_class' ] );
	}

	public function remove_products_post_class_filter() {
		remove_filter( 'post_class', [ $this, 'add_product_post_class' ] );
	}

	// public function register_wc_hooks() {
	// 	wc()->frontend_includes();
	// }



}
