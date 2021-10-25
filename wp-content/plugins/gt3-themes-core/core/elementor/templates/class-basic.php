<?php
namespace GT3\Elementor\Templates;

use Elementor\Core\Base\Document;
use Elementor\Modules\Library\Documents\Library_Document;

abstract class Basic extends Library_Document {

	public function __construct(array $data = []){
		parent::__construct($data);

		add_action('admin_bar_menu', array( $this, 'add_menu_in_admin_bar' ), 500);
	}

	public static function edit_url($id = 0){
		$url = add_query_arg(
			[
				'post'   => $id,
				'action' => 'elementor',
			],
			admin_url('post.php')
		);

		return $url;
	}

	public function get_container_attributes(){
		$attributes = parent::get_container_attributes();

		$attributes['class'] .= ' gt3-template-'.$this->get_main_id().' gt3-template '.$this->get_name().'-template';

		return $attributes;
	}

	public function add_menu_in_admin_bar(\WP_Admin_Bar $wp_admin_bar){
		if (is_null($this->post)) return;
		$wp_admin_bar->add_menu(
			[
				'id'     => 'elementor_edit_doc_'.$this->get_main_id(),
				'parent' => 'elementor_edit_page',
				'title'  => sprintf('<span class="elementor-edit-link-title">%s</span><span class="elementor-edit-link-type">%s</span>', $this->get_post()->post_title, $this::get_title()),
				'href'   => $this->get_edit_url(),
			]
		);
	}
}
