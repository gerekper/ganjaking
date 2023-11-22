<?php

namespace ElementPack\Modules\SourceCode\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Source_Code extends Module_Base {

    public function get_name() {
        return 'bdt-source-code';
    }

    public function get_title() {
        return BDTEP . esc_html__('Source Code', 'bdthemes-element-pack');
    }

    public function get_icon() {
        return 'bdt-wi-source-code';
    }

    public function get_categories() {
        return ['element-pack'];
    }

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['prism', 'ep-styles'];
        } else {
            return ['prism'];
        }
    }

    public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['prism', 'clipboard', 'ep-scripts'];
        } else {
            return ['prism', 'clipboard', 'ep-source-code'];
        }
    }

    public function get_keywords() {
        return ['source', 'code', 'preformatted', 'pre'];
    }

    public function get_custom_help_url() {
        return 'https://youtu.be/vnqpD9aAmzg';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'source_code_section_content',
            [
                'label' => esc_html__('Source Code Content', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'theme',
            [
                'label'   => __('Select Style', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default'   => __('Default', 'bdthemes-element-pack'),
                    'dark'      => __('Dark', 'bdthemes-element-pack'),
                    'coy'       => __('Coy', 'bdthemes-element-pack'),
                    'funky'     => __('Funky', 'bdthemes-element-pack'),
                    'okaidia'   => __('Okaidia', 'bdthemes-element-pack'),
                    'solarized' => __('Solarized Light', 'bdthemes-element-pack'),
                    'tomorrow'  => __('Tomorrow Night', 'bdthemes-element-pack'),
                    'twilight'  => __('Twilight', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'source_code_copy_button',
            [
                'label'        => esc_html__('Copy Button', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'true',
            ]
        );

        $this->add_control(
            'source_code_language_selector',
            [
                'label'   => __('Select Language', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'language-markup',
                'options' => [
                    'language-markup'            => 'HTML markup',
                    'language-clike'             => 'C-like',
                    'language-css'               => 'CSS',
                    'language-sass'              => 'Sass',
                    'language-scss'              => 'Scss',
                    'language-less'              => 'Less',
                    'language-javascript'        => 'Javascript',
                    'language-php'               => 'PHP',
                    'language-phpdoc'            => 'PHP DOC',
                    'language-py'                => 'Python',
                    'language-c'                 => 'C ',
                    'language-cpp'               => 'C++ ',
                    'language-csharp'            => 'C# ',
                    'language-aspnet'            => 'Asp.net (C#) ',
                    'language-django'            => 'Django ',
                    'language-git'               => 'Git ',
                    'language-gml'               => 'GameMaker language ',
                    'language-go'                => 'Go ',
                    'language-java'              => 'Java ',
                    'language-javadoc'           => 'Java Doc',
                    'language-json'              => 'JSON',
                    'language-jsonp'             => 'JSONP',
                    'language-kotlin'            => 'Kotlin',
                    'language-markup-templating' => 'Markup templating',
                    'language-nginx'             => 'nginx',
                    'language-perl'              => 'Perl',
                    'language-jsx'               => 'React JSX',
                    'language-rb'                => 'Ruby',
                    'language-sql'               => 'SQL',
                    'language-swift'             => 'Swift',
                    'language-vbnet'             => 'VB.Net',
                    'language-vb'                => 'Visual Basic',
                ]
            ]
        );

        $this->add_control(
            'source_code_content',
            [
                'label'       => __('Source Code', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::CODE,
                'rows'        => 10,
                'default'     => "
                &lt;!DOCTYPE html&gt;
                &lt;html lang=\"en\"&gt;
                &lt;head&gt;
                &lt;meta charset=\"UTF-8\"&gt;
                &lt;title&gt;Document&lt;/title&gt;
                &lt;/head&gt;
                &lt;body&gt;
                &lt;h1&gt;Hello World!&lt;/h1&gt;
                &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit. &lt;/p&gt;
                &lt;/body&gt;
                &lt;/html&gt;",
                'placeholder' => __('Type your code here', 'bdthemes-element-pack'),
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );


        $this->add_control(
            'line_highlight',
            [
                'label'       => __('Line Highlight', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => '1, 2-5',
                'separator'   => 'before'
            ]
        );

        $this->add_control(
            'line_numbers',
            [
                'label'        => esc_html__('Line Numbers', 'bdthemes-element-pack'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Items', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'source_code_preview_height',
            [
                'label'     => __('Height', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 500,
                    'unit' => 'px',
                ],
                'range'     => [
                    'px' => [
                        'min' => 10,
                        'max' => 2000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-source-code pre' => 'max-height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'source_code_preview_border',
                'label'    => esc_html__('Border', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-source-code pre',
            ]
        );


        $this->add_control(
            'source_code_preview_border_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-source-code pre' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ',
                ],
            ]
        );

        $this->add_responsive_control(
            'source_code_preview_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-source-code pre' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'source_code_preview_margin',
            [
                'label'      => __('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-source-code pre' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'codes_typography',
                'selector' => '{{WRAPPER}} .bdt-source-code',
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('source-code', 'class', 'bdt-source-code');
        $theme = ($settings['theme']) ? $settings['theme'] : 'default';
        $this->add_render_attribute('source-code', 'class', 'prism-' . $theme);

        if ($settings['line_numbers'] == 'yes') {
            $this->add_render_attribute('source_code_pre', 'class', 'line-numbers');
        }

        $this->add_render_attribute('source_code_pre', 'class', $settings['source_code_language_selector']);


        if (!empty($settings['line_highlight'])) {
            $this->add_render_attribute('source_code_pre', 'data-line', $settings['line_highlight']);
        }

?>

        <div <?php $this->print_render_attribute_string('source-code'); ?>>

            <?php if ('yes' == $settings['source_code_copy_button']) : ?>
                <button class="bdt-copy-button"><?php
                                                echo esc_html__('Copy', 'bdthemes-element-pack') ?>
                </button>
            <?php endif; ?>

            <pre <?php echo $this->get_render_attribute_string('source_code_pre'); ?>>
            <code><?php echo esc_html($settings['source_code_content']); ?></code>
        </pre>

        </div>
<?php

    }
}
