<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Betterdocs_Pro
 * @subpackage Betterdocs_Pro/admin
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
if ( ! class_exists( 'BetterDocs_Report_Email' ) ) return;
class Betterdocs_Pro_Report_Email extends BetterDocs_Report_Email
{
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct()
	{
        parent::__construct();
		add_filter('betterdocs_reporting_frequency_settings', array( $this, 'reporting_frequency_settings' ), 10, 1 );
        add_filter('betterdocs_reporting_subject_settings', array( $this, 'reporting_subject' ), 10, 1 );
        add_filter('betterdocs_reporting_email_subject', array( $this, 'email_subject' ), 10, 1 );
        add_filter('betterdocs_select_reporting_data_settings', array( $this, 'select_reporting_data' ), 10, 1 );
        add_filter('betterdocs_test_reporting_frequency', array( $this, 'test_reporting_frequency' ), 10, 1 );
        add_filter('betterdocs_analytics_reporting_tables', array( $this, 'reporting_tables' ), 10, 4 );   
        if ( BetterDocs_DB::get_settings('enable_reporting') == 1 && BetterDocs_DB::get_settings('reporting_frequency') != 'betterdocs_weekly' ) {
            add_action('admin_init', array( $this, 'set_reporting_event' ));
            add_action('betterdocs_monthly_email_reporting', array( $this, 'send_email_monthly' ));
            add_action('betterdocs_daily_email_reporting', array( $this, 'send_email_daily' ));
        }
	}

    public function reporting_frequency_settings() {
        $settings = array(
            'name'     => 'reporting_frequency',
            'type'     => 'select',
            'label'    => __( 'Reporting Frequency', 'betterdocs-pro' ),
            'default'  => 'betterdocs_daily',
            'priority' => 1,
            'options'  => array(
                'betterdocs_daily'   => __( 'Once Daily', 'betterdocs-pro' ),
                'betterdocs_weekly'  => __( 'Once Weekly', 'betterdocs-pro' ),
                'betterdocs_monthly' => __( 'Once Monthly', 'betterdocs-pro' )
            ),
            'description' => __( 'It will be triggered on the first day of next month.', 'betterdocs' )
        );
        return $settings;
    }

    public function reporting_subject() {
        $settings = array(
            'name'     => 'reporting_subject_updated',
            'type'     => 'text',
            'label'    => __( 'Reporting Email Subject', 'betterdocs-pro' ),
            'default'  => wp_sprintf( '%s %s %s', __( 'Your Documentation Performance of', 'betterdocs' ),  get_bloginfo( 'name' ), __( 'Website', 'betterdocs' ) ),
            'priority' => 4,
        );
        return $settings;
    }

    public function email_subject() {
        $reporting_subject = BetterDocs_DB::get_settings('reporting_subject_updated');
        if ( isset( $reporting_subject  ) ) {
            return $reporting_subject;
        }
    }

    public function test_reporting_frequency() {
        return BetterDocs_DB::get_settings('reporting_frequency');
    }

    public function select_reporting_data() {
        $settings = array(
            'name'     => 'select_reporting_data',
            'type'        => 'select',
            'label'       => __('Select Reporting Data', 'betterdocs-pro'),
            'priority'    => 1,
            'multiple' => true,
            'options' => array(
                'overview' => __( 'Overview', 'betterdocs-pro' ),
                'top-docs' => __( 'Top Docs', 'betterdocs-pro' ),
                'most-search' => __( 'Most Searched Keywords', 'betterdocs-pro' ),
            ),
            'default' => array('overview', 'top-docs', 'most-search'),
        );
        return $settings;
    }

    public function reporting_tables( $analytics, $args, $frequency) {
        $reporting_tables = BetterDocs_DB::get_settings('select_reporting_data');

        if ( is_array( $reporting_tables ) ) {
            $analytics = '';

            if ( in_array('overview', $reporting_tables ) ) {
                $analytics .= $this->analytics_overview( $args, $frequency );
            }
            
            if ( in_array('top-docs', $reporting_tables ) ) {
                $analytics .= $this->leading_docs( $args['docs']['current_data'], $args['docs']['total_current_reactions'], $frequency );
            }
            
            if ( in_array( 'most-search', $reporting_tables ) ) {
                $analytics .= $this->search_keywords( $args['search']['keywords'], $frequency );
            }
        }

        return $analytics;
        
    }

    public function send_email_monthly() {
        return $this->send_email( 'betterdocs_monthly' );
    }

    public function send_email_daily() {
        return $this->send_email( 'betterdocs_daily' );
    }

    public function set_reporting_event() {
        if( BetterDocs_DB::get_settings('enable_reporting') == false ) {
            return;
        }

        if( $this->reporting_frequency() === 'betterdocs_daily' ) {
            $datetime = strtotime( "+1days 9AM" );
            $this->mail_report_deactivation( 'betterdocs_weekly_email_reporting' );
            $this->mail_report_deactivation( 'betterdocs_monthly_email_reporting' );
            if ( ! wp_next_scheduled ( 'betterdocs_daily_email_reporting' ) ) {
                wp_schedule_event( $datetime, $this->reporting_frequency(), 'betterdocs_daily_email_reporting' );
            }
        }
        if( $this->reporting_frequency() === 'betterdocs_monthly' ) {
            $datetime = strtotime( "first day of next month 9AM" );
            $this->mail_report_deactivation( 'betterdocs_daily_email_reporting' );
            $this->mail_report_deactivation( 'betterdocs_weekly_email_reporting' );
            if ( ! wp_next_scheduled ( 'betterdocs_monthly_email_reporting' ) ) {
                wp_schedule_event( $datetime, $this->reporting_frequency(), 'betterdocs_monthly_email_reporting' );
            }
        }
    }
}

new Betterdocs_Pro_Report_Email;