<?php
namespace WPDeveloper\BetterDocsPro\Utils;

use WPDeveloper\BetterDocs\Utils\BlockTemplate as BlockTemplateFree;
use WPDeveloper\BetterDocsPro\Core\MultipleKB;
/**
 * Utility methods used for serving block templates from BetterDocs Blocks.
 * {@internal This class and its methods should only be used within the class-block-template-controller.php and is not intended for public use.}
 */
class BlockTemplate extends BlockTemplateFree {

    private $multipleKB;

    public function __construct( MultipleKB $multipleKB ) {
        $this->multipleKB = $multipleKB;
    }

    const ELIGIBLE_FOR_DOC_ARCHIVE_FALLBACK = [ 'taxonomy-knowledge_base', 'taxonomy-doc_category', 'taxonomy-doc_tag' ];

    public function get_templates_directory_pro( $template_type = 'wp_template' ) {
        return BETTERDOCS_PRO_FSE_TEMPLATES_PATH . DIRECTORY_SEPARATOR;
    }

    public function get_plugin_block_template_types() {
        $plugin_template_types = parent::get_plugin_block_template_types();

        if ( ! $this->multipleKB->is_enable ) {
            return $plugin_template_types;
        }

        $plugin_template_types['archive-docs'] = [
            'title'       => _x( 'Multiple KB', 'Template name', 'betterdocs-pro' ),
            'description' => __( 'Template used to display Knowledge Bases.', 'betterdocs-pro' )
        ];

        $plugin_template_types['taxonomy-knowledge_base'] = [
            'title'       => _x( 'Docs Page', 'Template name', 'betterdocs-pro' ),
            'description' => __( 'Template used to display Docs Page.', 'betterdocs-pro' )
        ];

        return $plugin_template_types;
    }

    public function get_templates_fils_from_betterdocs( $template_type ) {
        $free_template_files = parent::get_templates_fils_from_betterdocs( $template_type );

        if ( ! $this->multipleKB->is_enable ) {
            return $free_template_files;
        }
        unset($free_template_files[0]);
        $directory      = $this->get_templates_directory_pro( $template_type );
        $template_files = $this->get_template_paths( $directory );
        $template_files = array_merge($free_template_files, $template_files);
        return $template_files;
    }

    /**
     * Finds all nested template part file paths in a theme's directory.
     *
     * @param string $base_directory The theme's file path.
     * @return array $path_list A list of paths to all template part files.
     */
    public function get_template_paths( $base_directory ) {
        $path_list = parent::get_template_paths( $base_directory );
        if ( ! $this->multipleKB->is_enable ) {
            return $path_list;
        } else {
            unset($path_list[1]);
        }

        if ( file_exists( BETTERDOCS_PRO_FSE_TEMPLATES_PATH ) ) {
            $_pro_templates = [];
            $nested_files      = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( BETTERDOCS_PRO_FSE_TEMPLATES_PATH ) );
            $nested_html_files = new \RegexIterator( $nested_files, '/^.+\.html$/i', \RecursiveRegexIterator::GET_MATCH );
            foreach ( $nested_html_files as $path => $file ) {
                $_pro_templates[] = $path;
            }

            $path_list = array_merge( $_pro_templates, $path_list );
        }
        return $path_list;
    }

    public function convert_slug_to_title( $template_slug ) {
        $title = parent::convert_slug_to_title($template_slug);

        if ( ! $this->multipleKB->is_enable ) {
            return $title;
        }

        if ($template_slug === 'archive-docs') {
            return __('Multiple KB', 'betterdocs');
        }

        if ($template_slug === 'taxonomy-knowledge_base') {
            return __('Docs Page', 'betterdocs');
        }

        return $title;
    }


}
