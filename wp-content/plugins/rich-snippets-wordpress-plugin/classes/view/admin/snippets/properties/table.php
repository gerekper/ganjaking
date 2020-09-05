<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var string[] $html The rendered properties.
 * @var Rich_Snippet $snippet The current snippet.
 * @var \WP_Post $post
 */
$html    = $this->arguments[0];
$snippet = $this->arguments[1];
$post    = $this->arguments[2];

$html_id = uniqid( 'wpb_rs_schema_new_property_' );

?>

<table class="widefat wpb-rs-property-list" data-snippet_id="<?php echo esc_attr( $snippet->id ); ?>"
       data-schema_type="<?php echo esc_attr( $snippet->get_type() ); ?>">
    <thead>
    <tr>
        <th colspan="2">
			<?php
			echo sprintf(
				__( 'Property list for %s', 'rich-snippets-schema' ),
				sprintf(
					'<a href="%s" target="_blank">%s <span class="dashicons dashicons-editor-help"></span></a>',
					esc_url( $snippet->get_type() ),
					esc_html( $snippet->get_type() )
				)
			);

			printf(
				'<input type="hidden" name="snippets[%s][id]" value="%s"/>',
				$snippet->id,
				esc_attr( $snippet->get_type() )
			);

			?>
            <a class="wpb-rs-property-expander" href="#">
                <span class="dashicons dashicons-arrow-right"></span>
                <span><?php _e( 'Expand all', 'rich-snippets-schema' ); ?></span>
            </a>
        </th>
    </tr>
	<?php
	if ( ! $snippet->is_main_snippet() ):
		/**
		 * Main Snippet hook.
		 *
		 * Hooks into the HTML table of the main snippet.
		 *
		 * @hook wpbuddy/rich_snippets/properties/table/main
		 *
		 * @param {Rich_Snippet} $snippet
		 *
		 * @since 2.19.0
		 */
		do_action(
			'wpbuddy/rich_snippets/properties/table/main',
			$snippet,
            $html_id
		);
		?>

	<?php endif; ?>
    <tr>
        <th><?php _e( 'Property', 'rich-snippets-schema' ); ?></th>
        <th><?php _e( 'Type', 'rich-snippets-schema' ); ?></th>
    </tr>
    </thead>
    <tbody>
	<?php echo implode( '', $html ); ?>
    </tbody>
    <tfoot>
    <tr class="wpb-rs-schema-property-row-new">
        <td class="first">
            <label for="<?php echo esc_attr( $html_id ); ?>"><?php _e( 'Add new property', 'rich-snippets-schema' ); ?></label>
        </td>
        <td class="second">
            <input type="text" name="wpb_rs_schema_property" id="<?php echo esc_attr( $html_id ); ?>"
                   class="wpb-rs-schema-new-property wpb-rs-select" autocomplete="off"
                   placeholder="<?php _e( 'Search for a new property...', 'rich-snippets-schema' ); ?>"/>
            <button class="button wpb-rs-new-property-button">
                <span class="dashicons dashicons-yes"></span>
            </button>
        </td>
    </tr>
    </tfoot>
</table>
