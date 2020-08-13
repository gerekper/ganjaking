<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 * @author     WPDeveloper <support@wpdeveloper.net>
 */
class BetterDocs_Public {

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
		add_action( 'init', array( $this, 'public_hooks' ) );

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
		 * defined in BetterDocs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The BetterDocs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, BETTERDOCS_PUBLIC_URL . 'css/betterdocs-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'simplebar', BETTERDOCS_PUBLIC_URL . 'css/simplebar.css', array(), $this->version, 'all' );

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
		 * defined in BetterDocs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The BetterDocs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script('masonry');
		wp_enqueue_script( 'clipboard', BETTERDOCS_PUBLIC_URL . 'js/clipboard.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, BETTERDOCS_PUBLIC_URL . 'js/betterdocs-public.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'simplebar', BETTERDOCS_PUBLIC_URL . 'js/simplebar.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'betterdocspublic', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'post_id' => get_the_ID(),  
			'copy_text' => esc_html__('Copied','betterdocs'),  
			'sticky_toc_offset' => BetterDocs_DB::get_settings('sticky_toc_offset'),  
		));
	}
	
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function public_hooks() {
		add_filter( 'archive_template', array( $this, 'get_docs_archive_template' ) );
		add_filter( 'single_template', array( $this, 'get_docs_single_template' ), 99 );
		add_filter( 'template_include', array( $this, 'load_docs_taxonomy_template' ) );
		// add_filter( 'template_include', array( $this, 'get_docs_tag_taxonomy_template' ) );
		$enable_toc = BetterDocs_DB::get_settings('enable_toc');
		if($enable_toc == 1) {
			add_filter( 'the_content', array( $this, 'betterdocs_the_content' ) );
		}
		$defaults = betterdocs_generate_defaults();
		if( is_array( $defaults ) && $defaults['betterdocs_docs_layout_select'] === 'layout-2' ) {
			add_filter( 'betterdocs_doc_page_cat_icon_size2_default', 80 );
		}
		if( is_admin() ) {
			add_filter('plugin_action_links_' . BETTERDOCS_BASENAME, array($this, 'insert_plugin_links'));
		}
	}

	/**
	 * Get Archive Template for the docs base directory.
	 *
	 * @since    1.0.0
	 */
	public function get_docs_archive_template( $archive_template ) {

		if ( is_post_type_archive( 'docs' ) ) {

			$docs_layout = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');

			if ( $docs_layout === 'layout-2' ) {

				$archive_template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';

			} else {

				$archive_template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';

			}
			
		}

		return $archive_template;
	}

	/**
	 * Get Category Taxonomy Template for the docs base directory.
	 *
	 * @since    1.0.0
	 */
	public function get_docs_category_taxonomy_template( $template ) {

		if ( is_tax( 'doc_category' ) ) {
			$template = BETTERDOCS_PUBLIC_PATH . 'betterdocs-category-template.php';
		}
		return $template;
	}

	/**
	 * Get Tags Taxonomy Template for the docs base directory.
	 *
	 * @since    1.0.0
	 */
	public function get_docs_tag_taxonomy_template( $template ) {

		if ( is_tax( 'doc_tag' ) ) {
			$template = BETTERDOCS_PUBLIC_PATH . 'public/betterdocs-tag-template.php';
		}
		return $template;
	}
	
	/**
	 * Get Tags Taxonomy Template for the docs base directory.
	 *
	 * @since    1.0.0
	 */
	public function load_docs_taxonomy_template( $template ) {
		
		$docs_layout = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');
		$tax = BetterDocs_Helper::get_tax();

		if ( $tax === 'doc_category' ) {

			$template = BETTERDOCS_PUBLIC_PATH . 'betterdocs-category-template.php';

		} else if ( is_tax( 'doc_tag' ) ) {

			$template = BETTERDOCS_PUBLIC_PATH . 'betterdocs-tag-template.php';

		} else if ( $tax === 'knowledge_base' && $docs_layout === 'layout-2') {

			$template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';

		} else if ( $tax === 'knowledge_base' ) {

			$template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';
			
		}

		return $template;
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
			$single_template = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/template-single/layout-1.php';
		}
		return $single_template;
	}

	/**
	 * Get supported heading tag from settings
	 *
	 * @since    1.0.0
	 */
	private static function htag_support(){

		$supported_tag = BetterDocs_DB::get_settings('supported_heading_tag');

		if( ! empty( $supported_tag ) && $supported_tag !== 'off' ) {

			$tags = implode(',',$supported_tag);
		
		}

		return $tags;

	}

	public function list_hierarchy() {

		$toc_hierarchy = BetterDocs_DB::get_settings('toc_hierarchy');
		
		// Whether or not the TOC should be built flat or hierarchical.
		$content = get_the_content();
		preg_match_all( '/(<h(['.self::htag_support().']{1})[^>]*>).*<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER );
		$html         = '';

		$current_depth      = 100;    // headings can't be larger than h6 but 100 as a default to be sure
		$numbered_items     = array();
		$numbered_items_min = null;

		if ( $toc_hierarchy == 1 ) {

			$html .= '<ul class="toc-list betterdocs-hierarchial-toc">';
			
			// find the minimum heading to establish our baseline
			foreach ( $matches as $i => $match ) {

				if ( $current_depth > $matches[ $i ][2] ) {
					$current_depth = (int) $matches[ $i ][2];
				}

			}

			$numbered_items[ $current_depth ] = 0;
			$numbered_items_min               = $current_depth;

			foreach ( $matches as $i => $match ) {

				$level = $matches[ $i ][2];
				$count = $i + 1;

				if ( $current_depth == (int) $matches[ $i ][2] ) {

					$html .= '<li class="betterdocs-toc-heading-level-' . $current_depth . '">';
				}

				// start lists
				if ( $current_depth != (int) $matches[ $i ][2] ) {

					for ( $current_depth; $current_depth < (int) $matches[ $i ][2]; $current_depth++ ) {
						$numbered_items[ $current_depth + 1 ] = 0;
						$html .= '<ul class="betterdocs-toc-list-level-' . $level . '"><li class="betterdocs-toc-heading-level-' . $level . '">';
					}

				}

				$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
				$title = strip_tags( $title );
				$has_id = preg_match('/id=(["\'])(.*?)\1[\s>]/si', $matches[ $i ][0], $matched_ids);
				$id = $has_id ? $matched_ids[2] : $i . '-toc-title';

				$html .= '<a href="#' . $id . '">' . $title . '</a>';

				// end lists
				if ( $i != count( $matches ) - 1 ) {

					if ( $current_depth > (int) $matches[ $i + 1 ][2] ) {

						for ( $current_depth; $current_depth > (int) $matches[ $i + 1 ][2]; $current_depth-- ) {

							$html .= '</li></ul>';
							$numbered_items[ $current_depth ] = 0;
						}
					}

					if ( $current_depth == (int) @$matches[ $i + 1 ][2] ) {

						$html .= '</li>';
					}

				} else {

					// this is the last item, make sure we close off all tags
					for ( $current_depth; $current_depth >= $numbered_items_min; $current_depth-- ) {

						$html .= '</li>';

						if ( $current_depth != $numbered_items_min ) {
							$html .= '</ul>';
						}
					}
				}
			}

			$html .= '</ul>';

		} else {

			$html .= '<ul class="toc-list">';

			foreach ( $matches as $i => $match ) {

				$count = $i + 1;

				$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
				$title = strip_tags( apply_filters( 'ez_toc_title', $title ), apply_filters( 'ez_toc_title_allowable_tags', '' ) );

				$html .= '<li>';

				$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
				$title = strip_tags( $title );
				$has_id = preg_match('/id=(["\'])(.*?)\1[\s>]/si', $matches[ $i ][0], $matched_ids);
				$id = $has_id ? $matched_ids[2] : $i . '-toc-title';

				$html .= '<a href="#'.$id.'">' . $title . '</a>';

				$html .= '</li>';

			}

			$html .= '</ul>';

		}
		
		return $html;
	}

	
	/**
	 * Return table of content list before single post the_content
	 * 
	 * @since    1.0.0
	 */
	public function betterdocs_the_content($content) {

		if ( in_array( get_post()->post_type, [ 'docs' ] ) && is_singular( 'docs' ) ) {

			$collapsible_toc_mobile = BetterDocs_DB::get_settings('collapsible_toc_mobile');
			$toc_class = array( 'betterdocs-toc' );

			if ( $collapsible_toc_mobile == 1 ) {

				$toc_class[] = 'collapsible-sm';
				$collapsible_arrow = "<svg class='angle-icon angle-up' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='angle-up' class='svg-inline--fa fa-angle-up fa-w-10' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path fill='currentColor' d='M177 159.7l136 136c9.4 9.4 9.4 24.6 0 33.9l-22.6 22.6c-9.4 9.4-24.6 9.4-33.9 0L160 255.9l-96.4 96.4c-9.4 9.4-24.6 9.4-33.9 0L7 329.7c-9.4-9.4-9.4-24.6 0-33.9l136-136c9.4-9.5 24.6-9.5 34-.1z'></path></svg><svg class='angle-icon angle-down' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='angle-down' class='svg-inline--fa fa-angle-down fa-w-10' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path fill='currentColor' d='M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z'></path></svg>";
			
			} else {

				$collapsible_arrow = null;

			}

			if ( preg_match_all( '/(<h(['.self::htag_support().']{1})[^>]*>).*<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER ) ) {
				
				$table_of_content = '<div class="' . implode( ' ', $toc_class ) . '">
					<span class="toc-title">' . esc_html__( 'Table of Contents', 'betterdocs' ) . $collapsible_arrow . '</span>';
				$table_of_content .= self::list_hierarchy();
				$table_of_content .= '</div>';
				$index = 0;

				$content = preg_replace_callback( '#<(h['.self::htag_support().'])(.*?)>(.*?)</\1>#si', function ($matches) use (&$index, &$table_of_content) {
					
					$tag = $matches[1];
					$title = strip_tags($matches[3]);
					$has_id = preg_match('/id=(["\'])(.*?)\1[\s>]/si', $matches[2], $matched_ids);
					$id = $has_id ? $matched_ids[2] : $index++ . '-toc-title';

					if ($has_id) {

						return $matches[0];
					}
					
					$title_link_ctc = BetterDocs_DB::get_settings('title_link_ctc');
					
					if ( $title_link_ctc == 1 ) {

						$hash_link = '<a href="#'.$id.'" class="anchor" data-clipboard-text="'. get_permalink() .'#'. $id .'" data-title="'.esc_html__('Copy URL','betterdocs').'">#</a>';
					
					} else {

						$hash_link = '';
					
					}

					return sprintf('<%s%s class="betterdocs-content-heading" id="%s">%s %s</%s>', $tag, $matches[2], $id, $matches[3], $hash_link, $tag);
				
				}, $content );
				

				$content = $table_of_content . '<div id="betterdocs-single-content" class="betterdocs-content" itemscope itemtype="http://schema.org/Article">'. $content . '</div>';
			
			} else {

				$content = '<div id="betterdocs-single-content" class="betterdocs-content" itemscope itemtype="http://schema.org/Article">'. $content . '</div>';
			
			}

			return $content;

		} else {

			return $content;

		}
	}

	/**
	 * Insert quick action link to plugin page
	 * 
	 * @since    1.1.5
	 */
	public function insert_plugin_links($links) {

        // settings
        $links[] = sprintf('<a href="admin.php?page=betterdocs-settings">' . esc_html__('Settings','betterdocs') . '</a>');
        //$links[] = sprintf('<a href="https://betterdocs.co/docs" target="_blank">' . esc_html__('Documentation','betterdocs') . '</a>');

        return $links;
	
	}

}
