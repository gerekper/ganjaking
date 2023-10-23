<?php
if( !defined( 'ABSPATH' ) ){
	exit;
}

$act =  isset( $_REQUEST['act'] ) ? $_REQUEST['act'] : '';


if( isset( $_REQUEST['tab_id'] ) && $_REQUEST['tab_id'] > 0 ){
	$tab_id = $_REQUEST['tab_id'];
}else{
    $tab = get_default_post_to_edit( 'ywtm_tab', true );
    $tab_id = $tab->ID;
}

if( 'save' == $act ){

	$post_title = isset( $_REQUEST[ 'post_title'] ) ?  $_REQUEST[ 'post_title'] : '';
	$post_excerpt = isset( $_REQUEST[ 'post_excerpt'] ) ?  $_REQUEST[ 'post_excerpt'] : '';

	wp_update_post( array(
		'ID'                => $tab_id,
		'post_title'        => $post_title,
		'post_excerpt'      => $post_excerpt,
		'post_status'       => 'publish'
	) );

}

global $post;

$old_post = $post;
$post = get_post( $tab_id );
$endpoint_url = $section_obj->get_url( $section_obj->get_current_subsection( true ) );

$post_title = isset( $post->post_title ) ? $post->post_title  : '';
$post_excerpt = isset( $post->post_excerpt ) ? $post->post_excerpt : '';

?>
<form name="post" action="<?php echo add_query_arg( array( 'tab_id' => $tab_id ), $endpoint_url ); ?>" method="post" id="post">

	<input type="hidden" name="id" value="<?php echo $tab_id; ?>">
	<input type="hidden" name="tab_id" value="<?php echo $tab_id; ?>">
	<input type="hidden" name="act" value="save">
    <input type="hidden" name="post_type" value="ywtm_tab">

    <p class="form-field">
        <label for="post_title"><?php echo __('Tab Title', 'yith-frontend-manager-for-woocommerce'); ?></label>
        <input type="text" name="post_title" value="<?php echo $post_title; ?>" id="post_title">
    </p>
    <p class="form-field">
        <label for="excerpt"><?php echo __('Excerpt', 'yith-frontend-manager-for-woocommerce');?></label>
        <textarea id="excerpt" name="post_excerpt"  rows="1" cols="40"><?php echo $post_excerpt;?></textarea>
    </p>

	<?php do_action( 'yith_wcfm_tabs_show_metaboxes', $post , $tab_id );?>
	<input type="submit" value="Save" />
</form>