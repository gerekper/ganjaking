<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Rich_Snippet;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var string[] $html The rendered properties.
 * @var Rich_Snippet $snippet The current snippet.
 */
$html    = $this->arguments[0];
$snippet = $this->arguments[1];

?>

<table class="widefat striped wpb-rs-property-list" data-snippet_id="<?php echo esc_attr( $snippet->id ); ?>"
       data-schema_type="<?php echo esc_attr( $snippet->get_type() ); ?>">
    <thead>
    <tr>
        <th colspan="3">
			<?php

			printf(
				__( 'Property list for %s', 'rich-snippets-schema' ),
				sprintf(
					'<a href="%s" target="_blank">%s <span class="dashicons dashicons-editor-help"></span></a>',
					esc_url( $snippet->get_type() ),
					esc_html( $snippet->get_type() )
				)
			);
			?>
        </th>
    </tr>
    </thead>
    <tbody>
	<?php echo implode( '', $html ); ?>
    </tbody>
</table>
