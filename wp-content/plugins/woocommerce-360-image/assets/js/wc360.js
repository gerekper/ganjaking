window.onload = wc360_init;

var product1;
function wc360_init() {
	var all_images = wc360_vars.images,
		image_array = jQuery.parseJSON( all_images );

	product1 = jQuery( '.threesixty' ).ThreeSixty({
		totalFrames : image_array.length,
		currentFrame: 1,
		endFrame    : image_array.length,
		framerate   : wc360_vars.framerate,
		playSpeed   : wc360_vars.playspeed,
		imgList     : '.threesixty_images',
		progress    : '.spinner',
		filePrefix  : '',
		height      : wc360_vars.height,
		width       : wc360_vars.width,
		navigation  : wc360_vars.navigation,
		imgArray    : image_array,
		responsive  : wc360_vars.responsive,
		drag        : wc360_vars.drag,
		disableSpin : wc360_vars.spin,
		plugins     : ['ThreeSixtyFullscreen'],
	});

	jQuery( document ).trigger( 'wc360:init', [ product1 ] );
}
