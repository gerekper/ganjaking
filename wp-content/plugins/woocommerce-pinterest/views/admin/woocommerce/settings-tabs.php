<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} ?>

<?php
/**
 * Used vars list
 *
 * @var array $tabs
 * @var string $baseUrl
 * @var string $selectedTab
 */

$i = count( $tabs );

?>

<ul class="subsubsub">
	<?php 
	foreach ( $tabs as $name => $slug ) :
		$tabUrl = add_query_arg( 'pinterest-tab', $slug, $baseUrl ); 
		?>

		<?php $i --; ?>

		<li>
			<a href="<?php echo esc_url_raw( $tabUrl ); ?>"
			   class="<?php echo $slug === $selectedTab ? 'current' : ''; ?>"><?php echo wp_kses( $name, array() ); ?></a>
			<?php echo $i > 0 ? '|' : ''; ?>
		</li>

	<?php endforeach; ?>
</ul>



