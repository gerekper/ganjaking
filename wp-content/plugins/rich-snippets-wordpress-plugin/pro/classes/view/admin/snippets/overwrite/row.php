<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Rich_Snippet;
use wpbuddy\rich_snippets\Schema_Property;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var Schema_Property $prop
 * @var Rich_Snippet $snippet
 * @var \WP_Post $post
 */
$prop               = $this->arguments[0];
$snippet            = $this->arguments[1];
$post               = $this->arguments[2];
$overwrite_post_id  = $this->arguments[3];
$main_snippet_id    = $this->arguments[4];
$parent_snippet_id  = $this->arguments[5];
$parent_property_id = $this->arguments[6];
$input_name         = $this->arguments[7];

/**
 * @var string $subfield_select
 * @var mixed|Rich_Snippet $value
 */
$subfield_select = isset( $prop->value[0] ) ? $prop->value[0] : '';
$value           = isset( $prop->value[1] ) ? $prop->value[1] : $prop->value;

$input_name .= $prop->label;

?>

<tr class="wpb-rs-schema-property-row <?php echo ! $prop->overridable_multiple ? '' : 'overridable-multiple'; ?>"
    data-prop_name="<?php echo esc_attr( $prop->overridable_input_name ); ?>">
    <td class="wpb-rs-schema-property-name">
		<?php echo esc_html( Helper_Model::instance()->remove_schema_url( $prop->id ) ); ?>
    </td>
    <td>
        <div class="wpb-rs-schema-property-field">
            <div class="wpb-rs-schema-property-field-intro">
				<?php
				$html_id = uniqid();

				/**
				 * Overwrite Property Field Action Before.
				 *
				 * Allows to output data before a property field is printed out on the overwrite screen.
				 *
				 * @hook  wpbuddy/rich_snippets/overwrite/property/field/before
				 *
				 * @param {Schema_Property} $prop
				 * @param {Rich_Snippet} $snippet
				 * @param {WP_Post} $post
				 *
				 * @since 2.0.0
				 */
				do_action(
					'wpbuddy/rich_snippets/overwrite/property/field/before',
					$prop,
					$snippet,
					$post
				);

				if ( false !== stripos( $subfield_select, 'global_snippet_' ) ):
					View::admin_snippets_overwrite_warnings_reference( str_replace( 'global_snippet_', '', $subfield_select ) );
				else:

				/**
				 * Render Property HTML field.
				 *
				 * Allows third party plugins to display html code when a property field is printed out.
				 *
				 * @hook  wpbuddy/rich_snippets/rest/property/html/fields
				 *
				 * @param {array} $args Arguments with the following keys: property, current_type, html_id, property_id, input_name, selected, value, screen
				 *
				 * @since 2.0.0
				 */
				do_action( 'wpbuddy/rich_snippets/rest/property/html/fields', array(
					'property'     => $prop,
					'current_type' => $snippet->get_type(),
					'html_id'      => $html_id,
					'property_id'  => $prop->uid,
					'input_name'   => $prop->overridable_input_name,
					'selected'     => $subfield_select,
					'value'        => $value,
					'screen'       => 'overwrite',
				) );

				/**
				 * Overwrite Property Field Action After.
				 *
				 * Allows to output data after a property fieldwas printed out on the overwrite screen.
				 *
				 * @hook  wpbuddy/rich_snippets/overwrite/property/field/after
				 *
				 * @param {Schema_Property} $prop
				 * @param {Rich_Snippet} $snippet
				 * @param {WP_Post} $post
				 *
				 * @since 2.0.0
				 */
				do_action(
					'wpbuddy/rich_snippets/overwrite/property/field/after',
					$prop,
					$snippet,
					$post
				);

				?>
                <p class="description wpb-rs-schema-property-comment">
					<?php
					echo esc_html( strip_tags( $prop->comment ) );

					/**
					 * Overwrite Property Comment Action.
					 *
					 * Allows third party plugins to add data to the comment section of a property on the overwrite screen.
					 *
					 * @hook  wpbuddy/rich_snippets/overwrite/property/comment
					 *
					 * @param {Schema_Property} $prop
					 * @param {Rich_Snippet} $snippet
					 *
					 * @since 2.0.0
					 */
					do_action( 'wpbuddy/rich_snippets/overwrite/property/comment', $prop, $snippet );
					?>
                </p>
            </div>
            <div class="wpb-rs-schema-property-subclass-properties">
				<?php
				if ( $value instanceof Rich_Snippet ) {
					echo Admin_Snippets_Overwrite_Controller::instance()->get_property_table(
						$value,
						$post,
						$overwrite_post_id,
						$main_snippet_id,
						$snippet->id,
						'',
						$prop->overridable_input_name . '_'
					);
				}
				?>
            </div>
			<?php
			endif;

			/**
			 * After Overwrite Property Action.
			 *
			 * Allows plugins to add data after a property has printed to the overwrite screen.
			 *
			 * @hook  wpbuddy/rich_snippets/overwrite/property/after
			 *
			 * @param {Schema_Property} $prop
			 * @param {Rich_Snippet} $snippet
			 * @param {WP_Post} $post
			 *
			 * @since 2.0.0
			 */
			do_action(
				'wpbuddy/rich_snippets/overwrite/property/after',
				$prop,
				$snippet,
				$post
			);
			?>
        </div>
    </td>
    <td class="wpb-rs-schema-property-options">
		<?php
		if ( $prop->overridable_multiple ) {
			echo '<a class="wpb-rs-delete-property" href="#"><span class="dashicons dashicons-trash"></span></a>';
			echo '<a class="wpb-rs-duplicate-property" href="#"><span class="dashicons dashicons-plus"></span></a>';
		}
		?>
    </td>
</tr>
