<?php

namespace MailOptin\Core\EmailCampaigns\PostsEmailDigest;

use Carbon\Carbon;
use MailOptin\Core\Connections\ConnectionFactory;
use MailOptin\Core\EmailCampaigns\AbstractTriggers;
use MailOptin\Core\EmailCampaigns\Misc;
use MailOptin\Core\Repositories\EmailCampaignMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;

class PostsEmailDigest extends AbstractTriggers
{
    public function __construct()
    {
        parent::__construct();

        add_action('mo_hourly_recurring_job', [$this, 'run_job']);
    }

    public function last_processed_at($email_campaign_id)
    {
        return EmailCampaignMeta::get_meta_data($email_campaign_id, 'last_processed_at');
    }

    public function timezone()
    {
        return wp_timezone_string();
    }

    public function post_collect_query($email_campaign_id)
    {
        $item_count = EmailCampaignRepository::get_merged_customizer_value($email_campaign_id, 'item_number');

        $parameters = [
            'posts_per_page' => $item_count,
            'post_status'    => 'publish',
            'post_type'      => 'post',
            'order'          => 'DESC',
            'orderby'        => 'post_date'
        ];

        $post_authors = ER::get_merged_customizer_value($email_campaign_id, 'post_authors');

        if ( ! empty($post_authors)) {
            $parameters['author'] = implode(',', $post_authors);
        }

        $custom_post_type = ER::get_merged_customizer_value($email_campaign_id, 'custom_post_type');

        if ($custom_post_type != 'post') {
            $parameters['post_type'] = $custom_post_type;

            $custom_post_type_settings = ER::get_merged_customizer_value($email_campaign_id, 'custom_post_type_settings');

            if ( ! empty($custom_post_type_settings)) {

                $custom_post_type_settings = json_decode($custom_post_type_settings, true);

                if (is_array($custom_post_type_settings)) {

                    $parameters['tax_query'] = [];

                    foreach ($custom_post_type_settings as $taxonomy => $digest_terms) {
                        if ( ! empty($digest_terms)) {
                            $parameters['tax_query'][] = [
                                'taxonomy' => $taxonomy,
                                'field'    => 'term_id',
                                'terms'    => array_map('absint', $digest_terms)
                            ];
                        }
                    }
                }
            }
        } else {
            $categories = ER::get_merged_customizer_value($email_campaign_id, 'post_categories');
            $tags       = ER::get_merged_customizer_value($email_campaign_id, 'post_tags');

            if ( ! empty($categories)) {
                $parameters['category'] = implode(',', array_map('trim', $categories));
            }

            if ( ! empty($tags)) {
                $parameters['tag_id'] = implode(',', array_map('trim', $tags));
            }
        }

        return $parameters;
    }

    public function post_collection($email_campaign_id)
    {
        $newer_than_timestamp = EmailCampaignMeta::get_meta_data($email_campaign_id, 'created_at');
        // backward compat for bug we've fixed where meta was being saved as array.
        if (is_array($newer_than_timestamp)) {
            $newer_than_timestamp = $newer_than_timestamp[0];
        }

        $last_processed_at = $this->last_processed_at($email_campaign_id);

        if ( ! empty($last_processed_at)) {
            $newer_than_timestamp = $last_processed_at;
        }

        $parameters = $this->post_collect_query($email_campaign_id);

        $parameters['date_query'] = [
            [
                'column' => 'post_date',
                'after'  => $newer_than_timestamp
            ]
        ];

        return get_posts(apply_filters('mo_post_digest_get_posts_args', $parameters, $email_campaign_id));
    }

    /**
     * @param $email_campaign_id
     * @param Carbon $schedule_hour
     *
     * @param $digest_type
     *
     * @return bool
     */
    public function should_send($email_campaign_id, $schedule_hour, $digest_type)
    {
        $timezone   = $this->timezone();
        $carbon_now = $this->carbon_set_week_start_end(Carbon::now($timezone));

        $last_processed_at = EmailCampaignMeta::get_meta_data($email_campaign_id, 'last_processed_at');

        if ( ! empty($last_processed_at)) {

            $last_processed_at_carbon_instance = $this->carbon_set_week_start_end(
                Carbon::createFromFormat('Y-m-d H:i:s', $this->last_processed_at($email_campaign_id), $timezone)
            );

            if ($digest_type == 'every_day') {
                if ($last_processed_at_carbon_instance->isToday()) {
                    return false;
                }
            }

            if ($digest_type == 'every_week') {
                if ($last_processed_at_carbon_instance->isSameAs('o-W', $carbon_now)) {
                    return false;
                }
            }

            if ($digest_type == 'every_month') {
                if ($last_processed_at_carbon_instance->isCurrentMonth()) {
                    return false;
                }
            }
        }

        // add an hour grace so missed schedule can still run.
        if ($schedule_hour->diffInRealHours($carbon_now) <= 1) {
            return true;
        }

        return false;
    }

    /**
     * Set start and end day of a week.
     *
     * @param Carbon $carbon
     *
     * @return Carbon
     */
    public function carbon_set_week_start_end($carbon)
    {
        $start_of_week = absint(get_option('start_of_week', 1));
        $end_of_week   = $start_of_week == 0 ? 6 : $start_of_week - 1;

        $carbon->setWeekStartsAt($start_of_week);
        $carbon->setWeekEndsAt($end_of_week);

        return $carbon;
    }

    public function run_job()
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) return;

        $postDigests = EmailCampaignRepository::get_by_email_campaign_type(ER::POSTS_EMAIL_DIGEST);

        if (empty($postDigests)) return;


        foreach ($postDigests as $postDigest) {

            $email_campaign_id = absint($postDigest['id']);

            if (ER::is_campaign_active($email_campaign_id) === false) continue;

            $schedule_interval   = ER::get_merged_customizer_value($email_campaign_id, 'schedule_interval');
            $schedule_time       = absint(ER::get_merged_customizer_value($email_campaign_id, 'schedule_time'));
            $schedule_day        = absint(ER::get_merged_customizer_value($email_campaign_id, 'schedule_day'));
            $schedule_month_date = absint(ER::get_merged_customizer_value($email_campaign_id, 'schedule_month_date'));

            $timezone = $this->timezone();

            $carbon_now = $this->carbon_set_week_start_end(Carbon::now($timezone));

            $carbon_today = $this->carbon_set_week_start_end(Carbon::today($timezone));

            $schedule_hour = $carbon_today->hour($schedule_time);

            switch ($schedule_interval) {
                case 'every_day':
                    if ($schedule_hour->lessThanOrEqualTo($carbon_now) &&
                        $this->should_send($email_campaign_id, $schedule_hour, 'every_day')) {
                        $this->create_and_send_campaign($email_campaign_id);
                    }
                    break;
                case 'every_week':
                    if ($carbon_today->isDayOfWeek($schedule_day) &&
                        $schedule_hour->lessThanOrEqualTo($carbon_now) &&
                        $this->should_send($email_campaign_id, $schedule_hour, 'every_week')) {
                        $this->create_and_send_campaign($email_campaign_id);
                    }
                    break;
                case 'every_month':
                    if ($carbon_now->day == $schedule_month_date &&
                        $schedule_hour->lessThanOrEqualTo($carbon_now) &&
                        $this->should_send($email_campaign_id, $schedule_hour, 'every_month')) {
                        $this->create_and_send_campaign($email_campaign_id);
                    }
                    break;
            }
        }
    }

    public function create_and_send_campaign($email_campaign_id)
    {
        $campaign_id = $this->create_campaign($email_campaign_id);

        if ($campaign_id) {
            $this->send_campaign($email_campaign_id, $campaign_id);
        }
    }

    /**
     * @param $email_campaign_id
     *
     * @return bool|int
     */
    public function create_campaign($email_campaign_id)
    {
        $email_subject = Misc::parse_email_subject(ER::get_merged_customizer_value($email_campaign_id, 'email_campaign_subject'));

        $post_collection = $this->post_collection($email_campaign_id);

        if (empty($post_collection)) return false;

        $content_html = (new Templatify($email_campaign_id, $post_collection))->forge();

        return $this->save_campaign_log(
            $email_campaign_id,
            $email_subject,
            $content_html
        );
    }

    /**
     * Does the actual campaign sending.
     *
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     */
    public function send_campaign($email_campaign_id, $campaign_log_id)
    {
        $campaign           = $this->CampaignLogRepository->getById($campaign_log_id);
        $connection_service = $this->connection_service($email_campaign_id);

        $connection_instance = ConnectionFactory::make($connection_service);

        EmailCampaignMeta::update_meta_data($email_campaign_id, 'last_processed_at', current_time('mysql'));

        $response = $connection_instance->send_newsletter(
            $email_campaign_id,
            $campaign_log_id,
            $campaign->title,
            $connection_instance->replace_placeholder_tags($campaign->content_html, 'html'),
            $connection_instance->replace_placeholder_tags($campaign->content_text, 'text')
        );

        if (isset($response['success']) && (true === $response['success'])) {
            $this->update_campaign_status($campaign_log_id, 'processed');
        } else {
            $this->update_campaign_status($campaign_log_id, 'failed');
        }
    }

    /**
     * Singleton.
     *
     * @return PostsEmailDigest
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}