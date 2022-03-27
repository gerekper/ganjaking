<?php

class GP_Inventory_Type_Simple extends GP_Inventory_Type {

	public static $type = 'simple';

	public $approved_payments_only = true;

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		parent::__construct();
	}

	public function pre_render( $form ) {
		foreach ( $form['fields'] as &$field ) {
			if ( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$available = (int) $this->get_available_stock( $field );

			if ( rgar( $field, 'gpiShowAvailableInventory', false ) && $available > 0 ) {
				$message = $this->get_inventory_available_message( $field );
				$message = $this->replace_available_inventory_merge_tags( $message, $field );

				$field->description = '<div class="gpi-available-inventory-message" style="padding-bottom: 13px;">' . $message . '</div>' . $field->description;
			}
		}

		return $form;
	}

	/**
	 * @param $message string
	 * @param $field GF_Field
	 * @param $available integer
	 *
	 * @return string
	 */
	public function replace_available_inventory_merge_tags( $message, $field, $available = null ) {
		if ( $available === null ) {
			$available = (int) $this->get_available_stock( $field );
		}

		$message = str_replace( '{available}', number_format_i18n( $available ), $message );
		$message = str_replace( '{limit}', number_format_i18n( $this->get_stock_quantity( $field ) ), $message );

		if ( strpos( $message, '{claimed}' ) !== false ) {
			$message = str_replace( '{claimed}', number_format_i18n( $this->get_claimed_inventory( $field ) ), $message );
		}

		$message = GFCommon::replace_variables( $message, GFAPI::get_form( $field->formId ), GFFormsModel::get_current_lead() );

		preg_match( '/{(.*?)\|(.*?)}/', $message, $plural_singular_match );

		if ( $plural_singular_match ) {
			$plural_singular_tag = $plural_singular_match[0];
			$singular            = $plural_singular_match[1];
			$plural              = $plural_singular_match[2];

			if ( $available === 0 || $available > 1 ) {
				$message = str_replace( $plural_singular_tag, $plural, $message );
			} else {
				$message = str_replace( $plural_singular_tag, $singular, $message );
			}
		}

		return $message;
	}

	public function get_available_stock( $field ) {
		return $this->get_stock_quantity( $field ) - $this->get_claimed_inventory( $field );
	}

	public function is_in_stock( $field ) {
		$available_stock = $this->get_available_stock( $field );

		/**
		 * Filter whether the current field is in stock.
		 *
		 * @since 1.0-beta-1.1
		 *
		 * @param bool     $is_in_stock     Whether the current field is in stock.
		 * @param int|null $available_stock The amount of available stock. Will return `null` for choice-based fields.
		 */
		return gf_apply_filters( array( 'gpi_is_in_stock', $field->formId, $field->id ), $available_stock > 0, $field, $available_stock );
	}

	public function get_stock_quantity( $field ) {
		/**
		 * Filter the inventory limit for fields using the Simple Inventory Type.
		 *
		 * @since 1.0-beta-1.0
		 *
		 * @param int       $inventory_limit  Inventory limit of the current field.
		 * @param GF_Field  $field            The current field.
		 */
		return gf_apply_filters( array( 'gpi_inventory_limit_simple', $field->formId, $field->id ), rgar( $field, 'gpiInventoryLimit', 0 ), $field );
	}

	public function limit_by_approved_payments_only( $query ) {
		$valid_statuses  = array( 'Approved', /* old */ 'Paid', 'Active' );
		$query['where'] .= sprintf( ' AND ( e.payment_status IN ( %s ) OR e.payment_status IS NULL )', self::prepare_strings_for_mysql_in_statement( $valid_statuses ) );
		return $query;
	}

	public static function prepare_strings_for_mysql_in_statement( $strings ) {
		$wrapped = array();
		foreach ( $strings as $string ) {
			$wrapped[] = sprintf( '"%s"', $string );
		}
		return implode( ', ', $wrapped );
	}

	/**
	 * @param $field GF_Field
	 *
	 * Add hooks to customize query
	 */
	public function add_query_hooks( $field ) {
		if ( $this->approved_payments_only ) {
			add_filter( 'gpi_query', array( $this, 'limit_by_approved_payments_only' ) );
		}

		add_filter( 'gpi_query', array( $this, 'exclude_current_gravityview_edit_entry' ) );
	}

	/**
	 * Remove hooks for query customization
	 */
	public function remove_query_hooks() {
		remove_filter( 'gpi_query', array( $this, 'limit_by_approved_payments_only' ) );
		remove_filter( 'gpi_query', array( $this, 'exclude_current_gravityview_edit_entry' ) );
	}

	/**
	 * @param GF_Field $field Field with tracked inventory.
	 *
	 * @return mixed|void
	 */
	public function get_claimed_inventory_query( $field ) {
		global $wpdb;

		$form_id = $field->formId;

		$query = array(
			'select' => 'SELECT sum( em.meta_value ) as quantity',
			'from'   => "FROM {$wpdb->prefix}gf_entry_meta em",
			'join'   => "INNER JOIN {$wpdb->prefix}gf_entry e ON e.id = em.entry_id",
			'where'  => '',
		);

		/*
		 * Default meta key(s) to query with.
		 *
		 *  * Product fields without choices - quantity input will be used, even if it's hidden
		 *  * Product fields with choices - the meta key will be switched to the product field itself. The quantity
		 *     will be joined in as an additional column.
		 *  * Non-product choice fields - query using the field ID. In the event that the field is a checkbox fields,
		 *     the query will be augmented to include a LIKE to capture all of the checkbox inputs.
		 */
		$meta_keys = $this->get_quantity_input_ids( $field );

		if ( empty( $meta_keys ) ) {
			$query['where'] = $wpdb->prepare( "
                WHERE em.form_id = %d
                AND (em.meta_key = %s)
                AND e.status = 'active'\n",
				$form_id, $field->id
			);
		} else {
			$meta_keys_array = implode( ', ', array_map( 'esc_sql', $meta_keys ) );

			$query['where'] = $wpdb->prepare( "
                WHERE em.form_id = %d
                AND em.meta_key IN( {$meta_keys_array} )
                AND e.status = 'active'\n",
				$form_id
			);
		}

		if ( class_exists( 'GF_Partial_Entries' ) ) {
			$query['where'] .= " AND em.entry_id NOT IN( SELECT entry_id FROM {$wpdb->prefix}gf_entry_meta WHERE meta_key = 'partial_entry_id' )";
		}

		/**
		 * Filter the query used to get claimed inventory counts.
		 *
		 * @since 1.0-beta-1.0
		 *
		 * @param array  $query    MySQL query parts.
		 * @param array  $field    Field with tracked inventory.
		 */
		$query = gf_apply_filters( array( 'gpi_query', $form_id, $field->id ), $query, $field );

		return $query;
	}

	/**
	 * @param $field GF_Field
	 *
	 * @return string
	 */
	public function get_inventory_available_message( $field ) {
		return rgar( $field, 'gpiMessageInventoryAvailable', gp_inventory()->inventory_available_default_message() );
	}

	/**
	 * Exclude the entry being edited in GravityView from inventory counts.
	 *
	 * Without this, you can't reselect choices that the current entry has consumed.
	 */
	public function exclude_current_gravityview_edit_entry( $query ) {
		global $wpdb;

		if ( ! class_exists( 'GravityView_Edit_Entry' ) ) {
			return $query;
		}

		// For non-AJAX requests, use whatever GravityView already set. During AJAX property field refresh, GravityView
		// will not have set it, so we need to set it using whatever is posted to lid. We'll then validate that the
		// current user can actually edit whatever entry ID is passed in the lid parameter.
		$current_entry = GravityView_Edit_Entry::getInstance()->instances['render']->get_entry();
		$ajax_entry    = GFAPI::get_entry( rgpost( 'lid' ) );

		if ( ! $current_entry && wp_doing_ajax() ) {
			if ( $ajax_entry && ! is_wp_error( $ajax_entry ) && GravityView_Edit_Entry::check_user_cap_edit_entry( $ajax_entry ) ) {
				$current_entry = $ajax_entry;
			}
		}

		if ( ! $current_entry ) {
			return $query;
		}

		if ( $current_entry ) {
			$query['where'] .= $wpdb->prepare( "\nAND em.entry_id != %d", $current_entry['id'] );
		}

		return $query;
	}

}

function gp_inventory_type_simple() {
	return GP_Inventory_Type_Simple::get_instance();
}
