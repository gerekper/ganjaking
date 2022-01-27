<?php

    /**  The template for displaying the footer 	**/

    // theme contact form 7 shortcode area
    agro_footer_form();

    // theme custom google map area
    agro_footer_map();

    echo "</main>";
    if ( '1' == agro_settings( 'use_custom_footer_template' ) && class_exists( 'Agro_Saved_Templates' ) ) {

        Agro_Saved_Templates::vc_print_saved_template( agro_settings('custom_footer_template' ) );

    } else {

        echo do_action('agro_footer_action');

    }

    // theme back to top button
    agro_backtop();
    // Site Wrapper End
    echo "</div>";

    do_action('agro_before_footer');

    wp_footer();

?>

</body>
</html>
