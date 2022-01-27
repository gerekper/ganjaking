<?php if (!defined('ABSPATH')) {
	die;
}
/**
 * Used vars list
 *
 * @var array $variables
 * @var string $fieldName
 */
?>

<?php foreach ($variables as $variable => $variableTitle) : ?>
  <?php if( ! in_array( $variableTitle, ['Link', 'Description', 'Excerpt'] ) ): ?>
    <button class="button-secondary" type="button" data-var="<?php echo esc_html($variable); ?>"
        data-field="#<?php echo esc_html($fieldName); ?>">
      <?php echo esc_html($variableTitle); ?>
    </button>
  <?php endif; ?>
<?php endforeach; ?>
