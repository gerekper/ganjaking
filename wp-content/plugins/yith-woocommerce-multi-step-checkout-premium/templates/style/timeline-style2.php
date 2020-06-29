<style>
    #checkout_timeline.style2 li .timeline-wrapper .timeline-step {
        border-color: <?php echo get_option( 'yith_wcms_timeline_style2_bubble_background_color' ); ?>;
        background-color: <?php echo get_option( 'yith_wcms_timeline_style2_bubble_background_color' ); ?>;
        color: <?php echo get_option( 'yith_wcms_timeline_style2_bubble_color' ); ?>;
    }

    #checkout_timeline.horizontal.style2 li:first-child,
    #checkout_timeline.vertical.style2 li:last-child,
    #checkout_timeline.style2 li {
        border-color: <?php echo get_option('yith_wcms_timeline_style2_border_color')?>;
    }

    #checkout_timeline.style2 li .timeline-wrapper {
        color: <?php echo get_option( 'yith_wcms_timeline_style2_step_color' ); ?>;
    }

    #checkout_timeline.style2 li {
        background-color: <?php echo get_option( 'yith_wcms_timeline_style2_background_color' ); ?>;
    }

     #checkout_timeline.style2 li.active .timeline-wrapper .timeline-step {
        border-color: <?php echo get_option( 'yith_wcms_timeline_style2_current_bubble_background_color' ); ?>;
        background-color: <?php echo get_option( 'yith_wcms_timeline_style2_current_bubble_background_color' ); ?>;
        color: <?php echo get_option( 'yith_wcms_timeline_style2_current_bubble_color' ); ?>;
    }

    #checkout_timeline.style2 li.active {
        background-color: <?php echo get_option( 'yith_wcms_timeline_style2_active_background_color' ); ?>;
    }

     #checkout_timeline.style2 li.active .timeline-wrapper {
        color: <?php echo get_option( 'yith_wcms_timeline_style2_current_step_color' ); ?>;
    }
</style>