<?php
/**
 * General Setting Form
 *
 * @package UAEL
 */

use UltimateElementor\Classes\UAEL_Helper;

$branding      = UAEL_Helper::get_white_labels();
$widgets       = UAEL_Helper::get_widget_options();
$hide_branding = UAEL_Helper::is_hide_branding();

$kb_data   = UAEL_Helper::knowledgebase_data();
$enable_kb = $kb_data['enable_knowledgebase'];
$kb_url    = $kb_data['knowledgebase_url'];

$support_data   = UAEL_Helper::support_data();
$enable_support = $support_data['enable_support'];
$support_url    = $support_data['support_url'];

$enable_beta = ( isset( $branding['enable_beta_box'] ) && 'disable' === $branding['enable_beta_box'] ) ? false : true;
$allow_beta  = UAEL_Helper::get_admin_settings_option( '_uael_beta', 'disable' );

$post_skins = UAEL_Helper::get_post_skin_options();

?>

<div class="uael-container uael-general <?php echo ( ! $enable_kb && ! $enable_support && ! $enable_beta ) ? 'uael-hide-branding' : ''; ?>">
<div id="poststuff">
	<div id="post-body" class="columns-2">
		<div id="post-body-content">
			<!-- All WordPress Notices below header -->
			<h1 class="screen-reader-text"> <?php esc_html_e( 'General', 'uael' ); ?> </h1>
				<div class="widgets postbox">
					<h2 class="hndle uael-flex uael-settings-widgets-heading">
						<span><?php esc_html_e( 'Filters: ', 'uael' ); ?></span>
						<ul class="uael-widget-filters">
							<li class="filter-active">
								<label for="uael-filter-1"><?php esc_html_e( 'All', 'uael' ); ?></label>
								<input type="radio" id="uael-filter-1" class="uael-filter-tab " data-category="all"/>
							</li>
							<li>
								<label for="uael-filter-2"><?php esc_html_e( 'Features', 'uael' ); ?></label>
								<input type="radio" id="uael-filter-2" class="uael-filter-tab" data-category="feature"/>
							</li>
							<li>
								<label for="uael-filter-3"><?php esc_html_e( 'Content', 'uael' ); ?></label>
								<input type="radio" id="uael-filter-3" class="uael-filter-tab" data-category="content"/>
							</li>
							<li>
								<label for="uael-filter-4"><?php esc_html_e( 'Creative', 'uael' ); ?></label>
								<input type="radio" id="uael-filter-4" class="uael-filter-tab" data-category="creative"/>
							</li>
							<li>
								<label for="uael-filter-5"><?php esc_html_e( 'Form', 'uael' ); ?></label>
								<input type="radio" id="uael-filter-5" class="uael-filter-tab" data-category="form"/>
							</li>
							<li>
								<label for="uael-filter-6"><?php esc_html_e( 'SEO', 'uael' ); ?></label>
								<input type="radio" id="uael-filter-6" class="uael-filter-tab" data-category="seo"/>
							</li>
							<li>
								<label for="uael-filter-7"><?php esc_html_e( 'Woo', 'uael' ); ?></label>
								<input type="radio" id="uael-filter-7" class="uael-filter-tab" data-category="woo"/>
							</li>
							<li>
								<label for="uael-filter-8"><?php esc_html_e( 'Extensions', 'uael' ); ?></label>
								<input type="radio" id="uael-filter-8" class="uael-filter-tab" data-category="extension"/>
							</li>
						</ul>
						<div class="uael-bulk-actions-wrap">
							<a class="bulk-action uael-activate-all button"> <?php esc_html_e( 'Activate All', 'uael' ); ?> </a>
							<a class="bulk-action uael-deactivate-all button"> <?php esc_html_e( 'Deactivate All', 'uael' ); ?> </a>
						</div>
					</h2>
						<div class="uael-list-section">
							<?php
							if ( is_array( $widgets ) && ! empty( $widgets ) ) :
								?>
								<ul class="uael-widget-list uael-option-type-widget">
									<?php
									foreach ( $widgets as $addon => $info ) {
										$doc_url       = ( isset( $info['doc_url'] ) && ! empty( $info['doc_url'] ) ) ? ' href="' . esc_url( $info['doc_url'] ) . '"' : '';
										$anchor_target = ( isset( $info['doc_url'] ) && ! empty( $info['doc_url'] ) ) ? ' target=_blank rel=noopener' : '';
										$class         = 'deactivate';
										$widget_link   = array(
											'link_class' => 'uael-activate-widget',
											'link_text'  => __( 'Activate', 'uael' ),
										);

										if ( $info['is_activate'] ) {
											$class       = 'activate';
											$widget_link = array(
												'link_class' => 'uael-deactivate-widget',
												'link_text'  => __( 'Deactivate', 'uael' ),
											);
										}
										switch ( $info['slug'] ) {
											case 'uael-white-label':
												$class       = $info['slug'];
												$widget_link = array(
													'link_url'   => admin_url( 'options-general.php' ),
												);
												$link_url    = add_query_arg(
													array(
														'page' => UAEL_SLUG,
														'action' => 'branding',
													),
													$widget_link['link_url']
												);
												$widget_link = array(
													'link_class' => 'uael-white-label-module',
													'link_text'  => __( 'Settings', 'uael' ),
													'link_url'   => $link_url,
												);
												break;
										}

										$category = isset( $info['category'] ) ? $info['category'] : '';

										$widget_name_html = '<li id="' . esc_attr( $addon ) . '"  class="filter-item-active ' . esc_attr( $class ) . '" data-category="' . esc_attr( $category ) . '"><div class="uael-widget-title">' . esc_html( $info['title'] ) . '</div>';

										echo wp_kses_post( $widget_name_html );

										if ( 'White_Label' !== $addon ) {
											printf(
												'<label class="uael-switch"><input type="checkbox" class="%1$s" %2$s><span class="uael-slider uael-round"/></label>',
												esc_attr( $widget_link['link_class'] ),
												$info['is_activate'] ? esc_attr( 'checked' ) : ''
											);
										}

										printf(
											'<div class="uael-widget-link-wrapper"><a class="uael-widget-doc-link" href="%1$s" %2$s>%3$s</a>',
											( isset( $info['doc_url'] ) && ! empty( $info['doc_url'] ) ) ? esc_url( $info['doc_url'] ) : '',
											esc_attr( $anchor_target ),
											esc_html__( 'Docs', 'uael' )
										);

										if ( isset( $info['setting_url'] ) ) {

											printf(
												'<a href="%1$s" class="%2$s"> %3$s </a>',
												esc_url( $info['setting_url'] ),
												esc_attr( 'uael-advanced-settings' ),
												esc_html( $info['setting_text'] )
											);
										}

										echo '</div></li>';
									}
									?>
								</ul>
							<?php endif; ?>
						</div>
				</div>
		</div>
		<?php if ( $enable_kb || $enable_support || $enable_beta ) { ?>
			<div class="postbox-container uael-sidebar" id="postbox-container-1">
				<div id="side-sortables">
					<?php if ( $enable_kb ) { ?>
						<div class="postbox">
							<h2 class="hndle uael-normal-cusror">
								<span class="dashicons dashicons-book"></span>
								<span><?php esc_html_e( 'Knowledge Base', 'uael' ); ?></span>
							</h2>
							<div class="inside">
								<p>
									<?php esc_html_e( 'Not sure how something works? Take a peek at the knowledge base and learn.', 'uael' ); ?>
								</p>
								<a href='<?php echo esc_url( $kb_url ); ?> ' target="_blank" rel="noopener"><?php esc_html_e( 'Visit Knowledge Base »', 'uael' ); ?></a>
							</div>
						</div>
					<?php } ?>
					<?php if ( $enable_support ) { ?>
						<div class="postbox">
							<h2 class="hndle uael-normal-cusror">
								<span class="dashicons dashicons-sos"></span>
								<span><?php esc_html_e( 'Five Star Support', 'uael' ); ?></span>
							</h2>
							<div class="inside">
								<p>
									<?php
									printf(
										/* translators: %1$s: uael name. */
										esc_html__( 'Got a question? Get in touch with %1$s developers. We\'re happy to help!', 'uael' ),
										wp_kses_post( UAEL_PLUGIN_NAME )
									);
									?>
								</p>
								<?php
									$uael_support_link      = apply_filters( 'uael_support_link', $support_url );
									$uael_support_link_text = apply_filters( 'uael_support_link_text', __( 'Submit a Ticket »', 'uael' ) );

									printf(
										/* translators: %1$s: uael support link. */
										'%1$s',
										! empty( $uael_support_link ) ? '<a href=' . esc_url( $uael_support_link ) . ' target="_blank" rel="noopener">' . esc_html( $uael_support_link_text ) . '</a>' :
										esc_html( $uael_support_link_text )
									);
								?>
							</div>
						</div>
					<?php } ?>
					<?php if ( $enable_beta ) { ?>
						<div class="postbox">
							<h2 class="hndle uael-normal-cusror">
								<span class="dashicons dashicons-update"></span>
								<span><?php esc_html_e( 'Allow Beta Updates', 'uael' ); ?></span>
							</h2>
							<div class="inside">
								<p>
									<?php
									esc_html_e( 'Enable this option to receive update notifications for beta versions.', 'uael' );
									?>
								</p>
								<p class="admin-help uael-p">
								<?php
									$a_tag_open  = '<a target="_blank" rel="noopener" href="' . esc_url( UAEL_DOMAIN . 'docs/enabling-automatic-beta-updates-for-uael/?utm_source=uael-pro-dashboard&utm_medium=uael-menu-page&utm_campaign=uael-pro-plugin' ) . '">';
									$a_tag_close = '</a>';

									printf(
										/* translators: %1$s: a tag open. */
										esc_attr__( 'Please read %1$s this article %2$s to know more.', 'uael' ),
										wp_kses_post( $a_tag_open ),
										wp_kses_post( $a_tag_close )
									);
								?>
								<p>
								<label for="uael-gen-enable-beta-update">
									<?php

									if ( 'disable' === $allow_beta ) {
										$beta_string = __( 'Enable Beta Updates', 'uael' );
									} else {
										$beta_string = __( 'Disable Beta Updates', 'uael' );
									}
									?>
									<button class="button uael-button-spinner" id="uael-gen-enable-beta-update" data-value="<?php echo esc_attr( $allow_beta ); ?>"><?php echo esc_html( $beta_string ); ?></button>
								</label>
								</p>
							</div>
						</div>
					<?php } ?>
					<?php
					if ( bsf_display_rollback_version_form( 'uael' ) ) {
						?>
						<div class="postbox">
							<h2 class="hndle uael-normal-cusror">
								<span><?php esc_html_e( 'Version Control', 'uael' ); ?></span>
							</h2>
							<div class="inside">
							<?php
								$product_id = 'uael';
								bsf_get_version_rollback_form( $product_id );
							?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<!-- /post-body -->
	<br class="clear">
</div>
</div>
