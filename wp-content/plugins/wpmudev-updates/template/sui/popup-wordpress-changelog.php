<?php
/**
 * Dashboard popup template: Project changelog
 *
 * Displays the changelog of a specific project.
 *
 * Following variables are passed into the template:
 *   $pid (project ID)
 *
 * @since  4.0.5
 * @package WPMUDEV_Dashboard
 */

$item = WPMUDEV_Dashboard::$site->get_project_infos( $pid, true );

if ( ! $item || ! is_object( $item ) ) {
	return;
}

$dlg_id = 'dlg-' . md5( time() . '-' . $pid );

?>
<div id="content" class="<?php echo esc_attr( $dlg_id ); ?>">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Condensed%3A400%2C700%7CRoboto%3A400%2C500%2C300%2C300italic%2C100" type="text/css" media="all" />
<style>
	* {
		box-sizing: border-box;
		-moz-box-sizing: border-box;
	}
	html, body {
		margin: 0;
		padding: 0;
		height: 100%;
		font-family: 'Roboto', 'Helvetica Neue', Helvetica, sans-serif;
		font-size: 15px;
	}
	h1, h2, h3, h4 {
		font-family: 'Roboto Condensed', 'Roboto', 'Helvetica Neue', Helvetica, sans-serif;
		font-weight: 700;
		color: #777771;
	}
	h1 {
		font-size: 3em;
	}
	p {
		font-size: 1.2em;
		font-weight: 300;
		color: #777771;
	}
	a {
		color: #19b4cf;
		text-decoration: none;
	}
	a:hover,
	a:focus,
	a:active {
		color: #387ac1;
	}
	#content {
		min-height: 100%;
		text-align: center;
		background: #FFF;
		position: absolute;
		left: 0;
		top: 0;
		right: 0;
		bottom: 0;
		overflow: auto;
	}
	#content .excerpt {
		width: 100%;
		background-color: #14485F;
		padding: 10px;
		color: #FFF;
	}
	#content .excerpt h1 {
		margin: 30px;
		color: #FFF;
		font-weight: 100;
	}
	#content .versions h4 {
		font-size: 15px;
		text-transform: uppercase;
		text-align: left;
		padding: 0 0 15px;
		font-weight: bold;
		line-height: 20px;
	}
	#content .excerpt a {
		float: left;
		margin-right: 40px;
		text-decoration: none;
		color: #6ECEDE;
	}
	#content .excerpt a:hover,
	#content .excerpt a:focus,
	#content .excerpt a:active {
		color: #C7F7FF;
	}
	#content .footer {
		background-color: #0B2F3F;
		padding: 20px 0;
		margin: 0;
		position: relative;
	}
	#content .footer p {
		color: #FFF;
		margin: 10px 0;
		padding: 0;
		font-size: 15px;
	}
	#content .information {
		padding: 0;
		text-align: left;
	}
	#content .versions > li {
		border-bottom: 1px solid #E5E5E5;
		padding: 40px;
		margin: 0;
	}
	#content .versions > li.new {
		background: #fffff6;
	}
	#content .information .current-version,
	#content .information .new-version {
		border-radius: 5px;
		color: #FFF;
		cursor: default;
		display: inline-block;
		position: relative;
		top: -2px;
		margin: 0 0 0 10px;
		padding: 1px 5px;
		font-size: 10px;
		line-height: 20px;
		height: 20px;
		box-sizing: border-box;
	}
	#content .information .new-version {
		background: #FDCE43;
		text-shadow: 0 1px 1px #DDAE30;
	}
	#content .current-version {
		background: #00ACCA;
		text-shadow: 0 1px 1px #008CAA;
	}
	#content .versions {
		margin: 0;
		padding: 0;
	}
	#content .versions .changes {
		list-style: disc;
		padding: 0 0 0 20px;
		margin: 0;
	}
	#content .versions .changes li {
		padding: 3px 0 3px 20px;
		margin: 0;
		color: #777771;
		cursor: default;
	}
	#content .version-meta {
		float: right;
		text-align: right;
	}
</style>

	<div class="excerpt">
		<h1><?php printf( esc_attr__( '%s changelog', 'wpmudev' ), esc_html( $item->name ) ); ?></h1>
	</div>

	<div class="information">

	<ul class="versions">
	<?php
	foreach ( $item->changelog as $log ) {
		$row_class = '';
		$badge = '';

		if ( ! is_array( $log ) ) { continue; }
		if ( empty( $log ) ) { continue; }

		if ( $item->is_installed ) {
			// -1 .. local is higher (dev) | 0 .. equal | 1 .. new version available
			$version_check = version_compare( $log['version'], $item->version_installed );

			if ( $item->version_installed && 1 === $version_check ) {
				$row_class = 'new';
			}

			if ( $item->version_installed ) {
				if ( 0 === $version_check ) {
					$badge = sprintf(
						'<div class="current-version">%s %s</div>',
						'<i aria-hidden="true" class="wdv-icon wdv-icon-ok"></i>',
						__( 'Current', 'wpmudev' )
					);
				} elseif ( 1 === $version_check ) {
					$badge = sprintf(
						'<div class="new-version">%s %s</div>',
						'<i aria-hidden="true" class="wdv-icon wdv-icon-star"></i>',
						__( 'New', 'wpmudev' )
					);
				}
			}
		}

		$version = $log['version'];

		if ( empty( $log['time'] ) ) {
			$rel_date = '';
		} else {
			$rel_date = date_i18n( get_option( 'date_format' ), $log['time'] );
		}

		printf(
			'<li class="%1$s"><h4>%2$s %3$s <small class="version-meta">%4$s</small></h4>',
			esc_attr( $row_class ),
			sprintf(
				esc_html__( 'Version %s', 'wpmudev' ), esc_html( $version )
			),
			wp_kses_post( $badge ),
			esc_html( $rel_date )
		);

		$notes = explode( "\n", $log['log'] );
		$detail_level = 0;
		$detail_class = 'intro';

		echo '<ul class="changes">';
		foreach ( $notes as $note ) {
			if ( 0 === strpos( $note, '<p>' ) ) {
				if ( 1 == $detail_level ) {
					printf(
						'<li class="toggle-details">
						<a role="button" href="#" class="for-intro">%s</a><a href="#" class="for-detail">%s</a>
						</li>',
						esc_html__( 'Show all changes', 'wpmudev' ),
						esc_html__( 'Hide details', 'wpmudev' )
					);
					$detail_class = 'detail';
				}
				$detail_level += 1;
			}

			$note = stripslashes( $note );
			$note = preg_replace( '/(<br ?\/?>|<p>|<\/p>)/', '', $note );
			$note = trim( preg_replace( '/^\s*(\*|\-)\s*/', '', $note ) );
			$note = str_replace( array( '<', '>' ), array( '&lt;', '&gt;' ), $note );
			$note = preg_replace( '/`(.*?)`/', '<code>\1</code>', $note );
			if ( empty( $note ) ) { continue; }

			printf(
				'<li class="version-%s">%s</li>',
				esc_attr( $detail_class ),
				wp_kses_post( $note )
			);
		}
		echo '</ul></li>';
	}
	?>
	</ul>
	</div>

	<div class="footer">
		<p>Copyright 2009 - <?php echo esc_html( date( 'Y' ) ); ?> WPMU DEV</p>
	</div>

	<style>
	.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes .for-detail,
	.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes .version-detail {
		display: none;
	}
	.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes .for-intro {
		display: inline-block;
	}
	.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes.show-details .for-intro {
		display: none;
	}
	.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes.show-details .for-detail {
		display: inline-block;
	}
	.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes.show-details .version-detail {
		display: list-item;
	}
	.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes .toggle-details {
		padding: 8px 0 4px;
		text-align: right;
		font-size: 12px;
		list-style: none;
	}
	</style>
</div>
