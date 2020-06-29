<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * WC_Report_Vendors_Registered
 *
 * @author      Andrea Grillo <andrea.grillo@yithemes.com>
 * @category    Admin
 * @version     2.1.0
 */

if ( ! defined( 'YITH_WPV_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Report_Vendors_Registered' ) ) {


    class YITH_Report_Vendors_Registered extends WC_Admin_Report {

        /**
         * Output the report
         */
        public function output_report() {

            $this->chart_colours = array(
                'totals'    => '#3498db',
                'enabled'   => '#5cc488',
                'disabled'  => '#e74c3c',
            );

            $current_range = YITH_Reports()->get_current_date_range();

            $this->calculate_current_range( $current_range );

            $args = array(
                'report'        => $this,
                'current_range' => $current_range,
                'ranges'        => YITH_Reports()->get_ranges()
            );

            $this->get_chart_data();

            yith_wcpv_get_template( 'vendors-registered', $args, 'woocommerce/admin/reports' );
        }

        /**
         * Get the legend for the main chart sidebar
         *
         * @return array
         */
        public function get_chart_legend() {
            $legend = array();

            $legend = array(
                'totals' => array(
                    'title'            => sprintf( __( '%s registered vendors', 'yith-woocommerce-product-vendors' ), '<strong>' . $this->chart_data['totals'] . '</strong>' ),
                    'widget_title'     => sprintf( __( '%s total ', 'yith-woocommerce-product-vendors' ), '<strong>' . $this->chart_data['totals'] . '</strong>' ),
                    'color'            => $this->chart_colours['totals'],
                    'highlight_series' => 0
                ),

                'enabled' => array(
                    'title'            => sprintf( __( '%s with selling capability', 'yith-woocommerce-product-vendors' ), '<strong>' . $this->chart_data['enabled'] . '</strong>' ),
                    'widget_title'     => sprintf( __( '%s enabled', 'yith-woocommerce-product-vendors' ), '<strong>' . $this->chart_data['enabled'] . '</strong>' ),
                    'color'            => $this->chart_colours['enabled'],
                    'highlight_series' => 1
                ),

                'disabled' => array(
                    'title'            => sprintf( __( '%s without selling capability', 'yith-woocommerce-product-vendors' ), '<strong>' . $this->chart_data['disabled'] . '</strong>' ),
                    'widget_title'     => sprintf( __( '%s disabled', 'yith-woocommerce-product-vendors' ), '<strong>' . $this->chart_data['disabled'] . '</strong>' ),
                    'color'            => $this->chart_colours['disabled'],
                    'highlight_series' => 2
                ),
            );

            return $legend;
        }

        /**
         * The chart data
         */
        public function get_chart_data() {
            global $wpdb;

            $vendors = array(
                'totals'  => count( YITH_Vendors()->get_vendors( array( 'fields' => 'ids' ) ) ),
                'enabled' => count( YITH_Vendors()->get_vendors( array( 'fields' => 'ids', 'enabled_selling' => 'yes' ) ) ),
            );

            $vendors['disabled'] = absint( $vendors['totals'] - $vendors['enabled'] );

            $this->chart_data   = $vendors;
            $termmeta_table     = YITH_Vendors()->termmeta_table;
            $termmeta_term_id   = YITH_Vendors()->termmeta_term_id;
            
            $sql = "SELECT meta_value as post_date, count(wtm.term_id) as vendors_number
                    FROM {$termmeta_table} as wtm
                    JOIN {$wpdb->term_taxonomy} as tt
                    ON wtm.{$termmeta_term_id} = tt.term_id
                    WHERE tt.taxonomy = %s
                    AND wtm.meta_key = %s
                    GROUP BY post_date";

            $results                    = $wpdb->get_results( $wpdb->prepare( $sql, YITH_Vendors()->get_taxonomy_name(), 'registration_date' ) );
            $prepared_chart_data        = $this->prepare_chart_data( $results, 'post_date', 'vendors_number', $this->chart_interval, $this->start_date, $this->chart_groupby );
            $this->chart_data['series'] = json_encode( array_values( $prepared_chart_data ) );
        }

        /**
         * Get the main char information
         *
         * @return string|void
         */
        public function get_main_chart() {
            global $wp_locale;
            ?>
            <div class="chart-container">
                <div class="chart-placeholder main"></div>
            </div>
            <script type="text/javascript">

                var main_chart;

                jQuery(function(){
                    var plot_data = <?php echo $this->chart_data['series']; ?>;
                    var drawGraph = function( highlight ) {
                        var series = [
                            {
                                label: "<?php echo esc_js( __( 'Totals of registered vendors', 'yith-woocommerce-product-vendors' ) ) ?>",
                                data: plot_data,
                                yaxis: 1,
                                color: '<?php echo $this->chart_colours['totals']; ?>',
                                points: { show: true, radius: 5, lineWidth: 3, fillColor: '#fff', fill: true },
                                lines: { show: true, lineWidth: 4, fill: false },
                                enable_tooltip: true,
								append_tooltip: "<?php echo ' ' . __( 'new vendors', 'yith-woocommerce-product-vendors' ); ?>",
                                shadowSize: 0,
                                hoverable: true
                            }
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
                                    show: true
                                },
                                grid: {
                                    color: '#aaa',
                                    borderColor: 'transparent',
                                    borderWidth: 0,
                                    hoverable: true
                                },
                                xaxes: [ {
                                    mode: "time",
                                    color: '#aaa',
                                    position: "bottom",
                                    tickColor: 'transparent',
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
                                        color: '#d4d9dc',
                                        font: { color: "#aaa" }
                                    }
                                ]
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
        }

        /**
         * [get_chart_widgets description]
         *
         * @return array
         */
        public function get_chart_widgets() {
            $widgets = array();

            $widgets[] = array(
                'title'    => '',
                'callback' => array( $this, 'enabled_vs_disabled' )
            );

            return $widgets;
        }

        /**
         * Enabled Vs Disabled vendors
         */
        public function enabled_vs_disabled() {

            $legend = $this->get_chart_legend();

            ?>
            <div class="chart-container">
                <h3 style="text-align: center"><?php _e( 'Enabled Vs Disabled Vendors', 'yith-woocommerce-product-vendors' ) ?></h3>
                <div class="chart-placeholder enabled_vs_disabled pie-chart" style="height:200px; cursor: pointer;"></div>
                <ul class="pie-chart-legend">
                    <li style="border-color: <?php echo $this->chart_colours['enabled']; ?>">
                        <?php echo $legend['enabled']['widget_title'] ?>
                    </li>
                    <li style="border-color: <?php echo $this->chart_colours['disabled']; ?>">
                        <?php echo $legend['disabled']['widget_title'] ?>
                    </li>
                </ul>
            </div>
            <script type="text/javascript">
                jQuery(function(){
                    jQuery.plot(
                        jQuery('.chart-placeholder.enabled_vs_disabled'),
                        [
                            {
                                data:  "<?php echo $this->chart_data['enabled'] ?>",
                                color: '<?php echo $this->chart_colours['enabled']; ?>'
                            },
                            {
                                data:  "<?php echo $this->chart_data['disabled'] ?>",
                                color: '<?php echo $this->chart_colours['disabled']; ?>'
                            }
                        ],
                        {
                            grid: {
                                hoverable: true
                            },
                            series: {
                                pie: {
                                    show: true,
                                    radius: 1,
                                    innerRadius: 0.6,
                                    label: {
                                        show: false
                                    }
                                },
                                enable_tooltip: true,
                                append_tooltip: "<?php echo ' ' . __( 'vendors', 'yith-woocommerce-product-vendors' ); ?>"
                            },
                            legend: {
                                show: false
                            }
                        }
                    );

                    jQuery('.chart-placeholder.customers_vs_guests').resize();
                });
            </script>
            <?php
        }
    }
}
