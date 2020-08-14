<?php


if ( !defined( 'YITH_COG_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( !class_exists( 'YITH_COG_Custom_Columns' ) ) {
    /**
     * YITH_COG_Custom_Columns
     *
     * @since 1.0.0
     */
    class YITH_COG_Custom_Columns {

        /**
         * Single instance of the class
         *
         * @var \YITH_COG_Custom_Columns
         * @since 1.0.0
         */
        protected static $instance;


        /**
         * Returns single instance of the class
         *
         * @return \YITH_COG_Custom_Columns
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$instance ) ) {
                $self::$instance = new $self;
            }

            return $self::$instance;
        }

        /**
         * Constructor
         */
        public function __construct() {

            // Custom columns
            add_filter( 'yith_add_custom_columns', array( $this, 'add_custom_column' ), 10, 1);
            add_filter( 'yith_add_custom_columns_stock', array( $this, 'add_custom_column_stock' ), 10, 1);
            add_filter( 'yith_columns_switch', array( $this, 'custom_columns_switch' ), 10, 1);
            add_filter( 'yith_columns_switch_stock', array( $this, 'custom_columns_switch_stock' ), 10, 1);

            // Tag columns
            add_filter( 'yith_add_custom_columns', array( $this, 'add_tag_column' ), 10, 1);
            add_filter( 'yith_add_custom_columns_stock', array( $this, 'add_tag_column_stock' ), 10, 1);

            // Margin percentage columns
            add_filter( 'yith_add_custom_columns', array( $this, 'add_margin_percentage_column' ), 10, 1);
        }


        /** Custom Columns. ****************************************************/

        /**
         * Add a Custom Field Column.
         */
        public function add_custom_column( $columns ){

            $custom_field_names = get_option('yith_cog_add_columns');

            if (!empty($custom_field_names)){
                $custom_fields_array = array_map( 'trim', explode( ",", $custom_field_names ));

                foreach ( $custom_fields_array as $custom_field_name ){
                    if (isset($custom_field_name)) {
                        $columns[$custom_field_name] = esc_html__( $custom_field_name, 'yith-cost-of-goods-for-woocommerce' );
                    }
                }
            }
            return $columns;
        }



        /**
         * Add a Custom Field Column content.
         */
        public function custom_columns_switch( $column_name ){

            global $product;

            switch ( $column_name ) {

                case $column_name :

                    $custom_value = yit_get_prop( $product, $column_name );
                    echo $custom_value;

                    break;
            }
        }


        /** Filter products by tags. ****************************************************/

        /**
         *  Include a Tag Column option.
         */
        public function include_tag_column() {

            return 'yes' === get_option( 'yith_cog_tag_column' );
        }


        /**
         * Add a Tag Column.
         */
        public function add_tag_column( $columns ){

            if ( $this->include_tag_column() == true ){
                $columns['tag'] = esc_html__( 'Tags', 'yith-cost-of-goods-for-woocommerce' );
            }
            return $columns;
        }



        /** Custom Columns Stock. ****************************************************/

        /**
         * Add a Custom Field Column.
         */
        public function add_custom_column_stock( $columns ){

            $custom_field_names = get_option('yith_cog_add_columns');

            if (!empty($custom_field_names)){
                $custom_fields_array = array_map( 'trim', explode( ",", $custom_field_names ));

                foreach ( $custom_fields_array as $custom_field_name ){
                    if (isset($custom_field_name)) {
                        $columns[$custom_field_name] = esc_html__( $custom_field_name, 'yith-cost-of-goods-for-woocommerce' );
                    }
                }
            }
            return $columns;
        }


        /**
         * Add a Custom Field Column content.
         */
        public function custom_columns_switch_stock( $column_name ){

            global $product;

            switch ( $column_name ) {

                case $column_name :

                    $custom_value = yit_get_prop( $product, $column_name );
                    echo $custom_value;

                    break;
            }
        }


        /** Filter products by tags. ****************************************************/

        /**
         * Add a Tag Column.
         */
        public function add_tag_column_stock( $columns ){

            if ( $this->include_tag_column() == true ){
                $columns['tag'] = esc_html__( 'Tags', 'yith-cost-of-goods-for-woocommerce' );
            }
            return $columns;
        }


        /**
         *  Include a Tag Column option.
         */
        public function include_margin_percentage_column() {

            return 'yes' === get_option( 'yith_cog_percentage_column' );
        }


        /**
         * Add a Tag Column.
         */
        public function add_margin_percentage_column( $columns ){

            if ( $this->include_margin_percentage_column() == true ){
                $columns['margin_percentage'] = esc_html__( 'Margin %', 'yith-cost-of-goods-for-woocommerce' );
            }
            return $columns;
        }
    }
}