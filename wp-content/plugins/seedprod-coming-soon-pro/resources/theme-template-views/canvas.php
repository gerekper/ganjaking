<?php
get_header();
// get the page template
echo seedprod_pro_get_theme_template_by_type_condition( 'page', false, false, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
get_footer();
