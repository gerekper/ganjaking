<?php

/**
 * Class Licenses
 *
 * Отображает страницу, где расположена плитка с лицензиями как плагина, так и его компонентов
 */
class Licenses extends WIS_Page
{

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

        $this->id            = 'licenses';
        $this->menu_title    = '<span style="color:#f18500">' . __( 'Licenses', 'instagram-slider-widget' ) . '</span>';
        $this->page_title    = __( 'Licenses', 'instagram-slider-widget' );
        $this->template_name = "licenses";
        $this->menu_target   = "widgets-" . $plugin->getPluginName();
        $this->capabilitiy   = "manage_options";
    }

    public function indexAction()
    {

        $licenses = [
            [
                'name' => 'instagram_slider_widget',
                'title' => __('Social Slider Widget', 'instagram-slider-widget'),
                'license_url' => 'license-wisw',
                'icon' => 'https://ps.w.org/instagram-slider-widget/assets/icon-256x256.png',
                'description' => __('DISPLAY INSTAGRAM FEEDS IN WIDGETS, POSTS, PAGES, OR ANYWHERE ELSE USING SHORTCODES.', 'instagram-slider-widget'),
            ],
        ];

        echo $this->render('licenses', ['licenses' => $licenses]);
    }

}
