<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Controls_Tab_Toggle extends WP_Customize_Control
{
    public $type = 'mailoptin_tab_toggle';

    public function enqueue()
    {
        wp_enqueue_script('mo-customizer-tab-toggle-control', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/tab-toggle/control.js', array('jquery'), false, true);
    }

    public function render_content()
    {
        $tabs = [
            'general' => ['title' => __('General', 'mailoptin'), 'icon' => 'dashicons-admin-settings'],
            'style' => ['title' => __('Style', 'mailoptin'), 'icon' => 'dashicons-admin-customizer'],
            'advance' => ['title' => __('Advance', 'mailoptin'), 'icon' => 'dashicons-admin-generic'],
        ];
        echo '<div class="mailoptin-toggle-control-tab">';
        foreach ($tabs as $key => $value) {
            $title = $value['title'];
            $name = '_customize-radio-' . $this->id;
            $dashicon = sprintf('<span class="dashicons %s"></span>', $value['icon']);
            ?>
            <input
                    class="mailoptin-toggle-control-radio"
                    id="<?= $this->id . $key; ?>"
                    type="radio"
                    name="<?= $name; ?>"
                    style="display: none"
                    value="<?php echo $key ?>"
                <?php checked('general', $key); ?>
            />
            <div class="mo-toggle-tab-wrapper mo-<?= $key; ?>">
                <label for="<?= $this->id . $key; ?>" class="mo-single-toggle-tab">
                    <?= $dashicon; ?>
                    <?= $title; ?>
                </label>
            </div>
            <?php
        }
        echo '</div>';
    }
}
