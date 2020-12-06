<?php
namespace GroovyMenu;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


class VirtualPagesPageTemplate {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class.
	 *
	 * @param array $add_templates
	 *
	 * @return VirtualPagesPageTemplate
	 */
	public static function getInstance( $add_templates = array() ) {

		if ( null === self::$instance ) {
			self::$instance = new VirtualPagesPageTemplate( $add_templates );
		}

		return self::$instance;

	}

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 *
	 * @param array $add_templates
	 */
	private function __construct( $add_templates = array() ) {

		if ( ! is_array( $add_templates ) ) {
			$add_templates = array(
				'template/Preview.php' => 'GroovyMenu preset preview',
			);
		}

		$this->templates = array();

		// Add a filter to the template include to determine if the page has our template assigned and return it's path.
		add_filter(
			'template_include',
			array( $this, 'viewProjectTemplate' )
		);

		// Add your templates to this array.
		$this->templates = $add_templates;

	}

	/**
	 * Checks if the template is assigned to the page
	 *
	 * @param string $template path to template file.
	 *
	 * @return string
	 */
	public function viewProjectTemplate( $template ) {

		// Get global post.
		global $post;

		// Return template if post is empty.
		if ( ! $post ) {
			return $template;
		}

		// Return template if post->is_virtual is empty.
		if ( ! isset( $post->is_virtual ) || ! $post->is_virtual ) {
			return $template;
		}

		// Return template if post->gm_vp_flag_on is empty.
		if ( ! isset( $post->gm_vp_flag_on ) || ! $post->gm_vp_flag_on ) {
			return $template;
		}

		// Return template if post->page_template is empty.
		if ( empty( $post->page_template ) ) {
			return $template;
		}

		$page_template = $post->page_template;

		// Return default template if we don't have a custom one defined.
		if ( ! isset( $this->templates[ $page_template ] ) ) {
			return $template;
		}

		if ( ! defined( 'GROOVY_MENU_DIR' ) ) {
			$plugin_dir = plugin_dir_path( __FILE__ ) . '../';
		} else {
			$plugin_dir = GROOVY_MENU_DIR;
		}

		$file = str_replace( array(
			'\\',
			'/',
		), DIRECTORY_SEPARATOR, $plugin_dir . $page_template );

		// Just to be safe, we check if the file exist first.
		if ( file_exists( $file ) ) {
			return $file;
		}

		// Return template.
		return $template;

	}

}
