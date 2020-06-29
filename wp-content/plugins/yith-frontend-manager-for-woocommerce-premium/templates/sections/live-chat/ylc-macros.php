<?php
/**
 * Frontend Manager Products
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post_type, $post_type_object, $wp_post_types, $wp_query;

/**
 * Before query
 */
$post_type        = 'ylc-macro';
$post_type_object = get_post_type_object( $post_type );
set_current_screen( $post_type );
$GLOBALS['hook_suffix'] = 'ylc-macro';
$act                    = ! empty( $_GET['act'] ) ? $_GET['act'] : '';

if ( 'delete' == $act && ! empty( $_GET['macro_id'] ) ) {

	$macro = get_post( $_GET['macro_id'] );

	if ( $macro->post_type == 'ylc-macro' ) {
		wp_trash_post( $_GET['macro_id'] );
		$message = _x( 'Macro deleted successfully', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
		$type    = 'success';
	} else {
		$message = _x( 'Macro does not exist', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
		$type    = 'error';
	}

	wc_add_notice( $message, $type );
	
}

$wp_list_table = new YITH_Chat_Macro_List_Table( array( 'screen' => $post_type, 'section_obj' => $section_obj ) );
$pagenum       = $wp_list_table->get_pagenum();
$doaction      = $wp_list_table->current_action();
$wp_list_table->prepare_items();

do_action( 'yith_wcfm_before_section_template', $section, $subsection, $act );
$title = $post_type_object->labels->name;
?>
	<div id="yith-wcfm-ylc-macro">
		<h1>
			<?php echo __( 'Chat Macros', 'yith-frontend-manager-for-woocommerce' ); ?>
		</h1>
		<?php
		$wp_list_table->display();
		?>
	</div>
<?php

do_action( 'yith_wcfm_after_section_template', $section, $subsection, $act );
