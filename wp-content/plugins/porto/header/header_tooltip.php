<?php
global $porto_settings;

$tooltip_content = empty( $porto_settings['show-header-tooltip'] ) ? '' : $porto_settings['header-tooltip'];
$tooltip_content = do_shortcode( apply_filters( 'porto_header_tooltip', $tooltip_content ) );
if ( $tooltip_content ) :
	?>
<div class="porto-tooltip">
	<span class="tooltip-icon"><i class="fas fa-exclamation"></i></span>
	<div class="tooltip-popup">
		<span class="tooltip-close"><i class="fas fa-times"></i></span>
		<div class="content">
			<?php echo porto_strip_script_tags( $tooltip_content ); ?>
		</div>
	</div>
</div>
<?php endif; ?>
