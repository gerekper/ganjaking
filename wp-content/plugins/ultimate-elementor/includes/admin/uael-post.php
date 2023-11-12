<?php
/**
 * Posts Setting Form
 *
 * @package UAEL
 */

use UltimateElementor\Classes\UAEL_Helper;

$post_skins = UAEL_Helper::get_post_skin_options();

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="uael-container uael-general uael-post">
<div id="poststuff">
	<div id="post-body" class="columns-2">
		<div id="post-body-content">
			<!-- All WordPress Notices below header -->
			<h1 class="screen-reader-text"> <?php esc_html_e( 'Post', 'uael' ); ?> </h1>
				<div class="widgets postbox">
					<h2 class="hndle uael-flex uael-settings-widgets-heading"><span><?php esc_html_e( 'Post Widget Skins', 'uael' ); ?></span>
						<div class="uael-bulk-actions-wrap">
							<a class="bulk-action uael-activate-skins-all button"> <?php esc_html_e( 'Activate All', 'uael' ); ?> </a>
							<a class="bulk-action uael-deactivate-skins-all button"> <?php esc_html_e( 'Deactivate All', 'uael' ); ?> </a>
						</div>
					</h2>
						<div class="uael-list-section">
							<?php
							if ( is_array( $post_skins ) && ! empty( $post_skins ) ) :
								?>
								<ul class="uael-widget-list uael-option-type-skin">
									<?php
									foreach ( $post_skins as $skin => $info ) {
										$class     = 'deactivate';
										$skin_link = array(
											'link_class' => 'uael-activate-widget',
											'link_text'  => __( 'Activate', 'uael' ),
										);
										if ( $info['is_activate'] ) {
											$class     = 'activate';
											$skin_link = array(
												'link_class' => 'uael-deactivate-widget',
												'link_text'  => __( 'Deactivate', 'uael' ),
											);
										}

										echo '<li id="' . esc_attr( $skin ) . '"  class="' . esc_attr( $class ) . '"><a class="uael-widget-title">' . esc_html( $info['title'] ) . '</a><div class="uael-widget-link-wrapper">';

										printf(
											'<label class="uael-switch"><input type="checkbox" class="%1$s" %2$s><span class="uael-slider uael-round"/></label>',
											esc_attr( $skin_link['link_class'] ),
											$info['is_activate'] ? esc_attr( 'checked' ) : '',
										);

										if ( $info['is_activate'] && isset( $info['setting_url'] ) ) {

											printf(
												'<a href="%1$s" class="%2$s"> %3$s </a>',
												esc_url( $info['setting_url'] ),
												esc_attr( 'uael-advanced-settings' ),
												esc_html( $info['setting_text'] )
											);
										}

										echo '</div><div class="uael-post-skin-image"><img src="' . esc_url( $info['image'] ) . '""></div></li>';
									}
									?>
								</ul>
							<?php endif; ?>
						</div>
				</div>
		</div>
	</div>
	<!-- /post-body -->
	<br class="clear">
</div>
</div>
