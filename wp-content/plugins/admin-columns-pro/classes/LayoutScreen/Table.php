<?php

namespace ACP\LayoutScreen;

use AC\Form\Element\Select;
use AC\ListScreen;
use AC\Registrable;

class Table implements Registrable {

	public function register() {
		add_action( 'ac/table/list_screen', array( $this, 'set_current_layout' ), 9 ); // Early priority
		add_action( 'ac/table_scripts', array( $this, 'table_scripts' ) );
		add_action( 'ac/admin_footer', array( $this, 'switcher' ) );
	}

	/**
	 * @param ListScreen $list_screen
	 */
	public function set_current_layout( $list_screen ) {
		$layouts = ACP()->layouts( $list_screen );

		// User switched layout
		if ( isset( $_GET['layout'] ) ) {
			if ( $layout = $layouts->get_layout_by_id( $_GET['layout'] ) ) {
				$layouts->set_user_preference( $layout );
			}
		}

		// Current user layouts
		if ( $layouts->get_layouts_for_current_user() ) {
			$layout = $layouts->get_user_preference();

			// when no longer available use the first user layout
			if ( ! $layout ) {
				$layout = $layouts->get_first_layout_for_current_user();
			}

			$list_screen->set_layout_id( $layout->get_id() );
		} // User doesn't have eligible layouts.. but the current (null) layout does exists, then the WP default columns are loaded
		else if ( $layouts->get_layout_by_id( null ) ) {
			// _wp_default_ does not exists therefor will load WP default
			$list_screen->set_layout_id( '_wp_default_' );
		}
	}

	private function get_layout_link( $link, $id ) {
		return add_query_arg( array( 'layout' => $id ), $link );
	}

	/**
	 * @param ListScreen $list_screen
	 */
	public function switcher( $list_screen ) {
		if ( ! $list_screen ) {
			return;
		}

		$link = $list_screen->get_screen_link();

		if ( $post_status = filter_input( INPUT_GET, 'post_status', FILTER_SANITIZE_STRING ) ) {
			$link = add_query_arg( array( 'post_status' => $post_status ), $link );
		}

		if ( $author = filter_input( INPUT_GET, 'author', FILTER_SANITIZE_STRING ) ) {
			$link = add_query_arg( array( 'author' => $author ), $link );
		}

		$layouts = ACP()->layouts( $list_screen )->get_layouts_for_current_user();

		if ( count( $layouts ) > 1 ) : ?>
			<form class="layout-switcher">
				<label for="column-view-selector" class="label screen-reader-text">
					<?php _e( 'Switch View', 'codepress-admin-columns' ); ?>
				</label>
				<span class="spinner"></span>

				<?php

				$options = array();

				foreach ( $layouts as $layout ) {
					$options[ $this->get_layout_link( $link, $layout->get_id() ) ] = $layout->get_name();
				}

				$select = new Select( 'layout', $options );

				$select->set_attribute( 'id', 'column-view-selector' )
				       ->set_attribute( 'data-ac-tip', __( 'Switch View', 'codepress-admin-columns' ) )
				       ->set_value( $this->get_layout_link( $link, $list_screen->get_layout_id() ) );

				echo $select->render();

				?>
				<script type="text/javascript">
					jQuery( document ).ready( function( $ ) {
						$( '.layout-switcher' ).change( function() {
							var _select = $( this ).addClass( 'loading' ).find( 'select' ).attr( 'disabled', 1 );
							window.location = _select.val();
						} );
					} );
				</script>
			</form>
		<?php
		endif;
	}

	/**
	 * Loads scripts on the list screen
	 */
	public function table_scripts() {
		wp_enqueue_script( 'acp-layouts', ACP()->get_url() . 'assets/core/js/layouts-listings-screen.js', array( 'jquery' ), ACP()->get_version() );
		wp_enqueue_style( 'acp-layouts', ACP()->get_url() . 'assets/core/css/layouts-listings-screen.css', array(), ACP()->get_version() );
	}

}