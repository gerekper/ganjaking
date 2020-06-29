<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Mfn_Elementor
{
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION = '7.0';
	private static $_instance = null;

	/**
	 * Instance
	 * Ensures only one instance of the class is loaded or can be loaded.
	 */

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 */

	public function __construct()
	{

		add_action( 'after_setup_theme', [ $this, 'init' ] );

	}

	/**
	 * Initialize
	 */

	public function init(){

		// Check if Elementor installed and activated

		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Check for required Elementor version

		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Add actions

		require_once( get_theme_file_path( '/functions/plugins/elementor/class-mfn-elementor-helper.php' ) );

		add_action( 'elementor/elements/categories_registered', 'Mfn_Elementor_Helper::categories_registered' );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );

		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_styles_editor' ] );

	}

	/*
	 * Frontend styles
	 */

	public function enqueue_styles() {

		wp_enqueue_style( 'mfn-elementor', get_theme_file_uri( '/functions/plugins/elementor/assets/elementor.css' ) );

	}

	/*
	 * Editor styles
	 */

	public function enqueue_styles_editor() {

		wp_enqueue_style( 'mfn-elementor-editor', get_theme_file_uri( '/functions/plugins/elementor/assets/elementor-editor.css' ) );

	}

	/**
	 * Admin notice
	 * Warning when the site doesn't have a minimum required Elementor version.
	 */

	public function admin_notice_minimum_elementor_version() {

		// if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] ); // plugin only

		$message = 'Theme requires <strong>Elementor</strong> version <strong>'. self::MINIMUM_ELEMENTOR_VERSION .'</strong> or greater.';
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 * Warning when the site doesn't have a minimum required PHP version.
	 */

	public function admin_notice_minimum_php_version() {

		// if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = 'Theme requires <strong>PHP</strong> version <strong>'. self::MINIMUM_PHP_VERSION .'</strong> or greater.';
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Init Widgets
	 * Include widgets files and register them
	 */

	public function init_widgets() {

		$widgets = [
			'accordion',
			'article-box',
			'before-after',
			'blockquote',
			'blog',
			'blog-news',
			'blog-slider',
			'blog-teaser',
			'call-to-action',
			'chart',
			'clients',
			'clients-slider',
			'code',
			'contact-box',
			'countdown',
			'counter',
			'fancy-divider',
			'fancy-heading',
			'faq',
			'feature-box',
			'feature-list',
			'flat-box',
			'gallery',
			'helper',
			'hover-box',
			'hover-color',
			'how-it-works',
			'icon-box',
			'info-box',
			'list',
			'offer',
			'offer-thumb',
			'opening-hours',
			'our-team',
			'photo-box',
			'portfolio',
			'portfolio-grid',
			'portfolio-photo',
			'portfolio-slider',
			'pricing-item',
			'progress-bars',
			'promo-box',
			'quick-fact',
			'shop',
			'shop-slider',
			'slider',
			'sliding-box',
			'story-box',
			'tabs',
			'testimonials',
			'testimonials-list',
			'timeline',
			'trailer-box',
			'zoom-box',
		];

		foreach( $widgets as $widget ){

			require_once( get_theme_file_path( '/functions/plugins/elementor/class-mfn-elementor-widget-'. $widget .'.php' ) );

			$class = '\Mfn_Elementor_Widget_'. str_replace( ' ', '_', ucfirst(str_replace( '-', ' ', $widget )));
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new $class() );

		}

	}

}

Mfn_Elementor::instance();
