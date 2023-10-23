<?php
/**
 * Preset filter - Admin view
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Templates\Admin
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset    bool|YITH_WCAN_Preset
 * @var $filter    YITH_WCAN_Filter
 * @var $filter_id int
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="filter_<?php echo esc_attr( $filter_id ); ?>" class="yith-toggle-row ui-sortable-handle" data-item_key="<?php echo esc_attr( $filter_id ); ?>">
	<div class="yith-toggle-title">
		<i class="title-arrow yith-icon yith-icon-arrow-right-alt"></i>
		<h3 class="title">
				<?php
				$filter_title = $filter->get_title();

				if ( $filter_title ) {
					echo esc_html( $filter_title );
				} else {
					echo wp_kses_post( sprintf( '<span class="no-title">%s</span>', _x( '&lt; no title &gt;', '[Admin] Message shown when filter has empty title', 'yith-woocommerce-ajax-navigation' ) ) );
				}
				?>
		</h3>
		<?php
		yith_plugin_fw_get_field(
			array(
				'id'    => "filters_{$filter_id}_enabled",
				'name'  => "filters[{$filter_id}][enabled]",
				'value' => $filter->is_enabled() ? 'yes' : 'no',
				'type'  => 'onoff',
			),
			true
		);
		?>
		<span class="show-on-hover delete yith-icon-trash"></span>
		<span class="show-on-hover clone yith-icon-clone"></span>
	</div>
	<div class="yith-toggle-content">
		<?php
		$fields = YITH_WCAN_Filter::get_fields();

		if ( ! empty( $fields ) ) :
			foreach ( $fields as $field_slug => $field ) :
				$field_id   = "filters_{$filter_id}_{$field_slug}";
				$field_name = "filters[{$filter_id}][{$field_slug}]";

				$field_args = array_merge(
					$field,
					array(
						'index'  => $filter_id,
						'id'     => $field_id,
						'name'   => $field_name,
						'filter' => $filter,
						'value'  => method_exists( $filter, "get_{$field_slug}" ) ? $filter->{"get_{$field_slug}"}() : '',
					)
				);

				// special case for terms.
				if ( 'term_ids' === $field_slug ) {
					$field_args['options'] = $filter->get_terms( 'id=>name', 'edit' );
				}

				?>
				<div class="yith-toggle-content-row">
					<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
					<?php yith_plugin_fw_get_field( $field_args, true ); ?>

					<?php if ( ! empty( $field['desc'] ) ) : ?>
						<span class="description"><?php echo wp_kses_post( $field['desc'] ); ?></span>
					<?php endif; ?>
				</div>
				<?php
			endforeach;
		endif;
		?>
		<div class="yith-toggle-content-buttons">
			<div class="spinner"></div>
			<button class="save button-primary" class="button-primary"><?php echo esc_html_x( 'Save Filter', '[Admin] Save filter button, in new/edit preset page', 'yith-woocommerce-ajax-navigation' ); ?></button>
			<button class="delete button-secondary" class="button-secondary yith-delete-button"><?php echo esc_html_x( 'Delete Filter', '[Admin] Delete filter button, in new/edit preset page', 'yith-woocommerce-ajax-navigation' ); ?></button>
		</div>
	</div>
</div>
