<?php

defined( 'ABSPATH' ) or exit;



if ( ! class_exists( 'YITH_COG_Admin_Report' ) ) {
    require_once( YITH_COG_PATH . 'includes/admin/reports/abstract-yith-cog-admin-report.php' );
}

/**
 * @class      YITH_COG_Report_Data_Category
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
class YITH_COG_Report_Stock_Data_Category extends YITH_COG_Admin_Report {


    /** @var array category IDs for the report */
    public $category_ids;

    /**
     * Main Instance
     *
     * @var YITH_COG_Report_Stock_Data_Category
     * @since 1.0
     */
    protected static $_instance = null;


    /**
     * Construct
     *
     * @since 1.0
     */
    public function __construct() {

        $this->set_category_ids();
    }


    /**
     * Set the category IDs for the report
     */
    protected function set_category_ids() {

        $this->category_ids = isset( $_GET['category_ids'] ) ? array_filter( array_map( 'absint', (array) $_GET['category_ids'] ) ) : array();

    }


    /**
     * Get all product IDs in a parent category and its children
     */
    public function get_product_ids_in_category( $category_id_array ) {

        $term_ids = array();
        foreach ( $category_id_array as $category_id ){
            $term_ids[] = get_term_children( $category_id, 'product_cat' );
        }
        return array_unique( get_objects_in_term( array_merge( $term_ids, (array) $category_id_array ), 'product_cat' ) );
    }


    /**
     * Render the report data, including legend and chart
     */
    public function output_report() {

        include( YITH_COG_TEMPLATE_PATH . '/html/html-report-stock.php');
    }

    /**
     * Render the export CSV button
     */
    public function output_export_button( $args = array() ) {

        ?>
        <a
                href="#"
                download="report-<?php echo 'YITH_COG_Stock_' . esc_attr( date('Y-m-d') ); ?>.csv"
                class="yith_export_csv export_csv"
                data-export="table"
        >
            <?php _e( 'Export CSV', 'yith-cost-of-goods-for-woocommerce' ); ?>
        </a>
        <?php
    }


    /**
     * Render the "Export to CSV" button
     */
    public function get_export_button() {
        $this->output_export_button();
    }



    /**
     * Output the category select
     */
    public function output_category_widget() {

        $categories = get_terms( 'product_cat', array( 'orderby' => 'name' ) );
        ?>
        <form method="GET">
            <div>
                <select
                        multiple="multiple"
                        data-placeholder="<?php _e( 'Select categories&hellip;', 'yith-cost-of-goods-for-woocommerce' ); ?>"
                        class="wc-enhanced-select" id="category_ids"
                        name="category_ids[]"
                        style="width: 205px;">
                    <?php
                    $r                 = array();
                    $r['pad_counts']   = 1;
                    $r['hierarchical'] = 1;
                    $r['hide_empty']   = 1;
                    $r['value']        = 'id';
                    $r['selected']     = $this->category_ids;

                    include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-dropdown-walker.php' );

                    echo wc_walk_category_dropdown_tree( $categories, 0, $r );
                    ?>
                </select>
                <a href="#" class="select_none"><?php esc_html_e( 'None', 'yith-cost-of-goods-for-woocommerce' ); ?></a>
                <a href="#" class="select_all"><?php esc_html_e( 'All', 'yith-cost-of-goods-for-woocommerce' ); ?></a>
                <input type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'yith-cost-of-goods-for-woocommerce' ); ?>" />
                <input type="hidden" name="page" value="<?php if ( ! empty( $_GET['page'] ) ) echo esc_attr( $_GET['page'] ) ?>" />
                <input type="hidden" name="tab" value="<?php if ( ! empty( $_GET['tab'] ) ) echo esc_attr( $_GET['tab'] ) ?>" />
                <input type="hidden" name="report" value="<?php if ( ! empty( $_GET['report'] ) ) echo esc_attr( $_GET['report'] ) ?>" />
            </div>
            <script type="text/javascript">
                jQuery( function() {
                    // select all
                    jQuery( '.chart-widget' ).on( 'click', '.select_all', function() {
                        jQuery(this).closest( 'div' ).find( 'select option' ).attr( "selected", "selected" );
                        jQuery(this).closest( 'div' ).find('select').change();
                        return false;
                    } );

                    // select none
                    jQuery( '.chart-widget').on( 'click', '.select_none', function() {
                        jQuery(this).closest( 'div' ).find( 'select option' ).removeAttr( "selected" );
                        jQuery(this).closest( 'div' ).find('select').change();
                        return false;
                    } );
                } );
            </script>
        </form>
        <?php
    }


    /**
     * Get the widgets for this report
     */
    public function get_chart_widgets() {

        return array(
            array(
                'title'    => esc_html__( 'Categories', 'yith-cost-of-goods-for-woocommerce' ),
                'callback' => array( $this, 'output_category_widget' ),
            ),
        );
    }


}
