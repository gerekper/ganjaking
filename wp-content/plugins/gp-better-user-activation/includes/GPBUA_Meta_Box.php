<?php

class GPBUA_Meta_Box {

  public function __construct() {

	// activation page meta boxes
	add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ));
	add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );

	// reset content ajax
	add_action('wp_ajax_gpbua_reset_content', array( $this, 'gpbua_reset_content' ));

  }

  public function add_meta_box() {

	if( !gpbua_is_activation_page() ) {
	  return;
	}

	add_meta_box(
	  'gpbua_activation_metabox', // $id
	  __( 'Activation View Content', 'gp-better-user-activation' ), // $title
	  array( $this, 'meta_box_callback' ), // $callback
	  'page', // $page
	  'normal', // $context
	  'high' // $priority
	);

  }

	public function save_meta_boxes( $post_id, $post ) {

		if( ! gpbua_is_activation_page() ) {
		  return;
		}

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return;
		}

		$this->save_meta( $post_id );

	}

	public function save_meta( $post_id ) {
		$meta_boxes = $this->definitions();
		foreach( $meta_boxes as $mb ) {
			$this->update_meta( $mb['key'], $post_id );
		}
	}

	private function update_meta( $key, $post_id ) {

		$meta_key = '_gpbua_activation_' . $key;
		$new_meta_value = isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value ) {
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );
		}
		/* If the new meta value does not match the old value, update it. */
		else if ( $new_meta_value && $new_meta_value != $meta_value ) {
			update_post_meta( $post_id, $meta_key, $new_meta_value );
		}

	}

  /*
   * Check if the content for a given field/meta has ever been edited
   * If not edited we will be loading the default content
   */
  public function has_content_been_edited( $post_id, $meta_key ) {
	if( in_array( $meta_key, get_post_custom_keys( $post_id ) ) ) {
	  return true;
	}
	return false;
  }

  public function meta_box_callback( $post ) {

	$tabs = array();

	// loop through each defined view
	foreach( $this->definitions() as $index => $view ) {

	$key = $view['key'];
	  $field_id = '_gpbua_activation_' . $key;
	  $tabs[$index]['field_id'] = $field_id;
	  $tabs[$index]['key'] = $view['key'];
	  $tabs[$index]['title'] = $view['title'];

	  $edited = $this->has_content_been_edited( gpbua_get_activation_page_id(), $field_id );
	  if( $edited ) {
		$tabs[$index]['content'] = get_post_meta( gpbua_get_activation_page_id(), $field_id, true );
	  } else {
		$tabs[$index]['content'] = gpbua()->get_default_content( $key );
	  }

	}

	require_once( gpbua()->get_base_path() . '/templates/tabs-meta.php' );

  }

  public function render_reset_content_button( $view ) {
	echo sprintf(
		'<button class="gpbua-reset-content-button button button-secondary" data-gpbua_view="%s">%s</button>',
		$view, __( 'Reset Default Content', 'gp-better-user-activation' )
	);
  }

	public function render_merge_tag_select( $view ) {

		$tags     = GPBUA_Merge_Tags::get_merge_tags( $view );
		$options  = array( sprintf( '<option value="">%s</option>', __( 'Insert Merge Tag', 'gp-better-user-activation' ) ) );

		foreach( $tags as $tag ) {
			$options[] = sprintf( '<option value="%s">%s</option>', $tag['tag'], $tag['label'] );
		}

		printf( '<select class="gpbua-merge-tag-select" onchange="GPBUA.insertMergeTag( this, this.value );">%s</select>', implode( "\n", $options ) );
	}

  // ajax callback to reset field content to default
  public function gpbua_reset_content() {
	$view = $_POST['view'];
	$content = gpbua()->get_default_content( $view );
	print $content;
	wp_die();
  }

  public function render_merge_tag_support_list( $tab_key ) {



	print '<p style="margin-top: 20px;">';
	print '<span style="font-weight: bold">';
	print __( 'Supported merge tags', 'gp-better-user-activation' );
	print ':</span>.';
	print '</p>';
	if( $no_entry ) {
	  print '<p style="font-style: italic;">';
	  print __( ' Note that the use of entry-based merge tags are not supported here because the activation page will not have access to an entry.', 'gp-better-user-activation' );
	  print '</p>';
	}

  }

	/*
	* Returns the meta box view definitions
	*/
	public function definitions() {
		return array(
			array(
				'key' => 'success',
				'title' => __( 'Activation Success', 'gp-better-user-activation' ),
			),
			/*array(
				'key' => 'success_site',
				'title' => __( 'Site Activation Success', 'gp-better-user-activation' ),
			),*/
			array(
				'key' => 'error_already_active',
				'title' => __( 'Already Active', 'gp-better-user-activation' ),
			),
			/*array(
				'key' => 'error_already_active_site',
				'title' => __( 'Site Already Active', 'gp-better-user-activation' )
			),*/
			array(
				'key' => 'error_no_key',
				'title' => 'No Key',
			),
			array(
				'key' => 'error',
				'title' => 'Other Error',
			),
		);
	}

}