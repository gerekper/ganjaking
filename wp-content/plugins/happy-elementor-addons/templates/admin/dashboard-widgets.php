<?php
/**
 * Dashboard widgets tab template
 */

defined( 'ABSPATH' ) || die();

$widgets = self::get_widgets();
$catwise_widgets = self::get_widget_map_catwise();
$inactive_widgets = \Happy_Addons\Elementor\Widgets_Manager::get_inactive_widgets();

$total_widgets_count = count( $widgets );

if ( ! ha_has_pro() && ! empty( $total_widgets_count ) ) {
	$total_widgets_count = ( $total_widgets_count - 2 );
}

?>
<div class="ha-dashboard-panel">
    <div class="ha-dashboard-panel__header">
        <div class="ha-dashboard-panel__header-content">
            <h2><?php esc_html_e( 'Happy Widgets', 'happy-elementor-addons' ); ?></h2>
            <p class="f16"><?php printf( esc_html__( 'Here is the list of our all %s widgets. You can enable or disable widgets from here to optimize loading speed and Elementor editor experience. %sAfter enabling or disabling any widget make sure to click the Save Changes button.%s', 'happy-elementor-addons' ), $total_widgets_count, '<strong>', '</strong>' ); ?></p>

            <div class="ha-action-list">
                <button type="button" class="ha-action--btn" data-filter="*"><?php esc_html_e( 'All', 'happy-elementor-addons' ); ?></button>
                <button type="button" class="ha-action--btn" data-filter="free"><?php esc_html_e( 'Free', 'happy-elementor-addons' ); ?></button>
                <button type="button" class="ha-action--btn" data-filter="pro"><?php esc_html_e( 'Pro', 'happy-elementor-addons' ); ?></button>
                <span class="ha-action--divider">|</span>
                <button type="button" class="ha-action--btn" data-action="enable"><?php esc_html_e( 'Enable All', 'happy-elementor-addons' ); ?></button>
                <button type="button" class="ha-action--btn" data-action="disable"><?php esc_html_e( 'Disable All', 'happy-elementor-addons' ); ?></button>
            </div>
        </div>
    </div>

    <div class="ha-dashboard-widgets">
        <?php
		if( $catwise_widgets ):
			foreach( $catwise_widgets as $cat => $widgets) :
				if( $widgets ):
					printf('<h2 %s>%s %s</h2><br>',
						"style='width: 100%; margin-left: 10px;'",
						ucwords(str_replace('-', ' ', $cat)),
						__( 'Widgets', 'happy-elementor-addons' )
					);
					foreach ( $widgets as $widget_key => $widget_data ) :
						$title = isset( $widget_data['title'] ) ? $widget_data['title'] : '';
						$icon = isset( $widget_data['icon'] ) ? $widget_data['icon'] : '';
						$is_pro = isset( $widget_data['is_pro'] ) && $widget_data['is_pro'] ? true : false;
						$demo_url = isset( $widget_data['demo'] ) && $widget_data['demo'] ? $widget_data['demo'] : '';
						$is_placeholder = $is_pro && ! ha_has_pro();
						$class_attr = 'ha-dashboard-widgets__item';

						if ( $is_pro ) {
							$class_attr .= ' item--is-pro';
						}

						$checked = '';

						if ( ! in_array( $widget_key, $inactive_widgets ) ) {
							$checked = 'checked="checked"';
						}

						if ( $is_placeholder ) {
							$class_attr .= ' item--is-placeholder';
							$checked = 'disabled="disabled"';
						}
						?>
						<div class="<?php echo $class_attr; ?>">
							<?php if ( $is_pro ) : ?>
								<span class="ha-dashboard-widgets__item-badge"><?php esc_html_e( 'Pro', 'happy-elementor-addons' ); ?></span>
							<?php endif; ?>
							<span class="ha-dashboard-widgets__item-icon"><i class="<?php echo $icon; ?>"></i></span>
							<h3 class="ha-dashboard-widgets__item-title">
								<label for="ha-widget-<?php echo $widget_key; ?>" <?php echo $is_placeholder ? 'data-tooltip="Get pro"' : ''; ?>><?php echo $title; ?></label>
								<?php if ( $demo_url ) : ?>
									<a href="<?php echo esc_url( $demo_url ); ?>" target="_blank" rel="noopener" data-tooltip="<?php esc_attr_e( 'Click to view demo', 'happy-elementor-addons' ); ?>" class="ha-dashboard-widgets__item-preview"><i aria-hidden="true" class="eicon-device-desktop"></i></a>
								<?php endif; ?>
							</h3>
							<div class="ha-dashboard-widgets__item-toggle ha-toggle">
								<input id="ha-widget-<?php echo $widget_key; ?>" <?php echo $checked; ?> type="checkbox" class="ha-toggle__check ha-widget" name="widgets[]" value="<?php echo $widget_key; ?>">
								<b class="ha-toggle__switch"></b>
								<b class="ha-toggle__track"></b>
							</div>
						</div>
					<?php
					endforeach;
				endif;
			endforeach;
		endif;
        ?>
    </div>

    <div class="ha-dashboard-panel__footer">
        <button disabled class="ha-dashboard-btn ha-dashboard-btn--save" type="submit"><?php esc_html_e( 'Save Settings', 'happy-elementor-addons' ); ?></button>
    </div>
</div>
