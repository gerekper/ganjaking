<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$custom_tag = 'script';
$first_tag = 'style';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<meta name="viewport" content="width=device-width"/>
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php wp_head(); ?>
	<<?php echo esc_attr( $first_tag ); ?>>
		body {
			background-color: #FFF;
			color: #000;
			font-size: 12px;
		}

		<?php
		// @codingStandardsIgnoreLine
		print wpbakery()->parseShortcodesCustomCss( $shortcodes_string );
		?>
		.vc_gitem-preview {
			margin: 60px auto;
		}

		.vc_gitem-preview .vc_grid-item {
			display: block;
			margin: 0 auto;
		}

		.vc_grid-item-width-dropdown {
			margin-top: 10px;
			text-align: center;
		}

		.vc_container {
			margin: 0 15px;
		}

		img {
			width: 100%;
		}
	</<?php echo esc_attr( $first_tag ); ?>>
</head>
<div id="vc_grid-item-primary" class="vc_grid-item-site-content">
	<div id="vc_grid-item-content" role="vc_grid-item-main">
		<div class="vc_gitem-preview" data-vc-grid-settings="{}">
			<div class="vc_container">
				<div class="vc_row">
					<?php
					// @codingStandardsIgnoreLine
					print $grid_item->renderItem( $post );
					?>
				</div>
			</div>

		</div>
	</div>
	<!-- #content -->
</div>
<!-- #primary -->
<?php wp_footer(); ?>
<<?php echo esc_attr( $custom_tag ); ?>>
	var currentWidth = '<?php echo esc_js( $default_width_value ); ?>',
		vcSetItemWidth = function ( value ) {
			jQuery( '.vc_grid-item' ).removeClass( 'vc_col-sm-' + currentWidth )
				.addClass( 'vc_col-sm-' + value );
			currentWidth = value;
		}, changeAnimation;
	changeAnimation = function ( animation ) {
		var $animatedBlock, prevAnimation;
		$animatedBlock = jQuery( '.vc_gitem-animated-block' );
		prevAnimation = $animatedBlock.data( 'vcAnimation' );
		$animatedBlock.hide()
			.addClass( 'vc_gitem-animate vc_gitem-animate-' + animation )
			.removeClass( 'vc_gitem-animate-' + prevAnimation )
			.data( 'vcAnimation', animation );
		setTimeout( function () {
			$animatedBlock.show();
		}, 100 );
	};
	jQuery( document ).ready( function ( $ ) {
		window.parent.vc && window.parent.vc.app.showPreview( currentWidth );
	} );
</<?php echo esc_attr( $custom_tag ); ?>>
</body>
</html>
