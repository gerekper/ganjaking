<?php
/**
 * WC_Report_Coupon_Campaign
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Reports
 * @version     2.1.0
 */

// TODO : name the class WC_Report_Coupon_Campaigns - I can't currently do this because the $name parameter for the WooCommerce get_report() method is passing in the wrong name. Not sure where it's getting that value from.

class WC_Report_Campaigns extends WC_Admin_Report {

    public  $chart_colours = array();
    public  $coupon_codes = array();
    private $taxonomy;
    private $selected_campaign;

    private $campaign_coupons;

    // data that's prepared for the main chart
    private $order_counts;
    private $order_discount_amounts;
    private $order_revenue;

    // data that's prepared for the legend
    private $legend_order_counts;
    private $legend_order_discount_amounts;
    private $legend_order_revenue;


    /**
     * Constructor
     */
    public function __construct() {

        global $wc_coupon_campaigns;
        $this->taxonomy = $wc_coupon_campaigns->tax;

        if ( isset( $_GET['coupon_codes'] ) && is_array( $_GET['coupon_codes'] ) ) {
            $this->coupon_codes = array_filter( array_map( 'sanitize_text_field', $_GET['coupon_codes'] ) );
        } elseif ( isset( $_GET['coupon_codes'] ) ) {
            $this->coupon_codes = array_filter( array( sanitize_text_field( $_GET['coupon_codes'] ) ) );
        }

        // the campaign we're currently looking at
        $this->selected_campaign = isset( $_GET['campaign'] ) ? $_GET['campaign'] : '';

        // prepare the data we'll need for the legend & chart
        if ( $this->selected_campaign ) {
            // $this->prepare_all_chart_data();
        }
    }

    /**
     * Get the legend for the main chart sidebar
     * @return array
     */
    public function get_chart_legend() {

        $legend = array();

        // we need a campaign selected to continue
        if( $this->selected_campaign ) {

            // prepare all data
            $this->prepare_all_chart_data();

            // get the legend data
            $this->get_legend_stats();

            $legend[] = array(
                'title' => sprintf( __( '%s total orders in campaign', 'wc_coupon_campaigns' ), '<strong>' . (int) $this->legend_order_counts . '</strong>' ),
                'color' => $this->chart_colours['campaign_orders' ],
                'highlight_series' => 0
            );
            $legend[] = array(
                'title' => sprintf( __( '%s total campaign discount', 'wc_coupon_campaigns' ), '<strong>' . wc_price( $this->legend_order_discount_amounts ) . '</strong>' ),
                'color' => $this->chart_colours['campaign_discount' ],
                'highlight_series' => 2
            );
            $legend[] = array(
                'title' => sprintf( __( '%s total revenue from campaign', 'wc_coupon_campaigns' ), '<strong>' . wc_price( $this->legend_order_revenue ) . '</strong>' ),
                'color' => $this->chart_colours['campaign_revenue' ],
                'highlight_series' => 1
            );

        }

        return $legend;
    }

    /**
     * Calculate the coupon statistics we'll need for the legend
     */
    public function get_legend_stats() {

        // make sure we have data set
        if ( $this->order_counts && $this->order_discount_amounts && $this->order_revenue) {
            // let's take the data we have for the chart and sum it up
            foreach( $this->order_counts as $key => $value ) {
                $this->legend_order_counts += $value[1];
            }
            foreach( $this->order_discount_amounts as $key => $value ) {
                $this->legend_order_discount_amounts += $value[1];
            }
            foreach( $this->order_revenue as $key => $value ) {
                $this->legend_order_revenue += $value[1];
            }
        }

    }

    /**
     * Get the coupon query
     * @return WP_Query
     */
    public function get_coupon_query() {

        $coupon_args = array(
            'post_type'      => 'shop_coupon',
            'posts_per_page' => -1,
            'post_status'    => array( 'publish', 'draft' ),
            'tax_query'      => array(
                array(
                    'taxonomy' => $this->taxonomy,
                    'field'    => 'id',
                    'terms'    => $this->selected_campaign
                )
            ),
            'orderby'        => 'title',
            'order'          => 'ASC'
        );

        return new WP_Query( $coupon_args );
    }

    /**
     * Output the report
     */
    public function output_report() {
        $ranges = array(
            'year'         => __( 'Year', 'wc_coupon_campaigns' ),
            'last_month'   => __( 'Last Month', 'wc_coupon_campaigns' ),
            'month'        => __( 'This Month', 'wc_coupon_campaigns' ),
            '7day'         => __( 'Last 7 Days', 'wc_coupon_campaigns' )
        );

        $this->chart_colours = array(
            'campaign_orders'   => '#3498db',
            'campaign_discount' => '#75b9e7',
            'campaign_revenue'  => '#e67e22'
        );

        $current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : '7day';

        if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
            $current_range = '7day';
        }

        $this->calculate_current_range( $current_range );

        include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php');
    }

    /**
     * [get_chart_widgets description]
     * @return array
     */
    public function get_chart_widgets() {
        $widgets = array();

        $widgets[] = array(
            'title'    => __( 'Filter by campaign', 'wc_coupon_campaigns' ),
            'callback' => array( $this, 'coupons_widget' )
        );

        return $widgets;
    }

    /**
     * Product selection
     * @return void
     */
    public function coupons_widget() {

        // get campaigns
        $campaign_options = $this->get_campaigns();

        // if we have campaigns then print the form to narrow them down
        if ( $campaign_options ) : ?>
            <form method="GET">
                <p>
                    <label for="campaign"><?php _e( 'Campaign:', 'wc_coupon_campaigns' ); ?></label> <select name="campaign" id="campaign" class="wc-enhanced-select chosen_select"><?php echo $campaign_options; ?></select>
                </p>
                <p>
                    <input type="submit" class="button" value="<?php _e( 'Show', 'wc_coupon_campaigns' ); ?>" />
                </p>
                <input type="hidden" name="range" value="<?php if ( ! empty( $_GET['range'] ) ) echo esc_attr( $_GET['range'] ) ?>" />
                <input type="hidden" name="start_date" value="<?php if ( ! empty( $_GET['start_date'] ) ) echo esc_attr( $_GET['start_date'] ) ?>" />
                <input type="hidden" name="end_date" value="<?php if ( ! empty( $_GET['end_date'] ) ) echo esc_attr( $_GET['end_date'] ) ?>" />
                <input type="hidden" name="page" value="<?php if ( ! empty( $_GET['page'] ) ) echo esc_attr( $_GET['page'] ) ?>" />
                <input type="hidden" name="tab" value="<?php if ( ! empty( $_GET['tab'] ) ) echo esc_attr( $_GET['tab'] ) ?>" />
                <?php
	            if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
					?>
	                <script type="text/javascript">
	                    jQuery(function() {
	                        jQuery('select.chosen_select').chosen();
	                    });
	                </script>
					<?php
				}
				?>
                </p>
            </form>
        <?php else : ?>
            <span><?php _e( 'No campaigns found', 'wc_coupon_campaigns' ); ?></span>
        <?php endif;
    }

    /**
     * Get the campaigns
     * @return mixed
     */
    public function get_campaigns( ) {

        // get the campaigns
        $campaign_args = array(
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false
        );
        $campaigns = get_terms( $this->taxonomy, $campaign_args );

        // create options for select box
        $campaign_options = '<option value="0">No campaign selected</option>';
        foreach( $campaigns as $campaign ) {
            $campaign_options .= '<option value="' . $campaign->term_id . '"' . selected( $campaign->term_id, $this->selected_campaign, false ) . '>' . $campaign->name . '</option>';
        }

        return $campaign_options;
    }

    /**
     * Output an export link
     */
    public function get_export_button() {
        $current_range = ! empty( $_GET['range'] ) ? $_GET['range'] : '7day';
        ?>
        <a
            href="#"
            download="report-<?php echo $current_range; ?>-<?php echo date_i18n( 'Y-m-d', current_time('timestamp') ); ?>.csv"
            class="export_csv"
            data-export="chart"
            data-xaxes="<?php _e( 'Date', 'wc_coupon_campaigns' ); ?>"
            data-groupby="<?php echo $this->chart_groupby; ?>"
        >
            <?php _e( 'Export CSV', 'wc_coupon_campaigns' ); ?>
        </a>
        <?php
    }

    /**
     * Round our totals correctly
     * @param  string $amount
     * @return string
     */
    private function round_chart_totals( $amount ) {
        if ( is_array( $amount ) ) {
            return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
        } else {
            return wc_format_decimal( $amount, wc_get_price_decimals() );
        }
    }

    /**
     * Get the main chart
     * @return string
     */
    public function get_main_chart() {
        global $wp_locale;

        if ( ! $this->selected_campaign ) {
            ?>
            <div class="chart-container">
                <p class="chart-prompt"><?php _e( '&larr; Choose a campaign to view stats', 'wc_coupon_campaigns' ); ?></p>
            </div>
            <?php
        } elseif ( ! isset( $this->order_counts ) ) {
            ?>
            <div class="chart-container">
                <p class="chart-prompt"><?php _e( 'This coupon hasn&apos;t been used', 'wc_coupon_campaigns' ); ?></p>
            </div>
            <?php
        } else {

            // Encode in json format
            $chart_data = json_encode( array(
                'order_discount_amounts' => array_map( array( $this, 'round_chart_totals' ), array_values( $this->order_discount_amounts ) ),
                'order_counts'           => array_values( $this->order_counts ),
                'order_revenue'          => array_map( array( $this, 'round_chart_totals' ), array_values( $this->order_revenue ) ),
            ) );
            ?>
            <div class="chart-container">
                <div class="chart-placeholder main"></div>
            </div>
            <script type="text/javascript">
                var main_chart;

                jQuery(function(){
                    var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );

                    var drawGraph = function( highlight ) {
                        var series = [
                            {
                                label: "<?php echo esc_js( __( 'Number of orders', 'wc_coupon_campaigns' ) ) ?>",
                                data: order_data.order_counts,
                                color: '<?php echo $this->chart_colours['campaign_orders' ]; ?>',
                                bars: { fillColor: '<?php echo $this->chart_colours['campaign_orders' ]; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
                                shadowSize: 0,
                                hoverable: false
                            },
                            {
                                label: "<?php echo esc_js( __( 'Revenue amount', 'wc_coupon_campaigns' ) ) ?>",
                                data: order_data.order_revenue,
                                yaxis: 2,
                                color: '<?php echo $this->chart_colours['campaign_revenue' ]; ?>',
                                points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
                                lines: { show: true, lineWidth: 4, fill: false },
                                shadowSize: 0
                            },
                            {
                                label: "<?php echo esc_js( __( 'Discount amount', 'wc_coupon_campaigns' ) ) ?>",
                                data: order_data.order_discount_amounts,
                                yaxis: 2,
                                color: '<?php echo $this->chart_colours['campaign_discount']; ?>',
                                points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
                                lines: { show: true, lineWidth: 4, fill: false },
                                shadowSize: 0,
                                prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>"
                            },
                        ];

                        if ( highlight !== 'undefined' && series[ highlight ] ) {
                            highlight_series = series[ highlight ];

                            highlight_series.color = '#9c5d90';

                            if ( highlight_series.bars )
                                highlight_series.bars.fillColor = '#9c5d90';

                            if ( highlight_series.lines ) {
                                highlight_series.lines.lineWidth = 5;
                            }
                        }

                        main_chart = jQuery.plot(
                            jQuery('.chart-placeholder.main'),
                            series,
                            {
                                legend: {
                                    show: false
                                },
                                grid: {
                                    color: '#aaa',
                                    borderColor: 'transparent',
                                    borderWidth: 0,
                                    hoverable: true
                                },
                                xaxes: [ {
                                    color: '#aaa',
                                    position: "bottom",
                                    tickColor: 'transparent',
                                    mode: "time",
                                    timeformat: "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
                                    monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
                                    tickLength: 1,
                                    minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
                                    font: {
                                        color: "#aaa"
                                    }
                                } ],
                                yaxes: [
                                    {
                                        min: 0,
                                        minTickSize: 1,
                                        tickDecimals: 0,
                                        color: '#ecf0f1',
                                        font: { color: "#aaa" }
                                    },
                                    {
                                        position: "right",
                                        min: 0,
                                        tickDecimals: 2,
                                        alignTicksWithAxis: 1,
                                        color: 'transparent',
                                        font: { color: "#aaa" }
                                    }
                                ],
                            }
                        );

                        jQuery('.chart-placeholder').resize();
                    }

                    drawGraph();

                    jQuery('.highlight_series').hover(
                        function() {
                            drawGraph( jQuery(this).data('series') );
                        },
                        function() {
                            drawGraph();
                        }
                    );
                });
            </script>
            <?php
        } // else
    }

    /**
     * Figure out exactly which orders have coupons attached to them
     * @return WP_Query
     */
    public function get_orders_with_coupons() {

        // get the WP query with all of our coupons
        $query = $this->get_coupon_query();

        $orders = array();
        // if we have any coupons that match the query
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                // get the list of orders the coupons have been applied to
                $coupon_orders = (array) get_post_meta( get_the_ID(), '_coupon_orders', true );
                $orders = array_merge( $orders, $coupon_orders );
            }
            // remove any empty array elements
            $orders = array_filter( $orders, 'strlen' );
        }

        return $orders;
    }


    /**
     * Query the DB and figure out the data that will go in the chart
     */
    public function prepare_all_chart_data() {

        // Get orders and dates in range - we want the SUM of campaign coupons used, SUM of order discount, SUM of total order revenue, and the date

        // get the list of orders that these coupons were used in
        $coupon_orders = $this->get_orders_with_coupons();

        // if the coupon hasn't been used in any order no need to do a query
        if ( $coupon_orders ) {
            $orders  = $this->get_order_report_data( array(
                'data' => array(
                    '_order_total' => array(
                        'type'     => 'meta',
                        'function' => 'SUM',
                        'name'     => 'total_sales'
                    ),
                    'ID' => array(
                        'type'            => 'post_data',
                        'function'        => 'COUNT',
                        'name'            => 'total_orders',
                        'distinct'        => true,
                    ),
                    'post_date' => array(
                        'type'            => 'post_data',
                        'function'        => '',
                        'name'            => 'post_date'
                    ),
                ),
                'where' => array(
                    array(
                        'type'            => 'order_id',
                        'key'             => 'posts.ID',
                        'value'           => $coupon_orders,
                        'operator'        => 'IN'
                    )
                ),
                'group_by'     => $this->group_by_query,
                'order_by'     => 'post_date ASC',
                'query_type'   => 'get_results',
                'filter_range' => true,
            ) );

            $order_discount_amounts = $this->get_order_report_data( array(
                'data' => array(
                    'discount_amount' => array(
                        'type'            => 'order_item_meta',
                        'order_item_type' => 'coupon',
                        'function'        => 'SUM',
                        'name'            => 'discount_amount'
                    ),
                    'post_date' => array(
                        'type'     => 'post_data',
                        'function' => '',
                        'name'     => 'post_date'
                    ),
                ),
                'where' => array(
                    array(
                        'type'     => 'order_item',
                        'key'      => 'order_item_name',
                        'value'    => $this->coupon_codes,
                        'operator' => 'IN'
                    ),
                    array(
                        'type'     => 'order_item',
                        'key'      => 'posts.ID',
                        'value'    => $coupon_orders,
                        'operator' => 'IN'
                    )
                ),
                'group_by'     => $this->group_by_query . ', order_item_name',
                'order_by'     => 'post_date ASC',
                'query_type'   => 'get_results',
                'filter_range' => true
            ) );

            // Prepare data for report
            $this->order_counts           = $this->prepare_chart_data( $orders, 'post_date', 'total_orders', $this->chart_interval, $this->start_date, $this->chart_groupby );
            $this->order_revenue          = $this->prepare_chart_data( $orders, 'post_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );
            $this->order_discount_amounts = $this->prepare_chart_data( $order_discount_amounts, 'post_date', 'discount_amount', $this->chart_interval, $this->start_date, $this->chart_groupby );
        } // if


    }
}
