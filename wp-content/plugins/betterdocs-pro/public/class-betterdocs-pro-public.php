<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpdeveloper.com
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
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
class Betterdocs_Pro_Public
{
    use BetterDocs_Content_Restrictions;
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

    public $internal_kb;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->internal_kb = $this->content_restriction();
        add_action('shutdown', array($this, 'shutdown')); 
		add_filter('betterdocs_docs_layout_select_choices', array($this, 'customizer_docs_page_layout_choices'));
        add_filter('betterdocs_archive_layout_choices', array( $this, 'category_archive_templates' ), 10, 1 );
        add_filter('betterdocs_archive_sidebar_template', array( $this, 'betterdocs_sidebar_layout_template' ), 10, 1 );
		add_filter('betterdocs_archive_template', array($this, 'get_docs_archive_template'));
		add_filter('betterdocs_single_layout_select_choices', array($this, 'customizer_single_layout_choices'));
		add_filter('betterdocs_single_template', array($this, 'get_docs_single_template'));
        add_filter('betterdocs_layout_documentation_page_settings', array($this, 'popular_docs_settings'));
        add_filter('betterdocs_option_default_settings', array($this, 'betterdocs_default_option_setting'), 10, 1);
        add_filter('betterdocs_search_form_atts', array($this, 'search_form_atts'));
        add_action('betterdocs_live_search_form_footer', array($this, 'srarch_form_footer'), 10, 1);
        add_action('betterdocs_after_live_search_form', array($this, 'popular_srarch'), 10, 1);
        add_action('betterdocs_advance_search_settings', array($this, 'advance_search_settings'));
        add_filter('child_category_exclude', array( $this, 'child_category_exclude' ), 10, 1);
        add_action('betterdocs_popular_keyword_limit_settings', array($this, 'popular_keyword_limit'));
        add_filter('betterdocs_search_button_text', array($this, 'search_button_text'), 10, 1);
        add_filter('betterdocs_posts_number', array( $this, 'betterdocs_add_note' ), 10, 1 );
        
        if ($this->internal_kb == 1) {
            add_filter('betterdocs_category_terms_object', array($this, 'restrict_doc_category'), 10, 1);
            add_filter('betterdocs_kb_terms_object', array($this, 'restrict_kb'), 10, 1);
            add_filter('betterdocs_tag_tax_query', array($this, 'restrict_tax_query'), 10, 2);
            add_filter('betterdocs_live_search_tax_query', array($this, 'search_articles_args'), 10, 1);
            add_filter('betterdocs_uncategorized_args', array( $this, 'uncategorized_docs_query' ), 10, 1 );
        }
        $live_search = BetterDocs_DB::get_settings('advance_search');
        if ($live_search == 1) {
            add_action('betterdocs_search_section', array($this, 'advance_search'), 10, 1);
        }
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_styles()
	{
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

        wp_register_style( $this->plugin_name, plugin_dir_url(__FILE__) . 'css/betterdocs-pro-public.css', array(), $this->version, 'all' );
	}

	public function enqueue_styles()
    {
        wp_enqueue_style( $this->plugin_name);
    }

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_scripts()
	{
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
        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/betterdocs-pro-public.js', array('jquery'), $this->version, true);
	}

    public function enqueue_scripts()
    {
        $current_term_id    = BetterDocs_Helper::get_tax() == 'doc_category' && get_queried_object() != null ? get_queried_object()->term_id : '';
		$multiple_kb		= BetterDocs_DB::get_settings('multiple_kb') != 'off' ? true : false;
		$terms_order		= BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'ASC' : BetterDocs_DB::get_settings('terms_order');
		$terms_orderby		= BetterDocs_DB::get_settings('alphabetically_order_term') != 'off' ? 'name' : BetterDocs_DB::get_settings('terms_orderby');
		$tax_page			= class_exists('BetterDocs_Helper') ? BetterDocs_Helper::get_tax() : '';
        $nested_subcategory = BetterDocs_DB::get_settings('nested_subcategory') != 'off' ? true : false;
        $kb_slug			= $multiple_kb == true ?  BetterDocs_Multiple_Kb::kb_slug() : '';
		$terms_count        = count( BetterDocs_Helper::get_doc_terms( $multiple_kb, $terms_order, $terms_orderby, $tax_page, $current_term_id, $nested_subcategory, $kb_slug ) );
        wp_enqueue_script($this->plugin_name);

        $single_reactions = array(
            'FEEDBACK' => array(
                'DISPLAY' => true,
                'TEXT'    => esc_html__('How did you feel?', 'betterdocs-pro'),
                'SUCCESS' => esc_html__('Thanks for your feedback', 'betterdocs-pro'),
                'URL'     => home_url() . '?rest_route=/betterdocs/feedback',
            ),
        );
        
        wp_localize_script($this->plugin_name, 'betterdocs_pro', $single_reactions);

        /**
         * This php to js object is created to keep data in key-value pairs,
         * which are to be used in other categories show more button(Important JS Object)
         **/
        wp_localize_script(
			$this->plugin_name,
			'show_more_catergories',
			array(
                'ajax_url' 			=> admin_url( 'admin-ajax.php' ),
                'nonce'             => wp_create_nonce('show-more-catergories'),
				'term_count' 	    => $terms_count,
				'tax_page' 		    => $tax_page,
				'current_term_id'   => $current_term_id,
				'kb_slug'			=> $kb_slug
			)
		);
    }

    public function load_assets()
    {
        $this->register_styles();
        $this->register_scripts();
        if ($this->is_templates() == true) {
            $this->enqueue_styles();
            $this->enqueue_scripts();
        } else {
            add_action('betterdocs_before_shortcode_load', array( $this, 'enqueue_styles'));
            add_action('betterdocs_before_shortcode_load', array( $this, 'enqueue_scripts'));
        }
    }

    public function is_templates()
    {
        if(is_plugin_active('elementor/elementor.php') && is_plugin_active('elementor-pro/elementor-pro.php')){
            $document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
            if (\Elementor\Plugin::instance()->editor->is_edit_mode() || (( get_post_meta(get_the_ID(), '_elementor_template_type', true)) && $document->is_built_with_elementor())) {
                return true;
            }
        }

        $tax = BetterDocs_Helper::get_tax();
        if (is_post_type_archive('docs') || $tax === 'knowledge_base' || $tax === 'doc_category' || is_singular('docs')) {
            return true;
        }
        return false;
    }

    public function is_betterdocs()
    {
        $tax = BetterDocs_Helper::get_tax();
        if (is_post_type_archive('docs') || $tax === 'knowledge_base' || $tax === 'doc_category' || is_singular('docs')) {
            return true;
        }
        return false;
    }

    public function category_archive_templates( $layouts ) {
        unset($layouts['layout-2']['pro']);
        unset($layouts['layout-3']['pro']);
        unset($layouts['layout-6']['pro']);
        return $layouts;
    }

    public function child_category_exclude( $settings ){
        unset( $settings['disable'] );
        return $settings;
    }

    /**
     * Get Docs Page Template for docs base directory.
     *
     * @param $template
     * @return mixed|string
     * @since    1.0.2
     */
	public function get_docs_archive_template($template)
	{
        $this->internal_kb_restriction();
        $docs_layout    = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');
        $archive_layout = get_theme_mod('betterdocs_archive_layout_select', 'layout-1'); 
        $tax = BetterDocs_Helper::get_tax();

        if($tax === 'knowledge_base') {
            $object = get_queried_object();
            setcookie('last_knowledge_base', $object->slug, time() + (86400 * 30), "/");
        }

        if (is_post_type_archive('docs')) {
            $multikb_layout = get_theme_mod('betterdocs_multikb_layout_select', 'layout-1');
            $layout_select = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');
            if (BetterDocs_Multiple_Kb::$enable == 1 && $multikb_layout === 'layout-2') {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/multiple-kb-2.php';
            } elseif (BetterDocs_Multiple_Kb::$enable == 1 && $multikb_layout === 'layout-3') {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/multiple-kb-3.php';
            } elseif (BetterDocs_Multiple_Kb::$enable == 1 && $multikb_layout === 'layout-4') {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/multiple-kb-tab-grid.php';
            } elseif (BetterDocs_Multiple_Kb::$enable == 1) {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/multiple-kb.php';
            } elseif ($layout_select === 'layout-2') {
                $template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';
            } elseif ($layout_select === 'layout-3') {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-box-3.php';
            } elseif ($layout_select === 'layout-4') {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-list-2.php';
            } elseif ($layout_select === 'layout-5') {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-layout-5.php';
            } elseif( $layout_select === 'layout-6' ) {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-layout-6.php';
            } else {
                $template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';
            }
        } elseif ($tax === 'doc_category') {
            if( $archive_layout == 'layout-1' ) {
                $template = BETTERDOCS_PUBLIC_PATH . 'partials/doc-category-templates/category-template-1.php';
            } elseif( $archive_layout == 'layout-6' ) {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/doc-category-templates/category-template-2.php';
            }
        } elseif (is_tax('doc_tag')) {
            $template = BETTERDOCS_PUBLIC_PATH . 'betterdocs-tag-template.php';
        } elseif ($tax === 'knowledge_base' && $docs_layout === 'layout-2') {
            $template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';
        } elseif ($tax === 'knowledge_base' && $docs_layout === 'layout-3') {
            $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-box-3.php';
        } elseif ($tax === 'knowledge_base' && $docs_layout === 'layout-4') {
            $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-list-2.php';
        } elseif ($tax === 'knowledge_base' && $docs_layout === 'layout-5') {
            $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-layout-5.php';
        } elseif( $tax === 'knowledge_base' &&  $docs_layout == 'layout-6' ){
            $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-layout-6.php';
        }elseif ($tax === 'knowledge_base') {
            $template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';
        }
		return $template;
	}

	public function customizer_docs_page_layout_choices($choices)
	{
        unset( $choices['layout-3']['pro'] );
        unset( $choices['layout-4']['pro'] );
        unset( $choices['layout-5']['pro'] );
        unset( $choices['layout-6']['pro'] );
		return $choices;
	}

    public function betterdocs_sidebar_layout_template( $path ) {
        $layout = get_theme_mod('betterdocs_archive_layout_select');
        if( $layout == 'layout-2' ) {
            $path = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/sidebars/sidebar-2.php';
        } else if( $layout == 'layout-3' ){
            $path = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/sidebars/sidebar-3.php';
        }
        return $path;
    }

	/**
	 * Get Single Page Template for docs base directory.
	 *
	 * @param int $single_template Overirde single templates.
	 * 
	 * @since    1.0.0
	 */
	public function get_docs_single_template($single_template)
	{
		if (is_singular('docs')) {
            setcookie('docs_visited_' . get_the_ID(), rand().get_the_ID(), time() + (86400 * 180), "/");
            $this->internal_kb_restriction();
			$layout_select = get_theme_mod('betterdocs_single_layout_select', 'layout-1');
			if ($layout_select === 'layout-2') {
				$single_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/template-single/layout-2.php';
			} elseif ($layout_select === 'layout-3') {
				$single_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/template-single/layout-3.php';
			} elseif ($layout_select === 'layout-4') {
                $single_template = BETTERDOCS_PUBLIC_PATH . 'partials/template-single/layout-4.php';
            } elseif ($layout_select === 'layout-5') {
                $single_template = BETTERDOCS_PUBLIC_PATH . 'partials/template-single/layout-5.php';
            } elseif($layout_select === 'layout-6'){
                $single_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/template-single/layout-6.php';
            }else {
                $single_template = BETTERDOCS_PUBLIC_PATH . 'partials/template-single/layout-1.php';
            }
		}
		return $single_template;
	}

	public function customizer_single_layout_choices($choices)
	{
		$choices['layout-2'] = array(
            'label' => esc_html__('Minimalist Layout', 'betterdocs-pro'),
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-2.png',
		);
		$choices['layout-3'] = array(
            'label' => esc_html__('Artisan Layout', 'betterdocs-pro'),
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-3.png',
		);
		$choices['layout-4'] = array(
            'label' => esc_html__('Abstract Layout', 'betterdocs-pro'),
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-4.png',
		);
		$choices['layout-5'] = array(
            'label' => esc_html__('Modern Layout', 'betterdocs-pro'),
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-5.png',
		);
        unset($choices['layout-6']['pro']);
		return $choices;
	}

    public function popular_docs_settings($settings) 
    {
        $settings['fields']['popular_docs'] = array(
            'type'        => 'title',
            'label'       => __('Popular Docs' , 'betterdocs-pro'),
            'priority'    => 10,
        );

        $settings['fields']['betterdocs_popular_docs_text'] = array(
            'type'        => 'text',
            'label'       => __('Popular Docs Text' , 'betterdocs-pro'),
            'default'     => __('Popular Docs', 'betterdocs-pro'),
            'priority'    => 10,
        );

        $settings['fields']['betterdocs_popular_docs_number'] = array(
            'type'      => 'number',
            'label'     => __('Popular Posts Number' , 'betterdocs-pro'),
            'default'   => 10,
            'priority'	=> 10
        );

        return $settings;
    }

    public function betterdocs_default_option_setting($values) 
    {
        $values['betterdocs_popular_docs_text']   = esc_html__('Popular Docs', 'betterdocs-pro');
        $values['betterdocs_popular_docs_number'] = 10;
        $values['search_button_text']             = esc_html__('Search','betterdocs-pro');
        $values['reporting_frequency']            = 'betterdocs_daily';
        $values['select_reporting_data']          = array('overview', 'top-docs', 'most-search');
        $values['reporting_subject_updated']      = wp_sprintf( '%s %s %s', __( 'Your Documentation Performance of', 'betterdocs' ),  get_bloginfo( 'name' ), __( 'Website', 'betterdocs' ) );
        $values['child_category_exclude']         = 'false';
        return $values;
    }

    public function advance_search_settings()
    {
        $settings = array(
            'type'        => 'checkbox',
            'label'       => __('Enable Advanced Search' , 'betterdocs-pro'),
            'default'     => 1,
            'priority'    => 10
        );
        return $settings;
    }

    public function search_button_text($settings) {
        $settings = array(
            'type'     => 'text',
            'label'    => __('Search Button Text', 'betterdocs-pro'),
            'priority' => 10,
            'default'  => esc_html__('Search','betterdocs-pro'),
        );
        return $settings;
    }

    public function betterdocs_add_note( $settings ) {
        $settings['description'] = __('Note: This setting is not applicable for handbook layout.' , 'betterdocs-pro');
        return $settings;
    }

    public function popular_keyword_limit()
    {
        $settings = array(
            'type'        => 'number',
            'label'       => __('Minimum amount of Keywords Search' , 'betterdocs-pro'),
            'default'     => 5,
            'priority'    => 10
        );
        return $settings;
    }

    public function popular_search_keyword()
    {
        $keywords = array();
        $search_table = get_option( 'betterdocs_db_version' );
        $popular_keyword_limit = BetterDocs_DB::get_settings('popular_keyword_limit');
        if ($search_table == true) {
            global $wpdb;
            $select = "SELECT search_keyword.keyword, SUM(search_log.count) as count";
            $join = "FROM {$wpdb->prefix}betterdocs_search_keyword as search_keyword 
                    JOIN {$wpdb->prefix}betterdocs_search_log as search_log on search_keyword.id = search_log.keyword_id";
            $get_search_keyword = $wpdb->get_results(
                $wpdb->prepare("
                        {$select}
                        {$join}
                        GROUP BY search_log.keyword_id
                        ORDER BY count DESC
                        LIMIT %d
                    ", $popular_keyword_limit)
            );

            if ($get_search_keyword) {
                foreach ($get_search_keyword as $key=>$value) {
                    if ($value > $popular_keyword_limit) {
                        array_push($keywords, $value->keyword);
                    }
                }
            }
        }

        return $keywords;
    }

    public function search_form_atts($atts)
    {
        $search_button_text = BetterDocs_DB::get_settings('search_button_text');
        $atts['category_search'] = false;
        $atts['search_button'] = false;
        $atts['popular_search'] = false;
        $atts['popular_search_title'] = false;
        $atts['search_button_text'] = empty($search_button_text) ? 'Search' : $search_button_text;
        return $atts;
    }

    public function srarch_form_footer($get_args) {
        if ( $get_args['category_search'] == true ) {
            $exclude_child_terms = BetterDocs_DB::get_settings('child_category_exclude') != 'off' && BetterDocs_DB::get_settings('child_category_exclude') != 'false' ? 'true' : 'false';
            echo '<select class="betterdocs-search-category">
                <option value="">'.esc_html__('All Categories','betterdocs-pro').'</option>
                '.BetterDocs_Helper::term_options('doc_category', '', $exclude_child_terms).'
            </select>';
        }

        if ( $get_args['search_button'] == true ) {
            echo '<input class="search-submit" type="submit" value="'.esc_html__($get_args['search_button_text'],'betterdocs-pro').'">';
        }

        if (BetterDocs_DB::get_settings('multiple_kb') == 1 && BetterDocs_DB::get_settings('kb_based_search') == 1) {
            $kb_slug = BetterDocs_Multiple_Kb::kb_slug();
            echo '<input type="hidden" value="' . esc_attr($kb_slug) . '" class="betterdocs-search-kbslug betterdocs-search-submit">';
        }
    }

    public function popular_srarch($get_args) {
        $html = '';
        $output = betterdocs_generate_output_pro();
        if ( $get_args['popular_search' ] == true && !empty($this->popular_search_keyword()) ) {
            if ($get_args['popular_search_title'] == true) {
                $search_title = $get_args['popular_search_title'];
            } else {
                $search_title = $output['betterdocs_popular_search_text'];
            }
            $html = '<div class="betterdocs-popular-search-keyword">';
            $html .= '<span class="popular-search-title">'.esc_html($search_title).' </span>';
            foreach ($this->popular_search_keyword() as $keyword) {
                $html .= '<span class="popular-keyword">'.$keyword.'</span>';
            }
            $html .= '</div>';
        }
        echo $html;
    }

    public function advance_search()
    {
        $output = betterdocs_generate_output();
        $output_pro = betterdocs_generate_output_pro();
        $search_placeholder = BetterDocs_DB::get_settings('search_placeholder');
        $search_heading_switch = $output['betterdocs_live_search_heading_switch'];
        $search_heading = $output['betterdocs_live_search_heading'];
        $search_subheading = $output['betterdocs_live_search_subheading'];
        $search_category = $output_pro['betterdocs_category_search_toggle'];
        $search_button = $output_pro['betterdocs_search_button_toggle'];
        $popular_search = $output_pro['betterdocs_popular_search_toggle'];
        $heading_tag = $output['betterdocs_live_search_heading_tag'];
        $subheading_tag = $output['betterdocs_live_search_subheading_tag'];

        return '<div class="betterdocs-search-form-wrap">'. do_shortcode( '[betterdocs_search_form 
            placeholder="'.$search_placeholder.'" 
            enable_heading="'.$search_heading_switch.'"
            heading="'.$search_heading.'" 
            subheading="'.$search_subheading.'"
            category_search="'.$search_category.'"
            search_button="'.$search_button.'"
            popular_search="'.$popular_search.'"
            heading_tag="'.$heading_tag.'"
            subheading_tag="'.$subheading_tag.'"]').'</div>';
    }


    public function shutdown() {
        global $migration_Process;
        global $wpdb;

        $queue_set = get_option('betterdocs_analytics_migration_queue_set', false);
        if( $queue_set ) {
            return;
        }

        $completed = get_option('betterdocs_analytics_migration', false);
        if( $completed ) {
            return;
        }

        $count = count($wpdb->get_results(
            "SELECT post_id, meta_value
		FROM {$wpdb->prefix}postmeta
		WHERE meta_key = '_betterdocs_meta_impression_per_day'"
        ));

        $per_page = 10;
        $total_page = ceil($count / $per_page);

        for( $page = 1; $page <= $total_page; $page++ ) {
            $offset = ($page * $per_page) - $per_page;
            $migration_Process->push_to_queue( [
                'count' => $count,
                'total_page' => $total_page,
                'page_now' => $page,
                'per_page' => $per_page,
                'offset' => $offset,
            ] );
        }

        update_option('betterdocs_analytics_migration_queue_set', true);

        $migration_Process->save()->dispatch();
    }
}
