<?php

use AC\ListScreen;
use AC\View;
use ACP\Controller\ListScreenCreate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="sidebox layouts" data-type="<?= $this->list_screen->get_key(); ?>">

	<div class="header">
		<h3>
			<span class="header-content"><?php _e( 'Column Sets', 'codepress-admin-columns' ); ?></span>
			<a class="button add-new">
				<span class="add"><?= esc_html( __( '+ Add set', 'codepress-admin-columns' ) ); ?></span>
				<span class="close"><?= esc_html( __( 'Cancel', 'codepress-admin-columns' ) ); ?></span>
			</a>
		</h3>
	</div>
	<?php
	$view = new View( [
		'list_screen' => $this->list_screen,
		'nonce'       => wp_create_nonce( 'create-layout' ),
	] );

	$view->set_template( 'admin/create-list-screen' );

	echo $view->render();

	if ( $this->list_screens->count() > 1 ) : ?>

		<div class="layouts__items">

			<?php foreach ( $this->list_screens as $list_screen ) : ?>
				<?php /** @var ListScreen $list_screen */ ?>
				<?php $is_current = $this->list_screen->get_layout_id() == $list_screen->get_layout_id(); ?>
				<?php $onclick = apply_filters( 'ac/delete_confirmation', true ) ? ' onclick="return confirm(\'' . esc_attr( addslashes( sprintf( __( "Warning! The %s columns data will be deleted. This cannot be undone. 'OK' to delete, 'Cancel' to stop", 'codepress-admin-columns' ), "'" . $list_screen->get_label() . "'" ) ) ) . '\');"' : ''; ?>

				<?php
				$class = [];

				if ( $is_current ) {
					$class[] = '-current';
				}
				if ( $list_screen->is_read_only() ) {
					$class[] = '-read_only';
				}
				?>

				<div class="layouts__item <?= esc_attr( implode( ' ', $class ) ); ?>" data-screen="<?= esc_attr( $list_screen->get_layout_id() ); ?>">
					<div class="layouts__item__move">
						<span class="cpacicon-move"></span>
					</div>
					<div class="layouts__item__title">
						<?php

						$title = esc_html( $list_screen->get_title() );

						if ( empty( $title ) ) {
							$title = __( '(no name)', 'codepress-admin-coluns' );
						}

						$title = sprintf( '<span data-label>%s</span>', $title );
						if ( ! $is_current ) {
							echo ac_helper()->html->link( $list_screen->get_edit_link(), $title, [ 'class' => 'select' ] );
						} else {
							echo $title;
						}

						$description = [];

						$roles = $list_screen->get_preference( 'roles' );
						$users = $list_screen->get_preference( 'users' );

						if ( $roles ) {
							if ( 1 === count( $roles ) ) {
								$_roles = get_editable_roles();
								$role = $roles[0];
								$description[] = isset( $_roles[ $role ] ) ? $_roles[ $role ]['name'] : $role;
							} else {
								$description[] = __( 'Roles', 'codepress-admin-columns' );
							}
						}
						if ( $users ) {
							if ( 1 === count( $users ) ) {
								$user = get_userdata( $users[0] );

								if ( $user instanceof WP_User ) {
									$user_name = ucfirst( ac_helper()->user->get_display_name( $user, 'first_last_name' ) );

									if ( ! $user_name ) {
										$user_name = __( 'User', 'codepress-admin-columns' );
									}

									$description[] = $user_name;
								}
							} else {
								$description[] = __( 'Users' );
							}
						}

						$description = implode( ' & ', array_filter( $description ) );

						if ( $description ) {
							printf( '<span class="layouts__item__permissions">%s</span>', ac_helper()->html->tooltip( ac_helper()->icon->dashicon( [ 'icon' => 'admin-users', 'class' => 'gray' ] ), $description ) );
						}

						?>
						<small class="layouts__item__id"><?= sprintf( '%s: %s', __( 'ID', 'codepress-admin-columns' ), $list_screen->get_layout_id() ); ?></small>
					</div>
					<div class="layouts__item__extra">
						<div class="layouts__item__state">
							<?php if ( $list_screen->is_read_only() ): ?>
								<div class="layouts__item__state__readonly">
									<?= ac_helper()->html->tooltip( ac_helper()->icon->dashicon( [ 'icon' => 'lock', 'class' => 'gray' ] ), __( 'Read Only', 'codepress-admin-columns' ) ); ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="layouts__item__actions">
							<?php if ( ! $list_screen->is_read_only() ): ?>
								<form method="post" class="delete">
									<input type="hidden" name="_ac_nonce" value="<?= wp_create_nonce( 'delete-layout' ); ?>"/>
									<input type="hidden" name="<?= ListScreenCreate::PARAM_ACTION; ?>" value="<?= ListScreenCreate::PARAM_DELETE_LIST; ?>">
									<input type="hidden" name="layout_id" value="<?= esc_attr( $list_screen->get_layout_id() ); ?>">
									<input type="hidden" name="list_screen" value="<?= esc_attr( $list_screen->get_key() ); ?>">
									<input type="submit" class="delete" value="<?= esc_attr( __( 'Delete', 'codepress-admin-columns' ) ); ?>"<?= $onclick; ?>/>
								</form>
							<?php endif; ?>
						</div>
					</div>
				</div>

			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>