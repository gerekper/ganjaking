<?php

namespace WPDeveloper\BetterDocsPro\Admin;

use \WPDeveloper\BetterDocs\Admin\ReportEmail as FreeReportEmail;

class ReportEmail extends FreeReportEmail {

    public function init() {
        parent::init();

        add_filter( 'betterdocs_analytics_reporting_tables', [$this, 'reporting_tables'], 10, 4 );

        add_action( 'betterdocs_monthly_email_reporting', [$this, 'send_pro_email'] );
        add_action( 'betterdocs_daily_email_reporting', [$this, 'send_pro_email'] );
    }

    public function reporting_tables( $analytics_body, $args, $frequency, $report_email ) {
        $_reporting_metrics = $this->settings->get( 'select_reporting_data', ['overview', 'top-docs', 'most-search'] );

        if ( $_reporting_metrics && ! in_array( 'overview', $_reporting_metrics ) ) {
            unset( $analytics_body['overview'] );
        }

        if ( $_reporting_metrics && ! in_array( 'top-docs', $_reporting_metrics ) ) {
            unset( $analytics_body['leading-docs'] );
        }

        if ( $_reporting_metrics && ! in_array( 'most-search', $_reporting_metrics ) ) {
            unset( $analytics_body['search-keyword'] );
        }

        return $analytics_body;
    }

    public function email_subject() {
        return $this->settings->get( 'reporting_subject' );
    }

    public function send_pro_email() {
        return $this->send_email( $this->settings->get( 'reporting_frequency', 'betterdocs_weekly' ) );
    }

    /**
     * Enable Cron Function
     * Hook: admin_init
     */
    public function activate() {
        $day       = $this->settings->get( 'reporting_day', 'monday' );
        $frequency = $this->settings->get( 'reporting_frequency', 'betterdocs_weekly' );

        if ( $frequency === 'betterdocs_weekly' ) {
            $datetime = strtotime( "next $day 9AM", current_time( 'timestamp' ) );
            $hook     = 'betterdocs_weekly_email_reporting';
        }

        if ( $frequency === 'betterdocs_daily' ) {
            $datetime = strtotime( "+1days 9AM" );
            $hook     = 'betterdocs_daily_email_reporting';
        }

        if ( $frequency === 'betterdocs_monthly' ) {
            $datetime = strtotime( "first day of next month 9AM" );
            $hook     = 'betterdocs_monthly_email_reporting';
        }

        $this->schedule_event( $datetime, $frequency, $hook );
    }
}
