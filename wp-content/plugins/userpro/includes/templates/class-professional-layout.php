<?php

if (!defined('ABSPATH')) {
    exit;
}

class UP_ProfessionalLayout
{
    /**
     * Professional layout instance
     * @var null
     */
    protected static $instance = null;

    /**
     * @since 4.9.31
     * @return UP_ProfessionalLayout|null
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * UP_ProfessionalLayout constructor.
     */
    public function __construct()
    {
        $this->init();

        add_action('init', [$this, 'remove_comment_support'], 100);
        add_filter('comments_open', [$this, 'up_disable_comments'], 20, 2);

        add_filter('up_navigation_tabs', [$this, 'navigation']);

    }

    public function navigation( $nav ){

        $nav['info'] = [
            'icon' => 'up-fas up-fa-clipboard-list',
            'title' => 'Info',
            'callback' => ['UP_User', 'getProfileData'],
            'type' => 'object',
            'id' => 'profile-info',
        ];

        return $nav;
    }

    public function up_disable_comments() {
        return false;
    }

    /**
     * Professional layout init
     * @since 4.9.31
     */
    public static function init()
    {
        wp_register_style('userpro_professional_layout_styles', userpro_url . 'assets/css/layout4.css');
        wp_register_script('userpro_professional_layout_scripts', userpro_url . 'profile-layouts/layout4/js/professional.js', '', '', true);

        wp_enqueue_style('userpro_professional_layout_styles');
        wp_enqueue_script( 'userpro_professional_layout_scripts' );

    }
}