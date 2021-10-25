<?php 
namespace MasterHeaderFooter\Theme_Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * TwentyNineteen support for the header footer.
 */
class TwentyNineteen {

	/**
	 * Run all the Actions / Filters.
	 */
	function __construct($template_ids) {
		if($template_ids[0] != null){
			add_action( 'get_header', [ $this, 'get_header' ] );
		}
		if($template_ids[1] != null){
			add_action( 'get_footer', [ $this, 'get_footer' ] );
		}
		if($template_ids[2] != null){
			add_filter('comments_template', array($this, 'jltma_get_comment_form'));
		}
	}


	public function jltma_get_comment_form( $comment_template ){
        ob_start();
        return JLTMA_PLUGIN_PATH . '/inc/view/theme-support-comment.php';
		ob_get_clean();
	}


	public function get_header( $name ) {
		add_action('masteraddons/template/after_header', function(){
			echo '<div id="page" class="site">';
			echo '<div id="content" class="site-content">';
		});
		
		require JLTMA_PLUGIN_PATH . '/inc/view/theme-support-header.php';

		$templates = [];
		$name = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "header-{$name}.php";
		}

		$templates[] = 'header.php';

		// Avoid running wp_head hooks again
		remove_all_actions( 'wp_head' );
		ob_start();
		// It cause a `require_once` so, in the get_header it self it will not be required again.
		locate_template( $templates, true );
		ob_get_clean();
	}

	public function get_footer( $name ) {
		add_action('masteraddons/template/after_footer', function(){
			echo '</div></div>';
		});

		require JLTMA_PLUGIN_PATH . '/inc/view/theme-support-footer.php';

		$templates = [];
		$name = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "footer-{$name}.php";
		}

		$templates[] = 'footer.php';

		ob_start();
		// It cause a `require_once` so, in the get_header it self it will not be required again.
		locate_template( $templates, true );
		ob_get_clean();
	}


}