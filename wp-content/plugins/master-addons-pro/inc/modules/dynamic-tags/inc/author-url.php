<?php
namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JLTMA_Author_URL extends Data_Tag {

	public function get_name() {
		return 'jltma-author-url';
	}

	public function get_group() {
		return 'author';
	}

	public function get_categories() {
		return [ TagsModule::URL_CATEGORY ];
	}

	public function get_title() {
		return esc_html__( 'Author URL', MELA_TD );
	}

	public function get_panel_template_setting_key() {
		return 'url';
	}

	public function get_value( array $options = [] ) {
		$value = '';

		if ( 'archive' === $this->get_settings( 'url' ) ) {
			global $authordata;

			if ( $authordata ) {
				$value = get_author_posts_url( $authordata->ID, $authordata->user_nicename );
			}
		} else {
			$value = get_the_author_meta( 'url' );
		}

		return $value;
	}

	protected function _register_controls() {
		$this->add_control(
			'url',
			[
				'label' => esc_html__( 'URL', MELA_TD ),
				'type' => Controls_Manager::SELECT,
				'default' => 'archive',
				'options' => [
					'archive' => esc_html__( 'Author Archive', MELA_TD ),
					'website' => esc_html__( 'Author Website', MELA_TD ),
				],
			]
		);
	}
}
