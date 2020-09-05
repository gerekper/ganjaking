<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Rich_Snippet;

/**
 * @var Rich_Snippet $snippet
 */
$snippet = $this->arguments[0];
$html_id = $this->arguments[1];
?>

<tr class="wpb-rs-property-loop">
    <th>
		<?php _e( 'Loop', 'rich-snippets-schema' ); ?>
    </th>
    <th>
        <select class="wpb-rs-schema-property-field-loop-select"
                name="<?php printf( 'snippets[%s][loop]', esc_attr( $snippet->id ) ); ?>"
                id="wpb-rs-schema-property-loop-select-<?php echo esc_attr( $html_id ); ?>">
			<?php

			$internal_loop_options = Fields_Model::get_loop_subselect_options(
				$snippet->get_type(),
				$snippet->get_loop_type()
			);

			echo implode( '', $internal_loop_options );
			?>
        </select>
		<?php
		printf(
			'<a href="%s" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>',
			Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/loops/', 'global-snippet-creation' )
		);
		?>
    </th>
</tr>