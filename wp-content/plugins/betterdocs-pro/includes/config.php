<?php
use WPDeveloper\BetterDocsPro\Utils\Views;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use WPDeveloper\BetterDocs\Editors\Editor;
use WPDeveloper\BetterDocs\Utils\Database;
use WPDeveloper\BetterDocsPro\Core\Request;
use WPDeveloper\BetterDocsPro\Core\Rewrite;
use WPDeveloper\BetterDocsPro\Core\Settings;
use WPDeveloper\BetterDocsPro\Utils\Enqueue;
use WPDeveloper\BetterDocsPro\Utils\BlockTemplate;
use WPDeveloper\BetterDocs\Utils\BlockTemplate as BlockTemplateFree;
use WPDeveloper\BetterDocsPro\Admin\Analytics;
use WPDeveloper\BetterDocsPro\Admin\ReportEmail;
use WPDeveloper\BetterDocsPro\Editors\Elementor;
use WPDeveloper\BetterDocsPro\Editors\BlockEditor;
use WPDeveloper\BetterDocsPro\Core\Query as Query;
use WPDeveloper\BetterDocs\Core\Query as FreeQuery;
use WPDeveloper\BetterDocs\Utils\Views as FreeViews;
use WPDeveloper\BetterDocs\Core\Request as FreeRequest;
use WPDeveloper\BetterDocs\Core\Rewrite as FreeRewrite;
use WPDeveloper\BetterDocsPro\Admin\Customizer\Defaults;
use WPDeveloper\BetterDocs\Core\Settings as FreeSettings;
use WPDeveloper\BetterDocs\Admin\Analytics as FreeAnalytics;
use WPDeveloper\BetterDocs\Editors\Elementor as FreeElementor;
use \WPDeveloper\BetterDocs\Admin\ReportEmail as FreeReportEmail;
use WPDeveloper\BetterDocs\Editors\BlockEditor as FreeBlockEditor;
use WPDeveloper\BetterDocsPro\Admin\Customizer\Sections\FaqBuilder;
use WPDeveloper\BetterDocs\Admin\Customizer\Defaults as FreeDefaults;
use WPDeveloper\BetterDocs\Admin\Customizer\Sections\FaqBuilder as FreeFaqBuilder;
use WPDeveloper\BetterDocs\Core\Scripts as FreeScripts;
use WPDeveloper\BetterDocsPro\Core\Scripts;
use WPDeveloper\BetterDocsPro\Editors\BlockEditor\Blocks\MultipleKB;

return [
    Enqueue::class         => new Enqueue( BETTERDOCS_PRO_ABSURL, BETTERDOCS_PRO_ABSPATH, BETTERDOCS_PRO_VERSION ),
    FreeViews::class       => function ( $container ) {
        return new Views( BETTERDOCS_ABSPATH . 'views/', $container, 'layouts/', BETTERDOCS_PRO_ABSPATH . 'views/' );
    },
    FreeScripts::class       => function ( $container ) {
        return $container->get( Scripts::class );
    },
    FreeDefaults::class    => function ( $container ) {
        return new Defaults( $container->get( Database::class ) );
    },
    FreeSettings::class    => function ( $container ) {
        return new Settings( $container->get( Database::class ) );
    },
    FreeRequest::class     => function ( $container ) {
        return $container->get( Request::class );
    },
    FreeRewrite::class     => function ( $container ) {
        return $container->get( Rewrite::class );
    },
    FreeAnalytics::class   => function ( $container ) {
        return $container->get( Analytics::class );
    },
    FreeFaqBuilder::class  => function ( $container ) {
        return $container->get( FaqBuilder::class );
    },
    FreeQuery::class       => function ( $container ) {
        return $container->get( Query::class );
    },
    FreeReportEmail::class => function ( $container ) {
        return $container->get( ReportEmail::class );
    },
    BlockTemplateFree::class => function ( $container ) {
        return $container->get( BlockTemplate::class );
    },
    Editor::class          => function ( $container ) {
        /**
         * Commented FREE Elementor Editor Initialization for Multiple Calling, some warning elementor showing
         */
        return new Editor( $container, [
            // 'elementor'    => FreeElementor::class,
            'blockEditor'  => BlockEditor::class,
            'elementor' => Elementor::class
        ] );
    }
];
