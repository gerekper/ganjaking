<?php
namespace DynamicContentForElementor\AdminPages\Features;

use DynamicContentForElementor\Assets;
use DynamicContentForElementor\LicenseSystem;
use DynamicContentForElementor\Plugin;

class FeaturesPage {
	private $tabs;

	public function __construct() {
		$this->tabs = $this->init_tabs();
	}

	public function init_tabs() {
		$tabs = [
			new class('widgets') extends GroupedListTab {
				public function get_label() {
					return __( 'Widgets', 'dynamic-content-for-elementor' );
				}
				public function get_groups_key() {
					return 'category';
				}
				public function get_groups() {
					return \DynamicContentForElementor\Features::get_widgets_groups();
				}
				public function should_calculate_usage() {
					return true;
				}
				public function calculate_usage( $feature, $elementor_controls_usage ) {
					$calculate_usage = 0;
					foreach ( $elementor_controls_usage as $key => $value ) {
						if ( isset( $elementor_controls_usage[ $key ][ $feature ] ) ) {
							$calculate_usage += $elementor_controls_usage[ $key ][ $feature ]['count'];
						}
					}
					return $calculate_usage;
				}
				public function get_all_tab_features() {
					$features = Plugin::instance()->features->filter( [ 'type' => 'widget' ] );
					// We want all extension that are not legacy
					$features = array_filter( $features, function( $f ) {
						return ! ( $f['legacy'] ?? false );
					});
					return $features;
				}
			},
			new class('extensions') extends GroupedListTab {
				public function get_label() {
					return __( 'Extensions', 'dynamic-content-for-elementor' );
				}
				public function get_groups() {
					return \DynamicContentForElementor\Features::get_extensions_groups();
				}
				public function get_groups_key() {
					return 'category';
				}
				public function get_all_tab_features() {
					$extensions = Plugin::instance()->features->filter( [ 'type' => 'extension' ] );
					// We want all extension that are not dynamic tag or legacy
					$extensions = array_filter( $extensions, function( $e ) {
						return ( $e['extension_type'] ?? '' ) !== 'dynamic-tag' && ! ( $e['legacy'] ?? false );
					});
					return $extensions;
				}
			},
			new class( 'dynamic-tags' ) extends GroupedListTab {
				public function get_label() {
					return __( 'Dynamic Tags', 'dynamic-content-for-elementor' );
				}
				public function get_groups() {
					return \DynamicContentForElementor\Features::get_dynamic_tags_groups();
				}
				public function get_groups_key() {
					return 'category';
				}
				public function get_all_tab_features() {
					$extensions = Plugin::instance()->features->filter( [ 'type' => 'extension' ] );
					$bundled = Plugin::instance()->features->filter_bundled( [ 'type' => 'extension' ] );
					$extensions += $bundled;
					// We want all extension that are dynamic tag and not legacy
					$extensions = array_filter( $extensions, function( $e ) {
						return ( $e['extension_type'] ?? '' ) === 'dynamic-tag' && ! ( $e['legacy'] ?? false );
					});
					return $extensions;
				}
			},
			new class( 'page-settings' ) extends ListTab {
				public function get_label() {
					return __( 'Page Settings', 'dynamic-content-for-elementor' );
				}
				public function get_all_tab_features() {
					$features = Plugin::instance()->features->filter( [ 'type' => 'page-setting' ] );
					$features = array_filter( $features, function( $f ) {
						return ! ( $f['legacy'] ?? false );
					});
					return $features;
				}
			},
			new class( 'global-settings' ) extends ListTab {
				public function get_label() {
					return __( 'Global Settings', 'dynamic-content-for-elementor' );
				}
				public function get_all_tab_features() {
					$features = Plugin::instance()->features->filter( [ 'type' => 'global-setting' ] );
					$features = array_filter( $features, function( $f ) {
						return ! ( $f['legacy'] ?? false );
					});
					return $features;
				}
			},
			new FrontendNavigator(),
			new class( 'legacy' ) extends GroupedListTab {
				public function should_display_count() {
					return false;
				}
				public function get_label() {
					return __( 'Legacy', 'dynamic-content-for-elementor' );
				}
				public function get_groups_key() {
					return 'type';
				}
				public function get_groups() {
					return [
						'widget' => __( 'Widgets', 'dynamic-content-for-elementor' ),
						'extension' => __( 'Extensions', 'dynamic-content-for-elementor' ),
						'global-setting' => __( 'Global Settings', 'dynamic-content-for-elementor' ),
					];
				}
				public function get_all_tab_features() {
					return Plugin::instance()->features->filter( [ 'legacy' => true ] );
				}
			},
		];
		$tabs_dict = [];
		foreach ( $tabs as $tab ) {
			$tabs_dict[ $tab->get_name() ] = $tab;
		}
		return $tabs_dict;
	}

	public function page_callback( $tplsys = false ) {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			if ( ! isset( $_POST['dce-settings-page'] )
				|| ! wp_verify_nonce( $_POST['dce-settings-page'], 'dce-settings-page' )
			) {
				wp_die( __( 'Nonce verification error.', 'dynamic-content-for-elementor' ) );
			}
		}

		// add error/update messages
		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'dce_messages', 'dce_message', __( 'Settings Saved', 'dynamic-content-for-elementor' ), 'updated' );
		}

		Plugin::instance()->assets::enqueue_dce_icons();

		// show error/update messages
		settings_errors( 'dce_messages' );
		$admin_page_title = esc_html( get_admin_page_title() );
		echo '<div class="wrap">';
		echo "<h1>$admin_page_title</h1>";
		echo '<div id="dce-settings-tabs-wrapper" class="nav-tab-wrapper">';
		$active_tab_name = $_GET['tab'] ?? 'widgets';
		foreach ( $this->tabs as $tab ) {
			$tab_class = '';
			if ( $active_tab_name === $tab->get_name() ) {
				$tab_class = 'nav-tab-active';
			}
			$tab_name = $tab->get_name();
			echo "<a class='nav-tab $tab_class' href='?page=dce-features&tab={$tab_name}'>";
			echo $tab->get_label();
			if ( $tab->should_display_count() ) {
				$count = $tab->get_count();
				echo "&nbsp;<span class='dce-badge'>{$count}</span>";
			}
			echo '</a>';
		}
		echo '</div>';
		echo '<div class="dce-container" style="display: block;">';
		$active_tab = $this->tabs[ $active_tab_name ];
		$active_tab->render();
		echo '</div></div>';
	}
}
