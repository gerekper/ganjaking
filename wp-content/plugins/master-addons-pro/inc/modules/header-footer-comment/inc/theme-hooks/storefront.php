<?php
namespace MasterHeaderFooter\Theme_Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Header & Footer will replace force fully
 */

class Storefront {

	/**
	 * Instance of Storefront.
	 *
	 * @var Storefront
	 */
	private static $instance;

	private $header;
	private $footer;
	private $comment;


	function __construct($template_ids) {

		$this->header  = $template_ids[0];
		$this->footer  = $template_ids[1];
		$this->comment = $template_ids[2];

		if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
			$this->elementor = \Elementor\Plugin::instance();
		}

		if($this->header != null){
			add_action( 'template_redirect', [ $this, 'setup_header' ], 10 );
			add_action( 'storefront_before_header', 'hfe_render_header', 500 );
			add_action( 'wp_enqueue_scripts', [ $this, 'styles' ] );
		}

		if($this->footer != null){
			add_action( 'storefront_before_footer', 'hfe_render_before_footer' );
			add_action( 'wp_enqueue_scripts', [ $this, 'styles' ] );
		}

		if($this->comment != null){
			add_filter('comments_template', array($this, 'jltma_get_comment_form'));
		}

	}


	public function jltma_get_comment_form( $comment_template ){
        ob_start();
        return JLTMA_PLUGIN_PATH . '/inc/view/theme-support-comment.php';
		ob_get_clean();
	}

	public function styles() {
		$css = '';

		if ( true === hfe_header_enabled() ) {
			$css .= '.site-header {
				display: none;
			}';
		}

		if ( true === hfe_footer_enabled() ) {
			$css .= '.site-footer {
				display: none;
			}';
		}

		wp_add_inline_style( 'hfe-style', $css );
	}

	/**
	 * Disable header from the theme.
	 */
	public function setup_header() {
		for ( $priority = 0; $priority < 200; $priority ++ ) {
			remove_all_actions( 'storefront_header', $priority );
		}
	}

	/**
	 * Disable footer from the theme.
	 */
	public function setup_footer() {
		for ( $priority = 0; $priority < 200; $priority ++ ) {
			remove_all_actions( 'storefront_footer', $priority );
		}
	}

}