<?php
namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JLTMA_Shortcode extends Tag {
	public function get_name() {
		return 'jltma-shortcode';
	}

	public function get_title() {
		return esc_html__( 'Shortcode', MELA_TD );
	}

	public function get_group() {
		return 'site';
	}

	public function get_categories() {
		return [
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::GALLERY_CATEGORY,
			TagsModule::IMAGE_CATEGORY,
			TagsModule::MEDIA_CATEGORY,
		];
	}

	protected function _register_controls() {
		$this->add_control(
			'shortcode',
			[
				'label' => esc_html__( 'Shortcode', MELA_TD ),
				'type'  => Controls_Manager::TEXTAREA,
			]
		);
	}

	public function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['shortcode'] ) ) {
			return;
		}

		$shortcode_string = $settings['shortcode'];

		$value = do_shortcode( $shortcode_string );

		/**
		 * Should Escape.
		 *
		 * Used to allow 3rd party to avoid shortcode dynamic from escaping
		 *
		 * @since 2.2.1
		 *
		 * @param bool defaults to true
		 */
		$should_escape = apply_filters( 'jltma/core_elements/dynamic_tags/shortcode/should_escape', true );

		if ( $should_escape ) {
			$value = wp_kses_post( $value );
		}

		echo $value;
	}
}
