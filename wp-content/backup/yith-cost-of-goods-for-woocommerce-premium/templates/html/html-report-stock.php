<?php


require_once ( YITH_COG_PATH . '/includes/admin/reports/stock-reports/class.yith-cog-report-stock-table.php' );



if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//Template of the 'Stock by ... ' links
wc_get_template( 'html/html-admin-report-stock-links.php', array(), '' , YITH_COG_TEMPLATE_PATH );

?>
<div id="poststuff" class="woocommerce-reports-wide">
    <div style="float: right"><?php $this->get_export_button(); ?></div>
    <br>
    <div class="postbox">
        <?php if ( empty( $hide_sidebar ) ) : ?>
        <div class="inside chart-with-sidebar">
            <div class="chart-sidebar" ">
            <?php if ( $legends = $this->get_chart_legend() ) : ?>
                <ul class="chart-legend">
                    <?php foreach ( $legends as $legend ) : ?>
                        <?php // @codingStandardsIgnoreLine ?>
                        <li style="border-color: <?php echo $legend['color']; ?>" <?php if ( isset( $legend['highlight_series'] ) ) echo 'class="highlight_series ' . ( isset( $legend['placeholder'] ) ? 'tips' : '' ) . '" data-series="' . esc_attr( $legend['highlight_series'] ) . '"'; ?> data-tip="<?php echo isset( $legend['placeholder'] ) ? $legend['placeholder'] : ''; ?>">
                            <?php echo $legend['title']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <ul class="chart-widgets">
                <?php foreach ( $this->get_chart_widgets() as $widget ) : ?>
                    <li class="chart-widget">
                        <?php if ( $widget['title'] ) : ?><h4><?php echo $widget['title']; ?></h4><?php endif; ?>
                        <?php call_user_func( $widget['callback'] ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="main"></div>
    </div>
    <?php endif; ?>
    </div>
</div>

