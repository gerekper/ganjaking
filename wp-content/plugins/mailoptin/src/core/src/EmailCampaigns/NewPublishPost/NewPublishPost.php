<?php

namespace MailOptin\Core\EmailCampaigns\NewPublishPost;

use MailOptin\Core\Connections\ConnectionFactory;
use MailOptin\Core\EmailCampaigns\AbstractTriggers;
use MailOptin\Core\EmailCampaigns\Misc;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;
use pb_backupbuddy;
use WP_Post;

class NewPublishPost extends AbstractTriggers
{
    public function __construct()
    {
        parent::__construct();

        add_action('transition_post_status', array($this, 'new_publish_post'), 1, 3);

        add_action('mailoptin_send_scheduled_email_campaign', array($this, 'send_scheduled_email_campaign'), 10, 2);
    }

    /**
     * Send scheduled newsletter.
     *
     * @param int $email_campaign_id
     * @param int $campaign_id
     */
    public function send_scheduled_email_campaign($email_campaign_id, $campaign_id)
    {
        // self::send_campaign()automatically update campaign status when processed or failed.
        $this->send_campaign($email_campaign_id, $campaign_id);
    }

    /**
     * Get time email campaign is set to go out.
     *
     * @param int $email_campaign_id
     *
     * @return string
     */
    public function schedule_time($email_campaign_id)
    {
        $schedule_digit = $this->schedule_digit($email_campaign_id);
        $schedule_type  = $this->schedule_type($email_campaign_id);
        if (empty($schedule_digit) || empty($schedule_type)) return false;

        return $schedule_digit . $schedule_type;
    }

    /**
     * @param string $new_status New post status.
     * @param string $old_status Old post status.
     * @param WP_Post $post Post object.
     */
    public function new_publish_post($new_status, $old_status, $post)
    {
        if ($new_status == 'publish' && $old_status != 'publish') {

            // fix incompatibility with backupbuddy making post content empty.
            if (class_exists('pb_backupbuddy') && method_exists('pb_backupbuddy', 'remove_action')) {
                pb_backupbuddy::remove_action(array('save_post', 'save_post_iterate_edits_since_last'));
            }

            // hopefully this will cause all custom field to be updated before new post is triggered.
            do_action('save_post', $post->ID, $post, true);

            if (get_post_meta($post->ID, '_mo_disable_npp', true) == 'yes') return;

            $new_publish_post_campaigns = ER::get_by_email_campaign_type(ER::NEW_PUBLISH_POST);

            foreach ($new_publish_post_campaigns as $npp_campaign) {
                $email_campaign_id = absint($npp_campaign['id']);

                if (ER::is_campaign_active($email_campaign_id) === false) continue;

                $custom_post_type = ER::get_merged_customizer_value($email_campaign_id, 'custom_post_type');

                $post_type_support = ['post'];

                if ($custom_post_type != 'post') {
                    $post_type_support = [$custom_post_type];
                }

                $post_type_support = apply_filters('mo_new_publish_post_post_types_support', $post_type_support, $email_campaign_id);

                if ( ! in_array($post->post_type, $post_type_support)) continue;

                $npp_post_authors = ER::get_merged_customizer_value($email_campaign_id, 'post_authors');

                if ( ! empty($npp_post_authors)) {
                    if ( ! in_array($post->post_author, $npp_post_authors)) continue;
                }

                $custom_post_type_settings = ER::get_merged_customizer_value($email_campaign_id, 'custom_post_type_settings');

                if ($custom_post_type != 'post' && ! empty($custom_post_type_settings)) {
                    $custom_post_type_settings = json_decode($custom_post_type_settings, true);

                    if (is_array($custom_post_type_settings)) {
                        foreach ($custom_post_type_settings as $taxonomy => $npp_terms) {
                            if ( ! empty($npp_terms)) {
                                $npp_terms  = array_map('absint', $npp_terms);
                                $post_terms = array_map('absint', wp_get_object_terms($post->ID, $taxonomy, ['fields' => 'ids']));

                                // do not check if $post_terms is empty because if no term is on the post, wp_get_object_terms return empty array
                                // so we can use the empty to check against if NPP requires certain term(s)
                                if (is_array($npp_terms) && ! empty($npp_terms)) {
                                    $result = array_intersect($post_terms, $npp_terms);
                                    if (empty($result)) continue 2;
                                }
                            }
                        }
                    }
                } else {
                    $npp_categories  = ER::get_merged_customizer_value($email_campaign_id, 'post_categories');
                    $npp_tags        = ER::get_merged_customizer_value($email_campaign_id, 'post_tags');
                    $post_categories = wp_get_post_categories($post->ID, ['fields' => 'ids']);
                    $post_tags       = wp_get_post_tags($post->ID, ['fields' => 'ids']);

                    // do not check if $post_categories is empty because if no category is on the post, wp_get_post_categories return empty array
                    // so we can use the empty to check against if NPP requires certain category(s)
                    if (is_array($npp_categories) && ! empty($npp_categories)) {
                        // use intersect to check if categories match.
                        $result = array_intersect($post_categories, $npp_categories);
                        if (empty($result)) continue;
                    }

                    if (is_array($npp_tags) && ! empty($npp_tags)) {
                        // use intersect to check if categories match.
                        $result = array_intersect($post_tags, $npp_tags);
                        if (empty($result)) continue;
                    }
                }

                $send_immediately_active = $this->send_immediately($email_campaign_id);
                $email_subject           = Misc::parse_email_subject(ER::get_merged_customizer_value($email_campaign_id, 'email_campaign_subject'));

                $content_html = (new Templatify($email_campaign_id, $post))->forge();

                $campaign_id = $this->save_campaign_log(
                    $email_campaign_id,
                    self::format_campaign_subject($email_subject, $post),
                    $content_html
                );

                if ($send_immediately_active) {
                    $this->send_campaign($email_campaign_id, $campaign_id);
                } else {

                    if ( ! $this->schedule_time($email_campaign_id)) continue;

                    // convert schedule time to timestamp.
                    $schedule_time_timestamp = \MailOptin\Core\strtotime_utc($this->schedule_time($email_campaign_id));

                    $response = wp_schedule_single_event(
                        $schedule_time_timestamp,
                        'mailoptin_send_scheduled_email_campaign',
                        [$email_campaign_id, $campaign_id]
                    );

                    // wp_schedule_single_event() return false if event wasn't scheduled.
                    if (false !== $response) {
                        $this->update_campaign_status($campaign_id, 'queued', $schedule_time_timestamp);
                    }
                }
            }
        }
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
     * Replace any placeholder in email subject to correct value.
     *
     * @param string $email_subject
     * @param \stdClass|WP_Post $data_source
     *
     * @return mixed
     */
    public static function format_campaign_subject($email_subject, $data_source)
    {
        $search  = ['{{title}}'];
        $replace = [$data_source->post_title];

        return do_shortcode(str_replace($search, $replace, $email_subject));
    }

    /**
     * Singleton.
     *
     * @return NewPublishPost
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