<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Fields.
 *
 * Prepares HTML fields to use.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Fields_Model {

	public static $primitive_types = [
		# Data types
		'http://schema.org/DataType',
		'http://schema.org/Boolean',
		'http://schema.org/Date',
		'http://schema.org/DateTime',
		'http://schema.org/Number',
		'http://schema.org/Text',
		'http://schema.org/Time',

		# Enumerations
		'http://schema.org/Enumeration',
		'http://schema.org/ActionStatusType',
		'http://schema.org/BoardingPolicyType',
		'http://schema.org/BookFormatType',
		'http://schema.org/BusinessEntityType',
		'http://schema.org/BusinessFunction',
		'http://schema.org/ContactPointOption',
		'http://schema.org/DayOfWeek',
		'http://schema.org/DeliveryMethod',
		'http://schema.org/DigitalDocumentPermissionType',
		'http://schema.org/EventAttendanceModeEnumeration',
		'http://schema.org/EventStatusType',
		'http://schema.org/GamePlayMode',
		'http://schema.org/GameServerStatus',
		'http://schema.org/GenderType',
		'http://schema.org/GovernmentBenefitsType',
		'http://schema.org/HealthAspectEnumeration',
		'http://schema.org/ItemAvailability',
		'http://schema.org/ItemListOrderType',
		'http://schema.org/LegalForceStatus',
		'http://schema.org/LegalValueLevel',
		'http://schema.org/MapCategoryType',
		'http://schema.org/MediaManipulationRatingEnumeration',
		'http://schema.org/MedicalEnumeration',
		'http://schema.org/MedicalTrialDesign',
		'http://schema.org/MerchantReturnEnumeration',
		'http://schema.org/MusicAlbumProductionType',
		'http://schema.org/MusicAlbumReleaseType',
		'http://schema.org/MusicReleaseFormatType',
		'http://schema.org/NonprofitType',
		'http://schema.org/OfferItemCondition',
		'http://schema.org/OrderStatus',
		'http://schema.org/PaymentMethod',
		'http://schema.org/PaymentStatusType',
		'http://schema.org/PhysicalActivityCategory',
		'http://schema.org/QualitativeValue',
		'http://schema.org/RefundTypeEnumeration',
		'http://schema.org/ReservationStatusType',
		'http://schema.org/RestrictedDiet',
		'http://schema.org/ReturnFeesEnumeration',
		'http://schema.org/RsvpResponseType',
		'http://schema.org/Specialty',
		'http://schema.org/WarrantyScope',
	];

	/**
	 * Magic method for setting up the class.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		add_action(
			'wpbuddy/rich_snippets/rest/property/html/fields',
			array( $this, 'textfield' )
		);

		add_action(
			'wpbuddy/rich_snippets/rest/property/html/fields',
			array( $this, 'the_descendants_select' )
		);

		/**
		 * Fields Hooks Init.
		 *
		 * Allows third party plugins too hook into the Field_Model class after it has been initialized.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/hooks_init
		 *
		 * @param {Field_Model} $field_model
		 *
		 * @since 2.0.0
		 */
		do_action_ref_array( 'wpbuddy/rich_snippets/fields/hooks_init', array( &$this ) );
	}


	/**
	 * Get label from field value.
	 *
	 * @param string $value
	 *
	 * @return string
	 * @since 2.14.0
	 */
	public static function get_label( $value ) {
		$v = self::get_internal_values();

		foreach ( $v as $schema => $options ) {
			foreach ( $options as $o ) {
				if ( $o['id'] === $value ) {
					return $o['label'];
				}
			}
		}

		return $value;
	}

	/**
	 * All possible internal values.
	 *
	 * @return array
	 * @since 2.7.0 public
	 *
	 * @since 2.0.0
	 */
	public static function get_internal_values() {

		$textfield = [
			'id'          => 'textfield',
			'label'       => esc_html_x( 'Direct text input', 'subselect field', 'rich-snippets-schema' ),
			'description' => __( 'Enter your custom text here.', 'rich-snippets-schema' ),
		];

		$textfield_meta = [
			'id'          => 'textfield_meta',
			'label'       => esc_html_x( 'Post meta field', 'subselect field', 'rich-snippets-schema' ),
			'description' => __( 'Enter the name of the text meta field.', 'rich-snippets-schema' ),
		];

		$textfield_termmeta = [
			'id'          => 'textfield_termmeta',
			'label'       => esc_html_x( 'Term meta field', 'subselect field', 'rich-snippets-schema' ),
			'description' => __( 'Enter the name of the term meta field.', 'rich-snippets-schema' ),
		];

		$textfield_option_string = [
			'id'          => 'textfield_option_string',
			'label'       => esc_html_x( 'Option', 'subselect field', 'rich-snippets-schema' ),
			'description' => __( 'Enter the name of the option from the WordPress options database table.', 'rich-snippets-schema' )
		];

		$textfield_option_date = [
			'id'          => 'textfield_option_date',
			'label'       => $textfield_option_string['label'],
			'description' => $textfield_option_string['description']
		];

		$textfield_option_integer = [
			'id'          => 'textfield_option_integer',
			'label'       => $textfield_option_string['label'],
			'description' => $textfield_option_string['description']
		];

		$textfield_option_time = [
			'id'          => 'textfield_option_time',
			'label'       => $textfield_option_string['label'],
			'description' => $textfield_option_string['description']
		];

		$textfield_option_url = [
			'id'          => 'textfield_option_url',
			'label'       => $textfield_option_string['label'],
			'description' => $textfield_option_string['description']
		];

		$textfield_sequential_number = [
			'id'          => 'textfield_sequential_number',
			'label'       => esc_html_x( 'Sequential Number', 'subselect field', 'rich-snippets-schema' ),
			'description' => __( 'Enter a name for the counter.', 'rich-snippets-schema' )
		];

		$values = array(
			'http://schema.org/Text'             => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
				$textfield_option_string,
				array(
					'id'    => 'term_description',
					'label' => esc_html_x( 'Term description (archive pages only)', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_title',
					'label' => esc_html_x( 'Post title', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_excerpt',
					'label' => esc_html_x( 'Post excerpt', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_author_name',
					'label' => esc_html_x( 'Post author name', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'blog_title',
					'label' => esc_html_x( 'Blog title', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'blog_description',
					'label' => esc_html_x( 'Blog description', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_content',
					'label' => esc_html_x( 'Post content', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_id',
					'label' => esc_html_x( 'Post ID', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_category',
					'label' => esc_html_x( 'Category', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'term_title',
					'label' => esc_html_x( 'Term title (loop and archive pages only)', 'subselect field', 'rich-snippets-schema' ),
				),
			),
			'http://schema.org/XPathType'        => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
				$textfield_option_string,
			),
			'http://schema.org/Integer'          => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
				$textfield_option_integer,
				$textfield_sequential_number,
				array(
					'id'    => 'term_description',
					'label' => esc_html_x( 'Term description (archive pages only)', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_id',
					'label' => esc_html_x( 'Post ID', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_thumbnail_width',
					'label' => esc_html_x( 'Post thumbnail width', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_thumbnail_height',
					'label' => esc_html_x( 'Post thumbnail height', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'site_icon_width',
					'label' => esc_html_x( 'Site icon width', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'site_icon_height',
					'label' => esc_html_x( 'Site icon height', 'subselect field', 'rich-snippets-schema' ),
				),
			),
			'http://schema.org/Time'             => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
				$textfield_option_time
			),
			'http://schema.org/DayOfWeek'        => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
			),
			'http://schema.org/Date'             => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
				$textfield_option_date,
				array(
					'id'    => 'term_description',
					'label' => esc_html_x( 'Term description (archive pages only)', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_date',
					'label' => esc_html_x( 'Post published date', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_modified_date',
					'label' => esc_html_x( 'Post modified date', 'subselect field', 'rich-snippets-schema' ),
				),
			),
			'http://schema.org/ImageObject'      => array(
				array(
					'id'    => 'current_post_thumbnail_url',
					'label' => esc_html_x( 'Post thumbnail url', 'subselect field', 'rich-snippets-schema' ),
				),
			),
			'http://schema.org/URL'              => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
				$textfield_option_url,
				array(
					'id'    => 'term_description',
					'label' => esc_html_x( 'Term description (archive pages only)', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_url',
					'label' => esc_html_x( 'Post URL', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_thumbnail_url',
					'label' => esc_html_x( 'Post thumbnail url', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_post_author_url',
					'label' => esc_html_x( 'Post author url', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'blog_url',
					'label' => esc_html_x( 'Blog URL (Site URL)', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'home_url',
					'label' => esc_html_x( 'Home URL', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'site_icon_url',
					'label' => esc_html_x( 'Site icon URL', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'current_category_url',
					'label' => esc_html_x( 'Category URL', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'term_url',
					'label' => esc_html_x( 'Term URL (loop and archive pages only)', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'search_url',
					'label' => esc_html_x( 'Search URL', 'subselect field', 'rich-snippets-schema' ),
				),
				array(
					'id'    => 'search_url_search_term',
					'label' => esc_html_x( 'Search URL (with {search_term_string} placeholder)', 'subselect field', 'rich-snippets-schema' ),
				),
			),
			'http://schema.org/Duration'         => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
			),
			'http://schema.org/Intangible'       => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
			),
			'http://schema.org/Quantity'         => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
				$textfield_option_integer,
			),
			'http://schema.org/Energy'           => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
			),
			'http://schema.org/Mass'             => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
			),
			'http://schema.org/CssSelectorType'  => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
			),
			'http://schema.org/DeliveryMethod'   => array(
				$textfield,
				$textfield_meta,
				$textfield_termmeta,
			),
			'http://schema.org/BusinessFunction' => array(
				$textfield,
			),
		);

		$taxonomies = get_taxonomies( [ 'show_ui' => true ], 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			$values['http://schema.org/Text'][] = [
				'id'    => 'taxonomy_' . $taxonomy->name,
				'label' => sprintf(
					__( 'Comma separated list of %s', 'rich-snippets-schema' ),
					esc_html( $taxonomy->label )
				),
			];
		}

		$values['http://schema.org/Thing']             = $values['http://schema.org/URL'];
		$values['http://schema.org/EntryPoint']        = $values['http://schema.org/URL'];
		$values['http://schema.org/DateTime']          = $values['http://schema.org/Date'];
		$values['http://schema.org/Number']            = $values['http://schema.org/Integer'];
		$values['http://schema.org/QuantitativeValue'] = $values['http://schema.org/Integer'];
		$values['http://schema.org/Distance']          = $values['http://schema.org/Integer'];

		/**
		 * Internal subselect values.
		 *
		 * This filter can be used to add additional options to the subselect item.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/internal_subselect/values
		 *
		 * @param {array} $values The return parameter: an array of values.
		 * @returns {array} An array of values.
		 *
		 * @since 2.0.0
		 */
		return apply_filters(
			'wpbuddy/rich_snippets/fields/internal_subselect/values',
			$values
		);
	}


	/**
	 * Fetches internal subselect values.
	 *
	 * @param Schema_Property $prop
	 * @param string $schema
	 * @param string $selected The selected item.
	 *
	 * @return string[] Array of HTML <option> fields.
	 * @since 2.0.0
	 *
	 */
	public static function get_internal_subselect_options( $prop, $schema, $selected ) {

		$values = self::get_internal_values();

		$options = [];

		foreach ( $values as $value_schema => $fields ) {
			if ( ! in_array( $value_schema, $prop->range_includes ) ) {
				continue;
			}
			foreach ( $fields as $field ) {
				$options[ $field['id'] ] = sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $field['id'] ),
					selected( $selected, $field['id'], false ),
					esc_html( Helper_Model::instance()->remove_schema_url( $field['label'] ) )
				);
			}

		}

		/**
		 * Internal subselect values.
		 *
		 * This filter can be used to add additional options to the subselect item.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/internal_subselect/options
		 *
		 * @param {array} $options The return parameter: an array of options.
		 * @param {Schema_Property} $prop The current property.
		 * @param {string} $schema The current schema class.
		 * @param {string } $selected The current selected item.
		 *
		 * @since 2.0.0
		 * @returns {array} An array of options.
		 */
		return apply_filters(
			'wpbuddy/rich_snippets/fields/internal_subselect/options',
			$options,
			$prop,
			$schema,
			$selected
		);
	}


	/**
	 * Checks if a schema can have a certain field.
	 *
	 * @param Schema_Property $property
	 * @param string $field i.e. 'textfield'
	 *
	 * @return bool
	 * @since 2.12.2
	 *
	 */
	private function is_field_possible( Schema_Property $property, $field ) {

		if ( ! is_array( $property->range_includes ) ) {
			return false;
		}

		if ( count( $property->range_includes ) <= 0 ) {
			return false;
		}

		$fields = self::get_internal_values();

		$found = 0;
//		$current_field = is_array( $property->value ) && array_key_exists( 0, $property->value ) && is_scalar( $property->value[0] ) ? $property->value[0] : 'textfield';

		foreach ( $property->range_includes as $range_include ) {
			if ( ! isset( $fields[ $range_include ] ) ) {
				continue;
			}

			foreach ( $fields[ $range_include ] as $possible_field ) {
				if ( false !== stripos( $possible_field['id'], $field ) ) {
					$found ++;
					break;
				}
//				if ( $current_field === $possible_field['id'] && false !== stripos( $possible_field['id'], $field ) ) {
//					$found ++;
//				}
			}
		}

		return $found > 0;
	}


	/**
	 * Prints a simple text field.
	 *
	 * @param array $args
	 *
	 * @since 2.0.0
	 *
	 */
	public function textfield( $args ) {

		/**
		 * @var Schema_Property $property
		 * @var string $current_type
		 * @var string $html_id
		 * @var string $property_id
		 * @var string $input_name
		 * @var string $selected
		 * @var mixed $value
		 * @var string $screen
		 */
		extract( $args );

		$is_textfield_possible = $this->is_field_possible( $property, 'textfield' );

		if ( 'overwrite' === $screen && ! ( $is_textfield_possible && ! $property->inherits_sub_schema() ) ) {
			return;
		}

		$textvalue = is_scalar( $value ) ? $value : '';

		$input_name = 'edit' === $screen ? $input_name . '[textfield]' : $input_name;

		printf(
			'<textarea data-name="textfield" class="wpb-rs-schema-property-field-text wpb-rs-schema-property-field-text-%s regular-text %s" name="%s">%s</textarea>',
			$html_id,
			$is_textfield_possible && ! $property->inherits_sub_schema() ? '' : 'wpb-rs-hidden',
			$input_name,
			esc_textarea( $textvalue )
		);
	}


	/**
	 * Returns all Methods that can be used internally to fill values.
	 *
	 * @return callable[] Array of callables where the array key is the value ID like textfield | current_post_title |
	 *     current_post_thumbnail |...
	 * @since 2.0.0
	 * @since 2.2.0 Renamed from 'get_internal_values_ids'.
	 *
	 * @see   Values_Model::init()
	 *
	 */
	public static function get_internal_values_methods() {

		$ret_array = array();

		foreach ( self::get_internal_values() as $el ) {
			foreach ( $el as $e ) {
				$ret_array[ $e['id'] ] = isset( $e['method'] ) ? $e['method'] : $e['id'];
			}
		}

		return $ret_array;
	}


	/**
	 * Fetches schema types that can be included directly.
	 *
	 * @param Schema_Property $prop
	 * @param string $schema_type
	 * @param string $selected The selected item.
	 *
	 * @return string[] Array of HTML <option> fields.
	 * @since 2.0.0
	 *
	 */
	public static function get_descendants_types_subselect_options( $prop, $schema_type, $selected ) {

		$values = Schemas_Model::get_type_descendants( $prop->range_includes );

		if ( is_wp_error( $values ) ) {
			$values = array();
		}

		/**
		 * 'direct descendants' subselect values filter.
		 *
		 * This filter can be used to add additional options to the 'direct descendants' subfield select.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/descendants_subselect/values
		 *
		 * @param {array} $values The return parameter: an array of values.
		 * @param {Schema_Property} $prop The current property.
		 * @param {string} $schema The current schema class.
		 *
		 * @returns {array} The array of values.
		 *
		 * @since 2.0.0
		 *
		 */
		$values = apply_filters(
			'wpbuddy/rich_snippets/fields/descendants_subselect/values',
			$values,
			$prop,
			$schema_type
		);

		$options = array();

		foreach ( $values as $type ) {
			$options[ $type ] = sprintf(
				'<option value="descendant-%1$s" %2$s>%3$s</option>',
				esc_attr( $type ),
				selected( $selected, 'descendant-' . $type, false ),
				esc_html( Helper_Model::instance()->remove_schema_url( $type ) )
			);
		}

		/**
		 * Descendants values.
		 *
		 * This filter can be used to add additional options to the 'direct descendants' subselect.
		 *
		 * @hook   wpbuddy/rich_snippets/fields/descendants_subselect/options
		 *
		 * @param  {array}  $var The return parameter: an array of options.
		 * @param  {Schema_Property} $prop The current property.
		 * @param  {string} $schema   The current schema class.
		 * @param  {string} $selected The current selected item.
		 *
		 * @returns {array} The array of options.
		 *
		 * @since  2.0.0
		 */
		return apply_filters(
			'wpbuddy/rich_snippets/fields/descendants_subselect/options',
			$options,
			$prop,
			$schema_type,
			$selected
		);
	}


	/**
	 * Prints a select box for the descendant values
	 *
	 * @param $args
	 *
	 * @since 2.7.0
	 *
	 */
	public static function the_descendants_select( $args ) {

		if ( 'overwrite' !== $args['screen'] ) {
			return;
		}

		if ( ! empty( $args['selected'] ) && false === stripos( $args['selected'], '://schema.org' ) ) {
			return;
		}

		$descendants_select_options = Fields_Model::get_descendants_types_subselect_options(
			$args['property'],
			$args['current_type'],
			$args['value']
		);

		if ( count( $descendants_select_options ) <= 0 ) {
			return;
		}

		printf(
			'<select name="%s">%s</select>',
			$args['input_name'],
			implode( '', $descendants_select_options )
		);
	}


	/**
	 * Checks if a field is selectable.
	 *
	 * @param Schema_Property
	 * @param string $field_name
	 *
	 * @return bool
	 * @since 2.7.0
	 *
	 */
	public static function is_field_selectable( $prop, $field_name ) {
		$values = self::get_internal_values();

		foreach ( $values as $value_schema => $fields ) {
			if ( ! in_array( $value_schema, $prop->range_includes ) ) {
				continue;
			}
			foreach ( $fields as $field ) {
				if ( isset( $field['id'] ) && $field['id'] === $field_name ) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Returns all 'reference' values.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public static function get_reference_values() {

		$values = array(
			'textfield_id'            => _x( 'Custom ID', 'Link to a custom ID on a page.', 'rich-snippets-schema' ),
			'current_post_content_id' => __( 'Post Content (as CreativeWork, deprecated)', 'rich-snippets-schema' ),
		);

		/**
		 * 'link to' subselect values filter.
		 *
		 * This filter can be used to add additional options to the 'link to' subfield select.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/link_to_subselect/values
		 *
		 * @param {array} $values The return parameter: an array of values.
		 * @returns {array} An array of values.
		 *
		 * @since 2.0.0
		 */
		$values = apply_filters(
			'wpbuddy/rich_snippets/fields/link_to_subselect/values',
			$values
		);

		return $values;
	}


	/**
	 * Returns all 'link_to' IDs that can be used to fill values.
	 *
	 * @return array
	 */
	public static function get_reference_values_ids() {

		$values = self::get_reference_values();
		$values = array_keys( $values );

		return array_combine( $values, $values );
	}


	/**
	 * Fetches 'related' subselect options.
	 *
	 * @param Schema_Property $prop
	 *
	 * @return array
	 * @since 2.14.0
	 *
	 */
	public static function get_related_values( $prop ) {

		$values = $prop->range_includes;

		$children = [];
		foreach ( $values as $schema_type ) {
			$c = Schemas_Model::get_children( $schema_type );
			if ( is_wp_error( $c ) ) {
				continue;
			}
			$children = array_merge( $children, $c );
		}

		$children = array_unique( $children );
		$values   = array_merge( $values, $children );
		unset( $children );

//		@todo this is too slow at this point in time
//		because it calls @see Schemas_Model::get_type_details()
//		$values = array_filter( $values, function ( $schema ) {
//			return ! Schemas_Model::is_enumeration( $schema );
//		} );

		sort( $values, SORT_NATURAL );

		return $values;
	}


	/**
	 * Fetches 'related' subselect options.
	 *
	 * @param Schema_Property $prop
	 * @param string $schema
	 * @param string $selected The selected item.
	 *
	 * @return string[] Array of HTML <option> fields.
	 * @since 2.0.0
	 *
	 */
	public static function get_related_subselect_options( $prop, $schema, $selected ) {

		/**
		 * 'related' subselect values filter.
		 *
		 * This filter can be used to add additional options to the 'related' subfield select.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/related_subselect/values
		 *
		 * @param {array}
		 * @param {array} $values The return parameter: an array of values.
		 * @param {Schema_Property} $prop The current property.
		 * @param {string} $schema The current schema class.
		 *
		 * @returns {array}
		 *
		 * @since 2.0.0 An array of values.
		 */
		$values = apply_filters(
			'wpbuddy/rich_snippets/fields/related_subselect/values',
			self::get_related_values( $prop ),
			$prop,
			$schema
		);

		/**
		 * Filter primitive types
		 */
		$values  = array_diff( $values, self::$primitive_types );
		$options = array();

		foreach ( $values as $schema_class ) {
			$options[ $schema_class ] = sprintf(
				'<option data-has_schema="1" value="%1$s" %2$s>%3$s</option>',
				esc_attr( $schema_class ),
				selected( $selected, $schema_class, false ),
				esc_html( Helper_Model::instance()->remove_schema_url( $schema_class ) )
			);
		}

		/**
		 * Related values.
		 *
		 * This filter can be used to add additional options to the 'related' subselect.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/related_subselect/options
		 *
		 * @param {array} $options The return parameter: an array of options.
		 * @param {Schema_Property} $prop The current property.
		 * @param {string} $schema The current schema class.
		 * @param {string} $selected The current selected item.
		 *
		 * @returns {array} An array of options.
		 *
		 * @since 2.0.0
		 */
		return apply_filters(
			'wpbuddy/rich_snippets/fields/related_subselect/options',
			$options,
			$prop,
			$schema,
			$selected
		);
	}


	/**
	 * Fetches 'reference' subselect options.
	 *
	 * @param Schema_Property $prop
	 * @param string $schema
	 * @param string $selected The selected item.
	 *
	 * @return string[] Array of HTML <option> fields.
	 * @since 2.0.0
	 *
	 */
	public static function get_reference_subselect_options( $prop, $schema, $selected ) {

		$values = self::get_reference_values();

		$options = array();

		foreach ( $values as $value => $label ) {
			$options[] = sprintf(
				'<option data-use-textfield="%s" value="%s" %s>%s</option>',
				false !== stripos( $value, 'textfield' ) ? 1 : 0,
				$value,
				selected( $selected, $value, false ),
				esc_html( $label )
			);
		}

		/**
		 * Internal 'reference' values.
		 *
		 * This filter can be used to add additional options to the 'reference' subselect.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/reference_subselect/options
		 *
		 * @param {array} $options The return parameter: an array of options.
		 * @param {Schema_Property} $pro The current property.
		 * @param {string} $schema The current schema class.
		 * @param {string} $selected The current selected item.
		 *
		 * @returns {array} An array of options.
		 *
		 * @since 2.0.0
		 * @since 2.1.1 Renamed from 'wpbuddy/rich_snippets/fields/link_to_subselect/options'
		 *
		 */
		return apply_filters(
			'wpbuddy/rich_snippets/fields/reference_subselect/options',
			$options,
			$prop,
			$schema,
			$selected
		);

	}
}
