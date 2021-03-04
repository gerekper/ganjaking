<?php

namespace ACP\Editing;

use AC;
use ACP;
use ACP\Editing;
use WP_Error;

/**
 * @since 4.0
 */
abstract class Model extends ACP\Model {

	const VIEW_BULK_EDITABLE = 'bulk_editable';
	const VIEW_PLACEHOLDER = 'placeholder';
	const VIEW_REQUIRED = 'required';
	const VIEW_TYPE = 'type';

	/**
	 * @var WP_Error
	 */
	private $error;

	/**
	 * @param WP_Error $error
	 */
	protected function set_error( WP_Error $error ) {
		$this->error = $error;
	}

	/**
	 * @return bool
	 */
	public function has_error() {
		return $this->error instanceof WP_Error;
	}

	/**
	 * @return WP_Error
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * @return bool Check if editing is enabled by user
	 */
	public function is_active() {
		$setting = $this->column->get_setting( 'edit' );

		if ( ! $setting instanceof Editing\Settings ) {
			return false;
		}

		return $setting->is_active();
	}

	/**
	 * Get editing settings
	 * @return array {
	 * @type string     $type          Type of form field. Accepts: attachment, checkboxlist, checklist, color, float, email, media, number, password, select, select2_dropdown, select2_tags, text, textarea, togglable or url. Default is 'text.
	 * @type string     $placeholder   Add a placeholder text. Only applies to type: text, url, number, password, email.
	 * @type array      $options       Options for select form element. Only applies to type: togglable, select, select2_dropdown and select2_tags.
	 * @type string     $js            If a selector is provided, editable will be delegated to the specified targets. Example: [ 'js' => [ 'selector' => 'a.my-class' ] ];
	 * @type bool       $ajax_populate Populates the available select2 dropdown values through ajax. Only applies to the type: 'select2_dropdown'. Ajax callback used is 'get_editable_ajax_options()'.
	 * @type string|int $range_step    Determines the number intervals for the 'number' type field. Default is 'any'.
	 * @type string     $store_values  If a field can hold multiple values we store the key unless $store_values is set to (bool) true. Default is (bool) false.
	 * @type bool       $bulk_editable If this model supports Bulk Edit
	 * }
	 */
	public function get_view_settings() {
		return [
			'type' => 'text',
		];
	}

	/**
	 * DB value used for storing the edited data
	 *
	 * @param int $id
	 *
	 * @return array|object|string
	 */
	public function get_edit_value( $id ) {
		return $this->column->get_raw_value( $id );
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function get_value( $id ) {
		$value = $this->get_edit_value( $id );

		/**
		 * Filter the raw value, used for editability, for a column
		 *
		 * @param mixed     $value  Column value used for editability
		 * @param int       $id     Post ID to get the column editability for
		 * @param AC\Column $column Column object
		 *
		 * @since 4.0
		 */
		$value = apply_filters( 'acp/editing/value', $value, $id, $this->column );
		$value = apply_filters( 'acp/editing/value/' . $this->column->get_type(), $value, $id, $this->column );

		return $value;
	}

	/**
	 * @param int          $id
	 * @param string|array $value
	 *
	 * @return bool
	 * @since 4.0
	 */
	abstract protected function save( $id, $value );

	/**
	 * @param int   $id
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function update( $id, $value ) {
		$this->error = null;

		/**
		 * Filter for changing the value before storing it to the DB
		 *
		 * @param mixed     $value Value send from inline edit ajax callback
		 * @param AC\Column $column
		 * @param int       $id
		 *
		 * @since 4.0
		 */
		$value = apply_filters( 'acp/editing/save_value', $value, $this->column, $id );

		$result = $this->save( $id, $value );

		if ( ! $this->error ) {
			/**
			 * Fires after a inline-edit successfully saved a value
			 *
			 * @param AC\Column $column Column instance
			 * @param int       $id     Item ID
			 * @param string    $value  User submitted input
			 *
			 * @since 4.0
			 */
			do_action( 'acp/editing/saved', $this->column, $id, $value );
		}

		return $result;
	}

	/**
	 * Register column field settings
	 */
	public function register_settings() {
		$can_bulk_edit = isset( $this->get_view_settings()[ self::VIEW_BULK_EDITABLE ] ) ? $this->get_view_settings()[ self::VIEW_BULK_EDITABLE ] : true;

		$this->column->add_setting( new Editing\Settings( $this->column ) );

		if ( $can_bulk_edit ) {
			$this->column->add_setting( new Editing\Settings\BulkEditing( $this->column ) );
		}
	}

}