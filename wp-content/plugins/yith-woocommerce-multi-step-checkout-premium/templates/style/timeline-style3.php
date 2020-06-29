<style>
    #checkout_timeline.style3 li .timeline-wrapper {
        background-color: <?php echo get_option( 'yith_wcms_timeline_style3_background_color' ); ?>;
        border-color: <?php echo get_option( 'yith_wcms_timeline_style3_border_color' ); ?>;
        color: <?php echo get_option( 'yith_wcms_timeline_style3_step_color' ); ?>
    }

    #checkout_timeline.style3 li.active .timeline-wrapper{
        background-color: <?php echo get_option( 'yith_wcms_timeline_style3_active_background_color' ); ?>;
        border-color: <?php echo get_option( 'yith_wcms_timeline_style3_active_background_color' ); ?>;
        color: <?php echo get_option( 'yith_wcms_timeline_style3_current_step_color' ) ?>;
    }
</style>