<?php

namespace ACP\ThirdParty\YoastSeo;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;
use ReflectionException;

final class DeprecatedAddon
	implements AC\Registrable {

	public function register() {
		add_action( 'ac/column_types', [ $this, 'set_columns' ] );
		add_action( 'ac/column_groups', [ $this, 'set_groups' ] );
		add_action( 'ac/table/list_screen', [ $this, 'hide_yoast_filters' ] );
		add_action( 'ac/settings/before_columns', [ $this, 'check_yoast_deprecated_message' ] );
	}

	public function check_yoast_deprecated_message( AC\ListScreen $list_screen ) {
		if ( $this->uses_yoast_pro_features( $list_screen ) ) {

			$message = sprintf( '%s %s',
				__( "You're using Yoast SEO columns. Support for these columns have been moved to the new Yoast SEO integration.", 'codepress-admin-columns' ),
				sprintf( __( "If you want to keep using Yoast SEO columns, please download and install the Yoast integration from %s.", 'codepress-admin-columns' ),
					sprintf( '<a href="%s">%s</a>', ac_get_admin_url( 'addons' ), __( 'the add-ons tab', 'codepress-admin-columns' ) )
				)
			);

			$notice = new AC\Message\InlineMessage( sprintf( '<p>%s</p>', $message ) );
			echo $notice->set_type( AC\Message::WARNING )->render();
		}
	}

	private function uses_yoast_pro_features( AC\ListScreen $list_screen ) {

		foreach ( $list_screen->get_columns() as $column ) {
			if ( false !== strpos( get_class( $column ), 'ACP\ThirdParty\YoastSeo' ) ) {
				if ( ! $column->is_original() ) {
					return true;
				}

				if ( $column instanceof Sorting\Sortable ) {
					/** @var Sorting\Settings $setting */
					$setting = $column->get_setting( 'sort' );
					if ( $setting && $setting->is_active() ) {
						return true;
					}
				}

				if ( $column instanceof Editing\Editable && $column->editing()->is_active() ) {
					/** @var Editing\Settings $setting */
					$setting = $column->get_setting( 'edit' );
					if ( $setting && $setting->is_active() ) {
						return true;
					}
				}

				if ( $column instanceof Filtering\Filterable && $column->filtering()->is_active() ) {
					/** @var Filtering\Settings $setting */
					$setting = $column->get_setting( 'filter' );
					if ( $setting && $setting->is_active() ) {
						return true;
					}
				}

				if ( $column instanceof Export\Exportable && $column->export()->is_active() ) {
					/** @var Export\Settings\Column $setting */
					$setting = $column->get_setting( 'export' );
					if ( $setting && $setting->is_active() ) {
						return true;
					}
				}

				if ( $column instanceof Search\Searchable ) {
					/** @var Search\Settings\Column $setting */
					$setting = $column->get_setting( 'search' );
					if ( $setting && $setting->is_active() ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function hide_yoast_filters( $list_screen ) {
		global $wpseo_meta_columns;

		if ( ! $wpseo_meta_columns ) {
			return;
		}

		if ( ! $list_screen->get_column_by_name( 'wpseo-score' ) ) {
			remove_action( 'restrict_manage_posts', [ $wpseo_meta_columns, 'posts_filter_dropdown' ] );
		}

		if ( ! $list_screen->get_column_by_name( 'wpseo-score-readability' ) ) {
			remove_action( 'restrict_manage_posts', [ $wpseo_meta_columns, 'posts_filter_dropdown_readability' ] );
		}

	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @throws ReflectionException
	 */
	public function set_columns( $list_screen ) {
		$list_screen->register_column_types_from_dir( __NAMESPACE__ . '\Column' );
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function set_groups( $groups ) {
		$groups->register_group( 'yoast-seo', __( 'Yoast SEO', 'wordpress-seo' ), 25 );
	}

}