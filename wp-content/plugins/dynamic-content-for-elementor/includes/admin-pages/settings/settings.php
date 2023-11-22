<?php

namespace DynamicContentForElementor\AdminPages\Settings;

use DynamicContentForElementor\Tokens;
class Settings extends \DynamicContentForElementor\AdminPages\Settings\SettingsPage
{
    const PAGE_ID = 'dce-settings';
    // fix old filter whitelist bug:
    public function before_register()
    {
        if (\is_array(get_option('dce_tokens_filters_whitelist'))) {
            Tokens::fix_filters_whitelist();
        }
    }
    /**
     * Get settings page title.
     *
     * Retrieve the title for the settings page.
     *
     * @return string
     */
    protected function get_page_title()
    {
        return __('Settings', 'dynamic-content-for-elementor');
    }
    /**
     * @param string $id
     * @return string
     */
    protected function tokens_filters_whitelist($id)
    {
        $value = esc_textarea(get_option('dce_' . $id, ''));
        $html = "<textarea placeholder='my_function' cols='30' rows='5' id='dce_{$id}' name='dce_{$id}'>{$value}</textarea>";
        $html .= '<p class="description">' . __('One filter per line', 'dynamic-content-for-elementor') . '</p>';
        return $html;
    }
    /**
     * @return void
     */
    protected function render_tokens_intro()
    {
        echo '<h2>' . __('Tokens', 'dynamic-content-for-elementor') . '</h2>';
        echo '<p>' . __('A Token is a specially formatted chunk of text that serves as a placeholder for a dynamically generated value.', 'dynamic-content-for-elementor') . ' ' . '<a target="_blank" href="https://dnmc.ooo/tokensdoc">' . __('More info...', 'dynamic-content-for-elementor') . '</a><br />';
        echo __('You can manipulate the results with filters, which are PHP and WordPress functions.', 'dynamic-content-for-elementor') . '</p>';
    }
    /**
     * @return array<string,mixed>
     */
    public function create_tabs()
    {
        $tabs = ['tokens' => ['label' => esc_html__('Tokens', 'dynamic-content-for-elementor'), 'sections' => ['tokens' => ['callback' => [$this, 'render_tokens_intro'], 'fields' => ['tokens_status' => ['label' => esc_html__('Status', 'dynamic-content-for-elementor'), 'field_args' => ['type' => 'select', 'std' => 'enable', 'options' => ['enable' => esc_html__('Enable', 'dynamic-content-for-elementor'), 'disable' => esc_html__('Disable', 'dynamic-content-for-elementor')]]], 'active_tokens' => ['label' => esc_html__('Active Tokens', 'dynamic-content-for-elementor'), 'field_args' => ['type' => 'checkbox_list', 'std' => \array_keys(Tokens::get_tokens_list()), 'options' => Tokens::get_tokens_options()]], 'tokens_filters_whitelist' => ['label' => esc_html__('Filters Whitelist', 'dynamic-content-for-elementor'), 'field_args' => ['type' => 'raw_html', 'html' => $this->tokens_filters_whitelist('tokens_filters_whitelist')]]]]]]];
        return $tabs;
    }
}
