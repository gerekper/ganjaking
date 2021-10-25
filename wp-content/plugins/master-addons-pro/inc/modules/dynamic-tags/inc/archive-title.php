<?php
namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use MasterAddons\Inc\Helper\Master_Addons_Helper;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JLTMA_Archive_Title extends Tag {
	public function get_name() {
		return 'jltma-archive-title';
	}

	public function get_title() {
		return esc_html__( 'Archive Title', MELA_TD );
	}

	public function get_group() {
		return 'archive';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	public function render() {
		$include_context = 'yes' === $this->get_settings( 'include_context' );

		$title = Master_Addons_Helper::jltma_get_page_title( $include_context );

		echo wp_kses_post( $title );
	}

	protected function _register_controls() {
		$this->add_control(
			'include_context',
			[
				'label' => esc_html__( 'Include Context', MELA_TD ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
	}
}
