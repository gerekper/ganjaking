<?php

if( !defined( 'ABSPATH' ) ){
    exit;
}

if (! class_exists( 'YITH_WSFL_Install' ) ){

    /**Create plugin table
     * @since 1.0.0
     * Class YITH_WSFL_Install
     */
    class YITH_WSFL_Install {

        /**Single instance of class
         * @since 1.0.0
         * @var \YITH_WSFL_Install
         */
        private static $instance;

        /**The table name
         * @since 1.0.0
         * @var string
         */
        public $_table_name;

        /**Construct class
         * @since 1.0.0
         */
        public function __construct(){

            global $wpdb;

            $this->_table_name  =   $wpdb->prefix. 'ywsfl_list';
            $wpdb->yith_wsfl_table  =   $this->_table_name;

            define( 'YWSFL_LIST_TABLE', $this->_table_name );

        }

        public function init(){

            $this->_add_table();
            $this->_add_page();
            update_option( 'ywsfl_db_version', YWSFL_DB_VERSION );
        }

        /** update db
         * @author YIThemes
         * @since 1.0.1
         */
        public function update(){

            $this->_update_table();
            update_option( 'ywsfl_db_version', YWSFL_DB_VERSION );
        }

        public function is_table_created(){
            global $wpdb;
            $number_of_tables = $wpdb->query("SHOW TABLES LIKE '{$this->_table_name}'" );

            return (bool) ( $number_of_tables == 1 );

        }
        private function _add_table(){
            if( !$this->is_table_created() ) {

                global $wpdb;

                $sql    =   "CREATE TABLE {$this->_table_name} (
                                  ID int( 11 ) NOT NULL AUTO_INCREMENT,
                                  product_id int( 11 ) NOT NULL,
                                  variation_id int ( 11 ) DEFAULT -1,
                                  user_id int( 11 ) NOT NULL,
                                  quantity int ( 11 ) NOT NULL,
                                  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  PRIMARY KEY( ID ),
                                  KEY( product_id )
                                )DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

                if (! function_exists( 'dbDelta' ) )
                        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

                dbDelta($sql);
            }
            return;
        }

        /** add a column in table
         * @author YIThemes
         * @since 1.0.1
        */
        private function _update_table(){

            global $wpdb;

            $sql    =   "ALTER TABLE {$this->_table_name} ADD COLUMN variation_id int ( 11 ) DEFAULT -1;";
            $wpdb->query( $sql );

            return;
        }

        private function _add_page() {
            global $wpdb;

            $option_value = get_option( 'yith-wsfl-page-id' );

            if ( $option_value > 0 && get_post( $option_value ) )
                return;

            $page_found = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_name` = 'saveforlater' LIMIT 1;" );
            if ( $page_found ) :
                if ( ! $option_value )
                    update_option( 'yith-wsfl-page-id', $page_found );
                return;
            endif;

            $page_data = array(
                'post_status' 		=> 'publish',
                'post_type' 		=> 'page',
                'post_author' 		=> 1,
                'post_name' 		=> esc_sql( _x( 'saveforlater', 'page_slug', 'yith-woocommerce-save-for-later' ) ),
                'post_title' 		=> __( 'Save for later', 'yith-woocommerce-save-for-later' ),
                'post_content' 		=> '[yith_wsfl_saveforlater]',
                'post_parent' 		=> 0,
                'comment_status' 	=> 'closed'
            );
            $page_id = wp_insert_post( $page_data );

            update_option( 'yith-wsfl-page-id', $page_id );
            update_option( 'yith_wsfl_saveforlater_page_id', $page_id );
        }

        /**return single instance of class
         * @since 1.0.0
         * @return YITH_WSFL_Install
         */
        public static function  get_instance(){
            if( is_null( self::$instance ) ){
                self::$instance = new self();
            }
            return self::$instance;
        }



    }
}

function YITH_WSFL_Install(){
    return YITH_WSFL_Install::get_instance();
}