<?php
/*
 * Template for Plan List Items in frontend
 */

$manager = YITH_WCMBS_Manager();

$titles = array(
    'type'     => __( 'Type', 'yith-woocommerce-membership' ),
    'name'     => __( 'Name', 'yith-woocommerce-membership' ),
    'download' => apply_filters( 'yith_wcmb_download_table_title',__( 'Download', 'yith-woocommerce-membership' )),
);

if ( !empty( $posts ) ) {
?>
<table class='yith-wcmbs-my-account-plan-list-items yith-wcmbs-membership-table shop_table_responsive'>
    <?php if ( isset( $posts[ 0 ] ) && is_numeric( $posts[ 0 ] ) ) : ?>
        <tr>
            <th class="yith-wcmbs-membership-table-icon"><?php echo $titles[ 'type' ] ?></th>
            <th class="yith-wcmbs-membership-table-title"><?php echo $titles[ 'name' ] ?></th>
            <th class="yith-wcmbs-membership-table-download"><?php echo $titles[ 'download' ] ?></th>
        </tr>
    <?php endif ?>

    <?php

    $plan_id = !!$plan ? $plan->plan_id : $plan_id;

    $products_manager = YITH_WCMBS_Products_Manager();
    $first            = true;
    foreach ( $posts as $value ) {
        if ( is_numeric( $value ) ) {
            $post_title = get_the_title( $value );
            $post_type  = get_post_type( $value );
            $post_link  = $manager->get_post_link( $value, get_current_user_id() );

            $icon = "<span class='yith-wcmbs-post-type-icon yith-wcmbs-post-type-icon-{$post_type}'></span>";

            $title        = !empty( $post_link ) ? "<a href='{$post_link}'>{$post_title}</a>" : "{$post_title}";
            $availability = '';
            $download     = '';

            $delay_time = get_post_meta( $value, '_yith_wcmbs_plan_delay', true );

            $access = false;

            if( apply_filters( 'yith_wcmb_skip_not_downloadable_items',false, $post_type, $value, $plan ) ){
                continue;
            }

            if ( $post_type == 'product' ) {
                if ( $products_manager->is_allowed_download() ) {
                    $access = YITH_WCMBS_Products_Manager()->user_has_access_to_product( get_current_user_id(), $value );
                }
            } else {
                $access = $manager->user_has_access_to_post( get_current_user_id(), $value );
            }

            if ( !empty( $delay_time[ $plan_id ] ) && !$access ) {
                // The item has delay time for this plan
                $delay = $delay_time[ $plan_id ];

                if ( $plan->is_active() ) {
                    $availability_date = date( wc_date_format(), strtotime( '+ ' . $delay . ' days', $plan->start_date + ( $plan->paused_days * 60 * 60 * 24 ) ) );

                    $availability = '<span class="yith-wcmbs-plan-items-availability-info">' . sprintf( __( 'Availability date: %s ', 'yith-woocommerce-membership' ), $availability_date ) . '</span>';
                } else {
                    $availability = '<span class="yith-wcmbs-plan-items-availability-info">' . sprintf( _n( 'available after %s day since the beginning of the membership', 'available after %s days since the beginning of the membership', $delay, 'yith-woocommerce-membership' ), $delay ) . '</span>';
                }
            }
            if ( $post_type == 'product' ) {
                //$download = do_shortcode( '[membership_download_product_links link_class="yith-wcmbs-download-links" tooltip="yes" id="' . $value . '"]<span class="dashicons dashicons-download"></span>[/membership_download_product_links]' );
                $download = do_shortcode( '[membership_download_product_links link_class="yith-wcmbs-membership-content-button" tooltip="yes" id="' . $value . '"]' );
            }

            $download_class = !$download ? 'yith-wcmbs-responsive-hidden' : '';
            ?>

            <tr>
                <td class="yith-wcmbs-membership-table-icon" data-title="<?php echo $titles[ 'type' ] ?>"><?php echo $icon ?></td>
                <td class="yith-wcmbs-membership-table-title" data-title="<?php echo $titles[ 'name' ] ?>"><?php echo $title . $availability ?></td>
                <td class="yith-wcmbs-membership-table-download <?php echo $download_class ?>" data-title="<?php echo $titles[ 'download' ] ?>"><?php echo $download ?></td>
            </tr>


            <?php


        } elseif ( is_string( $value ) ) {
            $border_top_class = $first ? '' : 'yith-wcmbs-border-top';
            ?>
            <tr class="yith-wcmbs-membership-table-title <?php echo $border_top_class ?>">
                <th colspan='3'><?php echo $value ?></th>
            </tr>
            <tr>
                <th class="yith-wcmbs-membership-table-icon"><?php _e( 'Type', 'yith-woocommerce-membership' ) ?></th>
                <th class="yith-wcmbs-membership-table-title"><?php _e( 'Name', 'yith-woocommerce-membership' ) ?></th>
                <th class="yith-wcmbs-membership-table-download"><?php _e( 'Download', 'yith-woocommerce-membership' ) ?></th>
            </tr>
            <?php

        } else {
            continue;
        }
        $first = false;
    }

    echo "</table>";
    }
    ?>

