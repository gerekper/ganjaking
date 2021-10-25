<?php 
namespace MasterHeaderFooter\Theme_Hooks;
use MasterHeaderFooter\Master_Header_Footer;
use MasterHeaderFooter\Inc\Comments;

defined( 'ABSPATH' ) || exit;

/**
 * Astra theme compatibility.
 */
class Astra {

	/**
	 * Instance of Elementor Frontend class.
	 *
	 * @var \Elementor\Frontend()
	 */
	private $elementor;

	private $header;
	private $footer;
	private $comment;

	/**
	 * Run all the Actions / Filters.
	 */
	function __construct($template_ids) {
		
		$this->header 	= $template_ids[0];
		$this->footer  	= $template_ids[1];
		$this->comment 	= $template_ids[2];
		
		if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
			$this->elementor = \Elementor\Plugin::instance();
		}

		if($this->header != null){
			add_action( 'template_redirect', array( $this, 'jltma_remove_theme_header_markup' ), 10 );
			add_action( 'astra_header', [$this, 'add_plugin_header_markup'] );
		}

		if($this->footer != null){
			add_action( 'template_redirect', array( $this, 'remove_theme_footer_markup' ), 10 );
			add_action( 'astra_footer', [$this, 'add_plugin_footer_markup'] );
		}

		if($this->comment != null){
			remove_filter( 'comment_form_default_fields', 'astra_comment_form_default_fields_markup' );
			add_filter('comments_template', array($this, 'jltma_add_comment_markup'));
		}

	}


    public function jltma_add_comment_markup(){
    	return JLTMA_PLUGIN_PATH . '/inc/view/theme-support-comment.php';
    }


	// header actions
	public function jltma_remove_theme_header_markup() {
		remove_action( 'astra_header', 'astra_header_markup' );
    }
    
    public function add_plugin_header_markup(){
			do_action('masteraddons/template/before_header');
			echo '<div class="jltma-template-content-markup jltma-template-content-header">';
				echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($this->header);
			echo '</div>';
			do_action('masteraddons/template/after_header');
    }
 

	// footer actions
	public function remove_theme_footer_markup() {
		remove_action( 'astra_footer', 'astra_footer_markup' );
    }

    public function add_plugin_footer_markup(){
			do_action('masteraddons/template/before_footer');
			echo '<div class="jltma-template-content-markup jltma-template-content-footer">';
			echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($this->footer); ;
			echo '</div>';
			do_action('masteraddons/template/after_footer');
    }
 
}