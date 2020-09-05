<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var Rich_Snippet $snippet
 * @var \WP_Post     $post
 */
$snippet = $this->arguments[0];
$post    = $this->arguments[1];
//$overwrite_view = isset( $this->arguments[2] ) ? $this->arguments[2] : false;
$main_type  = $snippet->get_type();
$controller = Admin_Snippets_Controller::instance();

?>

<div class="wpb-rs-loader updating-message"><p class="updating-message"></p></div>

<div class="wpb-rs-schema-main" data-uid="<?php echo esc_attr( $snippet->id ); ?>"
	 data-type="<?php echo esc_attr( $snippet->get_type() ); ?>">
	<label for="wpb_rs_schema_main_select_<?php echo esc_attr( $snippet->id ); ?>"><?php esc_html_e( 'Select a thing', 'rich-snippets-schema' ) ?></label>
	<input type="text" name="wpb_rs_schema_main_select[<?php echo esc_attr( $snippet->id ); ?>]"
		   id="wpb_rs_schema_main_select_<?php echo esc_attr( $snippet->id ); ?>"
		   autocomplete="off"
		   class="wpb-rs-schema-main-select wpb-rs-select" value="<?php echo esc_attr( $main_type ); ?>">

	<button class="button wpb-rs-new-type-button">
		<span class="dashicons dashicons-yes"></span>
	</button>
	<p class="wpb-rs-popular">
		<?php
		esc_html_e( 'Popular:', 'rich-snippets-schema' );

		$popular_items = Schemas_Model::get_popular_types();

		foreach ( $popular_items as $popular_item_value => $popular_item_label ) {
			printf( '<a href="#" class="button small" data-value="%s">%s</a>', esc_attr( $popular_item_value ), esc_html( $popular_item_label ) );
		}

		printf(
			'<a class="button small button-primary help" href="%s" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>',
			Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/structured-data/module-3/', 'snip-popular' )
		);
		?>
	</p>
	<div class="wpb-rs-property-list-main">
		<?php echo $controller->get_property_table( $snippet, array(), $post ); ?>
	</div>
</div>
