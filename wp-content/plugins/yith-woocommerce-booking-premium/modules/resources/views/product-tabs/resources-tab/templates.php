<?php
/**
 * Resources tab templates
 *
 * @package YITH\Booking\Modules\Resources\Views
 */

defined( 'YITH_WCBK' ) || exit;
?>

<script type="text/html" id="tmpl-yith-wcbk-resources-modal-content">
	<div id="yith-wcbk-resources-modal-content">
		<div id="yith-wcbk-resources-modal-search__wrapper">
			<i class="yith-icon yith-icon-magnifier"></i>
			<input type="text" id="yith-wcbk-resources-modal-search" placeholder="<?php esc_attr_e( 'Search', 'yith-booking-for-woocommerce' ); ?>"/>
		</div>
		<div id="yith-wcbk-resources-modal-table-wrapper"></div>
	</div>
</script>

<script type="text/html" id="tmpl-yith-wcbk-resources-modal-content-blank">
	<?php
	$breadcrumb         = sprintf(
		'YITH > Booking > %s > %s',
		_x( 'Configuration', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
		_x( 'Resources', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' )
	);
	$resources_list_url = add_query_arg( array( 'post_type' => YITH_WCBK_Post_Types::RESOURCE ), admin_url( 'edit.php' ) );

	$message = implode(
		'<br />',
		array(
			__( 'You have no resources yet!', 'yith-booking-for-woocommerce' ),
			'<small>' .
			sprintf(
			// translators: %s is the settings path (YITH > Booking > Configuration > Resources).
				esc_html__( 'You can create resources in %s', 'yith-booking-for-woocommerce' ),
				'<a href="' . esc_url( $resources_list_url ) . '">' . esc_html( $breadcrumb ) . '</a>'
			) .
			'</small>',
		)
	);

	yith_plugin_fw_get_component(
		array(
			'class'    => 'yith-wcbk-resources-modal-table__no-resources',
			'type'     => 'list-table-blank-state',
			'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
			'message'  => $message,
		),
		true
	);
	?>
</script>

<script type="text/html" id="tmpl-yith-wcbk-resources-modal-table">

	<# if ( data.items.length ) { #>
	<div class="yith-wcbk-resources-modal-table__container">
		<table class="yith-wcbk-resources-modal-table yith-plugin-fw__boxed-table widefat">
			<tbody>

			<# _(data.items).each( function( item ) { #>

			<tr class="resource {{{data.product_resource_ids.indexOf(item.id) > -1 ? 'added' : ''}}}" data-id="{{item.id}}" data-name="{{item.name}}" data-image="{{item.image}}">
				<td class="resource__id">#{{{item.id}}}</td>
				<td class="resource__image">{{{item.image}}}</td>
				<td class="resource__name">{{{item.name}}}</td>
				<td class="resource__actions">
					<span class="yith-plugin-fw__button--primary add"><?php esc_html_e( 'Add', 'yith-booking-for-woocommerce' ); ?></span>
					<div class="added-wrap">
						<div class="added">
							<i class="yith-icon yith-icon-check-alt"></i>
							<?php esc_html_e( 'Added', 'yith-booking-for-woocommerce' ); ?>
						</div>
						<span class="remove"><?php esc_html_e( 'Remove', 'yith-booking-for-woocommerce' ); ?></span>
					</div>
				</td>
			</tr>

			<# } ); #>
			</tbody>
		</table>
	</div>
	<div class="yith-wcbk-resources-modal-table__pagination" data-current-page="{{{data.page}}}" data-total-pages="{{{data.total_pages}}}">
		<span class="pagination-action prev {{{data.page < 2 ? 'disabled' : ''}}}">
			<i class="yith-icon yith-icon-arrow-left-alt"></i>
		</span>
		<span class="current">
			<?php
			echo esc_html(
				sprintf(
				// translators: 1. the current page number; 2. the total number of pages. Example: 5 of 13.
					_x( '%1$s of %2$s', 'Pagination', 'yith-booking-for-woocommerce' ),
					'{{{data.page}}}',
					'{{{data.total_pages}}}'
				)
			);
			?>
		</span>
		<span class="pagination-action next {{{data.page >= data.total_pages ? 'disabled' : ''}}}">
			<i class="yith-icon yith-icon-arrow-right-alt"></i>
		</span>
	</div>
	<# } else { #>

	<?php
	yith_plugin_fw_get_component(
		array(
			'class'    => 'yith-wcbk-resources-modal-table__no-resources-found',
			'type'     => 'list-table-blank-state',
			'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
			'message'  => __( 'No resources found!', 'yith-booking-for-woocommerce' ),
		),
		true
	);
	?>

	<# } #>
</script>

<script type="text/html" id="tmpl-yith-wcbk-product-resource-data">
	<?php
	$resource_data  = new YITH_WCBK_Resource_Data();
	$resource_id    = '{{{data.id}}}';
	$resource_name  = '{{{data.name}}}';
	$resource_image = '{{{data.image}}}';
	$opened         = true;
	yith_wcbk_get_module_view( 'resources', 'product-tabs/resources-tab/resource.php', compact( 'resource_data', 'resource_id', 'resource_name', 'resource_image', 'opened' ) );
	?>
</script>
