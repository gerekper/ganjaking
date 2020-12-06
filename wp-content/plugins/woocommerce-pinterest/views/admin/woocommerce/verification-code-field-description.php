<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php
/**
 * Used vars list
 *
 * @var string $documentationLink
 */
?>

<p>
	<?php
	/* translators: '%s' is replaced with <a> tag*/
	echo sprintf(__('Verify your domain in your Pinterest Business account. Follow %s to find your verification code.',
		'woocommerce-pinterest'), '<a
        href="' . esc_url($documentationLink) . '"
        target="_blank">' .
		esc_html__('these instructions', 'woocommerce-pinterest') . '</a>');
	?>

</p>
