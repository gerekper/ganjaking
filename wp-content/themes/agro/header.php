<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>

	<!-- Meta UTF8 charset -->
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>

</head>

<!-- BODY START -->
<body <?php body_class(); ?>>

<?php

    if ( function_exists( 'wp_body_open' ) ) {
        wp_body_open();
    }
    // use this action to add any content after archive page container element
    agro_preloader();

    // include logo, menu and more contents
    do_action('agro_header_action');

    // use this action to add any content before  header container element
    do_action('agro_before_header');

?>

<!-- Site Wrapper -->
<div id="app" class="nt-theme-wrapper">
<main role="main">
