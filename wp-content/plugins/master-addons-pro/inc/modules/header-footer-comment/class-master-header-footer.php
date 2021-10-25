<?php
namespace MasterHeaderFooter;

defined( 'ABSPATH' ) || exit;

if( !class_exists('Master_Header_Footer') ){

	class Master_Header_Footer{

		public $dir;

		public $url;

		private static $plugin_path;

	    private static $plugin_url;

	    private static $_instance = null;

		const MINIMUM_PHP_VERSION = '5.6';

	    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

		private static $plugin_name = 'Master Header Footer & Comment Form Builder';

	    public function __construct(){
			
			$this->jltma_include_files();

	        add_action('admin_footer', [$this, 'jltma_header_footer_modal_view']);
    	}

		public function jltma_include_files(){
	        include JLTMA_PLUGIN_PATH . '/inc/cpt.php';
	        include JLTMA_PLUGIN_PATH . '/inc/api/rest-api.php';
	        include JLTMA_PLUGIN_PATH . '/inc/api/cpt-api.php';
	        include JLTMA_PLUGIN_PATH . '/inc/cpt-hooks.php';
	        include JLTMA_PLUGIN_PATH . '/inc/jltma-activator.php';
	        include JLTMA_PLUGIN_PATH . '/inc/header-footer-assets.php';
	        include JLTMA_PLUGIN_PATH . '/inc/api/handler-api.php';
	        include JLTMA_PLUGIN_PATH . '/inc/api/select2-api.php';
	        include JLTMA_PLUGIN_PATH . '/inc/comments/class-comments-builder.php';
		}

		public function jltma_header_footer_modal_view(){
			$screen = get_current_screen();
			if($screen->id == 'edit-master_template'){
				include_once JLTMA_PLUGIN_PATH . '/inc/view/modal-options.php';
			}			
		}


	    public static function render_elementor_content_css($content_id){
	        if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
	            $css_file = new \Elementor\Core\Files\CSS\Post( $content_id );
	            $css_file->enqueue();
	        }
	    }

		public static function render_elementor_content($content_id){
			$elementor_instance = \Elementor\Plugin::instance();
			return $elementor_instance->frontend->get_builder_content_for_display( $content_id , true);
		}

	    public static function get_instance() {
	        if ( is_null( self::$_instance ) ) {
	            self::$_instance = new self();
	        }
	        return self::$_instance;
	    }
	}
}

