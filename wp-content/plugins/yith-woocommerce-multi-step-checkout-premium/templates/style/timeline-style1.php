<style>
    #checkout_timeline.style1 li .timeline-wrapper {
        background-color: <?php echo get_option( 'yith_wcms_timeline_style1_background_color' ) ?>;
        color: <?php echo get_option( 'yith_wcms_timeline_style1_step_color' ) ?>;
    }

    #checkout_timeline.style1 li .timeline-wrapper .timeline-label {
        color: <?php echo get_option( 'yith_wcms_timeline_style1_step_label_color' ) ?>;
    }

    #checkout_timeline.style1 li .timeline-wrapper .timeline-step {
        background-color: <?php echo get_option( 'yith_wcms_timeline_style1_step_background_color' ) ?>;
    }

    #checkout_timeline.style1 li.active .timeline-wrapper .timeline-label {
        color: <?php echo get_option( 'yith_wcms_timeline_style1_current_step_label_color' ) ?>;
    }

    #checkout_timeline.style1 li.active .timeline-wrapper {
        background-color: <?php echo get_option( 'yith_wcms_timeline_style1_active_background_color' ) ?>;
        color: <?php echo get_option( 'yith_wcms_timeline_style1_current_step_color' ) ?>;
    }

    #checkout_timeline.style1 li.active .timeline-wrapper .timeline-step {
        background-color: <?php echo get_option( 'yith_wcms_timeline_style1_current_step_background_color' ) ?>;
    }
</style>