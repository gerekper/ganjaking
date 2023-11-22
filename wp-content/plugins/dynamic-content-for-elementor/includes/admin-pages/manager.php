<?php

namespace DynamicContentForElementor\AdminPages;

use DynamicContentForElementor\Plugin;
class Manager
{
    public $features_page;
    public $api;
    public $license;
    /**
     * @var Notices
     */
    public $notices;
    public $template_system;
    /**
     * @var Settings\Settings
     */
    public $settings;
    public function __construct()
    {
        $this->features_page = new \DynamicContentForElementor\AdminPages\Features\FeaturesPage();
        $this->api = new \DynamicContentForElementor\AdminPages\Api();
        $this->template_system = new \DynamicContentForElementor\AdminPages\TemplateSystem();
        $this->license = new \DynamicContentForElementor\AdminPages\License();
        $this->notices = new \DynamicContentForElementor\AdminPages\Notices();
        $this->settings = new \DynamicContentForElementor\AdminPages\Settings\Settings();
        add_action('admin_init', [$this, 'maybe_redirect_to_wizard_on_activation']);
        add_action('admin_menu', [$this, 'add_menu_pages'], 200);
        add_action('admin_notices', [$this, 'warning_old_conditional']);
        add_action('elementor/init', [$this, 'warning_lazyload']);
        $this->warning_features_bloat();
    }
    public function maybe_redirect_to_wizard_on_activation()
    {
        if (!get_transient('dce_activation_redirect')) {
            return;
        }
        if (wp_doing_ajax()) {
            return;
        }
        delete_transient('dce_activation_redirect');
        if (is_network_admin() || isset($_GET['activate-multi'])) {
            return;
        }
        if (get_option('dce_done_activation_redirection')) {
            return;
        }
        update_option('dce_done_activation_redirection', \true);
        wp_safe_redirect(admin_url('admin.php?page=dce-features'));
        exit;
    }
    public static function get_dynamic_ooo_icon_svg_base64()
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 88.74 71.31"><path d="M35.65,588.27h27.5c25.46,0,40.24,14.67,40.24,35.25v.2c0,20.58-15,35.86-40.65,35.86H35.65Zm27.81,53.78c11.81,0,19.65-6.51,19.65-18v-.2c0-11.42-7.84-18-19.65-18H55.41v36.26Z" transform="translate(-35.65 -588.27)" fill="#a8abad"/><path d="M121.69,609.94a33.84,33.84,0,0,0-7.56-11.19,36.51,36.51,0,0,0-11.53-7.56A37.53,37.53,0,0,0,88,588.4a43.24,43.24,0,0,0-5.4.34,36.53,36.53,0,0,1,20.76,10,33.84,33.84,0,0,1,7.56,11.19,35.25,35.25,0,0,1,2.7,13.79v.2a34.79,34.79,0,0,1-2.75,13.79,35.21,35.21,0,0,1-19.19,18.94,36.48,36.48,0,0,1-9.27,2.45,42.94,42.94,0,0,0,5.39.35,37.89,37.89,0,0,0,14.67-2.8,35.13,35.13,0,0,0,19.19-18.94,34.79,34.79,0,0,0,2.75-13.79v-.2A35.25,35.25,0,0,0,121.69,609.94Z" transform="translate(-35.65 -588.27)" fill="#a8abad" /></svg>';
        return \base64_encode($svg);
    }
    public function add_menu_pages()
    {
        // Menu
        add_menu_page(DCE_PRODUCT_NAME, DCE_PRODUCT_NAME, 'manage_options', 'dce-features', [$this->features_page, 'page_callback'], 'data:image/svg+xml;base64,' . self::get_dynamic_ooo_icon_svg_base64(), '58.6');
        // Features
        add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . __('Features', 'dynamic-content-for-elementor'), __('Features', 'dynamic-content-for-elementor'), 'manage_options', 'dce-features', [$this->features_page, 'page_callback']);
        add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . __('Settings', 'dynamic-content-for-elementor'), __('Settings', 'dynamic-content-for-elementor'), 'manage_options', 'dce-settings', [$this->settings, 'display_settings_page']);
        // HTML Templates (only for PDF Generator for Elementor Pro Form or PDF Button)
        if (Plugin::instance()->features->is_feature_active('ext_form_pdf') || Plugin::instance()->features->is_feature_active('wdg_pdf')) {
            add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . __('HTML Templates', 'dynamic-content-for-elementor'), __('HTML Templates', 'dynamic-content-for-elementor'), 'manage_options', 'edit.php?post_type=' . \DynamicContentForElementor\PdfHtmlTemplates::CPT);
        }
        // Template System
        add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . __('Template System', 'dynamic-content-for-elementor'), __('Template System', 'dynamic-content-for-elementor'), 'manage_options', 'dce-templatesystem', [$this->template_system, 'display_form']);
        // Integrations
        add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . __('Integrations', 'dynamic-content-for-elementor'), __('Integrations', 'dynamic-content-for-elementor'), 'manage_options', 'dce-integrations', [$this->api, 'display_form']);
        // License
        add_submenu_page('dce-features', DCE_PRODUCT_NAME . ' - ' . __('License', 'dynamic-content-for-elementor'), __('License', 'dynamic-content-for-elementor'), 'administrator', 'dce-license', [$this->license, 'show_license_form']);
    }
    /**
     * @return void
     */
    public function warning_lazyload()
    {
        $lazyload = \Elementor\Plugin::instance()->experiments->is_feature_active('e_lazyload');
        if ($lazyload) {
            $msg = esc_html__('The Elementor Experiment Lazy Load is not currently compatible with all Dynamic.ooo features, in particular it causes problems with background images inside a loop.', 'dynamic-content-for-elementor');
            \DynamicContentForElementor\Plugin::instance()->admin_pages->notices->warning($msg, 'lazyload');
        }
    }
    public function warning_old_conditional()
    {
        if (isset($_POST['save-dce-feature'])) {
            return;
            // settings are being saved, we can't be sure of the extension status.
        }
        $features = \DynamicContentForElementor\Plugin::instance()->features->get_features_status();
        if ($features['ext_form_visibility'] === 'active') {
            $msg = __('It appears that the extension Conditional Fields (old version) for Elementor Pro Form is enabled. Notice that this is a legacy extension that is known to cause problems with form validation. We recommend disabling it if you don’t need it. You can do it from the ', 'dynamic-content-for-elementor');
            $url = admin_url('admin.php?page=dce-features&tab=legacy');
            $msg .= "<a href='{$url}'>" . __('Features Dashboard', 'dynamic-content-for-elementor') . '</a>.';
            $msg .= ' ' . __('You can use the new version instead: Conditional Fields for Elementor Pro Form.', 'dynamic-content-for-elementor');
            $msg .= " <a href='https://help.dynamic.ooo/en/articles/5576284-switch-conditional-fields-old-version-to-conditional-fields-v2-for-elementor-pro-form'>" . __('Read more...', 'dynamic-content-for-elementor') . '</a>';
            \DynamicContentForElementor\Plugin::instance()->admin_pages->notices->error($msg);
        }
    }
    public function warning_features_bloat()
    {
        if (isset($_POST['save-dce-feature'])) {
            return;
            // settings are being saved, we can't be sure of the feature status.
        }
        $features = \DynamicContentForElementor\Plugin::instance()->features->filter(['legacy' => \true], 'NOT');
        $active = \array_filter($features, function ($f) {
            return $f['status'] === 'active';
        });
        $ratio = \count($active) / \count($features);
        if ($ratio > 0.95) {
            $msg = __('Most features are currently active. This could slow down the Elementor Editor. It is recommended that you disable the features you don\'t need. This can be done on the ', 'dynamic-content-for-elementor');
            $url = admin_url('admin.php?page=dce-features');
            $msg .= "<a href='{$url}'>" . __('Features Page', 'dynamic-content-for-elementor') . '</a>.';
            $this->notices->warning($msg, 'features_bloat');
        }
    }
}
