<?php
namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JLTMA_Archive_Meta extends Tag {

	public function get_name() {
		return 'jltma-archive-meta';
	}

	public function get_title() {
		return esc_html__( 'Archive Meta', MELA_TD );
	}

	public function get_group() {
		return 'archive';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	public function get_panel_template() {
		return ' ({{{ key }}})';
	}

	public function render() {
		$key = $this->get_settings( 'key' );

		if ( empty( $key ) ) {
			return;
		}

		$value = '';

		if ( is_category() || is_tax() ) {
			$value = get_term_meta( get_queried_object_id(), $key, true );
		} elseif ( is_author() ) {
			$value = get_user_meta( get_queried_object_id(), $key, true );
		}

		echo wp_kses_post( $value );
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}

	protected function _register_controls() {
		$this->add_control(
			'key',
			[
				'label' => esc_html__( 'Meta Key', MELA_TD ),
			]
		);
	}
}
