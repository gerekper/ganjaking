<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title>
			<?php echo wp_get_document_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</title>
	<?php endif; ?>

	<?php wp_head(); ?>
	<meta name="viewport" content="<?php echo apply_filters( 'seedprod_meta_content_viewport','width=device-width, initial-scale=1.0'); ?>">

</head>
<body <?php body_class( 'sp-antialiased' ); ?>>

<?php echo seedprod_pro_get_theme_template_by_type_condition( 'header', false, false, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
