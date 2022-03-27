<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $seedprod_theme_requirements;
$page_id = get_option( 'seedprod_global_css_page_id' );

global $wpdb;
$tablename        = $wpdb->prefix . 'posts';
$sql              = "SELECT * FROM $tablename WHERE id= %d";
$safe_sql         = $wpdb->prepare( $sql, $page_id ); // phpcs:ignore 
$spage            = $wpdb->get_row( $safe_sql ); // phpcs:ignore
$settings         = json_decode( $spage->post_content_filtered );
$google_fonts_str = seedprod_pro_construct_font_str( $settings );

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

	<?php if ( ! empty( $google_fonts_str ) ) : ?>
	<!-- Google Font -->
	<link rel="stylesheet" href="<?php echo esc_url( $google_fonts_str ); ?>"> <?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>
	<?php endif; ?>

	<?php wp_head(); ?>
	<meta name="viewport" content="<?php echo apply_filters( 'seedprod_meta_content_viewport','width=device-width, initial-scale=1.0'); ?>">

</head>
<body <?php body_class( 'sp-antialiased' ); ?>>

<?php echo seedprod_pro_get_theme_template_by_type_condition( 'header', false, false, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
