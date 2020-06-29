<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

class WP_Customize_Tinymce_Control extends \WP_Customize_Control
{
    /** @var string HTML ID of the tinymce editor */
    protected $editor_id;

    /** @var string height of the tinymce editor */
    protected $editor_height = 250;

    protected $media_buttons = true;

    protected $textarea_rows = 10;

    protected $quicktags = false;

    public function __construct($manager, $id, $args = array())
    {
        $this->editor_id = isset($args['editor_id']) ? $args['editor_id'] : $this->editor_id;
        $this->editor_height = isset($args['textarea_rows']) ? '' : (isset($args['editor_height']) ? $args['editor_height'] : $this->editor_height);
        $this->media_buttons = isset($args['media_buttons']) ? $args['media_buttons'] : $this->media_buttons;
        $this->quicktags = isset($args['quicktags']) ? $args['quicktags'] : $this->quicktags;

        $this->textarea_rows = isset($args['textarea_rows']) ? $args['textarea_rows'] : $this->textarea_rows;
        parent::__construct($manager, $id, $args);
    }

    public function enqueue()
    {
        wp_enqueue_script('cs-tinymce-control', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/tinymce-control.js', ['jquery'], false, true);
    }

    /**
     * Render the content on the theme customizer page
     */
    public function render_content()
    {
        static $i = 1;
        ?>
        <label> <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php
            $settings = apply_filters('mailoptin_tinymce_customizer_control', array(
                'textarea_name' => $this->id,
                'teeny' => true,
                'wpautop' => false,
                'editor_height' => $this->editor_height,
                'media_buttons' => $this->media_buttons,
                'textarea_rows' => $this->textarea_rows,
                'quicktags' => $this->quicktags,
            ),
                $this->editor_id
            );
            $this->filter_editor_setting_link();
            wp_editor($this->value(), $this->editor_id, $settings);

            if ($i == apply_filters('mailoptin_tinymce_customizer_control_count', 3)) {
                do_action('admin_print_footer_scripts');
            }
            $i++;
            ?>
        </label>
        <?php
    }

    private function filter_editor_setting_link()
    {
        add_filter('the_editor',
            function ($output) {
                return preg_replace('/<textarea/', '<textarea ' . $this->get_link(), $output, 1);
            }
        );
    }
}