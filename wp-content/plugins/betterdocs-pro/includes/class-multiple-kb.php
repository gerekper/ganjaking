<?php

/**
 * Multiple knowledge base functions.
 *
 *
 *
 * @since      2.0.0
 * @package    BetterDocs
 * @subpackage BetterDocs/includes
 * @author     WPDeveloper <support@wpdeveloper.com>
 */

class BetterDocs_Multiple_Kb
{
	public static $enable;

	public static function init()
	{
		self::$enable = self::get_multiple_kb();
		add_action('betterdocs_multi_kb_settings', array(__CLASS__, 'kb_settings'));
		add_action('betterdocs_disable_root_slug_mkb_settings', array(__CLASS__, 'disable_root_slug_mkb'));
        add_action('betterdocs_kb_based_search_settings', array(__CLASS__, 'kb_based_search_settings'));
		add_action('betterdocs_shortcode_fields', array(__CLASS__, 'pro_shortcodes'));
		// we need to do add this filter outside so that user can see the tag when they enable the "Enable Multiple Knowledge Base" option without reloading the settlings page.
		add_filter('betterdocs_doc_permalink_tags', array(__CLASS__, 'doc_permalink_tags'));
		if (self::$enable == 1) {
			add_action('init', array(__CLASS__, 'register_knowledge_base'));
			add_filter('betterdocs_docs_rewrite', array(__CLASS__, 'docs_rewrite'));
			add_filter('betterdocs_category_rewrite', array(__CLASS__, 'doc_category_rewrite'));
			add_filter('post_type_link', array(__CLASS__, 'docs_show_permalinks'), 1, 3);
			add_filter('betterdocs_doc_permalink_default', array(__CLASS__, 'doc_permalink_default'), 10, 3);
			add_filter('nav_menu_link_attributes', array(__CLASS__, 'doc_category_nav_menu_permalink'), 10, 2);
			add_action('betterdocs_cat_template_multikb', array(__CLASS__, 'get_multiple_kb'));
			add_action('betterdocs_postcount', array(__CLASS__, 'postcount'), 10, 7);
			add_action('betterdocs_category_list_shortcode', array(__CLASS__, 'category_list_shortcode'), 10, 2);
			add_action('betterdocs_category_box_shortcode', array(__CLASS__, 'category_box_shortcode'), 10, 3);
			add_action('betterdocs_sidebar_category_shortcode', array(__CLASS__, 'sidebar_category_shortcode'), 10, 3);
			add_action('betterdocs_list_tax_query_arg', array(__CLASS__, 'list_tax_query'), 10, 4);
			add_action('betterdocs_taxonomy_object_meta_query', array(__CLASS__, 'taxonomy_object_meta_query'), 10, 3);
			add_action('betterdocs_child_taxonomy_meta_query', array(__CLASS__, 'child_taxonomy_meta_query'), 10, 3);
			add_action('betterdocs_kb_uncategorized_tax_query', array(__CLASS__, 'kb_uncategorized_tax_query'), 10, 2);
			add_action('betterdocs_breadcrumb_archive_html', array(__CLASS__, 'breadcrumb_archive'), 10, 2);
			add_action('betterdocs_breadcrumb_before_single_cat_html', array(__CLASS__, 'breadcrumb_single'), 10, 2);
			add_action('betterdocs_breadcrumb_term_permalink', array(__CLASS__, 'breadcrumb_term_permalink'));
			add_action('term_link', array(__CLASS__, 'doc_category_link'), 10 , 3);
			add_action('betterdocs_doc_category_add_form_before', array(__CLASS__, 'doc_category_add_form'));
			add_action('betterdocs_doc_category_update_form_before', array(__CLASS__, 'doc_category_update_form'));
			add_filter('doc_category_row_actions', array(__CLASS__, 'disable_category_view'), 10, 2);
			add_action('knowledge_base_add_form_fields', array(__CLASS__, 'add_knowledge_base_meta'), 10, 2);
			add_action('created_knowledge_base', array(__CLASS__, 'save_knowledge_base_meta'), 10, 2);
			add_action('knowledge_base_edit_form_fields', array(__CLASS__, 'update_knowledge_base_meta'), 10, 2);
			add_action('edited_knowledge_base', array(__CLASS__, 'updated_knowledge_base_meta'), 10, 2);
			add_action('admin_footer', array(__CLASS__, 'kb_script'));
			add_action( 'parse_request', array(__CLASS__, 'docs_rewrite_parse_request') );
			add_action('admin_head', array(__CLASS__, 'admin_order_terms'));
			add_action('wp_ajax_update_knowledge_base_order', array(__CLASS__, 'update_knowledge_base_order'));
			add_action('betterdocs_internal_kb_fields', array(__CLASS__, 'internal_kb_settings_field'));
			add_action('parse_term_query', array(__CLASS__, 'parse_knowledge_base_term_query'), 10, 1);
			if (BetterDocs_DB::get_settings('kb_based_search') == 1) {
                add_action('betterdocs_live_search_tax_query', array(__CLASS__, 'live_search_tax_query'), 10, 2);
            }
		}
	}

	public static function kb_settings()
	{
		$settings = array(
			'type'        => 'checkbox',
			'label'       => __('Enable Multiple Knowledge Base', 'betterdocs-pro'),
			'default'     => '',
			'priority'    => 10,
		);
		return $settings;
	}

    public static function disable_root_slug_mkb()
	{
		$settings = array(
            'type'        => 'checkbox',
            'label'       => __('Disable Root slug for KB Archives' , 'betterdocs-pro'),
            'default'     => '',
            'help'        => __('<strong>Note:</strong> if you disable root slug for KB Archives, your individual knowledge base URL will be like this: <b><i>https://example.com/knowledgebase-1</i></b>' , 'betterdocs-pro'),
            'priority'    => 10,
		);
		return $settings;
	}

    public static function internal_kb_settings_field($settings)
    {
        $settings['restrict_kb'] = array(
            'type'        => 'select',
            'label'       => __('Restriction on Knowledge Bases', 'betterdocs-pro'),
            'help'        => __('<strong>Note:</strong> Selected Knowledge Bases will be restricted  ' , 'betterdocs-pro'),
            'priority'    => 4,
            'multiple'    => true,
            'default'     => 'all',
            'options'     => BetterDocs_Settings::get_terms_list('knowledge_base')
        );
        return $settings;
    }

	public static function parse_knowledge_base_term_query( $term_query ) {
		$screen = function_exists('get_current_screen') ? get_current_screen() : '';
        if( empty( $term_query->query_vars['taxonomy'] ) || ! in_array( 'knowledge_base', $term_query->query_vars['taxonomy'], true ) || empty( $screen ) || ( empty( $screen ) && $screen->taxonomy !== 'knowledge_base' ) ) {
            return;
        }
        $term_query->query_vars['meta_query'] = [
			[
				'key' => 'kb_order',
				'type' => 'NUMERIC'
			]
		];
		$term_query->query_vars['orderby'] = 'meta_value_num';
	}

    public static function kb_based_search_settings()
    {
        return array(
            'type'        => 'checkbox',
            'label'       => __('Search Result based on Knowledge Base', 'betterdocs-pro'),
            'default'     => '',
            'priority'    => 10,
        );
    }

	public static function pro_shortcodes($settings)
	{
		$settings['category_box_l3_shortcode'] = array(
			'type'      => 'text',
			'label'     => __('Category Box- Layout 3' , 'betterdocs-pro'),
			'default'   => '[betterdocs_category_box_2]',
			'readonly'	=> true,
			'clipboard' => true,
			'priority'	=> 10,
			'help'      => __('<strong>You can use:</strong> [betterdocs_category_box_2 column="" nested_subcategory="" terms="" terms_orderby="" kb_slug="" multiple_knowledge_base="false" disable_customizer_style="false" title_tag="h2"]' , 'betterdocs-pro'),
		);
		$settings['category_grid_2_shortcode'] = array(
			'type'      => 'text',
			'label'     => __('Category Grid- Layout 4' , 'betterdocs-pro'),
			'default'   => '[betterdocs_category_grid_2]',
			'readonly'	=> true,
			'clipboard' => true,
			'priority'	=> 10,
			'help'      => __('<strong>You can use:</strong> [betterdocs_category_grid_2 sidebar_list="false" orderby="" order="" count="true" icon="" masonry="" column="" posts="" nested_subcategory="" terms="" kb_slug="" terms_orderby="" terms_order="" multiple_knowledge_base="false" disable_customizer_style="false" title_tag="h2"]', 'betterdocs-pro')
		);
		$settings['multiple_kb_shortcode'] = array(
			'type'      => 'text',
			'label'     => __('Multiple KB- Layout 1' , 'betterdocs-pro'),
			'default'   => '[betterdocs_multiple_kb]',
			'readonly'	=> true,
			'clipboard' => true,
			'priority'	=> 10,
			'help'      => __('<strong>You can use:</strong> [betterdocs_multiple_kb column="" terms="" disable_customizer_style="false" title_tag="h2"]' , 'betterdocs-pro'),
		);
		$settings['multiple_kb_2_shortcode'] = array(
			'type'      => 'text',
			'label'     => __('Multiple KB- Layout 2' , 'betterdocs-pro'),
			'default'   => '[betterdocs_multiple_kb_2]',
			'readonly'	=> true,
			'clipboard' => true,
			'priority'	=> 10,
			'help'      => __('<strong>You can use:</strong> [betterdocs_multiple_kb_2 column="" terms="" disable_customizer_style="false" title_tag="h2"]' , 'betterdocs-pro'),
		);
		$settings['multiple_kb_3_shortcode'] = array(
			'type'      => 'text',
			'label'     => __('Multiple KB- Layout 3' , 'betterdocs-pro'),
			'default'   => '[betterdocs_multiple_kb_list]',
			'readonly'	=> true,
			'clipboard' => true,
			'priority'	=> 10,
			'help'      => __('<strong>You can use:</strong> [betterdocs_multiple_kb_list terms="" disable_customizer_style="false" title_tag="h2"]' , 'betterdocs-pro'),
		);
		$settings['multiple_kb_4_shortcode'] = array(
			'type'      => 'text',
			'label'     => __('Multiple KB- Layout 4' , 'betterdocs-pro'),
			'default'   => '[betterdocs_multiple_kb_tab_grid]',
			'readonly'	=> true,
			'clipboard' => true,
			'priority'	=> 10,
			'help'      => __('<strong>You can use:</strong> [betterdocs_multiple_kb_tab_grid terms="" disable_customizer_style="false" terms_orderby="" terms_order="" orderby="" order="" posts_per_grid="" title_tag="h2"]' , 'betterdocs-pro'),
		);
		$settings['mkb_popular_docs'] = array(
			'type'      => 'text',
			'label'     => __('Popular Docs' , 'betterdocs-pro'),
			'default'   => '[betterdocs_popular_articles]',
			'readonly'	=> true,
			'clipboard' => true,
			'priority'	=> 10,
			'help'      => __('<strong>You can use:</strong> [betterdocs_popular_articles post_per_page="" title="Popular Docs" title_tag="h2" multiple_knowledge_base="false" disable_customizer_style="false"]', 'betterdocs-pro')
		);
		return $settings;
	}

	public static function get_multiple_kb()
	{
        return BetterDocs_DB::get_settings('multiple_kb');
	}

	public static function postcount($term_count, $multiple_kb, $term_id, $term_slug, $count, $nested_subcategory=false, $knowledge_base=false)
	{
        global $wp_query;
		
        if ($nested_subcategory==false && $term_count == 0) {
            return $term_count;
        }

		$kb_terms = get_terms('knowledge_base');
		$doc_category_terms = get_terms('doc_category');

        if ($knowledge_base == false && is_singular('docs')) {
            $kb_terms = self::single_kb_terms();
            $knowledge_base = ($kb_terms) ? $kb_terms[0]->slug : '';
        } else if ($knowledge_base == false) {
            $knowledge_base = isset($wp_query->query['knowledge_base']) ? $wp_query->query['knowledge_base'] : '';
        }
		
		if ($multiple_kb == true && !empty($kb_terms) && !empty($doc_category_terms) && $knowledge_base != 'non-knowledgebase') {
			$term_count = self::count_category($knowledge_base, $term_slug, $nested_subcategory);
		}
		return $term_count;
	}

	public static function count_category($kb_slug, $cat_slug, $nested_subcategory)
	{
		$args = array(
			'post_type'   => 'docs',
			'post_status' => 'publish',
		);

		$taxes = array('knowledge_base', 'doc_category');
		$tax_map = array();

		foreach ($taxes as $tax) {
			$terms = get_terms($tax);
			foreach ($terms as $term)
				$tax_map[$tax][$term->slug] = $term->term_taxonomy_id;
		}

		$args['tax_query'] = array(
			'relation' => 'AND'
		);

		if (array_key_exists('knowledge_base', $tax_map) && !empty($tax_map['knowledge_base'][$kb_slug])) {
			$args['tax_query'][] = array(
				'taxonomy' => 'knowledge_base',
				'field' => 'term_taxonomy_id',
				'terms' => array($tax_map['knowledge_base'][$kb_slug]),
				'operator' => 'IN',
                'include_children'  => true,
			);

            if ($nested_subcategory == false && $kb_slug != '') {
                $args['tax_query'][0]['include_children'] = false;
            }
		}

		if (array_key_exists('doc_category', $tax_map) && !empty($tax_map['doc_category'][$cat_slug])) {
			$args['tax_query'][] = array(
				'taxonomy' => 'doc_category',
				'field' => 'term_taxonomy_id',
				'operator' => 'IN',
				'terms' => array($tax_map['doc_category'][$cat_slug]),
				'include_children'  => true,
			);

            if ($nested_subcategory == false && $kb_slug != '') {
                $args['tax_query'][1]['include_children'] = false;
            }
		}

		$query = new WP_Query($args);
		return $query->found_posts;
	}

	public static function category_list_shortcode($shortcode, $terms_orderby)
	{
        $output 	 = betterdocs_generate_output();
		$terms_order = BetterDocs_DB::get_settings('terms_order');
		if (is_tax('knowledge_base')) {
			$shortcode = do_shortcode('[betterdocs_category_grid multiple_knowledge_base="true" terms_order="'.$terms_order.'" terms_orderby="'.esc_html($terms_orderby).'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'"]');
		} else {
			$shortcode = do_shortcode('[betterdocs_category_grid terms_orderby="'.esc_html($terms_orderby).'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'"]');
		}

		return $shortcode;
	}

	public static function category_box_shortcode($shortcode, $terms_orderby, $terms_order)
	{
        $output = betterdocs_generate_output();
		if (is_tax('knowledge_base')) {
			$shortcode = do_shortcode('[betterdocs_category_box multiple_knowledge_base="true" terms_order="'.esc_html($terms_order).'" terms_orderby="'.esc_html($terms_orderby).'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'" border_bottom="'.$output['betterdocs_doc_page_box_border_bottom'].'"]');
		} else {
			$shortcode = do_shortcode('[betterdocs_category_box terms_order="'.esc_html($terms_order).'" terms_orderby="'.esc_html($terms_orderby).'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'" border_bottom="'.$output['betterdocs_doc_page_box_border_bottom'].'"]');
		}

		return $shortcode;
	}

	public static function sidebar_category_shortcode($shortcode, $terms_orderby, $terms_order)
	{
        $output = betterdocs_generate_output();
		return do_shortcode('[betterdocs_category_grid terms_order="'.$terms_order.'" terms_orderby="'.esc_html($terms_orderby).'" sidebar_list="true" posts_per_grid="-1" multiple_knowledge_base="true" kb_slug="'.self::kb_slug().'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_sidebar_title_tag']).'"]');
	}

	public static function breadcrumb_archive($html, $delimiter)
	{
		global $wp_query;

		$archive = '';
		$kb_term = isset($wp_query->query_vars['knowledge_base']) ? $wp_query->query_vars['knowledge_base'] : '';
		if ($kb_term != 'non-knowledgebase') {
			$get_kb_term = get_term_by('slug', $kb_term, 'knowledge_base');
			$kb_term_id = isset( $get_kb_term->term_id ) ?  $get_kb_term->term_id : '';
			$archive .= !is_wp_error(betterdocs_get_term_parents_list($kb_term_id, 'knowledge_base', $delimiter)) ? betterdocs_get_term_parents_list($kb_term_id, 'knowledge_base', $delimiter) : '';
			$archive .= '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>';
		}

		$cat_term = $wp_query->query_vars['doc_category'];
		$get_cat_term = get_term_by('slug', $cat_term, 'doc_category');
		$cat_term_id = $get_cat_term->term_id;
		$archive .= betterdocs_get_term_parents_list($cat_term_id, 'doc_category', $delimiter);

		return $html = $archive;
	}

	public static function single_kb_terms()
    {
        global $post;
		
        $kb_terms = array();
        $term = wp_get_post_terms($post->ID, 'knowledge_base');
		if (! is_wp_error( $term ) && !empty($term)) {
			$kb_terms[] = $term[0];
			if (isset($_COOKIE['last_knowledge_base']) && has_term($_COOKIE['last_knowledge_base'], 'knowledge_base')) {
				$kb_terms[0] = get_term_by('slug', $_COOKIE['last_knowledge_base'], 'knowledge_base');
			}
		}
        return $kb_terms;
    }

	public static function breadcrumb_single($html, $delimiter)
	{
        $kb_terms = self::single_kb_terms();
		if ($kb_terms) {
			$html = '<li class="betterdocs-breadcrumb-item breadcrumb-delimiter"> ' . $delimiter . ' </li>'
				. betterdocs_get_term_parents_list($kb_terms[0]->term_id, 'knowledge_base', $delimiter);
		}
		return $html;
	}

	public static function register_knowledge_base()
	{
        $disable_root_slug_mkb = BetterDocs_DB::get_settings('disable_root_slug_mkb');
        $docs_archive = BetterDocs_Docs_Post_Type::$docs_archive;
		$permalink = get_option( 'permalink_structure' );

        if ( $disable_root_slug_mkb == 1 && $permalink == "/%postname%/" ) {
            $docs_archive = '/';
        }

		/**
		 * Register knowledge base taxonomy
		 */
		$manage_labels = array(
			'name'                       => esc_html__('Knowledge Base', 'betterdocs-pro'),
			'singular_name'              => esc_html__('Knowledge Base', 'betterdocs-pro'),
			'search_items'               => esc_html__('Search Knowledge Base', 'betterdocs-pro'),
			'all_items'                  => esc_html__('All Knowledge Base', 'betterdocs-pro'),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => esc_html__('Edit Knowledge Base', 'betterdocs-pro'),
			'update_item'                => esc_html__('Update Knowledge Base', 'betterdocs-pro'),
			'not_found'                  => esc_html__('No Knowledge Base found.', 'betterdocs-pro'),
			'add_new_item'               => esc_html__('Add New Knowledge Base', 'betterdocs-pro'),
			'new_item_name'              => esc_html__('New Knowledge Base Name', 'betterdocs-pro'),
			'add_or_remove_items'        => esc_html__('Add or reomve Knowledge Base', 'betterdocs-pro'),
			'menu_name'                  => esc_html__('Knowledge Base', 'betterdocs-pro'),
		);

		$manage_args = array(
			'hierarchical'          => true,
			'labels'                => $manage_labels,
			'show_ui'               => true,
			'update_count_callback' => '_update_post_term_count',
			'show_admin_column'     => true,
			'query_var'             => true,
			'show_in_rest'          => true,
			'has_archive'           => true,
			'rewrite'               => array('slug' => $docs_archive, 'with_front' => false),
			'capabilities' => [
                'manage_terms' => 'manage_knowledge_base_terms',
                'edit_terms'   => 'edit_knowledge_base_terms',
                'delete_terms' => 'delete_knowledge_base_terms',
                'assign_terms' => 'edit_docs'
            ]
		);

		register_taxonomy('knowledge_base', array(BetterDocs_Docs_Post_Type::$post_type), $manage_args);
	}

 	/**
     * Check if post exists by slug.
     *
     * @see    https://wpcodebook.com/snippets/check-if-post-exists-by-slug-in-wordpress/
     * @return mixed boolean false if no posts exist; post ID otherwise.
     */
    public static function post_exists_by_slug( $post_slug, $post_type = 'docs' )
    {
		$loop_posts = new WP_Query( array( 'post_type' => $post_type, 'post_status' => 'any', 'name' => $post_slug, 'posts_per_page' => 1, 'fields' => 'all' ) );

        return ( $loop_posts->have_posts() ? $loop_posts->posts[0] : false );
	}

	/**
     * Fix query vars, if they mismatched.
     *
     */
    public static function fix_query_vars( $slug, $wp )
    {
		$return = false;
		$post = self::post_exists_by_slug($slug);
		if(!empty( $post->post_type )){
			if($post->post_type == 'docs'){
				$wp->query_vars['docs'] = $slug;
				$wp->query_vars['name'] = $slug;
				$wp->query_vars['post_type'] = 'docs';
				$return = true;
			}
			elseif($post->post_type == 'page'){
				$wp->query_vars['pagename'] = $slug;
				$return = true;
			}
			elseif($post->post_type == 'post'){
				$wp->query_vars['name'] = $slug;
				$return = true;
			}
		}
		return $return;
	}

	public static function docs_rewrite_parse_request($wp)
    {
		global $wp_rewrite;
        if(isset($wp->query_vars['post_type'], $wp->query_vars['knowledge_base'], $wp->query_vars['name']) && $wp->query_vars['post_type'] == 'docs'){
            $loop_posts = new WP_Query( array(
                'post_type'      => 'docs',
                'post_status'    => 'any',
                'name'           => $wp->query_vars['name'],
                'posts_per_page' => 1,
                'fields'         => 'all',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'knowledge_base',
                        'field'    => 'slug',
                        'terms'    => $wp->query_vars['knowledge_base'],
                    ),
                ),
            ) );
            if(!$loop_posts->have_posts()){
                $wp->query_vars['pagename'] = $wp->query_vars['knowledge_base'] . "/" . $wp->query_vars['name'];
                unset( $wp->query_vars['knowledge_base'] );
                unset( $wp->query_vars['post_type'] );
                unset( $wp->query_vars['name'] );
                unset( $wp->query_vars['docs'] );
            }
        }

		if(!isset($wp->query_vars['docs']) && !isset($wp->query_vars['pagename']) && !isset($wp->query_vars['name'])){
			$is_done = false;

			if(isset($wp->query_vars['knowledge_base']) && $wp->query_vars['knowledge_base'] !== 'non-knowledgebase' && !term_exists($wp->query_vars['knowledge_base'], 'knowledge_base')){
				$is_done = self::fix_query_vars($wp->query_vars['knowledge_base'], $wp);
				unset($wp->query_vars['knowledge_base']);
			}
			if(!$is_done && isset($wp->query_vars['doc_category']) && !term_exists($wp->query_vars['doc_category'], 'doc_category')){
				$is_done = self::fix_query_vars($wp->query_vars['doc_category'], $wp);
				unset($wp->query_vars['doc_category']);
			}
			// checking whether the kb and cat is switched position.
			if(!$is_done && isset($wp->query_vars['knowledge_base']) && isset($wp->query_vars['doc_category']) && term_exists($wp->query_vars['knowledge_base'], 'doc_category') && term_exists($wp->query_vars['doc_category'], 'knowledge_base') ){
				$knowledge_base = $wp->query_vars['knowledge_base'];
				$wp->query_vars['knowledge_base']	= $wp->query_vars['doc_category'];
				$wp->query_vars['doc_category']	= $knowledge_base;
			}
		}
	}

	public static function docs_rewrite()
	{
        $permalink =  BetterDocs_DB::get_settings('permalink_structure');
        if ( method_exists('BetterDocs_Helper','permalink_stracture') ) {
            $permalink = BetterDocs_Helper::permalink_stracture(BetterDocs_Docs_Post_Type::$docs_slug, $permalink);
        }
		if(empty($permalink)){
			$permalink = trim(BetterDocs_Docs_Post_Type::$docs_slug, '/') . '/%knowledge_base%/%doc_category%';
		}

        return array('slug' => trim($permalink, '/'), 'with_front' => false);
	}

    public static function doc_category_rewrite()
    {
        $disable_root_slug_archive = BetterDocs_DB::get_settings('disable_root_slug_archive');
        $docs_archive = BetterDocs_Docs_Post_Type::$docs_slug;
        if ( $disable_root_slug_archive == 1 ) {
            $docs_archive = '/';
        }
        return array('slug' => trim($docs_archive, '/') . '/%knowledge_base%', 'with_front' => false);
    }

	public static function list_tax_query($tax_query, $multiple_kb, $tax_slug, $kb_slug)
	{
		global $wp_query;
        if (is_singular('docs')) {
            $kb_terms = self::single_kb_terms();
            $knowledge_base = ($kb_terms) ? $kb_terms[0]->slug : '';
        } elseif ($kb_slug) {
            $knowledge_base = $kb_slug;
        } else {
            $knowledge_base = isset($wp_query->query['knowledge_base']) ? $wp_query->query['knowledge_base'] : '';
        }

		if ($multiple_kb == true && $knowledge_base != 'non-knowledgebase') {
			$taxes = array('knowledge_base', 'doc_category');
			$tax_map = array();

			foreach ($taxes as $tax) {
				$terms = get_terms(
					array(
						'taxonomy' => $tax,
						'hide_empty' => false
					)
				);
				foreach ($terms as $term)
					$tax_map[$tax][$term->slug] = $term->term_taxonomy_id;
			}

			$tax_query = array(
				'relation' => 'AND'
			);

			if (array_key_exists('knowledge_base', $tax_map) && !empty($tax_map['knowledge_base'][$knowledge_base])) {
				$tax_query['tax_query'][] = array(
					'taxonomy' => 'knowledge_base',
					'field' => 'term_taxonomy_id',
					'terms' => array($tax_map['knowledge_base'][$knowledge_base]),
					'operator' => 'IN',
					'include_children'  => false,
				);
			}

			if (array_key_exists('doc_category', $tax_map) && !empty($tax_map['doc_category'][$tax_slug])) {
				$tax_query['tax_query'][] = array(
					'taxonomy' => 'doc_category',
					'field' => 'term_taxonomy_id',
					'operator' => 'IN',
					'terms' => array($tax_map['doc_category'][$tax_slug]),
					'include_children'  => false,
				);
			}
		}
        return $tax_query;
	}

	public static function taxonomy_object_meta_query($meta_query, $multiple_kb, $kb_slug)
	{
		if ($multiple_kb == true || $kb_slug) {
            $value = $kb_slug ? $kb_slug : self::kb_slug();
			$meta_query = array(
					'relation' => 'OR',
					array(
						'key'       => 'doc_category_knowledge_base',
						'value'     => $value,
						'compare'   => 'LIKE'
					)
			);
		}
        return $meta_query;
	}

	public static function child_taxonomy_meta_query($meta_query, $multiple_kb, $kb_slug)
	{
        if ($multiple_kb == true || $kb_slug) {
            $value = $kb_slug ? $kb_slug : self::kb_slug();
			$meta_query = array(
				array(
					'key'       => 'doc_category_knowledge_base',
					'value'     => $value,
					'compare'   => 'LIKE'
				)
			);
		}
        return $meta_query;
	}

	public static function kb_slug()
	{
		global $post, $wp_query;
		$kb_slug = '';
		$object = get_queried_object();
		if (is_singular('docs')) {
            $kb_terms = self::single_kb_terms();
            $kb_slug = ($kb_terms) ? $kb_terms[0]->slug : '';
		} elseif (is_tax('doc_category')) {
			$kb_slug = self::doc_category_kb_slug($object->term_id);
		} elseif (is_tax('knowledge_base')) {
			$kb_slug = $object->slug;
		}

		return $kb_slug;
	}

	public static function doc_category_kb_slug($term_id)
	{
		$kb_slug = get_term_meta($term_id, 'doc_category_knowledge_base', true);

		if (is_array($kb_slug)) {
			global $wp;
			$current_url = home_url(add_query_arg(array(), $wp->request));
			$url_parse = wp_parse_url($current_url);
			$url_path = isset($url_parse['path']) ? $url_parse['path'] : '';
			$path_arr = explode("/", $url_path);
			$reverse_path_arr = array_reverse($path_arr);
			$get_kb_slug_path = isset($reverse_path_arr[1]) ? $reverse_path_arr[1] : '';

			if (in_array($get_kb_slug_path, $kb_slug)) {
				$kb_slug = $get_kb_slug_path;
			} else {
				$kb_slug = $kb_slug[0];
			}
		}

		return $kb_slug;
	}

	public static function breadcrumb_term_permalink($term_permalink)
	{
		$kb_slug = self::kb_slug();

		if ($kb_slug) {
			$term_permalink = str_replace('%knowledge_base%', $kb_slug, $term_permalink);
		} else {
			$term_permalink = str_replace('%knowledge_base%', 'non-knowledgebase', $term_permalink);
		}

		return $term_permalink;
	}

	public static function doc_category_nav_menu_permalink($atts, $item)
	{
		if ($item->type == 'taxonomy' && $item->object == 'doc_category') {
			$atts['href'] = self::doc_category_permalink($atts['href'], $item->object_id);
		}
		return $atts;
	}

	public static function kb_uncategorized_tax_query($tax_query, $wp_query)
	{
		$tax_query = array(
			array(
				'taxonomy' => 'knowledge_base',
				'field'    => 'slug',
				'terms'    => $wp_query->query['knowledge_base'],
				'operator'          => 'AND',
				'include_children'  => false
			),
		);

		return $tax_query;
	}

	public static function docs_show_permalinks($url, $post = null, $leavename = false)
	{
		if ($post->post_type != 'docs') {
			return $url;
		}
		global $wp_query;

		$knowledgebase = 'knowledge_base';
		$knowledgebase_tag = '%' . $knowledgebase . '%';
		$knowledgebase_terms = wp_get_object_terms($post->ID, $knowledgebase);

		if( ! isset( $wp_query->query_vars[ 'knowledge_base' ] ) ) {
			if (is_array($knowledgebase_terms) && sizeof($knowledgebase_terms) > 0) {
				$knowledgebase_terms = $knowledgebase_terms[0]->slug;
			} else {
				$knowledgebase_terms = 'non-knowledgebase';
			}
		} else {
			$knowledgebase_terms = $wp_query->query_vars[ 'knowledge_base' ];
		}
		// replace taxonomy tag with the term slug: /docs/%knowledge_base%/category/articlename
		return str_replace($knowledgebase_tag, $knowledgebase_terms, $url);
	}

	public static function doc_permalink_default($docs_slug)
	{
		$docs_slug = trim(BetterDocs_Docs_Post_Type::$docs_slug, '/') . '/%knowledge_base%/%doc_category%';
		return $docs_slug;
	}

	public static function disable_category_view($actions, $tag)
	{
		unset($actions['view']);
		return $actions;
	}

	public static function doc_category_permalink($termlink, $term_id)
	{
		$knowledgebase = 'knowledge_base';
		$knowledgebase_tag = '%' . $knowledgebase . '%';
		$kb_arr = get_term_meta($term_id, 'doc_category_knowledge_base', true);

		if (empty($kb_arr[0])) {
			$knowledge_base = 'non-knowledgebase';
		} else {
			$knowledge_base = $kb_arr[0];
		}

		$termlink = str_replace($knowledgebase_tag, $knowledge_base, $termlink);

		return $termlink;
	}

	public static function doc_category_link($termlink, $term, $taxonomy)
	{
        if ($taxonomy != 'doc_category') return $termlink;

        $kb_slug = self::kb_slug();

		if (empty($kb_slug)) {
			$category = get_term_by('slug', $term->slug, $taxonomy, ARRAY_A);
			$kb_arr = get_term_meta($category['term_id'], 'doc_category_knowledge_base', true);

			if (empty($kb_arr[0])) {
				$kb_slug = 'non-knowledgebase';
			} else {
				$kb_slug = $kb_arr[0];
			}
		}

        return str_replace('%knowledge_base%', $kb_slug, $termlink);
	}

	public static function doc_category_add_form()
	{
		$manage_docs_terms = get_terms('knowledge_base', array('hide_empty' => false));
		if ($manage_docs_terms) {
			$html = '<div class="form-field term-group">
				<label>' . esc_html__('Knowledge Base', 'betterdocs-pro') . '</label>
				<select id="doc-category-kb" class="doc-category-kb" name="doc_category_kb[]" multiple="multiple">
					<option value="" selected>' . esc_html__('No Knowledge Base', 'betterdocs-pro') . '</option>';
			foreach ($manage_docs_terms as $term) {
				$html .= '<option value="' . esc_attr($term->slug) . '">' . $term->name . '</option>';
			}
			$html .= '</select>
			</div>';

			echo $html;
		}
	}

	public static function doc_category_update_form($term)
	{
		$knowledge_base = get_term_meta($term->term_id, 'doc_category_knowledge_base', true);
		$manage_docs_terms = get_terms('knowledge_base', array('hide_empty' => false));

		if ($manage_docs_terms) {
			$html = '<tr class="form-field term-group-wrap">
				<th scope="row">
					<label>' . esc_html__('Knowledge Base', 'betterdocs-pro') . '</label>
				</th>
				<td>
					<select id="doc-category-kb" class="doc-category-kb" name="doc_category_kb[]" multiple="multiple">
						<option value="">' . esc_html__('No Knowledge Base', 'betterdocs-pro') . '</option>';
			foreach ($manage_docs_terms as $term) {
				$selected = (is_array($knowledge_base) && in_array($term->slug, $knowledge_base)) ? ' selected' : '';
				$html .= '<option value="' . esc_attr($term->slug) . '"' . $selected . '>' . $term->name . '</option>';
			}
			$html .= '</select>
				</td>
			</tr>';

			echo $html;
		}
	}

	/**
	 * Add a form field in the new category page
	 *
	 * @since 1.3.1
	 */
	public static function add_knowledge_base_meta($taxonomy)
	{
		echo '<div class="form-field term-group">
			<label for="knowledge-base-image-id">' . esc_html__('KB Icon', 'betterdocs-pro') . '</label>
			<input type="hidden" id="knowledge-base-image-id" name="term_meta[image-id]" class="custom_media_url" value="">
			<div id="knowledge-base-image-wrapper">
				<img src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">
			</div>
			<p>
				<input type="button" class="button button-secondary betterdocs_tax_media_button" id="betterdocs_tax_media_button" name="betterdocs_tax_media_button" value="' . esc_html__('Add Image', 'betterdocs-pro') . '" />
				<input type="button" class="button button-secondary doc_tax_media_remove" id="doc_tax_media_remove" name="doc_tax_media_remove" value="' . esc_html__('Remove Image', 'betterdocs-pro') . '" />
			</p>
		</div>';
	}

	/**
	 * Save the form field
	 *
	 * @since 1.3.1
	 */
	public static function save_knowledge_base_meta($term_id)
	{
		if (isset($_POST['term_meta'])) {
			$term_meta = get_option("knowledge_base_$term_id");
			$cat_keys = array_keys($_POST['term_meta']);

			foreach ($cat_keys as $key) {
				if (isset($_POST['term_meta'][$key])) {
					add_term_meta($term_id, "knowledge_base_$key", $_POST['term_meta'][$key]);
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
		}
		$order = self::get_max_taxonomy_order('knowledge_base');
        update_term_meta($term_id, 'kb_order', $order++);
	}

	/**
	 * Edit the form field
	 *
	 * @since 1.3.1
	 */
	public static function update_knowledge_base_meta($term, $taxonomy)
	{
		$kb_icon_id = get_term_meta($term->term_id, 'knowledge_base_image-id', true);
		do_action('betterdocs_knowledge_base_update_form_before', $term);

		$html = '<tr class="form-field term-group-wrap batterdocs-cat-media-upload">
			<th scope="row">
				<label for="knowledge-base-image-id">' . esc_html__('KB Icon', 'betterdocs-pro') . '</label>
			</th>
			<td>
				<input type="hidden" id="knowledge-base-image-id" name="term_meta[image-id]" value="'.esc_attr($kb_icon_id).'">
				<div id="knowledge-base-image-wrapper">';

			if ($kb_icon_id) {
				$html .= wp_get_attachment_image($kb_icon_id, 'thumbnail');
			} else {
				$html .= '<img src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
			}
		$html .= '</div>
				<p>
					<input type="button" class="button button-secondary betterdocs_tax_media_button" id="betterdocs_tax_media_button" name="betterdocs_tax_media_button" value="' . esc_html__('Add Image', 'betterdocs-pro') . '" />
					<input type="button" class="button button-secondary doc_tax_media_remove" id="doc_tax_media_remove" name="doc_tax_media_remove" value="' . esc_html__('Remove Image', 'betterdocs-pro') . '" />
				</p>
			</td>
		</tr>';

		echo $html;
	}

	/*
     * Update the form field value
     *
     * @since 1.3.1
    */
	public static function updated_knowledge_base_meta($term_id)
	{
		if (isset($_POST['term_meta'])) {
			$cat_keys = array_keys($_POST['term_meta']);
			foreach ($cat_keys as $key) {
				if (isset($_POST['term_meta'][$key])) {
					update_term_meta($term_id, "knowledge_base_$key", $_POST['term_meta'][$key]);
				}
			}
		}
	}

	/*
     * Add script
     *
     * @since 1.3.1
    */
	public static function kb_script()
	{
		global $current_screen;
		if ($current_screen->id == 'edit-knowledge_base') {
			?>
			<script>
				jQuery(document).ready(function($) {
					function betterdocs_kb_media_upload(button_class) {
						var _custom_media = true,
							_betterdocs_send_attachment = wp.media.editor.send.attachment;
						$('body').on('click', button_class, function(e) {
							var button_id = '#' + $(this).attr('id');
							var send_attachment_bkp = wp.media.editor.send.attachment;
							var button = $(button_id);
							_custom_media = true;
							wp.media.editor.send.attachment = function(props, attachment) {
								if (_custom_media) {
									$('#knowledge-base-image-id').val(attachment.id);
									$('#knowledge-base-image-wrapper').html(
										'<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />'
									);
									$('#knowledge-base-image-wrapper .custom_media_image').attr('src', attachment
										.url).css('display', 'block');
								} else {
									return _betterdocs_send_attachment.apply(button_id, [props, attachment]);
								}
							}
							wp.media.editor.open(button);
							return false;
						});
					}

					betterdocs_kb_media_upload('.betterdocs_tax_media_button.button');

					$('body').on('click', '.doc_tax_media_remove', function() {
						$('#knowledge-base-image-id').val('');
						$('#knowledge-base-image-wrapper').html(
							'<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />'
						);
					});

					$(document).ajaxComplete(function(event, xhr, settings) {
						var queryStringArr = settings.data.split('&');
						if ($.inArray('action=add-tag', queryStringArr) !== -1) {
							var xml = xhr.responseXML;
							$response = $(xml).find('term_id').text();
							if ($response != "") {
								// Clear the thumb image
								$('#knowledge-base-image-wrapper').html('');
							}
						}
					});
				});
			</script>
		<?php }
	}

	/**
	 *
	 * Default the taxonomy's terms' order if it's not set.
	 *
	 * @param string $tax_slug The taxonomy's slug.
	 */
	public static function default_term_order($tax_slug)
	{
		$terms = get_terms($tax_slug, array('hide_empty' => false));
		$order = self::get_max_taxonomy_order($tax_slug);

		foreach ($terms as $term) {
			if (!get_term_meta($term->term_id, 'kb_order', true)) {
				update_term_meta($term->term_id, 'kb_order', $order);
				$order++;
			}
		}
	}

	/**
	 * Order the terms on the admin side.
	 */
	public static function admin_order_terms()
	{
		$screen = function_exists('get_current_screen') ? get_current_screen() : '';
        $screen_id = isset($screen->id) ? $screen->id : '';
		if (in_array($screen_id, array('toplevel_page_betterdocs-admin', 'betterdocs_page_betterdocs-settings'))) {
			self::default_term_order('knowledge_base');
		}

		if (
			!isset($_GET['orderby'])
			&& !empty($screen)
			&& !empty($screen->base)
			&& $screen->base === 'edit-tags'
			&& $screen->taxonomy === 'knowledge_base'
		) {
			self::default_term_order($screen->taxonomy);

			add_filter('terms_clauses', array(__CLASS__, 'set_tax_order'), 10, 3);
		}
	}

	/**
	 *
	 * Get the maximum kb_order for this taxonomy.
	 * This will be applied to terms that don't have a tax position.
	 *
	 */

	private static function get_max_taxonomy_order($tax_slug)
	{
		global $wpdb;
		$max_term_order = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT MAX( CAST( tm.meta_value AS UNSIGNED ) )
				FROM $wpdb->terms t
				JOIN $wpdb->term_taxonomy tt ON t.term_id = tt.term_id AND tt.taxonomy = '%s'
				JOIN $wpdb->termmeta tm ON tm.term_id = t.term_id WHERE tm.meta_key = 'kb_order'",
				$tax_slug
			)
		);
		$max_term_order = is_array($max_term_order) ? current($max_term_order) : 0;
		return (int) $max_term_order === 0 || empty($max_term_order) ? 1 : (int) $max_term_order + 1;
	}

	/**
	 * Re-Order the taxonomies based on the kb_order value.
	 *
	 * @param array $pieces     Array of SQL query clauses.
	 * @param array $taxonomies Array of taxonomy names.
	 * @param array $args       Array of term query args.
	 */
	public static function set_tax_order($pieces, $taxonomies, $args)
	{
		foreach ($taxonomies as $taxonomy) {
			global $wpdb;
			if ($taxonomy === 'knowledge_base') {
				$join_statement = " LEFT JOIN $wpdb->termmeta AS kb_term_meta ON t.term_id = kb_term_meta.term_id AND kb_term_meta.meta_key = 'kb_order'";

				if (!self::does_substring_exist($pieces['join'], $join_statement)) {
					$pieces['join'] .= $join_statement;
				}

				$pieces['orderby'] = 'ORDER BY CAST( kb_term_meta.meta_value AS UNSIGNED )';
			}
		}
		return $pieces;
	}

	/**
	 * Check if a substring exists inside a string.
	 *
	 * @param string $string    The main string (haystack) we're searching in.
	 * @param string $substring The substring we're searching for.
	 *
	 * @return bool True if substring exists, else false.
	 */
	protected static function does_substring_exist($string, $substring)
	{
		return strstr($string, $substring) !== false;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $tags
	 * @return void
	 */
	public static function doc_permalink_tags($tags)
	{
		$knowledge_enabled = self::get_multiple_kb() == 1 ? '' : 'hidden';
		$tags['%knowledge_base%'] = [
			'class'      => "knowledge-base $knowledge_enabled",
			'aria-label' => __( 'Knowledge Bas', 'betterdocs-pro' ),
			'data-added' => __( 'knowledge_base added to permalink structure', 'betterdocs-pro' ),
			'data-used'  => __( 'knowledge_base (already used in permalink structure)', 'betterdocs-pro' ),
		];
		return $tags;
	}

	/**
	 *
	 * AJAX Handler to update terms' tax position.
	 *
	 */
	static function update_knowledge_base_order()
	{
		if (!check_ajax_referer('knowledge_base_order_nonce', 'knowledge_base_order_nonce', false)) {
			wp_send_json_error();
		}

		$kb_ordering_data = filter_var_array(wp_unslash($_POST['kb_ordering_data']), FILTER_SANITIZE_NUMBER_INT);
		$kb_index       = filter_var(wp_unslash($_POST['kb_index']), FILTER_SANITIZE_NUMBER_INT);

		foreach ($kb_ordering_data as $order_data) {
			if ($kb_index > 0) {
				$current_position = get_term_meta($order_data['term_id'], 'kb_order', true);

				if ((int) $current_position < (int) $kb_index) {
					continue;
				}
			}

			update_term_meta($order_data['term_id'], 'kb_order', ((int) $order_data['order'] + (int) $kb_index));
		}
		wp_send_json_success();
	}

    static function live_search_tax_query($tax_query, $post) {
        if (empty($post['kb_slug'])) return;
        return array(
            'taxonomy' => 'knowledge_base',
            'field'     => 'slug',
            'terms'    => $post['kb_slug'],
            'operator' => 'AND',
            'include_children' => false
        );
    }
}

BetterDocs_Multiple_Kb::init();
