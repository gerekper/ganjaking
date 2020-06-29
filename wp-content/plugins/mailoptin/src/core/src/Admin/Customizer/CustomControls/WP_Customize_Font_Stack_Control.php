<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

/**
 * A class to create a dropdown for all google fonts
 */
class WP_Customize_Font_Stack_Control extends \WP_Customize_Control
{
    public function __construct($manager, $id, $args = array())
    {
        parent::__construct($manager, $id, $args);
    }

    /**
     * @return string
     */
    public function render_content()
    {
        $system_font_optgroup_label = __('System Fonts', 'mailoptin');
        $google_font_optgroup_label = __('Google Fonts', 'mailoptin');

        $fonts = [$system_font_optgroup_label => ControlsHelpers::get_system_font_stack()] + [$google_font_optgroup_label => WP_Customize_Google_Font_Control::get_fonts(300)];

        if ( ! empty($fonts)) {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <select <?php $this->link(); ?>>
                    <?php

                    printf('<option value="inherit" %s>%s</option>', selected($this->value(), 'inherit', false), __('Inherit from Theme', 'mailoptin'));

                    foreach ($fonts as $key => $font) {
                        if (is_array($font)) {
                            printf('<optgroup label="%s">', $key);
                            foreach ($font as $font2) {
                                $option_value = $font2;
                                if ($key == $google_font_optgroup_label) {
                                    $option_value = str_replace(' ', '+', $font2);
                                }
                                printf('<option value="%s" %s>%s</option>', $option_value, selected($this->value(), $option_value, false), $font2);
                            }

                            echo '</optgroup>';
                        } else {
                            printf('<option value="%s" %s>%s</option>', $font, selected($this->value(), $font, false), $font);
                        }
                    }
                    ?>
                </select>
                <?php if ( ! empty($this->description)) : ?>
                    <span class="description customize-control-description"><?php echo $this->description; ?></span>
                <?php endif; ?>
            </label>
            <?php
        }
    }
}