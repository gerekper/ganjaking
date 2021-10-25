<?php
namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JLTMA_Post_Excerpt extends Tag {

	public function get_name() {
		return 'jltma-post-excerpt';
	}

	public function get_title() {
		return esc_html__( 'Post Excerpt', MELA_TD );
	}

	public function get_group() {
		return 'post';
	}

	public function get_categories() {
		return [ TagsModule::TEXT_CATEGORY ];
	}

	public function render() {
		// Allow only a real `post_excerpt` and not the trimmed `post_content` from the `get_the_excerpt` filter
		$post = get_post();

		if ( ! $post || empty( $post->post_excerpt ) ) {
			return;
		}

		echo wp_kses_post( $post->post_excerpt );
	}
}
