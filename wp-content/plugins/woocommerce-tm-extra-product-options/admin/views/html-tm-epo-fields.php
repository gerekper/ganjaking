<?php
/**
 * View for displaying saved TM EPOs
 *
 * @package Extra Product Options/Admin/Views
 * @version 4.8
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $post_type_object )
     && isset( $bulk_counts )
     && isset( $post_type )
     && isset( $general_messages )
     && isset( $post_type_object )
) {

	?>
    <div class="wrap">
    <h2><?php esc_html_e( 'Global Extra Product Options', 'woocommerce-tm-extra-product-options' );
		if ( isset( $post_new_file ) && current_user_can( $post_type_object->cap->create_posts ) ) {
			echo ' <a href="' . esc_url( admin_url( $post_new_file ) ) . '" class="add-new-h2">' . esc_html( $post_type_object->labels->add_new ) . '</a>';
		}
		?>
    </h2>

	<?php
	// If we have a bulk message to issue:
	$messages = array();
	foreach ( $bulk_counts as $message => $count ) {
		if ( isset( $bulk_messages[ $post_type ][ $message ] ) ) {
			$messages[] = sprintf( $bulk_messages[ $post_type ][ $message ], number_format_i18n( $count ) );
		}
		if ( $message == 'trashed' && isset( $_REQUEST['ids'] ) ) {
			$ids = preg_replace( '/[^0-9,]/', '', $_REQUEST['ids'] );
			if ( isset( $_REQUEST['from_bulk'] ) ) {
				$tm_nonce = "bulk-posts";
				$tm_bulk  = "&tm_bulk=1";
			} elseif ( isset( $_REQUEST['action'] ) ) {
				$tm_nonce = $_REQUEST['action'] . "-post_" . $_REQUEST['ids'];
				$tm_bulk  = "";
			} elseif ( isset( $_REQUEST['trashed'] ) && $_REQUEST['trashed'] == 1 ) {
				$tm_nonce = "untrash-post_" . $_REQUEST['ids'];
				$tm_bulk  = "";
			}
			if ( isset( $tm_nonce ) ) {
				$messages[] = '<a href="' . esc_url( wp_nonce_url( "edit.php?post_type=product&page=" . esc_attr( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK ) . "&doaction=undo&action=untrash&ids=$ids" . $tm_bulk, $tm_nonce ) ) . '">' . esc_html__( 'Undo', 'woocommerce-tm-extra-product-options' ) . '</a>';
			}
		}
	}
	$error_messages = array();
	if ( isset( $_REQUEST['message'] ) ) {
		$error_messages[] = $general_messages[ $post_type ][ $_REQUEST['message'] ];
	}
	if ( $messages ) {
		echo '<div id="message" class="updated"><p>' . wp_kses_post( join( ' ', $messages ) ) . '</p></div>';
	}
	unset( $messages );
	if ( $error_messages ) {
		echo '<div id="message" class="error"><p>' . wp_kses_post( join( ' ', $error_messages ) ) . '</p></div>';
	}
	unset( $error_messages );

	$_SERVER['REQUEST_URI'] = esc_url_raw( remove_query_arg( array( 'locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed' ), $_SERVER['REQUEST_URI'] ) );
	?>
	<?php do_action( 'tm_list_table_action', 'views' ); ?>

    <form id="posts-filter" action="" method="get">

		<?php do_action( 'tm_list_table_action', 'search_box', array( 'text' => $post_type_object->labels->search_items, 'input_id' => 'post' ) ); ?>
        <input type="hidden" name="tm_bulk" class="post_is_bulk" value="1"/>
        <input type="hidden" name="post_status" class="post_status_page" value="<?php echo ! empty( $_REQUEST['post_status'] ) ? esc_attr( $_REQUEST['post_status'] ) : 'all'; ?>"/>
        <input type="hidden" name="post_type" class="post_type_page" value="product"/>
        <input type="hidden" name="page" class="page_page" value="<?php echo esc_attr( THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK ); ?>"/>
		<?php if ( ! empty( $_REQUEST['show_sticky'] ) ) { ?>
            <input type="hidden" name="show_sticky" value="1"/>
		<?php } ?>
		<?php do_action( 'tm_list_table_action', 'display' ); ?>
    </form>
	<?php do_action( 'tm_list_table_action', 'inline_edit' ); ?>
    <div id="ajax-response"></div>
    <br class="clear"/>
    </div><?php
}