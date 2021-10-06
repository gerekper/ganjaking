<?php

namespace ACP\Editing;

use AC\Column;
use AC\Request;
use ACP;
use WP_Error;

/**
 * @deprecated 5.6
 */
abstract class Model implements Service {

	const VIEW_BULK_EDITABLE = 'bulk_editable';

	const VIEW_PLACEHOLDER = 'placeholder';
	const VIEW_REQUIRED = 'required';
	const VIEW_TYPE = 'type';

	/**
	 * @var Column
	 */
	protected $column;

	/**
	 * @var WP_Error
	 */
	protected $error;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_view( $context ) {
		$settings = $this->get_view_settings();

		// Backwards compatibility
		$is_bulk_editable = ! isset( $settings[ self:: VIEW_BULK_EDITABLE ] ) || false !== $settings[ self:: VIEW_BULK_EDITABLE ];

		if ( Service::CONTEXT_BULK === $context && ! $is_bulk_editable ) {
			return false;
		}

		return new View\Legacy( $this->get_view_settings() );
	}

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
	 * @return Column
	 */
	public function get_column() {
		return $this->column;
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
			self::VIEW_TYPE => 'text',
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

	public function get_value( $id ) {
		return $this->get_edit_value( $id );
	}

	/**
	 * @param int          $id
	 * @param string|array $value
	 *
	 * @return bool
	 */
	abstract protected function save( $id, $value );

	public function update( Request $request ) {
		return $this->save( $request->get( 'id' ), $request->get( 'value' ) );
	}

	/**
	 * Register column field settings
	 */
	public function register_settings() {
	}

}