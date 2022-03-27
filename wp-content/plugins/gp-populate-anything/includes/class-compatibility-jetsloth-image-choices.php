<?php

class GPPA_Compatibility_JetSloth_Image_Choices {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		if ( ! function_exists( 'gf_image_choices' ) ) {
			return;
		}

		add_filter( 'gppa_input_choice', array( $this, 'add_image_to_choice' ), 10, 4 );
		add_action( 'gform_editor_js', array( $this, 'add_image_choice_template' ), 1 );
	}

	public function add_image_to_choice( $choice, $field, $object, $objects ) {
		$templates = rgar( $field, 'gppa-choices-templates', array() );

		if ( rgar( $templates, 'imageChoices_image' ) ) {
			$choice['imageChoices_image'] = gp_populate_anything()->process_template( $field, 'imageChoices_image', $object, 'choices', $objects );

			/* In lieu of another template row for choices, just try to get the attachment ID from the URL. */
			$attachment_id = attachment_url_to_postid( $choice['imageChoices_image'] );

			if ( $attachment_id ) {
				$choice['imageChoices_imageID'] = $attachment_id;
			}
		}

		return $choice;
	}

	public function add_image_choice_template() {
		?>
		<script type="text/javascript">
			window.gform.addFilter( 'gppa_template_rows', function ( templateRows, field, populate ) {
				if ( populate !== 'choices' ) {
					return templateRows;
				}

				if ( typeof window['imageChoicesAdmin'] !== 'undefined' && imageChoicesAdmin.fieldCanHaveImages( field ) ) {
					templateRows.push( {
						id: 'imageChoices_image',
						label: '<?php echo esc_js( __( 'Image', 'gp-populate-anything' ) ); ?>',
						required: false,
					} );
				}

				return templateRows;
			} );
		</script>
		<?php
	}

}


function gppa_compatibility_jetsloth_image_choices() {
	return GPPA_Compatibility_JetSloth_Image_Choices::get_instance();
}
