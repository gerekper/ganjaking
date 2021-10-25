<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title>
			<?php echo wp_get_document_title(); ?>
		</title>
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php do_action('masteraddons/template/before_header'); ?>
<div class="jltma-template-content-markup jltma-template-content-header jltma-template-content-theme-support">
<?php
	$template = \MasterHeaderFooter\JLTMA_HF_Activator::template_ids();
	echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($template[0]); 
?>
</div>
<?php do_action('masteraddons/template/after_header'); ?>