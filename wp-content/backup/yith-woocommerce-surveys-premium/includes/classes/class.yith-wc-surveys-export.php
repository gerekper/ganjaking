<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Surveys_Export' ) ){

    class YITH_WC_Surveys_Export{

        public static $instance;

        /**
         * @var string Separator from columns in CSV files
         */
        public $fields_separator = ";";

        /**
         * @var string Separator for new lines in CSV files
         */
        public $newline_separator = "\r\n";


        public function __construct(){

            $this->columns = array(
                'answer' => __( 'Answer', 'yith-woocommece-surveys' ),
                'tot_votes' => __( 'Votes', 'yith-woocommece-surveys' ),
                'visible_in' => __( 'Visible in', 'yith-woocommece-surveys' ),
                'tot_order' => __( 'Order totals', 'yith-woocommece-surveys' ),
                'order_details' => __( 'Order Details', 'yith-woocommece-surveys' )
            );
        }

        /**
         * @author YIThemes
         * @since 1.0.0
         * @return YITH_WC_Surveys_Export
         */
        public static function get_instance(){

            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function export_data( $items, $filters = array() ){

            $date = date("Y_m_d_H_i_s");

            $file_name = 'survey_export_'.$date.'.csv';

            foreach( $filters as $filter )
                $items = $this->filter_by_type( $items, $filter );


            file_put_contents( $file_name, $this->render_surveys( $items ) );
            yith_download_file( $file_name );

        }

        /**
         * Build a CSV row for columns title
         *
         * @param $columns columns to be shown
         *
         * @return string CSV formatted row
         */
        public function get_csv_columns_title_row() {

            $csv_title = '';

            foreach ( $this->columns as $key=>$column ) {
                $csv_title .= ucwords( strtolower( $column ) ) . $this->fields_separator;
            }

            $csv_title .= $this->newline_separator;
            return $csv_title;
        }

        /**
         * Build a CSV row for a specific survey
         *
         * @param $survey
         */
        public function get_survey_csv( $survey ) {

            $csv_row = '';

            foreach ($this->columns as $key=>$column ) {

                $value = is_array( $survey[$key] ) ? implode(',', $survey[$key] ): $survey[$key];
                $csv_row .= '"' . trim( $value ) . '"' . $this->fields_separator;
            }

            return $csv_row;
        }

        /**
         * @param $surveys
         * @return string
         */
        public function render_surveys( $surveys ){


            $survey_csv = $this->get_csv_columns_title_row();


            foreach( $surveys as $survey ){

                $survey_csv.= $this->get_survey_csv( $survey );
                $survey_csv .= $this->newline_separator;
            }

            return $survey_csv;
        }

        /**
         * filter by survey type
         * @author YIThemes
         * @since 1.0.0
         * @param $items
         * @param $type
         * @return array
         */
        private function filter_by_type( $items, $type )
        {
                $index = 'visible_in';
                $new_items = array();


                foreach( array_keys( $items ) as $key ) {
                    $temp[$key] = $items[$key][$index];

                    if ( $temp[$key] != $type ) {
                        $new_items[$key] = $items[$key];

                    }
                }

                return $new_items;

        }
    }
}