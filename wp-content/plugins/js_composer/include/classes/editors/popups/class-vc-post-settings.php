<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Post settings like custom css for page are displayed here.
 *
 * @since 4.3
 */
class Vc_Post_Settings {
	protected $editor;

	/**
	 * @param $editor
	 */
	public function __construct( $editor ) {
		$this->editor = $editor;
	}

	public function editor() {
		return $this->editor;
	}

	public function renderUITemplate() {
		vc_include_template( 'editors/popups/vc_ui-panel-post-settings.tpl.php',
		array(
			'box' => $this,
			'can_unfiltered_html_cap' =>
				vc_user_access()->part( 'unfiltered_html' )->checkStateAny( true, null )->get(),
		) );
	}
}
