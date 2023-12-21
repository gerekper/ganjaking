<?php
namespace Happy_Addons_Pro\Widget\Skins\Post_Grid;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Outbox extends Skin_Base {

	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'outbox';
	}

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Outbox', 'happy-addons-pro' );
    }

	/**
	 * Update All Feature Image Style
	 */
	protected function all_style_of_feature_image() {

		$this->image_height_margin_style();

		$this->image_border_radius_styles();

		$this->image_css_filter_styles();

		// Add avater bg color
		$this->add_control(
			'avatar_bg',
			[
				'label' => __( 'View', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'avater_bg',
				'selectors' => [
					'{{WRAPPER}} .ha-pg-outbox .ha-pg-item .ha-pg-avatar svg' => 'fill: {{outbox_item_box_background_color.VALUE}};',
				],
			]
		);
	}

}
