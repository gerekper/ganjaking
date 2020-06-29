<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Tinymce_Expanded_Editor extends WP_Customize_Control
{
    public $type = 'mailoptin_tinymce_expanded_editor';

    public function enqueue()
    {
        wp_enqueue_script('mo-customizer-tinymce-expanded-editor', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/tinymce-expanded-editor/control.js', array('jquery'), false, true);
        wp_enqueue_style('mo-customizer-tinymce-expanded-editor', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/tinymce-expanded-editor/control.css', array(), false);
        wp_localize_script('mo-customizer-tinymce-expanded-editor', 'moTinyMceExpandedEditor', [
            'button_open_text' => __('Open content editor', 'mailoptin'),
            'button_close_text' => __('Close content editor', 'mailoptin'),
        ]);
    }

    public function render_content()
    {
        ?>
        <label class="customize-control-title"><?php echo esc_html($this->label); ?></label>
        <a href="#" class="button button-hero mo-tinymce-expanded-editor-btn" data-control-id="<?= $this->id; ?>">
            <div class="mo-tinymce-expanded-control-wrapper">
                <span class="dashicons dashicons-edit"></span>
                <span><?php esc_html_e('Open content editor', 'mailoptin'); ?></span>
            </div>
        </a>
        <?php
    }
}
