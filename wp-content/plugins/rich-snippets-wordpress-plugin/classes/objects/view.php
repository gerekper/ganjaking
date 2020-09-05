<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class View.
 *
 * Renders a view.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class View {

	/**
	 * The instance.
	 *
	 * @var View
	 *
	 * @since 2.0.0
	 */
	protected static $_instance = null;


	/**
	 * If the init method has been called.
	 *
	 * @var bool
	 *
	 * @since 2.0.0
	 */
	private $initialized = false;


	/**
	 * The current template name.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	private $template_name = '';


	/**
	 * The arguments.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $arguments = array();


	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return   View
	 *
	 * @since 2.0.0
	 */
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}


	/**
	 * Magic function for cloning.
	 *
	 * Disallow cloning as this is a singleton class.
	 *
	 * @since 2.0.0
	 */
	protected function __clone() {
	}


	/**
	 * Magic method for setting upt the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {
	}


	/**
	 * Initializes admin stuff
	 */
	public function init() {

		if ( $this->initialized ) {
			return;
		}


		$this->initialized = true;
	}


	/**
	 * Renders a view.
	 *
	 * If a template is called as a static method, it will be rendered @param string $name
	 *
	 * @param array $arguments
	 *
	 * @return bool Always returns true.
	 * @since 2.0.0
	 *
	 * @see   View::render()
	 *
	 */
	public static function __callStatic( $name, $arguments ) {

		$instance                = self::instance();
		$instance->arguments     = $arguments;
		$instance->template_name = $name;

		if ( ! method_exists( $instance, $name ) ) {

			return $instance->render();
		}

		return true;
	}


	/**
	 * Renders a view.
	 *
	 * @return bool Always returns true.
	 * @since 2.0.0
	 *
	 */
	public function render() {

		$name = str_replace( array( '_', '-' ), '/', $this->template_name );

		/**
		 * Template name filter.
		 *
		 * Change the name of the template file.
		 *
		 * @hook  wpbuddy/rich_snippets/view/name
		 *
		 * @param {string} $name Template file name.
		 *
		 * @returns {string}
		 *
		 * @since 2.0.0
		 */
		$name = apply_filters(
			'wpbuddy/rich_snippets/view/name',
			$name
		);

		$file = plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'classes/view/' . $name . '.php';


		/**
		 * Template file filter.
		 *
		 * Change the path to the template file.
		 *
		 * @hook  wpbuddy/rich_snippets/view/file
		 *
		 * @param {string} $file Template file path.
		 * @param {string} $name Template file name.
		 *
		 * @returns {string} Path to template file.
		 *
		 * @since 2.0.0
		 */
		$file = apply_filters( 'wpbuddy/rich_snippets/view/file', $file, $name );

		$pro_file = plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'pro/classes/view/' . $name . '.php';

		if ( ! is_file( $file ) || is_file( $pro_file ) ) {
			$file = apply_filters( 'wpbuddy/rich_snippets/view/file', $pro_file, $name );
		}


		if ( is_file( $file ) ) {
			/**
			 * Before view render action.
			 *
			 * Add data before a template file gets rendered.
			 *
			 * @hook  wpbuddy/rich_snippets/view/render/before
			 *
			 * @param {string} $name Filename.
			 * @param {string} $file File path.
			 *
			 * @since 2.0.0
			 */
			do_action( 'wpbuddy/rich_snippets/view/render/before', $name, $file );

			/**
			 * Dynamic before view render action.
			 *
			 * Add data before ta template file gets rendered.
			 *
			 * @hook  wpbuddy/rich_snippets/view/render/before/{$name}
			 *
			 * @param {string} $file File path.
			 *
			 * @since 2.0.0
			 */
			do_action( 'wpbuddy/rich_snippets/view/render/before/' . $name, $file );

			include $file;

			/**
			 * After view render action.
			 *
			 * Add data after a template file gets rendered.
			 *
			 * @hook  wpbuddy/rich_snippets/view/render/after
			 *
			 * @param {string} $name Template file name.
			 * @param {string} $file Template file path.
			 *
			 * @since 2.0.0
			 */
			do_action( 'wpbuddy/rich_snippets/view/render/after', $name, $file );

			/**
			 * Dynamic after view render action.
			 *
			 * Add data after a template file gets rendered.
			 *
			 * @hook  wpbuddy/rich_snippets/view/render/after/{$name}
			 *
			 * @param {string} $file Template file path.
			 *
			 * @since 2.0.0
			 */
			do_action( 'wpbuddy/rich_snippets/view/render/after/' . $name, $file );
		}

		return true;
	}

}
