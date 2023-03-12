<?php

namespace ACP\Table;

use AC\Capabilities;
use AC\Form\Element\Select;
use AC\ListScreen;
use AC\ListScreenRepository\Filter\ExcludeAdmin;
use AC\ListScreenRepository\Sort\ManualOrder;
use AC\ListScreenRepository\Storage;
use AC\Registerable;

class Switcher implements Registerable {

	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function register() {
		add_action( 'ac/admin_footer', [ $this, 'switcher' ] );
	}

	private function add_filter_args_to_url( string $link ): string {
		$post_status = filter_input( INPUT_GET, 'post_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( $post_status ) {
			$link = add_query_arg( [ 'post_status' => $post_status ], $link );
		}

		$author = filter_input( INPUT_GET, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( $author ) {
			$link = add_query_arg( [ 'author' => $author ], $link );
		}

		return $link;
	}

	public function switcher( ListScreen $list_screen ): void {
		$user = wp_get_current_user();

		if ( ! $user ) {
			return;
		}

		$list_screens = $this->storage->find_all_by_user( $list_screen->get_key(), $user, new ManualOrder() );
		$list_screens = ( new ExcludeAdmin( $user ) )->filter( $list_screens );

		// Add current list screeen for when an admin visits the table for a user or role specific listscreen
		if ( current_user_can( Capabilities::MANAGE ) && ! $list_screens->contains( $list_screen ) ) {
			$list_screens->add( $list_screen );
		}

		if ( $list_screens->count() > 1 ) : ?>
			<form class="layout-switcher">
				<label for="column-view-selector" class="label screen-reader-text">
					<?php _e( 'Switch View', 'codepress-admin-columns' ); ?>
				</label>
				<span class="spinner"></span>

				<?php

				$options = [];

				foreach ( $list_screens as $_list_screen ) {
					$options[ $this->add_filter_args_to_url( $_list_screen->get_screen_link() ) ] = $_list_screen->get_title() ?: __( 'Original', 'codepress-admin-columns' );
				}

				$select = new Select( 'layout', $options );
				$select->set_attribute( 'id', 'column-view-selector' )
				       ->set_attribute( 'data-ac-tip', __( 'Switch View', 'codepress-admin-columns' ) )
				       ->set_value( $this->add_filter_args_to_url( $list_screen->get_screen_link() ) );

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

}