<?php

/**
 * Dashboard analytics tab template
 */

defined('ABSPATH') || die();

$widgets =  ha_has_pro() ? self::get_widgets() : \Happy_Addons\Elementor\Widgets_Manager::get_local_widgets_map();
$inactive_widgets = \Happy_Addons\Elementor\Widgets_Manager::get_inactive_widgets();
$used_widget = self::get_raw_usage();
$unuse_widget = self::get_un_usage();

$total_widgets_count = count( $widgets );
$total_used_widget_count = count( $used_widget );
$total_unuse_widget_count = count( $unuse_widget );

$disable_btn = count( $unuse_widget ) == count( array_intersect( $unuse_widget, $inactive_widgets ) );
?>
<div class="ha-dashboard-panel ha-dashboard-panel-analytics">

	<?php if ( ! \Elementor\Tracker::is_allow_track() ) : ?>
		<div class="ha-dashboard-analytics-notice">
			<div class="ha-dashboard-panel__header flex-content used-widgets">
				<div class="ha-dashboard-panel__header-content">
					<h2><?php esc_html_e( "Analytics Data Not Available", "happy-elementor-addons" ); ?></h2>
					<p class="f16" style="margin: 0 0;"><?php printf( esc_html__( 'To see Analytics you need to follow these 2 steps:', 'happy-elementor-addons' ) ); ?></p>
				</div>
			</div>
			<p class="f16 step">
				<?php
					printf( '<strong>%s</strong> %s <a href="%s" target="_blank">%s</a> %s',
						__( 'Step - 1:', 'happy-elementor-addons' ),
						__( 'Go to dashboard>Elementor>Settings>Experiment tab and tick "Usage Data Sharing" (last option) then save the change.', 'happy-elementor-addons' ),
						admin_url( 'admin.php?page=elementor#tab-experiments' ),
						__( 'Click here', 'happy-elementor-addons' ),
						__( 'to go to the page.', 'happy-elementor-addons' )
					);
				?>
			</p>
			<p class="f16 step" style="margin: 0 0;">
				<?php
					printf( '<strong>%s</strong> %s <a href="%s" target="_blank">%s</a> %s',
						__( 'Step - 2:', 'happy-elementor-addons' ),
						__( 'Go to dashboard>Elementor>System Info>Elements Usage and press the "Recalculate" button.', 'happy-elementor-addons' ),
						admin_url( 'admin.php?page=elementor-system-info' ),
						__( 'Click here', 'happy-elementor-addons' ),
						__( 'to go to the page.', 'happy-elementor-addons' )
					);
				?>
			</p>
		</div>
	<?php else: ?>
		<!-- Used Widget Analytics -->
		<div class="ha-dashboard-panel__header flex-content used-widgets">
			<div class="ha-dashboard-panel__header-content">
				<h2><?php esc_html_e( 'Used Widgets', 'happy-elementor-addons' ); ?></h2>
				<?php if( $total_used_widget_count ): ?>
					<p class="f16" style="margin: 0 0;"><?php printf( esc_html__( 'You are using only %s %s widgets. %s', 'happy-elementor-addons' ), '<strong>', $total_used_widget_count,  '</strong>' ); ?></p>
				<?php else: ?>
					<p class="f16"><?php printf( esc_html__( 'No used widget found!', 'happy-elementor-addons' ) ); ?></p>
				<?php endif; ?>
			</div>

			<div class="ha-dashboard-panel__header-summary">
				<div class="data"><?php printf( esc_html__('Total Widget: %s', 'happy-elementor-addons' ), $total_widgets_count);?></div>
				<div class="data"><?php printf( esc_html__('Used: %s', 'happy-elementor-addons' ), $total_used_widget_count);?></div>
				<div class="data"><?php printf( esc_html__('Unused: %s', 'happy-elementor-addons' ), $total_unuse_widget_count);?></div>
			</div>
		</div>

		<div class="ha-dashboard-analytics" style="margin-bottom: 80px;">
			<?php
			foreach ($used_widget as $key => $data) :
				?>
				<div class="ha-dashboard-analytics__item">
					<fieldset>
					<?php
						if( isset( $widgets[$key]['is_pro'] ) && $widgets[$key]['is_pro'] ){
							printf( esc_html__('%sPRO%s', 'happy-elementor-addons' ), '<legend class="pro">', '</legend>');
						}else{
							printf( esc_html__('%sFREE%s', 'happy-elementor-addons' ), '<legend class="free">', '</legend>');
						}
					?>
						<div class="widget_inner">
							<div class="widget-title"><?php echo $widgets[$key]['title'];?></div>
							<span class="ha-dashboard-analytics__item-total-count"><?php esc_html_e('total use: ', 'happy-elementor-addons'); ?><?php echo $data;?></span>
						</div>
					</fieldset>
				</div>
			<?php
			endforeach;
			?>
		</div>



		<!-- Unused Widget Analytics -->
		<div class="ha-dashboard-panel__header flex-content unused-widgets">
			<div class="ha-dashboard-panel__header-content">
				<h2><?php esc_html_e( 'Unused Widgets', 'happy-elementor-addons' ); ?></h2>
				<?php if( $total_unuse_widget_count ): ?>
					<p class="f16"><?php printf( esc_html__( '%s %s widgets %s are unused right now. You can disable this to make the site faster.', 'happy-elementor-addons' ), '<strong>', $total_unuse_widget_count,  '</strong>' ); ?></p>
				<?php else: ?>
					<p class="f16"><?php printf( esc_html__( 'No unused widget found!', 'happy-elementor-addons' ) ); ?></p>
				<?php endif; ?>
			</div>
			<?php if( !empty($unuse_widget) ) :?>
				<button id="ha-dashboard-analytics-disable" class="ha-dashboard-btn ha-dashboard-analytics__unused_disable" type="submit" <?php echo $disable_btn ? 'disabled' : ''; ?>>
					<?php echo esc_html__( 'Disable all unused widget', 'happy-elementor-addons' ); ?>
				</button>
				<input type="hidden" name="disable-unused-widgets" value="false">
			<?php endif;?>
		</div>

		<?php if( !empty($unuse_widget) ) :?>
		<div class="ha-dashboard-analytics">
			<?php
			foreach ($unuse_widget as $key => $data) :
				?>
				<div class="ha-dashboard-analytics__item">
					<fieldset>
					<?php
						if( isset( $widgets[$data]['is_pro'] ) && $widgets[$data]['is_pro'] ){
							printf( esc_html__('%sPRO%s', 'happy-elementor-addons' ), '<legend class="pro">', '</legend>');
						}else{
							printf( esc_html__('%sFREE%s', 'happy-elementor-addons' ), '<legend class="free">', '</legend>');
						}
					?>
						<div class="widget_inner">
							<div class="widget-title">
								<?php echo $widgets[$data]['title'];?>
								<?php if( in_array( $data, $inactive_widgets ) ) : ?>
									<span class="disable" title="Disable"></span>
								<?php else:?>
									<span class="enabled" title="Enabled"></span>
								<?php endif;?>
							</div>
							<span class="ha-dashboard-analytics__item-total-count"><?php echo esc_html('total use: 0');?></span>
						</div>
					</fieldset>
				</div>
			<?php
			endforeach;
			?>
		</div>
		<?php endif;?>
	<?php endif;?>
</div>
