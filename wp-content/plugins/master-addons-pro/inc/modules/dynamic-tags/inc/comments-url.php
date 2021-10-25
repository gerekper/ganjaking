<?php
namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JLTMA_Comments_URL extends Data_Tag {

	public function get_name() {
		return 'jltma-comments-url';
	}

	public function get_title() {
		return esc_html__( 'Comments URL', MELA_TD );
	}

	public function get_group() {
		return 'comments';
	}

	public function get_categories() {
		return [ TagsModule::URL_CATEGORY ];
	}

	public function get_value( array $options = [] ) {
		return get_comments_link();
	}
}
