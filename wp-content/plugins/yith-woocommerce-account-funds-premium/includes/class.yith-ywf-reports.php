<?php
if( !defined('ABSPATH')){
    exit;
}

if( !class_exists( 'YITH_YWF_Reports')){

    class YITH_YWF_Reports extends WC_Admin_Reports{

        protected static $_instance = null;
        protected $report_path;

        /**
         * YITH_YWF_Reports constructor.
         */
        public function __construct()
        {
            $this->report_path = YITH_FUNDS_INC.'reports/';

            add_filter('woocommerce_admin_reports', array( $this, 'add_funds_reports' ) );

        }

        /**
         * Add funds reports to wc_reports
         * @author YITHEMES
         * @since 1.0.0
         * @param array $reports
         * @return array
         */
        public function add_funds_reports( $reports ){

            $fund_reports = array(
              'ywf_funds' => array(
                    'title' => __('Deposits', 'yith-woocommerce-account-funds'),
                  'reports' => array(
                      "deposit_order" => array(
                          'title' => __('Deposits','yith-woocommerce-account-funds'),
                          'description' => '',
                          'hide_title' => true,
                          'callback' => array( $this, 'load_reports' )
                      )
                  )
              ),
            );

           /* $reports['orders']['reports']['sales_by_funds'] = array(
                    'title' => __('Sales by funds', 'yith-woocommerce-account-funds'),
                    'description' => '',
                    'hide_title' => true,
                    'callback' => array( $this, 'load_reports')
            );*/

            return array_merge( $reports, $fund_reports );
        }

        /**
         * @author YITHEMES
         * @since 1.0.0
         * @param string $name
         */
        public function load_reports( $name ){

            $class = 'YITH_YWF_Report_'.$name ;
            $name  = 'class.yith-ywf-report-' . sanitize_title( str_replace( '_', '-', $name ) ) . '.php';

            if ( file_exists( $this->report_path . $name ) ) {
                include_once( $this->report_path . $name );
            } else if ( ! class_exists( $class ) ) {
                return;
            }

            $report = new $class();
            $report->output_report();
        }

        /**
         * @param $path
         * @param $name
         * @param $class
         * @return mixed
         */
        public function ywf_admin_reports_path( $path, $name, $class ){

            return $path;
        }

        /**
         * @author YITHEMES
         * @since 1.0.0
         * @return YITH_Funds unique access
         */
        public static function get_instance(){

            if( is_null( self::$_instance ) ){

                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Output an export link
         */
        public function get_export_button() {
            $current_range = $this->get_current_date_range();
            ?>
            <a
                href="#"
                download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time( 'timestamp' ) ); ?>.csv"
                class="export_csv"
                data-export="chart"
                data-xaxes="<?php _e( 'Date', 'yith-woocommerce-account-funds' ); ?>"
                data-groupby="<?php echo isset( $this->chart_groupby ) ? $this->chart_groupby : ''; ?>"
            >
                <?php _e( 'CSV export', 'yith-woocommerce-account-funds' ); ?>
            </a>
            <?php
        }

        /**
         * Get hte current date range
         *
         * @return string The current range
         */
        public function get_current_date_range(){
            $current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

            if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
                $current_range = '7day';
            }
            return $current_range;
        }

        /**
         * Get the date ranges
         *
         * @return array
         */
        public function get_ranges(){
            return array(
                'year'       => __( 'Year', 'yith-woocommerce-account-funds' ),
                'last_month' => __( 'Last month', 'yith-woocommerce-account-funds' ),
                'month'      => __( 'This month', 'yith-woocommerce-account-funds' ),
                '7day'       => __( 'Last 7 days', 'yith-woocommerce-account-funds' )
            );
        }

        /**
         * Get the date query args for WP_Query
         *
         * @param $start_date   The start date
         * @param $end_date     The end date
         *
         * @return array The query args
         */
        public function get_wp_query_date_args( $start_date, $end_date ) {
            return array(
                'date_query' => array(
                    'after'     => array(
                        'year'  => date( 'Y', $start_date ),
                        'month' => date( 'n', $start_date ),
                        'day'   => date( 'j', $start_date )
                    ),
                    'before'    => array(
                        'year'  => date( 'Y', $end_date ),
                        'month' => date( 'n', $end_date ),
                        'day'   => date( 'j', $end_date )
                    ),
                    'inclusive' => true
                )
            );
        }

    }
}

/**
 * Main instance of plugin
 *
 * @return YITH_YWF_Reports
 * @since  1.0
 */
if ( ! function_exists( 'YITH_YWF_Reports' ) ) {
    /**
     * @return YITH_YWF_Reports
     */
    function YITH_YWF_Reports() {
        return YITH_YWF_Reports::get_instance();
    }
}

YITH_YWF_Reports();
