<?php


defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function add_actions() {
		add_action( 'elementor/widgets/register', [ $this, 'on_widgets_registered' ] );

	}
	
	public function on_widgets_registered() {
		$this->includes();
		$this->register_widget();
	}

	
	private function includes() {
		require __DIR__ . '/test_widget.class.php';
	}

	
	private function register_widget() {
		\Elementor\Plugin::instance()->widgets_manager->register( new ElementorWidgetTest() );
	}
	
}

new Plugin();
