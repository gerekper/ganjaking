<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\Plugin;
use ElementorPro\Plugin as ElementorPro;
use ElementorPro\Modules\QueryControl\Module as QueryModule;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class FavoritesAction extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    /**
     * Get Scripts Depends
     *
     * @return array<string>
     */
    public function get_script_depends()
    {
        return [];
    }
    /**
     * Get Style Depends
     *
     * @return array<string>
     */
    public function get_style_depends()
    {
        return [];
    }
    /**
     * Has Action
     *
     * @var boolean
     */
    public $has_action = \true;
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_favorites';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return '<span class="color-dce icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Favorites', 'dynamic-content-for-elementor');
    }
    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_dce_form_favorites', ['label' => $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="elementor-panel-alert elementor-panel-alert-warning">' . __('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor') . '</div>']);
            $widget->end_controls_section();
            return;
        }
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('dce_form_favorite_action', ['label' => __('Action', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['add' => __('Add', 'dynamic-content-for-elementor'), 'remove' => __('Remove', 'dynamic-content-for-elementor')], 'default' => 'add']);
        $repeater->add_control('dce_form_favorite_scope', ['label' => __('Scope', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['cookie' => ['title' => __('Cookie', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-cookie'], 'user' => ['title' => __('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user']], 'toggle' => \false, 'default' => 'user']);
        $repeater->add_control('dce_form_favorite_key', ['label' => __('Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'my_favorites', 'description' => __('The unique name that identifies the favorites in user meta or cookies', 'dynamic-content-for-elementor')]);
        $repeater->add_control('dce_form_favorite_post_id', ['label' => __('Post ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'description' => __('In favorites you need to save a post ID', 'dynamic-content-for-elementor')]);
        $repeater->add_control('dce_form_favorite_cookie_expiration', ['label' => __('Cookie expiration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 30, 'min' => 0, 'description' => __('Value is in days. Set 0 or empty for session duration', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_favorite_scope' => 'cookie']]);
        $widget->add_control('dce_form_favorites', ['label' => __('Favorites', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'title_field' => '{{{ dce_form_favorite_key }}} ({{{ dce_form_favorite_action }}})', 'fields' => $repeater->get_controls()]);
        $widget->end_controls_section();
    }
    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     * @return void
     */
    public function run($record, $ajax_handler)
    {
        $fields = Helper::get_form_data($record);
        $settings = $record->get('form_settings');
        $settings = Helper::get_dynamic_value($settings, $fields);
        $favorites = $settings['dce_form_favorites'];
        if (empty($favorites)) {
            return;
        }
        foreach ($favorites as $favorite) {
            if (empty($favorite['dce_form_favorite_key']) || empty($favorite['dce_form_favorite_post_id'])) {
                continue;
            }
            $key = $favorite['dce_form_favorite_key'];
            $id = $favorite['dce_form_favorite_post_id'];
            $current_favorites = $this->get_current_favorites($key, $favorite['dce_form_favorite_scope']);
            if ('add' === $favorite['dce_form_favorite_action']) {
                // Add Action
                if ('cookie' === $favorite['dce_form_favorite_scope']) {
                    $cookie_expiration = $favorite['dce_form_favorite_cookie_expiration'] ?? 0;
                    $this->add_action_cookie($key, $id, $current_favorites, \time() + $cookie_expiration * 86400);
                } elseif ('user' === $favorite['dce_form_favorite_scope']) {
                    $this->add_action_user($key, $id, $current_favorites);
                }
            } else {
                // Remove Action
                if ('cookie' === $favorite['dce_form_favorite_scope']) {
                    $cookie_expiration = $favorite['dce_form_favorite_cookie_expiration'] ?? 0;
                    $this->remove_action_cookie($key, $id, $current_favorites, \time() + $cookie_expiration * 86400);
                } elseif ('user' === $favorite['dce_form_favorite_scope']) {
                    $this->remove_action_user($key, $id, $current_favorites);
                }
            }
        }
    }
    /**
     * Add action for cookie scope
     *
     * @param string $key
     * @param int $id
     * @param array<mixed> $current_favorites
     * @param int $expiration
     * @return void
     */
    protected function add_action_cookie(string $key, int $id, array $current_favorites, int $expiration)
    {
        if (empty($current_favorites)) {
            $current_favorites = [$id];
        } else {
            $current_favorites[] = $id;
        }
        $this->save_cookie($key, $id, $current_favorites, $expiration);
    }
    /**
     * Remove action for cookie scope
     *
     * @param string $key
     * @param int $id
     * @param array<int,mixed> $current_favorites
     * @param int $expiration
     * @return void
     */
    protected function remove_action_cookie(string $key, int $id, array $current_favorites, int $expiration)
    {
        if (empty($current_favorites)) {
            return;
        }
        $id_position = \array_search($id, $current_favorites, \true);
        if (\false === $id_position) {
            return;
        }
        $current_favorites = $this->remove_from_favorite($id_position, $current_favorites);
        $this->save_cookie($key, $id, $current_favorites, $expiration);
    }
    /**
     * Add action for user scope
     *
     * @param string $key
     * @param int $id
     * @param array<mixed> $current_favorites
     * @return void
     */
    protected function add_action_user(string $key, int $id, array $current_favorites)
    {
        $current_user_id = get_current_user_id();
        if (!$current_user_id) {
            // User not logged
            return;
        }
        $current_favorites = $this->add_to_favorite($id, $current_favorites);
        update_user_meta($current_user_id, $key, $current_favorites);
    }
    /**
     * Remove action for user scope
     *
     * @param string $key
     * @param int $id
     * @param array<int,mixed> $current_favorites
     * @return void
     */
    protected function remove_action_user(string $key, int $id, array $current_favorites)
    {
        $current_user_id = get_current_user_id();
        if (!$current_user_id) {
            // User not logged
            return;
        }
        if (empty($current_favorites)) {
            return;
        }
        $id_position = \array_search($id, $current_favorites, \true);
        if (\false === $id_position) {
            return;
        }
        $current_favorites = $this->remove_from_favorite($id_position, $current_favorites);
        update_user_meta($current_user_id, $key, $current_favorites);
    }
    /**
     * Retrieve the favorite value before to make operations
     *
     * @param string $key
     * @param string $scope
     * @return array<mixed><mixed>
     */
    protected function get_current_favorites(string $key, string $scope)
    {
        $current_favorites = [];
        switch ($scope) {
            case 'user':
                $current_user_id = get_current_user_id();
                $current_favorites = get_user_meta($current_user_id, $key, \true);
                if (!\is_array($current_favorites)) {
                    $current_favorites = [];
                }
                break;
            case 'cookie':
                if (isset($_COOKIE[$key])) {
                    $current_favorites = Helper::str_to_array(',', sanitize_text_field($_COOKIE[$key]));
                }
                break;
        }
        if (Helper::is_wpml_active()) {
            return Helper::wpml_translate_object_id($current_favorites);
        }
        return $current_favorites;
    }
    /**
     * Add a given post ID to favorite_value
     *
     * @param int $id
     * @param array<mixed> $current_favorites
     * @return array<mixed>
     */
    protected function add_to_favorite(int $id, array $current_favorites)
    {
        if (empty($current_favorites)) {
            $current_favorites = [$id];
        } else {
            $current_favorites[] = $id;
        }
        return $current_favorites;
    }
    /**
     * Remove a given post ID from favorite_value
     *
     * @param int $position
     * @param array<mixed> $current_favorites
     * @return array<mixed>
     */
    protected function remove_from_favorite(int $position, array $current_favorites)
    {
        unset($current_favorites[$position]);
        return $current_favorites;
    }
    /**
     * Save the favorite value in the cookie
     *
     * @param string $key
     * @param int $id
     * @param array<mixed> $current_favorites
     * @param int $expiration
     * @return void
     */
    protected function save_cookie(string $key, int $id, array $current_favorites, int $expiration)
    {
        $http_host = 'localhost' === $_SERVER['HTTP_HOST'] ? '' : sanitize_text_field($_SERVER['HTTP_HOST']);
        $current_favorites = \implode(',', $current_favorites);
        @\setcookie($key, $current_favorites, $expiration, '/', $http_host);
        $cookies_counter = get_option('dce_favorite_cookies', []);
        if (isset($cookies_counter[$key][$id])) {
            $cookies_counter[$key][$id]++;
        } else {
            $cookies_counter[$key][$id] = 1;
        }
        update_option('dce_favorite_cookies', $cookies_counter);
    }
    public function on_export($element)
    {
        return $element;
    }
}
