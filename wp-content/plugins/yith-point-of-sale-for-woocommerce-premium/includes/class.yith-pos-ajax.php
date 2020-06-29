<?php
!defined( 'YITH_POS' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_POS_Ajax' ) ) {
    /**
     * it handles the assets
     *
     * @class  YITH_POS_Ajax
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
     */
    class YITH_POS_Ajax {
        private static $_instance;

        /**
         * Singleton implementation
         *
         * @return YITH_POS_Ajax
         */
        public static function get_instance() {
            return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
        }

        /**
         * YITH_POS_Ajax constructor.
         */
        private function __construct() {
            $ajax_actions = array(
                'wizard_save_store',
                'wizard_get_summary',
                'store_toggle_enabled',
                'register_toggle_enabled',
                'check_user_login',
                'create_user',
                'create_register',
                'update_register',
                'delete_register',
                'search_categories',
                'get_cashiers_by_shop',
                'heartbeat_tick',
	            'gateway_toggle_enable',
	            'update_register_session'
            );

            foreach ( $ajax_actions as $ajax_action ) {
                add_action( 'wp_ajax_yith_pos_' . $ajax_action, array( $this, $ajax_action ) );
                add_action( 'wp_ajax_nopriv_yith_pos_' . $ajax_action, array( $this, $ajax_action ) );
            }
        }

        private function _ajax_start( $context = 'admin' ) {
            error_reporting( 0 );

            !defined( 'YITH_POS_DOING_AJAX' ) && define( 'YITH_POS_DOING_AJAX', true );
            if ( 'admin' === $context ) {
                !defined( 'YITH_POS_DOING_AJAX_ADMIN' ) && define( 'YITH_POS_DOING_AJAX_ADMIN', true );
            } elseif ( 'frontend' === $context ) {
                !defined( 'YITH_POS_DOING_AJAX_FRONTEND' ) && define( 'YITH_POS_DOING_AJAX_FRONTEND', true );
            }
        }

        /**
         * Wizard Saving Store
         */
        public function wizard_save_store() {
            $request = $_REQUEST;
            if ( !empty( $request[ 'post_ID' ] ) ) {
                $request[ 'ID' ] = $request[ 'post_ID' ];
                unset( $request[ 'post_ID' ] );

                $id                      = $request[ 'ID' ];
                $request[ 'post_title' ] = yith_pos_get_store_name( $id );

            }
            wp_insert_post( $request );
            wp_die();
        }

        /**
         * Wizard Saving Store
         */
        public function wizard_get_summary() {
            if ( !empty( $_REQUEST[ 'id' ] ) ) {
                $id    = absint( $_REQUEST[ 'id' ] );
                $store = yith_pos_get_store( $id );
                yith_pos_get_view( 'panel/store-wizard-summary.php', compact( 'store' ) );
            }
            wp_die();
        }

        /**
         * Store Toggle Enabled
         */
        public function store_toggle_enabled() {
            if ( !empty( $_REQUEST[ 'id' ] ) && !empty( $_REQUEST[ 'enabled' ] ) && !empty( $_REQUEST[ 'security' ] ) && wp_verify_nonce( $_REQUEST[ 'security' ], 'store-toggle-enabled' ) ) {
                $id      = absint( $_REQUEST[ 'id' ] );
                $enabled = 'yes' === $_REQUEST[ 'enabled' ];
                $store   = yith_pos_get_store( $id );
                if ( $store ) {
                    $store->set_enabled( $enabled );
                    $store->save();
                    wp_send_json( array(
                                      'success'    => true,
                                      'new_status' => $enabled
                                  ) );
                } else {
                    wp_send_json( array(
                                      'error' => sprintf( __( 'Error: Store #%s not found', 'yith-point-of-sale-for-woocommerce' ), $id )
                                  ) );
                }
            }
            wp_send_json( array(
                              'error' => __( 'Error: Invalid request. Try again!', 'yith-point-of-sale-for-woocommerce' )
                          ) );
        }

        /**
         * Register Toggle Enabled
         */
        public function register_toggle_enabled() {
            if ( !empty( $_REQUEST[ 'id' ] ) && !empty( $_REQUEST[ 'enabled' ] ) && !empty( $_REQUEST[ 'security' ] ) && wp_verify_nonce( $_REQUEST[ 'security' ], 'register-toggle-enabled' ) ) {
                $id       = absint( $_REQUEST[ 'id' ] );
                $enabled  = 'yes' === $_REQUEST[ 'enabled' ];
                $register = yith_pos_get_register( $id );
                if ( $register ) {
                    $register->set_enabled( $enabled );
                    $register->save();
                    wp_send_json( array(
                                      'success'    => true,
                                      'new_status' => $enabled
                                  ) );
                } else {
                    wp_send_json( array(
                                      'error' => sprintf( __( 'Error: Register #%s not found', 'yith-point-of-sale-for-woocommerce' ), $id )
                                  ) );
                }
            }
            wp_send_json( array(
                              'error' => __( 'Error: Invalid request. Try again!', 'yith-point-of-sale-for-woocommerce' )
                          ) );
        }

        /**
         * Check if a username with that user_login exists.
         */
        public function check_user_login() {

            if ( !isset( $_POST[ 'value' ] ) || !isset( $_POST[ 'field' ] ) ) {
                die();
            }

            $user_obj = get_user_by( $_POST[ 'field' ], $_POST[ 'value' ] );

            wp_send_json( array(
                              'is_valid' => $user_obj ? 0 : 1
                          ) );
        }

        /**
         * Register a manager or a cashier.
         */
        public function create_user() {
            $response = array(
                'error' => __( 'Error: Invalid request. Try again!', 'yith-point-of-sale-for-woocommerce' )
            );
            if ( wp_verify_nonce( $_REQUEST[ 'security' ], 'yith-pos-create-user' ) ) {
                $user_type = $_POST[ 'user_type' ];
                $user_data = array(
                    'user_login' => $_POST[ 'user_login' ],
                    'user_email' => $_POST[ 'user_email' ],
                    'user_pass'  => $_POST[ 'user_pass' ],
                    'first_name' => $_POST[ 'first_name' ],
                    'last_name'  => $_POST[ 'last_name' ]
                );

                if ( $user_type ) {
                    $user_data[ 'role' ] = 'yith_pos_' . $user_type;
                }

                $user_id = wp_insert_user( $user_data );
                if ( is_wp_error( $user_id ) ) {
                    $error_message = $user_id->get_error_message( $user_id->get_error_code() );
                    $response      = array(
                        'error' => $error_message,
                    );
                } else {
                    $user_data[ 'id' ] = $user_id;
                    $full_name         = sprintf( "%s %s", $user_data[ 'first_name' ], $user_data[ 'last_name' ] );
                    $response          = array(
                        'success'        => true,
                        'user'           => $user_data,
                        'user_id'        => $user_id,
                        'user_name_html' => sprintf( "%s (%s)", $full_name, $user_data[ 'user_email' ] )
                    );
                }
            }

            wp_send_json( $response );
        }

        /**
         * Create a register
         */
        public function create_register() {
            $response = array(
                'error' => __( 'Error: Invalid request. Try again!', 'yith-point-of-sale-for-woocommerce' )
            );
            if ( isset( $_POST[ 'security' ] ) && wp_verify_nonce( $_POST[ 'security' ], 'yith-pos-create-register' ) ) {
                $name    = isset( $_POST[ 'name' ] ) ? $_POST[ 'name' ] : '';
                $post_id = wp_insert_post( array( 'post_title' => $name, 'post_status' => 'publish', 'post_type' => YITH_POS_Post_Types::$register ) );
                if ( is_wp_error( $post_id ) ) {
                    $error_message = $post_id->get_error_message( $post_id->get_error_code() );
                    $response      = array(
                        'error' => $error_message,
                    );
                } else {
                    $params = $_POST;
                    unset( $params[ 'security' ] );
                    global $register, $register_id;
                    $register = yith_pos_get_register( $post_id );
                    $register->set_props( $params );
                    $register_id = $register->save( true );
                    ob_start();
                    yith_pos_get_view( 'metabox/store-registers-list-single.php' );

                    $register_html = ob_get_clean();
                    $response      = array(
                        'success'       => true,
                        'register_id'   => $register_id,
                        'register_html' => $register_html
                    );
                }
            }

            wp_send_json( $response );
        }

        /**
         * Update a register
         */
        public function update_register() {
            $response = array(
                'error' => __( 'Error: Invalid request. Try again!', 'yith-point-of-sale-for-woocommerce' )
            );
            if ( isset( $_POST[ 'security' ] ) && wp_verify_nonce( $_POST[ 'security' ], 'yith-pos-update-register' ) && isset( $_POST[ 'id' ] ) ) {
                $post_id = absint( $_POST[ 'id' ] );
                $params  = $_POST;
                unset( $params[ 'security' ] );
                unset( $params[ 'id' ] );
                $register = yith_pos_get_register( $post_id );
                if ( $register ) {
                    $register->set_props( $params );
                    $register_id = $register->save();
                    if ( $register_id ) {
                        $response = array(
                            'success'     => true,
                            'register_id' => $register_id,
                            'message'     => __( 'Register updated correctly!', 'yith-point-of-sale-for-woocommerce' ),
                        );
                    } else {
                        $response = array(
                            'error' => sprintf( __( 'Error: Something had gone wrong. Please try again!', 'yith-point-of-sale-for-woocommerce' ) ),
                        );
                    }
                } else {
                    $response = array(
                        'error' => sprintf( __( 'Error: Register #%s not found. Please try again!', 'yith-point-of-sale-for-woocommerce' ), $post_id ),
                    );
                }
            }

            wp_send_json( $response );
        }

        /**
         * Delete a register
         */
        public function delete_register() {
            $response = array(
                'error' => __( 'Error: Invalid request. Try again!', 'yith-point-of-sale-for-woocommerce' )
            );
            if ( isset( $_POST[ 'security' ] ) && wp_verify_nonce( $_POST[ 'security' ], 'yith-pos-delete-register' ) && isset( $_POST[ 'id' ] ) ) {
                $post_id  = absint( $_POST[ 'id' ] );
                $register = yith_pos_get_register( $post_id );
                if ( $register ) {
                    $response = array(
                        'success' => $register->trash(),
                    );
                } else {
                    $response = array(
                        'error' => sprintf( __( 'Error: Register #%s not found. Please try again!', 'yith-point-of-sale-for-woocommerce' ), $post_id ),
                    );
                }
            }

            wp_send_json( $response );
        }


        /**
         * Get Categories via Ajax
         *
         * @since 1.0
         */
        public function search_categories() {

            check_ajax_referer( 'search-products', 'security' );

            if ( !current_user_can( 'edit_products' ) ) {
                wp_die( -1 );
            }

            if ( !$search_text = wc_clean( stripslashes( $_GET[ 'term' ] ) ) ) {
                wp_die();
            }

            $found_tax = array();
            $args      = array(
                'taxonomy'   => array( 'product_cat' ),
                'orderby'    => 'id',
                'order'      => 'ASC',
                'hide_empty' => true,
                'fields'     => 'all',
                'name__like' => $search_text,
            );

            if ( $terms = get_terms( $args ) ) {
                foreach ( $terms as $term ) {
                    $term->formatted_name        .= $term->name . ' (' . $term->count . ')';
                    $found_tax[ $term->term_id ] = $term->formatted_name;
                }
            }

            wp_send_json( $found_tax );
        }


        /**
         * Get cashiers
         */
        public function get_cashiers_by_shop() {
            if ( !isset( $_POST[ 'store' ] ) ) {
                die;
            }

            $cashiers = yith_pos_get_employees( 'cashier', $_POST[ 'store' ] );
            $list     = array();
            if ( $cashiers ) {
                foreach ( $cashiers as $cashier_id ) {
                    $cashier = get_user_by( 'ID', $cashier_id );
                    $list[]  = array(
                        'id'   => $cashier->ID,
                        'name' => $cashier->first_name . ' ' . $cashier->last_name . ' (' . $cashier->user_email . ')',
                    );
                }

                $response = array( 'cashiers' => $list );
            } else {
                $response = array( 'error' => __( 'No cashiers found for the selected Store.', 'yith-point-of-sale-for-woocommerce' ) );
            }

            wp_send_json( $response );
        }

        /**
         * Check and Set the lock of the register fired through the heartbeat
         */
        public function heartbeat_tick() {
            $response = array( 'success' => false );

            if ( isset( $_REQUEST[ 'register_id' ], $_REQUEST[ 'user_id' ], $_REQUEST[ 'security' ] ) && wp_verify_nonce( $_REQUEST[ 'security' ], 'yith-pos-heartbeat' ) ) {
                $register_id = absint( $_REQUEST[ 'register_id' ] );
                $user_id     = absint( $_REQUEST[ 'user_id' ] );

                if ( $user_id === get_current_user_id() ) {
                    $response[ 'success' ] = true;

                    $register = yith_pos_get_register( $register_id );

                    if ( $register->has_status( 'opened' ) ) {
                        if ( !$user_editing_id = yith_pos_check_register_lock( $register_id ) ) {
                            $response[ 'lock' ] = yith_pos_set_register_lock( $register_id );
                        } else {
                            $response[ 'locked_by' ] = $user_editing_id;
                        }
                    } else {
                        $response[ 'register_closed' ] = true;
                    }
                }
            }

            wp_send_json( $response );
        }

	    /**
	     *
	     */
	    public  function gateway_toggle_enable() {

		    if ( current_user_can( 'manage_woocommerce' ) && check_ajax_referer( 'woocommerce-toggle-payment-gateway-enabled', 'security' ) && isset( $_POST['gateway_id'] ) ) {

			    // Load gateways.
			    $enabled_gateways = yith_pos_get_enabled_gateways_option();

			    // Get posted gateway.
			    $gateway_id = wc_clean( wp_unslash( $_POST['gateway_id'] ) );

			    if ( in_array( $gateway_id, $enabled_gateways ) ) {
			    	$enabled = false;
			    	$i = array_search( $gateway_id, $enabled_gateways );
				    if( $i ) {
				    	unset( $enabled_gateways[ $i ] );
				    }
			    } else {
				    array_push( $enabled_gateways, $gateway_id );
				    $enabled = true;
			    }


			    update_option( 'yith_pos_general_gateway_enabled', $enabled_gateways );

			    wp_send_json_success( $enabled );
			    wp_die();
		    }

		    wp_send_json_error( 'invalid_gateway_id' );
		    wp_die();

	    }

	    public function update_register_session(  ) {

		    if ( isset( $_REQUEST['session'] ) && check_ajax_referer( 'yith-pos-register-session-update-' . $_REQUEST['session']['sessionId'] ) ) {

		    	$ajax_data = $_REQUEST['session'];
		    	$content   = $_REQUEST['content'];

		    	switch( $content ){
				    case 'add_cash_in_hand':
				    YITH_POS_Register_Session::update_cash_in_hand( $ajax_data['sessionId'], $ajax_data['cashInHand']);
				    break;
				    case 'close_register':
					    YITH_POS_Register_Session::close_register( $ajax_data['sessionId'], $ajax_data['totals'], $ajax_data['note']);
					    break;
			    }

			    wp_send_json_success( 'updated' );
			    wp_die();

		    }

		    wp_send_json_error( 'invalid_session_id' );
		    wp_die();
	    }
    }
}