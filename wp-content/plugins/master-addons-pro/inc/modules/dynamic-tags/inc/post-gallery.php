<?php
namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JLTMA_Post_Gallery extends Data_Tag {

	public function get_name() {
		return 'jltma-post-gallery';
	}

	public function get_title() {
		return esc_html__( 'Post Image Attachments', MELA_TD );
	}

	public function get_categories() {
		return [ TagsModule::GALLERY_CATEGORY ];
	}

	public function get_group() {
		return 'post';
	}

	public function get_value( array $options = [] ) {
		$images = get_attached_media( 'image' );

		$value = [];

		foreach ( $images as $image ) {
			$value[] = [
				'id' => $image->ID,
			];
		}

		return $value;
	}
}
