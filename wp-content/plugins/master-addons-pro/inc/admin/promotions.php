<?php

namespace MasterAddons\Admin\Promotions;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/4/19
 */

if (!defined('ABSPATH')) {
    exit;
} // No, Direct access Sir !!!

if (!class_exists('Master_Addons_Promotions')) {
    class Master_Addons_Promotions
    {

        private static $instance = null;

        public static function get_instance()
        {
            if (!self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct()
        {

            // Admin Notices
            add_action('admin_init', [$this, 'jltma_admin_notice_init']);

            //Notices
            add_action('admin_notices', [$this, 'jltma_latest_update_details'], 10);

           

                //Black Friday & Cyber Monday Offer
                add_action('admin_notices', [$this, 'jltma_black_friday_cyber_monday_deals'], 10);
                add_action('admin_notices', [$this, 'jltma_request_review_after_seven_days'], 10);
                add_action('admin_notices', [$this, 'jltma_request_review_after_fifteen_days'], 10);
                add_action('admin_notices', [$this, 'jltma_request_review_after_thirty_days'], 10);
          

            // Styles
            add_action('admin_print_styles', [$this, 'jltma_admin_notice_styles']);
        }

        public function jltma_admin_notice_init()
        {
            add_action('wp_ajax_dismiss_admin_notice', [$this, 'jltma_dismiss_admin_notice']);
        }

        public function jltma_latest_update_details()
        {
            if (!self::is_admin_notice_active('jltma-disable-update-notice-forever')) {
                return;
            }

            $blog_update_message = sprintf(
                __('%1$s got <strong>Huge Updates</strong> %2$s %3$s %4$s %5$s %6$s <br> <strong>Check Changelogs for </strong> <a href="%7$s" target="__blank">%8$s</a>', MELA_TD),

                '<strong>' . esc_html__('Master Addons for Elementor v', MELA_TD) . MELA_VERSION . '</strong>',
                '<br><br>' . __('✅ Fully re-coded with optimized codes', MELA_TD) . '<br>',
                __('✅ <b>Gallery Slider</b> updated ', MELA_TD) . '<br>',
                __('✅ All Slick Slider Library updated with Swiper Slider', MELA_TD) . '<br>',
                __('✅ Blog and Filterable Gallery Updated', MELA_TD) . '<br>',
                // __( '✅ <b>Updated:</b> Animated Headlines, Creative Buttons, Team Members, Infobox, Progressbar etc', MELA_TD ) . '<br>',
                __('✅ Latest WordPress v5.6 Elementor Compatibility & better UX', MELA_TD) . '<br>',
                esc_url_raw('https://master-addons.com/changelogs/'),
                esc_html__('More Details', MELA_TD)
            );

            printf('<div data-dismissible="jltma-disable-update-notice-forever" class="jltma-admin-notice updated notice notice-success is-dismissible"><p>%1$s</p></div>', $blog_update_message);
        }

        public function jltma_dismiss_admin_notice()
        {
            $option_name        = sanitize_text_field($_POST['option_name']);
            $dismissible_length = sanitize_text_field($_POST['dismissible_length']);

            if ('forever' != $dismissible_length) {
                // If $dismissible_length is not an integer default to 1
                $dismissible_length = (0 == absint($dismissible_length)) ? 1 : $dismissible_length;
                $dismissible_length = strtotime(absint($dismissible_length) . ' days');
            }

            check_ajax_referer('dismissible-notice', 'nonce');
            self::set_admin_notice_cache($option_name, $dismissible_length);
            wp_die();
        }

        public static function set_admin_notice_cache($id, $timeout)
        {
            $cache_key = 'jltma-admin-notice-' . md5($id);
            update_site_option($cache_key, $timeout);

            return true;
        }

        public static function is_admin_notice_active($arg)
        {
            $array       = explode('-', $arg);
            $length      = array_pop($array);
            $option_name = implode('-', $array);
            $db_record   = self::get_admin_notice_cache($option_name);

            if ('forever' == $db_record) {
                return false;
            } elseif (absint($db_record) >= time()) {
                return false;
            } else {
                return true;
            }
        }

        public static function get_admin_notice_cache($id = false)
        {
            if (!$id) {
                return false;
            }

            $cache_key = 'jltma-admin-notice-' . md5($id);
            $timeout   = get_site_option($cache_key);
            $timeout   = 'forever' === $timeout ? time() + 45 : $timeout;

            if (empty($timeout) || time() > $timeout) {
                return false;
            }

            return $timeout;
        }

        public function jltma_admin_notice_styles()
        { ?>
            <style type="text/css">
                .master-addons-review-notice .notice-dismiss {
                    padding: 0 0 0 26px
                }

                .master-addons-review-notice .notice-dismiss:before {
                    display: none
                }

                .master-addons-review-notice.master-addons-review-notice {
                    padding: 10px 10px 10px 0;
                    background-color: #fff;
                    border-radius: 3px;
                    border-left: 4px solid transparent
                }

                .master-addons-review-notice .master-addons-review-thumbnail {
                    width: 114px;
                    float: left;
                    line-height: 80px;
                    text-align: center;
                    border-right: 4px solid transparent
                }

                .master-addons-review-notice .master-addons-review-thumbnail img {
                    width: 60px;
                    vertical-align: middle
                }

                .master-addons-review-notice .master-addons-review-text {
                    overflow: hidden
                }

                .master-addons-review-notice .master-addons-review-text h3 {
                    font-size: 24px;
                    margin: 0 0 5px;
                    font-weight: 400;
                    line-height: 1.3
                }

                .master-addons-review-notice .master-addons-review-text p {
                    font-size: 13px;
                    margin: 0 0 5px
                }

                .master-addons-review-notice .master-addons-review-ul {
                    margin: 0;
                    padding: 0
                }

                .master-addons-review-notice .master-addons-review-ul li {
                    display: inline-block;
                    margin-right: 15px
                }

                .master-addons-review-notice .master-addons-review-ul li a {
                    display: inline-block;
                    color: #4b00e7;
                    text-decoration: none;
                    padding-left: 26px;
                    position: relative
                }

                .master-addons-review-notice .master-addons-review-ul li a span {
                    position: absolute;
                    left: 0;
                    top: -2px
                }
            </style>
        <?php }

        public function jltma_get_total_interval($interval, $type)
        {
            switch ($type) {
                case 'years':
                    return $interval->format('%Y');
                    break;
                case 'months':
                    $years = $interval->format('%Y');
                    $months = 0;
                    if ($years) {
                        $months += $years * 12;
                    }
                    $months += $interval->format('%m');
                    return $months;
                    break;
                case 'days':
                    return $interval->format('%a');
                    break;
                case 'hours':
                    $days = $interval->format('%a');
                    $hours = 0;
                    if ($days) {
                        $hours += 24 * $days;
                    }
                    $hours += $interval->format('%H');
                    return $hours;
                    break;
                case 'minutes':
                    $days = $interval->format('%a');
                    $minutes = 0;
                    if ($days) {
                        $minutes += 24 * 60 * $days;
                    }
                    $hours = $interval->format('%H');
                    if ($hours) {
                        $minutes += 60 * $hours;
                    }
                    $minutes += $interval->format('%i');
                    return $minutes;
                    break;
                case 'seconds':
                    $days = $interval->format('%a');
                    $seconds = 0;
                    if ($days) {
                        $seconds += 24 * 60 * 60 * $days;
                    }
                    $hours = $interval->format('%H');
                    if ($hours) {
                        $seconds += 60 * 60 * $hours;
                    }
                    $minutes = $interval->format('%i');
                    if ($minutes) {
                        $seconds += 60 * $minutes;
                    }
                    $seconds += $interval->format('%s');
                    return $seconds;
                    break;
                case 'milliseconds':
                    $days = $interval->format('%a');
                    $seconds = 0;
                    if ($days) {
                        $seconds += 24 * 60 * 60 * $days;
                    }
                    $hours = $interval->format('%H');
                    if ($hours) {
                        $seconds += 60 * 60 * $hours;
                    }
                    $minutes = $interval->format('%i');
                    if ($minutes) {
                        $seconds += 60 * $minutes;
                    }
                    $seconds += $interval->format('%s');
                    $milliseconds = $seconds * 1000;
                    return $milliseconds;
                    break;
                default:
                    return NULL;
            }
        }

        public function jltma_days_differences()
        {

            $install_date = get_option('jltma_activation_time');
            // $install_date = strtotime('2020-11-16 14:39:05'); // Testing datetime
            $jltma_date_format = 'Y-m-d H:i:s';
            $jltma_datetime1 = \DateTime::createFromFormat('U', $install_date);
            $jltma_datetime2 = \DateTime::createFromFormat('U', strtotime("now"));

            $interval = $jltma_datetime2->diff($jltma_datetime1);
            $jltma_days_diff = $this->jltma_get_total_interval($interval, 'days');
            return $jltma_days_diff;
        }

        public function jltma_admin_upgrade_pro_notice($notice_key)
        {
           
                return;
            
        ?>
            <div data-dismissible="<?php echo esc_attr($notice_key); ?>" id="<?php echo esc_attr($notice_key); ?>" class="jltma-admin-notice updated notice notice-success is-dismissible">
                <div id="master-addons-upgrade-pro-notice" class="master-addons-review-notice">
                    <?php
                    $jltma_upsell_notice = sprintf(
                        __('%1$s <strong>%2$s</strong> %3$s <strong>ENJOY25</strong>', MELA_TD),
                        __("We’re with you! We're offering", MELA_TD),
                        __('25% Discount', MELA_TD),
                        __(" on all pricing due to the impact of COVID-19! Coupon Code: ", MELA_TD)
                    );
                    printf(
                        '%1$s <a href="%2$s" target="_blank">%3$s</a> %4$s',
                        $jltma_upsell_notice,
                       "",
                        __('Upgrade Pro', MELA_TD),
                        __('now', MELA_TD)
                    );
                    ?>
                </div>
            </div>
        <?php }


        // Black Friday & Cyber Monday Offer
        public function jltma_admin_bf_cm_upgrade_pro_notice($notice_key)
        {
             return;
            
        ?>

            <div data-dismissible="<?php echo esc_attr($notice_key); ?>" id="<?php echo esc_attr($notice_key); ?>" class="jltma-admin-notice updated notice notice-success is-dismissible">
                <div id="master-addons-bfcm-upgrade-notice" class="master-addons-review-notice">
                    <div class="master-addons-review-thumbnail">
                        <img src="<?php echo  esc_url(MELA_IMAGE_DIR) . 'logo.png' ?>" alt="Master Addons">
                    </div>
                    <div class="master-addons-review-text">
                        <h3><?php _e('<strong>Black Friday & Cyber Monday</strong> Deals - <strong>50% Off</strong> !', MELA_TD) ?></h3>
                        <p><?php _e('We\'re offering <strong>HUGE 50% Discount</strong> on all plans on this Black Friday & Cyber Monday. Valid till <strong>30th November, 2020</strong>. Coupon Code: <strong>BLACKFRIDAY50</strong> <a href="' . ma_el_fs()->get_upgrade_url() . '" target="_blank"><strong>Upgrade to Pro</strong></a>', MELA_TD) ?></p>

                        <ul class="master-addons-review-ul">
                            <li><a href="<?php echo esc_url_raw(ma_el_fs()->get_upgrade_url()); ?>" target="_blank"><span class="dashicons dashicons-external"></span><?php _e('Upgrade Now', MELA_TD) ?></a></li>
                            <li><a href="#" class="notice-dismiss"><span class="dashicons dashicons-smiley"></span><?php _e('I\'ve already left a review', MELA_TD) ?></a></li>
                            <li><a href="#" class="notice-dismiss"><span class="dashicons dashicons-dismiss"></span><?php _e('Never show again', MELA_TD) ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>


        <?php }


        public function jltma_admin_notice_ask_for_review($notice_key)
        {
          
                return;
            
        ?>

            <div data-dismissible="<?php echo esc_attr($notice_key); ?>" id="<?php echo esc_attr($notice_key); ?>" class="jltma-admin-notice updated notice notice-success is-dismissible">
                <div id="master-addons-review-notice" class="master-addons-review-notice">
                    <div class="master-addons-review-thumbnail">
                        <img src="<?php echo  esc_attr(MELA_IMAGE_DIR) . 'logo.png' ?>" alt="Master Addons">
                    </div>
                    <div class="master-addons-review-text">

                        <h3><?php _e('Enjoying <strong>Master Addons</strong>?', MELA_TD) ?></h3>
                        <p><?php _e('Seems like you are enjoying <strong>Master Addons</strong>. Would you please show us a little Love by rating us in the <a href="https://wordpress.org/support/plugin/master-addons/reviews/#postform" target="_blank"><strong>WordPress.org</strong></a>?', MELA_TD) ?></p>

                        <ul class="master-addons-review-ul">
                            <li><a href="https://wordpress.org/support/plugin/master-addons/reviews/#postform" target="_blank"><span class="dashicons dashicons-external"></span><?php _e('Sure! I\'d love to!', MELA_TD) ?></a></li>
                            <li><a href="#" class="notice-dismiss"><span class="dashicons dashicons-smiley"></span><?php _e('I\'ve already left a review', MELA_TD) ?></a></li>
                            <li><a href="#" class="notice-dismiss"><span class="dashicons dashicons-dismiss"></span><?php _e('Never show again', MELA_TD) ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

<?php }


        public function jltma_request_review_after_seven_days()
        {
            $jltma_seven_day_notice = $this->jltma_days_differences();
            if ($jltma_seven_day_notice >= 7 && $jltma_seven_day_notice < 15) {
                $this->jltma_admin_notice_ask_for_review('jltma-days-72');
            }
        }

        public function jltma_request_review_after_ten_days()
        {
            $jltma_seven_to_ten_days = $this->jltma_days_differences();
            if ($jltma_seven_to_ten_days > 7 && $jltma_seven_to_ten_days < 10) {
                $this->jltma_admin_upgrade_pro_notice('jltma-days-10');
            }
        }

        public function jltma_request_review_after_fifteen_days()
        {
            $jltma_fifteen_day_notice = $this->jltma_days_differences();
            if ($jltma_fifteen_day_notice > 7 && $jltma_fifteen_day_notice < 15) {
                $this->jltma_admin_notice_ask_for_review('jltma-days-15');
            }
        }

        public function jltma_request_review_after_tweenty_days()
        {
            $jltma_tweenty_day_notice = $this->jltma_days_differences();
            if ($jltma_tweenty_day_notice > 20) {
                $this->jltma_admin_upgrade_pro_notice('jltma-days-20');
            }
        }

        public function jltma_request_review_after_thirty_days()
        {
            $jltma_thirty_day_notice = $this->jltma_days_differences();
            if ($jltma_thirty_day_notice > 30) {
                $this->jltma_admin_notice_ask_for_review('jltma-days-30');
            }
        }


        public function jltma_request_review_after_fourty_five_days()
        {
            $jltma_fourtyfive_day_notice = $this->jltma_days_differences();
            if ($jltma_fourtyfive_day_notice > 45) {
                $this->jltma_admin_upgrade_pro_notice('jltma-days-45');
            }
        }

        public function jltma_request_review_after_ninety_days()
        {
            $jltma_ninety_day_notice = $this->jltma_days_differences();
            if ($jltma_ninety_day_notice > 90) {
                $this->jltma_admin_upgrade_pro_notice('jltma-days-90');
            }
        }

        public function jltma_black_friday_cyber_monday_deals()
        {
            $today = date("Y-m-d");
            $expire = '2020-12-30';
            $today_time = strtotime($today);
            $expire_time = strtotime($expire);
            if ($expire_time >= $today_time) {
                $this->jltma_admin_bf_cm_upgrade_pro_notice('jltma-bfcm-2021-forever');
            }
        }
    }

    Master_Addons_Promotions::get_instance();
}
