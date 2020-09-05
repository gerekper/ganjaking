<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Plugin Update Information class.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Plugin_Update_Information extends Dictionary {

	public $basename = '';
	public $id = '';
	public $slug = '';
	public $plugin = '';
	public $new_version = '';
	public $url = '';
	public $package = '';
	public $upgrade_notice = '';
	public $name = '';
	public $version = '';
	public $author = '';
	public $requires = '';
	public $homepage = '';
	public $downloaded = '';
	public $download_link = '';
	public $external = true;
	public $tested = '';
	public $icons = [];

	/**
	 * @var Plugin_Update_Information_Sections
	 */
	public $sections = object;


	/**
	 * Plugin_Update_Information constructor.
	 *
	 * @param array $data
	 *
	 * @since 2.0.0
	 */
	public function __construct( array $data ) {

		parent::__construct( $data );

		if ( is_array( $this->sections ) ) {
			$this->sections = new Plugin_Update_Information_Sections( $this->sections );
		}

		if ( ! $this->sections instanceof Plugin_Update_Information_Sections ) {
			$this->sections = new Plugin_Update_Information_Sections( array() );
		}
	}

}
