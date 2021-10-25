<?php
namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JLTMA_Featured_Image_Data extends Tag {

	public function get_name() {
		return 'jltma-featured-image-data';
	}

	public function get_group() {
		return 'media';
	}

	public function get_categories() {
		return [
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
		];
	}

	public function get_title() {
		return esc_html__( 'Featured Image Data', MELA_TD );
	}

	private function get_attacment() {
		$settings = $this->get_settings();
		$id = get_post_thumbnail_id();

		if ( ! $id ) {
			return false;
		}

		return get_post( $id );
	}

	public function render() {
		$settings = $this->get_settings();
		$attachment = $this->get_attacment();

		if ( ! $attachment ) {
			return '';
		}

		$value = '';

		switch ( $settings['attachment_data'] ) {
			case 'alt':
				$value = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
				break;
			case 'caption':
				$value = $attachment->post_excerpt;
				break;
			case 'description':
				$value = $attachment->post_content;
				break;
			case 'href':
				$value = get_permalink( $attachment->ID );
				break;
			case 'src':
				$value = $attachment->guid;
				break;
			case 'title':
				$value = $attachment->post_title;
				break;
		}
		echo wp_kses_post( $value );
	}

	protected function _register_controls() {

		$this->add_control(
			'attachment_data',
			[
				'label' => esc_html__( 'Data', MELA_TD ),
				'type' => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => [
					'title' => esc_html__( 'Title', MELA_TD ),
					'alt' => esc_html__( 'Alt', MELA_TD ),
					'caption' => esc_html__( 'Caption', MELA_TD ),
					'description' => esc_html__( 'Description', MELA_TD ),
					'src' => esc_html__( 'File URL', MELA_TD ),
					'href' => esc_html__( 'Attachment URL', MELA_TD ),
				],
			]
		);
	}
}
