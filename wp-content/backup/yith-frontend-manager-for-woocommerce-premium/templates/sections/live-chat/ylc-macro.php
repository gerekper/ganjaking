<?php

defined( 'ABSPATH' ) or exit;

$act = isset( $_REQUEST['act'] ) ? $_REQUEST['act'] : '';

if ( $act == 'save' ) {

	$new_macro_id = isset( $_POST['id'] ) ? $_POST['id'] : 0;

	if ( $new_macro_id > 0 ) {

		wp_update_post( array(
			                'ID'           => $_POST['id'],
			                'post_title'   => $_POST['post_title'],
			                'post_content' => $_POST['post_content'],
			                'post_status'  => 'publish'
		                ) );

	} else {

		$new_macro_id = wp_insert_post( array(
			                                'post_title'   => $_POST['post_title'],
			                                'post_content' => $_POST['post_content'],
			                                'post_status'  => 'publish',
			                                'post_type'    => 'ylc-macro',
		                                ) );

	}

}

if ( isset( $new_macro_id ) && $new_macro_id > 0 ) {
	$macro_id = $new_macro_id;
} elseif ( isset( $_GET['macro_id'] ) && $_GET['macro_id'] > 0 ) {
	$macro_id = $_GET['macro_id'];
} else {
	$macro_id = 0;
}

$post_title = $post_content = '';

if ( $macro_id > 0 ) {
	$macro = get_post( $macro_id );
} else {
	$macro    = get_default_post_to_edit( 'ylc-macro', true );
	$macro_id = $macro->ID;
}

if ( ! empty( $macro ) ) {
	$post_title   = $macro->post_title;
	$post_content = $macro->post_content;
}

$endpoint_url = $section_obj->get_url( $section_obj->get_current_subsection( true ) );

?>

<div id="yith-wcfm-product" class="yith-wcfm-product yith-wcfm-form">

	<h1><?php echo __( 'Chat macro', 'yith-frontend-manager-for-woocommerce' ); ?></h1>

	<?php
	if( $act == 'save' ) {
		$message = $new_macro_id != 0 ? 'success' : 'error';
		$section_obj->show_wc_notice( $message );
	}
	?>

	<form name="post" action="<?php echo add_query_arg( array( 'macro_id' => $macro_id ), $endpoint_url ); ?>" method="post" id="post">

		<input type="hidden" name="id" value="<?php echo $macro_id; ?>">
		<input type="hidden" name="macro_id" value="<?php echo $macro_id; ?>">
		<input type="hidden" name="act" value="save">

		<p class="form-field">
			<label for="post_title"><?php echo __( 'Title', 'yith-frontend-manager-for-woocommerce' ); ?></label>
			<input type="text" name="post_title" value="<?php echo $post_title; ?>" id="post_title">
		</p>

		<p class="form-field">
			<label for="post_content"><?php echo __( 'Description', 'yith-frontend-manager-for-woocommerce' ); ?></label>
		</p>

		<div class="visual-editor"><?php wp_editor( $post_content, 'post_content', $settings = array(
				'media_buttons' => false,
				'quicktags'     => false,
				'tinymce'       => false
			) ); ?>
		</div>
		<br />
		<input type="submit" value="Save" />

	</form>

</div>
