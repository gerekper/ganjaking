<?php

namespace ElementPack\Modules\WcProducts\Skins;

use Elementor\Controls_Manager;
use Elementor\Skin_Base;
use ElementPack\Base\Module_Base;
use ElementPack\Modules\WcProducts\Module;
use ElementPack\Modules\WcProducts\Widgets\Products;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Classic extends Skin_Base {

	protected function _register_controls_actions() {
		parent::_register_controls_actions();
		add_action('elementor/element/bdt-wc-products/section_woocommerce_layout/after_section_start', [$this, 'register_controls']);
	}

	public function get_id() {
		return 'classic';
	}

	public function get_title() {
		return esc_html__('Classic', 'bdthemes-element-pack');
	}

	public function register_controls(Module_Base $widget) {
		$this->parent = $widget;

		$this->add_control(
			'columns',
			[
				'label' => esc_html__('Columns', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
				],
				'default' => '4',
			]
		);
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();
		$this->parent->query_posts();

		$query = $this->parent->get_query();

		if (!$query->have_posts()) {
			return;
		}

		global $woocommerce_loop;

		$woocommerce_loop['columns'] = (int) $this->get_instance_value('columns');

		Module::instance()->add_products_post_class_filter();

		echo '<div class="woocommerce columns-' . $woocommerce_loop['columns'] . '">';

		woocommerce_product_loop_start();

		while ($query->have_posts()) : $query->the_post();
			wc_get_template_part('content', 'product');
		endwhile;

		woocommerce_product_loop_end();

		wc_reset_loop();

		wp_reset_postdata();

		echo '</div>';

		Module::instance()->remove_products_post_class_filter();
	}

	public function render_amp() {
	}
}
