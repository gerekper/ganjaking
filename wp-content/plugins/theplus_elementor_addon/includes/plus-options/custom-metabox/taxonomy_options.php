<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'cmb2_admin_init', 'theplus_ele_taxonomy_options_metaboxes' );
function theplus_get_list_taxonomies() {
	$args = array(
		'public'   => true,
     'show_ui' => true
	);
	$output = 'names'; // or objects
	$operator = 'and'; // 'and' or 'or'
	
	$taxonomies = get_taxonomies( $args, $output, $operator );
	if ( $taxonomies ) {		
		foreach ( $taxonomies  as $taxonomy ) {
			$options[$taxonomy] = $taxonomy;
		}
		if(in_array('product_cat',$options)){
			unset($options['product_cat']);
		}
		return $options;
	}	
	
}

function theplus_ele_taxonomy_options_metaboxes() {

	$prefix = 'tp_taxonomy_';	
	$taxonomy_field = new_cmb2_box(
		array(
			'id'         => 'taxonomy_options_metaboxes',
			'title'      => esc_html__('ThePlus Taxonomy Options', 'theplus'),
			'pages'      => array('category'),
			'object_types'     => array( 'term' ),
			'taxonomies'       => theplus_get_list_taxonomies(),
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, 
		)
	);	
	$taxonomy_field->add_field( 
		array(
			'name'    => 'Thumbnail',	
			'id'      => $prefix.'image',
			'type'    => 'file',	
			'options' => array(
				'url' => false,
			),
			'text'    => array(
				'add_upload_file_text' => 'Upload/Add image'
			),	
			'query_args' => array(		
				 'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
					'image/svg',
				 ),
			),
			'preview_size' => 'thumbnail',
			'column' => array(
				'position' => 1,
				'name'     => 'Image',
			),
			'display_cb' => 'tp_display_taxonomy_image',
		)
	);
}

/**
 * Manually render a field column display.
 *
 * @param  array      $field_args Array of field arguments.
 * @param  CMB2_Field $field      The field object
 */
function tp_display_taxonomy_image( $field_args, $field ) {
	?>
	<div class="custom-column-display <?php echo $field->row_classes(); ?>">	
	
		<img src="<?php echo $field->value; ?>" style="max-width:40px;" />
	</div>
	<?php
}