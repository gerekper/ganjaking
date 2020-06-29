<?php
if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWCM_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of Yit WooCommerce Cart Messages
 *
 * @class   YWCM_Cart_Messages
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */
if ( !class_exists( 'YWCM_Cart_Messages' ) ) {

    class YWCM_Cart_Messages {

        /**
         * @var $_panel Panel Object
         */
        protected $_panel;

        /**
         * @var $_premium string Premium tab template file name
         */
        protected $_premium = 'premium.php';

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-cart-messages/';

        /**
         * @var string Panel page
         */
        protected $_panel_page = 'yith_woocommerce_cart_messages';

        /**
         * @var string List of messages
         */
        protected $messages = array();

        /**
         * @var string Doc Url
         */
        public $doc_url = 'https://docs.yithemes.com/yith-woocommerce-cart-messages/';

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0
         * @author Emanuela Castorina
         */

        public function __construct() {

            $this->create_menu_items();

            add_action( 'wp_loaded', array( $this, 'load_messages'), 30);

            //Add action links
	        add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWCM_DIR . '/' . basename( YITH_YWCM_FILE ) ), array( $this, 'action_links' ) );
	        add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

            if( defined('YITH_YWCM_FREE_INIT') ){
                add_action( 'admin_init', array( $this, 'register_pointer' ) );
            }

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles_scripts' ) );
        }

        /**
         * Load the messages
         *
         * @since  1.1.4
         * @author Emanuela Castorina
         */
        public function load_messages(){
            $this->messages = YWCM_Cart_Message()->get_messages();

            $this->print_messages();
        }

        /**
         * Create Menu Items
         *
         * Print admin menu items
         *
         * @since  1.0
         * @author Emanuela Castorina
         */

        private function create_menu_items() {
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
            add_action( 'after_setup_theme', array( $this, 'call_instance_object' ), 5 );

            // Add a panel under YITH Plugins tab
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
            add_action( 'yith_woocommerce_cart_messages_premium', array( $this, 'premium_tab' ) );
        }

        /**
         * Enqueue style scripts in administrator
         *
         * Enqueue style and scripts files
         *
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
         * @return  void
         */

        public function admin_enqueue_styles_scripts() {
            wp_enqueue_style( 'yith_ywcm', YITH_YWCM_ASSETS_URL.'/css/admin.css' );

        }

        /**
         * Load YIT Plugin Framework
         *
         * @since  1.0
         * @access public
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */

        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if( ! empty( $plugin_fw_data ) ){
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }



        /**
         * Call instance object
         *
         * call the instance of YWMC_Cart_Message
         * @since  1.0
         * @access public
         * @return void
         * @author Emanuela Castorina
         */

        public function call_instance_object() {
            YWCM_Cart_Message();
        }


        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */

        public function register_panel() {

            if ( !empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = array(
	            'cart-messages'=> __( 'Cart Messages', 'yith-woocommerce-cart-messages' ),
                'settings' => __( 'Settings', 'yith-woocommerce-cart-messages' )
            );

            if ( defined( 'YITH_YWCM_FREE_INIT' ) ) {
                $admin_tabs['premium'] = __( 'Premium Version', 'yith-woocommerce-cart-messages' );
            }

            if ( defined( 'YITH_YWCM_PREMIUM' ) ) {
                $admin_tabs['layout'] = __( 'Layouts', 'yith-woocommerce-cart-messages' );
            }

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => _x( 'YITH WooCommerce Cart Messages', 'Plugin name, no translate.', 'yith-woocommerce-cart-messages' ),
                'menu_title'       => _x( 'Cart Messages', 'Plugin name, no translate.', 'yith-woocommerce-cart-messages' ),
                'capability'       => apply_filters( 'yith_ywcm_register_panel_capabilities', 'manage_options' ),
                'parent'           => '',
                'parent_page'      => 'yith_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_YWCM_DIR . 'plugin-options',
                'class'            => yith_set_wrapper_class(),
            );

            /* === Fixed: not updated theme  === */
            if ( !class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
                require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

            add_action( 'woocommerce_admin_field_yith_ywcm_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );
        }

        /**
         * Premium Tab Template
         *
         * Load the premium tab template on admin page
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */

        public function premium_tab() {
            $premium_tab_template = YITH_YWCM_TEMPLATE_PATH . '/admin/' . $this->_premium;
            if ( file_exists( $premium_tab_template ) ) {
                include_once( $premium_tab_template );
            }
        }


        /**
         * Action Links
         *
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         *
         * @return   mixed Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed
         * @use      plugin_action_links_{$plugin_file_name}
         */

	    public function action_links( $links ) {
		    $links = yith_add_action_links( $links, $this->_panel_page, true );
		    return $links;
	    }

        /**
         * Print Messages
         *
         * Print all message in cart and checkout
         *
         * @return   void
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
         */

        public function print_messages() {

            if ( get_option( 'ywcm_show_in_cart' ) == 'yes' ) {
                $priority = apply_filters( 'ywcm_before_cart_priority', 10 );
                add_action( 'woocommerce_before_cart', array( $this, 'print_message' ), $priority );
                add_action( 'woocommerce_before_cart_table', array( $this, 'print_message' ) );
                add_action( 'woocommerce_before_cart_contents', array( $this, 'print_message' ) );
                add_action( 'woocommerce_after_cart_contents', array( $this, 'print_message' ) );
                add_action( 'woocommerce_after_cart_table', array( $this, 'print_message' ) );
                add_action( 'woocommerce_after_cart', array( $this, 'print_message' ) );
            }

            if ( get_option( 'ywcm_show_in_checkout' ) == 'yes' ) {
                $priority = apply_filters( 'ywcm_before_checkout_form_priority', 10 );
                add_action( 'woocommerce_before_checkout_form', array( $this, 'print_message' ), $priority );
            }

            if ( get_option( 'ywcm_show_in_single_product' ) == 'yes' ) {
                add_action( 'woocommerce_before_single_product', array( $this, 'print_message' ) );

            }

            if ( get_option( 'ywcm_show_in_shop_page' ) == 'yes' ) {
                add_action( 'woocommerce_archive_description', array( $this, 'print_message' ) );
            }

        }

        /**
         * Print Message
         *
         * Print single message in cart and checkout
         *
         * @return   void
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
         */
        public function print_message( ) {
            if( ! $this->messages ){
                return;
            }

	        foreach ( $this->messages as $key => $message ) {
		        if ( apply_filters( 'yith_ywcm_is_valid_message', $this->is_valid( $message->ID ), $message->ID ) ) {

			        $is_valid = true;


			        if ( is_cart() && apply_filters( 'ywcm_show_message_in_cart', defined('WOOCOMMERCE_CART') ) ) {
				        $message_position = get_post_meta( $message->ID, '_ywcm_message_cart_page_position', true );
				        $message_position = empty( $message_position ) ? 'woocommerce_before_cart_contents' : $message_position;


				        if ( current_action() != $message_position && ! apply_filters('ywcm_skip_check_on_action', false) ) {
					        $is_valid = false;
				        }
			        }

			        if ( $is_valid  ) {

				        $message_type = get_post_meta( $message->ID, '_ywcm_message_type', true );
				        $layout       = ( get_post_meta( $message->ID, '_ywcm_message_layout', true ) !== '' ) ? get_post_meta( $message->ID, '_ywcm_message_layout', true ) : 'layout';
				        $args         = ( method_exists( $this, 'get_' . $message_type . '_args' ) ) ? $this->{'get_' . $message_type . '_args'}( $message ) : false;
				        if ( $args ) {
					        $args['ywcm_id'] = $message->ID;
					        yit_plugin_get_template( YITH_YWCM_DIR, '/layouts/' . $layout . '.php', $args );
				        }

				        unset( $this->messages[ $key ] );
			        }
		        }
	        }
        }


        /**
         * Get Product Cart
         *
         * Return an array with the args to print into message or false if the message can't be print
         *
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
         * @return   mixed array || bool if the message can't be print
         */

        public function get_products_cart_args( $message ) {

            $args = array();

            $args['text'] = get_post_meta( $message->ID, '_ywcm_message_products_cart_text', true );

            if ( $args['text'] == '' ) {
                return false;
            }

	        $minimum_quantity    = get_post_meta( $message->ID, '_ywcm_message_products_cart_minimum', true );
	        $threshold_quantity  = get_post_meta( $message->ID, '_ywcm_products_cart_threshold_quantity', true );
	        $products            = get_post_meta( $message->ID, '_ywcm_products_cart_products', true );
	        $products_to_exclude = (array) get_post_meta( $message->ID, '_ywcm_products_cart_products_exclude', true );

	        $product_in_cart_quantity = 0;
	        $products_in_cart         = array();
	        $products_in_cart_titles  = array();



	        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
		        $product    = $values['data'];
		        $product_id = $values['data']->get_id();
		        $parent_id  = yit_get_base_product_id( $product );

		        $is_an_exclusion = in_array( $product_id, $products_to_exclude ) || in_array( $parent_id, $products_to_exclude );

		        if ( ! $is_an_exclusion && ( $products == '' || in_array( $product_id, $products ) || in_array( $parent_id, $products ) ) ) {

			        $products_in_cart[]                     = $product;
			        $products_in_cart_titles[ $product_id ] = $product->get_title();
			        $product_in_cart_quantity += $values['quantity'];
		        }

	        }

	        if ( empty( $products_in_cart_titles ) ) {
		        return false;
	        }
	        $product_in_cart_quantity = apply_filters('ywcm_product_in_cart_quantity', $product_in_cart_quantity );

	        if ( $threshold_quantity != '' && $product_in_cart_quantity < $threshold_quantity ) {
		        return false;
	        }

	        if ( $minimum_quantity != '' && $product_in_cart_quantity > $minimum_quantity ) {
		        return false;
	        }

            $remaining_quantity = apply_filters('ywcm_remaining_quantity_in_cart', $minimum_quantity - $product_in_cart_quantity, $message->ID);

	        if ( $remaining_quantity <= 0 ) {
		        return false;
	        }

            $titles             = implode( ', ', $products_in_cart_titles );

            $args['text']       = str_replace( '{remaining_quantity}', '<strong>' . $remaining_quantity . '</strong>', $args['text'] );
            $args['text']       = str_replace( '{products}', $titles, $args['text'] );
            $args['text']       = str_replace( '{quantity}', $product_in_cart_quantity, $args['text'] );
            $args['text']       = str_replace( '{required_quantity}', $minimum_quantity, $args['text'] );

	        $name =  ywcm_get_current_user_name();

	        if ( ! empty( $name ) ) {
		        $args['text'] = str_replace( '{user_name}', ' ' . ywcm_get_current_user_name(), $args['text'] );
	        }

	        $args['button'] = $this->get_button_options( $message->ID );
	        $args['slug']   = $message->post_name;

            return $args;

        }

        /**
         * Get Categories Cart
         *
         * Return an array with the args to print into message or false if the message can't be print
         *
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
         * @return   mixed array || bool if the message can't be print
         */

        public function get_categories_cart_args( $message ) {

            if ( empty( WC()->cart->cart_contents ) ) {
                return false;
            }

            $args = array();

	        $args['text'] = get_post_meta( $message->ID, '_ywcm_message_categories_cart_text', true );
	        if ( $args['text'] == '' ) {
		        return false;
	        }

	        $categories = get_post_meta( $message->ID, '_ywcm_message_category_cart_categories', true );
	        if ( $categories == '' ) {
		        return false;
	        }

            $products_in_cart_titles = array();
            $category_in_cart        = array();

	        foreach ( $categories as $category ) {
		        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
			        $product = $values['data'];
			        $product_id = $product->get_id();
			        $parent = yit_get_base_product_id( $product );

			        if ( has_term( $category, 'product_cat', $product->get_id() ) || has_term( $category, 'product_cat', $parent )  ) {
				        if ( ! in_array( $category, $category_in_cart ) ) {
					        $category_in_cart[] = $category;
				        }
				        $products_in_cart_titles[ $product->get_id() ] = $product->get_title();
			        }
		        }
	        }

            if ( empty( $category_in_cart ) ) {
                return false;
            }

            $titles       = implode( ', ', $products_in_cart_titles );
            $args['text'] = str_replace( '{products}', $titles, $args['text'] );
	        $name =  ywcm_get_current_user_name();
	        if ( ! empty( $name ) ) {
		        $args['text'] = str_replace( '{user_name}', ' ' . ywcm_get_current_user_name(), $args['text'] );
	        }

            $cat_names = array();
            //get categories names
            foreach ( $categories as $category ) {
                $term = get_term_by( 'slug', $category, 'product_cat' );
                if ( !empty( $term ) ) {
                    $cat_names[] = $term->name;
                }
            }
            $categories_titles = implode( ', ', $cat_names );
            $args['text']      = str_replace( '{categories}', $categories_titles, $args['text'] );
	        $name =  ywcm_get_current_user_name();
	        if ( ! empty( $name ) ) {
		        $args['text'] = str_replace( '{user_name}', ' ' . ywcm_get_current_user_name(), $args['text'] );
	        }
            $args['button']    = $this->get_button_options( $message->ID );
            $args['slug'] = $message->post_name;
            return $args;

        }


        /**
         * Get simple message
         *
         * Return an array with the args to print into message or false if the message can't be print
         *
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
         * @return   mixed array || bool if the message can't be print
         */

        public function get_simple_message_args( $message ) {

            $args         = array();
            $args['text'] = get_post_meta( $message->ID, '_ywcm_message_simple_message_text', true );

            if ( $args['text'] == '' ) {
                return false;
            }
	        $name =  ywcm_get_current_user_name();
	        if ( ! empty( $name ) ) {
		        $args['text'] = str_replace( '{user_name}', ' ' . ywcm_get_current_user_name(), $args['text'] );
	        }

            $args['slug'] = $message->post_name;
            $args['button'] = $this->get_button_options( $message->ID );
            return $args;

        }


        /**
         * Get button option
         *
         * return the button option of message
         *
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
         * @return   string
         */

        function get_button_options( $message_id ) {

            $button   = '';
            $btn_text = get_post_meta( $message_id, '_ywcm_message_button', true );
            $btn_url  = get_post_meta( $message_id, '_ywcm_message_button_url', true );
            $anchor   = get_post_meta( $message_id, '_ywcm_message_button_anchor', true);
            $anchor = $anchor ? ' target="_blank" ' : '';


            if ( $btn_text != '' && $btn_url != '' ) {
                $button = ' <a class="button" href="' . esc_url( $btn_url ) . '" '. $anchor .' rel="nofollow">' . $btn_text . '</a>';
            }

            return $button;
        }

        /**
         * Is a valid message
         *
         * return a boolean if the message is valid or is expired
         *
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
         * @return   string
         */

        function is_valid( $message_id ) {
	        $start = get_post_meta( $message_id, '_ywcm_message_start', true );
	        $expire = get_post_meta( $message_id, '_ywcm_message_expire', true );
            if ( $start == '' && $expire == '' ) {
                return true;
            }

            $today_dt  = new DateTime();

	        if ( $start != ''  ) {
		        $start_dt = new DateTime( $start );
		        if ( $today_dt < $start_dt ) {
			        return false;
		        }
	        }


	        if ( $expire != ''  ) {
		        $expire_dt = new DateTime( $expire );
		        if ( $today_dt > $expire_dt ) {
			        return false;
		        }
	        }

            return true;
	        
        }

        /**
         * plugin_row_meta
         *
         * add the action links to plugin admin page
         *
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         *
         * @return   Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      plugin_row_meta
         */
	    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWCM_INIT' ) {
		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
			    $new_row_meta_args['slug'] = YITH_YWCM_SLUG;
			    $new_row_meta_args['is_premium'] = true;
		    }

		    return $new_row_meta_args;
	    }


        public function register_pointer(){

            if( ! class_exists( 'YIT_Pointers' ) ){
                include_once( 'plugin-fw/lib/yit-pointers.php' );
            }

            $args[] = array(
                'screen_id'     => 'plugins',
                'pointer_id' => 'yith_woocommerce_cart_messages',
                'target'     => '#toplevel_page_yit_plugin_panel',
                'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
                    __( 'YITH WooCommerce Cart Messages', 'yith-woocommerce-cart-messages' ),
                    __( 'In the YIT Plugins tab you can find the YITH WooCommerce Cart Messages options.
With this menu, you can access to all the settings of our plugins that you have activated.
YITH WooCommerce Cart Messages is available in an outstanding PREMIUM version with many new options, <a href="'.$this->_premium_landing.'">discover it now</a>', 'yith-woocommerce-cart-messages' )
                ),
                'position'   => array( 'edge' => 'left', 'align' => 'center' ),
                'init'  => YITH_YWCM_FREE_INIT
            );


            YIT_Pointers()->register( $args );
        }


    }
}

