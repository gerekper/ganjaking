<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder;


use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements\ElementInterface;

abstract class AbstractElement implements ElementInterface
{
    public function __construct()
    {
        add_filter('mo_email_content_elements', [$this, 'define_element']);
        add_action('customize_controls_print_footer_scripts', [$this, 'js_template']);
    }

    public function define_element($elements)
    {
        $elements[] = [
            'id'                 => $this->id(),
            'title'              => $this->title(),
            'icon'               => $this->icon(),
            'description'        => $this->description(),
            'tabs'               => $this->tabs(),
            'settings'           => $this->settings(),
            'is_premium_element' => $this->is_premium_element()
        ];

        return $elements;
    }

    public function is_premium_element()
    {
        return false;
    }

    public function element_block_settings()
    {
        return [
            'block_background_color' => [
                'label' => esc_html__('Background Color', 'mailoptin'),
                'type'  => 'color_picker',
                'tab'   => 'tab-block-settings'
            ],
            'block_padding'          => [
                'label' => esc_html__('Padding', 'mailoptin'),
                'type'  => 'dimension',
                'tab'   => 'tab-block-settings'
            ]
        ];
    }

    public function js_template()
    {
        printf('<script type="text/html" id="tmpl-mo-email-content-element-%s">', $this->id()); ?>
        <div id="mo-email-content-settings-area" data-element-id="{{data.element_id}}">
            <div class="mo-email-content-widget-top mo-email-content-part-widget-top">
                <div class="mo-email-content-widget-title"><h3><?= $this->title() ?></h3></div>
            </div>
            <div class="mo-email-content-widget-content">
                <div class="mo-email-content-modal-motabs">
                    <ul class="motabs">
                        <?php
                        $tabs = $this->tabs();
                        if (is_array($tabs) && ! empty($tabs)) {
                            foreach ($tabs as $key => $label) { ?>
                                <li data-tab-id="<?= $key ?>" class="motab is-active">
                                    <h3><?= $label ?></h3>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
                <div class="mo-email-content-widget-form">
                    <?php foreach ($this->settings() as $name => $setting) : ?>
                        <div class="mo-email-content-blocks customize-control <?= ! empty($setting['tab']) ? $setting['tab'] : ''; ?>">
                            <?php if ( ! empty($setting['label'])) : ?>
                                <label for="<?= $name ?>" class="customize-control-title"><?= esc_html($setting['label']) ?></label>
                            <?php endif;
                            call_user_func(
                                ['MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements\SettingsFields', $setting['type']],
                                $name, $setting, $this->id()
                            );
                            if ( ! empty($setting['description'])) : ?>
                                <span class="description customize-control-description"><?= esc_html($setting['description']) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="mo-email-content-footer-wrap">
                        <a href="#" class="mo-email-content-footer-link mo-delete"><?php esc_html_e('Delete', 'mailoptin'); ?></a> |
                        <a href="#" class="mo-email-content-footer-link mo-duplicate"><?php esc_html_e('Duplicate', 'mailoptin'); ?></a>
                        <button class="button button-large button-primary mo-apply"><?php esc_html_e('Apply', 'mailoptin'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php echo '</script>';
    }

    public static function get_instance()
    {
        static $instance = false;

        $class = get_called_class();

        if ( ! $instance) {
            $instance = new $class();
        }

        return $instance;
    }
}