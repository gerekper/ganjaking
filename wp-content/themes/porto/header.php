<?php 
global $porto_block_template;
if ( empty( $porto_block_template ) ) :
?>
	<!DOCTYPE html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />

		<link rel="profile" href="https://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
<?php else :
	porto_action_head();
endif; 
?>
<?php get_template_part( 'header/header_before' ); ?>
