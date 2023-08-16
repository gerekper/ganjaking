<?php

namespace WPDeveloper\BetterDocsPro\Admin\Customizer;

use WPDeveloper\BetterDocs\Utils\Base;
use WPDeveloper\BetterDocsPro\Admin\Customizer\Defaults;
use WPDeveloper\BetterDocsPro\Admin\Customizer\Sections\Sidebar;
use WPDeveloper\BetterDocsPro\Admin\Customizer\Sections\DocsPage;
use WPDeveloper\BetterDocsPro\Admin\Customizer\Sections\LiveSearch;
use WPDeveloper\BetterDocsPro\Admin\Customizer\Sections\MultipleKB;
use WPDeveloper\BetterDocsPro\Admin\Customizer\Sections\ArchivePage;

class Customizer extends Base {
    /**
     * Defaults
     *
     * @var Defaults
     */
    public $defaults;

    /**
     * Enqueue
     *
     * @var \WPDeveloper\BetterDocsPro\Utils\Enqueue
     */
    private $assets;

    public function __construct( Defaults $defaults ) {
        $this->defaults = $defaults;
        $this->assets   = betterdocs_pro()->assets;

        $this->remove_pro_badge();
        add_action( 'customize_controls_enqueue_scripts', [$this, 'enqueue'] );
        add_filter( 'betterdocs_customizer_settings', [$this, 'customer_settings'] );
        add_action( 'customize_preview_init', [$this, 'customize_preview_init_pro'] );
        add_action( 'wp_head', [$this, 'dynamic_css'] );
    }

    public function remove_pro_badge() {
        add_filter( 'betterdocs_docs_layout_select_choices', [$this, 'remove_pro'] );
        add_filter( 'betterdocs_archive_layout_choices', [$this, 'remove_pro'] );
        add_filter( 'betterdocs_single_layout_select_choices', [$this, 'remove_pro'] );
    }

    public function remove_pro( $options ) {
        $options = array_map( function ( $item ) {
            if ( isset( $item['pro'] ) ) {
                unset( $item['pro'] );
            }

            return $item;
        }, $options );

        return $options;
    }

    public function enqueue() {
        betterdocs_pro()->assets->enqueue( 'betterdocs-customize-condition-pro', 'customizer/js/customizer-condition.js' );
    }

    public function customize_preview_init_pro() {
        betterdocs_pro()->assets->enqueue( 'betterdocs-customizer-pro', 'customizer/js/customizer.js', ['customize-preview'] );
    }

    public function customer_settings( $_settings ) {
        $_new_settings = [];

        if ( betterdocs_pro()->multiple_kb->is_enable ) {
            $_new_settings[] = MultipleKB::class;
        }

        $_new_settings[] = DocsPage::class;
        $_new_settings[] = Sidebar::class;
        $_new_settings[] = ArchivePage::class;
        if ( betterdocs()->settings->get( 'advance_search' ) == 1 ) {
            $_new_settings[] = LiveSearch::class;
        }

        return array_merge( $_settings, $_new_settings );
    }

    public function dynamic_css() {
        /**
         * Don't remove this line, it's used in dynamic.css.php file.
         */
        $mods = $this->defaults->theme_mods();
        require __DIR__ . '/dynamic.css.php';

        ob_start();
        echo '<style type="text/css">';
        echo $css->get_output( true );
        echo '</style>';
        echo ob_get_clean();
    }
}
