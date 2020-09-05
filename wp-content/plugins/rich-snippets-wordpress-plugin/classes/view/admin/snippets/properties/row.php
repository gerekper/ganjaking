<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var Schema_Property $prop
 * @var Rich_Snippet    $snippet
 * @var \WP_Post        $post
 */
$prop    = $this->arguments[0];
$snippet = $this->arguments[1];
$post    = $this->arguments[2];

/**
 * @var string             $subfield_select
 * @var mixed|Rich_Snippet $value
 */
$subfield_select = isset( $prop->value[0] )
	? $prop->value[0]
	: ( Fields_Model::is_field_selectable( $prop, 'textfield' ) ? 'textfield' : '' );

$value = isset( $prop->value[1] ) ? $prop->value[1] : $prop->value;

$sub_snippet = ( $value instanceof Rich_Snippet ) ? $value->id : '';

$input_name = sprintf(
	'snippets[%s][properties][%s]',
	esc_attr( $snippet->id ),
	$prop->uid
);

?>

<tr class="wpb-rs-schema-property-row">
    <td class="wpb-rs-schema-property-name">
		<?php echo esc_html( Helper_Model::instance()->remove_schema_url( $prop->id ) ); ?>
    </td>
    <td class="wpb-rs-schema-property-type">
		<span class="wpb-rs-schema-property-type-selected"><?php
			echo Helper_Model::instance()->get_field_type_label( $subfield_select );
			?></span>
        <div class="wpb-rs-schema-property-field-intro-actions">
			<?php
			/**
			 * Snippet Property Action filter.
			 *
			 * Allows to filter property actions.
			 *
			 * @hook   wpbuddy/rich_snippets/rest/property/html/actions
			 *
			 * @param  {array} $actions Actions to include.
			 *
			 * @return {array} All the actions.
			 *
			 * @since  2.0.0
			 */
			$actions = apply_filters( 'wpbuddy/rich_snippets/rest/property/html/actions', array(
				'edit'   => __( 'Edit' ),
				'delete' => __( 'Delete' ),
			), $prop, $snippet );
			?>
            <ul class="wpb-rs-schema-property-actions">
				<?php
				array_walk( $actions, function ( &$action, $class ) {

					$action = sprintf(
						'<li><span class="%s"><a href="#">%s</a></span></li>',
						esc_attr( $class ),
						esc_html( $action )
					);
				} );
				echo implode( '', $actions );

				if ( 'wpb-rs-global' === $post->post_type ):
					$html_id_overridable = uniqid( 'overridable' );
					$html_id_multiple = uniqid( 'overridable_multiple' );
					?>
                    <li>
                        <label class="wpb-rs-schema-property-actions-overridable wpb-rs-nowrap"
                               title="<?php echo esc_attr__( 'Allow to overwrite the value.', 'rich-snippets-schema' ); ?>"
                               for="<?php echo esc_attr( $html_id_overridable ); ?>">
                            <input type="checkbox" value="1"
                                   id="<?php echo esc_attr( $html_id_overridable ); ?>"
                                   name="<?php echo esc_attr( $input_name ); ?>[overridable]"
								<?php checked( $prop->overridable ); ?> />
							<?php _e( 'Overridable', 'rich-snippets-schema' ); ?>
                        </label>
                    </li>

                    <li>
                        <label title="<?php echo esc_attr__( 'This property can be used multiple times.', 'rich-snippets-schema' ); ?>"
                               for="<?php echo esc_attr( $html_id_multiple ); ?>" class="wpb-rs-nowrap">
                            <input type="checkbox" value="1" id="<?php echo esc_attr( $html_id_multiple ); ?>"
                                   name="<?php echo esc_attr( $input_name ); ?>[overridable_multiple]"
								<?php checked( $prop->overridable_multiple ); ?> />
							<?php _e( 'List', 'rich-snippets-schema' ); ?>
                        </label>
                    </li>

				<?php endif; ?>
            </ul>
        </div>
    </td>
</tr>
<tr class="wpb-rs-schema-property-field">
    <td colspan="2">
		<?php
		printf(
			'<input type="hidden" name="%s[id]" value="%s" />',
			esc_attr( $input_name ),
			esc_attr( $prop->id )
		);

		printf(
			'<input type="hidden" name="%s[ref]" value="%s" class="wpb-rs-schema-property-ref" />',
			esc_attr( $input_name ),
			esc_attr( $sub_snippet )
		);

		$html_id = uniqid();

		?>
        <table class="wpb-rs-schema-property-field-options widefat">
            <tbody>
            <tr>
                <td><?php _e( 'Description', 'rich-snippets-schema' ); ?></td>
                <td>
                    <p class="description wpb-rs-schema-property-comment">
						<?php
						echo esc_html( strip_tags( $prop->comment ) );

						/**
						 * Property Comment Action.
						 *
						 * Allows plugins to add data to the property comment section.
						 *
						 * @hook  wpbuddy/rich_snippets/rest/property/html/comment
						 *
						 * @param {Schema_Property} $prop
						 * @param {Rich_Snippet} $snippet
						 *
						 * @since 2.0.0
						 */
						do_action( 'wpbuddy/rich_snippets/rest/property/html/comment', $prop, $snippet );
						?>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="wpb-rs-schema-property-field-select-<?php echo esc_attr( $html_id ) ?>">
						<?php _e( 'Field Type', 'rich-snippets-schema' ); ?>
                    </label>
                </td>
                <td>
                    <select class="wpb-rs-schema-property-field-subfield-select"
                            name="<?php echo $input_name; ?>[subfield_select]"
                            id="wpb-rs-schema-property-field-select-<?php echo esc_attr( $html_id ); ?>">
                        <option value=""><?php _ex( '[None]', 'String to show that nothing is selected on a subfield.', 'rich-snippets-schema' ) ?></option>
						<?php

						/**
						 * Internal values
						 */
						$internal_select_options = Fields_Model::get_internal_subselect_options(
							$prop,
							$snippet->get_type(),
							$subfield_select
						);

						if ( count( $internal_select_options ) > 0 ) {
							printf(
								'<optgroup label="%s">%s</optgroup>',
								esc_attr( __( 'Internal values', 'rich-snippets-schema' ) ),
								implode( '', $internal_select_options )
							);
						}


						/**
						 * Related types
						 */
						if ( is_array( $prop->range_includes ) ) {
							$related_select_options = Fields_Model::get_related_subselect_options(
								$prop,
								$snippet->get_type(),
								$subfield_select
							);

							if ( count( $related_select_options ) > 0 ) {
								printf(
									'<optgroup label="%s">%s</optgroup>',
									esc_attr( __( 'Related types', 'rich-snippets-schema' ) ),
									implode( '', $related_select_options )
								);
							}
						}


						/**
						 * Descendant types
						 */
						if ( is_array( $prop->range_includes ) ) {
							$descendants_select_options = Fields_Model::get_descendants_types_subselect_options(
								$prop,
								$snippet->get_type(),
								$subfield_select
							);

							if ( count( $descendants_select_options ) > 0 ) {
								printf(
									'<optgroup label="%s">%s</optgroup>',
									esc_attr( __( 'Descendant types', 'rich-snippets-schema' ) ),
									implode( '', $descendants_select_options )
								);
							}
						}


						/**
						 * 'Reference' options
						 */
						?>
                        <optgroup label="<?php esc_attr_e( 'Reference to', 'rich-snippets-schema' ) ?>">
							<?php
							$internal_reference_options = Fields_Model::get_reference_subselect_options(
								$prop,
								$snippet->get_type(),
								$subfield_select
							);

							echo implode( '', $internal_reference_options );
							?>
                        </optgroup>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php _e( 'Field Value', 'rich-snippets-schema' ); ?></td>
                <td>
                    <div class="wpb-rs-schema-property-subclass-properties">
						<?php
						if ( $value instanceof Rich_Snippet ) {
							echo Admin_Snippets_Controller::instance()->get_property_table( $value, array(), $post );
						}
						?>
                    </div>
                    <div class="wpb-rs-schema-property-extra-fields">
						<?php
						/**
						 * Property fields hook.
						 *
						 * Allows to display additional fields.
                         *
                         * @hook wpbuddy/rich_snippets/rest/property/html/fields
						 *
						 * @param {array}           $field_args
						 * @param {Schema_Property} $field_args.property
						 * @param {string}          $field_args.current_type
						 * @param {string}          $field_args.html_id
						 * @param {string }         $field_args.property_id
						 * @param {string}          $field_args.input_name
						 * @param {string}          $field_args.selected The selected item.
						 * @param {string}          $field_args.value    The current value.
						 *
						 * @since 2.0.0
						 */
						do_action( 'wpbuddy/rich_snippets/rest/property/html/fields', array(
							'property'     => $prop,
							'current_type' => $snippet->get_type(),
							'html_id'      => $html_id,
							'property_id'  => $prop->uid,
							'input_name'   => $input_name,
							'selected'     => $subfield_select,
							'value'        => $value,
							'screen'       => 'edit',
						) );
						?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
