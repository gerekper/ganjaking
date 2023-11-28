<?php

/**
 * Template Name: Horizontal
 *
 */

use Essential_Addons_Elementor\Pro\Classes\Helper;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

$horizontal_layout  = ! empty( $settings['content_timeline_layout_horizontal'] ) ? esc_html( $settings['content_timeline_layout_horizontal'] ) : esc_html( 'center' );
$navigation_type    = ! empty( $settings['eael_content_timeline_navigation_type'] ) ? $settings['eael_content_timeline_navigation_type'] : esc_html( 'scrollbar' );
$query = isset( $query ) ? $query : new WP_Query();
?>
<div class="eael-horizontal-timeline eael-horizontal-timeline--layout-<?php echo esc_attr( $horizontal_layout ) ?> eael-horizontal-timeline--align-left eael-horizontal-timeline--<?php echo esc_attr( $navigation_type ) ?>">
    <?php if( 'arrows' === $navigation_type ) : ?>
    <div class="eael-horizontal-timeline-inner">
    <?php endif; ?>
    
    <div class="eael-horizontal-timeline-track">
        <?php 
        switch ( $horizontal_layout ) {
            case 'top':
                ?>

                <div class="eael-horizontal-timeline-list eael-horizontal-timeline-list--top <?php echo esc_attr( $horizontal_layout ); ?>">
                    <?php $this->print_horizontal_timeline_content( $settings, $query, 'top' ); ?>
                </div>
                <div class="eael-horizontal-timeline-list eael-horizontal-timeline-list--middle <?php echo esc_attr( $horizontal_layout ); ?>">
                    <div class="eael-horizontal-timeline__line"></div>
                    <?php $this->print_horizontal_timeline_content( $settings, $query, 'middle' ); ?>
                </div>
                <div class="eael-horizontal-timeline-list eael-horizontal-timeline-list--bottom <?php echo esc_attr( $horizontal_layout ); ?>">
                    <?php $this->print_horizontal_timeline_content( $settings, $query, 'bottom' ); ?>
                </div>
                
                <?php
                break;
            case 'middle':
                ?>

                <div class="eael-horizontal-timeline-list eael-horizontal-timeline-list--top <?php echo esc_attr( $horizontal_layout ); ?>">
                    <?php $this->print_horizontal_timeline_content( $settings, $query, 'top' ); ?>
                </div>
                <div class="eael-horizontal-timeline-list eael-horizontal-timeline-list--middle <?php echo esc_attr( $horizontal_layout ); ?>">
                    <div class="eael-horizontal-timeline__line"></div>
                    <?php $this->print_horizontal_timeline_content( $settings, $query, 'middle' ); ?>
                </div>
                <div class="eael-horizontal-timeline-list eael-horizontal-timeline-list--bottom <?php echo esc_attr( $horizontal_layout ); ?>">
                    <?php $this->print_horizontal_timeline_content( $settings, $query, 'bottom' ); ?>
                </div>

                <?php
                break;
            case 'bottom':
                ?>

                <div class="eael-horizontal-timeline-list eael-horizontal-timeline-list--bottom <?php echo esc_attr( $horizontal_layout ); ?>">
                    <?php $this->print_horizontal_timeline_content( $settings, $query, 'bottom' ); ?>
                </div>
                <div class="eael-horizontal-timeline-list eael-horizontal-timeline-list--middle <?php echo esc_attr( $horizontal_layout ); ?>">
                    <div class="eael-horizontal-timeline__line"></div>
                    <?php $this->print_horizontal_timeline_content( $settings, $query, 'middle' ); ?>
                </div>
                <div class="eael-horizontal-timeline-list eael-horizontal-timeline-list--top <?php echo esc_attr( $horizontal_layout ); ?>">
                    <?php $this->print_horizontal_timeline_content( $settings, $query, 'top' ); ?>
                </div>

                <?php
                break;
        }
        ?>
    </div>

    <?php 
    if ( 'arrows' === $navigation_type && ! empty( $settings['eael_content_timeline_arrow_type'] ) ) { 
        printf( '<i class="%s eael-arrow eael-prev-arrow eael-arrow-disabled"></i>', esc_attr( $settings['eael_content_timeline_arrow_type'] ) );
        printf( '<i class="%s eael-arrow eael-next-arrow"></i>', esc_attr( $settings['eael_content_timeline_arrow_type'] ) );
    }
    ?>

    <?php if( 'arrows' === $navigation_type ) : ?>
    </div> <!-- /.eael-horizontal-timeline-inner -->
    <?php endif; ?>
</div>
<?php
