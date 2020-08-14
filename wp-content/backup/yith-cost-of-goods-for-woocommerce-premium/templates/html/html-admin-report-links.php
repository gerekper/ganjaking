<?php
/**
 * Admin View: Page - Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

    $reports = YITH_COG_Report_Links::get_reports();
    $current_tab = 'reports';
    $current_report = 'by_date';

    if ( sizeof( $reports[ $current_tab ]['reports'] ) > 1 ) {

        ?>
        <ul class="subsubsub">
            <li><?php

                $links = array();

                foreach ( $reports[ $current_tab ]['reports'] as $key => $report ) {

                    $link = '<a href="admin.php?page=yith_cog_setting&tab=' . urlencode( $current_tab ) . '&amp;report=' . urlencode( $key ) . '" class="';

                    if ( $key == $current_report ) {
                        $link .= 'current';
                    }

                    $link .= '">' . $report['title'] . '</a>';

                    $links[] = $link;

                }

                echo implode( ' | </li><li>', $links );

                ?></li>
        </ul>
        <br class="clear" />
        <?php
    }

    if ( isset( $reports[ $current_tab ]['reports'][ $current_report ] ) ) {

        $report = $reports[ $current_tab ]['reports'][ $current_report ];

        if ( ! isset( $report['hide_title'] ) || true != $report['hide_title'] ) {
            echo '<h1>' . esc_html( $report['title'] ) . '</h1>';
        } else {
            echo '<h1 class="screen-reader-text">' . esc_html( $report['title'] ) . '</h1>';
        }

        if ( $report['description'] ) {
            echo '<p>' . $report['description'] . '</p>';
        }

        if ( $report['callback'] && ( is_callable( $report['callback'] ) ) ) {
            call_user_func( $report['callback'], $current_report );
        }
    }
    ?>
