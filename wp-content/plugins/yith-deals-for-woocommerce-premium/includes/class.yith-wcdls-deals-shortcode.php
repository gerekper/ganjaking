<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if (!defined('YITH_WCDLS_PATH')) {
    exit('Direct access forbidden.');
}
/**
 *
 *
 * @class      YITH_WCDLS_Deals_Shortcodes
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
 *
 */
if (!class_exists('YITH_WCDLS_Deals_Shortcodes')) {
    /**
     * Class YITH_WCDLS_Deals_Shortcodes
     *
     * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCDLS_Deals_Shortcodes
    {

        public static function init()
        {
            $shortcodes = array(
                'yith_wcdls_accept_offer' => __CLASS__ . '::yith_wcdls_accept_offer',
                'yith_wcdls_decline_offer' => __CLASS__ . '::yith_wcdls_decline_offer'
            );

            foreach ($shortcodes as $shortcode => $function) {
                add_shortcode($shortcode, $function);
            }

           // shortcode_atts( array('id' => ''), array(), 'yith_auction_show_list_bid');

        }

        /**
         * ShortCode for accept button
         *
         * @since 1.0.0
         */
        public static function yith_wcdls_accept_offer($atts)
        {
            ob_start();
            ?>
            <input type="submit" class="button alt yith-wcdls-accept" name="" id="" style='' value="<?php esc_html_e('Accept offer','yith-deals-for-woocommerce'); ?> ">
            <?php

            return ob_get_clean();
        }

        /**
         * ShortCode for decline button
         *
         * @since 1.0.0
         */
        public static  function yith_wcdls_decline_offer($atts) {
            ob_start();
            ?>
            
            <input type="submit" class="button yith-wcdls-decline" name="" id="" style='' value="<?php esc_html_e('Decline offer','yith-deals-for-woocommerce'); ?>">

            <?php

            return  ob_get_clean();
        }
    }
}