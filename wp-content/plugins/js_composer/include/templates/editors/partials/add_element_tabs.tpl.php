<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$other_tab = false;

$tabs = isset( $is_default_tab ) ? array() : array(
	array(
		'name' => esc_html__( 'All', 'js_composer' ),
		'active' => true,
		'filter' => '*',
	),
);

foreach ( $categories as $key => $name ) {
	if ( '_other_category_' === $name ) {
		$other_tab = array(
			'name' => esc_html__( 'Other', 'js_composer' ),
			'filter' => '.js-category-' . $key,
			'active' => false,
		);
		continue;
	}

	if ( 'deprecated' === $name ) {
		$name = esc_html__( 'Deprecated', 'js_composer' );
		$filter = '.js-category-deprecated';
	} elseif ( '_my_elements_' === $name ) {
		$name = esc_html__( 'My Elements', 'js_composer' );
		$filter = '.js-category-_my_elements_';
	} else {
		$filter = '.js-category-' . md5( $name );
	}

	$is_active = isset( $is_default_tab ) && 0 === $key ? true : false;

	$tabs[] = array(
		'name' => $name,
		'filter' => $filter,
		'active' => $is_active,
	);
}

if ( $other_tab ) {
	$tabs[] = $other_tab;
}

$tabs = apply_filters( 'vc_add_element_categories', $tabs );

?>
<ul class="vc_general vc_ui-tabs-line" data-vc-ui-element="panel-tabs-controls">
	<?php foreach ( $tabs as $key => $v ) : ?>
		<?php

		$classes = array( 'vc_edit-form-tab-control' );
		if ( $v['active'] ) {
			$classes[] = 'vc_active';
		}

		?>
		<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-vc-ui-element="panel-add-element-tab" <?php echo isset( $is_default_tab ) ? 'data-tab-index="' . esc_attr( $key ) .  '"' : '' ?>>
			<button class="vc_ui-tabs-line-trigger vc_add-element-filter-button"
					data-vc-ui-element="panel-tab-control"
					data-filter="<?php echo esc_attr( $v['filter'] ); ?>">
			<?php
			// @codingStandardsIgnoreLine
			print $v['name'];
			?>
			</button>
		</li>
	<?php endforeach ?>

	<li class="vc_ui-tabs-line-dropdown-toggle" data-vc-action="dropdown" data-vc-content=".vc_ui-tabs-line-dropdown" data-vc-ui-element="panel-tabs-line-toggle">
		<span class="vc_ui-tabs-line-trigger" data-vc-accordion="" data-vc-container=".vc_ui-tabs-line-dropdown-toggle" data-vc-target=".vc_ui-tabs-line-dropdown"></span>
		<ul class="vc_ui-tabs-line-dropdown" data-vc-ui-element="panel-tabs-line-dropdown"></ul>
	</li>
</ul>
