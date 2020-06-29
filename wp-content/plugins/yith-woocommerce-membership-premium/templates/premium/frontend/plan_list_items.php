<?php
/*
 * Template for Plan List Items in frontend
 */

$manager = YITH_WCMBS_Manager();
?>
<div class="yith-wcmbs-plan-list-shortcode-container yith-wcmbs-plan-list-container-<?php echo $plan_id; ?>">
    <?php
    if ( !empty( $posts ) ) {
        echo "<ul class='yith-wcmbs-plan-list-items'>";

        $content_start     = '<ul class="child">';
        $content_end       = '</ul></li>';
        $print_content_end = false;

        $loop = 0;
        foreach ( $posts as $value ) {
            if ( is_numeric( $value ) ) {
                $post_title = get_the_title( $value );
                $post_type  = get_post_type( $value );
                $post_link  = $manager->get_post_link( $value, get_current_user_id() );

                $post_type_icon = "<span class='yith-wcmbs-post-type-icon yith-wcmbs-post-type-icon-{$post_type}'></span>";

                if ( !$show_icons ) {
                    $post_type_icon = '';
                }

                $text_html = !empty( $post_link ) ? "<a href='{$post_link}'>{$post_type_icon} {$post_title}</a>" : "{$post_type_icon} {$post_title}";

                $delay_time = get_post_meta( $value, '_yith_wcmbs_plan_delay', true );

                if ( !empty( $delay_time[ $plan_id ] ) ) {
                    // The item has delay time for this plan
                    $delay = $delay_time[ $plan_id ];

                    if ( $active_plan && $active_plan instanceof YITH_WCMBS_Membership ) {
                        $availability_date = date( wc_date_format(), strtotime( '+ ' . $delay . ' days', $active_plan->start_date + ( $active_plan->paused_days * 60 * 60 * 24 ) ) );

                        $text_html .= '<span class="yith-wcmbs-plan-items-availability-info">' . sprintf( __( 'Availability date: %s ', 'yith-woocommerce-membership' ), $availability_date ) . '</span>';
                    } else {
                        $text_html .= '<span class="yith-wcmbs-plan-items-availability-info">' . sprintf( _n( 'available after %s day since the beginning of the membership', 'available after %s days since the beginning of the membership', $delay, 'yith-woocommerce-membership' ), $delay ) . '</span>';
                    }
                }

                if ( $post_type == 'product' ) {
                    //$text_html .= do_shortcode( '[membership_download_product_links link_class="yith-wcmbs-download-links" tooltip="yes" id="' . $value . '"]<span class="dashicons dashicons-download"></span>[/membership_download_product_links]' );
                    $text_html .= do_shortcode( '[membership_download_product_links link_class="yith-wcmbs-download-links" tooltip="yes" id="' . $value . '"]' );
                    //$text_html .= do_shortcode( '[membership_download_product_links link_class="yith-wcmbs-membership-content-button" tooltip="yes" id="' . $value . '"]' );
                }

                echo "<li class='yith-wcmbs-plan-item' rel='{$loop}'>{$text_html}</li>";

            } elseif ( is_string( $value ) ) {
                if ( $print_content_end ) {
                    echo $content_end;
                    $print_content_end = false;
                }

                echo "<li class='yith-wcmbs-plan-item-text' rel='{$loop}'><p>{$value}</p>";

                echo $content_start;
                $print_content_end = true;

            } else {
                continue;
            }
            $loop++;
        }

        if ( $print_content_end ) {
            echo $content_end;
        }
        echo '</ul>';
    }
    ?>
</div>
