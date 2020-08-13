<?php

/**
 * The template for displaying cookie group popup on front
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr folder
 *
 * @version 1.0
 *
 */

if(!defined('ABSPATH')) {
	exit;
}

/** @var array $options */

$header_style = isset($options['cookie_modal_header_color']) ? 'style="color: ' . esc_attr($options['cookie_modal_header_color']) . '"' : '';
$text_style = isset($options['cookie_modal_text_color']) ? 'style="color: ' . esc_attr($options['cookie_modal_text_color']) . '"' : '';

?>

<h2 <?php echo $header_style; ?>><?php esc_html_e('Privacy settings', 'ct-ultimate-gdpr'); ?></h2>
<div class="ct-ultimate-gdpr-cookie-modal-desc">
    <p <?php echo $text_style; ?>><?php esc_html_e('Decide which cookies you want to allow.', 'ct-ultimate-gdpr'); ?></p>
    <p <?php echo $text_style; ?>><?php esc_html_e('You can change these settings at any time. However, this can result in some functions no longer being available. For information on deleting the cookies, please consult your browser’s help function.', 'ct-ultimate-gdpr'); ?></p>
    <span <?php echo $text_style; ?>><?php esc_html_e('Learn more about the cookies we use.', 'ct-ultimate-gdpr'); ?></span>
</div>
<h3 <?php echo $header_style; ?>><?php esc_html_e('With the slider, you can enable or disable different types of cookies:', 'ct-ultimate-gdpr'); ?></h3>
