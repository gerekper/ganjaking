<?php
/**
 * The Template for displaying the custom tab.
 *
 * @var array  $options         Array of options.
 * @var string $current_tab     The current tab.
 * @var string $current_sub_tab The current sub-tab.
 * @package YITH\PluginFramework\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$defaults       = array(
	'action'         => '',
	'show_container' => true,
	'show_header'    => true,
	'title'          => '',
	'description'    => '',
);
$is_sub_tab     = ! ! $current_sub_tab;
$options        = wp_parse_args( $options, $defaults );
$the_action     = $options['action'];
$show_container = $options['show_container'];
$the_title      = $options['title'];
$description    = $options['description'];
$show_header    = $options['show_header'] && ! ! $the_title;
$tab_id         = sanitize_key( implode( '-', array_filter( array( 'yith-plugin-fw-panel-custom-tab', $current_tab, $current_sub_tab ) ) ) );
?>
<div id='<?php echo esc_attr( $tab_id ); ?>' class='yith-plugin-fw__panel__content__page'>
	<?php if ( $show_header ) : ?>
		<div class="yith-plugin-fw__panel__content__page__heading">
			<h1 class="yith-plugin-fw__panel__content__page__title yith-plugin-fw-panel-custom-tab-title"><?php echo wp_kses_post( $the_title ); ?></h1>

			<?php if ( $description ) : ?>
				<div class="yith-plugin-fw__panel__content__page__description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php
	if ( $show_container ) {
		echo '<div id="' . esc_attr( $tab_id ) . '" class="yith-plugin-fw-panel-custom-tab-container">';
		if ( $is_sub_tab ) {
			echo '<div class="yith-plugin-fw-panel-custom-sub-tab-container">';
		}
	}

	do_action( $the_action );

	if ( $show_container ) {
		if ( $is_sub_tab ) {
			echo '</div><!-- /.yith-plugin-fw-panel-custom-sub-tab-container -->';
		}
		echo '</div><!-- /.yith-plugin-fw-panel-custom-tab-container -->';
	}
	?>
</div>
