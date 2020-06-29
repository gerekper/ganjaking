<?php
defined('WYSIJA') or die('Restricted access');
/*
Class Analytics.
It's a sort of useful stats and numbers generator about MailPoet usage.
It also handles the MixPanel integration.
$analytics = new WJ_Analytics();
$analytics->generate_data();
$analytics->send();
*/
class WJ_Analytics {

    // All analytics data to be sent to JS.
    private $analytics_data = array(
        'php_version' => array(
          'label' => 'PHP version',
          'value' => ''
		),
        'monthly_emails_sent' => array(
          'label' => 'Monthly emails sent',
          'value' => ''
        ),
        'lists_with_more_than_25' => array(
          'label' => 'Lists with more than 25 subscribers',
          'value' => ''
        ),
        'confirmed_subscribers' => array(
          'label' => 'Confirmed subscribers',
          'value' => ''
        ),
        'range_confirmed_subscribers' => array(
          'label' => 'Range Confirmed subscribers',
          'value' => ''
        ),
        'unconfirmed_subscribers' => array(
          'label' => 'Unconfirmed subscribers',
          'value' => ''
        ),
        'standard_newsletters' => array(
          'label' => 'Standard newsletters',
          'value' => ''
        ),
        'auto_newsletters' => array(
          'label' => 'Auto newsletters',
          'value' => ''
        ),
        'wordpress_version' => array(
          'label' => 'WordPress Version',
          'value' => ''
        ),
        'plugin_version' => array(
          'label' => 'Plugin Version',
          'value' => ''
        ),
        'license_type' => array(
          'label' => 'License type',
          'value' => ''
        ),
        'sending_method' => array(
          'label' => 'Sending method',
          'value' => ''
        ),
        'smtp_hostname' => array(
          'label' => 'Smtp hostname',
          'value' => ''
        ),
        'activation_email_status' => array(
          'label' => 'Activation Email',
          'value' => ''
        ),
        'average_open_rate' => array(
          'label' => 'Open rate',
          'value' => ''
        ),
        'average_click_rate' => array(
          'label' => 'Click rate',
          'value' => ''
        ),
        'industry' => array(
          'label' => 'Industry',
          'value' => ''
        ),
        'wordpress_language' => array(
          'label' => 'WordPress Language',
          'value' => ''
        ),
        'rtl' => array(
          'label' => 'Rtl',
          'value' => ''
        ),
        'beta' => array(
          'label' => 'Beta',
          'value' => ''
        ),
        'archive_page' => array(
          'label' => 'Archive Page',
          'value' => ''
        ),
        'dkim_status' => array(
          'label' => 'DKIM Active',
          'value' => ''
        ),
        'subscribe_in_comments' => array(
          'label' => 'Subscribe in comments',
          'value' => ''
        ),
        'subscribe_on_register' => array(
          'label' => 'Subscribe on registration',
          'value' => ''
        ),
        'browser_link' => array(
          'label' => 'View in browser link',
          'value' => ''
        ),
        'profile_edit'=> array(
          'label' => 'Subcsribers can edit profile',
          'value' => ''
        ),
        'html_edit' => array(
          'label' => 'Allow HTML edit',
          'value' => ''
        ),
        'mailpoet_cron' => array(
          'label' => 'MailPoet Cron Enabled',
          'value' => ''
        ),
        'forms_number' => array(
          'label' => 'Total number of forms',
          'value' => ''
        ),
        'using_custom_fields' => array(
          'label' => 'Using custom fields',
          'value' => ''
        ),
        'custom_fields_input' => array(
          'label' => 'Custom Fields: Input field',
          'value' => ''
        ),
        'custom_fields_textarea' => array(
          'label' => 'Custom Fields: Textarea',
          'value' => ''
        ),
        'custom_fields_select' => array(
          'label' => 'Custom Fields: Select',
          'value' => ''
        ),
        'custom_fields_checkbox' => array(
          'label' => 'Custom Fields: Checkbox',
          'value' => ''
        ),
        'custom_fields_radio' => array(
          'label' => 'Custom Fields: Radio',
          'value' => ''
        ),
        'active_last_week' => array(
          'label' => 'Active last week',
          'value' => ''
        ),
        'is_multisite' => array(
          'label' => 'Using Multisite',
          'value' => ''
        ),
        'bounce_enabled' => array(
          'label' => 'Using bounce',
          'value' => ''
      )
    );

    function __construct() {
    }

    /**
     * Send data to Mixpanel by enqueuing the analytics JS file.
     * @return
     */
    public function send() {

        // Enqueue analytics Javascript.
        wp_enqueue_script('analytics', WYSIJA_URL . 'js/analytics.js', array(), WYSIJA::get_version());
        // Make analytics data available in JS.
        wp_localize_script('analytics', 'analytics_data', $this->analytics_data);
    }

    /**
     * Generate fresh data and store it in the $analytics_data Class property.
     * @return
     */
    public function generate_data() {

      foreach ($this->analytics_data as $key => $data) {
        $method = $key;
        $this->analytics_data[$key]['value'] = call_user_func(array($this, $method));
      }

    }

    /**
     * Calculate Emails sent in the last 30 days.
     * @return Int
     */
    private function monthly_emails_sent() {

        $model_email_user_stat = WYSIJA::get('email_user_stat', 'model');
        $query = 'SELECT COUNT(*) as total_emails
              FROM ' . '[wysija]' . $model_email_user_stat->table_name . '
              WHERE DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= sent_at';
        $result = $model_email_user_stat->query('get_res', $query);

        $total_emails = $result[0]['total_emails'];
        switch (true) {
          case ($total_emails <= 1000):
            $track = 'Less than 1000';
            break;
          case ((1001 <= $total_emails) && ($total_emails <= 2500)):
            $track = '1001 to 2500';
            break;
          case ((2501 <= $total_emails) && ($total_emails <= 5000)):
            $track = '2501 to 5000';
            break;
            case ((5001 <= $total_emails) && ($total_emails <= 10000)):
            $track = '5001 to 10000';
            break;
          case ((10001 <= $total_emails) && ($total_emails <= 25000)):
            $track = '10001 to 25000';
            break;
          case ((25001 <= $total_emails) && ($total_emails <= 50000)):
            $track = '25001 to 50000';
            break;
          case ((50001 <= $total_emails) && ($total_emails <= 100000)):
            $track = '50001 to 100000';
            break;
          case (100001 <= $total_emails):
            $track = 'More than 100001';
            break;
          default:
            $track = 'No emails sent';
            break;
          }

          return $track;

    }

    /*
    Pass through the WordPress version.
    */
    private function wordpress_version() {
      return get_bloginfo('version');
    }

    /*
    Pass through the WordPress language.
    */
    private function wordpress_language() {
      return get_bloginfo('language');
    }

    /*
    The plugin version.
    */
    private function plugin_version() {
      return WYSIJA::get_version();
    }

    /**
     * Calculate lists with more than 25 subscribers.
     * @return Int
     */
    private function lists_with_more_than_25() {

        $model_user_list = WYSIJA::get('user_list', 'model');
        $query = 'SELECT list_id, COUNT(*) as count
              FROM ' . '[wysija]' . $model_user_list->table_name . '
              GROUP BY list_id
              HAVING COUNT(*) >= 25';
        $result = $model_user_list->query('get_res', $query);
        $lists_count = count($result);

        return $lists_count;
    }

    /**
     * Calculate total subscribers.
     * @return Int
     */
     private function total_subcsribers() {

        $model_user = WYSIJA::get('user', 'model');
        $query = 'SELECT COUNT(*) as total_subscribers
          FROM ' . '[wysija]' . $model_user->table_name;
        $result = $model_user->query('get_res', $query);

        return $result[0]['total_subscribers'];
    }

    /**
     * Calculate confirmed subscribers.
     * @return Int
     */
    private function confirmed_subscribers() {

        $model_user = WYSIJA::get('user', 'model');
        $query = 'SELECT COUNT(*) as confirmed_subscribers
              FROM ' . '[wysija]' . $model_user->table_name . '
              WHERE  status = 1';
        $result = $model_user->query('get_res', $query);

        $confirmed_subscribers =  $result[0]['confirmed_subscribers'];
        $total_subscribers = $this->total_subcsribers();
        $confirmed_percentage = round(($confirmed_subscribers * 100) / $total_subscribers);

        return $confirmed_percentage;
    }

    /**
     *
     * @return string range eg: 0-100 101-200 2001-500
     */
    private function range_confirmed_subscribers(){
        $model_user = WYSIJA::get('user', 'model');
        $query = 'SELECT COUNT(*) as confirmed_subscribers
              FROM ' . '[wysija]' . $model_user->table_name . '
              WHERE  status = 1';
        $result = $model_user->query('get_res', $query);

        $confirmed_subscribers =  (int) $result[0]['confirmed_subscribers'];

        $ranges_increment = array( 2000 => 100, 10000 => 500, 20000 => 1000, 40000 => 2000, 100000 => 5000, 200000 => 10000, 500000 => 25000, 1000000 => 50000);

        $found_range = $this->range_finder( $confirmed_subscribers, $ranges_increment );

        return $found_range['lower'].' - '.$found_range['upper'];
    }

    private function range_finder($value, $ranges_increment){

        $limit_max = 0;
        foreach( $ranges_increment as $limit => $range_increment ){
            $small_limit = $limit_max + $range_increment;
            $limit_max = $limit;

            if( $value > $limit_max){
                continue;
            }

            while( $value >= $small_limit && $small_limit <= $limit_max ){

                if( $value > $small_limit ){
                    $min_value = $small_limit - $range_increment + 1;
                }
                if( $value == $small_limit ){
                    break;
                }
                $small_limit += $range_increment;
            }

            if( $value <= $small_limit){
                break;
            }
        }

        if( $value > $limit_max){
            return array( 'lower' => $limit_max , 'upper' => 'above' );
        }else{
            if( $value < 1 ){
                return array( 'lower' => 0 , 'upper' => 'or undefined' );
            }else{
                $min_value = $small_limit - $range_increment + 1;
                return array( 'lower' => $min_value , 'upper' => $small_limit );
            }
        }

    }

    /**
     * Calculate unconfirmed subscribers.
     * @return Int
     */
    public function unconfirmed_subscribers() {

        $model_user = WYSIJA::get('user', 'model');
        $query = 'SELECT COUNT(*) as unconfirmed_subscribers
              FROM ' . '[wysija]' . $model_user->table_name . '
              WHERE  status = 0';
        $result = $model_user->query('get_res', $query);

        return $result[0]['unconfirmed_subscribers'];
    }

    /**
     * Calculate standard newsletters total.
     * @return Int
     */
    private function standard_newsletters() {

        $model_email = WYSIJA::get('email', 'model');
        $query = 'SELECT COUNT(*) as standard_newsletters
              FROM ' . '[wysija]' . $model_email->table_name . '
              WHERE type = 1
              AND status = 2';
        $result = $model_email->query('get_res', $query);

        return $result[0]['standard_newsletters'];
    }

    /**
     * Calculate auto newsletters total.
     * @return Int
     */
    private function auto_newsletters() {

        $model_email = WYSIJA::get('email', 'model');
        $query = 'SELECT COUNT(*) as auto_newsletters
              FROM ' . '[wysija]' . $model_email->table_name . '
              WHERE  type = 2';
        $result = $model_email->query('get_res', $query);

        return $result[0]['auto_newsletters'];
    }

    /**
     * Check license type in use.
     * @return String Free | Premium
     */
    private function license_type() {

        $model_config = WYSIJA::get('config', 'model');
        $is_premium = $model_config->getValue('premium_key');

        if ($is_premium) {
            $license_type = 'Premium';
        } else {
            $license_type = 'Free';
        }

        return $license_type;
    }

    /**
     * Get sending method in use.
     * @return String
     */
    private function sending_method() {

        $model_config = WYSIJA::get('config', 'model');
        return $model_config->getValue('sending_method');
    }

    /**
     * Get smtp hostname in use.
     * @return String
     */
    private function smtp_hostname() {

        $model_config = WYSIJA::get('config', 'model');
        return $model_config->getValue('smtp_host');
    }

    /**
     * Get activation email status.
     * @return String On | Off
     */
    private function activation_email_status() {

        $model_config = WYSIJA::get('config', 'model');
        $activation_email_status = $model_config->getValue('confirm_dbleoptin');

        if ($activation_email_status === 1) {
            $result = 'On';
        } else {
            $result = 'Off';
        }

        return $result;
    }

    /**
     * Get DKIM status.
     * @return String Yes | No
     */
     private function dkim_status() {
        $model_config = WYSIJA::get('config', 'model');
        $dkim_status = $model_config->getValue('dkim_active');
        if ($dkim_status === 1) {
            $result = 'Yes';
        } else {
            $result = 'No';
        }
        return $result;
    }

    /**
     * Get subscribe in comments.
     * @return String Yes | No
     */
     private function subscribe_in_comments() {
        $model_config = WYSIJA::get('config', 'model');
        $subscribe_in_comments = $model_config->getValue('commentform');
        if ($subscribe_in_comments == 1) {
            $result = 'Yes';
        } else {
            $result = 'No';
        }
        return $result;
    }

    /**
     * Get subscribe during registration option.
     * @return String Yes | No
     */
     private function subscribe_on_register() {
        $model_config = WYSIJA::get('config', 'model');
        $subscribe_on_register = $model_config->getValue('registerform');
        if ($subscribe_on_register == 1) {
            $result = 'Yes';
        } else {
            $result = 'No';
        }
        return $result;
    }

    /**
     * Get view in browser option.
     * @return String Yes | No
     */
     private function browser_link() {
        $model_config = WYSIJA::get('config', 'model');
        $browser_link = $model_config->getValue('viewinbrowser');
        if ($browser_link == 1) {
            $result = 'Yes';
        } else {
            $result = 'No';
        }
        return $result;
    }

    /**
     * Get profile edit option.
     * @return String Yes | No
     */
     private function profile_edit() {
        $model_config = WYSIJA::get('config', 'model');
        $profile_edit = $model_config->getValue('manage_subscriptions');
        if ($profile_edit == 1) {
            $result = 'Yes';
        } else {
            $result = 'No';
        }
        return $result;
    }

    /**
     * Get html edit in newsletter option.
     * @return String Yes | No
     */
     private function html_edit() {
        $model_config = WYSIJA::get('config', 'model');
        $html_edit = $model_config->getValue('html_source');
        if ($html_edit == 1) {
            $result = 'Yes';
        } else {
            $result = 'No';
        }
        return $result;
    }

    /**
     * Get mailpoet cron option.
     * @return String Yes | No
     */
     private function mailpoet_cron() {
        $model_config = WYSIJA::get('config', 'model');
        $cron_option = $model_config->getValue('cron_manual');
        if ($cron_option == 1) {
            $result = 'Yes';
        } else {
            $result = 'No';
        }
        return $result;
    }

    /**
     * Calculate average open rate.
     * @return Int
     */
    private function average_open_rate() {

        $model_email_user_stat = WYSIJA::get('email_user_stat', 'model');
        $query = 'SELECT COUNT(*) as opened_emails
              FROM ' . '[wysija]' . $model_email_user_stat->table_name . '
              WHERE status = 1';
        $result = $model_email_user_stat->query('get_res', $query);

        $opened_emails = $result[0]['opened_emails'];
        $total_emails = $this->total_emails_sent();

        if ($total_emails == 0) {
            $average_open_rate = 0;
        } else {
            $average_open_rate = round(($opened_emails * 100) / $total_emails);
        }

        return $average_open_rate;
    }

    /**
     * Calculate average click rate.
     * @return String opened/total
     */
    private function average_click_rate() {

        $model_email_user_stat = WYSIJA::get('email_user_stat', 'model');
        $query = 'SELECT COUNT(*) as clicked_emails
              FROM ' . '[wysija]' . $model_email_user_stat->table_name . '
              WHERE status = 2';
        $result = $model_email_user_stat->query('get_res', $query);

        $clicked_emails = $result[0]['clicked_emails'];
        $total_emails = $this->total_emails_sent();

        if ($total_emails == 0) {
            $average_click_rate = 0;
        } else {
            $average_click_rate = round(($clicked_emails * 100) / $total_emails);
        }

        return $average_click_rate;
    }

    /**
     * Get all emails sent.
     * @return Int
     */
    private function total_emails_sent() {

        $model_email_user_stat = WYSIJA::get('email_user_stat', 'model');
        $query = 'SELECT COUNT(*) as all_emails
                FROM ' . '[wysija]' . $model_email_user_stat->table_name . '';
        $result = $model_email_user_stat->query('get_res', $query);

        return $result[0]['all_emails'];
    }

    /**
     * Get total number of forms.
     * @return Int
     */
    private function forms_number() {

        $model_form = WYSIJA::get('forms', 'model');
        $query = 'SELECT COUNT(*) as forms_number
                FROM ' . '[wysija]' . $model_form->table_name . '';
        $result = $model_form->query('get_res', $query);

        return $result[0]['forms_number'];
    }

    /**
     * Get Industry specified in the settings page.
     * @return String
     */
    public function industry() {
        $model_config = WYSIJA::get('config', 'model');
        return $model_config->getValue('industry');
    }

    /**
     * Get if is using right to left language.
     * @return String
     */
    private function rtl() {

        if (is_rtl()) {
            $is_rtl = 'Yes';
        } else {
            $is_rtl = 'No';
        }

        return $is_rtl;
    }

    /**
    * Check if it's multisite.
     * @return String
     */
    private function is_multisite() {

        if (is_multisite()) {
          $is_multisite = 'Yes';
        } else {
          $is_multisite = 'No';
        }

        return $is_multisite;
    }

    /**
     * Get if is using beta mode
     * @return String
     */
    private function beta() {

        $model_config = WYSIJA::get('config', 'model');

        if ($model_config->getValue('beta_mode')) {
            $is_beta = 'Yes';
        } else {
            $is_beta = 'No';
        }

        return $is_beta;
    }

    /**
     * Checks if user used the archive page feature.
     * @return String true | false
     */
    private function archive_page() {

        $model_config = WYSIJA::get('config', 'model');
        $archive_lists = $model_config->getValue('archive_lists');

        if (!empty($archive_lists)) {
            $used_archive = 'true';
        } else {
            $used_archive = 'false';
        }

        return $used_archive;
    }

    /*
    Check if user is using custom fields or not.
    # => Yes | No
    */
    private function using_custom_fields() {
      $fields = WJ_Field::get_all();
      if ($fields != null) {
        $result = 'Yes';
      } else {
        $result = 'No';
      }
      return $result;
    }

    /*
    How many input custom fields are in use.
    # => int | null
    */
    private function custom_fields_input() {
      global $wpdb;
      $field = new WJ_Field();
      $table_name =  $field->get_table_name();
      $result = $wpdb->get_var(
        "SELECT COUNT(*) as inputs
        FROM  $table_name
        WHERE type = 'input'"
      );
      if ($result == null) {
        $result = '0';
      }
      return $result;
    }

    /*
    How many textarea custom fields are in use.
    # => int | null
    */
    private function custom_fields_textarea() {
      global $wpdb;
      $field = new WJ_Field();
      $table_name =  $field->get_table_name();
      $result = $wpdb->get_var(
        "SELECT COUNT(*) as textareas
        FROM  $table_name
        WHERE type = 'textarea'"
      );
      if ($result == null) {
        $result = '0';
      }
      return $result;
    }

    /*
    How many select custom fields are in use.
    # => int | null
    */
    private function custom_fields_select() {
      global $wpdb;
      $field = new WJ_Field();
      $table_name =  $field->get_table_name();
      $result = $wpdb->get_var(
        "SELECT COUNT(*) as selects
        FROM  $table_name
        WHERE type = 'select'"
      );
      if ($result == null) {
        $result = '0';
      }
      return $result;
    }

    /*
    How many checkbox custom fields are in use.
    # => int | null
    */
    private function custom_fields_checkbox() {
      global $wpdb;
      $field = new WJ_Field();
      $table_name =  $field->get_table_name();
      $result = $wpdb->get_var(
        "SELECT COUNT(*) as checkboxes
        FROM  $table_name
        WHERE type = 'checkbox'"
      );
      if ($result == null) {
        $result = '0';
      }
      return $result;
    }

    /*
    How many checkbox custom fields are in use.
    # => int | null
    */
    private function custom_fields_radio() {
      global $wpdb;
      $field = new WJ_Field();
      $table_name =  $field->get_table_name();
      $result = $wpdb->get_var(
        "SELECT COUNT(*) as radios
        FROM  $table_name
        WHERE type = 'radio'"
      );
      if ($result == null) {
        $result = '0';
      }
      return $result;
    }

    /*
    Check if user has been active in the last week.
    This means he sent at least one email.
    # => Yes | No
    */
    private function active_last_week() {
      global $wpdb;
      $model_stats = WYSIJA::get('email_user_stat', 'model');
      $table_name = '[wysija]' . $model_stats->table_name;

      $query = 'SELECT COUNT(*) as activities
      FROM ' . $table_name .
      ' WHERE sent_at > UNIX_TIMESTAMP(date_sub(now(), interval 1 week))';

      $result = $model_stats->query('get_res', $query);
      $result = $result[0]['activities'];

      if ($result > 0) {
        return 'Yes';
      }
      return 'No';
    }

	/**
	 * Check PHP versions
	 * @return string
	 */
	private function php_version() {
		$php_version_factors = explode('.', phpversion());
		$main_version		 = (int)$php_version_factors[0];
		$sub_version		 = isset($php_version_factors[1]) ? (int)$php_version_factors[1] : null;

		$php_version = 'others';
		if ($sub_version !== null) {
			if ($main_version == 4) {
				$php_version = '4.x';
			} elseif ($main_version >= 5) {
				$php_version = implode('.', array( $main_version, $sub_version ));
			}
		}
		return $php_version;
	}


    /**
     * Check if bounce is enabled
     * @return string
     */
    private function bounce_enabled() {
      $multisite_prefix = '';
      if ( is_multisite() ) {
        $multisite_prefix = 'ms_';
      }
      $model_config = WYSIJA::get('config', 'model');
      return ($model_config->getValue(
          $multisite_prefix . 'bounce_process_auto')
        ) ? "Yes" : "No";
    }
}
