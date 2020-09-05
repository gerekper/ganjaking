<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Support.
 *
 * Admin support actions.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.19.0
 */
class Admin_Support_Controller extends \wpbuddy\rich_snippets\Admin_Support_Controller {

	/**
	 * Add metaboxes for the support page.
	 *
	 * @since 2.19.0
	 */
	public function add_metaboxes() {
		parent::add_metaboxes();

		add_meta_box(
			'support-validity',
			_x( 'Support', 'metabox title', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_support_metabox_validity' ),
			'rich-snippets-support',
			'side',
			'high'
		);

		add_meta_box(
			'support-deactivate-site',
			_x( 'License Deactivation', 'metabox title', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_support_metabox_deactivation' ),
			'rich-snippets-support',
			'side',
			'high'
		);

		add_meta_box(
			'support-features',
			__( 'Feature Requests', 'rich-snippets-schema' ),
			array( '\wpbuddy\rich_snippets\View', 'admin_support_metabox_features' ),
			'rich-snippets-support',
			'normal'
		);
	}
}