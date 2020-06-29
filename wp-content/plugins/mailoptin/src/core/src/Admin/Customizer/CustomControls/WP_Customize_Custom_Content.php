<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Custom_Content extends WP_Customize_Control
{
    public $type = 'mailoptin_custom_content';

    // Whitelist content parameter
    public $content = '';

    public $no_wrapper_div = false;

    public $block_class = 'mo-custom-content-block';

    /**
     * Render the control's content.
     *
     * Allows the content to be overridden without having to rewrite the wrapper.
     *
     * @since   1.0.0
     * @return  void
     */
    public function render_content()
    {
        if (!isset($this->content)) return;

        if (isset($this->label)) {
            echo '<span class="customize-control-title">' . $this->label . '</span>';
        }

        if (!$this->no_wrapper_div) {
            echo "<div class=\"customize-{$this->block_class}\">" . $this->content . '</div>';
        } else {
            echo $this->content;
        }

        if (isset($this->description)) {
            echo '<span class="description customize-control-description">' . $this->description . '</span>';
        }
    }
}