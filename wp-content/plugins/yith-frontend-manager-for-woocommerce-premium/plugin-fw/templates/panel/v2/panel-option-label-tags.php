<?php
/**
 * The template for displaying the tags in option labels.
 *
 * @var array $field
 * @package    YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$option_tags = (array) ( $field['option_tags'] ?? array() );

foreach ( $option_tags as $key => $option_tag ) {
	if ( 'premium' === $option_tag ) {
		$option_tags[ $key ] = array(
			'label' => _x( 'PREMIUM', 'Panel option tag', 'yith-plugin-fw' ),
			'color' => 'premium',
		);
	}
}

?>

<?php if ( $option_tags ) : ?>
	<div class="yith-plugin-fw__panel__option__label__tags">
		<?php
		foreach ( $option_tags as $option_tag ) {
			$option_tag['type'] = 'tag';
			yith_plugin_fw_get_component( $option_tag, true );
		}
		?>
	</div>
<?php endif; ?>
