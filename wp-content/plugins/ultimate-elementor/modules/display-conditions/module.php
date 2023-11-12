<?php
/**
 * UAEL Display Conditions feature.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\DisplayConditions;

use Elementor\Controls_Manager;

use UltimateElementor\Base\Module_Base;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Module_Base {

	/**
	 * Module should load or not.
	 *
	 * @since 1.32.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Get Module Name.
	 *
	 * @since 1.32.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'uael-display-conditions';
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.32.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-frontend-script' );
	}

	/**
	 * Check if this is a widget.
	 *
	 * @since 1.32.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public function is_widget() {
		return false;
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.32.0
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'DisplayConditions',
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		if ( UAEL_Helper::is_widget_active( 'DisplayConditions' ) ) {

			add_action( 'elementor/element/common/_section_style/after_section_end', array( __CLASS__, 'add_controls_sections' ), 1, 2 );
			// Activate column for column.
			add_action( 'elementor/element/column/section_advanced/after_section_end', array( __CLASS__, 'add_controls_sections' ), 1, 2 );
			// Activate sections for sections.
			add_action( 'elementor/element/section/section_advanced/after_section_end', array( __CLASS__, 'add_controls_sections' ), 1, 2 );

			self::create_files();
		}
	}

	/**
	 * Creates required files/directories for maxmind DB.
	 *
	 * @since 1.35.1
	 * @access private
	 */
	private static function create_files() {
		// Allow us to easily interact with the filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		// Install files and folders for uploading files and prevent hotlinking.
		$upload_dir = wp_upload_dir();
		$files      = array(
			'base'    => $upload_dir['basedir'] . '/uael_uploads',
			'file'    => '.htaccess',
			'content' => 'deny from all',
		);
		if ( wp_mkdir_p( $files['base'] ) && ! file_exists( trailingslashit( $files['base'] ) . $files['file'] ) ) {
			$wp_filesystem->put_contents( $files['base'] . '/' . $files['file'], $files['content'], FS_CHMOD_FILE );
		}
	}

	/**
	 * Added display condition section.
	 *
	 * @since 1.32.0
	 *
	 * @param array $element returns controls array.
	 * @param array $args return arguments.
	 * @access public
	 */
	public static function add_controls_sections( $element, $args ) {

			$element->start_controls_section(
				'display_conditions_section',
				array(
					'tab'   => Controls_Manager::TAB_ADVANCED,
					/* translators: %s admin link */
					'label' => sprintf( __( '%1s - Display Conditions', 'uael' ), UAEL_PLUGIN_SHORT_NAME ),
				)
			);

				include_once 'display-conditions.php';

				$call_controls = new Display_Conditions();
				$call_controls->add_controls( $element, $args );

			$element->end_controls_section();
	}


}
