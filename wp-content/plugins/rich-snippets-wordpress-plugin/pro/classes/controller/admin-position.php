<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Cache_Model;
use wpbuddy\rich_snippets\View;
use function wpbuddy\rich_snippets\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_Position_Controller
 *
 * Starts up all the admin things needed to positioning snippets.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class Admin_Position_Controller {

	/**
	 * The instance.
	 *
	 * @var Admin_Position_Controller
	 *
	 * @since 2.0.0
	 */
	protected static $_instance = null;


	/**
	 * If this instance has been initialized already.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	protected $_initialized = false;


	/**
	 * The current rule row number.
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	protected $_current_rule_row_no = 0;


	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return   Admin_Position_Controller
	 *
	 * @since 2.0.0
	 */
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}


	/**
	 * Magic function for cloning.
	 *
	 * Disallow cloning as this is a singleton class.
	 *
	 * @since 2.0.0
	 */
	protected function __clone() {
	}


	/**
	 * Magic method for setting upt the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {
	}


	/**
	 * Init metaboxes.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		if ( $this->_initialized ) {
			return;
		}

		add_action( 'wpbuddy/rich_snippets/schemas/metaboxes', array( self::$_instance, 'add_metaboxes' ) );

		add_action( 'admin_enqueue_scripts', array( self::$_instance, 'enqueue_scripts' ) );

		add_action( 'wpbuddy/rich_snippets/global_schemas/save', array( self::$_instance, 'save_positions' ) );

		$this->_initialized = true;
	}


	/**
	 * Enqueue Scripts
	 *
	 * @param string $hook_suffix
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts( $hook_suffix ) {

		if ( ! ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) ) {
			return;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'wpb-rs-global' !== $screen->id ) {
			return;
		}

		wp_enqueue_style(
			'wpb-rs-admin-position',
			plugins_url( 'css/pro/admin-position.css', rich_snippets()->get_plugin_file() ),
			array( 'wpb-rs-admin-snippets' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/pro/admin-position.css' )
		);

		wp_enqueue_script(
			'wpb-rs-admin-position',
			plugins_url( 'js/pro/admin-position.js', rich_snippets()->get_plugin_file() ),
			array( 'wpb-rs-admin-snippets' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/pro/admin-position.js' )
		);

		$args = call_user_func( function () {

			$o           = new \stdClass();
			$o->nonce    = wp_create_nonce( 'wp_rest' );
			$o->rest_url = untrailingslashit( rest_url( 'wpbuddy/rich_snippets/v1' ) );

			return $o;
		} );

		wp_add_inline_script( 'wpb-rs-admin-position', "var WPB_RS_POS = " . \json_encode( $args ) . ";", 'before' );
	}


	/**
	 * Adds the metaboxes.
	 *
	 * @since 2.0.0
	 */
	public function add_metaboxes() {

		# the position rules metabox
		add_meta_box(
			'wp-rs-mb-position',
			_x( 'Position', 'metabox title', 'rich-snippets-schema' ),
			array( self::$_instance, 'render_position_meta_box' ),
			'wpb-rs-global',
			'advanced',
			'high'
		);
	}

	/**
	 * Renders the meta box.
	 *
	 * @param \WP_Post $post
	 * @param array $metabox
	 *
	 * @since 2.0.0
	 *
	 */
	public function render_position_meta_box( $post, $metabox ) {

		View::admin_position_metabox( $post );
	}


	/**
	 * Returns an array of possible page rules.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function get_params() {

		$params = array(
			array(
				'label'  => _x( 'Built-in Parameters', 'rich-snippets-schema' ),
				'params' => array(
					'post_type'     => __( 'Post Type', 'rich-snippets-schema' ),
					'post'          => __( 'Post, Page, Custom Post Type' ),
					'post_template' => __( 'Post Template', 'rich-snippets-schema' ),
					'post_status'   => __( 'Post Status', 'rich-snippets-schema' ),
					'post_format'   => __( 'Post Format', 'rich-snippets-schema' ),
					'post_category' => __( 'Post Category', 'rich-snippets-schema' ),
					'child_terms'   => __( 'Child Terms of', 'rich-snippets-schema' ),
					'post_taxonomy' => __( 'Post Taxonomy', 'rich-snippets-schema' ),
					'page_template' => __( 'Page Template', 'rich-snippets-schema' ),
					'page_type'     => __( 'Page Type', 'rich-snippets-schema' ),
					'page_parent'   => __( 'Page Parent', 'rich-snippets-schema' ),
				),
			),
		);


		/**
		 * Custom page rules.
		 *
		 * Returns custom page rules.
		 *
		 * @hook  wpbuddy/rich_snippets/position/custom_params
		 *
		 * @param {array} $array Custom params.
		 *
		 * @returns {array} Custom params.
		 *
		 * @since 2.0.0
		 */
		$custom_params = apply_filters( 'wpbuddy/rich_snippets/position/custom_params', array() );

		if ( count( $custom_params ) > 0 ) {
			$prams[] = array(
				'label'  => _x( 'Custom Parameters', 'Custom page rules for selecting a rule.', 'rich-snippets-schema' ),
				'params' => $custom_params,
			);
		}

		/**
		 * Page rule filter.
		 *
		 * Allows to customize possible rules.
		 *
		 * @hook  wpbuddy/rich_snippets/position/params
		 *
		 * @param {array} $params Params.
		 *
		 * @returns {array} The params.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'wpbuddy/rich_snippets/position/params', $params );
	}


	/**
	 * Returns a list of operators.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function get_operators() {

		$operators = array(
			'==' => __( 'is equal to', 'rich-snippets-schema' ),
			'!=' => __( 'is not equal to', 'rich-snippets-schema' ),
		);

		/**
		 * Position operators filter.
		 *
		 * Allows to add or customize the operators used in the position metabox.
		 *
		 * @hook  wpbuddy/rich_snippets/position/operators
		 *
		 * @param {array} $operators A list of operators.
		 *
		 * @returns {array} The operator list.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'wpbuddy/rich_snippets/position/operators', $operators );
	}


	/**
	 * Prints the param dropdown.
	 *
	 * @param null|Position_Rule $rule
	 *
	 * @since 2.0.0
	 *
	 */
	public function print_param_select( $rule = null ) {

		?>
        <select name="wpb_rs_position_rule[%rule_group%][%rule%][param]">
			<?php
			foreach ( $this->get_params() as $optgroup ) {
				printf( '<optgroup label="%s">', esc_html( $optgroup['label'] ) );

				foreach ( $optgroup['params'] as $param_value => $param_label ) {
					printf(
						'<option %s value="%s">%s</option>',
						( $rule instanceof Position_Rule ) ?
							selected( $rule->param, $param_value, false ) : '',
						esc_attr( $param_value ),
						esc_html( $param_label )
					);
				}

				echo '</optgroup>';
			}
			?>
        </select>
		<?php
	}


	/**
	 * Prints the operator dropdown.
	 *
	 * @param null|Position_Rule $rule
	 *
	 * @since 2.0.0
	 *
	 */
	public function print_operator_select( $rule = null ) {
		?>
        <select name="wpb_rs_position_rule[%rule_group%][%rule%][operator]">
			<?php
			foreach ( $this->get_operators() as $operator => $operator_label ) {
				printf(
					'<option %s value="%s">%s</option>',
					( $rule instanceof Position_Rule ) ?
						selected( $rule->operator, $operator, false ) : '',
					esc_attr( $operator ),
					esc_html( $operator_label )
				);
			}
			?>
        </select>
		<?php
	}


	/**
	 * Returns possible param selections for a rule.
	 *
	 * @param Position_Rule $rule
	 *
	 * @return array
	 * @since 2.14.0
	 *
	 */
	public function get_value_select_options( $rule = null ) {
		$rule_param = $rule->param ?? 'post_type';

		$values = array();

		switch ( $rule_param ) {
			case 'post_template':
				$values        = array( 'default' => __( 'Default template', 'rich-snippets-schema' ) );
				$all_templates = wp_get_theme()->get_post_templates();
				foreach ( $all_templates as $post_type => $templates ) {
					$values[ $post_type ]['label']  = call_user_func( function ( $pt ) {

						$post_type_obj = get_post_type_object( $pt );
						if ( ! $post_type_obj instanceof \WP_Post_Type ) {
							return $pt;
						}

						$labels = get_post_type_labels( $post_type_obj );

						return $labels->singular_name;
					}, $post_type );
					$values[ $post_type ]['values'] = call_user_func( function ( $tpls, $pt ) {

						$vals = array();
						foreach ( $tpls as $file => $name ) {
							$key          = sprintf( '%s:%s', $pt, $file );
							$vals[ $key ] = $name;
						}

						return $vals;
					}, $templates, $post_type );
				}
				break;
			case 'page_template':
				$values        = array( 'default' => __( 'Default template', 'rich-snippets-schema' ) );
				$all_templates = wp_get_theme()->get_post_templates();

				if ( isset( $all_templates['page'] ) ) {
					$values = array_merge( $values, $all_templates['page'] );
				}
				break;
			case 'post_status':
				global $wp_post_statuses;

				if ( ! empty( $wp_post_statuses ) ) {
					foreach ( $wp_post_statuses as $status ) {
						$values[ $status->name ] = $status->label;
					}

				}
				break;
			case 'post_format':
				$values = get_post_format_strings();
				break;
			case 'post_category':
				$values = get_categories( array(
					'hide_empty' => false,
				) );

				$values = wp_list_pluck( $values, 'cat_name', 'cat_ID' );
				array_walk( $values, function ( &$value, $key ) {
					$value = sprintf( '%s (%d)', $value, $key );
				} );
				break;
			case 'child_terms':
			case 'post_taxonomy':
				$taxonomies = get_taxonomies( false, 'objects' );
				$ignore     = array( 'nav_menu', 'link_category' );
				$values     = array();

				/**
				 * @var \WP_Taxonomy $taxonomy
				 */
				foreach ( $taxonomies as $taxonomy ) {
					if ( in_array( $taxonomy->name, $ignore ) ) {
						continue;
					}

					$values[ $taxonomy->name ] = array(
						'label' => sprintf(
							'%s (%s)',
							$taxonomy->label,
							$taxonomy->name
						),
					);

					$terms = get_terms( array(
						'taxonomy'   => $taxonomy->name,
						'hide_empty' => false,
					) );

					if ( is_wp_error( $terms ) ) {
						continue;
					}

					if ( empty( $terms ) ) {
						continue;
					}

					if ( $taxonomy->hierarchical ) {
						$terms = _get_term_children( 0, $terms, $taxonomy->name );
					}

					foreach ( $terms as $term ) {
						$k = sprintf( '%s:%d', $taxonomy->name, $term->term_id );

						$values[ $taxonomy->name ]['values'][ $k ] = $this->get_term_title( $term );
					}
				}
				break;
			case 'post':
			case 'page_parent':
				# Load at least the selected value
				if ( ! empty( $rule->value ) ) {
					$values[ $rule->value ] = sprintf(
						'%s (%s, %d)',
						get_the_title( $rule->value ),
						get_post_type( $rule->value ),
						$rule->value
					);
				}

				break;
			case 'page_type':
				$values = array(
					'all'        => __( "Everything (globally active)", 'rich-snippets-schema' ),
					'front_page' => __( "Front Page", 'rich-snippets-schema' ),
					'posts_page' => __( "Posts Page", 'rich-snippets-schema' ),
					'top_level'  => __( "Top Level Page (has no parent)", 'rich-snippets-schema' ),
					'parent'     => __( "Parent Page (has children)", 'rich-snippets-schema' ),
					'child'      => __( "Child Page (has parent)", 'rich-snippets-schema' ),
					'search'     => __( "Search Results Page", 'rich-snippets-schema' ),
					'archive'    => __( "All Archive Pages", 'rich-snippets-schema' ),
				);

				$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );

				foreach ( $taxonomies as $taxonomy ) {
					$values[ 'archive_' . $taxonomy->name ] = sprintf(
						__( 'Archive page: %s', 'rich-snippets-schema' ),
						esc_html( $taxonomy->label )
					);
				}

				break;
			case 'post_type':
			default:

				/**
				 * Value-Select Post-type filter.
				 *
				 * Allows to change the post type arguments when fetching post types in the value select.
				 *
				 * @hook  wpbuddy/rich_snippets/position/values/post_type_args
				 *
				 * @param array $post_type_args
				 *
				 * @return array Post type arguments.
				 *
				 * @since 2.14.0
				 */
				$post_type_args = apply_filters(
					'wpbuddy/rich_snippets/position/values/post_type_args',
					array( 'publicly_queryable' => true, )
				);

				$values = get_post_types(
					$post_type_args,
					'objects'
				);

				$values = wp_list_pluck( $values, 'label', 'name' );

				/**
				 * Add page to the list
				 */
				$post_type = get_post_type_object( 'page' );
				if ( $post_type instanceof \WP_Post_Type ) {
					$values['page'] = $post_type->label;
				}
		}


		return $values;

	}

	/**
	 * Prints the value dropdown.
	 *
	 * @param null|Position_Rule $rule
	 *
	 * @since 2.0.0
	 *
	 */
	public function print_value_select( $rule = null ) {

		$rule_param = $rule->param ?? 'post_type';
		$rule_value = $rule->value ?? null;

		$make_select2 = 'page_parent' === $rule_param || 'post' === $rule_param;
		$values       = $this->get_value_select_options( $rule );

		/**
		 * Position value filter.
		 *
		 * This filter can be used to filter position values.
		 *
		 * @hook  wpbuddy/rich_snippets/position/values
		 *
		 * @param {array}              $values The current values.
		 * @param {null|Position_Rule} $rule   The current rule.
		 *
		 * @returns {array}
		 *
		 * @since 2.0.0
		 */
		$values = apply_filters( 'wpbuddy/rich_snippets/position/values', $values, $rule );

		?>
        <select name="wpb_rs_position_rule[%rule_group%][%rule%][value]"
                data-make_select2="<?php echo absint( $make_select2 ); ?>"
                data-param="<?php echo esc_attr( $rule_param ); ?>"
                id="<?php echo esc_attr( uniqid() ); ?>">
			<?php
			foreach ( $values as $value => $label ) {

				if ( is_array( $label ) ) {
					$sub_values = $label;

					if ( ! isset( $sub_values['label'] ) ) {
						continue;
					}

					if ( ! isset( $sub_values['values'] ) ) {
						continue;
					}

					printf(
						'<optgroup label="%s">',
						esc_attr( $sub_values['label'] )
					);

					foreach ( $sub_values['values'] as $sub_value => $sub_label ) {
						printf(
							'<option %s value="%s">%s</option>',
							selected( $rule_value, $sub_value, false ),
							esc_attr( $sub_value ),
							esc_html( $sub_label )
						);
					}

					print( '</optgroup>' );

					continue;
				}

				printf(
					'<option %s value="%s">%s</option>',
					selected( $rule_value, $value, false ),
					esc_attr( $value ),
					esc_html( $label )
				);
			}
			?>
        </select>
		<?php
	}


	/**
	 * Prints a rule row.
	 *
	 * @param null|Position_Rule $rule
	 *
	 * @since 2.0.0
	 *
	 */
	public function print_rule_row( $rule = null ) {

		?>
        <tr class="wpb-rs-rule" data-rule_row="<?php echo esc_attr( $this->_current_rule_row_no ); ?>">
            <td class="wpb-rs-param">
				<?php $this->print_param_select( $rule ); ?>
            </td>
            <td class="wpb-rs-operator">
				<?php $this->print_operator_select( $rule ); ?>
            </td>
            <td class="wpb-rs-value">
				<?php $this->print_value_select( $rule ); ?>
            </td>
            <td class="wpb-rs-rule-action wpb-rs-rule-add">
                <a href="#" class="button"><?php
					echo esc_html_x( 'and', 'Local AND when adding a new position rule', 'rich-snippets-schema' );
					?></a>
            </td>
            <td class="wpb-rs-rule-action wpb-rs-rule-remove">
                <a href="#" class="button"><span class="dashicons dashicons-no-alt"></span></a>
            </td>
        </tr>
		<?php
		$this->_current_rule_row_no ++;
	}


	/**
	 * Saves all position rules.
	 *
	 * @param int $post_id
	 *
	 * @since 2.0.0
	 *
	 */
	public function save_positions( $post_id ) {

		if ( ! isset( $_POST['wpb_rs_position_rule'] ) ) {
			return;
		}

		if ( ! is_array( $_POST['wpb_rs_position_rule'] ) ) {
			return;
		}

		# clear rule cache
		if ( 'wpb-rs-global' === get_post_type( $post_id ) ) {
			Cache_Model::clear_singular_rule( $post_id );
		}

		$position_rules = $_POST['wpb_rs_position_rule'];

		$ruleset = Rules_Model::convert_to_ruleset( $position_rules );

		Rules_Model::update_ruleset( $post_id, $ruleset );
	}


	/**
	 * Prints a rule group break
	 *
	 * @since 2.0.0
	 */
	public function print_group_break() {

		?>
        <tr class="wpb-rs-rule-group-break">
            <td colspan="4">
				<?php echo esc_html_x( 'or', 'logical OR when defining a ruleset', 'rich-snippets-schema' ); ?>
            </td>
            <td class="wpb-rs-rule-action wpb-rs-rulegroup-remove">
                <a href="#" class="button"><span class="dashicons dashicons-no-alt"></span></a>
            </td>
        </tr>
		<?php
	}


	/**
	 * Returns the term title for a select box.
	 *
	 * @param \WP_Term $term
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	private function get_term_title( $term ) {

		$title = $term->name;

		if ( empty( $title ) ) {
			$title = _x( '(no title)', 'term title', 'rich-snippets-schema' );
		}

		if ( is_taxonomy_hierarchical( $term->taxonomy ) ) {
			$ancestors = get_ancestors( $term->term_id, $term->taxonomy );
			$title     = str_repeat( '- ', count( $ancestors ) ) . $title;
		}

		return $title;

	}

}
