<?php
/**
 * Frontend class for messages
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */

if ( !defined ( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists ( 'YITH_WCMBS_Messages_Manager_Frontend' ) ) {
    /**
     * Messages Manager Frontend class.
     * The class manage all the admin behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Messages_Manager_Frontend {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCMBS_Messages_Manager_Frontend
         * @since 1.0.0
         */
        private static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCMBS_Messages_Manager_Frontend
         * @since 1.0.0
         */
        public static function get_instance () {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        private function __construct () {

            add_action ( 'wp_ajax_yith_wcmbs_user_send_message', array ( $this, 'ajax_user_send_message' ) );
            add_action ( 'wp_ajax_yith_wcmbs_user_get_older_messages', array ( $this, 'ajax_get_older_messages' ) );
            add_action (
                'wp_ajax_nopriv_yith_wcmbs_user_get_older_messages', array (
                $this, 'ajax_get_older_messages'
            )
            );

        }

        /**
         * get the count of messages by user id
         *
         * @param int $user_id id of the user
         * @param int $offset  the offset value for messages
         *
         * @return int
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_messages_count_by_user_id ( $user_id, $offset = 0 ) {
            $thread_id = $this->get_thread_id ( $user_id );

            $comment_count = get_comment_count( $thread_id );
            $message_count = $comment_count[ 'total_comments' ];
            return $message_count;
        }

        /**
         * get the messages by user id
         *
         * @param int $user_id id of the user
         * @param int $offset  the offset value for messages
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_messages_by_user_id ( $user_id, $offset = 0 ) {
            $thread_id = $this->get_thread_id ( $user_id );

            if ( $thread_id ) {
                $args = array (
                    'post_id' => $thread_id,
                    'orderby' => 'comment_ID',
                    'order'   => 'DESC',
                    'approve' => 'approve',
                    'type'    => 'message',
                    'number'  => 10,
                    'offset'  => $offset,
                );

                $messages = get_comments ( $args );

                if ( !empty( $messages ) ) {

                    $messages = array_reverse ( $messages );

                    foreach ( $messages as $message ) {
                        $args = array (
                            'message' => $message,
                        );

                        wc_get_template ( 'frontend/message.php', $args, YITH_WCMBS_TEMPLATE_PATH . '/', YITH_WCMBS_TEMPLATE_PATH . '/' );
                    }
                }
            }

        }

        /**
         * get the thread id by user id
         *
         * @param int $user_id id of the user
         *
         * @return int|bool
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_thread_id ( $user_id ) {
            $t_args = array (
                'posts_per_page' => 1,
                'post_type'      => 'yith-wcmbs-thread',
                'meta_key'       => '_user_id',
                'meta_value'     => $user_id

            );
            $thread = get_posts ( $t_args );

            if ( $thread ) {
                return $thread[ 0 ]->ID;
            }

            return false;
        }


        public function ajax_user_send_message () {
            if ( !empty( $_POST[ 'user_id' ] ) && isset( $_POST[ 'message' ] ) ) {
                $user_id   = $_POST[ 'user_id' ];
                $thread_id = $this->get_thread_id ( $user_id );
                $message   = $_POST[ 'message' ];

                if ( empty( $thread_id ) ) {
                    $new_thread = array (
                        'post_title'  => get_the_author_meta ( 'user_login', $user_id ),
                        'post_status' => 'publish',
                        'post_type'   => 'yith-wcmbs-thread'
                    );

                    $thread_id = wp_insert_post ( $new_thread );
                    if ( $thread_id ) {
                        update_post_meta ( $thread_id, '_user_id', $user_id );
                    }
                }

                $message_id = YITH_WCMBS_Messages_Manager_Admin ()->add_message_to_thread ( $message, $thread_id, $user_id, true );

                $message_obj = get_comment ( $message_id );

                $args = array (
                    'message' => $message_obj,
                );

                wc_get_template ( 'frontend/message.php', $args, YITH_WCMBS_TEMPLATE_PATH . '/', YITH_WCMBS_TEMPLATE_PATH . '/' );

            }
            die();
        }


        /**
         * get the older messages [AJAX]
         *
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function ajax_get_older_messages () {
            if ( !empty( $_POST[ 'user_id' ] ) && isset( $_POST[ 'offset' ] ) ) {
                $this->get_messages_by_user_id ( $_POST[ 'user_id' ], $_POST[ 'offset' ] );
            }
            die();
        }
    }
}

/**
 * Unique access to instance of YITH_WCMBS_Messages_Manager_Frontend class
 *
 * @return \YITH_WCMBS_Messages_Manager_Frontend
 * @since 1.0.0
 */
function YITH_WCMBS_Messages_Manager_Frontend () {
    return YITH_WCMBS_Messages_Manager_Frontend::get_instance ();
}