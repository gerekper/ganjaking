<?php

/**
 * The template for displaying [ultimate_gdpr_protection] shortcode view
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/shortcode folder
 *
 * @version 1.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<span class="ct-ultimate-gdpr-shortcode-protection-label"><?php echo esc_html( $options['label'] ); ?></span>
<div class="ct-ultimate-gdpr-shortcode-protection blur" data-level="<?php echo esc_attr( $options['level'] ); ?>">
    <?php echo base64_encode( do_shortcode( $options['content'] ) ); ?>
</div>