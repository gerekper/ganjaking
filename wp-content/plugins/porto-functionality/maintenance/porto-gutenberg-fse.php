<?php
/**
 * Porto Gutenberg Full Site Editing
 *
 * @author  P-THEMES
 * @package Porto
 * @since 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Gutenberg Fse Class
 *
 * @since 2.5.0
 */
class Porto_Gutenberg_Fse {

	/**
	 * The Constructor
	 *
	 * @since 2.5.0
	 */
	public function __construct() {
		add_action( 'rest_after_insert_wp_template_part', array( $this, 'save_content_style' ), 100, 3 );
		add_action( 'rest_after_insert_wp_template', array( $this, 'save_content_style' ), 100, 3 );
		add_action( 'render_block_core_template_part_post', array( $this, 'get_template_part_style' ), 100, 4 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 20 );
		add_action( 'wp_footer', array( $this, 'add_structure_the_block_template' ) );
		add_filter( 'default_template_types', array( $this, 'insert_default_templates' ) );
		add_filter( 'run_wptexturize', array( $this, 'texturize' ) );
		add_action( 'wp_head', array( $this, 'head_above_metas' ), 2 );
	}

	/**
	 * Hide body to avoid rendering template like header, footer and page-wrapper without id or class
	 *
	 * @since 2.5.0
	 */
	public function enqueue_styles() {
		global $porto_settings;
		if ( ! empty( $porto_settings['enable-gfse'] ) ) {
			echo '<style>body{opacity:0!important;visibility:hidden!important;}</style>' . PHP_EOL;
		}
	}

	/**
	 * Add attributes and style
	 *
	 * Add #header to <header>, page-wrapper class to "wp-site-blocks"
	 *
	 * @since 2.5.0
	 */
	public function add_structure_the_block_template() {
		global $porto_settings;
		if ( ! empty( $porto_settings['enable-gfse'] ) ) {
			?>
		<script>
		( function() {
			var header_template = document.querySelector( 'header' ),
				footer_template = document.querySelector( 'footer' ),
				page_wrapper =  document.querySelector( '.wp-site-blocks' );

			if ( header_template ) {
				header_template.setAttribute( 'id', 'header' );
			}
			
			if ( footer_template ) {
				footer_template.setAttribute( 'id', 'footer' );
				footer_template.classList.add( 'footer' );
			}

			if ( page_wrapper ) {
				page_wrapper.classList.add( 'page-wrapper' );
			}
			
		}() );
		</script>
		<style>
			body{opacity:1!important;visibility:visible!important;}
		</style>
			<?php
		}
	}

	/**
	 * Save the custom css as post meta
	 *
	 * @since 2.5.0
	 */
	public function save_content_style( $post, $request, $flag ) {
		global $porto_settings;
		if ( ( false !== strpos( $post->post_content, '<!-- wp:porto' ) ) && ! empty( $porto_settings['enable-gfse'] ) ) {
			$blocks  = parse_blocks( $post->post_content );
			$post_id = $post->ID;
			if ( ! empty( $blocks ) ) {
				ob_start();
				$css = '';
				if ( false !== strpos( $post->post_content, '<!-- wp:porto-hb' ) ) {
					// Header Shortcode
					PortoBuildersHeader::get_instance()->include_style( $blocks );
				}
				// Normal Gutenberg Shortcode
				PortoShortcodesClass::get_instance()->include_style( $blocks );

				$css = ob_get_clean();
				if ( $css ) {
					update_post_meta( $post_id, 'porto_blocks_style_options_css', wp_strip_all_tags( $css ) );
				} else {
					delete_post_meta( $post_id, 'porto_blocks_style_options_css' );
				}
			}
		}
	}

	/**
	 * Get Template Part Style used in Template - such as Header part, Footer part, General part
	 *
	 * @since 2.5.0
	 */
	public function get_template_part_style( $template_part_id, $attributes, $template_part_post, $content ) {
		global $porto_settings;
		if ( ! empty( $porto_settings['enable-gfse'] ) ) {
			$css = get_post_meta( $template_part_post->ID, 'porto_blocks_style_options_css', true );
			if ( $css ) {
				global $porto_gutenberg_tp;
				if ( ! isset( $porto_gutenberg_tp ) ) {
					$porto_gutenberg_tp = '';
				}
				$porto_gutenberg_tp .= $css;
			}
		}
	}

	/**
	 * Name Default Block Templates
	 *
	 * @since 2.5.0
	 */
	public function insert_default_templates( $default_template_types ) {
		$default_template_types = array_merge(
			$default_template_types,
			array(
				'archive-event'             => array(
					'title'       => __( 'Archive Event', 'porto-functionality' ),
					'description' => __( 'Displays Events and Event Taxonomies.', 'porto-functionality' ),
				),
				'archive-faq'               => array(
					'title'       => __( 'Archive Faq', 'porto-functionality' ),
					'description' => __( 'Displays Faqs.', 'porto-functionality' ),
				),
				'archive-member'            => array(
					'title'       => __( 'Archive Member', 'porto-functionality' ),
					'description' => __( 'Displays Members.', 'porto-functionality' ),
				),
				'archive-portfolio'         => array(
					'title'       => __( 'Archive Portfolio', 'porto-functionality' ),
					'description' => __( 'Displays Portfolios.', 'porto-functionality' ),
				),
				'archive-product'           => array(
					'title'       => __( 'Product Archive', 'porto-functionality' ),
					'description' => __( 'Shop and Product Taxonomies.', 'porto-functionality' ),
				),
				'single-event'              => array(
					'title'       => __( 'Single Event', 'porto-functionality' ),
					'description' => __( 'Displays a Single Event Page.', 'porto-functionality' ),
				),
				'single-faq'                => array(
					'title'       => __( 'Single Faq', 'porto-functionality' ),
					'description' => __( 'Displays a Single Faq Page.', 'porto-functionality' ),
				),
				'single-member'             => array(
					'title'       => __( 'Single Member', 'porto-functionality' ),
					'description' => __( 'Displays a Single Member Page.', 'porto-functionality' ),
				),
				'single-portfolio'          => array(
					'title'       => __( 'Single Portfolio', 'porto-functionality' ),
					'description' => __( 'Displays a Single Portfolio Page.', 'porto-functionality' ),
				),
				'single-product'            => array(
					'title'       => __( 'Single Product', 'porto-functionality' ),
					'description' => __( 'Displays a Single Product Page.', 'porto-functionality' ),
				),
				'product-search-results'    => array(
					'title'       => __( 'Product Search', 'porto-functionality' ),
					'description' => __( 'Displays Product Search results.', 'porto-functionality' ),
				),

				'taxonomy-faq_cat'          => array(
					'title'       => __( 'Faq Category', 'porto-functionality' ),
					'description' => __( 'Displays Faq Category Page.', 'porto-functionality' ),
				),
				'taxonomy-member_cat'       => array(
					'title'       => __( 'Member Category', 'porto-functionality' ),
					'description' => __( 'Displays Member Category Page.', 'porto-functionality' ),
				),
				'taxonomy-portfolio_skills' => array(
					'title'       => __( 'Portfolio Skill', 'porto-functionality' ),
					'description' => __( 'Displays Portfolio Skill Page.', 'porto-functionality' ),
				),
				'taxonomy-portfolio_cat'    => array(
					'title'       => __( 'Portfolio Category', 'porto-functionality' ),
					'description' => __( 'Displays Portfolio Category Page.', 'porto-functionality' ),
				),
				'taxonomy-product_cat'      => array(
					'title'       => __( 'Product Category', 'porto-functionality' ),
					'description' => __( 'Displays Product Category Page.', 'porto-functionality' ),
				),
				'taxonomy-product_tag'      => array(
					'title'       => __( 'Product Tag', 'porto-functionality' ),
					'description' => __( 'Displays Product Tag Page.', 'porto-functionality' ),
				),
			)
		);
		return $default_template_types;
	}

	/**
	 * Escape from texturize function
	 *
	 * @since 2.5.0
	 */
	public function texturize( $run_texturize ) {
		global $porto_settings;
		if ( ! empty( $porto_settings['enable-gfse'] ) ) {
			$run_texturize = false;
		}
		return $run_texturize;
	}

	/**
	 * Site Metas
	 *
	 * @since 6.5.0
	 */
	public function head_above_metas() {
		global $porto_settings;
		if ( ! empty( $porto_settings['enable-gfse'] ) ) {
			?>
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
			<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
			<link rel="profile" href="https://gmpg.org/xfn/11" />
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
			<?php
		}
	}
}

new Porto_Gutenberg_Fse;
