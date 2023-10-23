<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if( ! class_exists( 'YITH_Frontend_Manager_Admin' ) ){

    class YITH_Frontend_Manager_Admin {

        /**
         * @var YIT_Plugin_Panel_Woocommerce instance
         */
        protected $_panel;

        /**
         * @var YIT_Plugin_Panel_Woocommerce instance
         */
        protected $_panel_page = 'yith_wcfm_panel';

        /**
         * @var string Official plugin documentation
         */
        protected $_official_documentation = 'https://docs.yithemes.com/yith-frontend-manager-for-woocommerce/';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-frontend-manager/';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live = '';

        /**
         * YITH_Frontend_Manager_Admin constructor.
         */
        public function __construct(){

            /* Action links and Row meta */
	        /* === Show Plugin Information === */
	        add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCFM_PATH . '/' . basename( YITH_WCFM_FILE ) ), array( $this, 'action_links' ) );

            /* Panel Settings */
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
            add_action( 'update_option', array( $this, 'check_save_endpoint' ), 10, 3 );

            /* Premium Tab */
            add_action( 'yith_wcfm_premium_tab', array( $this, 'show_premium_tab' ) );

            /* Style & Scripts */
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            /* Remove the wp admin bar if the user check the "remove" option */
            add_action( 'admin_bar_menu', array( $this, 'admin_bar_menus' ), 35 );

	        add_action( 'woocommerce_admin_field_yith_wcfm_button', array( $this, 'admin_field_button' ), 10, 1 );

	        if( current_user_can( 'manage_woocommerce' ) ){
		        /* === Admin Message if the gateway is enabled for vendors === */
		        add_action( 'admin_notices', array( $this, 'print_wc_stripe_connect_redirect_uri_message' ) );
            }
	        if( ! wp_doing_ajax() ){
		        /**
		         * Flush rewrite rule after:
		         *
		         * Theme switching
		         * User Switching
		         * Login
		         * Logout
		         * New user created
		         * New Vendor Created/Updated
		         * YITH WooCommerce Multi Vendor plugin activation
		         * Click on Flush Rewrite rules button
                 * Save Permalinks structure
		         */
		        $flush_rewrite_rules_actions = array(
			        'after_switch_theme',
			        'switch_theme',
			        'switch_to_user',
			        'switch_back_user',
			        'switch_off_user',
			        'wp_login',
			        'wp_logout',
			        'edit_user_created_user',
			        'yith_wpv_after_save_taxonomy',
			        'yith_wcmv_after_setup',
			        'generate_rewrite_rules'
		        );

		        foreach( $flush_rewrite_rules_actions as $action ){
			        add_action( $action, 'YITH_Frontend_Manager::regenerate_transient_rewrite_rule_transient' );
		        }
	        }
        }

        /**
         * Add Scripts & Styles
         *
         * @return   void
         * @since    1.0
         * @author   YITH <plugins@yithemes.com>
         *
         */
        public function enqueue_scripts() {
            global $pagenow;
	        if ( 'admin.php' == $pagenow && ! empty( $_GET['page'] ) && $this->_panel_page == $_GET['page'] ) {
		        wp_enqueue_style( 'yith_wcfm_admin', YITH_WCFM_STYLE_URL . 'admin.css', array(), YITH_WCFM_VERSION );
		        $enabled_label  = _x( 'enabled', '[admin endpoints section]: label', 'yith-frontend-manager-for-woocommerce' );
		        $disabled_label = _x( 'disabled', '[admin endpoints section]: label', 'yith-frontend-manager-for-woocommerce' );
		        $inline_style   = sprintf( '.yith_wcfm_section_enabled h2:after {content: "%s"}.yith_wcfm_section_disabled h2:after {content: "%s"}', $enabled_label, $disabled_label );
		        wp_add_inline_style( 'yith_wcfm_admin', $inline_style );
	        }

            $allowed_tabs = array( 'settings' );
            $is_allowed_tab = empty( $_GET['tab'] ) || ( ! empty( $_GET['tab'] ) && in_array( $_GET['tab'], $allowed_tabs ) );
            if( 'admin.php' == $pagenow && ! empty( $_GET['page'] ) && $this->_panel_page == $_GET['page'] && $is_allowed_tab ){
                wp_enqueue_script( 'yith_wcfm_admin_script', YITH_WCFM_SCRIPT_URL . 'admin.js', array( 'jquery' ), YITH_WCFM_VERSION, true );
                $script_args = array(
                    'tab' => ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'settings',
                    'flush_confirm_message' => __( 'Are you sure you want to flush permalink settings?', 'yith-frontend-manager-for-woocommerce' ),
                    'flushed_message'       => __( 'Flushed!', 'yith-frontend-manager-for-woocommerce' )
                );

                wp_localize_script( 'yith_wcfm_admin_script', 'yith_wcfm', $script_args );
            }

            $this->enqueue_stripe_connect_scripts();

            do_action( 'yith_wcfm_admin_enqueue_scripts' );
        }

	    /**
	     * Enqueue Scripts
	     *
	     * @return void
	     * @since 2.6.0
	     */
	    public function enqueue_stripe_connect_scripts() {
		    $current_page = isset( $_GET['page'] ) ? $_GET['page'] : '';
		    if ( 'yith_wcfm_panel' == $current_page ) {
			    wp_enqueue_script( 'yith-wcsc-admin' );
		    }
	    }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @use      /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {

            if ( ! empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = apply_filters( 'yith_wcfm_admin_tabs', array(
                'settings'      => __( 'General Settings', 'yith-frontend-manager-for-woocommerce' ),
                'endpoints'     => __( 'Endpoints', 'yith-frontend-manager-for-woocommerce' ),
                'premium'       => __( 'Premium Version', 'yith-frontend-manager-for-woocommerce' ),
            ) );

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => 'YITH Frontend Manager for WooCommerce',
                'menu_title'       => 'Frontend Manager',
				/**
				 * APPLY_FILTERS: yit_wcfm_plugin_options_capability
				 *
				 * Filters the capability for see menu item.
				 *
				 * @param string $capability plugin menu capability.
				 *
				 * @return string
				 */
                'capability'       => apply_filters( 'yit_wcfm_plugin_options_capability', 'manage_options' ),
                'parent'           => '',
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_WCFM_PATH . 'settings',
                'plugin_slug'      => YITH_WCFM_SLUG,
                'is_premium'       => defined( YITH_WCFM_PREMIUM ),
            );

            $this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
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
         * @return mixed
         * @use      plugin_action_links_{$plugin_file_name}
         */
        public function action_links( $links ) {
	        $links = yith_add_action_links( $links, $this->_panel_page, true, YITH_WCFM_SLUG );
	        return $links;
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
         * @use      plugin_row_meta
         */
	    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCFM_INIT' ) {
		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
			    $new_row_meta_args['slug']       = YITH_WCFM_SLUG;
			    $new_row_meta_args['is_premium'] = true;
		    }

		    return $new_row_meta_args;
	    }

        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri() {
            return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing;
        }

        /**
         * Show the premium tabs
         *
         * @since   1.0.0
         * @return  string The premium landing link
         */
        public function show_premium_tab() {
            yith_wcfm_get_template( 'premium', array(), 'admin' );
        }

        /**
         * Set a transient if the admin change the frontend manager endpoint
         *
         * @since   1.0.0
         * @return  string The premium landing link
         */
        public function check_save_endpoint( $option, $old_value, $new_value ){
            //@TODO: Check if transient works fine
            $regex = '/yith_wcfm_.*_section_slug/';
            $endpoint_is_changed = get_site_transient( YITH_Frontend_Manager()->get_rewrite_rules_transient() );
            if( ! $endpoint_is_changed && $old_value !== $new_value && preg_match( $regex, $option ) ){
                set_site_transient( YITH_Frontend_Manager()->get_rewrite_rules_transient(), true );
            }
        }

        /**
         * Add admin bar menu item
         *
         * @since   1.0.0
         * @return  bool false or nothing
         */
        public function admin_bar_menus( $wp_admin_bar ){
	        /**
	         * if is on frontend or user not logged in: Stop!
	         */
            if ( ! is_admin() || ! is_user_logged_in() ) {
	            return false;
            }

	        /**
	         * Show only when the user is a member of this site, or they're a super admin.
	         */
            if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
	            return false;
            }

            $main_page_url = yith_wcfm_get_main_page_url();

	        /**
	         * is Frontend Manager main page url set
	         */
            if( empty( $main_page_url ) ){
	            return false;
            }

            if( ! YITH_Frontend_Manager()->current_user_can_manage_woocommerce_on_front() ){
                return false;
            }

            // Add an option to visit frontend manager page
            $wp_admin_bar->add_node( array(
                'parent' => 'site-name',
                'id'     => 'view-frontend-manager',
                'title'  => __( 'Frontend Manager', 'yith-frontend-manager-for-woocommerce' ),
                'href'   => $main_page_url,
            ) );
        }

        /**
         * Add the custom typoe option "button"
         *
         * @param $value field value
         *
         * @since  1.0
         * @return void
         */
        public function admin_field_button( $value ) {
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                </th>
                <td class="forminp">
                    <input type="button" name="force_review" id="<?php echo $value['id'] ?>" value="<?php echo $value['name'] ?>" class="button-secondary" />
                    <span class="description with-spinner">
                        <?php echo $value['desc']; ?>
                    </span>
                    <span class="spinner"></span>
                </td>
            </tr>
            <?php
        }

	    /**
	     * Add redirect URI message for vendors
	     *
	     * @since 2.6.0
	     * @return void
	     */
	    public function print_wc_stripe_connect_redirect_uri_message() {
		    $current_page  = isset( $_GET['page'] ) ? $_GET['page'] : '';
		    $section       = isset( $_GET['section'] ) ? $_GET['section'] : '';
		    $redirect_uri  = yith_wcfm_get_stripe_redirect_uri_for_vendors();

		    if ( defined( 'YITH_WCSC_PREMIUM' ) && 'yes' != get_option( 'yith_wcmv_redirected_uri_for_vendors_on_front', 'no' ) && ( 'yith_wcsc_panel' == $current_page || 'yith-stripe-connect' == $section || 'yith_wpv_panel' == $current_page || 'yith_wcfm_panel' == $current_page ) ) {
			    ?>
                <div class="notice notice-warning yith_wcsc_message yith_wcsc_message_redirect_uri_for_vendors_frontend"
                     data-action="redirect_uri_done_for_vendors_on_front">
                    <p><?php echo sprintf( __( '<b>YITH Stripe Connect for WooCommerce (Frontend Manager Integration) -</b> Define the following <b>Redirect URI</b> %s in your <b>Redirect URIs</b> section at the following path <a href="%s" target="_blank">Stripe Dashboard > Connect > Settings</a>.', 'yith-stripe-connect-for-woocommerce' ), '<code>' . $redirect_uri . '</code>', 'https://dashboard.stripe.com/account/applications/settings' ); ?></p>
                    <p>
                        <a class="button-primary"> <?php echo __( 'Done', 'yith-frontend-manager-for-woocommerce' ); ?> </a>
                    </p>

                </div>
			    <?php
		    }
	    }
    }
}
