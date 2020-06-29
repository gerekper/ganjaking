<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Ace_Editor_Control extends WP_Customize_Control
{
    public $type = 'mailoptin_ace_editor';

    public $language = 'javascript';

    public $theme = 'monokai';

    public $editor_id = 'mo-ace-js';

    public function enqueue()
    {
        wp_enqueue_script(
            'mailoptin-ace-js',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/ace-editor/ace.js',
            array('jquery'),
            false,
            true
        );

        wp_enqueue_script(
            'mailoptin-ace-init',
            MAILOPTIN_ASSETS_URL . 'js/customizer-controls/ace-init.js',
            array('jquery', 'mailoptin-ace-js', 'customize-base'),
            false,
            true
        );
    }

    public function render_content()
    {
        ?>
        <label>
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>

            <?php if (!empty($this->description)) : ?>
                <span class="description customize-control-description"><?php echo $this->description; ?></span>
            <?php endif; ?>

            <div id="<?php echo $this->editor_id; ?>" data-block-type="ace" data-ace-lang="<?php echo $this->language; ?>" data-ace-theme="<?php echo $this->theme; ?>" style="position:relative;width:100%;height:300px;"></div>

            <textarea id="<?php echo $this->editor_id; ?>-textarea" <?php $this->link(); ?> style="display:none;">
                <?php $this->value(); ?>
            </textarea>

        </label>
        <?php
    }
}