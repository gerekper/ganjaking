<?php
/*
Plugin Name: WooCommerce PDF Invoices
Plugin URI: https://woocommerce.com/products/pdf-invoices/
Description: Attach a PDF Invoice to the completed order email and allow invoices to be downloaded from customer's My Account page. 
Version: 4.17.2
Author: Andrew Benbow
Author URI: http://www.chromeorange.co.uk
WC requires at least: 3.5.0
WC tested up to: 7.4.0
Woo: 228318:7495e3f13cc0fa3ee07304691d12555c
*/

/*  Copyright 2020  Andrew Benbow  (email : support@chromeorange.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    /**
     * Required functions
     */
    if ( ! function_exists( 'woothemes_queue_update' ) ) {
        require_once( 'woo-includes/woo-functions.php' );
    }

    /**
     * Plugin updates
     */
    woothemes_queue_update( plugin_basename( __FILE__ ), '7495e3f13cc0fa3ee07304691d12555c', '228318' );

    /**
     * Defines
     */
    define( 'PDFVERSION' , '4.17.2' );
    define( 'PDFLANGUAGE', 'woocommerce-pdf-invoice' );
    define( 'PDFSETTINGS' , admin_url( 'admin.php?page=woocommerce_pdf' ) );
    define( 'PDFSUPPORTURL' , 'http://support.woothemes.com/' );
    define( 'PDFDOCSURL' , 'http://docs.woothemes.com/document/woocommerce-pdf-invoice-setup-and-customization/');
    define( 'PDFPLUGINURL', plugin_dir_url( __FILE__ ) );
    define( 'PDFPLUGINPATH', plugin_dir_path( __FILE__ ) );
    define( 'PDFFONTSPATH', plugin_dir_path( __FILE__ ) . 'lib/fonts/' );

    /**
     * Localization
     */
    $locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-pdf-invoice' );
    load_textdomain( 'woocommerce-pdf-invoice', WP_LANG_DIR . "/woocommerce-pdf-invoice/woocommerce-pdf-invoice-$locale.mo" );
    load_plugin_textdomain( 'woocommerce-pdf-invoice', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    /**
     * Don't do anything else unless WC is active
     */
    if ( is_woocommerce_active() ) {

        // Load PDF Invoice Defaults
        include( 'classes/class-invoice-defaults.php' );

        // Load helper functions
        include( 'classes/helper-functions-class.php' );

        /**
         * Admin Settings
         */
        if ( is_admin() ) {
            include( 'classes/class-pdf-admin-functions.php' );
            include( 'classes/settings/class-pdf-settings-class.php' );
            include( 'classes/class-pdf-export.php' );
            include( 'classes/class-pdf-debug.php' );
            include( 'classes/class-pdf-order-meta-box.php' );
            // include( 'classes/class-pdf-template-editor.php' );
            
            // Include order meta class
            include( 'classes/class-show-hidden-order-meta.php' );

            // System Status Additions
            include( 'classes/systemstatus/system-status-additions-class.php' );

            // Admin Notices
            include( 'classes/systemstatus/admin-notices.php' );
        }

        /**
         * Sending PDFs and such
         * Only load if necessary
         * Prevents conflicts with other plugins that use DOMPDF
         */
        $actions_array = array( 'woocommerce_mark_order_status', 'pdfinvoice-admin-send-pdf', 'pdf_email_invoice', 'pdf_create_invoice' );

        if( 
            ( isset($_GET['action']) && in_array( $_GET['action'], $actions_array ) ) || 
            ( ! empty( $_POST['wc_order_action'] ) ) ||
            isset($_GET['pdfid'] ) 
        ) {
            require_once( 'classes/class-pdf-send-pdf-class.php' );
        }

        /**
         * Various PDF functions
         * - Order meta box
         * - My Account download PDF Invoice link
         */
        include( 'classes/class-pdf-functions-class.php' );

        // Upgrade PDF Invoices
        include( 'classes/class-pdf-upgrades-class.php' );

        /**
         * WPML Compatibility
         */
        include( 'classes/class-wpml-integration.php' );

    } // End is_woocommerce_active

    /**
     * Load Admin Class
     * Used for plugin links, seems to break if added to an include file
     * so it's got it's own class for now.
     */
    class WC_pdf_admin {

        public function __construct() {

            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this,'plugin_links' ) );

            // Support for Order / Customer CSV/XML Export plugin
            add_filter( 'wc_customer_order_export_csv_order_headers', array( $this, 'add_pdf_invoice_to_csv_export_column_headers' ) );
            add_filter( 'wc_customer_order_export_csv_order_row', array( $this, 'add_pdf_invoice_to_csv_export_column_data' ), 10, 3 );
            add_filter( 'wc_customer_order_export_xml_order_data', array( $this, 'add_pdf_invoice_to_xml_export_column_data' ), 10, 3 );


        }

        /**
         * Plugin page links
         */
        public static function plugin_links( $links ) {

            $plugin_links = array(
                '<a href="' . PDFSUPPORTURL . '">' . __( 'Support', 'woocommerce-pdf-invoice' ) . '</a>',
                '<a href="' . PDFDOCSURL . '">' . __( 'Docs', 'woocommerce-pdf-invoice' ) . '</a>',
            );

            return array_merge( $plugin_links, $links );

        }

        /**
         * [activate description]
         * @return [type] [description]
         */
        public static function activate() {
            self::do_install_woocommerce_pdf_invoice();
            self::do_install_update_order_meta_invoice_date();
            // self::do_install_woocommerce_pdf_invoice_pages();
            
        }

        /**
         * [deactivate description]
         * @return [type] [description]
         */
        public static function deactivate() {
            // empty
        }

        /**
         * Installation functions
         *
         * Create temporary folder and files. PDFs will be stored here as required
         */
        public static function do_install_woocommerce_pdf_invoice() {

            // Install files and folders for uploading files and prevent hotlinking
            $upload_dir =  wp_upload_dir();
            $upload_dir =  $upload_dir['basedir'] . '/woocommerce_pdf_invoice';

            $upload_dir =  apply_filters( 'woocommerce_pdf_invoice_pdf_upload_dir', $upload_dir );

            $files = array(
                array(
                    'base'      => $upload_dir,
                    'file'      => '.htaccess',
                    'content'   => '
order allow,deny
<Files ~ "\.(zip)$">
    allow from all
</Files>'
                ),
                array(
                    'base'      => $upload_dir,
                    'file'      => 'index.html',
                    'content'   => ''
                )
            );

            foreach ( $files as $file ) {

                if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {

                    if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
                        fwrite( $file_handle, $file['content'] );
                        fclose( $file_handle );
                    }

                }

            }

        } // do_install_woocommerce_pdf_invoice

        /**
         * Installation functions
         *
         * Install WooCommerce PDF Invoice template pages
         */
        public static function do_install_woocommerce_pdf_invoice_pages() {

            // Get the default invoice template ID
            $invoice_template_id = null !== get_option('woocommerce_pdf_invoice_main_template_id') ? get_option('woocommerce_pdf_invoice_main_template_id') : 0;

            // Get the default terms template ID
            $terms_template_id = null !== get_option('woocommerce_pdf_invoice_terms_template_id') ? get_option('woocommerce_pdf_invoice_terms_template_id') : 0;

            // Insert / Update main PDF Invoice template
            $invoice_args                   = self::get_pdf_invoice_template_content( $invoice_template_id, 'invoice' );
            $default_invoice_template_id    = wp_insert_post( $invoice_args );
            update_option( 'woocommerce_pdf_invoice_main_template_id', $default_invoice_template_id );

            // Insert / Update terms PDF Invoice template
            $terms_args                 = self::get_pdf_invoice_template_content( $terms_template_id, 'terms' );
            $default_terms_template_id  = wp_insert_post( $terms_args );
            update_option( 'woocommerce_pdf_invoice_terms_template_id', $default_terms_template_id );

        }

        /**
         * Installation functions
         *
         * Update order meta
         */
        public static function do_install_update_order_meta_invoice_date() {

            $_woocommerce_pdf_invoice_version = get_option( 'woocommerce_pdf_invoice_version' );

            if ( !$_woocommerce_pdf_invoice_version || version_compare( $_woocommerce_pdf_invoice_version, '4.5.4', '>=' ) ) {

                if( !class_exists('WC_pdf_functions') ){
                    include( 'classes/class-pdf-functions-class.php' );
                }

                WC_pdf_functions::pdf_invoice_update_order_meta_invoice_date();
            }

            delete_option( 'woocommerce_pdf_invoice_version' );

            // Fix for serialised _invoice_created date
            if ( !$_woocommerce_pdf_invoice_version || version_compare( $_woocommerce_pdf_invoice_version, '4.15.3', '>=' ) ) {

                if( !class_exists('WC_pdf_upgrades') ){
                    include( 'classes/class-pdf-upgrades-class.php' );
                }

                WC_pdf_upgrades::pdf_invoice_upgrade_order_meta_invoice_creation_date();
            }

            update_option( 'woocommerce_pdf_invoice_version', PDFVERSION );

        }

        /**
         * [add_pdf_invoice_to_csv_export_column_headers description]
         * @param [type] $column_headers [description]
         */
        public function add_pdf_invoice_to_csv_export_column_headers( $column_headers ) { 
 
            $column_headers['invoice_num']  = __('Invoice_Number', 'woocommerce-pdf-invoice');
            $column_headers['invoice_date'] = __('Invoice_Date', 'woocommerce-pdf-invoice');

            return $column_headers;
        }

        /**
         * [add_pdf_invoice_to_csv_export_column_data description]
         * @param [type] $order_data    [description]
         * @param [type] $order         [description]
         * @param [type] $csv_generator [description]
         */
        public function add_pdf_invoice_to_csv_export_column_data( $order_data, $order, $csv_generator ) {

            $settings = get_option( 'woocommerce_pdf_invoice_settings' );

            $order_id = $order->get_id();

            if( !class_exists('WC_send_pdf') ){
                include( 'classes/class-pdf-send-pdf-class.php' );
            }

            $one_row_per_item = false;
            $new_order_data   = array();

            $nvoice_num       = esc_html( get_post_meta( $order_id, '_invoice_number_display', true ) );

            // ( $order_id, $usedate, $sendsomething = false, $display_date = 'invoice', $date_format )
            $invoice_date     = esc_html( WC_send_pdf::get_woocommerce_pdf_date( $order_id,'completed', true, 'invoice', $settings['pdf_date_format']  ) );

            $pdf_data = array(
                    'invoice_num'   => $nvoice_num,
                    'invoice_date'  => $invoice_date,
            );

            // determine if the selected format is "one row per item"
            if ( version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ) {
                $one_row_per_item = ( 'default_one_row_per_item' === $csv_generator->order_format || 'legacy_one_row_per_item' === $csv_generator->order_format );
            // v4.0.0 - 4.0.2
            } elseif ( ! isset( $csv_generator->format_definition ) ) {
                // get the CSV Export format definition
                $format_definition = wc_customer_order_csv_export()->get_formats_instance()->get_format( $csv_generator->export_type, $csv_generator->export_format );
                $one_row_per_item = isset( $format_definition['row_type'] ) && 'item' === $format_definition['row_type'];
            // v4.0.3+
            } else {
                $one_row_per_item = 'item' === $csv_generator->format_definition['row_type'];
            }

            if ( $one_row_per_item ) {
                foreach ( $order_data as $data ) {
                    $new_order_data[] = array_merge( (array) $data, $pdf_data );
                }
            } else {
                $new_order_data = array_merge( $order_data, $pdf_data );
            }

            return $new_order_data;
        }

        /**
         * [add_pdf_invoice_to_xml_export_column_data description]
         * @param [type] $order_data [description]
         * @param [type] $order      [description]
         * @param [type] $this       [description]
         */
        public function add_pdf_invoice_to_xml_export_column_data( $order_data, $order, $xml ) {

            $settings = get_option( 'woocommerce_pdf_invoice_settings' );

            $order_id = $order->get_id();

            if( !class_exists('WC_send_pdf') ){
                include( 'classes/class-pdf-send-pdf-class.php' );
            }

            $one_row_per_item = false;
            $new_order_data   = array();

            $nvoice_num       = esc_html( get_post_meta( $order_id, '_invoice_number_display', true ) );

            // ( $order_id, $usedate, $sendsomething = false, $display_date = 'invoice', $date_format )
            $invoice_date     = esc_html( WC_send_pdf::get_woocommerce_pdf_date( $order_id,'completed', true, 'invoice', $settings['pdf_date_format']  ) );

            $pdf_data = array(
                    'invoice_num'   => $nvoice_num,
                    'invoice_date'  => $invoice_date,
            );

            $new_order_data = array_merge( $order_data, $pdf_data );

            return $new_order_data;
        }

    } // WC_pdf_admin

    if ( is_admin() ) {

        // Load the admin class
        $GLOBALS['WC_pdf_admin'] = new WC_pdf_admin();

        // Installation and uninstallation hooks
        register_activation_hook(__FILE__, array('WC_pdf_admin', 'activate'));
        register_deactivation_hook(__FILE__, array('WC_pdf_admin', 'deactivate'));

    }

    /**
     * woocommerce_pdf_invoice_temp_folder_check()
     *
     * Make sure temporary folder and files exist.
     * usefull if site if moved from test domain and plugin is already active
     *
     * Only happens when admin visits dashboard
     */
    add_action( 'wp_dashboard_setup', 'woocommerce_pdf_invoice_temp_folder_check' );
    add_action( 'wp_user_dashboard_setup', 'woocommerce_pdf_invoice_temp_folder_check' );

    function woocommerce_pdf_invoice_temp_folder_check() {

        $upload_dir =  wp_upload_dir();
        $upload_dir =  $upload_dir['basedir'] . '/woocommerce_pdf_invoice';
        // Filter to allow changing the location for PDF storeage
        $upload_dir =  apply_filters( 'woocommerce_pdf_invoice_pdf_upload_dir', $upload_dir );

        if ( !file_exists( $upload_dir . '/.htaccess' ) ) {
            WC_pdf_admin::do_install_woocommerce_pdf_invoice();
        }

    } // woocommerce_pdf_invoice_temp_folder_check()
