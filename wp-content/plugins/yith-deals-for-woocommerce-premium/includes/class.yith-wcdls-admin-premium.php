<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'YITH_WCDLS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Deals_Admin_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_Deals_Admin_Premium' ) ) {
    /**
     * Class YITH_Deals_Admin_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Deals_Admin_Premium extends YITH_Deals_Admin
    {
        
        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct()
        {
            /* === Register Panel Settings === */
            $this->show_premium_landing = false;


            /* Register plugin to licence/update system */
            add_action('wp_loaded', array($this, 'register_plugin_for_activation'), 99);
            add_action('admin_init', array($this, 'register_plugin_for_updates'));
            add_action( 'woocommerce_admin_field_yith_wcdls_box_size', array( $this, 'set_box_size' ), 10, 1 );

            parent::__construct();
        }


        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation()
        {
            if (!class_exists('YIT_Plugin_Licence')) {
                require_once YITH_WCDLS_PATH . '/plugin-fw/licence/lib/yit-licence.php';
                require_once YITH_WCDLS_PATH . '/plugin-fw/licence/lib/yit-plugin-licence.php';
            }
            YIT_Plugin_Licence()->register(YITH_WCDLS_INIT, YITH_WCDLS_SECRETKEY, YITH_WCDLS_SLUG);

        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates()
        {
            if (!class_exists('YIT_Upgrade')) {
                require_once(YITH_WCDLS_PATH . '/plugin-fw/lib/yit-upgrade.php');
            }
            YIT_Upgrade()->register(YITH_WCDLS_SLUG, YITH_WCDLS_INIT);
        }

        /**
         * Enqueue Scripts
         *
         * Register and enqueue scripts for Admin
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return void
         */

        public function enqueue_scripts()
        {
            global $post;
            parent::enqueue_scripts();
            wp_register_script('yith_wcdls_admin_premium', YITH_WCDLS_ASSETS_URL . 'js/wcdls-admin-premium.js', array('jquery', 'jquery-ui-sortable','jquery-ui-datepicker','wc-enhanced-select'), YITH_WCDLS_VERSION, true);

            wp_localize_script('yith_wcdls_admin_premium', 'yith_wcdls_admin', apply_filters('yith_wcdls_admin_localize', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'before_3_0' => version_compare( WC()->version, '3.0', '<' ) ? true : false,
                'search_categories_nonce' => wp_create_nonce( 'search-categories' ),
                'search_tags_nonce'       => wp_create_nonce( 'search-tags' ),
            )));
            if ( is_admin() && ( is_page('yith_wcdls_panel_product_deals') || isset( $post ) && 'yith_wcdls_offer' == $post->post_type ) ) {

                wp_enqueue_style( 'jquery-ui-style' );
                wp_enqueue_style('woocommerce_admin_styles');
                wp_enqueue_script('yith_wcdls_admin_premium');
            }

            do_action('yith_wcdls_enqueue_scripts_premium');
        }

        /**
         * Add box size to standard WC types
         *
         * @since 1.0.0
         * @access public
         * @author Francesco Licandro <francesco.licandro@yithemes.com>
         */
        public function set_box_size( $value ){

            $option_values = get_option( $value['id'] );
            $width  = isset( $option_values['width'] ) ? $option_values['width'] : $value['default']['width'];
            $height = isset( $option_values['height'] ) ? $option_values['height'] : $value['default']['height'];

            ?><tr valign="top">
            <th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
            <td class="forminp yith_box_size_settings">

                <input name="<?php echo esc_attr( $value['id'] ); ?>[width]" id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo $width; ?>" />
                &times;
                <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo $height; ?>" />px

                <div><span class="description"><?php echo $value['desc'] ?></span></div>

            </td>
            </tr><?php

        }

        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.0.3
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCDLS_INIT' ) {
            $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
                $new_row_meta_args['is_premium'] = true;
            }

            return $new_row_meta_args;
        }
        /**
         * Regenerate auction prices
         *
         * Action Links
         *
         * @return void
         * @since    1.0.3
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, $this->_panel_page, true );
            return $links;
        }

    }
}