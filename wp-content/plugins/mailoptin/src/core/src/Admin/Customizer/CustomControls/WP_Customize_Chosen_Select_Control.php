<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use WP_Customize_Control;

class WP_Customize_Chosen_Select_Control extends WP_Customize_Control
{
    public $type = 'mailoptin_chosen';

    /**
     * @var string search for posts by default.
     */
    public $search_type = 'post';

    public $is_multiple = true;

    public function enqueue()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_style('mailoptin-customizer-chosen', MAILOPTIN_ASSETS_URL . 'chosen/chosen.min.css');
        wp_enqueue_script('mailoptin-customizer-chosen', MAILOPTIN_ASSETS_URL . 'chosen/chosen.jquery.min.js', array('jquery'), MAILOPTIN_VERSION_NUMBER);
        wp_enqueue_script('mailoptin-customizer-chosen-control', MAILOPTIN_ASSETS_URL . 'js/customizer-controls/chosen.js', array('jquery', 'mailoptin-customizer-chosen'), MAILOPTIN_VERSION_NUMBER);
    }

    public function render_content()
    {
        ?>
        <label>
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <select class="mailoptin-chosen" data-search-type="<?php echo $this->search_type; ?>" <?php $this->link(); ?> <?php echo $this->is_multiple ? 'multiple': ''; ?>>
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