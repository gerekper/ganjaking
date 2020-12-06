<?php if (!defined('ABSPATH')) {
	die;
}
/**
 * Used vars list
 *
 * @var array $variables
 */
?>

<?php foreach ($variables as $variable => $variableTitle) : ?>
	<button class="button-secondary" type="button" data-var="<?php echo esc_html($variable); ?>"
			data-field="#woocommerce_pinterest_pin_description">
		<?php echo esc_html($variableTitle); ?>
	</button>
<?php endforeach; ?>
