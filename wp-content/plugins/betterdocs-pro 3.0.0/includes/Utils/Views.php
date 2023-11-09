<?php
namespace WPDeveloper\BetterDocsPro\Utils;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use Exception;
use WPDeveloper\BetterDocs\Utils\Views as FreeViews;
use WPDeveloper\BetterDocs\Dependencies\DI\Container;

class Views extends FreeViews {
    private $pro_layout_directory;
    private $pro_views_path;
    private $pro_layouts_directory;

    public function __construct( $path, Container $container, $layout_directory = 'layouts/', $pro_path = null ) {
        parent::__construct( $path, $container, $layout_directory );

        if ( $pro_path === null ) {
            throw new Exception( 'Arguments #4 for ' . __CLASS__ . ' cannot be empty or null.' );
        }

        $this->pro_views_path        = $pro_path;
        $this->pro_layouts_directory = $this->pro_views_path . $layout_directory;
    }

    public function get_layouts( $_local_dir = 'category-grid', $is_pro = false ) {
        $_origin_layouts = parent::get_layouts( $_local_dir, true );

        $dir = $this->pro_layouts_directory . $_local_dir;
        if ( ! is_dir( $dir ) ) {
            return $_origin_layouts;
        }

        $_pro_layouts = $this->normalize_scandir( $dir );
        return array_merge( $_origin_layouts, $_pro_layouts );
    }

    public function path( $name, $default = '' ) {
        $name      = str_replace( $this->path, '', $name );
        $name      = str_replace( $this->pro_views_path, '', $name );
        $name      = str_replace( '.php', '', $name );
        $_filename = $this->path . $name . '.php';

        if ( ! file_exists( $_filename ) ) {
            $_filename = $this->pro_views_path . $name . '.php';
        }

        if( ! file_exists( $_filename ) ) {
            $_filename = $this->path . $default . '.php';
        }

        if ( file_exists( $_filename ) ) {
            $this->_view_type = 'pro';
            return $_filename;
        }
    }
}
