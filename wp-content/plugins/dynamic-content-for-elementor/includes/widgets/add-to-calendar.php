<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AddToCalendar extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-add-to-calendar'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_button', ['label' => __('Button', 'dynamic-content-for-elementor')]);
        $this->add_control('button_type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), 'info' => __('Info', 'dynamic-content-for-elementor'), 'success' => __('Success', 'dynamic-content-for-elementor'), 'warning' => __('Warning', 'dynamic-content-for-elementor'), 'danger' => __('Danger', 'dynamic-content-for-elementor')], 'prefix_class' => 'elementor-button-']);
        $this->add_control('text', ['label' => __('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'default' => __('Add to Calendar', 'dynamic-content-for-elementor'), 'placeholder' => __('Add to Calendar', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'prefix_class' => 'elementor%s-align-', 'default' => '']);
        $this->add_control('size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'style_transfer' => \true]);
        $this->add_control('selected_icon', ['label' => __('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'skin' => 'inline', 'label_block' => \false]);
        $this->add_control('icon_align', ['label' => __('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => __('Before', 'dynamic-content-for-elementor'), 'right' => __('After', 'dynamic-content-for-elementor')], 'condition' => ['selected_icon[value]!' => '']]);
        $this->add_control('icon_indent', ['label' => __('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('icon_size', ['label' => __('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 10, 'max' => 60]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_control('view', ['label' => __('View', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => 'traditional']);
        $this->add_control('button_css_id', ['label' => __('Button ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'default' => '', 'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id', 'dynamic-content-for-elementor'), 'label_block' => \false, 'description' => __('Please make sure the ID is unique and not used elsewhere on the page where this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'dynamic-content-for-elementor'), 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->start_controls_tabs('tabs_button_style');
        $this->start_controls_tab('tab_button_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('button_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};', '{{WRAPPER}} a.elementor-button:hover svg, {{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} a.elementor-button:focus svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};']]);
        $this->add_control('button_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};']]);
        $this->add_control('hover_animation', ['label' => __('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border', 'selector' => '{{WRAPPER}} .elementor-button', 'separator' => 'before']);
        $this->add_control('border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'button_box_shadow', 'selector' => '{{WRAPPER}} .elementor-button']);
        $this->add_responsive_control('text_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_calendar', ['label' => __('Calendar', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_calendar_format', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['gcalendar' => __('Google Calendar', 'dynamic-content-for-elementor'), 'ics' => __('ICS (for iCal and Outlook)', 'dynamic-content-for-elementor'), 'web_outlook' => __('Outlook.com Calendar', 'dynamic-content-for-elementor'), 'yahoo' => __('Yahoo Calendar', 'dynamic-content-for-elementor')], 'default' => 'gcalendar', 'toggle' => \false]);
        $this->add_control('filename', ['label' => __('Filename', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'condition' => ['dce_calendar_format' => 'ics']]);
        $this->add_control('dce_calendar_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $this->add_control('dce_calendar_datetime_format', ['label' => __('Date Field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['picker' => ['title' => __('DateTime Picker', 'dynamic-content-for-elementor'), 'icon' => 'eicon-date'], 'string' => ['title' => __('Dynamic String', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-i-cursor']], 'default' => 'string', 'toggle' => \false]);
        $this->add_control('dce_calendar_datetime_start', ['label' => __('DateTime Start', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DATE_TIME, 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'picker']]);
        $this->add_control('dce_calendar_datetime_end', ['label' => __('DateTime End', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DATE_TIME, 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'picker']]);
        $this->add_control('dce_calendar_datetime_string_format', ['label' => __('Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'Y-m-d H:i', 'placeholder' => 'Y-m-d H:i', 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'string']]);
        // This is required because unfortunately JetEngine can store dates like this.
        $this->add_control('dce_calendar_epoch_as_local', ['label' => __('Local Unix Epoch', 'dynamic-content-for-elementor'), 'description' => __('This Unix Epoch does not represent a specific point in time but a local time.'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_calendar_datetime_string_format' => 'U']]);
        $this->add_control('dce_calendar_datetime_start_string', ['label' => __('DateTime Start', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'string']]);
        $this->add_control('dce_calendar_datetime_end_string', ['label' => __('DateTime End', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'string']]);
        $this->add_control('dce_calendar_description', ['label' => __('Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG]);
        $this->add_control('dce_calendar_location', ['label' => __('Address', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true]);
        $this->end_controls_section();
    }
    /**
     * Create a new DateTime in the local tz using $date only for its local
     * representation.
     */
    private function create_datetime_from_local_representation($date)
    {
        $f = 'Y-m-d\\TH:i:s';
        return \DateTime::createFromFormat($f, $date->format($f), new \DateTimeZone(wp_timezone_string()));
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        // Dates
        $date_from = $settings['dce_calendar_datetime_format'] != 'string' ? $settings['dce_calendar_datetime_start'] : $settings['dce_calendar_datetime_start_string'];
        $date_to = $settings['dce_calendar_datetime_format'] != 'string' ? $settings['dce_calendar_datetime_end'] : $settings['dce_calendar_datetime_end_string'];
        // Don't render if the start date is empty
        if (empty($date_from)) {
            Helper::notice('', __('Please enter the start date', 'dynamic-content-for-elementor'));
            return;
        }
        // Date Format
        $date_format = $settings['dce_calendar_datetime_string_format'] ?? 'Y-m-d H:i';
        // From
        $from = \DateTime::createFromFormat($date_format, $date_from, new \DateTimeZone(wp_timezone_string()));
        if ($settings['dce_calendar_epoch_as_local'] === 'yes') {
            $from = $this->create_datetime_from_local_representation($from);
        }
        if (!$from) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
				<div class="elementor-alert elementor-alert-danger">
					<h5 class="elementor-alert-title"><?php 
                _e('Warning', 'dynamic-content-for-elementor');
                ?></h5>
					<?php 
                _e('DateTime Format:', 'dynamic-content-for-elementor');
                ?> <b><?php 
                echo $date_format;
                ?></b><br>
					<?php 
                _e('Start date is wrong:', 'dynamic-content-for-elementor');
                ?> <b><?php 
                echo $date_from;
                ?></b><br>
				</div>
				<?php 
            }
            return;
        }
        // To - If the end date is empty set it on +1 day from start date
        if (empty($date_to)) {
            $to = \DateTime::createFromFormat($date_format, $date_from, new \DateTimeZone(wp_timezone_string()));
            $to = $to->modify('+ 1 day');
        } else {
            $to = \DateTime::createFromFormat($date_format, $date_to, new \DateTimeZone(wp_timezone_string()));
            if ($settings['dce_calendar_epoch_as_local'] === 'yes') {
                $to = $this->create_datetime_from_local_representation($to);
            }
        }
        if (!$to) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
				<div class="elementor-alert elementor-alert-danger">
					<h5 class="elementor-alert-title"><?php 
                _e('Warning', 'dynamic-content-for-elementor');
                ?></h5>
					<?php 
                _e('DateTime Format:', 'dynamic-content-for-elementor');
                ?> <b><?php 
                echo $date_format;
                ?></b><br>
					<?php 
                _e('End date is wrong:', 'dynamic-content-for-elementor');
                ?> <b><?php 
                echo $date_to;
                ?></b>
				</div>
				<?php 
            }
            return;
        }
        if ($from > $to) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
				<div class="elementor-alert elementor-alert-danger">
					<h5 class="elementor-alert-title"><?php 
                _e('Warning', 'dynamic-content-for-elementor');
                ?></h5>
					<?php 
                echo \sprintf(__('TO time (%1$s) must be greater than FROM time (%2$s)', 'dynamic-content-for-elementor'), $date_to, $date_from);
                ?>
				</div>
				<?php 
            }
            return;
        }
        $title = $settings['dce_calendar_title'] ?? __('Event', 'dynamic-content-for-elementor');
        $description = $settings['dce_calendar_description'] ?? '';
        $address = $settings['dce_calendar_location'] ?? '';
        $link = \DynamicOOOS\Spatie\CalendarLinks\Link::create($title, $from, $to)->description($description)->address($address);
        switch ($settings['dce_calendar_format']) {
            case 'gcalendar':
                $link = $link->google();
                break;
            case 'ics':
                if (current_user_can('administrator') && \strpos(wp_timezone_string(), ':')) {
                    echo '<div style="color: red">';
                    echo esc_html__('ICS file may be invalid. In the WordPress settings the Timezone is set as an offset, for example UTC+1. This isn’t supported. To fix this please set it with a city, for example Rome.', 'dynamic-content-for-elementor');
                    echo '</div>';
                }
                $link = $link->ics();
                if (!empty($settings['filename'])) {
                    $this->add_render_attribute('button', 'download', sanitize_file_name($settings['filename']));
                }
                break;
            case 'web_outlook':
                $link = $link->webOutlook();
                break;
            case 'yahoo':
                $link = $link->yahoo();
                break;
            default:
                $link = $link->google();
        }
        $this->add_render_attribute('wrapper', 'class', 'elementor-button-wrapper');
        $this->add_render_attribute('button', 'href', $link);
        $this->add_render_attribute('button', 'class', 'elementor-button-link');
        $this->add_render_attribute('button', 'target', '_blank');
        $this->add_render_attribute('button', 'rel', 'nofollow');
        $this->add_render_attribute('button', 'class', 'elementor-button');
        $this->add_render_attribute('button', 'role', 'button');
        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button', 'id', sanitize_text_field($settings['button_css_id']));
        }
        if (!empty($settings['size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . sanitize_text_field($settings['size']));
        }
        if ($settings['hover_animation']) {
            $this->add_render_attribute('button', 'class', 'elementor-animation-' . sanitize_text_field($settings['hover_animation']));
        }
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('wrapper');
        ?>>
			<a <?php 
        echo $this->get_render_attribute_string('button');
        ?>>
			<?php 
        $this->render_text();
        ?>
			</a>
		</div>
		<?php 
    }
    protected function render_text()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute(['content-wrapper' => ['class' => ['elementor-button-content-wrapper', 'dce-flexbox']], 'icon-align' => ['class' => ['elementor-button-icon', 'elementor-align-icon-' . $settings['icon_align']]], 'text' => ['class' => 'elementor-button-text']]);
        $this->add_inline_editing_attributes('text', 'none');
        ?>
		<span <?php 
        echo $this->get_render_attribute_string('content-wrapper');
        ?>>
			<?php 
        if (!empty($settings['selected_icon']['value'])) {
            ?>
				<span <?php 
            echo $this->get_render_attribute_string('icon-align');
            ?>>
					<?php 
            Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
            ?>
				</span>
			<?php 
        }
        ?>
			<span <?php 
        echo $this->get_render_attribute_string('text');
        ?>><?php 
        echo wp_kses_post($settings['text']);
        ?></span>
		</span>
		<?php 
    }
}
