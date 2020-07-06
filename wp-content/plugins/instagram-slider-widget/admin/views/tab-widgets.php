<?php
/**
 * @var string $content
 * @var array $insta_widgets
 * @var array $demo_widgets
 */
?>
<div class="wis-widgets-container">
    <h2><?= __( 'Recommended widget templates', 'instagram-slider-widget' ); ?></h2>
    <div class="wis-demo-widgets">
		<?php if ( is_array( $insta_widgets ) && ! empty( $insta_widgets ) ): ?>
            <div class="wis-row">
				<?php foreach ( $insta_widgets as $key => $insta_widget ) {
					$shortcode    = "[jr_instagram id='{$key}']";
					$wis_demo_pro = "";
					if ( ! WIS_Plugin::app()->is_premium() && isset( $insta_widget['premium'] ) && $insta_widget['premium'] ) {
						$shortcode    = '';
						$wis_demo_pro = "wis_demo_pro";
					}
					if ( isset( $insta_widget['demo'] ) && ! empty( $insta_widget['demo'] ) ) {
						$demo_id = $insta_widget['demo'];
						?>
                        <div class="wis-col-16">
                            <div><p style="text-align: center;"><img
                                            src="<?php echo WIS_PLUGIN_URL . "/admin/assets/img/demo/{$demo_id}.svg"; ?>"
                                            alt=""></p>
                            </div>
                            <div class="wis-demo-shortcode">
                                <p><?= __( 'Shortcode for this widget', 'instagram-slider-widget' ); ?><span
                                            class="<?= $wis_demo_pro; ?>"></span></p>
                                <input id="wis_insta_shortcode" onclick="this.setSelectionRange(0, this.value.length)"
                                       type="text" class="wis-demo-widefat" value="<?= $shortcode; ?>"
                                       readonly="readonly">
                                <p><?= $insta_widget['title']; ?></p>
                            </div>
                        </div>
						<?php
					}
				} ?>
            </div>
		<?php endif; ?>
		<?php
		$account = $this->get_current_account();
		if ( ! isset( $demo_id ) && ! empty( $account ) ) { ?>
            <a class="button button-primary" href="<?php echo add_query_arg( [ 'do' => 'add_demo' ] ); ?>">Add demo
                widgets</a>
		<?php } else if ( ! isset( $demo_id ) && empty( $account ) ) { ?>
            <a class="button button-primary" disabled="disabled" href="#">Add demo
                widgets</a> <div style="display: inline-block; line-height: 30px;"><?=__( 'Add instagram account in plugin settings', 'instagram-slider-widget' );?></div>
		<?php } ?>
    </div>

    <div class="wis-demo-widgets">
		<?php echo $content; ?>
    </div>

    <style>
        .widget-inside
        {
            border-top: none;
            padding: 1px 15px 15px 15px;
            line-height: 1.2;
        }
    </style>
    <script>
        jQuery(document).ready(function ($) {
            $('.widget:not([id*="jr_insta_slider"])').remove();
            //$('[id*="jr_insta_slider"]').before($('#jr_insta_shortcode').val())
            $('.sidebar-name').find('button.handlediv').remove();
        });
    </script>

	<?php
	echo "<pre>";
	//print_r( $demo );
	//print_r( $insta_widgets );
	echo "</pre>";
	?>
</div>
