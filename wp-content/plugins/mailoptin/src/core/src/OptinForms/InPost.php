<?php

namespace MailOptin\Core\OptinForms;

use MailOptin\Core\Admin\Customizer\OptinForm\OptinFormFactory;
use MailOptin\Core\Repositories\OptinCampaignsRepository as Repository;
use MailOptin\Core\Repositories\OptinCampaignsRepository;


class InPost
{
    use PageTargetingRuleTrait, UserTargetingRuleTrait;

    public function __construct()
    {
        add_filter('the_content', [$this, 'insert_optin'], 99999999999999999999);
    }

    public function insert_optin($content)
    {
        // needed to prevent the optin from showing on post excerpt (on homepage / post listing)
        if (is_front_page() || ! is_singular()) return $content;

        if (isset($_GET['mohide']) && $_GET['mohide'] == 'true') return $content;

        $optin_ids = get_transient('mo_get_optin_ids_inpost_display');

        if ($optin_ids === false) {
            $optin_ids = Repository::get_inpost_optin_ids();
            set_transient('mo_get_optin_ids_inpost_display', $optin_ids, HOUR_IN_SECONDS);
        }

        foreach ($optin_ids as $id) {

            $id = absint($id);

            do_action('mailoptin_before_inpost_optin_display_determinant', $id, $optin_ids);

            // if it is a split test variant, skip
            if (Repository::is_split_test_variant($id)) continue;

            // if optin is not enabled, pass.
            if ( ! Repository::is_activated($id)) continue;

            $id = Repository::choose_split_test_variant($id);

            $optin_position = Repository::get_merged_customizer_value($id, 'inpost_form_optin_position');

            if ( ! OptinCampaignsRepository::is_test_mode($id)) {

                // if optin global exit/interaction and success cookie result fails, move to next.
                if ( ! Repository::global_cookie_check_result()) continue;

                if ( ! apply_filters('mailoptin_show_optin_form', true, $id)) continue;

                if ( ! $this->user_targeting_rule_checker($id)) {
                    continue;
                }

                if ( ! $this->page_level_targeting_rule_checker($id)) {
                    continue;
                }

                if ( ! $this->query_level_targeting_rule_checker($id)) {
                    continue;
                }
            }

            $optin_form = OptinFormFactory::build($id);

            if ('between_content' == $optin_position) {

                $content_array = explode('</p>', $content);

                // using array_values because we want the array key re-indexed.
                // see https://stackoverflow.com/a/20373067/2648410
                $content_array = array_values(
                    array_filter($content_array, function ($val) {
                        // useful to remove any redundant <p></p>
                        return ! empty(trim($val));
                    })
                );

                $content_length     = count($content_array);
                $content_first_half = ceil($content_length / 2);
                $content_to_return  = '';

                for ($i = 0; $i < $content_first_half; $i++) {
                    $content_to_return .= $content_array[$i] . '</p>';
                }

                $content_to_return .= $optin_form;

                for ($i = $content_first_half; $i < $content_length; $i++) {
                    $content_to_return .= $content_array[$i] . '</p>';
                }

                $content = $content_to_return;

            } elseif ('before_content' == $optin_position) {
                $content = $optin_form . $content;
            } else {
                $content .= $optin_form;
            }

            do_action('mailoptin_after_inpost_optin_display_determinant', $id, $optin_ids);
        }


        return $content;
    }

    /**
     * @return InPost
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Query level output checker
     */
    public function query_level_targeting_rule_checker($id)
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            return true;
        }

        $action = sanitize_text_field(Repository::get_customizer_value($id, 'filter_query_action'));
        $query  = sanitize_text_field(Repository::get_customizer_value($id, 'filter_query_string'));
        $value  = sanitize_text_field(Repository::get_customizer_value($id, 'filter_query_value'));
        $match  = false;

        if ( ! $action || $action == '0') {
            return true;
        }

        if ($action && $query && isset($_GET[$query]) && (empty($value) || $_GET[$query] == $value)) {
            $match = true;
        }

        if ('hide' == $action) {
            return ! $match;
        }

        return $match;

    }

}