<?php

namespace ACP\Admin;

use AC;
use AC\Asset;
use AC\Asset\Location;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenPost;
use AC\ListScreenRepository\Sort;
use AC\ListScreenRepository\Storage;
use AC\Registrable;
use AC\Type\Url;
use AC\View;
use ACP\ListScreen\Media;
use ACP\Settings\ListScreen\HideOnScreen;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use WP_User;

class Settings implements Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Location\Absolute
	 */
	private $location;

	public function __construct( Storage $storage, Location\Absolute $location ) {
		$this->storage = $storage;
		$this->location = $location;
	}

	public function register() {
		add_action( 'ac/settings/before_columns', [ $this, 'render_title' ] );
		add_action( 'ac/settings/sidebox', [ $this, 'render_sidebar' ] );
		add_action( 'ac/settings/sidebox', [ $this, 'render_sidebar_help' ] );
		add_action( 'ac/admin_scripts/columns', [ $this, 'admin_scripts' ] );
		add_action( 'ac/settings/after_title', [ $this, 'render_submenu_view' ] );
		add_action( 'ac/settings/after_columns', [ $this, 'render_settings' ] );
		add_filter( 'ac/read_only_message', [ $this, 'read_only_message' ], 10, 2 );
	}

	public function render_submenu_view( ListScreen $current_list_screen ) {
		if ( ! apply_filters( 'acp/admin/enable_submenu', false ) ) {
			return;
		}

		$list_screens = $this->get_list_screens( $current_list_screen->get_key() );

		if ( $list_screens->count() <= 1 ) {
			return;
		}

		ob_start();
		foreach ( $list_screens as $list_screen ) : ?>
			<li data-screen="<?= esc_attr( $list_screen->get_layout_id() ); ?>">
				<a class="<?= $list_screen->get_layout_id() === $current_list_screen->get_layout_id() ? 'current' : ''; ?>" href="<?= add_query_arg( [ 'layout_id' => $list_screen->get_layout_id() ], $current_list_screen->get_edit_link() ); ?>"><?php echo esc_html( $list_screen->get_title() ? $list_screen->get_title() : __( '(no name)', 'codepress-admin-columns' ) ); ?></a>
			</li>
		<?php endforeach;

		$items = ob_get_clean();

		$menu = new View( [
			'items' => $items,
		] );

		echo $menu->set_template( 'admin/edit-submenu' );
	}

	/**
	 * @param string $key
	 *
	 * @return ListScreenCollection
	 */
	private function get_list_screens( $key ) {
		static $list_screen_types;

		if ( null === $list_screen_types ) {
			$list_screen_types = $this->storage->find_all( [
				'key'  => $key,
				'sort' => new Sort\ManualOrder(),
			] );
		}

		return $list_screen_types;
	}

	public function render_title( ListScreen $list_screen ) {
		$list_screens = $this->get_list_screens( $list_screen->get_key() );

		if ( $list_screens->count() <= 1 ) {
			return;
		}

		$view = new View( [
			'title' => $list_screen->get_title(),
		] );

		$view->set_template( 'admin/list-screen-title' );

		echo $view->render();
	}

	public function render_sidebar( ListScreen $current_list_screen ) {
		$list_screens = $this->get_list_screens( $current_list_screen->get_key() );

		$sidebar = new View( [
			'list_screen'  => $current_list_screen,
			'list_screens' => $list_screens,
		] );

		$sidebar->set_template( 'admin/list-screens-sidebar' );

		echo $sidebar->render();
	}

	/**
	 * Admin Scripts
	 */
	public function admin_scripts() {
		wp_deregister_script( 'select2' ); // try to remove any other version of select2

		$style = new Asset\Style( 'acp-layouts', $this->location->with_suffix( 'assets/core/css/layouts.css' ) );
		$style->enqueue();

		// Select2
		wp_enqueue_style( 'ac-select2' );
		wp_enqueue_script( 'ac-select2' );

		$script = new Asset\Script( 'acp-layouts', $this->location->with_suffix( 'assets/core/js/layouts.js' ), ['ac-admin-page-columns'] );
		$script->enqueue();

		wp_localize_script( 'acp-layouts', 'acp_layouts', [
			'roles'  => __( 'Select roles', 'codepress-admin-columns' ),
			'users'  => __( 'Select users', 'codepress-admin-columns' ),
			'_nonce' => wp_create_nonce( 'acp-layout' ),
		] );
	}

	private function get_tooltip_hs_content() {
		ob_start();
		?>
		<p>
			<?php _e( 'Make horizontal scrolling the default when users visit the table.', 'codepress-admin-columns' ); ?>
			<?php _e( 'Users can change this preference by opening “screen options” on the table screen.', 'codepress-admin-columns' ); ?>
		</p>
		<p>
			<?php _e( 'This feature will automatically adjust the width of the columns based on the screen size.', 'codepress-admin-columns' ); ?>
			<?php _e( 'When the columns do not fit on the screen you can horizontally scroll your columns by dragging the horizontal scrollbar or by swiping left or right with the mouse.', 'codepress-admin-columns' ); ?>
		</p>
		<p>
			<img src="<?= esc_url( $this->location->get_url() ); ?>assets/core/images/horizontal-scrolling.png" alt=""/>
		</p>
		<?php

		return ob_get_clean();
	}

	public function render_settings( ListScreen $list_screen ) {
		$roles = $list_screen->get_preference( 'roles' );

		if ( empty( $roles ) || ! is_array( $roles ) ) {
			$roles = [];
		}

		$users = $list_screen->get_preference( 'users' );

		if ( empty( $users ) || ! is_array( $users ) ) {
			$users = [];
		}

		$tooltip_horizontal_scrolling = new AC\Admin\Tooltip( 'horizontal_scrolling', [
			'content'    => $this->get_tooltip_hs_content(),
			'link_label' => '<img src="' . AC()->get_url() . 'assets/images/question.svg" alt="?" class="ac-setbox__row__th__info">',
			'title'      => __( 'Horizontal Scrolling', 'codepress-admin-columns' ),
			'position'   => 'right_bottom',
		] );

		$view = new AC\View( [
			'list_screen'    => $list_screen,
			'preferences'    => $list_screen->get_preferences(),
			'hide_on_screen' => $this->get_checkboxes( $list_screen ),
			'select_roles'   => $this->select_roles( $roles, $list_screen->is_read_only() ),
			'select_users'   => $this->select_users( $users, $list_screen->is_read_only() ),
			'tooltip_hs'     => $tooltip_horizontal_scrolling,
		] );

		$view->set_template( 'admin/list-screen-settings' );

		echo $view->render();
	}

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return string HTML
	 */
	private function get_checkboxes( ListScreen $list_screen ) {

		$collection = new HideOnScreenCollection();

		$collection->add( new HideOnScreen\Filters(), 30 )
		           ->add( new HideOnScreen\Search(), 90 )
		           ->add( new HideOnScreen\BulkActions(), 100 );

		if ( $list_screen instanceof ListScreenPost ) {
			$collection->add( new HideOnScreen\FilterPostDate(), 32 );

			if ( is_object_in_taxonomy( $list_screen->get_post_type(), 'category' ) ) {
				$collection->add( new HideOnScreen\FilterCategory(), 34 );
			}

			if ( post_type_supports( $list_screen->get_post_type(), 'post-formats' ) ) {
				$collection->add( new HideOnScreen\FilterPostFormat(), 36 );
			}

			if ( $list_screen instanceof Media ) {
				$collection->add( new HideOnScreen\FilterMediaItem(), 31 );
			}
		}

		do_action( 'acp/admin/settings/hide_on_screen', $collection, $list_screen );

		$checkboxes = [];

		/** @var HideOnScreen $hide_on_screen */
		foreach ( $collection->all() as $hide_on_screen ) {

			$class = '';

			if ( $hide_on_screen->get_dependent_on() ) {
				$class = '-indent';
			}

			// do not indent smart filters
			if ( 'hide_smart_filters' === $hide_on_screen->get_name() ) {
				$class = '';
			}

			$checkboxes[] = $this->render_checkbox(
				$hide_on_screen->get_name(),
				$hide_on_screen->get_label(),
				$hide_on_screen->is_hidden( $list_screen ),
				$hide_on_screen->get_dependent_on(),
				$class
			);
		}

		return implode( $checkboxes );
	}

	private function render_checkbox( $name, $label, $is_checked, $dependent_on = [], $class = '' ) {
		ob_start();
		// the hidden field makes sure we also save the 'off' state. This allows us to set a 'default' value.
		$attr_name = sprintf( 'settings[%s]', $name );
		?>
		<label class="<?= esc_attr( $class ); ?>" data-setting="<?= $name; ?>" data-dependent="<?= implode( ',', $dependent_on ); ?>">
			<input name="<?= $attr_name; ?>" type="hidden" value="off">
			<input name="<?= $attr_name; ?>" type="checkbox" <?php checked( $is_checked ); ?>> <?= esc_html( $label ); ?>
		</label>
		<?php
		return ob_get_clean();
	}

	public function render_sidebar_help() {
		?>
		<div id="layout-help" class="hidden">
			<h3><?php _e( 'Sets', 'codepress-admin-columns' ); ?></h3>

			<p>
				<?php _e( "Sets allow users to switch between different column views.", 'codepress-admin-columns' ); ?>
			</p>
			<p>
				<?php _e( "Available sets are selectable from the overview screen. Users can have their own column view preference.", 'codepress-admin-columns' ); ?>
			<p>
			<p>
				<img src="<?= esc_url( $this->location->get_url() ); ?>assets/core/images/layout-selector.png" alt=""/>
			</p>
			<p>
				<a href="<?= esc_url( ( new Url\Documentation( Url\Documentation::ARTICLE_COLUMN_SETS ) )->get_url() ); ?>" target="_blank"><?php _e( 'Online documentation', 'codepress-admin-columns' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * @param array $roles
	 * @param bool  $is_disabled
	 *
	 * @return AC\Form\Element\MultiSelect
	 */
	private function select_roles( array $roles = [], $is_disabled = false ) {
		$select = new AC\Form\Element\MultiSelect( 'settings[roles][]', $this->get_grouped_role_names() );

		$roles = array_map( 'strval', array_filter( $roles ) );

		$select->set_value( $roles )
		       ->set_attribute( 'class', 'roles' )
		       ->set_attribute( 'style', 'width: 100%;' )
		       ->set_attribute( 'id', 'listscreen_roles' );

		if ( $is_disabled ) {
			$select->set_attribute( 'disabled', 'dsiabled' );
		}

		return $select;
	}

	/**
	 * @return array
	 */
	private function get_grouped_role_names() {
		if ( ! function_exists( 'get_editable_roles' ) ) {
			return [];
		}

		$roles = [];

		foreach ( get_editable_roles() as $name => $role ) {
			$group = __( 'Other', 'codepress-admin-columns' );

			// Core roles
			if ( in_array( $name, [ 'super_admin', 'administrator', 'editor', 'author', 'contributor', 'subscriber' ] ) ) {
				$group = __( 'Default', 'codepress-admin-columns' );
			}

			/**
			 * @param string $group Role group
			 * @param string $name  Role name
			 *
			 * @since 4.0
			 */
			$group = apply_filters( 'ac/editing/role_group', $group, $name );

			if ( ! isset( $roles[ $group ] ) ) {
				$roles[ $group ]['title'] = $group;
				$roles[ $group ]['options'] = [];
			}

			$roles[ $group ]['options'][ $name ] = $role['name'];
		}

		return $roles;
	}

	/**
	 * @param array $user_ids
	 * @param bool  $is_disabled
	 *
	 * @return AC\Form\Element\MultiSelect
	 */
	private function select_users( array $user_ids = [], $is_disabled = false ) {
		$options = [];

		$user_ids = array_map( 'intval', array_filter( $user_ids ) );

		foreach ( $user_ids as $user_id ) {
			$user = get_userdata( $user_id );

			if ( ! $user instanceof WP_User ) {
				continue;
			}

			$options[ (string) $user_id ] = ac_helper()->user->get_display_name( $user_id );
		}

		$select = new AC\Form\Element\MultiSelect( 'settings[users][]', $options );

		$select->set_value( $user_ids )
		       ->set_attribute( 'class', 'users' )
		       ->set_attribute( 'style', 'width: 100%;' )
		       ->set_attribute( 'id', 'listscreen_users' );

		if ( $is_disabled ) {
			$select->set_attribute( 'disabled', 'dsiabled' );
		}

		return $select;
	}

	/**
	 * @param string     $message
	 * @param ListScreen $list_screen
	 *
	 * @return string
	 */
	public function read_only_message( $message, $list_screen ) {
		if ( $list_screen->is_read_only() ) {
			$message .= '<br/>' . sprintf( __( 'You can make an editable copy of this set by clicking %s on the right.', 'codepress-admin-columns' ), '"<strong>' . __( '+ Add set', 'codepress-admin-columns' ) . '</strong>"' );
		}

		return $message;
	}

}