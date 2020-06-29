<?php
!defined( 'YITH_POS' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_POS_Register_Post_Type_Admin' ) ) {
    /**
     * Class YITH_POS_Register_Post_Type_Admin
     * Main Class
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
     */
    class YITH_POS_Register_Post_Type_Admin {

        /** @var YITH_POS_Register_Post_Type_Admin */
        protected static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_POS_Register_Post_Type_Admin
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_POS_Register_Post_Type_Admin constructor
         */
        public function __construct() {
            add_filter( 'get_user_option_screen_layout_' . YITH_POS_Post_Types::$register, '__return_true' );

            add_filter( 'manage_' . YITH_POS_Post_Types::$register . '_posts_columns', array( $this, 'manage_list_columns' ) );
            add_action( 'manage_' . YITH_POS_Post_Types::$register . '_posts_custom_column', array( $this, 'render_list_columns' ), 10, 2 );
            add_filter( 'default_hidden_columns', array( $this, 'default_hidden_columns' ), 10, 2 );
            add_filter( 'bulk_actions-edit-' . YITH_POS_Post_Types::$register, array( $this, 'manage_bulk_actions' ), 10, 1 );
            add_filter( 'post_row_actions', array( $this, 'manage_row_actions' ), 10, 2 );

            add_action( 'restrict_manage_posts', array( $this, 'render_filters' ), 10, 1 );
            add_action( 'pre_get_posts', array( $this, 'filter_registers' ), 10, 1 );
        }

        /**
         * Filter registers by Store and Status
         *
         * @param WP_Query $query
         */
        public function filter_registers( $query ) {
            if ( $query->is_main_query() && isset( $query->query[ 'post_type' ] ) && YITH_POS_Post_Types::$register === $query->query[ 'post_type' ] ) {
                $meta_query = !!$query->get( 'meta_query' ) ? $query->get( 'meta_query' ) : array();
                $changed    = false;
                if ( !empty( $_REQUEST[ 'store' ] ) ) {
                    $changed      = true;
                    $meta_query[] = array(
                        'key'   => '_store_id',
                        'value' => absint( $_REQUEST[ 'store' ] )
                    );
                }

                if ( !empty( $_REQUEST[ 'status' ] ) ) {
                    $changed = true;
                    $status  = wc_clean( $_REQUEST[ 'status' ] );
                    if ( 'closed' === $status ) {
                        $meta_query[] = array(
                            'relation' => 'OR',
                            array( 'key' => '_status', 'value' => 'closed' ),
                            array( 'key' => '_status', 'compare' => 'NOT EXISTS' ),
                        );
                    } else {
                        $meta_query[] = array(
                            'key'   => '_status',
                            'value' => $status
                        );
                    }
                }
                if ( $changed ) {
                    $query->set( 'meta_query', $meta_query );
                }
            }
        }

        /**
         * render filters for Store and Status
         *
         * @param $post_type
         */
        public function render_filters( $post_type ) {
            if ( YITH_POS_Post_Types::$register === $post_type ) {
                $selected_store  = isset( $_REQUEST[ 'store' ] ) ? absint( $_REQUEST[ 'store' ] ) : '';
                $selected_status = isset( $_REQUEST[ 'status' ] ) ? wc_clean( $_REQUEST[ 'status' ] ) : '';

                $store_ids   = yith_pos_get_stores();
                $store_names = array_map( 'yith_pos_get_register_name', $store_ids );
                $stores      = array_combine( $store_ids, $store_names );
                echo "<select name='store'>";
                echo "<option value=''>" . __( 'Filter by store', 'yith-point-of-sale-for-woocommerce' ) . "</option>";
                foreach ( $stores as $id => $name ) {
                    echo "<option value='{$id}' " . selected( $id, $selected_store, false ) . ">$name</option>";
                }
                echo "</select>";

                $statuses = yith_pos_register_statuses();

                echo "<select name='status'>";
                echo "<option value=''>" . __( 'Filter by status', 'yith-point-of-sale-for-woocommerce' ) . "</option>";
                foreach ( $statuses as $id => $name ) {
                    echo "<option value='{$id}' " . selected( $id, $selected_status, false ) . ">$name</option>";
                }
                echo "</select>";
            }
        }


        /**
         * Manage the columns of the Register List
         *
         * @param array $columns
         * @return array
         */
        public function manage_list_columns( $columns ) {
            $date_text = $columns[ 'date' ];
            unset( $columns[ 'date' ] );
            unset( $columns[ 'title' ] );

            $new_columns[ 'cb' ] = $columns[ 'cb' ];
            unset( $columns[ 'cb' ] );

            $new_columns[ 'name' ]    = __( 'Register Name', 'yith-point-of-sale-for-woocommerce' );
            $new_columns[ 'store' ]   = __( 'Store', 'yith-point-of-sale-for-woocommerce' );
            $new_columns[ 'info' ]    = __( 'Info', 'yith-point-of-sale-for-woocommerce' );
            $new_columns[ 'status' ]  = __( 'Status', 'yith-point-of-sale-for-woocommerce' );
            $new_columns[ 'enabled' ] = __( 'Enabled', 'yith-point-of-sale-for-woocommerce' );

            $new_columns = array_merge( $new_columns, $columns );

            $new_columns[ 'date' ] = $date_text;

            return $new_columns;
        }

        /**
         * Render the columns of the Register List
         *
         * @param array $column
         * @param int   $post_id
         */
        public function render_list_columns( $column, $post_id ) {
            $register = yith_pos_get_register( $post_id );
            switch ( $column ) {
                case 'name':
                    echo "<strong>" . $register->get_name() . "</strong>";
                    break;
                case 'store':
                    $store_id = $register->get_store_id();
                    echo yith_pos_get_post_edit_link_html( $store_id );
                    break;

                case 'info':
                    $type = $register->is_guest_enabled() ? __( 'Guest Register', 'yith-point-of-sale-for-woocommerce' ) : __( 'Standard Register', 'yith-point-of-sale-for-woocommerce' );
                  //  echo "<div>{$type}</div>";
                    if ( $register->is_receipt_enabled() ) {
                        $receipt_id = $register->get_receipt_id();
                        echo "<div>" . sprintf( __( "Receipt: %s", 'yith-point-of-sale-for-woocommerce' ), yith_pos_get_post_edit_link_html( $receipt_id ) ) . "</div>";
                    } else {
                        echo "<div>" . __( "No Receipt", 'yith-point-of-sale-for-woocommerce' ) . "</div>";
                    }
                    break;
                case 'status':
                    $status      = $register->get_status();
                    $status_name = yith_pos_get_register_status_name( $status );
                    echo "<span class='yith-pos-register-status yith-pos-register-status--{$status}'>{$status_name}</span>";
                    if ( $user = yith_pos_get_register_lock( $register->get_id() ) ) {
                        echo '<div class="yith-pos-register-status__used-by">' . yith_pos_get_employee_name( $user ) . '</div>';
                    }
                    break;
                case 'enabled':
                    if ( $register->is_published() ) {
                        echo "<div class='yith-plugin-ui'>";
                        echo yith_plugin_fw_get_field( array(
                                                           'type'  => 'onoff',
                                                           'class' => 'yith-pos-register-toggle-enabled',
                                                           'value' => $register->is_enabled() ? 'yes' : 'no',
                                                           'data'  => array(
                                                               'register-id' => $register->get_id(),
                                                               'security'    => wp_create_nonce( 'register-toggle-enabled' )
                                                           )
                                                       ) );
                        echo "</div>";
                    } else {
                        $post_status     = $register->get_post_status();
                        $post_status_obj = get_post_status_object( $post_status );
                        echo "<div class='yith-pos-post-status yith-pos-post-status--{$post_status}'>{$post_status_obj->label}</div>";
                    }
                    break;
            }
        }

        /**
         * Set the default hidden columns of the Register List
         *
         * @param array     $hidden
         * @param WP_Screen $screen
         * @return array
         */
        public function default_hidden_columns( $hidden, $screen ) {
            if ( 'edit-' . YITH_POS_Post_Types::$register === $screen->id ) {
                $hidden[] = 'date';
            }

            return $hidden;
        }

        /**
         * Manage the bulk actions in the Register List
         *
         * @param array $actions
         * @return array
         */
        public function manage_bulk_actions( $actions ) {
            if ( isset( $actions[ 'edit' ] ) ) {
                unset( $actions[ 'edit' ] );
            }
            return $actions;
        }

        /**
         * Manage the row actions in the Register List
         *
         * @param array   $actions
         * @param WP_Post $post
         * @return array
         */
        public function manage_row_actions( $actions, $post ) {
            if ( YITH_POS_Post_Types::$register === get_post_type( $post ) ) {
                if ( isset( $actions[ 'inline hide-if-no-js' ] ) ) {
                    unset( $actions[ 'inline hide-if-no-js' ] );
                }

                if ( isset( $actions[ 'edit' ] ) ) {
                    unset( $actions[ 'edit' ] );

                    if ( $register = yith_pos_get_register( $post->ID ) ) {
                        $store_id           = $register->get_store_id();
                        $edit_register_link = add_query_arg( array( 'yith-pos-edit-register' => $post->ID ), get_edit_post_link( $store_id ) );

                        $actions[ 'edit' ] = "<a href='$edit_register_link'>" . __( 'Edit', 'yith-point-of-sale-for-woocommerce' ) . "</a>";
                    };
                }

                $open_register_link = add_query_arg( array(
                                                         'yith-pos-register-direct-login-nonce' => wp_create_nonce( 'yith-pos-register-direct-login' ),
                                                         'register'                             => $post->ID
                                                     ), yith_pos_get_pos_page_url() );

                $actions[ 'open-register' ] = "<a href='$open_register_link'>" . __( 'Open Register', 'yith-point-of-sale-for-woocommerce' ) . "</a>";
            }
            return $actions;
        }


    }

}