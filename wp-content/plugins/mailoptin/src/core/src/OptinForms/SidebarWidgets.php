<?php

namespace MailOptin\Core\OptinForms;

use MailOptin\Core\Admin\Customizer\OptinForm\OptinFormFactory;
use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class SidebarWidgets extends \WP_Widget
{
    use PageTargetingRuleTrait, UserTargetingRuleTrait, QueryStringTargetingRuleTrait;

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'mo_optin_widgets',
            esc_html__('MailOptin', 'mailoptin'),
            array('description' => esc_html__('Display a MailOptin sidebar/widget optin form in a widget area.', 'mailoptin')) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        $sidebar_optin_id = isset($instance['sidebar_optin_id']) ? $instance['sidebar_optin_id'] : false;
        $title            = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : false;

        if (isset($_GET['mohide']) && $_GET['mohide'] == 'true') return '';

        if ( ! OCR::is_activated($sidebar_optin_id)) return '';

        $sidebar_optin_id = OCR::choose_split_test_variant($sidebar_optin_id);

        // is_customize_preview() takes care of showing the optin regardless of any display rules in customizer
        if (is_customize_preview() || ! OptinCampaignsRepository::is_test_mode($sidebar_optin_id)) {

            if ( ! OCR::global_cookie_check_result()) return '';

            $sidebar_optin_uuid = OptinCampaignsRepository::get_optin_campaign_uuid($sidebar_optin_id);

            // we are adding this check to prevent sidebar <aside tag> from being included in markup when state after conversion
            // is optin form hidden.
            if (OptinCampaignsRepository::get_merged_customizer_value($sidebar_optin_id, 'state_after_conversion') == 'optin_form_hidden' &&
                OptinCampaignsRepository::user_has_successful_optin($sidebar_optin_uuid)
            ) {
                // if state after conversion is set to 'optin form hidden', return nothing.
                return '';
            }

            if ( ! apply_filters('mailoptin_show_optin_form', true, $sidebar_optin_id)) return '';

            if ( ! $this->user_targeting_rule_checker($sidebar_optin_id)) {
                return '';
            }

            if ( ! $this->query_level_targeting_rule_checker($sidebar_optin_id)) {
                return '';
            }

            if ( ! $this->page_level_targeting_rule_checker($sidebar_optin_id)) return '';
        }

        do_action('mo_sidebar_optin_widget_before_output', $args, $instance);

        echo $args['before_widget'];

        do_action('mo_sidebar_optin_widget_before_optin_form', $args, $instance);

        if ($title) echo $args['before_title'] . $title . $args['after_title'];

        echo OptinFormFactory::build($sidebar_optin_id);

        do_action('mo_sidebar_optin_widget_after_optin_form', $args, $instance);

        echo $args['after_widget'];

        do_action('mo_sidebar_optin_widget_after_output', $args, $instance);
    }

    /**
     * Back-end widget form.
     *
     * @see \WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     *
     * @return void
     */
    public function form($instance)
    {
        $title          = ! empty($instance['title']) ? $instance['title'] : '';
        $saved_optin_id = isset($instance['sidebar_optin_id']) ? $instance['sidebar_optin_id'] : '';

        do_action('mo_sidebar_optin_widget_before_form', $instance);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'mailoptin'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <?php do_action('mo_sidebar_optin_widget_middle_form', $instance); ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('sidebar_optin_id')); ?>"><?php esc_attr_e('Select Optin:', 'mailoptin'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('sidebar_optin_id')); ?>" name="<?php echo esc_attr($this->get_field_name('sidebar_optin_id')); ?>" style="width: 100%;">
                <option value="" <?php selected('', $saved_optin_id); ?>><?php _e('Select...', 'mailoptin'); ?></option>
                <?php foreach (OCR::get_sidebar_optin_ids() as $optin_campaign_id) : ?>
                    <?php $is_activated = OCR::is_activated($optin_campaign_id); ?>
                    <?php if (OCR::is_split_test_variant($optin_campaign_id)) continue; ?>

                    <?php $optin_campaign_id = absint($optin_campaign_id); ?>
                    <?php if ( ! $is_activated) : ?>
                        <option disabled="disabled" value="<?php echo $optin_campaign_id; ?>" <?php selected($optin_campaign_id, $saved_optin_id); ?> ><?php echo OCR::get_optin_campaign_name($optin_campaign_id) . sprintf(' (%s)', __('Not Activated', 'mailoptin')); ?></option>
                    <?php endif; ?>
                    <?php if ($is_activated) : ?>
                        <option value="<?php echo $optin_campaign_id; ?>" <?php selected($optin_campaign_id, $saved_optin_id); ?>><?php echo OCR::get_optin_campaign_name($optin_campaign_id); ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </p>

        <?php
        do_action('mo_sidebar_optin_widget_after_form', $instance);
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance                     = array();
        $instance['title']            = ( ! empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['sidebar_optin_id'] = ( ! empty($new_instance['sidebar_optin_id'])) ? absint($new_instance['sidebar_optin_id']) : '';

        return $instance;
    }

    public static function widget_registration()
    {
        register_widget(__CLASS__);
    }
}