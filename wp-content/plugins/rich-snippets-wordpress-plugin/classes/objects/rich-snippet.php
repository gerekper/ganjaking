<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Json object.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Rich_Snippet {

	/**
	 * The snippet ID.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $id = '';


	/**
	 * The context.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $context = 'http://schema.org';


	/**
	 * The type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $type = 'Thing';


	/**
	 * If the object has been prepared for output.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private $_is_ready = false;


	/**
	 * Shows if the current snippet is the main/parent snippet.
	 *
	 * @since 2.5.4
	 *
	 * @var bool
	 */
	private $_is_main_snippet = false;


	/**
	 * The plugin version the snippet was created with.
	 *
	 * @since 2.5.4
	 *
	 * @var string
	 */
	private $_version_created = null;


	/**
	 * If SNIP should iterate over this snippet with a loop.
	 *
	 * The output will then create an array of multiple snippets of the same item as this one.
	 *
	 * @since 2.8.0
	 *
	 * @var string
	 */
	private $_loop = null;


	/**
	 * The parent snippet ID.
	 *
	 * @since 2.14.1
	 *
	 * @var null|string
	 */
	private $_parent_id = null;


	/**
	 * The main (mother) snippet ID (not necessarily the parent).
	 *
	 * @since 2.14.1
	 *
	 * @var null|string
	 */
	private $_main_id = null;


	/**
	 * A helper array used for overwriting properties.
	 *
	 * @since      2.14.3
	 * @deprecated 2.14.18
	 *
	 * @var array
	 */
	private $_overwrite_name_helper = [];


	/**
	 * Rich_Snippet constructor.
	 *
	 * @param array
	 *
	 * @since 2.5.4 Added $args parameter.
	 *
	 * @since 2.0.0
	 */
	public function __construct( $args = [] ) {

		foreach ( $args as $arg_key => $arg_value ) {
			$this->{$arg_key} = $arg_value;
		}

		$this->id = uniqid( 'snip-' );
	}


	/**
	 * Sets properties.
	 *
	 * @param array $props
	 *
	 * @since 2.0.0
	 *
	 */
	public function set_props( $props = array() ) {

		foreach ( $props as $prop ) {
			$this->set_prop( $prop['name'], $prop['value'], isset( $prop['id'] ) ? $prop['id'] : null );
		}
	}


	/**
	 * Sets a single property.
	 *
	 * Will add a '-prop-xxx' unique ID to each property that is not a class var.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @param string|null $id A unique ID for this prop (without the '-prop-' prefix)
	 *
	 * @since 2.0.0
	 *
	 */
	public function set_prop( $name, $value, $id = null ) {

		if ( empty( $id ) ) {
			$id = uniqid( '-prop-' );
		} else {
			if ( false !== stripos( $id, 'prop-' ) ) {
				$id = '-' . $id;
			} else {
				$id = '-prop-' . $id;
			}
		}

		$this->{$name . $id} = $value;
	}


	/**
	 * Returns an array of properties.
	 *
	 * @return Schema_Property[]
	 *
	 * @note  This function maybe slow when used on the Frontend as it searches all schemas.
	 *
	 * @since 2.0.0
	 */
	public function get_properties() {

		$object_vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$object_props = array_diff_key( $object_vars, $class_vars );

		$props = array();

		foreach ( $object_props as $k => $v ) {
			$prop_id  = $this->normalize_property_name( $k );
			$prop_uid = str_replace( $prop_id . '-prop-', '', $k );
			$prop_id  = 'http://schema.org/' . $prop_id;

			$prop = Schemas_Model::get_property_by_id( $prop_id );

			if ( $prop instanceof Schema_Property ) {
				$prop->value                = $v;
				$prop->overridable          = $v['overridable'] ?? false;
				$prop->overridable_multiple = $v['overridable_multiple'] ?? false;
				$prop->uid                  = $prop_uid;

				$props[] = $prop;
			}
		}

		return $props;
	}


	/**
	 * Get overridable properties.
	 *
	 * @param int|null $post_id
	 * @param string $input_name
	 * @param string $object_tpe
	 *
	 * @return \stdClass[]|Schema_Property[]
	 *
	 * @since 2.14.0
	 * @since 2.14.8 Added $object_type parameter.
	 */
	public function get_overridable_properties( $post_id = null, $input_name = '', $object_tpe = 'stdClass' ) {

		$object_vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$object_props = array_diff_key( $object_vars, $class_vars );

		$props = array();

		foreach ( $object_props as $k => $v ) {
			if ( isset( $v['overridable'] ) && true === $v['overridable'] ) {

				$props[ $k ] = (object) [
					'uid'   => $this->get_property_uid( $k ),
					'label' => $this->normalize_property_name( $k ),
					'value' => $v,
				];

				# do we have this constellation:
				# - same property name
				# - AND property is overwritable_multiple
				# - AND has different sub-schema?
				# If YES, we need to set a different label.
				$has_same_prop_with_diff_value = call_user_func( function ( $prop_list, $search, $uid ) {
					foreach ( $prop_list as $key => $prop ) {
						if ( 0 === strpos( $key, $search ) && false === strpos( $key, $search . '-prop-' . $uid ) ) {
							return true;
						}
					}

					return false;
				}, $object_props, $props[ $k ]->label, $props[ $k ]->uid );

				$props[ $k ]->possible_duplicate = $has_same_prop_with_diff_value;

				unset( $has_same_prop_with_diff_value, $k, $v );
			}
		}

		unset( $object_props, $class_vars, $object_vars );

		if ( count( $props ) <= 0 ) {
			return [];
		}

		/**
		 * End here means: no data to overwrite.
		 * OR: no post_id and input_name specified to get the overwritten data from.
		 */
		if ( is_null( $post_id ) ) {
			return $props;
		}

		if ( empty( $input_name ) ) {
			return $props;
		}

		global $wpdb;
		$new_props = $props;

		$prop_counter = function ( $props, $name, $uid = null ) {
			$i = 0;
			foreach ( $props as $prop ) {
				if ( $prop->label === $name ) {
					if ( is_null( $uid ) ) {
						$i ++;
					} else {
						if ( $prop->uid === $uid ) {
							$i ++;
						}
					}
				}
			}

			return $i;
		};

		/**
		 * Find duplicate props
		 */
		foreach ( $props as $p => $prop ) {
			if ( ! $prop->value['overridable_multiple'] ) {
				continue;
			}

			$meta_key = $input_name . $prop->label;

			if ( $prop->possible_duplicate ) {
				$meta_key .= '-' . $prop->uid . '-';
			}

			$no = intval( $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM ( SELECT COUNT(*) FROM {$wpdb->postmeta} as pm WHERE post_id = %d AND meta_key REGEXP '{$meta_key}[0-9]+' GROUP BY SUBSTRING_INDEX( SUBSTRING_INDEX( meta_key, %s, -1 ), '_', 1 ) ) as table_a",
				$post_id,
				$meta_key
			) ) );

			$no = $no - $prop_counter( $props, $prop->label, $prop->possible_duplicate ? $prop->uid : null );

			# back compat
			$meta_key_back_compat = $name_back_compat = preg_replace( '#-[0-9a-z]+-#', '', $meta_key );
			if ( $meta_key_back_compat !== $meta_key ) {
				$no_back_compat = intval( $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*) FROM ( SELECT COUNT(*) FROM {$wpdb->postmeta} as pm WHERE post_id = %d AND meta_key REGEXP '{$meta_key_back_compat}[0-9]+' GROUP BY SUBSTRING_INDEX( SUBSTRING_INDEX( meta_key, %s, -1 ), '_', 1 ) ) as table_a",
					$post_id,
					$meta_key_back_compat
				) ) );

				$no_back_compat = $no_back_compat - $prop_counter( $props, $prop->label, null );

				$no = max( 0, $no, $no_back_compat );

				unset( $no_back_compat );
			}

			if ( $no > 0 ) {
				for ( $i = 0; $i < $no; $i ++ ) {
					$new_props[] = clone $prop;
				}
				unset( $i );
			}

			unset( $p, $prop, $meta_key, $no );
		}

		unset( $prop_counter );

		$name_helper             = [];
		$name_helper_back_compat = [];

		/**
		 * Fill the values
		 */
		foreach ( $new_props as $k => $prop ) {

			$helper_label = $prop->possible_duplicate ? $prop->label . "-{$prop->uid}-" : $prop->label;

			if ( ! isset( $name_helper[ $helper_label ] ) ) {
				$name_helper[ $helper_label ] = - 1;
			}
			$name_helper[ $helper_label ] ++;

			# back compat
			if ( ! isset( $name_helper_back_compat[ $prop->label ] ) ) {
				$name_helper_back_compat[ $prop->label ] = - 1;
			}
			$name_helper_back_compat[ $prop->label ] ++;

			# the input name
			# make sure we add the UID if there is another property with the same name
			$name = sprintf(
				'%s_%s%s%d',
				substr( $input_name, 0, strrpos( $input_name, '_' ) ),
				$prop->label,
				$prop->possible_duplicate ? '-' . $prop->uid . '-' : '', # only add this if we have multiple properties with the same name
				$name_helper[ $helper_label ]
			);

			# back compat
			$name_back_compat = preg_replace( '#-[0-9a-z]+-#', '', $name );

			# make sure the name fits into the database row
			# @todo make this work. Note that the SQL query for finding duplicate rows needs to be rewritten, too!
//			if ( strlen( $name ) > 255 ) {
//				$elements = preg_split( "#snippet_[0-9]+_#", $name );
//				if ( is_array( $elements ) && isset( $elements[1] ) ) {
//					$name = str_replace( $elements[1], Helper_Model::instance()->get_short_hash( $elements[1] ), $name );
//				}
//			}

			$new_props[ $k ]->overridable_input_name = $name;
			$new_props[ $k ]->overwritten            = false;

			$value = get_post_meta( $post_id, $name, true );

			if ( empty( $value ) ) {
				# back compat
				if ( $name !== $name_back_compat ) {
					$value = get_post_meta( $post_id, $name_back_compat, true );
					if ( empty( $value ) ) {
						continue;
					}
				} else {
					continue;
				}
			}

			$new_props[ $k ]->value[1]    = $value;
			$new_props[ $k ]->overwritten = true;
		}

		unset( $name_helper, $name_helper_back_compat );

		# create Schema_Property objects if necessary
		if ( 'Schema_Property' === $object_tpe ) {

			$props     = $new_props;
			$new_props = [];

			foreach ( $props as $k => $prop ) {

				$real_prop = Schemas_Model::get_property_by_id( 'http://schema.org/' . $prop->label );

				if ( $real_prop instanceof Schema_Property ) {
					$real_prop->value                  = $prop->value;
					$real_prop->overridable            = $prop->value['overridable'] ?? false;
					$real_prop->overridable_multiple   = $prop->value['overridable_multiple'] ?? false;
					$real_prop->uid                    = $prop->uid;
					$real_prop->overridable_input_name = $prop->overridable_input_name;

					$new_props[ $k ] = $real_prop;
				}
			}
		}

		return $new_props;
	}


	/**
	 * Removes "-prop-*****" names from property names.
	 *
	 * @param string $prop
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	private function normalize_property_name( $prop ) {

		$prop_id = strstr( $prop, '-prop-', true );

		return str_replace( '-prop-', '', $prop_id );
	}


	/**
	 * Returns a property value for a given full url (e.g. https://schema.org/image )
	 *
	 * @param string $url
	 *
	 * @return mixed|null Null if value does not exist.
	 * @since 2.0.0
	 *
	 */
	public function get_property_value_by_path( $url ) {

		$url      = untrailingslashit( $url );
		$val_name = Helper_Model::instance()->remove_schema_url( $url );

		if ( isset( $this->{$val_name} ) ) {
			return $this->{$val_name};
		}

		return null;
	}


	/**
	 * Returns a property by uid.
	 *
	 * @param string $uid
	 *
	 * @return bool|Schema_Property
	 *
	 * @since 2.14.0
	 */
	public function get_property_by_uid( $uid ) {
		$object_vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$object_props = array_diff_key( $object_vars, $class_vars );

		$object_props_keys = array_keys( $object_props );

		foreach ( $object_props_keys as $k ) {
			$name = $this->normalize_property_name( $k );
			$name = str_replace( $name . '-prop-', '', $k );

			if ( $name === $uid ) {
				return $this->{$k};
			}
		}

		return false;
	}


	/**
	 * Searches the property name when the UID is given.
	 *
	 * @param string $uid
	 *
	 * @return bool|string
	 *
	 * @since 2.14.0
	 */
	public function get_property_name_by_uid( $uid ) {
		$object_vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$object_props = array_diff_key( $object_vars, $class_vars );

		$object_props_keys = array_keys( $object_props );

		foreach ( $object_props_keys as $k ) {
			$name = $this->normalize_property_name( $k );
			$name = str_replace( $name . '-prop-', '', $k );

			if ( $name === $uid ) {
				return $k;
			}
		}

		return false;
	}


	/**
	 * Returns the UID of a property.
	 *
	 * @param string $property_name
	 *
	 * @return string
	 *
	 * @since 2.14.0
	 */
	public function get_property_uid( $property_name ) {
		$name = $this->normalize_property_name( $property_name );

		return str_replace( $name . '-prop-', '', $property_name );
	}


	/**
	 * Outputs a JSON-String of the object.
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function __toString(): string {

		if ( ! $this->_is_ready ) {
			return sprintf( '<!--%s-->',
				__( 'Object is not ready for output, yet. Please call \wpbuddy\rich_snippets\Rich_Snippet::prepare_for_output() first.',
					'rich-snippets-schema' )
			);
		}

		$prettyprint = (bool) get_option( 'wpb_rs/setting/frontend_json_prettyprint', false );

		return json_encode( $this, $prettyprint ? JSON_PRETTY_PRINT : 0 );
	}


	/**
	 * Iterates over all items in a loop and prepares the items.
	 *
	 * @param array $meta_info
	 *
	 * @since 2.8.0
	 *
	 */
	private function prepare_loop_items( $meta_info ) {
		$vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$props = array_diff_key( $vars, $class_vars );

		foreach ( $props as $prop_name_with_id => $prop_value ) {
			if ( ! isset( $prop_value[1] ) ) {
				continue;
			}

			/**
			 * @var Rich_Snippet $child_snippet
			 */
			$child_snippet = $prop_value[1];

			if ( ! $child_snippet instanceof Rich_Snippet ) {
				continue;
			}

			if ( ! $child_snippet->is_loop() ) {
				continue;
			}

			unset( $this->{$prop_name_with_id} );

			$prop_name_without_id = $this->normalize_property_name( $prop_name_with_id );

			$items = $child_snippet->get_items_for_loop( $meta_info['current_post_id'] );

			foreach ( $items as $loop_item_id => $loop_item ) {
				$snippet = clone $child_snippet;
				$snippet->reset_loop();

				$item_meta_info                    = $meta_info;
				$item_meta_info['current_post_id'] = $loop_item_id;
				$item_meta_info['object']          = $loop_item;
				$item_meta_info['in_the_loop']     = true;

				$this->set_prop( $prop_name_without_id, $snippet->prepare_for_output( $item_meta_info ) );
			}
		}

	}


	/**
	 * Prepares object for output.
	 *
	 * @param array $meta_info
	 *
	 * @return \wpbuddy\rich_snippets\Rich_Snippet
	 *
	 * @since 2.0.0
	 *
	 */
	public function prepare_for_output( array $meta_info = array() ): Rich_Snippet {

		$meta_info = wp_parse_args( $meta_info, array(
			'current_post_id' => 0,
			'snippet_post_id' => 0,
			'input'           => '',
		) );

		if ( $this->_is_ready ) {
			return $this;
		}

		# overwrite values, if any
		$this->overwrite_values( $meta_info );

		# prepare loop items
		$this->prepare_loop_items( $meta_info );

		# merge multiple properties together
		$this->merge_multiple_props();

		# fill all values
		$this->fill_values( $meta_info );

		# rename some properties
		$this->{'@context'} = $this->context;
		$this->{'@type'}    = $this->type;

		# inject custom JSON+LD data
		$this->inject_custom_json_ld( $meta_info );

		# add "creator" property if needed
		if ( $this->_is_main_snippet && (bool) get_option( 'wpb_rs/setting/frontend_json_creator', false ) ) {
			$this->{'@context'}     = [ $this->{'@context'}, 'snip' => 'https://rich-snippets.io/' ];
			$this->{'snip:creator'} = 'SNIP';
		}

		# delete all internal object vars
		foreach ( array_keys( get_class_vars( __CLASS__ ) ) as $k ) {
			unset( $this->{$k} );
		}

		foreach ( array_keys( get_object_vars( $this ) ) as $k ) {

			if ( ! isset( $this->{$k} ) ) {
				continue;
			}

			# filter empty props if they are not integers or floats
			if ( true === $this->is_property_empty( $k ) ) {
				unset( $this->{$k} );
				continue;
			}

			/**
			 * Workaround: Scalar values need to be transformed to strings.
			 * This is because the structured data test tools don't like integer values.
			 */
			if ( is_scalar( $this->{$k} ) ) {
				$this->{$k} = (string) $this->{$k};
				continue;
			}

			/**
			 * Filter empty array values
			 */
			if ( is_array( $this->{$k} ) ) {
				$this->{$k} = array_filter( $this->{$k} );
				$this->{$k} = array_values( $this->{$k} );
				continue;
			}

		}

		/**
		 * Rich Snippet Prepare Output Action.
		 *
		 * Allows third party plugins to perform any actions after a Snippet has been prepared for output.
		 *
		 * @hook  wpbuddy/rich_snippets/rich_snippet/prepare
		 *
		 * @param {Rich_Snippet} $rich_snippet
		 *
		 * @since 2.0.0
		 */
		do_action_ref_array( 'wpbuddy/rich_snippets/rich_snippet/prepare', array( &$this ) );

		$this->_is_ready = true;

		return $this;
	}


	/**
	 * Prepares the snippet for export.
	 *
	 * @since 2.13.3
	 */
	public function prepare_for_export() {

		if ( $this->is_loop() ) {
			$this->loop = $this->_loop;
		}

		foreach ( array_keys( get_object_vars( $this ) ) as $k ) {
			if ( isset( $this->{$k} ) && is_array( $this->{$k} ) && isset( $this->{$k}[1] ) && $this->{$k}[1] instanceof Rich_Snippet ) {
				$this->{$k}[1]->prepare_for_export();
			}
		}
	}


	/**
	 * Returns the value.
	 *
	 * @param mixed $var A key-value pair where the first is the field type and the second is the value itself.
	 * @param array $meta_info
	 *
	 * @return mixed
	 * @since 2.0.0
	 *
	 * @see   \wpbuddy\rich_snippets\Admin_Snippets_Controller::search_value_by_id()
	 */
	private function get_the_value( $var, array $meta_info ) {

		$field_type  = '';
		$overwritten = false;

		if ( is_array( $var ) ) {
			$overwritten = array_key_exists( 'overwritten', $var ) ? $var['overwritten'] : false;

			if ( isset( $var[1] ) && ( $var[1] instanceof Rich_Snippet ) ) {
				if ( is_array( $var ) && isset( $var['input_name'] ) ) {
					$meta_info['input'] = $var['input_name'] . '_';
				}
				$var = $var[1]->prepare_for_output( $meta_info );
			} else {
				$field_type = $var[0];

				if ( empty( $field_type ) ) {
					return '';
				}

				$var = isset( $var[1] ) ? $var[1] : '';

			}
		}

		/**
		 * Rich_Snippet value filter.
		 *
		 * Allows plugins to hook into the value that will be outputted later.
		 *
		 * @hook  wpbuddy/rich_snippets/rich_snippet/value
		 *
		 * @param {mixed}        $value      The value.
		 * @param {string}       $field_type The field type (ie. textfield).
		 * @param {Rich_Snippet} $object     The current Rich_Snippet object.
		 * @param {array}        $meta_info
		 * @param {bool}         $overwritten If the property has been overwritten on a per-post-basis.
		 *
		 * @returns {mixed}
		 *
		 * @since 2.0.0
		 *
		 */
		$var = apply_filters( 'wpbuddy/rich_snippets/rich_snippet/value', $var, $field_type, $this, $meta_info, $overwritten );

		# bail early if there is no field type (maybe when the value was overwritten)
		if ( empty( $field_type ) ) {
			return $var;
		}

		/**
		 * Rich_Snippet value type filter.
		 *
		 * Allows plugins to hook into the value. The last parameter is the $field_type (ie. textfield).
		 *
		 * @hook  wpbuddy/rich_snippets/rich_snippet/value/{$field_type}
		 *
		 * @param {mixed}        $value  The value.
		 * @param {Rich_Snippet} $object The current Rich_Snippet object.
		 * @param {array}        $meta_info
		 * @param {bool}         $overwritten If the property has been overwritten on a per-post-basis.
		 *
		 * @returns {mixed}
		 *
		 * @since 2.0.0
		 */
		$var = apply_filters( 'wpbuddy/rich_snippets/rich_snippet/value/' . $field_type, $var, $this, $meta_info, $overwritten );

		return $var;
	}


	/**
	 * Gets the main type i.e. http://schema.org/Thing
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function get_type(): string {

		return trailingslashit( $this->context ) . $this->type;
	}


	/**
	 * Checks if a snippet has properties.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function has_properties(): bool {

		$object_vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$object_props = array_diff_key( $object_vars, $class_vars );

		return count( $object_props ) > 1;
	}


	/**
	 * Merges multiple props together.
	 *
	 * @since 2.0.0
	 */
	private function merge_multiple_props() {

		$vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$props = array_diff_key( $vars, $class_vars );

		foreach ( $props as $prop_key => $prop_value ) {
			$real_prop_name = $this->normalize_property_name( $prop_key );

			if ( ! isset( $this->{$real_prop_name} ) ) {
				$this->{$real_prop_name} = $prop_value;
				unset( $this->{$prop_key} );
				continue;
			}

			if ( ! $this->{$real_prop_name} instanceof Multiple_Property ) {
				# create new Multiple_Property
				$mp = new Multiple_Property();

				# copy the previous value
				$mp[] = $this->{$real_prop_name};

				# replace the previous value
				$this->{$real_prop_name} = $mp;
			}

			$this->{$real_prop_name}[] = $prop_value;

			unset( $this->{$prop_key} );
		}

	}


	/**
	 * Fills property values.
	 *
	 * @param array $meta_info
	 */
	private function fill_values( $meta_info ) {

		$vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$props = array_diff_key( $vars, $class_vars );

		foreach ( $props as $name => $var ) {

			if ( ! $this->{$name} instanceof Multiple_Property ) {
				$this->{$name} = $this->get_the_value( $var, $meta_info );
			} else {
				$sub_props = array();
				foreach ( $this->{$name} as $sub_prop_key => $sub_prop ) {

					$sub_props[ $sub_prop_key ] = $this->get_the_value( $sub_prop, $meta_info );
				}
				$this->{$name} = $sub_props;
			}
		}
	}


	/**
	 * Checks if the snippet has properties that can be overwritten.
	 *
	 * @return bool
	 *
	 * @since 2.2.0
	 */
	public function has_overridable_props() {

		$vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$props = array_diff_key( $vars, $class_vars );

		foreach ( $props as $name => $var ) {
			if ( ! isset( $var['overridable'] ) ) {
				continue;
			}

			if ( $var['overridable'] ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Adds necessary IDs to a snippet.
	 *
	 * @param Rich_Snippet $snippet
	 *
	 * @return Rich_Snippet
	 *
	 * @since 2.14.1
	 */
	public function idfy( $main_id, $parent_id ) {

		$this->_parent_id = $parent_id;
		$this->_main_id   = $main_id;

		$vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$props = array_diff_key( $vars, $class_vars );

		foreach ( $props as $prop_name_with_id => $prop ) {

			if ( ! is_array( $this->{$prop_name_with_id} ) ) {
				continue;
			}

			if ( ! isset( $this->{$prop_name_with_id}[1] ) ) {
				continue;
			}

			if ( ! $this->{$prop_name_with_id}[1] instanceof Rich_Snippet ) {
				continue;
			}

			$this->{$prop_name_with_id}[1]->idfy( $main_id, $this->id );
		}
	}


	/**
	 * Overwrites values, if any.
	 *
	 * @param array $meta_info
	 *
	 * @since 2.2.0
	 */
	public function overwrite_values( $meta_info ) {

		if ( empty( $meta_info['current_post_id'] ) ) {
			return;
		}

		/**
		 * Back compat: old format
		 */
		$overwrite_data = Helper_Model::instance()->get_properties_to_overwrite( $meta_info['current_post_id'], $this->_main_id, $this->id, $this->_parent_id );
		if ( count( $overwrite_data ) > 0 ) {

			$did_overwrite = false;

			if ( is_array( $overwrite_data ) && count( $overwrite_data ) > 0 ) {
				$this->overwrite_properties( $overwrite_data );
				$did_overwrite = true;
			}

			$list_data = Helper_Model::instance()->get_properties_to_list( $meta_info['current_post_id'], $this->_main_id, $this, $this->_parent_id );
			if ( is_array( $list_data ) && count( $list_data ) > 0 ) {
				foreach ( $list_data as $snippet_id => $ld ) {
					$this->create_duplicate( $ld['prop_name'], $ld['properties'], $snippet_id );
				}
				$did_overwrite = true;
			}

			if ( $did_overwrite ) {
				return;
			}
		}

		/**
		 * New format
		 */
		$props = $this->get_overridable_properties( $meta_info['current_post_id'], $meta_info['input'] );

		if ( count( $props ) <= 0 ) {
			return;
		}

		$props_to_delete = [];

		foreach ( $props as $prop ) {
			$props_to_delete[]        = $prop->uid;
			$prop_value               = unserialize( serialize( $prop->value ) );
			$prop_value['input_name'] = $prop->overridable_input_name;

			if ( isset( $prop->overwritten ) && $prop->overwritten ) {
				# avoid overwriting
				$prop_value['overwritten'] = true;
			}

			$this->set_prop( $prop->label, $prop_value );
		}

		$props_to_delete = array_filter( $props_to_delete );

		foreach ( $props_to_delete as $prop_to_delete ) {
			$prop_name = $this->get_property_name_by_uid( $prop_to_delete );
			if ( isset( $this->{$prop_name} ) ) {
				unset( $this->{$prop_name} );
			}
		}
	}


	/**
	 * Integrates custom JSON+LD values for the main snippet.
	 *
	 * @param array $meta_info
	 *
	 * @since 2.4.0
	 */
	private function inject_custom_json_ld( $meta_info ) {

		if ( ! isset( $meta_info['snippet_post_id'] ) ) {
			return;
		}

		if ( empty( $meta_info['snippet_post_id'] ) ) {
			return;
		}

		if ( ! $this->is_main_snippet() ) {
			return;
		}

		$json_ld_data = (array) get_post_meta( $meta_info['snippet_post_id'], '_wpb_rs_jsonld', true );

		/**
		 * Custom JSON+LD filter.
		 *
		 * Allows to hook into custom JSON+LD code.
		 *
		 * @hook  wpbuddy/rich_snippets/rich_snippet/json+ld
		 *
		 * @param {array} $json_ld_data
		 * @param {array} $meta_info
		 *
		 * @returns {array}
		 *
		 * @since 2.4.0
		 */
		$json_ld_data = apply_filters( 'wpbuddy/rich_snippets/rich_snippet/json+ld', $json_ld_data, $meta_info );

		$json_ld_data = array_filter( $json_ld_data );

		foreach ( $json_ld_data as $key => $value ) {
			/**
			 * Custom JSON+LD value filter.
			 *
			 * Allows to change a custom JSON+LD value.
			 *
			 * @hook  wpbuddy/rich_snippets/rich_snippet/json+ld/value
			 *
			 * @param {mixed} $value
			 * @param {mixed} $meta_info
			 *
			 * @since 2.4.0
			 *
			 * @returns {mixed}
			 */
			$value = apply_filters( 'wpbuddy/rich_snippets/rich_snippet/json+ld/value', $value, $meta_info );

			/**
			 * Dynamic custom JSON+LD value filter.
			 *
			 * Allows to change a custom JSON+LD value.
			 *
			 * @hook  wpbuddy/rich_snippets/rich_snippet/json+ld/value/{key}
			 *
			 * @param {mixed} $value
			 * @param {mixed} $meta_info
			 *
			 * @since 2.4.0
			 *
			 * @returns {mixed}
			 */
			$value = apply_filters( 'wpbuddy/rich_snippets/rich_snippet/json+ld/value/' . $key, $value, $meta_info );

			if ( empty( $value ) ) {
				continue;
			}

			$this->{$key} = $value;
		}
	}


	/**
	 * Get the plugin version the snippet was created with.
	 *
	 * @return string x.x.x format.
	 * @since 2.5.4
	 *
	 */
	public function get_version_created() {
		return $this->_version_created ?? null;
	}


	/**
	 * Sets the _is_main_snippet property. But only if the snippet was created with plugin version number 2.5.3 or
	 * lower (meaning, the version string is empty).
	 *
	 * @param $val
	 *
	 * @deprecated 2.4.5
	 *
	 * @since      2.5.4
	 *
	 */
	public function set_is_main_snippet( $val ) {
		$plugin_version = $this->get_version_created();

		if ( empty( $plugin_version ) ) {
			$this->_is_main_snippet = $val;
		}
	}


	/**
	 * If the current snippet is the main/parent snippet.
	 *
	 * @return bool
	 * @since 2.5.4
	 *
	 */
	public function is_main_snippet() {
		return $this->_is_main_snippet ?? false;
	}


	/**
	 * If a loop is configured for this snippet.
	 *
	 * @return bool
	 * @since 2.8.0
	 *
	 */
	public function is_loop() {
		return ! empty( $this->_loop );
	}


	/**
	 * The type of the loop.
	 *
	 * @return string
	 * @since 2.8.0
	 *
	 */
	public function get_loop_type() {
		return $this->_loop;
	}


	/**
	 * Returns the loop items.
	 *
	 * @param int $post_id
	 *
	 * @return mixed[] Array of items (could be objects)
	 *
	 * @since 2.8.0
	 */
	public function get_items_for_loop( $post_id ) {

		$items = [];

		if ( 'main_query' === $this->_loop ) {
			global $wp_the_query;

			if ( isset( $wp_the_query ) && $wp_the_query instanceof \WP_Query ) {
				$items = $wp_the_query->get_posts();
			}

			$items = array_combine( wp_list_pluck( $items, 'ID' ), $items );

		} elseif ( 'page_parents' === $this->_loop ) {
			$items = get_post_ancestors( $post_id ); #@todo test this again
			$items = array_reverse( $items );
			$items = array_combine( $items, $items );

		} elseif ( 0 === stripos( $this->_loop, 'menu_' ) ) {

			$menu_name = str_replace( 'menu_', '', $this->_loop );

			$items = wp_get_nav_menu_items( $menu_name );

			$menu_id = call_user_func( function ( $items, $id ) {
				foreach ( $items as $item ) {
					if ( isset( $item->object_id ) && $item->object_id == $id ) {
						return $item->ID;
					}
				}

				return $id;
			}, $items, $post_id );

			$items = Helper_Model::instance()->filter_item_hierarchy(
				$items,
				$menu_id,
				'menu_item_parent',
				'ID'
			);

			$items = array_reverse( $items );
			$items = array_combine( wp_list_pluck( $items, 'object_id' ), $items );

		} elseif ( 0 === stripos( $this->_loop, 'taxonomy_' ) ) {
			$taxonomy = str_replace( 'taxonomy_', '', $this->_loop );

			if ( 0 === $post_id && is_archive() ) {
				# On archive pages, fetch the queried object id
				$term_id = get_queried_object_id();
			} else {
				if ( 'category' === $taxonomy ) {
					$term_id = Helper_Model::instance()->get_primary_category( $post_id );
				} else {
					$term_id = Helper_Model::instance()->get_primary_term( $taxonomy, $post_id );
				}
			}

			if ( $term_id > 0 ) {
				$items   = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
				$items   = array_reverse( $items );
				$items[] = $term_id;

				array_walk( $items, function ( &$term_id, $key, $taxonomy ) {
					$term = get_term( $term_id, $taxonomy );

					if ( ! $term instanceof \WP_Term ) {
						$term_id = null;

						return null;
					}

					$term_id = $term;
				}, $taxonomy );

				$items = array_filter( $items );
				$items = array_combine( wp_list_pluck( $items, 'term_id' ), $items );
			}

		}

		/**
		 * Loop Items filer.
		 *
		 * Add/Change loop items.
		 *
		 * @hook  wpbuddy/rich_snippets/rich_snippet/loop/items
		 *
		 * @param {array}        $items
		 * @param {Rich_Snippet} $this
		 * @param {int}          $post_id
		 *
		 * @returns {array} They key should be the item ID. The value the object of the item.
		 * @since 2.8.0
		 */
		return apply_filters( 'wpbuddy/rich_snippets/rich_snippet/loop/items', $items, $this, $post_id );
	}


	/**
	 * Resets the loop to NULL.
	 *
	 * @return void
	 * @since 2.8.0
	 */
	public function reset_loop() {
		$this->_loop = null;
	}


	/**
	 * Checks if all properties are empty.
	 *
	 * @return bool
	 *
	 * @since 2.12.1
	 */
	public function is_empty() {
		if ( ! $this->_is_ready ) {
			return false;
		}

		$object_vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$object_props = array_diff_key( $object_vars, $class_vars );

		unset( $object_props['@context'] );
		unset( $object_props['@type'] );

		foreach ( array_keys( $object_props ) as $k ) {
			if ( ! $this->is_property_empty( $k ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Checks if a property is empty.
	 *
	 * @param string $property_name
	 *
	 * @return bool
	 * @since 2.12.1
	 *
	 */
	public function is_property_empty( string $property_name ) {

		if ( ! isset( $this->{$property_name} ) ) {
			return null;
		}

		if ( ! ( is_int( $this->{$property_name} ) || is_float( $this->{$property_name} ) ) && empty( $this->{$property_name} ) ) {

			# do not strip number entered by a user (recognizable by a string)
			if ( ! ctype_digit( $this->{$property_name} ) ) {
				return true;
			}
		}

		if ( $this->{$property_name} instanceof Rich_Snippet && $this->{$property_name}->is_empty() ) {
			return true;
		}

		if ( is_array( $this->{$property_name} ) ) {
			foreach ( $this->{$property_name} as $k => $sub_prop ) {
				if ( ! $sub_prop instanceof Rich_Snippet ) {
					continue;
				}

				if ( $sub_prop->is_empty() ) {
					unset( $this->{$property_name}[ $k ] );
				}
			}
		}

		return false;
	}


	/**
	 * Overwrites properties.
	 *
	 * @param array $properties
	 *
	 * @since      2.14.0
	 *
	 * @dperecated 2.14.3
	 */
	public function overwrite_properties( $properties ) {
		foreach ( $properties as $property ) {

			$name = $this->get_property_name_by_uid( $property['prop_id'] );

			if ( false === $name ) {
				continue;
			}

			if ( ! ( isset( $this->{$name}['overridable'] ) && $this->{$name}['overridable'] ) ) {
				continue;
			}

			if ( isset( $this->{$name}['overridable_multiple'] ) && $this->{$name}['overridable_multiple'] && isset( $this->{$name}['overwritten'] ) && $this->{$name}['overwritten'] ) {
				# if it's overwritten, we need to create a new prop.
				$copy                     = $this->{$name};
				$copy[0]                  = 'overwrite'; # this makes sure that overwritten properties do not get overwritten again later in the "get_value" function
				$copy[1]                  = $property['value'];
				$copy['list_item']        = true;
				$copy['original_prop_id'] = $property['prop_id'];
				$this->set_prop( $this->normalize_property_name( $name ), $copy );
				unset( $copy );
			} else {
				$this->{$name}[0]             = 'overwrite'; # this makes sure that overwritten properties do not get overwritten again later in the "get_value" function
				$this->{$name}[1]             = $property['value'];
				$this->{$name}['overwritten'] = true;
			}


		}

	}


	/**
	 * Returns all snippet IDs from properties that are Rich_Snippets themselves.
	 *
	 * @return array
	 * @since 2.14.0
	 */
	public function get_sub_snippet_ids() {
		$object_vars = get_object_vars( $this );

		$class_vars = get_class_vars( __CLASS__ );

		$object_props = array_keys( array_diff_key( $object_vars, $class_vars ) );

		$d = [];

		foreach ( $object_props as $name ) {
			if ( $this->{$name}[1] instanceof Rich_Snippet ) {
				$d[ $name ] = $this->{$name}[1]->id;
			}
		}

		return $d;
	}


	public function get_sub_snippet_ids_deep() {
		$sub_snippets_ids = $this->get_sub_snippet_ids();

		$all = [];

		foreach ( $sub_snippets_ids as $prop_name_and_id => $sub_snippet_id ) {
			$more = $this->{$prop_name_and_id}[1]->get_sub_snippet_ids_deep();
			$all  = array_merge( $all, $more );
		}

		return array_merge( $sub_snippets_ids, $all );
	}


	/**
	 * Duplicates properties.
	 *
	 * @param string $prop_name
	 * @param array $properties
	 * @param string $snippet_id
	 *
	 * @since      2.14.0
	 *
	 * @deprecated 2.14.3
	 */
	public function create_duplicate( $prop_name, $properties, $snippet_id ) {
		if ( ! isset( $this->{$prop_name} ) ) {
			return;
		}

		# make sure this is not copied by reference
		$duplicate = unserialize( serialize( $this->{$prop_name} ) );

		if ( ! $duplicate[1] instanceof Rich_Snippet ) {
			return;
		}

		$duplicate[1]->id = $snippet_id;
		$duplicate[1]->overwrite_properties( $properties );

		$name = $this->normalize_property_name( $prop_name );

		$this->set_prop( $name, $duplicate );
	}


	/**
	 * Returns the main snippet (if in a loop).
	 *
	 * @return string|null
	 * @since 2.14.27
	 */
	public function get_main_snippet_id() {
		return $this->_main_id;
	}
}