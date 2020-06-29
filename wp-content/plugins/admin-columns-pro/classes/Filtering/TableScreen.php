<?php

namespace ACP\Filtering;

use AC\Asset\Enqueueable;

abstract class TableScreen {

	/**
	 * @var Model[]
	 */
	protected $models;

	/**
	 * @var Enqueueable[]
	 */
	private $assets;

	/**
	 * @param array $models
	 * @param array $assets
	 */
	public function __construct( array $models, array $assets ) {
		$this->models = $models;
		$this->assets = $assets;

		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
		add_action( 'ac/admin_head', [ $this, 'add_indicator' ], 10, 0 );
		add_action( 'ac/admin_head', [ $this, 'hide_default_dropdowns' ], 10, 0 );
	}

	public function scripts() {
		wp_enqueue_style( 'ac-jquery-ui' );

		foreach ( $this->assets as $asset ) {
			$asset->enqueue();
		}
	}

	/**
	 * Colors the column label orange on the listing screen when it is being filtered
	 */
	public function add_indicator() {
		$classes = [];

		foreach ( $this->models as $model ) {
			if ( ! $model->is_active() || ! $model->get_filter_value() ) {
				continue;
			}

			$column_class = 'thead tr th.column-' . $model->get_column()->get_name();

			$classes[] = $column_class;
			$classes[] = $column_class . ' > a span:first-child';
		}

		if ( ! $classes ) {
			return;
		}

		?>

		<style>
			<?php echo implode( ', ', $classes ) .  '{ font-weight: bold; position: relative; }'; ?>
		</style>

		<?php
	}

	/**
	 * @since 3.8
	 */
	public function hide_default_dropdowns() {
		$disabled = [];

		foreach ( $this->models as $model ) {
			if ( $model instanceof Model\Delegated && ! $model->is_active() ) {
				$disabled[] = '#' . $model->get_dropdown_attr_id();
			}
		}

		if ( ! $disabled ) {
			return;
		}

		?>

		<style>
			<?php echo implode( ', ', $disabled ) . '{ display: none; }'; ?>
		</style>

		<?php
	}

	protected function get_data_from_cache( Model $model ) {
		$cache = new Cache\Model( $model );
		$data = $cache->get();

		if ( ! $data ) {
			$data = [
				'options' => [
					Markup\Dropdown::get_disabled_prefix() . 'loading' => __( 'Loading values', 'codepress-admin-columns' ) . ' ...',
				],
			];
		}

		return $data;
	}

	/**
	 * @return string
	 * @since 3.6
	 */
	public function update_dropdown_cache() {
		ob_start();

		foreach ( $this->models as $model ) {
			if ( ! $model->is_active() || $model->is_ranged() ) {
				continue;
			}

			$cache = new Cache\Model( $model );
			$cache->put_if_expired();

			$this->render_model( $model );
		}

		return ob_get_clean();
	}

	public function render_markup() {
		foreach ( $this->models as $model ) {
			$this->render_model( $model );
		}
	}

	/**
	 * Display dropdown markup
	 *
	 * @param Model $model
	 */
	protected function render_model( Model $model ) {
		if ( $model instanceof Model\Delegated || ! $model->is_active() ) {
			return;
		}

		$column = $model->get_column();

		// Check filter
		$filter_setting = $column->get_setting( 'filter' );

		if ( ! $filter_setting instanceof Settings ) {
			return;
		}

		// Get label
		$label = $filter_setting->get_filter_label();

		if ( ! $label ) {
			$label = $filter_setting->get_filter_label_default();
		}

		// Get name
		$name = $column->get_name();

		// Range inputs or select dropdown
		if ( $model->is_ranged() ) {
			$min = $model->get_request_var( 'min' );
			$max = $model->get_request_var( 'max' );

			switch ( $model->get_data_type() ) {
				case 'date':
					$markup = new Markup\Ranged\Date( $name, $label, $min, $max );

					break;
				case 'numeric':
					$markup = new Markup\Ranged\Number( $name, $label, $min, $max );

					break;
				default:
					return;
			}
		} else {
			$enable_cache = apply_filters( 'acp/filtering/cache/enable', true, $column );

			$data = $enable_cache
				? $this->get_data_from_cache( $model )
				: $model->get_filtering_data();

			$defaults = [
				'order'        => true,
				'options'      => [],
				'empty_option' => false,
				'label'        => $label, // backcompat
				'limit'        => 5000,
			];

			$data = array_merge( $defaults, $data );

			$data = apply_filters( 'acp/filtering/dropdown_args', $data, $model->get_column() );

			$markup = new Markup\Dropdown( $name );
			$markup->set_value( $model->get_request_var() )
			       ->set_label( $label )
			       ->set_order( $data['order'] );

			// backwards compatible for the acp/filtering/dropdown_args filter
			if ( is_array( $data['options'] ) ) {
				$limit = absint( $data['limit'] );

				if ( count( $data['options'] ) >= $limit ) {
					$data['options'] = array_slice( $data['options'], 0, $limit, true );
					$data['options'][ $markup::get_disabled_prefix() . 'limit' ] = '───── ' . sprintf( __( 'Limited to %s items' ), $limit ) . ' ─────';
				}

				$markup->set_options( $data['options'] );
			}

			// backwards compatible for the default options, this should be done using an array as well
			if ( true === $data['empty_option'] ) {
				$markup->set_empty()
				       ->set_nonempty();
			} elseif ( is_array( $data['empty_option'] ) ) {
				$markup->set_empty( $data['empty_option'][0] )
				       ->set_nonempty( $data['empty_option'][1] );
			}
		}

		echo $markup->render();
	}

}