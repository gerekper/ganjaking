<?php
/**
 * YITH WCWTL Importer Steps
 *
 * @since   1.6.0
 * @package YITH WooCommerce Waiting List
 */

defined( 'YITH_WCWTL' ) || exit;

?>
<ol class="yith-wcwtl-importer-steps">
	<?php foreach ( $this->steps as $step_key => $step ) : ?>
		<?php
		$step_class = '';
		if ( $step_key === $this->current_step || array_search( $this->current_step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true ) ) {
			$step_class = 'active';
		}
		?>
		<li class="<?php echo esc_attr( $step_class ); ?>">
			<?php echo esc_html( $step['name'] ); ?>
		</li>
	<?php endforeach; ?>
</ol>
