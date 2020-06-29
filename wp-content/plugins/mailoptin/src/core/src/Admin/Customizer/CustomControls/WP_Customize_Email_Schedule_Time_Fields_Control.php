<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

use Carbon\Carbon;
use MailOptin\Core\Repositories\EmailCampaignRepository;
use WP_Customize_Control;

class WP_Customize_Email_Schedule_Time_Fields_Control extends WP_Customize_Control
{
    public $type = 'mailoptin_email_campaign_schedule';

    public $format = EmailCampaignRepository::NEW_PUBLISH_POST;

    public function select_attrs($select_attrs)
    {
        foreach ($select_attrs as $attr => $value) {
            echo $attr . '="' . esc_attr($value) . '" ';
        }
    }

    public function mo_input_attrs($input_attrs)
    {
        foreach ($input_attrs as $attr => $value) {
            echo $attr . '="' . esc_attr($value) . '" ';
        }
    }

    public function digest_control_script()
    {
        ?>
        <script type="text/javascript">
            (function ($) {
                var do_action = function (val) {

                    var value = this.value || val;

                    $('#schedule_interval').css('width', '');
                    $('.schedule-subselect').hide().css('width', '');

                    if (value === 'every_day') {
                        $('#schedule_interval').css('width', 'auto');
                        $('#schedule_time').show().css('width', 'auto');
                    }

                    if (value === 'every_week') {
                        $('#schedule_day').show();
                        $('#schedule_time').show();
                    }

                    if (value === 'every_month') {
                        $('#schedule_month_date').show();
                        $('#schedule_time').show();
                    }
                };

                do_action($('#schedule_interval').val());
                $(document).on('change', '#schedule_interval', do_action);

            })(jQuery);
        </script>
        <?php
    }

    public function new_publish_post_format()
    {
        if ($this->format !== EmailCampaignRepository::NEW_PUBLISH_POST) return;

        $settings = array_keys($this->settings);

        $select_choices = [
            'minutes' => __('Minutes', 'mailoptin'),
            'hours' => __('Hours', 'mailoptin'),
            'days' => __('Days', 'mailoptin'),
        ];

        $input_attrs = [
            'size' => 2,
            'maxlength' => 2,
            'style' => 'width:auto',
            'pattern' => '([0-9]){2}'
        ];

        $select_attrs = ['style' => 'width:auto'];
        ?>
        <div>
            <input type="text" <?php echo $this->mo_input_attrs($input_attrs); ?> value="<?php echo esc_attr($this->value($settings[0])); ?>" <?php $this->link($settings[0]); ?> />
            <select <?php $this->link($settings[1]); ?> <?php echo $this->select_attrs($select_attrs); ?> >
                <?php
                foreach ($select_choices as $value => $label)
                    echo '<option value="' . esc_attr($value) . '"' . selected($this->value($settings[1]), $value, false) . '>' . $label . '</option>';
                ?>
            </select>
            <span style="display: inline-block" class="description customize-control-description"><?php _e('after publishing', 'mailoptin'); ?></span>
        </div>
        <?php
    }

    public function email_digest_format()
    {
        if ($this->format !== EmailCampaignRepository::POSTS_EMAIL_DIGEST) return;

        $settings = array_keys($this->settings);

        $schedule_interval_choices = [
            'every_day' => __('Every Day', 'mailoptin'),
            'every_week' => __('Every Week', 'mailoptin'),
            'every_month' => __('Every Month', 'mailoptin'),
        ];

        $time_choices = [
            '00' => __('12:00 am', 'mailoptin'),
            '01' => __('1:00 am', 'mailoptin'),
            '02' => __('2:00 am', 'mailoptin'),
            '03' => __('3:00 am', 'mailoptin'),
            '04' => __('4:00 am', 'mailoptin'),
            '05' => __('5:00 am', 'mailoptin'),
            '06' => __('6:00 am', 'mailoptin'),
            '07' => __('7:00 am', 'mailoptin'),
            '08' => __('8:00 am', 'mailoptin'),
            '09' => __('9:00 am', 'mailoptin'),
            '10' => __('10:00 am', 'mailoptin'),
            '11' => __('11:00 am', 'mailoptin'),
            '12' => __('12:00 pm', 'mailoptin'),
            '13' => __('1:00 pm', 'mailoptin'),
            '14' => __('2:00 pm', 'mailoptin'),
            '15' => __('3:00 pm', 'mailoptin'),
            '16' => __('4:00 pm', 'mailoptin'),
            '17' => __('5:00 pm', 'mailoptin'),
            '18' => __('6:00 pm', 'mailoptin'),
            '19' => __('7:00 pm', 'mailoptin'),
            '20' => __('8:00 pm', 'mailoptin'),
            '21' => __('9:00 pm', 'mailoptin'),
            '22' => __('10:00 pm', 'mailoptin'),
            '23' => __('11:00 pm', 'mailoptin'),
        ];

        $day_choices = [
            Carbon::SUNDAY => __('Sunday', 'mailoptin'),
            Carbon::MONDAY => __('Monday', 'mailoptin'),
            Carbon::TUESDAY => __('Tuesday', 'mailoptin'),
            Carbon::WEDNESDAY => __('Wednesday', 'mailoptin'),
            Carbon::THURSDAY => __('Thursday', 'mailoptin'),
            Carbon::FRIDAY => __('Friday', 'mailoptin'),
            Carbon::SATURDAY => __('Saturday', 'mailoptin')
        ];

        $month_date_choices = [
            '1' => __('1st', 'mailoptin'),
            '2' => __('2nd', 'mailoptin'),
            '3' => __('3rd', 'mailoptin'),
            '4' => __('4th', 'mailoptin'),
            '5' => __('5th', 'mailoptin'),
            '6' => __('6th', 'mailoptin'),
            '7' => __('7th', 'mailoptin'),
            '8' => __('8th', 'mailoptin'),
            '9' => __('9th', 'mailoptin'),
            '10' => __('10th', 'mailoptin'),
            '11' => __('11th', 'mailoptin'),
            '12' => __('12th', 'mailoptin'),
            '13' => __('13th', 'mailoptin'),
            '14' => __('14th', 'mailoptin'),
            '15' => __('15th', 'mailoptin'),
            '16' => __('16th', 'mailoptin'),
            '17' => __('17th', 'mailoptin'),
            '18' => __('18th', 'mailoptin'),
            '19' => __('19th', 'mailoptin'),
            '20' => __('20th', 'mailoptin'),
            '21' => __('21st', 'mailoptin'),
            '22' => __('22nd', 'mailoptin'),
            '23' => __('23rd', 'mailoptin'),
            '24' => __('24th', 'mailoptin'),
            '25' => __('25th', 'mailoptin'),
            '26' => __('26th', 'mailoptin'),
            '27' => __('27th', 'mailoptin'),
            '28' => __('28th', 'mailoptin'),
        ];

        $input_attrs = $select_attrs = [];

        ?>

        <select <?php $this->link($settings[0]); ?> id="<?= $settings[0] ?>" <?php echo $this->select_attrs($select_attrs); ?>>
            <?php
            foreach ($schedule_interval_choices as $value => $label)
                echo '<option value="' . esc_attr($value) . '"' . selected($this->value($settings[0]), $value, false) . '>' . $label . '</option>';
            ?>
        </select>

        <select class="schedule-subselect" <?php $this->link('schedule_month_date'); ?> id="<?= 'schedule_month_date' ?>">
            <?php
            foreach ($month_date_choices as $value => $label)
                echo '<option value="' . esc_attr($value) . '"' . selected($this->value('schedule_month_date'), $value, false) . '>' . $label . '</option>';
            ?>
        </select>

        <select class="schedule-subselect" <?php $this->link('schedule_day'); ?> id="<?= 'schedule_day' ?>">
            <?php
            foreach ($day_choices as $value => $label)
                echo '<option value="' . esc_attr($value) . '"' . selected($this->value('schedule_day'), $value, false) . '>' . $label . '</option>';
            ?>
        </select>

        <select class="schedule-subselect" <?php $this->link('schedule_time'); ?> id="<?= 'schedule_time' ?>" <?php echo $this->select_attrs($select_attrs); ?>>
            <?php
            foreach ($time_choices as $value => $label)
                echo '<option value="' . esc_attr($value) . '"' . selected($this->value('schedule_time'), $value, false) . '>' . $label . '</option>';
            ?>
        </select>
        <?php

        $this->digest_control_script();
    }

    public function render_content()
    {
        ?>
        <label>
            <?php if (!empty($this->label)) : ?>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>
            <?php $this->new_publish_post_format(); ?>
            <?php $this->email_digest_format(); ?>
        </label>

        <?php if (!empty($this->description)) : ?>
        <span class="description customize-control-description"><?php echo $this->description; ?></span>
    <?php endif;
    }
}