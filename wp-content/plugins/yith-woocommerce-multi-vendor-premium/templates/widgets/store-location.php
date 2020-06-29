<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
extract( $instance );
?>

<div class="clearfix widget store-location">
    <h3 class="widget-title"><?php echo $title ?></h3>
    <div class="yith-wpv-store-location-wrapper">
        <div id="store-maps" class="gmap3" style="height: 300px;"></div>
        <?php if( $show_gmaps_link ) : ?>
            <a href="<?php echo $gmaps_link ?>" target="_blank"><?php _e( 'Show in Google Maps', 'yith-woocommerce-product-vendors' ) ?></a>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
(function ($) {
    $("#store-maps").gmap3({
        map   : {
            options: {
                zoom                     : <?php echo apply_filters( 'yith_wcmv_store_location_zoom', 15 ) ?>,
                disableDefaultUI         : <?php echo apply_filters( 'yith_wcmv_store_location_disableDefaultUI', 1 ) ?>,
                mapTypeControl           : <?php echo apply_filters( 'yith_wcmv_store_location_mapTypeControl', 0 ) ?>,
                panControl               : <?php echo apply_filters( 'yith_wcmv_store_location_panControl', 0 ) ?>,
                zoomControl              : <?php echo apply_filters( 'yith_wcmv_store_location_zoomControl', 0 ) ?>,
                scaleControl             : <?php echo apply_filters( 'yith_wcmv_store_location_scaleControl', 0 ) ?>,
                streetViewControl        : <?php echo apply_filters( 'yith_wcmv_store_location_streetViewControl', 0 ) ?>,
                rotateControl            : <?php echo apply_filters( 'yith_wcmv_store_location_rotateControl', 0 ) ?>,
                rotateControlOptions     : <?php echo apply_filters( 'yith_wcmv_store_location_rotateControlOptions', 0 ) ?>,
                overviewMapControl       : <?php echo apply_filters( 'yith_wcmv_store_location_overviewMapControl', 0 ) ?>,
                OverviewMapControlOptions: <?php echo apply_filters( 'yith_wcmv_store_location_OverviewMapControlOptions', 0 ) ?>,
            },
            address: "<?php echo $vendor->location ?>"
        },
        marker: {
            address: "<?php echo $vendor->location ?>"
        }
    });
})(jQuery)
</script>