<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Single_Select_With_Group_Control extends WP_Customize_Control
{
    public $type = 'mailoptin_single_select_group';

    public function render_content()
    {
        ?>
        <label>
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <select class="mailoptin-single_select_group" <?php $this->link(); ?>>
                <?php
                foreach ($this->choices as $key => $value) {
                    if (is_array($value)) {
                        echo "<optgroup label='$key'>";
                        foreach ($value as $key2 => $value2) {
                            echo '<option value="' . esc_attr($key2) . '"' . $this->_selected($key2) . '>' . $value2 . '</option>';
                        }
                        echo "</optgroup>";
                    } else {
                        echo '<option value="' . esc_attr($key) . '"' . $this->_selected($key) . '>' . $value . '</option>';
                    }
                }
                ?>
            </select>
        </label>

        <?php if (!empty($this->description)) : ?>
        <span class="description customize-control-description"><?php echo $this->description; ?></span>
    <?php endif;
    }

    protected function _selected($key)
    {
        return in_array($key, (array)$this->value()) ? 'selected=selected' : null;
    }
}