<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Datetimepicker
 *
 * @class RightPress_Datetimepicker
 * @package RightPress
 * @author RightPress
 */
class RightPress_Datetimepicker extends RightPress_Asset
{

    protected $key          = 'datetimepicker';
    protected $is_included  = false;

    /**
     * Constructor
     *
     * @access public
     * @param array $args
     * @return void
     */
    public function __construct($args)
    {

        // Environment variables
        $this->path = trailingslashit(dirname(__FILE__));
        $this->url  = RightPress_Loader::get_component_url('rightpress-assets-component', 'assets/datetimepicker/');

        // Call parent constructor
        parent::__construct($args);
    }

    /**
     * Define scripts
     *
     * @access public
     * @return void
     */
    public function define_scripts()
    {

        $this->scripts = array(
            'rightpress-datetimepicker-scripts' => array(
                'relative_url'  => 'assets/js/jquery.datetimepicker.full.min.js',
                'dependencies'  => array('jquery'),
                'variables'     => array(
                    'datetime_config'   => array($this, 'get_datetime_config'),
                    'date_config'       => array($this, 'get_date_config'),
                    'time_config'       => array($this, 'get_time_config'),
                    'locale'            => array($this, 'get_locale'),
                ),
            ),
        );
    }

    /**
     * Define styles
     *
     * @access public
     * @return void
     */
    public function define_styles()
    {

        $this->styles = array(
            'rightpress-datetimepicker-styles' => array(
                'relative_url' => 'assets/css/jquery.datetimepicker.min.css',
            ),
        );
    }

    /**
     * Get date config
     *
     * @access public
     * @return array
     */
    public function get_date_config()
    {

        return array(
            'timepicker'        => false,
            'dayOfWeekStart'    => RightPress_Help::get_start_of_week(),
            'closeOnDateSelect' => true,
            'lazyInit'          => true,
            'format'            => $this->get_date_format(),
            'formatDate'        => $this->get_date_format(),
        );
    }

    /**
     * Get date format
     *
     * @access public
     * @return string
     */
    public function get_date_format()
    {

        // Date format set in args
        if (isset($this->args['date_format'])) {
            return $this->args['date_format'];
        }

        // Return default date format
        return 'Y-m-d';
    }

    /**
     * Get time config
     *
     * @access public
     * @return array
     */
    public function get_time_config()
    {

        // Define time config
        $time_config = array(
            'datepicker'            => false,
            'formatTime'            => get_option('time_format'),
            'defaultTime'           => '08:00',
            'closeOnDateSelect'     => false,
            'timepickerScrollbar'   => true,
            'lazyInit'              => true,
            'format'                => $this->get_time_format(),
            'formatTime'            => $this->get_time_format(),
            'step'                  => $this->get_step(),
        );

        // Add extra arguments if provided in constructor
        if (isset($this->args['allow_times'])) {
            $time_config['allowTimes'] = $this->args['allow_times'];
        }

        return $time_config;
    }

    /**
     * Get time format
     *
     * @access public
     * @return string
     */
    public function get_time_format()
    {

        // Time format set in args
        if (isset($this->args['time_format'])) {
            return $this->args['time_format'];
        }

        // Return default time format
        return 'H:i';
    }

    /**
     * Get step
     *
     * @access public
     * @return int
     */
    public function get_step()
    {

        // Step value set in args
        if (isset($this->args['time_step'])) {
            return $this->args['time_step'];
        }

        // Return default step value
        return 60;
    }

    /**
     * Get datetime config
     *
     * @access public
     * @return array
     */
    public function get_datetime_config()
    {

        // Get date and time config
        $date_config = $this->get_date_config();
        $time_config = $this->get_time_config();

        // Merge configs
        $config = array_merge($date_config, $time_config);

        // Enable date picker and time picker
        $config['datepicker'] = true;
        $config['timepicker'] = true;

        // Set datetime format
        $config['format'] = $date_config['format'] . ' ' . $time_config['format'];

        // Return config
        return $config;
    }

    /**
     * Get datetime format
     *
     * @access public
     * @return string
     */
    public function get_datetime_format()
    {

        // Datetime format set in args
        if (isset($this->args['datetime_format'])) {
            return $this->args['datetime_format'];
        }

        // Return default datetime format
        return ($this->get_date_format() . ' ' . $this->get_time_format());
    }

    /**
     * Get locale
     *
     * @access public
     * @return string
     */
    public function get_locale()
    {

        // Define WP/Datetimepicker locale pairs
        $map = array(
            'ar' => 'ar', 'az' => 'az', 'bg' => 'bg', 'bs_BA' => 'bs',
            'ca' => 'ca', 'cs_CZ' => 'cs', 'da_DK' => 'da', 'de' => 'de',
            'de_CH' => 'de', 'el' => 'el', 'en_GB' => 'en-GB', 'es_AR' => 'es',
            'es_CL' => 'es', 'es_CO' => 'es', 'es_CR' => 'es', 'es_GT' => 'es',
            'es_MX' => 'es', 'es_PE' => 'es', 'es_PR' => 'es', 'es' => 'es',
            'es_VE' => 'es', 'et' => 'et', 'eu' => 'eu', 'fa_IR' => 'fa',
            'fa_AF' => 'fa', 'fi' => 'fi', 'fr_BE' => 'fr', 'fr_CA' => 'fr',
            'fr' => 'fr', 'gl_ES' => 'gl', 'he_IL' => 'he', 'hr' => 'hr',
            'hu' => 'hu', 'id' => 'id', 'it' => 'it', 'ja' => 'ja',
            'ko_KR' => 'kr', 'lt' => 'lt', 'lv' => 'lv', 'mk' => 'mk',
            'mn' => 'mn', 'nl' => 'nl', 'nl_BE' => 'nl', 'nb_NO' => 'no',
            'nn_NO' => 'no', 'pl' => 'pl', 'pt' => 'pt', 'pt_BR' => 'pt-BR',
            'ro' => 'ro', 'ru' => 'ru', 'sv_SE' => 'se', 'sk' => 'sk',
            'sl_SI' => 'sl', 'sq' => 'sq', 'sr_RS' => 'sr', 'th' => 'th',
            'tr' => 'tr', 'uk' => 'uk', 'vi' => 'vi', 'zh_CN' => 'zh',
            'zh_HK' => 'zh-TW', 'zh_TW' => 'zh-TW',
        );

        // Get WordPress locale
        $wp_locale = RightPress_Help::get_optimized_locale('mixed');

        // Locale is defined
        return isset($map[$wp_locale]) ? $map[$wp_locale] : 'en';
    }





}
