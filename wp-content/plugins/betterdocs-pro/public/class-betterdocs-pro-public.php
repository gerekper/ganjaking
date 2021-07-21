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
class Betterdocs_Pro_Public
{
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
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_filter('betterdocs_docs_layout_select_choices', array($this, 'customizer_docs_page_layout_choices'));
		add_filter('betterdocs_archive_template', array($this, 'get_docs_archive_template'));
		add_filter('betterdocs_single_layout_select_choices', array($this, 'customizer_single_layout_choices'));
		add_filter('single_template', array($this, 'get_docs_single_template'), 100);
        add_filter('betterdocs_post_order_options', array($this, 'post_order_options'), 1);
        add_filter('betterdocs_post_order_default', array($this, 'post_order_default'), 1);
		add_action('betterdocs_docs_before_social', array($this, 'betterdocs_article_reactions'));
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

        wp_register_style( 'simplebar', plugin_dir_url(__FILE__) . 'css/simplebar.css', array(), $this->version, 'all' );
        wp_register_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/betterdocs-pro-public.css', array(), $this->version, 'all');
	}

	public function enqueue_styles()
    {
        wp_enqueue_style( 'simplebar');
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
        wp_register_script('simplebar', plugin_dir_url(__FILE__) . 'js/simplebar.js', array( 'jquery' ), $this->version, true);
        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/betterdocs-pro-public.js', array('jquery'), $this->version, true);
	}

    public function enqueue_scripts()
    {
        wp_enqueue_script('simplebar');
        wp_enqueue_script($this->plugin_name);
        $single_reactions = array(
            'FEEDBACK' => array(
                'DISPLAY' => true,
                'TEXT'    => esc_html__('How did you feel?', 'betterdocs-pro'),
                'SUCCESS' => esc_html__('Thanks for your feedback', 'betterdocs-pro'),
                'URL'     => home_url() . '?rest_route=/betterdocs/feedback',
            ),
        );
        wp_localize_script($this->plugin_name, 'betterdocs', $single_reactions);
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

    public function get_404_template()
    {
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        get_template_part( 404 );
        exit();
    }

    public function restricted_redirect_url()
    {
        $restricted_redirect_url = BetterDocs_DB::get_settings('restricted_redirect_url');
        if ($restricted_redirect_url) {
            wp_redirect($restricted_redirect_url);
        } else {
            $this->get_404_template();
        }
    }

    public function content_visibility_by_role()
    {
        global $current_user;
        $roles = $current_user->roles;
        $content_visibility = BetterDocs_DB::get_settings('content_visibility');
        if (is_user_logged_in() && is_array($content_visibility) && (in_array($roles[0], $content_visibility) || in_array('all', $content_visibility))) {
            return true;
        } else {
            return false;
        }
    }

    public function internal_kb_restriction()
    {
        global $wp_query;
        $content_restriction = BetterDocs_DB::get_settings('enable_content_restriction');
        $restrict_template = BetterDocs_DB::get_settings('restrict_template');
        $restrict_template = !empty($restrict_template) ? $restrict_template : array();
        $restrict_category = BetterDocs_DB::get_settings('restrict_category');
        $restrict_category = !empty($restrict_category) ? $restrict_category : array();
        $restrict_kb = BetterDocs_DB::get_settings('restrict_kb');
        $restrict_kb = !empty($restrict_kb) ? $restrict_kb : array();
        $tax = BetterDocs_Helper::get_tax();

        $cat_terms = get_the_terms(get_the_ID(), 'doc_category');
        $kb_terms = get_the_terms(get_the_ID(), 'knowledge_base');

        if ($this->is_templates() && $content_restriction == 1 && $this->content_visibility_by_role() == false
            && (is_array($restrict_template) && in_array('all', $restrict_template)
                || (is_array($restrict_template) && in_array('docs', $restrict_template))
                || ($tax === 'knowledge_base'
                    && (is_array($restrict_template) && in_array('knowledge_base', $restrict_template)
                        && (is_array($restrict_kb) && (in_array('all', $restrict_kb) || in_array($wp_query->query['knowledge_base'], $restrict_kb)))))
                || ($tax === 'doc_category'
                    && (is_array($restrict_template) && in_array('doc_category', $restrict_template)
                        && (is_array($restrict_category) && (in_array('all', $restrict_category) || in_array($wp_query->query['doc_category'], $restrict_category)))))
                || (is_singular('docs')
                    && ((is_array($restrict_template) && in_array('doc_category', $restrict_template))
                        && (is_array($restrict_category) && (in_array('all', $restrict_category) || in_array($cat_terms[0]->slug, $restrict_category)))
                        || ((is_array($restrict_template) && in_array('knowledge_base', $restrict_template))
                            && (is_array($restrict_kb) && (in_array('all', $restrict_kb) || in_array($kb_terms[0]->slug, $restrict_kb)))))
                )
            )
        ) {
            $this->restricted_redirect_url();
        }
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
        $docs_layout = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');
        $tax = BetterDocs_Helper::get_tax();
        if (is_post_type_archive('docs')) {
            $multikb_layout = get_theme_mod('betterdocs_multikb_layout_select', 'layout-1');
            $layout_select = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');

            if (BetterDocs_Multiple_Kb::$enable == 1 && $multikb_layout === 'layout-2') {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/multiple-kb-2.php';
            } elseif (BetterDocs_Multiple_Kb::$enable == 1) {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/multiple-kb.php';
            } elseif ($layout_select === 'layout-2') {
                $template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';
            } elseif ($layout_select === 'layout-3') {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-box-3.php';
            } elseif ($layout_select === 'layout-4') {
                $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-list-2.php';
            } else {
                $template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';
            }
        } elseif ($tax === 'doc_category') {
            $template = BETTERDOCS_PUBLIC_PATH . 'betterdocs-category-template.php';
        } elseif (is_tax('doc_tag')) {
            $template = BETTERDOCS_PUBLIC_PATH . 'betterdocs-tag-template.php';
        } elseif ($tax === 'knowledge_base' && $docs_layout === 'layout-2') {
            $template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';
        } elseif ($tax === 'knowledge_base' && $docs_layout === 'layout-3') {
            $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-box-3.php';
        } elseif ($tax === 'knowledge_base' && $docs_layout === 'layout-4') {
            $template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/archive-template/category-list-2.php';
        } elseif ($tax === 'knowledge_base') {
            $template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';
        }
		return $template;
	}

	public function customizer_docs_page_layout_choices($choices)
	{
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
	public function get_docs_single_template($single_template)
	{
        $this->internal_kb_restriction();
		if (is_singular('docs')) {
			$layout_select = get_theme_mod('betterdocs_single_layout_select', 'layout-1');
			if ($layout_select === 'layout-2') {
				$single_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/template-single/layout-2.php';
			} elseif ($layout_select === 'layout-3') {
				$single_template = BETTERDOCS_PRO_PUBLIC_PATH . 'partials/template-single/layout-3.php';
			} else {
				$single_template = BETTERDOCS_PUBLIC_PATH . 'partials/template-single/layout-1.php';
			}
		}
		return $single_template;
	}

	public function customizer_single_layout_choices($choices)
	{
		$choices['layout-2'] = array(
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-2.png',
		);
		$choices['layout-3'] = array(
			'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-3.png',
		);
		return $choices;
	}

	public function betterdocs_article_reactions($reactions = '')
	{
		$post_reactions = get_theme_mod('betterdocs_post_reactions', true);

		if ($post_reactions == true) {
			$reactions = do_shortcode('[betterdocs_article_reactions]');
		}

		return $reactions;
	}

    public function post_order_options($options)
    {
        $options['betterdocs_order'] = __('BetterDocs Order', 'betterdocs');
        return $options;
    }

    public function post_order_default()
    {
        return 'betterdocs_order';
    }
}
