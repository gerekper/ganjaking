<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.2
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement Status Tab on plugin settings page.
 *
 * @since 1.0.0
 *
 **/
final class TabStatus extends Tab {

    /**
     * Slug of current tab.
     *
     * @const TAB_SLUG
     **/
    const TAB_SLUG = 'status';

	/**
	 * The one true StatusTab.
	 *
     * @var TabStatus
	 **/
	private static $instance;

	/**
	 * Generate Status Tab.
	 *
	 * @access public
     * @return void
	 **/
	public function add_settings() {

		/** Status Tab. */
        $this->add_settings_base( self::TAB_SLUG );

	}

    /**
     * Render form with all settings fields.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function do_settings() {

        /** No status tab, nothing to do. */
        if ( ! $this->is_enabled( self::TAB_SLUG ) ) { return; }

        /** Render title. */
        $this->render_title( self::TAB_SLUG );

        /** Render fields. */
        $this->do_settings_base( self::TAB_SLUG );

        /** Render Reports. */
        $this->render_reports();

        /** Render Privacy Notice. */
        $this->render_privacy_notice();

    }

	/**
	 * Render "System Requirements" field.
	 *
     * @since 1.0.0
	 * @access public
     *
     * @return void
	 **/
	public function render_reports() {

	    $tabs = Plugin::get_tabs();
        if ( ! isset( $tabs[ self::TAB_SLUG ][ 'reports' ] ) ) { return; }

        $reports = [];

        /** Server. */
        if ( $tabs[ self::TAB_SLUG ][ 'reports' ][ 'server' ][ 'enabled' ] ) {
            $reports[ 'server' ] = ReporterServer::get_instance();
        }

        /** WordPress. */
        if ( $tabs[ self::TAB_SLUG ][ 'reports' ][ 'wordpress' ][ 'enabled' ] ) {
            $reports[ 'wordpress' ] = ReporterWordPress::get_instance();
        }

        /** Plugins. */
        if ( $tabs[ self::TAB_SLUG ][ 'reports' ][ 'plugins' ][ 'enabled' ] ) {
            $reports[ 'plugins' ] = ReporterPlugins::get_instance();
        }

        /** Theme. */
        if ( $tabs[ self::TAB_SLUG ][ 'reports' ][ 'theme' ][ 'enabled' ] ) {
            $reports[ 'theme' ] = ReporterTheme::get_instance();
        }

		?>
		<div class="mdc-system-requirements">

			<?php foreach ( $reports as $key => $report ) : ?>
                <div class="mdp-status-<?php esc_attr_e( $key ); ?>">
                    <table class="mdc-system-requirements-table">
                        <thead>
                            <tr>
                                <th colspan="2"><?php esc_html_e( $report->get_title() ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ( $report->get_report() as $row ) {

                            if ( is_array( $row['value'] ) ) {

                                $this->render_array( $row['value'] );

                            } else {

                                $this->render_string( $row );

                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>

		</div>
        <?php

	}

    /**
     * Render row if value is string.
     *
     * @param array $row - Report row.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
	private function render_string( $row ) {
	    ?>
        <tr>
            <td><?php esc_html_e( $row['label'] ); ?>:</td>
            <td><span class="mdc-system-value"><?php echo wp_kses_post( $row['value'] ); ?></span></td>
            <th class="mdc-text-left">
                <?php if ( isset( $row['warning'] ) && $row['warning'] ) : ?>
                    <i class="material-icons mdc-system-warn">warning</i>
                    <?php isset( $row['recommendation'] ) ? esc_html_e( $row['recommendation'] ) : ''; ?>
                <?php endif; ?>
            </th>
        </tr>
        <?php
    }

    /**
     * Render row if value is array.
     *
     * @param array $value - Report row value.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     **/
    private function render_array( $value ) {

        foreach ( $value as $plugin_info ): ?>
            <tr>
                <td>
                    <?php
                    if ( $plugin_info['PluginURI'] ) {
                        echo "<a href='" . esc_url( $plugin_info['PluginURI'] ) . "'>" . esc_html__( $plugin_info['Name'] ) . "</a>";
                    } else {
                        esc_html_e( $plugin_info['Name'] );
                    }

                    if ( $plugin_info['Version'] ) { echo ' - ' . esc_html__( $plugin_info['Version'] ); }
                    ?>
                </td>
                <td>
                    <?php
                    if ( $plugin_info['Author'] ) {

                        echo "By ";

                        if ( $plugin_info['AuthorURI'] ) {
                            echo "<a href='" . esc_url( $plugin_info['AuthorURI'] ) . "'>" . esc_html__( $plugin_info['Author'] ) . "</a>";
                        } else {
                            esc_html_e( $plugin_info['Author'] );
                        }
                    }
                    ?>
                </td>
            </tr>
        <?php
        endforeach;

    }

	/**
	 * Render Privacy Notice.
	 *
	 * @access public
     * @return void
     **/
	public function render_privacy_notice() {

	    ?>
        <div class="mdc-text-field-helper-line">
            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--persistent"><?php
                esc_html_e( 'Some data will be sent to our server to verify purchase and to ensure that a plugin is compatible with your install. We will never collect any confidential data. All data is stored anonymously.', 'ungrabber' ); ?>
            </div>
        </div>
        <?php

    }

	/**
	 * Main StatusTab Instance.
	 * Insures that only one instance of StatusTab exists in memory at any one time.
	 *
	 * @static
     * @return TabStatus
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
