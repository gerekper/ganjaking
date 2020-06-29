<?php

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

defined ( 'YITH_YWAR_POST_TYPE' ) || define ( 'YITH_YWAR_POST_TYPE', 'ywar_reviews' );

defined ( 'YITH_YWAR_META_KEY_RATING' ) || define ( 'YITH_YWAR_META_KEY_RATING', '_ywar_rating' );
defined ( 'YITH_YWAR_META_KEY_PRODUCT_ID' ) || define ( 'YITH_YWAR_META_KEY_PRODUCT_ID', '_ywar_product_id' );
defined ( 'YITH_YWAR_META_IMPORTED' ) || define ( 'YITH_YWAR_META_IMPORTED', '_ywar_imported' );
defined ( 'YITH_YWAR_META_APPROVED' ) || define ( 'YITH_YWAR_META_APPROVED', '_ywar_approved' );
defined ( 'YITH_YWAR_META_THUMB_IDS' ) || define ( 'YITH_YWAR_META_THUMB_IDS', '_ywar_thumb_ids' );
defined ( 'YITH_YWAR_META_COMMENT_ID' ) || define ( 'YITH_YWAR_META_COMMENT_ID', '_ywar_comment_id' );
defined ( 'YITH_YWAR_META_KEY_INAPPROPRIATE_LIST' ) || define ( 'YITH_YWAR_META_KEY_INAPPROPRIATE_LIST', '_ywar_inappropriate_list' );
defined ( 'YITH_YWAR_META_KEY_INAPPROPRIATE_COUNT' ) || define ( 'YITH_YWAR_META_KEY_INAPPROPRIATE_COUNT', '_ywar_inappropriate_count' );
defined ( 'YITH_YWAR_META_KEY_FEATURED' ) || define ( 'YITH_YWAR_META_KEY_FEATURED', '_ywar_featured' );
defined ( 'YITH_YWAR_META_UPVOTES_COUNT' ) || define ( 'YITH_YWAR_META_UPVOTES_COUNT', '_ywar_upvotes_count' );
defined ( 'YITH_YWAR_META_DOWNVOTES_COUNT' ) || define ( 'YITH_YWAR_META_DOWNVOTES_COUNT', '_ywar_downvotes_count' );
defined ( 'YITH_YWAR_META_VOTES' ) || define ( 'YITH_YWAR_META_VOTES', '_ywar_votes' );
defined ( 'YITH_YWAR_META_STOP_REPLY' ) || define ( 'YITH_YWAR_META_STOP_REPLY', '_ywar_stop_reply' );
defined ( 'YITH_YWAR_META_REVIEW_USER_ID' ) || define ( 'YITH_YWAR_META_REVIEW_USER_ID', '_ywar_review_user_id' );
defined ( 'YITH_YWAR_META_REVIEW_AUTHOR' ) || define ( 'YITH_YWAR_META_REVIEW_AUTHOR', '_ywar_review_author' );
defined ( 'YITH_YWAR_META_REVIEW_AUTHOR_CUSTOM' ) || define ( 'YITH_YWAR_META_REVIEW_AUTHOR_CUSTOM', '_ywar_review_author_custom' );
defined ( 'YITH_YWAR_META_REVIEW_AUTHOR_EMAIL' ) || define ( 'YITH_YWAR_META_REVIEW_AUTHOR_EMAIL', '_ywar_review_author_email' );
defined ( 'YITH_YWAR_META_REVIEW_AUTHOR_URL' ) || define ( 'YITH_YWAR_META_REVIEW_AUTHOR_URL', '_ywar_review_author_url' );
defined ( 'YITH_YWAR_META_REVIEW_AUTHOR_IP' ) || define ( 'YITH_YWAR_META_REVIEW_AUTHOR_IP', '_ywar_review_author_IP' );
defined ( 'YITH_YWAR_META_REVIEW_BLOCK_EDIT' ) || define ( 'YITH_YWAR_META_REVIEW_BLOCK_EDIT', '_ywar_review_edit_blocked' );

defined ( 'YITH_YWAR_ACTION_APPROVE_REVIEW' ) || define ( 'YITH_YWAR_ACTION_APPROVE_REVIEW', 'approve-review' );
defined ( 'YITH_YWAR_ACTION_UNTRASH_REVIEW' ) || define ( 'YITH_YWAR_ACTION_UNTRASH_REVIEW', 'untrash-review' );
defined ( 'YITH_YWAR_ACTION_TRASH_REVIEW' ) || define ( 'YITH_YWAR_ACTION_TRASH_REVIEW', 'trash' );
defined ( 'YITH_YWAR_ACTION_DELETE_REVIEW' ) || define ( 'YITH_YWAR_ACTION_DELETE_REVIEW', 'delete' );
defined ( 'YITH_YWAR_ACTION_UNAPPROVE_REVIEW' ) || define ( 'YITH_YWAR_ACTION_UNAPPROVE_REVIEW', 'unapprove-review' );

defined ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_CONTENT' ) || define ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_CONTENT', 'review-text' );
defined ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_RATING' ) || define ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_RATING', 'review-rating' );
defined ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_DATE' ) || define ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_DATE', 'review-date' );
defined ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_AUTHOR' ) || define ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_AUTHOR', 'review-author' );
defined ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_PRODUCT' ) || define ( 'YITH_YWAR_TABLE_COLUMN_REVIEW_PRODUCT', 'review-product' );

/**
 * Check if a jetpack module is currently active and try disabling before activating this one
 */
if ( function_exists ( 'yith_deactive_jetpack_module' ) ) {
    global $yith_jetpack_1;
    yith_deactive_jetpack_module ( $yith_jetpack_1, 'YITH_YWAR_PREMIUM', plugin_basename ( __FILE__ ) );
}

if ( ! function_exists ( 'is_plugin_active' ) ) {
    if ( !function_exists( 'get_plugins' ) ) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
}

if ( ! function_exists ( 'yith_initialize_plugin_fw' ) ) {
    /**
     * Initialize the YITH Plugin FW
     *
     * @param string $plugin_dir the YITH Plugin FW starting path
     *
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    function yith_initialize_plugin_fw ( $plugin_dir ) {
        if ( ! function_exists ( 'yit_deactive_free_version' ) ) {
            require_once $plugin_dir . 'plugin-fw/yit-deactive-plugin.php';
        }

        if ( ! function_exists ( 'yith_plugin_registration_hook' ) ) {
            require_once $plugin_dir . 'plugin-fw/yit-plugin-registration-hook.php';
        }

        /* Plugin Framework Version Check */
        if ( ! function_exists ( 'yit_maybe_plugin_fw_loader' ) && file_exists ( $plugin_dir . 'plugin-fw/init.php' ) ) {
            require_once ( $plugin_dir . 'plugin-fw/init.php' );
        }
    }
}

if ( ! function_exists ( 'yith_ywar_install_woocommerce_admin_notice' ) ) {
    /**
     * Show a notice when WooCommerce is not enabled
     *
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    function yith_ywar_install_woocommerce_admin_notice () {
        ?>
        <div class="error">
            <p><?php _e ( 'YITH WooCommerce Advanced Reviews is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-advanced-reviews' ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists ( ' yith_maybe_script_minified_path' ) ) {
    /**
     * Return the path to a minified script file, if exists
     *
     * @param $script_path string script path, without extension
     *
     * @return string the path to the resource to use
     */
    function yith_maybe_script_minified_path ( $script_path ) {
        $maintenance = isset( $_GET[ "script_debug_on" ] );
        $suffix      = ( defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || $maintenance ? '' : '.min';

        return $script_path . $suffix . '.js';
    }
}

if ( ! function_exists ( 'yith_ywar_notify_product_reviews_update' ) ) {
    /**
     * When a product review change its status, delete the transient that store the values relative to the ratings
     *
     * @param int $product_id the product id
     *
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    function yith_ywar_notify_product_reviews_update ( $product_id ) {
        if ( ! $product_id ) {
            return;
        }

        do_action ( 'yith_ywar_product_reviews_updated', $product_id );
    }
}

if ( ! function_exists ( 'yith_ywar_notify_review_update' ) ) {
    /**
     * When a specific review changes, delete the transient that store the values relative to the relative product ratings
     *
     * @param int $review_id the review id
     *
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    function yith_ywar_notify_review_update ( $review_id ) {

        if ( ! $review_id ) {
            return;
        }

        //  Notify the update of the review
        $product_id = get_post_meta ( $review_id, YITH_YWAR_META_KEY_PRODUCT_ID, true );

        yith_ywar_notify_product_reviews_update ( $product_id );
    }
}