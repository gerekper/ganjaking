<?php

namespace ElementorLayerSlider;

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

final class LS_Elementor {

	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	const MINIMUM_PHP_VERSION = '5.4';


	private static $_instance = null;


	public static function instance() {

		if( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	public function widget_scripts() {

		ls_enqueue_slider_library();

		wp_enqueue_style(
			'ls-elementor',
			LS_ROOT_URL.'/static/admin/css/elementor.css',
			array( 'elementor-editor' ),
			LS_PLUGIN_VERSION
		);

		wp_enqueue_script(
			'ls-elementor-backend',
			LS_ROOT_URL.'/static/admin/js/elementor-backend.js',
			array( 'kreatura-modal-window' ),
			LS_PLUGIN_VERSION,
			true
		);

		wp_localize_script(
			'ls-elementor-backend',
			'LS_Widget', array(
				'editorUrl'	=> admin_url( 'admin.php?page=layerslider&action=edit&id=' ),
				'i18n'		=> array(
					'modalTitle' => __('Quick Edit LayerSlider', 'LayerSlider'),
					'ChangesYouMadeMayNotBeSaved' => __( 'Changes you made may not be saved. Are you sure you want to continue?', 'LayerSlider' ),
				),
			)
		);
	}


	private function include_widgets_files() {

		require_once( LS_ROOT_PATH . '/classes/class.ls.elementor.widget.php' );
	}


	public function register_widgets() {

		// Load Widget files
		$this->include_widgets_files();

		// Register Widget
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\LS_Elementor_Widget() );

		// Override user provided advanced settings in order to
		// force loading all scripts in the preview window.
		if( is_admin() || ! empty( $_GET['elementor-preview'] ) ) {
			add_filter( 'ls_conditional_script_loading', function( $value ) { return false; } );
			add_filter( 'ls_include_at_footer', function( $value ) { return false; } );
			add_filter( 'ls_load_all_js_files', function( $value ) { return true; } );
		}
	}


	public function init() {

		// Check if Elementor installed and activated
		if( ! did_action( 'elementor/loaded' ) ) {
			return false;
		}

		// Check for required Elementor version
		if( version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '<' ) ) {
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			return false;
		}

		// Register widget scripts
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'widget_scripts' ) );

		// Register widgets
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );
	}


	private function __construct() {

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}
}

LS_Elementor::instance();
