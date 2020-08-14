<?php
/**
 * Admin class for messages
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_Messages_Manager_Admin' ) ) {
    /**
     * Messages Manager Admin class.
     * The class manage all the admin behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Messages_Manager_Admin {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCMBS_Messages_Manager_Admin
         * @since 1.0.0
         */
        private static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCMBS_Messages_Manager_Admin
         * @since 1.0.0
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        private function __construct() {
            // Register Post Type
            add_action( 'init', array( $this, 'post_type_register' ) );

            // Send Message Actions for users
            add_filter( 'user_row_actions', array( $this, 'add_send_message_action' ), 10, 2 );
            add_action( 'admin_action_send_message_to_member', array( $this, 'admin_action_send_message_to_member' ) );

            // add user info in Messages
            add_filter( 'manage_yith-wcmbs-thread_posts_columns', array( $this, 'edit_messages_columns' ) );
            add_filter( 'manage_yith-wcmbs-thread_posts_custom_column', array( $this, 'render_messages_columns' ), 10, 3 );

            // Remove Actions for messages
            add_filter( 'post_row_actions', array( $this, 'thread_row_actions' ), 10, 2 );

            // Remove Messages from comments list
            add_action( 'pre_get_comments', array( $this, 'remove_messages_from_comments' ) );

            add_filter( 'bulk_actions-edit-yith-wcmbs-thread', array( $this, 'remove_bulk_actions_for_threads' ) );

            // Enqueue Scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

            add_action( 'wp_ajax_yith_wcmbs_send_message', array( $this, 'ajax_send_message' ) );
            add_action( 'wp_ajax_yith_wcmbs_get_older_messages', array( $this, 'ajax_get_older_messages' ) );
        }

        /**
         * Register Plans custom post type with options metabox
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function post_type_register() {
            /*
             * REGISTER POST TYPE FOR MESSAGES
             */
            $labels = array(
                'all_items'          => __( 'Messages', 'yith-woocommerce-membership' ),
                'name'               => __( 'Messages', 'yith-woocommerce-membership' ),
                'singular_name'      => __( 'Message', 'yith-woocommerce-membership' ),
                'add_new'            => __( 'Add Message', 'yith-woocommerce-membership' ),
                'add_new_item'       => __( 'New Message', 'yith-woocommerce-membership' ),
                'edit_item'          => __( 'Message', 'yith-woocommerce-membership' ),
                'view_item'          => __( 'View Message', 'yith-woocommerce-membership' ),
                'not_found'          => __( 'Message not found', 'yith-woocommerce-membership' ),
                'not_found_in_trash' => __( 'Message not found in trash', 'yith-woocommerce-membership' )
            );

            $args = array(
                'labels'               => $labels,
                'public'               => false,
                'show_ui'              => true,
                'menu_position'        => 10,
                'exclude_from_search'  => true,
                'capability_type'      => 'post',
                'map_meta_cap'         => true,
                'rewrite'              => true,
                'has_archive'          => false,
                'hierarchical'         => false,
                'show_in_nav_menus'    => false,
                'show_in_menu'         => 'edit.php?post_type=yith-wcmbs-plan',
                'menu_icon'            => 'dashicons-groups',
                'supports'             => false,
                'register_meta_box_cb' => array( $this, 'register_metaboxes' )
            );

            register_post_type( 'yith-wcmbs-thread', $args );
        }


        /**
         * Register the metaboxes for this CPT
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function register_metaboxes() {
            remove_meta_box( 'submitdiv', 'yith-wcmbs-thread', 'side' );
            add_meta_box( 'yith-wcmbs-user-info', __( 'User Info', 'yith-woocommerce-membership' ), array( $this, 'metabox_user_info_render' ), 'yith-wcmbs-thread', 'side', 'high' );
            add_meta_box( 'yith-wcmbs-messages', __( 'Messages', 'yith-woocommerce-membership' ), array( $this, 'metabox_messages_render' ), 'yith-wcmbs-thread', 'normal', 'high' );
            add_meta_box( 'yith-wcmbs-send-message', __( 'Send Message', 'yith-woocommerce-membership' ), array( $this, 'metabox_send_message_render' ), 'yith-wcmbs-thread', 'normal', 'default' );
        }

        /**
         * Render the metabox containing the user info
         *
         *
         * @param       $post
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function metabox_user_info_render( $post ) {
            $member_id = get_post_meta( $post->ID, '_user_id', true );

            $user   = get_user_by( 'id', $member_id );
            $member = YITH_WCMBS_Members()->get_member( $member_id );

            $username  = $user->user_login;
            $firstname = $user->user_firstname;
            $lastname  = $user->user_lastname;
            $email     = $user->user_email;

            $plan_names                     = $member->get_membership_plans( array( 'return' => 'names' ) );
            $no_active_membership_plan_text = '<em>' . __( 'This user doesn\'t have any active membership plan!', 'yith-woocommerce-membership' ) . '</em>';
            $plans                          = !empty( $plan_names ) ? implode( ', ', $plan_names ) : $no_active_membership_plan_text;

            $t_args = compact( 'username', 'firstname', 'lastname', 'email', 'plans' );

            wc_get_template( 'metaboxes/user_info.php', $t_args, YITH_WCMBS_TEMPLATE_PATH . '/', YITH_WCMBS_TEMPLATE_PATH . '/' );
        }

        /**
         * Render the metabox containing the Messages
         *
         *
         * @param       $post
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function metabox_messages_render( $post ) {
            $args = array(
                'post_id' => $post->ID,
                'orderby' => 'comment_ID',
                'order'   => 'DESC',
                'approve' => 'approve',
                'type'    => 'message',
                'number'  => 10
            );

            $messages = get_comments( $args );

            if ( !empty( $messages ) ) {
                echo '<input id="yith-wcmbs-get-older-messages" type="button" class="button button-secondary" value="' . __( 'Get older messages', 'yith-woocommerce-membership' ) . '"/>';
                echo '<span id= "get-older-spinner" class="spinner"></span>';
            }

            $comment_count = get_comment_count( $post->ID );
            $message_count = $comment_count[ 'total_comments' ];

            echo "<ul id='yith-wcmbs-messages-list' data-messages-count='{$message_count}'>";
            if ( !empty( $messages ) ) {

                $messages = array_reverse( $messages );

                foreach ( $messages as $message ) {

                    $args = array(
                        'message' => $message,
                    );

                    wc_get_template( 'metaboxes/message.php', $args, YITH_WCMBS_TEMPLATE_PATH . '/', YITH_WCMBS_TEMPLATE_PATH . '/' );
                }
            }
            echo "</ul>";
        }

        /**
         * get the older messages [AJAX]
         *
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function ajax_get_older_messages() {
            if ( !empty( $_POST[ 'thread_id' ] ) && isset( $_POST[ 'offset' ] ) ) {

                $args = array(
                    'post_id' => $_POST[ 'thread_id' ],
                    'orderby' => 'comment_ID',
                    'order'   => 'DESC',
                    'approve' => 'approve',
                    'type'    => 'message',
                    'number'  => 10,
                    'offset'  => $_POST[ 'offset' ]
                );

                $messages = get_comments( $args );

                if ( !empty( $messages ) ) {
                    $messages = array_reverse( $messages );

                    foreach ( $messages as $message ) {
                        $args = array(
                            'message' => $message,
                        );
                        wc_get_template( 'metaboxes/message.php', $args, YITH_WCMBS_TEMPLATE_PATH . '/', YITH_WCMBS_TEMPLATE_PATH . '/' );
                    }
                }
            }

            die();
        }

        /**
         * Render the metabox containing the send message text box
         *
         *
         * @param       $post
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function metabox_send_message_render( $post ) {
            $args = array(
                'thread_id' => $post->ID
            );
            wc_get_template( 'metaboxes/send_message.php', $args, YITH_WCMBS_TEMPLATE_PATH . '/', YITH_WCMBS_TEMPLATE_PATH . '/' );
        }


        /**
         * Do actions send_message_to_member
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function admin_action_send_message_to_member() {
            if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] = 'send_message_to_member' ) {
                if ( isset( $_REQUEST[ 'user_id' ] ) ) {
                    $user_id = absint( $_REQUEST[ 'user_id' ] );

                    $message_args = array(
                        'posts_per_page' => 1,
                        'post_type'      => 'yith-wcmbs-thread',
                        'meta_key'       => '_user_id',
                        'meta_value'     => $user_id

                    );
                    $message      = get_posts( $message_args );

                    if ( $message ) {
                        $message_id     = $message[ 0 ]->ID;
                        $admin_edit_url = admin_url( 'post.php?post=' . $message_id . '&action=edit' );
                        wp_redirect( $admin_edit_url );
                    } else {
                        $new_message = array(
                            'post_title'  => get_the_author_meta( 'user_login', $user_id ),
                            'post_status' => 'publish',
                            'post_type'   => 'yith-wcmbs-thread'
                        );

                        $new_message_id = wp_insert_post( $new_message );

                        if ( $new_message_id ) {
                            update_post_meta( $new_message_id, '_user_id', $user_id );

                            $admin_edit_url = admin_url( 'post.php?post=' . $new_message_id . '&action=edit' );
                            wp_redirect( $admin_edit_url );
                        }
                    }
                }
            }
        }


        /**
         * Adds a message (comment) to a thread
         *
         * @param string $message Message to add
         * @param int    $thread_id Id of the thread
         * @param bool   $sent_by_user message is sent by user?
         * @param int    $user_id the id of the user
         *
         * @return int Comment ID
         */
        public function add_message_to_thread( $message, $thread_id, $user_id = 0, $sent_by_user = false ) {

            $u_id = ( $user_id ) ? $user_id : get_current_user_id();

            $user                 = get_user_by( 'id', $u_id );
            $comment_author       = $user->user_login;
            $comment_author_email = $user->user_email;

            $comment_post_ID    = $thread_id;
            $comment_author_url = '';
            $comment_agent      = 'yith_wcmbs';
            $comment_content    = $message;
            $comment_type       = 'message';
            $comment_parent     = 0;
            $comment_approved   = 1;
            $user_id            = $u_id;

            $comment_data = apply_filters( 'yith_wcmbs_message_data', compact( 'user_id', 'comment_post_ID', 'comment_author', 'comment_agent', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'comment_approved' ), array(
                'thread_id'      => $thread_id,
                'sent_by_user' => $sent_by_user
            ) );

            $comment_id = wp_insert_comment( $comment_data );

            if ( $sent_by_user ) {
                update_comment_meta( $comment_id, 'sent_by_user', true );
            }

            $my_post = array(
                'ID'            => $thread_id,
                'post_date'     => date( 'Y-m-d H:i:s' ),
                'post_date_gmt' => gmdate( 'Y-m-d H:i:s' ),
            );
            // Update the post into the database
            wp_update_post( $my_post );

            return $comment_id;
        }

        /**
         * Adds a message (comment) to a thread [AJAX]
         *
         */
        public function ajax_send_message() {
            if ( !empty( $_POST[ 'thread_id' ] ) && !empty( $_POST[ 'user_id' ] ) && isset( $_POST[ 'message' ] ) ) {
                $message    = $_POST[ 'message' ];
                $thread_id  = $_POST[ 'thread_id' ];
                $user_id    = $_POST[ 'user_id' ];
                $message_id = $this->add_message_to_thread( $message, $thread_id, $user_id );

                $message_obj = get_comment( $message_id );

                $args = array(
                    'message' => $message_obj,
                );

                wc_get_template( 'metaboxes/message.php', $args, YITH_WCMBS_TEMPLATE_PATH . '/', YITH_WCMBS_TEMPLATE_PATH . '/' );

            } else {
                _e( 'An error occurred while sending the message', 'yith-woocommerce-membership' );
            }
            die();
        }


        /**
         * Add Send Message action link in Users LIST
         *
         * @param array   $actions An array of row action links. Defaults are
         *                         'Edit', 'Delete'.
         * @param WP_User $user The user object.
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         * @return array
         */
        public function add_send_message_action( $actions, $user ) {
            $member       = new YITH_WCMBS_Member_Premium( $user->ID );
            $member_plans = $member->get_membership_plans();
            if ( !empty( $member_plans ) ) {
                $admin_url                           = admin_url();
                $link                                = add_query_arg( array( 'action' => 'send_message_to_member', 'user_id' => $user->ID ), $admin_url );
                $action_name                         = __( 'Send Message', 'yith-woocommerce-membership' );
                $actions[ 'send_message_to_member' ] = "<a href='{$link}'>{$action_name}</a>";
            }

            return $actions;
        }


        /**
         * Add column in Message table list
         *
         * @param array $columns
         *
         * @return array
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function edit_messages_columns( $columns ) {
            unset( $columns[ 'cb' ] );
            unset( $columns[ 'date' ] );
            $columns[ 'title' ]          = __( 'User', 'yith-woocommerce-membership' );
            $columns[ 'messages_count' ] = '<span class="dashicons dashicons-admin-comments"></span>';
            $columns[ 'last_sender' ]    = __( 'Last answer by', 'yith-woocommerce-membership' );
            $columns[ 'date' ]           = __( 'Last date', 'yith-woocommerce-membership' );

            return $columns;
        }

        /**
         * Add column in Message table list
         *
         * @param string $column_name
         * @param int    $post
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         * @return string
         */
        public function render_messages_columns( $column_name, $post_id ) {

            switch ( $column_name ) {
                case 'messages_count':
                    $comment_count = get_comment_count( $post_id );
                    $message_count = $comment_count[ 'total_comments' ];
                    echo "<span class='yith-wcmbs-messages-count'>{$message_count}</span>";
                    break;
                case 'last_sender':
                    $args     = array(
                        'post_id' => $post_id,
                        'orderby' => 'comment_ID',
                        'order'   => 'DESC',
                        'approve' => 'approve',
                        'type'    => 'message',
                        'number'  => 1
                    );
                    $messages = get_comments( $args );
                    if ( !empty( $messages ) ) {
                        $message = $messages[ 0 ];
                        /*$user_id = absint( $message->user_id );
                        $user    = get_user_by( 'id', $user_id );
                        echo isset($user->user_login) ? $user->user_login : '';*/
                        echo $message->comment_author;
                    }
                    break;
            }

        }


        /**
         * Edit Actions for thread in Thread List
         *
         * @param array   $actions An array of row action links. Defaults are
         *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
         *                         'Delete Permanently', 'Preview', and 'View'.
         * @param WP_Post $post The post object.
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         * @return array
         */
        public function thread_row_actions( $actions, $post ) {
            if ( $post->post_type == 'yith-wcmbs-thread' ) {
                unset( $actions[ 'view' ] );
                unset( $actions[ 'inline hide-if-no-js' ] );
                unset( $actions[ 'trash' ] );
                unset( $actions[ 'edit' ] );
                $actions[ 'delete' ] = "<a class='submitdelete' title='" . esc_attr__( 'Delete this item permanently' ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Thread', 'yith-woocommerce-membership' ) . "</a>";
            }

            return $actions;
        }

        /**
         * Remove Messages in Comments List
         *
         * @param WP_Comment_Query $query The query object
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function remove_messages_from_comments( $query ) {
            if ( function_exists( 'get_current_screen' ) ) {
                $screen = get_current_screen();
                if ( $screen && $screen->id == 'edit-comments' ) {
                    $query->query_vars[ 'type__not_in' ] = 'message';
                }
            }
        }


        /**
         * Remove Bulk Actions for threads
         *
         * @param array $actions
         *
         * @return array
         *
         * @since       1.0.0
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function remove_bulk_actions_for_threads( $actions ) {
            return array();
        }


        public function admin_enqueue_scripts() {
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            $screen = get_current_screen();

            if ( $screen->id == 'yith-wcmbs-thread' ) {
                wp_enqueue_script( 'yith_wcmbs_admin_messages_js', YITH_WCMBS_ASSETS_URL . '/js/admin_messages' . $suffix . '.js', array( 'jquery' ), YITH_WCMBS_VERSION, true );
            }

            if ( $screen->id == 'yith-wcmbs-thread' || $screen->id == 'edit-yith-wcmbs-thread' ) {
                wp_enqueue_style( 'yith-wcmbs-admin-messages-styles', YITH_WCMBS_ASSETS_URL . '/css/admin_messages.css', array(), YITH_WCMBS_VERSION );
            }
        }


    }
}

/**
 * Unique access to instance of YITH_WCMBS_Messages_Manager_Admin class
 *
 * @return \YITH_WCMBS_Messages_Manager_Admin
 * @since 1.0.0
 */
function YITH_WCMBS_Messages_Manager_Admin() {
    return YITH_WCMBS_Messages_Manager_Admin::get_instance();
}