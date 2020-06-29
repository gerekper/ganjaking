<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Elementor Groovy Menu Plugin Widget.
 *
 * Elementor widget that inserts an Groovy Menu content into the page.
 *
 * @since 2.1.1
 */
class Elementor_Groovy_Menu_Plugin extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since  2.1.1
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'groovy_menu_plugin';
	}

	/**
	 * Get widget title.
	 *
	 * @since  2.1.1
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Groovy Menu', 'groovy-menu' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since  2.1.1
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-bars groovy-menu-plugin-icon';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Groovy Menu plugin widget belongs to.
	 *
	 * @since  2.1.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general', 'theme-elements' ];
	}

	public function get_keywords() {
		return [ 'menu', 'mega', 'grooni', 'header' ];
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since  2.1.1
	 * @access protected
	 */
	protected function render() {

		if ( function_exists( 'groovy_menu' ) ) {
			groovy_menu();
		}

	}

}
