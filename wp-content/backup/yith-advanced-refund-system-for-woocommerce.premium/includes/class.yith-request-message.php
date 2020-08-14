<?php

if ( ! defined( 'YITH_WCARS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Request_Message
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Request_Message' ) ) {
    /**
     * Class YITH_Request_Message
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Request_Message {

        /**
         * Message ID
         *
         * @var int
         * @since 1.0
         */
        public $ID = 0;

        /**
         * @var string Request ID
         */
        public $request = null;

        /**
         * Message date
         *
         * @var string
         * @since 1.0
         */
        public $date = null;

        /**
         * Message content
         *
         * @var string
         * @since 1.0
         */
        public $message = null;

        /**
         * Author ID
         *
         * @var string
         * @since 1.0
         */
        public $author = null;

        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0
         */
        public function __construct( $id = 0 ) {
            global $wpdb;

            $this->ywcars_messages_table = $wpdb->prefix . YITH_ARS_DB::$ywcars_messages_table;
            $this->ywcars_messagemeta_table = $wpdb->prefix . YITH_ARS_DB::$ywcars_messagemeta_table;
            if ( $id ) {
                $this->get_data( $id );
            }
        }

        /**
         * Save the current object
         */
        public function save() {
            global $wpdb;

            if ( $this->request && $this->message && $this->author ) {
                $insert_query =
                    "INSERT INTO $this->ywcars_messages_table ( ID, request, date, message, author )"
                    . " VALUES ( NULL, '" . $this->request . "', NOW(), '" . $this->message . "', '" . $this->author . "' )";
                $wpdb->query( $insert_query );
                $this->ID = $wpdb->insert_id;
            }
        }

        private function get_data( $id ) {
            global $wpdb;

            $this->ID = $id;

            $query   = $wpdb->prepare( "SELECT * FROM $this->ywcars_messages_table WHERE ID = %d", $this->ID );
            $results = $wpdb->get_row( $query, ARRAY_A );

            $this->request = $results['request'];
            $this->date = $results['date'];
            $this->message = $results['message'];
            $this->author = $results['author'];

        }

        function add_message_meta( $meta_key, $meta_value ) {
            global $wpdb;

            if ( $meta_key && $meta_value ) {
                $insert_query =
                    "INSERT INTO $this->ywcars_messagemeta_table ( meta_id, message, meta_key, meta_value )"
                    . " VALUES ( NULL, '" . $this->ID . "','" . $meta_key . "', '" . $meta_value . "' )";
                $wpdb->query( $insert_query );
            }
        }

        function get_message_metas() {
            global $wpdb;

            if ( $this->ID ) {
                $query   = $wpdb->prepare( "SELECT * FROM $this->ywcars_messagemeta_table WHERE message = %d", $this->ID );
                $results = $wpdb->get_results( $query, ARRAY_A );

                if ( ! $results ) {
                    return false;
                }
                $message_metas = array();
                foreach ( $results as $result ) {
                    $meta_key = $result['meta_key'];
                    $meta_value = $result['meta_value'];
                    $message_metas[$meta_key] = $meta_value;
                }
                return $message_metas;
            }
        }


    }
}