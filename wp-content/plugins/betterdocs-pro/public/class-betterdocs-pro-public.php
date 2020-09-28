<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/public
 * @author     WPDeveloper <support@wpdeveloper.net>
 */
class Betterdocs_Pro_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_filter( 'betterdocs_docs_layout_select_choices', array( $this, 'customizer_docs_page_layout_choices' ) );
		add_filter( 'archive_template', array( $this, 'get_docs_archive_template' ) , 100 );
		add_filter( 'betterdocs_single_layout_select_choices', array( $this, 'customizer_single_layout_choices' ) );
		add_filter( 'single_template', array( $this, 'get_docs_single_template' ), 100 );
		add_filter( 'template_include', array( $this, 'load_docs_taxonomy_template' ), 100 );
		add_action( 'betterdocs_docs_before_social', array( $this, 'betterdocs_article_reactions' ) );
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Betterdocs_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Betterdocs_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/betterdocs-pro-public.css', array(), $this->version, 'all' );
		$layout_select = get_theme_mod('betterdocs_single_layout_select', 'layout-1');
		if($layout_select === 'layout-2'){
			wp_enqueue_style( 'betterdocs-layout-2', BETTERDOCS_PRO_PUBLIC_URL . 'css/template/layout-2.css', array(), $this->version, 'all' );
		}elseif($layout_select === 'layout-3'){
			wp_enqueue_style( 'betterdocs-layout-3', BETTERDOCS_PRO_PUBLIC_URL . 'css/template/layout-3.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Betterdocs_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Betterdocs_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/betterdocs-pro-public.js', array( 'jquery' ), $this->version, true );
		
		$enable_instant_answer = BetterDocs_DB::get_settings('enable_disable');
		if($enable_instant_answer == 'off') {
			$single_reactions = array(
				'FEEDBACK' => array(
					'DISPLAY' => true,
					'TEXT'    => esc_html__('How did you feel?','betterdocs-pro'),
					'SUCCESS' => esc_html__('Thanks for your feedback','betterdocs-pro'),
					'URL'     => home_url() . '?rest_route=/betterdocs/feedback',
				),
			);
			wp_localize_script($this->plugin_name, 'betterdocs', $single_reactions);
		}
	}

	/**
	 * Get Docs Page Template for docs base directory.
	 *
	 * @param int $archive_template Override.
	 * 
	 * @since    1.0.2
	 */
	public function get_docs_archive_template( $archive_template ) {

		if ( is_post_type_archive( 'docs' ) ) {
			
			$multikb_layout = get_theme_mod('betterdocs_multikb_layout_select', 'layout-1');
			$layout_select = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');

			if ( BetterDocs_Multiple_Kb::$enable == 1 && $multikb_layout === 'layout-2') {

				$archive_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/multiple-kb-2.php';

			} elseif ( BetterDocs_Multiple_Kb::$enable == 1 ) {

				$archive_template = BETTERDOCS_PRO_PUBLIC_PATH. 'partials/archive-template/multiple-kb.php';

			} elseif ( $layout_select === 'layout-2' ) {

				$archive_template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';

			} elseif ( $layout_select === 'layout-3' ) {

				$archive_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-box-3.php';

			} elseif ( $layout_select === 'layout-4' ) {

				$archive_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-list-2.php';

			} else {

				$archive_template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';

			}

		}

		return $archive_template;
	}

	public function customizer_docs_page_layout_choices($choices) {

		$choices['layout-3'] = array(
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-3.png',
		);
		$choices['layout-4'] = array(
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-4.png',
		);
		return $choices;

	}

	/**
	 * Get Single Page Template for docs base directory.
	 *
	 * @param int $single_template Overirde single templates.
	 * 
	 * @since    1.0.0
	 */
	public function get_docs_single_template( $single_template ) {

		if ( is_singular( 'docs' ) ) {

			$layout_select = get_theme_mod('betterdocs_single_layout_select', 'layout-1');
			if( $layout_select === 'layout-2' ) {
				$single_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/template-single/layout-2.php';
			} elseif ( $layout_select === 'layout-3' ) {
				$single_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/template-single/layout-3.php';
			} else {
				$single_template = BETTERDOCS_PUBLIC_PATH . 'partials/template-single/layout-1.php';
			}

		}
		return $single_template;
	}

	public function customizer_single_layout_choices($choices){

		$choices['layout-2'] = array(
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-2.png',
		);
		$choices['layout-3'] = array(
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-3.png',
		);
		return $choices;

	}

	public function load_docs_taxonomy_template( $template ) {

		$docs_layout = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');
		$tax = BetterDocs_Helper::get_tax();

		if ( $tax === 'doc_category' ) {

			$template = BETTERDOCS_PUBLIC_PATH . 'betterdocs-category-template.php';

		} elseif ( is_tax( 'doc_tag' ) ) {

			$template = BETTERDOCS_PUBLIC_PATH . 'betterdocs-tag-template.php';

		} elseif ( $tax === 'knowledge_base' && $docs_layout === 'layout-2') {

			$template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';

		} elseif ( $tax === 'knowledge_base' && $docs_layout === 'layout-3') {

			$template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-box-3.php';

		} elseif ( $tax === 'knowledge_base' && $docs_layout === 'layout-4') {

			$template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-list-2.php';

		} elseif ( $tax === 'knowledge_base' ) {

			$template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';
			
		}

		return $template;
	}

	public function betterdocs_article_reactions( $reactions = '' ) {

		$post_reactions = get_theme_mod( 'betterdocs_post_reactions', true );

		if ( $post_reactions == true ) {

			$reactions = do_shortcode( '[betterdocs_article_reactions]' );

		}

		return $reactions;
		
	}

}
