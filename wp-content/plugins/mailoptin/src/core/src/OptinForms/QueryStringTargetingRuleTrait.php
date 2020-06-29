<?php

namespace MailOptin\Core\OptinForms;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;

trait QueryStringTargetingRuleTrait
{
    /**
     * Query level output checker
     *
     * @param int $id
     *
     * @return bool
     */
    public function query_level_targeting_rule_checker($id)
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            return true;
        }

        $action = sanitize_text_field(OCR::get_customizer_value($id, 'filter_query_action'));
        $query  = sanitize_text_field(OCR::get_customizer_value($id, 'filter_query_string'));
        $value  = sanitize_text_field(OCR::get_customizer_value($id, 'filter_query_value'));
        $match  = false;

        if ( ! $action || $action == '0') return true;

        if (empty($query)) return true;

        if (isset($_GET[$query]) && (empty($value) || $_GET[$query] == $value)) {
            $match = true;
        }

        if ('hide' == $action) {
            return ! $match;
        }

        return $match;

    }
}