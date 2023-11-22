<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Features;
use DynamicContentForElementor\Core\Upgrade\Manager as UpgradeManager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Main Plugin Class
 *
 * @since 0.0.1
 */
class Plugin
{
    /**
     * @var \DynamicContentForElementor\Controls
     */
    public $controls;
    /**
     * @var \DynamicContentForElementor\Widgets
     */
    public $widgets;
    /**
     * @var \DynamicLicense\License
     */
    public $license_system;
    /**
     * @var \DynamicContentForElementor\Features
     */
    public $features;
    /**
     * @var \DynamicContentForElementor\SaveGuard
     */
    public $save_guard;
    /**
     * @var \DynamicContentForElementor\Cryptocurrency
     */
    public $cryptocurrency;
    /**
     * @var \DynamicContentForElementor\PdfHtmlTemplates
     */
    public $pdf_html_templates;
    /**
     * @var \DynamicContentForElementor\TemplateSystem
     */
    public $template_system;
    /**
     * @var \DynamicContentForElementor\Updates
     */
    public $updates;
    /**
     * @var \DynamicContentForElementor\Wpml
     */
    public $wpml;
    /**
     * @var \DynamicContentForElementor\Stripe
     */
    public $stripe;
    /**
     * @var AdminPages\Manager
     */
    public $admin_pages;
    /**
     * @var \DynamicContentForElementor\PageSettings
     */
    public $page_settings;
    /**
     * @var \DynamicContentForElementor\Extensions
     */
    public $extensions;
    /**
     * @var \DynamicContentForElementor\RollbackManager
     */
    public $rollback_manager;
    public $assets;
    protected static $instance;
    public $cron;
    /**
     * @var UpgradeManager
     */
    public $upgrade;
    /**
     * Constructor
     *
     * @since 0.0.1
     * @access public
     */
    public function __construct()
    {
        self::$instance = $this;
        $this->init();
    }
    /**
     * @return Plugin
     */
    public static function instance()
    {
        if (\is_null(self::$instance)) {
            // Ensure Elementor Free is active
            if (!did_action('elementor/loaded')) {
                add_action('admin_notices', 'dce_fail_load');
                // having a function return ?Plugin would put too much burden on the code:
                // @phpstan-ignore-next-line
                return null;
            }
            // Check Elementor version
            if (\version_compare(ELEMENTOR_VERSION, DCE_MINIMUM_ELEMENTOR_VERSION, '<')) {
                add_action('admin_notices', 'dce_admin_notice_minimum_elementor_version');
                // @phpstan-ignore-next-line
                return null;
            }
            // Check Elementor Pro version
            if (\defined('ELEMENTOR_PRO_VERSION') && \version_compare(ELEMENTOR_PRO_VERSION, DCE_MINIMUM_ELEMENTOR_PRO_VERSION, '<')) {
                add_action('admin_notices', 'dce_admin_notice_minimum_elementor_pro_version');
                // @phpstan-ignore-next-line
                return null;
            }
            new self();
        }
        return self::$instance;
    }
    public function init()
    {
        $this->init_managers();
        add_action('elementor/init', [$this, 'add_dce_to_elementor'], 0);
        add_filter('plugin_action_links_' . DCE_PLUGIN_BASE, [$this, 'plugin_action_links']);
        add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 2);
        add_filter('pre_handle_404', '\\DynamicContentForElementor\\Helper::allow_posts_pagination', 999, 2);
        // Enchanted Tab for Elementor Pro Form
        $features_enchanted = self::instance()->features->filter_by_tag('enchanted');
        $active_features_enchanted = \array_filter($features_enchanted, function ($e) {
            return 'active' === $e['status'];
        });
        if (!empty($active_features_enchanted)) {
            add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'add_form_fields_enchanted_tab']);
        }
    }
    public function init_managers()
    {
        $this->save_guard = new \DynamicContentForElementor\SaveGuard();
        $this->features = new Features();
        $this->controls = new \DynamicContentForElementor\Controls();
        $this->page_settings = new \DynamicContentForElementor\PageSettings();
        $this->admin_pages = new \DynamicContentForElementor\AdminPages\Manager();
        $this->widgets = new \DynamicContentForElementor\Widgets();
        $this->stripe = new \DynamicContentForElementor\Stripe();
        $this->pdf_html_templates = new \DynamicContentForElementor\PdfHtmlTemplates();
        $this->cryptocurrency = new \DynamicContentForElementor\Cryptocurrency();
        new \DynamicContentForElementor\Ajax();
        $this->assets = new \DynamicContentForElementor\Assets();
        new \DynamicContentForElementor\Dashboard();
        $this->license_system = new \DynamicOOOS\DynamicLicense\License(
            ['prefix' => DCE_PREFIX, 'plugin_base' => DCE_PLUGIN_BASE, 'slug' => DCE_SLUG, 'admin_license_page' => 'dce-license', 'beta_option' => 'dce_beta', 'product_unique_id' => 'WP-DCE-1', 'version' => DCE_VERSION, 'license_url' => DCE_LICENSE_URL, 'pricing_page' => DCE_PRICING_PAGE],
            $this->admin_pages->notices,
            // translators: all are HTML tags:
            ['buy' => esc_html__('It seems that your copy is not activated, please %1$sactivate it%2$s or %3$sbuy a new license%4$s.', 'dynamic-content-for-elementor')]
        );
        $this->rollback_manager = new \DynamicContentForElementor\RollbackManager();
        $this->updates = new \DynamicContentForElementor\Updates();
        $this->wpml = new \DynamicContentForElementor\Wpml();
        $this->cron = new \DynamicContentForElementor\Cron();
        $this->template_system = new \DynamicContentForElementor\TemplateSystem();
        new \DynamicContentForElementor\Elements();
        // WP-CLI Integration
        if (\defined('WP_CLI') && WP_CLI) {
            new \DynamicContentForElementor\WpCli();
            \WP_CLI::add_command('dce', \DynamicContentForElementor\WpCli::class);
        }
        // Init hook
        do_action('dynamic_content_for_elementor/init');
    }
    /**
     * Activation fired by 'register_activation_hook'
     */
    public static function activation()
    {
        set_transient('dce_activation_redirect', \true, MINUTE_IN_SECONDS);
    }
    /**
     * Uninstall fired by 'register_uninstall_hook'
     */
    public static function uninstall()
    {
        self::instance()->license_system->deactivate_license();
        // If the deactivation request returns an error the license key is not removed, so it's better to remove the key manually
        delete_option('dce_license_key');
        self::instance()->cron->clear_all_tasks();
        if (\defined('DCE_REMOVE_ALL_DATA') && DCE_REMOVE_ALL_DATA) {
            delete_option(DCE_TEMPLATE_SYSTEM_OPTION);
            delete_option(Features::FEATURES_STATUS_OPTION);
        }
    }
    /**
     * Add Actions
     *
     * @since 0.0.1
     *
     * @access private
     */
    public function add_dce_to_elementor()
    {
        // Global Settings Panel
        \DynamicContentForElementor\GlobalSettings::init();
        $this->upgrade = UpgradeManager::instance();
        // Controls
        if (\version_compare(ELEMENTOR_VERSION, '3.5.0', '>=')) {
            add_action('elementor/controls/controls_registered', [$this->controls, 'on_controls_registered']);
        } else {
            add_action('elementor/controls/register', [$this->controls, 'on_controls_registered']);
        }
        // Force Dynamic Tags
        if (!\defined('DCE_NO_CM_OVERRIDE') || !DCE_NO_CM_OVERRIDE) {
            \Elementor\Plugin::$instance->controls_manager = new \DynamicContentForElementor\ForceDynamicTags();
        }
        // Extensions
        $this->extensions = new \DynamicContentForElementor\Extensions();
        // Page Settings
        $this->page_settings->on_page_settings_registered();
        // Widgets
        add_action('elementor/widgets/register', [$this->widgets, 'on_widgets_registered']);
    }
    /**
     * Add Enchanted Tab for Form Fields
     *
     * This form tab is used for many extensions. We put it here avoiding repetition
     *
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function add_form_fields_enchanted_tab(\Elementor\Widget_Base $widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['form_fields_enchanted_tab' => ['type' => 'tab', 'tab' => 'enchanted', 'label' => '<i class="dynicon icon-dyn-logo-dce" aria-hidden="true"></i>', 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_enchanted_tab', 'condition' => ['field_type!' => 'step']]];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public static function plugin_action_links($links)
    {
        $links['config'] = '<a title="' . __('Features', 'dynamic-content-for-elementor') . '" href="' . admin_url() . 'admin.php?page=dce-features">' . __('Features', 'dynamic-content-for-elementor') . '</a>';
        return $links;
    }
    public function plugin_row_meta($plugin_meta, $plugin_file)
    {
        if ('dynamic-content-for-elementor/dynamic-content-for-elementor.php' === $plugin_file) {
            $row_meta = ['docs' => '<a href="https://help.dynamic.ooo/" aria-label="' . esc_attr(__('View Documentation', 'dynamic-content-for-elementor')) . '" target="_blank">' . __('Docs', 'dynamic-content-for-elementor') . '</a>', 'community' => '<a href="https://facebook.com/groups/dynamic.ooo" aria-label="' . esc_attr(__('Facebook Community', 'dynamic-content-for-elementor')) . '" target="_blank">' . __('FB Community', 'dynamic-content-for-elementor') . '</a>'];
            $plugin_meta = \array_merge($plugin_meta, $row_meta);
        }
        return $plugin_meta;
    }
}
\DynamicContentForElementor\Plugin::instance();
