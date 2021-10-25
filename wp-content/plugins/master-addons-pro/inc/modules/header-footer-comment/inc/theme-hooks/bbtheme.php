<?php 
namespace MasterHeaderFooter\Theme_Hooks;
use MasterHeaderFooter\Master_Header_Footer;

defined( 'ABSPATH' ) || exit;

/**
 * BB theme theme compatibility.
 */
class Bbtheme {

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
		$this->header = $template_ids[0];
		$this->footer = $template_ids[1];
		$this->comment = $template_ids[2];

		if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
			$this->elementor = \Elementor\Plugin::instance();
		}

		if($this->header != null){
			add_filter( 'fl_header_enabled', '__return_false' );
			add_action( 'fl_before_header', [$this, 'add_plugin_header_markup'] );
		}

		if($this->footer != null){
			add_filter( 'fl_footer_enabled', '__return_false' );
			add_action( 'fl_after_content', [$this, 'add_plugin_footer_markup'] );
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


	// header actions
	public function add_plugin_header_markup(){

		if(class_exists('\FLTheme')){
			$header_layout = \FLTheme::get_setting( 'fl-header-layout' );

			if ( 'none' == $header_layout || is_page_template( 'tpl-no-header-footer.php' ) ) {
				return;
			}
		}

		do_action('masteraddons/template/before_header');
		?>
			<header id="masthead" itemscope="itemscope" itemtype="https://schema.org/WPHeader">
				<div class="jltma-template-content-markup jltma-template-content-header">
					<?php echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($this->header); ?>
				</div>
			</header>
			<style>
				[data-type="header"] {
					display: none !important;
				}
			</style>
		<?php
		do_action('masteraddons/template/after_header');
  }
 

	// footer actions
	  public function add_plugin_footer_markup(){
			if ( is_page_template( 'tpl-no-header-footer.php' ) ) {
				return;
			}
	
			do_action('masteraddons/template/before_footer'); ?>

				<footer itemscope="itemscope" itemtype="https://schema.org/WPFooter">
				<?php echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($this->footer); ; ?>
				</footer>

			<?php 
			do_action('masteraddons/template/after_footer');
    }
 

}