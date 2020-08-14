<?php

defined('YWQA_CUSTOM_POST_TYPE_NAME') || define('YWQA_CUSTOM_POST_TYPE_NAME', 'question_answer');

//region    ***************  METAKEY name definition    *******************

defined('YWQA_METAKEY_DISCUSSION_VOTES') || define('YWQA_METAKEY_DISCUSSION_VOTES', '_ywqa_discussion_votes');
defined('YWQA_METAKEY_DISCUSSION_UPVOTES') || define('YWQA_METAKEY_DISCUSSION_UPVOTES', '_ywqa_discussion_upvote_count');
defined('YWQA_METAKEY_DISCUSSION_DOWNVOTES') || define('YWQA_METAKEY_DISCUSSION_DOWNVOTES', '_ywqa_discussion_downvote_count');
defined('YWQA_METAKEY_ANSWER_ABUSE_REPORTS') || define('YWQA_METAKEY_ANSWER_ABUSE_REPORTS', '_ywqa_answer_abuse_reports');
defined('YWQA_METAKEY_ANSWER_ABUSE_COUNT') || define('YWQA_METAKEY_ANSWER_ABUSE_COUNT', '_ywqa_answer_abuse_count');
defined('YWQA_METAKEY_NOTIFY_USER') || define('YWQA_METAKEY_NOTIFY_USER', '_ywqa_notify_user');
defined('YWQA_METAKEY_PRODUCT_ID') || define('YWQA_METAKEY_PRODUCT_ID', '_ywqa_product_id');
defined('YWQA_METAKEY_DISCUSSION_TYPE') || define('YWQA_METAKEY_DISCUSSION_TYPE', '_ywqa_type');
defined('YWQA_METAKEY_VERSION') || define('YWQA_METAKEY_VERSION', '_ywqa_version');
defined('YWQA_METAKEY_DISCUSSION_AUTHOR_ID') || define('YWQA_METAKEY_DISCUSSION_AUTHOR_ID', '_ywqa_discussion_author_id');
defined('YWQA_METAKEY_DISCUSSION_AUTHOR_NAME') || define('YWQA_METAKEY_DISCUSSION_AUTHOR_NAME', '_ywqa_discussion_author_name');
defined('YWQA_METAKEY_DISCUSSION_AUTHOR_EMAIL') || define('YWQA_METAKEY_DISCUSSION_AUTHOR_EMAIL', '_ywqa_discussion_author_email');
//endregion

if (!function_exists('ywqa_strip_trim_text')) {
    /**
     * Strip html tags from a text and trim to fixed length
     *
     * @param string $text text to be shown
     * @param int $chars Number of characheters to show before the ellipses
     *
     * @return string
     */
    function ywqa_strip_trim_text($text, $chars = 50) {
        return wc_trim_string(wp_strip_all_tags($text), $chars);
    }
}

if (!function_exists('yith_number_to_letter')) {

    /**
     * Takes a number and converts it to a-z,aa-zz,aaa-zzz, etc with uppercase option
     *
     * @access    public
     *
     * @param int $num number to convert
     * @param bool $uppercase return uppercase string or not
     *
     * @return    string    letters from number input
     */
    function yith_number_to_letter($num, $uppercase = false) {
        $num -= 1;

        $letter = chr(($num % 26) + 97);
        $letter .= (floor($num / 26) > 0) ? str_repeat($letter, floor($num / 26)) : '';

        return ($uppercase ? strtoupper($letter) : $letter);
    }
}

if ( ! function_exists( 'yith_ywqa_premium_install' ) ) {

	function yith_ywqa_premium_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywqa_premium_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_ywqa_premium_init' );
		}
	}

	add_action( 'plugins_loaded', 'yith_ywqa_premium_install', 11 );
}


if ( ! function_exists( 'YWQA' ) ) {

	function YWQA() {
		_deprecated_function( 'YWQA', '1.1.23', 'YITH_YWQA' );

		return YITH_WooCommerce_Question_Answer_Premium::get_instance();
	}
}

if ( ! function_exists( 'YITH_YWQA' ) ) {

	function YITH_YWQA() {

		return YITH_WooCommerce_Question_Answer_Premium::get_instance();
	}
}

if ( ! function_exists( 'yith_ywqa_premium_install_woocommerce_admin_notice' ) ) {

	function yith_ywqa_premium_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'YITH WooCommerce Questions and Answers is enabled but not effective. It requires WooCommerce in order to work.', 'yit' ); ?></p>
		</div>
		<?php
	}
}